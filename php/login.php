<?php
session_start();
header("Content-Type: application/json");

require_once "../config/db.php";

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

/* Find user and verify test permit */
$stmt = $pdo->prepare(
    "SELECT user_id, password, test_permit FROM users WHERE email = :email AND test_permit = :permit LIMIT 1"
);
$stmt->execute([
    'email' => $email,
    'permit' => $test_permit
]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode(["success" => false, "message" => "Invalid email or test permit"]);
    exit;
}

/* Verify password */
if (!password_verify($password, $user['password'])) {
    echo json_encode(["success" => false, "message" => "Incorrect password"]);
    exit;
}

/* Login success */
session_regenerate_id(true);
$_SESSION['user_id'] = $user['user_id'];

echo json_encode([
    "success" => true,
    "message" => "Login successful"
]);
exit;
