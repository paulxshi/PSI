<?php
// Xendit Webhook Handler for Payment Confirmation
// This script receives payment notifications from Xendit and updates the database
// IMPORTANT: Must ALWAYS return 200 to Xendit to acknowledge receipt (except 403 for bad token)

// Register shutdown function to catch fatal errors and still return 200
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        if (!headers_sent()) {
            http_response_code(200);
            header('Content-Type: application/json');
        }
        error_log('[WEBHOOK FATAL] ' . $error['message'] . ' in ' . $error['file'] . ':' . $error['line']);
        echo json_encode(['success' => false, 'message' => 'Internal error acknowledged']);
    }
});

header('Content-Type: application/json');

require_once('../config/db.php');
require_once('../config/payment_config.php');

// Check request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed. Use POST.']);
    exit;
}

// Get the raw POST data
$rawPayload = file_get_contents('php://input');
$data = json_decode($rawPayload, true);

// Check if JSON decoding failed
if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
    $jsonError = json_last_error_msg();
    error_log('[WEBHOOK ERROR] Invalid JSON payload: ' . $jsonError . ' | Raw: ' . substr($rawPayload, 0, 500));
    // Return 200 to stop Xendit retries on bad payload
    http_response_code(200);
    echo json_encode(['error' => 'Invalid JSON payload', 'acknowledged' => true]);
    exit;
}

// Log webhook receipt
error_log("====== XENDIT WEBHOOK RECEIVED ======");
error_log("Mode: " . getPaymentModeDisplay());
error_log("Timestamp: " . date('Y-m-d H:i:s'));
error_log("Raw Payload: " . $rawPayload);
error_log("=====================================");

// Verify webhook authenticity via X-Callback-Token header
$callbackToken = $_SERVER['HTTP_X_CALLBACK_TOKEN'] ?? '';

if (PAYMENT_MODE === 'production') {
    if (empty($callbackToken) || $callbackToken !== XENDIT_WEBHOOK_TOKEN) {
        error_log('[WEBHOOK SECURITY] Invalid callback token in production mode. Received: ' . substr($callbackToken, 0, 10) . '...');
        // 403 is correct for token mismatch — Xendit will NOT retry on 403
        http_response_code(403);
        echo json_encode(['error' => 'Invalid callback token']);
        exit;
    }
}

// In test mode, log if token doesn't match
if (PAYMENT_MODE === 'test' && !empty($callbackToken)) {
    if ($callbackToken !== XENDIT_WEBHOOK_TOKEN) {
        error_log('[WEBHOOK WARNING] Callback token mismatch in test mode');
    }
}

// Validate required fields
if (!isset($data['id']) || !isset($data['status']) || !isset($data['external_id'])) {
    // Return 200 to acknowledge — bad data won't get better on retry
    http_response_code(200);
    echo json_encode(['error' => 'Missing required fields', 'acknowledged' => true]);
    exit;
}

$invoiceId  = $data['id'];
$status     = strtoupper($data['status']);
$externalId = $data['external_id'];

// Extract payment method directly from the webhook payload (no extra API call needed)
$paymentMethod = 'UNKNOWN';
$paymentChannel = '';
$paymentSource = '';

// Get top-level payment_method and payment_channel
if (!empty($data['payment_method'])) {
    $paymentMethod = strtoupper($data['payment_method']);
}
if (!empty($data['payment_channel'])) {
    $paymentChannel = strtoupper($data['payment_channel']);
}

// Get payment_details.source (e.g., "PayMaya/ Maya Wallet", "GCash", etc.)
if (!empty($data['payment_details']['source'])) {
    $paymentSource = $data['payment_details']['source'];
}

// Build a descriptive payment method string
if (!empty($paymentSource)) {
    // Use the source as the primary label since it's the most human-readable
    // e.g., "QRPH - PayMaya/ Maya Wallet" or "EWALLET - GCash"
    $paymentMethod = !empty($paymentChannel) 
        ? $paymentChannel . ' - ' . $paymentSource 
        : $paymentMethod . ' - ' . $paymentSource;
} elseif (!empty($paymentChannel)) {
    $paymentMethod = $paymentChannel;
}

// If the webhook includes a payments array, extract richer details (older webhook format)
if (isset($data['payments']) && is_array($data['payments']) && !empty($data['payments'])) {
    $pmt         = $data['payments'][0];
    $method      = $pmt['payment_method'] ?? null;
    $ewalletType = $pmt['ewallet_type'] ?? null;
    $channelCode = $pmt['channel_code'] ?? null;
    $cardBrand   = $pmt['card_brand'] ?? null;

    if (!empty($ewalletType)) {
        $paymentMethod = strtoupper($method) . ' - ' . strtoupper($ewalletType);
    } elseif (!empty($channelCode)) {
        $paymentMethod = strtoupper($method) . ' - ' . strtoupper($channelCode);
    } elseif (!empty($cardBrand)) {
        $paymentMethod = strtoupper($method) . ' - ' . strtoupper($cardBrand);
    } elseif (!empty($method)) {
        $paymentMethod = strtoupper($method);
    }
}

error_log("[WEBHOOK] Invoice: $invoiceId | Status: $status | Method: $paymentMethod");

try {
    // Handle non-PAID statuses (EXPIRED, CANCELLED, etc.)
    if ($status !== 'PAID') {
        $updateQuery = "UPDATE payments SET status = :status, updated_at = NOW(), channel = :payment_method 
                        WHERE xendit_invoice_id = :invoice_id";
        $stmt = $pdo->prepare($updateQuery);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        $stmt->bindParam(':payment_method', $paymentMethod, PDO::PARAM_STR);
        $stmt->bindParam(':invoice_id', $invoiceId, PDO::PARAM_STR);
        $stmt->execute();
        
        http_response_code(200);
        echo json_encode(['message' => 'Status updated to ' . $status]);
        exit;
    }
    
    // ===== Process PAID status =====
    $pdo->beginTransaction();
    
    // 1. Update payments table
    $updatePaymentQuery = "UPDATE payments 
                           SET status = 'PAID', 
                               paid_at = NOW(), 
                               updated_at = NOW(),
                               xendit_response = :xendit_response,
                               channel = :payment_method
                           WHERE xendit_invoice_id = :invoice_id
                           AND status != 'PAID'";
    
    $stmt = $pdo->prepare($updatePaymentQuery);
    $stmt->bindParam(':xendit_response', $rawPayload, PDO::PARAM_STR);
    $stmt->bindParam(':payment_method', $paymentMethod, PDO::PARAM_STR);
    $stmt->bindParam(':invoice_id', $invoiceId, PDO::PARAM_STR);
    $stmt->execute();
    
    $rowsAffected = $stmt->rowCount();
    
    if ($rowsAffected === 0) {
        // Payment already processed or not found — acknowledge to stop retries
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
        // Return 200 even on not found — Xendit retrying won't help
        http_response_code(200);
        error_log("[WEBHOOK ERROR] Payment record not found for invoice: $invoiceId");
        echo json_encode(['message' => 'Payment record not found', 'acknowledged' => true]);
        exit;
    }
    
    $userId     = $payment['user_id'];
    $examineeId = $payment['examinee_id'];
    
    // 3. Update examinees table
    $updateExamineeQuery = "UPDATE examinees 
                            SET status = 'Scheduled', 
                                examinee_status = 'Registered',
                                updated_at = NOW() 
                            WHERE examinee_id = :examinee_id 
                            AND status = 'Awaiting Payment'";
    
    $stmt = $pdo->prepare($updateExamineeQuery);
    $stmt->bindParam(':examinee_id', $examineeId, PDO::PARAM_INT);
    $stmt->execute();
    
    // 4. Update users table
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
    
    error_log("[WEBHOOK] Payment processed successfully for user_id: $userId, invoice: $invoiceId");
    
    // ===== Return 200 IMMEDIATELY to Xendit before sending email =====
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Payment processed successfully',
        'user_id' => $userId,
        'invoice_id' => $invoiceId
    ]);
    
    // Flush the response to Xendit so it gets 200 right away
    if (function_exists('fastcgi_finish_request')) {
        fastcgi_finish_request();
    } else {
        // For Apache mod_php: flush output buffers
        if (ob_get_level() > 0) ob_end_flush();
        flush();
    }
    
    // ===== Send email AFTER returning 200 (non-blocking) =====
    try {
        $emailQuery = "
            SELECT 
                u.email, u.first_name, u.last_name, u.test_permit,
                s.scheduled_date,
                v.venue_name, v.region
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
            $dateObj = new DateTime($userDetails['scheduled_date']);
            $formattedDate = $dateObj->format('F j, Y');
            
            $emailData = [
                'email'          => $userDetails['email'],
                'first_name'     => $userDetails['first_name'],
                'last_name'      => $userDetails['last_name'],
                'test_permit'    => $userDetails['test_permit'],
                'scheduled_date' => $formattedDate,
                'venue_name'     => $userDetails['venue_name'],
                'region'         => $userDetails['region']
            ];
            
            $n8nWebhookUrl = 'https://n8n.srv1069938.hstgr.cloud/webhook/payment-confirmation';
            
            $ch = curl_init($n8nWebhookUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($emailData));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
            
            $emailResponse = curl_exec($ch);
            $emailHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($emailHttpCode === 200) {
                error_log("[WEBHOOK] Email sent to: {$userDetails['email']}");
            } else {
                error_log("[WEBHOOK] Email failed for: {$userDetails['email']} (HTTP: $emailHttpCode)");
            }
        }
    } catch (Exception $emailError) {
        error_log("[WEBHOOK] Email error: " . $emailError->getMessage());
    }
    
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("[WEBHOOK DB ERROR] " . $e->getMessage());
    
    // ALWAYS return 200 to Xendit — DB errors won't be fixed by retrying
    http_response_code(200);
    echo json_encode(['error' => 'Database error acknowledged', 'acknowledged' => true]);
    
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("[WEBHOOK ERROR] " . $e->getMessage());
    
    // ALWAYS return 200 to Xendit
    http_response_code(200);
    echo json_encode(['error' => 'Error acknowledged', 'acknowledged' => true]);
}
?>
