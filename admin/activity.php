<?php
require_once __DIR__ . '/_init.php';
require_login();
require_permission('settings.manage');
$adminTitle='Activity Logs';
$activeMenu='activity';
$rows=db_query('SELECT a.*, u.name FROM activity_logs a LEFT JOIN users u ON u.id=a.user_id ORDER BY a.id DESC LIMIT 200')->fetchAll();
include __DIR__ . '/../templates/admin_header.php';
?>
<div class="panel"><table class="table table-sm"><thead><tr><th>Date</th><th>User</th><th>Module</th><th>Action</th><th>Description</th><th>IP</th></tr></thead><tbody><?php foreach($rows as $r): ?><tr><td><?php echo e(format_date($r['created_at'],'M d, Y H:i')); ?></td><td><?php echo e($r['name']); ?></td><td><?php echo e($r['module_name']); ?></td><td><?php echo e($r['action']); ?></td><td><?php echo e($r['description']); ?></td><td><?php echo e($r['ip_address']); ?></td></tr><?php endforeach; ?></tbody></table></div>
<?php include __DIR__ . '/../templates/admin_footer.php'; ?>
