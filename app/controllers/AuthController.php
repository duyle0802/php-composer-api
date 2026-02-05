<?php

namespace App\Controllers;

class AuthController
{
    private $db;
    private $user_model;

    public function __construct($db)
    {
        $this->db = $db;
        $this->user_model = new \App\Models\User($db);
    }

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return json_encode(['success' => false, 'message' => 'Method not allowed']);
        }

        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['username'], $data['email'], $data['password'], $data['full_name'])) {
            http_response_code(400);
            return json_encode(['success' => false, 'message' => 'Missing required fields']);
        }

        $username = trim($data['username']);
        $email = trim($data['email']);
        $password = $data['password'];
        $full_name = trim($data['full_name']);

        if (strlen($username) < 3) {
            http_response_code(400);
            return json_encode(['success' => false, 'message' => 'Username must be at least 3 characters']);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            return json_encode(['success' => false, 'message' => 'Invalid email format']);
        }

        if (strlen($password) < 6) {
            http_response_code(400);
            return json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']);
        }

        if ($this->user_model->usernameExists($username)) {
            http_response_code(400);
            return json_encode(['success' => false, 'message' => 'Username already exists']);
        }

        if ($this->user_model->emailExists($email)) {
            http_response_code(400);
            return json_encode(['success' => false, 'message' => 'Email already exists']);
        }

        if ($this->user_model->register($username, $email, $password, $full_name)) {
            http_response_code(201);
            return json_encode(['success' => true, 'message' => 'Registration successful']);
        } else {
            http_response_code(500);
            return json_encode(['success' => false, 'message' => 'Registration failed']);
        }
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return json_encode(['success' => false, 'message' => 'Method not allowed']);
        }

        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['username'], $data['password'])) {
            http_response_code(400);
            return json_encode(['success' => false, 'message' => 'Missing username or password']);
        }

        $username = trim($data['username']);
        $password = $data['password'];

        $user = $this->user_model->getUserByUsername($username);

        if (!$user || !password_verify($password, $user['password'])) {
            http_response_code(401);
            return json_encode(['success' => false, 'message' => 'Invalid username or password']);
        }

        if ($user['is_banned']) {
            http_response_code(403);
            return json_encode(['success' => false, 'message' => 'Your account has been banned']);
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['role'] = $user['role'];

        http_response_code(200);
        return json_encode(['success' => true, 'message' => 'Login successful', 'user' => ['id' => $user['id'], 'username' => $user['username'], 'role' => $user['role'], 'full_name' => $user['full_name']]]);
    }

    public function logout()
    {
        session_destroy();
        http_response_code(200);
        return json_encode(['success' => true, 'message' => 'Logout successful']);
    }

    public function getProfile()
    {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            return json_encode(['success' => false, 'message' => 'Not authenticated']);
        }

        $user = $this->user_model->getUserById($_SESSION['user_id']);

        if (!$user) {
            http_response_code(404);
            return json_encode(['success' => false, 'message' => 'User not found']);
        }

        unset($user['password']);
        http_response_code(200);
        return json_encode(['success' => true, 'user' => $user]);
    }

    public function checkAuth()
    {
        if (isset($_SESSION['user_id'])) {
            http_response_code(200);
            return json_encode(['success' => true, 'authenticated' => true, 'user' => ['id' => $_SESSION['user_id'], 'username' => $_SESSION['username'], 'role' => $_SESSION['role'], 'full_name' => $_SESSION['full_name']]]);
        } else {
            http_response_code(200);
            return json_encode(['success' => true, 'authenticated' => false]);
        }
    }
}
