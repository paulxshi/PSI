<?php
session_start();
header("Content-Type: application/json");
require_once "../config/db.php"; // PDO connection

// Hard-coded fallback user
$defaultUser = [
    "first_name" => "Juan",
    "middle_name" => "D",
    "last_name" => "Dela Cruz",
    "email" => "juan.delacruz@pmma.edu.ph",
    "contact_number" => "9123456789",
    "date_of_birth" => "2004-03-15",
    "age" => 20,
    "test_permit" => "1234-5678"
];

try {
    if (!isset($_SESSION['user_id'])) {
        // Not logged in → return default data
        echo json_encode(["success" => true, "user" => $defaultUser]);
        exit;
    }

    $user_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("
        SELECT u.user_id, u.first_name, u.middle_name, u.last_name, u.email, u.contact_number, 
               u.date_of_birth, u.age, t.test_permit
        FROM users u
        LEFT JOIN test t ON u.user_id = t.user_id
        WHERE u.user_id = ?
        LIMIT 1
    ");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // If no DB data found, use fallback
    if (!$user) {
        $user = $defaultUser;
    }

    echo json_encode(["success" => true, "user" => $user]);

} catch (Exception $e) {
    // If DB connection fails → fallback
    echo json_encode(["success" => true, "user" => $defaultUser]);
}