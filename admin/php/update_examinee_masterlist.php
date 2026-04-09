<?php
// Update Examinee Masterlist (for editing uploaded records)
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
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$firstName = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
$lastName = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
$middleName = isset($_POST['middle_name']) ? trim($_POST['middle_name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';

// Validation
$errors = [];

if ($id <= 0) {
    $errors[] = 'Invalid record ID';
}

if (empty($firstName)) {
    $errors[] = 'First name is required';
}

if (empty($lastName)) {
    $errors[] = 'Last name is required';
}

if (empty($email)) {
    $errors[] = 'Email is required';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email format';
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Validation failed',
        'errors' => $errors
    ]);
    exit;
}

try {
    // Check if record exists and is not registered (used = 0)
    $stmt = $pdo->prepare("SELECT id, used, test_permit FROM examinee_masterlist WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $record = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$record) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Record not found'
        ]);
        exit;
    }

    if ($record['used'] == 1) {
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'message' => 'Cannot edit registered examinees'
        ]);
        exit;
    }

    // Check for duplicate name (excluding current record)
    $checkNameStmt = $pdo->prepare("
        SELECT id, test_permit 
        FROM examinee_masterlist 
        WHERE first_name = :first_name 
        AND last_name = :last_name 
        AND id != :current_id 
        LIMIT 1
    ");
    $checkNameStmt->execute([
        ':first_name' => $firstName,
        ':last_name' => $lastName,
        ':current_id' => $id
    ]);

    if ($checkNameStmt->rowCount() > 0) {
        $existing = $checkNameStmt->fetch(PDO::FETCH_ASSOC);
        http_response_code(409);
        echo json_encode([
            'success' => false,
            'message' => "Duplicate name: '$firstName $lastName' already exists with test permit: {$existing['test_permit']}"
        ]);
        exit;
    }

    // Check for duplicate email (excluding current record)
    $checkEmailStmt = $pdo->prepare("
        SELECT id, test_permit 
        FROM examinee_masterlist 
        WHERE email = :email 
        AND id != :current_id 
        LIMIT 1
    ");
    $checkEmailStmt->execute([
        ':email' => $email,
        ':current_id' => $id
    ]);

    if ($checkEmailStmt->rowCount() > 0) {
        $existing = $checkEmailStmt->fetch(PDO::FETCH_ASSOC);
        http_response_code(409);
        echo json_encode([
            'success' => false,
            'message' => "Email already exists with test permit: {$existing['test_permit']}"
        ]);
        exit;
    }

    // Update record
    $updateStmt = $pdo->prepare("
        UPDATE examinee_masterlist 
        SET first_name = :first_name,
            last_name = :last_name,
            middle_name = :middle_name,
            email = :email
        WHERE id = :id
    ");

    $updateStmt->execute([
        ':first_name' => $firstName,
        ':last_name' => $lastName,
        ':middle_name' => $middleName,
        ':email' => $email,
        ':id' => $id
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Examinee record updated successfully'
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
