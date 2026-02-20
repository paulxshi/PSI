<?php
require_once "../../config/db.php";
require_once "../../php/log_activity.php";
session_start();

header('Content-Type: application/json');

// Get JSON input
$data = json_decode(file_get_contents("php://input"), true);

// Validate required data
if (!isset($data['examinee_id'], $data['action'])) {
    echo json_encode([
        "success" => false,
        "message" => "Missing data"
    ]);
    exit;
}

$examineeId = $data['examinee_id'];
$action = $data['action'];

/* Determine status and prepare correct query */
if ($action === "complete") {

    $status = "Completed";

    // ✅ Update status + scanned_at
    $stmt = $pdo->prepare("
        UPDATE examinees
        SET examinee_status = ?,
            scanned_at = NOW()
        WHERE examinee_id = ?
    ");

} elseif ($action === "reject") {

    $status = "Rejected";

    // ✅ Update status ONLY (no scanned_at)
    $stmt = $pdo->prepare("
        UPDATE examinees
        SET examinee_status = ?,
        scanned_at = NOW()
        WHERE examinee_id = ?
    ");

} else {
    echo json_encode([
        "success" => false,
        "message" => "Invalid action"
    ]);
    exit;
}

// Execute update
$success = $stmt->execute([$status, $examineeId]);

if (!$success) {
    echo json_encode([
        "success" => false,
        "message" => "Database update failed"
    ]);
    exit;
}

/* Log activity (only if admin session exists) */
if (isset($_SESSION['user_id'])) {

    $metadata = [
        'examinee_id' => $examineeId,
        'new_status' => $status,
        'action' => $action
    ];

    logActivity(
        'admin_examinee_updated',
        "Admin updated examinee #{$examineeId} status to {$status}",
        $_SESSION['user_id'],
        $_SESSION['username'] ?? 'Admin',
        $_SESSION['email'] ?? '',
        'admin',
        'info',
        $metadata
    );
}

// Final success response
echo json_encode([
    "success" => true,
    "message" => "Status updated to " . $status
]);