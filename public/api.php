<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

// Load autoloader (nếu sử dụng Composer)
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

// Manual autoloader cho models và controllers
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../app/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    
    // Split by backslash and convert first part (folder) to lowercase
    $parts = explode('\\', $relative_class);
    $parts[0] = strtolower($parts[0]);
    $file = $base_dir . implode('/', $parts) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// Initialize Database
$db = new Database();
$pdo = $db->connect();

// Get request path
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Handle both Apache and PHP built-in server routing
if (strpos($request_uri, '/PHPCom_APIver/api/') !== false) {
    // Apache routing
    $request_uri = str_replace('/PHPCom_APIver/api/', '', $request_uri);
} elseif (strpos($request_uri, '/api/') !== false) {
    // PHP built-in server routing
    $request_uri = str_replace('/api/', '', $request_uri);
}

// Remove leading and trailing slashes
$request_uri = trim($request_uri, '/');

// Route handling
$routes = [
    // Auth routes
    'auth/register' => ['method' => 'POST', 'controller' => 'AuthController', 'action' => 'register'],
    'auth/login' => ['method' => 'POST', 'controller' => 'AuthController', 'action' => 'login'],
    'auth/logout' => ['method' => 'POST', 'controller' => 'AuthController', 'action' => 'logout'],
    'auth/profile' => ['method' => 'GET', 'controller' => 'AuthController', 'action' => 'getProfile'],
    'auth/logout' => ['method' => 'POST', 'controller' => 'AuthController', 'action' => 'logout'],
    'auth/profile' => ['method' => 'GET', 'controller' => 'AuthController', 'action' => 'getProfile'],
    'auth/check' => ['method' => 'GET', 'controller' => 'AuthController', 'action' => 'checkAuth'],
    'auth/test-login' => ['method' => 'GET', 'controller' => 'AuthController', 'action' => 'testLogin'],
    
    // Product routes
    'products' => ['method' => 'GET', 'controller' => 'ProductController', 'action' => 'getAll'],
    'products/featured' => ['method' => 'GET', 'controller' => 'ProductController', 'action' => 'getFeatured'],
    'products/search' => ['method' => 'GET', 'controller' => 'ProductController', 'action' => 'searchAndFilter'],
    'products/detail' => ['method' => 'GET', 'controller' => 'ProductController', 'action' => 'getById'],
    'products/create' => ['method' => 'POST', 'controller' => 'ProductController', 'action' => 'create'],
    'products/update' => ['method' => 'POST', 'controller' => 'ProductController', 'action' => 'update'],
    'products/delete' => ['method' => 'GET', 'controller' => 'ProductController', 'action' => 'delete'],
    
    // Category routes
    'categories' => ['method' => 'GET', 'controller' => 'CategoryController', 'action' => 'getAll'],
    'categories/detail' => ['method' => 'GET', 'controller' => 'CategoryController', 'action' => 'getById'],
    'categories/create' => ['method' => 'POST', 'controller' => 'CategoryController', 'action' => 'create'],
    'categories/update' => ['method' => 'POST', 'controller' => 'CategoryController', 'action' => 'update'],
    'categories/delete' => ['method' => 'GET', 'controller' => 'CategoryController', 'action' => 'delete'],
    
    // Cart routes
    'cart/add' => ['method' => 'POST', 'controller' => 'CartController', 'action' => 'addItem'],
    'cart/items' => ['method' => 'GET', 'controller' => 'CartController', 'action' => 'getItems'],
    'cart/update' => ['method' => 'POST', 'controller' => 'CartController', 'action' => 'updateQuantity'],
    'cart/remove' => ['method' => 'GET', 'controller' => 'CartController', 'action' => 'removeItem'],
    'cart/clear' => ['method' => 'POST', 'controller' => 'CartController', 'action' => 'clearCart'],
    'cart/total' => ['method' => 'GET', 'controller' => 'CartController', 'action' => 'getTotal'],
    'cart/count' => ['method' => 'GET', 'controller' => 'CartController', 'action' => 'getItemCount'],
    
    // Order routes
    'orders/create' => ['method' => 'POST', 'controller' => 'OrderController', 'action' => 'createOrder'],
    'orders/calculate-shipping' => ['method' => 'POST', 'controller' => 'OrderController', 'action' => 'calculateShipping'],
    'orders/detail' => ['method' => 'GET', 'controller' => 'OrderController', 'action' => 'getOrder'],
    'orders/user' => ['method' => 'GET', 'controller' => 'OrderController', 'action' => 'getUserOrders'],
    'orders/all' => ['method' => 'GET', 'controller' => 'OrderController', 'action' => 'getAllOrders'],
    'orders/status' => ['method' => 'POST', 'controller' => 'OrderController', 'action' => 'updateOrderStatus'],
    'payment/momo-ipn' => ['method' => 'POST', 'controller' => 'OrderController', 'action' => 'handleMoMoCallback'],
    'payment/momo-simulate' => ['method' => 'GET', 'controller' => 'OrderController', 'action' => 'simulateMoMoPayment'],
    'payment/momo-return' => ['method' => 'GET', 'controller' => 'OrderController', 'action' => 'handleMoMoReturn'],
    
    // User routes
    'users/all' => ['method' => 'GET', 'controller' => 'UserController', 'action' => 'getAllUsers'],
    'users/detail' => ['method' => 'GET', 'controller' => 'UserController', 'action' => 'getUserById'],
    'users/update' => ['method' => 'POST', 'controller' => 'UserController', 'action' => 'updateUser'],
    'users/ban' => ['method' => 'GET', 'controller' => 'UserController', 'action' => 'banUser'],
    'users/unban' => ['method' => 'GET', 'controller' => 'UserController', 'action' => 'unbanUser'],
    'users/delete' => ['method' => 'GET', 'controller' => 'UserController', 'action' => 'deleteUser'],

    // Address routes
    'address/add' => ['method' => 'POST', 'controller' => 'AddressController', 'action' => 'addAddress'],
    'address/list' => ['method' => 'GET', 'controller' => 'AddressController', 'action' => 'getAddresses'],
    
    // Contact routes
    'contact/send' => ['method' => 'POST', 'controller' => 'ContactController', 'action' => 'send'],

    // Admin Dashboard routes
    'admin/stats' => ['method' => 'GET', 'controller' => 'AdminController', 'action' => 'getDashboardStats'],
    'admin/charts' => ['method' => 'GET', 'controller' => 'AdminController', 'action' => 'getChartData'],

    // Upload routes
    'upload/image' => ['method' => 'POST', 'controller' => 'UploadController', 'action' => 'upload'],
];

// Process request
$route_found = false;

foreach ($routes as $route => $route_config) {
    // Check for exact match or match with query string
    $route_match = ($request_uri === $route) || (strpos($request_uri, $route . '?') === 0);
    
    if ($route_match && $_SERVER['REQUEST_METHOD'] === $route_config['method']) {
        $controller_class = 'App\\Controllers\\' . $route_config['controller'];
        
        if (class_exists($controller_class)) {
            $action = $route_config['action'];
            $controller = new $controller_class($pdo);
            header('Content-Type: application/json');
            echo $controller->$action();
            $route_found = true;
            break;
        }
    }
}

if (!$route_found) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Route not found']);
}
