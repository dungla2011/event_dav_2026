<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Trang quản trị mã thiết bị cho app kiosk check-in (/api/app/v1).
 * Cùng bảng app_v1_devices với task-cli/site/events-dav/app_device.php; token chỉ lưu sha256,
 * nên token mới chỉ hiện đúng một lần ngay sau khi cấp.
 */
class AppDeviceAdminController extends Controller
{
    const BASE_URL = 'https://events.dav.edu.vn/api/app/v1';

    public function index()
    {
        $devices = DB::table('app_v1_devices')->orderByDesc('id')->get();
        $new = session('app_device_new');
        $qr = null;
        if ($new) {
            $payload = json_encode(['token' => $new['token'], 'base_url' => self::BASE_URL]);
            $qr = (new \SimpleSoftwareIO\QrCode\Generator)->size(260)->margin(1)->generate($payload);
        }

        return view('admin.app-device.index', ['devices' => $devices, 'new' => $new, 'qr' => $qr]);
    }

    public function store(Request $request)
    {
        $name = trim((string) $request->input('name'));
        if ($name === '' || mb_strlen($name) > 128) {
            return back()->with('app_device_error', 'Tên thiết bị phải có từ 1 đến 128 ký tự.');
        }
        $token = 'dav_'.bin2hex(random_bytes(20));
        $id = DB::table('app_v1_devices')->insertGetId([
            'name' => $name,
            'token_hash' => hash('sha256', $token),
            'enabled' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.app-device.index')
            ->with('app_device_new', ['id' => $id, 'name' => $name, 'token' => $token]);
    }

    public function toggle($id)
    {
        $d = DB::table('app_v1_devices')->where('id', (int) $id)->first();
        if ($d) {
            DB::table('app_v1_devices')->where('id', $d->id)
                ->update(['enabled' => $d->enabled ? 0 : 1, 'updated_at' => now()]);
        }

        return redirect()->route('admin.app-device.index');
    }
}
