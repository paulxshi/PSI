-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 20, 2026 at 03:07 AM
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
-- Database: `test_psi`
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
  `role` enum('admin','examinee','system') DEFAULT NULL,
  `severity` enum('info','warning','error','critical') DEFAULT 'info',
  `metadata` text DEFAULT NULL COMMENT 'JSON data for additional context',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`log_id`, `user_id`, `username`, `email`, `activity_type`, `description`, `ip_address`, `user_agent`, `role`, `severity`, `metadata`, `created_at`) VALUES
(1, 30, 'cezaralmadrones@gmail.com', 'cezaralmadrones@gmail.com', 'registration_completed', 'New user registered: cezaralmadrones@gmail.com (Permit: 34)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', '{\"user_id\":\"30\",\"test_permit\":\"34\",\"email\":\"cezaralmadrones@gmail.com\"}', '2026-02-19 16:00:07'),
(2, 31, 'ivanivan@gmail.com', 'ivanivan@gmail.com', 'registration_completed', 'New user registered: ivanivan@gmail.com (Permit: 2121)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', '{\"user_id\":\"31\",\"test_permit\":\"2121\",\"email\":\"ivanivan@gmail.com\"}', '2026-02-19 16:23:40'),
(3, 32, 'fdfdfd@gmail.com', 'fdfdfd@gmail.com', 'registration_completed', 'New user registered: fdfdfd@gmail.com (Permit: 2222)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', '{\"user_id\":\"32\",\"test_permit\":\"2222\",\"email\":\"fdfdfd@gmail.com\"}', '2026-02-19 16:35:05'),
(4, 32, 'Admin', '', 'admin_schedule_created', 'Admin created new schedule for Tagum City, Mindanao on 2026-05-03', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'admin', 'info', '{\"schedule_id\":\"0\",\"venue\":\"Tagum City\",\"region\":\"Mindanao\",\"date\":\"2026-05-03\",\"capacity\":100,\"price\":40}', '2026-02-19 16:37:11'),
(5, 31, 'John Sir', 'ivanivan@gmail.com', 'login_success', 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', NULL, '2026-02-19 16:38:15'),
(6, 31, 'John Sir', 'ivanivan@gmail.com', 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', NULL, '2026-02-19 16:41:46'),
(7, 1, 'Admin', 'leeivanalmadrones6@gmail.com', 'login_success', 'Admin logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'admin', 'info', NULL, '2026-02-19 16:41:53'),
(8, 1, NULL, NULL, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'admin', 'info', NULL, '2026-02-19 16:43:21'),
(9, 1, 'Admin', 'leeivanalmadrones6@gmail.com', 'login_success', 'Admin logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'admin', 'info', NULL, '2026-02-19 16:43:43'),
(10, 33, 'susanalmadrones6@gmail.com', 'susanalmadrones6@gmail.com', 'registration_completed', 'New user registered: susanalmadrones6@gmail.com (Permit: 9999)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', '{\"user_id\":\"33\",\"test_permit\":\"9999\",\"email\":\"susanalmadrones6@gmail.com\"}', '2026-02-19 16:45:43'),
(11, 33, NULL, 'susanalmadrones6@gmail.com', 'password_reset', 'User reset their password via email link', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', NULL, '2026-02-19 16:47:12'),
(12, 33, 'Juan Jade', 'susanalmadrones6@gmail.com', 'login_failed', 'Incorrect password', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'warning', NULL, '2026-02-19 16:47:33'),
(13, 33, 'Juan Jade', 'susanalmadrones6@gmail.com', 'login_success', 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', NULL, '2026-02-19 16:47:37'),
(14, 33, 'Juan Jade', 'susanalmadrones6@gmail.com', 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', NULL, '2026-02-19 16:48:03'),
(15, 24, NULL, NULL, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'admin', 'info', NULL, '2026-02-19 19:41:05'),
(16, 1, 'Admin', 'leeivanalmadrones6@gmail.com', 'login_success', 'Admin logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'admin', 'info', NULL, '2026-02-19 19:41:14'),
(17, 1, 'Admin', 'leeivanalmadrones6@gmail.com', 'login_success', 'Admin logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'admin', 'info', NULL, '2026-02-20 09:32:18'),
(18, 1, 'Admin', '', 'schedule_changed', 'Admin rescheduled examinee (Permit: 9999) to Manila on 2026-04-04', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'admin', 'info', '{\"user_id\":33,\"test_permit\":\"9999\",\"old_schedule_id\":51,\"new_schedule_id\":32,\"new_date\":\"2026-04-04\",\"venue\":\"Manila\",\"region\":\"Luzon\"}', '2026-02-20 09:39:49');

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
  `scanned_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `examinees`
--

INSERT INTO `examinees` (`examinee_id`, `user_id`, `test_permit`, `schedule_id`, `status`, `date_of_registration`, `updated_at`, `examinee_status`, `scanned_at`) VALUES
(1, 2, '22-0828', NULL, 'Pending', '2026-02-18 08:47:01', '2026-02-18 14:10:37', NULL, NULL),
(2, 3, '890-1234', NULL, 'Pending', '2026-02-18 11:38:47', '2026-02-18 14:10:37', NULL, NULL),
(3, 4, '789-0123', 46, 'Scheduled', '2026-02-18 11:57:00', '2026-02-18 14:10:37', NULL, NULL),
(4, 5, '678-9012', 46, 'Scheduled', '2026-02-18 13:25:59', '2026-02-18 14:10:37', NULL, NULL),
(5, 6, '567-8901', 46, 'Awaiting Payment', '2026-02-18 13:44:08', '2026-02-18 14:10:37', NULL, NULL),
(6, 7, '456-7890', NULL, 'Pending', '2026-02-18 14:11:40', '2026-02-18 14:11:40', NULL, NULL),
(7, 8, '345-6789', 47, 'Awaiting Payment', '2026-02-18 14:16:21', '2026-02-18 14:18:27', NULL, NULL),
(8, 9, '901-2345', 47, 'Awaiting Payment', '2026-02-18 14:54:15', '2026-02-18 14:55:02', NULL, NULL),
(9, 10, '123-4567', 47, 'Awaiting Payment', '2026-02-18 15:58:10', '2026-02-18 16:08:46', NULL, NULL),
(10, 11, '000-1111', 47, 'Awaiting Payment', '2026-02-18 16:22:18', '2026-02-18 16:22:36', NULL, NULL),
(11, 12, '999-0002', 46, 'Awaiting Payment', '2026-02-18 16:40:33', '2026-02-18 16:54:42', NULL, NULL),
(12, 13, '888-0001', 46, 'Awaiting Payment', '2026-02-18 17:06:43', '2026-02-18 17:06:56', NULL, NULL),
(13, 14, '112-2334', 47, 'Awaiting Payment', '2026-02-18 17:21:08', '2026-02-18 17:21:16', NULL, NULL),
(14, 15, '223-3445', 47, 'Awaiting Payment', '2026-02-18 17:27:41', '2026-02-18 17:28:03', NULL, NULL),
(15, 16, '2026-102', 47, 'Scheduled', '2026-02-18 17:34:34', '2026-02-18 17:36:36', NULL, NULL),
(16, 17, '2026-103', NULL, 'Pending', '2026-02-18 17:45:06', '2026-02-18 17:45:06', NULL, NULL),
(17, 18, '2026-104', 46, 'Scheduled', '2026-02-18 17:48:15', '2026-02-18 17:50:57', NULL, NULL),
(18, 19, '2026-105', 48, 'Scheduled', '2026-02-18 18:21:55', '2026-02-18 18:23:13', 'Registered', NULL),
(19, 20, '222-3333', NULL, 'Pending', '2026-02-18 21:36:38', '2026-02-18 21:36:38', NULL, NULL),
(20, 21, '333-4444', 49, 'Scheduled', '2026-02-18 21:50:13', '2026-02-18 21:56:13', 'Registered', NULL),
(21, 22, '444-5555', 50, 'Awaiting Payment', '2026-02-18 22:16:43', '2026-02-18 22:17:03', NULL, NULL),
(22, 23, '22', 47, 'Scheduled', '2026-02-19 08:15:23', '2026-02-20 10:06:26', 'Completed', '2026-02-11 10:06:24'),
(23, 24, '555-6666', 44, 'Scheduled', '2026-02-19 08:39:11', '2026-02-20 10:06:23', 'Completed', '2026-02-12 10:06:21'),
(24, 25, '123', 48, 'Scheduled', '2026-02-19 08:52:50', '2026-02-20 10:06:19', 'Completed', '2026-02-11 10:06:17'),
(25, 26, '12345', 42, 'Scheduled', '2026-02-19 09:00:04', '2026-02-20 10:06:16', 'Completed', '2026-02-19 10:06:12'),
(26, 27, '456', 49, 'Awaiting Payment', '2026-02-19 09:20:11', '2026-02-19 09:21:15', NULL, NULL),
(27, 28, '223', NULL, 'Pending', '2026-02-19 09:24:51', '2026-02-19 09:24:51', NULL, NULL),
(28, 29, '0909', 33, 'Scheduled', '2026-02-19 11:19:02', '2026-02-19 14:44:10', 'Completed', NULL),
(29, 30, '34', 52, 'Scheduled', '2026-02-19 16:00:07', '2026-02-19 16:17:12', 'Registered', NULL),
(30, 31, '2121', 53, 'Scheduled', '2026-02-19 16:23:40', '2026-02-19 16:24:14', 'Registered', NULL),
(31, 32, '2222', 48, 'Scheduled', '2026-02-19 16:35:05', '2026-02-19 16:35:37', 'Registered', NULL),
(32, 33, '9999', 32, 'Scheduled', '2026-02-19 16:45:43', '2026-02-20 09:39:49', 'Registered', NULL);

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

--
-- Dumping data for table `examinee_masterlist`
--

INSERT INTO `examinee_masterlist` (`id`, `test_permit`, `last_name`, `first_name`, `middle_name`, `email`, `used`, `used_by`, `uploaded_at`) VALUES
(1, '901-2345', 'Diaz', 'Kimberly', 'Louise', 'gdfg', 0, 9, '2026-02-18 00:19:18'),
(2, '112-2334', 'Valdez', 'Christian', 'Mark', 'sdsa', 1, 14, '2026-02-18 00:19:18'),
(3, '223-3445', 'Alvarez', 'Samantha', 'Jane', 'das', 1, 15, '2026-02-18 00:19:18'),
(4, '2026-102', 'Dela Cruz', 'Maria', '', 'fasd', 1, 16, '2026-02-18 00:19:18'),
(5, '2026-103', 'De Guzman', 'Carlos', 'Miguel', 'fdsfs', 1, 17, '2026-02-18 00:19:18'),
(6, '2026-104', 'San Juan', 'Ana', 'Sofia', 'dfg', 1, 18, '2026-02-18 00:19:18'),
(7, '2026-105', 'Dela Peña', 'Pedro', '', 'fdsfd', 1, 19, '2026-02-18 00:19:18'),
(8, '22-0890', 'Martinez', 'Luis', 'Antonio', '', 1, 20, '2026-02-18 00:19:18'),
(9, '333-4444', 'Ramos', 'Isabella', 'Marie', 'rwerw', 1, 21, '2026-02-18 00:19:18'),
(10, '22', 'Gonzales', 'Mark', 'Anthony', 'lkl', 1, 23, '2026-02-18 00:19:18'),
(11, '123', 'Bautista', 'Angela', 'Rose', 'ivssalmadrones6@gmail.com', 1, 25, '2026-02-18 00:19:18'),
(12, '666-7777', 'Navarro', 'Michael', 'James', 'michael.navarro@example.com', 0, NULL, '2026-02-18 00:19:18'),
(13, '777-8888', 'Villanueva', 'Sarah', 'Joy', 'sarah.villanueva@example.com', 0, NULL, '2026-02-18 00:19:18'),
(14, '888-0001', 'Cruz', 'Daniel', 'Lee', 'dasd', 1, 13, '2026-02-18 00:19:18'),
(15, '999-0002', 'Gomez', 'Patricia', 'Anne', 'jhgjh', 1, 12, '2026-02-18 00:19:18'),
(16, '000-1111', 'Ramirez', 'Joshua', 'Paul', 'rere', 1, 11, '2026-02-18 00:19:18'),
(17, '123-4567', 'Lim', 'Christine', 'Mae', 'sad', 1, 10, '2026-02-18 00:19:18'),
(18, '234-5678', 'Aquino', 'Kevin', 'John', 'louise@gmail.com', 0, NULL, '2026-02-18 00:19:18'),
(19, '345-6789', 'Flores', 'Karen', 'Grace', 'asds', 1, 8, '2026-02-18 00:19:18'),
(20, '456-7890', 'Mendoza', 'Ryan', 'Patrick', 'ryan@gmail.com', 1, 7, '2026-02-18 00:19:18'),
(21, '567-8901', 'Torres', 'Jessica', 'Claire', 'claire@gmail.com', 1, 6, '2026-02-18 00:19:18'),
(22, '678-9012', 'Morales', 'Adrian', 'Joseph', 'joseph@gmail.com', 1, 5, '2026-02-18 00:19:18'),
(23, '789-0123', 'Castro', 'Nicole', 'Faith', 'faith@gmail.com', 1, 4, '2026-02-18 00:19:18'),
(24, '890-1234', 'Ortiz', 'Brandon', 'Keith', 'keith@gmail.com', 1, 3, '2026-02-18 00:19:18'),
(25, '22-0828', 'Gabo', 'John Paul', 'Doe', 'gabo@gmail.com', 1, 2, '2026-02-18 00:20:49'),
(26, '22-0829', 'Gab', 'John Paul', '', 'gab6@gmail.com', 0, NULL, '2026-02-18 03:27:40'),
(27, '12345', 'dasdasd', 'dasdas', 'dasdasd', 'sdasdasda@gmail.com', 1, 26, '2026-02-19 00:59:26'),
(28, '456', 'dfsdfds', 'fdsfsdf', 'fsdfsdf', 'dfsdfdsf@gmail.com', 1, 27, '2026-02-19 01:17:07'),
(29, '223', 'qwer', 'qwerqwe', 'qwewew', 'juan@gmail.com', 1, 28, '2026-02-19 01:23:32'),
(30, '0909', 'John', 'Zuelos', '6', 'cezaralmadrones6@gmail.com', 1, 29, '2026-02-19 03:17:47'),
(31, '34', 'dsfd', 'fsdfsd', 'fsdfsdf', 'cezaralmadrones@gmail.com', 1, 30, '2026-02-19 07:59:06'),
(32, '2121', 'Sir', 'John', 'Go', 'ivanivan@gmail.com', 1, 31, '2026-02-19 08:22:41'),
(33, '2222', 'rere', 'rere', 'rere', 'fdfdfd@gmail.com', 1, 32, '2026-02-19 08:33:57'),
(34, '9999', 'Jade', 'Juan', 'A', 'susanalmadrones6@gmail.com', 1, 33, '2026-02-19 08:43:10');

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

--
-- Dumping data for table `otp_verifications`
--

INSERT INTO `otp_verifications` (`verification_id`, `email`, `otp`, `otp_attempts`, `is_used`, `purpose`, `created_at`, `expires_at`, `verified_at`) VALUES
(1, 'ivssalmadrones@gmail.com', '125808', 0, 0, 'registration', '2026-02-18 08:24:58', '2026-02-18 01:34:58', NULL),
(2, 'ivssalmadrones@gmail.com', '404588', 0, 0, 'registration', '2026-02-18 08:46:14', '2026-02-18 01:56:14', NULL),
(3, 'ivssalmadrones@gmail.com', '942188', 0, 0, 'registration', '2026-02-18 11:38:20', '2026-02-18 04:48:20', NULL),
(4, 'ivssalmadrones@gmail.com', '603509', 0, 0, 'registration', '2026-02-18 11:56:25', '2026-02-18 05:06:25', NULL),
(5, 'ivssalmadrones@gmail.com', '103561', 0, 0, 'registration', '2026-02-18 13:25:39', '2026-02-18 06:35:39', NULL),
(6, 'ivssalmadrones@gmail.com', '924672', 0, 0, 'registration', '2026-02-18 13:43:32', '2026-02-18 06:53:32', NULL),
(7, 'ivssalmadrones@gmail.com', '751834', 0, 0, 'registration', '2026-02-18 14:10:54', '2026-02-18 07:20:54', NULL),
(8, 'ivssalmadrones@gmail.com', '198680', 0, 0, 'registration', '2026-02-18 14:15:41', '2026-02-18 07:25:41', NULL),
(9, 'ivssalmadrones@gmail.com', '147381', 0, 0, 'registration', '2026-02-18 14:53:47', '2026-02-18 08:03:47', NULL),
(10, 'ivssalmadrones@gmail.com', '530766', 0, 0, 'registration', '2026-02-18 15:57:50', '2026-02-18 09:07:50', NULL),
(11, 'ivssalmadrones@gmail.com', '792290', 0, 0, 'registration', '2026-02-18 16:22:02', '2026-02-18 09:32:02', NULL),
(12, 'ivssalmadrones@gmail.com', '792134', 0, 0, 'registration', '2026-02-18 16:40:14', '2026-02-18 09:50:14', NULL),
(13, 'ivssalmadrones@gmail.com', '992774', 0, 0, 'registration', '2026-02-18 17:06:08', '2026-02-18 10:16:08', NULL),
(14, 'ivssalmadrones@gmail.com', '930816', 0, 0, 'registration', '2026-02-18 17:20:47', '2026-02-18 10:30:47', NULL),
(15, 'ivssalmadrones@gmail.com', '746725', 0, 0, 'registration', '2026-02-18 17:27:20', '2026-02-18 10:37:20', NULL),
(16, 'ivssalmadrones@gmail.com', '581428', 0, 0, 'registration', '2026-02-18 17:34:18', '2026-02-18 10:44:18', NULL),
(17, 'ivssalmadrones@gmail.com', '287643', 0, 0, 'registration', '2026-02-18 17:44:35', '2026-02-18 10:54:35', NULL),
(18, 'ivssalmadrones@gmail.com', '925478', 0, 0, 'registration', '2026-02-18 17:47:48', '2026-02-18 10:57:48', NULL),
(19, 'ivssalmadrones@gmail.com', '259978', 0, 0, 'registration', '2026-02-18 18:21:36', '2026-02-18 11:31:36', NULL),
(20, 'ivssalmadrones@gmail.com', '393820', 0, 0, 'registration', '2026-02-18 21:36:06', '2026-02-18 14:46:06', NULL),
(21, 'ivssalmadrones@gmail.com', '843254', 0, 0, 'registration', '2026-02-18 21:49:43', '2026-02-18 14:59:43', NULL),
(22, 'theakei@gmail.com', '567919', 0, 0, 'registration', '2026-02-18 22:09:54', '2026-02-18 15:19:54', NULL),
(23, 'ivssalmadrones@gmail.com', '852750', 0, 0, 'registration', '2026-02-18 22:14:38', '2026-02-18 15:24:38', NULL),
(24, 'ivssalmadrones@gmail.com', '962735', 0, 0, 'registration', '2026-02-19 08:14:24', '2026-02-19 01:24:24', NULL),
(25, 'ivssalmadrones6@gmail.com', '742423', 0, 0, 'registration', '2026-02-19 08:38:52', '2026-02-19 01:48:52', NULL),
(26, 'ivssalmadrones6@gmail.com', '360153', 0, 0, 'registration', '2026-02-19 08:52:30', '2026-02-19 02:02:30', NULL),
(27, 'sdasdasda@gmail.com', '913490', 0, 0, 'registration', '2026-02-19 08:59:45', '2026-02-19 02:09:45', NULL),
(28, 'dfsdfdsf@gmail.com', '271461', 0, 0, 'registration', '2026-02-19 09:17:45', '2026-02-19 02:27:45', NULL),
(29, 'dfsdfdsf@gmail.com', '573539', 0, 0, 'registration', '2026-02-19 09:19:20', '2026-02-19 02:29:20', NULL),
(30, 'juan@gmail.com', '729696', 0, 0, 'registration', '2026-02-19 09:24:08', '2026-02-19 02:34:08', NULL),
(31, 'cezaralmadrones6@gmail.com', '802615', 0, 0, 'registration', '2026-02-19 11:18:35', '2026-02-19 04:28:35', NULL),
(32, 'cezaralmadrones@gmail.com', '363524', 0, 0, 'registration', '2026-02-19 15:59:46', '2026-02-19 09:09:46', NULL),
(33, 'ivanivan@gmail.com', '195000', 0, 0, 'registration', '2026-02-19 16:23:04', '2026-02-19 09:33:04', NULL),
(34, 'fdfdfd@gmail.com', '158732', 0, 0, 'registration', '2026-02-19 16:34:22', '2026-02-19 09:44:22', NULL),
(35, 'susanalmadrones6@gmail.com', '145883', 0, 0, 'registration', '2026-02-19 16:45:09', '2026-02-19 09:55:09', NULL);

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

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`reset_id`, `user_id`, `email`, `otp`, `otp_attempts`, `is_used`, `created_at`, `expires_at`, `used_at`) VALUES
(1, 28, 'juan@gmail.com', '931114', 0, 0, '2026-02-19 09:25:27', '2026-02-19 02:35:27', NULL),
(2, 28, 'laarnialmadrones@gmail.com', '275588', 0, 1, '2026-02-19 09:26:08', '2026-02-19 02:36:08', '2026-02-19 09:26:59'),
(3, 33, 'susanalmadrones6@gmail.com', '318179', 0, 1, '2026-02-19 16:46:37', '2026-02-19 09:56:37', '2026-02-19 16:47:12');

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
  `email_sent` tinyint(1) DEFAULT 0,
  `xendit_response` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `payment_date`, `user_id`, `examinee_id`, `status`, `paid_at`, `created_at`, `updated_at`, `xendit_invoice_id`, `external_id`, `amount`, `email_sent`, `xendit_response`) VALUES
(1, '2026-02-18 14:36:23', 8, 7, 'PENDING', NULL, '2026-02-18 14:36:23', '2026-02-18 14:36:23', '69955de65b4b3bdc07ae9087', 'PMMA_345-6789_1771396581', 1000.00, 0, NULL),
(4, '2026-02-18 14:52:33', 8, 7, 'PENDING', NULL, '2026-02-18 14:52:33', '2026-02-18 14:52:33', '699561b053b9bc3482141ea3', 'PMMA_345-6789_1771397552', 100.00, 0, NULL),
(5, '2026-02-18 14:55:19', 9, 8, 'PENDING', NULL, '2026-02-18 14:55:19', '2026-02-18 14:55:19', '6995625653b9bc3482141fb3', 'PMMA_901-2345_1771397717', 1000.00, 0, NULL),
(6, '2026-02-18 15:01:19', 9, 8, 'PENDING', NULL, '2026-02-18 15:01:19', '2026-02-18 15:01:19', '699563be53b9bc3482142289', 'PMMA_901-2345_1771398077', 100.00, 0, NULL),
(7, '2026-02-18 16:08:53', 10, 9, 'PENDING', NULL, '2026-02-18 16:08:53', '2026-02-18 16:08:53', '6995739553b9bc3482143dfb', 'PMMA_123-4567_1771402132', 1000.00, 0, NULL),
(8, '2026-02-18 16:22:42', 11, 10, 'PENDING', NULL, '2026-02-18 16:22:42', '2026-02-18 16:22:42', '699576d153b9bc3482144393', 'PMMA_000-1111_1771402960', 1000.00, 0, NULL),
(9, '2026-02-18 16:40:50', 12, 11, 'PAID', '2026-02-18 16:42:07', '2026-02-18 16:40:50', '2026-02-18 16:42:07', '69957b115b4b3bdc07aec3e6', 'PMMA_999-0002_1771404048', 1000.00, 0, '{\"id\":\"69957b115b4b3bdc07aec3e6\",\"external_id\":\"FORCE_TRIGGER_1771404127099\",\"status\":\"PAID\",\"amount\":1000,\"paid_at\":\"2026-02-18T08:42:07.099Z\",\"created\":\"2026-02-18T08:42:07.099Z\",\"updated\":\"2026-02-18T08:42:07.099Z\"}'),
(10, '2026-02-18 16:54:46', 12, 11, 'PENDING', NULL, '2026-02-18 16:54:46', '2026-02-18 16:54:46', '69957e5553b9bc34821450db', 'PMMA_999-0002_1771404884', 1500.00, 0, NULL),
(11, '2026-02-18 17:07:11', 13, 12, 'PENDING', NULL, '2026-02-18 17:07:11', '2026-02-18 17:07:11', '6995813e5b4b3bdc07aecebd', 'PMMA_888-0001_1771405629', 1500.00, 0, NULL),
(12, '2026-02-18 17:21:21', 14, 13, 'PENDING', NULL, '2026-02-18 17:21:21', '2026-02-18 17:21:21', '699584905b4b3bdc07aed4ae', 'PMMA_112-2334_1771406479', 1000.00, 0, NULL),
(13, '2026-02-18 17:28:08', 15, 14, 'PENDING', NULL, '2026-02-18 17:28:08', '2026-02-18 17:28:08', '6995862753b9bc3482145eb9', 'PMMA_223-3445_1771406886', 1000.00, 0, NULL),
(14, '2026-02-18 17:34:51', 16, 15, 'PAID', '2026-02-18 17:36:36', '2026-02-18 17:34:51', '2026-02-18 17:36:36', '699587bb5b4b3bdc07aedaab', 'PMMA_2026-102_1771407290', 1000.00, 0, '{\"id\":\"699587bb5b4b3bdc07aedaab\",\"external_id\":\"PMMA_2026-102_1771407290\",\"status\":\"PAID\",\"amount\":1000,\"paid_at\":\"2026-02-18T09:36:36.517Z\",\"created\":\"2026-02-18T09:36:36.517Z\",\"updated\":\"2026-02-18T09:36:36.517Z\"}'),
(15, '2026-02-18 17:50:11', 18, 17, 'PAID', '2026-02-18 17:50:57', '2026-02-18 17:50:11', '2026-02-18 17:50:57', '69958b525b4b3bdc07aee119', 'PMMA_2026-104_1771408210', 1500.00, 0, '{\"id\":\"69958b525b4b3bdc07aee119\",\"external_id\":\"PMMA_2026-104_1771408210\",\"status\":\"PAID\",\"amount\":1500,\"paid_at\":\"2026-02-18T09:50:57.679Z\",\"created\":\"2026-02-18T09:50:57.681Z\",\"updated\":\"2026-02-18T09:50:57.681Z\"}'),
(16, '2026-02-18 18:22:09', 19, 18, 'PAID', '2026-02-18 18:23:12', '2026-02-18 18:22:09', '2026-02-18 18:23:12', '699592d05b4b3bdc07aeed78', 'PMMA_2026-105_1771410127', 100.00, 0, '{\"id\":\"699592d05b4b3bdc07aeed78\",\"external_id\":\"PMMA_2026-105_1771410127\",\"status\":\"PAID\",\"amount\":100,\"paid_at\":\"2026-02-18T10:23:12.975Z\",\"created\":\"2026-02-18T10:23:12.976Z\",\"updated\":\"2026-02-18T10:23:12.976Z\"}'),
(17, '2026-02-18 21:54:32', 21, 20, 'PAID', '2026-02-18 21:56:13', '2026-02-18 21:54:32', '2026-02-18 21:56:13', '6995c49753b9bc348214b77e', 'PMMA_333-4444_1771422870', 200.00, 0, '{\"id\":\"6995c49753b9bc348214b77e\",\"external_id\":\"PMMA_333-4444_1771422870\",\"status\":\"PAID\",\"amount\":200,\"paid_at\":\"2026-02-18T13:56:13.156Z\",\"created\":\"2026-02-18T13:56:13.156Z\",\"updated\":\"2026-02-18T13:56:13.156Z\"}'),
(18, '2026-02-18 22:17:27', 22, 21, 'PENDING', NULL, '2026-02-18 22:17:27', '2026-02-18 22:17:27', '6995c9f553b9bc348214be5e', 'PMMA_444-5555_1771424245', 200.00, 0, NULL),
(19, '2026-02-19 08:16:16', 23, 22, 'PAID', '2026-02-19 08:17:04', '2026-02-19 08:16:16', '2026-02-19 08:17:04', '6996564e53b9bc34821556a7', 'PMMA_22_1771460174', 1000.00, 0, '{\"id\":\"6996564e53b9bc34821556a7\",\"external_id\":\"PMMA_22_1771460174\",\"status\":\"PAID\",\"amount\":1000,\"paid_at\":\"2026-02-19T00:17:04.700Z\",\"created\":\"2026-02-19T00:17:04.701Z\",\"updated\":\"2026-02-19T00:17:04.701Z\"}'),
(20, '2026-02-19 08:39:29', 24, 23, 'PAID', '2026-02-19 08:42:12', '2026-02-19 08:39:29', '2026-02-19 08:42:12', '69965bc053b9bc3482156288', 'PMMA_555-6666_1771461568', 1500.00, 0, '{\"id\":\"69965bc053b9bc3482156288\",\"external_id\":\"PMMA_555-6666_1771461568\",\"status\":\"PAID\",\"amount\":1500,\"paid_at\":\"2026-02-19T00:42:12.861Z\",\"created\":\"2026-02-19T00:42:12.863Z\",\"updated\":\"2026-02-19T00:42:12.863Z\"}'),
(21, '2026-02-19 08:53:07', 25, 24, 'PAID', '2026-02-19 08:53:38', '2026-02-19 08:53:07', '2026-02-19 08:53:38', '69965ef253b9bc3482156926', 'PMMA_123_1771462385', 100.00, 0, '{\"id\":\"69965ef253b9bc3482156926\",\"external_id\":\"PMMA_123_1771462385\",\"status\":\"PAID\",\"amount\":100,\"paid_at\":\"2026-02-19T00:53:38.135Z\",\"created\":\"2026-02-19T00:53:38.135Z\",\"updated\":\"2026-02-19T00:53:38.135Z\"}'),
(22, '2026-02-19 09:00:22', 26, 25, 'PAID', '2026-02-19 09:02:50', '2026-02-19 09:00:22', '2026-02-19 09:02:50', '699660a553b9bc3482156cdc', 'PMMA_12345_1771462819', 100.00, 0, '{\"id\":\"699660a553b9bc3482156cdc\",\"external_id\":\"PMMA_12345_1771462819\",\"status\":\"PAID\",\"amount\":100,\"paid_at\":\"2026-02-19T01:02:50.781Z\",\"created\":\"2026-02-19T01:02:50.782Z\",\"updated\":\"2026-02-19T01:02:50.782Z\"}'),
(23, '2026-02-19 11:36:08', 29, 28, 'PENDING', NULL, '2026-02-19 11:36:08', '2026-02-19 11:36:08', '6996852753b9bc348215aac5', 'PMMA_0909_1771472166', 140.00, 0, NULL),
(24, '2026-02-19 11:36:32', 29, 28, 'PENDING', NULL, '2026-02-19 11:36:32', '2026-02-19 11:36:32', '6996853f53b9bc348215aaea', 'PMMA_0909_1771472191', 140.00, 0, NULL),
(25, '2026-02-19 11:38:37', 29, 28, 'PAID', '2026-02-19 11:39:08', '2026-02-19 11:38:37', '2026-02-19 11:39:08', '699685bd53b9bc348215abc9', 'PMMA_0909_1771472316', 160.00, 0, '{\"id\":\"699685bd53b9bc348215abc9\",\"external_id\":\"PMMA_0909_1771472316\",\"status\":\"PAID\",\"amount\":160,\"paid_at\":\"2026-02-19T03:39:08.254Z\",\"created\":\"2026-02-19T03:39:08.254Z\",\"updated\":\"2026-02-19T03:39:08.254Z\"}'),
(26, '2026-02-19 16:16:34', 30, 29, 'PAID', '2026-02-19 16:17:12', '2026-02-19 16:16:34', '2026-02-19 16:17:12', '6996c6e153b9bc3482161906', 'PMMA_34_1771488993', 130.00, 0, '{\"id\":\"6996c6e153b9bc3482161906\",\"external_id\":\"PMMA_34_1771488993\",\"status\":\"PAID\",\"amount\":130,\"paid_at\":\"2026-02-19T08:17:12.545Z\",\"created\":\"2026-02-19T08:17:12.545Z\",\"updated\":\"2026-02-19T08:17:12.545Z\"}'),
(27, '2026-02-19 16:23:52', 31, 30, 'PAID', '2026-02-19 16:24:14', '2026-02-19 16:23:52', '2026-02-19 16:24:14', '6996c8975b4b3bdc07b0934e', 'PMMA_2121_1771489431', 160.00, 0, '{\"id\":\"6996c8975b4b3bdc07b0934e\",\"external_id\":\"PMMA_2121_1771489431\",\"status\":\"PAID\",\"amount\":160,\"paid_at\":\"2026-02-19T08:24:14.342Z\",\"created\":\"2026-02-19T08:24:14.342Z\",\"updated\":\"2026-02-19T08:24:14.342Z\"}'),
(28, '2026-02-19 16:35:18', 32, 31, 'PAID', '2026-02-19 16:35:37', '2026-02-19 16:35:18', '2026-02-19 16:35:37', '6996cb4553b9bc3482162065', 'PMMA_2222_1771490116', 100.00, 0, '{\"id\":\"6996cb4553b9bc3482162065\",\"external_id\":\"PMMA_2222_1771490116\",\"status\":\"PAID\",\"amount\":100,\"paid_at\":\"2026-02-19T08:35:37.879Z\",\"created\":\"2026-02-19T08:35:37.880Z\",\"updated\":\"2026-02-19T08:35:37.880Z\"}'),
(29, '2026-02-19 16:45:54', 33, 32, 'PAID', '2026-02-19 16:46:14', '2026-02-19 16:45:54', '2026-02-19 16:46:14', '6996cdc153b9bc3482162467', 'PMMA_9999_1771490752', 130.00, 0, '{\"id\":\"6996cdc153b9bc3482162467\",\"external_id\":\"PMMA_9999_1771490752\",\"status\":\"PAID\",\"amount\":130,\"paid_at\":\"2026-02-19T08:46:14.915Z\",\"created\":\"2026-02-19T08:46:14.915Z\",\"updated\":\"2026-02-19T08:46:14.915Z\"}');

-- --------------------------------------------------------

--
-- Table structure for table `rate_limits`
--

CREATE TABLE `rate_limits` (
  `id` int(11) NOT NULL,
  `action_type` varchar(50) NOT NULL COMMENT 'Type of action: login, registration, etc.',
  `identifier` varchar(100) NOT NULL COMMENT 'IP address or user identifier',
  `attempts` int(11) DEFAULT 0 COMMENT 'Number of attempts made',
  `first_attempt` datetime NOT NULL COMMENT 'Timestamp of first attempt',
  `last_attempt` datetime NOT NULL COMMENT 'Timestamp of last attempt',
  `blocked_until` datetime DEFAULT NULL COMMENT 'Blocked until this time'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rate_limits`
--

INSERT INTO `rate_limits` (`id`, `action_type`, `identifier`, `attempts`, `first_attempt`, `last_attempt`, `blocked_until`) VALUES
(4, 'registration', '::1', 4, '2026-02-19 16:00:07', '2026-02-19 16:45:43', NULL),
(5, 'login', '::1', 3, '2026-02-19 16:38:15', '2026-02-19 16:47:37', NULL);

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
(22, 1, '2026-02-17', 0, 0.00, 'Incoming', 45, 200),
(23, 2, '2026-02-19', 0, 0.00, 'Incoming', 0, 100),
(24, 3, '2026-02-23', 0, 0.00, 'Incoming', 0, 300),
(25, 3, '2026-02-23', 0, 0.00, 'Incoming', 34, 300),
(27, 2, '2026-10-23', 0, 0.00, 'Incoming', 108, 200),
(28, 4, '2026-02-26', 0, 0.00, 'Incoming', 59, 250),
(29, 1, '2026-03-04', 0, 0.00, 'Completed', 38, 200),
(30, 1, '2026-02-23', 0, 0.00, 'Incoming', 0, 300),
(31, 1, '2026-03-12', 0, 0.00, 'Incoming', 209, 400),
(32, 1, '2026-04-04', 0, 0.00, 'Incoming', 41, 250),
(33, 1, '2026-03-04', 0, 0.00, 'Incoming', 1, 3000),
(34, 5, '2026-03-01', 0, 0.00, 'Incoming', 378, 500),
(36, 7, '2026-03-20', 0, 0.00, 'Incoming', 582, 1000),
(37, 8, '2026-03-30', 0, 0.00, 'Incoming', 78, 100),
(42, 3, '2026-03-10', 0, 0.00, 'Incoming', 1, 150),
(44, 3, '2026-02-04', 0, 1500.00, 'Incoming', 1, 150),
(45, 11, '2026-03-31', 0, 1500.00, 'Incoming', 0, 150),
(46, 11, '2026-02-19', 0, 1500.00, 'Incoming', 6, 190),
(47, 11, '2026-02-25', 0, 1000.00, 'Incoming', 8, 200),
(48, 3, '2026-02-27', 0, 100.00, 'Incoming', 3, 150),
(49, 3, '2026-02-05', 0, 200.00, 'Incoming', 2, 150),
(50, 3, '2026-02-19', 0, 200.00, 'Incoming', 1, 20),
(51, 12, '2026-05-01', 0, 130.00, 'Incoming', 0, 100),
(52, 12, '2026-05-01', 0, 130.00, 'Incoming', 1, 100),
(53, 14, '2026-05-02', 0, 160.00, 'Incoming', 1, 100),
(54, 11, '2026-05-03', 0, 40.00, 'Incoming', 0, 100);

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
  `password` varchar(255) NOT NULL,
  `email_verified` tinyint(1) DEFAULT 0,
  `status` enum('incomplete','active','blocked') DEFAULT 'incomplete',
  `role` enum('examinee','admin') DEFAULT 'examinee',
  `failed_login_attempts` int(11) DEFAULT 0,
  `last_login` datetime DEFAULT NULL,
  `date_of_registration` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `test_permit`, `last_name`, `first_name`, `middle_name`, `email`, `contact_number`, `date_of_birth`, `age`, `gender`, `school`, `address`, `nationality`, `password`, `email_verified`, `status`, `role`, `failed_login_attempts`, `last_login`, `date_of_registration`, `updated_at`) VALUES
(1, '-806', 'Almadrones', 'Lee Ivan', NULL, 'leeivanalmadrones6@gmail.com', '09123456789', '1995-01-01', NULL, 'Male', 'System Administrator', 'Philippines', 'Filipino', '$2y$10$GPOcbTTUMe20YIg4sFHhd.GOP7s4Qr0h/.SxvaMHOENJU4W9j9OV2', 1, 'active', 'admin', 0, NULL, '2026-02-18 08:16:17', '2026-02-18 14:10:37'),
(2, '22-0828', 'Gabo', 'John Paul', 'Oliva', 'gabo@gmail.com', '0923232323', '2004-02-10', 22, 'Male', 'PMMA', '', '', '$2y$10$u69CjToEaHpzDPwlQ1fvDe.PRuYzbmTlWGP9svOfDq9uBjfBywNZS', 1, 'incomplete', 'examinee', 0, NULL, '2026-02-18 08:47:01', '2026-02-18 14:10:37'),
(3, '890-1234', 'Ortiz', 'Brandon', 'Keith', 'keith@gmail.com', '09086687599', '2004-02-18', 21, 'Male', 'PMMA', '', '', '$2y$10$YGCdciLlVvkAiE24N8tSFeOifWgakGs9nPEQNFmaZ92ZBM2bzdG0S', 1, 'incomplete', 'examinee', 0, NULL, '2026-02-18 11:38:47', '2026-02-18 14:10:37'),
(4, '789-0123', 'Castro', 'Nicole', 'Faith', 'faith@gmail.com', '09086687599', '2005-02-03', 21, 'Female', 'PMMA', '', '', '$2y$10$ZCeoG0R/lpzOTaBnw1HIw.PJZwUNgebxyuTwVoNFk4tQvrqrA36km', 1, 'incomplete', 'examinee', 0, NULL, '2026-02-18 11:57:00', '2026-02-18 14:10:37'),
(5, '678-9012', 'Morales', 'Adrian', 'Joseph', 'morales@gmail.com', '09086687599', '2004-02-12', 22, 'Male', 'PMMA', '', '', '$2y$10$wvZc.vf5yN4g6VnvDFfaJOh9MTLm5atOQrJbA63dkIYZxg2C5hcPW', 1, 'incomplete', 'examinee', 0, NULL, '2026-02-18 13:25:59', '2026-02-18 14:10:37'),
(6, '567-8901', 'Torres', 'Jessica', 'Claire', 'claire@gmail.com', '09086687599', '2004-03-11', 21, 'Male', 'PMMA', '', '', '$2y$10$MT6Q6Bjm29RaCbucfYBhI.F9imZJcnoqj7RBY8j8NPbxnL88XcuSm', 1, 'incomplete', 'examinee', 0, NULL, '2026-02-18 13:44:08', '2026-02-18 14:10:37'),
(7, '456-7890', 'Mendoza', 'Ryan', 'Patrick', 'ryan@gmail.com', '09086687599', '2004-02-12', 22, 'Male', 'PMMA', '', '', '$2y$10$Mu3v5QMx0VGd6pOM.7Qz5.P0Kb9vqDgM4sZvu0P9Z1bRubvIuWy.S', 1, 'incomplete', 'examinee', 0, NULL, '2026-02-18 14:11:40', '2026-02-18 14:14:47'),
(8, '345-6789', 'Flores', 'Karen', 'Grace', 'gdfg', '09086687599', '2004-02-10', 22, 'Male', 'PMMA', '', '', '$2y$10$5jhXZa/GRGCJ4drm07jVFOG14dsytEih6yTcbIEp74BPTypB9mF1u', 1, 'incomplete', 'examinee', 0, NULL, '2026-02-18 14:16:21', '2026-02-18 17:01:16'),
(9, '901-2345', 'Diaz', 'Kimberly', 'Louise', 'louissse@gmail.com', '09086687599', '2008-02-07', 18, 'Male', 'PMMA', '', '', '$2y$10$yzU7K0dFhnYI9EAkzDdIte6dn9Q0EiVFUBI7.jAGFYMoJUkQLpZYG', 1, 'incomplete', 'examinee', 0, NULL, '2026-02-18 14:54:15', '2026-02-18 15:56:30'),
(10, '123-4567', 'Lim', 'Christine', 'Mae', 'dasd', '09086687599', '2004-02-10', 22, 'Male', 'PMMA', '', '', '$2y$10$4qGgx6WXd/5dwN0rbuuk8.AV4XDSLi8o6INUd6WY8I6dIO3pekWDi', 1, 'incomplete', 'examinee', 0, NULL, '2026-02-18 15:58:10', '2026-02-18 16:21:08'),
(11, '000-1111', 'Ramirez', 'Joshua', 'Paul', 'wq', '09086687599', '2004-02-10', 22, 'Male', 'PMMA', '', '', '$2y$10$r6DSetxXyG3u3fhBQiedauSUB551ew0AvPoFv.NMfA0Ya2E63G7Li', 1, 'incomplete', 'examinee', 0, NULL, '2026-02-18 16:22:18', '2026-02-18 16:39:09'),
(12, '999-0002', 'Gomez', 'Patricia', 'Anne', 'sdas', '09086687599', '2004-02-17', 22, 'Male', 'PMMA', '', '', '$2y$10$lv6axW/P/F98Bf0jUzEXT..Oaen19/5e8jJr42Bw/Cv3Bgfhv1vv2', 1, 'active', 'examinee', 0, NULL, '2026-02-18 16:40:33', '2026-02-18 17:01:12'),
(13, '888-0001', 'Cruz', 'Daniel', 'Lee', 'das', '09086687599', '2004-02-11', 22, 'Male', 'PMMA', '', '', '$2y$10$9pmNOO13A/C5CSnXTJagp.hTVW/iTavdgCB.4j4KcTKFW1AcFdUme', 1, 'incomplete', 'examinee', 0, NULL, '2026-02-18 17:06:43', '2026-02-18 17:17:23'),
(14, '112-2334', 'Valdez', 'Christian', 'Mark', 'fsdfds', '09086687599', '2004-02-17', 22, 'Male', 'PMMA', '', '', '$2y$10$yXOLNagFLqStXnCzq5PzEeypbDMZXir/q8jK/mI.bGe7teZYCaSVK', 1, 'incomplete', 'examinee', 0, NULL, '2026-02-18 17:21:08', '2026-02-18 17:26:44'),
(15, '223-3445', 'Alvarez', 'Samantha', 'Jane', 'fsdf', '09086687599', '2004-02-10', 22, 'Male', 'PMMA', '', '', '$2y$10$Id9JPIuvnlgFd62XA89FBeIcMQac/P/KVvghwjsvj60uteAv9.hq.', 1, 'incomplete', 'examinee', 0, NULL, '2026-02-18 17:27:41', '2026-02-18 17:33:23'),
(16, '2026-102', 'Dela Cruz', 'Maria', 'Jane', 'fsfds', '09086687599', '2005-02-17', 21, 'Male', 'PMMA', '', '', '$2y$10$JK6Of2hEdB5RDXS0kr/jvOOodMwBZpi2A.UDqv65qr4wRmG1wJR96', 1, 'active', 'examinee', 0, NULL, '2026-02-18 17:34:34', '2026-02-18 17:43:23'),
(17, '2026-103', 'De Guzman', 'Carlos', 'Miguel', 'uty', '09086687599', '2004-02-12', 22, 'Male', 'PMMA', '', '', '$2y$10$58iP4..pAfcXiE98buodJ.avcQ3YK9b7lM0sQw88iDJRs4N1IpojO', 1, 'incomplete', 'examinee', 0, NULL, '2026-02-18 17:45:06', '2026-02-18 17:47:26'),
(18, '2026-104', 'San Juan', 'Ana', 'Sofia', 'dsdas', '09086687599', '2003-02-06', 23, 'Male', 'PMMA', '', '', '$2y$10$bnyVx50rXbcFwl.gxhLIT.jZnXdInKL9QNy9yChjnjezZGUU9wk3G', 1, 'active', 'examinee', 0, NULL, '2026-02-18 17:48:15', '2026-02-18 18:20:10'),
(19, '2026-105', 'Dela Peña', 'Pedro', 'Sofia', 'dasdsa', '09086687599', '2005-02-10', 21, 'Male', 'PMMA', '', '', '$2y$10$2Ox1nfeuHtKvdzqXFxHriOvaSX1BxUewa4EElNwMN.P3OU3Um9.yu', 1, 'active', 'examinee', 0, NULL, '2026-02-18 18:21:55', '2026-02-18 21:30:57'),
(20, '222-3333', 'Martinez', 'Luis', 'Antonio', 'sdfdf', '09086687599', '2004-02-10', 22, 'Male', 'PMMA', '', '', '$2y$10$8LNd5bl9AIAS6JyOrewMOO3oI1Bw3WpXMyUV1.vHyR1rYIjHJ/NaO', 1, 'incomplete', 'examinee', 0, NULL, '2026-02-18 21:36:38', '2026-02-18 21:48:50'),
(21, '333-4444', 'Ramos', 'Isabella', 'Marie', 'werew', '09086687599', '2004-02-10', 22, 'Male', 'PMMA', '', '', '$2y$10$if6/bhP18edr9k5rB9dOz.BYVj2O4AZi3r799eM/YMVtD7XkhZMyq', 1, 'active', 'examinee', 0, NULL, '2026-02-18 21:50:13', '2026-02-18 22:14:31'),
(22, '444-5555', 'Gonzales', 'Mark', 'Anthony', 'eeee', '09086687599', '2024-02-13', 2, 'Male', 'PMMA', '', '', '$2y$10$v0v90izYgWk.5bmsUH1luORP563GM0LMaF9oZxY79LTLBj396E6ZK', 1, 'incomplete', 'examinee', 0, NULL, '2026-02-18 22:16:43', '2026-02-19 08:05:33'),
(23, '22', 'Gonzales', 'Mark', 'Anthony', 'sasa', '09086687599', '2004-02-10', 22, 'Male', 'PMMA', '', '', '$2y$10$gmg6OuRGowPhuED31MrmSuSJ.eGq0NzpOp8KC5Ss8ydVDkn86enFC', 1, 'active', 'examinee', 0, NULL, '2026-02-19 08:15:23', '2026-02-19 08:36:58'),
(24, '555-6666', 'Bautista', 'Angela', 'Rose', 'fsdfd', '09086687599', '2004-02-10', 22, 'Male', 'PMMA', '', '', '$2y$10$Ora7UzFOUYxsvSe0EFkR5.cX/MNIZRsNlM8PR62ZCQLwOhojxiLPm', 1, 'active', 'examinee', 0, NULL, '2026-02-19 08:39:11', '2026-02-19 08:51:17'),
(25, '123', 'Bautista', 'Angela', 'Rose', 'ivssalmadrones6@gmail.com', '09086687599', '2004-02-10', 22, 'Male', 'PMMA', '', '', '$2y$10$jFhTIn80rrZbI2YuGfHSYem0PUmuVEN1hHGfG5tAspG4.WH/Qxrcm', 1, 'active', 'examinee', 0, NULL, '2026-02-19 08:52:50', '2026-02-19 08:53:38'),
(26, '12345', 'dasdasd', 'dasdas', 'dasdasd', 'sdasdasda@gmail.com', '09086687599', '2026-02-11', 0, 'Male', 'PMMA', '', '', '$2y$10$39s06LG6zMOsQvzoQVXYWuZ5MKgqUUcWOfKZ7T626ehss6zCDOFIu', 1, 'active', 'examinee', 0, NULL, '2026-02-19 09:00:04', '2026-02-19 09:02:50'),
(27, '456', 'dfsdfds', 'fdsfsdf', 'fsdfsdf', 'dfsdfdsf@gmail.com', '09086687599', '2026-02-11', 0, 'Male', 'PMMA', '', '', '$2y$10$0BfUAx.3hQrYKGCy2O6ejuq9gToUOWC/bn9Sp9dvafH7YxkABtWZ6', 1, 'incomplete', 'examinee', 0, NULL, '2026-02-19 09:20:11', '2026-02-19 09:20:11'),
(28, '223', 'qwer', 'qwerqwe', 'qwewew', 'laarnialmadrones@gmail.com', '09086687599', '2004-02-19', 21, 'Male', 'PMMA', '', '', '$2y$10$CZ/lDJ/YcQm94qO1BNsxS.DRm3Of8FDyVuskZ6ZFwnTs0wdzrMfJ6', 1, 'incomplete', 'examinee', 0, NULL, '2026-02-19 09:24:51', '2026-02-19 09:26:59'),
(29, '0909', 'John', 'Zuelos', '6', 'cezaralmadrones6@gmail.com', '09086687599', '2004-02-10', 22, 'Male', 'PMMA', '', '', '$2y$10$pYLa/Udj13GRbPA8H/cW2u.OyhZG0Byyq3TK7HoLo7IDBD0DVpF/.', 1, 'active', 'examinee', 0, NULL, '2026-02-19 11:19:02', '2026-02-19 11:39:08'),
(30, '34', 'dsfd', 'fsdfsd', 'fsdfsdf', 'cezaralmadrones@gmailsasa.com', '09086687599', '2004-02-17', 22, 'Male', 'PMMA', '', '', '$2y$10$Adlqy6MyNkVGD5cH2xHuOeoJENgtN.VUGMbukNYWM4ZVsbEO97U1S', 1, 'active', 'examinee', 0, NULL, '2026-02-19 16:00:07', '2026-02-19 16:41:04'),
(31, '2121', 'Sir', 'John', 'Go', 'ivanivan@gmail.com', '09086687599', '2004-02-10', 22, 'Female', 'PMMA', '', '', '$2y$10$dlY5S9zsFtUON0OOpcIcEejOeWl.oOrMjhBlHfeEq0..d1BcGv74K', 1, 'active', 'examinee', 0, NULL, '2026-02-19 16:23:40', '2026-02-19 16:24:14'),
(32, '2222', 'rere', 'rere', 'rere', 'fdfdfd@gmail.com', '09086687599', '2026-02-11', 0, 'Male', 'PMMA', '', '', '$2y$10$QseWW6erujTSQzurxPBkj.KUuMGdQzx7RWEVYOsFdRolEOIm0dYjm', 1, 'active', 'examinee', 0, NULL, '2026-02-19 16:35:05', '2026-02-19 16:35:37'),
(33, '9999', 'Jade', 'Juan', 'A', 'susanalmadrones6@gmail.com', '09086687599', '2004-02-09', 22, 'Male', 'PMMA', '', '', '$2y$10$baXPI3UznRy8tbGWV9b/vecAOx3nVN/BkLd9udcoHdlwtUxYel0tC', 1, 'active', 'examinee', 0, NULL, '2026-02-19 16:45:43', '2026-02-19 16:47:12');

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
(1, 'Manila', 'Luzon'),
(2, 'Cebu', 'Visayas'),
(3, 'Bicol', 'Luzon'),
(4, 'Davao', 'Mindanao'),
(5, 'Iloilo City', 'Visayas'),
(6, 'Quezon City', 'Luzon'),
(7, 'Pasig City', 'Luzon'),
(8, 'Bacolod City', 'Visayas'),
(9, 'Roxas City', 'Visayas'),
(10, 'Digos City', 'Mindanao'),
(11, 'Tagum City', 'Mindanao'),
(12, 'Daet', 'Luzon'),
(13, 'Labo', 'Luzon'),
(14, 'Vinzons', 'Luzon');

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
  ADD KEY `schedule_id` (`schedule_id`);

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
  ADD KEY `idx_action_identifier` (`action_type`,`identifier`),
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
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `examinees`
--
ALTER TABLE `examinees`
  MODIFY `examinee_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `examinee_masterlist`
--
ALTER TABLE `examinee_masterlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `otp_verifications`
--
ALTER TABLE `otp_verifications`
  MODIFY `verification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `reset_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `rate_limits`
--
ALTER TABLE `rate_limits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `schedule_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `venue`
--
ALTER TABLE `venue`
  MODIFY `venue_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

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
  ADD CONSTRAINT `examinees_ibfk_2` FOREIGN KEY (`schedule_id`) REFERENCES `schedules` (`schedule_id`) ON DELETE SET NULL;

--
-- Constraints for table `examinee_masterlist`
--
ALTER TABLE `examinee_masterlist`
  ADD CONSTRAINT `fk_masterlist_used_by` FOREIGN KEY (`used_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

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
