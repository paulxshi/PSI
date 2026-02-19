<?php
// Set JSON header immediately - BEFORE anything else
header('Content-Type: application/json');

session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/RateLimiter.php';

// Initialize rate limiter
$rateLimiter = new RateLimiter($pdo);
$clientIP = $_SERVER['REMOTE_ADDR'];

// Check rate limit - max 8 registration attempts, blocked for 15 minutes after exceeding
if (!$rateLimiter->checkLimit('registration', $clientIP, 8, 900)) {
    $secondsRemaining = $rateLimiter->getTimeUntilUnblocked('registration', $clientIP);
    
    // Convert to user-friendly time format
    if ($secondsRemaining < 60) {
        $timeDisplay = ceil($secondsRemaining) . " second" . (ceil($secondsRemaining) != 1 ? "s" : "");
    } else {
        $minutes = ceil($secondsRemaining / 60);
        $timeDisplay = $minutes . " minute" . ($minutes != 1 ? "s" : "");
    }
    
    $message = "Too many registration attempts. Please try again in " . $timeDisplay . ".";
    
    http_response_code(429);
    echo json_encode([
        'success' => false, 
        'message' => $message,
        'retry_after' => $secondsRemaining
    ]);
    exit;
}

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
$test_permit = trim($_POST['test_permit'] ?? '');
$date_of_birth = trim($_POST['date_of_birth'] ?? '');
$contact_number = trim($_POST['contact_number'] ?? '');
$gender = trim($_POST['gender'] ?? '');
$school = trim($_POST['school'] ?? '');
$address = trim($_POST['address'] ?? '');
$nationality = trim($_POST['nationality'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// Exam schedule and payment fields will be handled separately
// For now, we only save personal information during registration

error_log("Email to verify: $email");
error_log("Test Permit: $test_permit");

// Email will be verified client-side via OTP
// No database OTP verification needed for client-side only approach

$errors = [];

// Test permit validation
if (empty($test_permit)) {
    $errors[] = 'Test permit is required.';
}

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

// Verify test permit exists in examinee_masterlist
try {
    $permitStmt = $pdo->prepare("
        SELECT id, test_permit, last_name, first_name, middle_name, email, used
        FROM examinee_masterlist
        WHERE test_permit = :test_permit
        LIMIT 1
    ");
    $permitStmt->execute([':test_permit' => $test_permit]);
    $permitRecord = $permitStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$permitRecord) {
        respond(false, 'Test permit not found in examinee masterlist.', 404);
    }
    
    // Check if permit has already been used
    if ($permitRecord['used'] == 1) {
        respond(false, 'This test permit has already been registered.', 409);
    }
    
    // Verify email matches the masterlist record (security check)
    if (strtolower($permitRecord['email']) !== strtolower($email)) {
        respond(false, 'Email does not match the test permit record.', 422);
    }
    
    // Verify name matches the masterlist record for consistency (allow flexibility)
    $masterlistFullName = trim($permitRecord['first_name'] . ' ' . $permitRecord['last_name']);
    $submittedFullName = trim($first_name . ' ' . $last_name);
    
    error_log("Masterlist Name: $masterlistFullName, Submitted Name: $submittedFullName");
    
} catch (PDOException $e) {
    error_log('Test permit verification error: ' . $e->getMessage());
    respond(false, 'Error verifying test permit.', 500);
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
    // Begin transaction for consistency
    $pdo->beginTransaction();
    
    // Insert user data with test_permit and status='incomplete'
    // Status will be changed to 'active' after payment confirmation via webhook
    $sql = 'INSERT INTO users (test_permit, last_name, first_name, middle_name, email, date_of_birth, age, contact_number, password, gender, school, address, nationality, email_verified, status, role)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, ?, ?)';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $test_permit,
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
        $address !== '' ? $address : '',
        $nationality !== '' ? $nationality : '',
        'incomplete', // User cannot login until payment is confirmed
        'examinee'
    ]);

    // Get the newly created user ID
    $userId = $pdo->lastInsertId();

    // Create examinees table entry with 'Pending' status
    // Status will be updated to 'Scheduled' after schedule selection
    // examinee_status will be set to 'Registered' after payment
    $examineeStmt = $pdo->prepare("
        INSERT INTO examinees (user_id, test_permit, status, examinee_status)
        VALUES (?, ?, 'Pending', NULL)
    ");
    $examineeStmt->execute([$userId, $test_permit]);

    // Mark test permit as used in examinee_masterlist
    $markUsedStmt = $pdo->prepare("
        UPDATE examinee_masterlist
        SET used = 1, used_by = :user_id
        WHERE test_permit = :test_permit
    ");
    $markUsedStmt->execute([
        ':user_id' => $userId,
        ':test_permit' => $test_permit
    ]);

    // Commit transaction
    $pdo->commit();

    // Log registration completion
    require_once __DIR__ . '/log_activity.php';
    $metadata = [
        'user_id' => $userId,
        'test_permit' => $test_permit,
        'email' => $email
    ];
    logActivity(
        'registration_completed',
        "New user registered: {$email} (Permit: {$test_permit})",
        $userId,
        $email,
        $email,
        'examinee',
        'info',
        $metadata
    );

    // Create temporary session to allow user to complete registration flow
    // This allows schedule selection and payment, but NOT full login access
    // Full login is only allowed after payment confirmation (status='active')
    session_regenerate_id(true);
    $_SESSION['user_id'] = $userId;
    $_SESSION['registration_flow'] = true; // Flag to indicate incomplete registration
    $_SESSION['test_permit'] = $test_permit;
    
    respond(true, 'Registration successful! Please proceed to schedule selection and payment.', 200);
} catch (PDOException $e) {
    // Rollback transaction on error
    try {
        $pdo->rollBack();
    } catch (Exception $rollbackError) {
        error_log('Rollback error: ' . $rollbackError->getMessage());
    }
    
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
