<?php
$metaTitle = 'News - ' . APP_NAME;
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$pageNo = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$newsData = news_paginated($pageNo, 6, $search);
$items = $newsData['items'];
$totalPages = (int) ceil($newsData['total'] / $newsData['per_page']);
?>
<main class="container-xxl py-4 story-section">
    <div class="d-flex justify-content-between align-items-center mb-4 reveal">
        <div>
            <h1 class="mb-1">News & Blog</h1>
            <p class="text-muted mb-0">Health tips, community stories, event coverage and station updates.</p>
        </div>
    </div>

    <form class="mb-4 reveal" action="<?php echo e(url('news')); ?>" method="get">
        <input type="hidden" name="route" value="news">
        <div class="input-group">
            <span class="input-group-text bg-white"><span class="material-symbols-outlined">search</span></span>
            <input class="form-control" name="q" value="<?php echo e($search); ?>" placeholder="Search news">
        </div>
    </form>

    <div class="row g-4">
        <?php if (!$items): ?>
            <div class="col-12 reveal reveal-delay-1">
                <div class="section-card text-center p-4">
                    <h5 class="mb-1">No posts match your search</h5>
                    <p class="text-muted mb-0">Try a different keyword or clear filters to view all stories.</p>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($items as $index => $n): ?>
                <article class="col-md-6 col-lg-4 reveal reveal-delay-<?php echo e(($index % 3) + 1); ?>">
                    <div class="section-card news-card floating-card h-100">
                        <div class="media-cover mb-3" style="background-image:url('<?php echo e(media_url($n['featured_image'])); ?>')"></div>
                        <small class="text-primary fw-semibold"><?php echo e($n['category_name']); ?></small>
                        <small class="text-muted d-block mb-2"><?php echo e(format_date($n['publish_date'])); ?></small>
                        <h5><a href="<?php echo e(url('news/' . $n['slug'])); ?>" data-pjax><?php echo e($n['title']); ?></a></h5>
                        <p class="text-muted"><?php echo e($n['summary']); ?></p>
                        <a href="<?php echo e(url('news/' . $n['slug'])); ?>" data-pjax class="fw-semibold">Read more</a>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="mt-4 d-flex gap-2 reveal">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a class="btn btn-sm <?php echo $i === $pageNo ? 'btn-live' : 'btn-outline-secondary'; ?>" href="<?php echo e(url('news?page=' . $i . ($search ? '&q=' . urlencode($search) : ''))); ?>" data-pjax><?php echo e($i); ?></a>
        <?php endfor; ?>
    </div>
</main>
