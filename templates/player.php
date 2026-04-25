<?php
$streamUrl = setting('radio_stream_url', 'https://5.39.82.219/22094/listen.mp3');
$streamTitle = setting('radio_stream_title', 'EKO FM Live');
$embedScript = setting('radio_embed_script', '//myradiostream.com/embed/mayugefmuganda');

$fallbackNowPlaying = trim((string) preg_replace('/^now\s*playing\s*:\s*/i', '', $streamTitle));
$currentShow = function_exists('current_program_on_air') ? current_program_on_air() : null;
$nowPlayingName = $currentShow
    ? $currentShow['title']
    : ($fallbackNowPlaying !== '' ? $fallbackNowPlaying : 'EKO FM Live');
?>
<div id="live-player" class="radio-player player-shell">
    <div class="player-top">
        <div class="status">
            <span class="dot-live"></span>
            <span>LIVE BROADCAST</span>
            <span class="live-wave" style="margin-left: 8px;">
                <span></span><span></span><span></span>
            </span>
        </div>
        <button id="player-minimize" class="collapse-btn" aria-label="Toggle player">
            <span class="material-symbols-outlined">expand_more</span>
        </button>
    </div>

    <div class="player-body">
        <div class="info">
            <h3>Now Playing</h3>
            <p id="stream-title"><?php echo e($nowPlayingName); ?></p>

            <div class="controls">
                <button id="player-toggle" class="play-btn" aria-label="Play/Pause">
                    <span class="material-symbols-outlined">play_arrow</span>
                </button>
                <div class="volume-container" style="display:flex; align-items:center; gap: 8px; flex:1;">
                    <span class="material-symbols-outlined" style="font-size: 18px; opacity: 0.7;">volume_up</span>
                    <input id="player-volume" type="range" min="0" max="1" step="0.01" value="0.8">
                </div>
            </div>
            
            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 8px;">
                <!--<span class="live-badge">LIVE</span>
                <span style="font-size: 11px; opacity: 0.7; font-weight: 500;">Listeners: <span id="listeners-count">Loading...</span></span> -->
            </div>
        </div>
    </div>
</div>

<audio id="global-audio" preload="none" crossorigin="anonymous" data-embed-script="<?php echo e($embedScript); ?>" data-base-url="<?php echo e(rtrim(BASE_URL, '/')); ?>" data-now-playing="<?php echo e($nowPlayingName); ?>">
    <source src="<?php echo e($streamUrl); ?>" type="audio/mpeg">
</audio>
