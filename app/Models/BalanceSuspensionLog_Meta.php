<?php

namespace App\Models;

use LadLib\Common\Database\MetaOfTableInDb;

class BalanceSuspensionLog_Meta extends MetaOfTableInDb
{
    public static $modelClass = BalanceSuspensionLog::class;
    public static $modelName = 'BalanceSuspensionLog';

    public static function getCoreFields()
    {
        return [
            'id' => 'ID',
            'user_id' => 'Người dùng',
            'reason' => 'Lý do',
            'suspended_at' => 'Tạm dừng lúc',
            'resumed_at' => 'Gỡ tạm dừng lúc',
            'duration_minutes' => 'Thời lượng (phút)',
        ];
    }

    public static function _name($item, $typeGet = '')
    {
        if (!$item) return '';

        $status = $item->resumed_at ? '✅ Đã gỡ' : '🚫 Đang tạm dừng';
        $durationText = $item->duration_minutes ? ($item->duration_minutes / 60) . ' giờ' : 'Đang tạm dừng';

        return <<<HTML
            <div style="padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                <div><strong>Suspension Log #{$item->id}</strong> - User #{$item->user_id}</div>
                <table style="width: 100%; margin-top: 8px; border-collapse: collapse;">
                    <tr style="background: #f5f5f5;">
                        <td style="padding: 8px; border: 1px solid #ddd;"><strong>Trạng thái</strong></td>
                        <td style="padding: 8px; border: 1px solid #ddd;"><strong>Lý do</strong></td>
                        <td style="padding: 8px; border: 1px solid #ddd;"><strong>Thời lượng</strong></td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border: 1px solid #ddd;">$status</td>
                        <td style="padding: 8px; border: 1px solid #ddd;">{$item->reason}</td>
                        <td style="padding: 8px; border: 1px solid #ddd;">$durationText</td>
                    </tr>
                </table>
                <div style="margin-top: 8px; font-size: 12px;">
                    <div>Tạm dừng: {$item->suspended_at}</div>
                    {$item->resumed_at}
                </div>
            </div>
        HTML;
    }
}
