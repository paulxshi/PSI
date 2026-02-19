<?php
// Reschedule exam for a registered user - updates examinees table with new schedule_id and manages slot counts
header('Content-Type: application/json');
session_start();

// Check if admin is logged in
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/log_activity.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$userId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
$newScheduleId = isset($_POST['schedule_id']) ? (int)$_POST['schedule_id'] : 0;

if (empty($userId) || empty($newScheduleId)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'User ID and Schedule ID are required']);
    exit;
}

try {
    // Get current examinee record with old schedule_id
    $checkStmt = $pdo->prepare("
        SELECT e.examinee_id, e.schedule_id as old_schedule_id, u.test_permit 
        FROM examinees e 
        JOIN users u ON e.user_id = u.user_id 
        WHERE e.user_id = :user_id AND e.status = 'Scheduled'
        LIMIT 1
    ");
    $checkStmt->execute([':user_id' => $userId]);
    $examinee = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if (!$examinee) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Examinee record not found or not in Scheduled status']);
        exit;
    }

    $oldScheduleId = $examinee['old_schedule_id'];

    // Check if trying to reschedule to same schedule
    if ($oldScheduleId == $newScheduleId) {
        echo json_encode(['success' => false, 'message' => 'Examinee is already scheduled for this date']);
        exit;
    }

    // Verify new schedule exists and has capacity
    $newScheduleStmt = $pdo->prepare("
        SELECT s.schedule_id, s.scheduled_date, s.num_of_examinees, s.num_registered,
               v.venue_name, v.region 
        FROM schedules s 
        JOIN venue v ON s.venue_id = v.venue_id 
        WHERE s.schedule_id = :schedule_id AND s.status = 'Incoming'
    ");
    $newScheduleStmt->execute([':schedule_id' => $newScheduleId]);
    $newSchedule = $newScheduleStmt->fetch(PDO::FETCH_ASSOC);

    if (!$newSchedule) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid schedule selection or schedule not available']);
        exit;
    }

    // Check if new schedule has available slots
    $availableSlots = $newSchedule['num_of_examinees'] - $newSchedule['num_registered'];
    if ($availableSlots <= 0) {
        echo json_encode(['success' => false, 'message' => 'Selected schedule is full. No available slots.']);
        exit;
    }

    // Begin transaction for atomic updates
    $pdo->beginTransaction();
    
    try {
        // 1. Decrease num_registered on old schedule (if exists)
        if ($oldScheduleId) {
            $decreaseOldStmt = $pdo->prepare("
                UPDATE schedules 
                SET num_registered = GREATEST(0, num_registered - 1)
                WHERE schedule_id = :schedule_id
            ");
            $decreaseOldStmt->execute([':schedule_id' => $oldScheduleId]);
        }

        // 2. Increase num_registered on new schedule
        $increaseNewStmt = $pdo->prepare("
            UPDATE schedules 
            SET num_registered = num_registered + 1
            WHERE schedule_id = :schedule_id
        ");
        $increaseNewStmt->execute([':schedule_id' => $newScheduleId]);

        // 3. Update examinees table with new schedule_id
        $updateExamineeStmt = $pdo->prepare("
            UPDATE examinees
            SET schedule_id = :schedule_id,
                updated_at = NOW()
            WHERE user_id = :user_id
        ");
        $updateExamineeStmt->execute([
            ':schedule_id' => $newScheduleId,
            ':user_id' => $userId
        ]);

        $pdo->commit();

        // Log activity
        if (isset($_SESSION['user_id'])) {
            $metadata = [
                'user_id' => $userId,
                'test_permit' => $examinee['test_permit'],
                'old_schedule_id' => $oldScheduleId,
                'new_schedule_id' => $newScheduleId,
                'new_date' => $newSchedule['scheduled_date'],
                'venue' => $newSchedule['venue_name'],
                'region' => $newSchedule['region']
            ];
            logActivity(
                'schedule_changed',
                "Admin rescheduled examinee (Permit: {$examinee['test_permit']}) to {$newSchedule['venue_name']} on {$newSchedule['scheduled_date']}",
                $_SESSION['user_id'],
                $_SESSION['username'] ?? 'Admin',
                $_SESSION['email'] ?? '',
                'admin',
                'info',
                $metadata
            );
        }

        echo json_encode([
            'success' => true,
            'message' => 'Exam successfully rescheduled',
            'data' => [
                'user_id' => $userId,
                'test_permit' => $examinee['test_permit'],
                'old_schedule_id' => $oldScheduleId,
                'new_schedule_id' => $newScheduleId,
                'scheduled_date' => $newSchedule['scheduled_date'],
                'venue' => $newSchedule['venue_name'],
                'region' => $newSchedule['region']
            ]
        ]);
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }

} catch (PDOException $e) {
    http_response_code(500);
    error_log('Reschedule error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
