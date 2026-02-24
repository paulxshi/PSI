<?php
session_start();
header("Content-Type: application/json");

require_once "../../config/db.php";

try {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(["success" => false, "message" => "Not logged in"]);
        exit;
    }

    $user_id = $_SESSION['user_id'];

    // 7-DAY RESTRICTION TEMPORARILY DISABLED
    /* ORIGINAL 7-DAY RESTRICTION CODE (COMMENTED OUT)
    // Check if user can upload (1-week restriction)
    $stmt = $pdo->prepare("SELECT last_profile_update FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $last_update = $stmt->fetchColumn();

    if ($last_update) {
        $last_update_time = strtotime($last_update);
        $current_time = time();
        $one_week = 7 * 24 * 60 * 60;

        if (($current_time - $last_update_time) < $one_week) {
            $days_left = ceil(($one_week - ($current_time - $last_update_time)) / (24 * 60 * 60));
            echo json_encode([
                "success" => false,
                "message" => "You can upload a new profile picture in $days_left day(s)",
                "can_upload" => false,
                "days_remaining" => $days_left
            ]);
            exit;
        }
    }
    */

    // Check if file was uploaded
    if (!isset($_FILES['profile_picture']) || $_FILES['profile_picture']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(["success" => false, "message" => "No file uploaded or upload error"]);
        exit;
    }

    $file = $_FILES['profile_picture'];
    $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
    $max_size = 5 * 1024 * 1024; // 5MB

    // Validate file type
    if (!in_array($file['type'], $allowed_types)) {
        echo json_encode(["success" => false, "message" => "Invalid file type. Only JPEG, PNG, and GIF are allowed"]);
        exit;
    }

    // Validate file size
    if ($file['size'] > $max_size) {
        echo json_encode(["success" => false, "message" => "File size too large. Maximum 5MB allowed"]);
        exit;
    }

    // Create upload directory if it doesn't exist
    $upload_dir = "../../uploads/profile_pictures/";
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = "user_" . $user_id . "_" . time() . "." . $extension;
    $filepath = $upload_dir . $filename;

    // Get old profile picture to delete if exists
    $stmt = $pdo->prepare("SELECT profile_picture FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $old_picture = $stmt->fetchColumn();

    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        echo json_encode(["success" => false, "message" => "Failed to save file"]);
        exit;
    }

    // Update database with new picture and timestamp
    $relative_path = "uploads/profile_pictures/" . $filename;
    $stmt = $pdo->prepare("UPDATE users SET profile_picture = ?, last_profile_update = NOW() WHERE user_id = ?");
    $stmt->execute([$relative_path, $user_id]);

    // Delete old profile picture if exists
    if ($old_picture && file_exists("../../" . $old_picture)) {
        unlink("../../" . $old_picture);
    }

    // Log activity
    $stmt = $pdo->prepare("
        INSERT INTO activity_logs (user_id, username, email, activity_type, description, ip_address, user_agent, role, severity)
        SELECT 
            user_id,
            CONCAT(first_name, ' ', last_name),
            email,
            'profile_picture_updated',
            'User updated their profile picture',
            ?,
            ?,
            'examinee',
            'info'
        FROM users WHERE user_id = ?
    ");
    $stmt->execute([$_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT'], $user_id]);

    echo json_encode([
        "success" => true,
        "message" => "Profile picture updated successfully.",
        "profile_picture" => $relative_path
        // Note: 7-day restriction temporarily disabled
        // "can_upload_again_at" => date('Y-m-d H:i:s', strtotime('+7 days'))
    ]);

} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    echo json_encode(["success" => false, "message" => "Database error"]);
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    echo json_encode(["success" => false, "message" => "Error uploading profile picture"]);
}
