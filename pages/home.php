<?php
$metaTitle = setting('home_meta_title', 'EKO FM | The Heartbeat of Karamoja');
$metaDescription = setting('home_meta_description', 'On-Air. Online. On-Ground. For Peace & Development.');

$heroTitle = setting('home_hero_title', 'EKO FM');
$heroSub = setting('home_hero_subtitle', 'The Heartbeat of Karamoja');
$heroLine = setting('home_hero_line', 'On-Air. Online. On-Ground.');
$heroCopy = setting('home_hero_copy', 'We are a community-driven radio station delivering music, real conversations, and life-changing information that informs, inspires, and empowers.');
$heroBg = media_url(setting('home_hero_bg', ''));
$heroSlidesRaw = json_decode(setting('home_hero_slides', '[]'), true);
$heroSlides = array();

if (is_array($heroSlidesRaw)) {
    foreach ($heroSlidesRaw as $slide) {
        if (!is_array($slide)) {
            continue;
        }
        $primaryText = isset($slide['button_primary_text']) ? $slide['button_primary_text'] : (isset($slide['cta_text']) ? $slide['cta_text'] : '');
        $primaryLink = isset($slide['button_primary_link']) ? $slide['button_primary_link'] : (isset($slide['cta_link']) ? $slide['cta_link'] : '');
        $secondaryText = isset($slide['button_secondary_text']) ? $slide['button_secondary_text'] : '';
        $secondaryLink = isset($slide['button_secondary_link']) ? $slide['button_secondary_link'] : '';
        $heroSlides[] = array(
            'title' => isset($slide['title']) && $slide['title'] !== '' ? $slide['title'] : $heroTitle,
            'subtitle' => isset($slide['subtitle']) && $slide['subtitle'] !== '' ? $slide['subtitle'] : $heroSub,
            'line' => isset($slide['line']) && $slide['line'] !== '' ? $slide['line'] : $heroLine,
            'copy' => isset($slide['copy']) && $slide['copy'] !== '' ? $slide['copy'] : $heroCopy,
            'button_primary_text' => trim($primaryText),
            'button_primary_link' => trim($primaryLink),
            'button_secondary_text' => trim($secondaryText),
            'button_secondary_link' => trim($secondaryLink),
            'badge' => isset($slide['badge']) ? $slide['badge'] : 'Music. Culture. Community. Impact.',
            'caption' => isset($slide['caption']) ? $slide['caption'] : 'On-Air. Online. On-Ground.',
            'foot' => isset($slide['foot']) ? $slide['foot'] : 'For Peace & Development',
            'card_opacity' => isset($slide['card_opacity']) ? max(0, min(95, (int) $slide['card_opacity'])) : 64,
            'card_position' => (isset($slide['card_position']) && in_array($slide['card_position'], array('left', 'center', 'right'), true)) ? $slide['card_position'] : 'left',
            'image_focus_x' => isset($slide['image_focus_x']) ? max(0, min(100, (int) $slide['image_focus_x'])) : 50,
            'image_focus_y' => isset($slide['image_focus_y']) ? max(0, min(100, (int) $slide['image_focus_y'])) : 50,
            'image_zoom' => isset($slide['image_zoom']) ? max(1, min(2.5, (float) $slide['image_zoom'])) : 1,
            'image' => isset($slide['image']) && $slide['image'] !== '' ? media_url($slide['image']) : $heroBg,
        );
    }
}

if (count($heroSlides) === 0) {
    $heroSlides[] = array(
        'title' => $heroTitle,
        'subtitle' => $heroSub,
        'line' => $heroLine,
        'copy' => $heroCopy,
        'button_primary_text' => setting('home_hero_cta_text', 'Listen Live'),
        'button_primary_link' => setting('home_hero_cta_link', '/listen-live'),
        'button_secondary_text' => '',
        'button_secondary_link' => '',
        'badge' => 'Music. Culture. Community. Impact.',
        'caption' => 'On-Air. Online. On-Ground.',
        'foot' => 'For Peace & Development',
        'card_opacity' => 64,
        'card_position' => 'left',
        'image_focus_x' => 50,
        'image_focus_y' => 50,
        'image_zoom' => 1,
        'image' => $heroBg,
    );
}

$latestNews = news_latest(3);
$shows = featured_shows(6);
$services = services_list(true);
?>

<main class="container-xxl mt-3">
    <section class="hero hero-slider reveal story-section" data-hero-slider>
        <div class="hero-slides">
            <?php foreach ($heroSlides as $index => $slide): ?>
                <?php
                    $buttons = array();

                    if (!empty($slide['button_primary_text']) && !empty($slide['button_primary_link'])) {
                        $primaryLink = $slide['button_primary_link'];
                        $primaryIsExternal = strpos($primaryLink, 'http://') === 0 || strpos($primaryLink, 'https://') === 0;
                        $buttons[] = array(
                            'text' => $slide['button_primary_text'],
                            'href' => $primaryIsExternal ? $primaryLink : url(ltrim($primaryLink, '/')),
                            'external' => $primaryIsExternal,
                            'class' => 'btn btn-live glow-button',
                        );
                    }

                    if (!empty($slide['button_secondary_text']) && !empty($slide['button_secondary_link'])) {
                        $secondaryLink = $slide['button_secondary_link'];
                        $secondaryIsExternal = strpos($secondaryLink, 'http://') === 0 || strpos($secondaryLink, 'https://') === 0;
                        $buttons[] = array(
                            'text' => $slide['button_secondary_text'],
                            'href' => $secondaryIsExternal ? $secondaryLink : url(ltrim($secondaryLink, '/')),
                            'external' => $secondaryIsExternal,
                            'class' => 'btn btn-glass',
                        );
                    }
                ?>
                <article class="hero-slide hero-card-pos-<?php echo e($slide['card_position']); ?> <?php echo $index === 0 ? 'is-active' : ''; ?>">
                    <div class="hero-slide-bg" style="background-image:url('<?php echo e($slide['image']); ?>');background-position:<?php echo e((int) $slide['image_focus_x']); ?>% <?php echo e((int) $slide['image_focus_y']); ?>%;--hero-bg-scale:<?php echo e(number_format((float) $slide['image_zoom'], 2, '.', '')); ?>;"></div>
                    <div class="hero-content hero-slide-card reveal reveal-delay-1" style="--hero-card-alpha: <?php echo e(number_format(((int) $slide['card_opacity']) / 100, 2, '.', '')); ?>;">
                        <?php if (trim($slide['badge']) !== ''): ?>
                            <span class="hero-badge"><span class="live-wave"><span></span><span></span><span></span></span> <?php echo e($slide['badge']); ?></span>
                        <?php endif; ?>
                        <h1 class="display-3 fw-bold mb-1"><?php echo e($slide['title']); ?></h1>
                        <h2 class="h2 mb-3"><?php echo e($slide['subtitle']); ?></h2>
                        <?php if (trim($slide['line']) !== ''): ?><p class="fw-semibold mb-2"><?php echo e($slide['line']); ?></p><?php endif; ?>
                        <?php if (trim($slide['copy']) !== ''): ?><p class="lead"><?php echo e($slide['copy']); ?></p><?php endif; ?>

                        <?php if (count($buttons) > 0): ?>
                            <div class="d-flex flex-wrap gap-2">
                                <?php foreach ($buttons as $button): ?>
                                    <a
                                        href="<?php echo e($button['href']); ?>"
                                        <?php if (!$button['external']): ?>data-pjax<?php endif; ?>
                                        <?php if ($button['external']): ?>target="_blank" rel="noopener"<?php endif; ?>
                                        class="<?php echo e($button['class']); ?>"
                                    >
                                        <?php echo e($button['text']); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <?php if (trim($slide['caption']) !== ''): ?><p class="hero-caption mt-3 mb-0"><?php echo e($slide['caption']); ?></p><?php endif; ?>
                        <?php if (trim($slide['foot']) !== ''): ?><p class="hero-foot mt-3 mb-0"><?php echo e($slide['foot']); ?></p><?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <?php if (count($heroSlides) > 1): ?>
            <div class="hero-slider-controls" aria-label="Hero controls">
                <button type="button" class="hero-slider-btn" data-hero-prev aria-label="Previous slide">
                    <span class="material-symbols-outlined">arrow_back</span>
                </button>
                <div class="hero-slider-dots" role="tablist" aria-label="Hero slides">
                    <?php foreach ($heroSlides as $index => $slide): ?>
                        <button type="button" class="hero-slider-dot <?php echo $index === 0 ? 'is-active' : ''; ?>" data-hero-dot="<?php echo e($index); ?>" aria-label="Go to slide <?php echo e($index + 1); ?>"></button>
                    <?php endforeach; ?>
                </div>
                <button type="button" class="hero-slider-btn" data-hero-next aria-label="Next slide">
                    <span class="material-symbols-outlined">arrow_forward</span>
                </button>
            </div>
        <?php endif; ?>
    </section>

    <section class="section-space story-section">
        <div class="row g-4 align-items-stretch">
            <div class="col-lg-7 reveal">
                <div class="section-card floating-card h-100">
                    <h3 class="section-title">About EKO FM</h3>
                    <p class="mb-0 text-muted">EKO FM is a dynamic radio station based in Karamoja, Uganda, committed to promoting peace, development, and community transformation through the power of media.</p>
                </div>
            </div>
            <div class="col-lg-5 reveal reveal-delay-1">
                <div class="section-card mini-cta floating-card h-100 d-flex flex-column justify-content-between">
                    <div>
                        <h4 class="mb-2">Work With Us</h4>
                        <p class="mb-3 text-muted">Reach thousands across Karamoja through radio, digital, and on-ground activation.</p>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="<?php echo e(url('advertise-partner')); ?>" data-pjax class="btn btn-live align-self-start">Advertise With EKO FM</a>
                        <a href="mailto:partnerships@ekoradio.fm?subject=<?php echo rawurlencode('Sponsorship Media Kit Request'); ?>" class="btn btn-glass align-self-start">Download Sponsorship Kit</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section-space story-section">
        <div class="d-flex justify-content-between align-items-end mb-3 reveal">
            <h3 class="section-title mb-0">Featured Shows</h3>
            <a href="<?php echo e(url('shows')); ?>" data-pjax>View all shows</a>
        </div>
        <div class="row g-4">
            <?php foreach ($shows as $index => $show): ?>
                <?php $showFocusX = (int) (isset($show['cover_focus_x']) ? $show['cover_focus_x'] : 50); ?>
                <?php $showFocusY = (int) (isset($show['cover_focus_y']) ? $show['cover_focus_y'] : 50); ?>
                <?php $briefDescription = trim((string) (isset($show['brief_description']) && $show['brief_description'] !== '' ? $show['brief_description'] : $show['description'])); ?>
                <div class="col-md-6 col-lg-4 reveal reveal-delay-<?php echo e(($index % 3) + 1); ?>">
                    <article class="section-card show-card floating-card position-relative">
                        <div class="show-cover" style="background-image:url('<?php echo e(media_url($show['cover_image'])); ?>');background-position:<?php echo e($showFocusX); ?>% <?php echo e($showFocusY); ?>%;"></div>
                        <h5 class="mt-3"><?php echo e($show['title']); ?></h5>
                        <p class="show-time mb-1"><?php echo e($show['day_of_week']); ?> | <?php echo e(substr($show['start_time'], 0, 5)); ?>-<?php echo e(substr($show['end_time'], 0, 5)); ?></p>
                        <?php if (!empty($show['tones'])): ?>
                            <div class="d-flex flex-wrap gap-2 mb-2">
                                <?php foreach ($show['tones'] as $tone): ?>
                                    <span class="tone-badge"><?php echo e($tone); ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        <p class="text-muted mb-0"><?php echo e($briefDescription); ?></p>
                        <a href="<?php echo e(url('shows/' . $show['slug'])); ?>" data-pjax class="stretched-link" aria-label="View details for <?php echo e($show['title']); ?>"></a>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="section-space story-section">
        <div class="d-flex justify-content-between align-items-end mb-3 reveal">
            <h3 class="section-title mb-0">Latest Content</h3>
            <a href="<?php echo e(url('news')); ?>" data-pjax>All news</a>
        </div>
        <div class="row g-4">
            <?php foreach ($latestNews as $index => $post): ?>
                <div class="col-md-4 reveal reveal-delay-<?php echo e(($index % 3) + 1); ?>">
                    <article class="section-card news-card floating-card h-100">
                        <div class="media-cover mb-3 card-image-zoom" style="background-image:url('<?php echo e(media_url($post['featured_image'])); ?>')"></div>
                        <small class="text-muted"><?php echo e(format_date($post['publish_date'])); ?></small>
                        <h5 class="mt-1"><a href="<?php echo e(url('news/' . $post['slug'])); ?>" data-pjax><?php echo e($post['title']); ?></a></h5>
                        <p class="text-muted mb-0"><?php echo e($post['summary']); ?></p>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="section-space partner-strip reveal story-section">
        <div class="section-card">
            <h4 class="mb-2">Trusted by NGOs, Health Organizations, Businesses and Government Agencies</h4>
            <p class="mb-0 text-muted">Partner with EKO FM for public awareness campaigns, product promotion, and community engagement on-ground.</p>
        </div>
    </section>

    <section class="section-space story-section">
        <h3 class="section-title reveal">What We Do</h3>
        <div class="row g-4">
            <?php foreach ($services as $index => $svc): ?>
                <div class="col-md-6 col-lg-3 reveal reveal-delay-<?php echo e(($index % 3) + 1); ?>">
                    <div class="section-card service-card floating-card h-100">
                        <h6 class="mb-2"><?php echo e($svc['title']); ?></h6>
                        <p class="mb-0 text-muted"><?php echo e($svc['description']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</main>
