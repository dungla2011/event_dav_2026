<?php
require_once 'lib_taxi.php';

echo "<h2>🧪 Test Word Matching Function</h2>";

// Test case cụ thể mà user hỏi
$testContent = "vn385 hạ rồi - hàng muối ck 300k sedan ki10 kvf5";
$testKeyword = "vn";

echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
echo "<h3>Test Case: Tìm từ 'vn' trong chuỗi</h3>";
echo "<strong>Nội dung:</strong> \"$testContent\"<br>";
echo "<strong>Từ khóa tìm:</strong> \"$testKeyword\"<br><br>";

$result = isWordMatch($testContent, $testKeyword);
if ($result) {
    echo "<span style='color: green; font-weight: bold;'>✅ KẾT QUẢ: MATCH - Từ 'vn' được tìm thấy như một từ độc lập</span>";
} else {
    echo "<span style='color: red; font-weight: bold;'>❌ KẾT QUẢ: NO MATCH - Từ 'vn' KHÔNG được tìm thấy như một từ độc lập</span>";
}

echo "<br><br><strong>Giải thích:</strong><br>";
echo "Hàm isWordMatch() sử dụng word boundary để tìm kiếm từ độc lập.<br>";
echo "Trong 'vn385', từ 'vn' không đứng độc lập vì sau nó là số '3' (thuộc nhóm ký tự từ).<br>";
echo "Do đó, 'vn' sẽ KHÔNG match với 'vn385'.<br>";
echo "</div>";

// Test thêm các trường hợp khác
echo "<h3>Các test case khác:</h3>";
$additionalTests = [
    "đi vn rồi" => "vn",
    "vn-airlines" => "vn", 
    "vn 385" => "vn",
    "vn, 385" => "vn",
    "385vn" => "vn",
    "vn385vn" => "vn"
];

foreach ($additionalTests as $content => $keyword) {
    $match = isWordMatch($content, $keyword);
    $status = $match ? "✅ MATCH" : "❌ NO MATCH";
    echo "<div style='margin: 10px 0; padding: 8px; background: #f0f0f0;'>";
    echo "<strong>Nội dung:</strong> \"$content\" → Tìm \"$keyword\" → <strong>$status</strong>";
    echo "</div>";
}

echo "<hr>";
echo "<h3>🔍 Chạy toàn bộ test suite:</h3>";
testWordMatching();
?> 