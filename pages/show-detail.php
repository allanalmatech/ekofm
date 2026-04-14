<?php
$show = show_by_slug($slug);
if (!$show) {
    http_response_code(404);
    include __DIR__ . '/404.php';
    return;
}

$metaTitle = $show['title'] . ' - EKO FM Shows';
$focusX = (int) (isset($show['cover_focus_x']) ? $show['cover_focus_x'] : 50);
$focusY = (int) (isset($show['cover_focus_y']) ? $show['cover_focus_y'] : 50);
?>
<main class="container py-4">
    <section class="hero mb-4 reveal story-section" data-parallax="0.2" style="min-height:420px;background-image:url('<?php echo e(media_url($show['cover_image'])); ?>');background-position:<?php echo e($focusX); ?>% <?php echo e($focusY); ?>%;">
        <div class="hero-content glass">
            <a class="btn btn-glass btn-sm mb-3" href="<?php echo e(url('shows')); ?>" data-pjax>Back to Shows</a>
            <h1 class="mb-2"><?php echo e($show['title']); ?></h1>
            <p class="show-time mb-2"><?php echo e($show['day_of_week']); ?> | <?php echo e(substr($show['start_time'], 0, 5)); ?>-<?php echo e(substr($show['end_time'], 0, 5)); ?></p>
            <p class="mb-0"><?php echo e($show['description']); ?></p>
        </div>
    </section>

    <section class="section-card floating-card reveal story-section">
        <h4 class="mb-3">About This Show</h4>
        <p class="text-muted"><?php echo e($show['description']); ?></p>

        <?php if ($show['slug'] === 'eko-doctor'): ?>
            <h5 class="mt-4">Highlights</h5>
            <ul>
                <li>Expert advice</li>
                <li>Live listener interaction</li>
                <li>Community health education</li>
            </ul>
        <?php endif; ?>

        <div class="d-flex flex-wrap gap-2 mt-3">
            <a class="btn btn-live" href="<?php echo e(url('listen-live')); ?>" data-pjax>Listen Live</a>
            <a class="btn btn-outline-primary" href="<?php echo e(url('advertise-partner')); ?>" data-pjax>Sponsor This Show</a>
        </div>
    </section>
</main>
