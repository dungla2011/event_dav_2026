# MP3 Duration API Setup Guide

## Overview
API để lấy duration của MP3 files với cache để tối ưu performance.

## Files
- `get-durations.php` - API endpoint để get duration
- `audio-player-pro.php` - Player với tích hợp duration API
- `cache/` - Folder chứa cache (tự động tạo)

## Requirements

### Option 1: FFprobe (Recommended - Fast & Accurate)
```bash
# Ubuntu/Debian
sudo apt-get update
sudo apt-get install ffmpeg

# CentOS/RHEL
sudo yum install ffmpeg

# Verify installation
ffprobe -version
```

### Option 2: getID3 PHP Library (Fallback)
```bash
composer require james-heinrich/getid3
```

### Option 3: File Size Estimation (Automatic Fallback)
Nếu không có ffprobe hoặc getID3, API sẽ tự động estimate dựa vào file size.
- Estimate: 1MB ≈ 60s (for 128kbps MP3)
- Không chính xác 100% nhưng vẫn dùng được

## API Usage

### Endpoint
```
GET get-durations.php?folder={folderIndex}
```

### Parameters
- `folder` (integer, required): Index của folder (0-based)
  - 0 = folder đầu tiên (alphabetically sorted)
  - 1 = folder thứ hai
  - ...

### Response Format
```json
{
  "folder": "Now I Know 1 Student Book",
  "durations": {
    "0": {
      "seconds": 245,
      "formatted": "4:05"
    },
    "1": {
      "seconds": 312,
      "formatted": "5:12"
    }
  },
  "cached": true
}
```

### Response Headers
- `X-Cache: HIT` - Data từ cache
- `X-Cache: MISS` - Data mới tính toán

## Cache System

### Cache Location
```
public/doing/english/cache/{md5_hash}.json
```

### Cache Invalidation
Cache tự động invalidate khi:
- Folder modification time thay đổi
- File được add/remove/modify trong folder

### Cache Structure
```json
{
  "folder": "Folder Name",
  "folder_mtime": 1697203200,
  "durations": { ... },
  "generated_at": 1697203200
}
```

### Manual Cache Clear
```bash
# Clear all cache
rm -rf public/doing/english/cache/*.json

# Clear specific folder cache
rm public/doing/english/cache/{hash}.json
```

## Integration in Player

### Automatic Loading
Player tự động call API khi load:
```javascript
// Load durations in background
loadDurations();
```

### Manual Reload
```javascript
// Force reload durations
durationsLoaded = false;
loadDurations();
```

## Performance

### First Load (Cache MISS)
- Tính toán duration cho tất cả files
- Thời gian: ~100-200ms per file với ffprobe
- Save vào cache

### Subsequent Loads (Cache HIT)
- Load từ cache
- Thời gian: < 10ms
- Instant response

### Example Timing
```
Folder: 50 MP3 files
- First load: ~5-10 seconds (calculating)
- Cache hits: < 50ms (instant)
```

## Troubleshooting

### Duration Shows "--:--"
- Check nếu ffprobe installed: `which ffprobe`
- Check PHP can execute shell commands
- Check file permissions

### Cache Not Working
- Check folder writable: `chmod 755 public/doing/english/`
- Check cache folder: `mkdir -p public/doing/english/cache`
- Check permissions: `chmod 755 public/doing/english/cache`

### Inaccurate Durations
- Using file size estimation (ffprobe not available)
- Install ffmpeg/ffprobe for accurate durations
- Or install getID3: `composer require james-heinrich/getid3`

## Security

### Path Protection
- Only accepts integer folder index
- No file path manipulation possible
- Base directory restricted to `/var/glx/english/`

### Cache Security
- Cache files stored with MD5 hash names
- No user input in cache file names
- Cache directory isolated

## Optimization Tips

### Pre-generate Cache
```bash
# Script to pre-generate all cache
php -r '
$baseDir = "/var/glx/english/";
$folders = array_diff(scandir($baseDir), [".", ".."]);
foreach ($folders as $i => $folder) {
    if (is_dir($baseDir . $folder)) {
        echo "Loading folder $i: $folder\n";
        $url = "https://mytree.vn/doing/english/get-durations.php?folder=$i";
        file_get_contents($url);
    }
}
'
```

### Cron Job for Cache Warmup
```bash
# Add to crontab - run every 6 hours
0 */6 * * * cd /var/www/html/doing/english && php warmup-cache.php
```

### Monitor Cache Size
```bash
# Check cache size
du -sh public/doing/english/cache/

# Count cache files
ls -1 public/doing/english/cache/*.json | wc -l
```

## API Alternatives

### Direct Metadata Loading (Not Recommended)
```javascript
// Don't do this - too slow!
audio.addEventListener('loadedmetadata', () => {
    duration = audio.duration;
});
```
❌ Requires loading entire file header  
❌ Multiple network requests  
❌ Slow for large playlists  

### Use Duration API (Recommended)
```javascript
// Do this - fast and cached!
fetch('get-durations.php?folder=0')
    .then(r => r.json())
    .then(data => updateDurations(data));
```
✅ Single API call  
✅ Cached results  
✅ Instant for subsequent loads  

## Future Enhancements

### Possible Improvements
- [ ] WebWorker for background loading
- [ ] IndexedDB client-side cache
- [ ] Batch API for multiple folders
- [ ] Progress indicator during calculation
- [ ] Admin UI for cache management
- [ ] Bitrate detection
- [ ] Album art extraction
- [ ] ID3 tags parsing (artist, album, etc.)

### Performance Targets
- Cache hit: < 10ms ✅
- Cache miss (50 files): < 5s ⚠️
- API response size: < 5KB ✅

## Support

### Common Commands
```bash
# Check ffprobe
ffprobe -version

# Test API
curl "https://mytree.vn/doing/english/get-durations.php?folder=0"

# Check cache
ls -lh public/doing/english/cache/

# Clear cache
rm -rf public/doing/english/cache/*.json

# Check PHP exec
php -r "echo exec('which ffprobe');"
```

### Contact
- API issues: Check server logs
- Performance: Monitor cache hit rate
- Accuracy: Install ffprobe for best results
