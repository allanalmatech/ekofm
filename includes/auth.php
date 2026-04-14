<?php
require_once __DIR__ . '/rbac.php';

function login_attempt_key()
{
    return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'cli';
}

function is_rate_limited()
{
    $ip = login_attempt_key();
    $stmt = db_query('SELECT COUNT(*) AS attempts FROM login_attempts WHERE ip_address = ? AND created_at >= (NOW() - INTERVAL 15 MINUTE)', array($ip));
    $row = $stmt->fetch();
    return $row && (int) $row['attempts'] >= 5;
}

function record_login_attempt($email, $success)
{
    db_query('INSERT INTO login_attempts (email, ip_address, successful, created_at) VALUES (?, ?, ?, NOW())', array($email, login_attempt_key(), $success ? 1 : 0));
}

function auth_login($email, $password)
{
    if (is_rate_limited()) {
        return array('ok' => false, 'message' => 'Too many attempts. Try again in 15 minutes.');
    }

    $stmt = db_query('SELECT * FROM users WHERE email = ? AND status = 1 LIMIT 1', array($email));
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
        record_login_attempt($email, false);
        return array('ok' => false, 'message' => 'Invalid login credentials.');
    }

    record_login_attempt($email, true);
    session_regenerate_id(true);
    $_SESSION['user_id'] = (int) $user['id'];
    log_activity('login', 'auth', (int) $user['id'], 'User logged in');

    return array('ok' => true, 'user' => $user);
}

function auth_logout()
{
    $user = current_user();
    if ($user) {
        log_activity('logout', 'auth', (int) $user['id'], 'User logged out');
    }
    $_SESSION = array();
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}

function require_login()
{
    if (!current_user()) {
        redirect('admin/login.php');
    }
}
