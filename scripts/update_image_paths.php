<?php
require_once __DIR__ . '/../config/config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Connected to database.\n";

    // 1. Fetch all products
    $stmt = $pdo->query("SELECT id, image FROM products");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Found " . count($products) . " products.\n";

    $updatedCount = 0;
    $skippedCount = 0;

    $stmtUpdate = $pdo->prepare("UPDATE products SET image = :image WHERE id = :id");

    foreach ($products as $product) {
        $id = $product['id'];
        $currentImage = $product['image'];
        $newImage = $currentImage;
        $needsUpdate = false;

        // Check if already correct
        if (strpos($currentImage, 'public/images/products_image/') === 0) {
            $skippedCount++;
            continue;
        }

        // If it's a URL, skip it
        if (filter_var($currentImage, FILTER_VALIDATE_URL)) {
             echo "Skipping URL: $currentImage (ID: $id)\n";
             $skippedCount++;
             continue;
        }
        
        // If it's just a filename (e.g. image.jpg) -> prepend
        // If it has a partial path (e.g. /images/image.jpg) -> tricky but user asked for specific path.
        // Assuming simple filename or partial path we want to fix.
        
        // If it starts with slash, remove it first
        $cleanImage = ltrim($currentImage, '/');
        
        // Final path
        $newImage = 'public/images/products_image/' . basename($cleanImage);
        
        if ($newImage !== $currentImage) {
            $stmtUpdate->execute([':image' => $newImage, ':id' => $id]);
            $updatedCount++;
            // echo "Updated ID $id: $currentImage -> $newImage\n";
        } else {
            $skippedCount++;
        }
    }

    echo "Update complete.\n";
    echo "Updated: $updatedCount products.\n";
    echo "Skipped: $skippedCount products.\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
