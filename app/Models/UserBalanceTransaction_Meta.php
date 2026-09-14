<?php

namespace App\Models;

use LadLib\Common\Database\MetaOfTableInDb;

class UserBalanceTransaction_Meta extends MetaOfTableInDb
{
    public static $modelClass = UserBalanceTransaction::class;
    public static $modelName = 'UserBalanceTransaction';

    public static function getCoreFields()
    {
        return [
            'id' => 'ID',
            'user_id' => 'Người dùng',
            'transaction_type' => 'Loại giao dịch',
            'service_type' => 'Dịch vụ',
            'amount' => 'Số tiền (đ)',
            'balance_after' => 'Dư sau (đ)',
            'transaction_date' => 'Ngày giờ',
        ];
    }

    public static function _name($item, $typeGet = '')
    {
        if (!$item) return '';

        $typeLabel = match($item->transaction_type) {
            'recharge' => '💳 Nạp tiền',
            'service_fee' => '📊 Chi dịch vụ',
            'refund' => '🔙 Hoàn lại',
            'adjustment' => '🔧 Điều chỉnh',
            'penalty' => '⚠️ Phạt',
            default => $item->transaction_type
        };

        $amountClass = $item->amount >= 0 ? 'color: green;' : 'color: red;';

        // Format amount for display
        $amountSign = $item->amount >= 0 ? '+' : '';
        $amountValue = abs($item->amount);
        $amountDisplay = $amountSign . '₫' . number_format($amountValue, 0, ',', '.');

        // Format balance for display
        $balanceDisplay = '₫' . number_format($item->balance_after, 0, ',', '.');

        return <<<HTML
            <div style="padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                <div><strong>Transaction #{$item->id}</strong> - User #{$item->user_id}</div>
                <table style="width: 100%; margin-top: 8px; border-collapse: collapse;">
                    <tr style="background: #f5f5f5;">
                        <td style="padding: 8px; border: 1px solid #ddd;"><strong>Loại</strong></td>
                        <td style="padding: 8px; border: 1px solid #ddd;"><strong>Số tiền</strong></td>
                        <td style="padding: 8px; border: 1px solid #ddd;"><strong>Dư sau</strong></td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border: 1px solid #ddd;">$typeLabel</td>
                        <td style="padding: 8px; border: 1px solid #ddd; $amountClass"><strong>$amountDisplay</strong></td>
                        <td style="padding: 8px; border: 1px solid #ddd;">$balanceDisplay</td>
                    </tr>
                </table>
                <div style="margin-top: 8px; font-size: 12px;">
                    <div>{$item->description}</div>
                    <div style="color: #666;">{$item->transaction_date}</div>
                </div>
            </div>
        HTML;
    }
}
