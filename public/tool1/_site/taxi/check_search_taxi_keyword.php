<?php


require '/var/www/html/vendor/autoload.php';
$app = require_once '/var/www/html/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);


/**
 * Test script cho TaxiUser::searchTaxiKeyword()
 *
 * Usage: Truy cập qua browser
 */


use App\Models\TaxiUser;

// Default messages
$defaultMessagesArray = [
    'Cần taxi đi sân bay Nội Bài',
    'Tôi cần TAXI gấp về nhà',
    'Gọi taxiabc cho tôi',
    'Taxation policy is important',
    'Xe taxi 7 chỗ có không?',
];

$defaultKeywords = "taxi,xe,hà nội";

// Nhận data từ form
$isSubmit = isset($_POST['search']);
$keywordsInput = $_POST['keywords'] ?? $defaultKeywords;

// Nhận messages từ input array
$testMessages = [];
if ($isSubmit && isset($_POST['msg'])) {
    foreach ($_POST['msg'] as $index => $content) {
        $content = trim($content);
        if (!empty($content)) {
            $testMessages[$index] = $content;
        }
    }
} else {
    // Dùng default
    $index = 1;
    foreach ($defaultMessagesArray as $content) {
        $testMessages[$index] = $content;
        $index++;
    }
}

// Thực hiện search
$startTime = microtime(true);
$results = TaxiUser::searchTaxiKeyword($testMessages, $keywordsInput);
$duration = round((microtime(true) - $startTime) * 1000, 2);

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Taxi Keyword Search</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background: #f5f5f5;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        h1 {
            color: #333;
            margin-top: 0;
            border-bottom: 3px solid #4CAF50;
            padding-bottom: 10px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
            color: #333;
        }

        textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            resize: vertical;
            transition: border-color 0.3s;
            height: 80px;
        }

        textarea:focus {
            outline: none;
            border-color: #4CAF50;
        }

        .btn {
            background: #4CAF50;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn:hover {
            background: #45a049;
        }

        .btn-add {
            background: #2196F3;
            color: white;
            padding: 8px 20px;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
            margin-top: 10px;
        }

        .btn-add:hover {
            background: #1976D2;
        }

        .stat {
            display: inline-block;
            background: #2196F3;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            margin-right: 10px;
            margin-bottom: 10px;
            font-weight: bold;
            font-size: 14px;
        }

        .info-box {
            background: #E3F2FD;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            border-left: 4px solid #2196F3;
        }

        .info-box ul {
            margin: 5px 0;
            padding-left: 20px;
        }

        /* Table styles */
        .test-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .test-table thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .test-table th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .test-table tbody tr {
            border-bottom: 1px solid #e0e0e0;
            transition: background 0.2s;
        }

        .test-table tbody tr:hover {
            background: #f5f5f5;
        }

        .test-table tbody tr.match {
            background: #E8F5E9;
        }

        .test-table tbody tr.no-match {
            background: #FFEBEE;
        }

        .test-table td {
            padding: 12px 15px;
            vertical-align: middle;
        }

        .test-table td:first-child {
            width: 60px;
            text-align: center;
            font-weight: bold;
            color: #666;
        }

        .test-table input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            transition: border-color 0.3s;
        }

        .test-table input[type="text"]:focus {
            outline: none;
            border-color: #4CAF50;
        }

        .result-cell {
            font-size: 14px;
            line-height: 1.6;
        }

        .result-cell b {
            background: #FFEB3B;
            padding: 2px 6px;
            border-radius: 3px;
            color: #d32f2f;
            font-weight: bold;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }

        .status-match {
            background: #4CAF50;
            color: white;
        }

        .status-no-match {
            background: #F44336;
            color: white;
        }

        .divider {
            height: 2px;
            background: linear-gradient(to right, #4CAF50, #2196F3);
            margin: 30px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Test Taxi Keyword Search</h1>

        <div class="info-box">
            <strong>ℹ️ Hướng dẫn:</strong>
            <ul>
                <li><strong>Messages:</strong> Mỗi dòng là 1 tin nhắn</li>
                <li><strong>Keywords:</strong> Các từ khóa cách nhau bởi dấu phẩy (,)</li>
                <li>Tìm kiếm <strong>whole word</strong> (không khớp substring)</li>
                <li>Tìm kiếm <strong>case-insensitive</strong> (không phân biệt hoa thường)</li>
                <li>Kết quả highlight từ khóa match bằng <b style="background: #FFEB3B; padding: 2px 4px;">màu vàng</b></li>
            </ul>
        </div>

        <form method="POST">
            <div class="form-group">
                <label for="keywords">🔑 Keywords (cách nhau bởi dấu phẩy):</label>
                <textarea name="keywords" id="keywords"><?php echo htmlspecialchars($keywordsInput); ?></textarea>
            </div>


        <!-- Test Table -->
        <table class="test-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th style="width: 45%;">Message Input</th>
                        <th style="width: 10%;">Status</th>
                        <th style="width: 45%;">Kết quả Match</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($testMessages as $id => $message):
                        $hasMatch = isset($results[$id]);
                        $rowClass = $hasMatch ? 'match' : 'no-match';
                    ?>
                        <tr class="<?php echo $rowClass; ?>">
                            <td><?php echo $id; ?></td>
                            <td>
                                <input type="text"
                                       name="msg[<?php echo $id; ?>]"
                                       value="<?php echo htmlspecialchars($message); ?>"
                                       placeholder="Nhập tin nhắn test...">
                            </td>
                            <td style="text-align: center;">
                                <?php if ($hasMatch): ?>
                                    <span class="status-badge status-match">✅ Match</span>
                                <?php else: ?>
                                    <span class="status-badge status-no-match">❌ No</span>
                                <?php endif; ?>
                            </td>
                            <td class="result-cell">
                                <?php
                                if ($hasMatch) {
                                    echo $results[$id];
                                } else {
                                    echo '<span style="color: #999;">Không match với keywords</span>';
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div style="margin-top: 20px; text-align: center;">
                <button type="submit" name="search" class="btn">🔍 Search</button>
                <button type="button" class="btn-add" onclick="addNewRow()">➕ Add Row</button>
            </div>
        </form>

        <script>
            function addNewRow() {
                const tbody = document.querySelector('.test-table tbody');
                const rowCount = tbody.querySelectorAll('tr').length;
                const newId = rowCount + 1;

                const newRow = document.createElement('tr');
                newRow.className = 'no-match';
                newRow.innerHTML = `
                    <td>${newId}</td>
                    <td>
                        <input type="text"
                               name="msg[${newId}]"
                               value=""
                               placeholder="Nhập tin nhắn test...">
                    </td>
                    <td style="text-align: center;">
                        <span class="status-badge status-no-match">❌ No</span>
                    </td>
                    <td class="result-cell">
                        <span style="color: #999;">Chưa search</span>
                    </td>
                `;

                tbody.appendChild(newRow);
            }
        </script>

    </div>
</body>
</html>
<?php
exit; // Dừng tại đây, không chạy code test DB cũ
$userId = 1;

echo "<br>\nUser ID: $userId\n\n";

// 1. Lấy keywords từ DB
$keywords = TaxiUser::getKeywordSearch($userId);
echo "<br>\nKeywords từ DB:\n";
echo $keywords . "\n\n";

if (empty($keywords)) {
    echo "<br>\n⚠️  User chưa có keywords! Vui lòng thêm MonitorItem cho user này.\n";
    exit;
}

// 2. Lấy tin nhắn cần search
echo "<br>\nĐang lấy tin nhắn từ DB...\n";
$messages = TaxiUser::getMessageNeedSearch($userId);
echo "<br>\nSố tin nhắn cần search: " . count($messages) . "\n\n";

if (empty($messages)) {
    echo "<br>\n⚠️  Không có tin nhắn nào cần search!\n";
    exit;
}

// 3. Thực hiện search
echo "<br>\nĐang search...\n";
$startTime = microtime(true);

$results = TaxiUser::searchTaxiKeyword($messages, $keywords);

$duration = round((microtime(true) - $startTime) * 1000, 2);

echo "<br>\nThời gian: {$duration}ms\n";
echo "<br>\nTìm thấy: " . count($results) . " tin nhắn\n\n";

if (!empty($results)) {
    echo "<br>\n=== KẾT QUẢ (Top 10) ===\n\n";

    $count = 0;
    foreach ($results as $id => $contentHighlighted) {
        $count++;
        if ($count > 10) break;

        echo "<br>\n[$id] $contentHighlighted\n\n";
    }

    if (count($results) > 10) {
        echo "<br>\n... và " . (count($results) - 10) . " tin nhắn khác\n";
    }
} else {
    echo "<br>\nKhông tìm thấy tin nhắn nào match với keywords!\n";
}

echo "<br>\n\n\n";
echo "<br>\n==========================================================\n";
echo "<br>\n=== TEST 3: WORD BOUNDARY DEMO ===\n";
echo "<br>\n==========================================================\n\n";

// Demo: Giải thích cách match nguyên word
$testCases = [
    ['content' => 'Cần taxi đi sân bay', 'keyword' => 'taxi', 'should_match' => true],
    ['content' => 'Cần taxiabc đi sân bay', 'keyword' => 'taxi', 'should_match' => false],
    ['content' => 'Tôi cần TAXI gấp', 'keyword' => 'taxi', 'should_match' => true],
    ['content' => 'Gọi taxi về nhà', 'keyword' => 'taxi', 'should_match' => true],
    ['content' => 'Taxation policy', 'keyword' => 'taxi', 'should_match' => false],
    ['content' => 'Đi Hà Nội không?', 'keyword' => 'hà nội', 'should_match' => true],
    ['content' => 'Thành phố Hà Nội', 'keyword' => 'hà nội', 'should_match' => true],
];

foreach ($testCases as $test) {
    $testData = [1 => $test['content']];
    $result = TaxiUser::searchTaxiKeyword($testData, $test['keyword']);

    $matched = !empty($result);
    $expectedIcon = $test['should_match'] ? '✅' : '❌';
    $actualIcon = $matched ? '✅' : '❌';
    $status = ($matched == $test['should_match']) ? '✅ PASS' : '❌ FAIL';

    echo "<br>\n{$status}\n";
    echo "<br>\n  Content: \"{$test['content']}\"\n";
    echo "<br>\n  Keyword: \"{$test['keyword']}\"\n";
    echo "<br>\n  Expected: " . ($test['should_match'] ? 'Match' : 'No match') . " {$expectedIcon}\n";
    echo "<br>\n  Actual: " . ($matched ? 'Match' : 'No match') . " {$actualIcon}\n";

    if ($matched) {
        echo "<br>\n  Result: " . reset($result) . "\n";
    }

    echo "<br>\n\n";
}

echo "<br>\n==========================================================\n";
echo "<br>\nTest completed!\n";
echo "<br>\n==========================================================";
