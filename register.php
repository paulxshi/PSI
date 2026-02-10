<?php
require __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed');
}

function respond($message, $code = 400) {
    http_response_code($code);
    echo $message;
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
$pmma_student_id = trim($_POST['pmma_student_id'] ?? '');
$test_permit = trim($_POST['test_permit'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

$errors = [];

if ($last_name === '' || $first_name === '' || $email === '' || $date_of_birth === '' || $contact_number === '' || $password === '' || $confirm_password === '') {
    $errors[] = 'Please fill in all required fields.';
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email address.';
}
if ($password !== $confirm_password) {
    $errors[] = 'Passwords do not match.';
}
if (strlen($password) < 8) {
    $errors[] = 'Password must be at least 8 characters.';
}

$dobDate = DateTime::createFromFormat('Y-m-d', $date_of_birth);
if (!$dobDate || $dobDate->format('Y-m-d') !== $date_of_birth) {
    $errors[] = 'Invalid date of birth.';
}

$ageVal = null;
if ($dobDate) {
    $today = new DateTime('today');
    $ageVal = $dobDate->diff($today)->y;
}

if ($errors) {
    respond(implode("<br>", $errors), 422);
}

$hash = password_hash($password, PASSWORD_DEFAULT);

try {
    $sql = 'INSERT INTO users (last_name, first_name, middle_name, email, date_of_birth, age, address, contact_number, password, pmma_student_id, test_permit)
            VALUES (:last_name, :first_name, :middle_name, :email, :dob, :age, :address, :contact, :password, :pmma, :permit)';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':last_name' => $last_name,
        ':first_name' => $first_name,
        ':middle_name' => $middle_name !== '' ? $middle_name : null,
        ':email' => $email,
        ':dob' => $date_of_birth,
        ':age' => $ageVal,
        ':address' => $address !== '' ? $address : null,
        ':contact' => $contact_number,
        ':password' => $hash,
        ':pmma' => $pmma_student_id !== '' ? $pmma_student_id : null,
        ':permit' => $test_permit !== '' ? $test_permit : null,
    ]);

    header('Location: login.html?registered=1');
    exit;
} catch (PDOException $e) {
    if (isset($e->errorInfo[1]) && $e->errorInfo[1] == 1062) {
        $msg = 'Duplicate entry';
        $text = $e->getMessage();
        if (stripos($text, 'email') !== false) $msg = 'Email already registered.';
        elseif (stripos($text, 'pmma_student_id') !== false) $msg = 'PMMA Student ID already registered.';
        elseif (stripos($text, 'test_permit') !== false) $msg = 'Test Permit already registered.';
        respond($msg, 409);
    }
    respond('Registration failed. Please try again later.', 500);
}
