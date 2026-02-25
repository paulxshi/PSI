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

    // Check if user has reached the 3-attempt limit
    $stmt = $pdo->prepare("SELECT profile_upload_attempts FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $upload_attempts = $stmt->fetchColumn();

    // Maximum 3 attempts allowed
    if ($upload_attempts >= 3) {
        echo json_encode([
            "success" => false,
            "message" => "You have reached the maximum limit of 3 profile picture uploads.",
            "can_upload" => false,
            "attempts_used" => $upload_attempts,
            "attempts_remaining" => 0
        ]);
        exit;
    }

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

    // Update database with new picture, timestamp, and increment attempts
    $relative_path = "uploads/profile_pictures/" . $filename;
    $stmt = $pdo->prepare("
        UPDATE users 
        SET profile_picture = ?, 
            last_profile_update = NOW(),
            profile_upload_attempts = profile_upload_attempts + 1
        WHERE user_id = ?
    ");
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

    // Get updated attempts count
    $stmt = $pdo->prepare("SELECT profile_upload_attempts FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $current_attempts = $stmt->fetchColumn();

    echo json_encode([
        "success" => true,
        "message" => "Profile picture updated successfully.",
        "profile_picture" => $relative_path,
        "attempts_used" => $current_attempts,
        "attempts_remaining" => 3 - $current_attempts
    ]);

} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    echo json_encode(["success" => false, "message" => "Database error"]);
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    echo json_encode(["success" => false, "message" => "Error uploading profile picture"]);
}
