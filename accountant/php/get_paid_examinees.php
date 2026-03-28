<?php
session_name('PSI_ACCOUNTANT');
session_start();
header('Content-Type: application/json');

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
    // Get filter parameters
    $region = isset($_GET['region']) ? $_GET['region'] : '';
    $venue = isset($_GET['venue']) ? $_GET['venue'] : '';
    $dateFrom = isset($_GET['dateFrom']) ? $_GET['dateFrom'] : '';
    $dateTo = isset($_GET['dateTo']) ? $_GET['dateTo'] : '';
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    
    // Pagination parameters
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $limit = isset($_GET['limit']) ? max(1, min(100, intval($_GET['limit']))) : 10;
    $exportAll = isset($_GET['exportAll']) && $_GET['exportAll'] === '1';
    
    // Validate date formats
    if (!empty($dateFrom) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateFrom)) {
        $dateFrom = '';
    }
    if (!empty($dateTo) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateTo)) {
        $dateTo = '';
    }
    
    // Build the query
    $query = "SELECT 
                u.test_permit,
                CONCAT(u.first_name, ' ', COALESCE(u.middle_name, ''), ' ', u.last_name) AS full_name,
                u.email,
                v.venue_name,
                v.region,
                s.scheduled_date,
                p.amount,
                p.paid_at,
                p.payment_id,
                p.xendit_invoice_id,
                p.external_id,
                p.channel,
                p.xendit_response
              FROM payments p
              INNER JOIN users u ON p.user_id = u.user_id
              INNER JOIN examinees e ON p.examinee_id = e.examinee_id
              INNER JOIN schedules s ON e.schedule_id = s.schedule_id
              INNER JOIN venue v ON s.venue_id = v.venue_id
              WHERE p.status = 'PAID'";
    
    $params = [];
    
    // Add region filter
    if (!empty($region)) {
        $query .= " AND v.region = :region";
        $params[':region'] = $region;
    }
    
    // Add venue filter
    if (!empty($venue)) {
        $query .= " AND v.venue_name = :venue";
        $params[':venue'] = $venue;
    }
    
    // Add date range filter
    if (!empty($dateFrom)) {
        $query .= " AND DATE(p.paid_at) >= :dateFrom";
        $params[':dateFrom'] = $dateFrom;
    }
    
    if (!empty($dateTo)) {
        $query .= " AND DATE(p.paid_at) <= :dateTo";
        $params[':dateTo'] = $dateTo;
    }
    
    // Add search filter
    if (!empty($search)) {
        $query .= " AND (u.test_permit LIKE :search OR 
                         u.first_name LIKE :search OR 
                         u.last_name LIKE :search OR 
                         u.email LIKE :search)";
        $params[':search'] = "%$search%";
    }
    
    $query .= " ORDER BY p.paid_at DESC";
    
    // Get total count first
    $countQuery = "SELECT COUNT(*) as total FROM (" . $query . ") as countTable";
    $countStmt = $pdo->prepare($countQuery);
    $countStmt->execute($params);
    $totalCount = (int)$countStmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Apply pagination unless exporting all
    if (!$exportAll) {
        $offset = ($page - 1) * $limit;
        $query .= " LIMIT " . intval($limit) . " OFFSET " . intval($offset);
    }
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $examinees = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Extract payment method from xendit_response
    foreach ($examinees as &$examinee) {
        // Use the channel column directly if available
        $paymentMethod = 'Online Payment';
        if (!empty($examinee['channel'])) {
            $paymentMethod = $examinee['channel'];
        } elseif (!empty($examinee['xendit_response'])) {
            $xenditData = json_decode($examinee['xendit_response'], true);
            if (isset($xenditData['payment_channel'])) {
                $paymentMethod = $xenditData['payment_channel'];
            } elseif (isset($xenditData['payment_method'])) {
                $paymentMethod = $xenditData['payment_method'];
            }
        }
        $examinee['payment_method'] = $paymentMethod;
        
        // Format date
        $examinee['paid_at_formatted'] = date('M d, Y h:i A', strtotime($examinee['paid_at']));
        $examinee['scheduled_date_formatted'] = date('M d, Y', strtotime($examinee['scheduled_date']));
        
        // Format amount
        $examinee['amount_formatted'] = '₱' . number_format($examinee['amount'], 2);
        
        // Remove xendit_response from output for cleaner data
        unset($examinee['xendit_response']);
    }
    
    $totalPages = $exportAll ? 1 : ceil($totalCount / $limit);
    
    echo json_encode([
        'success' => true,
        'data' => $examinees,
        'count' => $totalCount,
        'page' => $page,
        'limit' => $limit,
        'totalPages' => $totalPages
    ]);
    
} catch (PDOException $e) {
    error_log('Accountant get_paid_examinees error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while fetching payment records. Please try again later.'
    ]);
}
