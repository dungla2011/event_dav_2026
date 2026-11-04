<?php
/**
 * Quản lý mã thiết bị cho app kiosk check-in (/api/app/v1).
 *
 *   php app_device.php list
 *   php app_device.php create "Kiosk sảnh A"     → in token (chỉ hiện 1 lần) + chuỗi QR để quét trong app
 *   php app_device.php revoke <id>
 *   php app_device.php enable <id>
 *
 * Token chỉ lưu dạng sha256 trong bảng app_v1_devices (DB của events.dav.edu.vn), bảng tự tạo lần đầu.
 */

$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'events.dav.edu.vn';

require '/var/www/html/vendor/autoload.php';
$app = require_once '/var/www/html/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

const BASE_URL = 'https://events.dav.edu.vn/api/app/v1';

if (!Schema::hasTable('app_v1_devices')) {
    Schema::create('app_v1_devices', function (Blueprint $t) {
        $t->increments('id');
        $t->string('name', 128);
        $t->char('token_hash', 64)->unique();
        $t->tinyInteger('enabled')->default(1);
        $t->dateTime('last_seen_at')->nullable();
        $t->string('last_ip', 45)->nullable();
        $t->timestamps();
    });
    echo "Đã tạo bảng app_v1_devices\n";
}

$cmd = $argv[1] ?? 'list';

switch ($cmd) {
    case 'create':
        $name = trim($argv[2] ?? '');
        if ($name === '') {
            exit("Thiếu tên thiết bị: php app_device.php create \"Kiosk sảnh A\"\n");
        }
        $token = 'dav_' . bin2hex(random_bytes(20));
        $id = DB::table('app_v1_devices')->insertGetId([
            'name' => $name,
            'token_hash' => hash('sha256', $token),
            'enabled' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        echo "Thiết bị #$id: $name\n";
        echo "Token (chỉ hiện lần này): $token\n";
        echo "Chuỗi QR cho app: " . json_encode(['token' => $token, 'base_url' => BASE_URL]) . "\n";
        break;

    case 'revoke':
    case 'enable':
        $id = (int)($argv[2] ?? 0);
        $n = DB::table('app_v1_devices')->where('id', $id)
            ->update(['enabled' => $cmd === 'enable' ? 1 : 0, 'updated_at' => date('Y-m-d H:i:s')]);
        echo $n ? "Đã " . ($cmd === 'enable' ? 'bật' : 'thu hồi') . " thiết bị #$id\n" : "Không thấy thiết bị #$id\n";
        break;

    default:
        foreach (DB::table('app_v1_devices')->orderBy('id')->get() as $d) {
            printf("#%-3d %-30s %s  lần cuối: %s %s\n", $d->id, $d->name, $d->enabled ? 'BẬT' : 'TẮT',
                $d->last_seen_at ?? '-', $d->last_ip ?? '');
        }
}
