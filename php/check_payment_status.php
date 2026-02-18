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

require_once('../config/db.php');

$user_id = $_SESSION['user_id'];

try {
    // Get the most recent payment for this user
    $query = "SELECT payment_id, xendit_invoice_id, external_id, amount, status, paid_at, created_at 
              FROM payments 
              WHERE user_id = :user_id 
              ORDER BY created_at DESC 
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
    
    echo json_encode([
        'success' => true,
        'payment' => [
            'payment_id' => $payment['payment_id'],
            'external_id' => $payment['external_id'],
            'amount' => $payment['amount'],
            'status' => $payment['status'],
            'paid_at' => $payment['paid_at'],
            'created_at' => $payment['created_at']
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
