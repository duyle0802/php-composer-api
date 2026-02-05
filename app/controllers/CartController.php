<?php

namespace App\Controllers;

class CartController
{
    private $db;
    private $cart_model;
    private $product_model;

    public function __construct($db)
    {
        $this->db = $db;
        $this->cart_model = new \App\Models\Cart($db);
        $this->product_model = new \App\Models\Product($db);
    }

    public function addItem()
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

        if (!isset($data['product_id'], $data['quantity'])) {
            http_response_code(400);
            return json_encode(['success' => false, 'message' => 'Missing required fields']);
        }

        $product_id = (int)$data['product_id'];
        $quantity = (int)$data['quantity'];
        $user_id = $_SESSION['user_id'];

        $product = $this->product_model->getById($product_id);

        if (!$product) {
            http_response_code(404);
            return json_encode(['success' => false, 'message' => 'Product not found']);
        }

        if ($product['quantity_in_stock'] < $quantity) {
            http_response_code(400);
            return json_encode(['success' => false, 'message' => 'Insufficient stock']);
        }

        if ($this->cart_model->addItem($user_id, $product_id, $quantity)) {
            http_response_code(200);
            return json_encode(['success' => true, 'message' => 'Item added to cart']);
        } else {
            http_response_code(500);
            return json_encode(['success' => false, 'message' => 'Failed to add item to cart']);
        }
    }

    public function getItems()
    {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            return json_encode(['success' => false, 'message' => 'Not authenticated']);
        }

        $user_id = $_SESSION['user_id'];
        $items = $this->cart_model->getCartItems($user_id);

        http_response_code(200);
        return json_encode(['success' => true, 'items' => $items]);
    }

    public function updateQuantity()
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

        if (!isset($data['cart_id'], $data['quantity'])) {
            http_response_code(400);
            return json_encode(['success' => false, 'message' => 'Missing required fields']);
        }

        $cart_id = (int)$data['cart_id'];
        $quantity = (int)$data['quantity'];

        if ($quantity < 1) {
            http_response_code(400);
            return json_encode(['success' => false, 'message' => 'Quantity must be at least 1']);
        }

        if ($this->cart_model->updateQuantity($cart_id, $quantity)) {
            http_response_code(200);
            return json_encode(['success' => true, 'message' => 'Cart item updated']);
        } else {
            http_response_code(500);
            return json_encode(['success' => false, 'message' => 'Failed to update cart item']);
        }
    }

    public function removeItem()
    {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            return json_encode(['success' => false, 'message' => 'Not authenticated']);
        }

        $cart_id = isset($_GET['cart_id']) ? (int)$_GET['cart_id'] : null;

        if (!$cart_id) {
            http_response_code(400);
            return json_encode(['success' => false, 'message' => 'Cart ID is required']);
        }

        if ($this->cart_model->removeItem($cart_id)) {
            http_response_code(200);
            return json_encode(['success' => true, 'message' => 'Item removed from cart']);
        } else {
            http_response_code(500);
            return json_encode(['success' => false, 'message' => 'Failed to remove item from cart']);
        }
    }

    public function clearCart()
    {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            return json_encode(['success' => false, 'message' => 'Not authenticated']);
        }

        $user_id = $_SESSION['user_id'];

        if ($this->cart_model->clearCart($user_id)) {
            http_response_code(200);
            return json_encode(['success' => true, 'message' => 'Cart cleared']);
        } else {
            http_response_code(500);
            return json_encode(['success' => false, 'message' => 'Failed to clear cart']);
        }
    }

    public function getTotal()
    {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            return json_encode(['success' => false, 'message' => 'Not authenticated']);
        }

        $user_id = $_SESSION['user_id'];
        $total = $this->cart_model->getCartTotal($user_id);

        http_response_code(200);
        return json_encode(['success' => true, 'total' => $total]);
    }

    public function getItemCount()
    {
        if (!isset($_SESSION['user_id'])) {
            return json_encode(['success' => true, 'count' => 0]);
        }

        $user_id = $_SESSION['user_id'];
        $count = $this->cart_model->getCartItemCount($user_id);

        http_response_code(200);
        return json_encode(['success' => true, 'count' => $count]);
    }
}
