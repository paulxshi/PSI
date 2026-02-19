<?php

header('Content-Type: application/json');

require_once '../config/db.php';
require_once 'log_activity.php';

error_log("=== VERIFY OTP DEBUG ===");
error_log("Input: " . file_get_contents('php://input'));

$input = json_decode(file_get_contents('php://input'), true);
$email = trim($input['email'] ?? '');
$otp = trim($input['otp'] ?? '');
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

try {
    // Find the latest (unused) OTP from database
    $stmt = $pdo->prepare(
        'SELECT verification_id, otp, expires_at, is_used, otp_attempts 
         FROM otp_verifications 
         WHERE email = ? AND purpose = ? AND is_used = 0
         ORDER BY created_at DESC
         LIMIT 1'
    );
    $stmt->execute([$email, $purpose]);
    $verification = $stmt->fetch();

    if (!$verification) {
        error_log("No active OTP found for email: $email, purpose: $purpose");
        echo json_encode(['success' => false, 'message' => 'OTP session expired. Please request a new OTP']);
        exit;
    }

    $verification_id = $verification['verification_id'];
    error_log("Verification record found: verification_id = $verification_id");

    // Check if OTP has expired
    $expiresAt = strtotime($verification['expires_at']);
    $currentTime = time();

    if ($currentTime > $expiresAt) {
        error_log("OTP expired. Expires at: {$verification['expires_at']}, Current time: " . date('Y-m-d H:i:s'));
        
        // Mark as used to prevent further attempts
        $stmt = $pdo->prepare('UPDATE otp_verifications SET is_used = 1 WHERE verification_id = ?');
        $stmt->execute([$verification_id]);

        echo json_encode(['success' => false, 'message' => 'OTP has expired. Please request a new one']);
        exit;
    }

    // Check max attempts (max 3 attempts)
    if ($verification['otp_attempts'] >= 3) {
        error_log("Max OTP attempts exceeded (3)");
        
        // Mark as used
        $stmt = $pdo->prepare('UPDATE otp_verifications SET is_used = 1 WHERE verification_id = ?');
        $stmt->execute([$verification_id]);

        echo json_encode(['success' => false, 'message' => 'Too many failed attempts. Please request a new OTP']);
        exit;
    }

    // Verify OTP matches
    if ($otp !== $verification['otp']) {
        error_log("OTP mismatch - expected: " . $verification['otp'] . ", received: $otp");
        
        // Increment attempts
        $newAttempts = $verification['otp_attempts'] + 1;
        $stmt = $pdo->prepare('UPDATE otp_verifications SET otp_attempts = ? WHERE verification_id = ?');
        $stmt->execute([$newAttempts, $verification_id]);
        
        // Log failed OTP attempt
        logActivity('otp_failed', "OTP verification failed for purpose: $purpose. Attempts: $newAttempts/3", null, null, $email, 'examinee', 'warning');

        $attemptsLeft = 3 - $newAttempts;
        echo json_encode([
            'success' => false,
            'message' => "Invalid OTP. {$attemptsLeft} attempts remaining",
            'attempts_left' => $attemptsLeft
        ]);
        exit;
    }

    // OTP verified successfully - mark as used
    $stmt = $pdo->prepare('UPDATE otp_verifications SET is_used = 1, verified_at = NOW() WHERE verification_id = ?');
    $stmt->execute([$verification_id]);

    error_log("OTP verified successfully for verification_id: $verification_id");
    
    // Log successful OTP verification
    logActivity('otp_verified', "OTP verified successfully for purpose: $purpose", null, null, $email, 'examinee', 'info');

    echo json_encode([
        'success' => true,
        'message' => 'OTP verified successfully',
        'data' => ['email' => $email, 'verified_at' => date('Y-m-d H:i:s')]
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
