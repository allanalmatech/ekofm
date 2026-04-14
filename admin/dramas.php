<?php
require_once __DIR__ . '/_init.php';
require_login();
require_permission('dramas.manage');

$adminTitle = 'Drama Management';
$activeMenu = 'dramas';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['_token'])) {
        redirect('admin/dramas.php');
    }

    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $slugInput = isset($_POST['slug']) ? trim($_POST['slug']) : '';
    $slug = $slugInput !== '' ? slugify($slugInput) : slugify($title);
    $desc = isset($_POST['short_description']) ? trim($_POST['short_description']) : '';
    $category = isset($_POST['category_name']) ? trim($_POST['category_name']) : '';
    $status = isset($_POST['status']) ? (int) $_POST['status'] : 1;
    $cover = isset($_POST['current_cover']) ? $_POST['current_cover'] : '';
    $audio = isset($_POST['current_audio']) ? $_POST['current_audio'] : '';
    $audioUrl = isset($_POST['audio_url']) ? trim($_POST['audio_url']) : '';

    if (!empty($_FILES['cover_image']['name'])) {
        $up = upload_file($_FILES['cover_image'], array('jpg', 'jpeg', 'png', 'webp'), MAX_IMAGE_UPLOAD, 'dramas/covers');
        if ($up['ok']) {
            $cover = $up['path'];
        }
    }

    if (!empty($_FILES['audio_file']['name'])) {
        $up = upload_file($_FILES['audio_file'], array('mp3', 'wav', 'ogg', 'm4a'), MAX_AUDIO_UPLOAD, 'dramas/audio');
        if ($up['ok']) {
            $audio = $up['path'];
        }
    }

    if ($id > 0) {
        db_query(
            'UPDATE dramas SET title=?, slug=?, short_description=?, category_name=?, cover_image=?, audio_file=?, audio_url=?, status=?, updated_at=NOW() WHERE id=?',
            array($title, $slug, $desc, $category, $cover, $audio, $audioUrl, $status, $id)
        );
    } else {
        db_query(
            'INSERT INTO dramas (title, slug, short_description, category_name, cover_image, audio_file, audio_url, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())',
            array($title, $slug, $desc, $category, $cover, $audio, $audioUrl, $status)
        );
    }

    redirect('admin/dramas.php');
}

if (isset($_GET['delete'])) {
    db_query('DELETE FROM dramas WHERE id=?', array((int) $_GET['delete']));
    redirect('admin/dramas.php');
}

$rows = db_query('SELECT * FROM dramas ORDER BY id DESC')->fetchAll();

include __DIR__ . '/../templates/admin_header.php';
?>

<div class="panel">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Dramas</h5>
        <button type="button" class="btn btn-warning btn-sm" id="addDramaBtn" data-bs-toggle="modal" data-bs-target="#dramaModal">
            <span class="material-symbols-outlined align-middle" style="font-size:18px;">add</span>
            Add Drama
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-sm align-middle">
            <thead>
                <tr>
                    <th>Cover</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Audio</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!$rows): ?>
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">No dramas added yet.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($rows as $r): ?>
                    <tr>
                        <td>
                            <img src="<?php echo e(media_url($r['cover_image'])); ?>" alt="cover" style="width:72px;height:42px;object-fit:cover;border-radius:8px;">
                        </td>
                        <td>
                            <strong><?php echo e($r['title']); ?></strong>
                            <?php if (!empty($r['slug'])): ?>
                                <br><small class="text-muted"><?php echo e($r['slug']); ?></small>
                            <?php endif; ?>
                        </td>
                        <td><?php echo e($r['category_name']); ?></td>
                        <td><?php echo (int) $r['status'] === 1 ? 'Published' : 'Draft'; ?></td>
                        <td>
                            <?php if (!empty($r['audio_file'])): ?>
                                <span class="badge text-bg-success">File</span>
                            <?php endif; ?>
                            <?php if (!empty($r['audio_url'])): ?>
                                <span class="badge text-bg-primary">URL</span>
                            <?php endif; ?>
                            <?php if (empty($r['audio_file']) && empty($r['audio_url'])): ?>
                                <span class="text-muted">None</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <button
                                type="button"
                                class="btn btn-sm btn-outline-primary edit-drama-btn"
                                data-bs-toggle="modal"
                                data-bs-target="#dramaModal"
                                data-id="<?php echo e($r['id']); ?>"
                                data-title="<?php echo e($r['title']); ?>"
                                data-slug="<?php echo e($r['slug']); ?>"
                                data-category="<?php echo e($r['category_name']); ?>"
                                data-description="<?php echo e($r['short_description']); ?>"
                                data-status="<?php echo e((int) $r['status']); ?>"
                                data-cover="<?php echo e($r['cover_image']); ?>"
                                data-audio="<?php echo e($r['audio_file']); ?>"
                                data-audio-url="<?php echo e($r['audio_url']); ?>"
                            >
                                Edit
                            </button>
                            <a href="?delete=<?php echo e($r['id']); ?>" data-confirm="Delete this drama?" class="btn btn-sm btn-outline-danger">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="dramaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dramaModalTitle">Add Drama</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
                    <input type="hidden" name="id" id="dramaId" value="0">
                    <input type="hidden" name="current_cover" id="dramaCurrentCover" value="">
                    <input type="hidden" name="current_audio" id="dramaCurrentAudio" value="">

                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label">Title</label>
                            <input class="form-control" name="title" id="dramaTitle" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Slug (optional)</label>
                            <input class="form-control" name="slug" id="dramaSlug" placeholder="auto-from-title">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Category</label>
                            <input class="form-control" name="category_name" id="dramaCategory" placeholder="General">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" id="dramaStatus">
                                <option value="1">Published</option>
                                <option value="0">Draft</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" rows="4" name="short_description" id="dramaDescription"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Cover Image</label>
                            <input class="form-control" type="file" name="cover_image" accept=".jpg,.jpeg,.png,.webp">
                            <small class="text-muted d-block mt-1" id="dramaCoverHint">Upload image (jpg, jpeg, png, webp).</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Audio File</label>
                            <input class="form-control" type="file" name="audio_file" accept=".mp3,.wav,.ogg,.m4a">
                            <small class="text-muted d-block mt-1" id="dramaAudioHint">Upload audio file (mp3, wav, ogg, m4a).</small>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">External Audio URL (optional)</label>
                            <input class="form-control" name="audio_url" id="dramaAudioUrl" placeholder="https://...">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-warning">Save Drama</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function () {
    var addBtn = document.getElementById('addDramaBtn');
    var editButtons = document.querySelectorAll('.edit-drama-btn');

    var modalTitle = document.getElementById('dramaModalTitle');
    var idInput = document.getElementById('dramaId');
    var currentCoverInput = document.getElementById('dramaCurrentCover');
    var currentAudioInput = document.getElementById('dramaCurrentAudio');
    var titleInput = document.getElementById('dramaTitle');
    var slugInput = document.getElementById('dramaSlug');
    var categoryInput = document.getElementById('dramaCategory');
    var statusInput = document.getElementById('dramaStatus');
    var descriptionInput = document.getElementById('dramaDescription');
    var audioUrlInput = document.getElementById('dramaAudioUrl');
    var coverHint = document.getElementById('dramaCoverHint');
    var audioHint = document.getElementById('dramaAudioHint');

    function setForm(data) {
        modalTitle.textContent = data.id > 0 ? 'Edit Drama' : 'Add Drama';
        idInput.value = String(data.id || 0);
        currentCoverInput.value = data.cover || '';
        currentAudioInput.value = data.audio || '';
        titleInput.value = data.title || '';
        slugInput.value = data.slug || '';
        categoryInput.value = data.category || 'General';
        statusInput.value = String(data.status || 1);
        descriptionInput.value = data.description || '';
        audioUrlInput.value = data.audioUrl || '';
        coverHint.textContent = data.cover ? ('Current cover: ' + data.cover) : 'Upload image (jpg, jpeg, png, webp).';
        audioHint.textContent = data.audio ? ('Current audio: ' + data.audio) : 'Upload audio file (mp3, wav, ogg, m4a).';
    }

    if (addBtn) {
        addBtn.addEventListener('click', function () {
            setForm({ id: 0, status: 1, category: 'General' });
        });
    }

    for (var i = 0; i < editButtons.length; i++) {
        editButtons[i].addEventListener('click', function () {
            setForm({
                id: parseInt(this.getAttribute('data-id') || '0', 10),
                title: this.getAttribute('data-title') || '',
                slug: this.getAttribute('data-slug') || '',
                category: this.getAttribute('data-category') || 'General',
                description: this.getAttribute('data-description') || '',
                status: parseInt(this.getAttribute('data-status') || '1', 10),
                cover: this.getAttribute('data-cover') || '',
                audio: this.getAttribute('data-audio') || '',
                audioUrl: this.getAttribute('data-audio-url') || ''
            });
        });
    }
})();
</script>

<?php include __DIR__ . '/../templates/admin_footer.php'; ?>
