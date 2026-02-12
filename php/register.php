<?php
session_start();
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json');

error_log("=== REGISTER DEBUG ===");
error_log("POST data: " . print_r($_POST, true));

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

function respond($success, $message, $code = 400) {
    http_response_code($code);
    echo json_encode(['success' => $success, 'message' => $message]);
    exit;
}

$last_name = trim($_POST['last_name'] ?? '');
$first_name = trim($_POST['first_name'] ?? '');
$middle_name = trim($_POST['middle_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$date_of_birth = trim($_POST['date_of_birth'] ?? '');
$contact_number = trim($_POST['contact_number'] ?? '');
$gender = trim($_POST['gender'] ?? '');
$school = trim($_POST['school'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// Exam schedule fields
$region = trim($_POST['region'] ?? '');
$exam_venue = trim($_POST['exam_venue'] ?? '');
$exam_date = trim($_POST['exam_date'] ?? '');

// Payment fields
$payment_method = trim($_POST['payment_method'] ?? '');
$payment_reference = trim($_POST['payment_reference'] ?? '');
$payment_date = trim($_POST['payment_date'] ?? '');

error_log("Email to verify: $email");

// Verify OTP was verified in database
$stmt = $pdo->prepare("
    SELECT id, email, expires_at, verified_at 
    FROM otp_verifications 
    WHERE email = ? 
    AND purpose = 'registration' 
    AND verified_at IS NOT NULL 
    AND expires_at > NOW() 
    ORDER BY verified_at DESC 
    LIMIT 1
");

error_log("OTP Check SQL: " . $stmt->queryString);

$stmt->execute([$email]);
$verifyRecord = $stmt->fetch();

error_log("OTP verification record found: " . ($verifyRecord ? 'yes' : 'no'));

if (!$verifyRecord) {
    respond(false, 'Email verification is required. Please verify your email with OTP first.', 400);
}

$errors = [];

// Required field validation
if ($last_name === '' || $first_name === '' || $email === '' || $date_of_birth === '' || $contact_number === '' || $password === '' || $confirm_password === '') {
    $errors[] = 'Please fill in all required fields.';
}

// Email validation
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email address.';
}

// Password match validation
if ($password !== $confirm_password) {
    $errors[] = 'Passwords do not match.';
}

// Password strength validation
if (strlen($password) < 8) {
    $errors[] = 'Password must be at least 8 characters.';
}
if (!preg_match('/[A-Z]/', $password)) {
    $errors[] = 'Password must contain at least one uppercase letter.';
}
if (!preg_match('/[a-z]/', $password)) {
    $errors[] = 'Password must contain at least one lowercase letter.';
}
if (!preg_match('/[0-9]/', $password)) {
    $errors[] = 'Password must contain at least one number.';
}

// Date of birth validation
$dobDate = DateTime::createFromFormat('Y-m-d', $date_of_birth);
if (!$dobDate || $dobDate->format('Y-m-d') !== $date_of_birth) {
    $errors[] = 'Invalid date of birth.';
}

// Calculate age
$ageVal = null;
if ($dobDate) {
    $today = new DateTime('today');
    $ageVal = $dobDate->diff($today)->y;
}

if ($errors) {
    respond(false, implode('<br>', $errors), 422);
}


// Check if user already exists (same email)
$checkSql = "SELECT user_id FROM users WHERE email = ? LIMIT 1";
$checkStmt = $pdo->prepare($checkSql);
$checkStmt->execute([$email]);

if ($checkStmt->fetch()) {
    respond(false, 'Email already registered.', 409);
}


$hash = password_hash($password, PASSWORD_DEFAULT);

try {
    // Insert user data
    $sql = 'INSERT INTO users (last_name, first_name, middle_name, email, date_of_birth, age, contact_number, password, gender, school, email_verified, region, exam_venue, exam_date)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, ?, ?, ?)';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $last_name,
        $first_name,
        $middle_name !== '' ? $middle_name : '',
        $email,
        $date_of_birth,
        $ageVal !== null ? $ageVal : 0,
        $contact_number,
        $hash,
        $gender !== '' ? $gender : '',
        $school,
        $region,
        $exam_venue,
        $exam_date !== '' ? $exam_date : null
    ]);

    // Get the newly created user ID
    $userId = $pdo->lastInsertId();

    // Insert payment data if provided
    if ($payment_method !== '' && $payment_reference !== '') {
        $paymentSql = 'INSERT INTO payments (user_id, payment_method, payment_reference, payment_date, payment_status, created_at)
                       VALUES (?, ?, ?, ?, 'pending', NOW())';
        $paymentStmt = $pdo->prepare($paymentSql);
        $paymentStmt->execute([
            $userId,
            $payment_method,
            $payment_reference,
            $payment_date !== '' ? $payment_date : date('Y-m-d')
        ]);
    }

    // Clean up used OTP verification record
    $stmt = $pdo->prepare('DELETE FROM otp_verifications WHERE id = ?');
    $stmt->execute([$verifyRecord['id']]);

    // Create session
    session_regenerate_id(true);
    $_SESSION['user_id'] = $userId;
    $_SESSION['email'] = $email;
    $_SESSION['first_name'] = $first_name;
    $_SESSION['last_name'] = $last_name;

    respond(true, 'Registration successful!', 200);
} catch (PDOException $e) {
    error_log('Registration error: ' . $e->getMessage());
    if (isset($e->errorInfo[1]) && $e->errorInfo[1] == 1062) {
        $msg = 'User already registered.';
        $text = $e->getMessage();
        if (stripos($text, 'email') !== false) $msg = 'Email already registered.';
        elseif (stripos($text, 'contact_number') !== false) $msg = 'Contact number already registered.';
        respond(false, $msg, 409);
    }
    respond(false, 'Registration failed. Please try again later.', 500);
}
