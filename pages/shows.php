<?php
$metaTitle = 'Shows - EKO FM';
$shows = programs_list(true);
?>
<main class="container-xxl py-4 story-section">
    <div class="d-flex justify-content-between align-items-end mb-4 reveal">
        <div>
            <h1 class="mb-1">Shows</h1>
            <p class="text-muted mb-0">Music, stories, health, youth and community conversations.</p>
        </div>
        <a href="<?php echo e(url('schedule')); ?>" data-pjax class="btn btn-outline-primary">View Schedule</a>
    </div>

    <div class="row g-4">
        <?php if (!$shows): ?>
            <div class="col-12 reveal reveal-delay-1">
                <div class="section-card text-center p-4">
                    <h5 class="mb-1">No shows available right now</h5>
                    <p class="text-muted mb-0">Please check back for our updated programming lineup.</p>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($shows as $index => $show): ?>
                <?php $focusX = (int) (isset($show['cover_focus_x']) ? $show['cover_focus_x'] : 50); ?>
                <?php $focusY = (int) (isset($show['cover_focus_y']) ? $show['cover_focus_y'] : 50); ?>
                <div class="col-md-6 col-lg-4 reveal reveal-delay-<?php echo e(($index % 3) + 1); ?>">
                    <article class="section-card show-card floating-card position-relative">
                        <div class="show-cover" style="background-image:url('<?php echo e(media_url($show['cover_image'])); ?>');background-position:<?php echo e($focusX); ?>% <?php echo e($focusY); ?>%;"></div>
                        <h5 class="mt-3 mb-1"><?php echo e($show['title']); ?></h5>
                        <p class="show-time mb-2"><?php echo e($show['day_of_week']); ?> | <?php echo e(substr($show['start_time'], 0, 5)); ?>-<?php echo e(substr($show['end_time'], 0, 5)); ?></p>
                        <?php if (!empty($show['tones'])): ?>
                            <div class="d-flex flex-wrap gap-2 mb-2">
                                <?php foreach ($show['tones'] as $tone): ?>
                                    <span class="tone-badge"><?php echo e($tone); ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        <p class="text-muted mb-3"><?php echo e($show['description']); ?></p>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="<?php echo e(url('advertise-partner')); ?>" data-pjax class="btn btn-sm btn-outline-secondary position-relative" style="z-index:2;">Sponsor This Show</a>
                        </div>
                        <a href="<?php echo e(url('shows/' . $show['slug'])); ?>" data-pjax class="stretched-link" aria-label="View details for <?php echo e($show['title']); ?>"></a>
                    </article>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>
