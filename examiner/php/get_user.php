<?php

session_start();
header("Content-Type: application/json");
require_once "../../config/db.php";

try {
    // Allow fetching by user_id in GET for examiner use
    $user_id = null;
    if (isset($_GET['user_id']) && is_numeric($_GET['user_id'])) {
        $user_id = $_GET['user_id'];
        error_log("Fetching user data for user_id from GET: $user_id");
    } else if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        error_log("Fetching user data for user_id from SESSION: $user_id");
        // Check if registration is incomplete
        if (isset($_SESSION['incomplete_registration']) && $_SESSION['incomplete_registration'] === true) {
            error_log("User has incomplete registration");
            echo json_encode([
                "success" => false, 
                "message" => "Please complete your registration and payment first.",
                "redirect" => "../auth/login.html"
            ]);
            exit;
        }
    }

    if (!$user_id) {
        error_log("No user_id provided");
        echo json_encode(["success" => false, "message" => "No user_id provided"]);
        exit;
    }

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

    // Fetch selected meals for this user
    $mealsStmt = $pdo->prepare("
        SELECT m.meal_id, m.name AS meal_name, m.price AS meal_price
        FROM examinee_meals em
        JOIN meals m ON em.meal_id = m.meal_id
        WHERE em.user_id = ?
        ORDER BY em.selected_at ASC
    ");
    $mealsStmt->execute([$user_id]);
    $user['meals'] = $mealsStmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(["success" => true, "user" => $user]);

} catch (PDOException $e) {
    error_log("PDO Error in get_user.php: " . $e->getMessage());
    echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
} catch (Exception $e) {
    error_log("Error in get_user.php: " . $e->getMessage());
    echo json_encode(["success" => false, "message" => "Error loading user data: " . $e->getMessage()]);
}

