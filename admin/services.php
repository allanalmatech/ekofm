<?php
require_once __DIR__ . '/_init.php';
require_login();
require_permission('services.manage');
$adminTitle = 'Services';
$activeMenu = 'services';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['_token'])) { redirect('admin/services.php'); }
    $id = (int) $_POST['id'];
    $title = trim($_POST['title']);
    $desc = trim($_POST['description']);
    $icon = trim($_POST['icon_class']);
    $sort = (int) $_POST['sort_order'];
    $status = (int) $_POST['status'];
    if ($id > 0) db_query('UPDATE services SET title=?,description=?,icon_class=?,sort_order=?,status=?,updated_at=NOW() WHERE id=?', array($title,$desc,$icon,$sort,$status,$id));
    else db_query('INSERT INTO services (title,description,icon_class,sort_order,status,created_at,updated_at) VALUES (?,?,?,?,?,NOW(),NOW())', array($title,$desc,$icon,$sort,$status));
    redirect('admin/services.php');
}
if (isset($_GET['delete'])) { db_query('DELETE FROM services WHERE id=?', array((int)$_GET['delete'])); redirect('admin/services.php'); }
$edit = isset($_GET['edit']) ? db_query('SELECT * FROM services WHERE id=?', array((int)$_GET['edit']))->fetch() : null;
$rows = db_query('SELECT * FROM services ORDER BY sort_order ASC, id DESC')->fetchAll();
include __DIR__ . '/../templates/admin_header.php';
?>
<div class="row g-3"><div class="col-lg-4"><div class="panel"><h5><?php echo $edit?'Edit':'Add'; ?> Service</h5><form method="post"><input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>"><input type="hidden" name="id" value="<?php echo e($edit?$edit['id']:0); ?>"><input class="form-control mb-2" name="title" placeholder="Title" value="<?php echo e($edit?$edit['title']:''); ?>" required><textarea class="form-control mb-2" name="description" rows="3" placeholder="Description"><?php echo e($edit?$edit['description']:''); ?></textarea><input class="form-control mb-2" name="icon_class" placeholder="Icon class" value="<?php echo e($edit?$edit['icon_class']:''); ?>"><input class="form-control mb-2" type="number" name="sort_order" value="<?php echo e($edit?$edit['sort_order']:1); ?>"><select class="form-select mb-2" name="status"><option value="1">Active</option><option value="0" <?php echo ($edit && (int)$edit['status']===0)?'selected':''; ?>>Inactive</option></select><button class="btn btn-warning">Save</button></form></div></div><div class="col-lg-8"><div class="panel"><table class="table table-sm"><thead><tr><th>Title</th><th>Order</th><th>Status</th><th></th></tr></thead><tbody><?php foreach($rows as $r): ?><tr><td><?php echo e($r['title']); ?></td><td><?php echo e($r['sort_order']); ?></td><td><?php echo (int)$r['status']?'Active':'Inactive'; ?></td><td><a href="?edit=<?php echo e($r['id']); ?>" class="btn btn-sm btn-outline-primary">Edit</a> <a href="?delete=<?php echo e($r['id']); ?>" data-confirm="Delete?" class="btn btn-sm btn-outline-danger">Delete</a></td></tr><?php endforeach; ?></tbody></table></div></div></div>
<?php include __DIR__ . '/../templates/admin_footer.php'; ?>
