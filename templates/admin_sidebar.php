<?php
$menu = array(
    'dashboard' => array('Dashboard', 'dashboard.php', 'dashboard', 'news.view'),
    'news' => array('News', 'news.php', 'edit_note', 'news.view'),
    'programs' => array('Shows', 'programs.php', 'podcasts', 'programs.manage'),
    'dramas' => array('Dramas', 'dramas.php', 'movie_filter', 'dramas.manage'),
    'services' => array('Services', 'services.php', 'widgets', 'services.manage'),
    'rate-card' => array('Rate Card', 'ratecard.php', 'payments', 'ratecard.manage'),
    'pages' => array('Pages/Sections', 'pages.php', 'web', 'pages.manage'),
    'page-sections' => array('Page Section Blocks', 'page_sections.php', 'view_agenda', 'pages.manage'),
    'hero-slides' => array('Hero Slider', 'hero_slides.php', 'slideshow', 'settings.manage'),
    'radio' => array('Radio Settings', 'radio.php', 'tune', 'radio.manage'),
    'settings' => array('Site Settings', 'settings.php', 'settings', 'settings.manage'),
    'media' => array('Media Library', 'media.php', 'folder', 'media.manage'),
    'users' => array('Users', 'users.php', 'group', 'users.manage'),
    'roles' => array('Roles', 'roles.php', 'admin_panel_settings', 'roles.manage'),
    'contacts' => array('Contacts', 'contacts.php', 'mail', 'contact.manage'),
    'activity' => array('Activity Logs', 'activity.php', 'history', 'settings.manage')
);
?>
<aside class="admin-sidebar">
    <div class="admin-brand mb-3">Eko FM</div>
    <nav class="nav flex-column">
        <?php foreach ($menu as $key => $item): ?>
            <?php if ($key === 'dashboard' || has_permission($item[3])): ?>
                <a class="nav-link <?php echo $activeMenu === $key ? 'active' : ''; ?>" href="<?php echo e(url('admin/' . $item[1])); ?>"><span class="material-symbols-outlined align-middle me-2"><?php echo e($item[2]); ?></span> <?php echo e($item[0]); ?></a>
            <?php endif; ?>
        <?php endforeach; ?>
    </nav>
</aside>
