<?php
require "../../config/db.php";

ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Get raw input
$rawInput = file_get_contents("php://input");
$data = json_decode($rawInput, true);

// Validate JSON
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode([
        "status_class" => "invalid",
        "status_message" => "Invalid Request"
    ]);
    exit;
}

// Validate input
if (empty($data['external_id'])) {
    echo json_encode([
        "status_class" => "invalid",
        "status_message" => "Missing Transaction Number"
    ]);
    exit;
}

// ===============================
// QR Extraction
// ===============================
$qrValue = trim($data['external_id']);

// Handle TAB interpreted as Enter by scanner
$lines = preg_split('/\r?\n/', $qrValue);
$firstLine = trim($lines[0]); // Take only the first line

// Extract transaction number (last word of first line)
$parts = preg_split('/\s+/', $firstLine);
$invoiceNo = end($parts);

if (!$invoiceNo) {
    echo json_encode([
        "status_class" => "invalid",
        "status_message" => "Invalid QR Format"
    ]);
    exit;
}

// ===============================
// Payment Query
// ===============================
$stmt = $pdo->prepare("SELECT * FROM payments WHERE external_id = ?");
$stmt->execute([$invoiceNo]);
$payment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$payment) {
    echo json_encode([
        "status_class" => "invalid",
        "status_message" => "Payment Not Found"
    ]);
    exit;
}

if ($payment['status'] !== 'PAID') {
    echo json_encode([
        "status_class" => "invalid",
        "status_message" => "Payment Pending"
    ]);
    exit;
}

// ===============================
// Examinee
// ===============================
$stmt = $pdo->prepare("SELECT * FROM examinees WHERE examinee_id = ?");
$stmt->execute([$payment['examinee_id']]);
$examinee = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$examinee) {
    echo json_encode([
        "status_class" => "invalid",
        "status_message" => "Record Not Found"
    ]);
    exit;
}

$status = strtolower(trim($examinee['examinee_status']));
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

// ===============================
// User
// ===============================
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$payment['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode([
        "status_class" => "invalid",
        "status_message" => "User Not Found"
    ]);
    exit;
}

// ===============================
// Schedule
// ===============================
$stmt = $pdo->prepare("SELECT * FROM schedules WHERE schedule_id = ?");
$stmt->execute([$examinee['schedule_id']]);
$schedule = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$schedule) {
    echo json_encode([
        "status_class" => "invalid",
        "status_message" => "Schedule Not Found"
    ]);
    exit;
}

// ===============================
// Venue
// ===============================
$stmt = $pdo->prepare("SELECT * FROM venue WHERE venue_id = ?");
$stmt->execute([$schedule['venue_id']]);
$venue = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$venue) {
    echo json_encode([
        "status_class" => "invalid",
        "status_message" => "Venue Not Found"
    ]);
    exit;
}

// ===============================
// PH Timezone
// ===============================
date_default_timezone_set('Asia/Manila');
$scannedTime = date("Y-m-d H:i:s");

// ===============================
// Name formatting 
// ===============================
$middleInitial = !empty($user['middle_name']) ? strtoupper($user['middle_name'][0]) . "." : "";
$fullName = trim("{$user['first_name']} {$middleInitial} {$user['last_name']}");


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