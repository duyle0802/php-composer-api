<?php

namespace App\Models;

class User
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function register($username, $email, $password, $full_name)
    {
        $query = "INSERT INTO users (username, email, password, full_name) 
                  VALUES (:username, :email, :password, :full_name)";
        
        $stmt = $this->db->prepare($query);
        
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':full_name', $full_name);
        
        return $stmt->execute();
    }

    public function getUserByUsername($username)
    {
        $query = "SELECT * FROM users WHERE username = :username";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        return $stmt->fetch();
    }

    public function getUserById($id)
    {
        $query = "SELECT * FROM users WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch();
    }

    public function getUserByEmail($email)
    {
        $query = "SELECT * FROM users WHERE email = :email";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        return $stmt->fetch();
    }

    public function getAllUsers()
    {
        $query = "SELECT id, username, email, full_name, phone, address, role, is_banned, created_at FROM users";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    public function updateUser($id, $data)
    {
        $allowed_fields = ['full_name', 'phone', 'address', 'email'];
        $updates = [];
        $params = [];

        foreach ($data as $key => $value) {
            if (in_array($key, $allowed_fields)) {
                $updates[] = "$key = :$key";
                $params[$key] = $value;
            }
        }

        if (empty($updates)) {
            return false;
        }

        $params['id'] = $id;
        $query = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        
        foreach ($params as $key => $value) {
            $stmt->bindParam(':' . $key, $value);
        }
        
        return $stmt->execute();
    }

    public function banUser($id)
    {
        $query = "UPDATE users SET is_banned = 1 WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    public function unbanUser($id)
    {
        $query = "UPDATE users SET is_banned = 0 WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    public function deleteUser($id)
    {
        $query = "DELETE FROM users WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    public function usernameExists($username)
    {
        $query = "SELECT id FROM users WHERE username = :username";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }

    public function emailExists($email)
    {
        $query = "SELECT id FROM users WHERE email = :email";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }

    public function getAddresses($user_id)
    {
        $query = "SELECT * FROM user_addresses WHERE user_id = :user_id ORDER BY is_default DESC, created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
