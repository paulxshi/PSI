<?php
/**
 * Examinee Login
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
$test_permit = trim($_POST['test_permit'] ?? '');

if ($email === '' || $password === '' || $test_permit === '') {
    echo json_encode(["success" => false, "message" => "All fields are required"]);
    exit;
}

// Use centralized login handler - max 8 attempts, 15 min lockout
$result = handleLogin($pdo, $email, $password, 'examinee', ['test_permit' => $test_permit], 8, 900);

echo json_encode($result);
exit;
