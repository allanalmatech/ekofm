<?php
if (!isset($adminTitle)) {
    $adminTitle = 'Admin';
}
if (!isset($activeMenu)) {
    $activeMenu = 'dashboard';
}
$u = current_user();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e($adminTitle); ?> - Eko FM Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo e(url('assets/css/admin.css')); ?>" rel="stylesheet">
</head>
<body>
<div class="admin-layout">
    <?php include __DIR__ . '/admin_sidebar.php'; ?>
    <main class="admin-main">
        <div class="admin-topbar d-flex justify-content-between align-items-center">
            <div><h5 class="mb-0"><?php echo e($adminTitle); ?></h5></div>
            <div class="d-flex align-items-center gap-3"><small><?php echo e($u ? $u['name'] : ''); ?></small><a href="<?php echo e(url('admin/logout.php')); ?>" class="btn btn-sm btn-outline-danger">Logout</a></div>
        </div>
