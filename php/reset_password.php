<?php

header('Content-Type: application/json');

require_once '../config/db.php';

error_log("=== RESET PASSWORD DEBUG ===");
error_log("Input: " . file_get_contents('php://input'));

$input = json_decode(file_get_contents('php://input'), true);
$reset_id = $input['reset_id'] ?? null;
$email = trim($input['email'] ?? '');
$newPassword = $input['password'] ?? '';
$confirmPassword = $input['confirm_password'] ?? '';

error_log("Reset ID: $reset_id");
error_log("Email: $email");

// Validate inputs
if (empty($reset_id) || empty($email) || empty($newPassword) || empty($confirmPassword)) {
    error_log("Missing required fields");
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    error_log("Invalid email format");
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit;
}

// Validate password strength
if (strlen($newPassword) < 8) {
    error_log("Password too short");
    echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters long']);
    exit;
}

// Check password match
if ($newPassword !== $confirmPassword) {
    error_log("Passwords do not match");
    echo json_encode(['success' => false, 'message' => 'Passwords do not match']);
    exit;
}

// Validate password complexity (at least one uppercase, one lowercase, one number)
if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/', $newPassword)) {
    error_log("Password does not meet complexity requirements");
    echo json_encode([
        'success' => false,
        'message' => 'Password must contain uppercase, lowercase, and numbers'
    ]);
    exit;
}

try {
    // Verify the password reset record exists and is valid
    $stmt = $pdo->prepare(
        'SELECT pr.reset_id, pr.user_id, pr.email, pr.expires_at, pr.is_used, u.email as user_email
         FROM password_resets pr
         JOIN users u ON pr.user_id = u.user_id
         WHERE pr.reset_id = ? AND pr.email = ? AND pr.is_used = 0'
    );
    $stmt->execute([$reset_id, $email]);
    $reset = $stmt->fetch();

    if (!$reset) {
        error_log("Invalid reset record: reset_id=$reset_id, email=$email");
        echo json_encode(['success' => false, 'message' => 'Invalid or expired password reset request']);
        exit;
    }

    $user_id = $reset['user_id'];
    error_log("Valid reset record found for user_id: $user_id");

    // Check if reset token is expired
    $expiresAt = strtotime($reset['expires_at']);
    if (time() > $expiresAt) {
        error_log("Reset token expired");
        echo json_encode(['success' => false, 'message' => 'Password reset request has expired']);
        exit;
    }

    // Hash the new password
    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 10]);
    error_log("Password hashed successfully");

    // Start transaction
    $pdo->beginTransaction();

    try {
        // Update user password
        $stmt = $pdo->prepare('UPDATE users SET password = ? WHERE user_id = ?');
        $stmt->execute([$hashedPassword, $user_id]);
        error_log("Password updated for user_id: $user_id");

        // Mark password reset as used
        $stmt = $pdo->prepare(
            'UPDATE password_resets SET is_used = 1, used_at = NOW() WHERE reset_id = ?'
        );
        $stmt->execute([$reset_id]);
        error_log("Password reset marked as used");

        // Commit transaction
        $pdo->commit();

        error_log("Password reset completed successfully");

        echo json_encode([
            'success' => true,
            'message' => 'Password has been reset successfully',
            'data' => [
                'email' => $email,
                'reset_at' => date('Y-m-d H:i:s')
            ]
        ]);

    } catch (Exception $e) {
        // Rollback transaction on error
        $pdo->rollBack();
        error_log("Transaction error: " . $e->getMessage());
        throw $e;
    }

} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
    exit;
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred']);
    exit;
}
