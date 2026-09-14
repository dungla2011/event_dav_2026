<?php

namespace App\Http\ControllerApi;

use App\Http\Controllers\Controller;
use App\Models\CrmMessage;
use App\Models\MonitorConfig;
use App\Models\MonitorItem;
use App\Models\MonitorSetting;
use App\Models\TaxiUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Simple Zalo Proxy Controller
 *
 * Forward TẤT CẢ request /api/zalo/* sang Node.js server
 * Không cần định nghĩa từng endpoint riêng lẻ
 */
class ZaloSimpleProxyController extends Controller
{
    /**
     * Node.js server URL
     * Đổi thành URL thực tế của Node.js server
     */
    private string $nodeBaseUrl = 'http://localhost:3000';
    private $username;
    private $password;
    /**
     * Timeout cho HTTP request (giây)
     */
    private int $timeout = 30;
    public $userNameApp;
    public $userId;
    /**
     * Constructor - Load config từ .env nếu có
     */
    public function __construct()
    {
        $this->username = env('ZALO_API_USERNAME', 'admin');
        $this->password = env('ZALO_API_PASSWORD', '938475wufo87908u09');
        // Đọc URL từ .env nếu có
        // ZALO_NODE_URL=http://localhost:3000
        $this->nodeBaseUrl = env('ZALO_NODE_URL', 'http://localhost:3000');

        // Đọc timeout từ .env nếu có
        // ZALO_NODE_TIMEOUT=30
        $this->timeout = (int) env('ZALO_NODE_TIMEOUT', 30);

        $uid = 0;
        if($objUser = getCurrentUserId(1)){
            $uid = $objUser->getId();
            $this->userId = $uid;
            $this->userNameApp = $objUser->username;
        }
        else{
            return rtJsonApiError("Bạn cần đăng nhập trước!");
        }

//        die("UID = $uid / $this->userNameApp");
    }
    /**
     * Helper: Tạo HTTP client với Basic Auth
     */
    private function httpClient()
    {
        return Http::withBasicAuth($this->username, $this->password)
            ->timeout(10)
            ->withoutVerifying() // Nếu SSL có vấn đề
            ->acceptJson();
        // BỎ retry() hoàn toàn - xử lý HTTP status thủ công
    }
    /**
     * Trả lại các tin nhắn match với tìm kiêm cua users
     * @param Request $request
     * @param $name
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMatchMessages(Request $request, $name) {
        $uid = 0;
        $email = '';
        $objUser = getCurrentUserId(1);
        if($objUser) {
            $uid = $objUser->getId();
            $email = $objUser->email;
        }

        //Gọi api kiểm tra xem đã login zalo và lắng nghe chưa:
        try {
            $url = "http://localhost:3000/api/accounts/{$name}";
            $response = $this->httpClient()->get($url);

            // Kiểm tra HTTP status code TRƯỚC KHI parse JSON
            if ($response->status() == 404) {
                try {
                    $errorData = $response->json();
                    $errorMsg = $errorData['error'] ?? 'Tài khoản không tồn tại (2)';
                } catch (\Exception $jsonErr) {
                    $errorMsg = 'Tài khoản không tồn tại (JSON parse error)';
                }
                return rtJsonApiError("($email)\nBạn chưa đăng nhập tài khoản Zalo? \n\n{$errorMsg}");
            }
            // Parse response data khi thành công
            $ret = $response->json()['account'] ?? null;
            if (!$ret) {
                return rtJsonApiError("($email) Không thể lấy thông tin tài khoản Zalo, Bạn chưa đăng nhập?");
            }
            $ret = (object)$ret;
            // Kiểm tra trạng thái đăng nhập và lắng nghe
            if ($ret->hasCredentials !== true || $ret->isListening !== true) {
                return rtJsonApiError("($email) Tài khoản Zalo chưa Đăng nhập hoặc Chưa bật Lắng nghe tin nhắn!\nVui lòng Đăng nhập Zalo/Bật lắng nghe tin!");
            }

        } catch (\Exception $e) {
            // Chỉ catch NETWORK errors: timeout, connection refused, DNS failed
            return rtJsonApiError("($email) Không thể kết nối tới Zalo API Server!\nVui lòng kiểm tra:\n- Server http://localhost:3000 có đang chạy?\n- Network có bị block?\n\nChi tiết: " . $e->getMessage(),
                503
            );
        }

        //lấy ra tin cuối:
        $lastMsg = "(Chưa có tin nào)";
        $lastTime = '';
        if($lastMsgObj = CrmMessage::where('user_id', $uid)
            ->orderBy('id', 'desc')
            ->first()){
            $lastMsg = $lastMsgObj->content;
            $lastTime = $lastMsgObj->created_at->format('H:i:s');
        }

        //Từ khoá tìm kiêm:
        $searchKw = TaxiUser::getKeywordSearch($uid);
        if(!$searchKw)
            $searchKw = "Bạn chưa nhập Từ khoá tìm kiếm";
        else
            $searchKw = "Từ khoá tìm kiếm: $searchKw";


        $msgs = TaxiUser::getMessageNeedSearch($uid);
        $ret = TaxiUser::searchTaxiKeyword($msgs, $searchKw);

        $mm = [];
        //Để so sánh với các kq mới, mếu khác sẽ alert Firebase
        $lastIdListMatch = 0;

        foreach ($ret as $item){
            //Highlight từ khoá tìm kiêm:
//            $content = $item->content;
//            $wordList = array_map('trim', explode(',', $searchKw));
//            $wordList = array_filter($wordList); // Loại bỏ phần tử rỗng
//            foreach ($wordList as $word){
//                $wordEscaped = preg_quote($word, '/');
//                $content = preg_replace("/($wordEscaped)/iu", '<b style="color: red">$1</b>', $content);
//            }

            $oneMatch = [
                'msgId'=>$item->id,
                'content'=>$item->content_highlighted, //chưa highlight
                'group_link'=>'https://taxi24.vn',
                'group_name'=>$item->group_name_zl ?? 'chưa cập nhật, có thể cập nhật ở tin sau?',
                'time_str' => $item->created_at->format('H:i:s')
            ];
            $mm[] = $oneMatch;
            if($lastIdListMatch < intval($item->id))
                $lastIdListMatch = intval($item->id);
        }

        // Cache lastIdListMatch và so sánh để gửi Firebase
        $cacheKey = "taxi_match_ids_user_{$uid}";
        $oldIdList = intval(\Illuminate\Support\Facades\Cache::get($cacheKey, 0));

        if ($lastIdListMatch != $oldIdList) {
            output("/var/glx/weblog/debug_taxi_$uid.log", nowyh() .  " : UID $uid, tin khac: $lastIdListMatch !== $oldIdList");

            // Có thay đổi → Gọi Firebase
            $this->sendFireBaseTaxi($uid, $mm, $lastIdListMatch);
            // Cache mới, expire sau 1 giờ
            \Illuminate\Support\Facades\Cache::put($cacheKey, $lastIdListMatch, 3600);
        }

        $mm0 = [
            [
                'msgId'=>701,
                'content'=>'Hello <b style="color: red"> from Zalo </b> Proxy API <br> Hello <b style="color: red"> from Zalo </b> Proxy API
<br> Hello <b style="color: red"> from Zalo </b> Proxy API
',
                'group_link'=>'http://user.com/123',
                'group_name'=>'Nhóm 123',
                'time_str' => '11:30:20'
            ],
            [
                'msgId'=>12332,
                'content'=>'Hello2 from Zalo Proxy API',
                'group_link'=>'http://user.com/456',
                'group_name'=>'Nhóm 456',
                'time_str' => '11:30:20'
            ],
        ];


//        $mm = [];
//        $one =  [
//            'msgId'=>423,
//            'content'=>'Hello <b style="color: red"> from Zalo </b> Proxy API <br> Hello <b style="color: red"> from Zalo </b> Proxy API
//<br> Hello <b style="color: red"> from Zalo </b> Proxy API
//',
//            'group_link'=>'http://user.com/123',
//            'group_name'=>'Nhóm 123',
//            'time_str' => '11:30:20'
//        ];
//
//        for($i = 0; $i< 20; $i++){
//            $tmp = unserialize(serialize($one));
//            $n = $i + 100;
//            $tmp['msgId'] = $n;
//            $tmp['content'] = $n . " . " . $tmp['content'];
//            $mm[] = $tmp;
//        }
//        $domain = getDomainHostName();
//        return response()->json(['code' => -1, 'payload' => '121212',
//            'message' => 'Bấm vào đây, co loi', 'error_link' => "https://$domain/pricing"], 400);
//        return rtJsonApiError("Có lỗi...");

        if($lastMsg[0] == '{' && json_decode($lastMsg)){
            $lastMsg = "Ảnh/Biểu tượng";
        }

        $ret = [
            'status'=>"success",
            'loop_interval_seconds'=> 1,
            'server_time'=> nowh(),
            'count'=>count($mm),
            'match_string' => $searchKw,
            'last_message' => [
                'content'=> $lastMsg,
                'time_str' => $lastTime
            ],
            'data'=>$mm
        ];

        return response()->json($ret);

//        die(json_encode($ret, JSON_PRETTY_PRINT));
    }

    public function proxyAccount(Request $request, string $name = '', string $path = '')
    {
        // Chỉ định lại path cho đúng
        if ($path === '') {
            $path = 'accounts/' . $name;
        } else {
            $path = 'accounts/' . $name . '/' . $path;
        }

        //Xoá hết ký tự không phải chữ và số, gạch dưới trong $name
        $name = preg_replace('/[^a-zA-Z0-9_]/', '', $name);
        if($this->userNameApp != $name){
            return response()->json([
                'success' => false,
                'error' => "Account name mismatch: $name",
            ], 403);
        }

        //Chuyển sang hàm proxyToNodejs
        return $this->proxyToNodejs($request, $path);

        // die("   NAME = $name / PATH = $path   ");
     }

    /**
     * Proxy ALL requests to Node.js
     *
     * @param Request $request
     * @param string $path - Path sau /api/zalo/ (ví dụ: accounts, accounts/abc/qr-login, ...)
     * @return JsonResponse|mixed
     */
    public function proxyToNodejs(Request $request, string $path = '')
    {
        try {
            // 1. Xây dựng URL đầy đủ cho Node.js
            // /api/zalo/accounts/abc/qr-login -> http://localhost:3000/api/accounts/abc/qr-login
            $nodeUrl = $this->buildNodeUrl($path);

            // 2. Lấy HTTP method (GET, POST, PUT, DELETE, ...)
            $method = strtoupper($request->method());

            // 3. Lấy tất cả query parameters
            $queryParams = $request->query();

            // 4. Lấy body data (JSON, form data, ...)
            $bodyData = $request->all();

            // 5. Lấy headers (bỏ qua một số headers không cần thiết)
            $headers = $this->getForwardHeaders($request);

            // 6. Log request (optional - comment nếu không cần)
            Log::info('🔄 Proxying to Node.js', [
                'method' => $method,
                'path' => $path,
                'node_url' => $nodeUrl,
                'query' => $queryParams,
                'body_keys' => array_keys($bodyData),
            ]);

            // 7. Gửi request đến Node.js
            $response = $this->sendToNode($method, $nodeUrl, $queryParams, $bodyData, $headers);

            // 8. Log response (optional)
            Log::info('✅ Node.js response', [
                'status' => $response->status(),
                'success' => $response->successful(),
            ]);

            // 9. Trả về response từ Node.js cho client
            // Giữ nguyên status code, headers, và body
            return response($response->body(), $response->status())
                ->withHeaders($this->getResponseHeaders($response));

        } catch (\Exception $e) {
            // Log lỗi
            Log::error('❌ Proxy error', [
                'path' => $path,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Trả về lỗi 502 Bad Gateway
            return response()->json([
                'success' => false,
                'error' => 'Proxy error: ' . $e->getMessage(),
                'node_url' => $this->nodeBaseUrl,
                'path' => $path,
            ], 502);
        }
    }

    /**
     * Xây dựng URL Node.js đầy đủ
     *
     * @param string $path
     * @return string
     */
    private function buildNodeUrl(string $path): string
    {
        // Loại bỏ 'zalo' ở đầu path vì Node.js không có prefix này
        // /api/zalo/accounts -> /api/accounts

        // Đảm bảo path bắt đầu bằng /
        if (!str_starts_with($path, '/')) {
            $path = '/' . $path;
        }

        // Xây dựng URL
        $nodeUrl = rtrim($this->nodeBaseUrl, '/') . '/api' . $path;

        return $nodeUrl;
    }

    /**
     * Lấy headers cần forward sang Node.js
     *
     * @param Request $request
     * @return array
     */
    private function getForwardHeaders(Request $request): array
    {
        $headers = [];

        // Forward Content-Type
        if ($request->header('Content-Type')) {
            $headers['Content-Type'] = $request->header('Content-Type');
        }

        // Forward Authorization (nếu có)
        if ($request->header('Authorization')) {
            $headers['Authorization'] = $request->header('Authorization');
        }

        // Forward Accept
        if ($request->header('Accept')) {
            $headers['Accept'] = $request->header('Accept');
        }

        // Forward User-Agent
        if ($request->header('User-Agent')) {
            $headers['User-Agent'] = $request->header('User-Agent');
        }

        // Thêm custom header để Node.js biết request đến từ PHP proxy
        $headers['X-Forwarded-By'] = 'PHP-Proxy';
        $headers['X-Original-IP'] = $request->ip();

        return $headers;
    }

    /**
     * Gửi request đến Node.js
     *
     * @param string $method
     * @param string $url
     * @param array $queryParams
     * @param array $bodyData
     * @param array $headers
     * @return \Illuminate\Http\Client\Response
     */
    private function sendToNode(
        string $method,
        string $url,
        array $queryParams,
        array $bodyData,
        array $headers
    ): \Illuminate\Http\Client\Response {

        // Khởi tạo HTTP client với timeout
        $http = Http::timeout($this->timeout)
            ->withHeaders($headers);

        // Thêm query parameters vào URL
        if (!empty($queryParams)) {
            $http = $http->withQueryParameters($queryParams);
        }

        // Gửi request theo method
        switch ($method) {
            case 'GET':
                return $http->get($url);

            case 'POST':
                return $http->post($url, $bodyData);

            case 'PUT':
                return $http->put($url, $bodyData);

            case 'PATCH':
                return $http->patch($url, $bodyData);

            case 'DELETE':
                return $http->delete($url, $bodyData);

            default:
                // Fallback cho các method khác
                return $http->send($method, $url, [
                    'body' => $bodyData
                ]);
        }
    }

    /**
     * Lấy headers từ response của Node.js để forward về client
     *
     * @param \Illuminate\Http\Client\Response $response
     * @return array
     */
    private function getResponseHeaders(\Illuminate\Http\Client\Response $response): array
    {
        $headers = [];

        // Forward Content-Type
        if ($contentType = $response->header('Content-Type')) {
            $headers['Content-Type'] = $contentType;
        }

        // Forward các headers khác nếu cần
        // Ví dụ: Cache-Control, ETag, ...

        return $headers;
    }

    /**
     * Gửi thông báo Firebase khi có tin nhắn match mới
     *
     * @param int $userId User ID
     * @param array $matchedMessages Danh sách tin nhắn match
     * @param string $idList Chuỗi ID list (format: "123,456,789,")
     * @return void
     */
    private function sendFireBaseTaxi($userId, $matchedMessages, $idList)
    {
        try {

            $maxId = $idList;

            if($st = MonitorSetting::where('user_id', $userId)->first())
            {
                $count1 = count($matchedMessages);
                if($st->firebase_token && $st->firebase_token != ''){
                    $firebaseToken = $st->firebase_token;
                    $notification = [
                        'title' => "🚕 Tin mới : $maxId (Taxi24) !",
                        'body' => "Mã tin: $maxId ",
                        'click_action' => 'https://taxi24.vn',
                    ];

                    $data = [
                        'type' => 'taxi_match',
                        'count' => $count1,
                        'ids' => $idList,
                        'alert_type' => 'system_warning',
                        'severity' => 'high',
                    ];

                    // Gửi thông báo qua Firebase
                    if(1)
                    {
                        // Cách 2: Dùng Firebase Admin SDK (khuyến nghị)
                        $serviceAccountPath = ('/var/www/html/config/firebase-taxi.json');
                        if (file_exists($serviceAccountPath)) {
//                            throw new \Exception('Firebase service account file not found: ' . $serviceAccountPath);
                            $factory = (new \Kreait\Firebase\Factory)->withServiceAccount($serviceAccountPath);
                            $messaging = $factory->createMessaging();

                            $message = \Kreait\Firebase\Messaging\CloudMessage::withTarget('token', $firebaseToken)
                                ->withNotification($notification)
                                ->withData($data);

                            $result = $messaging->send($message);

                            \Illuminate\Support\Facades\Log::info('✅ Firebase notification sent (Admin SDK)', [
                                'result' => $result,
                            ]);
                        }
                    }
                }

            }



            // TODO: Implement Firebase notification
            // Example implementation:
            /*
            $user = \App\Models\User::find($userId);
            if (!$user || !$user->fcm_token) {
                return;
            }

            $notification = [
                'title' => '🚕 Có tin nhắn mới match!',
                'body' => 'Có ' . count($matchedMessages) . ' tin nhắn match với từ khóa của bạn',
                'click_action' => 'https://taxi.lad.vn/member/messages',
            ];

            $data = [
                'type' => 'taxi_match',
                'count' => count($matchedMessages),
                'ids' => $idList,
            ];

            // Send via Firebase
            // Firebase::messaging()->sendToDevice($user->fcm_token, $notification, $data);
            */

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('❌ Firebase send failed', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
