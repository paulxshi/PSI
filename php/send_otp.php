<?php

header('Content-Type: application/json');

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

// Generate secure 6-digit OTP
$otp = sprintf('%06d', random_int(100000, 999999));
error_log("Generated OTP: $otp");

// OTP expiry time (10 minutes)
$currentTime = time();
$expiresAt = date('Y-m-d H:i:s', $currentTime + 600);
error_log("Expires at: $expiresAt");

// Store OTP verification data - using PHP session
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Store in session with a unique key based on email and purpose
$_SESSION['otp_data'][$purpose][$email] = [
    'otp' => $otp,
    'expires_at' => $expiresAt
];

error_log("OTP stored in session");

$N8N_WEBHOOK_URL = 'https://n8n.srv1069938.hstgr.cloud/webhook/otp-email';

$ch = curl_init($N8N_WEBHOOK_URL);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['email' => $email, 'otp_code' => $otp]));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

error_log("N8N Response: $response, HTTP Code: $httpCode");

if ($error) {
    unset($_SESSION['otp_data'][$purpose][$email]);
    echo json_encode(['success' => false, 'message' => 'Failed to send OTP email. Please try again.']);
    exit;
}

if ($httpCode !== 200) {
    unset($_SESSION['otp_data'][$purpose][$email]);
    echo json_encode(['success' => false, 'message' => 'Failed to send OTP. Please try again.']);
    exit;
}

// Return success with OTP for frontend storage
echo json_encode([
    'success' => true, 
    'message' => 'OTP sent successfully! Please check your email.',
    'expires_in' => 600,
    'otp' => $otp
]);
