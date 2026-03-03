<?php
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once __DIR__ . '/../../config/db.php';

// Get parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status = isset($_GET['status']) ? trim($_GET['status']) : ''; // Filter by examinee_status
$region = isset($_GET['region']) ? trim($_GET['region']) : ''; // Filter by region
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 10; // Records per page
$offset = ($page - 1) * $limit;

try {
    $whereConditions = ["e.status = 'Scheduled'"]; // Only those who have paid and confirmed
    $params = [];

    if (!empty($status)) {
        // Show only examinees with specified examinee_status (e.g., 'Completed')
        $whereConditions[] = "e.examinee_status = :status";
        $params[':status'] = $status;
    } else {
        // Default view (Total Registered): EXCLUDE completed examinees
        $whereConditions[] = "(e.examinee_status IS NULL OR e.examinee_status != 'Completed')";
    }

    // Region filter
    if (!empty($region)) {
        $whereConditions[] = "v.region = :region";
        $params[':region'] = $region;
    }

    // Search condition
    if (!empty($search)) {
        $whereConditions[] = "(CONCAT(u.first_name, ' ', u.last_name) LIKE :search OR u.email LIKE :search OR u.test_permit LIKE :search)";
        $params[':search'] = '%' . $search . '%';
    }

    $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

    // Get total count
    $countQuery = "
        SELECT COUNT(*) as total 
        FROM examinees e
        INNER JOIN users u ON e.user_id = u.user_id
        LEFT JOIN schedules s ON e.schedule_id = s.schedule_id
        LEFT JOIN venue v ON s.venue_id = v.venue_id
        $whereClause
    ";
    $countStmt = $pdo->prepare($countQuery);
    $countStmt->execute($params);
    $totalRecords = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Get data with schedule information
    $dataQuery = "
        SELECT 
            u.user_id,
            u.test_permit,
            u.first_name,
            u.last_name,
            CONCAT(u.first_name, ' ', COALESCE(u.middle_name, ''), ' ', u.last_name) as full_name,
            u.email,
            u.contact_number,
            u.gender,
            u.date_of_birth,
            u.age,
            u.school,
            v.region,
            v.venue_name as exam_venue,
            s.scheduled_date as exam_date,
            e.status,
            e.examinee_status,
            e.schedule_id,
            e.date_of_registration,
            e.scanned_at as completed_date
        FROM examinees e
        INNER JOIN users u ON e.user_id = u.user_id
        LEFT JOIN schedules s ON e.schedule_id = s.schedule_id
        LEFT JOIN venue v ON s.venue_id = v.venue_id
        $whereClause
        ORDER BY e.date_of_registration DESC
        LIMIT :limit OFFSET :offset
    ";
    
    $dataStmt = $pdo->prepare($dataQuery);
    
    // Bind search params
    foreach ($params as $key => $value) {
        $dataStmt->bindValue($key, $value);
    }
    
    // Bind limit and offset
    $dataStmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $dataStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    
    $dataStmt->execute();
    $records = $dataStmt->fetchAll(PDO::FETCH_ASSOC);

    // Get summary counts
    $summaryStmt = $pdo->prepare("
        SELECT 
            SUM(CASE WHEN (examinee_status IS NULL OR examinee_status != 'Completed') THEN 1 ELSE 0 END) as total_registered,
            SUM(CASE WHEN examinee_status = 'Completed' THEN 1 ELSE 0 END) as completed,
            SUM(CASE WHEN examinee_status = 'Absent' THEN 1 ELSE 0 END) as absent,
            SUM(CASE WHEN examinee_status = 'Registered' THEN 1 ELSE 0 END) as registered
        FROM examinees
        WHERE status = 'Scheduled'
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
            'total_registered' => (int)$summary['total_registered'],
            'completed' => (int)$summary['completed'],
            'absent' => (int)$summary['absent'],
            'registered' => (int)$summary['registered']
        ]
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
