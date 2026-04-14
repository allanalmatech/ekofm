<?php
require_once __DIR__ . '/db.php';

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function url($path = '')
{
    $path = ltrim($path, '/');
    return rtrim(BASE_URL, '/') . '/' . $path;
}

function redirect($path)
{
    header('Location: ' . (strpos($path, 'http') === 0 ? $path : url($path)));
    exit;
}

function current_user()
{
    if (empty($_SESSION['user_id'])) {
        return null;
    }

    static $user = null;
    if ($user) {
        return $user;
    }

    $stmt = db_query(
        'SELECT u.*, r.name AS role_name, r.slug AS role_slug FROM users u LEFT JOIN roles r ON r.id = u.role_id WHERE u.id = ? LIMIT 1',
        array((int) $_SESSION['user_id'])
    );
    $user = $stmt->fetch();
    return $user ?: null;
}

function csrf_token()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf($token)
{
    return !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], (string) $token);
}

function flash($key, $value = null)
{
    if ($value === null) {
        if (!isset($_SESSION['flash'][$key])) {
            return null;
        }
        $tmp = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $tmp;
    }
    $_SESSION['flash'][$key] = $value;
}

function old($key, $default = '')
{
    return isset($_SESSION['old'][$key]) ? $_SESSION['old'][$key] : $default;
}

function set_old($data)
{
    $_SESSION['old'] = $data;
}

function clear_old()
{
    unset($_SESSION['old']);
}

function setting($key, $default = '')
{
    static $cache = array();
    if (array_key_exists($key, $cache)) {
        return $cache[$key];
    }
    $stmt = db_query('SELECT setting_value FROM settings WHERE setting_key = ? LIMIT 1', array($key));
    $row = $stmt->fetch();
    $cache[$key] = $row ? $row['setting_value'] : $default;
    return $cache[$key];
}

function save_setting($key, $value)
{
    db_query('INSERT INTO settings (setting_key, setting_value, updated_at) VALUES (?, ?, NOW()) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), updated_at = NOW()', array($key, $value));
}

function slugify($text)
{
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-') ?: uniqid('item-');
}

function upload_file($file, $allowedExt, $maxSize, $subDir)
{
    if (empty($file['name']) || $file['error'] !== UPLOAD_ERR_OK) {
        return array('ok' => false, 'error' => 'Upload failed.');
    }

    if ($file['size'] > $maxSize) {
        return array('ok' => false, 'error' => 'File too large.');
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExt)) {
        return array('ok' => false, 'error' => 'Invalid file type.');
    }

    $dir = rtrim(UPLOAD_PATH, '/') . '/' . trim($subDir, '/');
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }

    $filename = date('YmdHis') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $target = $dir . '/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $target)) {
        return array('ok' => false, 'error' => 'Unable to save uploaded file.');
    }

    return array('ok' => true, 'path' => trim($subDir, '/') . '/' . $filename, 'url' => UPLOAD_URL . '/' . trim($subDir, '/') . '/' . $filename);
}

function media_url($path)
{
    if (!$path) {
        return url('assets/images/placeholder.svg');
    }
    if (strpos($path, 'http://') === 0 || strpos($path, 'https://') === 0) {
        return $path;
    }
    return UPLOAD_URL . '/' . ltrim($path, '/');
}

function log_activity($action, $module, $itemId, $description)
{
    $user = current_user();
    db_query(
        'INSERT INTO activity_logs (user_id, action, module_name, item_id, description, ip_address, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())',
        array($user ? (int) $user['id'] : null, $action, $module, (int) $itemId, $description, isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '')
    );
}

function format_date($date, $format = 'M d, Y')
{
    if (empty($date)) {
        return '';
    }
    return date($format, strtotime($date));
}

function whatsapp_link($number)
{
    $digits = preg_replace('/[^0-9]/', '', (string) $number);
    if (strpos($digits, '0') === 0) {
        $digits = '256' . substr($digits, 1);
    }
    return 'https://wa.me/' . $digits;
}
