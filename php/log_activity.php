<?php
// Log Activity Function
// This function logs system activities to the activity_logs table

function logActivity($activityType, $description = null, $userId = null, $username = null, $email = null, $role = null, $severity = 'info', $metadata = null, $pdoConnection = null) {
    try {
        // Use provided connection or get global one
        if ($pdoConnection) {
            $pdo = $pdoConnection;
        } else {
            require_once __DIR__ . '/../config/db.php';
            global $pdo; // Access the global $pdo variable
            
            // Check if $pdo is available
            if (!isset($pdo) || !$pdo) {
                error_log('Activity logging failed: Database connection not available');
                return false;
            }
        }
        
        // Get IP address
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        
        // Get user agent
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;
        if ($userAgent && strlen($userAgent) > 255) {
            $userAgent = substr($userAgent, 0, 255);
        }
        
        // Convert metadata array to JSON if provided
        if (is_array($metadata)) {
            $metadata = json_encode($metadata);
        }
        
        // Insert activity log
        $stmt = $pdo->prepare("
            INSERT INTO activity_logs 
            (user_id, username, email, activity_type, description, ip_address, user_agent, role, severity, metadata, created_at) 
            VALUES 
            (:user_id, :username, :email, :activity_type, :description, :ip_address, :user_agent, :role, :severity, :metadata, NOW())
        ");
        
        $stmt->execute([
            ':user_id' => $userId,
            ':username' => $username,
            ':email' => $email,
            ':activity_type' => $activityType,
            ':description' => $description,
            ':ip_address' => $ipAddress,
            ':user_agent' => $userAgent,
            ':role' => $role,
            ':severity' => $severity,
            ':metadata' => $metadata
        ]);
        
        return true;
    } catch (PDOException $e) {
        // Log error but don't fail the main operation
        error_log('Activity logging failed (PDO): ' . $e->getMessage());
        return false;
    } catch (Exception $e) {
        // Catch any other exceptions
        error_log('Activity logging failed (Exception): ' . $e->getMessage());
        return false;
    }
}

// If called directly (not as a function include)
if (basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
    header('Content-Type: application/json');
    
    // Only allow POST requests
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        exit;
    }
    
    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['activity_type'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Activity type is required']);
        exit;
    }
    
    $result = logActivity(
        $data['activity_type'],
        $data['description'] ?? null,
        $data['user_id'] ?? null,
        $data['username'] ?? null,
        $data['email'] ?? null,
        $data['role'] ?? null
    );
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Activity logged']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to log activity']);
    }
}
?>
