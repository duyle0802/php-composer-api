<?php
/**
 * Test Protected Pages with Session
 */

$cookie_file = '/tmp/test_cookies.txt';

// Clear old cookies
@unlink($cookie_file);

echo "=========================================\n";
echo "Testing Protected Pages with Login\n";
echo "=========================================\n\n";

// Step 1: Login
echo "Step 1: Login as admin...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/api/auth/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'username' => 'admin',
    'password' => 'Admin@123'
]));
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code === 200) {
    echo "✅ Login successful\n";
} else {
    echo "❌ Login failed (HTTP $http_code)\n";
    echo "Response: " . substr($response, 0, 100) . "\n";
    exit;
}

// Step 2: Test protected pages
echo "\nStep 2: Testing protected pages...\n";

$pages = [
    'checkout' => 'Checkout page (requires login)',
    'admin' => 'Admin dashboard (requires admin role)',
    'profile' => 'User profile page',
    'orders' => 'User orders page'
];

$passed = 0;
$failed = 0;

foreach ($pages as $page => $description) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/?page=' . $page);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code === 200) {
        echo "✅ $page - HTTP 200 - $description\n";
        $passed++;
    } else {
        echo "⚠️  $page - HTTP $http_code - $description\n";
        if ($http_code === 403) {
            echo "   (HTTP 403 = Permission denied, expected for non-admin)\n";
        }
        $failed++;
    }
}

// Step 3: Summary
echo "\n=========================================\n";
echo "Protected Pages Test Summary\n";
echo "=========================================\n";
echo "✅ Loaded with 200: $passed\n";
echo "⚠️  Other status codes: $failed\n";

// Cleanup
@unlink($cookie_file);

echo "\nNote: HTTP 403 (Forbidden) is expected for admin page if user is not admin\n";
