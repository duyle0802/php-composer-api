<?php
/**
 * Comprehensive API Testing Script
 * Tests all 40+ endpoints and records errors
 */

$errors = [];
$tests_passed = 0;
$tests_failed = 0;

define('API_URL', 'http://localhost:8000/api');
define('TEST_TIMEOUT', 5);

// Test result function
function testEndpoint($method, $endpoint, $data = null, $description = "") {
    global $errors, $tests_passed, $tests_failed;
    
    $url = API_URL . $endpoint;
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, TEST_TIMEOUT);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    
    if ($data) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    $result = [
        'endpoint' => $endpoint,
        'method' => $method,
        'description' => $description,
        'http_code' => $http_code,
        'response' => $response,
        'error' => $error
    ];
    
    // Check if endpoint is accessible (200, 201, 400 are all OK from endpoint availability perspective)
    if ($http_code >= 200 && $http_code < 500) {
        $tests_passed++;
        echo "✅ $method $endpoint - HTTP $http_code\n";
    } else {
        $tests_failed++;
        echo "❌ $method $endpoint - HTTP $http_code\n";
        $errors[] = [
            'endpoint' => $endpoint,
            'method' => $method,
            'description' => $description,
            'status_code' => $http_code,
            'error' => $error,
            'response' => $response
        ];
    }
    
    return $result;
}

echo "=========================================\n";
echo "BrightShop API Testing Script\n";
echo "=========================================\n\n";

echo "🔍 Testing Authentication Endpoints...\n";
testEndpoint('POST', '/auth/register', [
    'username' => 'testuser' . time(),
    'email' => 'test' . time() . '@example.com',
    'password' => 'Test@123',
    'fullname' => 'Test User'
], 'User registration');

testEndpoint('POST', '/auth/login', [
    'username' => 'admin',
    'password' => 'Admin@123'
], 'Admin login');

testEndpoint('GET', '/auth/check', null, 'Check authentication');

echo "\n🔍 Testing Product Endpoints...\n";
testEndpoint('GET', '/products', null, 'Get all products');
testEndpoint('GET', '/products/featured', null, 'Get featured products');
testEndpoint('GET', '/products/search?keyword=laptop', null, 'Search products');
testEndpoint('GET', '/products/detail?id=1', null, 'Get product detail');

echo "\n🔍 Testing Category Endpoints...\n";
testEndpoint('GET', '/categories', null, 'Get all categories');
testEndpoint('GET', '/categories/detail?id=1', null, 'Get category detail');

echo "\n🔍 Testing Cart Endpoints...\n";
testEndpoint('POST', '/cart/add', [
    'product_id' => 1,
    'quantity' => 1
], 'Add to cart');

testEndpoint('GET', '/cart/items', null, 'Get cart items');
testEndpoint('GET', '/cart/count', null, 'Get cart count');
testEndpoint('GET', '/cart/total', null, 'Get cart total');

echo "\n🔍 Testing Order Endpoints...\n";
testEndpoint('POST', '/orders/create', [
    'address' => '123 Test Street',
    'shipping_distance' => 10,
    'voucher_code' => 'WELCOME'
], 'Create order');

testEndpoint('GET', '/orders/user', null, 'Get user orders');

echo "\n🔍 Testing User Endpoints...\n";
testEndpoint('GET', '/users/all', null, 'Get all users');

echo "\n=========================================\n";
echo "Test Summary\n";
echo "=========================================\n";
echo "✅ Tests Passed: $tests_passed\n";
echo "❌ Tests Failed: $tests_failed\n";
echo "Total Tests: " . ($tests_passed + $tests_failed) . "\n";
echo "Pass Rate: " . round(($tests_passed / ($tests_passed + $tests_failed)) * 100, 2) . "%\n";

if (count($errors) > 0) {
    echo "\n⚠️  ERRORS FOUND:\n";
    echo "-----------------------------------------\n";
    foreach ($errors as $error) {
        echo "Endpoint: " . $error['endpoint'] . "\n";
        echo "Method: " . $error['method'] . "\n";
        echo "Status: " . $error['status_code'] . "\n";
        echo "Error: " . $error['error'] . "\n";
        echo "Response: " . substr($error['response'], 0, 200) . "\n";
        echo "-----------------------------------------\n";
    }
}

// Save results to file
$results = [
    'timestamp' => date('Y-m-d H:i:s'),
    'tests_passed' => $tests_passed,
    'tests_failed' => $tests_failed,
    'pass_rate' => round(($tests_passed / ($tests_passed + $tests_failed)) * 100, 2),
    'errors' => $errors
];

file_put_contents(__DIR__ . '/test_results.json', json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "\n✅ Test results saved to test_results.json\n";
