<?php

namespace App\Models;

use LadLib\Common\Database\MetaOfTableInDb;

class UserBalance_Meta extends MetaOfTableInDb
{
    public static $modelClass = UserBalance::class;
    public static $modelName = 'UserBalance';
    
    public static function getCoreFields()
    {
        return [
            'id' => 'ID',
            'user_id' => 'Người dùng',
            'balance' => 'Số dư (đ)',
            'total_recharged' => 'Tổng nạp (đ)',
            'total_spent' => 'Tổng chi (đ)',
            'status' => 'Trạng thái',
            'is_frozen' => 'Đã khóa',
            'low_balance_threshold' => 'Ngưỡng cảnh báo (đ)',
        ];
    }

    public static function _name($item, $typeGet = '')
    {
        if (!$item) return '';
        
        $status = $item->status == 1 ? '✅ Active' : '❌ Inactive';
        $frozen = $item->is_frozen ? '🔒 Frozen' : '🔓 Normal';
        
        return <<<HTML
            <div style="padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                <div><strong>User #{$item->user_id}</strong></div>
                <table style="width: 100%; margin-top: 8px; border-collapse: collapse;">
                    <tr style="background: #f5f5f5;">
                        <td style="padding: 8px; border: 1px solid #ddd;"><strong>Dư tiền</strong></td>
                        <td style="padding: 8px; border: 1px solid #ddd;"><strong>Tổng nạp</strong></td>
                        <td style="padding: 8px; border: 1px solid #ddd;"><strong>Tổng chi</strong></td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border: 1px solid #ddd;">₫{number_format($item->balance, 0, ',', '.')}</td>
                        <td style="padding: 8px; border: 1px solid #ddd;">₫{number_format($item->total_recharged, 0, ',', '.')}</td>
                        <td style="padding: 8px; border: 1px solid #ddd;">₫{number_format($item->total_spent, 0, ',', '.')}</td>
                    </tr>
                </table>
                <div style="margin-top: 8px; font-size: 12px;">
                    <span>$status</span> | <span>$frozen</span>
                </div>
            </div>
        HTML;
    }
}
