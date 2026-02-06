<?php

namespace App\Models;

class UserAddress
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function create($user_id, $address_line, $lat, $lng, $is_default = 0)
    {
        // If set as default, unset other defaults for this user
        if ($is_default) {
            $this->clearDefault($user_id);
        }

        $query = "INSERT INTO user_addresses (user_id, address_line, lat, lng, is_default) 
                  VALUES (:user_id, :address_line, :lat, :lng, :is_default)";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':address_line', $address_line);
        $stmt->bindParam(':lat', $lat);
        $stmt->bindParam(':lng', $lng);
        $stmt->bindParam(':is_default', $is_default);
        
        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    public function getByUser($user_id)
    {
        $query = "SELECT * FROM user_addresses WHERE user_id = :user_id ORDER BY is_default DESC, created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getById($id)
    {
        $query = "SELECT * FROM user_addresses WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function setDefault($user_id, $address_id)
    {
        $this->clearDefault($user_id);
        
        $query = "UPDATE user_addresses SET is_default = 1 WHERE id = :id AND user_id = :user_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $address_id);
        $stmt->bindParam(':user_id', $user_id);
        return $stmt->execute();
    }

    private function clearDefault($user_id)
    {
        $query = "UPDATE user_addresses SET is_default = 0 WHERE user_id = :user_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
    }
}
