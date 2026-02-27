<?php
class RateLimiter {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->createTableIfNotExists();
    }
    
    private function createTableIfNotExists() {
        $sql = "CREATE TABLE IF NOT EXISTS rate_limits (
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
        
        try {
            $this->pdo->exec($sql);
        } catch (PDOException $e) {
            error_log("Rate limiter table creation error: " . $e->getMessage());
        }
    }
    
    /**
     * Check if action is within rate limit
     * @param string $actionType Type of action (e.g., 'login', 'registration')
     * @param string $identifier IP address or user identifier
     * @param int $maxAttempts Maximum attempts allowed (default: 5)
     * @param int $timeWindow Time window in seconds (default: 60 for 1 minute)
     * @return bool True if within limit, false if exceeded
     */
    public function checkLimit($actionType, $identifier, $maxAttempts = 5, $timeWindow = 60) {
        // Clean up expired blocks first
        $this->cleanup();
        
        // First, check if currently blocked (priority check)
        $blockStmt = $this->pdo->prepare("
            SELECT blocked_until
            FROM rate_limits
            WHERE action_type = ? AND identifier = ? AND blocked_until IS NOT NULL
            LIMIT 1
        ");
        $blockStmt->execute([$actionType, $identifier]);
        $blockRecord = $blockStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($blockRecord && $blockRecord['blocked_until']) {
            $blockedUntil = new DateTime($blockRecord['blocked_until']);
            $now = new DateTime();
            if ($now < $blockedUntil) {
                return false; // Still blocked
            } else {
                // Block expired, reset for fresh start
                $this->resetLimit($actionType, $identifier);
            }
        }
        
        // Get or create attempt record
        $stmt = $this->pdo->prepare("
            SELECT id, attempts, first_attempt
            FROM rate_limits
            WHERE action_type = ? AND identifier = ?
            LIMIT 1
        ");
        $stmt->execute([$actionType, $identifier]);
        $record = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $now = new DateTime();
        
        if (!$record) {
            // First attempt - record and allow
            $this->recordAttempt($actionType, $identifier, true);
            return true;
        }
        
        $firstAttempt = new DateTime($record['first_attempt']);
        $timeElapsed = $now->getTimestamp() - $firstAttempt->getTimestamp();
        
        // Check if time window has expired
        if ($timeElapsed > $timeWindow) {
            // Time window expired, reset and start fresh
            $this->resetLimit($actionType, $identifier);
            $this->recordAttempt($actionType, $identifier, true);
            return true;
        }
        
        // Within time window - increment attempt first, then check
        $newAttempts = $record['attempts'] + 1;
        
        // Update attempts count
        $updateStmt = $this->pdo->prepare("
            UPDATE rate_limits 
            SET attempts = ?, last_attempt = NOW()
            WHERE action_type = ? AND identifier = ?
        ");
        $updateStmt->execute([$newAttempts, $actionType, $identifier]);
        
        // Check if this attempt exceeds the limit
        if ($newAttempts >= $maxAttempts) {
            // Block for the full time window duration
            $this->blockIdentifier($actionType, $identifier, $timeWindow);
            return false;
        }
        
        return true;
    }
    
    /**
     * Get remaining attempts before rate limit
     * @param string $actionType Type of action
     * @param string $identifier IP address or user identifier
     * @param int $maxAttempts Maximum attempts allowed
     * @return int Number of remaining attempts
     */
    public function getRemainingAttempts($actionType, $identifier, $maxAttempts = 5) {
        $stmt = $this->pdo->prepare("
            SELECT attempts 
            FROM rate_limits
            WHERE action_type = ? AND identifier = ?
            LIMIT 1
        ");
        $stmt->execute([$actionType, $identifier]);
        $record = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$record) {
            return $maxAttempts;
        }
        
        return max(0, $maxAttempts - $record['attempts']);
    }
    
    /**
     * Get time until unblocked
     * @param string $actionType Type of action
     * @param string $identifier IP address or user identifier
     * @return int Seconds until unblocked, 0 if not blocked
     */
    public function getTimeUntilUnblocked($actionType, $identifier) {
        $stmt = $this->pdo->prepare("
            SELECT blocked_until 
            FROM rate_limits
            WHERE action_type = ? AND identifier = ?
            LIMIT 1
        ");
        $stmt->execute([$actionType, $identifier]);
        $record = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$record || !$record['blocked_until']) {
            return 0;
        }
        
        $blockedUntil = new DateTime($record['blocked_until']);
        $now = new DateTime();
        
        if ($now >= $blockedUntil) {
            return 0;
        }
        
        return $blockedUntil->getTimestamp() - $now->getTimestamp();
    }
    
    /**
     * Record an attempt (using INSERT ... ON DUPLICATE KEY UPDATE for atomicity)
     */
    private function recordAttempt($actionType, $identifier, $isFirst = false) {
        $sql = "INSERT INTO rate_limits (action_type, identifier, attempts, first_attempt, last_attempt)
                VALUES (?, ?, 1, NOW(), NOW())
                ON DUPLICATE KEY UPDATE 
                    attempts = IF(?, 1, attempts + 1),
                    first_attempt = IF(?, NOW(), first_attempt),
                    last_attempt = NOW(),
                    blocked_until = IF(?, NULL, blocked_until)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$actionType, $identifier, $isFirst, $isFirst, $isFirst]);
    }
    
    /**
     * Block identifier for specified duration
     */
    private function blockIdentifier($actionType, $identifier, $blockDuration) {
        $blockedUntil = date('Y-m-d H:i:s', time() + $blockDuration);
        $sql = "UPDATE rate_limits 
                SET blocked_until = ?
                WHERE action_type = ? AND identifier = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$blockedUntil, $actionType, $identifier]);
    }
    
    /**
     * Reset limit for identifier (public method for use after successful login)
     */
    public function resetLimit($actionType, $identifier) {
        $sql = "DELETE FROM rate_limits WHERE action_type = ? AND identifier = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$actionType, $identifier]);
    }
    
    /**
     * Clean up old records (older than 1 hour) and expired blocks
     */
    private function cleanup() {
        $sql = "DELETE FROM rate_limits 
                WHERE (blocked_until IS NULL AND first_attempt < DATE_SUB(NOW(), INTERVAL 1 HOUR))
                OR (blocked_until IS NOT NULL AND blocked_until < NOW())";
        try {
            $this->pdo->exec($sql);
        } catch (PDOException $e) {
            error_log("Rate limiter cleanup error: " . $e->getMessage());
        }
    }
}
