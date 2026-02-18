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
    "SELECT u.user_id, u.password, u.test_permit, u.status, u.role, u.first_name, u.last_name, u.email
     FROM users u
     WHERE u.email = :email AND u.test_permit = :permit 
     LIMIT 1"
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

/* Check user status - must be 'active' to login */
if ($user['status'] === 'incomplete') {
    echo json_encode([
        "success" => false, 
        "message" => "Your account is not yet activated. Please complete your registration and payment.",
        "redirect" => "registration.html"
    ]);
    exit;
}

if ($user['status'] === 'blocked') {
    echo json_encode([
        "success" => false, 
        "message" => "Your account has been blocked. Please contact support."
    ]);
    exit;
}

/* For examinees, check examinees table status */
if ($user['role'] === 'examinee') {
    $examineeStmt = $pdo->prepare(
        "SELECT status FROM examinees WHERE user_id = :user_id LIMIT 1"
    );
    $examineeStmt->execute([':user_id' => $user['user_id']]);
    $examinee = $examineeStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$examinee) {
        echo json_encode([
            "success" => false, 
            "message" => "Examinee record not found. Please complete your registration.",
            "redirect" => "registration.html"
        ]);
        exit;
    }
    
    /* Check if examinee status is 'Scheduled' */
    if ($examinee['status'] !== 'Scheduled') {
        echo json_encode([
            "success" => false, 
            "message" => "Please complete your exam schedule and payment before logging in.",
            "redirect" => "examsched.html"
        ]);
        exit;
    }
}

/* Login success */
session_regenerate_id(true);
$_SESSION['user_id'] = $user['user_id'];
$_SESSION['email'] = $user['email'];
$_SESSION['first_name'] = $user['first_name'];
$_SESSION['last_name'] = $user['last_name'];
$_SESSION['role'] = $user['role'];

echo json_encode([
    "success" => true,
    "message" => "Login successful",
    "role" => $user['role']
]);
exit;
