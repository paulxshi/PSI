<?php
// Xendit Webhook Handler for Payment Confirmation
// This script receives payment notifications from Xendit and updates the database

header('Content-Type: application/json');

require_once('config/db.php');
require_once('config/payment_config.php');

// Get the raw POST data
$rawPayload = file_get_contents('php://input');
$data = json_decode($rawPayload, true);

// Enhanced logging for debugging
error_log("====== XENDIT WEBHOOK RECEIVED ======");
error_log("Mode: " . getPaymentModeDisplay());
error_log("Timestamp: " . date('Y-m-d H:i:s'));
error_log("Raw Payload: " . $rawPayload);
error_log("Parsed Data: " . print_r($data, true));
error_log("Headers: " . print_r(getallheaders(), true));
error_log("=====================================");

// Verify webhook authenticity
// ⚠️ IMPORTANT: This is automatically enabled in production mode for security
$callbackToken = $_SERVER['HTTP_X_CALLBACK_TOKEN'] ?? '';

// In production mode, ALWAYS verify the webhook token
if (PAYMENT_MODE === 'production') {
    if (empty($callbackToken) || $callbackToken !== XENDIT_WEBHOOK_TOKEN) {
        error_log('[WEBHOOK SECURITY] Invalid callback token in production mode');
        http_response_code(403);
        echo json_encode(['error' => 'Invalid callback token']);
        exit;
    }
}

// In test mode, log if token doesn't match (but still allow for testing)
if (PAYMENT_MODE === 'test' && !empty($callbackToken)) {
    if ($callbackToken !== XENDIT_WEBHOOK_TOKEN) {
        error_log('[WEBHOOK WARNING] Callback token mismatch in test mode (Token: ' . $callbackToken . ')');
    }
}

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
    
    // Send payment confirmation email with schedule details
    try {
        // Fetch user details and schedule information
        $emailQuery = "
            SELECT 
                u.email,
                u.first_name,
                u.last_name,
                u.test_permit,
                s.scheduled_date,
                v.venue_name,
                v.region
            FROM users u
            INNER JOIN examinees e ON u.user_id = e.user_id
            INNER JOIN schedules s ON e.schedule_id = s.schedule_id
            INNER JOIN venue v ON s.venue_id = v.venue_id
            WHERE u.user_id = :user_id
        ";
        
        $emailStmt = $pdo->prepare($emailQuery);
        $emailStmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $emailStmt->execute();
        $userDetails = $emailStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($userDetails && !empty($userDetails['email'])) {
            // Format the date
            $dateObj = new DateTime($userDetails['scheduled_date']);
            $formattedDate = $dateObj->format('F j, Y'); // e.g., "March 15, 2026"
            
            // Prepare email data
            $emailData = [
                'email' => $userDetails['email'],
                'first_name' => $userDetails['first_name'],
                'last_name' => $userDetails['last_name'],
                'test_permit' => $userDetails['test_permit'],
                'scheduled_date' => $formattedDate,
                'venue_name' => $userDetails['venue_name'],
                'region' => $userDetails['region']
            ];
            
            // Log the data being sent for debugging
            error_log("Sending email data to n8n: " . json_encode($emailData));
            
            // Send email via n8n webhook
            $n8nWebhookUrl = 'https://n8n.srv1069938.hstgr.cloud/webhook/payment-confirmation';
            
            $ch = curl_init($n8nWebhookUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($emailData));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            
            $emailResponse = curl_exec($ch);
            $emailHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($emailHttpCode === 200) {
                error_log("Payment confirmation email sent successfully to: {$userDetails['email']}");
            } else {
                error_log("Failed to send payment confirmation email to: {$userDetails['email']} (HTTP: $emailHttpCode)");
            }
        }
    } catch (Exception $emailError) {
        // Don't fail the webhook if email fails - just log it
        error_log("Error sending payment confirmation email: " . $emailError->getMessage());
    }
    
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
