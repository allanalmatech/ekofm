<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../modules/programs.php';

header('Content-Type: application/json; charset=utf-8');

$currentShow = current_program_on_air();

if (!$currentShow) {
    echo json_encode(array(
        'ok' => true,
        'live' => false,
        'mode' => 'fallback',
        'title' => trim((string) preg_replace('/^now\s*playing\s*:\s*/i', '', setting('radio_stream_title', 'EKO FM Live'))),
    ));
    exit;
}

echo json_encode(array(
    'ok' => true,
    'live' => true,
    'mode' => 'live',
    'title' => $currentShow['title'],
    'slug' => $currentShow['slug'],
    'day_of_week' => $currentShow['day_of_week'],
    'start_time' => $currentShow['start_time'],
    'end_time' => $currentShow['end_time'],
));
