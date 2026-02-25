<?php
// Suppress all errors/warnings from being displayed (log them instead)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Start output buffering to catch any stray output
ob_start();

// Register shutdown function to catch fatal errors
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        ob_clean();
        header('Content-Type: application/json');
        
        $errorDetails = [
            'success' => false,
            'message' => 'Server configuration error: ' . $error['message'],
            'error_type' => 'PHP Fatal Error',
            'error_details' => $error['message'],
            'file' => basename($error['file']),
            'line' => $error['line'],
            'full_path' => $error['file'],
            'suggestion' => 'Check if all PHP extensions are enabled (curl, json, pdo) and restart Apache'
        ];
        
        echo json_encode($errorDetails, JSON_PRETTY_PRINT);
        error_log('[PAYMENT FATAL ERROR] ' . print_r($error, true));
    }
});

session_start();

// Clear any buffered output and set JSON header
ob_clean();
header('Content-Type: application/json');

// Check required PHP extensions
$requiredExtensions = ['curl', 'json', 'pdo'];
$missingExtensions = [];
foreach ($requiredExtensions as $ext) {
    if (!extension_loaded($ext)) {
        $missingExtensions[] = $ext;
    }
}

if (!empty($missingExtensions)) {
    echo json_encode([
        'success' => false,
        'message' => 'Server configuration error. Missing PHP extensions.',
        'debug' => [
            'missing_extensions' => $missingExtensions,
            'instructions' => 'Enable these extensions in php.ini and restart Apache'
        ]
    ]);
    exit;
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Session expired. Please log in again.'
    ]);
    exit;
}

require_once('../config/db.php');
require_once('../config/payment_config.php');

// Verify critical configuration
if (!isset($pdo) || !($pdo instanceof PDO)) {
    echo json_encode([
        'success' => false,
        'message' => 'Database connection error.',
        'debug' => 'PDO connection not initialized'
    ]);
    exit;
}

if (!defined('XENDIT_API_KEY') || !defined('XENDIT_INVOICE_URL')) {
    echo json_encode([
        'success' => false,
        'message' => 'Payment configuration error.',
        'debug' => 'Payment constants not defined'
    ]);
    exit;
}

// Get POST data
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['amount']) || !isset($input['description'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid payment data. Missing required fields.'
    ]);
    exit;
}

$user_id = $_SESSION['user_id'];
$amount = floatval($input['amount']);
$description = $input['description'];
$payment_method = isset($input['payment_method']) ? trim($input['payment_method']) : null;

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
    
    // Log current payment mode for tracking
    error_log('[CREATE PAYMENT] Processing payment in ' . getPaymentModeDisplay() . ' mode for user_id: ' . $user_id);
    
    // Use configuration from payment_config.php
    $xenditApiKey = XENDIT_API_KEY;
    $xenditUrl = XENDIT_INVOICE_URL;
    
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
        'success_redirect_url' => SUCCESS_REDIRECT_URL,
        'failure_redirect_url' => FAILURE_REDIRECT_URL,
        'currency' => CURRENCY,
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
