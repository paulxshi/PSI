<?php
// Separate session for accountants
session_name('PSI_ACCOUNTANT');
session_start();
header('Content-Type: application/json');

// Check if user is logged in and has proper role
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'accountant') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access - Accountant role required']);
    exit;
}

require_once('../../config/db.php');

try {
    // Get only venues that have at least one paid examinee
    $query = "SELECT DISTINCT v.venue_name, v.region 
              FROM venue v
              INNER JOIN schedules s ON v.venue_id = s.venue_id
              INNER JOIN examinees e ON s.schedule_id = e.schedule_id
              INNER JOIN payments p ON e.examinee_id = p.examinee_id
              WHERE p.status = 'PAID'
              ORDER BY v.region, v.venue_name";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $venues = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'venues' => $venues
    ]);
    
} catch (PDOException $e) {
    error_log('Accountant get_venues error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while fetching venues. Please try again later.'
    ]);
}
?>
