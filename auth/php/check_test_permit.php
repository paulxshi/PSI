<?php
// Check Test Permit in examinee_masterlist and return examinee data
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Strip ALL whitespace and invisible control chars (handles copy-paste, Excel artifacts)
$testPermit = preg_replace('/[\s\x00-\x1F\x7F]/u', '', trim($_POST['test_permit'] ?? ''));

if (empty($testPermit)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Test permit is required']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT id, test_permit, last_name, first_name, middle_name, email, used
        FROM examinee_masterlist
        WHERE test_permit = :test_permit
        LIMIT 1
    ");
    
    $stmt->execute([':test_permit' => $testPermit]);
    $record = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$record) {
        // Use 200 so the browser doesn't log "Failed to load resource" —
        // the JS reads data.success to determine outcome.
        echo json_encode([
            'success' => false,
            'message' => 'Test permit not found. Please check the number and try again.'
        ]);
        exit;
    }
    
    // Check if permit has already been used
    if ($record['used'] == 1) {
        echo json_encode([
            'success' => false,
            'message' => 'This test permit has already been used for registration.'
        ]);
        exit;
    }
    
    // Return examinee data
    echo json_encode([
        'success' => true,
        'message' => 'Test permit verified',
        'data' => [
            'id' => $record['id'],
            'test_permit' => $record['test_permit'],
            'last_name' => $record['last_name'],
            'first_name' => $record['first_name'],
            'middle_name' => $record['middle_name'],
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
