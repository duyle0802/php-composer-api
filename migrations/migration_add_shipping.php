<?php
// Manual .env loader for CLI scripts
if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        putenv(trim($name) . '=' . trim($value));
    }
}

require_once __DIR__ . '/../config/database.php';

echo "Running migration: Add 'shipping' status to orders table...\n";

try {
    $db = new Database();
    $pdo = $db->connect();

    // Check if 'shipping' is already in the enum (optional, but good for idempotency)
    // Actually ALTER TABLE on ENUM is safe to re-run if it matches.
    
    $query = "ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'confirmed', 'shipping', 'completed', 'cancelled') DEFAULT 'pending'";
    
    $pdo->exec($query);
    
    echo "✅ Successfully updated 'status' enum in 'orders' table.\n";
    
} catch (Exception $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
