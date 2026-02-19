<?php
// Get available schedules for rescheduling
header('Content-Type: application/json');
session_start();

// Check admin session
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

require_once '../config/db.php';

try {
    // Get all available schedules with venue information
    $query = "
        SELECT 
            s.schedule_id,
            s.scheduled_date,
            s.num_of_examinees,
            s.status,
            v.venue_id,
            v.venue_name,
            v.region
        FROM schedules s
        JOIN venue v ON s.venue_id = v.venue_id
        WHERE s.status = 'Incoming'
        ORDER BY s.scheduled_date ASC
    ";

    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $schedules
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
