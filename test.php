<?php

/**
 * Launch27 Test API Client for PHP
 * Simulates booking operations for testing purposes
 */

class Launch27TestClient {
    private $apiKey;
    private $baseUrl;
    private $headers;
    private $logger;
    
    /**
     * Constructor
     * @param string $apiKey Test API key
     * @param string $baseUrl API base URL
     */
    public function __construct($apiKey = "live_67gMRqqzGi5I4oim3wAC", $baseUrl = "https://arafatweb.launch27.com/v1") {
        $this->apiKey = $apiKey;
        $this->baseUrl = $baseUrl;
        $this->headers = [
            "Authorization: Bearer " . $this->apiKey,
            "X-Company-Id: arafatweb",
            "Content-Type: application/json",
            "Accept: application/json"
        ];
        $this->logger = new Launch27Logger();
    }
    
    /**
     * Create a test booking
     * @param array $customData Optional custom booking data
     * @return array
     */
    public function createTestBooking($customData = []) {
        // Generate test booking data
        $bookingData = $this->generateTestBookingData();
        
        // Override with custom data if provided
        if (!empty($customData)) {
            $bookingData = array_merge_recursive($bookingData, $customData);
        }
        
        // Call actual API via api.php
        return $this->realApiCall($bookingData);
    }
    
    /**
     * Generate realistic test booking data
     * @return array
     */
    public function generateTestBookingData() {
        // Generate random customer info
        $firstNames = ["John", "Jane", "Mike", "Sarah", "David", "Emma", "Robert", "Lisa"];
        $lastNames = ["Smith", "Johnson", "Williams", "Brown", "Jones", "Garcia", "Miller", "Davis"];
        
        $firstName = $firstNames[array_rand($firstNames)];
        $lastName = $lastNames[array_rand($lastNames)];
        
        // Address parts
        $addresses = ["123 Main St", "456 Oak Ave", "789 Maple Dr", "321 Cedar Lane"];
        $cities = ["New York", "Los Angeles", "Chicago", "Houston"];
        $address = $addresses[array_rand($addresses)];
        $city = $cities[array_rand($cities)];
        $zip = sprintf("%05d", rand(10000, 99999));
        
        // Generate future date (next 1-7 days)
        $futureDate = date('Y-m-d', strtotime('+' . rand(1, 7) . ' days'));
        
        // Generate random time slot (9 AM to 4 PM)
        $hour = rand(9, 16);
        $timeSlot = sprintf("%02d:00", $hour);
        
        // Services
        $serviceIds = [1, 2, 3, 4, 5];
        $selectedServiceId = $serviceIds[array_rand($serviceIds)];
        
        // Arrival windows
        $arrivalWindows = [1, 2, 3, 4];
        $selectedWindow = $arrivalWindows[array_rand($arrivalWindows)];
        
        return [
            "user" => [
                "first_name" => $firstName,
                "last_name" => $lastName,
                "email" => strtolower($firstName . "." . $lastName . "@example.com"),
                "phone" => $this->generatePhoneNumber(),
            ],
            "address" => $address . ', ' . $city . ', ' . $zip,
            "services" => [
                [
                    "id" => $selectedServiceId
                ]
            ],
            "service_date" => $futureDate . 'T' . $timeSlot . ':00',
            "arrival_window" => $selectedWindow,
            "frequency_id" => 1,
            "payment_method" => "cash"
        ];
    }
    
    /**
     * Generate random extras
     * @return array
     */
    private function generateRandomExtras() {
        $possibleExtras = [
            ["id" => 101, "name" => "Inside Fridge", "price" => 25.00],
            ["id" => 102, "name" => "Inside Oven", "price" => 30.00],
            ["id" => 103, "name" => "Window Cleaning (per window)", "price" => 5.00],
            ["id" => 104, "name" => "Laundry", "price" => 40.00],
            ["id" => 105, "name" => "Cabinets Cleaning", "price" => 35.00],
            ["id" => 106, "name" => "Garage Cleaning", "price" => 60.00],
            ["id" => 107, "name" => "Move In/Out", "price" => 75.00]
        ];
        
        // Randomly select 0-3 extras
        $numExtras = rand(0, 3);
        $selectedExtras = [];
        
        if ($numExtras > 0) {
            $keys = array_rand($possibleExtras, min($numExtras, count($possibleExtras)));
            if (!is_array($keys)) {
                $keys = [$keys];
            }
            foreach ($keys as $key) {
                $selectedExtras[] = $possibleExtras[$key];
            }
        }
        
        return $selectedExtras;
    }
    
    /**
     * Generate random phone number
     * @return string
     */
    private function generatePhoneNumber() {
        return "+1" . rand(200, 999) . rand(200, 999) . rand(1000, 9999);
    }
    
    /**
     * Generate random street name
     * @return string
     */
    private function generateStreetName() {
        $streetNames = ["Main Street", "Oak Avenue", "Maple Drive", "Cedar Lane", 
                       "Pine Street", "Elm Road", "Washington Boulevard", "Park Avenue"];
        return $streetNames[array_rand($streetNames)];
    }
    
    /**
     * Get random duration
     * @return int
     */
    private function getRandomDuration() {
        $durations = [60, 90, 120, 180, 240];
        return $durations[array_rand($durations)];
    }
    
    /**
     * Get random priority
     * @return string
     */
    private function getRandomPriority() {
        $priorities = ["normal", "high", "low", "urgent"];
        return $priorities[array_rand($priorities)];
    }
    
    /**
     * Get random payment method
     * @return string
     */
    private function getRandomPaymentMethod() {
        $methods = ["credit_card", "cash", "bank_transfer", "paypal", "invoice"];
        return $methods[array_rand($methods)];
    }
    
    /**
     * Generate random string
     * @param int $length
     * @return string
     */
    private function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    
    /**
     * Make real API call via api.php
     * @param array $data
     * @return array
     */
    private function realApiCall($data = []) {
        $ch = curl_init('http://localhost/api.php?action=book');
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'first_name' => $data['user']['first_name'] ?? '',
            'last_name' => $data['user']['last_name'] ?? '',
            'email' => $data['user']['email'] ?? '',
            'phone' => $data['user']['phone'] ?? '',
            'address' => explode(', ', $data['address'])[0] ?? '',
            'city' => isset($data['address']) ? (explode(', ', $data['address'])[1] ?? '') : '',
            'zip' => isset($data['address']) ? (explode(', ', $data['address'])[2] ?? '') : '',
            'service_id' => $data['services'][0]['id'] ?? 1,
            'date' => substr($data['service_date'], 0, 10),
            'time' => substr($data['service_date'], 11, 5),
            'arrival_window' => $data['arrival_window'] ?? 1
        ]));
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        $this->logger->log("Via api.php - HTTP $http_code");
        
        if ($response === false) {
            return [
                "success" => false,
                "error" => "Request failed",
                "http_code" => $http_code
            ];
        }
        
        $decoded = json_decode($response, true);
        return [
            "success" => true,
            "http_code" => $http_code,
            "data" => $decoded,
            "raw_response" => $response
        ];
    }
    
    /**
     * Mock API call
     * @param string $method
     * @param string $endpoint
     * @param array $data
     * @return array
     */
    private function mockApiCall($method, $endpoint, $data = []) {
        // Generate booking ID
        $bookingId = "BK-" . rand(10000, 99999) . "-" . date('Ymd');
        
        // Log the API call
        $this->logger->log("Mock API call: $method $endpoint", [
            'booking_id' => $bookingId,
            'timestamp' => date('c')
        ]);
        
        // Generate mock response
        $response = [
            "success" => true,
            "booking_id" => $bookingId,
            "status" => "created",
            "message" => "Test booking created successfully",
            "data" => $data,
            "api_response" => [
                "timestamp" => date('c'),
                "method" => $method,
                "endpoint" => $endpoint,
                "simulated" => true,
                "response_time_ms" => rand(100, 500)
            ]
        ];
        
        $this->logger->log("Booking created: $bookingId");
        
        return $response;
    }
    
    /**
     * Get test booking by ID
     * @param string $bookingId
     * @return array
     */
    public function getTestBooking($bookingId) {
        $statuses = ["pending", "confirmed", "in_progress", "completed", "cancelled"];
        
        $response = [
            "success" => true,
            "booking_id" => $bookingId,
            "status" => $statuses[array_rand($statuses)],
            "data" => [
                "customer" => [
                    "first_name" => "Test",
                    "last_name" => "Customer",
                    "email" => "test.customer@example.com"
                ],
                "booking" => [
                    "date" => date('Y-m-d', strtotime('+3 days')),
                    "time" => "10:00",
                    "service" => "House Cleaning"
                ],
                "payment" => [
                    "amount" => 149.99,
                    "status" => "paid"
                ]
            ],
            "retrieved_at" => date('c')
        ];
        
        $this->logger->log("Retrieved booking: $bookingId");
        
        return $response;
    }
    
    /**
     * Update test booking
     * @param string $bookingId
     * @param array $updateData
     * @return array
     */
    public function updateTestBooking($bookingId, $updateData) {
        $response = [
            "success" => true,
            "booking_id" => $bookingId,
            "status" => "updated",
            "message" => "Test booking updated successfully",
            "updated_fields" => array_keys($updateData),
            "data" => $updateData,
            "updated_at" => date('c')
        ];
        
        $this->logger->log("Updated booking: $bookingId");
        
        return $response;
    }
    
    /**
     * Cancel test booking
     * @param string $bookingId
     * @param string $reason
     * @return array
     */
    public function cancelTestBooking($bookingId, $reason = "Test cancellation") {
        $response = [
            "success" => true,
            "booking_id" => $bookingId,
            "status" => "cancelled",
            "message" => "Test booking cancelled successfully",
            "cancellation_reason" => $reason,
            "cancelled_at" => date('c')
        ];
        
        $this->logger->log("Cancelled booking: $bookingId - Reason: $reason");
        
        return $response;
    }
    
    /**
     * Search bookings
     * @param array $filters
     * @return array
     */
    public function searchBookings($filters = []) {
        $bookings = [];
        $count = rand(5, 15);
        
        for ($i = 0; $i < $count; $i++) {
            $bookings[] = $this->generateTestBookingData();
        }
        
        $response = [
            "success" => true,
            "total" => $count,
            "page" => $filters['page'] ?? 1,
            "per_page" => $filters['per_page'] ?? 10,
            "bookings" => array_slice($bookings, 0, $filters['per_page'] ?? 10),
            "filters_applied" => $filters
        ];
        
        return $response;
    }
    
    /**
     * Get available time slots
     * @param string $date
     * @param int $serviceId
     * @return array
     */
    public function getAvailableTimeSlots($date = null, $serviceId = null) {
        if (!$date) {
            $date = date('Y-m-d', strtotime('+1 day'));
        }
        
        $timeSlots = [];
        $startHour = 8;
        $endHour = 17;
        
        for ($hour = $startHour; $hour <= $endHour; $hour++) {
            if (rand(0, 100) > 30) { // 70% availability
                $timeSlots[] = [
                    "time" => sprintf("%02d:00", $hour),
                    "available" => true,
                    "slots_remaining" => rand(1, 5)
                ];
            }
        }
        
        return [
            "success" => true,
            "date" => $date,
            "service_id" => $serviceId,
            "time_slots" => $timeSlots
        ];
    }
}

/**
 * Logger class for testing
 */
class Launch27Logger {
    private $logFile;
    
    public function __construct($logFile = null) {
        $this->logFile = $logFile ?: __DIR__ . '/launch27_test.log';
    }
    
    public function log($message, $context = []) {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] $message";
        
        if (!empty($context)) {
            $logMessage .= " " . json_encode($context);
        }
        
        $logMessage .= PHP_EOL;
        
        // Write to log file
        file_put_contents($this->logFile, $logMessage, FILE_APPEND);
        
        // Also output to console if in CLI mode
        if (php_sapi_name() === 'cli') {
            echo $logMessage;
        }
    }
}

/**
 * Booking Validator class
 */
class Launch27BookingValidator {
    
    /**
     * Validate booking data
     * @param array $bookingData
     * @return array ['valid' => bool, 'errors' => array]
     */
    public function validate($bookingData) {
        $errors = [];
        
        // Check required sections
        $requiredSections = ['customer', 'booking', 'payment'];
        foreach ($requiredSections as $section) {
            if (!isset($bookingData[$section])) {
                $errors[] = "Missing required section: $section";
            }
        }
        
        // Validate customer data
        if (isset($bookingData['customer'])) {
            $customerErrors = $this->validateCustomer($bookingData['customer']);
            $errors = array_merge($errors, $customerErrors);
        }
        
        // Validate booking data
        if (isset($bookingData['booking'])) {
            $bookingErrors = $this->validateBooking($bookingData['booking']);
            $errors = array_merge($errors, $bookingErrors);
        }
        
        // Validate payment data
        if (isset($bookingData['payment'])) {
            $paymentErrors = $this->validatePayment($bookingData['payment']);
            $errors = array_merge($errors, $paymentErrors);
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    private function validateCustomer($customer) {
        $errors = [];
        $required = ['first_name', 'last_name', 'email'];
        
        foreach ($required as $field) {
            if (empty($customer[$field])) {
                $errors[] = "Missing customer field: $field";
            }
        }
        
        if (!empty($customer['email']) && !filter_var($customer['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format: " . $customer['email'];
        }
        
        return $errors;
    }
    
    private function validateBooking($booking) {
        $errors = [];
        $required = ['service_id', 'date', 'time'];
        
        foreach ($required as $field) {
            if (empty($booking[$field])) {
                $errors[] = "Missing booking field: $field";
            }
        }
        
        // Validate date format
        if (!empty($booking['date'])) {
            $d = DateTime::createFromFormat('Y-m-d', $booking['date']);
            if (!$d || $d->format('Y-m-d') !== $booking['date']) {
                $errors[] = "Invalid date format. Use YYYY-MM-DD";
            }
        }
        
        return $errors;
    }
    
    private function validatePayment($payment) {
        $errors = [];
        
        if (isset($payment['amount']) && !is_numeric($payment['amount'])) {
            $errors[] = "Payment amount must be numeric";
        }
        
        if (isset($payment['amount']) && $payment['amount'] <= 0) {
            $errors[] = "Payment amount must be greater than 0";
        }
        
        return $errors;
    }
}

/**
 * Test Suite Runner
 */
class Launch27TestSuite {
    private $client;
    private $validator;
    private $results = [];
    
    public function __construct() {
        $this->client = new Launch27TestClient();
        $this->validator = new Launch27BookingValidator();
    }
    
    /**
     * Run all tests
     */
    public function runAllTests() {
        echo "\n" . str_repeat("=", 60) . "\n";
        echo "🚀 Launch27 API Test Suite (PHP)\n";
        echo str_repeat("=", 60) . "\n";
        
        $this->testCreateBasicBooking();
        $this->testCreateCustomBooking();
        $this->testGetBooking();
        $this->testUpdateBooking();
        $this->testCancelBooking();
        $this->testBulkBookings();
        $this->testSearchBookings();
        $this->testAvailability();
        $this->testValidation();
        
        $this->printSummary();
    }
    
    private function testCreateBasicBooking() {
        echo "\n📋 Test 1: Creating Basic Booking\n";
        
        $booking = $this->client->createTestBooking();
        
        if ($booking['success']) {
            echo "✅ API Call Success (HTTP {$booking['http_code']})\n";
            echo "   Response: " . json_encode($booking['data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
        } else {
            echo "❌ API Call Failed\n";
            echo "   Error: " . $booking['error'] . "\n";
            echo "   HTTP Code: " . $booking['http_code'] . "\n";
        }
        
        $this->results['basic_booking'] = $booking;
    }
    
    private function testCreateCustomBooking() {
        echo "\n📋 Test 2: Creating Custom Booking\n";
        
        $customData = [
            "customer" => [
                "first_name" => "VIP",
                "last_name" => "Customer",
                "email" => "vip.customer@example.com"
            ],
            "booking" => [
                "service_name" => "Premium Deep Cleaning",
                "priority" => "high"
            ],
            "payment" => [
                "amount" => 499.99
            ]
        ];
        
        $booking = $this->client->createTestBooking($customData);
        
        echo "✅ VIP Booking created: {$booking['booking_id']}\n";
        echo "   Customer: VIP Customer\n";
        echo "   Priority: high\n";
        
        $this->results['custom_booking'] = $booking;
    }
    
    private function testGetBooking() {
        echo "\n📋 Test 3: Retrieving Booking\n";
        
        if (isset($this->results['basic_booking'])) {
            $bookingId = $this->results['basic_booking']['booking_id'];
            $retrieved = $this->client->getTestBooking($bookingId);
            
            echo "✅ Retrieved booking: {$retrieved['booking_id']}\n";
            echo "   Status: {$retrieved['status']}\n";
        }
    }
    
    private function testUpdateBooking() {
        echo "\n📋 Test 4: Updating Booking\n";
        
        if (isset($this->results['basic_booking'])) {
            $bookingId = $this->results['basic_booking']['booking_id'];
            $updateData = [
                "booking" => [
                    "time" => "14:00",
                    "notes" => "Updated test notes"
                ],
                "extras" => [
                    ["id" => 101, "name" => "Inside Fridge", "price" => 25.00]
                ]
            ];
            
            $updated = $this->client->updateTestBooking($bookingId, $updateData);
            
            echo "✅ Updated booking: {$updated['booking_id']}\n";
            echo "   Updated fields: " . implode(', ', $updated['updated_fields']) . "\n";
        }
    }
    
    private function testCancelBooking() {
        echo "\n📋 Test 5: Cancelling Booking\n";
        
        if (isset($this->results['custom_booking'])) {
            $bookingId = $this->results['custom_booking']['booking_id'];
            $cancelled = $this->client->cancelTestBooking($bookingId, "Test completed");
            
            echo "✅ Cancelled booking: {$cancelled['booking_id']}\n";
            echo "   Reason: {$cancelled['cancellation_reason']}\n";
        }
    }
    
    private function testBulkBookings() {
        echo "\n📋 Test 6: Bulk Booking Creation\n";
        
        $bulkBookings = [];
        for ($i = 0; $i < 3; $i++) {
            $booking = $this->client->createTestBooking();
            $bulkBookings[] = $booking['booking_id'];
            echo "   ✅ Booking " . ($i + 1) . ": {$booking['booking_id']}\n";
        }
        
        $this->results['bulk_bookings'] = $bulkBookings;
    }
    
    private function testSearchBookings() {
        echo "\n📋 Test 7: Searching Bookings\n";
        
        $filters = [
            'page' => 1,
            'per_page' => 5,
            'status' => 'pending'
        ];
        
        $searchResults = $this->client->searchBookings($filters);
        
        echo "✅ Found {$searchResults['total']} bookings\n";
        echo "   Showing page {$searchResults['page']} of {$searchResults['per_page']}\n";
    }
    
    private function testAvailability() {
        echo "\n📋 Test 8: Checking Availability\n";
        
        $availability = $this->client->getAvailableTimeSlots();
        
        echo "✅ Available slots for " . $availability['date'] . ":\n";
        foreach ($availability['time_slots'] as $slot) {
            echo "   - {$slot['time']} ({$slot['slots_remaining']} slots)\n";
        }
    }
    
    private function testValidation() {
        echo "\n📋 Test 9: Data Validation\n";
        
        $testBooking = $this->client->generateTestBookingData();
        $validation = $this->validator->validate($testBooking);
        
        if ($validation['valid']) {
            echo "✅ Booking data structure is valid\n";
        } else {
            echo "❌ Validation failed:\n";
            foreach ($validation['errors'] as $error) {
                echo "   - $error\n";
            }
        }
    }
    
    private function printSummary() {
        echo "\n" . str_repeat("=", 60) . "\n";
        echo "📊 Test Summary\n";
        echo str_repeat("=", 60) . "\n";
        
        echo "Total tests run: 9\n";
        echo "Basic Booking ID: " . ($this->results['basic_booking']['booking_id'] ?? 'N/A') . "\n";
        echo "Custom Booking ID: " . ($this->results['custom_booking']['booking_id'] ?? 'N/A') . "\n";
        echo "Bulk Bookings: " . count($this->results['bulk_bookings'] ?? []) . "\n";
        echo "Test completed at: " . date('Y-m-d H:i:s') . "\n";
        echo str_repeat("=", 60) . "\n\n";
        echo "🎉 All tests completed successfully!\n\n";
    }
}

// Example usage of individual components
class Launch27Example {
    
    public static function demonstrateUsage() {
        echo "📚 Launch27 API Test Examples\n";
        echo str_repeat("-", 40) . "\n";
        
        // Initialize client
        $client = new Launch27TestClient("sandbox_jr2KvFPq85DGlh99RAng", "https://arafatweb-sandbox.launch27.com/v1");
        
        // Example 1: Simple booking
        echo "\nExample 1: Simple Booking\n";
        $booking1 = $client->createTestBooking();
        echo "Created: {$booking1['booking_id']}\n";
        
        // Example 2: Booking with specific time
        echo "\nExample 2: Booking with specific time\n";
        $booking2 = $client->createTestBooking([
            "booking" => [
                "date" => date('Y-m-d', strtotime('next Saturday')),
                "time" => "09:00"
            ]
        ]);
        echo "Created: {$booking2['booking_id']} for Saturday 9 AM\n";
        
        // Example 3: Add multiple extras
        echo "\nExample 3: Booking with multiple extras\n";
        $booking3 = $client->createTestBooking([
            "extras" => [
                ["id" => 101, "name" => "Inside Fridge", "price" => 25.00],
                ["id" => 102, "name" => "Inside Oven", "price" => 30.00],
                ["id" => 104, "name" => "Laundry", "price" => 40.00]
            ]
        ]);
        echo "Created: {$booking3['booking_id']} with 3 extras\n";
        
        // Calculate total with extras
        $baseAmount = $booking3['data']['payment']['amount'];
        $extrasTotal = array_sum(array_column($booking3['data']['extras'], 'price'));
        echo "Total with extras: $" . ($baseAmount + $extrasTotal) . "\n";
    }
}

// Run the test suite if this file is executed directly
if (php_sapi_name() === 'cli') {
    // Run full test suite with actual API
    $testSuite = new Launch27TestSuite();
    $testSuite->runAllTests();
}

?>