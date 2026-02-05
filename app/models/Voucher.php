<?php

namespace App\Models;

class Voucher
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getByCode($code)
    {
        $query = "SELECT * FROM vouchers WHERE code = :code AND is_active = 1 AND valid_until > NOW() AND (max_uses = -1 OR current_uses < max_uses)";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':code', $code);
        $stmt->execute();
        
        return $stmt->fetch();
    }

    public function validateVoucher($code, $order_amount)
    {
        $voucher = $this->getByCode($code);
        
        if (!$voucher) {
            return ['valid' => false, 'message' => 'Voucher không tồn tại hoặc đã hết hạn'];
        }
        
        if ($order_amount < $voucher['min_order_amount']) {
            return ['valid' => false, 'message' => 'Số tiền đơn hàng không đủ để áp dụng voucher này'];
        }
        
        return ['valid' => true, 'voucher' => $voucher];
    }

    public function calculateDiscount($voucher, $order_amount)
    {
        $discount = 0;
        
        if ($voucher['discount_percent'] > 0) {
            $discount = ($order_amount * $voucher['discount_percent']) / 100;
        } else if ($voucher['discount_amount'] > 0) {
            $discount = $voucher['discount_amount'];
        }
        
        return min($discount, $order_amount);
    }

    public function incrementUsage($code)
    {
        $query = "UPDATE vouchers SET current_uses = current_uses + 1 WHERE code = :code";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':code', $code);
        
        return $stmt->execute();
    }

    public function create($code, $discount_percent, $discount_amount, $max_uses, $min_order_amount, $valid_from, $valid_until)
    {
        $query = "INSERT INTO vouchers (code, discount_percent, discount_amount, max_uses, min_order_amount, valid_from, valid_until)
                  VALUES (:code, :discount_percent, :discount_amount, :max_uses, :min_order_amount, :valid_from, :valid_until)";
        
        $stmt = $this->db->prepare($query);
        
        $stmt->bindParam(':code', $code);
        $stmt->bindParam(':discount_percent', $discount_percent);
        $stmt->bindParam(':discount_amount', $discount_amount);
        $stmt->bindParam(':max_uses', $max_uses, \PDO::PARAM_INT);
        $stmt->bindParam(':min_order_amount', $min_order_amount);
        $stmt->bindParam(':valid_from', $valid_from);
        $stmt->bindParam(':valid_until', $valid_until);
        
        return $stmt->execute();
    }
}
