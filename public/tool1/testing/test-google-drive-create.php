<?php

/**
 * Test tạo và edit file Google Sheet + Google Doc
 * Dùng service account credential có sẵn: /var/glx/credentials_dungbkhn_google_sheet.json
 *
 * Yêu cầu scope trong Google Cloud Console:
 *   - Google Sheets API
 *   - Google Docs API
 *   - Google Drive API
 */

$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'hateco.mytree.vn';
error_reporting(E_ALL);
ini_set('display_errors', 1);
define('DEF_TOOL_CMS', 1);

require_once '../../index.php';

$jsonFile = '/var/glx/credentials_dungbkhn_google_sheet.json';

if (!file_exists($jsonFile)) {
    exit('Credential file not found: ' . $jsonFile);
}

$action = $_GET['action'] ?? 'menu';

// ===== MENU =====
if ($action === 'menu') {
    echo '<h2>Test Google Drive API</h2>';
    echo '<ul>';
    echo '<li><a href="?action=create_sheet">1. Tạo Google Sheet mới</a></li>';
    echo '<li><a href="?action=write_sheet&sheet_id=">2. Ghi dữ liệu vào Sheet (cần sheet_id)</a></li>';
    echo '<li><a href="?action=create_doc">3. Tạo Google Doc mới</a></li>';
    echo '<li><a href="?action=list_drive">4. Liệt kê file trên Drive</a></li>';
    echo '</ul>';

    $sheetId = $_GET['sheet_id'] ?? '';
    if ($sheetId) {
        echo '<p>Sheet ID hiện tại: <strong>' . htmlspecialchars($sheetId) . '</strong></p>';
        echo '<p><a href="?action=write_sheet&sheet_id=' . urlencode($sheetId) . '">Ghi dữ liệu vào sheet này</a></p>';
    }
    exit;
}

// ===== KHỞI TẠO CLIENT =====
function makeClient(array $scopes): Google_Client
{
    global $jsonFile;
    $client = new Google_Client();
    $client->setApplicationName('Laravel01 Google Drive Test');
    $client->setScopes($scopes);
    $client->setAccessType('offline');
    $client->setAuthConfig($jsonFile);
    return $client;
}

// ===== 1. TẠO GOOGLE SHEET MỚI =====
if ($action === 'create_sheet') {
    try {
        $client = makeClient([
            \Google_Service_Sheets::SPREADSHEETS,
            \Google_Service_Drive::DRIVE,
        ]);
        $service = new \Google_Service_Sheets($client);

        $spreadsheet = new \Google_Service_Sheets_Spreadsheet([
            'properties' => [
                'title' => 'Test Sheet - ' . date('Y-m-d H:i:s'),
            ],
            'sheets' => [
                [
                    'properties' => [
                        'title' => 'Sheet1',
                        'index' => 0,
                    ],
                ],
            ],
        ]);

        $result = $service->spreadsheets->create($spreadsheet, ['fields' => 'spreadsheetId,spreadsheetUrl']);

        $sheetId  = $result->getSpreadsheetId();
        $sheetUrl = $result->getSpreadsheetUrl();

        echo '<h3>Tạo Sheet thành công!</h3>';
        echo '<p>Sheet ID: <strong>' . $sheetId . '</strong></p>';
        echo '<p>URL: <a href="' . htmlspecialchars($sheetUrl) . '" target="_blank">' . htmlspecialchars($sheetUrl) . '</a></p>';
        echo '<p><a href="?action=write_sheet&sheet_id=' . urlencode($sheetId) . '">Ghi dữ liệu vào sheet này</a></p>';
        echo '<p><a href="?action=menu&sheet_id=' . urlencode($sheetId) . '">Về menu</a></p>';

    } catch (\Exception $e) {
        echo '<h3 style="color:red">Lỗi tạo Sheet:</h3>';
        echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
        echo '<p>Kiểm tra scope Drive API đã được bật trong Google Cloud Console chưa.</p>';
    }
    exit;
}

// ===== 2. GHI DỮ LIỆU VÀO SHEET =====
if ($action === 'write_sheet') {
    $sheetId = $_GET['sheet_id'] ?? '';
    if (!$sheetId) {
        exit('Thiếu sheet_id. Ví dụ: ?action=write_sheet&sheet_id=YOUR_SHEET_ID');
    }

    try {
        $client  = makeClient([\Google_Service_Sheets::SPREADSHEETS]);
        $service = new \Google_Service_Sheets($client);

        // Ghi header + data
        $values = [
            ['STT', 'Họ tên',       'Email',                 'Điểm', 'Ghi chú'],
            [1,     'Nguyễn Văn A', 'a@example.com',         9.5,    'Xuất sắc'],
            [2,     'Trần Thị B',   'b@example.com',         8.0,    'Giỏi'],
            [3,     'Lê Văn C',     'c@example.com',         7.5,    'Khá'],
            [4,     'Phạm Thị D',   'd@example.com',         6.0,    'Trung bình'],
            ['',    'Ghi lúc:',     date('Y-m-d H:i:s'),    '',     ''],
        ];

        $body = new \Google_Service_Sheets_ValueRange(['values' => $values]);

        $params = ['valueInputOption' => 'USER_ENTERED'];
        $result = $service->spreadsheets_values->update(
            $sheetId,
            'Sheet1!A1',
            $body,
            $params
        );

        echo '<h3>Ghi dữ liệu thành công!</h3>';
        echo '<p>Số ô đã cập nhật: <strong>' . $result->getUpdatedCells() . '</strong></p>';
        echo '<p>Rows cập nhật: <strong>' . $result->getUpdatedRows() . '</strong></p>';

        // Đọc lại để xác nhận
        $readResult = $service->spreadsheets_values->get($sheetId, 'Sheet1!A1:E10');
        $rows = $readResult->getValues();
        echo '<h4>Dữ liệu hiện tại trong Sheet:</h4>';
        echo '<table border="1" cellpadding="5" cellspacing="0">';
        foreach ($rows as $row) {
            echo '<tr>';
            foreach ($row as $cell) {
                echo '<td>' . htmlspecialchars($cell) . '</td>';
            }
            echo '</tr>';
        }
        echo '</table>';

        echo '<p><a href="?action=menu&sheet_id=' . urlencode($sheetId) . '">Về menu</a></p>';

    } catch (\Exception $e) {
        echo '<h3 style="color:red">Lỗi ghi Sheet:</h3>';
        echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
    }
    exit;
}

// ===== 3. TẠO GOOGLE DOC MỚI =====
if ($action === 'create_doc') {
    try {
        $client = makeClient([
            'https://www.googleapis.com/auth/documents',
            \Google_Service_Drive::DRIVE,
        ]);
        $docsService = new \Google_Service_Docs($client);

        $doc = new \Google_Service_Docs_Document([
            'title' => 'Test Document - ' . date('Y-m-d H:i:s'),
        ]);

        $createdDoc = $docsService->documents->create($doc);
        $docId  = $createdDoc->getDocumentId();
        $docUrl = 'https://docs.google.com/document/d/' . $docId . '/edit';

        // Ghi nội dung vào Doc
        $requests = [
            new \Google_Service_Docs_Request([
                'insertText' => [
                    'location' => ['index' => 1],
                    'text'     => "Xin chào!\n\nĐây là document được tạo tự động từ Laravel.\nNgày tạo: " . date('Y-m-d H:i:s') . "\n\nNội dung test:\n- Dòng 1\n- Dòng 2\n- Dòng 3\n",
                ],
            ]),
        ];

        $batchUpdateRequest = new \Google_Service_Docs_BatchUpdateDocumentRequest([
            'requests' => $requests,
        ]);
        $docsService->documents->batchUpdate($docId, $batchUpdateRequest);

        echo '<h3>Tạo Google Doc thành công!</h3>';
        echo '<p>Document ID: <strong>' . $docId . '</strong></p>';
        echo '<p>URL: <a href="' . htmlspecialchars($docUrl) . '" target="_blank">' . htmlspecialchars($docUrl) . '</a></p>';
        echo '<p><a href="?action=menu">Về menu</a></p>';

    } catch (\Exception $e) {
        echo '<h3 style="color:red">Lỗi tạo Doc:</h3>';
        echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
        echo '<p>Kiểm tra Google Docs API đã được bật trong Google Cloud Console chưa.</p>';
        echo '<p>Service account cần có quyền trên Google Docs API.</p>';
    }
    exit;
}

// ===== 4. LIỆT KÊ FILE TRÊN DRIVE =====
if ($action === 'list_drive') {
    try {
        $client = makeClient([\Google_Service_Drive::DRIVE_READONLY]);
        $driveService = new \Google_Service_Drive($client);

        $optParams = [
            'pageSize' => 20,
            'fields'   => 'nextPageToken, files(id, name, mimeType, createdTime, webViewLink)',
            'orderBy'  => 'createdTime desc',
        ];

        $results = $driveService->files->listFiles($optParams);
        $files   = $results->getFiles();

        echo '<h3>File trên Google Drive (20 file mới nhất):</h3>';
        if (empty($files)) {
            echo '<p>Không có file nào. (Service account chỉ thấy file do chính nó tạo)</p>';
        } else {
            echo '<table border="1" cellpadding="5" cellspacing="0">';
            echo '<tr><th>Tên</th><th>Loại</th><th>Ngày tạo</th><th>Link</th></tr>';
            foreach ($files as $file) {
                $mimeShort = str_replace('application/vnd.google-apps.', '', $file->getMimeType());
                echo '<tr>';
                echo '<td>' . htmlspecialchars($file->getName()) . '</td>';
                echo '<td>' . htmlspecialchars($mimeShort) . '</td>';
                echo '<td>' . $file->getCreatedTime() . '</td>';
                echo '<td><a href="' . htmlspecialchars($file->getWebViewLink()) . '" target="_blank">Mở</a></td>';
                echo '</tr>';
            }
            echo '</table>';
        }
        echo '<p><a href="?action=menu">Về menu</a></p>';

    } catch (\Exception $e) {
        echo '<h3 style="color:red">Lỗi list Drive:</h3>';
        echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
        echo '<p>Kiểm tra Google Drive API đã bật và scope đúng chưa.</p>';
    }
    exit;
}

echo 'Action không hợp lệ. <a href="?action=menu">Về menu</a>';
