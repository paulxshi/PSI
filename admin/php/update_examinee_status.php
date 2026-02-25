<?php
require_once "../../config/db.php";
require_once "../../php/log_activity.php";

session_start();
header('Content-Type: application/json');

// Ensure request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method"
    ]);
    exit;
}

// Get JSON input
$data = json_decode(file_get_contents("php://input"), true);

// Validate required data
if (
    !isset($data['examinee_id']) ||
    !isset($data['action'])
) {
    echo json_encode([
        "success" => false,
        "message" => "Missing required data"
    ]);
    exit;
}

$examineeId = $data['examinee_id'];
$action = $data['action'];
$attended_schedule_id = $data['attended_schedule_id'] ?? null;

/* -------------------------------
   Determine status and query
--------------------------------*/

if ($action === "complete") {

    $status = "Completed";

    $stmt = $pdo->prepare("
        UPDATE examinees
        SET examinee_status = ?,
            scanned_at = NOW(),
            attended_schedule_id = ?
        WHERE examinee_id = ?
    ");

    $executeParams = [
        $status,
        $attended_schedule_id,
        $examineeId
    ];

} elseif ($action === "reject") {

    $status = "Rejected";

    // If you DON'T want scanned_at for rejected,
    // remove scanned_at from this query.
    $stmt = $pdo->prepare("
        UPDATE examinees
        SET examinee_status = ?,
            scanned_at = NOW(),
            attended_schedule_id = ?
        WHERE examinee_id = ?
    ");

    $executeParams = [
        $status,
        $attended_schedule_id,
        $examineeId
    ];

} else {
    echo json_encode([
        "success" => false,
        "message" => "Invalid action"
    ]);
    exit;
}

/* -------------------------------
   Execute Update
--------------------------------*/

try {

    $success = $stmt->execute($executeParams);

    if (!$success || $stmt->rowCount() === 0) {
        echo json_encode([
            "success" => false,
            "message" => "No record updated"
        ]);
        exit;
    }

} catch (PDOException $e) {

    echo json_encode([
        "success" => false,
        "message" => "Database error",
        "error" => $e->getMessage()
    ]);
    exit;
}

/* -------------------------------
   Log Activity (if admin logged in)
--------------------------------*/

if (isset($_SESSION['user_id'])) {

    $metadata = [
        'examinee_id' => $examineeId,
        'new_status' => $status,
        'action' => $action,
        'attended_schedule_id' => $attended_schedule_id
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

/* -------------------------------
   Success Response
--------------------------------*/

echo json_encode([
    "success" => true,
    "message" => "Status successfully updated to {$status}"
]);
exit;