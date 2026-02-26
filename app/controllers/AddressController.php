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

        // Geocode Address if lat/lng not provided
        if (isset($data['lat'], $data['lng'])) {
            $coords = ['lat' => $data['lat'], 'lng' => $data['lng']];
        } else {
            $coords = $this->geocodeAddress($address_line);
            if ($coords) {
                // geocodeAddress returns 'lon', map it to 'lng'
                $coords['lng'] = $coords['lon'];
            }
        }
        
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
        // We try to geocode the full address first
        $coords = $this->callNominatim($address);
        
        if ($coords) {
            return $coords;
        }

        // IMPROVEMENT: If full address fails, try to geocode without the street name
        // The address format from frontend is: "Street, Ward, District, Province"
        $parts = explode(',', $address);
        if (count($parts) >= 3) {
            // Remove the first part (Street) and try again
            array_shift($parts);
            $generalAddress = implode(',', $parts);
            $coords = $this->callNominatim($generalAddress);
            if ($coords) {
                 return $coords;
            }
        }
        
        // Final fallback: just District and Province
        if (count($parts) >= 2) {
             // Assuming the last two parts are District and Province
             $districtProvince = implode(',', array_slice($parts, -2));
             $coords = $this->callNominatim($districtProvince);
             return $coords;
        }

        return false;
    }

    private function callNominatim($query) {
        $url = "https://nominatim.openstreetmap.org/search?format=json&q=" . urlencode($query) . "&limit=1";
        
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
                'lon' => $data[0]['lon'] // Nominatim returns 'lon'
            ];
        }
        return false;
    }

}
