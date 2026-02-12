<?php

header('Content-Type: application/json');

error_log("=== SEND OTP DEBUG ===");
error_log("Input: " . file_get_contents('php://input'));

require_once __DIR__ . '/../config/db.php';

$input = json_decode(file_get_contents('php://input'), true);
$email = trim($input['email'] ?? '');
$purpose = $input['purpose'] ?? 'registration';

error_log("Email: $email, Purpose: $purpose");

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    error_log("Invalid email format");
    echo json_encode(['success' => false, 'message' => 'Valid email is required']);
    exit;
}

// Rate limiting: Check if OTP was sent recently (prevent spam)
$stmt = $pdo->prepare('SELECT created_at FROM otp_verifications WHERE email = ? AND purpose = ? AND verified_at IS NULL ORDER BY created_at DESC LIMIT 1');
$stmt->execute([$email, $purpose]);
$lastOtp = $stmt->fetch();

if ($lastOtp) {
    $lastTime = strtotime($lastOtp['created_at']);
    $currentTime = time();
    error_log("Last OTP time: " . $lastOtp['created_at'] . ", Current time: " . date('Y-m-d H:i:s'));
    if (($currentTime - $lastTime) < 60) {
        echo json_encode(['success' => false, 'message' => 'Please wait 60 seconds before requesting a new OTP']);
        exit;
    }
}

// Generate secure 6-digit OTP
$otp = sprintf('%06d', random_int(100000, 999999));
error_log("Generated OTP: $otp");

// OTP expiry time (10 minutes)
$currentTime = time();
$expiresAt = date('Y-m-d H:i:s', $currentTime + 600);
error_log("Current timestamp: $currentTime");
error_log("Expires at (computed): $expiresAt");

// Delete any existing unverified OTPs for this email
$stmt = $pdo->prepare('DELETE FROM otp_verifications WHERE email = ? AND purpose = ? AND verified_at IS NULL');
$stmt->execute([$email, $purpose]);

// Store OTP in database
$stmt = $pdo->prepare('INSERT INTO otp_verifications (email, otp, purpose, expires_at, verified_at, created_at) VALUES (?, ?, ?, ?, NULL, NOW())');
$insertResult = $stmt->execute([$email, $otp, $purpose, $expiresAt]);
error_log("Insert result: " . ($insertResult ? 'success' : 'failed'));

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
    $stmt = $pdo->prepare('DELETE FROM otp_verifications WHERE email = ? AND otp = ?');
    $stmt->execute([$email, $otp]);
    echo json_encode(['success' => false, 'message' => 'Failed to send OTP email. Please try again.']);
    exit;
}

if ($httpCode !== 200) {
    echo json_encode(['success' => false, 'message' => 'Failed to send OTP. Please try again.']);
    exit;
}

echo json_encode([
    'success' => true, 
    'message' => 'OTP sent successfully! Please check your email.',
    'expires_in' => 600
]);
