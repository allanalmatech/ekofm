<?php
$streamUrl = setting('radio_stream_url', 'https://5.39.82.219/22094/listen.mp3');
$streamTitle = setting('radio_stream_title', 'Now Playing: EKO FM Live');
$embedScript = setting('radio_embed_script', '//myradiostream.com/embed/mayugefmuganda');
?>
<div id="live-player" class="player-shell">
    <div class="container-xxl">
        <div class="player-bar floating-card">
            <div class="player-meta">
                <span class="dot-live"></span>
                <div>
                    <small class="d-block text-uppercase">Live Broadcast - Always On</small>
                    <strong id="stream-title"><?php echo e($streamTitle); ?></strong>
                </div>
            </div>

            <div class="player-controls">
                <button id="player-toggle" class="btn-icon" type="button" aria-label="Play/Pause">
                    <span class="material-symbols-outlined">play_arrow</span>
                </button>
                <input id="player-volume" type="range" min="0" max="1" step="0.01" value="0.8" aria-label="Volume">
            </div>

            <div class="player-right">
                <span class="badge-live">LIVE</span>
                <button id="player-minimize" class="player-toggle-min" type="button" aria-label="Toggle player compact mode">
                    <span class="material-symbols-outlined" style="font-size:18px;">expand_more</span>
                </button>
            </div>
        </div>
    </div>
</div>

<audio id="global-audio" preload="none" crossorigin="anonymous" data-embed-script="<?php echo e($embedScript); ?>" data-base-url="<?php echo e(rtrim(BASE_URL, '/')); ?>">
    <source src="<?php echo e($streamUrl); ?>" type="audio/mpeg">
</audio>
