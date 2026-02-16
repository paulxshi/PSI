<?php
// Check Test Permit in examinee_masterlist and return examinee data
header('Content-Type: application/json');

require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$testPermit = trim($_POST['test_permit'] ?? '');

if (empty($testPermit)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Test permit is required']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT id, test_permit, full_name, email, used
        FROM examinee_masterlist
        WHERE test_permit = :test_permit
        LIMIT 1
    ");
    
    $stmt->execute([':test_permit' => $testPermit]);
    $record = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$record) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Test permit not found in examinee masterlist'
        ]);
        exit;
    }
    
    // Check if permit has already been used
    if ($record['used'] == 1) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'This test permit has already been registered'
        ]);
        exit;
    }
    
    // Return examinee data
    echo json_encode([
        'success' => true,
        'message' => 'Test permit verified',
        'data' => [
            'test_permit' => $record['test_permit'],
            'full_name' => $record['full_name'],
            'email' => $record['email']
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
