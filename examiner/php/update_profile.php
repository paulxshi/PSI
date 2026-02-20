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

    // Get POST data
    $data = json_decode(file_get_contents("php://input"), true);

    if (!$data) {
        echo json_encode(["success" => false, "message" => "Invalid data"]);
        exit;
    }

    // Check if user can edit (last_profile_update restriction - once per week)
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
                "message" => "You can edit your profile again in $days_left day(s)",
                "can_edit" => false,
                "days_remaining" => $days_left
            ]);
            exit;
        }
    }

    // Validate and prepare updatable fields
    $allowed_fields = ['first_name', 'middle_name', 'last_name', 'contact_number', 'date_of_birth', 'school'];
    $update_fields = [];
    $update_values = [];

    foreach ($allowed_fields as $field) {
        if (isset($data[$field])) {
            $update_fields[] = "$field = ?";
            $update_values[] = $data[$field];
        }
    }

    // Always set nationality to Filipino by default
    $update_fields[] = "nationality = ?";
    $update_values[] = "Filipino";

    if (empty($update_fields)) {
        echo json_encode(["success" => false, "message" => "No valid fields to update"]);
        exit;
    }

    // Calculate age if date_of_birth is provided
    if (isset($data['date_of_birth'])) {
        $dob = new DateTime($data['date_of_birth']);
        $now = new DateTime();
        $age = $now->diff($dob)->y;
        $update_fields[] = "age = ?";
        $update_values[] = $age;
    }

    // Add last_profile_update timestamp
    $update_fields[] = "last_profile_update = NOW()";
    $update_values[] = $user_id;

    // Build and execute update query
    $sql = "UPDATE users SET " . implode(", ", $update_fields) . " WHERE user_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($update_values);

    // Log activity
    $stmt = $pdo->prepare("
        INSERT INTO activity_logs (user_id, username, email, activity_type, description, ip_address, user_agent, role, severity)
        SELECT 
            user_id,
            CONCAT(first_name, ' ', last_name),
            email,
            'admin_examinee_updated',
            'Examinee updated their profile information',
            ?,
            ?,
            'examinee',
            'info'
        FROM users WHERE user_id = ?
    ");
    $stmt->execute([$_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT'], $user_id]);

    echo json_encode([
        "success" => true,
        "message" => "Profile updated successfully. You can edit again in 7 days.",
        "can_edit_again_at" => date('Y-m-d H:i:s', strtotime('+7 days'))
    ]);

} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    echo json_encode(["success" => false, "message" => "Database error"]);
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    echo json_encode(["success" => false, "message" => "Error updating profile"]);
}
