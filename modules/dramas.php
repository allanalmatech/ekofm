<?php
require_once __DIR__ . '/../includes/functions.php';

function dramas_list($onlyPublished)
{
    if ($onlyPublished) {
        $stmt = db_query('SELECT * FROM dramas WHERE status = 1 ORDER BY id DESC');
        return $stmt->fetchAll();
    }
    $stmt = db_query('SELECT * FROM dramas ORDER BY id DESC');
    return $stmt->fetchAll();
}
