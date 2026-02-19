<?php
session_start();
require_once "../../config/db.php";
require_once "../../php/log_activity.php";

header('Content-Type: application/json');

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized access'
    ]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
    exit();
}

$schedule_id = (int)$_POST['schedule_id'];

if (empty($schedule_id)) {
    echo json_encode([
        'success' => false,
        'message' => 'Schedule ID is required'
    ]);
    exit();
}

try {
    $pdo->beginTransaction();
    
    // Check if schedule exists and get info
    $stmtCheck = $pdo->prepare("
        SELECT num_registered 
        FROM schedules 
        WHERE schedule_id = ?
    ");
    $stmtCheck->execute([$schedule_id]);
    $schedule = $stmtCheck->fetch(PDO::FETCH_ASSOC);
    
    if (!$schedule) {
        throw new Exception('Schedule not found');
    }
    
    // Check if there are registered examinees
    if ($schedule['num_registered'] > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Cannot delete schedule with registered examinees. Please reassign them first or mark schedule as Completed.'
        ]);
        $pdo->rollBack();
        exit();
    }
    
    // Delete schedule
    $stmtDelete = $pdo->prepare("DELETE FROM schedules WHERE schedule_id = ?");
    $stmtDelete->execute([$schedule_id]);
    
    $pdo->commit();
    
    // Log activity
    logActivity(
        'admin_schedule_deleted',
        "Admin deleted schedule #{$schedule_id}",
        $_SESSION['user_id'],
        $_SESSION['username'] ?? 'Admin',
        $_SESSION['email'] ?? '',
        'admin',
        'warning',
        ['schedule_id' => $schedule_id]
    );
    
    echo json_encode([
        'success' => true,
        'message' => 'Schedule deleted successfully!'
    ]);
    
} catch (Exception $e) {
    $pdo->rollBack();
    error_log('Schedule deletion error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error deleting schedule: ' . $e->getMessage()
    ]);
}
