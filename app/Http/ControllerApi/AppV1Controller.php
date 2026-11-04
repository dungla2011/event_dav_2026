<?php

namespace App\Http\ControllerApi;

use App\Http\Middleware\AppV1DeviceAuth;
use App\Models\EventAndUser;
use App\Models\EventInfo;
use App\Models\EventUserInfo;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * API cho app kiosk check-in (Event-DavAppFace2026): /api/app/v1/...
 * Cùng hợp đồng với /api/app/v1 của event.lad.vn (EventAppFace2026), bản rút gọn:
 * không có mặt lạ / danh bạ / SMS / đăng nhập Google.
 *
 * Nghiệp vụ lấy từ trang cũ public/tool1/_site/event_mng/qr-scaned-post.php:
 *  - QR: ".../data/<evId hoặc evRand>|<uid mã hoá | email>" hoặc "...data=<...>|<...>"
 *  - Mặt: ảnh gửi sang Flask 127.0.0.1:50000/detect_face (cache vector toàn hệ thống) → user_event_id
 *  - Khách hợp lệ khi có dòng event_and_users của sự kiện đang quét.
 *    QR của sự kiện cha cũng dùng được cho sự kiện con.
 *  - Ghi check-in = event_and_users.attend_at.
 *  - Chữ ký: PNG lưu thành FileUpload, id ghi vào event_and_users.signature
 *    (và event_user_infos.signature nếu khách chưa từng ký) — như saveSignatureUser.
 *
 * guest_ref = id dòng event_and_users (chỉ có nghĩa trong sự kiện đó).
 */
class AppV1Controller extends Controller
{
    const FACE_API_URL = 'http://127.0.0.1:50000/detect_face';
    const RECENT_LIMIT = 30;
    const SEARCH_LIMIT = 100;
    /** Ảnh khách nặng hơn mức này (byte) thì trả bản thu nhỏ. */
    const PHOTO_THUMB_OVER = 500 * 1024;

    private static function log1($str)
    {
        @file_put_contents('/var/glx/weblog/app_v1.log', date('Y-m-d H:i:s') . ' # ' . $str . "\n", FILE_APPEND);
    }

    private static function iso($dt)
    {
        if (!$dt) {
            return null;
        }
        $t = strtotime((string)$dt);

        return $t ? date('c', $t) : null;
    }

    /** Ảnh trong hệ thống hay trả đường dẫn tương đối (/test_cloud_file?fid=…) → URL đầy đủ cho app. */
    private static function absUrl($url)
    {
        if (!$url) {
            return null;
        }
        if (str_starts_with($url, '/')) {
            return request()->getSchemeAndHttpHost() . $url;
        }

        return $url;
    }

    /**
     * Ảnh khách cho app: ảnh gốc to (có ảnh tới 15MB, 5000px) hoặc có cờ xoay EXIF thì tạo bản 600px đã xoay
     * đúng chiều, lưu ở public/images/app-thumb/ và trả đường dẫn đó; còn lại trả null (dùng ảnh gốc).
     * Không dùng bản thu nhỏ có sẵn của hệ thống (type=thumb): nó bỏ cờ EXIF mà không xoay → ảnh nằm nghiêng,
     * và với ảnh nhỏ có khi còn nặng hơn ảnh gốc.
     */
    private static function appThumb(int $fid, string $path)
    {
        $orient = 1;
        if (function_exists('exif_read_data') && @exif_imagetype($path) === IMAGETYPE_JPEG) {
            $orient = (int)(@exif_read_data($path)['Orientation'] ?? 1);
        }
        if (filesize($path) <= self::PHOTO_THUMB_OVER && $orient <= 1) {
            return null;
        }
        $rel = "/images/app-thumb/$fid-600.jpg";
        $out = public_path($rel);
        if (is_file($out) && filemtime($out) >= filemtime($path)) {
            return $rel;
        }
        try {
            @mkdir(dirname($out), 0755, true);
            $im = new \Imagick($path);
            $im->setIteratorIndex(0);
            switch ($orient) {
                case 2: $im->flopImage(); break;
                case 3: $im->rotateImage('#000', 180); break;
                case 4: $im->flipImage(); break;
                case 5: $im->flopImage(); $im->rotateImage('#000', 270); break;
                case 6: $im->rotateImage('#000', 90); break;
                case 7: $im->flopImage(); $im->rotateImage('#000', 90); break;
                case 8: $im->rotateImage('#000', 270); break;
            }
            $im->setImageOrientation(\Imagick::ORIENTATION_TOPLEFT);
            if ($im->getImageWidth() > 600 || $im->getImageHeight() > 600) {
                $im->thumbnailImage(600, 600, true);
            }
            // PNG có nền trong suốt → nền trắng trước khi lưu JPEG.
            $im->setImageBackgroundColor('#fff');
            $im = $im->mergeImageLayers(\Imagick::LAYERMETHOD_FLATTEN);
            $im->setImageFormat('jpeg');
            $im->setImageCompressionQuality(82);
            $im->stripImage();
            $im->writeImage($out);
            $im->clear();

            return $rel;
        } catch (\Throwable $e) {
            self::log1("appThumb $fid lỗi: " . $e->getMessage());

            return null;
        }
    }

    /** Chữ thuần: bỏ thẻ HTML, giải &amp;… , gộp khoảng trắng. */
    private static function plain($s)
    {
        $s = trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags((string)$s), ENT_QUOTES | ENT_HTML5, 'UTF-8')));

        return $s === '' ? null : $s;
    }

    private static function fail($error, $http = 200, $extra = [])
    {
        return response()->json(['status' => 'fail', 'error' => $error] + $extra, $http);
    }

    private static function deviceName(Request $request)
    {
        return $request->attributes->get(AppV1DeviceAuth::ATTR)->name ?? '?';
    }

    private static function eventRequireSign(EventInfo $ev)
    {
        return (bool)($ev->require_sign || $ev->require_sign_this_event);
    }

    /** Như checkUserNeedSign() của trang cũ. */
    private static function needSign(EventInfo $ev, EventUserInfo $ue, EventAndUser $eu)
    {
        if ($ev->require_sign && !$ue->signature) {
            return true;
        }
        if ($ev->require_sign_this_event && !$eu->signature) {
            return true;
        }

        return false;
    }

    private static function eventSummary(EventInfo $ev)
    {
        $cover = null;
        try {
            $cover = $ev->getThumbInImageList_CloudLink() ?: null;
        } catch (\Throwable $e) {
        }

        return [
            'public_id' => (string)$ev->id,
            'name' => (string)self::plain($ev->name),
            'starts_at' => self::iso($ev->time_start),
            'ends_at' => self::iso($ev->time_end),
            'status' => $ev->status ? 'published' : 'draft',
            'location' => self::plain($ev->location),
            'summary' => self::plain($ev->name_sub),
            'cover_url' => self::absUrl($cover),
            'require_signature' => self::eventRequireSign($ev),
        ];
    }

    private static function guestMatch(EventInfo $ev, EventUserInfo $ue, EventAndUser $eu, $alreadyBefore)
    {
        $photo = null;
        try {
            $photo = $ue->getThumbInImageList_CloudLink() ?: null;
            $fid = explode(',', (string)$ue->image_list)[0];
            if ($photo && is_numeric($fid) && ($cl = \App\Models\FileUpload::getCloudObj($fid)) && is_file($cl->file_path)) {
                $photo = self::appThumb((int)$fid, $cl->file_path) ?? $photo;
            }
        } catch (\Throwable $e) {
        }
        $extra = [];
        for ($i = 1; $i <= 5; $i++) {
            $f = "extra_info$i";
            if ($v = self::plain($eu->$f)) {
                $extra[] = $v;
            }
        }
        $name = trim(preg_replace('/\s+/', ' ', $ue->getFullnameAndTitle()));

        return [
            'guest_ref' => (string)$eu->id,
            'guest_code' => (string)$eu->id,
            'name' => $name ?: (string)$ue->name,
            'organization' => self::plain($ue->organization),
            'rsvp' => $eu->confirm_join_at ? 'yes' : ($eu->deny_join_at ? 'no' : null),
            'checked_in_at' => self::iso($eu->attend_at),
            'already_checked_in' => (bool)$alreadyBefore,
            'signed' => !self::needSign($ev, $ue, $eu),
            'photo_url' => self::absUrl($photo),
            'extra' => $extra,
        ];
    }

    private static function findEvent($eventId)
    {
        if (!is_numeric($eventId)) {
            return null;
        }

        return EventInfo::find((int)$eventId);
    }

    /** Sự kiện chưa mở / đã đóng check-in → mã lỗi, còn lại null. Như trang cũ. */
    private static function eventWindowError(EventInfo $ev)
    {
        if ($ev->time_start || $ev->time_start_check_in) {
            $starts = array_filter([$ev->time_start, $ev->time_start_check_in]);
            if (min($starts) > nowyh()) {
                return 'event_not_started';
            }
        }
        if ($ev->time_end && $ev->time_end < nowyh()) {
            return 'event_ended';
        }

        return null;
    }

    /**
     * Khách (theo id event_user_infos) → kết quả quét. Ghi attend_at nếu $checkin.
     */
    private function resolveGuest(Request $request, EventInfo $ev, $ueId, $via, $checkin)
    {
        if ($err = self::eventWindowError($ev)) {
            return self::fail($err);
        }
        if (!$ue = EventUserInfo::find($ueId)) {
            return self::fail($via === 'qr' ? 'invalid_code' : 'no_match');
        }
        if (!$eu = EventAndUser::where('event_id', $ev->id)->where('user_event_id', $ue->id)->first()) {
            self::log1("ev $ev->id: uid $ue->id chưa đăng ký ($via)");

            return self::fail('not_invited', 200, ['guest_name' => trim($ue->getFullnameAndTitle())]);
        }
        $already = (bool)$eu->attend_at;
        $dev = self::deviceName($request);
        if ($checkin && !$already && !self::needSign($ev, $ue, $eu)) {
            $eu->attend_at = nowyh();
            $eu->addLog("App check-in ($via, $dev)");
            $eu->save();
        }
        self::log1("ev $ev->id: $via uid $ue->id eu $eu->id already=" . (int)$already . " checkin=" . (int)$checkin);

        return response()->json(['status' => 'success', 'data' => self::guestMatch($ev, $ue, $eu, $already)]);
    }

    // ── Endpoints ────────────────────────────────────────────────────────────

    public function me(Request $request)
    {
        $device = $request->attributes->get(AppV1DeviceAuth::ATTR);
        $search = trim(mb_substr((string)$request->query('q'), 0, 100));
        if ($search !== '') {
            // Tìm trên tất cả sự kiện, kể cả sự kiện không có giờ (danh bạ, form cập nhật…).
            // Collation đã bỏ qua dấu và hoa/thường, riêng đ ≠ d nên phải đổi tay để gõ "dau moi" ra "Đầu mối".
            $like = '%'.addcslashes(str_replace(['đ', 'Đ'], 'd', $search), '%_\\').'%';
            $events = EventInfo::where(function ($q) use ($like, $search) {
                $q->whereRaw("REPLACE(REPLACE(name, 'đ', 'd'), 'Đ', 'd') LIKE ?", [$like]);
                if (ctype_digit($search)) {
                    $q->orWhere('id', (int)$search);
                }
            })->orderByRaw('COALESCE(time_start, time_end) DESC')->limit(self::SEARCH_LIMIT)->get();
        } else {
            // Mặc định: các sự kiện có giờ gần hôm nay nhất (cả sắp tới lẫn đã qua), ngày muộn hơn xếp trên.
            $events = EventInfo::where(fn($q) => $q->whereNotNull('time_start')->orWhereNotNull('time_end'))
                ->orderByRaw('ABS(TIMESTAMPDIFF(SECOND, COALESCE(time_start, time_end), ?))', [nowyh()])
                ->limit(self::RECENT_LIMIT)->get()
                ->sortByDesc(fn($ev) => $ev->time_start ?: $ev->time_end);
            // Sự kiện máy đang chọn có thể nằm ngoài danh sách: vẫn trả về để app cập nhật thông tin.
            $include = (int)$request->query('include');
            if ($include && !$events->contains('id', $include) && ($ev = EventInfo::find($include))) {
                $events->push($ev);
            }
        }

        $orgName = 'Học viện Ngoại giao';
        try {
            $site = \App\Models\SiteMng::getInstance();
            if ($site && ($site->name ?? '')) {
                $orgName = $site->name;
            }
        } catch (\Throwable $e) {
        }

        return response()->json([
            'device' => ['name' => $device->name, 'kind' => 'device'],
            'organizer' => ['name' => $orgName],
            'events' => $events->map(fn($ev) => self::eventSummary($ev))->values(),
        ]);
    }

    public function face(Request $request, $eventId)
    {
        if (!$ev = self::findEvent($eventId)) {
            return self::fail('not_found', 404);
        }
        // Kiểm tra giờ trước: sự kiện đã đóng thì báo đúng lý do cho cả người lạ, không tốn lượt nhận diện.
        if ($err = self::eventWindowError($ev)) {
            return self::fail($err);
        }
        $file = $request->file('file');
        if (!$file || !$file->isValid()) {
            return self::fail('no_file', 422);
        }
        if ($file->getSize() > 3 * 1024 * 1024) {
            return self::fail('file_too_large', 422);
        }

        $ch = curl_init(self::FACE_API_URL);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => ['file' => new \CURLFile($file->getRealPath(), 'image/jpeg', 'face.jpg')],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 2,
            CURLOPT_TIMEOUT => 8,
        ]);
        $body = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $cerr = curl_error($ch);
        curl_close($ch);

        $res = $body ? json_decode($body, true) : null;
        if (!is_array($res)) {
            self::log1("face_api lỗi: HTTP $code $cerr");

            return self::fail('face_service_down', 503);
        }
        if (($res['status'] ?? '') !== 'success' || empty($res['data']['user_event_id'])) {
            $e = (string)($res['error'] ?? '');

            return self::fail(stripos($e, 'no face') !== false ? 'no_face' : 'no_match');
        }

        return $this->resolveGuest($request, $ev, (int)$res['data']['user_event_id'], 'face', $request->boolean('checkin'));
    }

    public function qr(Request $request, $eventId)
    {
        if (!$ev = self::findEvent($eventId)) {
            return self::fail('not_found', 404);
        }
        if ($err = self::eventWindowError($ev)) {
            return self::fail($err);
        }
        $code = trim((string)$request->input('code'));
        $data = '';
        if (str_contains($code, 'data=')) {
            $data = explode('data=', $code, 2)[1];
        } elseif (str_contains($code, '/data/')) {
            $data = explode('/data/', $code, 2)[1];
        }
        $data = urldecode($data);
        if (!$data || !str_contains($data, '|')) {
            return self::fail('invalid_code');
        }
        [$evPart, $userPart] = array_map('trim', explode('|', $data, 2));

        try {
            $evIdInQr = is_numeric($evPart) ? (int)$evPart : (int)qqgetIdFromRand_($evPart);
            $emailOrUid = $userPart;
            if (!str_contains($userPart, '@')) {
                if (!is_numeric($userPart) || strlen($userPart) >= 6) {
                    $tmp = dfh1b($userPart);
                    if (is_numeric($tmp)) {
                        $emailOrUid = $tmp;
                    }
                }
            }
        } catch (\Throwable $e) {
            return self::fail('invalid_code');
        }

        // QR của sự kiện cha dùng được ở sự kiện con.
        if ($evIdInQr != $ev->id && $evIdInQr != $ev->parent_id) {
            self::log1("qr sai sự kiện: qr ev $evIdInQr, đang quét $ev->id");

            return self::fail('wrong_event');
        }

        $ue = str_contains($emailOrUid, '@')
            ? EventUserInfo::where('email', $emailOrUid)->first()
            : (is_numeric($emailOrUid) ? EventUserInfo::find((int)$emailOrUid) : null);
        if (!$ue) {
            return self::fail('invalid_code');
        }

        return $this->resolveGuest($request, $ev, $ue->id, 'qr', $request->boolean('checkin', true));
    }

    public function checkin(Request $request, $eventId)
    {
        if (!$ev = self::findEvent($eventId)) {
            return self::fail('not_found', 404);
        }
        if ($err = self::eventWindowError($ev)) {
            return self::fail($err, 400);
        }
        $ref = (string)$request->input('guest_ref');
        $eu = is_numeric($ref) ? EventAndUser::where('id', (int)$ref)->where('event_id', $ev->id)->first() : null;
        if (!$eu || !($ue = EventUserInfo::find($eu->user_event_id))) {
            return self::fail('not_invited', 404);
        }

        $via = in_array($request->input('via'), ['face', 'qr']) ? $request->input('via') : 'manual';
        $dev = self::deviceName($request);
        $sig = (string)$request->input('signature');

        if ($sig) {
            if (!preg_match('#^data:image/png;base64,(.+)$#s', $sig, $m) || !($png = base64_decode($m[1], true))) {
                return self::fail('bad_signature', 422);
            }
            if (strlen($png) > 2 * 1024 * 1024) {
                return self::fail('bad_signature', 422);
            }
            $fileId = self::saveSignatureFile($png, $ev->id, $ue->id);
            if (!$fileId) {
                return self::fail('signature_save_failed', 500);
            }
            if (!$ue->signature) {
                $ue->signature = $fileId;
                $ue->addLog("Save signature from event $ev->id (app)");
                $ue->save();
            }
            $eu->signature = $fileId;
            $eu->addLog("Save signature (app, $dev)");
        } elseif (self::needSign($ev, $ue, $eu)) {
            return self::fail('signature_required', 400);
        }

        $already = (bool)$eu->attend_at;
        if (!$already) {
            $eu->attend_at = nowyh();
        }
        $eu->addLog("App check-in ($via, $dev)" . ($sig ? ' + chữ ký' : ''));
        $eu->save();
        self::log1("ev $ev->id: checkin eu $eu->id via $via sig=" . ($sig ? 1 : 0) . " already=" . (int)$already);

        return response()->json(['status' => 'success', 'data' => self::guestMatch($ev, $ue, $eu, $already)]);
    }

    /** Lưu PNG chữ ký thành FileUpload (chủ sở hữu = tài khoản admin tự động, như trang cũ). Trả id file. */
    private static function saveSignatureFile($png, $evId, $uid)
    {
        $tmp = tempnam(sys_get_temp_dir(), 'sig');
        file_put_contents($tmp, $png);
        $ownerId = 1;
        if ($email = env('AUTO_SET_ADMIN_EMAIL')) {
            $ownerId = \App\Models\User::where('email', $email)->value('id') ?: 1;
        }
        try {
            $ret = FileUploadControllerApi::uploadStatic((object)[
                'user_id' => $ownerId,
                'file_path_local_upload_' => $tmp,
                'file_name' => "signature-$uid-$evId-" . date('YmdHis') . '.png',
                'file_size' => strlen($png),
            ], 2);

            return is_numeric($ret) ? (int)$ret : null;
        } catch (\Throwable $e) {
            self::log1('lưu chữ ký lỗi: ' . $e->getMessage());

            return null;
        } finally {
            if (file_exists($tmp)) {
                @unlink($tmp);
            }
        }
    }
}
