<?php
session_name('PSI_ACCOUNTANT');
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

$result = handleLogin($pdo, $email, $password, 'accountant', [], 5, 900);

if ($result['success']) {
    $result['redirect'] = "../accountant/dashboard.html";
}

echo json_encode($result);
exit;
