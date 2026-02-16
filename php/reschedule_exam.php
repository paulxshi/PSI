<?php
// Reschedule exam for a registered user
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

$userId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
$examVenue = isset($_POST['exam_venue']) ? trim($_POST['exam_venue']) : '';
$examDate = isset($_POST['exam_date']) ? trim($_POST['exam_date']) : '';
$region = isset($_POST['region']) ? trim($_POST['region']) : '';

if (empty($userId) || empty($examVenue) || empty($examDate) || empty($region)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

// Validate date format
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $examDate)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid date format (use YYYY-MM-DD)']);
    exit;
}

try {
    // Check if user exists
    $checkStmt = $pdo->prepare("SELECT user_id, test_permit FROM users WHERE user_id = :user_id LIMIT 1");
    $checkStmt->execute([':user_id' => $userId]);
    $user = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit;
    }

    // Update exam schedule
    $updateStmt = $pdo->prepare("
        UPDATE users
        SET exam_venue = :exam_venue,
            exam_date = :exam_date,
            region = :region
        WHERE user_id = :user_id
    ");

    $updateStmt->execute([
        ':exam_venue' => $examVenue,
        ':exam_date' => $examDate,
        ':region' => $region,
        ':user_id' => $userId
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Exam rescheduled successfully',
        'data' => [
            'user_id' => $userId,
            'test_permit' => $user['test_permit'],
            'exam_venue' => $examVenue,
            'exam_date' => $examDate,
            'region' => $region
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
