<?php
$metaTitle = 'Dramas - ' . APP_NAME;
$items = dramas_list(true);

function drama_audio_mime($src)
{
    $path = parse_url((string) $src, PHP_URL_PATH);
    $ext = strtolower(pathinfo((string) $path, PATHINFO_EXTENSION));
    if ($ext === 'mp3') {
        return 'audio/mpeg';
    }
    if ($ext === 'wav') {
        return 'audio/wav';
    }
    if ($ext === 'ogg') {
        return 'audio/ogg';
    }
    if ($ext === 'm4a') {
        return 'audio/mp4';
    }
    return '';
}
?>
<main class="container-xxl py-4 story-section">
    <div class="mb-4 reveal">
        <h1 class="mb-1">Original Dramas</h1>
        <p class="text-muted mb-0">Immersive stories that educate, entertain and empower listeners.</p>
    </div>

    <div class="row g-4">
        <?php if (!$items): ?>
            <div class="col-12 reveal reveal-delay-1">
                <div class="section-card text-center p-4">
                    <h5 class="mb-1">No drama episodes yet</h5>
                    <p class="text-muted mb-0">New storytelling episodes will appear here once published.</p>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($items as $index => $d): ?>
                <div class="col-sm-6 col-lg-4 reveal reveal-delay-<?php echo e(($index % 3) + 1); ?>">
                    <div class="section-card drama-card floating-card h-100">
                        <div class="media-cover mb-3" style="background-image:url('<?php echo e(media_url($d['cover_image'])); ?>')"></div>
                        <h5><?php echo e($d['title']); ?></h5>
                        <p class="text-muted"><?php echo e($d['short_description']); ?></p>
                        <?php if (!empty($d['audio_url']) || !empty($d['audio_file'])): ?>
                            <?php $audioSrc = $d['audio_url'] ? $d['audio_url'] : media_url($d['audio_file']); ?>
                            <?php $audioMime = drama_audio_mime($audioSrc); ?>
                            <audio controls class="w-100 mt-auto">
                                <source src="<?php echo e($audioSrc); ?>"<?php echo $audioMime !== '' ? ' type="' . e($audioMime) . '"' : ''; ?>>
                                <a href="<?php echo e($audioSrc); ?>" target="_blank" rel="noopener">Open audio</a>
                            </audio>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>
