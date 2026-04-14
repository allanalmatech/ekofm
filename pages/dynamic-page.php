<?php
$metaTitle = $dynamicPage['meta_title'] ? $dynamicPage['meta_title'] : $dynamicPage['title'] . ' - ' . APP_NAME;
$metaDescription = $dynamicPage['meta_description'] ? $dynamicPage['meta_description'] : '';
$sections = get_page_sections($dynamicPage['id']);
?>
<main class="container py-4">
    <section class="section-card mb-4"><h1><?php echo e($dynamicPage['title']); ?></h1><p><?php echo nl2br(e($dynamicPage['content'])); ?></p></section>
    <?php foreach ($sections as $section): ?>
        <?php if ($section['is_visible']): ?>
            <section class="section-card mb-3">
                <h3><?php echo e($section['title']); ?></h3>
                <p><?php echo nl2br(e($section['content'])); ?></p>
                <?php if ($section['cta_text'] && $section['cta_link']): ?><a class="btn btn-live" href="<?php echo e($section['cta_link']); ?>"><?php echo e($section['cta_text']); ?></a><?php endif; ?>
            </section>
        <?php endif; ?>
    <?php endforeach; ?>
</main>
