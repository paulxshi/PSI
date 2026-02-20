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

// Get form values
$schedule_id = (int)$_POST['schedule_id'];
$region = trim($_POST['region']);
$venue_name = trim($_POST['venue_name']);
$scheduled_date = trim($_POST['date']); // Format: YYYY-MM-DD
$capacity = (int)$_POST['capacity'];
$price = (float)$_POST['price'];
$status = trim($_POST['status']);

// Validate required fields
if (empty($schedule_id) || empty($region) || empty($venue_name) || empty($scheduled_date) || $capacity <= 0 || $price < 0) {
    echo json_encode([
        'success' => false,
        'message' => 'All fields are required and must be valid.'
    ]);
    exit();
}

// Validate status
$validStatuses = ['Incoming', 'Closed', 'Completed'];
if (!in_array($status, $validStatuses)) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid status value'
    ]);
    exit();
}

try {
    $pdo->beginTransaction();
    
    // Get current schedule info to check venue
    $stmtCurrent = $pdo->prepare("
        SELECT s.venue_id, v.venue_name, v.region 
        FROM schedules s
        INNER JOIN venue v ON s.venue_id = v.venue_id
        WHERE s.schedule_id = ?
    ");
    $stmtCurrent->execute([$schedule_id]);
    $currentSchedule = $stmtCurrent->fetch(PDO::FETCH_ASSOC);
    
    if (!$currentSchedule) {
        throw new Exception('Schedule not found');
    }
    
    // Check if venue/region changed
    if ($currentSchedule['venue_name'] !== $venue_name || $currentSchedule['region'] !== $region) {
        // Check if new venue exists
        $stmtCheckVenue = $pdo->prepare("SELECT venue_id FROM venue WHERE venue_name = ? AND region = ?");
        $stmtCheckVenue->execute([$venue_name, $region]);
        $existingVenue = $stmtCheckVenue->fetch(PDO::FETCH_ASSOC);
        
        if ($existingVenue) {
            $venue_id = $existingVenue['venue_id'];
        } else {
            // Create new venue
            $stmtNewVenue = $pdo->prepare("INSERT INTO venue (venue_name, region) VALUES (?, ?)");
            $stmtNewVenue->execute([$venue_name, $region]);
            $venue_id = $pdo->lastInsertId();
        }
    } else {
        $venue_id = $currentSchedule['venue_id'];
    }
    
    // Update schedule
    $stmtUpdate = $pdo->prepare("
        UPDATE schedules 
        SET venue_id = ?,
            scheduled_date = ?,
            num_of_examinees = ?,
            price = ?,
            status = ?
        WHERE schedule_id = ?
    ");
    
    $stmtUpdate->execute([
        $venue_id,
        $scheduled_date,
        $capacity,
        $price,
        $status,
        $schedule_id
    ]);
    
    $pdo->commit();
    
    // Log activity
    $metadata = [
        'schedule_id' => $schedule_id,
        'venue' => $venue_name,
        'region' => $region,
        'date' => $scheduled_date,
        'capacity' => $capacity,
        'price' => $price,
        'status' => $status
    ];
    logActivity(
        'admin_schedule_edited',
        "Admin edited schedule #{$schedule_id}: {$venue_name}, {$region} on {$scheduled_date}",
        $_SESSION['user_id'],
        $_SESSION['username'] ?? 'Admin',
        $_SESSION['email'] ?? '',
        'admin',
        'info',
        $metadata
    );
    
    echo json_encode([
        'success' => true,
        'message' => 'Schedule updated successfully!'
    ]);
    
} catch (Exception $e) {
    $pdo->rollBack();
    error_log('Schedule update error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error updating schedule: ' . $e->getMessage()
    ]);
}
