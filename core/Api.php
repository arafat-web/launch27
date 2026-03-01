<?php
/**
 * Api
 * ───
 * HTTP client for the Launch27 REST API.
 */
class Api
{
    public function __construct(
        private string $baseUrl   = L27_BASE_URL,
        private string $apiKey    = L27_API_KEY,
        private string $companyId = L27_COMPANY_ID,
    ) {}

    /**
     * @param string      $endpoint  e.g. 'booking/services'
     * @param string      $method    GET | POST
     * @param array|null  $data      POST body (will be JSON-encoded)
     * @return string Raw JSON response
     */
    public function call(string $endpoint, string $method = 'GET', ?array $data = null): string
    {
        $url = rtrim($this->baseUrl, '/') . '/' . ltrim($endpoint, '/');

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT        => 20,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $this->apiKey,
                'X-Company-Id: '         . $this->companyId,
                'Content-Type: application/json',
                'Accept: application/json',
            ],
        ]);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST,       true);
            curl_setopt($ch, CURLOPT_POSTFIELDS,  json_encode($data ?? []));
        }

        $response  = curl_exec($ch);
        $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        Logger::log('API_CALL', [
            'endpoint'      => $endpoint,
            'method'        => $method,
            'status_code'   => $httpCode,
            'response_size' => strlen((string)$response),
            'error'         => $curlError ?: null,
        ], $httpCode >= 400 ? 'ERROR' : 'SUCCESS');

        if ($curlError) {
            return json_encode(['error' => 'cURL error: ' . $curlError]);
        }

        return (string)$response;
    }

    /**
     * Generic proxy: forward an arbitrary request to Launch27.
     *
     * @param string      $path      e.g. '/booking/services'
     * @param string      $method    HTTP method
     * @param array       $params    Query-string params
     * @param string|null $body      Raw request body (for POST/PUT/PATCH)
     * @return array ['code' => int, 'body' => string]
     */
    public function proxy(string $path, string $method = 'GET', array $params = [], ?string $body = null): array
    {
        $url = rtrim($this->baseUrl, '/') . '/' . ltrim($path, '/');
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $this->apiKey,
                'X-Company-Id: '         . $this->companyId,
                'Content-Type: application/json;charset=UTF-8',
                'Accept: application/json',
            ],
            CURLOPT_TIMEOUT        => 20,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        if ($body !== null && in_array($method, ['POST', 'PUT', 'PATCH'])) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        }

        $response  = curl_exec($ch);
        $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            return ['code' => 502, 'body' => json_encode(['error' => 'Proxy error: ' . $curlError])];
        }

        return ['code' => $httpCode, 'body' => (string)$response];
    }
}
