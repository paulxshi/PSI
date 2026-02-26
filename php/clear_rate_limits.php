<?php
/**
 * Clear Rate Limits - Debug Script
 * Clears all rate limit records and resets user lockouts
 */

require_once "../config/db.php";

try {
    // Drop and recreate rate_limits table with UNIQUE constraint
    $pdo->exec("DROP TABLE IF EXISTS rate_limits");
    
    $sql = "CREATE TABLE rate_limits (
        id INT AUTO_INCREMENT PRIMARY KEY,
        action_type VARCHAR(50) NOT NULL,
        identifier VARCHAR(100) NOT NULL,
        attempts INT DEFAULT 0,
        first_attempt DATETIME NOT NULL,
        last_attempt DATETIME NOT NULL,
        blocked_until DATETIME DEFAULT NULL,
        UNIQUE KEY unique_action_identifier (action_type, identifier),
        INDEX idx_blocked_until (blocked_until)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
    
    $pdo->exec($sql);
    
    // Add columns to users table if they don't exist
    $columns = [
        'last_failed_login' => "ALTER TABLE users ADD COLUMN last_failed_login DATETIME DEFAULT NULL",
        'lock_until' => "ALTER TABLE users ADD COLUMN lock_until DATETIME DEFAULT NULL"
    ];
    
    foreach ($columns as $column => $alterSql) {
        $checkStmt = $pdo->prepare("SHOW COLUMNS FROM users LIKE ?");
        $checkStmt->execute([$column]);
        if ($checkStmt->rowCount() === 0) {
            $pdo->exec($alterSql);
        }
    }
    
    // Reset all user lockouts
    $pdo->exec("UPDATE users SET failed_login_attempts = 0, last_failed_login = NULL, lock_until = NULL");
    
    echo json_encode([
        "success" => true,
        "message" => "Rate limit table rebuilt and all user lockouts cleared."
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => "Error: " . $e->getMessage()
    ]);
}
