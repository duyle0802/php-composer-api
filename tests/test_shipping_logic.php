<?php
// Mock Session
$_SESSION = [
    'user_id' => 1,
    'role' => 'admin'
];
$_SERVER['REQUEST_METHOD'] = 'POST';

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

// Autoload logic
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relative_class = substr($class, $len);
    $parts = explode('\\', $relative_class);
    $parts[0] = strtolower($parts[0]);
    $file = $base_dir . implode('/', $parts) . '.php';
    if (file_exists($file)) require $file;
});

echo "--- START VERIFICATION ---\n";

$db = new Database();
$pdo = $db->connect();

// 1. Test Address Creation (mocked geocode)
echo "\n1. Testing Address Creation...\n";
$addressController = new \App\Controllers\AddressController($pdo);

// Mock Input for Add Address
$addressInput = json_encode([
    'address_line' => 'Test Address ' . rand(1000, 9999), 
    'is_default' => 1
]);
// Capture output
ob_start();
// Inject input using a temporary file wrapper or just redefining file_get_contents doesn't work easily.
// Instead we'll modify the controller to accept data or just use a helper.
// But controller reads php://input. 
// Workaround: We can't easily mock php://input for the controller without a wrapper.
// So let's test the Model directly for creation and logic, or just instantiate controller and manually invoke logic if possible.
// Actually, I can test the logic:
$userAddressModel = new \App\Models\UserAddress($pdo);
// Create address manually with known lat/long
$lat = 10.8553677; // Store Lat
$lng = 106.6300405; // Store Lng (0 distance)
$addressId1 = $userAddressModel->create(1, "Store Location (0km)", $lat, $lng, 1);
echo "Created Address ID $addressId1 (0km)\n";

// Create address 150km away roughly
// 1 deg lat ~ 111km. 
$lat2 = 10.8553677 + (1.5); // +1.5 deg ~ 166km
$lng2 = 106.6300405;
$addressId2 = $userAddressModel->create(1, "Far Location (150km)", $lat2, $lng2, 0);
echo "Created Address ID $addressId2 (~166km)\n";

// Create address 300km away
$lat3 = 10.8553677 + (2.8); // +2.8 deg ~ 310km
$lng3 = 106.6300405;
$addressId3 = $userAddressModel->create(1, "Very Far Location (300km)", $lat3, $lng3, 0);
echo "Created Address ID $addressId3 (~310km)\n";


// 2. Test Shipping Calculation
echo "\n2. Testing Shipping Calculation...\n";
$orderController = new \App\Controllers\OrderController($pdo);

// Allow access to private/protected methods via reflection or just call the public endpoint logic?
// I'll call the public endpoint logic by mocking input stream? No, that's hard.
// I will temporarily make calculateMethod public or copy logic? NO.
// I can just call methods if I modify the controller or use Reflection.
// ReflectionMethod to test private method calculateDistance and calculateShippingFee.

$reflectionDistance = new ReflectionMethod(\App\Controllers\OrderController::class, 'calculateDistance');
$reflectionDistance->setAccessible(true);

$reflectionFee = new ReflectionMethod(\App\Controllers\OrderController::class, 'calculateShippingFee');
$reflectionFee->setAccessible(true);

// Test 0km
$dist1 = $reflectionDistance->invoke($orderController, $lat, $lng, $lat, $lng);
$fee1 = $reflectionFee->invoke($orderController, $dist1);
echo "Dist 1: " . round($dist1, 2) . "km - Fee: " . number_format($fee1) . " (Expected: 0)\n";

// Test 150km
$dist2 = $reflectionDistance->invoke($orderController, $lat, $lng, $lat2, $lng2);
$fee2 = $reflectionFee->invoke($orderController, $dist2);
echo "Dist 2: " . round($dist2, 2) . "km - Fee: " . number_format($fee2) . " (Expected: 50,000)\n";

// Test 300km
$dist3 = $reflectionDistance->invoke($orderController, $lat, $lng, $lat3, $lng3);
$fee3 = $reflectionFee->invoke($orderController, $dist3);
// > 250km cost: 50k + (floor((dist-250)/10) * 10k)
// Expected: 50,000 + (floor((310-250)/10) * 10,000) = 50k + (6 * 10k) = 110k
echo "Dist 3: " . round($dist3, 2) . "km - Fee: " . number_format($fee3) . " (Expected: ~110,000 depending on exact dist)\n";

echo "\n--- VERIFICATION COMPLETE ---\n";
