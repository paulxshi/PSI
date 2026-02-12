<?php
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
$age = trim($_POST['age'] ?? '');
$address = trim($_POST['address'] ?? '');
$contact_number = trim($_POST['contact_number'] ?? '');
$gender = trim($_POST['gender'] ?? '');
$nationality = trim($_POST['nationality'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

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
if ($verifyRecord) {
    error_log("Record ID: " . $verifyRecord['id']);
    error_log("Record email: " . $verifyRecord['email']);
    error_log("Record expires_at: " . $verifyRecord['expires_at']);
    error_log("Record verified_at: " . $verifyRecord['verified_at']);
} else {
    // Debug: Check if there are any records for this email
    $debugStmt = $pdo->prepare("SELECT id, verified_at, expires_at FROM otp_verifications WHERE email = ? ORDER BY created_at DESC LIMIT 5");
    $debugStmt->execute([$email]);
    $allRecords = $debugStmt->fetchAll();
    error_log("All records for email: " . print_r($allRecords, true));
}

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


// Check if user already exists (same name, DOB, gender)
$checkSql = "SELECT user_id FROM users 
             WHERE first_name = :first_name 
             AND last_name = :last_name 
             AND date_of_birth = :dob 
             AND gender = :gender 
             LIMIT 1";

$checkStmt = $pdo->prepare($checkSql);
$checkStmt->execute([
    ':first_name' => $first_name,
    ':last_name'  => $last_name,
    ':dob'        => $date_of_birth,
    ':gender'     => $gender !== '' ? $gender : ''
]);

if ($checkStmt->fetch()) {
    respond(false, 'User already registered.', 409);
}



$hash = password_hash($password, PASSWORD_DEFAULT);

try {
    $sql = 'INSERT INTO users (last_name, first_name, middle_name, email, date_of_birth, age, address, contact_number, password, gender, nationality, email_verified)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $last_name,
        $first_name,
        $middle_name !== '' ? $middle_name : '',
        $email,
        $date_of_birth,
        $ageVal !== null ? $ageVal : 0,
        $address !== '' ? $address : '',
        $contact_number,
        $hash,
        $gender !== '' ? $gender : '',
        $nationality !== '' ? $nationality : ''
    ]);

    // Clean up used OTP verification record
    $stmt = $pdo->prepare('DELETE FROM otp_verifications WHERE id = ?');
    $stmt->execute([$verifyRecord['id']]);

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
