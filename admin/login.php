<?php
require_once __DIR__ . '/_init.php';

if (current_user()) {
    redirect('admin/dashboard.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf(isset($_POST['_token']) ? $_POST['_token'] : '')) {
        flash('error', 'Invalid CSRF token.');
        redirect('admin/login.php');
    }
    $res = auth_login(trim($_POST['email']), trim($_POST['password']));
    if ($res['ok']) {
        redirect('admin/dashboard.php');
    }
    flash('error', $res['message']);
    redirect('admin/login.php');
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Eko FM Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo e(url('assets/css/admin.css')); ?>" rel="stylesheet">
</head>
<body class="d-flex align-items-center" style="min-height:100vh;background:#f6f8ff;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="panel">
                    <h3 class="mb-3">Eko FM Admin</h3>
                    <?php $err = flash('error'); if ($err): ?><div class="alert alert-danger"><?php echo e($err); ?></div><?php endif; ?>
                    <form method="post">
                        <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
                        <div class="mb-3"><label>Email</label><input class="form-control" type="email" name="email" required></div>
                        <div class="mb-3"><label>Password</label><input class="form-control" type="password" name="password" required></div>
                        <button class="btn btn-warning w-100">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
