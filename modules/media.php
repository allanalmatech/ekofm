<?php
require_once __DIR__ . '/../includes/functions.php';

function gallery_items($category)
{
    if ($category) {
        $stmt = db_query('SELECT * FROM media WHERE file_type = ? AND category_name = ? ORDER BY id DESC', array('image', $category));
        return $stmt->fetchAll();
    }
    $stmt = db_query('SELECT * FROM media WHERE file_type = ? ORDER BY id DESC', array('image'));
    return $stmt->fetchAll();
}
