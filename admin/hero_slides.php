<?php
require_once __DIR__ . '/_init.php';
require_login();
require_permission('settings.manage');

$adminTitle = 'Hero Slider Settings';
$activeMenu = 'hero-slides';

$slides = json_decode(setting('home_hero_slides', '[]'), true);
if (!is_array($slides)) {
    $slides = array();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['_token'])) {
        flash('error', 'Invalid request token.');
        redirect('admin/hero_slides.php');
    }

    $action = isset($_POST['action']) ? $_POST['action'] : '';

    if ($action === 'delete') {
        $index = isset($_POST['index']) ? (int) $_POST['index'] : -1;
        if (isset($slides[$index])) {
            unset($slides[$index]);
            $slides = array_values($slides);
            save_setting('home_hero_slides', json_encode($slides));
            flash('success', 'Slide deleted.');
        }
        redirect('admin/hero_slides.php');
    }

    if ($action === 'duplicate') {
        $index = isset($_POST['index']) ? (int) $_POST['index'] : -1;
        if (isset($slides[$index])) {
            $copy = $slides[$index];
            if (isset($copy['title']) && trim($copy['title']) !== '') {
                $copy['title'] = $copy['title'] . ' (Copy)';
            }
            array_splice($slides, $index + 1, 0, array($copy));
            save_setting('home_hero_slides', json_encode(array_values($slides)));
            flash('success', 'Slide duplicated.');
        }
        redirect('admin/hero_slides.php');
    }

    if ($action === 'save') {
        $index = isset($_POST['index']) ? (int) $_POST['index'] : -1;

        $currentImage = isset($_POST['current_image']) ? trim($_POST['current_image']) : '';
        $imagePath = $currentImage;

        if (!empty($_FILES['image']['name'])) {
            $up = upload_file($_FILES['image'], array('jpg', 'jpeg', 'png', 'webp'), MAX_IMAGE_UPLOAD, 'settings');
            if ($up['ok']) {
                $imagePath = $up['path'];
            }
        }

        $slide = array(
            'title' => isset($_POST['title']) ? trim($_POST['title']) : '',
            'subtitle' => isset($_POST['subtitle']) ? trim($_POST['subtitle']) : '',
            'line' => isset($_POST['line']) ? trim($_POST['line']) : '',
            'copy' => isset($_POST['copy']) ? trim($_POST['copy']) : '',
            'button_primary_text' => isset($_POST['button_primary_text']) ? trim($_POST['button_primary_text']) : '',
            'button_primary_link' => isset($_POST['button_primary_link']) ? trim($_POST['button_primary_link']) : '',
            'button_secondary_text' => isset($_POST['button_secondary_text']) ? trim($_POST['button_secondary_text']) : '',
            'button_secondary_link' => isset($_POST['button_secondary_link']) ? trim($_POST['button_secondary_link']) : '',
            'badge' => isset($_POST['badge']) ? trim($_POST['badge']) : '',
            'caption' => isset($_POST['caption']) ? trim($_POST['caption']) : '',
            'foot' => isset($_POST['foot']) ? trim($_POST['foot']) : '',
            'card_opacity' => max(0, min(95, (int) (isset($_POST['card_opacity']) ? $_POST['card_opacity'] : 64))),
            'card_position' => in_array(isset($_POST['card_position']) ? $_POST['card_position'] : '', array('left', 'center', 'right'), true) ? $_POST['card_position'] : 'left',
            'image_focus_x' => max(0, min(100, (int) (isset($_POST['image_focus_x']) ? $_POST['image_focus_x'] : 50))),
            'image_focus_y' => max(0, min(100, (int) (isset($_POST['image_focus_y']) ? $_POST['image_focus_y'] : 50))),
            'image_zoom' => max(1, min(2.5, (float) (isset($_POST['image_zoom']) ? $_POST['image_zoom'] : 1))),
            'image' => $imagePath,
        );

        if ($index >= 0 && isset($slides[$index])) {
            $slides[$index] = $slide;
            flash('success', 'Slide updated.');
        } else {
            $slides[] = $slide;
            flash('success', 'Slide created.');
        }

        save_setting('home_hero_slides', json_encode(array_values($slides)));
        redirect('admin/hero_slides.php');
    }
}

include __DIR__ . '/../templates/admin_header.php';
?>
<div class="panel soft-glass">
    <?php $okMsg = flash('success'); if ($okMsg): ?><div class="alert alert-success"><?php echo e($okMsg); ?></div><?php endif; ?>
    <?php $errMsg = flash('error'); if ($errMsg): ?><div class="alert alert-danger"><?php echo e($errMsg); ?></div><?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Slides</h5>
        <button type="button" class="btn btn-warning btn-sm" id="addSlideBtn" data-bs-toggle="modal" data-bs-target="#slideModal">
            <span class="material-symbols-outlined align-middle" style="font-size:18px;">add</span>
            Add Slide
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-sm align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Image</th>
                    <th>Title</th>
                    <th>Subtitle</th>
                    <th>Buttons</th>
                    <th>Card Style</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (count($slides) === 0): ?>
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">No slides yet. Click "Add Slide" to create one.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($slides as $idx => $slide): ?>
                    <tr>
                        <td><?php echo e($idx + 1); ?></td>
                        <td>
                            <?php if (!empty($slide['image'])): ?>
                                <?php $imgX = isset($slide['image_focus_x']) ? (int) $slide['image_focus_x'] : 50; ?>
                                <?php $imgY = isset($slide['image_focus_y']) ? (int) $slide['image_focus_y'] : 50; ?>
                                <img src="<?php echo e(media_url($slide['image'])); ?>" alt="slide" style="width:72px;height:42px;object-fit:cover;object-position:<?php echo e($imgX); ?>% <?php echo e($imgY); ?>%;border-radius:8px;">
                            <?php else: ?>
                                <span class="text-muted">No image</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo e(isset($slide['title']) ? $slide['title'] : ''); ?></td>
                        <td><?php echo e(isset($slide['subtitle']) ? $slide['subtitle'] : ''); ?></td>
                        <td>
                            <?php
                                $primaryText = isset($slide['button_primary_text']) && $slide['button_primary_text'] !== '' ? $slide['button_primary_text'] : (isset($slide['cta_text']) ? $slide['cta_text'] : '');
                                $primaryLink = isset($slide['button_primary_link']) && $slide['button_primary_link'] !== '' ? $slide['button_primary_link'] : (isset($slide['cta_link']) ? $slide['cta_link'] : '');
                                $secondaryText = isset($slide['button_secondary_text']) ? $slide['button_secondary_text'] : '';
                                $secondaryLink = isset($slide['button_secondary_link']) ? $slide['button_secondary_link'] : '';
                            ?>
                            <?php if ($primaryText !== '' && $primaryLink !== ''): ?>
                                <div><?php echo e($primaryText); ?> <small class="text-muted">(Primary)</small></div>
                                <small class="text-muted"><?php echo e($primaryLink); ?></small>
                            <?php endif; ?>
                            <?php if ($secondaryText !== '' && $secondaryLink !== ''): ?>
                                <div class="mt-1"><?php echo e($secondaryText); ?> <small class="text-muted">(Secondary)</small></div>
                                <small class="text-muted"><?php echo e($secondaryLink); ?></small>
                            <?php endif; ?>
                            <?php if (!(($primaryText !== '' && $primaryLink !== '') || ($secondaryText !== '' && $secondaryLink !== ''))): ?>
                                <span class="text-muted">No buttons</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php $cardOpacity = isset($slide['card_opacity']) ? (int) $slide['card_opacity'] : 64; ?>
                            <?php $cardPosition = isset($slide['card_position']) ? $slide['card_position'] : 'left'; ?>
                            <div><small>Opacity: <?php echo e($cardOpacity); ?>%</small></div>
                            <div><small class="text-muted">Position: <?php echo e(ucfirst($cardPosition)); ?></small></div>
                        </td>
                        <td class="text-end">
                            <form method="post" class="d-inline">
                                <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
                                <input type="hidden" name="action" value="duplicate">
                                <input type="hidden" name="index" value="<?php echo e($idx); ?>">
                                <button type="submit" class="btn btn-sm btn-outline-secondary" title="Duplicate Slide">
                                    <span class="material-symbols-outlined" style="font-size:18px;">content_copy</span>
                                </button>
                            </form>

                            <button
                                type="button"
                                class="btn btn-sm btn-outline-primary edit-slide-btn"
                                data-bs-toggle="modal"
                                data-bs-target="#slideModal"
                                data-index="<?php echo e($idx); ?>"
                                data-title="<?php echo e(isset($slide['title']) ? $slide['title'] : ''); ?>"
                                data-subtitle="<?php echo e(isset($slide['subtitle']) ? $slide['subtitle'] : ''); ?>"
                                data-line="<?php echo e(isset($slide['line']) ? $slide['line'] : ''); ?>"
                                data-copy="<?php echo e(isset($slide['copy']) ? $slide['copy'] : ''); ?>"
                                data-button-primary-text="<?php echo e(isset($slide['button_primary_text']) && $slide['button_primary_text'] !== '' ? $slide['button_primary_text'] : (isset($slide['cta_text']) ? $slide['cta_text'] : '')); ?>"
                                data-button-primary-link="<?php echo e(isset($slide['button_primary_link']) && $slide['button_primary_link'] !== '' ? $slide['button_primary_link'] : (isset($slide['cta_link']) ? $slide['cta_link'] : '')); ?>"
                                data-button-secondary-text="<?php echo e(isset($slide['button_secondary_text']) ? $slide['button_secondary_text'] : ''); ?>"
                                data-button-secondary-link="<?php echo e(isset($slide['button_secondary_link']) ? $slide['button_secondary_link'] : ''); ?>"
                                data-badge="<?php echo e(isset($slide['badge']) ? $slide['badge'] : ''); ?>"
                                data-caption="<?php echo e(isset($slide['caption']) ? $slide['caption'] : ''); ?>"
                                data-foot="<?php echo e(isset($slide['foot']) ? $slide['foot'] : ''); ?>"
                                data-card-opacity="<?php echo e(isset($slide['card_opacity']) ? (int) $slide['card_opacity'] : 64); ?>"
                                data-card-position="<?php echo e(isset($slide['card_position']) ? $slide['card_position'] : 'left'); ?>"
                                data-image="<?php echo e(isset($slide['image']) ? $slide['image'] : ''); ?>"
                                data-image-url="<?php echo e(isset($slide['image']) && $slide['image'] !== '' ? media_url($slide['image']) : ''); ?>"
                                data-image-focus-x="<?php echo e(isset($slide['image_focus_x']) ? (int) $slide['image_focus_x'] : 50); ?>"
                                data-image-focus-y="<?php echo e(isset($slide['image_focus_y']) ? (int) $slide['image_focus_y'] : 50); ?>"
                                data-image-zoom="<?php echo e(isset($slide['image_zoom']) ? (float) $slide['image_zoom'] : 1); ?>"
                                title="Edit Slide"
                            >
                                <span class="material-symbols-outlined" style="font-size:18px;">edit</span>
                            </button>

                            <form method="post" class="d-inline" onsubmit="return confirm('Delete this slide?');">
                                <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="index" value="<?php echo e($idx); ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Slide">
                                    <span class="material-symbols-outlined" style="font-size:18px;">delete</span>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="slideModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="slideModalTitle">Add Slide</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" enctype="multipart/form-data" id="slideForm">
                <div class="modal-body">
                    <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
                    <input type="hidden" name="action" value="save">
                    <input type="hidden" name="index" id="slideIndex" value="-1">
                    <input type="hidden" name="current_image" id="slideCurrentImage" value="">
                    <input type="hidden" name="image_focus_x" id="slideImageFocusX" value="50">
                    <input type="hidden" name="image_focus_y" id="slideImageFocusY" value="50">
                    <input type="hidden" name="image_zoom" id="slideImageZoom" value="1">

                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label">Title</label>
                            <input type="text" class="form-control" name="title" id="slideTitle">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Subtitle</label>
                            <input type="text" class="form-control" name="subtitle" id="slideSubtitle">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Top Line</label>
                            <input type="text" class="form-control" name="line" id="slideLine">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Badge</label>
                            <input type="text" class="form-control" name="badge" id="slideBadge">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Copy</label>
                            <textarea class="form-control" name="copy" id="slideCopy" rows="3"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Primary Button Text (Optional)</label>
                            <input type="text" class="form-control" name="button_primary_text" id="slidePrimaryButtonText" placeholder="Listen Live">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Primary Button Link (Optional)</label>
                            <input type="text" class="form-control" name="button_primary_link" id="slidePrimaryButtonLink" placeholder="/listen-live">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Secondary Button Text (Optional)</label>
                            <input type="text" class="form-control" name="button_secondary_text" id="slideSecondaryButtonText" placeholder="Partner With Us">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Secondary Button Link (Optional)</label>
                            <input type="text" class="form-control" name="button_secondary_link" id="slideSecondaryButtonLink" placeholder="/advertise-partner">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Caption (Optional)</label>
                            <input type="text" class="form-control" name="caption" id="slideCaption" placeholder="On-Air. Online. On-Ground.">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Footnote (Optional)</label>
                            <input type="text" class="form-control" name="foot" id="slideFoot" placeholder="For Peace & Development">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Slide Image</label>
                            <input type="file" class="form-control" name="image" id="slideImage" accept=".jpg,.jpeg,.png,.webp">
                            <small class="text-muted" id="slideImageHint"></small>
                            <div id="slideImagePreview" class="mt-2" style="position:relative;height:150px;border-radius:10px;border:1px solid rgba(37,99,235,.2);background:#eef2f7 center center / cover no-repeat;overflow:hidden;cursor:crosshair;">
                                <span id="slideImageFocusDot" style="position:absolute;left:50%;top:50%;width:14px;height:14px;border-radius:50%;border:2px solid #fff;background:rgba(37,99,235,.9);box-shadow:0 0 0 2px rgba(15,23,42,.25);transform:translate(-50%,-50%);"></span>
                            </div>
                            <div class="mt-2">
                                <label class="form-label mb-1">Image Zoom</label>
                                <input type="range" min="1" max="2.5" step="0.1" class="form-range" id="slideImageZoomRange" value="1">
                                <small class="text-muted">Zoom: <span id="slideImageZoomLabel">1.0</span>x</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Card Background Transparency (%)</label>
                            <input type="number" min="0" max="95" class="form-control" name="card_opacity" id="slideCardOpacity" value="64">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Card Position</label>
                            <select class="form-select" name="card_position" id="slideCardPosition">
                                <option value="left">Left</option>
                                <option value="center">Center</option>
                                <option value="right">Right</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Save Slide</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function () {
    var addBtn = document.getElementById('addSlideBtn');
    var editButtons = document.querySelectorAll('.edit-slide-btn');

    var modalTitle = document.getElementById('slideModalTitle');
    var slideIndex = document.getElementById('slideIndex');
    var slideCurrentImage = document.getElementById('slideCurrentImage');
    var slideTitle = document.getElementById('slideTitle');
    var slideSubtitle = document.getElementById('slideSubtitle');
    var slideLine = document.getElementById('slideLine');
    var slideBadge = document.getElementById('slideBadge');
    var slideCopy = document.getElementById('slideCopy');
    var slidePrimaryButtonText = document.getElementById('slidePrimaryButtonText');
    var slidePrimaryButtonLink = document.getElementById('slidePrimaryButtonLink');
    var slideSecondaryButtonText = document.getElementById('slideSecondaryButtonText');
    var slideSecondaryButtonLink = document.getElementById('slideSecondaryButtonLink');
    var slideCaption = document.getElementById('slideCaption');
    var slideFoot = document.getElementById('slideFoot');
    var slideCardOpacity = document.getElementById('slideCardOpacity');
    var slideCardPosition = document.getElementById('slideCardPosition');
    var slideImageHint = document.getElementById('slideImageHint');
    var slideImageInput = document.getElementById('slideImage');
    var slideImagePreview = document.getElementById('slideImagePreview');
    var slideImageFocusDot = document.getElementById('slideImageFocusDot');
    var slideImageFocusX = document.getElementById('slideImageFocusX');
    var slideImageFocusY = document.getElementById('slideImageFocusY');
    var slideImageZoom = document.getElementById('slideImageZoom');
    var slideImageZoomRange = document.getElementById('slideImageZoomRange');
    var slideImageZoomLabel = document.getElementById('slideImageZoomLabel');

    function setPreviewBackground(url) {
        slideImagePreview.style.backgroundImage = url ? ('url(' + url + ')') : 'none';
    }

    function setFocus(x, y) {
        var fx = Math.max(0, Math.min(100, parseInt(x, 10) || 50));
        var fy = Math.max(0, Math.min(100, parseInt(y, 10) || 50));
        slideImageFocusX.value = String(fx);
        slideImageFocusY.value = String(fy);
        slideImageFocusDot.style.left = fx + '%';
        slideImageFocusDot.style.top = fy + '%';
        slideImagePreview.style.backgroundPosition = fx + '% ' + fy + '%';
    }

    function setZoom(value) {
        var zoom = Math.max(1, Math.min(2.5, parseFloat(value) || 1));
        slideImageZoom.value = String(zoom);
        slideImageZoomRange.value = String(zoom);
        slideImageZoomLabel.textContent = zoom.toFixed(1);
        slideImagePreview.style.backgroundSize = (zoom * 100).toFixed(0) + '%';
    }

    function setForm(data) {
        modalTitle.textContent = data.index >= 0 ? 'Edit Slide' : 'Add Slide';
        slideIndex.value = String(data.index);
        slideCurrentImage.value = data.image || '';
        slideTitle.value = data.title || '';
        slideSubtitle.value = data.subtitle || '';
        slideLine.value = data.line || '';
        slideBadge.value = data.badge || '';
        slideCopy.value = data.copy || '';
        slidePrimaryButtonText.value = data.primaryButtonText || '';
        slidePrimaryButtonLink.value = data.primaryButtonLink || '';
        slideSecondaryButtonText.value = data.secondaryButtonText || '';
        slideSecondaryButtonLink.value = data.secondaryButtonLink || '';
        slideCaption.value = data.caption || '';
        slideFoot.value = data.foot || '';
        slideCardOpacity.value = data.cardOpacity || 64;
        slideCardPosition.value = data.cardPosition || 'left';
        slideImageHint.textContent = data.image ? ('Current image: ' + data.image) : 'Upload image (jpg, jpeg, png, webp).';
        setPreviewBackground(data.imageUrl || '');
        setFocus(data.imageFocusX || 50, data.imageFocusY || 50);
        setZoom(data.imageZoom || 1);
    }

    if (addBtn) {
        addBtn.addEventListener('click', function () {
            setForm({ index: -1 });
        });
    }

    for (var i = 0; i < editButtons.length; i++) {
        editButtons[i].addEventListener('click', function () {
            setForm({
                index: parseInt(this.getAttribute('data-index') || '-1', 10),
                title: this.getAttribute('data-title') || '',
                subtitle: this.getAttribute('data-subtitle') || '',
                line: this.getAttribute('data-line') || '',
                copy: this.getAttribute('data-copy') || '',
                primaryButtonText: this.getAttribute('data-button-primary-text') || '',
                primaryButtonLink: this.getAttribute('data-button-primary-link') || '',
                secondaryButtonText: this.getAttribute('data-button-secondary-text') || '',
                secondaryButtonLink: this.getAttribute('data-button-secondary-link') || '',
                badge: this.getAttribute('data-badge') || '',
                caption: this.getAttribute('data-caption') || '',
                foot: this.getAttribute('data-foot') || '',
                cardOpacity: this.getAttribute('data-card-opacity') || '64',
                cardPosition: this.getAttribute('data-card-position') || 'left',
                image: this.getAttribute('data-image') || '',
                imageUrl: this.getAttribute('data-image-url') || '',
                imageFocusX: this.getAttribute('data-image-focus-x') || '50',
                imageFocusY: this.getAttribute('data-image-focus-y') || '50',
                imageZoom: this.getAttribute('data-image-zoom') || '1'
            });
        });
    }

    if (slideImagePreview) {
        slideImagePreview.addEventListener('click', function (e) {
            var rect = slideImagePreview.getBoundingClientRect();
            var x = ((e.clientX - rect.left) / rect.width) * 100;
            var y = ((e.clientY - rect.top) / rect.height) * 100;
            setFocus(x, y);
        });
    }

    if (slideImageZoomRange) {
        slideImageZoomRange.addEventListener('input', function () {
            setZoom(this.value);
        });
    }

    if (slideImageInput) {
        slideImageInput.addEventListener('change', function () {
            if (!this.files || !this.files[0]) {
                return;
            }
            var reader = new FileReader();
            reader.onload = function (ev) {
                setPreviewBackground(ev.target.result);
            };
            reader.readAsDataURL(this.files[0]);
        });
    }
})();
</script>

<?php include __DIR__ . '/../templates/admin_footer.php'; ?>
