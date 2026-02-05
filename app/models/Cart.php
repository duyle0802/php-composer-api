<?php

namespace App\Models;

class Cart
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function addItem($user_id, $product_id, $quantity = 1)
    {
        $query = "INSERT INTO cart (user_id, product_id, quantity) 
                  VALUES (:user_id, :product_id, :quantity)
                  ON DUPLICATE KEY UPDATE quantity = quantity + :quantity";
        
        $stmt = $this->db->prepare($query);
        
        $stmt->bindParam(':user_id', $user_id, \PDO::PARAM_INT);
        $stmt->bindParam(':product_id', $product_id, \PDO::PARAM_INT);
        $stmt->bindParam(':quantity', $quantity, \PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    public function getCartItems($user_id)
    {
        $query = "SELECT c.id, c.user_id, c.product_id, c.quantity, p.name, p.price, p.quantity_in_stock, p.image
                  FROM cart c
                  LEFT JOIN products p ON c.product_id = p.id
                  WHERE c.user_id = :user_id
                  ORDER BY c.created_at DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    public function updateQuantity($cart_id, $quantity)
    {
        $query = "UPDATE cart SET quantity = :quantity WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $cart_id, \PDO::PARAM_INT);
        $stmt->bindParam(':quantity', $quantity, \PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    public function removeItem($cart_id)
    {
        $query = "DELETE FROM cart WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $cart_id, \PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    public function clearCart($user_id)
    {
        $query = "DELETE FROM cart WHERE user_id = :user_id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id, \PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    public function getCartTotal($user_id)
    {
        $query = "SELECT SUM(c.quantity * p.price) as total
                  FROM cart c
                  LEFT JOIN products p ON c.product_id = p.id
                  WHERE c.user_id = :user_id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id, \PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }

    public function getCartItemCount($user_id)
    {
        $query = "SELECT COUNT(*) as count FROM cart WHERE user_id = :user_id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id, \PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }
}
