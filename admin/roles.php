<?php
require_once __DIR__ . '/_init.php';
require_login();
require_permission('roles.manage');
$adminTitle='Roles & Permissions';
$activeMenu='roles';

if($_SERVER['REQUEST_METHOD']==='POST'){
    if(!verify_csrf($_POST['_token'])){redirect('admin/roles.php');}
    $id=(int)$_POST['id'];
    $name=trim($_POST['name']);
    $slug=trim($_POST['slug'])?:slugify($name);
    $permissions=isset($_POST['permissions'])?$_POST['permissions']:array();
    if($id>0){db_query('UPDATE roles SET name=?,slug=?,updated_at=NOW() WHERE id=?',array($name,$slug,$id));}
    else {db_query('INSERT INTO roles (name,slug,created_at,updated_at) VALUES (?,?,NOW(),NOW())',array($name,$slug));$id=(int)db()->lastInsertId();}
    db_query('DELETE FROM role_permissions WHERE role_id=?',array($id));
    foreach($permissions as $pid){db_query('INSERT INTO role_permissions (role_id,permission_id) VALUES (?,?)',array($id,(int)$pid));}
    redirect('admin/roles.php');
}
if(isset($_GET['delete'])){db_query('DELETE FROM roles WHERE id=? AND slug<>?',array((int)$_GET['delete'],'super_admin'));redirect('admin/roles.php');}
$edit=isset($_GET['edit'])?db_query('SELECT * FROM roles WHERE id=?',array((int)$_GET['edit']))->fetch():null;
$roles=db_query('SELECT * FROM roles ORDER BY id ASC')->fetchAll();
$perms=db_query('SELECT * FROM permissions ORDER BY slug ASC')->fetchAll();
$rolePerm=array();
if($edit){$rows=db_query('SELECT permission_id FROM role_permissions WHERE role_id=?',array((int)$edit['id']))->fetchAll();foreach($rows as $r){$rolePerm[(int)$r['permission_id']]=true;}}
include __DIR__ . '/../templates/admin_header.php';
?>
<div class="row g-3"><div class="col-lg-5"><div class="panel"><h5><?php echo $edit?'Edit':'Add'; ?> Role</h5><form method="post"><input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>"><input type="hidden" name="id" value="<?php echo e($edit?$edit['id']:0); ?>"><input class="form-control mb-2" name="name" placeholder="Role name" value="<?php echo e($edit?$edit['name']:''); ?>" required><input class="form-control mb-2" name="slug" placeholder="role_slug" value="<?php echo e($edit?$edit['slug']:''); ?>"><div class="small fw-bold mb-1">Permissions</div><div style="max-height:280px;overflow:auto" class="border rounded p-2 mb-2"><?php foreach($perms as $p): ?><div class="form-check"><input class="form-check-input" type="checkbox" name="permissions[]" value="<?php echo e($p['id']); ?>" id="perm_<?php echo e($p['id']); ?>" <?php echo isset($rolePerm[$p['id']])?'checked':''; ?>><label class="form-check-label" for="perm_<?php echo e($p['id']); ?>"><?php echo e($p['slug']); ?></label></div><?php endforeach; ?></div><button class="btn btn-warning">Save Role</button></form></div></div><div class="col-lg-7"><div class="panel"><table class="table table-sm"><thead><tr><th>Role</th><th>Slug</th><th></th></tr></thead><tbody><?php foreach($roles as $r): ?><tr><td><?php echo e($r['name']); ?></td><td><?php echo e($r['slug']); ?></td><td><a class="btn btn-sm btn-outline-primary" href="?edit=<?php echo e($r['id']); ?>">Edit</a> <?php if($r['slug']!=='super_admin'): ?><a class="btn btn-sm btn-outline-danger" href="?delete=<?php echo e($r['id']); ?>" data-confirm="Delete role?">Delete</a><?php endif; ?></td></tr><?php endforeach; ?></tbody></table></div></div></div>
<?php include __DIR__ . '/../templates/admin_footer.php'; ?>
