<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService
{
    private $mailer;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
        $this->setup();
    }

    private function setup()
    {
        try {
            // Server settings
            $this->mailer->isSMTP();
            $this->mailer->Host       = SMTP_HOST;
            $this->mailer->SMTPAuth   = true;
            $this->mailer->Username   = SMTP_USER;
            $this->mailer->Password   = SMTP_PASS;
            $this->mailer->SMTPSecure = SMTP_SECURE;
            $this->mailer->Port       = SMTP_PORT;

            // Recipients
            $this->mailer->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        } catch (Exception $e) {
            // Log error
            error_log("Email setup failed: " . $this->mailer->ErrorInfo);
        }
    }

    public function sendOrderConfirmation($order, $items, $is_admin = false)
    {
        try {
            $this->mailer->clearAddresses();
            
            if ($is_admin) {
                $this->mailer->addAddress(ADMIN_EMAIL, ADMIN_NAME);
                $subject = "New Order Notification #" . $order['id'];
            } else {
                $this->mailer->addAddress($order['email'], $order['full_name']);
                $subject = "Order Confirmation #" . $order['id'];
            }

            $this->mailer->isHTML(true);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $this->generateOrderHtml($order, $items, $is_admin);
            $this->mailer->AltBody = $this->generateOrderText($order, $items, $is_admin);

            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            error_log("Message could not be sent. Mailer Error: {$this->mailer->ErrorInfo}");
            return false;
        }
    }

    private function generateOrderHtml($order, $items, $is_admin)
    {
        $rows = '';
        foreach ($items as $item) {
            $rows .= "<tr>
                <td>{$item['name']}</td>
                <td>{$item['quantity']}</td>
                <td>" . number_format($item['price'], 0, ',', '.') . " ₫</td>
                <td>" . number_format($item['price'] * $item['quantity'], 0, ',', '.') . " ₫</td>
            </tr>";
        }

        $payment_status = ucfirst($order['payment_status']);
        $payment_method = strtoupper($order['payment_method']);
        
        $html = "
            <h2>Order #{$order['id']}</h2>
            <p><strong>Date:</strong> {$order['created_at']}</p>
            <p><strong>Customer:</strong> {$order['full_name']} ({$order['email']})</p>
            <p><strong>Shipping Address:</strong> {$order['shipping_address']}</p>
            <p><strong>Payment Method:</strong> $payment_method</p>
            <p><strong>Status:</strong> $payment_status</p>
            
            <table border='1' cellpadding='5' cellspacing='0' style='width: 100%; border-collapse: collapse;'>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    $rows
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan='3' align='right'><strong>Total Amount:</strong></td>
                        <td>" . number_format($order['total_amount'], 0, ',', '.') . " ₫</td>
                    </tr>
                    <tr>
                        <td colspan='3' align='right'><strong>Shipping Fee:</strong></td>
                        <td>" . number_format($order['shipping_cost'], 0, ',', '.') . " ₫</td>
                    </tr>
                    <tr>
                        <td colspan='3' align='right'><strong>Discount:</strong></td>
                        <td>-" . number_format($order['discount_amount'], 0, ',', '.') . " ₫</td>
                    </tr>
                     <tr>
                        <td colspan='3' align='right'><strong>Grand Total:</strong></td>
                        <td><strong>" . number_format($order['total_amount'] + $order['shipping_cost'] - $order['discount_amount'], 0, ',', '.') . " ₫</strong></td>
                    </tr>
                </tfoot>
            </table>
        ";

        return $html;
    }

    private function generateOrderText($order, $items, $is_admin)
    {
        // ... simplified text version
        return "Order #{$order['id']} details available in HTML version.";
    }

    public function sendContactMessage($name, $email, $message)
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress(ADMIN_EMAIL, ADMIN_NAME);
            $this->mailer->addReplyTo($email, $name);
            
            $this->mailer->isHTML(true);
            $this->mailer->Subject = "Contact Form: $name";
            $this->mailer->Body = "
                <h3>New Contact Message</h3>
                <p><strong>Name:</strong> $name</p>
                <p><strong>Email:</strong> $email</p>
                <p><strong>Message:</strong></p>
                <p>" . nl2br(htmlspecialchars($message)) . "</p>
            ";
            $this->mailer->AltBody = "Name: $name\nEmail: $email\n\nMessage:\n$message";

            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            error_log("Contact email failed: " . $this->mailer->ErrorInfo);
            return false;
        }
    }
}

