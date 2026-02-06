<?php
// test_contact.php
$PORT = 8006;
$BASE_URL = "http://localhost:$PORT";

function curl_req($path, $method = 'GET', $data = []) {
    global $BASE_URL;
    $ch = curl_init($BASE_URL . $path);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    }
    $res = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    echo "Path: $path | Code: $code\n";
    return json_decode($res, true);
}

// Test Contact Send
$res = curl_req('/api/contact/send', 'POST', [
    'name' => 'Test User',
    'email' => 'test@example.com',
    'message' => 'This is a test message.'
]);

print_r($res);
?>
