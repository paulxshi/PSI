<?php
require_once "../../config/db.php"; // your PDO connection

if (!isset($_GET['user_id'])) {
    error_log("get_transaction.php - No user_id in GET");
    echo json_encode(["status" => "error", "message" => "Missing user ID"]);
    exit;
}

$user_id = $_GET['user_id'];
error_log("get_transaction.php - User ID: " . $user_id);

try {
    // Get the latest successful payment of the user
    // Use COALESCE to handle NULL paid_at values
    $stmt = $pdo->prepare("
        SELECT external_id
        FROM payments
        WHERE user_id = :user_id
          AND status = 'paid'
        ORDER BY 
            COALESCE(paid_at, payment_date, created_at) DESC
        LIMIT 1
    ");

    $stmt->execute(['user_id' => $user_id]);
    $payment = $stmt->fetch(PDO::FETCH_ASSOC);

    error_log("get_transaction.php - Payment found: " . ($payment ? 'YES' : 'NO'));
    if ($payment) {
        error_log("get_transaction.php - External ID: " . $payment['external_id']);
        echo json_encode([
            "status" => "success",
            "external_id" => $payment['external_id']
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "No payment found"
        ]);
    }

} catch (PDOException $e) {
    error_log("get_transaction.php - PDO Error: " . $e->getMessage());
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
?>
