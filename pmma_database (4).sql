-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 09, 2026 at 02:33 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pmma_database`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `activity_type` enum('login_success','login_failed','logout','password_change','password_reset','account_lockout','otp_sent','otp_verified','otp_failed','registration','registration_completed','payment_created','payment_completed','payment_failed','schedule_changed','admin_schedule_created','admin_schedule_edited','admin_schedule_deleted','admin_examinee_updated') NOT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `role` enum('admin','accountant','examinee','system') DEFAULT NULL,
  `severity` enum('info','warning','error','critical') DEFAULT 'info',
  `metadata` text DEFAULT NULL COMMENT 'JSON data for additional context',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`log_id`, `user_id`, `username`, `email`, `activity_type`, `description`, `ip_address`, `user_agent`, `role`, `severity`, `metadata`, `created_at`) VALUES
(68, NULL, NULL, 'admin@psi-services.net', 'login_failed', 'Admin login failed - Email not found', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 'admin', 'warning', NULL, '2026-03-30 14:56:44'),
(69, 1, 'Lee Ivan Almadrones', 'leeivanalmadrones6@gmail.com', 'login_success', 'Admin logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 'admin', 'info', NULL, '2026-03-30 14:56:53'),
(70, 1, 'Lee Ivan Almadrones', 'leeivanalmadrones6@gmail.com', 'logout', 'Admin logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 'admin', 'info', NULL, '2026-03-30 15:10:10'),
(71, NULL, NULL, 'admin@psi-services.net', 'login_failed', 'Admin login failed - Email not found', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 'admin', 'warning', NULL, '2026-04-07 08:01:46'),
(72, NULL, NULL, 'admin@psi-services.net', 'login_failed', 'Admin login failed - Email not found', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 'admin', 'warning', NULL, '2026-04-07 08:02:48'),
(73, NULL, NULL, 'staff@psi-services.net', 'login_failed', 'Admin login failed - Email not found', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 'admin', 'warning', NULL, '2026-04-08 12:55:52'),
(74, NULL, NULL, 'admin@psi-services.net', 'login_failed', 'Admin login failed - Email not found', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 'admin', 'warning', NULL, '2026-04-08 12:55:57'),
(75, 1, 'Lee Ivan Almadrones', 'admin@psi-services.net', 'login_success', 'Admin logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 'admin', 'info', NULL, '2026-04-08 12:57:31'),
(76, NULL, NULL, 'staff@psi-services.net', 'login_failed', 'Admin login failed - Email not found', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 'admin', 'warning', NULL, '2026-04-09 08:10:42'),
(77, 1, 'Lee Ivan Almadrones', 'admin@psi-services.net', 'login_success', 'Admin logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 'admin', 'info', NULL, '2026-04-09 08:10:46'),
(78, 1, 'Lee Ivan Almadrones', 'admin@psi-services.net', 'logout', 'Admin logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 'admin', 'info', NULL, '2026-04-09 08:12:18'),
(79, 1, 'Lee Ivan Almadrones', 'admin@psi-services.net', 'login_success', 'Admin logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 'admin', 'info', NULL, '2026-04-09 08:12:32'),
(80, 1, 'Admin', 'admin@psi-services.net', 'admin_schedule_created', 'Admin created new schedule for Labo, Luzon on 2026-04-10', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 'admin', 'info', '{\"schedule_id\":\"0\",\"venue\":\"Labo\",\"region\":\"Luzon\",\"date\":\"2026-04-10\",\"capacity\":10,\"price\":100}', '2026-04-09 08:25:52'),
(81, 1, 'Admin', 'admin@psi-services.net', 'admin_schedule_deleted', 'Admin deleted schedule #3', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 'admin', 'warning', '{\"schedule_id\":3}', '2026-04-09 08:30:31'),
(82, 1, 'Admin', 'admin@psi-services.net', 'admin_schedule_created', 'Admin created new schedule for Labo, Luzon on 2026-04-10', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 'admin', 'info', '{\"schedule_id\":\"0\",\"venue\":\"Labo\",\"region\":\"Luzon\",\"date\":\"2026-04-10\",\"capacity\":10,\"price\":100}', '2026-04-09 08:31:08');

-- --------------------------------------------------------

--
-- Table structure for table `examinees`
--

CREATE TABLE `examinees` (
  `examinee_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `test_permit` varchar(50) NOT NULL,
  `schedule_id` int(11) DEFAULT NULL,
  `status` enum('Pending','Awaiting Payment','Scheduled','Completed') DEFAULT 'Pending',
  `date_of_registration` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `examinee_status` enum('Registered','Completed','Absent','Rejected') DEFAULT NULL,
  `scanned_at` datetime DEFAULT NULL,
  `attended_schedule_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `examinees`
--

INSERT INTO `examinees` (`examinee_id`, `user_id`, `test_permit`, `schedule_id`, `status`, `date_of_registration`, `updated_at`, `examinee_status`, `scanned_at`, `attended_schedule_id`) VALUES
(3, 7, 'TP-2026-00004', NULL, 'Scheduled', '2026-03-16 13:21:09', '2026-03-16 13:51:01', 'Registered', NULL, NULL),
(4, 8, 'TP-2026-00001', NULL, 'Scheduled', '2026-03-20 13:42:45', '2026-03-20 14:11:49', 'Registered', NULL, NULL),
(41, 9, 'TP-2026-00025', NULL, 'Scheduled', '2026-03-29 10:59:05', '2026-03-29 10:59:05', 'Registered', NULL, NULL),
(42, 10, 'TP-2026-00026', NULL, 'Scheduled', '2026-03-29 10:59:05', '2026-03-29 10:59:05', 'Registered', NULL, NULL),
(43, 11, 'TP-2026-00027', NULL, 'Scheduled', '2026-03-29 10:59:05', '2026-03-29 10:59:05', 'Registered', NULL, NULL),
(44, 12, 'TP-2026-00028', NULL, 'Scheduled', '2026-03-29 10:59:05', '2026-03-29 10:59:05', 'Registered', NULL, NULL),
(45, 13, 'TP-2026-00029', NULL, 'Scheduled', '2026-03-29 10:59:05', '2026-03-29 10:59:05', 'Registered', NULL, NULL),
(46, 14, 'TP-2026-00030', NULL, 'Scheduled', '2026-03-29 10:59:05', '2026-03-29 10:59:05', 'Registered', NULL, NULL),
(47, 15, 'TP-2026-00031', NULL, 'Scheduled', '2026-03-29 10:59:05', '2026-03-29 10:59:05', 'Registered', NULL, NULL),
(48, 16, 'TP-2026-00032', NULL, 'Scheduled', '2026-03-29 10:59:05', '2026-03-29 10:59:05', 'Registered', NULL, NULL),
(49, 17, 'TP-2026-00033', NULL, 'Scheduled', '2026-03-29 10:59:05', '2026-03-29 10:59:05', 'Registered', NULL, NULL),
(50, 18, 'TP-2026-00034', NULL, 'Scheduled', '2026-03-29 10:59:05', '2026-03-29 10:59:05', 'Registered', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `examinee_masterlist`
--

CREATE TABLE `examinee_masterlist` (
  `id` int(11) NOT NULL,
  `test_permit` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `used` tinyint(1) DEFAULT 0,
  `used_by` int(11) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `faqs`
--

CREATE TABLE `faqs` (
  `faq_id` int(11) NOT NULL,
  `question` varchar(255) NOT NULL,
  `answer` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faqs`
--

INSERT INTO `faqs` (`faq_id`, `question`, `answer`, `created_at`, `updated_at`) VALUES
(1, 'What happens after I complete the online registration?', 'Once your online registration is confirmed, you will receive a confirmation email. You may then Login to generate your Test Permit for verification. Please do not forget to check your scheduled date and venue. Also, wait for further announcements in case there are any updates.', '2026-02-22 03:01:53', '2026-02-23 03:06:54'),
(2, 'Do I need to bring anything for the onsite exam?', 'Yes. Bring 2 pencils, eraser, sharpener, your ID, your printed test permit provided after registration. These will be scanned at the entrance to verify your registration.', '2026-02-22 03:01:53', '2026-02-23 02:46:38'),
(3, 'How do I download my Test Permit?', 'To download your Test Permit, go to Account Settings > View Test Permit > Generate Test Permit. After clicking, the Test Permit will download to your device. Please print it, as it will serve as your verification before the examination.', '2026-02-23 02:59:17', '2026-02-23 03:01:50'),
(4, 'What if I lose my QR code or test permit?', 'You can log in to your account at any time to download your QR code or test permit again. If you encounter issues, contact the support team before your exam date.', '2026-02-22 03:01:53', '2026-02-23 02:26:24'),
(5, 'Can I reschedule my onsite examination?', 'Rescheduling depends on availability. Contact the site administrator for assistance.', '2026-02-22 03:01:53', '2026-02-23 03:02:06');

-- --------------------------------------------------------

--
-- Table structure for table `meals`
--

CREATE TABLE `meals` (
  `meal_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `schedule_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `otp_verifications`
--

CREATE TABLE `otp_verifications` (
  `verification_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `otp` varchar(255) NOT NULL,
  `otp_attempts` int(11) DEFAULT 0,
  `is_used` tinyint(1) DEFAULT 0,
  `purpose` varchar(50) DEFAULT 'registration',
  `created_at` datetime DEFAULT current_timestamp(),
  `expires_at` datetime NOT NULL,
  `verified_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `reset_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `otp` varchar(6) NOT NULL,
  `otp_attempts` int(3) DEFAULT 0,
  `is_used` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `expires_at` datetime NOT NULL,
  `used_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `payment_date` datetime DEFAULT current_timestamp(),
  `user_id` int(11) NOT NULL,
  `examinee_id` int(11) NOT NULL,
  `status` enum('PENDING','PAID','FAILED','EXPIRED') DEFAULT 'PENDING',
  `paid_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `xendit_invoice_id` varchar(100) NOT NULL,
  `external_id` varchar(100) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `channel` varchar(255) DEFAULT NULL,
  `email_sent` tinyint(1) DEFAULT 0,
  `xendit_response` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rate_limits`
--

CREATE TABLE `rate_limits` (
  `id` int(11) NOT NULL,
  `action_type` varchar(50) NOT NULL,
  `identifier` varchar(100) NOT NULL,
  `attempts` int(11) DEFAULT 0,
  `first_attempt` datetime NOT NULL,
  `last_attempt` datetime NOT NULL,
  `blocked_until` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rate_limits`
--

INSERT INTO `rate_limits` (`id`, `action_type`, `identifier`, `attempts`, `first_attempt`, `last_attempt`, `blocked_until`) VALUES
(4, 'registration', '::1', 1, '2026-03-20 13:42:45', '2026-03-20 13:42:45', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

CREATE TABLE `schedules` (
  `schedule_id` int(11) NOT NULL,
  `venue_id` int(11) NOT NULL,
  `scheduled_date` date NOT NULL,
  `capacity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `status` enum('Incoming','Closed','Completed') DEFAULT 'Incoming',
  `num_registered` int(11) NOT NULL DEFAULT 0,
  `num_of_examinees` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedules`
--

INSERT INTO `schedules` (`schedule_id`, `venue_id`, `scheduled_date`, `capacity`, `price`, `status`, `num_registered`, `num_of_examinees`) VALUES
(4, 2, '2026-04-10', 0, 100.00, 'Incoming', 0, 10);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `test_permit` varchar(50) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `contact_number` varchar(20) NOT NULL,
  `date_of_birth` date NOT NULL,
  `age` int(3) DEFAULT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `school` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `nationality` varchar(100) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `profile_upload_attempts` int(2) DEFAULT 0 COMMENT 'Number of profile picture uploads (max 3)',
  `last_profile_update` datetime DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `email_verified` tinyint(1) DEFAULT 0,
  `status` enum('incomplete','active','blocked') DEFAULT 'incomplete',
  `role` enum('examinee','admin','accountant') DEFAULT 'examinee',
  `failed_login_attempts` int(11) DEFAULT 0,
  `last_login` datetime DEFAULT NULL,
  `date_of_registration` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_failed_login` datetime DEFAULT NULL,
  `lock_until` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `test_permit`, `last_name`, `first_name`, `middle_name`, `email`, `contact_number`, `date_of_birth`, `age`, `gender`, `school`, `address`, `nationality`, `profile_picture`, `profile_upload_attempts`, `last_profile_update`, `password`, `email_verified`, `status`, `role`, `failed_login_attempts`, `last_login`, `date_of_registration`, `updated_at`, `last_failed_login`, `lock_until`) VALUES
(1, 'ADMIN-001', 'Almadrones', 'Lee Ivan', 'Oliva', 'admin@psi-services.net', '09123456789', '1990-01-01', 35, 'Male', 'System Administrator', 'Manila, Philippines', 'Filipino', NULL, 0, NULL, '$2y$10$kvAJtauJrxGQnrkceNrPm.unQdTXqtioY.HKMgjptaghgyjsJYtUW', 1, 'active', 'admin', 0, NULL, '2026-03-09 15:04:52', '2026-04-08 12:57:25', NULL, NULL),
(2, 'ACC-001', 'Ivan', 'Lee', 'Oliva', 'leeivanalmadrones@gmail.com', '09987654321', '1992-05-10', 33, 'Female', 'Finance Department', 'Quezon City, Philippines', 'Filipino', NULL, 0, NULL, '$2y$10$FEg7L9ylljt0tLwhdyFcgujyYBXqBc5vI8uKsd8waxq/IzmY3oAmC', 1, 'active', 'accountant', 0, NULL, '2026-03-09 15:05:43', '2026-03-20 13:41:36', NULL, NULL),
(4, 'ADMIN-002\r\n    ', 'Bernandino', 'Cezar', 'Oliva', 'bernandinocezar@gmail.com', '09123456789', '1990-01-01', 35, 'Male', 'System Administrator', 'Manila, Philippines', 'Filipino', NULL, 0, NULL, '$2y$10$5vetdOFASJEyF3520AXDi.A5lB6nBQ7ioWmLqzMF/1qZZIWgv3NQ2', 1, 'active', 'admin', 0, NULL, '2026-03-09 15:23:49', '2026-03-12 10:33:16', NULL, NULL),
(7, 'TP-2026-00004', 'Magana', 'Althea Kei', 'A.', 'ivssalmadrones@gmail.com', '09108987920', '2008-02-02', 18, 'Male', 'PMMA', '', 'Filipino', NULL, 0, NULL, '$2y$10$pacxApSasnLwVGY7E/oibu13NAlETCR649Jl5hp0t2V3m/bvIKEbK', 1, 'active', 'examinee', 2, NULL, '2026-03-16 13:21:09', '2026-03-18 17:24:24', '2026-03-18 17:24:24', NULL),
(8, 'TP-2026-00001', 'Almadrones', 'Lee Ivan', 'Oliva', 'leeivanalmadrones2004@gmail.com', '09108987920', '2008-02-02', 18, 'Male', 'PMMA', '', 'Filipino', NULL, 0, NULL, '$2y$10$eGDEJbXaGwQeGa2zMknUKOJTR5Ud9i9G9OuLFFvYjHLZ1yWUxU.9O', 1, 'active', 'examinee', 1, NULL, '2026-03-20 13:42:45', '2026-03-20 14:12:33', '2026-03-20 14:12:33', NULL),
(9, 'TP-2026-00025', 'Dela Cruz', 'Juan', 'Santos', 'juan1@test.com', '09111111111', '2000-02-01', 25, 'Male', 'UP Diliman', 'Quezon City', 'Filipino', NULL, 0, NULL, '$2y$10$samplehash', 1, 'active', '', 0, NULL, '2026-03-29 10:48:15', '2026-03-29 10:48:15', NULL, NULL),
(10, 'TP-2026-00026', 'Reyes', 'Maria', 'Lopez', 'maria2@test.com', '09111111112', '1999-03-05', 26, 'Female', 'UST', 'Manila', 'Filipino', NULL, 0, NULL, '$2y$10$samplehash', 1, 'active', '', 0, NULL, '2026-03-29 10:48:15', '2026-03-29 10:48:15', NULL, NULL),
(11, 'TP-2026-00027', 'Garcia', 'Pedro', 'Ramos', 'pedro3@test.com', '09111111113', '2001-04-10', 24, 'Male', 'FEU', 'Manila', 'Filipino', NULL, 0, NULL, '$2y$10$samplehash', 1, 'active', '', 0, NULL, '2026-03-29 10:48:15', '2026-03-29 10:48:15', NULL, NULL),
(12, 'TP-2026-00028', 'Torres', 'Ana', 'Cruz', 'ana4@test.com', '09111111114', '2002-05-15', 23, 'Female', 'NU', 'Quezon City', 'Filipino', NULL, 0, NULL, '$2y$10$samplehash', 1, 'active', '', 0, NULL, '2026-03-29 10:48:15', '2026-03-29 10:48:15', NULL, NULL),
(13, 'TP-2026-00029', 'Flores', 'Mark', 'Diaz', 'mark5@test.com', '09111111115', '1998-06-20', 27, 'Male', 'PUP', 'Manila', 'Filipino', NULL, 0, NULL, '$2y$10$samplehash', 1, 'active', '', 0, NULL, '2026-03-29 10:48:15', '2026-03-29 10:48:15', NULL, NULL),
(14, 'TP-2026-00030', 'Gonzales', 'Liza', 'Perez', 'liza6@test.com', '09111111116', '2000-07-25', 25, 'Female', 'UP Manila', 'Manila', 'Filipino', NULL, 0, NULL, '$2y$10$samplehash', 1, 'active', '', 0, NULL, '2026-03-29 10:48:15', '2026-03-29 10:48:15', NULL, NULL),
(15, 'TP-2026-00031', 'Bautista', 'Carlo', 'Mendoza', 'carlo7@test.com', '09111111117', '1997-08-30', 28, 'Male', 'Adamson', 'Manila', 'Filipino', NULL, 0, NULL, '$2y$10$samplehash', 1, 'active', '', 0, NULL, '2026-03-29 10:48:15', '2026-03-29 10:48:15', NULL, NULL),
(16, 'TP-2026-00032', 'Villanueva', 'Grace', 'Aquino', 'grace8@test.com', '09111111118', '2001-09-12', 24, 'Female', 'CEU', 'Makati', 'Filipino', NULL, 0, NULL, '$2y$10$samplehash', 1, 'active', '', 0, NULL, '2026-03-29 10:48:15', '2026-03-29 10:48:15', NULL, NULL),
(17, 'TP-2026-00033', 'Navarro', 'Jose', 'Castro', 'jose9@test.com', '09111111119', '1996-10-18', 29, 'Male', 'TIP', 'Quezon City', 'Filipino', NULL, 0, NULL, '$2y$10$samplehash', 1, 'active', '', 0, NULL, '2026-03-29 10:48:15', '2026-03-29 10:48:15', NULL, NULL),
(18, 'TP-2026-00034', 'Santos', 'Karla', 'Morales', 'karla10@test.com', '09111111120', '2002-11-22', 23, 'Female', 'DLSU', 'Manila', 'Filipino', NULL, 0, NULL, '$2y$10$samplehash', 1, 'active', '', 0, NULL, '2026-03-29 10:48:15', '2026-03-29 10:48:15', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `venue`
--

CREATE TABLE `venue` (
  `venue_id` int(11) NOT NULL,
  `venue_name` varchar(255) NOT NULL,
  `region` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `venue`
--

INSERT INTO `venue` (`venue_id`, `venue_name`, `region`) VALUES
(1, 'Daet', 'Luzon'),
(2, 'Labo', 'Luzon');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_activity_type` (`activity_type`),
  ADD KEY `idx_role` (`role`),
  ADD KEY `idx_severity` (`severity`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_composite` (`activity_type`,`role`,`created_at`);

--
-- Indexes for table `examinees`
--
ALTER TABLE `examinees`
  ADD PRIMARY KEY (`examinee_id`),
  ADD UNIQUE KEY `test_permit` (`test_permit`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `schedule_id` (`schedule_id`),
  ADD KEY `fk_examinees_attended_schedule` (`attended_schedule_id`);

--
-- Indexes for table `examinee_masterlist`
--
ALTER TABLE `examinee_masterlist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_test_permit` (`test_permit`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_used` (`used`),
  ADD KEY `fk_masterlist_used_by` (`used_by`);

--
-- Indexes for table `faqs`
--
ALTER TABLE `faqs`
  ADD PRIMARY KEY (`faq_id`);

--
-- Indexes for table `meals`
--
ALTER TABLE `meals`
  ADD PRIMARY KEY (`meal_id`),
  ADD KEY `fk_schedule` (`schedule_id`);

--
-- Indexes for table `otp_verifications`
--
ALTER TABLE `otp_verifications`
  ADD PRIMARY KEY (`verification_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`reset_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_otp` (`otp`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD UNIQUE KEY `xendit_invoice_id` (`xendit_invoice_id`),
  ADD KEY `idx_xendit_invoice_id` (`xendit_invoice_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_examinee_id` (`examinee_id`),
  ADD KEY `idx_external_id` (`external_id`);

--
-- Indexes for table `rate_limits`
--
ALTER TABLE `rate_limits`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_action_identifier` (`action_type`,`identifier`),
  ADD KEY `idx_blocked_until` (`blocked_until`);

--
-- Indexes for table `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`schedule_id`),
  ADD KEY `venue_id` (`venue_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `test_permit` (`test_permit`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `venue`
--
ALTER TABLE `venue`
  ADD PRIMARY KEY (`venue_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT for table `examinees`
--
ALTER TABLE `examinees`
  MODIFY `examinee_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `examinee_masterlist`
--
ALTER TABLE `examinee_masterlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `faqs`
--
ALTER TABLE `faqs`
  MODIFY `faq_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `meals`
--
ALTER TABLE `meals`
  MODIFY `meal_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `otp_verifications`
--
ALTER TABLE `otp_verifications`
  MODIFY `verification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `reset_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `rate_limits`
--
ALTER TABLE `rate_limits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `schedule_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `venue`
--
ALTER TABLE `venue`
  MODIFY `venue_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `examinees`
--
ALTER TABLE `examinees`
  ADD CONSTRAINT `examinees_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `examinees_ibfk_2` FOREIGN KEY (`schedule_id`) REFERENCES `schedules` (`schedule_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_examinees_attended_schedule` FOREIGN KEY (`attended_schedule_id`) REFERENCES `schedules` (`schedule_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `examinee_masterlist`
--
ALTER TABLE `examinee_masterlist`
  ADD CONSTRAINT `fk_masterlist_used_by` FOREIGN KEY (`used_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `meals`
--
ALTER TABLE `meals`
  ADD CONSTRAINT `fk_schedule` FOREIGN KEY (`schedule_id`) REFERENCES `schedules` (`schedule_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `fk_password_resets_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`examinee_id`) REFERENCES `examinees` (`examinee_id`) ON DELETE CASCADE;

--
-- Constraints for table `schedules`
--
ALTER TABLE `schedules`
  ADD CONSTRAINT `schedules_ibfk_1` FOREIGN KEY (`venue_id`) REFERENCES `venue` (`venue_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
