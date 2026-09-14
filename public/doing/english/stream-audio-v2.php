<?php
/**
 * Audio Streaming Script - Index-based (More Secure)
 * Stream audio files using folder index and file index
 * 
 * Usage: stream-audio.php?folder=0&file=0
 * 
 * Benefits:
 * - No path traversal risks
 * - No encoding issues
 * - Simple integer validation
 * - Cache-friendly
 */

// Get parameters
$folderIndex = isset($_GET['folder']) ? intval($_GET['folder']) : -1;
$fileIndex = isset($_GET['file']) ? intval($_GET['file']) : -1;

// Validate indexes
if ($folderIndex < 0 || $fileIndex < 0) {
    http_response_code(400);
    die('Invalid parameters');
}

// Base directory (outside webroot)
$baseDir = "/var/glx/english/";
$baseDir = realpath($baseDir);

if ($baseDir === false || !is_dir($baseDir)) {
    http_response_code(500);
    die('Base directory not found');
}

/**
 * Get all folders (playlists)
 */
function getFolders($path) {
    $folders = [];
    if (!is_dir($path)) return $folders;
    
    $items = scandir($path);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        $fullPath = $path . '/' . $item;
        if (is_dir($fullPath)) {
            $folders[] = $fullPath;
        }
    }
    sort($folders);
    return $folders;
}

/**
 * Get all MP3 files from a folder
 */
function getMp3Files($path) {
    $files = [];
    if (!is_dir($path)) return $files;
    
    $items = scandir($path);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        $fullPath = $path . '/' . $item;
        if (is_file($fullPath) && preg_match('/\.mp3$/i', $item)) {
            $files[] = $fullPath;
        }
    }
    sort($files);
    return $files;
}

// Get folders list
$folders = getFolders($baseDir);

if (!isset($folders[$folderIndex])) {
    http_response_code(404);
    die('Folder not found');
}

$selectedFolder = $folders[$folderIndex];

// Get files list
$files = getMp3Files($selectedFolder);

if (!isset($files[$fileIndex])) {
    http_response_code(404);
    die('File not found');
}

$filePath = $files[$fileIndex];

// Final security check: ensure file is within base directory
if (strpos($filePath, $baseDir) !== 0) {
    http_response_code(403);
    die('Access denied');
}

// Verify file exists and is readable
if (!is_file($filePath) || !is_readable($filePath)) {
    http_response_code(404);
    die('File not found or not readable');
}

// Get file info
$fileSize = filesize($filePath);
$fileName = basename($filePath);

// Check file size limit (500MB max)
if ($fileSize > 500 * 1024 * 1024) {
    http_response_code(413);
    die('File too large');
}

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

// Set headers for audio streaming
header('Content-Type: audio/mpeg');
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
            http_response_code(416);
            header('Content-Range: bytes */' . $fileSize);
            exit;
        }
        
        $length = $end - $start + 1;
        
        // Set partial content headers
        http_response_code(206);
        header('Content-Range: bytes ' . $start . '-' . $end . '/' . $fileSize);
        header('Content-Length: ' . $length);
        
        // Open file and seek to start position
        $fp = fopen($filePath, 'rb');
        fseek($fp, $start);
        
        // Stream the requested range
        $buffer = 8192;
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
        echo fread($fp, 8192);
        flush();
    }
    
    fclose($fp);
}

exit;
