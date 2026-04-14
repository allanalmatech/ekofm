<?php
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$metaTitle = 'Search - ' . APP_NAME;
$newsData = news_paginated(1, 20, $q);
?>
<main class="container py-4">
    <div class="mb-3 reveal">
        <h1 class="mb-2">Search</h1>
        <p class="text-muted mb-0">Find latest stories and updates quickly.</p>
    </div>
    <form class="mb-4 reveal reveal-delay-1" method="get" action="<?php echo e(url('search')); ?>"><input type="hidden" name="route" value="search"><input class="form-control" name="q" value="<?php echo e($q); ?>" placeholder="Search for news and content"></form>
    <div class="row g-4">
        <?php if (!$newsData['items']): ?>
            <div class="col-12 reveal reveal-delay-2">
                <div class="section-card text-center p-4">
                    <h5 class="mb-1">No results found</h5>
                    <p class="text-muted mb-0">Try another keyword or browse the latest news directly.</p>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($newsData['items'] as $index => $n): ?>
                <div class="col-md-6 reveal reveal-delay-<?php echo e(($index % 3) + 1); ?>">
                    <div class="section-card news-card">
                        <h5><a href="<?php echo e(url('news/' . $n['slug'])); ?>" data-pjax><?php echo e($n['title']); ?></a></h5>
                        <p class="text-muted mb-0"><?php echo e($n['summary']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>
