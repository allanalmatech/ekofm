<?php
$metaTitle = 'Rate Card - ' . APP_NAME;
$items = rate_cards(true);
?>
<main class="container-xxl py-4">
    <div class="d-flex justify-content-between align-items-end mb-4 reveal">
        <div>
            <span class="hero-badge text-bg-light">Transparent Pricing</span>
            <h1 class="mb-2">Rate Card</h1>
            <p class="text-muted mb-0">Simple, clear options for airtime, sponsorships, and branded communication.</p>
        </div>
    </div>
    <div class="row g-4">
        <?php if (!$items): ?>
            <div class="col-12 reveal reveal-delay-1">
                <div class="section-card text-center p-4">
                    <h5 class="mb-1">No pricing items available</h5>
                    <p class="text-muted mb-0">Rate card entries will appear here once published in admin.</p>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($items as $index => $r): ?>
                <div class="col-md-6 col-lg-4 reveal reveal-delay-<?php echo e(($index % 3) + 1); ?>">
                    <div class="section-card h-100">
                        <small class="text-primary fw-semibold"><?php echo e($r['category_name']); ?></small>
                        <h5 class="mt-1"><?php echo e($r['title']); ?></h5>
                        <p class="text-muted mb-3"><?php echo e($r['description']); ?></p>
                        <h4 class="mb-0 gradient-text"><?php echo e($r['price_label']); ?></h4>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>
