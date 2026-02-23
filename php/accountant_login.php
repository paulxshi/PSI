<?php
// Separate session for accountants
session_name('PSI_ACCOUNTANT');
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
        "SELECT user_id, password, role, first_name, last_name FROM users WHERE email = ? LIMIT 1"
    );
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        // Log failed accountant login attempt
        logActivity('login_failed', 'Accountant login failed - Email not found', null, null, $email, 'accountant', 'warning');
        
        echo json_encode(["success" => false, "message" => "Email not found"]);
        exit;
    }

    // Verify password
    if (!password_verify($password, $user['password'])) {
        // Log failed accountant login attempt
        logActivity('login_failed', 'Accountant login failed - Incorrect password', $user['user_id'], null, $email, 'accountant', 'warning');
        
        echo json_encode(["success" => false, "message" => "Incorrect password"]);
        exit;
    }

    // Check if user is an accountant
    if (!isset($user['role']) || $user['role'] !== 'accountant') {
        // Log unauthorized access attempt
        logActivity('login_failed', 'Accountant login failed - Unauthorized access attempt', $user['user_id'], null, $email, $user['role'] ?? 'unknown', 'error');
        
        echo json_encode(["success" => false, "message" => "You do not have accountant access"]);
        exit;
    }

    // Accountant login successful
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['email'] = $email;
    $_SESSION['first_name'] = $user['first_name'];
    $_SESSION['last_name'] = $user['last_name'];
    $_SESSION['role'] = 'accountant';

    error_log("Accountant login successful for user_id: " . $user['user_id']);
    
    // Log successful accountant login
    $username = $user['first_name'] . ' ' . $user['last_name'];
    logActivity('login_success', 'Accountant logged in successfully', $user['user_id'], $username, $email, 'accountant', 'info');

    echo json_encode([
        "success" => true,
        "message" => "Accountant login successful",
        "role" => "accountant",
        "redirect" => "../accountant/dashboard.html"
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
