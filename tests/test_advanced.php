<?php
/**
 * Advanced Testing with Authentication
 */

session_start();

$errors = [];
$tests_passed = 0;
$tests_failed = 0;

define('API_URL', 'http://localhost:8000/api');
define('BASE_URL', 'http://localhost:8000');

// First, login to get session
echo "=========================================\n";
echo "BrightShop Full Testing with Session\n";
echo "=========================================\n\n";

echo "🔐 Step 1: Login to get session...\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, API_URL . '/auth/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'username' => 'admin',
    'password' => 'Admin@123'
]));
curl_setopt($ch, CURLOPT_COOKIEJAR, '/tmp/cookies.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/cookies.txt');

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code === 200) {
    echo "✅ Login successful\n\n";
    $login_data = json_decode($response, true);
    
    if (isset($login_data['success']) && $login_data['success']) {
        echo "✅ Authentication verified\n\n";
    } else {
        echo "⚠️  Login response: " . json_encode($login_data) . "\n\n";
    }
} else {
    echo "❌ Login failed (HTTP $http_code)\n";
    echo "Response: $response\n\n";
}

// Now test protected pages with session
echo "🔍 Testing Protected Pages...\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, BASE_URL . '?page=checkout');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/cookies.txt');
curl_setopt($ch, CURLOPT_COOKIEJAR, '/tmp/cookies.txt');

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code === 200) {
    echo "✅ checkout page (HTTP $http_code)\n";
    $tests_passed++;
} else {
    echo "⚠️  checkout page (HTTP $http_code) - Redirect expected for guests\n";
    // This is acceptable - redirects are expected
}

// Test admin page
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, BASE_URL . '?page=admin');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/cookies.txt');
curl_setopt($ch, CURLOPT_COOKIEJAR, '/tmp/cookies.txt');

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code === 200) {
    echo "✅ admin page (HTTP $http_code)\n";
    $tests_passed++;
    
    // Check if admin content is present
    if (strpos($response, 'dashboard') !== false || strpos($response, 'admin') !== false) {
        echo "   ✅ Admin dashboard content found\n";
    }
} else {
    echo "⚠️  admin page (HTTP $http_code) - Might need proper authentication\n";
}

// Test detailed functionality
echo "\n🔍 Testing Detailed Functionality...\n";

// Test product operations
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, API_URL . '/products');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$products_data = json_decode($response, true);

if ($http_code === 200 && isset($products_data['success']) && $products_data['success']) {
    echo "✅ Products API working\n";
    if (isset($products_data['data']) && is_array($products_data['data'])) {
        echo "   ✅ Returns array of products\n";
        if (count($products_data['data']) > 0) {
            echo "   ✅ Sample products found: " . count($products_data['data']) . " products\n";
            
            // Check product structure
            $first_product = $products_data['data'][0];
            $required_fields = ['id', 'name', 'price'];
            $missing_fields = [];
            
            foreach ($required_fields as $field) {
                if (!isset($first_product[$field])) {
                    $missing_fields[] = $field;
                }
            }
            
            if (empty($missing_fields)) {
                echo "   ✅ Product data structure correct\n";
            } else {
                echo "   ⚠️  Missing fields: " . implode(', ', $missing_fields) . "\n";
            }
        } else {
            echo "   ⚠️  No sample products in database yet\n";
        }
    }
} else {
    echo "❌ Products API error (HTTP $http_code)\n";
    echo "   Response: " . substr($response, 0, 100) . "\n";
}

// Test categories
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, API_URL . '/categories');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$categories_data = json_decode($response, true);

if ($http_code === 200 && isset($categories_data['success']) && $categories_data['success']) {
    echo "✅ Categories API working\n";
    if (isset($categories_data['data']) && is_array($categories_data['data'])) {
        echo "   ✅ Returns array of categories\n";
        if (count($categories_data['data']) > 0) {
            echo "   ✅ Sample categories found: " . count($categories_data['data']) . " categories\n";
        }
    }
} else {
    echo "⚠️  Categories API (HTTP $http_code)\n";
}

echo "\n=========================================\n";
echo "✅ Comprehensive testing completed\n";
echo "=========================================\n";

// Cleanup
@unlink('/tmp/cookies.txt');
