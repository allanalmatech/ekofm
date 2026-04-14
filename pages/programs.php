<?php
$metaTitle = 'Programs - ' . APP_NAME;
$items = programs_list(true);
?>
<main class="container-xxl py-4">
    <div class="d-flex justify-content-between align-items-end mb-4 reveal">
        <div>
            <span class="hero-badge text-bg-light">Daily Lineup</span>
            <h1 class="mb-2">Programs Schedule</h1>
            <p class="text-muted mb-0">Discover every show, host, and time slot across the week.</p>
        </div>
    </div>
    <div class="row g-4">
        <?php if (!$items): ?>
            <div class="col-12 reveal reveal-delay-1">
                <div class="section-card text-center p-4">
                    <h5 class="mb-1">No programs available yet</h5>
                    <p class="text-muted mb-0">The latest schedule will appear here as soon as it is published.</p>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($items as $index => $p): ?>
                <div class="col-md-6 col-lg-4 reveal reveal-delay-<?php echo e(($index % 3) + 1); ?>">
                    <div class="section-card show-card h-100">
                        <h5><?php echo e($p['title']); ?></h5>
                        <p class="show-time mb-2"><?php echo e($p['day_of_week']); ?> <?php echo e(substr($p['start_time'], 0, 5)); ?> - <?php echo e(substr($p['end_time'], 0, 5)); ?></p>
                        <p class="mb-2"><strong>Host:</strong> <?php echo e($p['presenter']); ?></p>
                        <p class="text-muted mb-0"><?php echo e($p['description']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>
