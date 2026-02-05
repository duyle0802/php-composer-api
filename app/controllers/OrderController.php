<?php

namespace App\Controllers;

class OrderController
{
    private $db;
    private $order_model;
    private $product_model;
    private $cart_model;
    private $voucher_model;

    public function __construct($db)
    {
        $this->db = $db;
        $this->order_model = new \App\Models\Order($db);
        $this->product_model = new \App\Models\Product($db);
        $this->cart_model = new \App\Models\Cart($db);
        $this->voucher_model = new \App\Models\Voucher($db);
    }

    public function createOrder()
    {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            return json_encode(['success' => false, 'message' => 'Not authenticated']);
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return json_encode(['success' => false, 'message' => 'Method not allowed']);
        }

        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['shipping_address'], $data['shipping_distance'])) {
            http_response_code(400);
            return json_encode(['success' => false, 'message' => 'Missing required fields']);
        }

        $user_id = $_SESSION['user_id'];
        $shipping_address = trim($data['shipping_address']);
        $shipping_distance = (float)$data['shipping_distance'];
        $selected_items = isset($data['selected_items']) ? $data['selected_items'] : [];
        $voucher_code = isset($data['voucher_code']) ? trim($data['voucher_code']) : '';

        $cart_items = $this->cart_model->getCartItems($user_id);

        if (empty($selected_items)) {
            $selected_items = array_column($cart_items, 'id');
        }

        $total_amount = 0;
        $order_items = [];

        foreach ($cart_items as $item) {
            if (in_array($item['id'], $selected_items)) {
                $item_total = $item['quantity'] * $item['price'];
                $total_amount += $item_total;
                $order_items[] = $item;
            }
        }

        if ($total_amount == 0) {
            http_response_code(400);
            return json_encode(['success' => false, 'message' => 'No items selected']);
        }

        $discount_amount = 0;
        if ($voucher_code) {
            $validation = $this->voucher_model->validateVoucher($voucher_code, $total_amount);
            if ($validation['valid']) {
                $discount_amount = $this->voucher_model->calculateDiscount($validation['voucher'], $total_amount);
                $total_amount -= $discount_amount;
                $this->voucher_model->incrementUsage($voucher_code);
            }
        }

        $shipping_cost = 0;
        if ($shipping_distance > defined('FREE_SHIPPING_DISTANCE') ? FREE_SHIPPING_DISTANCE : 25) {
            $km_over_free = $shipping_distance - (defined('FREE_SHIPPING_DISTANCE') ? FREE_SHIPPING_DISTANCE : 25);
            $shipping_cost = ceil($km_over_free / 25) * (defined('SHIPPING_COST_PER_25KM') ? SHIPPING_COST_PER_25KM : 20000);
        }

        $final_total = $total_amount + $shipping_cost;

        $order_id = $this->order_model->createOrder($user_id, $total_amount, $shipping_cost, $discount_amount, $shipping_address, $shipping_distance);

        foreach ($order_items as $item) {
            $this->order_model->addOrderItem($order_id, $item['product_id'], $item['quantity'], $item['price']);
            $this->product_model->decreaseQuantity($item['product_id'], $item['quantity']);
            $this->cart_model->removeItem($item['id']);
        }

        http_response_code(201);
        return json_encode([
            'success' => true,
            'message' => 'Order created successfully',
            'order_id' => $order_id,
            'total_amount' => $total_amount,
            'shipping_cost' => $shipping_cost,
            'discount_amount' => $discount_amount,
            'final_total' => $final_total
        ]);
    }

    public function getOrder()
    {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            return json_encode(['success' => false, 'message' => 'Not authenticated']);
        }

        $order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : null;

        if (!$order_id) {
            http_response_code(400);
            return json_encode(['success' => false, 'message' => 'Order ID is required']);
        }

        $order = $this->order_model->getOrderById($order_id);

        if (!$order || ($order['user_id'] != $_SESSION['user_id'] && $_SESSION['role'] !== 'admin')) {
            http_response_code(404);
            return json_encode(['success' => false, 'message' => 'Order not found']);
        }

        $order_items = $this->order_model->getOrderItems($order_id);

        http_response_code(200);
        return json_encode(['success' => true, 'order' => $order, 'items' => $order_items]);
    }

    public function getUserOrders()
    {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            return json_encode(['success' => false, 'message' => 'Not authenticated']);
        }

        $user_id = $_SESSION['user_id'];
        $orders = $this->order_model->getOrdersByUser($user_id);

        http_response_code(200);
        return json_encode(['success' => true, 'orders' => $orders]);
    }

    public function getAllOrders()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            http_response_code(403);
            return json_encode(['success' => false, 'message' => 'Unauthorized']);
        }

        $orders = $this->order_model->getAllOrders();

        http_response_code(200);
        return json_encode(['success' => true, 'orders' => $orders]);
    }

    public function updateOrderStatus()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            http_response_code(403);
            return json_encode(['success' => false, 'message' => 'Unauthorized']);
        }

        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['order_id'], $data['status'])) {
            http_response_code(400);
            return json_encode(['success' => false, 'message' => 'Missing required fields']);
        }

        $order_id = (int)$data['order_id'];
        $status = $data['status'];

        if ($this->order_model->updateOrderStatus($order_id, $status)) {
            http_response_code(200);
            return json_encode(['success' => true, 'message' => 'Order status updated']);
        } else {
            http_response_code(500);
            return json_encode(['success' => false, 'message' => 'Failed to update order status']);
        }
    }
}
