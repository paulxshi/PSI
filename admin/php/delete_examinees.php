<?php
// Bulk delete examinees from the 'examinees' table
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

// Accept JSON body for bulk delete
$input = json_decode(file_get_contents('php://input'), true);
$examineeIds = [];
if (isset($input['examinee_ids']) && is_array($input['examinee_ids'])) {
    $examineeIds = array_map('intval', $input['examinee_ids']);
} elseif (isset($_POST['examinee_id'])) {
    $examineeIds = [ (int)$_POST['examinee_id'] ];
}

if (empty($examineeIds)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'No examinee IDs provided']);
    exit;
}

$deleted = [];
$failed = [];

try {
    foreach ($examineeIds as $id) {
        // Check if record exists and get schedule_id
        $checkStmt = $pdo->prepare("
            SELECT examinee_id, test_permit, schedule_id
            FROM examinees
            WHERE examinee_id = :id
            LIMIT 1
        ");
        $checkStmt->execute([':id' => $id]);
        $record = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if (!$record) {
            $failed[] = [ 'id' => $id, 'reason' => 'Not found' ];
            continue;
        }

        // Decrement num_registered in schedules table if schedule_id exists
        if (!empty($record['schedule_id'])) {
            $updateStmt = $pdo->prepare("
                UPDATE schedules
                SET num_registered = GREATEST(num_registered - 1, 0)
                WHERE schedule_id = :schedule_id
            ");
            $updateStmt->execute([':schedule_id' => $record['schedule_id']]);
        }

        $deleteStmt = $pdo->prepare("DELETE FROM examinees WHERE examinee_id = :id LIMIT 1");
        $deleteStmt->execute([':id' => $id]);
        $deleted[] = [ 'id' => $id, 'test_permit' => $record['test_permit'] ];
    }

    $success = count($deleted) > 0;
    $message = $success
        ? (count($deleted) . ' examinee(s) deleted successfully.')
        : 'No examinees deleted.';

    echo json_encode([
        'success' => $success,
        'message' => $message,
        'deleted' => $deleted,
        'failed' => $failed
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
