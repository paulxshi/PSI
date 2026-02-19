<?php
/**
 * Rate Limiter Class
 * Prevents brute force attacks by limiting the number of attempts
 */
class RateLimiter {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->createTableIfNotExists();
    }
    
    /**
     * Create rate_limits table if it doesn't exist
     */
    private function createTableIfNotExists() {
        $sql = "CREATE TABLE IF NOT EXISTS rate_limits (
            id INT AUTO_INCREMENT PRIMARY KEY,
            action_type VARCHAR(50) NOT NULL,
            identifier VARCHAR(100) NOT NULL,
            attempts INT DEFAULT 0,
            first_attempt DATETIME NOT NULL,
            last_attempt DATETIME NOT NULL,
            blocked_until DATETIME DEFAULT NULL,
            INDEX idx_action_identifier (action_type, identifier),
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
        // Clean up old records first
        $this->cleanup();
        
        $stmt = $this->pdo->prepare("
            SELECT attempts, first_attempt, blocked_until
            FROM rate_limits
            WHERE action_type = ? AND identifier = ?
            LIMIT 1
        ");
        $stmt->execute([$actionType, $identifier]);
        $record = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $now = new DateTime();
        
        // Check if currently blocked
        if ($record && $record['blocked_until']) {
            $blockedUntil = new DateTime($record['blocked_until']);
            if ($now < $blockedUntil) {
                return false; // Still blocked
            } else {
                // Block expired, reset
                $this->resetLimit($actionType, $identifier);
                return true;
            }
        }
        
        if (!$record) {
            // First attempt - allow and record
            $this->recordAttempt($actionType, $identifier, true);
            return true;
        }
        
        $firstAttempt = new DateTime($record['first_attempt']);
        $timeElapsed = $now->getTimestamp() - $firstAttempt->getTimestamp();
        
        if ($timeElapsed > $timeWindow) {
            // Time window expired, reset counter and allow
            $this->resetLimit($actionType, $identifier);
            $this->recordAttempt($actionType, $identifier, true);
            return true;
        }
        
        // Within time window - check attempt count
        if ($record['attempts'] >= $maxAttempts) {
            // Limit exceeded, block for the remaining time window
            $blockDuration = $timeWindow - $timeElapsed;
            $this->blockIdentifier($actionType, $identifier, max(60, $blockDuration));
            return false;
        }
        
        // Increment attempt counter and allow
        $this->recordAttempt($actionType, $identifier, false);
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
     * Record an attempt
     */
    private function recordAttempt($actionType, $identifier, $isFirst = false) {
        if ($isFirst) {
            $sql = "INSERT INTO rate_limits (action_type, identifier, attempts, first_attempt, last_attempt)
                    VALUES (?, ?, 1, NOW(), NOW())";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$actionType, $identifier]);
        } else {
            $sql = "UPDATE rate_limits 
                    SET attempts = attempts + 1, last_attempt = NOW()
                    WHERE action_type = ? AND identifier = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$actionType, $identifier]);
        }
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
     * Reset limit for identifier
     */
    private function resetLimit($actionType, $identifier) {
        $sql = "DELETE FROM rate_limits WHERE action_type = ? AND identifier = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$actionType, $identifier]);
    }
    
    /**
     * Clean up old records (older than 1 hour)
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
