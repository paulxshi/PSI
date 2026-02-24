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
    // Get all venues ordered by region and venue name
    $query = "SELECT DISTINCT venue_name, region 
              FROM venue 
              ORDER BY region, venue_name";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $venues = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'venues' => $venues
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
