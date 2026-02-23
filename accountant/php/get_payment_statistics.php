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
    // Get total revenue by region
    $regionQuery = "SELECT 
                        v.region,
                        COUNT(DISTINCT p.payment_id) as total_payments,
                        SUM(p.amount) as total_revenue,
                        COUNT(DISTINCT e.examinee_id) as total_examinees
                    FROM payments p
                    INNER JOIN examinees e ON p.examinee_id = e.examinee_id
                    INNER JOIN schedules s ON e.schedule_id = s.schedule_id
                    INNER JOIN venue v ON s.venue_id = v.venue_id
                    WHERE p.status = 'PAID'
                    GROUP BY v.region
                    ORDER BY v.region";
    
    $stmt = $pdo->prepare($regionQuery);
    $stmt->execute();
    $regionStats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format region data
    $regions = [
        'Luzon' => ['revenue' => 0, 'examinees' => 0, 'payments' => 0],
        'Visayas' => ['revenue' => 0, 'examinees' => 0, 'payments' => 0],
        'Mindanao' => ['revenue' => 0, 'examinees' => 0, 'payments' => 0]
    ];
    
    foreach ($regionStats as $stat) {
        $regions[$stat['region']] = [
            'revenue' => number_format($stat['total_revenue'], 2),
            'revenue_raw' => $stat['total_revenue'],
            'examinees' => $stat['total_examinees'],
            'payments' => $stat['total_payments']
        ];
    }
    
    // Get venue statistics
    $venueQuery = "SELECT 
                        v.venue_name,
                        v.region,
                        COUNT(DISTINCT s.schedule_id) as total_schedules,
                        COUNT(DISTINCT CASE WHEN p.status = 'PAID' THEN e.examinee_id END) as students_paid,
                        SUM(CASE WHEN p.status = 'PAID' THEN p.amount ELSE 0 END) as total_revenue
                    FROM venue v
                    LEFT JOIN schedules s ON v.venue_id = s.venue_id
                    LEFT JOIN examinees e ON s.schedule_id = e.schedule_id
                    LEFT JOIN payments p ON e.examinee_id = p.examinee_id
                    GROUP BY v.venue_id, v.venue_name, v.region
                    HAVING total_revenue > 0
                    ORDER BY total_revenue DESC";
    
    $stmt = $pdo->prepare($venueQuery);
    $stmt->execute();
    $venueStats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Group venue stats by region
    $venuesByRegion = [
        'Luzon' => [],
        'Visayas' => [],
        'Mindanao' => []
    ];
    
    foreach ($venueStats as $venue) {
        $venuesByRegion[$venue['region']][] = [
            'venue_name' => $venue['venue_name'],
            'total_schedules' => $venue['total_schedules'],
            'students_paid' => $venue['students_paid'],
            'total_revenue' => number_format($venue['total_revenue'], 2),
            'revenue_raw' => $venue['total_revenue']
        ];
    }
    
    // Get overall statistics
    $overallQuery = "SELECT 
                        COUNT(DISTINCT p.payment_id) as total_payments,
                        SUM(p.amount) as total_revenue,
                        COUNT(DISTINCT e.examinee_id) as total_examinees,
                        COUNT(DISTINCT s.schedule_id) as total_schedules
                     FROM payments p
                     INNER JOIN examinees e ON p.examinee_id = e.examinee_id
                     INNER JOIN schedules s ON e.schedule_id = s.schedule_id
                     WHERE p.status = 'PAID'";
    
    $stmt = $pdo->prepare($overallQuery);
    $stmt->execute();
    $overall = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'regions' => $regions,
        'venuesByRegion' => $venuesByRegion,
        'overall' => [
            'total_revenue' => number_format($overall['total_revenue'], 2),
            'revenue_raw' => $overall['total_revenue'],
            'total_payments' => $overall['total_payments'],
            'total_examinees' => $overall['total_examinees'],
            'total_schedules' => $overall['total_schedules']
        ]
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
