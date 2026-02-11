<?php
session_start();
header("Content-Type: application/json");

require_once "db.php";

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

/* Find user */
$stmt = $pdo->prepare(
    "SELECT user_id, password FROM users WHERE email = :email LIMIT 1"
);
$stmt->execute(['email' => $email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode(["success" => false, "message" => "Email not found"]);
    exit;
}

/* Verify password */
if (!password_verify($password, $user['password'])) {
    echo json_encode(["success" => false, "message" => "Incorrect password"]);
    exit;
}

/* Verify test permit */
$stmt = $pdo->prepare(
    "SELECT test_id FROM test WHERE user_id = :uid AND test_permit = :permit LIMIT 1"
);
$stmt->execute([
    'uid' => $user['user_id'],
    'permit' => $test_permit
]);

$test = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$test) {
    echo json_encode(["success" => false, "message" => "Invalid test permit"]);
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
