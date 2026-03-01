<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Not logged in'
    ]);
    exit;
}

require_once('../../config/db.php');

$user_id = $_SESSION['user_id'];

try {
    // Get the most recent payment for this user along with user email
    $query = "SELECT p.payment_id, p.xendit_invoice_id, p.external_id, p.amount, p.status, 
                     p.paid_at, p.created_at, p.xendit_response, u.email
              FROM payments p
              INNER JOIN users u ON p.user_id = u.user_id
              WHERE p.user_id = :user_id 
              ORDER BY p.created_at DESC 
              LIMIT 1";
    
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $payment = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$payment) {
        echo json_encode([
            'success' => false,
            'message' => 'No payment found'
        ]);
        exit;
    }
    
    // Extract payment method from xendit_response if available
    $paymentMethod = 'Online Payment'; // Default value
    if (!empty($payment['xendit_response'])) {
        $xenditData = json_decode($payment['xendit_response'], true);
        if (isset($xenditData['payment_channel'])) {
            $paymentMethod = $xenditData['payment_channel'];
        } elseif (isset($xenditData['payment_method'])) {
            $paymentMethod = $xenditData['payment_method'];
        }
    }
    
    echo json_encode([
        'success' => true,
        'payment' => [
            'payment_id' => $payment['payment_id'],
            'external_id' => $payment['external_id'],
            'amount' => $payment['amount'],
            'status' => $payment['status'],
            'paid_at' => $payment['paid_at'],
            'created_at' => $payment['created_at'],
            'payment_method' => $paymentMethod,
            'email' => $payment['email']
        ]
    ]);
    
} catch (PDOException $e) {
    error_log("Error in check_payment_status.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Database error'
    ]);
}
?>
