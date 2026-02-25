<?php
session_start();
header("Content-Type: application/json");

require_once "../../config/db.php";

try {
    // Get user_id from GET or SESSION
    $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null);

    error_log("get_payment_details.php - User ID: " . ($user_id ?? 'NULL'));

    if (!$user_id) {
        error_log("get_payment_details.php - No user ID provided");
        echo json_encode(["success" => false, "message" => "User ID not provided"]);
        exit;
    }

     // Get the latest successful payment of the user
    // Use COALESCE to handle NULL paid_at values, fallback to created_at or payment_date
    $stmt = $pdo->prepare("
        SELECT 
            payment_id,
            external_id,
            amount,
            status,
            paid_at,
            payment_date,
            created_at,
            xendit_invoice_id,
            channel
        FROM payments
        WHERE user_id = ?
          AND status = 'paid'
        ORDER BY 
            COALESCE(paid_at, payment_date, created_at) DESC
        LIMIT 1
    ");
    
    $stmt->execute([$user_id]);
    $payment = $stmt->fetch(PDO::FETCH_ASSOC);

    error_log("get_payment_details.php - Payment found: " . ($payment ? 'YES' : 'NO'));
    if ($payment) {
        error_log("get_payment_details.php - Payment data: " . json_encode($payment));
    }

    if ($payment) {
        echo json_encode([
            "success" => true,
            "payment" => $payment
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "No payment found for this user"
        ]);
    }

} catch (PDOException $e) {
    error_log("get_payment_details.php - PDO Error: " . $e->getMessage());
    error_log("get_payment_details.php - Error Code: " . $e->getCode());
    error_log("get_payment_details.php - Error Info: " . print_r($e->errorInfo, true));
    echo json_encode([
        "success" => false,
        "message" => "Database error",
        "error_detail" => $e->getMessage() // Include detail for debugging
    ]);
} catch (Exception $e) {
    error_log("get_payment_details.php - General Error: " . $e->getMessage());
    echo json_encode([
        "success" => false,
        "message" => "Error retrieving payment details",
        "error_detail" => $e->getMessage() // Include detail for debugging
    ]);
}
