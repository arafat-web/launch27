<?php
/**
 * Launch27 API Proxy
 * ------------------
 * Forwards API requests server-side, bypassing CORS restrictions.
 * Handles /booking/spots specially — Launch27 requires POST+JSON for that
 * endpoint even though our frontend calls it via a GET request.
 */

// ── Config ─────────────────────────────────────────────────────────────────
define('L27_SUBDOMAIN', getenv('L27_SUBDOMAIN') ?: 'arafatweb');
define('L27_COMPANY_ID', getenv('L27_COMPANY_ID') ?: 'arafatweb');
define('L27_API_KEY', getenv('L27_API_KEY') ?: 'live_67gMRqqzGi5I4oim3wAC');
define('L27_BASE_URL', 'https://' . L27_SUBDOMAIN . '.launch27.com/v1');

// ── CORS headers ────────────────────────────────────────────────────────────
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// ── Route ───────────────────────────────────────────────────────────────────
$path = '/' . ltrim($_GET['path'] ?? 'services', '/');
$method = $_SERVER['REQUEST_METHOD'];

// Map short paths → actual Launch27 endpoint paths
$pathMap = [
    '/services' => '/booking/services',
    '/windows' => '/booking/arrival_windows',
    '/arrival_windows' => '/booking/arrival_windows',
    '/spots' => '/booking/spots',
    '/booking/spots' => '/booking/spots',
];

$actualPath = $pathMap[$path] ?? $path;
$url = L27_BASE_URL . $actualPath;

// ── /booking/spots — Launch27 requires POST with a JSON body ─────────────────
// The Live widget sends: {"date":"YYYY-MM-DD","location_id":1,"mode":"new","days":35,"duration":0}
// Our browser GET (?path=/spots&date=...) is intercepted here and converted.
if ($actualPath === '/booking/spots') {
    $date = $_GET['date'] ?? date('Y-m-d');
    $locationId = (int) ($_GET['location_id'] ?? 1);
    $days = (int) ($_GET['days'] ?? 35);
    $duration = (int) ($_GET['duration'] ?? 0);

    $spotBody = json_encode([
        'date' => $date,
        'location_id' => $locationId,
        'mode' => 'new',
        'days' => $days,
        'duration' => $duration,
    ]);

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $spotBody,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . L27_API_KEY,
            'X-Company-Id: ' . L27_COMPANY_ID,
            'Content-Type: application/json;charset=UTF-8',
            'Accept: application/json',
        ],
        CURLOPT_TIMEOUT => 20,
        CURLOPT_SSL_VERIFYPEER => true,
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($curlError) {
        http_response_code(502);
        echo json_encode(['error' => 'Proxy error: ' . $curlError]);
        exit;
    }

    http_response_code($httpCode);
    echo $response;
    exit;
}

// ── All other endpoints ──────────────────────────────────────────────────────
// Forward query params (except 'path' itself)
$params = $_GET;
unset($params['path']);
if (!empty($params)) {
    $url .= '?' . http_build_query($params);
}

$ch = curl_init($url);

$headers = [
    'Authorization: Bearer ' . L27_API_KEY,
    'X-Company-Id: ' . L27_COMPANY_ID,
    'Content-Type: application/json',
    'Accept: application/json',
];

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CUSTOMREQUEST => $method,
    CURLOPT_HTTPHEADER => $headers,
    CURLOPT_TIMEOUT => 15,
    CURLOPT_SSL_VERIFYPEER => true,
]);

// Forward request body for POST/PUT/PATCH
if (in_array($method, ['POST', 'PUT', 'PATCH'])) {
    $body = file_get_contents('php://input');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
}

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($curlError) {
    http_response_code(502);
    echo json_encode(['error' => 'Proxy error: ' . $curlError]);
    exit;
}

http_response_code($httpCode);
echo $response;