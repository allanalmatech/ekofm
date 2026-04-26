<?php
require_once __DIR__ . '/_init.php';
require_login();
require_permission('programs.manage');

$adminTitle = 'Programs Schedule';
$activeMenu = 'programs';

$hasFocusX = db_query("SHOW COLUMNS FROM programs LIKE 'cover_focus_x'")->fetch();
if (!$hasFocusX) {
    db_query("ALTER TABLE programs ADD COLUMN cover_focus_x TINYINT UNSIGNED NOT NULL DEFAULT 50 AFTER cover_image");
}

$hasFocusY = db_query("SHOW COLUMNS FROM programs LIKE 'cover_focus_y'")->fetch();
if (!$hasFocusY) {
    db_query("ALTER TABLE programs ADD COLUMN cover_focus_y TINYINT UNSIGNED NOT NULL DEFAULT 50 AFTER cover_focus_x");
}

$hasBriefDescription = db_query("SHOW COLUMNS FROM programs LIKE 'brief_description'")->fetch();
if (!$hasBriefDescription) {
    db_query("ALTER TABLE programs ADD COLUMN brief_description TEXT NULL AFTER end_time");
}

$hasFullDescription = db_query("SHOW COLUMNS FROM programs LIKE 'full_description'")->fetch();
if (!$hasFullDescription) {
    db_query("ALTER TABLE programs ADD COLUMN full_description MEDIUMTEXT NULL AFTER brief_description");
}

$hasWhatToExpect = db_query("SHOW COLUMNS FROM programs LIKE 'what_to_expect'")->fetch();
if (!$hasWhatToExpect) {
    db_query("ALTER TABLE programs ADD COLUMN what_to_expect MEDIUMTEXT NULL AFTER full_description");
}

$hasTonesTable = db_query("SHOW TABLES LIKE 'tones'")->fetch();
if (!$hasTonesTable) {
    db_query('CREATE TABLE tones (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(120) NOT NULL UNIQUE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');
}

$hasProgramTonesTable = db_query("SHOW TABLES LIKE 'program_tones'")->fetch();
if (!$hasProgramTonesTable) {
    db_query('CREATE TABLE program_tones (id INT AUTO_INCREMENT PRIMARY KEY, program_id INT, tone_id INT, UNIQUE KEY uniq_program_tone (program_id, tone_id), KEY idx_program_tones_program (program_id), KEY idx_program_tones_tone (tone_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['_token'])) {
        redirect('admin/programs.php');
    }

    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $slugInput = isset($_POST['slug']) ? trim($_POST['slug']) : '';
    $slug = $slugInput !== '' ? slugify($slugInput) : slugify($title);
    $presenter = isset($_POST['presenter']) ? trim($_POST['presenter']) : '';
    $dayOfWeek = isset($_POST['day_of_week']) ? trim($_POST['day_of_week']) : '';
    $startTime = isset($_POST['start_time']) ? trim($_POST['start_time']) : null;
    $endTime = isset($_POST['end_time']) ? trim($_POST['end_time']) : null;
    $briefDescription = isset($_POST['brief_description']) ? trim($_POST['brief_description']) : '';
    $fullDescription = isset($_POST['full_description']) ? trim($_POST['full_description']) : '';
    $whatToExpect = isset($_POST['what_to_expect']) ? trim($_POST['what_to_expect']) : '';
    $tonesRaw = isset($_POST['tones']) ? trim($_POST['tones']) : '';

    if ($briefDescription === '' && isset($_POST['description'])) {
        $briefDescription = trim($_POST['description']);
    }
    if ($fullDescription === '' && $briefDescription !== '') {
        $fullDescription = $briefDescription;
    }

    $description = $briefDescription;
    $status = isset($_POST['status']) ? (int) $_POST['status'] : 1;
    $coverFocusX = isset($_POST['cover_focus_x']) ? max(0, min(100, (int) $_POST['cover_focus_x'])) : 50;
    $coverFocusY = isset($_POST['cover_focus_y']) ? max(0, min(100, (int) $_POST['cover_focus_y'])) : 50;

    $coverImage = isset($_POST['current_cover_image']) ? trim($_POST['current_cover_image']) : '';
    if (!empty($_FILES['cover_image']['name'])) {
        $up = upload_file($_FILES['cover_image'], array('jpg', 'jpeg', 'png', 'webp'), MAX_IMAGE_UPLOAD, 'programs');
        if ($up['ok']) {
            $coverImage = $up['path'];
        }
    }

    $programId = $id;
    if ($id > 0) {
        db_query(
            'UPDATE programs SET title=?, slug=?, presenter=?, cover_image=?, cover_focus_x=?, cover_focus_y=?, day_of_week=?, start_time=?, end_time=?, description=?, brief_description=?, full_description=?, what_to_expect=?, status=?, updated_at=NOW() WHERE id=?',
            array($title, $slug, $presenter, $coverImage, $coverFocusX, $coverFocusY, $dayOfWeek, $startTime, $endTime, $description, $briefDescription, $fullDescription, $whatToExpect, $status, $id)
        );
    } else {
        db_query(
            'INSERT INTO programs (title, slug, presenter, cover_image, cover_focus_x, cover_focus_y, day_of_week, start_time, end_time, description, brief_description, full_description, what_to_expect, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())',
            array($title, $slug, $presenter, $coverImage, $coverFocusX, $coverFocusY, $dayOfWeek, $startTime, $endTime, $description, $briefDescription, $fullDescription, $whatToExpect, $status)
        );
        $programId = (int) db()->lastInsertId();
    }

    if ($programId > 0) {
        $toneNames = array();
        if ($tonesRaw !== '') {
            $parts = explode(',', $tonesRaw);
            foreach ($parts as $part) {
                $name = trim($part);
                if ($name === '') {
                    continue;
                }
                $toneNames[$name] = $name;
            }
        }

        db_query('DELETE FROM program_tones WHERE program_id=?', array($programId));

        foreach (array_values($toneNames) as $toneName) {
            db_query('INSERT INTO tones (name) VALUES (?) ON DUPLICATE KEY UPDATE name=VALUES(name)', array($toneName));
            $toneRow = db_query('SELECT id FROM tones WHERE name=? LIMIT 1', array($toneName))->fetch();
            if (!$toneRow) {
                continue;
            }
            db_query('INSERT INTO program_tones (program_id, tone_id) VALUES (?, ?) ON DUPLICATE KEY UPDATE tone_id=VALUES(tone_id)', array($programId, (int) $toneRow['id']));
        }
    }

    redirect('admin/programs.php');
}

if (isset($_GET['delete'])) {
    db_query('DELETE FROM programs WHERE id=?', array((int) $_GET['delete']));
    redirect('admin/programs.php');
}

$edit = isset($_GET['edit']) ? db_query('SELECT * FROM programs WHERE id=?', array((int) $_GET['edit']))->fetch() : null;
$rows = db_query('SELECT * FROM programs ORDER BY day_of_week ASC, start_time ASC, id DESC')->fetchAll();

$programToneMap = array();
if ($rows) {
    $toneRows = db_query('SELECT pt.program_id, t.name FROM program_tones pt INNER JOIN tones t ON t.id = pt.tone_id ORDER BY t.name ASC')->fetchAll();
    foreach ($toneRows as $tr) {
        $pid = (int) $tr['program_id'];
        if (!isset($programToneMap[$pid])) {
            $programToneMap[$pid] = array();
        }
        $programToneMap[$pid][] = $tr['name'];
    }
}

$toneSuggestions = db_query('SELECT name FROM tones ORDER BY name ASC')->fetchAll();
$toneSuggestionNames = array();
foreach ($toneSuggestions as $ts) {
    $toneSuggestionNames[] = $ts['name'];
}

include __DIR__ . '/../templates/admin_header.php';
?>
<div class="panel">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Programs</h5>
        <button type="button" class="btn btn-warning btn-sm" id="addProgramBtn" data-bs-toggle="modal" data-bs-target="#programModal">
            <span class="material-symbols-outlined align-middle" style="font-size:18px;">add</span>
            Add Show
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-sm align-middle">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Program</th>
                    <th>Schedule</th>
                    <th>Host</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!$rows): ?>
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">No programs added yet.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($rows as $r): ?>
                    <tr>
                        <td>
                            <img src="<?php echo e(media_url($r['cover_image'])); ?>" alt="cover" style="width:72px;height:42px;object-fit:cover;object-position:<?php echo e((int) (isset($r['cover_focus_x']) ? $r['cover_focus_x'] : 50)); ?>% <?php echo e((int) (isset($r['cover_focus_y']) ? $r['cover_focus_y'] : 50)); ?>%;border-radius:8px;">
                        </td>
                        <td>
                            <strong><?php echo e($r['title']); ?></strong>
                            <?php if (!empty($r['slug'])): ?>
                                <br><small class="text-muted"><?php echo e($r['slug']); ?></small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php echo e($r['day_of_week']); ?>
                            <br><small class="text-muted"><?php echo e($r['start_time'] ? substr($r['start_time'], 0, 5) : '--:--'); ?>-<?php echo e($r['end_time'] ? substr($r['end_time'], 0, 5) : '--:--'); ?></small>
                        </td>
                        <td><?php echo e($r['presenter']); ?></td>
                        <td><?php echo (int) $r['status'] === 1 ? 'Active' : 'Inactive'; ?></td>
                        <td class="text-end">
                            <?php $rowTones = isset($programToneMap[(int) $r['id']]) ? $programToneMap[(int) $r['id']] : array(); ?>
                            <button
                                type="button"
                                class="btn btn-sm btn-outline-primary edit-program-btn"
                                data-bs-toggle="modal"
                                data-bs-target="#programModal"
                                data-id="<?php echo e($r['id']); ?>"
                                data-title="<?php echo e($r['title']); ?>"
                                data-slug="<?php echo e($r['slug']); ?>"
                                data-presenter="<?php echo e($r['presenter']); ?>"
                                data-day="<?php echo e($r['day_of_week']); ?>"
                                data-start-time="<?php echo e($r['start_time']); ?>"
                                data-end-time="<?php echo e($r['end_time']); ?>"
                                data-brief-description="<?php echo e(isset($r['brief_description']) ? $r['brief_description'] : $r['description']); ?>"
                                data-full-description="<?php echo e(isset($r['full_description']) ? $r['full_description'] : $r['description']); ?>"
                                data-what-to-expect="<?php echo e(isset($r['what_to_expect']) ? $r['what_to_expect'] : ''); ?>"
                                data-tones="<?php echo e(implode(', ', $rowTones)); ?>"
                                data-description="<?php echo e($r['description']); ?>"
                                data-status="<?php echo e((int) $r['status']); ?>"
                                data-image="<?php echo e($r['cover_image']); ?>"
                                data-image-url="<?php echo e(media_url($r['cover_image'])); ?>"
                                data-focus-x="<?php echo e((int) (isset($r['cover_focus_x']) ? $r['cover_focus_x'] : 50)); ?>"
                                data-focus-y="<?php echo e((int) (isset($r['cover_focus_y']) ? $r['cover_focus_y'] : 50)); ?>"
                                title="Edit Show"
                            >
                                Edit
                            </button>
                            <a href="?delete=<?php echo e($r['id']); ?>" data-confirm="Delete this program?" class="btn btn-sm btn-outline-danger">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="programModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="programModalTitle">Add Show</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
                    <input type="hidden" name="id" id="programId" value="0">
                    <input type="hidden" name="current_cover_image" id="programCurrentImage" value="">
                    <input type="hidden" name="cover_focus_x" id="programFocusX" value="50">
                    <input type="hidden" name="cover_focus_y" id="programFocusY" value="50">

                    <div class="row g-2">
                        <div class="col-12">
                            <h6 class="mb-1">Basic Information</h6>
                            <hr class="mt-0 mb-2">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Title</label>
                            <input class="form-control" name="title" id="programTitle" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Slug (optional)</label>
                            <input class="form-control" name="slug" id="programSlug" placeholder="auto-from-title">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Presenter</label>
                            <input class="form-control" name="presenter" id="programPresenter">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Day</label>
                            <input class="form-control" name="day_of_week" id="programDay" value="Monday">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Start Time</label>
                            <input class="form-control" type="time" name="start_time" id="programStartTime" value="06:00">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">End Time</label>
                            <input class="form-control" type="time" name="end_time" id="programEndTime" value="08:00">
                        </div>

                        <div class="col-12 mt-2">
                            <h6 class="mb-1">Description Sections</h6>
                            <hr class="mt-0 mb-2">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Brief Description</label>
                            <textarea class="form-control" rows="3" name="brief_description" id="programBriefDescription"></textarea>
                            <small class="text-muted d-block mt-1">Shown on cards/listings.</small>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Full Description</label>
                            <textarea class="form-control" rows="5" name="full_description" id="programFullDescription"></textarea>
                            <small class="text-muted d-block mt-1">Shown on the show details page.</small>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">What To Expect (optional)</label>
                            <textarea class="form-control" rows="4" name="what_to_expect" id="programWhatToExpect" placeholder="Bullet points or short paragraph."></textarea>
                            <small class="text-muted d-block mt-1">If empty, this section is hidden on the show details page.</small>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Tones</label>
                            <input class="form-control" name="tones" id="programTones" list="toneSuggestions" placeholder="Type tone, then comma for next">
                            <datalist id="toneSuggestions">
                                <?php foreach ($toneSuggestionNames as $toneName): ?>
                                    <option value="<?php echo e($toneName); ?>"></option>
                                <?php endforeach; ?>
                            </datalist>
                            <small class="text-muted d-block mt-1">Separate tones with commas. Example: Energetic, Smart, Interactive</small>
                        </div>

                        <div class="col-12 mt-2">
                            <h6 class="mb-1">Publishing & Media</h6>
                            <hr class="mt-0 mb-2">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" id="programStatus">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Show Image</label>
                            <input class="form-control" type="file" name="cover_image" id="programImageInput" accept=".jpg,.jpeg,.png,.webp">
                            <small class="text-muted d-block mt-1" id="programImageHint">Upload image (jpg, jpeg, png, webp).</small>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Thumbnail Focus Area</label>
                            <div id="programThumbPreview" style="position:relative;height:180px;border-radius:12px;border:1px solid rgba(37,99,235,.2);background:#e5e7eb center center / cover no-repeat;overflow:hidden;cursor:crosshair;">
                                <span id="programFocusDot" style="position:absolute;left:50%;top:50%;width:14px;height:14px;border-radius:50%;border:2px solid #fff;background:rgba(37,99,235,.85);box-shadow:0 0 0 2px rgba(15,23,42,.25);transform:translate(-50%,-50%);"></span>
                            </div>
                            <small class="text-muted d-block mt-1">Click preview to choose the area shown in show thumbnails.</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Save Show</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function () {
    var addBtn = document.getElementById('addProgramBtn');
    var editButtons = document.querySelectorAll('.edit-program-btn');

    var modalTitle = document.getElementById('programModalTitle');
    var idInput = document.getElementById('programId');
    var currentImageInput = document.getElementById('programCurrentImage');
    var titleInput = document.getElementById('programTitle');
    var slugInput = document.getElementById('programSlug');
    var presenterInput = document.getElementById('programPresenter');
    var dayInput = document.getElementById('programDay');
    var startInput = document.getElementById('programStartTime');
    var endInput = document.getElementById('programEndTime');
    var briefDescriptionInput = document.getElementById('programBriefDescription');
    var fullDescriptionInput = document.getElementById('programFullDescription');
    var whatToExpectInput = document.getElementById('programWhatToExpect');
    var tonesInput = document.getElementById('programTones');
    var statusInput = document.getElementById('programStatus');
    var imageHint = document.getElementById('programImageHint');
    var imageInput = document.getElementById('programImageInput');
    var preview = document.getElementById('programThumbPreview');
    var focusDot = document.getElementById('programFocusDot');
    var focusXInput = document.getElementById('programFocusX');
    var focusYInput = document.getElementById('programFocusY');

    function setPreviewImage(path) {
        preview.style.backgroundImage = path ? ('url(' + path + ')') : 'none';
    }

    function setFocus(x, y) {
        var fx = Math.max(0, Math.min(100, parseInt(x, 10) || 50));
        var fy = Math.max(0, Math.min(100, parseInt(y, 10) || 50));
        focusXInput.value = String(fx);
        focusYInput.value = String(fy);
        focusDot.style.left = fx + '%';
        focusDot.style.top = fy + '%';
        preview.style.backgroundPosition = fx + '% ' + fy + '%';
    }

    function setForm(data) {
        modalTitle.textContent = data.id > 0 ? 'Edit Show' : 'Add Show';
        idInput.value = String(data.id || 0);
        currentImageInput.value = data.image || '';
        titleInput.value = data.title || '';
        slugInput.value = data.slug || '';
        presenterInput.value = data.presenter || '';
        dayInput.value = data.day || 'Monday';
        startInput.value = data.startTime || '06:00';
        endInput.value = data.endTime || '08:00';
        briefDescriptionInput.value = data.briefDescription || data.description || '';
        fullDescriptionInput.value = data.fullDescription || data.briefDescription || data.description || '';
        whatToExpectInput.value = data.whatToExpect || '';
        tonesInput.value = data.tones || '';
        statusInput.value = String(data.status || 1);
        imageHint.textContent = data.image ? ('Current image: ' + data.image) : 'Upload image (jpg, jpeg, png, webp).';
        setPreviewImage(data.image ? data.imageUrl : '');
        setFocus(data.focusX || 50, data.focusY || 50);
    }

    if (addBtn) {
        addBtn.addEventListener('click', function () {
            setForm({ id: 0, status: 1 });
        });
    }

    for (var i = 0; i < editButtons.length; i++) {
        editButtons[i].addEventListener('click', function () {
            setForm({
                id: parseInt(this.getAttribute('data-id') || '0', 10),
                title: this.getAttribute('data-title') || '',
                slug: this.getAttribute('data-slug') || '',
                presenter: this.getAttribute('data-presenter') || '',
                day: this.getAttribute('data-day') || 'Monday',
                startTime: this.getAttribute('data-start-time') || '06:00',
                endTime: this.getAttribute('data-end-time') || '08:00',
                briefDescription: this.getAttribute('data-brief-description') || '',
                fullDescription: this.getAttribute('data-full-description') || '',
                whatToExpect: this.getAttribute('data-what-to-expect') || '',
                tones: this.getAttribute('data-tones') || '',
                description: this.getAttribute('data-description') || '',
                status: parseInt(this.getAttribute('data-status') || '1', 10),
                image: this.getAttribute('data-image') || '',
                imageUrl: this.getAttribute('data-image-url') || '',
                focusX: parseInt(this.getAttribute('data-focus-x') || '50', 10),
                focusY: parseInt(this.getAttribute('data-focus-y') || '50', 10)
            });
        });
    }

    if (preview) {
        preview.addEventListener('click', function (e) {
            var rect = preview.getBoundingClientRect();
            var x = ((e.clientX - rect.left) / rect.width) * 100;
            var y = ((e.clientY - rect.top) / rect.height) * 100;
            setFocus(x, y);
        });
    }

    if (imageInput) {
        imageInput.addEventListener('change', function () {
            if (!this.files || !this.files[0]) {
                return;
            }
            var reader = new FileReader();
            reader.onload = function (ev) {
                setPreviewImage(ev.target.result);
            };
            reader.readAsDataURL(this.files[0]);
        });
    }
})();
</script>

<?php include __DIR__ . '/../templates/admin_footer.php'; ?>
