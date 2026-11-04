<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Xác thực máy kiosk của /api/app/v1 bằng "Authorization: Bearer <token>".
 * Bảng app_v1_devices chỉ lưu sha256 của token; cấp / thu hồi bằng
 * task-cli/site/events-dav/app_device.php.
 */
class AppV1DeviceAuth
{
    const ATTR = 'app_v1_device';

    public function handle(Request $request, Closure $next)
    {
        $token = (string)$request->bearerToken();
        $dev = null;
        if (strlen($token) >= 16) {
            try {
                $dev = DB::table('app_v1_devices')
                    ->where('token_hash', hash('sha256', $token))
                    ->where('enabled', 1)
                    ->first();
            } catch (\Throwable $e) {
                $dev = null;
            }
        }
        if (!$dev) {
            return response()->json(['status' => 'fail', 'error' => 'unauthorized'], 401);
        }
        // Ghi lần dùng gần nhất, tối đa 1 lần / phút cho đỡ ghi DB mỗi lượt quét.
        if (!$dev->last_seen_at || strtotime($dev->last_seen_at) < time() - 60) {
            DB::table('app_v1_devices')->where('id', $dev->id)
                ->update(['last_seen_at' => date('Y-m-d H:i:s'), 'last_ip' => substr((string)$request->ip(), 0, 45)]);
        }
        $request->attributes->set(self::ATTR, $dev);

        return $next($request);
    }
}
