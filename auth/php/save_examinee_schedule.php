<?php
/**
 * Save Examinee Schedule Selection
 * Updates the examinees table with the selected schedule_id
 */

session_start();
require_once __DIR__ . '/../../config/db.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Please log in to continue.',
        'redirect' => 'login.html'
    ]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method.'
    ]);
    exit();
}

$user_id = $_SESSION['user_id'];
$schedule_id = isset($_POST['schedule_id']) ? (int)$_POST['schedule_id'] : 0;

// Validate schedule_id
if ($schedule_id <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Please select a valid examination schedule.'
    ]);
    exit();
}

try {
    // Start transaction
    $pdo->beginTransaction();
    
    // 1. Verify schedule exists and is available
    $scheduleStmt = $pdo->prepare("
        SELECT schedule_id, num_of_examinees, num_registered, status 
        FROM schedules 
        WHERE schedule_id = ?
    ");
    $scheduleStmt->execute([$schedule_id]);
    $schedule = $scheduleStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$schedule) {
        $pdo->rollBack();
        echo json_encode([
            'success' => false,
            'message' => 'Selected schedule not found.'
        ]);
        exit();
    }
    
    // Check if schedule is still available
    if ($schedule['status'] !== 'Incoming') {
        $pdo->rollBack();
        echo json_encode([
            'success' => false,
            'message' => 'This schedule is no longer available.'
        ]);
        exit();
    }
    
    // Check if schedule is full
    if ($schedule['num_registered'] >= $schedule['num_of_examinees']) {
        $pdo->rollBack();
        echo json_encode([
            'success' => false,
            'message' => 'This schedule is full. Please select another date.'
        ]);
        exit();
    }
    
    // 2. Check if examinee record exists
    $examineeStmt = $pdo->prepare("
        SELECT examinee_id, schedule_id, status 
        FROM examinees 
        WHERE user_id = ?
    ");
    $examineeStmt->execute([$user_id]);
    $examinee = $examineeStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$examinee) {
        $pdo->rollBack();
        echo json_encode([
            'success' => false,
            'message' => 'Examinee record not found. Please complete registration first.',
            'redirect' => 'registration.html'
        ]);
        exit();
    }
    
    // 3. If examinee already has a schedule, decrement old schedule's num_registered
    if ($examinee['schedule_id'] && $examinee['schedule_id'] != $schedule_id) {
        $decrementStmt = $pdo->prepare("
            UPDATE schedules 
            SET num_registered = GREATEST(0, num_registered - 1)
            WHERE schedule_id = ?
        ");
        $decrementStmt->execute([$examinee['schedule_id']]);
    }
    
    // 4. Update examinees table with new schedule
    // Set status to 'Awaiting Payment' - will be updated to 'Scheduled' after payment confirmation
    $updateExamineeStmt = $pdo->prepare("
        UPDATE examinees 
        SET schedule_id = ?, 
            status = 'Awaiting Payment'
        WHERE user_id = ?
    ");
    $updateExamineeStmt->execute([$schedule_id, $user_id]);
    
    // 5. Increment num_registered in schedules table (only if it's a new registration or change)
    if (!$examinee['schedule_id'] || $examinee['schedule_id'] != $schedule_id) {
        $incrementStmt = $pdo->prepare("
            UPDATE schedules 
            SET num_registered = num_registered + 1
            WHERE schedule_id = ?
        ");
        $incrementStmt->execute([$schedule_id]);
    }
    
    // 6. Check if schedule is now full and update status
    $checkFullStmt = $pdo->prepare("
        UPDATE schedules 
        SET status = 'Closed'
        WHERE schedule_id = ? 
        AND num_registered >= num_of_examinees 
        AND status = 'Incoming'
    ");
    $checkFullStmt->execute([$schedule_id]);
    
    // Commit transaction
    $pdo->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Schedule saved successfully!'
    ]);
    
} catch (PDOException $e) {
    $pdo->rollBack();
    error_log('Error saving examinee schedule: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error saving schedule. Please try again later.'
    ]);
}
