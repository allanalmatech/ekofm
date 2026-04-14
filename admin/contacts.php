<?php
require_once __DIR__ . '/_init.php';
require_login();
require_permission('contact.manage');
$adminTitle='Contact Messages';
$activeMenu='contacts';

if(isset($_GET['mark'])){db_query('UPDATE contact_messages SET status=? WHERE id=?', array('read',(int)$_GET['mark']));redirect('admin/contacts.php');}
$rows=db_query('SELECT * FROM contact_messages ORDER BY id DESC')->fetchAll();
include __DIR__ . '/../templates/admin_header.php';
?>
<div class="panel"><table class="table table-sm"><thead><tr><th>Date</th><th>Name</th><th>Email</th><th>Subject</th><th>Status</th><th></th></tr></thead><tbody><?php foreach($rows as $r): ?><tr><td><?php echo e(format_date($r['created_at'],'M d H:i')); ?></td><td><?php echo e($r['name']); ?></td><td><?php echo e($r['email']); ?></td><td><?php echo e($r['subject']); ?></td><td><?php echo e($r['status']); ?></td><td><a class="btn btn-sm btn-outline-primary" href="?mark=<?php echo e($r['id']); ?>">Mark Read</a></td></tr><tr><td colspan="6" class="text-muted"><?php echo e($r['message']); ?></td></tr><?php endforeach; ?></tbody></table></div>
<?php include __DIR__ . '/../templates/admin_footer.php'; ?>
