-- Complete SQL script for OTP system
-- Run this to create/fix the OTP verification table

-- Drop existing table if it exists (optional - remove this line if you want to keep existing data)
-- DROP TABLE IF EXISTS `otp_verifications`;

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

-- Add columns to users table for exam schedule and payment
ALTER TABLE `users` 
ADD COLUMN IF NOT EXISTS `school` VARCHAR(255) DEFAULT '' AFTER `contact_number`,
ADD COLUMN IF NOT EXISTS `email_verified` TINYINT(1) DEFAULT 0 AFTER `password`,
ADD COLUMN IF NOT EXISTS `region` VARCHAR(50) DEFAULT '' AFTER `email_verified`,
ADD COLUMN IF NOT EXISTS `exam_venue` VARCHAR(255) DEFAULT '' AFTER `region`,
ADD COLUMN IF NOT EXISTS `exam_date` DATE DEFAULT NULL AFTER `exam_venue`,
ADD COLUMN IF NOT EXISTS `payment_method` VARCHAR(50) DEFAULT '' AFTER `exam_date`,
ADD COLUMN IF NOT EXISTS `payment_reference` VARCHAR(100) DEFAULT '' AFTER `payment_method`,
ADD COLUMN IF NOT EXISTS `payment_date` DATE DEFAULT NULL AFTER `payment_reference`,
ADD COLUMN IF NOT EXISTS `payment_status` VARCHAR(20) DEFAULT 'pending' AFTER `payment_date`;

-- Update payments table to include additional fields
ALTER TABLE `payments` 
ADD COLUMN IF NOT EXISTS `payment_method` VARCHAR(50) DEFAULT '' AFTER `payment_amount`,
ADD COLUMN IF NOT EXISTS `payment_reference` VARCHAR(100) DEFAULT '' AFTER `payment_method`,
ADD COLUMN IF NOT EXISTS `payment_status` VARCHAR(20) DEFAULT 'pending' AFTER `payment_reference`;
