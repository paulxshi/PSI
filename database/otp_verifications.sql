-- OTP Verification Table for Secure Registration
-- Run these SQL statements in order

-- 1. Make verified_at nullable with NULL default (fixes zero-date issue)
ALTER TABLE otp_verifications MODIFY verified_at DATETIME NULL DEFAULT NULL;

-- 2. Clean up legacy zero-dates so the app treats them as "not verified"
UPDATE otp_verifications SET verified_at = NULL WHERE verified_at = '0000-00-00 00:00:00';

-- 3. Add helpful lookup index used by register.php for performance
ALTER TABLE otp_verifications ADD INDEX idx_email_purpose_verified (email, purpose, verified_at, expires_at);

-- 4. Full table structure for reference
-- CREATE TABLE IF NOT EXISTS `otp_verifications` (
--   `id` int(11) NOT NULL AUTO_INCREMENT,
--   `email` varchar(255) NOT NULL,
--   `otp` varchar(6) NOT NULL,
--   `purpose` enum('registration','password_reset') NOT NULL,
--   `attempts` int(11) NOT NULL DEFAULT 0,
--   `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
--   `expires_at` datetime NOT NULL,
--   `verified_at` datetime DEFAULT NULL,
--   PRIMARY KEY (`id`),
--   KEY `idx_email` (`email`),
--   KEY `idx_email_purpose_verified` (`email`, `purpose`, `verified_at`, `expires_at`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 5. Optional: Add email_verified column to users table for registration flow
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `email_verified` tinyint(1) NOT NULL DEFAULT 0 AFTER `otp_expiry`;
