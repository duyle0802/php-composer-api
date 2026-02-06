<?php

// Load environment variables from .env file
$env_file = __DIR__ . '/../.env';
if (file_exists($env_file)) {
    $lines = file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            putenv("$key=$value");
            $_ENV[$key] = $value;
        }
    }
}

// Start session
session_start();

// Define base URL dynamically
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$base_path = '';

// For PHP built-in server (localhost:8000)
if ($host === 'localhost:8000') {
    $base_path = '';
} else {
    // For Apache (localhost/PHPCom_APIver)
    $base_path = '/PHPCom_APIver';
}

define('BASE_URL', $protocol . '://' . $host . $base_path);
define('API_URL', BASE_URL . '/api');

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'Bright_Database');
define('DB_USER', 'root');
define('DB_PASS', 'Leduy0924A@');

// Email configuration for contact form
define('ADMIN_EMAIL', 'admin@brightshop.com');
define('ADMIN_NAME', 'BrightShop Admin');

// Pagination
define('ITEMS_PER_PAGE', 9);

// Store Location (Quang Trung Software City, Q12, HCMC)
define('STORE_LAT', 10.8553677);
define('STORE_LNG', 106.6300405);

// Google Maps API Key
define('GOOGLE_MAPS_API_KEY', 'YOUR_GOOGLE_MAPS_API_KEY');

// Shipping configuration
define('SHIPPING_FEE_UNDER_100KM', 0);
define('SHIPPING_FEE_100_250KM', 50000);
define('SHIPPING_FEE_BASE_OVER_250KM', 50000);
define('SHIPPING_FEE_STEP_OVER_250KM', 10000);
define('SHIPPING_STEP_DISTANCE', 10);

// Session timeout
define('SESSION_TIMEOUT', 1800); // 30 minutes
