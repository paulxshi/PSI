<?php
session_start();
header("Content-Type: application/json");

error_log("get_user.php called - Session ID: " . session_id());
error_log("Session user_id: " . ($_SESSION['user_id'] ?? 'NOT SET'));

require_once "../../config/db.php";

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
        SELECT u.user_id, u.first_name, u.middle_name, u.last_name, u.email, u.contact_number, 
               u.date_of_birth, u.age, u.test_permit, u.role, u.status, u.school,
               u.gender, u.address, u.nationality, u.profile_picture, u.last_profile_update,
               u.profile_upload_attempts,
               u.date_of_registration,
               s.scheduled_date as exam_date, v.venue_name as exam_venue, v.region
        FROM users u
        LEFT JOIN examinees e ON u.user_id = e.user_id
        LEFT JOIN schedules s ON e.schedule_id = s.schedule_id
        LEFT JOIN venue v ON s.venue_id = v.venue_id
        WHERE u.user_id = ?
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

    // Check 3-attempt restriction for profile picture upload
    $upload_attempts = isset($user['profile_upload_attempts']) ? (int)$user['profile_upload_attempts'] : 0;
    $can_upload_picture = $upload_attempts < 3;
    $attempts_remaining = 3 - $upload_attempts;
    
    $user['can_upload_picture'] = $can_upload_picture;
    $user['upload_attempts_used'] = $upload_attempts;
    $user['upload_attempts_remaining'] = $attempts_remaining;

    echo json_encode(["success" => true, "user" => $user]);

} catch (PDOException $e) {
    error_log("PDO Error in get_user.php: " . $e->getMessage());
    echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
} catch (Exception $e) {
    error_log("Error in get_user.php: " . $e->getMessage());
    echo json_encode(["success" => false, "message" => "Error loading user data: " . $e->getMessage()]);
}

