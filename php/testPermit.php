<?php
session_start();
header("Content-Type: application/json");
require_once "../config/db.php";

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
            
            t.test_id,
            t.test_permit,
            t.date_of_test,
            t.date_of_registration,
            t.venue,
            t.status,
            t.purpose,

            p.transaction_no,
            p.payment_date,
            p.payment_amount

        FROM users u
        LEFT JOIN examinees t ON u.user_id = t.user_id
        LEFT JOIN payments p ON t.test_id = p.test_id
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
