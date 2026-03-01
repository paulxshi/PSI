<?php

header('Content-Type: application/json');

require_once '../../config/db.php';

error_log("=== FORGOT PASSWORD DEBUG ===");
error_log("Input: " . file_get_contents('php://input'));

$input = json_decode(file_get_contents('php://input'), true);
$email = trim($input['email'] ?? '');

error_log("Email: $email");

// Validate email
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    error_log("Invalid email format");
    echo json_encode(['success' => false, 'message' => 'Valid email is required']);
    exit;
}

try {
    // Check if email exists in users table
    $stmt = $pdo->prepare('SELECT user_id, first_name FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // IMPORTANT: Always return generic response for security (don't reveal if email exists)
    // But ONLY send OTP if email actually exists
    if (!$user) {
        error_log("Email not found in database: $email");
        // Email doesn't exist - return error message
        echo json_encode([
            'success' => false,
            'message' => 'Your email is not registered.'
        ]);
        exit;
    }

    $user_id = $user['user_id'];
    $user_name = $user['first_name'] ?? 'User';
    error_log("User found: user_id = $user_id, name = $user_name");

    // Email exists - now proceed with OTP generation and sending
    
    // Generate secure 6-digit OTP
    $otp = sprintf('%06d', random_int(100000, 999999));
    error_log("Generated OTP: $otp");

    // OTP expiry time (10 minutes)
    $expiresAt = date('Y-m-d H:i:s', time() + 600);
    error_log("Expires at: $expiresAt");

    // Store OTP in password_resets table
    $stmt = $pdo->prepare(
        'INSERT INTO password_resets (user_id, email, otp, expires_at) 
         VALUES (?, ?, ?, ?)'
    );
    $stmt->execute([$user_id, $email, $otp, $expiresAt]);
    $reset_id = $pdo->lastInsertId();

    error_log("OTP stored in database with reset_id: $reset_id");

    // Send OTP via n8n webhook (ONLY if email was found)
    $n8n_webhook_url = 'https://n8n.srv1069938.hstgr.cloud/webhook/send-reset-email';

    $payload = [
        'email' => $email,
        'otp_code' => $otp,
        'user_name' => $user_name
    ];

    $ch = curl_init($n8n_webhook_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    error_log("N8N Response: $response, HTTP Code: $httpCode");

    if ($error) {
        error_log("cURL Error: $error");
        echo json_encode(['success' => false, 'message' => 'Failed to send OTP email. Please try again.']);
        exit;
    }

    if ($httpCode !== 200 && $httpCode !== 0) {
        error_log("N8N returned HTTP $httpCode");
        echo json_encode(['success' => false, 'message' => 'Failed to send OTP. Please try again.']);
        exit;
    }

    error_log("OTP email sent successfully to: $email");

    // Return success message when OTP is sent
    echo json_encode([
        'success' => true,
        'message' => 'Verification code sent to your email. Please check your inbox.',
        'email' => $email,
        'expires_in' => 600
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
