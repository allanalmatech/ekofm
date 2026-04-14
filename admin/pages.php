<?php
require_once __DIR__ . '/_init.php';
require_login();
require_permission('pages.manage');

$adminTitle = 'Pages & Sections';
$activeMenu = 'pages';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['_token'])) {
        redirect('admin/pages.php');
    }

    if ($_POST['form_type'] === 'page') {
        $id = (int) $_POST['id'];
        $title = trim($_POST['title']);
        $slug = trim($_POST['slug']) ?: slugify($title);
        $content = trim($_POST['content']);
        $metaTitle = trim($_POST['meta_title']);
        $metaDesc = trim($_POST['meta_description']);
        $status = (int) $_POST['status'];

        if ($id > 0) {
            db_query('UPDATE pages SET title=?, slug=?, content=?, meta_title=?, meta_description=?, status=?, updated_at=NOW() WHERE id=?', array($title, $slug, $content, $metaTitle, $metaDesc, $status, $id));
        } else {
            db_query('INSERT INTO pages (title, slug, content, meta_title, meta_description, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())', array($title, $slug, $content, $metaTitle, $metaDesc, $status));
        }
    }

    if ($_POST['form_type'] === 'home_section') {
        db_query('UPDATE homepage_sections SET sort_order=?, status=?, section_title=? WHERE id=?', array((int) $_POST['sort_order'], (int) $_POST['status'], trim($_POST['section_title']), (int) $_POST['id']));
    }

    redirect('admin/pages.php');
}

if (isset($_GET['delete'])) {
    db_query('DELETE FROM pages WHERE id=?', array((int) $_GET['delete']));
    redirect('admin/pages.php');
}

$edit = isset($_GET['edit']) ? db_query('SELECT * FROM pages WHERE id=?', array((int) $_GET['edit']))->fetch() : null;
$pages = db_query('SELECT * FROM pages ORDER BY id DESC')->fetchAll();
$homeSections = db_query('SELECT * FROM homepage_sections ORDER BY sort_order ASC')->fetchAll();

include __DIR__ . '/../templates/admin_header.php';
?>

<div class="row g-3">
    <div class="col-lg-5">
        <div class="panel">
            <h5><?php echo $edit ? 'Edit' : 'Add'; ?> Page</h5>
            <form method="post">
                <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
                <input type="hidden" name="form_type" value="page">
                <input type="hidden" name="id" value="<?php echo e($edit ? $edit['id'] : 0); ?>">

                <div class="mb-2"><label class="form-label">Title</label><input class="form-control" name="title" value="<?php echo e($edit ? $edit['title'] : ''); ?>" required></div>
                <div class="mb-2"><label class="form-label">Slug</label><input class="form-control" name="slug" value="<?php echo e($edit ? $edit['slug'] : ''); ?>"></div>
                <div class="mb-2"><label class="form-label">Content</label><textarea class="form-control" rows="5" name="content"><?php echo e($edit ? $edit['content'] : ''); ?></textarea></div>
                <div class="mb-2"><label class="form-label">Meta Title</label><input class="form-control" name="meta_title" value="<?php echo e($edit ? $edit['meta_title'] : ''); ?>"></div>
                <div class="mb-3"><label class="form-label">Meta Description</label><textarea class="form-control" rows="2" name="meta_description"><?php echo e($edit ? $edit['meta_description'] : ''); ?></textarea></div>
                <div class="mb-3"><label class="form-label">Status</label><select class="form-select" name="status"><option value="1" <?php echo ($edit && (int) $edit['status'] === 1) ? 'selected' : ''; ?>>Published</option><option value="0" <?php echo ($edit && (int) $edit['status'] === 0) ? 'selected' : ''; ?>>Draft</option></select></div>

                <button class="btn btn-warning">Save Page</button>
            </form>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="panel mb-3">
            <h5>Pages</h5>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead><tr><th>Title</th><th>Slug</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                    <?php foreach ($pages as $p): ?>
                        <tr>
                            <td><?php echo e($p['title']); ?></td>
                            <td><?php echo e($p['slug']); ?></td>
                            <td><?php echo (int) $p['status'] ? 'Published' : 'Draft'; ?></td>
                            <td>
                                <a class="btn btn-sm btn-outline-primary" href="?edit=<?php echo e($p['id']); ?>">Edit</a>
                                <a class="btn btn-sm btn-outline-danger" href="?delete=<?php echo e($p['id']); ?>" data-confirm="Delete page?">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="panel">
            <h5>Homepage Sections</h5>
            <p class="text-muted small">Control homepage block ordering and visibility.</p>
            <?php foreach ($homeSections as $s): ?>
                <form method="post" class="row g-2 align-items-center mb-2">
                    <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
                    <input type="hidden" name="form_type" value="home_section">
                    <input type="hidden" name="id" value="<?php echo e($s['id']); ?>">
                    <div class="col-4"><input class="form-control form-control-sm" name="section_title" value="<?php echo e($s['section_title']); ?>"></div>
                    <div class="col-3"><input class="form-control form-control-sm" type="number" name="sort_order" value="<?php echo e($s['sort_order']); ?>"></div>
                    <div class="col-3"><select class="form-select form-select-sm" name="status"><option value="1" <?php echo ((int) $s['status'] === 1) ? 'selected' : ''; ?>>Show</option><option value="0" <?php echo ((int) $s['status'] === 0) ? 'selected' : ''; ?>>Hide</option></select></div>
                    <div class="col-2"><button class="btn btn-sm btn-outline-primary">Save</button></div>
                </form>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../templates/admin_footer.php'; ?>
