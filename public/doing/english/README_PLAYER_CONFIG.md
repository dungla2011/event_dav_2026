# Audio Player Config & State Management

## Overview
Player tự động save và restore config mỗi lần bật lại, bao gồm:
- 🔀 Shuffle mode (On/Off)
- 🔁 Repeat mode (Off / All / One)
- 🔇 Mute state (On/Off)
- 🎵 Last playing track (Resume từ track cuối)

## Storage System

### LocalStorage Key Structure
```javascript
{
  "audioPlayerConfig": {
    "playlist1": {
      "shuffle": false,
      "repeatMode": 1,
      "muted": false,
      "lastTrack": 5,
      "lastUpdated": 1697203200000
    },
    "playlist2": { ... },
    "default": { ... }
  }
}
```

### Playlist Keys
- Mỗi playlist có config riêng (isolated)
- Key = folder path từ URL `?folder=xxx`
- "default" = homepage không có folder param

## Repeat Mode States

### Mode 0: Off (🔁 mờ)
- Không lặp lại
- Hết playlist thì dừng
- Click 1 lần → Mode 1

### Mode 1: All (🔁 sáng)
- Lặp lại toàn bộ playlist
- Hết bài cuối → Quay lại bài đầu
- Click 1 lần → Mode 2

### Mode 2: One (🔂 sáng)
- Lặp lại 1 bài hiện tại
- Chạy mãi 1 bài
- Click 1 lần → Mode 0

## Config Functions

### Load Config (Auto on page load)
```javascript
function loadConfig() {
    // Đọc từ localStorage
    // Restore: shuffle, repeat, mute, lastTrack
    // Return: startTrackIndex
}
```

### Save Config (Auto on every change)
```javascript
function saveConfig() {
    // Save hiện trạng vào localStorage
    // Trigger: playTrack, toggleShuffle, toggleRepeat, toggleMute
}
```

### Update Repeat Button
```javascript
function updateRepeatButton() {
    // Update UI: icon, opacity, title, active class
    // Console log current mode
}
```

## Auto-Save Triggers

### Track Change
```javascript
playTrack(index) {
    // ... play logic ...
    saveConfig(); // ✅ Auto save
}
```

### Shuffle Toggle
```javascript
toggleShuffle() {
    isShuffleOn = !isShuffleOn;
    saveConfig(); // ✅ Auto save
}
```

### Repeat Toggle
```javascript
toggleRepeat() {
    repeatMode = (repeatMode + 1) % 3;
    updateRepeatButton();
    saveConfig(); // ✅ Auto save
}
```

### Mute Toggle
```javascript
toggleMute() {
    audio.muted = !audio.muted;
    saveConfig(); // ✅ Auto save
}
```

## Visual Feedback

### Repeat Button States
| Mode | Icon | Opacity | Color | Title |
|------|------|---------|-------|-------|
| 0 Off | 🔁 | 0.5 | Gray | Repeat: Off |
| 1 All | 🔁 | 1.0 | Green | Repeat: All |
| 2 One | 🔂 | 1.0 | Green | Repeat: One |

### Shuffle Button
- Off: Gray, opacity 0.5
- On: Green (#1db954), active class

### Mute Button
- Unmuted: 🔊
- Muted: 🔇

## Debug Console Logs

### On Page Load
```
Loading saved config: {shuffle: false, repeatMode: 1, muted: false, lastTrack: 5}
Resuming from track: 6
Repeat mode: 1 All
```

### On Repeat Toggle
```
Repeat mode: 0 Off
Repeat mode: 1 All
Repeat mode: 2 One
```

### On Duration Load
```
Durations loaded: (from cache)
```

## Usage Examples

### Example 1: User Session
1. User mở playlist "Now I Know 1"
2. Bật shuffle, repeat all
3. Nghe đến track 10
4. Đóng tab
5. **Mở lại** → Tự động:
   - ✅ Shuffle: On
   - ✅ Repeat: All
   - ✅ Resume from track 10

### Example 2: Multiple Playlists
1. Playlist A: Shuffle On, Repeat One, Track 5
2. Chuyển sang Playlist B: Shuffle Off, Repeat All, Track 1
3. **Quay lại Playlist A** → Đúng config A (not B)

### Example 3: Clean State
1. User xoá localStorage: `localStorage.clear()`
2. Reload page
3. **Default state**:
   - Shuffle: Off
   - Repeat: Off
   - Mute: Off
   - Track: 1

## localStorage Size

### Estimated Size
```
Single playlist config: ~150 bytes
100 playlists: ~15KB
No limit concerns for typical usage
```

### Structure
```javascript
{
  "shuffle": false,      // 1 byte
  "repeatMode": 1,       // 1 byte
  "muted": false,        // 1 byte
  "lastTrack": 5,        // 1-2 bytes
  "lastUpdated": 1697... // 13 bytes
}
// Total: ~100 bytes + JSON overhead
```

## Privacy & Security

### Data Storage
- ✅ Local only (không gửi server)
- ✅ Per-browser (Chrome khác Firefox)
- ✅ Per-domain (mytree.vn isolated)

### No Sensitive Data
- Config chỉ lưu: mode, track index
- Không lưu: file paths, user info

### User Control
```javascript
// User có thể clear anytime
localStorage.removeItem('audioPlayerConfig');

// Hoặc clear all
localStorage.clear();
```

## Browser Compatibility

### Support
- ✅ Chrome/Edge: 100%
- ✅ Firefox: 100%
- ✅ Safari: 100%
- ✅ Mobile browsers: 100%

### Fallback
```javascript
try {
    localStorage.setItem(...);
} catch (e) {
    // Nếu localStorage disabled
    // Player vẫn chạy, chỉ không save
    console.error('Cannot save config:', e);
}
```

## Testing

### Manual Test Steps

#### Test 1: Repeat Mode Cycle
1. Click Repeat button
2. Check console: "Repeat mode: 1 All"
3. Check icon: 🔁 (sáng)
4. Click again
5. Check console: "Repeat mode: 2 One"
6. Check icon: 🔂
7. Click again
8. Check console: "Repeat mode: 0 Off"
9. Check icon: 🔁 (mờ)

#### Test 2: Config Persistence
1. Set Shuffle On, Repeat All
2. Play track 5
3. Reload page (F5)
4. Check: Shuffle On ✅
5. Check: Repeat All ✅
6. Check: Track 5 playing ✅

#### Test 3: Multi-Playlist
1. Playlist A: Shuffle On
2. Switch to Playlist B
3. Check: Shuffle Off (default)
4. Set Shuffle On in B
5. Switch back to A
6. Check: A still Shuffle On ✅

### Console Commands

#### View Current Config
```javascript
console.log(JSON.parse(localStorage.getItem('audioPlayerConfig')));
```

#### Clear Config
```javascript
localStorage.removeItem('audioPlayerConfig');
```

#### Set Custom Config
```javascript
const config = {
    'default': {
        shuffle: true,
        repeatMode: 2,
        muted: false,
        lastTrack: 10
    }
};
localStorage.setItem('audioPlayerConfig', JSON.stringify(config));
```

## Troubleshooting

### Config Not Saving
**Problem**: Changes không persist sau reload

**Solutions**:
1. Check localStorage enabled:
   ```javascript
   typeof(Storage) !== "undefined"
   ```
2. Check không ở incognito mode
3. Check browser quota not full
4. Check console errors

### Wrong Track Resuming
**Problem**: Resume sai track

**Solutions**:
1. Clear config: `localStorage.clear()`
2. Check `lastTrack < totalTracks`
3. Verify playlist order không đổi

### Repeat Not Cycling
**Problem**: Click repeat không đổi icon

**Solutions**:
1. Check console logs
2. Check `updateRepeatButton()` được call
3. Verify emoji rendering in browser

## Performance

### Save Frequency
- Every track change: ~1ms
- Every mode toggle: ~1ms
- Total overhead: Negligible

### Load Performance
- Read localStorage: < 1ms
- Parse JSON: < 1ms
- Update UI: < 5ms
- Total: < 10ms (imperceptible)

## Future Enhancements

### Possible Features
- [ ] Volume level persistence
- [ ] Playback speed persistence
- [ ] Queue management
- [ ] Favorites/bookmarks
- [ ] Listen history
- [ ] Cross-device sync (requires server)
- [ ] Import/export config

### Advanced Config
```javascript
{
  volume: 0.8,
  speed: 1.25,
  favorites: [2, 5, 10],
  history: [
    {track: 5, timestamp: 1697203200, duration: 123}
  ],
  theme: 'dark'
}
```

## Summary

✅ **Fixed**: Repeat mode chỉ cần click 1 lần để cycle  
✅ **Added**: Auto-save config to localStorage  
✅ **Added**: Auto-restore on page load  
✅ **Added**: Per-playlist isolated config  
✅ **Added**: Visual feedback with opacity/icon changes  
✅ **Added**: Console logs for debugging  

**User Experience**:
- Click 1 lần: Off → All → One → Off
- Mở lại page: Đúng config cũ + resume track cũ
- Mỗi playlist: Config riêng, không ảnh hưởng nhau
