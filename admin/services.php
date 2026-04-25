<?php
require_once __DIR__ . '/_init.php';
require_login();
require_permission('services.manage');

$adminTitle = 'Services';
$activeMenu = 'services';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['_token'])) {
        redirect('admin/services.php');
    }

    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $desc = isset($_POST['description']) ? trim($_POST['description']) : '';
    $icon = isset($_POST['icon_class']) ? trim($_POST['icon_class']) : '';
    $sort = isset($_POST['sort_order']) ? (int) $_POST['sort_order'] : 1;
    $status = isset($_POST['status']) ? (int) $_POST['status'] : 1;

    if ($id > 0) {
        db_query(
            'UPDATE services SET title=?, description=?, icon_class=?, sort_order=?, status=?, updated_at=NOW() WHERE id=?',
            array($title, $desc, $icon, $sort, $status, $id)
        );
    } else {
        db_query(
            'INSERT INTO services (title, description, icon_class, sort_order, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())',
            array($title, $desc, $icon, $sort, $status)
        );
    }

    redirect('admin/services.php');
}

if (isset($_GET['delete'])) {
    db_query('DELETE FROM services WHERE id=?', array((int) $_GET['delete']));
    redirect('admin/services.php');
}

$edit = isset($_GET['edit']) ? db_query('SELECT * FROM services WHERE id=?', array((int) $_GET['edit']))->fetch() : null;
$rows = db_query('SELECT * FROM services ORDER BY sort_order ASC, id DESC')->fetchAll();

include __DIR__ . '/../templates/admin_header.php';
?>

<div class="panel">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Services</h5>
        <button type="button" class="btn btn-warning btn-sm" id="addServiceBtn" data-bs-toggle="modal" data-bs-target="#serviceModal">
            <span class="material-symbols-outlined align-middle" style="font-size:18px;">add</span>
            Add Service
        </button>
    </div>

    <?php if (!$rows): ?>
        <div class="text-center text-muted py-4">No services added yet.</div>
    <?php else: ?>
        <div class="list-group list-group-flush">
            <?php foreach ($rows as $r): ?>
                <div class="list-group-item px-0 d-flex justify-content-between align-items-start gap-3">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <strong><?php echo e($r['title']); ?></strong>
                            <span class="badge <?php echo (int) $r['status'] === 1 ? 'text-bg-success' : 'text-bg-secondary'; ?>">
                                <?php echo (int) $r['status'] === 1 ? 'Active' : 'Inactive'; ?>
                            </span>
                            <span class="badge text-bg-light">Order <?php echo e($r['sort_order']); ?></span>
                        </div>
                        <?php if (!empty($r['description'])): ?>
                            <p class="mb-1 text-muted"><?php echo e($r['description']); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($r['icon_class'])): ?>
                            <small class="text-muted">Icon: <?php echo e($r['icon_class']); ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="d-flex gap-2">
                        <button
                            type="button"
                            class="btn btn-sm btn-outline-primary edit-service-btn"
                            data-bs-toggle="modal"
                            data-bs-target="#serviceModal"
                            data-id="<?php echo e($r['id']); ?>"
                            data-title="<?php echo e($r['title']); ?>"
                            data-description="<?php echo e($r['description']); ?>"
                            data-icon="<?php echo e($r['icon_class']); ?>"
                            data-sort="<?php echo e($r['sort_order']); ?>"
                            data-status="<?php echo e((int) $r['status']); ?>"
                        >
                            Edit
                        </button>
                        <a href="?delete=<?php echo e($r['id']); ?>" data-confirm="Delete this service?" class="btn btn-sm btn-outline-danger">Delete</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<div class="modal fade" id="serviceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="serviceModalTitle">Add Service</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form method="post">
                <div class="modal-body">
                    <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
                    <input type="hidden" name="id" id="serviceId" value="0">

                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label">Title</label>
                            <input class="form-control" name="title" id="serviceTitle" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Icon Class</label>
                            <input class="form-control" name="icon_class" id="serviceIcon" placeholder="material-symbols-outlined">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Sort Order</label>
                            <input class="form-control" type="number" name="sort_order" id="serviceSort" value="1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" id="serviceStatus">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" id="serviceDescription" rows="4" placeholder="Service description"></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Save Service</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function () {
    var addBtn = document.getElementById('addServiceBtn');
    var editButtons = document.querySelectorAll('.edit-service-btn');
    var modalTitle = document.getElementById('serviceModalTitle');
    var idInput = document.getElementById('serviceId');
    var titleInput = document.getElementById('serviceTitle');
    var descInput = document.getElementById('serviceDescription');
    var iconInput = document.getElementById('serviceIcon');
    var sortInput = document.getElementById('serviceSort');
    var statusInput = document.getElementById('serviceStatus');
    var modalElement = document.getElementById('serviceModal');

    function setForm(data) {
        modalTitle.textContent = data.id > 0 ? 'Edit Service' : 'Add Service';
        idInput.value = String(data.id || 0);
        titleInput.value = data.title || '';
        descInput.value = data.description || '';
        iconInput.value = data.icon || '';
        sortInput.value = String(data.sort || 1);
        statusInput.value = String(typeof data.status === 'number' ? data.status : 1);
    }

    if (addBtn) {
        addBtn.addEventListener('click', function () {
            setForm({
                id: 0,
                title: '',
                description: '',
                icon: '',
                sort: 1,
                status: 1
            });
        });
    }

    Array.prototype.forEach.call(editButtons, function (button) {
        button.addEventListener('click', function () {
            setForm({
                id: parseInt(button.getAttribute('data-id'), 10) || 0,
                title: button.getAttribute('data-title') || '',
                description: button.getAttribute('data-description') || '',
                icon: button.getAttribute('data-icon') || '',
                sort: parseInt(button.getAttribute('data-sort'), 10) || 1,
                status: parseInt(button.getAttribute('data-status'), 10) || 0
            });
        });
    });

    <?php if ($edit): ?>
    var initialEdit = <?php echo json_encode(array(
        'id' => (int) $edit['id'],
        'title' => (string) $edit['title'],
        'description' => (string) $edit['description'],
        'icon' => (string) $edit['icon_class'],
        'sort' => (int) $edit['sort_order'],
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
