<?php
session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Session expired. Please log in again.'
    ]);
    exit;
}

require_once('../config/db.php');

$user_id = $_SESSION['user_id'];

try {
    // Fetch exam details for the user
    $query = "
        SELECT 
            u.test_permit,
            CONCAT(u.first_name, ' ', COALESCE(u.middle_name, ''), ' ', u.last_name) AS full_name,
            e.status AS examinee_status,
            s.schedule_datetime,
            s.price,
            v.venue_name
        FROM users u
        INNER JOIN examinees e ON u.user_id = e.user_id
        INNER JOIN schedules s ON e.schedule_id = s.schedule_id
        INNER JOIN venue v ON s.venue_id = v.venue_id
        WHERE u.user_id = :user_id
    ";
    
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$result) {
        echo json_encode([
            'success' => false,
            'message' => 'No exam schedule found. Please select a schedule first.'
        ]);
        exit;
    }
    
    // Check if user has already passed payment stage
    if ($result['examinee_status'] === 'Scheduled') {
        echo json_encode([
            'success' => false,
            'message' => 'Payment already completed. You can now login.'
        ]);
        exit;
    }
    
    // Check if user is in correct stage
    if ($result['examinee_status'] !== 'Awaiting Payment') {
        echo json_encode([
            'success' => false,
            'message' => 'Please complete the previous steps first.'
        ]);
        exit;
    }
    
    echo json_encode([
        'success' => true,
        'data' => [
            'test_permit' => $result['test_permit'],
            'full_name' => $result['full_name'],
            'schedule_datetime' => $result['schedule_datetime'],
            'price' => $result['price'],
            'venue_name' => $result['venue_name']
        ]
    ]);
    
} catch (PDOException $e) {
    error_log("Database Error in get_payment_details.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Database error occurred. Please try again.'
    ]);
}
?>
