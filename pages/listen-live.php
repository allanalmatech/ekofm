<?php
$metaTitle = 'Listen Live - EKO FM';

$fallbackTitle = trim((string) preg_replace('/^now\s*playing\s*:\s*/i', '', setting('radio_stream_title', 'EKO FM Live')));
$currentShow = function_exists('current_program_on_air') ? current_program_on_air() : null;
$nowPlayingName = $currentShow
    ? $currentShow['title']
    : ($fallbackTitle !== '' ? $fallbackTitle : 'EKO FM Live');
?>
<main class="container-xxl py-4">
    <section class="live-page section-card floating-card reveal story-section" data-parallax="0.14">
        <span class="hero-badge mb-3">The Heartbeat of Karamoja</span>
        <h1 class="display-5 fw-bold mb-2">Listen Live</h1>
        <p class="text-muted mb-4">On-Air. Online. On-Ground. Join the community broadcast and stay connected wherever you are.</p>

        <div class="d-inline-flex align-items-center gap-2 mb-3">
            <span class="dot-live"></span>
            <span class="fw-semibold">Live Now</span>
        </div>

        <div class="mb-3 d-flex flex-wrap justify-content-center gap-2">
            <button id="hero-player-toggle" class="btn btn-live btn-lg glow-button">Play Live Stream</button>
            <a class="btn btn-outline-primary btn-lg" href="<?php echo e(url('shows')); ?>" data-pjax>Browse Shows</a>
        </div>

        <p class="mb-0">Now Playing: <strong id="hero-now-playing"><?php echo e($nowPlayingName); ?></strong></p>
    </section>
</main>
