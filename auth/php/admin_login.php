<?php
/**
 * Admin Login
 */
session_start();
header("Content-Type: application/json");

require_once "../../config/db.php";
require_once "LoginHandler.php";

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

// Use centralized login handler - max 5 attempts, 15 min lockout
$result = handleLogin($pdo, $email, $password, 'admin', [], 5, 900);

echo json_encode($result);
exit;
