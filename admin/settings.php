<?php
require_once __DIR__ . '/_init.php';
require_login();
require_permission('settings.manage');

$adminTitle = 'Site Settings';
$activeMenu = 'settings';

$groups = array(
    'branding' => array('site_name', 'site_tagline', 'footer_tagline'),
    'contact' => array('contact_location', 'contact_address', 'contact_phone', 'contact_email', 'contact_whatsapp', 'contact_map_embed'),
    'social_handles' => array('social_facebook_handle', 'social_x_handle', 'social_instagram_handle', 'social_tiktok_handle', 'social_youtube_handle', 'social_whatsapp_handle'),
    'social_urls' => array('social_facebook_url', 'social_x_url', 'social_instagram_url', 'social_tiktok_url', 'social_youtube_url'),
    'home' => array('home_hero_title', 'home_hero_subtitle', 'home_hero_line', 'home_hero_copy', 'home_hero_cta_text', 'home_hero_cta_link', 'partner_cta_title', 'partner_cta_text'),
    'seo' => array('home_meta_title', 'home_meta_description', 'contact_page_intro')
);

$allKeys = array();
foreach ($groups as $list) {
    foreach ($list as $key) {
        $allKeys[] = $key;
    }
}

function create_image_resource($path, $mime)
{
    if ($mime === 'image/jpeg') {
        return @imagecreatefromjpeg($path);
    }
    if ($mime === 'image/png') {
        return @imagecreatefrompng($path);
    }
    if ($mime === 'image/webp' && function_exists('imagecreatefromwebp')) {
        return @imagecreatefromwebp($path);
    }
    return null;
}

function crop_uploaded_asset($uploadedPath, $targetWidth, $targetHeight, $focusX, $focusY, $zoom)
{
    if (!function_exists('imagecreatetruecolor')) {
        return $uploadedPath;
    }

    $absoluteSource = rtrim(UPLOAD_PATH, '/') . '/' . ltrim($uploadedPath, '/');
    if (!is_file($absoluteSource)) {
        return $uploadedPath;
    }

    $info = @getimagesize($absoluteSource);
    if (!$info) {
        return $uploadedPath;
    }

    $srcWidth = (int) $info[0];
    $srcHeight = (int) $info[1];
    $mime = isset($info['mime']) ? $info['mime'] : '';
    if ($srcWidth <= 0 || $srcHeight <= 0) {
        return $uploadedPath;
    }

    $source = create_image_resource($absoluteSource, $mime);
    if (!$source) {
        return $uploadedPath;
    }

    $targetRatio = $targetWidth / $targetHeight;
    $sourceRatio = $srcWidth / $srcHeight;

    if ($sourceRatio > $targetRatio) {
        $cropHeight = $srcHeight;
        $cropWidth = (int) round($cropHeight * $targetRatio);
    } else {
        $cropWidth = $srcWidth;
        $cropHeight = (int) round($cropWidth / $targetRatio);
    }

    $focusX = max(0, min(100, (int) $focusX));
    $focusY = max(0, min(100, (int) $focusY));
    $zoom = (float) $zoom;
    if ($zoom < 1) {
        $zoom = 1;
    }
    if ($zoom > 3) {
        $zoom = 3;
    }

    $cropWidth = (int) round($cropWidth / $zoom);
    $cropHeight = (int) round($cropHeight / $zoom);
    if ($cropWidth < 1) {
        $cropWidth = 1;
    }
    if ($cropHeight < 1) {
        $cropHeight = 1;
    }
    $centerX = (int) round(($focusX / 100) * $srcWidth);
    $centerY = (int) round(($focusY / 100) * $srcHeight);

    $cropX = $centerX - (int) floor($cropWidth / 2);
    $cropY = $centerY - (int) floor($cropHeight / 2);
    $cropX = max(0, min($srcWidth - $cropWidth, $cropX));
    $cropY = max(0, min($srcHeight - $cropHeight, $cropY));

    $dest = imagecreatetruecolor($targetWidth, $targetHeight);
    imagealphablending($dest, false);
    imagesavealpha($dest, true);
    $transparent = imagecolorallocatealpha($dest, 0, 0, 0, 127);
    imagefilledrectangle($dest, 0, 0, $targetWidth, $targetHeight, $transparent);

    imagecopyresampled($dest, $source, 0, 0, $cropX, $cropY, $targetWidth, $targetHeight, $cropWidth, $cropHeight);

    $dir = dirname($absoluteSource);
    $fileName = 'crop_' . date('YmdHis') . '_' . bin2hex(random_bytes(4)) . '.png';
    $absoluteDest = $dir . '/' . $fileName;
    $relativeDest = trim(dirname($uploadedPath), './') . '/' . $fileName;

    if (@imagepng($dest, $absoluteDest, 7)) {
        @unlink($absoluteSource);
        imagedestroy($source);
        imagedestroy($dest);
        return ltrim($relativeDest, '/');
    }

    imagedestroy($source);
    imagedestroy($dest);
    return $uploadedPath;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['_token'])) {
        redirect('admin/settings.php');
    }

    foreach ($allKeys as $key) {
        save_setting($key, isset($_POST[$key]) ? trim($_POST[$key]) : '');
    }

    if (!empty($_FILES['home_hero_bg']['name'])) {
        $up = upload_file($_FILES['home_hero_bg'], array('jpg', 'jpeg', 'png', 'webp'), MAX_IMAGE_UPLOAD, 'settings');
        if ($up['ok']) {
            save_setting('home_hero_bg', $up['path']);
        }
    }

    if (!empty($_FILES['site_logo']['name'])) {
        $up = upload_file($_FILES['site_logo'], array('jpg', 'jpeg', 'png', 'webp'), MAX_IMAGE_UPLOAD, 'settings');
        if ($up['ok']) {
            $logoFocusX = isset($_POST['site_logo_focus_x']) ? (int) $_POST['site_logo_focus_x'] : 50;
            $logoFocusY = isset($_POST['site_logo_focus_y']) ? (int) $_POST['site_logo_focus_y'] : 50;
            $logoZoom = isset($_POST['site_logo_zoom']) ? (float) $_POST['site_logo_zoom'] : 1;
            $cropped = crop_uploaded_asset($up['path'], 256, 256, $logoFocusX, $logoFocusY, $logoZoom);
            save_setting('site_logo', $cropped);
            save_setting('site_logo_zoom', (string) $logoZoom);
        }
    }

    if (!empty($_FILES['site_favicon']['name'])) {
        $up = upload_file($_FILES['site_favicon'], array('jpg', 'jpeg', 'png', 'webp', 'ico'), MAX_IMAGE_UPLOAD, 'settings');
        if ($up['ok']) {
            $favFocusX = isset($_POST['site_favicon_focus_x']) ? (int) $_POST['site_favicon_focus_x'] : 50;
            $favFocusY = isset($_POST['site_favicon_focus_y']) ? (int) $_POST['site_favicon_focus_y'] : 50;
            $cropped = crop_uploaded_asset($up['path'], 128, 128, $favFocusX, $favFocusY, 1);
            save_setting('site_favicon', $cropped);
        }
    }

    flash('success', 'Settings saved');
    redirect('admin/settings.php');
}

include __DIR__ . '/../templates/admin_header.php';
?>
<div class="panel">
    <?php $okMsg = flash('success'); if ($okMsg): ?><div class="alert alert-success"><?php echo e($okMsg); ?></div><?php endif; ?>
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">

        <ul class="nav nav-tabs mb-3" role="tablist">
            <li class="nav-item" role="presentation"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-branding" type="button" role="tab">Branding</button></li>
            <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-contact" type="button" role="tab">Contact</button></li>
            <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-social" type="button" role="tab">Social</button></li>
            <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-home" type="button" role="tab">Home</button></li>
            <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-seo" type="button" role="tab">SEO</button></li>
            <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-assets" type="button" role="tab">Logo & Favicon</button></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="tab-branding" role="tabpanel">
                <div class="row g-3">
                    <?php foreach ($groups['branding'] as $key): ?>
                        <div class="col-md-6">
                            <label class="form-label"><?php echo e(ucwords(str_replace('_', ' ', $key))); ?></label>
                            <input class="form-control" name="<?php echo e($key); ?>" value="<?php echo e(setting($key, '')); ?>">
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="tab-pane fade" id="tab-contact" role="tabpanel">
                <div class="row g-3">
                    <?php foreach ($groups['contact'] as $key): ?>
                        <?php if ($key === 'contact_map_embed'): ?>
                            <div class="col-12">
                                <label class="form-label">Contact Map Embed (iframe)</label>
                                <textarea class="form-control" rows="4" name="contact_map_embed" placeholder="Paste Google Maps iframe code"><?php echo e(setting('contact_map_embed', '')); ?></textarea>
                                <small class="text-muted d-block mt-1">Example: &lt;iframe src="..." width="600" height="450" style="border:0;" loading="lazy"&gt;&lt;/iframe&gt;</small>
                            </div>
                        <?php else: ?>
                            <div class="col-md-6">
                                <label class="form-label"><?php echo e(ucwords(str_replace('_', ' ', $key))); ?></label>
                                <input class="form-control" name="<?php echo e($key); ?>" value="<?php echo e(setting($key, '')); ?>">
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="tab-pane fade" id="tab-social" role="tabpanel">
                <h6 class="mb-2">Social Handles</h6>
                <div class="row g-3 mb-3">
                    <?php foreach ($groups['social_handles'] as $key): ?>
                        <div class="col-md-6">
                            <label class="form-label"><?php echo e(ucwords(str_replace('_', ' ', $key))); ?></label>
                            <input class="form-control" name="<?php echo e($key); ?>" value="<?php echo e(setting($key, '')); ?>">
                        </div>
                    <?php endforeach; ?>
                </div>
                <h6 class="mb-2">Social URLs</h6>
                <div class="row g-3">
                    <?php foreach ($groups['social_urls'] as $key): ?>
                        <div class="col-md-6">
                            <label class="form-label"><?php echo e(ucwords(str_replace('_', ' ', $key))); ?></label>
                            <input class="form-control" name="<?php echo e($key); ?>" value="<?php echo e(setting($key, '')); ?>">
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="tab-pane fade" id="tab-home" role="tabpanel">
                <div class="row g-3">
                    <?php foreach ($groups['home'] as $key): ?>
                        <div class="col-md-6">
                            <label class="form-label"><?php echo e(ucwords(str_replace('_', ' ', $key))); ?></label>
                            <input class="form-control" name="<?php echo e($key); ?>" value="<?php echo e(setting($key, '')); ?>">
                        </div>
                    <?php endforeach; ?>
                    <div class="col-md-6">
                        <label class="form-label">Hero Background Image</label>
                        <input class="form-control" type="file" name="home_hero_bg" accept=".jpg,.jpeg,.png,.webp">
                        <?php if (setting('home_hero_bg', '') !== ''): ?>
                            <small class="text-muted d-block mt-1">Current: <?php echo e(setting('home_hero_bg', '')); ?></small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="tab-seo" role="tabpanel">
                <div class="row g-3">
                    <?php foreach ($groups['seo'] as $key): ?>
                        <div class="col-md-6">
                            <label class="form-label"><?php echo e(ucwords(str_replace('_', ' ', $key))); ?></label>
                            <input class="form-control" name="<?php echo e($key); ?>" value="<?php echo e(setting($key, '')); ?>">
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="tab-pane fade" id="tab-assets" role="tabpanel">
                <div class="row g-4">
                    <div class="col-lg-6">
                        <h6>Site Logo</h6>
                        <input type="hidden" name="site_logo_focus_x" id="siteLogoFocusX" value="50">
                        <input type="hidden" name="site_logo_focus_y" id="siteLogoFocusY" value="50">
                        <input type="hidden" name="site_logo_zoom" id="siteLogoZoom" value="<?php echo e(setting('site_logo_zoom', '1')); ?>">
                        <div id="siteLogoPreview" style="position:relative;height:180px;max-width:180px;border:1px solid rgba(37,99,235,.2);border-radius:12px;background:#eef2f7 center center / cover no-repeat;cursor:crosshair;overflow:hidden;">
                            <span id="siteLogoDot" style="position:absolute;left:50%;top:50%;width:14px;height:14px;border-radius:50%;border:2px solid #fff;background:rgba(37,99,235,.9);transform:translate(-50%,-50%);"></span>
                        </div>
                        <small class="text-muted d-block mt-1">Click preview to choose crop focal area (output 256x256 square).</small>
                        <div class="mt-2">
                            <label class="form-label mb-1">Zoom</label>
                            <input type="range" class="form-range" min="1" max="3" step="0.1" id="siteLogoZoomRange" value="<?php echo e(setting('site_logo_zoom', '1')); ?>">
                            <small class="text-muted">Zoom Level: <span id="siteLogoZoomLabel"><?php echo e(setting('site_logo_zoom', '1')); ?></span>x</small>
                        </div>
                        <input class="form-control mt-2" type="file" name="site_logo" id="siteLogoInput" accept=".jpg,.jpeg,.png,.webp">
                        <?php if (setting('site_logo', '') !== ''): ?>
                            <small class="text-muted d-block mt-1">Current: <?php echo e(setting('site_logo', '')); ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="col-lg-6">
                        <h6>Site Favicon</h6>
                        <input type="hidden" name="site_favicon_focus_x" id="siteFavFocusX" value="50">
                        <input type="hidden" name="site_favicon_focus_y" id="siteFavFocusY" value="50">
                        <div id="siteFavPreview" style="position:relative;height:140px;border:1px solid rgba(37,99,235,.2);border-radius:12px;background:#eef2f7 center center / cover no-repeat;cursor:crosshair;overflow:hidden;max-width:140px;">
                            <span id="siteFavDot" style="position:absolute;left:50%;top:50%;width:14px;height:14px;border-radius:50%;border:2px solid #fff;background:rgba(245,124,0,.95);transform:translate(-50%,-50%);"></span>
                        </div>
                        <small class="text-muted d-block mt-1">Click preview to choose crop focal area (output 128x128).</small>
                        <input class="form-control mt-2" type="file" name="site_favicon" id="siteFavInput" accept=".jpg,.jpeg,.png,.webp,.ico">
                        <?php if (setting('site_favicon', '') !== ''): ?>
                            <small class="text-muted d-block mt-1">Current: <?php echo e(setting('site_favicon', '')); ?></small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <button class="btn btn-warning mt-4">Save Settings</button>
    </form>
</div>

<script>
(function () {
    function bindFocusPicker(config) {
        var preview = document.getElementById(config.previewId);
        var dot = document.getElementById(config.dotId);
        var xInput = document.getElementById(config.xInputId);
        var yInput = document.getElementById(config.yInputId);
        var fileInput = document.getElementById(config.fileInputId);
        var zoomInput = config.zoomInputId ? document.getElementById(config.zoomInputId) : null;
        var zoomRange = config.zoomRangeId ? document.getElementById(config.zoomRangeId) : null;
        var zoomLabel = config.zoomLabelId ? document.getElementById(config.zoomLabelId) : null;

        if (!preview || !dot || !xInput || !yInput || !fileInput) {
            return;
        }

        function setFocus(x, y) {
            var fx = Math.max(0, Math.min(100, parseInt(x, 10) || 50));
            var fy = Math.max(0, Math.min(100, parseInt(y, 10) || 50));
            xInput.value = String(fx);
            yInput.value = String(fy);
            dot.style.left = fx + '%';
            dot.style.top = fy + '%';
            preview.style.backgroundPosition = fx + '% ' + fy + '%';
        }

        function applyZoom(level) {
            var zoom = Math.max(1, Math.min(3, parseFloat(level) || 1));
            if (zoomInput) {
                zoomInput.value = String(zoom);
            }
            if (zoomRange) {
                zoomRange.value = String(zoom);
            }
            if (zoomLabel) {
                zoomLabel.textContent = zoom.toFixed(1);
            }
            preview.style.backgroundSize = (zoom * 100).toFixed(0) + '%';
        }

        preview.addEventListener('click', function (e) {
            var rect = preview.getBoundingClientRect();
            var x = ((e.clientX - rect.left) / rect.width) * 100;
            var y = ((e.clientY - rect.top) / rect.height) * 100;
            setFocus(x, y);
        });

        fileInput.addEventListener('change', function () {
            if (!this.files || !this.files[0]) {
                return;
            }
            var reader = new FileReader();
            reader.onload = function (ev) {
                preview.style.backgroundImage = 'url(' + ev.target.result + ')';
            };
            reader.readAsDataURL(this.files[0]);
        });

        if (config.currentImageUrl) {
            preview.style.backgroundImage = 'url(' + config.currentImageUrl + ')';
        }
        if (zoomRange) {
            zoomRange.addEventListener('input', function () {
                applyZoom(this.value);
            });
        }
        applyZoom(config.defaultZoom || 1);
        setFocus(50, 50);
    }

    bindFocusPicker({
        previewId: 'siteLogoPreview',
        dotId: 'siteLogoDot',
        xInputId: 'siteLogoFocusX',
        yInputId: 'siteLogoFocusY',
        fileInputId: 'siteLogoInput',
        currentImageUrl: '<?php echo e(setting('site_logo', '') ? media_url(setting('site_logo', '')) : ''); ?>',
        zoomInputId: 'siteLogoZoom',
        zoomRangeId: 'siteLogoZoomRange',
        zoomLabelId: 'siteLogoZoomLabel',
        defaultZoom: '<?php echo e(setting('site_logo_zoom', '1')); ?>'
    });

    bindFocusPicker({
        previewId: 'siteFavPreview',
        dotId: 'siteFavDot',
        xInputId: 'siteFavFocusX',
        yInputId: 'siteFavFocusY',
        fileInputId: 'siteFavInput',
        currentImageUrl: '<?php echo e(setting('site_favicon', '') ? media_url(setting('site_favicon', '')) : ''); ?>'
    });
})();
</script>

<?php include __DIR__ . '/../templates/admin_footer.php'; ?>
