<?php
require_once __DIR__ . '/_init.php';
require_login();
require_permission('media.manage');
$adminTitle='Media Library';
$activeMenu='media';

if($_SERVER['REQUEST_METHOD']==='POST'){
    if(!verify_csrf($_POST['_token'])){redirect('admin/media.php');}
    if(!empty($_FILES['file']['name'])){
        $isAudio = isset($_POST['media_type']) && $_POST['media_type'] === 'audio';
        $category = trim(isset($_POST['category_name']) ? $_POST['category_name'] : '');
        $up = upload_file($_FILES['file'], $isAudio ? array('mp3','wav','ogg','m4a') : array('jpg','jpeg','png','webp','gif'), $isAudio ? MAX_AUDIO_UPLOAD : MAX_IMAGE_UPLOAD, 'media');
        if($up['ok']){
            db_query('INSERT INTO media (title, category_name, file_path, file_type, file_size, created_by, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())', array($_FILES['file']['name'], $category, $up['path'], $isAudio ? 'audio' : 'image', (int)$_FILES['file']['size'], current_user()['id']));
        }
    }
    redirect('admin/media.php');
}
if(isset($_GET['delete'])){db_query('DELETE FROM media WHERE id=?', array((int)$_GET['delete'])); redirect('admin/media.php');}
$rows=db_query('SELECT * FROM media ORDER BY id DESC')->fetchAll();
include __DIR__ . '/../templates/admin_header.php';
?>
<div class="panel mb-3"><h5>Upload Media</h5><form method="post" enctype="multipart/form-data" class="row g-2"><input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>"><div class="col-md-3"><select class="form-select" name="media_type"><option value="image">Image</option><option value="audio">Audio</option></select></div><div class="col-md-3"><input class="form-control" name="category_name" placeholder="Category e.g event-photos"></div><div class="col-md-4"><input class="form-control" type="file" name="file" required></div><div class="col-md-2"><button class="btn btn-warning w-100">Upload</button></div></form></div><div class="panel"><table class="table table-sm"><thead><tr><th>File</th><th>Type</th><th>Category</th><th>Path</th><th></th></tr></thead><tbody><?php foreach($rows as $r): ?><tr><td><?php echo e($r['title']); ?></td><td><?php echo e($r['file_type']); ?></td><td><?php echo e($r['category_name']); ?></td><td><code><?php echo e($r['file_path']); ?></code></td><td><a class="btn btn-sm btn-outline-danger" href="?delete=<?php echo e($r['id']); ?>" data-confirm="Delete media?">Delete</a></td></tr><?php endforeach; ?></tbody></table></div>
<?php include __DIR__ . '/../templates/admin_footer.php'; ?>
