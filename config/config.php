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
            // Only set if not already set (respect Docker env vars)
            if (getenv($key) === false) {
                putenv("$key=$value");
                $_ENV[$key] = $value;
            }
        }
    }
}

// Start session
session_start();

// Define base URL dynamically
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$base_path = '';

// For PHP built-in server (localhost:8000) or Docker (localhost:8080)
if ($host === 'localhost:8000' || $host === 'localhost:8080') {
    $base_path = '';
} else {
    // For Apache (localhost/PHPCom_APIver)
    $base_path = '/PHPCom_APIver';
}

define('BASE_URL', $protocol . '://' . $host . $base_path);
define('API_URL', BASE_URL . '/api');

// Database configuration
// Database configuration
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'Bright_Database');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASSWORD') ?: '');

// Email configuration for contact form
define('ADMIN_EMAIL', getenv('ADMIN_EMAIL') ?: 'admin@brightshop.com');
define('ADMIN_NAME', getenv('ADMIN_NAME') ?: 'BrightShop Admin');

// Email Configuration (SMTP)
define('SMTP_HOST', getenv('SMTP_HOST') ?: 'smtp.gmail.com');
define('SMTP_PORT', getenv('SMTP_PORT') ?: 587);
define('SMTP_USER', getenv('SMTP_USER') ?: '');
define('SMTP_PASS', getenv('SMTP_PASS') ?: '');
define('SMTP_SECURE', getenv('SMTP_SECURE') ?: 'tls');
define('SMTP_FROM_EMAIL', getenv('SMTP_FROM_EMAIL') ?: 'no-reply@brightshop.com');
define('SMTP_FROM_NAME', getenv('SMTP_FROM_NAME') ?: 'BrightShop');

// MoMo Payment Configuration
define('MOMO_PARTNER_CODE', getenv('MOMO_PARTNER_CODE') ?: '');
define('MOMO_ACCESS_KEY', getenv('MOMO_ACCESS_KEY') ?: '');
define('MOMO_SECRET_KEY', getenv('MOMO_SECRET_KEY') ?: '');
define('MOMO_API_ENDPOINT', getenv('MOMO_API_ENDPOINT') ?: 'https://test-payment.momo.vn/v2/gateway/api/create');
define('MOMO_NOTIFY_URL', getenv('MOMO_NOTIFY_URL') ?: BASE_URL . '/payment/momo-ipn');
define('MOMO_RETURN_URL', getenv('MOMO_RETURN_URL') ?: BASE_URL . '/payment/momo-return');

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
