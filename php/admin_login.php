<?php
session_start();
header("Content-Type: application/json");

require_once "../config/db.php";
require_once "log_activity.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
    echo json_encode(["success" => false, "message" => "Email and password are required"]);
    exit;
}

try {
    // Check if user exists and get their details including role
    $stmt = $pdo->prepare(
        "SELECT user_id, password, role FROM users WHERE email = ? LIMIT 1"
    );
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        // Log failed admin login attempt
        logActivity('login_failed', 'Admin login failed - Email not found', null, null, $email, 'admin', 'warning');
        
        echo json_encode(["success" => false, "message" => "Email not found"]);
        exit;
    }

    // Verify password
    if (!password_verify($password, $user['password'])) {
        // Log failed admin login attempt
        logActivity('login_failed', 'Admin login failed - Incorrect password', $user['user_id'], null, $email, 'admin', 'warning');
        
        echo json_encode(["success" => false, "message" => "Incorrect password"]);
        exit;
    }

    // Check if user is an admin
    if (!isset($user['role']) || $user['role'] !== 'admin') {
        // Log unauthorized access attempt
        logActivity('login_failed', 'Admin login failed - Unauthorized access attempt', $user['user_id'], null, $email, 'admin', 'error');
        
        echo json_encode(["success" => false, "message" => "You do not have admin access"]);
        exit;
    }

    // Admin login successful
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['is_admin'] = true;
    $_SESSION['role'] = 'admin';

    error_log("Admin login successful for user_id: " . $user['user_id']);
    
    // Log successful admin login
    logActivity('login_success', 'Admin logged in successfully', $user['user_id'], 'Admin', $email, 'admin', 'info');

    echo json_encode([
        "success" => true,
        "message" => "Admin login successful"
    ]);
    exit;

} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    echo json_encode(["success" => false, "message" => "Database error occurred"]);
    exit;
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    echo json_encode(["success" => false, "message" => "An error occurred"]);
    exit;
}
