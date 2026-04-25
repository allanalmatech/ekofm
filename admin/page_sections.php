<?php
require_once __DIR__ . '/_init.php';
require_login();
require_permission('pages.manage');

$adminTitle = 'Page Sections';
$activeMenu = 'page-sections';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['_token'])) {
        redirect('admin/page_sections.php');
    }

    $actionType = isset($_POST['action_type']) ? trim($_POST['action_type']) : 'save_section';

    if ($actionType === 'upload_activation_images') {
        $pageId = isset($_POST['page_id']) ? (int) $_POST['page_id'] : 0;
        $page = $pageId > 0 ? db_query('SELECT id, slug FROM pages WHERE id=? LIMIT 1', array($pageId))->fetch() : null;

        if ($page && $page['slug'] === 'activations' && isset($_FILES['gallery_images']) && is_array($_FILES['gallery_images']['name'])) {
            $maxSort = db_query('SELECT COALESCE(MAX(sort_order), 0) AS max_sort FROM page_sections WHERE page_id=?', array($pageId))->fetch();
            $sortBase = (int) $maxSort['max_sort'];
            $count = count($_FILES['gallery_images']['name']);

            for ($i = 0; $i < $count; $i++) {
                if (empty($_FILES['gallery_images']['name'][$i]) || (int) $_FILES['gallery_images']['error'][$i] !== UPLOAD_ERR_OK) {
                    continue;
                }

                $file = array(
                    'name' => $_FILES['gallery_images']['name'][$i],
                    'type' => $_FILES['gallery_images']['type'][$i],
                    'tmp_name' => $_FILES['gallery_images']['tmp_name'][$i],
                    'error' => $_FILES['gallery_images']['error'][$i],
                    'size' => $_FILES['gallery_images']['size'][$i],
                );

                $up = upload_file($file, array('jpg', 'jpeg', 'png', 'webp'), MAX_IMAGE_UPLOAD, 'page-sections/activations');
                if (!$up['ok']) {
                    continue;
                }

                db_query(
                    'INSERT INTO page_sections (page_id, section_key, title, content, cta_text, cta_link, image_path, sort_order, is_visible, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, NOW(), NOW())',
                    array(
                        $pageId,
                        'gallery_' . date('YmdHis') . '_' . bin2hex(random_bytes(3)),
                        'Activation Gallery Image',
                        '',
                        '',
                        '',
                        $up['path'],
                        $sortBase + $i + 1
                    )
                );
            }
        }

        redirect('admin/page_sections.php?page_id=' . $pageId);
    }

    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    $pageId = isset($_POST['page_id']) ? (int) $_POST['page_id'] : 0;
    $sectionKey = isset($_POST['section_key']) ? trim($_POST['section_key']) : '';
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $content = isset($_POST['content']) ? trim($_POST['content']) : '';
    $ctaText = isset($_POST['cta_text']) ? trim($_POST['cta_text']) : '';
    $ctaLink = isset($_POST['cta_link']) ? trim($_POST['cta_link']) : '';
    $sortOrder = isset($_POST['sort_order']) ? (int) $_POST['sort_order'] : 1;
    $isVisible = isset($_POST['is_visible']) ? (int) $_POST['is_visible'] : 1;
    $imagePath = isset($_POST['current_image_path']) ? trim($_POST['current_image_path']) : '';

    if (!empty($_FILES['image_file']['name'])) {
        $up = upload_file($_FILES['image_file'], array('jpg', 'jpeg', 'png', 'webp'), MAX_IMAGE_UPLOAD, 'page-sections');
        if ($up['ok']) {
            $imagePath = $up['path'];
        }
    }

    $sectionId = $id;
    if ($id > 0) {
        db_query(
            'UPDATE page_sections SET page_id=?, section_key=?, title=?, content=?, cta_text=?, cta_link=?, image_path=?, sort_order=?, is_visible=?, updated_at=NOW() WHERE id=?',
            array($pageId, $sectionKey, $title, $content, $ctaText, $ctaLink, $imagePath, $sortOrder, $isVisible, $id)
        );
    } else {
        db_query(
            'INSERT INTO page_sections (page_id, section_key, title, content, cta_text, cta_link, image_path, sort_order, is_visible, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())',
            array($pageId, $sectionKey, $title, $content, $ctaText, $ctaLink, $imagePath, $sortOrder, $isVisible)
        );
        $sectionId = (int) db()->lastInsertId();
    }

    if ($sectionId > 0 && isset($_FILES['gallery_images']) && is_array($_FILES['gallery_images']['name'])) {
        $count = count($_FILES['gallery_images']['name']);
        $user = current_user();
        $createdBy = $user ? (int) $user['id'] : null;

        for ($i = 0; $i < $count; $i++) {
            if (empty($_FILES['gallery_images']['name'][$i]) || (int) $_FILES['gallery_images']['error'][$i] !== UPLOAD_ERR_OK) {
                continue;
            }

            $file = array(
                'name' => $_FILES['gallery_images']['name'][$i],
                'type' => $_FILES['gallery_images']['type'][$i],
                'tmp_name' => $_FILES['gallery_images']['tmp_name'][$i],
                'error' => $_FILES['gallery_images']['error'][$i],
                'size' => $_FILES['gallery_images']['size'][$i],
            );

            $up = upload_file($file, array('jpg', 'jpeg', 'png', 'webp'), MAX_IMAGE_UPLOAD, 'page-sections/cards');
            if (!$up['ok']) {
                continue;
            }

            db_query(
                'INSERT INTO media (title, category_name, file_path, file_type, file_size, created_by, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())',
                array(
                    'Section Image',
                    'page-section-' . $sectionId,
                    $up['path'],
                    'image',
                    (int) $file['size'],
                    $createdBy
                )
            );
        }
    }

    redirect('admin/page_sections.php?page_id=' . $pageId);
}

if (isset($_GET['delete'])) {
    $deleteId = (int) $_GET['delete'];
    $pageId = isset($_GET['page_id']) ? (int) $_GET['page_id'] : 0;
    db_query('DELETE FROM page_sections WHERE id=?', array($deleteId));
    redirect('admin/page_sections.php?page_id=' . $pageId);
}

if (isset($_GET['delete_section_image'])) {
    $imageId = (int) $_GET['delete_section_image'];
    $pageId = isset($_GET['page_id']) ? (int) $_GET['page_id'] : 0;
    db_query('DELETE FROM media WHERE id=? AND category_name LIKE "page-section-%"', array($imageId));
    redirect('admin/page_sections.php?page_id=' . $pageId);
}

if (isset($_GET['delete_gallery'])) {
    $deleteId = (int) $_GET['delete_gallery'];
    $pageId = isset($_GET['page_id']) ? (int) $_GET['page_id'] : 0;
    db_query('DELETE FROM page_sections WHERE id=? AND section_key LIKE "gallery_%"', array($deleteId));
    redirect('admin/page_sections.php?page_id=' . $pageId);
}

$pages = db_query('SELECT id, title, slug FROM pages ORDER BY title ASC')->fetchAll();

$selectedPageId = isset($_GET['page_id']) ? (int) $_GET['page_id'] : 0;
if ($selectedPageId <= 0 && count($pages) > 0) {
    $selectedPageId = (int) $pages[0]['id'];
}

$edit = isset($_GET['edit']) ? db_query('SELECT * FROM page_sections WHERE id=?', array((int) $_GET['edit']))->fetch() : null;
if ($edit) {
    $selectedPageId = (int) $edit['page_id'];
}

$sections = $selectedPageId > 0
    ? db_query('SELECT ps.*, p.title AS page_title, p.slug AS page_slug FROM page_sections ps INNER JOIN pages p ON p.id = ps.page_id WHERE ps.page_id=? ORDER BY ps.sort_order ASC, ps.id ASC', array($selectedPageId))->fetchAll()
    : array();

$sectionGalleryMap = array();
if ($sections) {
    $validSectionIds = array();
    foreach ($sections as $sectionRow) {
        $sid = (int) $sectionRow['id'];
        $validSectionIds[$sid] = true;
        $sectionGalleryMap[$sid] = array();
    }

    $galleryRows = db_query('SELECT id, file_path, category_name FROM media WHERE file_type = ? AND category_name LIKE ? ORDER BY id DESC', array('image', 'page-section-%'))->fetchAll();
    foreach ($galleryRows as $gRow) {
        if (!preg_match('/^page-section-(\d+)$/', (string) $gRow['category_name'], $m)) {
            continue;
        }
        $sectionId = (int) $m[1];
        if (!isset($validSectionIds[$sectionId])) {
            continue;
        }
        $sectionGalleryMap[$sectionId][] = array(
            'id' => (int) $gRow['id'],
            'file_path' => (string) $gRow['file_path'],
            'url' => media_url($gRow['file_path']),
        );
    }
}

$selectedPageSlug = '';
foreach ($pages as $p) {
    if ((int) $p['id'] === (int) $selectedPageId) {
        $selectedPageSlug = (string) $p['slug'];
        break;
    }
}

$activationGallery = array();
if ($selectedPageSlug === 'activations' && $selectedPageId > 0) {
    $activationGallery = db_query('SELECT * FROM page_sections WHERE page_id=? AND section_key LIKE "gallery_%" ORDER BY sort_order ASC, id ASC', array($selectedPageId))->fetchAll();
}

include __DIR__ . '/../templates/admin_header.php';
?>

<div class="panel">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h5 class="mb-0">Page Sections</h5>
        <div class="d-flex gap-2 align-items-center">
            <form method="get" class="d-flex gap-2 align-items-center">
                <select class="form-select form-select-sm" name="page_id" onchange="this.form.submit()">
                    <?php foreach ($pages as $p): ?>
                        <option value="<?php echo e($p['id']); ?>" <?php echo (int) $selectedPageId === (int) $p['id'] ? 'selected' : ''; ?>>
                            <?php echo e($p['title']); ?> (<?php echo e($p['slug']); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
            <button type="button" class="btn btn-warning btn-sm" id="addSectionBtn" data-bs-toggle="modal" data-bs-target="#sectionModal">
                <span class="material-symbols-outlined align-middle" style="font-size:18px;">add</span>
                Add Section
            </button>
        </div>
    </div>

    <?php if (!$sections): ?>
        <div class="text-center text-muted py-4">No sections found for this page yet.</div>
    <?php else: ?>
        <div class="list-group list-group-flush">
            <?php foreach ($sections as $s): ?>
                <div class="list-group-item px-0 d-flex justify-content-between align-items-start gap-3">
                    <div>
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                            <strong><?php echo e($s['title'] !== '' ? $s['title'] : $s['section_key']); ?></strong>
                            <span class="badge text-bg-light">Key: <?php echo e($s['section_key']); ?></span>
                            <span class="badge text-bg-light">Order <?php echo e($s['sort_order']); ?></span>
                            <span class="badge <?php echo (int) $s['is_visible'] === 1 ? 'text-bg-success' : 'text-bg-secondary'; ?>">
                                <?php echo (int) $s['is_visible'] === 1 ? 'Visible' : 'Hidden'; ?>
                            </span>
                        </div>
                        <?php if (!empty($s['content'])): ?>
                            <p class="mb-1 text-muted"><?php echo e(mb_strlen($s['content']) > 180 ? mb_substr($s['content'], 0, 180) . '...' : $s['content']); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($s['image_path'])): ?>
                            <small class="text-muted d-block">Image: <?php echo e($s['image_path']); ?></small>
                        <?php endif; ?>
                        <?php $galleryCount = isset($sectionGalleryMap[(int) $s['id']]) ? count($sectionGalleryMap[(int) $s['id']]) : 0; ?>
                        <?php if ($galleryCount > 0): ?>
                            <small class="text-muted d-block">Gallery images: <?php echo e($galleryCount); ?></small>
                        <?php endif; ?>
                        <?php if (!empty($s['cta_text']) && !empty($s['cta_link'])): ?>
                            <small class="text-muted d-block">CTA: <?php echo e($s['cta_text']); ?> -> <?php echo e($s['cta_link']); ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="d-flex gap-2">
                        <button
                            type="button"
                            class="btn btn-sm btn-outline-primary edit-section-btn"
                            data-bs-toggle="modal"
                            data-bs-target="#sectionModal"
                            data-id="<?php echo e($s['id']); ?>"
                            data-page-id="<?php echo e($s['page_id']); ?>"
                            data-section-key="<?php echo e($s['section_key']); ?>"
                            data-title="<?php echo e($s['title']); ?>"
                            data-content="<?php echo e($s['content']); ?>"
                            data-cta-text="<?php echo e($s['cta_text']); ?>"
                            data-cta-link="<?php echo e($s['cta_link']); ?>"
                            data-image-path="<?php echo e($s['image_path']); ?>"
                            data-sort-order="<?php echo e($s['sort_order']); ?>"
                            data-visible="<?php echo e((int) $s['is_visible']); ?>"
                        >
                            Edit
                        </button>
                        <a href="?delete=<?php echo e($s['id']); ?>&page_id=<?php echo e($selectedPageId); ?>" data-confirm="Delete this section?" class="btn btn-sm btn-outline-danger">Delete</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php if ($selectedPageSlug === 'activations'): ?>
<div class="panel mt-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Activations Gallery Images</h5>
        <span class="text-muted small">Upload one or more images. Frontend will auto-slide when more than one image exists.</span>
    </div>

    <form method="post" enctype="multipart/form-data" class="mb-3">
        <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
        <input type="hidden" name="action_type" value="upload_activation_images">
        <input type="hidden" name="page_id" value="<?php echo e($selectedPageId); ?>">
        <div class="row g-2 align-items-end">
            <div class="col-md-9">
                <label class="form-label">Select Images</label>
                <input class="form-control" type="file" name="gallery_images[]" accept=".jpg,.jpeg,.png,.webp" multiple>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-warning w-100">Upload Gallery Images</button>
            </div>
        </div>
    </form>

    <?php if (!$activationGallery): ?>
        <div class="text-muted">No gallery images uploaded yet.</div>
    <?php else: ?>
        <div class="row g-3">
            <?php foreach ($activationGallery as $img): ?>
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <div class="border rounded p-2 h-100 d-flex flex-column">
                        <div style="aspect-ratio:4/3;border-radius:8px;background-size:cover;background-position:center;background-image:url('<?php echo e(media_url($img['image_path'])); ?>');"></div>
                        <small class="text-muted mt-2 text-truncate"><?php echo e($img['image_path']); ?></small>
                        <a href="?delete_gallery=<?php echo e($img['id']); ?>&page_id=<?php echo e($selectedPageId); ?>" data-confirm="Delete this gallery image?" class="btn btn-sm btn-outline-danger mt-2">Delete</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<div class="modal fade" id="sectionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sectionModalTitle">Add Section</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
                    <input type="hidden" name="id" id="sectionId" value="0">
                    <input type="hidden" name="current_image_path" id="sectionCurrentImage" value="">

                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label">Page</label>
                            <select class="form-select" name="page_id" id="sectionPageId" required>
                                <?php foreach ($pages as $p): ?>
                                    <option value="<?php echo e($p['id']); ?>"><?php echo e($p['title']); ?> (<?php echo e($p['slug']); ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Section Key</label>
                            <input class="form-control" name="section_key" id="sectionKey" placeholder="hero_intro" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Title</label>
                            <input class="form-control" name="title" id="sectionTitle">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Content</label>
                            <textarea class="form-control" rows="6" name="content" id="sectionContent"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">CTA Text</label>
                            <input class="form-control" name="cta_text" id="sectionCtaText" placeholder="Learn More">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">CTA Link</label>
                            <input class="form-control" name="cta_link" id="sectionCtaLink" placeholder="/contact or https://...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Sort Order</label>
                            <input class="form-control" type="number" name="sort_order" id="sectionSortOrder" value="1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Visibility</label>
                            <select class="form-select" name="is_visible" id="sectionVisible">
                                <option value="1">Visible</option>
                                <option value="0">Hidden</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Image (optional)</label>
                            <input class="form-control" type="file" name="image_file" accept=".jpg,.jpeg,.png,.webp">
                            <small class="text-muted d-block mt-1" id="sectionImageHint">Upload image (jpg, jpeg, png, webp).</small>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Additional Card Images (optional)</label>
                            <input class="form-control" type="file" name="gallery_images[]" accept=".jpg,.jpeg,.png,.webp" multiple>
                            <small class="text-muted d-block mt-1">Upload multiple images to create a slider on the card.</small>
                            <div id="sectionGalleryList" class="mt-2 small text-muted"></div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-warning">Save Section</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function () {
    var addBtn = document.getElementById('addSectionBtn');
    var editButtons = document.querySelectorAll('.edit-section-btn');
    var modalTitle = document.getElementById('sectionModalTitle');
    var idInput = document.getElementById('sectionId');
    var pageInput = document.getElementById('sectionPageId');
    var keyInput = document.getElementById('sectionKey');
    var titleInput = document.getElementById('sectionTitle');
    var contentInput = document.getElementById('sectionContent');
    var ctaTextInput = document.getElementById('sectionCtaText');
    var ctaLinkInput = document.getElementById('sectionCtaLink');
    var imageInput = document.getElementById('sectionCurrentImage');
    var sortInput = document.getElementById('sectionSortOrder');
    var visibleInput = document.getElementById('sectionVisible');
    var imageHint = document.getElementById('sectionImageHint');
    var galleryList = document.getElementById('sectionGalleryList');
    var selectedPageId = <?php echo (int) $selectedPageId; ?>;
    var modalElement = document.getElementById('sectionModal');
    var sectionGalleryMap = <?php echo json_encode($sectionGalleryMap); ?>;

    function renderGallery(sectionId) {
        if (!galleryList) {
            return;
        }

        var list = sectionGalleryMap[String(sectionId)] || [];
        if (!list.length) {
            galleryList.innerHTML = 'No additional images uploaded yet.';
            return;
        }

        var html = '<div class="d-flex flex-wrap gap-2">';
        for (var i = 0; i < list.length; i++) {
            var item = list[i];
            var deleteUrl = '?delete_section_image=' + encodeURIComponent(item.id) + '&page_id=' + encodeURIComponent(selectedPageId);
            html += '<span class="badge text-bg-light">Image ' + (i + 1) + ' <a class="ms-1 text-danger" href="' + deleteUrl + '" data-confirm="Delete this image?">x</a></span>';
        }
        html += '</div>';
        galleryList.innerHTML = html;
    }

    function setForm(data) {
        modalTitle.textContent = data.id > 0 ? 'Edit Section' : 'Add Section';
        idInput.value = String(data.id || 0);
        pageInput.value = String(data.pageId || selectedPageId || 0);
        keyInput.value = data.sectionKey || '';
        titleInput.value = data.title || '';
        contentInput.value = data.content || '';
        ctaTextInput.value = data.ctaText || '';
        ctaLinkInput.value = data.ctaLink || '';
        imageInput.value = data.imagePath || '';
        sortInput.value = String(data.sortOrder || 1);
        visibleInput.value = String(typeof data.visible === 'number' ? data.visible : 1);
        imageHint.textContent = data.imagePath ? ('Current image: ' + data.imagePath) : 'Upload image (jpg, jpeg, png, webp).';
        renderGallery(data.id || 0);
    }

    if (addBtn) {
        addBtn.addEventListener('click', function () {
            setForm({
                id: 0,
                pageId: selectedPageId,
                sectionKey: '',
                title: '',
                content: '',
                ctaText: '',
                ctaLink: '',
                imagePath: '',
                sortOrder: 1,
                visible: 1
            });
        });
    }

    Array.prototype.forEach.call(editButtons, function (button) {
        button.addEventListener('click', function () {
            setForm({
                id: parseInt(button.getAttribute('data-id'), 10) || 0,
                pageId: parseInt(button.getAttribute('data-page-id'), 10) || selectedPageId,
                sectionKey: button.getAttribute('data-section-key') || '',
                title: button.getAttribute('data-title') || '',
                content: button.getAttribute('data-content') || '',
                ctaText: button.getAttribute('data-cta-text') || '',
                ctaLink: button.getAttribute('data-cta-link') || '',
                imagePath: button.getAttribute('data-image-path') || '',
                sortOrder: parseInt(button.getAttribute('data-sort-order'), 10) || 1,
                visible: parseInt(button.getAttribute('data-visible'), 10) || 0
            });
        });
    });

    <?php if ($edit): ?>
    var initialEdit = <?php echo json_encode(array(
        'id' => (int) $edit['id'],
        'pageId' => (int) $edit['page_id'],
        'sectionKey' => (string) $edit['section_key'],
        'title' => (string) $edit['title'],
        'content' => (string) $edit['content'],
        'ctaText' => (string) $edit['cta_text'],
        'ctaLink' => (string) $edit['cta_link'],
        'imagePath' => (string) $edit['image_path'],
        'sortOrder' => (int) $edit['sort_order'],
        'visible' => (int) $edit['is_visible']
    )); ?>;
    setForm(initialEdit);
    if (modalElement && window.bootstrap && window.bootstrap.Modal) {
        window.bootstrap.Modal.getOrCreateInstance(modalElement).show();
    }
    <?php endif; ?>
})();
</script>

<?php include __DIR__ . '/../templates/admin_footer.php'; ?>
