<?php

namespace App\Models;

class Order
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function createOrder($user_id, $total_amount, $shipping_cost, $discount_amount, $shipping_address, $shipping_distance, $payment_method = 'cod', $payment_status = 'pending')
    {
        $query = "INSERT INTO orders (user_id, total_amount, shipping_cost, discount_amount, shipping_address, shipping_distance, payment_method, payment_status)
                  VALUES (:user_id, :total_amount, :shipping_cost, :discount_amount, :shipping_address, :shipping_distance, :payment_method, :payment_status)";
        
        $stmt = $this->db->prepare($query);
        
        $stmt->bindParam(':user_id', $user_id, \PDO::PARAM_INT);
        $stmt->bindParam(':total_amount', $total_amount);
        $stmt->bindParam(':shipping_cost', $shipping_cost);
        $stmt->bindParam(':discount_amount', $discount_amount);
        $stmt->bindParam(':shipping_address', $shipping_address);
        $stmt->bindParam(':shipping_distance', $shipping_distance);
        $stmt->bindParam(':payment_method', $payment_method);
        $stmt->bindParam(':payment_status', $payment_status);
        
        $stmt->execute();
        
        return $this->db->lastInsertId();
    }

    public function addOrderItem($order_id, $product_id, $quantity, $price)
    {
        $query = "INSERT INTO order_items (order_id, product_id, quantity, price)
                  VALUES (:order_id, :product_id, :quantity, :price)";
        
        $stmt = $this->db->prepare($query);
        
        $stmt->bindParam(':order_id', $order_id, \PDO::PARAM_INT);
        $stmt->bindParam(':product_id', $product_id, \PDO::PARAM_INT);
        $stmt->bindParam(':quantity', $quantity, \PDO::PARAM_INT);
        $stmt->bindParam(':price', $price);
        
        return $stmt->execute();
    }

    public function getOrderById($id)
    {
        $query = "SELECT * FROM orders WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch();
    }

    public function getOrdersByUser($user_id)
    {
        $query = "SELECT * FROM orders WHERE user_id = :user_id ORDER BY created_at DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    public function getOrderItems($order_id)
    {
        $query = "SELECT oi.*, p.name, p.image FROM order_items oi
                  LEFT JOIN products p ON oi.product_id = p.id
                  WHERE oi.order_id = :order_id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':order_id', $order_id, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    public function getAllOrders($status = null)
    {
        $query = "SELECT o.*, u.username, u.full_name FROM orders o
                  LEFT JOIN users u ON o.user_id = u.id";
        
        if ($status) {
            $query .= " WHERE o.status = :status";
        }
        
        $query .= " ORDER BY o.created_at DESC";
        
        $stmt = $this->db->prepare($query);
        
        if ($status) {
            $stmt->bindParam(':status', $status);
        }
        
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    public function updateOrderStatus($id, $status)
    {
        $query = "UPDATE orders SET status = :status WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->bindParam(':status', $status);
        
        return $stmt->execute();
    }

    public function completeOrder($id)
    {
        return $this->updateOrderStatus($id, 'completed');
    }
    public function updatePaymentStatus($id, $status, $transaction_id = null)
    {
        $query = "UPDATE orders SET payment_status = :status, transaction_id = :transaction_id WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':transaction_id', $transaction_id);
        
        return $stmt->execute();
    }
}
