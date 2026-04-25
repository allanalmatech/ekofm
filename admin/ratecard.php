<?php
require_once __DIR__ . '/_init.php';
require_login();
require_permission('ratecard.manage');

$adminTitle = 'Rate Card';
$activeMenu = 'rate-card';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['_token'])) {
        redirect('admin/ratecard.php');
    }

    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    $values = array(
        isset($_POST['category_name']) ? trim($_POST['category_name']) : '',
        isset($_POST['title']) ? trim($_POST['title']) : '',
        isset($_POST['description']) ? trim($_POST['description']) : '',
        isset($_POST['price_label']) ? trim($_POST['price_label']) : '',
        isset($_POST['sort_order']) ? (int) $_POST['sort_order'] : 1,
        isset($_POST['status']) ? (int) $_POST['status'] : 1
    );

    if ($id > 0) {
        $values[] = $id;
        db_query('UPDATE rate_cards SET category_name=?, title=?, description=?, price_label=?, sort_order=?, status=?, updated_at=NOW() WHERE id=?', $values);
    } else {
        db_query('INSERT INTO rate_cards (category_name, title, description, price_label, sort_order, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())', $values);
    }

    redirect('admin/ratecard.php');
}

if (isset($_GET['delete'])) {
    db_query('DELETE FROM rate_cards WHERE id=?', array((int) $_GET['delete']));
    redirect('admin/ratecard.php');
}

$edit = isset($_GET['edit']) ? db_query('SELECT * FROM rate_cards WHERE id=?', array((int) $_GET['edit']))->fetch() : null;
$rows = db_query('SELECT * FROM rate_cards ORDER BY category_name, sort_order, id DESC')->fetchAll();

include __DIR__ . '/../templates/admin_header.php';
?>

<div class="panel">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Rate Card Items</h5>
        <button type="button" class="btn btn-warning btn-sm" id="addRateBtn" data-bs-toggle="modal" data-bs-target="#rateModal">
            <span class="material-symbols-outlined align-middle" style="font-size:18px;">add</span>
            Add Item
        </button>
    </div>

    <?php if (!$rows): ?>
        <div class="text-center text-muted py-4">No rate card items added yet.</div>
    <?php else: ?>
        <div class="list-group list-group-flush">
            <?php foreach ($rows as $r): ?>
                <div class="list-group-item px-0 d-flex justify-content-between align-items-start gap-3">
                    <div>
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                            <strong><?php echo e($r['title']); ?></strong>
                            <span class="badge text-bg-light"><?php echo e($r['category_name']); ?></span>
                            <span class="badge <?php echo (int) $r['status'] === 1 ? 'text-bg-success' : 'text-bg-secondary'; ?>">
                                <?php echo (int) $r['status'] === 1 ? 'Active' : 'Inactive'; ?>
                            </span>
                            <span class="badge text-bg-light">Order <?php echo e($r['sort_order']); ?></span>
                        </div>
                        <div class="fw-semibold mb-1"><?php echo e($r['price_label']); ?></div>
                        <?php if (!empty($r['description'])): ?>
                            <p class="mb-0 text-muted"><?php echo e($r['description']); ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="d-flex gap-2">
                        <button
                            type="button"
                            class="btn btn-sm btn-outline-primary edit-rate-btn"
                            data-bs-toggle="modal"
                            data-bs-target="#rateModal"
                            data-id="<?php echo e($r['id']); ?>"
                            data-category="<?php echo e($r['category_name']); ?>"
                            data-title="<?php echo e($r['title']); ?>"
                            data-description="<?php echo e($r['description']); ?>"
                            data-price="<?php echo e($r['price_label']); ?>"
                            data-sort="<?php echo e($r['sort_order']); ?>"
                            data-status="<?php echo e((int) $r['status']); ?>"
                        >
                            Edit
                        </button>
                        <a class="btn btn-sm btn-outline-danger" href="?delete=<?php echo e($r['id']); ?>" data-confirm="Delete this rate item?">Delete</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<div class="modal fade" id="rateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rateModalTitle">Add Rate Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form method="post">
                <div class="modal-body">
                    <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
                    <input type="hidden" name="id" id="rateId" value="0">

                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label">Category</label>
                            <input class="form-control" name="category_name" id="rateCategory" value="Advertising">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Title</label>
                            <input class="form-control" name="title" id="rateTitle" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Price Label</label>
                            <input class="form-control" name="price_label" id="ratePrice" placeholder="N0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Sort Order</label>
                            <input class="form-control" type="number" name="sort_order" id="rateSort" value="1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" id="rateStatus">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" id="rateDescription" rows="4"></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-warning">Save Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function () {
    var addBtn = document.getElementById('addRateBtn');
    var editButtons = document.querySelectorAll('.edit-rate-btn');
    var modalTitle = document.getElementById('rateModalTitle');
    var idInput = document.getElementById('rateId');
    var categoryInput = document.getElementById('rateCategory');
    var titleInput = document.getElementById('rateTitle');
    var descInput = document.getElementById('rateDescription');
    var priceInput = document.getElementById('ratePrice');
    var sortInput = document.getElementById('rateSort');
    var statusInput = document.getElementById('rateStatus');
    var modalElement = document.getElementById('rateModal');

    function setForm(data) {
        modalTitle.textContent = data.id > 0 ? 'Edit Rate Item' : 'Add Rate Item';
        idInput.value = String(data.id || 0);
        categoryInput.value = data.category || 'Advertising';
        titleInput.value = data.title || '';
        descInput.value = data.description || '';
        priceInput.value = data.price || 'N0';
        sortInput.value = String(data.sort || 1);
        statusInput.value = String(typeof data.status === 'number' ? data.status : 1);
    }

    if (addBtn) {
        addBtn.addEventListener('click', function () {
            setForm({
                id: 0,
                category: 'Advertising',
                title: '',
                description: '',
                price: 'N0',
                sort: 1,
                status: 1
            });
        });
    }

    Array.prototype.forEach.call(editButtons, function (button) {
        button.addEventListener('click', function () {
            setForm({
                id: parseInt(button.getAttribute('data-id'), 10) || 0,
                category: button.getAttribute('data-category') || 'Advertising',
                title: button.getAttribute('data-title') || '',
                description: button.getAttribute('data-description') || '',
                price: button.getAttribute('data-price') || 'N0',
                sort: parseInt(button.getAttribute('data-sort'), 10) || 1,
                status: parseInt(button.getAttribute('data-status'), 10) || 0
            });
        });
    });

    <?php if ($edit): ?>
    var initialEdit = <?php echo json_encode(array(
        'id' => (int) $edit['id'],
        'category' => (string) $edit['category_name'],
        'title' => (string) $edit['title'],
        'description' => (string) $edit['description'],
        'price' => (string) $edit['price_label'],
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
