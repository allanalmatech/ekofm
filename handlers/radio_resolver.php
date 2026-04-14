<?php
require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json; charset=utf-8');

$embed = isset($_GET['embed']) ? trim($_GET['embed']) : '';
if ($embed === '') {
    http_response_code(400);
    echo json_encode(array('ok' => false, 'error' => 'Missing embed parameter'));
    exit;
}

if (strpos($embed, '//') === 0) {
    $embed = 'https:' . $embed;
}

$parts = @parse_url($embed);
if (!$parts || empty($parts['host']) || stripos($parts['host'], 'myradiostream.com') === false) {
    http_response_code(400);
    echo json_encode(array('ok' => false, 'error' => 'Unsupported embed host'));
    exit;
}

$path = isset($parts['path']) ? trim($parts['path'], '/') : '';
$pathParts = $path ? explode('/', $path) : array();
$station = '';
if (count($pathParts) >= 2 && strtolower($pathParts[0]) === 'embed') {
    $station = $pathParts[1];
}

if ($station === '') {
    http_response_code(400);
    echo json_encode(array('ok' => false, 'error' => 'Could not parse station id'));
    exit;
}

$jsonUrl = 'https://myradiostream.com/embed/json.php?s=' . urlencode($station);

$context = stream_context_create(array(
    'http' => array('timeout' => 8, 'ignore_errors' => true),
    'ssl' => array('verify_peer' => true, 'verify_peer_name' => true),
));

$response = @file_get_contents($jsonUrl, false, $context);
if ($response === false) {
    http_response_code(502);
    echo json_encode(array('ok' => false, 'error' => 'Failed to fetch station json'));
    exit;
}

$json = json_decode($response, true);
if (!is_array($json) || empty($json['url'])) {
    http_response_code(502);
    echo json_encode(array('ok' => false, 'error' => 'Invalid station json'));
    exit;
}

$streamUrl = $json['url'];
if (strpos($streamUrl, '//') === 0) {
    $streamUrl = 'https:' . $streamUrl;
}

echo json_encode(array(
    'ok' => true,
    'station' => $station,
    'url' => $streamUrl,
    'stats' => isset($json['stats']) ? $json['stats'] : '',
));
