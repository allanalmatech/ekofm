<?php
require_once __DIR__ . '/../includes/functions.php';

function services_list($onlyPublished)
{
    if ($onlyPublished) {
        $stmt = db_query('SELECT * FROM services WHERE status = 1 ORDER BY sort_order ASC, id DESC');
        return $stmt->fetchAll();
    }
    return db_query('SELECT * FROM services ORDER BY sort_order ASC, id DESC')->fetchAll();
}
