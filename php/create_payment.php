<?php
// Suppress all errors/warnings from being displayed (log them instead)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Start output buffering to catch any stray output
ob_start();

session_start();

// Clear any buffered output and set JSON header
ob_clean();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Session expired. Please log in again.'
    ]);
    exit;
}

require_once('../config/db.php');
// Note: Using cURL for Xendit API, no SDK needed

// Get POST data
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['amount']) || !isset($input['description'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid payment data.'
    ]);
    exit;
}

$user_id = $_SESSION['user_id'];
$amount = floatval($input['amount']);
$description = $input['description'];

try {
    // Fetch user and examinee details
    $query = "
        SELECT 
            u.user_id,
            u.email,
            u.first_name,
            u.last_name,
            u.test_permit,
            e.examinee_id,
            e.status AS examinee_status,
            e.schedule_id
        FROM users u
        INNER JOIN examinees e ON u.user_id = e.user_id
        WHERE u.user_id = :user_id
    ";
    
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        echo json_encode([
            'success' => false,
            'message' => 'User not found.'
        ]);
        exit;
    }
    
    // Validate email format
    if (empty($user['email']) || !filter_var($user['email'], FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid email address in your profile. Please contact support.',
            'debug' => [
                'email_found' => $user['email'] ?? 'NULL',
                'first_name' => $user['first_name'],
                'last_name' => $user['last_name']
            ]
        ]);
        exit;
    }
    
    // Verify user is in correct stage
    if ($user['examinee_status'] !== 'Awaiting Payment') {
        echo json_encode([
            'success' => false,
            'message' => 'Payment not available at this stage.'
        ]);
        exit;
    }
    
    // Check if there's already a pending payment
    $checkQuery = "SELECT payment_id, xendit_invoice_id FROM payments 
                   WHERE user_id = :user_id 
                   AND examinee_id = :examinee_id 
                   AND status IN ('PENDING', 'PAID') 
                   ORDER BY created_at DESC 
                   LIMIT 1";
    $checkStmt = $pdo->prepare($checkQuery);
    $checkStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $checkStmt->bindParam(':examinee_id', $user['examinee_id'], PDO::PARAM_INT);
    $checkStmt->execute();
    $existingPayment = $checkStmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existingPayment && $existingPayment['status'] === 'PAID') {
        echo json_encode([
            'success' => false,
            'message' => 'Payment already completed.'
        ]);
        exit;
    }
    
    // Xendit API Configuration
    $xenditApiKey = 'xnd_development_LkvLnIqM2G6qlGDFyMtdBUlpUI5Pr2SiZHhz4qRtp6QAkya4ME9Q6rvyIL150t'; 
    $xenditUrl = 'https://api.xendit.co/v2/invoices';
    
    // Generate unique external_id
    $external_id = 'PMMA_' . $user['test_permit'] . '_' . time();
    
    // Sanitize and prepare customer data
    $customerEmail = trim($user['email']);
    $customerFirstName = trim($user['first_name']) ?: 'Examinee';
    $customerLastName = trim($user['last_name']) ?: 'User';
    
    // Prepare invoice data
    $invoiceData = [
        'external_id' => $external_id,
        'amount' => $amount,
        'description' => $description,
        'invoice_duration' => 86400, // 24 hours
        'customer' => [
            'given_names' => $customerFirstName,
            'surname' => $customerLastName,
            'email' => $customerEmail
        ],
        'customer_notification_preference' => [
            'invoice_created' => ['email'],
            'invoice_reminder' => ['email'],
            'invoice_paid' => ['email']
        ],
        'success_redirect_url' => 'http://localhost/PSI/payment_success.html',
        'failure_redirect_url' => 'http://localhost/PSI/payment_failed.html',
        'currency' => 'PHP',
        'items' => [
            [
                'name' => 'PMMA Examination Fee',
                'quantity' => 1,
                'price' => $amount
            ]
        ]
    ];
    
    // Create Xendit invoice via cURL
    $ch = curl_init($xenditUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($invoiceData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Basic ' . base64_encode($xenditApiKey . ':')
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    $xenditResponse = json_decode($response, true);
    
    if ($httpCode !== 200 && $httpCode !== 201) {
        error_log("Xendit API Error (HTTP $httpCode): " . print_r($xenditResponse, true));
        error_log("Xendit Request: " . json_encode($invoiceData));
        
        // Debug message - remove in production
        $debugMessage = $xenditResponse['message'] ?? $curlError ?? 'Unknown error';
        
        echo json_encode([
            'success' => false,
            'message' => 'Failed to create payment invoice. Please try again.',
            'debug' => [
                'http_code' => $httpCode,
                'xendit_error' => $debugMessage,
                'curl_error' => $curlError,
                'response' => $xenditResponse,
                'sent_email' => $customerEmail ?? null,
                'sent_name' => ($customerFirstName ?? '') . ' ' . ($customerLastName ?? '')
            ]
        ]);
        exit;
    }
    
    // Save payment record to database
    $insertQuery = "INSERT INTO payments 
                    (user_id, examinee_id, xendit_invoice_id, external_id, amount, status, created_at) 
                    VALUES 
                    (:user_id, :examinee_id, :xendit_invoice_id, :external_id, :amount, 'PENDING', NOW())";
    
    $insertStmt = $pdo->prepare($insertQuery);
    $insertStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $insertStmt->bindParam(':examinee_id', $user['examinee_id'], PDO::PARAM_INT);
    $insertStmt->bindParam(':xendit_invoice_id', $xenditResponse['id'], PDO::PARAM_STR);
    $insertStmt->bindParam(':external_id', $external_id, PDO::PARAM_STR);
    $insertStmt->bindParam(':amount', $amount);
    $insertStmt->execute();
    
    echo json_encode([
        'success' => true,
        'invoice_url' => $xenditResponse['invoice_url'],
        'external_id' => $external_id
    ]);
    
} catch (PDOException $e) {
    error_log("Database Error in create_payment.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Database error occurred. Please try again.',
        'debug' => $e->getMessage() // Remove in production
    ]);
} catch (Exception $e) {
    error_log("Error in create_payment.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred. Please try again.',
        'debug' => $e->getMessage() // Remove in production
    ]);
}
?>
