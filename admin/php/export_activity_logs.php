<?php
// Export Activity Logs to CSV or PDF
ini_set('display_errors', 0);
error_reporting(E_ALL);
session_start();

// Check if admin is logged in
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    http_response_code(403);
    die('Unauthorized');
}

require_once __DIR__ . '/../../config/db.php';

// Get parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$activityType = isset($_GET['activity_type']) ? trim($_GET['activity_type']) : '';
$role = isset($_GET['role']) ? trim($_GET['role']) : '';
$severity = isset($_GET['severity']) ? trim($_GET['severity']) : '';
$dateFilter = isset($_GET['date_filter']) ? trim($_GET['date_filter']) : 'week';
$format = isset($_GET['format']) ? trim($_GET['format']) : 'csv';

try {
    // Build WHERE conditions
    $whereConditions = [];
    $params = [];

    // Activity type filter
    if (!empty($activityType)) {
        $whereConditions[] = "activity_type = :activity_type";
        $params[':activity_type'] = $activityType;
    }

    // Role filter
    if (!empty($role)) {
        $whereConditions[] = "role = :role";
        $params[':role'] = $role;
    }

    // Severity filter
    if (!empty($severity)) {
        $whereConditions[] = "severity = :severity";
        $params[':severity'] = $severity;
    }

    // Date filter
    switch ($dateFilter) {
        case 'today':
            $whereConditions[] = "DATE(created_at) = CURDATE()";
            break;
        case 'yesterday':
            $whereConditions[] = "DATE(created_at) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
            break;
        case 'week':
            $whereConditions[] = "created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
            break;
        case 'month':
            $whereConditions[] = "created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
            break;
        case 'all':
            // No date filter
            break;
        default:
            $whereConditions[] = "created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
    }

    // Search condition
    if (!empty($search)) {
        $whereConditions[] = "(username LIKE :search OR email LIKE :search OR ip_address LIKE :search OR description LIKE :search)";
        $params[':search'] = '%' . $search . '%';
    }

    $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

    // Get all matching records (no pagination for export)
    $dataQuery = "
        SELECT 
            log_id,
            user_id,
            username,
            email,
            activity_type,
            description,
            role,
            severity,
            ip_address,
            created_at
        FROM activity_logs
        $whereClause
        ORDER BY created_at DESC
    ";
    
    $dataStmt = $pdo->prepare($dataQuery);
    
    // Bind all parameters
    foreach ($params as $key => $value) {
        $dataStmt->bindValue($key, $value);
    }
    
    $dataStmt->execute();
    $records = $dataStmt->fetchAll(PDO::FETCH_ASSOC);

    // Export based on format
    if ($format === 'csv') {
        exportCSV($records);
    } elseif ($format === 'pdf') {
        exportPDF($records);
    } else {
        die('Invalid format');
    }

} catch (PDOException $e) {
    http_response_code(500);
    error_log('Export Activity Log PDO Error: ' . $e->getMessage());
    die('Database error occurred');
} catch (Exception $e) {
    http_response_code(500);
    error_log('Export Activity Log Error: ' . $e->getMessage());
    die('An error occurred while exporting logs');
}

// Export to CSV
function exportCSV($records) {
    $filename = 'activity_logs_' . date('Y-m-d_His') . '.csv';
    
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    $output = fopen('php://output', 'w');
    
    // Add UTF-8 BOM for Excel compatibility
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Add headers
    fputcsv($output, [
        'Log ID',
        'Timestamp',
        'User ID',
        'Username',
        'Email',
        'Role',
        'Activity Type',
        'Description',
        'Severity',
        'IP Address'
    ]);
    
    // Add data rows
    foreach ($records as $record) {
        fputcsv($output, [
            $record['log_id'],
            $record['created_at'],
            $record['user_id'],
            $record['username'] ?? 'N/A',
            $record['email'] ?? 'N/A',
            strtoupper($record['role'] ?? 'N/A'),
            $record['activity_type'],
            $record['description'],
            strtoupper($record['severity'] ?? 'N/A'),
            $record['ip_address'] ?? 'N/A'
        ]);
    }
    
    fclose($output);
    exit;
}

// Export to PDF
function exportPDF($records) {
    // Simple HTML-based PDF using browser's print-to-PDF
    // For a production system, consider using libraries like TCPDF or DomPDF
    
    $filename = 'activity_logs_' . date('Y-m-d_His');
    
    header('Content-Type: text/html; charset=utf-8');
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Activity Logs Report</title>
        <style>
            @media print {
                @page { 
                    size: A4 landscape; 
                    margin: 1cm;
                }
            }
            body {
                font-family: Arial, sans-serif;
                font-size: 10px;
                margin: 20px;
            }
            h1 {
                text-align: center;
                font-size: 18px;
                margin-bottom: 5px;
            }
            .meta {
                text-align: center;
                font-size: 10px;
                color: #666;
                margin-bottom: 20px;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 10px;
            }
            th, td {
                border: 1px solid #ddd;
                padding: 6px;
                text-align: left;
                font-size: 9px;
            }
            th {
                background-color: #f2f2f2;
                font-weight: bold;
            }
            tr:nth-child(even) {
                background-color: #f9f9f9;
            }
            .badge {
                padding: 2px 6px;
                border-radius: 3px;
                font-size: 8px;
                font-weight: bold;
            }
            .role-admin { background: #333; color: white; }
            .role-examinee { background: #0d6efd; color: white; }
            .role-system { background: #6c757d; color: white; }
            .severity-info { background: #0dcaf0; color: white; }
            .severity-warning { background: #ffc107; color: black; }
            .severity-error { background: #dc3545; color: white; }
            .severity-critical { background: #8b0000; color: white; }
            .auto-print { display: none; }
        </style>
        <script>
            window.onload = function() {
                // Automatically open print dialog
                window.print();
            }
        </script>
    </head>
    <body>
        <h1>PSI Activity Logs Report</h1>
        <div class="meta">
            Generated on <?php echo date('F d, Y g:i A'); ?> | 
            Total Records: <?php echo count($records); ?>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>Timestamp</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Activity</th>
                    <th>Description</th>
                    <th>Severity</th>
                    <th>IP Address</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($records as $record): ?>
                <tr>
                    <td><?php echo date('m/d/Y H:i:s', strtotime($record['created_at'])); ?></td>
                    <td><?php echo htmlspecialchars($record['username'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($record['email'] ?? 'N/A'); ?></td>
                    <td>
                        <span class="badge role-<?php echo strtolower($record['role'] ?? 'system'); ?>">
                            <?php echo strtoupper($record['role'] ?? 'N/A'); ?>
                        </span>
                    </td>
                    <td><?php echo htmlspecialchars($record['activity_type']); ?></td>
                    <td><?php echo htmlspecialchars(substr($record['description'], 0, 80)); ?></td>
                    <td>
                        <span class="badge severity-<?php echo strtolower($record['severity'] ?? 'info'); ?>">
                            <?php echo strtoupper($record['severity'] ?? 'N/A'); ?>
                        </span>
                    </td>
                    <td><?php echo htmlspecialchars($record['ip_address'] ?? 'N/A'); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div class="auto-print">
            This report will automatically open the print dialog. 
            Save as PDF using your browser's print functionality.
        </div>
    </body>
    </html>
    <?php
    exit;
}
?>
