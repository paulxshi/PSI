<?php
// Delete examinee masterlist record (only if not used)
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

// Get record ID
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if (empty($id)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Record ID is required']);
    exit;
}

try {
    // Check if record exists and is not used
    $checkStmt = $pdo->prepare("
        SELECT id, used, test_permit
        FROM examinee_masterlist
        WHERE id = :id
        LIMIT 1
    ");
    
    $checkStmt->execute([':id' => $id]);
    $record = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if (!$record) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Record not found']);
        exit;
    }

    // Check if record is used (registered)
    if ($record['used'] == 1) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Cannot delete registered examinee records']);
        exit;
    }

    // Delete record
    $deleteStmt = $pdo->prepare("DELETE FROM examinee_masterlist WHERE id = :id LIMIT 1");
    $deleteStmt->execute([':id' => $id]);

    echo json_encode([
        'success' => true,
        'message' => 'Examinee record deleted successfully',
        'data' => [
            'id' => $id,
            'test_permit' => $record['test_permit']
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
