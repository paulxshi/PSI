<?php

header('Content-Type: application/json');

error_log("=== VERIFY OTP DEBUG ===");
error_log("Input: " . file_get_contents('php://input'));

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$input = json_decode(file_get_contents('php://input'), true);
$email = $input['email'] ?? '';
$otp = $input['otp'] ?? '';
$purpose = $input['purpose'] ?? 'registration';
 
error_log("Received email: $email");
error_log("Received OTP: $otp");
error_log("Received purpose: $purpose");

if (empty($email) || empty($otp)) {
    error_log("Missing email or OTP");
    echo json_encode(['success' => false, 'message' => 'Email and OTP are required']);
    exit;
}

if (!preg_match('/^\d{6}$/', $otp)) {
    error_log("Invalid OTP format");
    echo json_encode(['success' => false, 'message' => 'Invalid OTP format']);
    exit;
}

// Check session for OTP data
if (!isset($_SESSION['otp_data'][$purpose][$email])) {
    error_log("No OTP found in session for email: $email, purpose: $purpose");
    error_log("Session data: " . print_r($_SESSION['otp_data'] ?? [], true));
    echo json_encode(['success' => false, 'message' => 'OTP session expired. Please request a new OTP']);
    exit;
}

$sessionData = $_SESSION['otp_data'][$purpose][$email];
error_log("Session data: " . print_r($sessionData, true));

// Check if OTP is expired
if (strtotime($sessionData['expires_at']) < time()) {
    error_log("OTP is expired");
    unset($_SESSION['otp_data'][$purpose][$email]);
    echo json_encode(['success' => false, 'message' => 'OTP has expired. Please request a new one']);
    exit;
}

// Verify OTP matches
if ($otp !== $sessionData['otp']) {
    error_log("OTP mismatch - expected: " . $sessionData['otp'] . ", received: $otp");
    echo json_encode(['success' => false, 'message' => 'Invalid OTP']);
    exit;
}

// Clear OTP from session after successful verification
unset($_SESSION['otp_data'][$purpose][$email]);

error_log("OTP verified successfully");

echo json_encode([
    'success' => true,
    'message' => 'OTP verified successfully',
    'data' => ['email' => $email, 'verified_at' => date('Y-m-d H:i:s')]
]);
