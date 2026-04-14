<?php
require_once __DIR__ . '/_init.php';
require_login();
require_permission('ratecard.manage');
$adminTitle = 'Rate Card';
$activeMenu = 'rate-card';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['_token'])) { redirect('admin/ratecard.php'); }
    $id=(int)$_POST['id'];
    $v=array(trim($_POST['category_name']),trim($_POST['title']),trim($_POST['description']),trim($_POST['price_label']),(int)$_POST['sort_order'],(int)$_POST['status']);
    if($id>0){$v[]=$id;db_query('UPDATE rate_cards SET category_name=?,title=?,description=?,price_label=?,sort_order=?,status=?,updated_at=NOW() WHERE id=?',$v);} else {db_query('INSERT INTO rate_cards (category_name,title,description,price_label,sort_order,status,created_at,updated_at) VALUES (?,?,?,?,?,?,NOW(),NOW())',$v);} redirect('admin/ratecard.php');
}
if(isset($_GET['delete'])){db_query('DELETE FROM rate_cards WHERE id=?',array((int)$_GET['delete']));redirect('admin/ratecard.php');}
$edit=isset($_GET['edit'])?db_query('SELECT * FROM rate_cards WHERE id=?',array((int)$_GET['edit']))->fetch():null;
$rows=db_query('SELECT * FROM rate_cards ORDER BY category_name, sort_order')->fetchAll();
include __DIR__ . '/../templates/admin_header.php';
?>
<div class="row g-3"><div class="col-lg-4"><div class="panel"><h5><?php echo $edit?'Edit':'Add'; ?> Rate Item</h5><form method="post"><input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>"><input type="hidden" name="id" value="<?php echo e($edit?$edit['id']:0); ?>"><input class="form-control mb-2" name="category_name" placeholder="Category" value="<?php echo e($edit?$edit['category_name']:'Advertising'); ?>"><input class="form-control mb-2" name="title" placeholder="Title" required value="<?php echo e($edit?$edit['title']:''); ?>"><textarea class="form-control mb-2" name="description" rows="2"><?php echo e($edit?$edit['description']:''); ?></textarea><input class="form-control mb-2" name="price_label" placeholder="Price label" value="<?php echo e($edit?$edit['price_label']:'₦0'); ?>"><input class="form-control mb-2" type="number" name="sort_order" value="<?php echo e($edit?$edit['sort_order']:1); ?>"><select class="form-select mb-2" name="status"><option value="1">Active</option><option value="0" <?php echo ($edit && (int)$edit['status']===0)?'selected':''; ?>>Inactive</option></select><button class="btn btn-warning">Save</button></form></div></div><div class="col-lg-8"><div class="panel"><table class="table table-sm"><thead><tr><th>Category</th><th>Title</th><th>Price</th><th></th></tr></thead><tbody><?php foreach($rows as $r): ?><tr><td><?php echo e($r['category_name']); ?></td><td><?php echo e($r['title']); ?></td><td><?php echo e($r['price_label']); ?></td><td><a class="btn btn-sm btn-outline-primary" href="?edit=<?php echo e($r['id']); ?>">Edit</a> <a class="btn btn-sm btn-outline-danger" href="?delete=<?php echo e($r['id']); ?>" data-confirm="Delete?">Delete</a></td></tr><?php endforeach; ?></tbody></table></div></div></div>
<?php include __DIR__ . '/../templates/admin_footer.php'; ?>
