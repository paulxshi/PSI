<?php
require "../../config/db.php";

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['transaction_no'])) {
    echo json_encode([
        "status_class" => "invalid",
        "status_message" => "No transaction number received."
    ]);
    exit;
}

$transactionNo = $data['transaction_no'];

/* --------------------------------------------------
   STEP 1: Check if payment exists
-------------------------------------------------- */
$stmt = $pdo->prepare("SELECT * FROM payments WHERE transaction_no = ?");
$stmt->execute([$transactionNo]);
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
   SUCCESS
-------------------------------------------------- */
$fullName = $user['first_name'] . " " . $user['middle_name'] . ". " . $user['last_name'];

echo json_encode([
    "status_class" => "valid",
    "status_message" => "✔ VALID – ALLOWED TO ENTER",

    "name" => $fullName,
    "test_permit" => $user['test_permit'],
    "exam_date" => date("F d, Y", strtotime($schedule['schedule_datetime'])),
    "venue" => $venue['venue_name'],
    "examinee_id" => $examinee['examinee_id'],
    "transaction_no" => $payment['transaction_no'],
    "payment_status" => $payment['status'],
    "payment_date" => $payment['payment_date'],
    "amount" => "₱" . number_format($payment['payment_amount'], 2),

    "debug" => "All joins successful"
]);
