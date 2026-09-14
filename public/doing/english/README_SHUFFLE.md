# Audio Player Shuffle Feature

## Overview
Shuffle button (🔀) để random thứ tự phát nhạc thay vì tuần tự.

## Default Behavior
- ❌ **Shuffle OFF** (mặc định)
- ✅ Play tuần tự: Track 1 → 2 → 3 → 4 → ...
- Next: +1, Prev: -1

## Shuffle Modes

### Mode 1: Shuffle OFF (Default) 🔀 (mờ)
```
Play order: 1 → 2 → 3 → 4 → 5 → ...
- Next: currentIndex + 1
- Prev: currentIndex - 1
- At end: Stop (unless repeat on)
```

### Mode 2: Shuffle ON (Active) 🔀 (xanh)
```
Play order: 3 → 1 → 5 → 2 → 4 → ...
- Next: Next in shuffle queue
- Prev: Previous in shuffle queue
- At end: Regenerate queue (if repeat all)
```

## How Shuffle Works

### 1. Generate Shuffle Queue
Khi click Shuffle ON:
```javascript
// Create random order (Fisher-Yates algorithm)
shuffleQueue = [0, 1, 2, 3, 4, ...] // All track indexes
shuffleQueue.shuffle() // Random order
// Example result: [3, 1, 5, 2, 4, ...]

shuffleIndex = 0 // Start at first in queue
```

### 2. Play Through Queue
- **Next**: Move to shuffleIndex + 1
- **Prev**: Move to shuffleIndex - 1
- **Direct click**: Find track in queue, update shuffleIndex

### 3. At End of Queue
**Without Repeat**:
- Stop at last track
- Play button shows ▶

**With Repeat All**:
- Regenerate new shuffle queue
- Continue playing (new random order)

## Fisher-Yates Shuffle Algorithm

```javascript
function generateShuffleQueue() {
    // Step 1: Create array with all indexes
    shuffleQueue = [0, 1, 2, 3, 4, 5]; // Example: 6 tracks
    
    // Step 2: Remove current playing track
    // (So current track stays, next will be random)
    const currentIdx = shuffleQueue.indexOf(currentTrackIndex);
    shuffleQueue.splice(currentIdx, 1);
    
    // Step 3: Shuffle remaining tracks
    for (let i = shuffleQueue.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [shuffleQueue[i], shuffleQueue[j]] = [shuffleQueue[j], shuffleQueue[i]];
    }
    
    // Step 4: Put current track at beginning
    shuffleQueue.unshift(currentTrackIndex);
    
    // Result example: [2, 4, 0, 5, 1, 3]
    // (Current is 2, rest are random)
}
```

## Behavior Matrix

| Shuffle | Repeat | Next Behavior | End Behavior |
|---------|--------|---------------|--------------|
| OFF | OFF | Track +1 | Stop at last |
| OFF | All | Track +1 | Loop to first |
| OFF | One | Replay current | Never ends |
| ON | OFF | Random queue | Stop at queue end |
| ON | All | Random queue | New random queue |
| ON | One | Replay current | Never ends |

## User Experience

### Scenario 1: Normal Play (Shuffle OFF)
```
User: Plays track 1
Auto: → Track 2 → Track 3 → Track 4 → Stop ✅
```

### Scenario 2: Shuffle ON
```
User: Plays track 1
User: Clicks Shuffle 🔀 (turns green)
Auto: Generates queue: [1, 4, 2, 5, 3]
User: Clicks Next
Auto: → Track 4 (next in queue)
User: Clicks Next
Auto: → Track 2 (next in queue) ✅
```

### Scenario 3: Direct Click During Shuffle
```
Shuffle queue: [1, 4, 2, 5, 3]
Current: Track 4 (index 1 in queue)
User: Clicks Track 5 directly
Auto: Finds 5 in queue (index 3)
Auto: Sets shuffleIndex = 3
User: Clicks Next
Auto: → Track 3 (next in queue) ✅
```

### Scenario 4: Shuffle + Repeat All
```
Shuffle queue: [1, 4, 2, 5, 3]
Current: Track 3 (last in queue)
Repeat: All (ON)
Auto: Track ends
Auto: Generates NEW queue: [3, 2, 1, 5, 4]
Auto: Plays Track 2 (first new random) ✅
```

## Console Logs

### Shuffle ON
```javascript
console.log('Shuffle ON - Queue generated');
console.log('Shuffle queue:', [3, 1, 5, 2, 4], '... (showing first 5)');
```

### Shuffle OFF
```javascript
console.log('Shuffle OFF');
```

### Playing Track
```javascript
// When user clicks track during shuffle
console.log('Syncing shuffle queue index');
```

## Visual Indicator

### Button States
| State | Icon | Color | Opacity |
|-------|------|-------|---------|
| OFF | 🔀 | Gray | 0.5 |
| ON | 🔀 | Green (#1db954) | 1.0 |

### CSS
```css
.control-btn.active {
    color: #1db954; /* Spotify green */
}
```

## Code Structure

### Variables
```javascript
let isShuffleOn = false;        // Shuffle state
let shuffleQueue = [];          // Random order array
let shuffleIndex = 0;           // Current position in queue
```

### Key Functions
```javascript
toggleShuffle()           // Turn on/off
generateShuffleQueue()    // Create random order
nextTrack()               // Handle next with shuffle
prevTrack()               // Handle prev with shuffle
playTrack(index)          // Sync shuffle index
```

## Testing

### Test 1: Default Sequential
1. Load page
2. Play track 1
3. Click Next
4. ✅ Expect: Track 2
5. Click Next
6. ✅ Expect: Track 3

### Test 2: Shuffle ON
1. Play track 1
2. Click Shuffle (turns green)
3. Console shows: "Shuffle ON - Queue generated"
4. Click Next
5. ✅ Expect: Random track (not track 2)
6. Click Next again
7. ✅ Expect: Different track (not duplicate)

### Test 3: Shuffle OFF Again
1. Shuffle is ON, playing track 4
2. Click Shuffle (turns off)
3. Console shows: "Shuffle OFF"
4. Click Next
5. ✅ Expect: Track 5 (sequential)

### Test 4: Prev Button with Shuffle
1. Shuffle ON
2. Queue: [1, 4, 2, 5, 3]
3. Play through to track 2 (index 2)
4. Click Prev
5. ✅ Expect: Track 4 (previous in queue)

### Test 5: Direct Click
1. Shuffle ON
2. Queue: [1, 4, 2, 5, 3]
3. Click track 5 directly
4. Click Next
5. ✅ Expect: Track 3 (next after 5 in queue)

## Edge Cases

### Case 1: Shuffle at Last Track
**Without Repeat**:
- ✅ Stop playing
- Button shows ▶

**With Repeat All**:
- ✅ Generate new queue
- Continue playing

### Case 2: Empty Queue
**Problem**: shuffleQueue is empty

**Solution**:
```javascript
if (isShuffleOn && shuffleQueue.length === 0) {
    generateShuffleQueue();
}
```

### Case 3: Track Not in Queue
**Problem**: User clicks track not in current queue

**Solution**:
```javascript
const queueIdx = shuffleQueue.indexOf(index);
if (queueIdx > -1) {
    shuffleIndex = queueIdx; // Found in queue
} else {
    generateShuffleQueue(); // Regenerate queue
}
```

## Performance

### Queue Generation
- Time: O(n) where n = total tracks
- Memory: O(n) for shuffleQueue array
- 100 tracks: ~1ms

### Fisher-Yates Shuffle
- Time: O(n)
- Truly random distribution
- No duplicate tracks in queue

## Comparison with Other Players

### Spotify
- ✅ Similar: Queue-based shuffle
- ✅ Similar: Regenerate at end
- ❌ Different: Spotify has "Smart Shuffle" (AI)

### YouTube Music
- ✅ Similar: Random order
- ❌ Different: No queue history (can't go back)

### Apple Music
- ✅ Similar: Queue-based
- ✅ Similar: Prev button works in shuffle

### Our Implementation
- ✅ Queue-based (like Spotify)
- ✅ Prev/Next work correctly
- ✅ Direct track click syncs queue
- ✅ Regenerates on repeat all
- ✅ No duplicate tracks until queue ends

## Benefits

### For Users
- ✅ True random (no duplicates in queue)
- ✅ Prev button works (go back in queue)
- ✅ Predictable behavior
- ✅ Works with repeat modes

### For Developers
- ✅ Clean logic (if/else on isShuffleOn)
- ✅ Easy to debug (console logs)
- ✅ Fisher-Yates = proven algorithm
- ✅ Syncs with direct clicks

## Future Enhancements

### Possible Features
- [ ] Show shuffle queue in UI
- [ ] "Up Next" preview
- [ ] Shuffle history (last 10 tracks)
- [ ] Smart shuffle (avoid similar tracks)
- [ ] Shuffle within playlist sections
- [ ] Custom shuffle weights (favor certain tracks)

### Smart Shuffle Example
```javascript
// Weight by play count (play less-played tracks more)
function generateSmartShuffle() {
    const weights = tracks.map(t => 1 / (t.playCount + 1));
    // Weighted random selection...
}
```

## Summary

✅ **Default**: Shuffle OFF (tuần tự: 1→2→3→4→...)  
✅ **Shuffle ON**: Random queue (3→1→5→2→4→...)  
✅ **Algorithm**: Fisher-Yates (truly random)  
✅ **Queue-based**: No duplicates until end  
✅ **Prev button**: Works in shuffle mode  
✅ **Repeat All**: Regenerates new random queue  
✅ **Save config**: Shuffle state persists after F5  

**Key Point**: Mặc định KHÔNG shuffle, phải click nút mới shuffle! 🔀
