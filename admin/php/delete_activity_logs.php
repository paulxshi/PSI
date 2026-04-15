<?php
// Delete all activity log records
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

// Check if deleteAll flag is set
$deleteAll = isset($_POST['deleteAll']) && $_POST['deleteAll'] === 'true';

if (!$deleteAll) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

try {
    // Get count before deletion
    $countStmt = $pdo->prepare("SELECT COUNT(*) as total FROM activity_logs");
    $countStmt->execute();
    $countResult = $countStmt->fetch(PDO::FETCH_ASSOC);
    $totalCount = $countResult['total'];

    // Delete all records - no logging to ensure complete cleanup
    $deleteStmt = $pdo->prepare("DELETE FROM activity_logs");
    $deleteStmt->execute();

    // Get number of affected rows
    $deletedCount = $deleteStmt->rowCount();

    echo json_encode([
        'success' => true,
        'message' => $deletedCount . ' activity log record(s) deleted successfully',
        'deletedCount' => $deletedCount,
        'totalCount' => $totalCount
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
