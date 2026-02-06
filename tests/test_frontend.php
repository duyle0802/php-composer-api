<?php
/**
 * Frontend Pages Testing Script
 */

$errors = [];
$tests_passed = 0;
$tests_failed = 0;

define('BASE_URL', 'http://localhost:8000');

// Pages to test
$pages = [
    'home' => ['page' => 'home'],
    'products' => ['page' => 'products'],
    'product-detail' => ['page' => 'product-detail', 'product_id' => 1],
    'cart' => ['page' => 'cart'],
    'checkout' => ['page' => 'checkout'],
    'login' => ['page' => 'login'],
    'register' => ['page' => 'register'],
    'about' => ['page' => 'about'],
    'contact' => ['page' => 'contact'],
    'profile' => ['page' => 'profile'],
    'orders' => ['page' => 'orders'],
    'admin' => ['page' => 'admin'],
];

echo "=========================================\n";
echo "BrightShop Frontend Pages Testing Script\n";
echo "=========================================\n\n";

foreach ($pages as $page_name => $params) {
    $query_string = http_build_query($params);
    $url = BASE_URL . '?' . $query_string;
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    // Check if page loaded successfully
    if ($http_code === 200) {
        echo "✅ $page_name (HTTP $http_code)\n";
        $tests_passed++;
        
        // Check for common HTML elements
        if (strpos($response, '<html') === false && strpos($response, '<!DOCTYPE') === false) {
            echo "   ⚠️  Warning: Page may not contain valid HTML\n";
        }
        
        // Check for errors in response
        if (stripos($response, 'fatal error') !== false) {
            echo "   ❌ FATAL ERROR FOUND IN PAGE\n";
            $errors[] = [
                'page' => $page_name,
                'type' => 'fatal_error',
                'message' => 'Fatal error detected in page output'
            ];
            $tests_failed++;
            $tests_passed--;
        }
        
        // Check for warnings
        if (stripos($response, 'warning') !== false && stripos($response, 'warning:') !== false) {
            echo "   ⚠️  Warning detected in page output\n";
        }
    } else if ($http_code === 404) {
        echo "❌ $page_name - PAGE NOT FOUND (HTTP 404)\n";
        $tests_failed++;
        $errors[] = [
            'page' => $page_name,
            'type' => 'not_found',
            'status_code' => $http_code
        ];
    } else {
        echo "❌ $page_name - HTTP $http_code\n";
        if ($error) {
            echo "   Error: $error\n";
        }
        $tests_failed++;
        $errors[] = [
            'page' => $page_name,
            'type' => 'http_error',
            'status_code' => $http_code,
            'error' => $error
        ];
    }
}

echo "\n=========================================\n";
echo "Frontend Test Summary\n";
echo "=========================================\n";
echo "✅ Tests Passed: $tests_passed\n";
echo "❌ Tests Failed: $tests_failed\n";
echo "Total Tests: " . ($tests_passed + $tests_failed) . "\n";
echo "Pass Rate: " . round(($tests_passed / ($tests_passed + $tests_failed)) * 100, 2) . "%\n";

if (count($errors) > 0) {
    echo "\n⚠️  ERRORS FOUND:\n";
    echo "-----------------------------------------\n";
    foreach ($errors as $error) {
        echo "Page: " . $error['page'] . "\n";
        echo "Error Type: " . $error['type'] . "\n";
        if (isset($error['status_code'])) {
            echo "Status: " . $error['status_code'] . "\n";
        }
        if (isset($error['message'])) {
            echo "Message: " . $error['message'] . "\n";
        }
        echo "-----------------------------------------\n";
    }
}

// Save results
$results = [
    'timestamp' => date('Y-m-d H:i:s'),
    'tests_passed' => $tests_passed,
    'tests_failed' => $tests_failed,
    'pass_rate' => round(($tests_passed / ($tests_passed + $tests_failed)) * 100, 2),
    'pages_tested' => count($pages),
    'errors' => $errors
];

file_put_contents('/var/www/html/PHPCom_APIver/frontend_test_results.json', json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "\n✅ Frontend test results saved to frontend_test_results.json\n";
