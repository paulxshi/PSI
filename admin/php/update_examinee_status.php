<?php
require_once "../../config/db.php";
require_once "../../php/log_activity.php";
session_start();


header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['examinee_id'], $data['action'])) {
    echo json_encode(["success" => false, "message" => "Missing data"]);
    exit;
}

$examineeId = $data['examinee_id'];
$action = $data['action'];

/* Determine new status */
if ($action === "complete") {
    $status = "Completed";
} elseif ($action === "reject") {
    $status = "Rejected";
} else {
    echo json_encode(["success" => false, "message" => "Invalid action"]);
    exit;
}

/* Update database */
$stmt = $pdo->prepare("
    UPDATE examinees
    SET examinee_status = ?
    WHERE examinee_id = ?
");

$stmt->execute([$status, $examineeId]);

// Log activity
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

echo json_encode([
    "success" => true,
    "message" => "Status updated to " . $status
]);
