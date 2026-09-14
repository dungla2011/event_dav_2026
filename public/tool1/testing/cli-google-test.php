<?php
/**
 * CLI test Google Sheets/Drive/Docs
 * Chạy: php cli-google-test.php [action]
 *   action: sheet_create | sheet_write <sheet_id> | doc_create | drive_list
 */

// Load composer autoload (không cần Laravel)
$autoload = __DIR__ . '/../../../vendor/autoload.php';
if (!file_exists($autoload)) {
    exit("Không tìm thấy vendor/autoload.php\n");
}
require_once $autoload;

// Tìm credential file
$credPaths = [
    '/var/glx/credentials_dungbkhn_google_sheet.json',         // Linux server
    'C:/glx/credentials_dungbkhn_google_sheet.json',           // Windows fallback
    __DIR__ . '/credentials_google.json',                       // Cùng thư mục
    __DIR__ . '/../../../config/credentials_google.json',       // Laravel config/
];

$jsonFile = null;
foreach ($credPaths as $p) {
    if (file_exists($p)) {
        $jsonFile = $p;
        break;
    }
}

if (!$jsonFile) {
    echo "=== LỖI: Không tìm thấy credential file ===\n";
    echo "Đặt file service account JSON vào một trong các đường dẫn sau:\n";
    foreach ($credPaths as $p) {
        echo "  - $p\n";
    }
    echo "\nCách lấy file: Google Cloud Console > IAM & Admin > Service Accounts > Keys > Add Key > JSON\n";
    exit(1);
}

echo "Credential: $jsonFile\n";

$action   = $argv[1] ?? 'help';
$sheetId  = $argv[2] ?? '';

// ===== HELP =====
if ($action === 'help' || $action === '--help') {
    echo "\nCách dùng:\n";
    echo "  php cli-google-test.php sheet_create          # Tạo Sheet mới\n";
    echo "  php cli-google-test.php sheet_write <ID>      # Ghi dữ liệu vào Sheet\n";
    echo "  php cli-google-test.php doc_create            # Tạo Google Doc mới\n";
    echo "  php cli-google-test.php drive_list            # Liệt kê file trên Drive\n";
    exit(0);
}

// ===== KHỞI TẠO CLIENT =====
function makeClient(array $scopes, string $jsonFile): Google_Client
{
    $client = new Google_Client();
    $client->setApplicationName('Laravel01 CLI Test');
    $client->setScopes($scopes);
    $client->setAccessType('offline');
    $client->setAuthConfig($jsonFile);
    return $client;
}

// ===== 1. TẠO SHEET MỚI =====
if ($action === 'sheet_create') {
    echo "\n[sheet_create] Đang tạo Google Sheet mới...\n";
    try {
        $client  = makeClient([
            \Google_Service_Sheets::SPREADSHEETS,
            \Google_Service_Drive::DRIVE,
        ], $jsonFile);
        $service = new \Google_Service_Sheets($client);

        $spreadsheet = new \Google_Service_Sheets_Spreadsheet([
            'properties' => ['title' => 'CLI Test Sheet - ' . date('Y-m-d H:i:s')],
            'sheets'     => [['properties' => ['title' => 'Sheet1']]],
        ]);

        $result  = $service->spreadsheets->create($spreadsheet, ['fields' => 'spreadsheetId,spreadsheetUrl']);
        $id      = $result->getSpreadsheetId();
        $url     = $result->getSpreadsheetUrl();

        echo "OK! Sheet tạo thành công.\n";
        echo "  Sheet ID : $id\n";
        echo "  URL      : $url\n";
        echo "\nGhi dữ liệu vào sheet này:\n";
        echo "  php cli-google-test.php sheet_write $id\n";

    } catch (\Exception $e) {
        echo "LỖI: " . $e->getMessage() . "\n";
        echo "Kiểm tra Google Drive API và Sheets API đã bật trong Cloud Console chưa.\n";
    }
    exit;
}

// ===== 2. GHI DỮ LIỆU VÀO SHEET =====
if ($action === 'sheet_write') {
    if (!$sheetId) {
        exit("Thiếu sheet_id. Dùng: php cli-google-test.php sheet_write <SHEET_ID>\n");
    }
    echo "\n[sheet_write] Ghi dữ liệu vào Sheet: $sheetId\n";
    try {
        $client  = makeClient([\Google_Service_Sheets::SPREADSHEETS], $jsonFile);
        $service = new \Google_Service_Sheets($client);

        $values = [
            ['STT', 'Họ tên',       'Email',               'Điểm', 'Ghi chú'],
            [1,     'Nguyễn Văn A', 'a@example.com',       9.5,    'Xuất sắc'],
            [2,     'Trần Thị B',   'b@example.com',       8.0,    'Giỏi'],
            [3,     'Lê Văn C',     'c@example.com',       7.5,    'Khá'],
            [4,     'Phạm Thị D',   'd@example.com',       6.0,    'Trung bình'],
            ['',    'Ghi lúc:',     date('Y-m-d H:i:s'),   '',     ''],
        ];

        $body   = new \Google_Service_Sheets_ValueRange(['values' => $values]);
        $result = $service->spreadsheets_values->update(
            $sheetId, 'Sheet1!A1', $body, ['valueInputOption' => 'USER_ENTERED']
        );

        echo "OK! Ghi thành công.\n";
        echo "  Cells cập nhật : " . $result->getUpdatedCells() . "\n";
        echo "  Rows cập nhật  : " . $result->getUpdatedRows() . "\n";

        // Đọc lại
        $read = $service->spreadsheets_values->get($sheetId, 'Sheet1!A1:E6');
        $rows = $read->getValues();
        echo "\nDữ liệu hiện tại:\n";
        foreach ($rows as $row) {
            echo '  ' . implode(' | ', array_map(fn($c) => str_pad($c, 16), $row)) . "\n";
        }

    } catch (\Exception $e) {
        echo "LỖI: " . $e->getMessage() . "\n";
    }
    exit;
}

// ===== 3. TẠO GOOGLE DOC =====
if ($action === 'doc_create') {
    echo "\n[doc_create] Đang tạo Google Doc mới...\n";
    try {
        $client      = makeClient([
            'https://www.googleapis.com/auth/documents',
            \Google_Service_Drive::DRIVE,
        ], $jsonFile);
        $docsService = new \Google_Service_Docs($client);

        $doc        = new \Google_Service_Docs_Document(['title' => 'CLI Test Doc - ' . date('Y-m-d H:i:s')]);
        $created    = $docsService->documents->create($doc);
        $docId      = $created->getDocumentId();

        // Ghi nội dung
        $requests = [
            new \Google_Service_Docs_Request([
                'insertText' => [
                    'location' => ['index' => 1],
                    'text'     => "Xin chào!\n\nDocument được tạo từ CLI PHP.\nNgày: " . date('Y-m-d H:i:s') . "\n\n- Item 1\n- Item 2\n- Item 3\n",
                ],
            ]),
        ];
        $batch = new \Google_Service_Docs_BatchUpdateDocumentRequest(['requests' => $requests]);
        $docsService->documents->batchUpdate($docId, $batch);

        echo "OK! Doc tạo thành công.\n";
        echo "  Doc ID : $docId\n";
        echo "  URL    : https://docs.google.com/document/d/$docId/edit\n";

    } catch (\Exception $e) {
        echo "LỖI: " . $e->getMessage() . "\n";
        echo "Kiểm tra Google Docs API đã bật trong Cloud Console chưa.\n";
    }
    exit;
}

// ===== 4. LIỆT KÊ FILE DRIVE =====
if ($action === 'drive_list') {
    echo "\n[drive_list] Liệt kê file trên Drive (service account)...\n";
    try {
        $client       = makeClient([\Google_Service_Drive::DRIVE_READONLY], $jsonFile);
        $driveService = new \Google_Service_Drive($client);

        $results = $driveService->files->listFiles([
            'pageSize' => 20,
            'fields'   => 'files(id, name, mimeType, createdTime)',
            'orderBy'  => 'createdTime desc',
        ]);
        $files = $results->getFiles();

        if (empty($files)) {
            echo "Không có file nào. (Service account chỉ thấy file do chính nó tạo)\n";
        } else {
            echo str_pad('Tên file', 40) . str_pad('Loại', 20) . "Ngày tạo\n";
            echo str_repeat('-', 80) . "\n";
            foreach ($files as $file) {
                $type = str_replace('application/vnd.google-apps.', '', $file->getMimeType());
                echo str_pad($file->getName(), 40) . str_pad($type, 20) . $file->getCreatedTime() . "\n";
            }
        }
    } catch (\Exception $e) {
        echo "LỖI: " . $e->getMessage() . "\n";
    }
    exit;
}

echo "Action không hợp lệ: $action\nDùng --help để xem hướng dẫn.\n";
