<?php
require_once __DIR__ . '/../includes/functions.php';

function programs_list($onlyPublished)
{
    if ($onlyPublished) {
        $stmt = db_query('SELECT * FROM programs WHERE status = 1 ORDER BY day_of_week ASC, start_time ASC');
        return $stmt->fetchAll();
    }
    $stmt = db_query('SELECT * FROM programs ORDER BY id DESC');
    return $stmt->fetchAll();
}

function featured_shows($limit)
{
    $stmt = db_query('SELECT * FROM programs WHERE status = 1 ORDER BY id ASC LIMIT ' . (int) $limit);
    return $stmt->fetchAll();
}

function show_by_slug($slug)
{
    $stmt = db_query('SELECT * FROM programs WHERE slug = ? AND status = 1 LIMIT 1', array($slug));
    return $stmt->fetch();
}
