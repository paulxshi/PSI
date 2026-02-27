<?php
require "../../config/db.php";


ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode([
        "status_class" => "invalid",
        "status_message" => "Invalid Request"
    ]);
    exit;
}

if (!isset($data['external_id'])) {
    echo json_encode([
        "status_class" => "invalid",
        "status_message" => "Missing Transaction Number"
    ]);
    exit;
}

$invoiceNo = $data['external_id'];
$stmt = $pdo->prepare("SELECT * FROM payments WHERE external_id = ?");
$stmt->execute([$invoiceNo]);
$payment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$payment || $payment['status'] !== 'PAID') {
    echo json_encode([
        "status_class" => "invalid",
        "status_message" => !$payment ? "Payment Not Found" : "Payment Pending",
        "debug" => !$payment ? "No record in payments table" : "Payment status is not PAID"
    ]);
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM examinees WHERE examinee_id = ?");
$stmt->execute([$payment['examinee_id']]);
$examinee = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$examinee) {
    echo json_encode([
        "status_class" => "invalid",
        "status_message" => "Record Not Found",
        "debug" => "Invalid examinee_id: " . $payment['examinee_id']
    ]);
    exit;
}

$status = strtolower($examinee['examinee_status']);

if ($status === 'completed') {
    echo json_encode([
        "status_class" => "already_used",
        "status_message" => "Already Completed",
        "examinee_id" => $examinee['examinee_id']
    ]);
    exit;
}

if ($status === 'rejected') {
    echo json_encode([
        "status_class" => "rejected",
        "status_message" => "Entry Rejected",
        "examinee_id" => $examinee['examinee_id']
    ]);
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$payment['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode([
        "status_class" => "invalid",
        "status_message" => "User Not Found",
        "debug" => "Invalid user_id: " . $payment['user_id']
    ]);
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM schedules WHERE schedule_id = ?");
$stmt->execute([$examinee['schedule_id']]);
$schedule = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$schedule) {
    echo json_encode([
        "status_class" => "invalid",
        "status_message" => "Schedule Not Found",
        "debug" => "Invalid schedule_id: " . $examinee['schedule_id']
    ]);
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM venue WHERE venue_id = ?");
$stmt->execute([$schedule['venue_id']]);
$venue = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$venue) {
    echo json_encode([
        "status_class" => "invalid",
        "status_message" => "Venue Not Found",
        "debug" => "Invalid venue_id: " . $schedule['venue_id']
    ]);
    exit;
}


$scannedTime = date("Y-m-d H:i:s");



$fullName = $user['first_name'] . " " . $user['middle_name'] . ". " . $user['last_name'];

echo json_encode([
    "status_class" => "valid",
    "status_message" => "Verified - Proceed",

    "name" => $fullName,
    "test_permit" => $user['test_permit'],

    "exam_date" => $schedule['scheduled_date'],
    "exam_date_display" => date("F d, Y", strtotime($schedule['scheduled_date'])),

    "venue" => $venue['venue_name'],
    "examinee_id" => $examinee['examinee_id'],
    "invoice_no" => $payment['external_id'],
    "payment_status" => $payment['status'],
    "payment_date" => $payment['payment_date'],
    "amount" => "₱" . number_format($payment['amount'], 2),

    "scanned_at" => $scannedTime
]);
