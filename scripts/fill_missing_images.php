<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

$db = new Database();
$pdo = $db->connect();

$stmt = $pdo->query("SELECT id, name, image FROM products");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$placeholder = __DIR__ . '/../public/images/products_image/no-image.png';
if (!file_exists($placeholder)) {
    die("Placeholder image not found at $placeholder\n");
}

$count = 0;
foreach ($products as $product) {
    if (empty($product['image'])) continue;
    
    $path = __DIR__ . '/../' . $product['image'];
    $dir = dirname($path);

    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    if (!file_exists($path)) {
        echo "Missing: " . $product['name'] . " -> Copying placeholder...\n";
        copy($placeholder, $path);
        $count++;
    }
}

echo "Filled $count missing images with placeholder.\n";
