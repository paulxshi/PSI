<?php
session_start();
require_once "../../config/db.php";
require_once "../../php/log_activity.php";

header('Content-Type: application/json');

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized access'
    ]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
    exit();
}


// Debug: log raw POST data
file_put_contents(__DIR__ . '/archive_debug.log', date('Y-m-d H:i:s') . " | POST: " . print_r($_POST, true) . "\n", FILE_APPEND);

$schedule_ids = isset($_POST['schedule_ids']) ? $_POST['schedule_ids'] : null;
if ($schedule_ids && is_string($schedule_ids)) {
    // Try to decode JSON if sent as string
    $decoded = json_decode($schedule_ids, true);
    if (is_array($decoded)) {
        $schedule_ids = $decoded;
    }
}

if (!$schedule_ids) {
    file_put_contents(__DIR__ . '/archive_debug.log', date('Y-m-d H:i:s') . " | No schedule_ids found\n", FILE_APPEND);
    echo json_encode([
        'success' => false,
        'message' => 'No schedule IDs provided.'
    ]);
    exit();
}

if (!is_array($schedule_ids)) {
    $schedule_ids = [$schedule_ids];
}
file_put_contents(__DIR__ . '/archive_debug.log', date('Y-m-d H:i:s') . " | schedule_ids: " . print_r($schedule_ids, true) . "\n", FILE_APPEND);

try {
    $pdo->beginTransaction();
    $placeholders = implode(',', array_fill(0, count($schedule_ids), '?'));
    $stmt = $pdo->prepare("UPDATE schedules SET archived = 1 WHERE schedule_id IN ($placeholders)");
    $result = $stmt->execute($schedule_ids);
    file_put_contents(__DIR__ . '/archive_debug.log', date('Y-m-d H:i:s') . " | SQL executed, result: " . print_r($result, true) . "\n", FILE_APPEND);
    $pdo->commit();

    // Log activity
    logActivity(
        'admin_schedule_archived',
        "Admin archived schedules: " . implode(", ", $schedule_ids),
        $_SESSION['user_id'],
        $_SESSION['username'] ?? 'Admin',
        $_SESSION['email'] ?? '',
        'admin',
        'info',
        ['schedule_ids' => $schedule_ids]
    );

    echo json_encode([
        'success' => true,
        'message' => 'Selected schedules archived successfully!'
    ]);
} catch (Exception $e) {
    $pdo->rollBack();
    error_log('Archive error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error archiving schedules: ' . $e->getMessage()
    ]);
}
