<?php
// Create Examinee Masterlist (manual entry by admin)
header('Content-Type: application/json');
session_start();

// Check if admin is logged in
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once __DIR__ . '/../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get POST data
$testPermit = isset($_POST['test_permit']) ? trim($_POST['test_permit']) : '';
$firstName  = isset($_POST['first_name'])  ? trim($_POST['first_name'])  : '';
$lastName   = isset($_POST['last_name'])   ? trim($_POST['last_name'])   : '';
$middleName = isset($_POST['middle_name']) ? trim($_POST['middle_name']) : '';
$email      = isset($_POST['email'])       ? trim($_POST['email'])       : '';

// Validation
$errors = [];

if (empty($testPermit)) {
    $errors[] = 'Test permit is required';
}

if (empty($firstName)) {
    $errors[] = 'First name is required';
}

if (empty($lastName)) {
    $errors[] = 'Last name is required';
}

if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email format';
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => implode(', ', $errors),
        'errors'  => $errors
    ]);
    exit;
}

try {
    // Check for duplicate test permit
    $checkPermitStmt = $pdo->prepare("
        SELECT id, test_permit, first_name, last_name, email
        FROM examinee_masterlist
        WHERE test_permit = :test_permit
        LIMIT 1
    ");
    $checkPermitStmt->execute([':test_permit' => $testPermit]);

    if ($checkPermitStmt->rowCount() > 0) {
        $existing = $checkPermitStmt->fetch(PDO::FETCH_ASSOC);
        http_response_code(409);
        echo json_encode([
            'success'        => false,
            'message'        => "Test permit '$testPermit' already exists in the system.",
            'duplicate_type' => 'test_permit',
            'existing_data'  => [
                'test_permit' => $existing['test_permit'],
                'full_name'   => $existing['first_name'] . ' ' . $existing['last_name'],
                'email'       => $existing['email']
            ]
        ]);
        exit;
    }

    // Check for duplicate email (only if provided)
    if (!empty($email)) {
        $checkEmailStmt = $pdo->prepare("
            SELECT id, test_permit, first_name, last_name, email
            FROM examinee_masterlist
            WHERE email = :email
            LIMIT 1
        ");
        $checkEmailStmt->execute([':email' => $email]);

        if ($checkEmailStmt->rowCount() > 0) {
            $existing = $checkEmailStmt->fetch(PDO::FETCH_ASSOC);
            http_response_code(409);
            echo json_encode([
                'success'        => false,
                'message'        => "Email '$email' is already registered in the system.",
                'duplicate_type' => 'email',
                'existing_data'  => [
                    'test_permit' => $existing['test_permit'],
                    'full_name'   => $existing['first_name'] . ' ' . $existing['last_name'],
                    'email'       => $existing['email']
                ]
            ]);
            exit;
        }
    }

    // Check for duplicate name
    $checkNameStmt = $pdo->prepare("
        SELECT id, test_permit, first_name, last_name, email
        FROM examinee_masterlist
        WHERE first_name = :first_name
        AND last_name = :last_name
        LIMIT 1
    ");
    $checkNameStmt->execute([
        ':first_name' => $firstName,
        ':last_name'  => $lastName
    ]);

    if ($checkNameStmt->rowCount() > 0) {
        $existing = $checkNameStmt->fetch(PDO::FETCH_ASSOC);
        http_response_code(409);
        echo json_encode([
            'success'        => false,
            'message'        => "An examinee named '$firstName $lastName' already exists in the system.",
            'duplicate_type' => 'name',
            'existing_data'  => [
                'test_permit' => $existing['test_permit'],
                'full_name'   => $existing['first_name'] . ' ' . $existing['last_name'],
                'email'       => $existing['email']
            ]
        ]);
        exit;
    }

    // Insert new record
    $insertStmt = $pdo->prepare("
        INSERT INTO examinee_masterlist (test_permit, first_name, last_name, middle_name, email, used, uploaded_at)
        VALUES (:test_permit, :first_name, :last_name, :middle_name, :email, 0, NOW())
    ");

    $insertStmt->execute([
        ':test_permit'  => $testPermit,
        ':first_name'   => $firstName,
        ':last_name'    => $lastName,
        ':middle_name'  => $middleName !== '' ? $middleName : null,
        ':email'        => $email
    ]);

    echo json_encode([
        'success'     => true,
        'message'     => 'Examinee record created successfully',
        'test_permit' => $testPermit
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error occurred'
    ]);
}
?>
