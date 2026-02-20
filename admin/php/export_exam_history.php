<?php
// Export Exam History for specific schedule as CSV
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

    // Get all registered examinees for this schedule (status = 'Scheduled')
    $examineesStmt = $pdo->prepare("
        SELECT 
            u.test_permit,
            CONCAT(u.first_name, ' ', COALESCE(u.middle_name, ''), ' ', u.last_name) as full_name,
            u.email,
            u.contact_number,
            e.status as registration_status,
            e.examinee_status as completion_status,
            e.updated_at
        FROM examinees e
        INNER JOIN users u ON e.user_id = u.user_id
        WHERE e.schedule_id = :schedule_id 
            AND e.status = 'Scheduled'
        ORDER BY u.last_name ASC, u.first_name ASC
    ");
    $examineesStmt->execute([':schedule_id' => $schedule_id]);
    $allExaminees = $examineesStmt->fetchAll(PDO::FETCH_ASSOC);

    // Separate examinees into two groups
    $registeredExaminees = array_filter($allExaminees, function($e) {
        return empty($e['completion_status']) || $e['completion_status'] !== 'Completed';
    });
    
    $completedExaminees = array_filter($allExaminees, function($e) {
        return $e['completion_status'] === 'Completed';
    });

    // Set headers for CSV download
    $filename = sprintf(
        "Exam_History_%s_%s_%s.csv",
        $schedule['venue_name'],
        date('Y-m-d', strtotime($schedule['scheduled_date'])),
        date('His')
    );
    
    // Clean filename - remove special characters
    $filename = preg_replace('/[^A-Za-z0-9_\-.]/', '_', $filename);

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');

    // Create output stream
    $output = fopen('php://output', 'w');

    // Add UTF-8 BOM for proper Excel encoding
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

    // Add header information
    fputcsv($output, ['EXAMINATION HISTORY REPORT']);
    fputcsv($output, ['']);
    fputcsv($output, ['Region:', $schedule['region']]);
    fputcsv($output, ['Venue:', $schedule['venue_name']]);
    fputcsv($output, ['Exam Date:', date('F d, Y', strtotime($schedule['scheduled_date']))]);
    fputcsv($output, ['Total Registered:', count($allExaminees)]);
    fputcsv($output, ['Total Completed:', count($completedExaminees)]);
    fputcsv($output, ['Report Generated:', date('F d, Y h:i A')]);
    fputcsv($output, ['']);
    fputcsv($output, ['']);

    // ========== SECTION 1: REGISTERED EXAMINEES ==========
    fputcsv($output, ['========== REGISTERED EXAMINEES (' . count($registeredExaminees) . ') ==========']);
    fputcsv($output, ['']);
    
    // Column headers for registered
    fputcsv($output, [
        'No.',
        'Test Permit',
        'Full Name',
        'Email',
        'Contact Number',
        'Registration Date'
    ]);

    // Add registered examinee data
    if (count($registeredExaminees) > 0) {
        $index = 1;
        foreach ($registeredExaminees as $examinee) {
            fputcsv($output, [
                $index++,
                $examinee['test_permit'] ?? '',
                $examinee['full_name'] ?? '',
                $examinee['email'] ?? '',
                $examinee['contact_number'] ?? '',
                $examinee['updated_at'] ? date('M d, Y h:i A', strtotime($examinee['updated_at'])) : ''
            ]);
        }
    } else {
        fputcsv($output, ['', 'No registered examinees found (not yet completed)']);
    }

    fputcsv($output, ['']);
    fputcsv($output, ['']);

    // ========== SECTION 2: COMPLETED EXAMINEES ==========
    fputcsv($output, ['========== COMPLETED EXAMINEES (' . count($completedExaminees) . ') ==========']);
    fputcsv($output, ['']);
    
    // Column headers for completed
    fputcsv($output, [
        'No.',
        'Test Permit',
        'Full Name',
        'Email',
        'Contact Number',
        'Completion Date'
    ]);

    // Add completed examinee data
    if (count($completedExaminees) > 0) {
        $index = 1;
        foreach ($completedExaminees as $examinee) {
            fputcsv($output, [
                $index++,
                $examinee['test_permit'] ?? '',
                $examinee['full_name'] ?? '',
                $examinee['email'] ?? '',
                $examinee['contact_number'] ?? '',
                $examinee['updated_at'] ? date('M d, Y h:i A', strtotime($examinee['updated_at'])) : ''
            ]);
        }
    } else {
        fputcsv($output, ['', 'No completed examinees found']);
    }

    fclose($output);
    exit;

} catch (PDOException $e) {
    error_log("Export Error: " . $e->getMessage());
    http_response_code(500);
    echo "Database error occurred";
    exit;
}
