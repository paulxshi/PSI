<?php
header('Content-Type: application/json');
session_start();

// Check if admin is logged in
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once __DIR__ . '/../../config/db.php';

// Check if requesting a single record by ID
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    try {
        $stmt = $pdo->prepare("
            SELECT 
                id, 
                test_permit, 
                last_name, 
                first_name, 
                middle_name,
                email, 
                used, 
                uploaded_at
            FROM examinee_masterlist
            WHERE id = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        $record = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($record) {
            echo json_encode([
                'success' => true,
                'record' => $record
            ]);
        } else {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => 'Record not found'
            ]);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
    exit;
}

// Get parameters for list view
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status = isset($_GET['status']) ? $_GET['status'] : ''; // 'all', '0', '1'
$date = isset($_GET['date']) ? $_GET['date'] : ''; // Date filter
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 10; // Records per page
$offset = ($page - 1) * $limit;

try {
    // Build base query
    $whereConditions = [];
    $params = [];

    // Search condition
    if (!empty($search)) {
        $whereConditions[] = "(test_permit LIKE :search OR CONCAT(first_name, ' ', last_name) LIKE :search OR email LIKE :search)";
        $params[':search'] = '%' . $search . '%';
    }

    // Status filter
    if ($status !== '' && ($status === '0' || $status === '1')) {
        $whereConditions[] = "used = :status";
        $params[':status'] = (int)$status;
    }

    // Date filter
    if (!empty($date)) {
        $whereConditions[] = "DATE(uploaded_at) = :date";
        $params[':date'] = $date;
    }

    $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

    // Get total count
    $countQuery = "SELECT COUNT(*) as total FROM examinee_masterlist $whereClause";
    $countStmt = $pdo->prepare($countQuery);
    $countStmt->execute($params);
    $totalRecords = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Get data
    $dataQuery = "
        SELECT 
            id, 
            test_permit, 
            last_name, 
            first_name, 
            middle_name,
            CONCAT(first_name, ' ', last_name) as full_name,
            email, 
            used, 
            uploaded_at
        FROM examinee_masterlist
        $whereClause
        ORDER BY uploaded_at DESC
        LIMIT :limit OFFSET :offset
    ";
    
    $dataStmt = $pdo->prepare($dataQuery);
    
    // Bind search/status params
    foreach ($params as $key => $value) {
        $dataStmt->bindValue($key, $value);
    }
    
    // Bind limit and offset
    $dataStmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $dataStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    
    $dataStmt->execute();
    $records = $dataStmt->fetchAll(PDO::FETCH_ASSOC);

    // Get total counts
    $countStmt = $pdo->prepare("
        SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN used = 0 THEN 1 ELSE 0 END) as not_registered,
            SUM(CASE WHEN used = 1 THEN 1 ELSE 0 END) as registered
        FROM examinee_masterlist
    ");
    $countStmt->execute();
    $counts = $countStmt->fetch(PDO::FETCH_ASSOC);

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
        'counts' => [
            'total_uploaded' => (int)$counts['total'],
            'total_registered' => (int)$counts['registered'],
            'total_not_registered' => (int)$counts['not_registered']
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
