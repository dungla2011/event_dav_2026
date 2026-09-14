<?php
/**
 * Professional Audio Player with Sticky UI
 * Modern Spotify-like interface
 */

// Get folder path from query string
$folderPath = $_GET['folder'] ?? '';
$baseDir = "/var/glx/english/";

// Build full path
if (empty($folderPath)) {
    $basePath = $baseDir;
} else {
    $basePath = $baseDir . $folderPath;
}

// Security checks
$basePath = realpath($basePath);
$baseDir = realpath($baseDir);

if ($basePath === false || $baseDir === false || strpos($basePath, $baseDir) !== 0) {
    die('Invalid folder path');
}

/**
 * Get playlists (subfolders)
 */
function getPlaylists($path) {
    $playlists = [];
    if (!is_dir($path)) return $playlists;

    $items = scandir($path);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        $fullPath = $path . '/' . $item;
        if (is_dir($fullPath)) {
            $playlists[] = ['name' => $item, 'path' => $item];
        }
    }
    return $playlists;
}

/**
 * Get MP3 files
 */
function getMp3Files($path, $folderIndex) {
    $mp3Files = [];
    if (!is_dir($path)) return $mp3Files;

    $items = scandir($path);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        $fullPath = $path . '/' . $item;
        if (is_file($fullPath) && preg_match('/\.mp3$/i', $item)) {
            $mp3Files[] = [
                'name' => pathinfo($item, PATHINFO_FILENAME),
                'filename' => $item,
                'path' => '',  // Will be set later
                'size' => filesize($fullPath)
            ];
        }
    }
    usort($mp3Files, function($a, $b) {
        return strcmp($a['filename'], $b['filename']);
    });
    
    // Set path using index (folder + file index)
    foreach ($mp3Files as $index => &$file) {
        $file['path'] = 'stream-audio-v2.php?folder=' . $folderIndex . '&file=' . $index;
    }
    
    return $mp3Files;
}

function formatFileSize($bytes) {
    if ($bytes >= 1048576) return number_format($bytes / 1048576, 2) . ' MB';
    if ($bytes >= 1024) return number_format($bytes / 1024, 2) . ' KB';
    return $bytes . ' B';
}

// Get data
$playlists = getPlaylists($basePath);
$currentPlaylist = $_GET['playlist'] ?? ($playlists[0]['name'] ?? '');

// Find current playlist index
$currentPlaylistIndex = 0;
foreach ($playlists as $index => $playlist) {
    if ($playlist['name'] === $currentPlaylist) {
        $currentPlaylistIndex = $index;
        break;
    }
}

$currentPlaylistPath = $basePath . '/' . $currentPlaylist;
$mp3Files = getMp3Files($currentPlaylistPath, $currentPlaylistIndex);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title><?php echo htmlspecialchars($currentPlaylist ?: 'Audio Player'); ?> - Music Player</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #363636;
            color: #fff;
            overflow-x: hidden;
        }

        /* Top Sticky Bar */
        .top-bar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 64px;
            background: #2a2a2a;
            border-bottom: 1px solid #404040;
            display: flex;
            align-items: center;
            padding: 0 24px;
            z-index: 100;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }

        .playlist-selector {
            display: flex;
            align-items: center;
            gap: 16px;
            flex: 1;
        }

        .playlist-button {
            background: #1db954;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 14px;
        }

        .playlist-button:hover {
            background: #1ed760;
            transform: scale(1.05);
        }

        .current-playlist {
            flex: 1;
            /*font-size: 24px;*/
            /*font-weight: 700;*/
            display: flex;
            align-items: center;
            gap: 12px;
        }

        /* Playlist Modal */
        .playlist-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.8);
            z-index: 200;
            padding: 80px 24px 120px;
            overflow-y: auto;
        }

        .playlist-modal.show {
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }

        .playlist-modal-content {
            background: #404040;
            border-radius: 16px;
            padding: 32px;
            max-width: 600px;
            width: 100%;
        }

        .playlist-modal-header {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .close-modal {
            background: none;
            border: none;
            color: #fff;
            font-size: 32px;
            cursor: pointer;
            border-radius: 50%;
            width: 40px;
            height: 40px;
        }

        .close-modal:hover {
            background: rgba(255,255,255,0.1);
        }

        .playlist-grid {
            display: grid;
            gap: 12px;
        }

        .playlist-card {
            background: #2a2a2a;
            padding: 20px;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .playlist-card:hover {
            background: #404040;
            transform: translateX(8px);
        }

        .playlist-card.active {
            background: #1db954;
        }

        /* Main Content */
        .main-container {
            margin-top: 64px;
            margin-bottom: 120px;
            padding: 5px;
        }

        .track-list-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .track-list-header {
            display: grid;
            grid-template-columns: 50px 1fr 100px 80px;
            gap: 16px;
            padding: 12px 16px;
            border-bottom: 1px solid #505050;
            margin-bottom: 8px;
            color: #b3b3b3;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .track-list {
            list-style: none;
        }

        .track-item {
            display: grid;
            grid-template-columns: 50px 1fr 100px 80px;
            gap: 16px;
            padding: 12px 10px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            align-items: center;
            margin-bottom: 4px;
        }

        .track-item:hover {
            background: #404040;
        }

        .track-item.playing {
            background: #2a2a2a;
        }

        .track-number {
            text-align: center;
            color: #b3b3b3;
            font-size: 16px;
        }

        .track-item.playing .track-number {
            color: #1db954;
        }

        .track-item.playing .track-number::before {
            content: '▶';
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .track-name {
            font-size: 16px;
            color: #fff;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .track-item.playing .track-name {
            color: #1db954;
        }

        .track-duration, .track-size {
            color: #b3b3b3;
            font-size: 14px;
            text-align: right;
        }

        /* Bottom Player */
        .bottom-player {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #2a2a2a;
            border-top: 1px solid #505050;
            z-index: 100;
            box-shadow: 0 -4px 12px rgba(0,0,0,0.3);
        }

        .progress-bar-container {
            width: 100%;
            height: 4px;
            background: #505050;
            cursor: pointer;
        }

        .progress-bar {
            height: 100%;
            background: #1db954;
            width: 0%;
            position: relative;
        }

        .progress-bar::after {
            content: '';
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 12px;
            height: 12px;
            background: #fff;
            border-radius: 50%;
            opacity: 0;
        }

        .progress-bar-container:hover .progress-bar::after {
            opacity: 1;
        }

        .player-content {
            display: flex;
            flex-direction: column;
            padding: 12px 24px 16px;
            gap: 12px;
        }

        .player-track-info {
            width: 100%;
            text-align: center;
            overflow: hidden;
            white-space: nowrap;
        }

        .player-track-combined {
            display: flex;
            align-items: center;
            flex: 1;
            overflow: hidden;
            font-size: 13px;
            color: #fff;
        }

        .player-track-meta {
            color: #b3b3b3;
            font-weight: 500;
            white-space: nowrap;
        }

        .player-track-name {
            color: #fff;
            font-weight: 600;
            margin-left: 8px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .player-track-info {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            margin-bottom: 12px;
            gap: 16px;
        }

        .player-track-combined {
            display: flex;
            align-items: center;
            flex: 1;
            overflow: hidden;
        }

        .player-track-meta {
            color: #b3b3b3;
            font-size: 11px;
            white-space: nowrap;
        }

        .player-track-name {
            color: #fff;
            font-weight: 600;
            margin-left: 8px;
        }

        .time-display {
            display: flex;
            gap: 8px;
            font-size: 12px;
            color: #b3b3b3;
            white-space: nowrap;
        }

        .player-controls-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
        }

        .player-controls {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
        }

        .control-buttons {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .control-btn {
            background: none;
            border: none;
            color: #b3b3b3;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 24px;
            padding: 8px;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .control-btn:hover {
            color: #fff;
            transform: scale(1.1);
            background: rgba(255,255,255,0.1);
        }

        .control-btn.active {
            color: #1db954;
        }

        .seek-btn {
            font-size: 13px;
            font-weight: bold;
            width: 44px;
            height: 44px;
        }

        .play-pause-btn {
            width: 48px;
            height: 48px;
            background: #fff;
            color: #000;
            border-radius: 50%;
            font-size: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 8px;
        }

        .play-pause-btn:hover {
            transform: scale(1.1);
        }

        .empty-state {
            text-align: center;
            padding: 80px 20px;
            color: #b3b3b3;
        }

        @media (max-width: 768px) {
            .track-list-header { display: none; }
            .track-item {
                grid-template-columns: 40px 1fr 60px;
            }
            .track-size { display: none; }
            .track-duration {
                display: block;
                font-size: 12px;
            }
            .control-buttons {
                gap: 8px;
            }
            .control-btn {
                font-size: 35px;
                width: 40px;
                height: 40px;
            }
            .control-btn.active {
                color: #1db954;
            }
            .play-pause-btn {
                width: 48px;
                height: 48px;
                font-size: 20px;
            }
            .option-btn {
                font-size: 20px;
                width: 40px;
                height: 40px;
            }
        }
    </style>
</head>
<body>
    <!-- Top Sticky Bar -->
    <div class="top-bar">
        <div class="playlist-selector">
            <button class="playlist-button" onclick="togglePlaylistModal()">
                📁 
            </button>
            <div class="current-playlist">
                <span>🎵</span>
                <span><?php echo htmlspecialchars($currentPlaylist ?: 'No Playlist'); ?></span>
            </div>
        </div>
    </div>

    <!-- Playlist Modal -->
    <div class="playlist-modal" id="playlistModal">
        <div class="playlist-modal-content">
            <div class="playlist-modal-header">
                <span>Select Playlist</span>
                <button class="close-modal" onclick="togglePlaylistModal()">×</button>
            </div>
            <div class="playlist-grid">
                <?php if (empty($playlists)): ?>
                    <div style="text-align:center;padding:40px;color:#b3b3b3;">No playlists found</div>
                <?php else: ?>
                    <?php foreach ($playlists as $playlist): ?>
                        <div class="playlist-card <?php echo $playlist['name'] === $currentPlaylist ? 'active' : ''; ?>"
                             onclick="window.location.href='?folder=<?php echo urlencode($folderPath); ?>&playlist=<?php echo urlencode($playlist['name']); ?>'">
                            <span style="font-size:32px;">📁</span>
                            <span style="font-size:18px;font-weight:600;"><?php echo htmlspecialchars($playlist['name']); ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Main Content - Track List -->
    <div class="main-container">
        <div class="track-list-container">
            <?php if (empty($mp3Files)): ?>
                <div class="empty-state">
                    <div style="font-size:64px;margin-bottom:24px;">🎧</div>
                    <h3>No tracks found</h3>
                    <p>Add some MP3 files to this playlist</p>
                </div>
            <?php else: ?>
                <div class="track-list-header">
                    <div>#</div>
                    <div>Title</div>
                    <div>Duration</div>
                    <div>Size</div>
                </div>
                <ul class="track-list">
                    <?php foreach ($mp3Files as $index => $file): ?>
                        <li class="track-item <?php echo $index === 0 ? 'playing' : ''; ?>"
                            data-index="<?php echo $index; ?>"
                            data-src="<?php echo htmlspecialchars($file['path']); ?>"
                            data-title="<?php echo htmlspecialchars($file['name']); ?>"
                            data-size="<?php echo formatFileSize($file['size']); ?>"
                            onclick="playTrack(<?php echo $index; ?>)">
                            <div class="track-number"><?php echo $index + 1; ?></div>
                            <div class="track-name"><?php echo htmlspecialchars($file['name']); ?></div>
                            <div class="track-duration">--:--</div>
                            <div class="track-size"><?php echo formatFileSize($file['size']); ?></div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bottom Sticky Player -->
    <div class="bottom-player">
        <div class="progress-bar-container" onclick="seek(event)">
            <div class="progress-bar" id="progressBar"></div>
        </div>
        <div class="player-content">
            <!-- Track Info - Top Row -->
            <div class="player-track-info">
                <div class="player-track-combined" id="playerTrackCombined">
                    <span class="player-track-meta" id="playerTrackMeta">
                        <?php echo !empty($mp3Files) ? 'Track 1 of ' . count($mp3Files) : ''; ?>
                    </span>
                    <span class="player-track-name" id="playerTrackName">
                        <?php echo !empty($mp3Files) ? htmlspecialchars($mp3Files[0]['name']) : 'No track'; ?>
                    </span>
                </div>
                <div class="time-display">
                    <span id="currentTime">0:00</span>
                    <span>/</span>
                    <span id="totalTime">0:00</span>
                </div>
            </div>

            <!-- Controls - Bottom Row -->
            <div class="player-controls-wrapper">
                <div class="player-controls">
                    <div class="control-buttons">
                        <button class="control-btn" id="shuffleBtn" onclick="toggleShuffle()" title="Shuffle">
                            🔀
                        </button>
                        <button class="control-btn seek-btn" onclick="seekBackward()" title="Backward 5 seconds">
                            -5s
                        </button>
                        <button class="control-btn" onclick="prevTrack()" title="Previous">
                            ⏮
                        </button>
                        <button class="play-pause-btn" id="playPauseBtn" onclick="togglePlayPause()" title="Play/Pause">
                            ▶
                        </button>
                        <button class="control-btn" onclick="nextTrack()" title="Next">
                            ⏭
                        </button>
                        <button class="control-btn seek-btn" onclick="seekForward()" title="Forward 5 seconds">
                            +5s
                        </button>
                        <button class="control-btn" id="repeatBtn" onclick="toggleRepeat()" title="Repeat">
                            �
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <audio id="audioPlayer" preload="metadata"></audio>

    <script>
        const audio = document.getElementById('audioPlayer');
        const playPauseBtn = document.getElementById('playPauseBtn');
        const progressBar = document.getElementById('progressBar');
        const currentTime = document.getElementById('currentTime');
        const totalTime = document.getElementById('totalTime');
        const trackItems = document.querySelectorAll('.track-item');
        const playerTrackName = document.getElementById('playerTrackName');
        const playerTrackMeta = document.getElementById('playerTrackMeta');
        const playerTrackCombined = document.getElementById('playerTrackCombined');
        const shuffleBtn = document.getElementById('shuffleBtn');
        const repeatBtn = document.getElementById('repeatBtn');
        const muteBtn = document.getElementById('muteBtn');

        let currentTrackIndex = 0;
        const totalTracks = trackItems.length;
        let isShuffleOn = false;
        let repeatMode = 0; // 0: off, 1: all, 2: one
        let durationsLoaded = false;
        
        // Shuffle queue
        let shuffleQueue = [];
        let shuffleIndex = 0;

        // Config key for localStorage
        const STORAGE_KEY = 'audioPlayerConfig';
        const PLAYLIST_KEY = getCurrentPlaylistKey();

        // Get current playlist key
        function getCurrentPlaylistKey() {
            const urlParams = new URLSearchParams(window.location.search);
            const folderPath = urlParams.get('folder') || 'default';
            return folderPath;
        }

        // Update shuffle button visual
        function updateShuffleButton() {
            shuffleBtn.classList.toggle('active', isShuffleOn);
            if (isShuffleOn) {
                shuffleBtn.style.opacity = '1';
                shuffleBtn.title = 'Shuffle: ON';
            } else {
                shuffleBtn.style.opacity = '0.5';
                shuffleBtn.title = 'Shuffle: OFF';
            }
            console.log('Shuffle mode:', isShuffleOn ? 'ON' : 'OFF');
        }

        // Load saved config
        function loadConfig() {
            try {
                const saved = localStorage.getItem(STORAGE_KEY);
                if (saved) {
                    const config = JSON.parse(saved);
                    
                    // Check if this playlist has saved state
                    if (config[PLAYLIST_KEY]) {
                        const playlistConfig = config[PLAYLIST_KEY];
                        
                        console.log('Loading saved config:', playlistConfig);
                        
                        // Restore shuffle
                        if (playlistConfig.shuffle !== undefined) {
                            isShuffleOn = playlistConfig.shuffle;
                            updateShuffleButton();
                            if (isShuffleOn) {
                                generateShuffleQueue();
                            }
                        }
                        
                        // Restore repeat mode
                        if (playlistConfig.repeatMode !== undefined) {
                            repeatMode = playlistConfig.repeatMode;
                            updateRepeatButton();
                        }
                        
                        // Restore mute
                        if (playlistConfig.muted !== undefined) {
                            audio.muted = playlistConfig.muted;
                            if (muteBtn) {
                                muteBtn.textContent = audio.muted ? '🔇' : '🔊';
                            }
                        } 
                        
                        // Restore last track and position
                        if (playlistConfig.lastTrack !== undefined && playlistConfig.lastTrack < totalTracks) {
                            currentTrackIndex = playlistConfig.lastTrack;
                            const lastPosition = playlistConfig.lastPosition || 0;
                            
                            if (lastPosition > 0) {
                                console.log('Resuming from track:', currentTrackIndex + 1, 'at', Math.floor(lastPosition) + 's');
                            } else {
                                console.log('Resuming from track:', currentTrackIndex + 1);
                            }
                            
                            return {
                                trackIndex: currentTrackIndex,
                                position: lastPosition
                            };
                        }
                    }
                }
            } catch (e) {
                console.error('Failed to load config:', e);
            }
            return { trackIndex: 0, position: 0 };
        }

        // Save config
        function saveConfig() {
            try {
                const saved = localStorage.getItem(STORAGE_KEY);
                const config = saved ? JSON.parse(saved) : {};
                
                // Save current playlist config
                config[PLAYLIST_KEY] = {
                    shuffle: isShuffleOn,
                    repeatMode: repeatMode,
                    muted: audio.muted,
                    lastTrack: currentTrackIndex,
                    lastPosition: audio.currentTime || 0, // Save playback position
                    lastUpdated: Date.now()
                };
                
                localStorage.setItem(STORAGE_KEY, JSON.stringify(config));
            } catch (e) {
                console.error('Failed to save config:', e);
            }
        }

        // Save position periodically (every 3 seconds)
        let savePositionTimer = null;
        function startPositionSaving() {
            // Clear existing timer
            if (savePositionTimer) {
                clearInterval(savePositionTimer);
            }
            
            // Save position every 3 seconds while playing
            savePositionTimer = setInterval(() => {
                if (!audio.paused && audio.currentTime > 0) {
                    saveConfig();
                }
            }, 3000);
        }

        // Update repeat button visual
        function updateRepeatButton() {
            repeatBtn.classList.toggle('active', repeatMode > 0);
            if (repeatMode === 0) {
                repeatBtn.textContent = '🔁';
                repeatBtn.style.opacity = '0.5';
                repeatBtn.title = 'Repeat: Off';
            } else if (repeatMode === 1) {
                repeatBtn.textContent = '🔁';
                repeatBtn.style.opacity = '1';
                repeatBtn.title = 'Repeat: All';
            } else if (repeatMode === 2) {
                repeatBtn.textContent = '🔂';
                repeatBtn.style.opacity = '1';
                repeatBtn.title = 'Repeat: One';
            }
            console.log('Repeat mode:', repeatMode, ['Off', 'All', 'One'][repeatMode]);
        }

        // Load durations from API
        async function loadDurations() {
            if (durationsLoaded) return;
            
            const urlParams = new URLSearchParams(window.location.search);
            const folderPath = urlParams.get('folder') || '';
            
            // Get folder index from current URL
            const currentUrl = window.location.href;
            const folderIndex = getFolderIndexFromPath(folderPath);
            
            try {
                const response = await fetch(`get-durations.php?folder=${folderIndex}`);
                const data = await response.json();
                
                if (data.durations) {
                    // Update track list durations
                    trackItems.forEach((item, index) => {
                        if (data.durations[index]) {
                            const durationCell = item.querySelector('.track-duration');
                            if (durationCell) {
                                durationCell.textContent = data.durations[index].formatted;
                                durationCell.dataset.seconds = data.durations[index].seconds;
                            }
                        }
                    });
                    
                    durationsLoaded = true;
                    console.log('Durations loaded:', data.cached ? '(from cache)' : '(fresh)');
                }
            } catch (error) {
                console.error('Failed to load durations:', error);
            }
        }
        
        // Helper to get folder index from path
        function getFolderIndexFromPath(folderPath) {
            // Extract folder index from URL or default to 0
            // This matches the folder scanning order in stream-audio-v2.php
            <?php
            $allFolders = [];
            $baseDirReal = realpath($baseDir);
            if ($baseDirReal && is_dir($baseDirReal)) {
                $items = scandir($baseDirReal);
                foreach ($items as $item) {
                    if ($item === '.' || $item === '..') continue;
                    $fullPath = $baseDirReal . '/' . $item;
                    if (is_dir($fullPath)) {
                        $allFolders[] = $item;
                    }
                }
                sort($allFolders);
            }
            
            $currentFolderIndex = 0;
            if (!empty($folderPath)) {
                $currentFolderIndex = array_search(basename($basePath), $allFolders);
                if ($currentFolderIndex === false) $currentFolderIndex = 0;
            }
            ?>
            return <?php echo $currentFolderIndex; ?>;
        }

        // Initialize
        if (totalTracks > 0) {
            // Initialize button states
            updateRepeatButton();
            updateShuffleButton();
            
            // Load saved config first
            const savedState = loadConfig();
            loadTrack(savedState.trackIndex, savedState.position); // Load with position
            
            // Start position saving timer
            startPositionSaving();
            
            // Load durations in background
            loadDurations();
        }

        // Load track without auto-playing (for initial load)
        function loadTrack(index, seekPosition = 0) {
            if (index < 0 || index >= totalTracks) return;
            currentTrackIndex = index;
            const track = trackItems[index];

            // Set audio source but don't play
            audio.src = track.dataset.src;

            const trackTitle = track.dataset.title;
            const trackMeta = `Track ${index + 1} of ${totalTracks}`;
            
            playerTrackName.textContent = trackTitle;
            playerTrackMeta.textContent = trackMeta;

            trackItems.forEach(item => item.classList.remove('playing'));
            track.classList.add('playing');

            // Scroll to center the track (for initial load)
            setTimeout(() => {
                track.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center',
                    inline: 'nearest'
                });
            }, 300);

            // Keep play button (don't change to pause)
            playPauseBtn.textContent = '▶';
            
            // Restore playback position when metadata loaded
            if (seekPosition > 0) {
                audio.addEventListener('loadedmetadata', function seekToPosition() {
                    if (audio.duration >= seekPosition) {
                        audio.currentTime = seekPosition;
                        console.log('Restored position:', Math.floor(seekPosition) + 's');
                    }
                    // Remove listener after seeking once
                    audio.removeEventListener('loadedmetadata', seekToPosition);
                }, { once: true });
            }
        }

        function playTrack(index) {
            if (index < 0 || index >= totalTracks) return;
            currentTrackIndex = index;
            const track = trackItems[index];

            audio.src = track.dataset.src;
            audio.play().catch(err => {
                console.log('Autoplay blocked by browser:', err);
                playPauseBtn.textContent = '▶';
            });

            const trackTitle = track.dataset.title;
            const trackMeta = `Track ${index + 1} of ${totalTracks}`;
            
            playerTrackName.textContent = trackTitle;
            playerTrackMeta.textContent = trackMeta;

            // Sync shuffle queue index if shuffle is on
            if (isShuffleOn && shuffleQueue.length > 0) {
                const queueIdx = shuffleQueue.indexOf(index);
                if (queueIdx > -1) {
                    shuffleIndex = queueIdx;
                } else {
                    // Track not in queue, regenerate
                    generateShuffleQueue();
                }
            }

            trackItems.forEach(item => item.classList.remove('playing'));
            track.classList.add('playing');

            // Scroll to center the playing track (with small delay for render)
            setTimeout(() => {
                track.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center',
                    inline: 'nearest'
                });
                console.log('📜 Scrolling to track:', index + 1);
            }, 100);

            // Button will be updated by 'play' event listener
            
            // Save config when track changes
            saveConfig();
        }

        function togglePlayPause() {
            if (audio.paused) {
                audio.play().catch(err => {
                    console.log('Play failed:', err);
                });
            } else {
                audio.pause();
            }
            // Button text updated by play/pause event listeners
        }

        function nextTrack() {
            if (isShuffleOn) {
                // Move to next in shuffle queue
                shuffleIndex++;
                
                // If reached end of queue
                if (shuffleIndex >= shuffleQueue.length) {
                    if (repeatMode === 1) {
                        // Repeat all: regenerate shuffle queue
                        generateShuffleQueue();
                        shuffleIndex = 1; // Skip current track (at index 0)
                    } else {
                        // No repeat: stop
                        shuffleIndex = shuffleQueue.length - 1;
                        return;
                    }
                }
                
                playTrack(shuffleQueue[shuffleIndex]);
            } else {
                // Normal sequential play (default behavior)
                let nextIndex = currentTrackIndex + 1;
                
                // If at end of playlist
                if (nextIndex >= totalTracks) {
                    if (repeatMode === 1) {
                        // Repeat all: go to first track
                        nextIndex = 0;
                    } else {
                        // No repeat: stop at last track
                        return;
                    }
                }
                
                playTrack(nextIndex);
            }
        }

        function prevTrack() {
            if (isShuffleOn) {
                // Move to previous in shuffle queue
                shuffleIndex--;
                
                // If at beginning of queue
                if (shuffleIndex < 0) {
                    if (repeatMode === 1) {
                        // Repeat all: go to end of queue
                        shuffleIndex = shuffleQueue.length - 1;
                    } else {
                        // No repeat: stay at first track
                        shuffleIndex = 0;
                        return;
                    }
                }
                
                playTrack(shuffleQueue[shuffleIndex]);
            } else {
                // Normal sequential play (default behavior)
                let prevIndex = currentTrackIndex - 1;
                
                // If at beginning of playlist
                if (prevIndex < 0) {
                    if (repeatMode === 1) {
                        // Repeat all: go to last track
                        prevIndex = totalTracks - 1;
                    } else {
                        // No repeat: stay at first track
                        return;
                    }
                }
                
                playTrack(prevIndex);
            }
        }

        // Seek backward 5 seconds
        function seekBackward() {
            if (audio.currentTime >= 5) {
                audio.currentTime -= 5;
            } else {
                audio.currentTime = 0;
            }
        }

        // Seek forward 5 seconds
        function seekForward() {
            const newTime = audio.currentTime + 5;
            if (newTime < audio.duration) {
                audio.currentTime = newTime;
            } else {
                audio.currentTime = audio.duration;
            }
        }

        function toggleShuffle() {
            isShuffleOn = !isShuffleOn;
            
            // Update visual state
            updateShuffleButton();
            
            if (isShuffleOn) {
                // Generate shuffle queue
                generateShuffleQueue();
                console.log('🔀 Shuffle ON - Queue generated');
            } else {
                // Clear shuffle queue
                shuffleQueue = [];
                shuffleIndex = 0;
                console.log('➡️ Shuffle OFF - Sequential mode');
            }
            
            saveConfig();
        }
        
        // Generate shuffle queue (Fisher-Yates shuffle)
        function generateShuffleQueue() {
            // Create array of all track indexes
            shuffleQueue = [];
            for (let i = 0; i < totalTracks; i++) {
                shuffleQueue.push(i);
            }
            
            // Remove current track from queue
            const currentIdx = shuffleQueue.indexOf(currentTrackIndex);
            if (currentIdx > -1) {
                shuffleQueue.splice(currentIdx, 1);
            }
            
            // Shuffle remaining tracks
            for (let i = shuffleQueue.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [shuffleQueue[i], shuffleQueue[j]] = [shuffleQueue[j], shuffleQueue[i]];
            }
            
            // Add current track at beginning
            shuffleQueue.unshift(currentTrackIndex);
            shuffleIndex = 0;
            
            console.log('Shuffle queue:', shuffleQueue.slice(0, 5), '... (showing first 5)');
        }

        function toggleRepeat() {
            // Cycle: off (0) → all (1) → one (2) → off (0)
            repeatMode = (repeatMode + 1) % 3;
            updateRepeatButton();
            saveConfig();
        }

        function toggleMute() {
            audio.muted = !audio.muted;
            if (muteBtn) {
                muteBtn.textContent = audio.muted ? '🔇' : '🔊';
            }
            saveConfig();
        }

        function seek(e) {
            const percent = e.offsetX / e.target.offsetWidth;
            audio.currentTime = percent * audio.duration;
        }

        function formatTime(seconds) {
            const mins = Math.floor(seconds / 60);
            const secs = Math.floor(seconds % 60);
            return mins + ':' + (secs < 10 ? '0' : '') + secs;
        }

        function togglePlaylistModal() {
            document.getElementById('playlistModal').classList.toggle('show');
        }

        // Audio events
        audio.addEventListener('play', () => {
            playPauseBtn.textContent = '⏸';
        });

        audio.addEventListener('pause', () => {
            playPauseBtn.textContent = '▶';
            // Save position when paused
            saveConfig();
        });

        audio.addEventListener('timeupdate', () => {
            const percent = (audio.currentTime / audio.duration) * 100;
            progressBar.style.width = percent + '%';
            currentTime.textContent = formatTime(audio.currentTime);
        });

        audio.addEventListener('loadedmetadata', () => {
            totalTime.textContent = formatTime(audio.duration);
        });

        audio.addEventListener('ended', () => {
            if (repeatMode === 2) {
                // Repeat one: replay current track
                audio.play();
            } else if (repeatMode === 1) {
                // Repeat all: play next (shuffle or sequential)
                nextTrack();
            } else if (isShuffleOn && shuffleIndex < shuffleQueue.length - 1) {
                // Shuffle on, not at end of queue
                nextTrack();
            } else if (!isShuffleOn && currentTrackIndex < totalTracks - 1) {
                // Sequential, not at end
                nextTrack();
            } else {
                // Reached end, no repeat
                playPauseBtn.textContent = '▶';
                saveConfig();
            }
        });

        // Save position before page unload
        window.addEventListener('beforeunload', () => {
            saveConfig();
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            if (e.key === ' ') {
                e.preventDefault();
                togglePlayPause();
            } else if (e.key === 'ArrowRight') {
                e.preventDefault();
                seekForward(); // Tua tới 5 giây
            } else if (e.key === 'ArrowLeft') {
                e.preventDefault();
                seekBackward(); // Tua lùi 5 giây
            }
        });
    </script>
</body>
</html>
