<?php
require "../../config/db.php";


/* --------------------------------------------
   Prevent PHP warnings from breaking JSON
--------------------------------------------- */
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

/* --------------------------------------------
   Read JSON input from fetch()
--------------------------------------------- */
$data = json_decode(file_get_contents("php://input"), true);

/* ✅ PLACE THE CHECK RIGHT HERE */
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode([
        "status_class" => "invalid",
        "status_message" => "Invalid JSON received."
    ]);
    exit;
}

/* --------------------------------------------
   Now validate expected field
--------------------------------------------- */
if (!isset($data['external_id'])) {
    echo json_encode([
        "status_class" => "invalid",
        "status_message" => "No transaction number received."
    ]);
    exit;
}

$invoiceNo = $data['external_id'];
/* --------------------------------------------------
   STEP 1: Check if payment exists
-------------------------------------------------- */
$stmt = $pdo->prepare("SELECT * FROM payments WHERE external_id = ?");
$stmt->execute([$invoiceNo]);
$payment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$payment) {
    echo json_encode([
        "status_class" => "invalid",
        "status_message" => "Payment not found.",
        "debug" => "No record in payments table"
    ]);
    exit;
}

/* --------------------------------------------------
   STEP 2: Check examinee
-------------------------------------------------- */
$stmt = $pdo->prepare("SELECT * FROM examinees WHERE examinee_id = ?");
$stmt->execute([$payment['examinee_id']]);
$examinee = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$examinee) {
    echo json_encode([
        "status_class" => "invalid",
        "status_message" => "Examinee record missing.",
        "debug" => "Invalid examinee_id: " . $payment['examinee_id']
    ]);
    exit;
}

/* --------------------------------------------------
   STEP 2.1: Check if already completed
-------------------------------------------------- */
$status = strtolower($examinee['examinee_status']);

if ($status === 'completed') {
    echo json_encode([
        "status_class" => "already_used",
        "status_message" => "⚠ EXAMINEE ALREADY MARKED COMPLETED",
        "examinee_id" => $examinee['examinee_id']
    ]);
    exit;
}

if ($status === 'rejected') {
    echo json_encode([
        "status_class" => "rejected",
        "status_message" => "❌ EXAMINEE ENTRY WAS REJECTED",
        "examinee_id" => $examinee['examinee_id']
    ]);
    exit;
}

/* --------------------------------------------------
   STEP 3: Check user
-------------------------------------------------- */
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$payment['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode([
        "status_class" => "invalid",
        "status_message" => "User record missing.",
        "debug" => "Invalid user_id: " . $payment['user_id']
    ]);
    exit;
}

/* --------------------------------------------------
   STEP 4: Check schedule
-------------------------------------------------- */
$stmt = $pdo->prepare("SELECT * FROM schedules WHERE schedule_id = ?");
$stmt->execute([$examinee['schedule_id']]);
$schedule = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$schedule) {
    echo json_encode([
        "status_class" => "invalid",
        "status_message" => "Schedule not found.",
        "debug" => "Invalid schedule_id: " . $examinee['schedule_id']
    ]);
    exit;
}

/* --------------------------------------------------
   STEP 5: Check venue
-------------------------------------------------- */
$stmt = $pdo->prepare("SELECT * FROM venue WHERE venue_id = ?");
$stmt->execute([$schedule['venue_id']]);
$venue = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$venue) {
    echo json_encode([
        "status_class" => "invalid",
        "status_message" => "Venue missing.",
        "debug" => "Invalid venue_id: " . $schedule['venue_id']
    ]);
    exit;
}


/* --------------------------------------------------
   STEP 6: Update scanned_at timestamp
-------------------------------------------------- */

// Double-check if already scanned (extra safety)
/*
if (!empty($examinee['scanned_at'])) {
    echo json_encode([
        "status_class" => "already_used",
        "status_message" => "⚠ QR ALREADY SCANNED",
        "scanned_at" => $examinee['scanned_at'],
        "examinee_id" => $examinee['examinee_id']
    ]);
    exit;
} */

// Get updated timestamp
$scannedTime = date("Y-m-d H:i:s");



/* --------------------------------------------------
   SUCCESS
-------------------------------------------------- */
$fullName = $user['first_name'] . " " . $user['middle_name'] . ". " . $user['last_name'];

echo json_encode([
    "status_class" => "valid",
    "status_message" => "✔ VALID – ALLOWED TO ENTER",

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

    "scanned_at" => $scannedTime,

    "debug" => "Scan recorded successfully"
]);
