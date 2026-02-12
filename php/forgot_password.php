<?php

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

require_once __DIR__ . '/../config/db.php';

$input = json_decode(file_get_contents('php://input'), true);
$email = trim($input['email'] ?? '');

if (empty($email)) {
    echo json_encode(['success' => false, 'message' => 'Email is required']);
    exit;
}

// Check if email exists in database
$stmt = $pdo->prepare('SELECT user_id FROM users WHERE email = :email');
$stmt->execute([':email' => $email]);
$user = $stmt->fetch();

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'Email not found']);
    exit;
}

// Generate 6-digit OTP
$otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
$otp_expiry = date('Y-m-d H:i:s', strtotime('+5 minutes'));

// Update OTP in database
$stmt = $pdo->prepare('UPDATE users SET otp = :otp, otp_expiry = :otp_expiry WHERE email = :email');
$stmt->execute([
    ':otp' => $otp,
    ':otp_expiry' => $otp_expiry,
    ':email' => $email
]);

// N8n Webhook URL
$N8N_WEBHOOK_URL = 'https://n8n.srv1069938.hstgr.cloud/webhook-test/otp-email';

// Forward request to N8n webhook
$ch = curl_init($N8N_WEBHOOK_URL);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['email' => $email, 'otp' => $otp]));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo json_encode(['success' => false, 'message' => 'Failed to send OTP']);
    exit;
}

// Return N8n response (in production, you might want to handle this differently)
echo json_encode(['success' => true, 'message' => 'OTP sent to your email']);
