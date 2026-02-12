<?php

header('Content-Type: application/json');

error_log("=== VERIFY OTP DEBUG ===");
error_log("Input: " . file_get_contents('php://input'));

require_once __DIR__ . '/../config/db.php';

$input = json_decode(file_get_contents('php://input'), true);
$email = $input['email'] ?? '';
$otp = $input['otp'] ?? '';
$purpose = $input['purpose'] ?? 'registration';

error_log("Email: $email, OTP: $otp, Purpose: $purpose");

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

$stmt = $pdo->prepare("
    SELECT id, otp, expires_at, verified_at 
    FROM otp_verifications 
    WHERE email = ? 
    AND purpose = ? 
    AND verified_at IS NULL
    ORDER BY created_at DESC 
    LIMIT 1
");

error_log("SQL: " . $stmt->queryString);
error_log("Params: email=$email, purpose=$purpose");

$stmt->execute([$email, $purpose]);
$record = $stmt->fetch(PDO::FETCH_ASSOC);

error_log("Record found: " . ($record ? 'yes' : 'no'));
if ($record) {
    error_log("Record ID: " . $record['id']);
    error_log("Record OTP: " . $record['otp']);
    error_log("Record expires_at: " . $record['expires_at']);
    error_log("Record verified_at: " . $record['verified_at']);
    error_log("Current time: " . date('Y-m-d H:i:s'));
    error_log("strtotime(expires_at): " . strtotime($record['expires_at']));
    error_log("time(): " . time());
    error_log("Is expired: " . (strtotime($record['expires_at']) < time() ? 'yes' : 'no'));
}

if (!$record) {
    echo json_encode(['success' => false, 'message' => 'No pending OTP found for this email']);
    exit;
}

if (strtotime($record['expires_at']) < time()) {
    error_log("OTP is expired");
    echo json_encode(['success' => false, 'message' => 'OTP has expired. Please request a new one']);
    exit;
}

if ($otp !== $record['otp']) {
    error_log("OTP mismatch");
    echo json_encode(['success' => false, 'message' => 'Invalid OTP']);
    exit;
}

$updateStmt = $pdo->prepare("UPDATE otp_verifications SET verified_at = NOW() WHERE id = ?");
$updateResult = $updateStmt->execute([$record['id']]);
error_log("Update result: " . ($updateResult ? 'success' : 'failed'));

echo json_encode([
    'success' => true,
    'message' => 'OTP verified successfully',
    'data' => ['email' => $email, 'verified_at' => date('Y-m-d H:i:s')]
]);
