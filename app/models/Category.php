<?php

namespace App\Models;

class Category
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getAll()
    {
        $query = "SELECT * FROM categories ORDER BY name ASC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    public function getById($id)
    {
        $query = "SELECT * FROM categories WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch();
    }

    public function create($name, $description, $image)
    {
        $query = "INSERT INTO categories (name, description, image)
                  VALUES (:name, :description, :image)";
        
        $stmt = $this->db->prepare($query);
        
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':image', $image);
        
        return $stmt->execute();
    }

    public function update($id, $name, $description, $image = null)
    {
        if ($image !== null) {
            $query = "UPDATE categories SET name = :name, description = :description, image = :image WHERE id = :id";
        } else {
            $query = "UPDATE categories SET name = :name, description = :description WHERE id = :id";
        }
        
        $stmt = $this->db->prepare($query);
        
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        
        if ($image !== null) {
            $stmt->bindParam(':image', $image);
        }
        
        return $stmt->execute();
    }

    public function delete($id)
    {
        $query = "DELETE FROM categories WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    public function nameExists($name, $exclude_id = null)
    {
        if ($exclude_id !== null) {
            $query = "SELECT id FROM categories WHERE name = :name AND id != :exclude_id";
        } else {
            $query = "SELECT id FROM categories WHERE name = :name";
        }
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':name', $name);
        
        if ($exclude_id !== null) {
            $stmt->bindParam(':exclude_id', $exclude_id, \PDO::PARAM_INT);
        }
        
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }
}
