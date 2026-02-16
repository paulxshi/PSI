<?php
// Get registered examinees from users table with search and pagination
header('Content-Type: application/json');
session_start();

// Check if admin is logged in
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once __DIR__ . '/../config/db.php';

// Get parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 10; // Records per page
$offset = ($page - 1) * $limit;

try {
    // Build base query
    $whereConditions = ['status = "approved"']; // Only approved registrations
    $params = [];

    // Search condition
    if (!empty($search)) {
        $whereConditions[] = "(CONCAT(first_name, ' ', last_name) LIKE :search OR email LIKE :search OR test_permit LIKE :search)";
        $params[':search'] = '%' . $search . '%';
    }

    $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

    // Get total count
    $countQuery = "SELECT COUNT(*) as total FROM users $whereClause";
    $countStmt = $pdo->prepare($countQuery);
    $countStmt->execute($params);
    $totalRecords = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Get data
    $dataQuery = "
        SELECT 
            user_id,
            CONCAT(first_name, ' ', last_name) as full_name,
            first_name,
            last_name,
            email,
            contact_number,
            test_permit,
            gender,
            date_of_birth,
            age,
            school,
            region,
            exam_venue,
            exam_date,
            status,
            date_of_registration
        FROM users
        $whereClause
        ORDER BY date_of_registration DESC
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
            COUNT(*) as total_registered,
            SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected
        FROM users
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
            'approved' => (int)$summary['approved'],
            'pending' => (int)$summary['pending'],
            'rejected' => (int)$summary['rejected']
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
