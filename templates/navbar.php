<?php
$currentRoute = isset($currentRoute) ? $currentRoute : 'home';
$navLinks = array(
    'home' => 'Home',
    'listen-live' => 'Listen Live',
    'shows' => 'Shows',
    'dramas' => 'Dramas',
    'news' => 'News',
    'about' => 'About',
    'advertise-partner' => 'Advertise',
    'contact' => 'Contact'
);
$socialLinks = array(
    array('url' => setting('social_facebook_url', 'https://www.facebook.com/share/1CK8U1M63U/'), 'icon' => 'public', 'label' => 'Facebook'),
    array('url' => setting('social_x_url', 'https://x.com/ekofmkotido'), 'icon' => 'alternate_email', 'label' => 'X'),
    array('url' => setting('social_instagram_url', 'https://www.instagram.com/ekofmlive?utm_source=qr&igsh=MXY3YnJ5ZGxlNGFkcQ=='), 'icon' => 'photo_camera', 'label' => 'Instagram'),
    array('url' => setting('social_tiktok_url', 'https://www.tiktok.com/@91.2.eko.fm?_r=1&_t=ZS-94z2dBOly7A'), 'icon' => 'music_note', 'label' => 'TikTok'),
    array('url' => setting('social_youtube_url', 'https://youtube.com/@ekofm-x2n1l?si=p2Z3IpjNiSMWvnBq'), 'icon' => 'smart_display', 'label' => 'YouTube'),
    array('url' => whatsapp_link(setting('contact_whatsapp', '0791996450')), 'icon' => 'chat', 'label' => 'WhatsApp')
);
$siteLogo = setting('site_logo', '');
?>
<header class="site-header">
    <nav class="navbar nav-shell py-3">
        <div class="container-xxl">
            <div class="header-menu-content nav-wrap">
                <div class="nav-left">
                    <a class="navbar-brand brand-mark" href="<?php echo e(url('/')); ?>" data-pjax>
                        <?php if ($siteLogo !== ''): ?>
                            <img src="<?php echo e(media_url($siteLogo)); ?>" alt="EKO FM" class="brand-logo">
                        <?php else: ?>
                            <span class="material-symbols-outlined">radio</span>
                        <?php endif; ?>
                        <span>
                            <strong>EKO FM</strong>
                            <small class="d-block text-muted brand-sub">The Heartbeat of Karamoja</small>
                        </span>
                    </a>
                </div>

                <div class="desktop-nav" aria-label="Primary navigation">
                    <ul class="navbar-nav nav-primary">
                        <?php foreach ($navLinks as $slug => $label): ?>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $currentRoute === $slug ? 'active' : ''; ?>" href="<?php echo e($slug === 'home' ? url('/') : url($slug)); ?>" data-pjax><?php echo e($label); ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="nav-right">
                    <div class="nav-action-group">
                        <div class="follow-menu" data-follow-menu>
                            <button class="follow-toggle" type="button" aria-expanded="false" aria-controls="followMenuPanel">
                                <span class="material-symbols-outlined">groups</span>
                                Follow Us
                            </button>
                            <div class="follow-popover" id="followMenuPanel" role="menu">
                                <?php foreach ($socialLinks as $social): ?>
                                    <a href="<?php echo e($social['url']); ?>" target="_blank" rel="noopener" role="menuitem">
                                        <span class="material-symbols-outlined"><?php echo e($social['icon']); ?></span>
                                        <?php echo e($social['label']); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <a href="<?php echo e(url('listen-live')); ?>" data-pjax class="btn btn-live glow-button nav-cta">Listen Live</a>
                    </div>
                </div>

                <button class="navbar-toggler menu-toggle" id="menuToggle" type="button" aria-controls="mainNavPanel" aria-expanded="false" aria-label="Open menu">
                    <span class="material-symbols-outlined">menu</span>
                </button>
            </div>
        </div>
    </nav>

    <div class="mobile-menu-panel" id="mainNavPanel" hidden>
        <button class="mobile-menu-backdrop" type="button" data-menu-close aria-label="Close menu"></button>
        <div class="mobile-menu-sheet" role="dialog" aria-modal="true" aria-label="Mobile navigation">
            <div class="mobile-menu-head">
                <span class="mobile-menu-title">Main Menu</span>
                <button type="button" class="mobile-close" data-menu-close aria-label="Close menu">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <ul class="mobile-nav-list">
                <?php foreach ($navLinks as $slug => $label): ?>
                    <li class="mobile-nav-item">
                        <a class="mobile-nav-link <?php echo $currentRoute === $slug ? 'active' : ''; ?>" href="<?php echo e($slug === 'home' ? url('/') : url($slug)); ?>" data-pjax>
                            <span><?php echo e($label); ?></span>
                            <span class="material-symbols-outlined">arrow_forward</span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>

            <a href="<?php echo e(url('listen-live')); ?>" data-pjax class="btn btn-live glow-button mobile-listen-btn">Listen Live</a>

            <div class="mobile-social-grid">
                <?php foreach ($socialLinks as $social): ?>
                    <a href="<?php echo e($social['url']); ?>" target="_blank" rel="noopener">
                        <span class="material-symbols-outlined"><?php echo e($social['icon']); ?></span>
                        <?php echo e($social['label']); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="scroll-progress" aria-hidden="true"><span></span></div>
</header>
