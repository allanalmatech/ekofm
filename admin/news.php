<?php
require_once __DIR__ . '/_init.php';
require_login();
require_permission('news.view');

$adminTitle = 'News Management';
$activeMenu = 'news';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_permission('news.create');
    if (!verify_csrf($_POST['_token'])) {
        flash('error', 'Invalid CSRF token.');
        redirect('admin/news.php');
    }

    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $slug = isset($_POST['slug']) && trim($_POST['slug']) !== '' ? trim($_POST['slug']) : slugify($title);
    $summary = isset($_POST['summary']) ? trim($_POST['summary']) : '';
    $content = isset($_POST['content']) ? trim($_POST['content']) : '';
    $categoryId = isset($_POST['category_id']) ? (int) $_POST['category_id'] : 0;
    $status = isset($_POST['status']) ? trim($_POST['status']) : 'draft';
    $publishDate = isset($_POST['publish_date']) ? trim($_POST['publish_date']) : date('Y-m-d');
    $metaTitle = isset($_POST['meta_title']) ? trim($_POST['meta_title']) : '';
    $metaDescription = isset($_POST['meta_description']) ? trim($_POST['meta_description']) : '';

    $imagePath = isset($_POST['current_image']) ? $_POST['current_image'] : '';
    if (!empty($_FILES['featured_image']['name'])) {
        $up = upload_file($_FILES['featured_image'], array('jpg', 'jpeg', 'png', 'webp'), MAX_IMAGE_UPLOAD, 'news');
        if ($up['ok']) {
            $imagePath = $up['path'];
        }
    }

    if ($id > 0) {
        require_permission('news.edit');
        db_query(
            'UPDATE news_posts SET category_id=?, title=?, slug=?, summary=?, content=?, featured_image=?, publish_date=?, status=?, meta_title=?, meta_description=?, updated_at=NOW() WHERE id=?',
            array($categoryId, $title, $slug, $summary, $content, $imagePath, $publishDate, $status, $metaTitle, $metaDescription, $id)
        );
        log_activity('update', 'news', $id, 'Updated news post: ' . $title);
    } else {
        $user = current_user();
        db_query(
            'INSERT INTO news_posts (category_id, title, slug, summary, content, featured_image, publish_date, status, meta_title, meta_description, created_by, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())',
            array($categoryId, $title, $slug, $summary, $content, $imagePath, $publishDate, $status, $metaTitle, $metaDescription, $user['id'])
        );
        log_activity('create', 'news', (int) db()->lastInsertId(), 'Created news post: ' . $title);
    }

    redirect('admin/news.php');
}

if (isset($_GET['delete']) && has_permission('news.delete')) {
    $id = (int) $_GET['delete'];
    db_query('DELETE FROM news_posts WHERE id=?', array($id));
    log_activity('delete', 'news', $id, 'Deleted news post #' . $id);
    redirect('admin/news.php');
}

$cats = db_query('SELECT * FROM news_categories ORDER BY name ASC')->fetchAll();
$rows = db_query('SELECT n.*, c.name AS category_name FROM news_posts n LEFT JOIN news_categories c ON c.id = n.category_id ORDER BY n.id DESC')->fetchAll();

include __DIR__ . '/../templates/admin_header.php';
?>

<div class="panel">
    <?php $okMsg = flash('success'); if ($okMsg): ?><div class="alert alert-success"><?php echo e($okMsg); ?></div><?php endif; ?>
    <?php $errMsg = flash('error'); if ($errMsg): ?><div class="alert alert-danger"><?php echo e($errMsg); ?></div><?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">News List</h5>
        <?php if (has_permission('news.create')): ?>
            <button type="button" class="btn btn-warning btn-sm" id="addNewsBtn" data-bs-toggle="modal" data-bs-target="#newsModal">
                <span class="material-symbols-outlined align-middle" style="font-size:18px;">add</span>
                Add News
            </button>
        <?php endif; ?>
    </div>

    <div class="table-responsive">
        <table class="table table-sm align-middle">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!$rows): ?>
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">No news posts yet.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($rows as $r): ?>
                    <tr>
                        <td>
                            <strong><?php echo e($r['title']); ?></strong>
                            <?php if (!empty($r['slug'])): ?><br><small class="text-muted"><?php echo e($r['slug']); ?></small><?php endif; ?>
                        </td>
                        <td><?php echo e($r['category_name']); ?></td>
                        <td><?php echo e($r['status']); ?></td>
                        <td><?php echo e(format_date($r['publish_date'])); ?></td>
                        <td class="text-end">
                            <?php if (has_permission('news.edit')): ?>
                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-primary edit-news-btn"
                                    data-bs-toggle="modal"
                                    data-bs-target="#newsModal"
                                    data-id="<?php echo e($r['id']); ?>"
                                    data-title="<?php echo e($r['title']); ?>"
                                    data-slug="<?php echo e($r['slug']); ?>"
                                    data-summary="<?php echo e($r['summary']); ?>"
                                    data-content="<?php echo e($r['content']); ?>"
                                    data-category-id="<?php echo e((int) $r['category_id']); ?>"
                                    data-status="<?php echo e($r['status']); ?>"
                                    data-publish-date="<?php echo e(substr($r['publish_date'], 0, 10)); ?>"
                                    data-meta-title="<?php echo e($r['meta_title']); ?>"
                                    data-meta-description="<?php echo e($r['meta_description']); ?>"
                                    data-current-image="<?php echo e($r['featured_image']); ?>"
                                >
                                    Edit
                                </button>
                            <?php endif; ?>

                            <?php if (has_permission('news.delete')): ?>
                                <a href="?delete=<?php echo e($r['id']); ?>" data-confirm="Delete this post?" class="btn btn-sm btn-outline-danger">Delete</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="newsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newsModalTitle">Add News</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
                    <input type="hidden" name="id" id="newsId" value="0">
                    <input type="hidden" name="current_image" id="newsCurrentImage" value="">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Title</label>
                            <input class="form-control" name="title" id="newsTitle" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Slug</label>
                            <input class="form-control" name="slug" id="newsSlug" placeholder="auto-from-title">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Category</label>
                            <select class="form-select" name="category_id" id="newsCategoryId">
                                <?php foreach ($cats as $c): ?>
                                    <option value="<?php echo e($c['id']); ?>"><?php echo e($c['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" id="newsStatus">
                                <option value="draft">Draft</option>
                                <option value="published">Published</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Publish Date</label>
                            <input class="form-control" type="date" name="publish_date" id="newsPublishDate" value="<?php echo e(date('Y-m-d')); ?>">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Summary</label>
                            <textarea class="form-control" name="summary" id="newsSummary" rows="2"></textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Content</label>
                            <textarea class="form-control" name="content" id="newsContent" rows="7"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Featured Image</label>
                            <input class="form-control" type="file" name="featured_image" accept=".jpg,.jpeg,.png,.webp">
                            <small class="text-muted d-block mt-1" id="newsImageHint">Upload image (jpg, jpeg, png, webp).</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Meta Title</label>
                            <input class="form-control" name="meta_title" id="newsMetaTitle">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Meta Description</label>
                            <textarea class="form-control" name="meta_description" id="newsMetaDescription" rows="2"></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-warning">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function () {
    var addBtn = document.getElementById('addNewsBtn');
    var editButtons = document.querySelectorAll('.edit-news-btn');

    var modalTitle = document.getElementById('newsModalTitle');
    var idInput = document.getElementById('newsId');
    var currentImageInput = document.getElementById('newsCurrentImage');
    var titleInput = document.getElementById('newsTitle');
    var slugInput = document.getElementById('newsSlug');
    var summaryInput = document.getElementById('newsSummary');
    var contentInput = document.getElementById('newsContent');
    var categoryInput = document.getElementById('newsCategoryId');
    var statusInput = document.getElementById('newsStatus');
    var publishDateInput = document.getElementById('newsPublishDate');
    var metaTitleInput = document.getElementById('newsMetaTitle');
    var metaDescriptionInput = document.getElementById('newsMetaDescription');
    var imageHint = document.getElementById('newsImageHint');

    function setForm(data) {
        modalTitle.textContent = data.id > 0 ? 'Edit News' : 'Add News';
        idInput.value = String(data.id || 0);
        currentImageInput.value = data.currentImage || '';
        titleInput.value = data.title || '';
        slugInput.value = data.slug || '';
        summaryInput.value = data.summary || '';
        contentInput.value = data.content || '';
        categoryInput.value = String(data.categoryId || categoryInput.value);
        statusInput.value = data.status || 'draft';
        publishDateInput.value = data.publishDate || '<?php echo e(date('Y-m-d')); ?>';
        metaTitleInput.value = data.metaTitle || '';
        metaDescriptionInput.value = data.metaDescription || '';
        imageHint.textContent = data.currentImage ? ('Current image: ' + data.currentImage) : 'Upload image (jpg, jpeg, png, webp).';
    }

    if (addBtn) {
        addBtn.addEventListener('click', function () {
            setForm({ id: 0, status: 'draft', publishDate: '<?php echo e(date('Y-m-d')); ?>' });
        });
    }

    for (var i = 0; i < editButtons.length; i++) {
        editButtons[i].addEventListener('click', function () {
            setForm({
                id: parseInt(this.getAttribute('data-id') || '0', 10),
                title: this.getAttribute('data-title') || '',
                slug: this.getAttribute('data-slug') || '',
                summary: this.getAttribute('data-summary') || '',
                content: this.getAttribute('data-content') || '',
                categoryId: parseInt(this.getAttribute('data-category-id') || '0', 10),
                status: this.getAttribute('data-status') || 'draft',
                publishDate: this.getAttribute('data-publish-date') || '<?php echo e(date('Y-m-d')); ?>',
                metaTitle: this.getAttribute('data-meta-title') || '',
                metaDescription: this.getAttribute('data-meta-description') || '',
                currentImage: this.getAttribute('data-current-image') || ''
            });
        });
    }
})();
</script>

<?php include __DIR__ . '/../templates/admin_footer.php'; ?>
