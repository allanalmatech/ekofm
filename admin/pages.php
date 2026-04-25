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
        $socialImage = isset($_POST['current_social_image']) ? trim($_POST['current_social_image']) : '';

        if (!empty($_FILES['social_image_file']['name'])) {
            $up = upload_file($_FILES['social_image_file'], array('jpg', 'jpeg', 'png', 'webp'), MAX_IMAGE_UPLOAD, 'pages');
            if ($up['ok']) {
                $socialImage = $up['path'];
            }
        }

        if ($id > 0) {
            db_query('UPDATE pages SET title=?, slug=?, content=?, meta_title=?, meta_description=?, social_image=?, status=?, updated_at=NOW() WHERE id=?', array($title, $slug, $content, $metaTitle, $metaDesc, $socialImage, $status, $id));
        } else {
            db_query('INSERT INTO pages (title, slug, content, meta_title, meta_description, social_image, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())', array($title, $slug, $content, $metaTitle, $metaDesc, $socialImage, $status));
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

<div class="panel mb-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Pages</h5>
        <button type="button" class="btn btn-warning btn-sm" id="addPageBtn" data-bs-toggle="modal" data-bs-target="#pageModal">
            <span class="material-symbols-outlined align-middle" style="font-size:18px;">add</span>
            Add Page
        </button>
    </div>

    <?php if (!$pages): ?>
        <div class="text-center text-muted py-4">No pages added yet.</div>
    <?php else: ?>
        <div class="list-group list-group-flush">
            <?php foreach ($pages as $p): ?>
                <div class="list-group-item px-0 d-flex justify-content-between align-items-start gap-3">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <strong><?php echo e($p['title']); ?></strong>
                            <span class="badge <?php echo (int) $p['status'] === 1 ? 'text-bg-success' : 'text-bg-secondary'; ?>">
                                <?php echo (int) $p['status'] === 1 ? 'Published' : 'Draft'; ?>
                            </span>
                        </div>
                        <div class="text-muted mb-1">/<?php echo e($p['slug']); ?></div>
                        <?php if (!empty($p['meta_title']) || !empty($p['meta_description'])): ?>
                            <small class="text-muted">
                                SEO: <?php echo e($p['meta_title'] ?: '-'); ?>
                                <?php if (!empty($p['meta_description'])): ?> | <?php echo e($p['meta_description']); ?><?php endif; ?>
                            </small>
                        <?php endif; ?>
                    </div>

                    <div class="d-flex gap-2">
                        <button
                            type="button"
                            class="btn btn-sm btn-outline-primary edit-page-btn"
                            data-bs-toggle="modal"
                            data-bs-target="#pageModal"
                            data-id="<?php echo e($p['id']); ?>"
                            data-title="<?php echo e($p['title']); ?>"
                            data-slug="<?php echo e($p['slug']); ?>"
                            data-content="<?php echo e($p['content']); ?>"
                            data-meta-title="<?php echo e($p['meta_title']); ?>"
                            data-meta-description="<?php echo e($p['meta_description']); ?>"
                            data-social-image="<?php echo e($p['social_image']); ?>"
                            data-status="<?php echo e((int) $p['status']); ?>"
                        >
                            Edit
                        </button>
                        <a class="btn btn-sm btn-outline-danger" href="?delete=<?php echo e($p['id']); ?>" data-confirm="Delete page?">Delete</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
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

<div class="modal fade" id="pageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pageModalTitle">Add Page</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
                    <input type="hidden" name="form_type" value="page">
                    <input type="hidden" name="id" id="pageId" value="0">
                    <input type="hidden" name="current_social_image" id="pageCurrentSocialImage" value="">

                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label">Title</label>
                            <input class="form-control" name="title" id="pageTitle" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Slug</label>
                            <input class="form-control" name="slug" id="pageSlug" placeholder="auto-from-title">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Content</label>
                            <textarea class="form-control" rows="8" name="content" id="pageContent"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Meta Title</label>
                            <input class="form-control" name="meta_title" id="pageMetaTitle">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" id="pageStatus">
                                <option value="1">Published</option>
                                <option value="0">Draft</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Meta Description</label>
                            <textarea class="form-control" rows="3" name="meta_description" id="pageMetaDescription"></textarea>
                        </div>
                        <div class="col-md-12 d-none" id="activationsImageFieldWrap">
                            <label class="form-label">Activations Hero Image</label>
                            <input class="form-control" type="file" name="social_image_file" id="pageSocialImageFile" accept=".jpg,.jpeg,.png,.webp">
                            <small class="text-muted d-block mt-1" id="pageSocialImageHint">Upload image used for the Activations page hero fallback.</small>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-warning">Save Page</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function () {
    var addBtn = document.getElementById('addPageBtn');
    var editButtons = document.querySelectorAll('.edit-page-btn');
    var modalTitle = document.getElementById('pageModalTitle');
    var idInput = document.getElementById('pageId');
    var titleInput = document.getElementById('pageTitle');
    var slugInput = document.getElementById('pageSlug');
    var contentInput = document.getElementById('pageContent');
    var metaTitleInput = document.getElementById('pageMetaTitle');
    var metaDescInput = document.getElementById('pageMetaDescription');
    var socialImageInput = document.getElementById('pageCurrentSocialImage');
    var slugFieldWrap = document.getElementById('activationsImageFieldWrap');
    var socialImageHint = document.getElementById('pageSocialImageHint');
    var socialImageFile = document.getElementById('pageSocialImageFile');
    var statusInput = document.getElementById('pageStatus');
    var modalElement = document.getElementById('pageModal');

    function updateActivationsImageVisibility() {
        var slug = (slugInput.value || '').trim().toLowerCase();
        var isActivations = slug === 'activations';
        if (slugFieldWrap) {
            slugFieldWrap.classList.toggle('d-none', !isActivations);
        }

        if (isActivations && socialImageHint) {
            socialImageHint.textContent = socialImageInput.value
                ? ('Current image: ' + socialImageInput.value)
                : 'Upload image used for the Activations page hero fallback.';
        }
    }

    function setForm(data) {
        modalTitle.textContent = data.id > 0 ? 'Edit Page' : 'Add Page';
        idInput.value = String(data.id || 0);
        titleInput.value = data.title || '';
        slugInput.value = data.slug || '';
        contentInput.value = data.content || '';
        metaTitleInput.value = data.metaTitle || '';
        metaDescInput.value = data.metaDescription || '';
        socialImageInput.value = data.socialImage || '';
        if (socialImageFile) {
            socialImageFile.value = '';
        }
        statusInput.value = String(typeof data.status === 'number' ? data.status : 1);
        updateActivationsImageVisibility();
    }

    if (addBtn) {
        addBtn.addEventListener('click', function () {
            setForm({
                id: 0,
                title: '',
                slug: '',
                content: '',
                metaTitle: '',
                metaDescription: '',
                socialImage: '',
                status: 1
            });
        });
    }

    Array.prototype.forEach.call(editButtons, function (button) {
        button.addEventListener('click', function () {
            setForm({
                id: parseInt(button.getAttribute('data-id'), 10) || 0,
                title: button.getAttribute('data-title') || '',
                slug: button.getAttribute('data-slug') || '',
                content: button.getAttribute('data-content') || '',
                metaTitle: button.getAttribute('data-meta-title') || '',
                metaDescription: button.getAttribute('data-meta-description') || '',
                socialImage: button.getAttribute('data-social-image') || '',
                status: parseInt(button.getAttribute('data-status'), 10) || 0
            });
        });
    });

    if (slugInput) {
        slugInput.addEventListener('input', updateActivationsImageVisibility);
    }

    <?php if ($edit): ?>
    var initialEdit = <?php echo json_encode(array(
        'id' => (int) $edit['id'],
        'title' => (string) $edit['title'],
        'slug' => (string) $edit['slug'],
        'content' => (string) $edit['content'],
        'metaTitle' => (string) $edit['meta_title'],
        'metaDescription' => (string) $edit['meta_description'],
        'socialImage' => (string) $edit['social_image'],
        'status' => (int) $edit['status']
    )); ?>;
    setForm(initialEdit);
    if (modalElement && window.bootstrap && window.bootstrap.Modal) {
        window.bootstrap.Modal.getOrCreateInstance(modalElement).show();
    }
    <?php endif; ?>
})();
</script>

<?php include __DIR__ . '/../templates/admin_footer.php'; ?>
