<?php
// Xendit Webhook Handler for Payment Confirmation
// This script receives payment notifications from Xendit and updates the database

header('Content-Type: application/json');

require_once('config/db.php');

// Get the raw POST data
$rawPayload = file_get_contents('php://input');
$data = json_decode($rawPayload, true);

// Enhanced logging for debugging
error_log("====== XENDIT WEBHOOK RECEIVED ======");
error_log("Timestamp: " . date('Y-m-d H:i:s'));
error_log("Raw Payload: " . $rawPayload);
error_log("Parsed Data: " . print_r($data, true));
error_log("Headers: " . print_r(getallheaders(), true));
error_log("=====================================");

// Verify webhook authenticity (optional but recommended)
// You can verify the X-CALLBACK-TOKEN header matches your webhook verification token
$callbackToken = $_SERVER['HTTP_X_CALLBACK_TOKEN'] ?? '';
$expectedToken = 'your_webhook_verification_token_here'; // Set this in Xendit dashboard

// For development, we'll skip token verification, but enable in production
// if ($callbackToken !== $expectedToken) {
//     http_response_code(403);
//     echo json_encode(['error' => 'Invalid callback token']);
//     exit;
// }

// Validate required fields
if (!isset($data['id']) || !isset($data['status']) || !isset($data['external_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid webhook data']);
    exit;
}

$invoiceId = $data['id'];
$status = strtoupper($data['status']);
$externalId = $data['external_id'];

try {
    // Only process PAID status
    if ($status !== 'PAID') {
        // Update payment status for other statuses (EXPIRED, CANCELLED, etc.)
        $updateQuery = "UPDATE payments SET status = :status, updated_at = NOW() 
                        WHERE xendit_invoice_id = :invoice_id";
        $stmt = $pdo->prepare($updateQuery);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        $stmt->bindParam(':invoice_id', $invoiceId, PDO::PARAM_STR);
        $stmt->execute();
        
        http_response_code(200);
        echo json_encode(['message' => 'Status updated to ' . $status]);
        exit;
    }
    
    // Begin transaction for atomicity
    $pdo->beginTransaction();
    
    // 1. Update payments table
    $updatePaymentQuery = "UPDATE payments 
                           SET status = 'PAID', 
                               paid_at = NOW(), 
                               updated_at = NOW(),
                               xendit_response = :xendit_response
                           WHERE xendit_invoice_id = :invoice_id 
                           AND status != 'PAID'";
    
    $stmt = $pdo->prepare($updatePaymentQuery);
    $stmt->bindParam(':xendit_response', $rawPayload, PDO::PARAM_STR);
    $stmt->bindParam(':invoice_id', $invoiceId, PDO::PARAM_STR);
    $stmt->execute();
    
    $rowsAffected = $stmt->rowCount();
    
    if ($rowsAffected === 0) {
        // Payment already processed or not found
        $pdo->rollBack();
        http_response_code(200);
        echo json_encode(['message' => 'Payment already processed or not found']);
        exit;
    }
    
    // 2. Get user_id and examinee_id from payments table
    $getPaymentQuery = "SELECT user_id, examinee_id FROM payments WHERE xendit_invoice_id = :invoice_id";
    $stmt = $pdo->prepare($getPaymentQuery);
    $stmt->bindParam(':invoice_id', $invoiceId, PDO::PARAM_STR);
    $stmt->execute();
    $payment = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$payment) {
        $pdo->rollBack();
        http_response_code(404);
        echo json_encode(['error' => 'Payment record not found']);
        exit;
    }
    
    $userId = $payment['user_id'];
    $examineeId = $payment['examinee_id'];
    
    // 3. Update examinees table status to 'Scheduled' and examinee_status to 'Registered'
    $updateExamineeQuery = "UPDATE examinees 
                            SET status = 'Scheduled', 
                                examinee_status = 'Registered',
                                updated_at = NOW() 
                            WHERE examinee_id = :examinee_id 
                            AND status = 'Awaiting Payment'";
    
    $stmt = $pdo->prepare($updateExamineeQuery);
    $stmt->bindParam(':examinee_id', $examineeId, PDO::PARAM_INT);
    $stmt->execute();
    
    // 4. Update users table status to 'active'
    $updateUserQuery = "UPDATE users 
                        SET status = 'active', 
                            updated_at = NOW() 
                        WHERE user_id = :user_id 
                        AND status = 'incomplete'";
    
    $stmt = $pdo->prepare($updateUserQuery);
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    
    // Commit transaction
    $pdo->commit();
    
    // Log success
    error_log("Payment processed successfully for user_id: $userId, invoice_id: $invoiceId");
    
    // Send success response to Xendit
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Payment processed successfully',
        'user_id' => $userId,
        'invoice_id' => $invoiceId
    ]);
    
} catch (PDOException $e) {
    // Rollback on error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    error_log("Database Error in webhook.php: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode(['error' => 'Database error occurred']);
    
} catch (Exception $e) {
    // Rollback on error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    error_log("Error in webhook.php: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode(['error' => 'An error occurred']);
}
?>
