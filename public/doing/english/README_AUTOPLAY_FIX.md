# Audio Player Autoplay Fix

## Problem
Khi F5 reload page, nút play hiện **Pause (⏸)** thay vì **Play (▶)**  
→ Confusing vì Chrome block autoplay, nhạc không chạy

## Root Cause
```javascript
// Old code
function playTrack(index) {
    audio.src = track.dataset.src;
    audio.play(); // ❌ Chrome blocks this!
    playPauseBtn.textContent = '⏸'; // But button changes anyway
}
```

Chrome's autoplay policy:
- ❌ Block autoplay without user interaction
- ✅ Allow play after user click/touch

## Solution

### 1. Split Load vs Play Functions

#### `loadTrack()` - For initial load (no autoplay)
```javascript
function loadTrack(index) {
    audio.src = track.dataset.src;
    // Don't call audio.play()
    playPauseBtn.textContent = '▶'; // Keep play button
}
```

#### `playTrack()` - For user actions (with autoplay)
```javascript
function playTrack(index) {
    audio.src = track.dataset.src;
    audio.play().catch(err => {
        console.log('Autoplay blocked:', err);
        playPauseBtn.textContent = '▶';
    });
}
```

### 2. Use Audio Event Listeners

Instead of manually setting button text, let events do it:

```javascript
// Auto-update button based on actual audio state
audio.addEventListener('play', () => {
    playPauseBtn.textContent = '⏸';
});

audio.addEventListener('pause', () => {
    playPauseBtn.textContent = '▶';
});
```

**Benefits**:
- ✅ Button synced with actual playback state
- ✅ Handles autoplay blocks gracefully
- ✅ Works with browser play/pause controls
- ✅ Consistent across all scenarios

### 3. Update Initialize Code

```javascript
// Old
if (totalTracks > 0) {
    playTrack(0); // ❌ Tries to autoplay
}

// New
if (totalTracks > 0) {
    loadTrack(0); // ✅ Just loads, doesn't play
}
```

## Testing

### Scenario 1: Fresh Load
1. Open page (F5)
2. ✅ Button shows: **▶ (Play)**
3. Click play button
4. ✅ Audio plays, button changes to: **⏸ (Pause)**

### Scenario 2: User Clicks Track
1. Click track in list
2. Calls `playTrack()` with user gesture
3. ✅ Audio plays (allowed by Chrome)
4. ✅ Button shows: **⏸**

### Scenario 3: Next/Prev Buttons
1. Click Next/Prev during playback
2. Calls `playTrack()` (user interaction)
3. ✅ Audio continues playing
4. ✅ Button stays: **⏸**

### Scenario 4: Space Bar
1. Press Space to play/pause
2. User gesture → allowed
3. ✅ Works correctly
4. ✅ Button synced via events

## Browser Autoplay Policies

### Chrome/Edge
- ❌ Block autoplay on page load
- ✅ Allow after user click/touch
- ✅ Allow if user has interacted with domain before

### Firefox
- ❌ Block autoplay by default
- ✅ Allow after user gesture
- ⚙️ Can be enabled in settings

### Safari
- ❌ Strictest autoplay policy
- ✅ Only allow with user gesture
- ❌ No autoplay even with prior interaction

## Code Changes Summary

### Modified Functions
1. ✅ `loadTrack(index)` - NEW function for initial load
2. ✅ `playTrack(index)` - Added `.catch()` for autoplay block
3. ✅ `togglePlayPause()` - Removed manual button updates
4. ✅ Audio event listeners - Added `play` and `pause` events

### Removed Code
```javascript
// ❌ Removed manual button updates
playPauseBtn.textContent = '⏸';
playPauseBtn.textContent = '▶';
```

### Added Code
```javascript
// ✅ Event-driven button updates
audio.addEventListener('play', () => {
    playPauseBtn.textContent = '⏸';
});

audio.addEventListener('pause', () => {
    playPauseBtn.textContent = '▶';
});
```

## Benefits

### User Experience
- ✅ Clear visual state (Play button on load)
- ✅ No confusion about why music isn't playing
- ✅ Follows web standards

### Developer Experience
- ✅ Less manual state management
- ✅ Event-driven = less bugs
- ✅ Works across all browsers

### Compliance
- ✅ Follows Chrome autoplay policy
- ✅ Respects user preferences
- ✅ No console warnings

## Related Issues

### Issue: Button out of sync
**Before**: Manual updates could miss edge cases  
**After**: Events ensure button always matches audio state

### Issue: Autoplay blocked but button says pause
**Before**: No error handling for `.play()` failure  
**After**: `.catch()` handles blocks gracefully

### Issue: Resume from saved track tries to autoplay
**Before**: Used `playTrack()` which auto-plays  
**After**: Use `loadTrack()` which just loads

## Future Considerations

### Media Session API
For enhanced browser controls:
```javascript
if ('mediaSession' in navigator) {
    navigator.mediaSession.metadata = new MediaMetadata({
        title: trackTitle,
        artist: 'Artist Name',
        album: 'Album Name',
    });
}
```

### Background Audio
If implementing background playback:
- Still respect autoplay policy
- Use Media Session for controls
- Handle tab visibility changes

## Summary

**Fixed**: Play button now shows **▶** on page load (F5)  
**Method**: Split `loadTrack()` vs `playTrack()`, use audio events  
**Result**: Compliant with Chrome autoplay policy, better UX  

**Key Principle**: Let the audio element's events drive the UI, not vice versa.
