<?php

namespace App\Controllers;

class CategoryController
{
    private $db;
    private $category_model;

    public function __construct($db)
    {
        $this->db = $db;
        $this->category_model = new \App\Models\Category($db);
    }

    public function getAll()
    {
        $categories = $this->category_model->getAll();

        http_response_code(200);
        return json_encode(['success' => true, 'categories' => $categories]);
    }

    public function getById()
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : null;

        if (!$id) {
            http_response_code(400);
            return json_encode(['success' => false, 'message' => 'Category ID is required']);
        }

        $category = $this->category_model->getById($id);

        if (!$category) {
            http_response_code(404);
            return json_encode(['success' => false, 'message' => 'Category not found']);
        }

        http_response_code(200);
        return json_encode(['success' => true, 'category' => $category]);
    }

    public function create()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            http_response_code(403);
            return json_encode(['success' => false, 'message' => 'Unauthorized']);
        }

        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['name'])) {
            http_response_code(400);
            return json_encode(['success' => false, 'message' => 'Category name is required']);
        }

        $name = trim($data['name']);
        $description = isset($data['description']) ? trim($data['description']) : '';
        $image = isset($data['image']) ? $data['image'] : '';

        if ($this->category_model->nameExists($name)) {
            http_response_code(400);
            return json_encode(['success' => false, 'message' => 'Category name already exists']);
        }

        if ($this->category_model->create($name, $description, $image)) {
            http_response_code(201);
            return json_encode(['success' => true, 'message' => 'Category created successfully']);
        } else {
            http_response_code(500);
            return json_encode(['success' => false, 'message' => 'Failed to create category']);
        }
    }

    public function update()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            http_response_code(403);
            return json_encode(['success' => false, 'message' => 'Unauthorized']);
        }

        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['id'], $data['name'])) {
            http_response_code(400);
            return json_encode(['success' => false, 'message' => 'Missing required fields']);
        }

        $id = (int)$data['id'];
        $name = trim($data['name']);
        $description = isset($data['description']) ? trim($data['description']) : '';
        $image = isset($data['image']) ? $data['image'] : null;

        if ($this->category_model->nameExists($name, $id)) {
            http_response_code(400);
            return json_encode(['success' => false, 'message' => 'Category name already exists']);
        }

        if ($this->category_model->update($id, $name, $description, $image)) {
            http_response_code(200);
            return json_encode(['success' => true, 'message' => 'Category updated successfully']);
        } else {
            http_response_code(500);
            return json_encode(['success' => false, 'message' => 'Failed to update category']);
        }
    }

    public function delete()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            http_response_code(403);
            return json_encode(['success' => false, 'message' => 'Unauthorized']);
        }

        $id = isset($_GET['id']) ? (int)$_GET['id'] : null;

        if (!$id) {
            http_response_code(400);
            return json_encode(['success' => false, 'message' => 'Category ID is required']);
        }

        if ($this->category_model->delete($id)) {
            http_response_code(200);
            return json_encode(['success' => true, 'message' => 'Category deleted successfully']);
        } else {
            http_response_code(500);
            return json_encode(['success' => false, 'message' => 'Failed to delete category']);
        }
    }
}
