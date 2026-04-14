<?php
$metaTitle = 'Gallery / Media - EKO FM';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';
$items = gallery_items($category);
?>
<main class="container-xxl py-4">
    <div class="mb-3 reveal">
        <h1 class="mb-1">Gallery / Media</h1>
        <p class="text-muted mb-0">Event photos, studio moments and community outreach highlights.</p>
    </div>

    <div class="d-flex flex-wrap gap-2 mb-4 reveal reveal-delay-1">
        <a class="btn btn-sm <?php echo $category === '' ? 'btn-live' : 'btn-outline-secondary'; ?>" href="<?php echo e(url('gallery')); ?>" data-pjax>All</a>
        <a class="btn btn-sm <?php echo $category === 'event-photos' ? 'btn-live' : 'btn-outline-secondary'; ?>" href="<?php echo e(url('gallery?category=event-photos')); ?>" data-pjax>Event Photos</a>
        <a class="btn btn-sm <?php echo $category === 'studio-shots' ? 'btn-live' : 'btn-outline-secondary'; ?>" href="<?php echo e(url('gallery?category=studio-shots')); ?>" data-pjax>Studio Shots</a>
        <a class="btn btn-sm <?php echo $category === 'community-outreach' ? 'btn-live' : 'btn-outline-secondary'; ?>" href="<?php echo e(url('gallery?category=community-outreach')); ?>" data-pjax>Community Outreach</a>
    </div>

    <div class="row g-3 media-grid">
        <?php foreach ($items as $index => $media): ?>
            <div class="col-6 col-md-4 col-lg-3 reveal reveal-delay-<?php echo e(($index % 3) + 1); ?>">
                <a href="<?php echo e(media_url($media['file_path'])); ?>" target="_blank" class="gallery-card" style="background-image:url('<?php echo e(media_url($media['file_path'])); ?>')"></a>
            </div>
        <?php endforeach; ?>
    </div>
</main>
