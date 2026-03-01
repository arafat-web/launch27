<?php

error_reporting(0);
ini_set('display_errors', 0);

$API_KEY    = "live_67gMRqqzGi5I4oim3wAC";
$COMPANY_ID = "arafatweb";

$BASE = "https://arafatweb.launch27.com/v1";

$LOG_DIR = __DIR__ . '/logs';
if (!is_dir($LOG_DIR)) {
    mkdir($LOG_DIR, 0755, true);
}

/* =====================
   LOGGING SYSTEM
=====================*/

function log_event($event_type, $data, $status = 'INFO') {
    global $LOG_DIR;
    
    $timestamp = date('Y-m-d H:i:s');
    $log_file = $LOG_DIR . '/' . date('Y-m-d') . '_bookings.log';
    
    $log_entry = [
        'timestamp' => $timestamp,
        'type' => $event_type,
        'status' => $status,
        'data' => is_array($data) ? $data : ['message' => $data],
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ];
    
    $log_line = json_encode($log_entry) . "\n";
    file_put_contents($log_file, $log_line, FILE_APPEND);
    
    return $log_entry;
}

function get_logs($lines = 100) {
    global $LOG_DIR;
    
    $log_file = $LOG_DIR . '/' . date('Y-m-d') . '_bookings.log';
    if (!file_exists($log_file)) {
        return ['error' => 'No logs for today'];
    }
    
    $logs = [];
    $file_lines = file($log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    // Get last N lines
    $file_lines = array_slice($file_lines, -$lines);
    
    foreach ($file_lines as $line) {
        if (!empty($line)) {
            $logs[] = json_decode($line, true);
        }
    }
    
    return array_reverse($logs);
}

/* =====================
   CURL HELPER
=====================*/

function callAPI($endpoint, $method = "GET", $data = null)
{

    global $API_KEY, $COMPANY_ID, $BASE;

    $url = $BASE . '/' . $endpoint;

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    curl_setopt($ch, CURLOPT_HTTPHEADER, [

        "Authorization: Bearer " . $API_KEY,
        "X-Company-Id: " . $COMPANY_ID,
        "Content-Type: application/json",

    ]);

    if ($method === "POST") {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);

    // Log API call
    log_event('API_CALL', [
        'endpoint' => $endpoint,
        'method' => $method,
        'status_code' => $http_code,
        'request_size' => strlen(json_encode($data ?? [])),
        'response_size' => strlen($response),
        'error' => $curl_error ?: null
    ], $http_code >= 400 ? 'ERROR' : 'SUCCESS');

    return $response;
}

/* =====================
   ROUTER
=====================*/

$action = $_GET['action'] ?? '';

/* =====================
   GET LOGS
=====================*/

if ($action === "logs") {
    header('Content-Type: application/json');
    $limit = $_GET['limit'] ?? 100;
    $logs = get_logs((int)$limit);
    echo json_encode([
        "success" => true,
        "count" => count($logs),
        "logs" => $logs
    ]);
    exit;
}

/* =====================
   GET SERVICES
=====================*/

if ($action === "services") {

    header('Content-Type: application/json');
    $response = callAPI("booking/services");
    echo json_encode([
        "debug" => [
            "url" => "https://arafatweb-sandbox.launch27.com/v1/booking/services",
            "base" => $BASE
        ],
        "response" => $response
    ]);

    exit;
}

/* =====================
   GET ARRIVAL WINDOWS
=====================*/

if ($action === "windows") {

    header('Content-Type: application/json');
    $response = callAPI("booking/arrival_windows");
    echo json_encode([
        "debug" => [
            "url" => "https://arafatweb-sandbox.launch27.com/v1/arrival_windows",
            "base" => $BASE
        ],
        "response" => $response
    ]);

    exit;
}

/* =====================
   CREATE BOOKING
=====================*/

if ($action === "book") {

    // Log booking request start
    log_event('BOOKING_ATTEMPT', [
        'first_name' => $_POST['first_name'] ?? '',
        'last_name' => $_POST['last_name'] ?? '',
        'email' => $_POST['email'] ?? '',
        'date' => $_POST['date'] ?? '',
        'time' => $_POST['time'] ?? '',
        'service_id' => $_POST['service_id'] ?? ''
    ], 'INFO');

    // Convert 12-hour time format (e.g. "8:00 AM") to 24-hour format (e.g. "08:00")
    $timeStr = trim($_POST['time']);
    if (preg_match('/(\d{1,2}):(\d{2})\s*(AM|PM)/i', $timeStr, $matches)) {
        $hour = (int)$matches[1];
        $minute = $matches[2];
        $period = strtoupper($matches[3]);
        
        if ($period === 'PM' && $hour !== 12) {
            $hour += 12;
        } elseif ($period === 'AM' && $hour === 12) {
            $hour = 0;
        }
        
        $time24 = sprintf("%02d:%02d", $hour, $minute);
    } else {
        // Already in 24-hour format
        $time24 = $timeStr;
    }

    $datetime = $_POST['date'] . 'T' . $time24 . ':00';
    
    $pricing_params = json_decode($_POST['pricing_parameters'] ?? '[]', true);
    
    // Build services array with pricing parameters
    $service_id = (int) ($_POST['service_id'] ?? 0);
    $services = [
        [
            "id" => $service_id,
        ]
    ];
    
    // Add pricing_parameters to service (required by API)
    if (!empty($pricing_params)) {
        // Ensure each pricing parameter has required fields (id and quantity)
        $services[0]["pricing_parameters"] = array_map(function($p) {
            $param = ['id' => $p['id'] ?? 1];
            // Use quantity if it exists, otherwise use value, otherwise default to 1
            $param['quantity'] = $p['quantity'] ?? ($p['value'] ?? 1);
            return $param;
        }, $pricing_params);
    } else {
        // Default pricing parameter if none provided (API requires at least one)
        $services[0]["pricing_parameters"] = [
            ["id" => 1, "quantity" => 1]
        ];
    }

    $data = [

        "user"           => [
            "first_name" => $_POST['first_name'],
            "last_name"  => $_POST['last_name'],
            "email"      => $_POST['email'],
            "phone"      => $_POST['phone'],
        ],

        "address"        => $_POST['address'],
        
        "city"           => $_POST['city'],
        
        "state"          => substr($_POST['state'] ?? 'NY', 0, 3),
        
        "zip"            => $_POST['zip'],

        "services"       => $services,

        "service_date"   => $datetime,

        "arrival_window" =>
        (int) ($_POST['arrival_window'] ?? 0),

        "frequency_id"   => 1,

        "payment_method" => "cash",

    ];

    $response = callAPI(
        "booking",
        "POST",
        $data
    );

    header('Content-Type: application/json');
    
    $decoded = json_decode($response, true);
    
    // Log the raw API response for debugging
    log_event('API_RESPONSE_DETAILED', [
        'email' => $_POST['email'] ?? '',
        'raw_response' => substr($response, 0, 500),
        'decoded_response' => $decoded
    ], 'INFO');
    
    // Check if response has errors
    if (is_array($decoded) && isset($decoded['(root)'])) {
        // API validation error
        http_response_code(422);
        log_event('BOOKING_ERROR', [
            'email' => $_POST['email'] ?? '',
            'error_type' => 'validation',
            'details' => $decoded['(root)']
        ], 'ERROR');
        echo json_encode([
            "success" => false,
            "error" => "Validation error",
            "details" => $decoded
        ]);
    } else if (isset($decoded['booking_id'])) {
        // Success
        log_event('BOOKING_SUCCESS', [
            'email' => $_POST['email'] ?? '',
            'booking_id' => $decoded['booking_id'],
            'amount' => $decoded['total'] ?? 'unknown'
        ], 'SUCCESS');
        echo json_encode([
            "success" => true,
            "booking_id" => $decoded['booking_id'],
            "id" => $decoded['booking_id'],
            "data" => $decoded
        ]);
    } else {
        // Other responses - check if this is an error
        if (is_array($decoded)) {
            log_event('BOOKING_RESPONSE', [
                'email' => $_POST['email'] ?? '',
                'response_keys' => array_keys($decoded),
                'response_data' => $decoded
            ], 'INFO');
        } else {
            log_event('BOOKING_RESPONSE', [
                'email' => $_POST['email'] ?? '',
                'response_type' => gettype($decoded),
                'response_data' => $decoded
            ], 'INFO');
        }
        echo json_encode([
            "success" => true,
            "data" => $decoded
        ]);
    }

    exit;
}