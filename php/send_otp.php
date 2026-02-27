<?php

header('Content-Type: application/json');

require_once '../config/db.php';

error_log("=== SEND OTP DEBUG ===");
error_log("Input: " . file_get_contents('php://input'));

$input = json_decode(file_get_contents('php://input'), true);
$email = trim($input['email'] ?? '');
$purpose = $input['purpose'] ?? 'registration';

error_log("Email: $email, Purpose: $purpose");

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    error_log("Invalid email format");
    echo json_encode(['success' => false, 'message' => 'Valid email is required']);
    exit;
}

if ($purpose === 'registration') {
    $checkStmt = $pdo->prepare('SELECT user_id FROM users WHERE email = ? LIMIT 1');
    $checkStmt->execute([$email]);
    
    if ($checkStmt->fetch()) {
        error_log("Email already exists: $email");
        echo json_encode(['success' => false, 'message' => 'This email is already registered. Please use a different email or login to your account.']);
        exit;
    }
}

try {
    // Generate secure 6-digit OTP
    $otp = sprintf('%06d', random_int(100000, 999999));
    error_log("Generated OTP: $otp");

    // OTP expiry time (10 minutes)
    $expiresAt = date('Y-m-d H:i:s', time() + 600);
    error_log("Expires at: $expiresAt");

    // Store OTP in otp_verifications table
    $stmt = $pdo->prepare(
        'INSERT INTO otp_verifications (email, otp, purpose, expires_at) 
         VALUES (?, ?, ?, ?)'
    );
    $stmt->execute([$email, $otp, $purpose, $expiresAt]);
    $verification_id = $pdo->lastInsertId();

    error_log("OTP stored in database with verification_id: $verification_id");

    // Send OTP via n8n webhook
    $N8N_WEBHOOK_URL = 'https://n8n.srv1069938.hstgr.cloud/webhook/otp-email';

    $ch = curl_init($N8N_WEBHOOK_URL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['email' => $email, 'otp_code' => $otp]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    error_log("N8N Response: $response, HTTP Code: $httpCode");

    if ($error) {
        error_log("cURL Error: $error");
        echo json_encode(['success' => false, 'message' => 'Failed to send OTP email. Please try again.']);
        exit;
    }

    if ($httpCode !== 200 && $httpCode !== 0) {
        error_log("N8N returned HTTP $httpCode");
        echo json_encode(['success' => false, 'message' => 'Failed to send OTP. Please try again.']);
        exit;
    }

    error_log("OTP email sent successfully");

    // Return success (DO NOT send OTP to frontend for security)
    echo json_encode([
        'success' => true, 
        'message' => 'OTP sent successfully! Please check your email.',
        'expires_in' => 600
    ]);

} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
    exit;
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred']);
    exit;
}
