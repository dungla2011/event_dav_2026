<?php
/**
 * API to get MP3 file durations with caching
 * Returns duration in seconds for each file in a folder
 * 
 * Usage: get-durations.php?folder=0
 */

// Get folder index
$folderIndex = isset($_GET['folder']) ? intval($_GET['folder']) : -1;

if ($folderIndex < 0) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Invalid folder index']);
    exit;
}

// Base directory
$baseDir = "/var/glx/english/";
$baseDir = realpath($baseDir);

if ($baseDir === false || !is_dir($baseDir)) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Base directory not found']);
    exit;
}

// Cache directory
$cacheDir = __DIR__ . '/cache';
if (!is_dir($cacheDir)) {
    mkdir($cacheDir, 0755, true);
}

// Get folders
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

// Get MP3 files
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

// Get MP3 duration using ffprobe (fast and accurate)
function getDurationFfprobe($filePath) {
    $command = sprintf(
        'ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 %s 2>&1',
        escapeshellarg($filePath)
    );
    
    $output = shell_exec($command);
    $duration = floatval(trim($output));
    
    return $duration > 0 ? $duration : null;
}

// Get MP3 duration using getID3 (if available)
function getDurationGetId3($filePath) {
    if (!class_exists('getID3')) {
        return null;
    }
    
    try {
        $getID3 = new getID3();
        $fileInfo = $getID3->analyze($filePath);
        
        if (isset($fileInfo['playtime_seconds'])) {
            return floatval($fileInfo['playtime_seconds']);
        }
    } catch (Exception $e) {
        return null;
    }
    
    return null;
}

// Get MP3 duration - try multiple methods
function getMp3Duration($filePath) {
    // Try ffprobe first (most reliable)
    $duration = getDurationFfprobe($filePath);
    if ($duration !== null) {
        return $duration;
    }
    
    // Try getID3 as fallback
    $duration = getDurationGetId3($filePath);
    if ($duration !== null) {
        return $duration;
    }
    
    // If both fail, estimate from file size (rough estimate: 1MB ≈ 60s for 128kbps MP3)
    $fileSize = filesize($filePath);
    return round($fileSize / (128000 / 8)); // 128kbps = 16KB/s
}

// Format seconds to MM:SS
function formatDuration($seconds) {
    if ($seconds <= 0) return '0:00';
    
    $minutes = floor($seconds / 60);
    $secs = $seconds % 60;
    
    return sprintf('%d:%02d', $minutes, $secs);
}

// Get folders list
$folders = getFolders($baseDir);

if (!isset($folders[$folderIndex])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Folder not found']);
    exit;
}

$selectedFolder = $folders[$folderIndex];
$folderName = basename($selectedFolder);

// Cache file path
$cacheKey = md5($selectedFolder);
$cacheFile = $cacheDir . '/' . $cacheKey . '.json';

// Check cache validity (if folder modification time changed, invalidate cache)
$folderModTime = filemtime($selectedFolder);
$cacheValid = false;

if (file_exists($cacheFile)) {
    $cacheData = json_decode(file_get_contents($cacheFile), true);
    
    if (isset($cacheData['folder_mtime']) && $cacheData['folder_mtime'] == $folderModTime) {
        $cacheValid = true;
    }
}

// If cache valid, return cached data
if ($cacheValid && isset($cacheData['durations'])) {
    header('Content-Type: application/json');
    header('X-Cache: HIT');
    echo json_encode([
        'folder' => $folderName,
        'durations' => $cacheData['durations'],
        'cached' => true
    ]);
    exit;
}

// Cache miss - calculate durations
$files = getMp3Files($selectedFolder);
$durations = [];

foreach ($files as $index => $filePath) {
    $duration = getMp3Duration($filePath);
    $durations[$index] = [
        'seconds' => round($duration),
        'formatted' => formatDuration(round($duration))
    ];
}

// Save to cache
$cacheData = [
    'folder' => $folderName,
    'folder_mtime' => $folderModTime,
    'durations' => $durations,
    'generated_at' => time()
];

file_put_contents($cacheFile, json_encode($cacheData, JSON_PRETTY_PRINT));

// Return response
header('Content-Type: application/json');
header('X-Cache: MISS');
echo json_encode([
    'folder' => $folderName,
    'durations' => $durations,
    'cached' => false
]);
