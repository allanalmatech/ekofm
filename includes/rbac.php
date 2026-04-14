<?php
require_once __DIR__ . '/functions.php';

function user_permissions($userId)
{
    $perms = array();

    $rolePerms = db_query(
        'SELECT p.slug FROM permissions p
         INNER JOIN role_permissions rp ON rp.permission_id = p.id
         INNER JOIN users u ON u.role_id = rp.role_id
         WHERE u.id = ?',
        array((int) $userId)
    )->fetchAll();

    $userPerms = db_query(
        'SELECT p.slug FROM permissions p
         INNER JOIN user_permissions up ON up.permission_id = p.id
         WHERE up.user_id = ?',
        array((int) $userId)
    )->fetchAll();

    foreach ($rolePerms as $row) {
        $perms[$row['slug']] = true;
    }
    foreach ($userPerms as $row) {
        $perms[$row['slug']] = true;
    }

    return array_keys($perms);
}

function has_permission($permission)
{
    $user = current_user();
    if (!$user) {
        return false;
    }

    if (!empty($user['role_slug']) && $user['role_slug'] === 'super_admin') {
        return true;
    }

    static $memo = array();
    $uid = (int) $user['id'];
    if (!isset($memo[$uid])) {
        $memo[$uid] = user_permissions($uid);
    }

    return in_array($permission, $memo[$uid]);
}

function require_permission($permission)
{
    if (!has_permission($permission)) {
        http_response_code(403);
        echo '<h1>403 Forbidden</h1><p>You do not have permission to access this area.</p>';
        exit;
    }
}
