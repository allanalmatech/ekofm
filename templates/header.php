<?php
if (!isset($metaTitle)) {
    $metaTitle = APP_NAME;
}
if (!isset($metaDescription)) {
    $metaDescription = setting('seo_default_description', 'EKO FM - The Heartbeat of Karamoja');
}
if (!isset($bodyClass)) {
    $bodyClass = '';
}

$metaImage = isset($metaImage) ? $metaImage : media_url(setting('social_default_image', ''));
$siteFavicon = setting('site_favicon', '');
$currentRouteSafe = isset($currentRoute) ? $currentRoute : 'home';
$isHomeRoute = $currentRouteSafe === 'home';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e($metaTitle); ?></title>
    <meta name="description" content="<?php echo e($metaDescription); ?>">
    <meta property="og:title" content="<?php echo e($metaTitle); ?>">
    <meta property="og:description" content="<?php echo e($metaDescription); ?>">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="EKO FM">
    <meta property="og:image" content="<?php echo e($metaImage); ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="theme-color" content="#1E3A8A">
    <?php if ($siteFavicon !== ''): ?>
        <link rel="icon" type="image/png" href="<?php echo e(media_url($siteFavicon)); ?>">
    <?php endif; ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo e(url('assets/css/style.css')); ?>" rel="stylesheet">
</head>
<body class="<?php echo e(trim($bodyClass . ' site-body')); ?>" data-route="<?php echo e($currentRouteSafe); ?>" data-home="<?php echo $isHomeRoute ? '1' : '0'; ?>">
<?php include __DIR__ . '/navbar.php'; ?>
<div id="pjax-container" class="site-main">
