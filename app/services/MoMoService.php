<?php

namespace App\Services;

class MoMoService
{
    private $partnerCode;
    private $accessKey;
    private $secretKey;
    private $apiEndpoint;

    public function __construct()
    {
        $this->partnerCode = MOMO_PARTNER_CODE;
        $this->accessKey = MOMO_ACCESS_KEY;
        $this->secretKey = MOMO_SECRET_KEY;
        $this->apiEndpoint = MOMO_API_ENDPOINT;
    }

    public function createPayment($order_id, $amount, $orderInfo = "Payment for order")
    {
        // Check if credentials are set
        if (empty($this->partnerCode) || empty($this->accessKey) || empty($this->secretKey)) {
            // Fallback to Simulation Mode if config is missing
            $orderId = (string)$order_id . "_" . time();
            return [
                'payUrl' => BASE_URL . '/api/payment/momo-simulate?orderId=' . $orderId . '&amount=' . $amount
            ];
        }

        $requestId = (string)time() . "";
        $orderId = (string)$order_id . "_" . time(); // Unique order ID for MoMo
        $requestType = "captureWallet";
        // $extraData = "email=test@test.com"; // Optional
        $extraData = "";

        $rawHash = "accessKey=" . $this->accessKey . 
                   "&amount=" . $amount . 
                   "&extraData=" . $extraData . 
                   "&ipnUrl=" . MOMO_NOTIFY_URL . 
                   "&orderId=" . $orderId . 
                   "&orderInfo=" . $orderInfo . 
                   "&partnerCode=" . $this->partnerCode . 
                   "&redirectUrl=" . MOMO_RETURN_URL . 
                   "&requestId=" . $requestId . 
                   "&requestType=" . $requestType;

        $signature = hash_hmac("sha256", $rawHash, $this->secretKey);

        $data = [
            'partnerCode' => $this->partnerCode,
            'partnerName' => "Test Wallet",
            'storeId' => "MomoTestStore",
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'redirectUrl' => MOMO_RETURN_URL,
            'ipnUrl' => MOMO_NOTIFY_URL,
            'lang' => 'vi',
            'extraData' => $extraData,
            'requestType' => $requestType,
            'signature' => $signature
        ];

        $result = $this->execPostRequest($this->apiEndpoint, json_encode($data));
        return json_decode($result, true);
    }

    public function verifySignature($data)
    {
        if (!isset($data['signature'])) return false;

        $rawHash = "accessKey=" . $this->accessKey .
                   "&amount=" . $data['amount'] .
                   "&extraData=" . $data['extraData'] .
                   "&message=" . $data['message'] .
                   "&orderId=" . $data['orderId'] .
                   "&orderInfo=" . $data['orderInfo'] .
                   "&orderType=" . $data['orderType'] .
                   "&partnerCode=" . $data['partnerCode'] .
                   "&payType=" . $data['payType'] .
                   "&requestId=" . $data['requestId'] .
                   "&responseTime=" . $data['responseTime'] .
                   "&resultCode=" . $data['resultCode'] .
                   "&transId=" . $data['transId'];

        $signature = hash_hmac("sha256", $rawHash, $this->secretKey);

        return hash_equals($signature, $data['signature']);
    }

    private function execPostRequest($url, $data)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data)
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        
        $result = curl_exec($ch);
        curl_close($ch);
        
        return $result;
    }
}
