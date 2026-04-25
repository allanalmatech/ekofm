<?php
$metaTitle = 'Live & Community Activations - EKO FM';
$metaDescription = 'From Radio to Real Life. Explore EKO FM community impact and live experiences.';

$activationsPage = get_page_by_slug('activations');
$sections = $activationsPage ? get_page_sections((int) $activationsPage['id']) : array();
$sectionMap = array();

foreach ($sections as $section) {
    $sectionMap[$section['section_key']] = $section;
}

$hero = isset($sectionMap['hero']) ? $sectionMap['hero'] : null;
$intro = isset($sectionMap['intro']) ? $sectionMap['intro'] : null;
$sponsorCta = isset($sectionMap['sponsor_cta']) ? $sectionMap['sponsor_cta'] : null;
$footerCta = isset($sectionMap['footer_cta']) ? $sectionMap['footer_cta'] : null;

$communityItems = array();
$liveItems = array();
$galleryItems = array();

foreach ($sections as $section) {
    if ((int) $section['is_visible'] !== 1) {
        continue;
    }
    if (strpos($section['section_key'], 'community_') === 0) {
        $communityItems[] = $section;
    }
    if (strpos($section['section_key'], 'live_') === 0) {
        $liveItems[] = $section;
    }
    if (strpos($section['section_key'], 'gallery_') === 0 && !empty($section['image_path'])) {
        $galleryItems[] = $section;
    }
}

$cardGalleryMap = array();
$cardIds = array();
foreach ($communityItems as $item) {
    $cardIds[(int) $item['id']] = true;
}
foreach ($liveItems as $item) {
    $cardIds[(int) $item['id']] = true;
}

if ($cardIds) {
    $galleryRows = db_query('SELECT file_path, category_name FROM media WHERE file_type = ? AND category_name LIKE ?', array('image', 'page-section-%'))->fetchAll();
    foreach ($galleryRows as $row) {
        if (!preg_match('/^page-section-(\d+)$/', (string) $row['category_name'], $m)) {
            continue;
        }
        $sectionId = (int) $m[1];
        if (!isset($cardIds[$sectionId])) {
            continue;
        }
        if (!isset($cardGalleryMap[$sectionId])) {
            $cardGalleryMap[$sectionId] = array();
        }
        $cardGalleryMap[$sectionId][] = (string) $row['file_path'];
    }
}

$mediaKitEmail = 'partnerships@ekoradio.fm';
$mediaKitLink = 'mailto:' . $mediaKitEmail . '?subject=' . rawurlencode('Sponsorship Media Kit Request');
?>

<main class="container py-4">
    <?php
    $heroImagePath = '';
    if ($hero && !empty($hero['image_path'])) {
        $heroImagePath = $hero['image_path'];
    } elseif ($activationsPage && !empty($activationsPage['social_image'])) {
        $heroImagePath = $activationsPage['social_image'];
    }
    $heroImage = $heroImagePath !== '' ? media_url($heroImagePath) : media_url('');
    ?>
    <section class="mb-4 reveal story-section activations-hero" style="--hero-image:url('<?php echo e($heroImage); ?>');">
        <div class="activations-hero-content">
            <p class="activations-kicker">From Radio to Real Life</p>
            <h1 class="mb-3 text-white"><?php echo e($hero && $hero['title'] !== '' ? $hero['title'] : 'Live & Community Activations'); ?></h1>
            <p class="mb-0 activations-hero-copy">
                <?php echo e($hero && $hero['content'] !== '' ? $hero['content'] : 'EKO FM goes beyond the airwaves to connect with communities in real life through activations that create impact and shared experiences.'); ?>
            </p>
            <div class="d-flex flex-wrap gap-2 mt-4">
                <a class="btn activations-btn-primary" href="<?php echo e($mediaKitLink); ?>">
                    <span class="material-symbols-outlined">download</span>
                    <?php echo e($hero && $hero['cta_text'] !== '' ? $hero['cta_text'] : 'Download Sponsorship Kit'); ?>
                </a>
                <a class="btn activations-btn-secondary" href="<?php echo e(url('contact')); ?>" data-pjax>
                    <span class="material-symbols-outlined">call</span>
                    Contact Us
                </a>
            </div>
        </div>
    </section>

    <section class="mb-4 reveal reveal-delay-1 story-section page-intro-glass">
        <p class="mb-0 text-muted"><?php echo e($intro && $intro['content'] !== '' ? $intro['content'] : ($activationsPage ? $activationsPage['content'] : 'Explore how EKO FM brings brands, listeners, and communities together through meaningful experiences.')); ?></p>
    </section>

    <?php if ($galleryItems): ?>
        <section class="section-card floating-card mb-4 reveal reveal-delay-1 story-section">
            <h3 class="section-title mb-3">Activation Gallery</h3>
            <?php if (count($galleryItems) === 1): ?>
                <div class="activations-gallery-single" style="background-image:url('<?php echo e(media_url($galleryItems[0]['image_path'])); ?>');"></div>
            <?php else: ?>
                <div id="activationsGalleryCarousel" class="carousel slide activations-gallery-carousel" data-bs-ride="carousel">
                    <div class="carousel-indicators">
                        <?php foreach ($galleryItems as $index => $item): ?>
                            <button type="button" data-bs-target="#activationsGalleryCarousel" data-bs-slide-to="<?php echo e($index); ?>" class="<?php echo $index === 0 ? 'active' : ''; ?>" aria-current="<?php echo $index === 0 ? 'true' : 'false'; ?>" aria-label="Slide <?php echo e($index + 1); ?>"></button>
                        <?php endforeach; ?>
                    </div>
                    <div class="carousel-inner rounded-4">
                        <?php foreach ($galleryItems as $index => $item): ?>
                            <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                <div class="activations-gallery-slide" style="background-image:url('<?php echo e(media_url($item['image_path'])); ?>');"></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#activationsGalleryCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#activationsGalleryCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            <?php endif; ?>
        </section>
    <?php endif; ?>

    <section class="section-space story-section">
        <div class="row g-4">
            <div class="col-lg-6 reveal">
                <h3 class="section-title">Community Impact</h3>
                <div class="d-grid gap-3">
                    <?php foreach ($communityItems as $item): ?>
                        <?php
                        $itemImages = array();
                        if (!empty($item['image_path'])) {
                            $itemImages[] = $item['image_path'];
                        }
                        if (isset($cardGalleryMap[(int) $item['id']])) {
                            foreach ($cardGalleryMap[(int) $item['id']] as $extraImage) {
                                if (!in_array($extraImage, $itemImages, true)) {
                                    $itemImages[] = $extraImage;
                                }
                            }
                        }
                        $carouselId = 'communityCardCarousel' . (int) $item['id'];
                        ?>
                        <article class="section-card floating-card activation-card h-100">
                            <?php if (count($itemImages) > 1): ?>
                                <div id="<?php echo e($carouselId); ?>" class="carousel slide activation-card-carousel mb-3" data-bs-ride="carousel">
                                    <div class="carousel-inner rounded-3">
                                        <?php foreach ($itemImages as $index => $imgPath): ?>
                                            <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                                <div class="activation-image-slot" style="background-image:url('<?php echo e(media_url($imgPath)); ?>');"></div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <button class="carousel-control-prev" type="button" data-bs-target="#<?php echo e($carouselId); ?>" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Previous</span>
                                    </button>
                                    <button class="carousel-control-next" type="button" data-bs-target="#<?php echo e($carouselId); ?>" data-bs-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Next</span>
                                    </button>
                                </div>
                            <?php elseif (count($itemImages) === 1): ?>
                                <div class="activation-image-slot mb-3" style="background-image:url('<?php echo e(media_url($itemImages[0])); ?>');"></div>
                            <?php endif; ?>
                            <h5 class="mb-2"><?php echo e($item['title']); ?></h5>
                            <p class="mb-0 text-muted"><?php echo e($item['content']); ?></p>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="col-lg-6 reveal reveal-delay-1">
                <h3 class="section-title">Live Experiences</h3>
                <div class="d-grid gap-3">
                    <?php foreach ($liveItems as $item): ?>
                        <?php
                        $itemImages = array();
                        if (!empty($item['image_path'])) {
                            $itemImages[] = $item['image_path'];
                        }
                        if (isset($cardGalleryMap[(int) $item['id']])) {
                            foreach ($cardGalleryMap[(int) $item['id']] as $extraImage) {
                                if (!in_array($extraImage, $itemImages, true)) {
                                    $itemImages[] = $extraImage;
                                }
                            }
                        }
                        $carouselId = 'liveCardCarousel' . (int) $item['id'];
                        ?>
                        <article class="section-card floating-card activation-card h-100">
                            <?php if (count($itemImages) > 1): ?>
                                <div id="<?php echo e($carouselId); ?>" class="carousel slide activation-card-carousel mb-3" data-bs-ride="carousel">
                                    <div class="carousel-inner rounded-3">
                                        <?php foreach ($itemImages as $index => $imgPath): ?>
                                            <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                                <div class="activation-image-slot" style="background-image:url('<?php echo e(media_url($imgPath)); ?>');"></div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <button class="carousel-control-prev" type="button" data-bs-target="#<?php echo e($carouselId); ?>" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Previous</span>
                                    </button>
                                    <button class="carousel-control-next" type="button" data-bs-target="#<?php echo e($carouselId); ?>" data-bs-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Next</span>
                                    </button>
                                </div>
                            <?php elseif (count($itemImages) === 1): ?>
                                <div class="activation-image-slot mb-3" style="background-image:url('<?php echo e(media_url($itemImages[0])); ?>');"></div>
                            <?php endif; ?>
                            <h5 class="mb-2"><?php echo e($item['title']); ?></h5>
                            <p class="mb-0 text-muted"><?php echo e($item['content']); ?></p>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <section class="section-card floating-card mb-4 reveal reveal-delay-1 story-section activation-cta-block">
        <h3 class="mb-2"><?php echo e($sponsorCta && $sponsorCta['title'] !== '' ? $sponsorCta['title'] : 'Partner With Us'); ?></h3>
        <p class="mb-3 text-muted"><?php echo e($sponsorCta && $sponsorCta['content'] !== '' ? $sponsorCta['content'] : 'EKO FM offers flexible sponsorship opportunities across all activations.'); ?></p>
        <a class="btn btn-live" href="<?php echo e($mediaKitLink); ?>"><?php echo e($sponsorCta && $sponsorCta['cta_text'] !== '' ? $sponsorCta['cta_text'] : 'Download Media Kit'); ?></a>
    </section>

    <section class="section-card floating-card reveal reveal-delay-2 story-section">
        <h5 class="mb-2"><?php echo e($footerCta && $footerCta['title'] !== '' ? $footerCta['title'] : 'Want to partner with us? Contact us today.'); ?></h5>
        <p class="text-muted mb-3">For now, request the sponsorship media kit directly by email.</p>
        <a class="btn btn-outline-primary" href="<?php echo e($mediaKitLink); ?>">Email <?php echo e($mediaKitEmail); ?></a>
    </section>
</main>
