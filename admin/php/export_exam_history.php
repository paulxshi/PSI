<?php
/**
 * Examination History CSV Export
 * 
 * Generates a clean, Excel-friendly CSV report of examination history
 * for a specific schedule. Uses pure PHP with no external dependencies.
 * 
 * Features:
 * - UTF-8 BOM for proper Excel encoding
 * - Dates formatted as YYYY-MM-DD HH:MM
 * - Phone numbers and test permit numbers prefixed with apostrophe to prevent Excel auto-formatting
 * - Clean, professional output without decorative lines
 * - Proper error handling and security checks
 */

session_start();
require_once "../../config/db.php";

// Check if admin is logged in
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    http_response_code(403);
    echo "Unauthorized access";
    exit;
}

// Get schedule_id from GET parameter
$schedule_id = isset($_GET['schedule_id']) ? (int)$_GET['schedule_id'] : 0;

if ($schedule_id <= 0) {
    http_response_code(400);
    echo "Invalid schedule ID";
    exit;
}

try {
    // Get schedule details
    $scheduleStmt = $pdo->prepare("
        SELECT 
            s.scheduled_date,
            v.venue_name,
            v.region
        FROM schedules s
        INNER JOIN venue v ON s.venue_id = v.venue_id
        WHERE s.schedule_id = :schedule_id
        LIMIT 1
    ");
    $scheduleStmt->execute([':schedule_id' => $schedule_id]);
    $schedule = $scheduleStmt->fetch(PDO::FETCH_ASSOC);

    if (!$schedule) {
        http_response_code(404);
        echo "Schedule not found";
        exit;
    }

    // Get all examinees for this schedule with status 'Registered' or 'Completed'
    $examineesStmt = $pdo->prepare("
        SELECT 
            u.test_permit,
            CONCAT(u.first_name, ' ', COALESCE(u.middle_name, ''), ' ', u.last_name) as full_name,
            u.email,
            u.contact_number,
            e.examinee_status,
            e.updated_at
        FROM examinees e
        INNER JOIN users u ON e.user_id = u.user_id
        WHERE e.attended_schedule_id = :schedule_id 
            AND e.examinee_status IN ('Registered', 'Completed')
        ORDER BY e.examinee_id ASC
    ");
    $examineesStmt->execute([':schedule_id' => $schedule_id]);
    $allExaminees = $examineesStmt->fetchAll(PDO::FETCH_ASSOC);

    // Set headers for CSV download
    $filename = sprintf(
        "Exam_History_%s_%s_%s.csv",
        preg_replace('/[^A-Za-z0-9_\-.]/', '_', $schedule['venue_name']),
        date('Y-m-d', strtotime($schedule['scheduled_date'])),
        date('His')
    );

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');

    // Create output stream
    $output = fopen('php://output', 'w');

    // Add UTF-8 BOM for proper Excel encoding
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

    // Helper function to format dates as YYYY-MM-DD
    $formatDate = function($dateString) {
        if (empty($dateString)) {
            return '';
        }
        return date('Y-m-d', strtotime($dateString));
    };

    // Helper function to prevent Excel auto-formatting of numbers
    // Prefixes with apostrophe to force text format
    $protectNumber = function($value) {
        if (empty($value)) {
            return '';
        }
        // Only protect if it looks like a number or phone number
        if (is_numeric($value) || preg_match('/^[0-9\-\+\s]+$/', $value)) {
            return "'" . $value;
        }
        return $value;
    };

    // EXAMINEES TABLE WITH STATUS COLUMN
    fputcsv($output, [
        'No.',
        'Test Permit No.',
        'Full Name',
        'Email Address',
        'Contact Number',
        'Registration Date',
        'Status'
    ]);

    if (count($allExaminees) > 0) {
        $index = 1;
        foreach ($allExaminees as $examinee) {
            fputcsv($output, [
                $index++,
                $protectNumber($examinee['test_permit'] ?? ''),
                $examinee['full_name'] ?? '',
                $examinee['email'] ?? '',
                $protectNumber($examinee['contact_number'] ?? ''),
                $formatDate($examinee['updated_at'] ?? ''),
                $examinee['examinee_status'] ?? ''
            ]);
        }
    } else {
        fputcsv($output, ['', '', 'No examinees found', '', '', '', '']);
    }

    fclose($output);
    exit;

} catch (PDOException $e) {
    error_log("Export Error: " . $e->getMessage());
    http_response_code(500);
    echo "Database error occurred";
    exit;
}
