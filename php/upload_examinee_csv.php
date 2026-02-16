<?php
// Handle CSV Upload to examinee_masterlist
header('Content-Type: application/json');
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
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

// Check if file was uploaded
if (!isset($_FILES['csvFile']) || $_FILES['csvFile']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error']);
    exit;
}

$file = $_FILES['csvFile'];

// Validate file type
$allowedMimes = ['text/csv', 'text/plain', 'application/vnd.ms-excel'];
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$fileType = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!in_array($fileType, $allowedMimes)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid file type. CSV file required']);
    exit;
}

// Read CSV file
$csvData = [];
if (($handle = fopen($file['tmp_name'], 'r')) !== false) {
    $header = null;
    $rowNum = 0;
    
    while (($row = fgetcsv($handle, 1000, ',')) !== false) {
        $rowNum++;
        
        // First row is header
        if ($rowNum === 1) {
            $header = $row;
            continue;
        }
        
        if (!$header) {
            continue;
        }
        
        // Map CSV columns to array
        $record = [];
        foreach ($header as $index => $columnName) {
            $record[strtolower(trim($columnName))] = isset($row[$index]) ? trim($row[$index]) : '';
        }
        
        $csvData[] = $record;
    }
    fclose($handle);
}

// Validate and insert data
$successCount = 0;
$errorCount = 0;
$errors = [];

try {
    $pdo->beginTransaction();
    
    // Prepare statement
    $stmt = $pdo->prepare("
        INSERT INTO examinee_masterlist (test_permit, full_name, email, used)
        VALUES (:test_permit, :full_name, :email, 0)
        ON DUPLICATE KEY UPDATE 
            full_name = VALUES(full_name),
            email = VALUES(email),
            used = 0
    ");
    
    foreach ($csvData as $index => $record) {
        $testPermit = $record['test_permit'] ?? $record['testpermit'] ?? $record['permit'] ?? '';
        $fullName = $record['full_name'] ?? $record['fullname'] ?? $record['name'] ?? '';
        $email = $record['email'] ?? '';
        
        // Validate required fields
        if (empty($testPermit)) {
            $errorCount++;
            $errors[] = "Row " . ($index + 2) . ": Missing test permit number";
            continue;
        }
        
        if (empty($fullName)) {
            $errorCount++;
            $errors[] = "Row " . ($index + 2) . ": Missing full name";
            continue;
        }
        
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errorCount++;
            $errors[] = "Row " . ($index + 2) . ": Invalid or missing email";
            continue;
        }
        
        // Insert record
        try {
            $stmt->execute([
                ':test_permit' => $testPermit,
                ':full_name' => $fullName,
                ':email' => $email
            ]);
            $successCount++;
        } catch (PDOException $e) {
            $errorCount++;
            $errors[] = "Row " . ($index + 2) . ": " . $e->getMessage();
        }
    }
    
    $pdo->commit();
    
    echo json_encode([
        'success' => true,
        'message' => "Imported $successCount records successfully" . ($errorCount > 0 ? " with $errorCount errors" : ''),
        'successCount' => $successCount,
        'errorCount' => $errorCount,
        'errors' => $errors,
        'totalRows' => count($csvData)
    ]);
    
} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
