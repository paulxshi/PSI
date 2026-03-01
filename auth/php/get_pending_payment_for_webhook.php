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
    $query = "SELECT payment_id, xendit_invoice_id, external_id, amount, created_at 
              FROM payments 
              WHERE user_id = :user_id 
              AND status = 'PENDING'
              ORDER BY created_at DESC 
              LIMIT 1";
    
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $payment = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$payment) {
        echo json_encode([
            'success' => false,
            'message' => 'No pending payment found'
        ]);
        exit;
    }
    
    echo json_encode([
        'success' => true,
        'payment' => $payment
    ]);
    
} catch (PDOException $e) {
    error_log("Error in get_pending_payment_for_webhook.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
