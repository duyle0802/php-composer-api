<?php
// Create placeholder images for banners
$width = 1200;
$height = 400;

// Create images based on parameter
$image = imagecreatetruecolor($width, $height);

// Get which banner (1, 2, or 3)
$banner = isset($_GET['banner']) ? $_GET['banner'] : 1;

// Set colors
$white = imagecolorallocate($image, 255, 255, 255);
$darkBlue = imagecolorallocate($image, 0, 102, 204);
$darkGreen = imagecolorallocate($image, 0, 153, 76);
$darkRed = imagecolorallocate($image, 204, 0, 0);
$black = imagecolorallocate($image, 0, 0, 0);

// Create gradients and text based on banner number
switch($banner) {
    case 1:
        // Banner 1 - Tech Blue
        imagefilledrectangle($image, 0, 0, $width, $height, $darkBlue);
        imagestring($image, 5, 50, 180, 'CONG NGHE HANG DAU', $white);
        break;
    case 2:
        // Banner 2 - Sales Green
        imagefilledrectangle($image, 0, 0, $width, $height, $darkGreen);
        imagestring($image, 5, 50, 180, 'GIA CANH TRANH', $white);
        break;
    case 3:
        // Banner 3 - Quality Red
        imagefilledrectangle($image, 0, 0, $width, $height, $darkRed);
        imagestring($image, 5, 50, 180, 'CHAT LUONG DAM BAO', $white);
        break;
}

// Output image
header('Content-Type: image/jpeg');
imagejpeg($image);
imagedestroy($image);
?>
