<?php

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$email = trim($input['email'] ?? '');
$otp = trim($input['otp'] ?? '');

if (empty($email) || empty($otp)) {
    echo json_encode(['success' => false, 'message' => 'Email and OTP are required']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email address']);
    exit;
}

if (!preg_match('/^\d{6}$/', $otp)) {
    echo json_encode(['success' => false, 'message' => 'Invalid OTP format']);
    exit;
}

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

if ($error) {
    echo json_encode(['success' => false, 'message' => 'Failed to send email. Please try again.']);
    exit;
}

if ($httpCode !== 200) {
    echo json_encode(['success' => false, 'message' => 'Failed to send OTP. Please try again.']);
    exit;
}

echo json_encode(['success' => true, 'message' => 'OTP sent successfully! Check your email.']);
