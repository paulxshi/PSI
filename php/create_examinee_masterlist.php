<?php
// Create new examinee masterlist record
header('Content-Type: application/json');
session_start();

// Check if admin is logged in
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get POST data
$testPermit = isset($_POST['test_permit']) ? trim($_POST['test_permit']) : '';
$lastName = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
$firstName = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
$middleName = isset($_POST['middle_name']) ? trim($_POST['middle_name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';

// Validate fields are not empty
if (empty($testPermit)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Test permit is required']);
    exit;
}

if (empty($lastName)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Last name is required']);
    exit;
}

if (empty($firstName)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'First name is required']);
    exit;
}

if (empty($email)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Email is required']);
    exit;
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit;
}

try {
    // Check if test_permit already exists
    $checkStmt = $pdo->prepare("SELECT id FROM examinee_masterlist WHERE test_permit = :test_permit LIMIT 1");
    $checkStmt->execute([':test_permit' => $testPermit]);
    
    if ($checkStmt->rowCount() > 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Test permit already exists']);
        exit;
    }

    // Check if email already exists
    $checkEmailStmt = $pdo->prepare("SELECT id FROM examinee_masterlist WHERE email = :email LIMIT 1");
    $checkEmailStmt->execute([':email' => $email]);
    
    if ($checkEmailStmt->rowCount() > 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Email already exists']);
        exit;
    }

    // Insert new record
    $insertStmt = $pdo->prepare("
        INSERT INTO examinee_masterlist (test_permit, last_name, first_name, middle_name, email, used)
        VALUES (:test_permit, :last_name, :first_name, :middle_name, :email, 0)
    ");

    $insertStmt->execute([
        ':test_permit' => $testPermit,
        ':last_name' => $lastName,
        ':first_name' => $firstName,
        ':middle_name' => $middleName,
        ':email' => $email
    ]);

    $newId = $pdo->lastInsertId();

    echo json_encode([
        'success' => true,
        'message' => 'Examinee record created successfully',
        'data' => [
            'id' => (int)$newId,
            'test_permit' => $testPermit,
            'last_name' => $lastName,
            'first_name' => $firstName,
            'middle_name' => $middleName,
            'email' => $email,
            'used' => 0,
            'uploaded_at' => date('Y-m-d H:i:s')
        ]
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
