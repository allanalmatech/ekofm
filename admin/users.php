<?php
require_once __DIR__ . '/_init.php';
require_login();
require_permission('users.manage');
$adminTitle='Users';
$activeMenu='users';

if($_SERVER['REQUEST_METHOD']==='POST'){
    if(!verify_csrf($_POST['_token'])){redirect('admin/users.php');}
    $id=(int)$_POST['id'];
    $name=trim($_POST['name']);
    $email=trim($_POST['email']);
    $role=(int)$_POST['role_id'];
    $status=(int)$_POST['status'];
    $pass=trim($_POST['password']);
    $userPerms=isset($_POST['permissions'])?$_POST['permissions']:array();
    if($id>0){
        if($pass!=='') db_query('UPDATE users SET name=?,email=?,password=?,role_id=?,status=?,updated_at=NOW() WHERE id=?',array($name,$email,password_hash($pass,PASSWORD_BCRYPT),$role,$status,$id));
        else db_query('UPDATE users SET name=?,email=?,role_id=?,status=?,updated_at=NOW() WHERE id=?',array($name,$email,$role,$status,$id));
    } else {
        db_query('INSERT INTO users (name,email,password,role_id,status,created_at,updated_at) VALUES (?,?,?,?,?,NOW(),NOW())',array($name,$email,password_hash($pass ?: 'Pass@123',PASSWORD_BCRYPT),$role,$status));
        $id=(int)db()->lastInsertId();
    }
    db_query('DELETE FROM user_permissions WHERE user_id=?',array($id));
    foreach($userPerms as $pid){db_query('INSERT INTO user_permissions (user_id,permission_id) VALUES (?,?)',array($id,(int)$pid));}
    redirect('admin/users.php');
}
if(isset($_GET['delete'])){db_query('DELETE FROM users WHERE id=?',array((int)$_GET['delete']));redirect('admin/users.php');}
$edit=isset($_GET['edit'])?db_query('SELECT * FROM users WHERE id=?',array((int)$_GET['edit']))->fetch():null;
$roles=db_query('SELECT * FROM roles ORDER BY name')->fetchAll();
$users=db_query('SELECT u.*, r.name role_name FROM users u LEFT JOIN roles r ON r.id=u.role_id ORDER BY u.id DESC')->fetchAll();
$perms=db_query('SELECT * FROM permissions ORDER BY slug')->fetchAll();
$userPerm=array();
if($edit){$rows=db_query('SELECT permission_id FROM user_permissions WHERE user_id=?',array((int)$edit['id']))->fetchAll();foreach($rows as $r){$userPerm[(int)$r['permission_id']]=true;}}
include __DIR__ . '/../templates/admin_header.php';
?>
<div class="row g-3"><div class="col-lg-5"><div class="panel"><h5><?php echo $edit?'Edit':'Create'; ?> User</h5><form method="post"><input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>"><input type="hidden" name="id" value="<?php echo e($edit?$edit['id']:0); ?>"><input class="form-control mb-2" name="name" placeholder="Name" value="<?php echo e($edit?$edit['name']:''); ?>" required><input class="form-control mb-2" type="email" name="email" placeholder="Email" value="<?php echo e($edit?$edit['email']:''); ?>" required><input class="form-control mb-2" type="password" name="password" placeholder="Password (leave empty keep old)"><select class="form-select mb-2" name="role_id"><?php foreach($roles as $r): ?><option value="<?php echo e($r['id']); ?>" <?php echo ($edit && $edit['role_id']==$r['id'])?'selected':''; ?>><?php echo e($r['name']); ?></option><?php endforeach; ?></select><select class="form-select mb-2" name="status"><option value="1">Active</option><option value="0" <?php echo ($edit && (int)$edit['status']===0)?'selected':''; ?>>Disabled</option></select><div class="small fw-bold">Extra User Permissions</div><div class="border rounded p-2 mb-2" style="max-height:220px;overflow:auto"><?php foreach($perms as $p): ?><div class="form-check"><input class="form-check-input" type="checkbox" name="permissions[]" value="<?php echo e($p['id']); ?>" id="uperm_<?php echo e($p['id']); ?>" <?php echo isset($userPerm[$p['id']])?'checked':''; ?>><label class="form-check-label" for="uperm_<?php echo e($p['id']); ?>"><?php echo e($p['slug']); ?></label></div><?php endforeach; ?></div><button class="btn btn-warning">Save User</button></form></div></div><div class="col-lg-7"><div class="panel"><table class="table table-sm"><thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th></th></tr></thead><tbody><?php foreach($users as $u): ?><tr><td><?php echo e($u['name']); ?></td><td><?php echo e($u['email']); ?></td><td><?php echo e($u['role_name']); ?></td><td><?php echo (int)$u['status']?'Active':'Disabled'; ?></td><td><a class="btn btn-sm btn-outline-primary" href="?edit=<?php echo e($u['id']); ?>">Edit</a> <a class="btn btn-sm btn-outline-danger" href="?delete=<?php echo e($u['id']); ?>" data-confirm="Delete user?">Delete</a></td></tr><?php endforeach; ?></tbody></table></div></div></div>
<?php include __DIR__ . '/../templates/admin_footer.php'; ?>
