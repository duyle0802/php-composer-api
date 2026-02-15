<?php

namespace App\Models;

class Product
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getAll($limit = null, $offset = null)
    {
        $query = "SELECT p.*, c.name as category_name FROM products p 
                  LEFT JOIN categories c ON p.category_id = c.id
                  ORDER BY p.created_at DESC";
        
        if ($limit !== null) {
            $query .= " LIMIT :limit OFFSET :offset";
        }
        
        $stmt = $this->db->prepare($query);
        
        if ($limit !== null) {
            $stmt->bindParam(':limit', $limit, \PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, \PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getById($id)
    {
        $query = "SELECT p.*, c.name as category_name FROM products p 
                  LEFT JOIN categories c ON p.category_id = c.id
                  WHERE p.id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch();
    }

    public function getByCategory($category_id, $limit = null, $offset = null)
    {
        $query = "SELECT p.*, c.name as category_name FROM products p 
                  LEFT JOIN categories c ON p.category_id = c.id
                  WHERE p.category_id = :category_id
                  ORDER BY p.created_at DESC";
        
        if ($limit !== null) {
            $query .= " LIMIT :limit OFFSET :offset";
        }
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':category_id', $category_id, \PDO::PARAM_INT);
        
        if ($limit !== null) {
            $stmt->bindParam(':limit', $limit, \PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, \PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function searchProducts($search, $category_id = null, $min_price = null, $max_price = null, $limit = null, $offset = null)
    {
        $query = "SELECT p.*, c.name as category_name FROM products p 
                  LEFT JOIN categories c ON p.category_id = c.id
                  WHERE 1=1"; // Start with true condition
        
        if (!empty($search)) {
             $query .= " AND (p.name LIKE :search_name OR p.description LIKE :search_desc)";
        }

        if ($category_id !== null) {
            $query .= " AND p.category_id = :category_id";
        }
        
        if ($min_price !== null) {
            $query .= " AND p.price >= :min_price";
        }
        
        if ($max_price !== null) {
            $query .= " AND p.price <= :max_price";
        }
        
        $query .= " ORDER BY p.created_at DESC";
        
        if ($limit !== null) {
            $query .= " LIMIT :limit OFFSET :offset";
        }
        
        $stmt = $this->db->prepare($query);
        
        if (!empty($search)) {
            $search_param = "%$search%";
            $stmt->bindParam(':search_name', $search_param);
            $stmt->bindParam(':search_desc', $search_param);
        }
        
        if ($category_id !== null) {
            $stmt->bindParam(':category_id', $category_id, \PDO::PARAM_INT);
        }
        
        if ($min_price !== null) {
            $stmt->bindParam(':min_price', $min_price);
        }
        
        if ($max_price !== null) {
            $stmt->bindParam(':max_price', $max_price);
        }
        
        if ($limit !== null) {
            $stmt->bindParam(':limit', $limit, \PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, \PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countAll()
    {
        $query = "SELECT COUNT(*) as count FROM products";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        $result = $stmt->fetch();
        return $result['count'];
    }

    public function countByCategory($category_id)
    {
        $query = "SELECT COUNT(*) as count FROM products WHERE category_id = :category_id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':category_id', $category_id, \PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch();
        return $result['count'];
    }

    public function countSearchResults($search, $category_id = null, $min_price = null, $max_price = null)
    {
        $query = "SELECT COUNT(*) as count FROM products WHERE 1=1";
        
        if (!empty($search)) {
            $query .= " AND (name LIKE :search_name OR description LIKE :search_desc)";
        }
        
        if ($category_id !== null) {
            $query .= " AND category_id = :category_id";
        }
        
        if ($min_price !== null) {
            $query .= " AND price >= :min_price";
        }
        
        if ($max_price !== null) {
            $query .= " AND price <= :max_price";
        }
        
        $stmt = $this->db->prepare($query);
        
        if (!empty($search)) {
            $search_param = "%$search%";
            $stmt->bindParam(':search_name', $search_param);
            $stmt->bindParam(':search_desc', $search_param);
        }
        
        if ($category_id !== null) {
            $stmt->bindParam(':category_id', $category_id, \PDO::PARAM_INT);
        }
        
        if ($min_price !== null) {
            $stmt->bindParam(':min_price', $min_price);
        }
        
        if ($max_price !== null) {
            $stmt->bindParam(':max_price', $max_price);
        }
        
        $stmt->execute();
        
        $result = $stmt->fetch();
        return $result['count'];
    }

    public function create($category_id, $name, $description, $price, $quantity_in_stock, $image)
    {
        $query = "INSERT INTO products (category_id, name, description, price, quantity_in_stock, image)
                  VALUES (:category_id, :name, :description, :price, :quantity_in_stock, :image)";
        
        $stmt = $this->db->prepare($query);
        
        $stmt->bindParam(':category_id', $category_id, \PDO::PARAM_INT);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':quantity_in_stock', $quantity_in_stock, \PDO::PARAM_INT);
        $stmt->bindParam(':image', $image);
        
        return $stmt->execute();
    }

    public function update($id, $category_id, $name, $description, $price, $quantity_in_stock, $image = null)
    {
        if ($image !== null) {
            $query = "UPDATE products SET category_id = :category_id, name = :name, description = :description, 
                      price = :price, quantity_in_stock = :quantity_in_stock, image = :image
                      WHERE id = :id";
        } else {
            $query = "UPDATE products SET category_id = :category_id, name = :name, description = :description, 
                      price = :price, quantity_in_stock = :quantity_in_stock
                      WHERE id = :id";
        }
        
        $stmt = $this->db->prepare($query);
        
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->bindParam(':category_id', $category_id, \PDO::PARAM_INT);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':quantity_in_stock', $quantity_in_stock, \PDO::PARAM_INT);
        
        if ($image !== null) {
            $stmt->bindParam(':image', $image);
        }
        
        return $stmt->execute();
    }

    public function delete($id)
    {
        $query = "DELETE FROM products WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    public function updateQuantity($id, $quantity)
    {
        $query = "UPDATE products SET quantity_in_stock = :quantity WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->bindParam(':quantity', $quantity, \PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    public function decreaseQuantity($id, $amount)
    {
        $query = "UPDATE products SET quantity_in_stock = quantity_in_stock - :amount WHERE id = :id AND quantity_in_stock >= :amount_check";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->bindParam(':amount', $amount, \PDO::PARAM_INT);
        $stmt->bindParam(':amount_check', $amount, \PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    public function getFeaturedProducts($limit = 6)
    {
        $query = "SELECT p.*, c.name as category_name FROM products p 
                  LEFT JOIN categories c ON p.category_id = c.id
                  WHERE p.quantity_in_stock > 0
                  ORDER BY p.created_at DESC
                  LIMIT :limit";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    public function searchSuggestions($search)
    {
        $query = "SELECT id, name, image FROM products 
                  WHERE name LIKE :search 
                  LIMIT 5";
        
        $stmt = $this->db->prepare($query);
        $search_param = "%$search%";
        $stmt->bindParam(':search', $search_param);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
}
