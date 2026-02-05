<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

// Initialize Database
$db = new Database();
$pdo = $db->connect();

// Get page
$page = isset($_GET['page']) ? basename($_GET['page']) : 'home';

// Whitelist allowed pages
$allowed_pages = ['home', 'products', 'product-detail', 'about', 'contact', 'login', 'register', 'cart', 'checkout', 'admin', 'order-confirmation'];

if (!in_array($page, $allowed_pages)) {
    $page = 'home';
}

// Check if user is authenticated
$is_authenticated = isset($_SESSION['user_id']);
$user_role = $_SESSION['role'] ?? null;
$user_id = $_SESSION['user_id'] ?? null;
$username = $_SESSION['username'] ?? null;
$full_name = $_SESSION['full_name'] ?? null;

// Redirect admin to admin page
if ($page === 'home' && $is_authenticated && $user_role === 'admin') {
    header('Location: ' . BASE_URL . '/?page=admin');
    exit;
}

// Protect admin page
if ($page === 'admin' && (!$is_authenticated || $user_role !== 'admin')) {
    header('Location: ' . BASE_URL . '/?page=login');
    exit;
}

// Protect checkout page
if ($page === 'checkout' && !$is_authenticated) {
    header('Location: ' . BASE_URL . '/?page=login');
    exit;
}

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BrightShop - Cửa hàng điện tử hàng đầu</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container">
                <a class="navbar-brand" href="<?php echo BASE_URL; ?>/?page=home">
                    <i class="fas fa-lightbulb"></i> BrightShop
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>/?page=home">Trang chủ</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>/?page=products">Sản phẩm</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>/?page=about">Về chúng tôi</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>/?page=contact">Liên hệ</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>/?page=cart">
                                <i class="fas fa-shopping-cart"></i> Giỏ hàng
                                <span class="badge bg-danger" id="cart-count" style="display: none;">0</span>
                            </a>
                        </li>
                        <?php if ($is_authenticated): ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-user"></i> Xin chào, <?php echo htmlspecialchars($full_name ?: $username); ?>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/?page=profile">Hồ sơ của tôi</a></li>
                                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/?page=orders">Đơn hàng của tôi</a></li>
                                    <?php if ($user_role === 'admin'): ?>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/?page=admin">Quản lý Admin</a></li>
                                    <?php endif; ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="#" onclick="logout()">Đăng xuất</a></li>
                                </ul>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo BASE_URL; ?>/?page=login">Đăng nhập</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <?php
        include_once __DIR__ . '/../app/views/' . $page . '.php';
        ?>
    </main>

    <!-- Footer -->
    <footer class="footer bg-dark text-white mt-5 py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5>Về BrightShop</h5>
                    <p>BrightShop là cửa hàng điện tử hàng đầu, cung cấp các sản phẩm điện tử chất lượng cao với giá cạnh tranh.</p>
                </div>
                <div class="col-md-4 mb-4">
                    <h5>Liên kết nhanh</h5>
                    <ul class="list-unstyled">
                        <li><a href="<?php echo BASE_URL; ?>/?page=home" class="text-white-50 text-decoration-none">Trang chủ</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/?page=products" class="text-white-50 text-decoration-none">Sản phẩm</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/?page=about" class="text-white-50 text-decoration-none">Về chúng tôi</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/?page=contact" class="text-white-50 text-decoration-none">Liên hệ</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-4">
                    <h5>Thông tin liên lạc</h5>
                    <p class="text-white-50">
                        Email: <?php echo ADMIN_EMAIL; ?><br>
                        Điện thoại: 1900-xxxx<br>
                        Địa chỉ: TP. Hồ Chí Minh, Việt Nam
                    </p>
                </div>
            </div>
            <hr class="bg-white-50">
            <div class="text-center">
                <p class="text-white-50 mb-0">&copy; 2026 BrightShop. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/common.js"></script>
    <script>
        // Initialize cart count
        updateCartCount();
    </script>
</body>
</html>
