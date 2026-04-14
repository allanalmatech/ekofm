<?php
require_once __DIR__ . '/../includes/functions.php';

function news_latest($limit)
{
    $stmt = db_query('SELECT n.*, c.name AS category_name, u.name AS author_name FROM news_posts n LEFT JOIN news_categories c ON c.id = n.category_id LEFT JOIN users u ON u.id = n.created_by WHERE n.status = ? ORDER BY n.publish_date DESC, n.id DESC LIMIT ' . (int) $limit, array('published'));
    return $stmt->fetchAll();
}

function news_paginated($page, $perPage, $search = '')
{
    $page = max(1, (int) $page);
    $offset = ($page - 1) * $perPage;
    $params = array('published');
    $where = ' WHERE n.status = ? ';
    if ($search !== '') {
        $where .= ' AND (n.title LIKE ? OR n.summary LIKE ? OR n.content LIKE ?) ';
        $term = '%' . $search . '%';
        $params[] = $term;
        $params[] = $term;
        $params[] = $term;
    }

    $countStmt = db_query('SELECT COUNT(*) AS total FROM news_posts n' . $where, $params);
    $total = (int) $countStmt->fetch()['total'];

    $sql = 'SELECT n.*, c.name AS category_name, u.name AS author_name FROM news_posts n LEFT JOIN news_categories c ON c.id = n.category_id LEFT JOIN users u ON u.id = n.created_by ' . $where . ' ORDER BY n.publish_date DESC, n.id DESC LIMIT ' . (int) $perPage . ' OFFSET ' . (int) $offset;
    $rows = db_query($sql, $params)->fetchAll();

    return array('items' => $rows, 'total' => $total, 'page' => $page, 'per_page' => $perPage);
}

function news_find_by_slug($slug)
{
    $stmt = db_query('SELECT n.*, c.name AS category_name, u.name AS author_name FROM news_posts n LEFT JOIN news_categories c ON c.id = n.category_id LEFT JOIN users u ON u.id = n.created_by WHERE n.slug = ? AND n.status = ? LIMIT 1', array($slug, 'published'));
    return $stmt->fetch();
}
