<?php
/**
 * Cache Warmup Script
 * Pre-generates duration cache for all folders
 * 
 * Usage: php warmup-cache.php
 */

$baseDir = "/var/glx/english/";
$baseDir = realpath($baseDir);

if (!$baseDir || !is_dir($baseDir)) {
    die("Error: Base directory not found: /var/glx/english/\n");
}

echo "=== MP3 Duration Cache Warmup ===\n";
echo "Base directory: $baseDir\n\n";

// Get all folders
$folders = [];
$items = scandir($baseDir);
foreach ($items as $item) {
    if ($item === '.' || $item === '..') continue;
    $fullPath = $baseDir . '/' . $item;
    if (is_dir($fullPath)) {
        $folders[] = $item;
    }
}
sort($folders);

echo "Found " . count($folders) . " folders\n\n";

// Process each folder
$successCount = 0;
$errorCount = 0;
$totalFiles = 0;
$startTime = microtime(true);

foreach ($folders as $index => $folder) {
    echo "[" . ($index + 1) . "/" . count($folders) . "] Processing: $folder\n";
    
    // Call API
    $apiUrl = "http://localhost" . dirname($_SERVER['SCRIPT_NAME']) . "/get-durations.php?folder=$index";
    
    $context = stream_context_create([
        'http' => [
            'timeout' => 300, // 5 minutes timeout
        ]
    ]);
    
    $folderStart = microtime(true);
    $response = @file_get_contents($apiUrl, false, $context);
    $folderTime = microtime(true) - $folderStart;
    
    if ($response === false) {
        echo "  ❌ Error: Failed to fetch data\n";
        $errorCount++;
        continue;
    }
    
    $data = json_decode($response, true);
    
    if (isset($data['error'])) {
        echo "  ❌ Error: " . $data['error'] . "\n";
        $errorCount++;
        continue;
    }
    
    if (isset($data['durations'])) {
        $fileCount = count($data['durations']);
        $totalFiles += $fileCount;
        $cached = isset($data['cached']) && $data['cached'] ? '(cached)' : '(fresh)';
        
        echo "  ✅ Success: $fileCount files $cached in " . number_format($folderTime, 2) . "s\n";
        $successCount++;
    } else {
        echo "  ⚠️  Warning: No durations in response\n";
        $errorCount++;
    }
    
    echo "\n";
    
    // Small delay to avoid overwhelming server
    usleep(100000); // 100ms
}

$totalTime = microtime(true) - $startTime;

echo "=== Summary ===\n";
echo "Total folders: " . count($folders) . "\n";
echo "Success: $successCount\n";
echo "Errors: $errorCount\n";
echo "Total files: $totalFiles\n";
echo "Total time: " . number_format($totalTime, 2) . "s\n";
echo "Average: " . number_format($totalTime / max(count($folders), 1), 2) . "s per folder\n";

if ($successCount > 0) {
    echo "\n✅ Cache warmup completed successfully!\n";
} else {
    echo "\n❌ Cache warmup failed!\n";
    exit(1);
}
