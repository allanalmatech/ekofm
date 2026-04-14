<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set('Africa/Lagos');

define('APP_NAME', 'Eko FM');
define('APP_ENV', 'production');

define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'ekofm');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

$https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
$scheme = $https ? 'https' : 'http';
$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
$docRoot = isset($_SERVER['DOCUMENT_ROOT']) ? str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT'])) : '';
$projectRoot = str_replace('\\', '/', realpath(dirname(__DIR__)));
$basePath = '';
if ($docRoot && $projectRoot && strpos($projectRoot, $docRoot) === 0) {
    $basePath = substr($projectRoot, strlen($docRoot));
}
$basePath = rtrim(str_replace('\\', '/', $basePath), '/');

define('BASE_URL', $scheme . '://' . $host . ($basePath ? $basePath : ''));
define('BASE_PATH', dirname(__DIR__));
define('UPLOAD_PATH', BASE_PATH . '/uploads');
define('UPLOAD_URL', BASE_URL . '/uploads');

define('MAX_IMAGE_UPLOAD', 5 * 1024 * 1024);
define('MAX_AUDIO_UPLOAD', 25 * 1024 * 1024);

if (!is_dir(UPLOAD_PATH)) {
    @mkdir(UPLOAD_PATH, 0755, true);
}
