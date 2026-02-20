<?php
session_start();
header("Content-Type: application/json");

require_once "../config/db.php";
require_once "RateLimiter.php";
require_once "log_activity.php";

// Initialize rate limiter
$rateLimiter = new RateLimiter($pdo);
$clientIP = $_SERVER['REMOTE_ADDR'];

// Check rate limit - max 8 login attempts, blocked for 15 minutes after exceeding
if (!$rateLimiter->checkLimit('login', $clientIP, 8, 900)) {
    $secondsRemaining = $rateLimiter->getTimeUntilUnblocked('login', $clientIP);
    
    // Convert to user-friendly time format
    if ($secondsRemaining < 60) {
        $timeDisplay = ceil($secondsRemaining) . " second" . (ceil($secondsRemaining) != 1 ? "s" : "");
    } else {
        $minutes = ceil($secondsRemaining / 60);
        $timeDisplay = $minutes . " minute" . ($minutes != 1 ? "s" : "");
    }
    
    $message = "Too many login attempts. Please try again in " . $timeDisplay . ".";
    
    echo json_encode([
        "success" => false, 
        "message" => $message,
        "retry_after" => $secondsRemaining
    ]);
    exit;
}

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
    // Log failed login attempt
    logActivity('login_failed', 'Invalid email or test permit', null, null, $email, 'examinee', 'warning');
    
    echo json_encode(["success" => false, "message" => "Invalid email or test permit"]);
    exit;
}

/* Verify password */
if (!password_verify($password, $user['password'])) {
    // Log failed login attempt
    logActivity('login_failed', 'Incorrect password', $user['user_id'], $user['first_name'] . ' ' . $user['last_name'], $user['email'], 'examinee', 'warning');
    
    echo json_encode(["success" => false, "message" => "Incorrect password"]);
    exit;
}

/* Check user status - must be 'active' to login */
if ($user['status'] === 'incomplete') {
    // Check examinee status to determine correct redirect based on progress
    $examineeCheckStmt = $pdo->prepare(
        "SELECT status, schedule_id FROM examinees WHERE user_id = :user_id LIMIT 1"
    );
    $examineeCheckStmt->execute([':user_id' => $user['user_id']]);
    $examineeCheck = $examineeCheckStmt->fetch(PDO::FETCH_ASSOC);
    
    if ($examineeCheck) {
        // User has completed registration, check next step
        if ($examineeCheck['status'] === 'Awaiting Payment' && $examineeCheck['schedule_id']) {
            // Schedule selected, needs to pay
            echo json_encode([
                "success" => false, 
                "message" => "Please complete your payment to activate your account.",
                "redirect" => "payment.html"
            ]);
            exit;
        } else {
            // Schedule not yet selected
            echo json_encode([
                "success" => false, 
                "message" => "Please select your exam schedule to continue.",
                "redirect" => "examsched.html"
            ]);
            exit;
        }
    } else {
        // No examinee record - should complete registration
        echo json_encode([
            "success" => false, 
            "message" => "Please complete your registration first.",
            "redirect" => "registration.html"
        ]);
        exit;
    }
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
        "SELECT status, schedule_id FROM examinees WHERE user_id = :user_id LIMIT 1"
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
        // Determine where to redirect based on progress
        if ($examinee['status'] === 'Awaiting Payment' && $examinee['schedule_id']) {
            // Schedule selected, needs to pay
            echo json_encode([
                "success" => false, 
                "message" => "Please complete your payment to access your dashboard.",
                "redirect" => "payment.html"
            ]);
            exit;
        } else {
            // Schedule not yet selected or in 'Pending' status
            echo json_encode([
                "success" => false, 
                "message" => "Please select your exam schedule before logging in.",
                "redirect" => "examsched.html"
            ]);
            exit;
        }
    }
}

/* Login success */
session_regenerate_id(true);
$_SESSION['user_id'] = $user['user_id'];
$_SESSION['email'] = $user['email'];
$_SESSION['first_name'] = $user['first_name'];
$_SESSION['last_name'] = $user['last_name'];
$_SESSION['role'] = $user['role'];

// Log successful login
logActivity('login_success', 'User logged in successfully', $user['user_id'], $user['first_name'] . ' ' . $user['last_name'], $user['email'], 'examinee', 'info');

echo json_encode([
    "success" => true,
    "message" => "Login successful",
    "role" => $user['role']
]);
exit;
