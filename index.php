<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/modules/pages.php';
require_once __DIR__ . '/modules/news.php';
require_once __DIR__ . '/modules/programs.php';
require_once __DIR__ . '/modules/dramas.php';
require_once __DIR__ . '/modules/services.php';
require_once __DIR__ . '/modules/ratecard.php';
require_once __DIR__ . '/modules/media.php';

$route = isset($_GET['route']) ? trim($_GET['route']) : 'home';
if ($route === '') {
    $route = 'home';
}

$currentRoute = $route;
$slug = isset($_GET['slug']) ? $_GET['slug'] : '';

$viewFile = __DIR__ . '/pages/' . $route . '.php';
if ($route === 'home') {
    $viewFile = __DIR__ . '/pages/home.php';
} elseif ($route === 'news-single') {
    $viewFile = __DIR__ . '/pages/news-single.php';
} elseif ($route === 'show-detail') {
    $viewFile = __DIR__ . '/pages/show-detail.php';
    $currentRoute = 'shows';
} elseif (!file_exists($viewFile)) {
    $dynamic = get_page_by_slug($route);
    if ($dynamic) {
        $viewFile = __DIR__ . '/pages/dynamic-page.php';
        $dynamicPage = $dynamic;
    } else {
        http_response_code(404);
        $viewFile = __DIR__ . '/pages/404.php';
    }
}

ob_start();
include $viewFile;
$content = ob_get_clean();

if (isset($_SERVER['HTTP_X_PJAX']) && $_SERVER['HTTP_X_PJAX'] === 'true') {
    echo '<div id="pjax-container">' . $content . '</div>';
    exit;
}

include __DIR__ . '/templates/header.php';
echo $content;
include __DIR__ . '/templates/footer.php';
