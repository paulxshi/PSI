<?php
session_start();
header("Content-Type: application/json");
require_once "../../config/db.php";

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "User not logged in"]);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("
        SELECT 
            u.user_id,
            u.first_name,
            u.middle_name,
            u.last_name,
            u.date_of_birth,
            u.age,
            u.gender,
            u.nationality,
            u.contact_number,
            u.email,
            
            e.user_id,
            e.test_permit,
            e.date_of_test,
            e.date_of_registration,
            e.schedule_id,
            e.status,
            e.purpose,

            p.transaction_no,
            p.payment_date,
            p.payment_amount

        FROM users u
        LEFT JOIN examinees e ON u.user_id = e.user_id
        LEFT JOIN payments p ON e.user_id = p.user_id
        WHERE u.user_id = ?
        ORDER BY p.payment_date DESC
        LIMIT 1
    ");

    $stmt->execute([$user_id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        echo json_encode(["success" => false, "message" => "No record found"]);
        exit;
    }

    echo json_encode(["success" => true, "data" => $data]);

} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
