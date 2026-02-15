<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

$db = new Database();
$pdo = $db->connect();

// Fetch all products
$stmt = $pdo->query("SELECT id, name, image FROM products");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$blueColor = '2a5298'; // Project primary color
$textColor = 'ffffff';

$count = 0;
foreach ($products as $product) {
    if (empty($product['image'])) continue;
    
    $path = __DIR__ . '/../' . $product['image'];
    $dir = dirname($path);

    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    // Only download if file doesn't exist (or is the generic no-image placeholder we just made)
    if (!file_exists($path) || filesize($path) < 5000) { // Assuming generic placeholder is small or we want to overwrite it
        
        $text = urlencode($product['name']);
        // Shorten text if too long
        if (strlen($product['name']) > 20) {
            $text = urlencode(substr($product['name'], 0, 20) . '...');
        }
        
        $url = "https://placehold.co/600x600/$blueColor/$textColor/png?text=$text";
        
        echo "Downloading for: " . $product['name'] . "...\n";
        
        $imageData = file_get_contents($url);
        if ($imageData) {
            file_put_contents($path, $imageData);
            $count++;
            echo "Saved to $path\n";
        } else {
            echo "Failed to download for " . $product['name'] . "\n";
        }
        
        // Polite delay
        usleep(200000); // 200ms
    }
}

echo "Downloaded $count named images.\n";
