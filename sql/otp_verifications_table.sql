-- Create otp_verifications table if it doesn't exist
CREATE TABLE IF NOT EXISTS `otp_verifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `otp` varchar(10) NOT NULL COMMENT 'OTP code (VARCHAR to preserve leading zeros)',
  `purpose` varchar(50) NOT NULL DEFAULT 'registration',
  `expires_at` datetime NOT NULL,
  `verified_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_email_purpose` (`email`, `purpose`),
  KEY `idx_expires_at` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
