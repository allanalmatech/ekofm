<?php
$metaTitle = 'Services - ' . APP_NAME;
$items = services_list(true);
?>
<main class="container-xxl py-4">
    <div class="d-flex justify-content-between align-items-end mb-4 reveal">
        <div>
            <span class="hero-badge text-bg-light">Community + Commercial</span>
            <h1 class="mb-2">Services</h1>
            <p class="text-muted mb-0">Production, promotions, and engagement services tailored for Karamoja audiences.</p>
        </div>
    </div>
    <div class="row g-4">
        <?php if (!$items): ?>
            <div class="col-12 reveal reveal-delay-1">
                <div class="section-card text-center p-4">
                    <h5 class="mb-1">No services published yet</h5>
                    <p class="text-muted mb-0">Please check back soon for our latest service offerings.</p>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($items as $index => $s): ?>
                <div class="col-md-6 col-lg-4 reveal reveal-delay-<?php echo e(($index % 3) + 1); ?>">
                    <div class="section-card service-card h-100">
                        <span class="chip mb-2">Service</span>
                        <h5><?php echo e($s['title']); ?></h5>
                        <p class="text-muted mb-0"><?php echo e($s['description']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>
