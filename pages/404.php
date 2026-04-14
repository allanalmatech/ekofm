<?php
$metaTitle = 'Not Found - ' . APP_NAME;
?>
<main class="container py-5 text-center">
    <div class="section-card reveal">
        <h1>404</h1>
        <p class="text-muted">The page you are looking for does not exist.</p>
    </div>
    <a class="btn btn-live" href="<?php echo e(url('/')); ?>" data-pjax>Back Home</a>
</main>
