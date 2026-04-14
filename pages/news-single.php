<?php
$post = news_find_by_slug($slug);
if (!$post) {
    http_response_code(404);
    include __DIR__ . '/404.php';
    return;
}
$metaTitle = $post['meta_title'] ? $post['meta_title'] : $post['title'] . ' - ' . APP_NAME;
$metaDescription = $post['meta_description'] ? $post['meta_description'] : $post['summary'];
$metaImage = media_url($post['social_image'] ? $post['social_image'] : $post['featured_image']);
?>
<main class="container py-4">
    <article class="section-card reveal">
        <a class="small" href="<?php echo e(url('news')); ?>" data-pjax>Back to News</a>
        <div class="media-cover mb-4" style="height:300px;background-image:url('<?php echo e(media_url($post['featured_image'])); ?>')"></div>
        <h1><?php echo e($post['title']); ?></h1>
        <p class="text-muted"><?php echo e($post['category_name']); ?> • <?php echo e(format_date($post['publish_date'])); ?> • <?php echo e($post['author_name']); ?></p>
        <div><?php echo nl2br(e($post['content'])); ?></div>
    </article>
</main>
