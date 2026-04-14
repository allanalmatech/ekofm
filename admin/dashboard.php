<?php
require_once __DIR__ . '/_init.php';
require_login();

$adminTitle = 'Dashboard';
$activeMenu = 'dashboard';

$stats = array(
    'news' => (int) db_query('SELECT COUNT(*) c FROM news_posts')->fetch()['c'],
    'dramas' => (int) db_query('SELECT COUNT(*) c FROM dramas')->fetch()['c'],
    'programs' => (int) db_query('SELECT COUNT(*) c FROM programs')->fetch()['c'],
    'users' => (int) db_query('SELECT COUNT(*) c FROM users')->fetch()['c']
);
$activities = db_query('SELECT a.*, u.name FROM activity_logs a LEFT JOIN users u ON u.id = a.user_id ORDER BY a.id DESC LIMIT 8')->fetchAll();

include __DIR__ . '/../templates/admin_header.php';
?>
<div class="panel soft-glass mb-3">
    <h5 class="mb-1">EKO FM Admin Overview</h5>
    <p class="text-muted mb-0">The Heartbeat of Karamoja - On-Air. Online. On-Ground.</p>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-3"><div class="panel soft-glass stat-card"><small>Total News</small><h2><?php echo e($stats['news']); ?></h2></div></div>
    <div class="col-md-3"><div class="panel soft-glass stat-card"><small>Total Dramas</small><h2><?php echo e($stats['dramas']); ?></h2></div></div>
    <div class="col-md-3"><div class="panel soft-glass stat-card"><small>Total Shows</small><h2><?php echo e($stats['programs']); ?></h2></div></div>
    <div class="col-md-3"><div class="panel soft-glass stat-card"><small>Total Users</small><h2><?php echo e($stats['users']); ?></h2></div></div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="panel soft-glass">
            <h5 class="mb-3">Latest Activity</h5>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead><tr><th>When</th><th>User</th><th>Module</th><th>Action</th><th>Description</th></tr></thead>
                    <tbody>
                    <?php foreach ($activities as $a): ?>
                        <tr><td><?php echo e(format_date($a['created_at'], 'M d, Y H:i')); ?></td><td><?php echo e($a['name']); ?></td><td><?php echo e($a['module_name']); ?></td><td><?php echo e($a['action']); ?></td><td><?php echo e($a['description']); ?></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="panel soft-glass">
            <h5 class="mb-3">Quick Links</h5>
            <div class="d-grid gap-2">
                <a class="quick-link" href="<?php echo e(url('admin/news.php')); ?>">Create News <span>&rarr;</span></a>
                <a class="quick-link" href="<?php echo e(url('admin/programs.php')); ?>">Manage Shows <span>&rarr;</span></a>
                <a class="quick-link" href="<?php echo e(url('admin/radio.php')); ?>">Radio Settings <span>&rarr;</span></a>
                <a class="quick-link" href="<?php echo e(url('admin/settings.php')); ?>">Site Settings <span>&rarr;</span></a>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../templates/admin_footer.php'; ?>
