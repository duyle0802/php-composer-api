<?php
require_once '/var/www/html/PHPCom_APIver/config/config.php';
require_once '/var/www/html/PHPCom_APIver/config/database.php';

spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = '/var/www/html/PHPCom_APIver/app/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    
    // Split by backslash and convert first part (folder) to lowercase
    $parts = explode('\\', $relative_class);
    $parts[0] = strtolower($parts[0]);
    $file = $base_dir . implode('/', $parts) . '.php';
    
    echo "Looking for: $file\n";
    if (file_exists($file)) {
        require $file;
        echo "Loaded OK\n";
    } else {
        echo "NOT FOUND\n";
    }
});

if (class_exists('App\\Controllers\\ProductController')) {
    echo "✅ CLASS EXISTS\n";
} else {
    echo "❌ CLASS NOT FOUND\n";
}
