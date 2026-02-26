<?php

namespace App\Controllers;

class AdminController
{
    private $db;
    private $user_model;
    private $product_model;
    private $category_model;
    private $order_model;

    public function __construct($db)
    {
        $this->db = $db;
        $this->user_model = new \App\Models\User($db);
        $this->product_model = new \App\Models\Product($db);
        $this->category_model = new \App\Models\Category($db);
        $this->order_model = new \App\Models\Order($db);
    }

    private function checkAdmin()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }
    }

    public function getDashboardStats()
    {
        $this->checkAdmin();

        // Total Active Users
        $queryUsers = "SELECT COUNT(*) as count FROM users WHERE is_banned = 0";
        $stmtUsers = $this->db->prepare($queryUsers);
        $stmtUsers->execute();
        $activeUsers = $stmtUsers->fetch(\PDO::FETCH_ASSOC)['count'];

        // Total Products
        $queryProducts = "SELECT COUNT(*) as count FROM products";
        $stmtProducts = $this->db->prepare($queryProducts);
        $stmtProducts->execute();
        $totalProducts = $stmtProducts->fetch(\PDO::FETCH_ASSOC)['count'];

        // Total Categories
        $queryCategories = "SELECT COUNT(*) as count FROM categories";
        $stmtCategories = $this->db->prepare($queryCategories);
        $stmtCategories->execute();
        $totalCategories = $stmtCategories->fetch(\PDO::FETCH_ASSOC)['count'];

        echo json_encode([
            'success' => true,
            'stats' => [
                'active_users' => $activeUsers,
                'total_products' => $totalProducts,
                'total_categories' => $totalCategories
            ]
        ]);
    }

    public function getChartData()
    {
        $this->checkAdmin();

        // 1. Order Status (Pie Chart) - Last 30 Days
        $queryStatus = "SELECT status, COUNT(*) as count 
                        FROM orders 
                        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) 
                        GROUP BY status";
        $stmtStatus = $this->db->prepare($queryStatus);
        $stmtStatus->execute();
        $statusData = $stmtStatus->fetchAll(\PDO::FETCH_ASSOC);

        // 2. Revenue (Bar/Line Chart) - Last 30 Days
        // Include 'paid' (MoMo) or 'completed' (COD)
        $queryRevenue = "SELECT DATE(created_at) as date, SUM(total_amount) as revenue 
                         FROM orders 
                         WHERE (payment_status = 'paid' OR status = 'completed')
                         AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) 
                         GROUP BY DATE(created_at) 
                         ORDER BY DATE(created_at) ASC";
        $stmtRevenue = $this->db->prepare($queryRevenue);
        $stmtRevenue->execute();
        $revenueData = $stmtRevenue->fetchAll(\PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'charts' => [
                'order_status' => $statusData,
                'revenue' => $revenueData
            ]
        ]);
    }
}
