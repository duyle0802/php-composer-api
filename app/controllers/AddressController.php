<?php

namespace App\Controllers;

class AddressController
{
    private $db;
    private $user_address_model;

    public function __construct($db)
    {
        $this->db = $db;
        $this->user_address_model = new \App\Models\UserAddress($db);
    }

    public function addAddress()
    {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Not authenticated']);
            return;
        }

        $data = json_decode(file_get_contents("php://input"), true);
        
        if (!isset($data['address_line'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Address is required']);
            return;
        }

        $address_line = trim($data['address_line']);
        $user_id = $_SESSION['user_id'];
        $is_default = isset($data['is_default']) ? (int)$data['is_default'] : 0;

        // Geocode Address
        $coords = $this->geocodeAddress($address_line);
        
        if (!$coords) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Could not find location for this address']);
            return;
        }

        $address_id = $this->user_address_model->create($user_id, $address_line, $coords['lat'], $coords['lng'], $is_default);

        if ($address_id) {
            echo json_encode(['success' => true, 'message' => 'Address added successfully', 'address_id' => $address_id]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to save address']);
        }
    }

    public function getAddresses()
    {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Not authenticated']);
            return;
        }

        $addresses = $this->user_address_model->getByUser($_SESSION['user_id']);
        echo json_encode(['success' => true, 'addresses' => $addresses]);
    }

    private function geocodeAddress($address)
    {
        // Use OpenStreetMap Nominatim API (Free)
        $url = "https://nominatim.openstreetmap.org/search?format=json&q=" . urlencode($address) . "&limit=1";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // User-Agent is REQUIRED by Nominatim Usage Policy
        curl_setopt($ch, CURLOPT_USERAGENT, "BrightShop-Ecommerce/1.0 (contact@brightshop.com)");
        
        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);

        if (!empty($data) && isset($data[0])) {
            return [
                'lat' => $data[0]['lat'],
                'lng' => $data[0]['lon']
            ];
        }

        return false;
    }
}
