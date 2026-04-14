<?php
require_once __DIR__ . '/../includes/functions.php';

function get_page_by_slug($slug)
{
    $stmt = db_query('SELECT * FROM pages WHERE slug = ? AND status = 1 LIMIT 1', array($slug));
    return $stmt->fetch();
}

function get_page_sections($pageId)
{
    $stmt = db_query('SELECT * FROM page_sections WHERE page_id = ? ORDER BY sort_order ASC, id ASC', array((int) $pageId));
    return $stmt->fetchAll();
}

function get_homepage_sections()
{
    $stmt = db_query('SELECT * FROM homepage_sections WHERE status = 1 ORDER BY sort_order ASC, id ASC');
    return $stmt->fetchAll();
}
