<?php

namespace App\Controllers;

class UploadController
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function upload()
    {
        // Check if user is admin
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            http_response_code(403);
            return json_encode(['success' => false, 'message' => 'Unauthorized']);
        }

        if (!isset($_FILES['image'])) {
            http_response_code(400);
            return json_encode(['success' => false, 'message' => 'No image uploaded']);
        }

        $file = $_FILES['image'];
        $fileName = $file['name'];
        $fileTmpName = $file['tmp_name'];
        $fileSize = $file['size'];
        $fileError = $file['error'];

        if ($fileError !== 0) {
            http_response_code(500);
            return json_encode(['success' => false, 'message' => 'File upload error code: ' . $fileError]);
        }

        // Validate extension
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($fileExt, $allowed)) {
            http_response_code(400);
            return json_encode(['success' => false, 'message' => 'Invalid file type. Allowed: ' . implode(', ', $allowed)]);
        }

        // Validate size (e.g., 5MB)
        if ($fileSize > 5 * 1024 * 1024) {
            http_response_code(400);
            return json_encode(['success' => false, 'message' => 'File too large. Max 5MB.']);
        }

        // Generate unique name to prevent overwriting
        $newFileName = uniqid('IMG_', true) . '.' . $fileExt;
        
        // Target directory relative to public/api.php execution context? 
        // We need absolute path for move_uploaded_file
        // __DIR__ is app/controllers. We need /var/www/html/PHPCom_APIver/public/products/main_image
        // Root dir is __DIR__ . '/../../'
        
        $uploadDir = __DIR__ . '/../../public/products/main_image/';
        
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $destination = $uploadDir . $newFileName;

        if (move_uploaded_file($fileTmpName, $destination)) {
            // Return path relative to project root for DB storage
            // Our frontend uses BASE_URL + path. 
            // If we store 'public/products/main_image/foo.jpg', and BASE_URL points to root, it works.
            
            $relativePath = 'public/products/main_image/' . $newFileName;
            
            http_response_code(200);
            return json_encode([
                'success' => true, 
                'message' => 'Upload successful',
                'path' => $relativePath,
                'url' => BASE_URL . '/' . $relativePath 
            ]);
        } else {
            http_response_code(500);
            return json_encode(['success' => false, 'message' => 'Failed to move uploaded file']);
        }
    }
}
