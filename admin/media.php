<?php
require_once __DIR__ . '/_init.php';
require_login();
require_permission('media.manage');

$adminTitle = 'Media Library';
$activeMenu = 'media';

$hasDescription = db_query("SHOW COLUMNS FROM media LIKE 'description'")->fetch();
if (!$hasDescription) {
    db_query("ALTER TABLE media ADD COLUMN description TEXT NULL AFTER category_name");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['_token'])) {
        redirect('admin/media.php');
    }

    $actionType = isset($_POST['action_type']) ? trim($_POST['action_type']) : '';

    if ($actionType === 'upload') {
        $mediaType = isset($_POST['media_type']) && $_POST['media_type'] === 'audio' ? 'audio' : 'image';
        $category = trim(isset($_POST['category_name']) ? $_POST['category_name'] : '');
        $description = trim(isset($_POST['description']) ? $_POST['description'] : '');
        $titleInput = trim(isset($_POST['title']) ? $_POST['title'] : '');
        $user = current_user();
        $createdBy = $user ? (int) $user['id'] : null;

        if (isset($_FILES['files']) && is_array($_FILES['files']['name'])) {
            $count = count($_FILES['files']['name']);
            for ($i = 0; $i < $count; $i++) {
                if (empty($_FILES['files']['name'][$i]) || (int) $_FILES['files']['error'][$i] !== UPLOAD_ERR_OK) {
                    continue;
                }

                $file = array(
                    'name' => $_FILES['files']['name'][$i],
                    'type' => $_FILES['files']['type'][$i],
                    'tmp_name' => $_FILES['files']['tmp_name'][$i],
                    'error' => $_FILES['files']['error'][$i],
                    'size' => $_FILES['files']['size'][$i],
                );

                $up = upload_file(
                    $file,
                    $mediaType === 'audio' ? array('mp3', 'wav', 'ogg', 'm4a') : array('jpg', 'jpeg', 'png', 'webp', 'gif'),
                    $mediaType === 'audio' ? MAX_AUDIO_UPLOAD : MAX_IMAGE_UPLOAD,
                    'media'
                );

                if (!$up['ok']) {
                    continue;
                }

                $rowTitle = $titleInput !== '' ? $titleInput : $file['name'];
                db_query(
                    'INSERT INTO media (title, category_name, description, file_path, file_type, file_size, created_by, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())',
                    array($rowTitle, $category, $description, $up['path'], $mediaType, (int) $file['size'], $createdBy)
                );
            }
        }

        redirect('admin/media.php');
    }

    if ($actionType === 'manage_selected') {
        $idsRaw = isset($_POST['selected_ids']) ? trim($_POST['selected_ids']) : '';
        $ids = array();
        if ($idsRaw !== '') {
            foreach (explode(',', $idsRaw) as $part) {
                $id = (int) trim($part);
                if ($id > 0) {
                    $ids[$id] = $id;
                }
            }
        }
        $ids = array_values($ids);

        if ($ids) {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $manageAction = isset($_POST['manage_action']) ? trim($_POST['manage_action']) : '';

            if ($manageAction === 'delete') {
                db_query('DELETE FROM media WHERE id IN (' . $placeholders . ')', $ids);
            }

            if ($manageAction === 'update') {
                $setCategory = trim(isset($_POST['set_category_name']) ? $_POST['set_category_name'] : '');
                $setDescription = trim(isset($_POST['set_description']) ? $_POST['set_description'] : '');
                $setTitle = trim(isset($_POST['set_title']) ? $_POST['set_title'] : '');

                if ($setCategory !== '') {
                    db_query('UPDATE media SET category_name=? WHERE id IN (' . $placeholders . ')', array_merge(array($setCategory), $ids));
                }

                if ($setDescription !== '') {
                    db_query('UPDATE media SET description=? WHERE id IN (' . $placeholders . ')', array_merge(array($setDescription), $ids));
                }

                if (count($ids) === 1 && $setTitle !== '') {
                    db_query('UPDATE media SET title=? WHERE id=?', array($setTitle, $ids[0]));
                }
            }
        }

        redirect('admin/media.php');
    }
}

$rows = db_query('SELECT * FROM media ORDER BY id DESC')->fetchAll();

include __DIR__ . '/../templates/admin_header.php';
?>

<div class="panel mb-3">
    <h5 class="mb-3">Upload Media</h5>
    <form method="post" enctype="multipart/form-data" id="mediaUploadForm">
        <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
        <input type="hidden" name="action_type" value="upload">

        <div class="row g-2 mb-2">
            <div class="col-md-3">
                <label class="form-label">Media Type</label>
                <select class="form-select" name="media_type" id="mediaTypeSelect">
                    <option value="image">Image</option>
                    <option value="audio">Audio</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Category</label>
                <input class="form-control" name="category_name" placeholder="event-photos">
            </div>
            <div class="col-md-3">
                <label class="form-label">Title (optional)</label>
                <input class="form-control" name="title" placeholder="Media title">
            </div>
            <div class="col-md-3">
                <label class="form-label">Description (optional)</label>
                <input class="form-control" name="description" placeholder="Describe this upload">
            </div>
        </div>

        <div class="media-dropzone" id="mediaDropzone" role="button" tabindex="0" aria-label="Upload media files">
            <span class="material-symbols-outlined">cloud_upload</span>
            <p class="mb-1">Drag and drop files here or click to browse</p>
            <small class="text-muted" id="mediaDropzoneHelp">Images: jpg, jpeg, png, webp, gif</small>
            <input class="d-none" type="file" name="files[]" id="mediaFilesInput" multiple required>
        </div>

        <div id="mediaUploadList" class="small text-muted mt-2"></div>

        <div class="mt-3 d-flex justify-content-end">
            <button class="btn btn-warning">Upload Selected</button>
        </div>
    </form>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="panel h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Media Thumbnails</h5>
                <small class="text-muted"><?php echo e(count($rows)); ?> item(s)</small>
            </div>

            <?php if (!$rows): ?>
                <div class="text-center text-muted py-4">No media uploaded yet.</div>
            <?php else: ?>
                <div class="media-grid" id="mediaGrid">
                    <?php foreach ($rows as $r): ?>
                        <?php $isImage = strtolower((string) $r['file_type']) === 'image'; ?>
                        <article
                            class="media-item"
                            data-id="<?php echo e($r['id']); ?>"
                            data-title="<?php echo e($r['title']); ?>"
                            data-category="<?php echo e($r['category_name']); ?>"
                            data-description="<?php echo e(isset($r['description']) ? $r['description'] : ''); ?>"
                            data-path="<?php echo e($r['file_path']); ?>"
                            data-type="<?php echo e($r['file_type']); ?>"
                            data-size="<?php echo e((int) $r['file_size']); ?>"
                            data-created="<?php echo e($r['created_at']); ?>"
                            data-url="<?php echo e(media_url($r['file_path'])); ?>"
                        >
                            <label class="media-select">
                                <input type="checkbox" class="media-select-input" value="<?php echo e($r['id']); ?>">
                            </label>
                            <div class="media-thumb" style="background-image:url('<?php echo e($isImage ? media_url($r['file_path']) : media_url('')); ?>');">
                                <?php if (!$isImage): ?>
                                    <span class="material-symbols-outlined">audiotrack</span>
                                <?php endif; ?>
                            </div>
                            <div class="media-meta">
                                <strong><?php echo e($r['title']); ?></strong>
                                <small><?php echo e($r['category_name'] !== '' ? $r['category_name'] : 'uncategorized'); ?> - <?php echo e($r['file_type']); ?></small>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="panel h-100">
            <h5 class="mb-3">Selected Media Manager</h5>

            <div id="mediaSelectionSummary" class="small text-muted mb-3">No item selected.</div>

            <div id="mediaSelectionPreview" class="media-sidebar-preview mb-3 d-none">
                <div id="mediaSelectionPreviewImage" class="media-sidebar-preview-image"></div>
                <div class="small mt-2" id="mediaSelectionPreviewText"></div>
            </div>

            <form method="post" id="mediaManageForm">
                <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
                <input type="hidden" name="action_type" value="manage_selected">
                <input type="hidden" name="selected_ids" id="mediaSelectedIds" value="">

                <div class="mb-2">
                    <label class="form-label">Title (single selection)</label>
                    <input class="form-control" name="set_title" id="mediaSetTitle" placeholder="Edit title for one selected item">
                </div>
                <div class="mb-2">
                    <label class="form-label">Category</label>
                    <input class="form-control" name="set_category_name" id="mediaSetCategory" placeholder="Set category for selected">
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" rows="4" name="set_description" id="mediaSetDescription" placeholder="Set description for selected"></textarea>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" name="manage_action" value="update" class="btn btn-outline-primary" id="mediaUpdateBtn" disabled>Update Selected</button>
                    <button type="submit" name="manage_action" value="delete" class="btn btn-outline-danger" id="mediaDeleteBtn" data-confirm="Delete selected media?" disabled>Delete Selected</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function () {
    var dropzone = document.getElementById('mediaDropzone');
    var filesInput = document.getElementById('mediaFilesInput');
    var uploadList = document.getElementById('mediaUploadList');
    var mediaTypeSelect = document.getElementById('mediaTypeSelect');
    var dropzoneHelp = document.getElementById('mediaDropzoneHelp');

    var grid = document.getElementById('mediaGrid');
    var selectedIdsInput = document.getElementById('mediaSelectedIds');
    var summary = document.getElementById('mediaSelectionSummary');
    var previewWrap = document.getElementById('mediaSelectionPreview');
    var previewImage = document.getElementById('mediaSelectionPreviewImage');
    var previewText = document.getElementById('mediaSelectionPreviewText');
    var setTitle = document.getElementById('mediaSetTitle');
    var setCategory = document.getElementById('mediaSetCategory');
    var setDescription = document.getElementById('mediaSetDescription');
    var updateBtn = document.getElementById('mediaUpdateBtn');
    var deleteBtn = document.getElementById('mediaDeleteBtn');

    function updateDropzoneHelp() {
        if (!dropzoneHelp || !mediaTypeSelect || !filesInput) {
            return;
        }
        if (mediaTypeSelect.value === 'audio') {
            dropzoneHelp.textContent = 'Audio: mp3, wav, ogg, m4a';
            filesInput.setAttribute('accept', '.mp3,.wav,.ogg,.m4a');
        } else {
            dropzoneHelp.textContent = 'Images: jpg, jpeg, png, webp, gif';
            filesInput.setAttribute('accept', '.jpg,.jpeg,.png,.webp,.gif');
        }
    }

    function listFiles() {
        if (!uploadList || !filesInput || !filesInput.files) {
            return;
        }

        if (!filesInput.files.length) {
            uploadList.textContent = '';
            return;
        }

        var text = 'Selected: ';
        for (var i = 0; i < filesInput.files.length; i++) {
            text += filesInput.files[i].name;
            if (i < filesInput.files.length - 1) {
                text += ', ';
            }
        }
        uploadList.textContent = text;
    }

    if (dropzone && filesInput) {
        dropzone.addEventListener('click', function () {
            filesInput.click();
        });

        dropzone.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                filesInput.click();
            }
        });

        dropzone.addEventListener('dragover', function (e) {
            e.preventDefault();
            dropzone.classList.add('is-dragover');
        });

        dropzone.addEventListener('dragleave', function () {
            dropzone.classList.remove('is-dragover');
        });

        dropzone.addEventListener('drop', function (e) {
            e.preventDefault();
            dropzone.classList.remove('is-dragover');
            if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length) {
                filesInput.files = e.dataTransfer.files;
                listFiles();
            }
        });

        filesInput.addEventListener('change', listFiles);
    }

    if (mediaTypeSelect) {
        mediaTypeSelect.addEventListener('change', updateDropzoneHelp);
        updateDropzoneHelp();
    }

    function getSelectedItems() {
        if (!grid) {
            return array();
        }
        var selected = array();
        var items = grid.querySelectorAll('.media-item');
        for (var i = 0; i < items.length; i++) {
            var checkbox = items[i].querySelector('.media-select-input');
            if (checkbox && checkbox.checked) {
                selected.push(items[i]);
            }
        }
        return selected;
    }

    function refreshSelectionState() {
        var selected = getSelectedItems();
        var ids = array();

        for (var i = 0; i < selected.length; i++) {
            ids.push(selected[i].getAttribute('data-id'));
            selected[i].classList.add('is-selected');
        }

        if (grid) {
            var allItems = grid.querySelectorAll('.media-item');
            for (var j = 0; j < allItems.length; j++) {
                var id = allItems[j].getAttribute('data-id');
                if (ids.indexOf(id) === -1) {
                    allItems[j].classList.remove('is-selected');
                }
            }
        }

        selectedIdsInput.value = ids.join(',');
        updateBtn.disabled = ids.length === 0;
        deleteBtn.disabled = ids.length === 0;

        if (ids.length === 0) {
            summary.textContent = 'No item selected.';
            previewWrap.classList.add('d-none');
            setTitle.value = '';
            setCategory.value = '';
            setDescription.value = '';
            setTitle.disabled = false;
            return;
        }

        if (ids.length === 1) {
            var item = selected[0];
            summary.textContent = '1 item selected. You can edit all fields.';
            previewWrap.classList.remove('d-none');
            previewImage.style.backgroundImage = 'url(' + (item.getAttribute('data-url') || '') + ')';
            previewText.textContent = (item.getAttribute('data-title') || '') + ' - ' + (item.getAttribute('data-type') || '');
            setTitle.value = item.getAttribute('data-title') || '';
            setCategory.value = item.getAttribute('data-category') || '';
            setDescription.value = item.getAttribute('data-description') || '';
            setTitle.disabled = false;
            return;
        }

        summary.textContent = ids.length + ' items selected. Update category/description in bulk or delete.';
        previewWrap.classList.add('d-none');
        setTitle.value = '';
        setTitle.disabled = true;
    }

    if (grid) {
        grid.addEventListener('change', function (e) {
            if (e.target && e.target.classList.contains('media-select-input')) {
                refreshSelectionState();
            }
        });

        grid.addEventListener('click', function (e) {
            var item = e.target.closest('.media-item');
            if (!item) {
                return;
            }
            if (e.target.closest('.media-select')) {
                return;
            }
            var cb = item.querySelector('.media-select-input');
            if (!cb) {
                return;
            }
            cb.checked = !cb.checked;
            refreshSelectionState();
        });
    }
})();
</script>

<?php include __DIR__ . '/../templates/admin_footer.php'; ?>
