<?php

require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/log_activity.php";

class LoginHandler {
    private $pdo;
    private $maxAttempts;
    private $lockoutDuration; // in seconds
    
    public function __construct($pdo, $maxAttempts = 5, $lockoutDuration = 900) {
        $this->pdo = $pdo;
        $this->maxAttempts = $maxAttempts;
        $this->lockoutDuration = $lockoutDuration;
        $this->ensureColumnsExist();
    }
    
    private function ensureColumnsExist() {
        try {
            // Add columns if they don't exist
            $columns = [
                "last_failed_login" => "ALTER TABLE users ADD COLUMN IF NOT EXISTS last_failed_login DATETIME DEFAULT NULL",
                "lock_until" => "ALTER TABLE users ADD COLUMN IF NOT EXISTS lock_until DATETIME DEFAULT NULL"
            ];
            
            foreach ($columns as $column => $sql) {
                try {
                    $this->pdo->exec($sql);
                } catch (PDOException $e) {
                    $checkStmt = $this->pdo->prepare("SHOW COLUMNS FROM users LIKE ?");
                    $checkStmt->execute([$column]);
                    if ($checkStmt->rowCount() === 0) {
                        $alterSql = "ALTER TABLE users ADD COLUMN $column DATETIME DEFAULT NULL";
                        $this->pdo->exec($alterSql);
                    }
                }
            }
        } catch (PDOException $e) {
            error_log("LoginHandler column check error: " . $e->getMessage());
        }
    }
    
    /**
     * Main login handler
     * @param string $email User email
     * @param string $password User password
     * @param string $role Expected role (examinee, admin, accountant)
     * @param array $extraFields Extra fields to validate (e.g., test_permit for examinees)
     * @return array Response with success status, message, and optional data
     */
    public function handleLogin($email, $password, $role, $extraFields = []) {
        // Build query based on role
        if ($role === 'examinee' && isset($extraFields['test_permit'])) {
            $stmt = $this->pdo->prepare(
                "SELECT user_id, password, test_permit, status, role, first_name, last_name, email,
                        failed_login_attempts, last_failed_login, lock_until
                 FROM users
                 WHERE email = :email AND test_permit = :permit
                 LIMIT 1"
            );
            $stmt->execute([
                'email' => $email,
                'permit' => $extraFields['test_permit']
            ]);
        } else {
            $stmt = $this->pdo->prepare(
                "SELECT user_id, password, status, role, first_name, last_name, email,
                        failed_login_attempts, last_failed_login, lock_until
                 FROM users
                 WHERE email = :email
                 LIMIT 1"
            );
            $stmt->execute(['email' => $email]);
        }
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // User not found
        if (!$user) {
            $message = ($role === 'examinee') ? "Invalid email or test permit" : "Email not found";
            logActivity('login_failed', ucfirst($role) . ' login failed - ' . $message, null, null, $email, $role, 'warning');
            return ['success' => false, 'message' => $message];
        }
        
        // Check if account is locked
        $lockCheck = $this->checkAccountLock($user);
        if (!$lockCheck['allowed']) {
            logActivity('account_lockout', "Login blocked - account locked for {$lockCheck['minutes_remaining']} more minutes", 
                       $user['user_id'], $user['first_name'] . ' ' . $user['last_name'], $email, $role, 'warning');
            return [
                'success' => false,
                'message' => "Too many login attempts. Please try again in {$lockCheck['time_display']}.",
                'retry_after' => $lockCheck['seconds_remaining']
            ];
        }
        
        // Verify password
        if (!password_verify($password, $user['password'])) {
            $this->recordFailedAttempt($user['user_id']);
            
            // Check if this attempt triggers a lock
            $newAttempts = ($user['failed_login_attempts'] ?? 0) + 1;
            if ($newAttempts >= $this->maxAttempts) {
                $this->lockAccount($user['user_id']);
                $minutes = ceil($this->lockoutDuration / 60);
                logActivity('account_lockout', "Account locked after {$newAttempts} failed attempts", 
                           $user['user_id'], $user['first_name'] . ' ' . $user['last_name'], $email, $role, 'warning');
                return [
                    'success' => false,
                    'message' => "Too many login attempts. Please try again in {$minutes} minute(s).",
                    'retry_after' => $this->lockoutDuration
                ];
            }
            
            logActivity('login_failed', ucfirst($role) . ' login failed - Incorrect password', 
                       $user['user_id'], $user['first_name'] . ' ' . $user['last_name'], $email, $role, 'warning');
            return ['success' => false, 'message' => 'Incorrect password'];
        }
        
        // Check role authorization
        if ($role !== 'examinee' && $user['role'] !== $role) {
            logActivity('login_failed', ucfirst($role) . ' login failed - Unauthorized access attempt', 
                       $user['user_id'], $user['first_name'] . ' ' . $user['last_name'], $email, $role, 'error');
            return ['success' => false, 'message' => "You do not have {$role} access"];
        }
        
        // For examinees, perform additional checks
        if ($role === 'examinee') {
            $examineeCheck = $this->checkExamineeStatus($user);
            if (!$examineeCheck['success']) {
                return $examineeCheck;
            }
        }
        
        // Login successful - reset failed attempts and clear lock
        $this->resetFailedAttempts($user['user_id']);
        
        // Set up session
        $this->setupSession($user, $role);
        
        // Log success
        $username = $user['first_name'] . ' ' . $user['last_name'];
        logActivity('login_success', ucfirst($role) . ' logged in successfully', 
                   $user['user_id'], $username, $email, $role, 'info');
        
        return [
            'success' => true,
            'message' => ucfirst($role) . ' login successful',
            'role' => $user['role'],
            'user' => [
                'user_id' => $user['user_id'],
                'email' => $user['email'],
                'first_name' => $user['first_name'],
                'last_name' => $user['last_name']
            ]
        ];
    }
    
    /**
     * Check if account is currently locked
     */
    private function checkAccountLock($user) {
        if (empty($user['lock_until'])) {
            return ['allowed' => true];
        }
        
        $lockUntil = new DateTime($user['lock_until']);
        $now = new DateTime();
        
        if ($now >= $lockUntil) {
            // Lock expired, reset
            $this->resetFailedAttempts($user['user_id']);
            return ['allowed' => true];
        }
        
        $secondsRemaining = $lockUntil->getTimestamp() - $now->getTimestamp();
        $minutesRemaining = ceil($secondsRemaining / 60);
        
        // Format time display
        if ($secondsRemaining < 60) {
            $timeDisplay = ceil($secondsRemaining) . " second" . (ceil($secondsRemaining) != 1 ? "s" : "");
        } else {
            $timeDisplay = $minutesRemaining . " minute" . ($minutesRemaining != 1 ? "s" : "");
        }
        
        return [
            'allowed' => false,
            'seconds_remaining' => $secondsRemaining,
            'minutes_remaining' => $minutesRemaining,
            'time_display' => $timeDisplay
        ];
    }
    
    /**
     * Record a failed login attempt
     */
    private function recordFailedAttempt($userId) {
        $stmt = $this->pdo->prepare(
            "UPDATE users SET 
                failed_login_attempts = COALESCE(failed_login_attempts, 0) + 1,
                last_failed_login = NOW()
             WHERE user_id = ?"
        );
        $stmt->execute([$userId]);
    }
    
    /**
     * Lock account for the configured duration
     */
    private function lockAccount($userId) {
        $lockUntil = date('Y-m-d H:i:s', time() + $this->lockoutDuration);
        $stmt = $this->pdo->prepare(
            "UPDATE users SET lock_until = ? WHERE user_id = ?"
        );
        $stmt->execute([$lockUntil, $userId]);
    }
    
    /**
     * Reset failed attempts and clear lock
     */
    private function resetFailedAttempts($userId) {
        $stmt = $this->pdo->prepare(
            "UPDATE users SET 
                failed_login_attempts = 0,
                last_failed_login = NULL,
                lock_until = NULL
             WHERE user_id = ?"
        );
        $stmt->execute([$userId]);
    }
    
    /**
     * Check examinee-specific status requirements
     */
    private function checkExamineeStatus($user) {
        // Check user status
        if ($user['status'] === 'incomplete') {
            $examineeStmt = $this->pdo->prepare(
                "SELECT status, schedule_id FROM examinees WHERE user_id = :user_id LIMIT 1"
            );
            $examineeStmt->execute([':user_id' => $user['user_id']]);
            $examinee = $examineeStmt->fetch(PDO::FETCH_ASSOC);
            
            if ($examinee) {
                if ($examinee['status'] === 'Awaiting Payment' && $examinee['schedule_id']) {
                    return [
                        'success' => false,
                        'message' => "Please complete your payment to activate your account.",
                        'redirect' => "payment.html"
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => "Please select your exam schedule to continue.",
                        'redirect' => "examsched.html"
                    ];
                }
            } else {
                return [
                    'success' => false,
                    'message' => "Please complete your registration first.",
                    'redirect' => "registration.html"
                ];
            }
        }
        
        if ($user['status'] === 'blocked') {
            return [
                'success' => false,
                'message' => "Your account has been blocked. Please contact support."
            ];
        }
        
        // Check examinees table status
        $examineeStmt = $this->pdo->prepare(
            "SELECT status, schedule_id FROM examinees WHERE user_id = :user_id LIMIT 1"
        );
        $examineeStmt->execute([':user_id' => $user['user_id']]);
        $examinee = $examineeStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$examinee) {
            return [
                'success' => false,
                'message' => "Examinee record not found. Please complete your registration.",
                'redirect' => "registration.html"
            ];
        }
        
        if ($examinee['status'] !== 'Scheduled') {
            if ($examinee['status'] === 'Awaiting Payment' && $examinee['schedule_id']) {
                return [
                    'success' => false,
                    'message' => "Please complete your payment to access your dashboard.",
                    'redirect' => "payment.html"
                ];
            } else {
                return [
                    'success' => false,
                    'message' => "Please select your exam schedule before logging in.",
                    'redirect' => "examsched.html"
                ];
            }
        }
        
        return ['success' => true];
    }
    
    /**
     * Set up user session
     */
    private function setupSession($user, $role) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['last_name'] = $user['last_name'];
        $_SESSION['role'] = $role;
        
        if ($role === 'admin') {
            $_SESSION['is_admin'] = true;
        }
    }
}

/**
 * Helper function for quick login handling
 */
function handleLogin($pdo, $email, $password, $role, $extraFields = [], $maxAttempts = 5, $lockoutDuration = 900) {
    $handler = new LoginHandler($pdo, $maxAttempts, $lockoutDuration);
    return $handler->handleLogin($email, $password, $role, $extraFields);
}
