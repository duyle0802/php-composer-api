<?php
/**
 * Router for PHP built-in server
 * Place this at root and run: php -S localhost:8000 router.php
 */

$requested_uri = $_SERVER['REQUEST_URI'];

// Check for files in public first
$requested_file = __DIR__ . '/public' . $requested_uri;
if (is_file($requested_file) || is_dir($requested_file)) {
    return false;
}

// Check for assets and other static files in root
if (is_file(__DIR__ . $requested_uri) && preg_match('/\.(js|css|jpg|jpeg|png|gif|ico|svg|woff|woff2|ttf|eot)$/i', $requested_uri)) {
    return false;
}

// Route API requests
if (strpos($requested_uri, '/api/') === 0) {
    include __DIR__ . '/public/api.php';
    return;
}

// Route all other requests to index.php
include __DIR__ . '/public/index.php';
