<?php

namespace App\Controllers;

class ProductController
{
    private $db;
    private $product_model;

    public function __construct($db)
    {
        $this->db = $db;
        $this->product_model = new \App\Models\Product($db);
    }

    public function getAll()
    {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = defined('ITEMS_PER_PAGE') ? ITEMS_PER_PAGE : 9;
        $offset = ($page - 1) * $limit;

        $products = $this->product_model->getAll($limit, $offset);
        $total = $this->product_model->countAll();
        $total_pages = ceil($total / $limit);

        http_response_code(200);
        return json_encode([
            'success' => true,
            'products' => $products,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $total_pages,
                'total_items' => $total,
                'items_per_page' => $limit
            ]
        ]);
    }

    public function getById()
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : null;

        if (!$id) {
            http_response_code(400);
            return json_encode(['success' => false, 'message' => 'Product ID is required']);
        }

        $product = $this->product_model->getById($id);

        if (!$product) {
            http_response_code(404);
            return json_encode(['success' => false, 'message' => 'Product not found']);
        }

        http_response_code(200);
        return json_encode(['success' => true, 'product' => $product]);
    }

    public function searchAndFilter()
    {
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : null;
        $min_price = isset($_GET['min_price']) ? (float)$_GET['min_price'] : null;
        $max_price = isset($_GET['max_price']) ? (float)$_GET['max_price'] : null;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = defined('ITEMS_PER_PAGE') ? ITEMS_PER_PAGE : 9;
        $offset = ($page - 1) * $limit;

        if (empty($search) && !$category_id) {
            http_response_code(400);
            return json_encode(['success' => false, 'message' => 'Search or category required']);
        }

        if ($search) {
            $products = $this->product_model->searchProducts($search, $category_id, $min_price, $max_price, $limit, $offset);
            $total = $this->product_model->countSearchResults($search, $category_id, $min_price, $max_price);
        } else {
            $products = $this->product_model->getByCategory($category_id, $limit, $offset);
            $total = $this->product_model->countByCategory($category_id);
        }

        $total_pages = ceil($total / $limit);

        http_response_code(200);
        return json_encode([
            'success' => true,
            'products' => $products,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $total_pages,
                'total_items' => $total,
                'items_per_page' => $limit
            ]
        ]);
    }

    public function getFeatured()
    {
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 6;
        $products = $this->product_model->getFeaturedProducts($limit);

        http_response_code(200);
        return json_encode(['success' => true, 'products' => $products]);
    }

    public function create()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            http_response_code(403);
            return json_encode(['success' => false, 'message' => 'Unauthorized']);
        }

        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['category_id'], $data['name'], $data['price'], $data['quantity_in_stock'])) {
            http_response_code(400);
            return json_encode(['success' => false, 'message' => 'Missing required fields']);
        }

        $category_id = (int)$data['category_id'];
        $name = trim($data['name']);
        $description = isset($data['description']) ? trim($data['description']) : '';
        $price = (float)$data['price'];
        $quantity_in_stock = (int)$data['quantity_in_stock'];
        $image = isset($data['image']) ? $data['image'] : '';

        if ($this->product_model->create($category_id, $name, $description, $price, $quantity_in_stock, $image)) {
            http_response_code(201);
            return json_encode(['success' => true, 'message' => 'Product created successfully']);
        } else {
            http_response_code(500);
            return json_encode(['success' => false, 'message' => 'Failed to create product']);
        }
    }

    public function update()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            http_response_code(403);
            return json_encode(['success' => false, 'message' => 'Unauthorized']);
        }

        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['id'], $data['category_id'], $data['name'], $data['price'], $data['quantity_in_stock'])) {
            http_response_code(400);
            return json_encode(['success' => false, 'message' => 'Missing required fields']);
        }

        $id = (int)$data['id'];
        $category_id = (int)$data['category_id'];
        $name = trim($data['name']);
        $description = isset($data['description']) ? trim($data['description']) : '';
        $price = (float)$data['price'];
        $quantity_in_stock = (int)$data['quantity_in_stock'];
        $image = isset($data['image']) ? $data['image'] : null;

        if ($this->product_model->update($id, $category_id, $name, $description, $price, $quantity_in_stock, $image)) {
            http_response_code(200);
            return json_encode(['success' => true, 'message' => 'Product updated successfully']);
        } else {
            http_response_code(500);
            return json_encode(['success' => false, 'message' => 'Failed to update product']);
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
            return json_encode(['success' => false, 'message' => 'Product ID is required']);
        }

        if ($this->product_model->delete($id)) {
            http_response_code(200);
            return json_encode(['success' => true, 'message' => 'Product deleted successfully']);
        } else {
            http_response_code(500);
            return json_encode(['success' => false, 'message' => 'Failed to delete product']);
        }
    }
}
