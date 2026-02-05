<?php

namespace App\Controllers;

class UserController
{
    private $db;
    private $user_model;

    public function __construct($db)
    {
        $this->db = $db;
        $this->user_model = new \App\Models\User($db);
    }

    public function getAllUsers()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            http_response_code(403);
            return json_encode(['success' => false, 'message' => 'Unauthorized']);
        }

        $users = $this->user_model->getAllUsers();

        http_response_code(200);
        return json_encode(['success' => true, 'users' => $users]);
    }

    public function getUserById()
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : null;

        if (!$id) {
            http_response_code(400);
            return json_encode(['success' => false, 'message' => 'User ID is required']);
        }

        if (!isset($_SESSION['user_id']) || ($_SESSION['user_id'] != $id && $_SESSION['role'] !== 'admin')) {
            http_response_code(403);
            return json_encode(['success' => false, 'message' => 'Unauthorized']);
        }

        $user = $this->user_model->getUserById($id);

        if (!$user) {
            http_response_code(404);
            return json_encode(['success' => false, 'message' => 'User not found']);
        }

        unset($user['password']);
        http_response_code(200);
        return json_encode(['success' => true, 'user' => $user]);
    }

    public function updateUser()
    {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            return json_encode(['success' => false, 'message' => 'Not authenticated']);
        }

        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['id'])) {
            http_response_code(400);
            return json_encode(['success' => false, 'message' => 'User ID is required']);
        }

        $id = (int)$data['id'];

        if ($_SESSION['user_id'] != $id && $_SESSION['role'] !== 'admin') {
            http_response_code(403);
            return json_encode(['success' => false, 'message' => 'Unauthorized']);
        }

        if ($this->user_model->updateUser($id, $data)) {
            http_response_code(200);
            return json_encode(['success' => true, 'message' => 'User updated successfully']);
        } else {
            http_response_code(500);
            return json_encode(['success' => false, 'message' => 'Failed to update user']);
        }
    }

    public function banUser()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            http_response_code(403);
            return json_encode(['success' => false, 'message' => 'Unauthorized']);
        }

        $id = isset($_GET['id']) ? (int)$_GET['id'] : null;

        if (!$id) {
            http_response_code(400);
            return json_encode(['success' => false, 'message' => 'User ID is required']);
        }

        if ($this->user_model->banUser($id)) {
            http_response_code(200);
            return json_encode(['success' => true, 'message' => 'User banned successfully']);
        } else {
            http_response_code(500);
            return json_encode(['success' => false, 'message' => 'Failed to ban user']);
        }
    }

    public function unbanUser()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            http_response_code(403);
            return json_encode(['success' => false, 'message' => 'Unauthorized']);
        }

        $id = isset($_GET['id']) ? (int)$_GET['id'] : null;

        if (!$id) {
            http_response_code(400);
            return json_encode(['success' => false, 'message' => 'User ID is required']);
        }

        if ($this->user_model->unbanUser($id)) {
            http_response_code(200);
            return json_encode(['success' => true, 'message' => 'User unbanned successfully']);
        } else {
            http_response_code(500);
            return json_encode(['success' => false, 'message' => 'Failed to unban user']);
        }
    }

    public function deleteUser()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            http_response_code(403);
            return json_encode(['success' => false, 'message' => 'Unauthorized']);
        }

        $id = isset($_GET['id']) ? (int)$_GET['id'] : null;

        if (!$id) {
            http_response_code(400);
            return json_encode(['success' => false, 'message' => 'User ID is required']);
        }

        if ($this->user_model->deleteUser($id)) {
            http_response_code(200);
            return json_encode(['success' => true, 'message' => 'User deleted successfully']);
        } else {
            http_response_code(500);
            return json_encode(['success' => false, 'message' => 'Failed to delete user']);
        }
    }
}
