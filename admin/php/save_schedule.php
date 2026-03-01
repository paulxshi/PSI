<?php
require_once "../../config/db.php";
require_once "../../php/log_activity.php";
session_start();

header('Content-Type: application/json');

// Check if form submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get form values and sanitize
    $region = trim($_POST['exam_region']);
    $venue_name = trim($_POST['exam_area']);
    $scheduled_date = trim($_POST['exam_date']); // Format: YYYY-MM-DD
    $exam_limit = (int)$_POST['exam_limit'];
    $exam_price = (float)$_POST['exam_price'];

    // Validate required fields
    if (empty($region) || empty($venue_name) || empty($scheduled_date) || $exam_limit <= 0 || $exam_price < 0) {
        echo json_encode([
            'success' => false,
            'message' => 'All fields are required and must be valid.'
        ]);
        exit();
    }

    try {
        // Start transaction
        $pdo->beginTransaction();

        $stmtCheck = $pdo->prepare("SELECT venue_id FROM venue WHERE venue_name = ? AND region = ?");
        $stmtCheck->execute([$venue_name, $region]);
        $existingVenue = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if ($existingVenue) {
            $venue_id = $existingVenue['venue_id'];
        } else {
            $stmtVenue = $pdo->prepare("INSERT INTO venue (venue_name, region) VALUES (?, ?)");
            $stmtVenue->execute([$venue_name, $region]);
            $venue_id = $pdo->lastInsertId();
        }

        $stmtSchedule = $pdo->prepare("
            INSERT INTO schedules (venue_id, scheduled_date, num_of_examinees, price, num_registered, status) 
            VALUES (?, ?, ?, ?, 0, 'Incoming')
        ");
        $stmtSchedule->execute([$venue_id, $scheduled_date, $exam_limit, $exam_price]);

        // Commit transaction
        $pdo->commit();

        // Log activity
        if (isset($_SESSION['user_id'])) {
            $metadata = [
                'schedule_id' => $pdo->lastInsertId(),
                'venue' => $venue_name,
                'region' => $region,
                'date' => $scheduled_date,
                'capacity' => $exam_limit,
                'price' => $exam_price
            ];
            logActivity(
                'admin_schedule_created',
                "Admin created new schedule for {$venue_name}, {$region} on {$scheduled_date}",
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
            'message' => 'Schedule successfully created!'
        ]);

    } catch (Exception $e) {
        $pdo->rollBack();
        error_log('Schedule creation error: ' . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Error creating schedule: ' . $e->getMessage()
        ]);
    }

    exit();
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method.'
    ]);
    exit();
}
