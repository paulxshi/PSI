<?php
// Get Activity Logs
// Retrieves activity logs with filters and pagination
ini_set('display_errors', 0); // Don't display errors in output
error_reporting(E_ALL); // But log them
header('Content-Type: application/json');
session_start();

// Check if admin is logged in
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once __DIR__ . '/../../config/db.php';

// Get parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$activityType = isset($_GET['activity_type']) ? trim($_GET['activity_type']) : '';
$role = isset($_GET['role']) ? trim($_GET['role']) : '';
$severity = isset($_GET['severity']) ? trim($_GET['severity']) : '';
$dateFilter = isset($_GET['date_filter']) ? trim($_GET['date_filter']) : 'week';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 20; // Records per page
$offset = ($page - 1) * $limit;

try {
    // Build WHERE conditions
    $whereConditions = [];
    $params = [];

    // Activity type filter
    if (!empty($activityType)) {
        $whereConditions[] = "a.activity_type = :activity_type";
        $params[':activity_type'] = $activityType;
    }

    // Role filter
    if (!empty($role)) {
        $whereConditions[] = "a.role = :role";
        $params[':role'] = $role;
    }

    // Severity filter
    if (!empty($severity)) {
        $whereConditions[] = "a.severity = :severity";
        $params[':severity'] = $severity;
    }

    // Date filter
    switch ($dateFilter) {
        case 'today':
            $whereConditions[] = "DATE(a.created_at) = CURDATE()";
            break;
        case 'yesterday':
            $whereConditions[] = "DATE(a.created_at) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
            break;
        case 'week':
            $whereConditions[] = "a.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
            break;
        case 'month':
            $whereConditions[] = "a.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
            break;
        case 'all':
            // No date filter
            break;
        default:
            $whereConditions[] = "a.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
    }

    // Search condition
    if (!empty($search)) {
        $whereConditions[] = "(a.username LIKE :search OR a.email LIKE :search OR a.ip_address LIKE :search OR a.description LIKE :search)";
        $params[':search'] = '%' . $search . '%';
    }

    $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

    // Get total count
    $countQuery = "SELECT COUNT(*) as total FROM activity_logs a $whereClause";
    $countStmt = $pdo->prepare($countQuery);
    $countStmt->execute($params);
    $totalRecords = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Get paginated data
    $dataQuery = "
        SELECT 
            a.log_id,
            a.user_id,
            a.username,
            a.email,
            a.activity_type,
            a.description,
            a.role,
            a.severity,
            a.metadata,
            a.ip_address,
            a.user_agent,
            a.created_at,
            u.first_name,
            u.middle_name,
            u.last_name
        FROM activity_logs a
        LEFT JOIN users u ON a.user_id = u.user_id
        $whereClause
        ORDER BY a.created_at DESC
        LIMIT :limit OFFSET :offset
    ";
    
    $dataStmt = $pdo->prepare($dataQuery);
    
    // Bind all parameters
    foreach ($params as $key => $value) {
        $dataStmt->bindValue($key, $value);
    }
    
    $dataStmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $dataStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    
    $dataStmt->execute();
    $records = $dataStmt->fetchAll(PDO::FETCH_ASSOC);

    // Get summary statistics
    $summaryStmt = $pdo->prepare("
        SELECT 
            COUNT(*) as total_today,
            SUM(CASE WHEN activity_type = 'login_success' THEN 1 ELSE 0 END) as success_logins,
            SUM(CASE WHEN activity_type = 'login_failed' THEN 1 ELSE 0 END) as failed_logins,
            SUM(CASE WHEN activity_type = 'account_lockout' THEN 1 ELSE 0 END) as lockouts
        FROM activity_logs
        WHERE DATE(created_at) = CURDATE()
    ");
    $summaryStmt->execute();
    $summary = $summaryStmt->fetch(PDO::FETCH_ASSOC);

    $totalPages = ceil($totalRecords / $limit);

    echo json_encode([
        'success' => true,
        'data' => $records,
        'pagination' => [
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_records' => $totalRecords,
            'limit' => $limit,
            'offset' => $offset
        ],
        'summary' => [
            'today_total' => (int)$summary['total_today'],
            'success_logins' => (int)$summary['success_logins'],
            'failed_logins' => (int)$summary['failed_logins'],
            'lockouts' => (int)$summary['lockouts']
        ]
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    error_log('Activity Log PDO Error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Database error occurred'
    ]);
} catch (Exception $e) {
    http_response_code(500);
    error_log('Activity Log Error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while retrieving logs'
    ]);
}
?>
