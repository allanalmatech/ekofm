<?php
require_once __DIR__ . '/_init.php';
require_login();
require_permission('radio.manage');

$adminTitle = 'Radio Settings';
$activeMenu = 'radio';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['_token'])) {
        redirect('admin/radio.php');
    }

    $streamUrl = trim($_POST['radio_stream_url']);
    $streamTitle = trim($_POST['radio_stream_title']);
    $embedScript = trim($_POST['radio_embed_script']);

    if (stripos($embedScript, '<script') !== false) {
        if (preg_match('/src\s*=\s*["\']([^"\']+)["\']/i', $embedScript, $m)) {
            $embedScript = trim($m[1]);
        }
    }

    $enabled = isset($_POST['radio_player_enabled']) ? '1' : '0';

    save_setting('radio_stream_url', $streamUrl);
    save_setting('radio_stream_title', $streamTitle);
    save_setting('radio_embed_script', $embedScript);
    save_setting('radio_player_enabled', $enabled);

    db_query(
        'INSERT INTO radio_settings (stream_url, stream_title, player_enabled, updated_by, updated_at) VALUES (?, ?, ?, ?, NOW())',
        array($streamUrl, $streamTitle, $enabled === '1' ? 1 : 0, current_user()['id'])
    );

    flash('success', 'Settings updated.');
    redirect('admin/radio.php');
}

include __DIR__ . '/../templates/admin_header.php';
$okMsg = flash('success');
?>

<div class="panel">
    <h5>Shoutcast Configuration</h5>
    <?php if ($okMsg): ?><div class="alert alert-success"><?php echo e($okMsg); ?></div><?php endif; ?>

    <form method="post">
        <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">

        <div class="mb-3">
            <label class="form-label">Stream URL</label>
            <input class="form-control" name="radio_stream_url" value="<?php echo e(setting('radio_stream_url', 'https://5.39.82.219/22094/listen.mp3')); ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Stream Title</label>
            <input class="form-control" name="radio_stream_title" value="<?php echo e(setting('radio_stream_title', 'Now Playing: EKO FM Live')); ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Embed Script URL (optional)</label>
            <input class="form-control" name="radio_embed_script" value="<?php echo e(setting('radio_embed_script', '//myradiostream.com/embed/mayugefmuganda')); ?>">
            <small class="text-muted">Use only the script src value, for example: //myradiostream.com/embed/mayugefmuganda</small>
        </div>

        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="radio_player_enabled" id="radio_player_enabled" <?php echo setting('radio_player_enabled', '1') === '1' ? 'checked' : ''; ?>>
            <label class="form-check-label" for="radio_player_enabled">Enable player site-wide</label>
        </div>

        <button class="btn btn-warning">Save Changes</button>
    </form>
</div>

<?php include __DIR__ . '/../templates/admin_footer.php'; ?>
