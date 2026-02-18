<?php
require_once "../../config/db.php";


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

echo json_encode([
    "success" => true,
    "message" => "Status updated to " . $status
]);
