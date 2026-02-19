<?php
// Reschedule exam for a registered user - updates examinees table with new schedule_id
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
$scheduleId = isset($_POST['schedule_id']) ? (int)$_POST['schedule_id'] : 0;

if (empty($userId) || empty($scheduleId)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'User ID and Schedule ID are required']);
    exit;
}

try {
    // Check if examinee record exists
    $checkStmt = $pdo->prepare("
        SELECT e.test_id, e.user_id, u.test_permit 
        FROM examinees e 
        JOIN users u ON e.user_id = u.user_id 
        WHERE e.user_id = :user_id LIMIT 1
    ");
    $checkStmt->execute([':user_id' => $userId]);
    $examinee = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if (!$examinee) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Examinee record not found']);
        exit;
    }

    // Verify schedule exists and get details
    $scheduleStmt = $pdo->prepare("
        SELECT s.schedule_id, s.scheduled_date, v.venue_name, v.region 
        FROM schedules s 
        JOIN venue v ON s.venue_id = v.venue_id 
        WHERE s.schedule_id = :schedule_id
    ");
    $scheduleStmt->execute([':schedule_id' => $scheduleId]);
    $schedule = $scheduleStmt->fetch(PDO::FETCH_ASSOC);

    if (!$schedule) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid schedule selection']);
        exit;
    }

    // Begin transaction for atomic updates
    $pdo->beginTransaction();
    
    try {
        // Update examinees table with new schedule_id
        $updateExamineeStmt = $pdo->prepare("
            UPDATE examinees
            SET schedule_id = :schedule_id,
                date_of_test = :date_of_test,
                venue = :venue
            WHERE user_id = :user_id
        ");

        $updateExamineeStmt->execute([
            ':schedule_id' => $scheduleId,
            ':date_of_test' => $schedule['scheduled_date'],
            ':venue' => $schedule['venue_name'],
            ':user_id' => $userId
        ]);

        // Also update users table for consistency
        $updateUserStmt = $pdo->prepare("
            UPDATE users
            SET exam_date = :exam_date,
                exam_venue = :exam_venue,
                region = :region
            WHERE user_id = :user_id
        ");

        $updateUserStmt->execute([
            ':exam_date' => substr($schedule['scheduled_date'], 0, 10),
            ':exam_venue' => $schedule['venue_name'],
            ':region' => $schedule['region'],
            ':user_id' => $userId
        ]);

        $pdo->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Exam successfully rescheduled to new schedule',
            'data' => [
                'user_id' => $userId,
                'test_permit' => $examinee['test_permit'],
                'schedule_id' => $scheduleId,
                'date_of_test' => $schedule['scheduled_date'],
                'venue' => $schedule['venue_name'],
                'region' => $schedule['region']
            ]
        ]);
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
