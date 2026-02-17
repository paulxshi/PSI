<?php
require_once "../../config/db.php"; // your PDO connection

if (!isset($_GET['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Missing user ID"]);
    exit;
}

$user_id = $_GET['user_id'];

try {
    // Get the latest successful payment of the user
    $stmt = $pdo->prepare("
        SELECT transaction_no
        FROM payments
        WHERE user_id = :user_id
        ORDER BY payment_date DESC
        LIMIT 1
    ");

    $stmt->execute(['user_id' => $user_id]);
    $payment = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($payment) {
        echo json_encode([
            "status" => "success",
            "transaction_no" => $payment['transaction_no']
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "No payment found"
        ]);
    }

} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
?>
