<?php

use Illuminate\Support\Facades\DB;

// ========== SETUP CHỈ KHI STANDALONE ==========

error_reporting(E_ALL);
ini_set('display_errors', 1);
define('DEF_TOOL_CMS', 1);
$_SERVER['SERVER_NAME'] = 'v5.mytree.vn';
require_once __DIR__.'/../../index.php';


// ========== CONVERT TREE NODES XY IDS ==========

function convertTreeNodesXY() {
    echo "🔄 Bắt đầu convert tree_nodes_xy IDs...\n";
    echo "=" . str_repeat("=", 50) . "\n";

    // Lấy tất cả bản ghi có tree_nodes_xy không null
    $records = DB::table('my_tree_infos')
        ->select('id', 'tree_nodes_xy')
        ->whereNotNull('tree_nodes_xy')
        ->where('tree_nodes_xy', '!=', '')
        ->where('tree_nodes_xy', '!=', '[]')
        ->get();

    echo "📋 Tìm thấy " . $records->count() . " bản ghi có tree_nodes_xy\n\n";

    $successCount = 0;
    $errorCount = 0;
    $totalNodesConverted = 0;
    $notFoundIds = [];

    foreach ($records as $record) {
        echo "🔍 Xử lý my_tree_infos.id = {$record->id}\n";

        try {
            // Decode JSON
            $treeNodes = json_decode($record->tree_nodes_xy, true);

            if (!is_array($treeNodes)) {
                echo "   ⚠️  tree_nodes_xy không phải JSON array hợp lệ\n";
                continue;
            }

            echo "   📊 Tìm thấy " . count($treeNodes) . " nodes\n";

            $hasChanges = false;
            $currentRecordNotFound = [];

            // Duyệt qua từng node
            foreach ($treeNodes as &$node) {
                if (!isset($node['id'])) {
                    continue;
                }

                $oldId = $node['id'];

                // Tìm new ID trong bảng gia_phas
                $giaPha = DB::table('gia_phas')
                    ->select('id')
                    ->where('id__', $oldId)
                    ->first();

                if ($giaPha) {
                    $newId = $giaPha->id;
                    $node['id'] = $newId;
                    $hasChanges = true;
                    $totalNodesConverted++;

                    echo "   ✅ {$oldId} -> {$newId}\n";
                } else {
                    $currentRecordNotFound[] = $oldId;
                    echo "   ❌ Không tìm thấy gia_phas.id__ = {$oldId}\n";
                }
            }

            // Cập nhật nếu có thay đổi
            if ($hasChanges) {
                $newTreeNodesXY = json_encode($treeNodes, JSON_UNESCAPED_UNICODE);

                DB::table('my_tree_infos')
                    ->where('id', $record->id)
                    ->update(['tree_nodes_xy' => $newTreeNodesXY]);

                echo "   💾 Đã cập nhật tree_nodes_xy\n";
                $successCount++;
            } else {
                echo "   ⚠️  Không có thay đổi nào\n";
            }

            // Collect not found IDs
            if (!empty($currentRecordNotFound)) {
                $notFoundIds = array_merge($notFoundIds, $currentRecordNotFound);
            }

        } catch (Exception $e) {
            echo "   ❌ Lỗi: " . $e->getMessage() . "\n";
            $errorCount++;
        }

        echo "\n";
    }

    // Tổng kết
    echo "📊 KẾT QUẢ:\n";
    echo "=" . str_repeat("=", 30) . "\n";
    echo "✅ Thành công: {$successCount} bản ghi\n";
    echo "❌ Lỗi: {$errorCount} bản ghi\n";
    echo "🔄 Tổng nodes converted: {$totalNodesConverted}\n";
    echo "📋 Tổng nodes không tìm thấy: " . count($notFoundIds) . "\n";

    // Hiển thị IDs không tìm thấy (unique)
    if (!empty($notFoundIds)) {
        $uniqueNotFound = array_unique($notFoundIds);
        echo "\n🔴 DANH SÁCH ID KHÔNG TÌM THẤY:\n";
        echo "-" . str_repeat("-", 40) . "\n";

        foreach ($uniqueNotFound as $id) {
            echo "   - {$id}\n";
        }

        echo "\n💡 Kiểm tra các ID này trong bảng gia_phas.id__\n";
    }

    return [
        'success' => $successCount,
        'errors' => $errorCount,
        'total_nodes_converted' => $totalNodesConverted,
        'not_found_ids' => array_unique($notFoundIds)
    ];
}

// ========== HELPER FUNCTIONS ==========

function previewTreeNodesXY($limit = 5) {
    echo "👀 PREVIEW tree_nodes_xy data (top {$limit}):\n";
    echo "=" . str_repeat("=", 50) . "\n";

    $records = DB::table('my_tree_infos')
        ->select('id', 'tree_nodes_xy')
        ->whereNotNull('tree_nodes_xy')
        ->where('tree_nodes_xy', '!=', '')
        ->where('tree_nodes_xy', '!=', '[]')
        ->limit($limit)
        ->get();

    foreach ($records as $record) {
        echo "🗂️  my_tree_infos.id = {$record->id}\n";

        try {
            $treeNodes = json_decode($record->tree_nodes_xy, true);

            if (is_array($treeNodes)) {
                echo "   📊 " . count($treeNodes) . " nodes:\n";

                foreach (array_slice($treeNodes, 0, 3) as $node) {
                    $id = $node['id'] ?? 'no-id';
                    $x = $node['x'] ?? 'no-x';
                    $y = $node['y'] ?? 'no-y';
                    echo "     - id: {$id}, x: {$x}, y: {$y}\n";
                }

                if (count($treeNodes) > 3) {
                    echo "     ... và " . (count($treeNodes) - 3) . " nodes khác\n";
                }
            } else {
                echo "   ❌ JSON không hợp lệ\n";
            }

        } catch (Exception $e) {
            echo "   ❌ Lỗi decode: " . $e->getMessage() . "\n";
        }

        echo "\n";
    }
}

function validateGiaPhasMapping() {
    echo "🔍 Kiểm tra mapping gia_phas.id__ -> id:\n";
    echo "=" . str_repeat("=", 40) . "\n";

    $totalGiaPhas = DB::table('gia_phas')->count();
    $hasIdField = DB::table('gia_phas')->whereNotNull('id__')->count();

    echo "📊 Tổng gia_phas: " . number_format($totalGiaPhas) . "\n";
    echo "📊 Có id__: " . number_format($hasIdField) . "\n";

    // Sample mapping
    echo "\n📋 Sample mapping (top 5):\n";
    $samples = DB::table('gia_phas')
        ->select('id', 'id__', 'name')
        ->whereNotNull('id__')
        ->limit(5)
        ->get();

    foreach ($samples as $sample) {
        echo "   {$sample->id__} -> {$sample->id} ({$sample->name})\n";
    }

    echo "\n";
}

// ========== MAIN EXECUTION ==========

echo "🚀 TREE NODES XY ID CONVERTER\n";
echo "=" . str_repeat("=", 50) . "\n\n";

try {
    // Validate data
    validateGiaPhasMapping();

    // Preview data
    previewTreeNodesXY(3);

    echo "📝 CHỌN HÀNH ĐỘNG:\n";
    echo "1. Preview data\n";
    echo "2. Validate gia_phas mapping\n";
    echo "3. Execute conversion\n";

    if(0)
    if (php_sapi_name() === 'cli') {
        echo "Nhập lựa chọn (1-3): ";
        $choice = trim(fgets(STDIN));
    } else {
        // Web mode - default to preview
        $choice = '1';
        echo "🔍 Chạy preview (web mode)...\n\n";
    }

    $choice = 3;

    switch ($choice) {
        case '1':
            previewTreeNodesXY(10);
            break;

        case '2':
            validateGiaPhasMapping();
            break;

        case '3':
            if (php_sapi_name() === 'cli') {
                echo "\n⚠️  Bạn có chắc muốn convert? (yes/no): ";
//                $confirm = trim(fgets(STDIN));

                convertTreeNodesXY();

//                if (strtolower($confirm) === 'yes') {
//
//                } else {
//                    echo "❌ Conversion bị hủy\n";
//                }
            } else {
                echo "❌ Conversion mode chỉ khả dụng trong CLI\n";
            }
            break;

        default:
            echo "❌ Lựa chọn không hợp lệ\n";
            break;
    }

} catch (Exception $e) {
    echo "❌ Lỗi fatal: " . $e->getMessage() . "\n";
}

echo "\n✅ Script hoàn thành\n";

?>
