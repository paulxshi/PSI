<?php
require __DIR__ . '/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed');
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
    http_response_code(400);
    exit('Email and password are required.');
}

$stmt = $pdo->prepare('SELECT user_id, password FROM users WHERE email = :email LIMIT 1');
$stmt->execute([':email' => $email]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password'])) {
    http_response_code(401);
    exit('Invalid credentials.');
}

session_regenerate_id(true);
$_SESSION['user_id'] = $user['user_id'];
header('Location: dashboard.html');
exit;
