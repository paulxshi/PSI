<?php
session_start();
header("Content-Type: application/json");

error_log("get_user.php called - Session ID: " . session_id());
error_log("Session user_id: " . ($_SESSION['user_id'] ?? 'NOT SET'));

require_once "../config/db.php";

try {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        error_log("No user_id in session");
        echo json_encode(["success" => false, "message" => "Not logged in"]);
        exit;
    }

    $user_id = $_SESSION['user_id'];
    error_log("Fetching user data for user_id: $user_id");

    // Get user data directly from users table
    $stmt = $pdo->prepare("
        SELECT user_id, first_name, middle_name, last_name, email, contact_number, 
               date_of_birth, age, test_permit, role, status, school, region, 
               gender, address, nationality, exam_venue, exam_date, pmma_student_id,
               date_of_registration
        FROM users
        WHERE user_id = ?
        LIMIT 1
    ");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        error_log("User not found in database for user_id: $user_id");
        echo json_encode(["success" => false, "message" => "User not found"]);
        exit;
    }

    error_log("User found: " . json_encode($user));

    // Set default values for fields that don't exist in this table
    $user['purpose'] = null;
    $user['date_of_test'] = $user['exam_date'] ?? null;

    echo json_encode(["success" => true, "user" => $user]);

} catch (PDOException $e) {
    error_log("PDO Error in get_user.php: " . $e->getMessage());
    echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
} catch (Exception $e) {
    error_log("Error in get_user.php: " . $e->getMessage());
    echo json_encode(["success" => false, "message" => "Error loading user data: " . $e->getMessage()]);
}

