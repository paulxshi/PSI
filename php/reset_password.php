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
$otp = trim($input['otp'] ?? '');
$new_password = $input['new_password'] ?? '';
$confirm_password = $input['confirm_password'] ?? '';

if (empty($email) || empty($otp) || empty($new_password) || empty($confirm_password)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

if ($new_password !== $confirm_password) {
    echo json_encode(['success' => false, 'message' => 'Passwords do not match']);
    exit;
}

if (strlen($new_password) < 8) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters']);
    exit;
}

if (!preg_match('/[A-Z]/', $new_password)) {
    echo json_encode(['success' => false, 'message' => 'Password must contain at least one uppercase letter']);
    exit;
}

if (!preg_match('/[a-z]/', $new_password)) {
    echo json_encode(['success' => false, 'message' => 'Password must contain at least one lowercase letter']);
    exit;
}

if (!preg_match('/[0-9]/', $new_password)) {
    echo json_encode(['success' => false, 'message' => 'Password must contain at least one number']);
    exit;
}

// First verify OTP is valid
$stmt = $pdo->prepare('SELECT user_id FROM users WHERE email = :email AND otp = :otp AND otp_expiry > NOW()');
$stmt->execute([
    ':email' => $email,
    ':otp' => $otp
]);
$user = $stmt->fetch();

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'Invalid or expired OTP']);
    exit;
}

// Hash new password and clear OTP
$hash = password_hash($new_password, PASSWORD_DEFAULT);
$stmt = $pdo->prepare('UPDATE users SET password = :password, otp = NULL, otp_expiry = NULL WHERE email = :email');
$stmt->execute([
    ':password' => $hash,
    ':email' => $email
]);

echo json_encode(['success' => true, 'message' => 'Password reset successfully']);
