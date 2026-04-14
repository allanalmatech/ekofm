<?php
require_once __DIR__ . '/../includes/functions.php';

function rate_cards($onlyPublished)
{
    if ($onlyPublished) {
        return db_query('SELECT * FROM rate_cards WHERE status = 1 ORDER BY category_name ASC, sort_order ASC, id DESC')->fetchAll();
    }
    return db_query('SELECT * FROM rate_cards ORDER BY category_name ASC, sort_order ASC, id DESC')->fetchAll();
}
