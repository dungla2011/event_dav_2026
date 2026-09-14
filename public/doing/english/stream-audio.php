<?php
/**
 * Audio Streaming Script with Multi-Layer Security
 * Stream audio files from outside webroot
 * 
 * Usage: stream-audio.php?file=lesson01/track01.mp3
 * 
 * Security Features:
 * - Layer 1: Null byte removal
 * - Layer 2: Dangerous pattern blocking
 * - Layer 3: Character whitelist
 * - Layer 4: Path normalization
 * - Layer 5: Realpath resolution
 * - Layer 6: Directory boundary check
 * - Layer 7: Directory blocking
 * - Layer 8: Extension whitelist
 * - Layer 9: File size limit
 * - Layer 10: Permission check
 * - Additional: Security headers, MIME type validation
 */

// Optional: Rate limiting (uncomment to enable)
/*
session_start();
$rateLimit = 50; // Max requests per minute
$rateLimitKey = 'audio_stream_' . $_SERVER['REMOTE_ADDR'];
$_SESSION[$rateLimitKey] = ($_SESSION[$rateLimitKey] ?? 0) + 1;

if ($_SESSION[$rateLimitKey] > $rateLimit) {
    http_response_code(429);
    die('Rate limit exceeded');
}
*/

// Optional: Access logging (uncomment to enable)
/*
$logFile = __DIR__ . '/audio-access.log';
$logEntry = date('Y-m-d H:i:s') . ' | ' . $_SERVER['REMOTE_ADDR'] . ' | ' . ($_GET['file'] ?? '') . "\n";
file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
*/

// Get file path from query string
$requestedFile = $_GET['file'] ?? '';

if (empty($requestedFile)) {
    http_response_code(400);
    die('No file specified');
}

// SECURITY LAYER 1: Remove any null bytes
$requestedFile = str_replace("\0", '', $requestedFile);

// SECURITY LAYER 2: Block dangerous patterns
$dangerousPatterns = [
    '../',           // Directory traversal
    '..\\',          // Directory traversal (Windows)
    '..',            // Any double dots
    '//',            // Protocol or root access
    '\\\\',          // Windows network path
    ':',             // Drive letter or stream wrapper
    "\0",            // Null byte
    '%00',           // Encoded null byte
    'php://',        // PHP stream wrapper
    'file://',       // File stream wrapper
    'data://',       // Data stream wrapper
    'http://',       // HTTP protocol
    'https://',      // HTTPS protocol
    'ftp://',        // FTP protocol
    'phar://',       // Phar wrapper
    'expect://',     // Expect wrapper
    'zip://',        // Zip wrapper
];

foreach ($dangerousPatterns as $pattern) {
    if (stripos($requestedFile, $pattern) !== false) {
        http_response_code(403);
        die('Invalid file path: Dangerous pattern detected');
    }
}

// SECURITY LAYER 3: Only allow alphanumeric, dash, underscore, dot, slash
if (!preg_match('/^[a-zA-Z0-9\/_\-\.]+$/', $requestedFile)) {
    http_response_code(403);
    die('Invalid file path: Invalid characters');
}

// SECURITY LAYER 4: Normalize path (remove ./ and multiple slashes)
$requestedFile = preg_replace('#/+#', '/', $requestedFile);
$requestedFile = preg_replace('#^\./+#', '', $requestedFile);
$requestedFile = trim($requestedFile, '/');

// Base directory (outside webroot)
$baseDir = "/var/glx/english/";
$filePath = $baseDir . $requestedFile;

// SECURITY LAYER 5: Realpath resolution
$filePath = realpath($filePath);
$baseDir = realpath($baseDir);

if ($filePath === false) {
    http_response_code(404);
    die('File not found');
}

if ($baseDir === false) {
    http_response_code(500);
    die('Base directory not found');
}

// SECURITY LAYER 6: Ensure file is within base directory
if (strpos($filePath, $baseDir) !== 0) {
    http_response_code(403);
    die('Access denied: File outside allowed directory');
}

// SECURITY LAYER 7: Check if path is a directory (only files allowed)
if (is_dir($filePath)) {
    http_response_code(403);
    die('Access denied: Cannot stream directories');
}

if (!is_file($filePath)) {
    http_response_code(404);
    die('File not found');
}

// SECURITY LAYER 8: Check file extension whitelist
$ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
$allowedExtensions = ['mp3', 'wav', 'ogg', 'm4a', 'aac'];

if (!in_array($ext, $allowedExtensions, true)) {
    http_response_code(403);
    die('Access denied: Invalid file type');
}

// SECURITY LAYER 9: Check file size (prevent abuse)
$maxFileSize = 500 * 1024 * 1024; // 500MB max
$fileSize = filesize($filePath);

if ($fileSize === false || $fileSize > $maxFileSize) {
    http_response_code(403);
    die('Access denied: File too large or unreadable');
}

// SECURITY LAYER 10: Check file permissions (readable)
if (!is_readable($filePath)) {
    http_response_code(403);
    die('Access denied: File not readable');
}

// Get file info
$fileName = basename($filePath);

// Map extension to MIME type
$mimeTypes = [
    'mp3' => 'audio/mpeg',
    'wav' => 'audio/wav',
    'ogg' => 'audio/ogg',
    'm4a' => 'audio/mp4',
    'aac' => 'audio/aac',
];
$contentType = $mimeTypes[$ext] ?? 'application/octet-stream';

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

// Set headers for audio streaming
header('Content-Type: ' . $contentType);
header('Content-Length: ' . $fileSize);
header('Content-Disposition: inline; filename="' . addslashes($fileName) . '"');
header('Accept-Ranges: bytes');
header('Cache-Control: public, max-age=31536000');

// Support for Range requests (seeking in audio)
if (isset($_SERVER['HTTP_RANGE'])) {
    // Parse range header
    if (preg_match('/bytes=(\d+)-(\d*)/', $_SERVER['HTTP_RANGE'], $matches)) {
        $start = intval($matches[1]);
        $end = !empty($matches[2]) ? intval($matches[2]) : $fileSize - 1;
        
        // Validate range
        if ($start > $end || $start >= $fileSize) {
            http_response_code(416); // Range Not Satisfiable
            header('Content-Range: bytes */' . $fileSize);
            exit;
        }
        
        $length = $end - $start + 1;
        
        // Set partial content headers
        http_response_code(206); // Partial Content
        header('Content-Range: bytes ' . $start . '-' . $end . '/' . $fileSize);
        header('Content-Length: ' . $length);
        
        // Open file and seek to start position
        $fp = fopen($filePath, 'rb');
        fseek($fp, $start);
        
        // Stream the requested range
        $buffer = 8192; // 8KB chunks
        $bytesLeft = $length;
        
        while ($bytesLeft > 0 && !feof($fp)) {
            $readSize = min($buffer, $bytesLeft);
            echo fread($fp, $readSize);
            $bytesLeft -= $readSize;
            flush();
        }
        
        fclose($fp);
    }
} else {
    // Stream entire file
    $fp = fopen($filePath, 'rb');
    
    while (!feof($fp)) {
        echo fread($fp, 8192); // 8KB chunks
        flush();
    }
    
    fclose($fp);
}

exit;
