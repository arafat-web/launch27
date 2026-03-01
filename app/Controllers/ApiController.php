<?php
/**
 * ApiController
 * ─────────────
 * Handles all JSON / proxy endpoints.
 * All methods set Content-Type: application/json and then exit.
 */
class ApiController
{
    private Api $api;

    public function __construct()
    {
        $this->api = new Api();

        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(204);
            exit;
        }
    }

    // ── GET /api/services ──────────────────────────────────────────────────────
    public function services(): void
    {
        $response = $this->api->call('booking/services');
        echo json_encode([
            'success'  => true,
            'response' => json_decode($response, true),
        ]);
    }

    // ── GET /api/windows ───────────────────────────────────────────────────────
    public function windows(): void
    {
        $response = $this->api->call('booking/arrival_windows');
        echo json_encode([
            'success'  => true,
            'response' => json_decode($response, true),
        ]);
    }

    // ── POST /api/book ─────────────────────────────────────────────────────────
    public function book(): void
    {
        Logger::log('BOOKING_ATTEMPT', [
            'first_name' => $_POST['first_name'] ?? '',
            'last_name'  => $_POST['last_name']  ?? '',
            'email'      => $_POST['email']      ?? '',
            'date'       => $_POST['date']       ?? '',
            'time'       => $_POST['time']       ?? '',
            'service_id' => $_POST['service_id'] ?? '',
        ]);

        // Convert 12-hour time → 24-hour
        $timeStr = trim($_POST['time'] ?? '');
        if (preg_match('/(\d{1,2}):(\d{2})\s*(AM|PM)/i', $timeStr, $m)) {
            $hour   = (int)$m[1];
            $minute = $m[2];
            $period = strtoupper($m[3]);
            if ($period === 'PM' && $hour !== 12) { $hour += 12; }
            elseif ($period === 'AM' && $hour === 12) { $hour = 0; }
            $time24 = sprintf('%02d:%02d', $hour, $minute);
        } else {
            $time24 = $timeStr;
        }

        $datetime       = ($_POST['date'] ?? '') . 'T' . $time24 . ':00';
        $pricingParams  = json_decode($_POST['pricing_parameters'] ?? '[]', true);
        $serviceId      = (int)($_POST['service_id'] ?? 0);

        $services = [['id' => $serviceId]];
        if (!empty($pricingParams)) {
            $services[0]['pricing_parameters'] = array_map(fn($p) => [
                'id'       => $p['id']  ?? 1,
                'quantity' => $p['quantity'] ?? ($p['value'] ?? 1),
            ], $pricingParams);
        } else {
            $services[0]['pricing_parameters'] = [['id' => 1, 'quantity' => 1]];
        }

        $payload = [
            'user'           => [
                'first_name' => $_POST['first_name'] ?? '',
                'last_name'  => $_POST['last_name']  ?? '',
                'email'      => $_POST['email']      ?? '',
                'phone'      => $_POST['phone']      ?? '',
            ],
            'address'        => $_POST['address']  ?? '',
            'city'           => $_POST['city']     ?? '',
            'state'          => substr($_POST['state'] ?? 'NY', 0, 3),
            'zip'            => $_POST['zip']      ?? '',
            'services'       => $services,
            'service_date'   => $datetime,
            'arrival_window' => (int)($_POST['arrival_window'] ?? 0),
            'frequency_id'   => 1,
            'payment_method' => 'cash',
        ];

        $raw     = $this->api->call('booking', 'POST', $payload);
        $decoded = json_decode($raw, true);

        Logger::log('API_RESPONSE_DETAILED', [
            'email'    => $_POST['email'] ?? '',
            'raw'      => substr($raw, 0, 500),
            'decoded'  => $decoded,
        ]);

        if (is_array($decoded) && isset($decoded['(root)'])) {
            http_response_code(422);
            Logger::log('BOOKING_ERROR', ['error' => 'validation', 'details' => $decoded['(root)']], 'ERROR');
            echo json_encode(['success' => false, 'error' => 'Validation error', 'details' => $decoded]);
        } elseif (isset($decoded['booking_id'])) {
            Logger::log('BOOKING_SUCCESS', ['booking_id' => $decoded['booking_id'], 'amount' => $decoded['total'] ?? '?'], 'SUCCESS');
            echo json_encode(['success' => true, 'booking_id' => $decoded['booking_id'], 'id' => $decoded['booking_id'], 'data' => $decoded]);
        } else {
            Logger::log('BOOKING_RESPONSE', ['decoded' => $decoded]);
            echo json_encode(['success' => true, 'data' => $decoded]);
        }
    }

    // ── GET|POST /api/proxy ────────────────────────────────────────────────────
    public function proxy(): void
    {
        // Path map (short → actual Launch27 path)
        $pathMap = [
            '/services'        => '/booking/services',
            '/windows'         => '/booking/arrival_windows',
            '/arrival_windows' => '/booking/arrival_windows',
            '/spots'           => '/booking/spots',
            '/booking/spots'   => '/booking/spots',
        ];

        $path       = '/' . ltrim($_GET['path'] ?? 'services', '/');
        $actualPath = $pathMap[$path] ?? $path;
        $method     = $_SERVER['REQUEST_METHOD'];

        // /booking/spots requires a POST with a JSON body
        if ($actualPath === '/booking/spots') {
            $body = json_encode([
                'date'        => $_GET['date']        ?? date('Y-m-d'),
                'location_id' => (int)($_GET['location_id'] ?? 1),
                'mode'        => 'new',
                'days'        => (int)($_GET['days']  ?? 35),
                'duration'    => (int)($_GET['duration'] ?? 0),
            ]);
            $result = $this->api->proxy($actualPath, 'POST', [], $body);
        } else {
            $params = $_GET;
            unset($params['path']);
            $body   = ($method !== 'GET') ? file_get_contents('php://input') : null;
            $result = $this->api->proxy($actualPath, $method, $params, $body);
        }

        http_response_code($result['code']);
        echo $result['body'];
    }

    // ── GET /api/logs ──────────────────────────────────────────────────────────
    public function logs(): void
    {
        $limit = (int)($_GET['limit'] ?? 100);
        $logs  = Logger::getLogs($limit);
        echo json_encode(['success' => true, 'count' => count($logs), 'logs' => $logs]);
    }
}
