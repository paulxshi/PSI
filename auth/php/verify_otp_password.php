<?php

header('Content-Type: application/json');

require_once '../../config/db.php';

error_log("=== VERIFY OTP PASSWORD DEBUG ===");
error_log("Input: " . file_get_contents('php://input'));

$input = json_decode(file_get_contents('php://input'), true);
$email = trim($input['email'] ?? '');
$otp = trim($input['otp'] ?? '');

error_log("Email: $email");
error_log("OTP received: $otp");

// Validate inputs
if (empty($email) || empty($otp)) {
    error_log("Missing email or OTP");
    echo json_encode(['success' => false, 'message' => 'Email and OTP are required']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    error_log("Invalid email format");
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit;
}

if (!preg_match('/^\d{6}$/', $otp)) {
    error_log("Invalid OTP format");
    echo json_encode(['success' => false, 'message' => 'OTP must be 6 digits']);
    exit;
}

try {
    // Find the latest (unused) OTP for this email
    $stmt = $pdo->prepare(
        'SELECT pr.reset_id, pr.user_id, pr.otp, pr.expires_at, pr.is_used, pr.otp_attempts, u.user_id as user_exists
         FROM password_resets pr
         JOIN users u ON pr.user_id = u.user_id
         WHERE pr.email = ? AND pr.is_used = 0
         ORDER BY pr.created_at DESC
         LIMIT 1'
    );
    $stmt->execute([$email]);
    $reset = $stmt->fetch();

    if (!$reset) {
        error_log("No active password reset found for email: $email");
        echo json_encode(['success' => false, 'message' => 'No password reset request found or already used']);
        exit;
    }

    $reset_id = $reset['reset_id'];
    error_log("Reset record found: reset_id = $reset_id");

    // Check if OTP has expired
    $expiresAt = strtotime($reset['expires_at']);
    $currentTime = time();

    if ($currentTime > $expiresAt) {
        error_log("OTP expired. Expires at: {$reset['expires_at']}, Current time: " . date('Y-m-d H:i:s'));
        
        // Mark as used to prevent further attempts
        $stmt = $pdo->prepare('UPDATE password_resets SET is_used = 1, used_at = NOW() WHERE reset_id = ?');
        $stmt->execute([$reset_id]);

        echo json_encode(['success' => false, 'message' => 'OTP has expired. Please request a new one']);
        exit;
    }

    // Check max attempts (max 3 attempts)
    if ($reset['otp_attempts'] >= 3) {
        error_log("Max OTP attempts exceeded (3)");
        
        // Mark as used
        $stmt = $pdo->prepare('UPDATE password_resets SET is_used = 1, used_at = NOW() WHERE reset_id = ?');
        $stmt->execute([$reset_id]);

        echo json_encode(['success' => false, 'message' => 'Too many failed attempts. Please request a new OTP']);
        exit;
    }

    // Verify OTP matches
    if ($otp !== $reset['otp']) {
        error_log("OTP mismatch - expected: " . $reset['otp'] . ", received: $otp");
        
        // Increment attempts
        $newAttempts = $reset['otp_attempts'] + 1;
        $stmt = $pdo->prepare('UPDATE password_resets SET otp_attempts = ? WHERE reset_id = ?');
        $stmt->execute([$newAttempts, $reset_id]);

        $attemptsLeft = 3 - $newAttempts;
        echo json_encode([
            'success' => false,
            'message' => "Invalid OTP. {$attemptsLeft} attempts remaining",
            'attempts_left' => $attemptsLeft
        ]);
        exit;
    }

    // OTP verified successfully
    error_log("OTP verified successfully for reset_id: $reset_id");

    // Generate a reset token (valid for 15 minutes)
    $reset_token = bin2hex(random_bytes(32));
    $token_expires = date('Y-m-d H:i:s', time() + 900); // 15 minutes

    // Store reset token temporarily (you can use a separate table or session)
    // For now, we'll return it to the frontend to use in next step
    
    echo json_encode([
        'success' => true,
        'message' => 'OTP verified successfully',
        'data' => [
            'reset_id' => $reset_id,
            'email' => $email,
            'verified_at' => date('Y-m-d H:i:s')
        ]
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
