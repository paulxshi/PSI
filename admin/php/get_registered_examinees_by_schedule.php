<?php
/**
 * Get registered examinees for a specific region/venue/schedule
 * Used by admin dashboard to display registered examinees
 */

require_once "../config/db.php";

header('Content-Type: application/json');

// Get filter parameters
$region = isset($_GET['region']) ? trim($_GET['region']) : '';
$venue_id = isset($_GET['venue_id']) ? (int)$_GET['venue_id'] : 0;
$schedule_id = isset($_GET['schedule_id']) ? (int)$_GET['schedule_id'] : 0;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$date_filter = isset($_GET['date']) ? trim($_GET['date']) : '';

try {
    // Build query to get registered examinees
    $sql = "
        SELECT 
            u.user_id,
            u.test_permit,
            u.email,
            CONCAT(u.first_name, ' ', u.middle_name, ' ', u.last_name) as full_name,
            u.first_name,
            u.last_name,
            s.scheduled_date,
            v.venue_name,
            v.region,
            e.status as examinee_status,
            e.date_of_registration
        FROM examinees e
        INNER JOIN users u ON e.user_id = u.user_id
        INNER JOIN schedules s ON e.schedule_id = s.schedule_id
        INNER JOIN venue v ON s.venue_id = v.venue_id
        WHERE 1=1
    ";
    
    $params = [];
    
    // Filter by region
    if (!empty($region)) {
        $sql .= " AND v.region = :region";
        $params[':region'] = $region;
    }
    
    // Filter by venue
    if ($venue_id > 0) {
        $sql .= " AND v.venue_id = :venue_id";
        $params[':venue_id'] = $venue_id;
    }
    
    // Filter by specific schedule
    if ($schedule_id > 0) {
        $sql .= " AND s.schedule_id = :schedule_id";
        $params[':schedule_id'] = $schedule_id;
    }
    
    // Filter by date
    if (!empty($date_filter)) {
        $sql .= " AND DATE(s.schedule_datetime) = :date_filter";
        $params[':date_filter'] = $date_filter;
    }
    
    // Search by test permit, name, or email
    if (!empty($search)) {
        $sql .= " AND (
            u.test_permit LIKE :search 
            OR u.first_name LIKE :search 
            OR u.last_name LIKE :search 
            OR u.email LIKE :search
            OR CONCAT(u.first_name, ' ', u.last_name) LIKE :search
        )";
        $params[':search'] = '%' . $search . '%';
    }
    
    // Only show scheduled examinees
    $sql .= " AND e.status = 'Scheduled'";
    
    // Order by registration date
    $sql .= " ORDER BY e.date_of_registration DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $examinees = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format dates for display
    foreach ($examinees as &$examinee) {
        $examinee['exam_date_formatted'] = date('F j, Y', strtotime($examinee['scheduled_date']));
    }
    
    echo json_encode([
        'success' => true,
        'data' => $examinees,
        'count' => count($examinees)
    ]);
    
} catch (PDOException $e) {
    error_log('Error fetching registered examinees: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching examinees: ' . $e->getMessage()
    ]);
}
