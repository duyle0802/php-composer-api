<?php

namespace App\Controllers;

class OrderController
{
    private $db;
    private $order_model;
    private $product_model;
    private $cart_model;
    private $voucher_model;

    public function __construct($db)
    {
        $this->db = $db;
        $this->order_model = new \App\Models\Order($db);
        $this->product_model = new \App\Models\Product($db);
        $this->cart_model = new \App\Models\Cart($db);
        $this->voucher_model = new \App\Models\Voucher($db);
    }

    public function calculateShipping()
    {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Not authenticated']);
            return;
        }

        $data = json_decode(file_get_contents("php://input"), true);
        $address_id = isset($data['address_id']) ? (int)$data['address_id'] : 0;

        if (!$address_id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Address ID is required']);
            return;
        }

        $user_address_model = new \App\Models\UserAddress($this->db);
        $address = $user_address_model->getById($address_id);

        if (!$address) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Address not found']);
            return;
        }

        $distance = $this->calculateDistance(STORE_LAT, STORE_LNG, $address['lat'], $address['lng']);
        $shipping_cost = $this->calculateShippingFee($distance);

        echo json_encode([
            'success' => true, 
            'distance' => round($distance, 2), 
            'shipping_cost' => $shipping_cost,
            'formatted_shipping_cost' => number_format($shipping_cost, 0, ',', '.') . ' ₫'
        ]);
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earth_radius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earth_radius * $c;
    }

    private function calculateShippingFee($distance)
    {
        if ($distance < 100) {
            return SHIPPING_FEE_UNDER_100KM;
        } elseif ($distance <= 250) {
            return SHIPPING_FEE_100_250KM;
        } else {
            $extra_km = $distance - 250;
            $steps = floor($extra_km / SHIPPING_STEP_DISTANCE);
            return SHIPPING_FEE_BASE_OVER_250KM + ($steps * SHIPPING_FEE_STEP_OVER_250KM);
        }
    }

    public function createOrder()
    {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Not authenticated']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['address_id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Shipping Address is required']);
            return;
        }

        $user_id = $_SESSION['user_id'];
        $address_id = (int)$data['address_id'];
        $payment_method = isset($data['payment_method']) ? $data['payment_method'] : 'cod';
        
        $user_address_model = new \App\Models\UserAddress($this->db);
        $address = $user_address_model->getById($address_id);

        if (!$address || $address['user_id'] != $user_id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid address']);
            return;
        }

        $selected_items = isset($data['selected_items']) ? $data['selected_items'] : [];
        $voucher_code = isset($data['voucher_code']) ? trim($data['voucher_code']) : '';

        $cart_items = $this->cart_model->getCartItems($user_id);

        if (empty($selected_items)) {
            $selected_items = array_column($cart_items, 'id');
        }

        $total_amount = 0;
        $order_items = [];

        foreach ($cart_items as $item) {
            if (in_array($item['id'], $selected_items)) {
                $item_total = $item['quantity'] * $item['price'];
                $total_amount += $item_total;
                $order_items[] = $item;
            }
        }

        if ($total_amount == 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'No items selected']);
            return;
        }

        $discount_amount = 0;
        if ($voucher_code) {
            $validation = $this->voucher_model->validateVoucher($voucher_code, $total_amount);
            if ($validation['valid']) {
                $discount_amount = $this->voucher_model->calculateDiscount($validation['voucher'], $total_amount);
                $total_amount -= $discount_amount;
                $this->voucher_model->incrementUsage($voucher_code);
            }
        }

        // Calculate Shipping
        $distance = $this->calculateDistance(STORE_LAT, STORE_LNG, $address['lat'], $address['lng']);
        $shipping_cost = $this->calculateShippingFee($distance);

        $final_total = $total_amount + $shipping_cost;

        // Payment Processing
        $payment_status = 'pending';
        $momo_pay_url = null;

        if ($payment_method === 'momo') {
            $momoService = new \App\Services\MoMoService();
            // Assuming createOrder returns lastInsertId, we create order first as 'pending'
            // We need order_id for MoMo.
        }

        $order_id = $this->order_model->createOrder($user_id, $total_amount, $shipping_cost, $discount_amount, $address['address_line'], $distance, $payment_method, $payment_status);

        foreach ($order_items as $item) {
            $this->order_model->addOrderItem($order_id, $item['product_id'], $item['quantity'], $item['price']);
            $this->product_model->decreaseQuantity($item['product_id'], $item['quantity']);
            $this->cart_model->removeItem($item['id']);
        }

        // Post-Order Actions
        if ($payment_method === 'momo') {
            $momoService = new \App\Services\MoMoService();
            $momoResponse = $momoService->createPayment($order_id, $final_total);
            if (isset($momoResponse['payUrl'])) {
                $momo_pay_url = $momoResponse['payUrl'];
            } else {
                 // Log error or handle failure
            }
        } else {
            // COD: Send Email Immediately
            // Fetch fresh order data with user usage
            $orderData = $this->order_model->getOrderById($order_id);
            // Need user email/name. getOrderById returns user_id, need to join or fetch user. 
            // In Order.php getOrderById does `SELECT * FROM orders`. 
            // getAllOrders does the join. Let's assume we can fetch user details.
            
            $user_model = new \App\Models\User($this->db);
            $user = $user_model->getUserById($user_id);
            $orderData['email'] = $user['email'];
            $orderData['full_name'] = $user['full_name'];
            
            $emailService = new \App\Services\EmailService();
            $emailService->sendOrderConfirmation($orderData, $order_items);
            $emailService->sendOrderConfirmation($orderData, $order_items, true); // Admin
        }

        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'Order created successfully',
            'order_id' => $order_id,
            'total_amount' => $total_amount,
            'shipping_cost' => $shipping_cost,
            'discount_amount' => $discount_amount,
            'final_total' => $final_total,
            'payment_url' => $momo_pay_url
        ]);
    }

    public function handleMoMoCallback()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        $momoService = new \App\Services\MoMoService();

        if ($momoService->verifySignature($data)) {
            $orderIdComposite = $data['orderId']; // e.g., 123_17000000
            $parts = explode('_', $orderIdComposite);
            $order_id = $parts[0];
            $resultCode = $data['resultCode'];

            if ($resultCode == 0) {
                // Success
                $this->order_model->updatePaymentStatus($order_id, 'paid', $data['transId']);
                
                // Send Email
                $orderData = $this->order_model->getOrderById($order_id);
                 // Need user details
                $user_model = new \App\Models\User($this->db);
                $user = $user_model->getUserById($orderData['user_id']);
                $orderData['email'] = $user['email'];
                $orderData['full_name'] = $user['full_name'];
                $orderData['payment_method'] = 'momo';
                $orderData['payment_status'] = 'paid'; // Manual override since DB fetch might lag or we just updated it
                
                $order_items = $this->order_model->getOrderItems($order_id);

                $emailService = new \App\Services\EmailService();
                $emailService->sendOrderConfirmation($orderData, $order_items);
                $emailService->sendOrderConfirmation($orderData, $order_items, true);

                echo json_encode(['success' => true, 'message' => 'Payment successful']);
            } else {
                // Failed
                $this->order_model->updatePaymentStatus($order_id, 'failed', $data['transId']);
                echo json_encode(['success' => false, 'message' => 'Payment failed']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid signature']);
        }
    }

    public function simulateMoMoPayment()
    {
        // Force HTML content type since this is a UI page, overriding API's default JSON
        header('Content-Type: text/html');

        $orderId = $_GET['orderId'] ?? '';
        $amount = $_GET['amount'] ?? 0;
        
        // Generate valid signature for return to satisfy validation
        $momoService = new \App\Services\MoMoService();
        
        // We need to match the signature generation in verifySignature
        // But verifySignature uses credentials from instance.
        // Since we are mocking, we assume keys are empty string if not set.
        
        $partnerCode = MOMO_PARTNER_CODE;
        $accessKey = MOMO_ACCESS_KEY;
        $secretKey = MOMO_SECRET_KEY;
        
        $requestId = time();
        $transId = rand(100000, 999999);
        $message = "Success";
        $responseTime = time();
        $extraData = "";
        $payType = "qr";
        $orderInfo = "Payment for order";
        $orderType = "momo_wallet";
        $resultCode = 0;
        
        $rawHash = "accessKey=" . $accessKey .
                   "&amount=" . $amount .
                   "&extraData=" . $extraData .
                   "&message=" . $message .
                   "&orderId=" . $orderId .
                   "&orderInfo=" . $orderInfo .
                   "&orderType=" . $orderType .
                   "&partnerCode=" . $partnerCode .
                   "&payType=" . $payType .
                   "&requestId=" . $requestId .
                   "&responseTime=" . $responseTime .
                   "&resultCode=" . $resultCode .
                   "&transId=" . $transId;

        $signature = hash_hmac("sha256", $rawHash, $secretKey);
        
        $returnUrl = BASE_URL . "/api/payment/momo-return?" . 
            "partnerCode=" . $partnerCode .
            "&accessKey=" . $accessKey .
            "&requestId=" . $requestId .
            "&amount=" . $amount .
            "&orderId=" . $orderId .
            "&orderInfo=" . $orderInfo .
            "&orderType=" . $orderType .
            "&transId=" . $transId .
            "&resultCode=" . $resultCode .
            "&message=" . $message .
            "&payType=" . $payType .
            "&responseTime=" . $responseTime .
            "&extraData=" . $extraData .
            "&signature=" . $signature;
            
        // Also fire IPN call in background? 
        // Real MoMo does both. We need to at least update status. 
        // Best to just use the Return URL handler to update status for simulation?
        // But Return handler just redirects.
        // IPN handler updates the DB.
        
        // Let's modify handleMoMoCallback to be callable via GET for simulation? No, bad security.
        // Let's manually trigger the update logic here or simulate a curl?
        
        // For simplicity, we will simulate the "Action" on the page.
        // When user clicks "Pay", we will use JS to fetch the IPN url to update DB, then redirect.
        
        $ipnUrl = BASE_URL . "/api/payment/momo-ipn";
        $ipnData = json_encode([
            'partnerCode' => $partnerCode,
            'accessKey' => $accessKey,
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'orderType' => $orderType,
            'transId' => $transId,
            'resultCode' => $resultCode,
            'message' => $message,
            'payType' => $payType,
            'responseTime' => $responseTime,
            'extraData' => $extraData,
            'signature' => $signature
        ]);
            
        echo '<!DOCTYPE html>
        <html>
        <head>
            <title>MoMo Simulation</title>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
            <style>
                body { background-color: #fce4ec; display: flex; align-items: center; justify-content: center; height: 100vh; font-family: sans-serif; }
                .momo-card { background: white; padding: 30px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); text-align: center; max-width: 400px; width: 100%; border: 1px solid #d81b60; }
                .logo { width: 80px; margin-bottom: 20px; border-radius: 10px; }
                .amount { font-size: 2em; color: #d81b60; font-weight: bold; margin: 15px 0; }
                .btn-pay { background-color: #d81b60; color: white; width: 100%; padding: 12px; border-radius: 10px; font-weight: bold; border: none; margin-bottom: 10px; }
                .btn-fail { background-color: #6c757d; color: white; width: 100%; padding: 12px; border-radius: 10px; font-weight: bold; border: none; }
            </style>
        </head>
        <body>
            <div class="momo-card">
                <img src="https://upload.wikimedia.org/wikipedia/vi/f/fe/MoMo_Logo.png" alt="MoMo" class="logo">
                <h4>Thanh toán MoMo (Mô phỏng)</h4>
                <p>Đơn hàng: <strong>' . htmlspecialchars($orderId) . '</strong></p>
                <div class="amount">' . number_format($amount, 0, ',', '.') . ' ₫</div>
                <p class="text-muted small">Đây là trang mô phỏng vì chưa cấu hình key thật.</p>
                
                <button class="btn-pay" onclick="confirmPay()">
                    <i class="fas fa-check"></i> Thanh toán thành công
                </button>
                <button class="btn-fail" onclick="cancelPay()">
                    <i class="fas fa-times"></i> Hủy giao dịch
                </button>
            </div>
            <script>
                function confirmPay() {
                    // Call IPN to update DB
                    fetch("' . $ipnUrl . '", {
                        method: "POST",
                        headers: {"Content-Type": "application/json"},
                        body: JSON.stringify(' . $ipnData . ')
                    }).then(() => {
                        window.location.href = "' . $returnUrl . '";
                    });
                }
                function cancelPay() {
                    window.location.href = "' . BASE_URL . '/order-failed.php";
                }
            </script>
        </body>
        </html>';
        exit;
    }

    public function handleMoMoReturn()
    {
         // Handle GET return from MoMo (for UI redirect)
         // similar verification or just show success page
         // For API, we might just redirect the user to a frontend success page.
         // For now, let's just return JSON.
         
         $data = $_GET;
         $momoService = new \App\Services\MoMoService();
         
         if ($momoService->verifySignature($data)) {
             if ($data['resultCode'] == 0) {
                 // Redirect to frontend success
                 header("Location: " . BASE_URL . "/order-success.php?order_id=" . explode('_', $data['orderId'])[0]);
             } else {
                 header("Location: " . BASE_URL . "/order-failed.php");
             }
         } else {
             echo "Invalid Signature";
         }
    }

    public function getOrder()
    {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            return json_encode(['success' => false, 'message' => 'Not authenticated']);
        }

        $order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : null;

        if (!$order_id) {
            http_response_code(400);
            return json_encode(['success' => false, 'message' => 'Order ID is required']);
        }

        $order = $this->order_model->getOrderById($order_id);

        if (!$order || ($order['user_id'] != $_SESSION['user_id'] && $_SESSION['role'] !== 'admin')) {
            http_response_code(404);
            return json_encode(['success' => false, 'message' => 'Order not found']);
        }

        $order_items = $this->order_model->getOrderItems($order_id);

        http_response_code(200);
        return json_encode(['success' => true, 'order' => $order, 'items' => $order_items]);
    }

    public function getUserOrders()
    {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            return json_encode(['success' => false, 'message' => 'Not authenticated']);
        }

        $user_id = $_SESSION['user_id'];
        $orders = $this->order_model->getOrdersByUser($user_id);

        http_response_code(200);
        return json_encode(['success' => true, 'orders' => $orders]);
    }

    public function getAllOrders()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            http_response_code(403);
            return json_encode(['success' => false, 'message' => 'Unauthorized']);
        }

        $orders = $this->order_model->getAllOrders();

        http_response_code(200);
        return json_encode(['success' => true, 'orders' => $orders]);
    }

    public function updateOrderStatus()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            http_response_code(403);
            return json_encode(['success' => false, 'message' => 'Unauthorized']);
        }

        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['order_id'], $data['status'])) {
            http_response_code(400);
            return json_encode(['success' => false, 'message' => 'Missing required fields']);
        }

        $order_id = (int)$data['order_id'];
        $status = $data['status'];

        if ($this->order_model->updateOrderStatus($order_id, $status)) {
            http_response_code(200);
            return json_encode(['success' => true, 'message' => 'Order status updated']);
        } else {
            http_response_code(500);
            return json_encode(['success' => false, 'message' => 'Failed to update order status']);
        }
    }
}
