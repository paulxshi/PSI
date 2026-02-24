-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 24, 2026 at 08:15 AM
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
  `role` enum('admin','accountant','examinee','system') DEFAULT NULL,
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
(18, 1, 'Admin', '', 'schedule_changed', 'Admin rescheduled examinee (Permit: 9999) to Manila on 2026-04-04', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'admin', 'info', '{\"user_id\":33,\"test_permit\":\"9999\",\"old_schedule_id\":51,\"new_schedule_id\":32,\"new_date\":\"2026-04-04\",\"venue\":\"Manila\",\"region\":\"Luzon\"}', '2026-02-20 09:39:49'),
(19, 1, NULL, NULL, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'admin', 'info', NULL, '2026-02-20 10:39:57'),
(20, NULL, NULL, 'leeivanalmadrones6@gmail.com', 'login_failed', 'Invalid email or test permit', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'warning', NULL, '2026-02-20 10:40:03'),
(21, 1, 'Admin', 'leeivanalmadrones6@gmail.com', 'login_success', 'Admin logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'admin', 'info', NULL, '2026-02-20 10:41:56'),
(22, 1, NULL, NULL, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'admin', 'info', NULL, '2026-02-20 11:09:01'),
(23, 34, 'jericho.ramos@email.com', 'jericho.ramos@email.com', 'registration_completed', 'New user registered: jericho.ramos@email.com (Permit: TP-2026-0105)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', '{\"user_id\":\"34\",\"test_permit\":\"TP-2026-0105\",\"email\":\"jericho.ramos@email.com\"}', '2026-02-20 11:16:15'),
(24, 34, 'Jericho Ramos', 'jericho.ramos@email.com', 'login_failed', 'Incorrect password', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'warning', NULL, '2026-02-20 11:22:04'),
(25, 34, 'Jericho Ramos', 'jericho.ramos@email.com', 'login_failed', 'Incorrect password', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'warning', NULL, '2026-02-20 11:22:04'),
(26, 34, 'Jericho Ramos', 'jericho.ramos@email.com', 'login_success', 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', NULL, '2026-02-20 11:22:09'),
(27, 1, 'Admin', 'leeivanalmadrones6@gmail.com', 'login_success', 'Admin logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'admin', 'info', NULL, '2026-02-20 11:26:27'),
(28, 1, 'Jericho Ramos', 'jericho.ramos@email.com', 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'admin', 'info', NULL, '2026-02-20 11:27:36'),
(29, 1, 'Admin', 'leeivanalmadrones6@gmail.com', 'login_success', 'Admin logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'admin', 'info', NULL, '2026-02-20 11:27:59'),
(30, 34, 'Jericho Ramos', 'jericho.ramos@email.com', 'login_success', 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', NULL, '2026-02-20 11:28:49'),
(31, 34, 'Jericho Ramos', 'jericho.ramos@email.com', 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', NULL, '2026-02-20 11:28:59'),
(32, 34, 'Jericho Ramos', 'jericho.ramos@email.com', 'login_success', 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', NULL, '2026-02-20 11:29:41'),
(33, 34, 'Jericho Ramos', 'jericho.ramos@email.com', 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', NULL, '2026-02-20 11:29:50'),
(34, 34, NULL, 'jericho.ramos@email.com', 'login_failed', 'Admin login failed - Unauthorized access attempt', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'admin', 'error', NULL, '2026-02-20 11:29:52'),
(35, 1, 'Admin', 'leeivanalmadrones6@gmail.com', 'login_success', 'Admin logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'admin', 'info', NULL, '2026-02-20 11:29:56'),
(36, 1, NULL, NULL, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'admin', 'info', NULL, '2026-02-20 11:31:03'),
(37, NULL, NULL, 'leeivanalmadrones6@gmail.com', 'login_failed', 'Invalid email or test permit', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'warning', NULL, '2026-02-20 11:32:36'),
(38, 34, 'Jericho Ramos', 'jericho.ramos@email.com', 'login_success', 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', NULL, '2026-02-20 11:32:40'),
(39, 34, 'Jericho Ramos', 'jericho.ramos@email.com', 'logout', 'Admin logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', NULL, '2026-02-20 11:41:01'),
(40, 1, 'Admin', 'leeivanalmadrones6@gmail.com', 'login_success', 'Admin logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'admin', 'info', NULL, '2026-02-20 11:41:13'),
(41, NULL, NULL, 'jericho.ramos@email.com', 'login_failed', 'Invalid email or test permit', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'warning', NULL, '2026-02-20 11:41:18'),
(42, 34, 'Jericho Ramos', 'jericho.ramos@email.com', 'login_success', 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', NULL, '2026-02-20 11:41:23'),
(43, 34, 'Jericho Ramos', 'jericho.ramos@email.com', 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', NULL, '2026-02-20 11:41:28'),
(44, 1, 'Admin', 'leeivanalmadrones6@gmail.com', 'login_success', 'Admin logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'admin', 'info', NULL, '2026-02-20 11:43:02'),
(45, 34, 'Jericho Ramos', 'jericho.ramos@email.com', 'login_success', 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', NULL, '2026-02-20 11:43:49'),
(46, 34, 'Jericho Ramos', 'jericho.ramos@email.com', 'logout', 'Admin logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', NULL, '2026-02-20 11:46:47'),
(47, 34, 'Jericho Ramos', 'jericho.ramos@email.com', 'login_success', 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', NULL, '2026-02-20 11:46:54'),
(48, 1, 'Admin', 'leeivanalmadrones6@gmail.com', 'login_success', 'Admin logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'admin', 'info', NULL, '2026-02-20 11:46:59'),
(49, 1, 'Jericho Ramos', 'jericho.ramos@email.com', 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'admin', 'info', NULL, '2026-02-20 11:47:08'),
(50, 34, 'Jericho Ramos', 'jericho.ramos@email.com', 'login_success', 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', NULL, '2026-02-20 13:28:21'),
(51, 34, 'Jericho Ramos', 'jericho.ramos@email.com', 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', NULL, '2026-02-20 13:52:54'),
(52, NULL, NULL, 'jericho.ramos@email.com', 'login_failed', 'Invalid email or test permit', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'warning', NULL, '2026-02-20 13:53:01'),
(53, 35, 'bianca.flores@email.com', 'bianca.flores@email.com', 'registration_completed', 'New user registered: bianca.flores@email.com (Permit: 0000)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', '{\"user_id\":\"35\",\"test_permit\":\"0000\",\"email\":\"bianca.flores@email.com\"}', '2026-02-20 13:53:46'),
(54, 36, 'ethan.navarro@email.com', 'ethan.navarro@email.com', 'registration_completed', 'New user registered: ethan.navarro@email.com (Permit: 7777)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', '{\"user_id\":\"36\",\"test_permit\":\"7777\",\"email\":\"ethan.navarro@email.com\"}', '2026-02-20 13:57:16'),
(55, 34, 'Jericho Ramos', 'jericho.ramos@email.com', 'login_success', 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', NULL, '2026-02-20 14:08:29'),
(56, 34, 'Jericho Ramos', 'jericho.ramos@email.com', 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', NULL, '2026-02-20 14:13:01'),
(57, 37, 'clarisse.bautista@email.com', 'clarisse.bautista@email.com', 'registration_completed', 'New user registered: clarisse.bautista@email.com (Permit: 1111)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', '{\"user_id\":\"37\",\"test_permit\":\"1111\",\"email\":\"clarisse.bautista@email.com\"}', '2026-02-20 14:13:41'),
(58, NULL, NULL, 'jericho.ramos@email.com', 'login_failed', 'Invalid email or test permit', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'warning', NULL, '2026-02-20 14:14:40'),
(59, 34, 'Jericho Ramos', 'jericho.ramos@email.com', 'login_success', 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', NULL, '2026-02-20 14:14:47'),
(60, 34, 'Jericho Ramos', 'jericho.ramos@email.com', 'login_success', 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', NULL, '2026-02-20 15:00:33'),
(61, 34, 'Jericho Ramos', 'jericho.ramos@email.com', 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', NULL, '2026-02-20 15:09:52'),
(62, 34, 'Jericho Ramos', 'jericho.ramos@email.com', 'login_success', 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', NULL, '2026-02-20 15:09:58'),
(63, 34, 'Lee Ivan Ramos', 'jericho.ramos@email.com', 'admin_examinee_updated', 'Examinee updated their profile information', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', NULL, '2026-02-20 15:21:15'),
(64, 38, 'adrian.lopez@email.com', 'adrian.lopez@email.com', 'registration_completed', 'New user registered: adrian.lopez@email.com (Permit: TP-2026-0101)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', '{\"user_id\":\"38\",\"test_permit\":\"TP-2026-0101\",\"email\":\"adrian.lopez@email.com\"}', '2026-02-20 15:55:13'),
(65, NULL, NULL, 'adrian.lopez@email.com', 'login_failed', 'Invalid email or test permit', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'warning', NULL, '2026-02-20 15:56:39'),
(66, 38, 'Adrian Lopez', 'adrian.lopez@email.com', 'login_success', 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', NULL, '2026-02-20 15:56:48'),
(67, 38, 'Adrian Lopez', 'adrian.lopez@email.com', '', 'User updated their profile picture', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', NULL, '2026-02-20 16:04:40'),
(68, 38, 'Lee Ivan Almadrones', 'adrian.lopez@email.com', 'admin_examinee_updated', 'Examinee updated their profile information', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', NULL, '2026-02-20 16:08:26'),
(69, 38, 'Adrian Lopez', 'adrian.lopez@email.com', 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', NULL, '2026-02-20 16:14:15'),
(70, 39, 'sarah.villanueva@example.com', 'sarah.villanueva@example.com', 'registration_completed', 'New user registered: sarah.villanueva@example.com (Permit: 777-8888)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', '{\"user_id\":\"39\",\"test_permit\":\"777-8888\",\"email\":\"sarah.villanueva@example.com\"}', '2026-02-20 16:15:15'),
(71, 39, 'Sarah Villanueva', 'sarah.villanueva@example.com', 'login_success', 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', NULL, '2026-02-20 16:16:12'),
(72, 40, 'Doe@gmail.com', 'Doe@gmail.com', 'registration_completed', 'New user registered: Doe@gmail.com (Permit: 434343)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', '{\"user_id\":\"40\",\"test_permit\":\"434343\",\"email\":\"Doe@gmail.com\"}', '2026-02-20 16:51:02'),
(73, 40, NULL, NULL, 'logout', 'Admin logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'admin', 'info', NULL, '2026-02-20 16:52:15'),
(74, 1, 'Admin', 'leeivanalmadrones6@gmail.com', 'login_success', 'Admin logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'admin', 'info', NULL, '2026-02-22 10:37:24'),
(75, 1, 'Admin', '', 'admin_schedule_created', 'Admin created new schedule for Bagong Silang, Luzon on 2026-08-01', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'admin', 'info', '{\"schedule_id\":\"0\",\"venue\":\"Bagong Silang\",\"region\":\"Luzon\",\"date\":\"2026-08-01\",\"capacity\":20,\"price\":500}', '2026-02-22 10:42:56'),
(76, 41, 'mikejohn@gmail.com', 'mikejohn@gmail.com', 'registration_completed', 'New user registered: mikejohn@gmail.com (Permit: 4444)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', '{\"user_id\":\"41\",\"test_permit\":\"4444\",\"email\":\"mikejohn@gmail.com\"}', '2026-02-22 10:51:58'),
(77, NULL, NULL, 'mikejohn@gmail.com', 'login_failed', 'Invalid email or test permit', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'warning', NULL, '2026-02-22 10:58:43'),
(78, 41, 'John Mike', 'mikejohn@gmail.com', 'login_success', 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', NULL, '2026-02-22 10:58:49'),
(79, 41, 'John Mike', 'mikejohn@gmail.com', 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', NULL, '2026-02-22 11:21:55'),
(80, NULL, NULL, 'ivssalmadrones@gmail.com', 'otp_verified', 'OTP verified successfully for purpose: registration', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', NULL, '2026-02-22 11:33:54'),
(81, 42, 'ivssalmadrones@gmail.com', 'ivssalmadrones@gmail.com', 'registration_completed', 'New user registered: ivssalmadrones@gmail.com (Permit: 101010)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', '{\"user_id\":\"42\",\"test_permit\":\"101010\",\"email\":\"ivssalmadrones@gmail.com\"}', '2026-02-22 11:34:18'),
(82, NULL, NULL, 'leeivanalmadrones6@gmail.com', 'login_failed', 'Invalid email or test permit', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'warning', NULL, '2026-02-22 11:35:56'),
(83, 42, 'Lee Ivan Almadrones', 'ivssalmadrones@gmail.com', 'login_failed', 'Incorrect password', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'warning', NULL, '2026-02-22 11:36:41'),
(84, 42, 'Lee Ivan Almadrones', 'ivssalmadrones@gmail.com', 'login_success', 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', NULL, '2026-02-22 11:37:11'),
(85, 42, 'Lee Ivan Almadrones', 'ivssalmadrones@gmail.com', 'login_success', 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', NULL, '2026-02-22 11:37:12'),
(86, 42, 'Lee Ivan Almadrones', 'ivssalmadrones@gmail.com', 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', NULL, '2026-02-22 11:48:11'),
(87, 42, 'Lee Ivan Almadrones', 'ivssalmadrones@gmail.com', 'logout', 'Admin logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', NULL, '2026-02-22 12:39:40'),
(88, 1, 'Admin', 'leeivanalmadrones6@gmail.com', 'login_success', 'Admin logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'admin', 'info', NULL, '2026-02-22 17:44:13'),
(89, 42, NULL, 'ivssalmadrones@gmail.com', 'password_reset', 'User reset their password via email link', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', NULL, '2026-02-22 17:56:41'),
(90, 1, NULL, NULL, 'logout', 'Admin logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'admin', 'info', NULL, '2026-02-22 18:00:23'),
(91, 1, 'Admin', '', 'admin_schedule_deleted', 'Admin deleted schedule #23', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'admin', 'warning', '{\"schedule_id\":23}', '2026-02-22 18:07:19'),
(92, 1, 'Admin', 'leeivanalmadrones6@gmail.com', 'login_success', 'Admin logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'admin', 'info', NULL, '2026-02-22 18:25:57'),
(93, 1, 'Admin', '', 'admin_schedule_created', 'Admin created new schedule for Labo, Mindanao on 2026-09-01', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'admin', 'info', '{\"schedule_id\":\"0\",\"venue\":\"Labo\",\"region\":\"Mindanao\",\"date\":\"2026-09-01\",\"capacity\":10,\"price\":100}', '2026-02-22 18:31:14'),
(94, NULL, NULL, 'ivssalmadrones@gmail.com', 'otp_verified', 'OTP verified successfully for purpose: registration', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', NULL, '2026-02-22 18:42:16'),
(95, 43, 'ivssalmadrones@gmail.com', 'ivssalmadrones@gmail.com', 'registration_completed', 'New user registered: ivssalmadrones@gmail.com (Permit: 1100)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', '{\"user_id\":\"43\",\"test_permit\":\"1100\",\"email\":\"ivssalmadrones@gmail.com\"}', '2026-02-22 18:42:26'),
(96, 43, 'Boi Jen', 'ivssalmadrones@gmail.com', 'login_success', 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', NULL, '2026-02-22 18:44:59'),
(97, 1, 'Admin', 'leeivanalmadrones6@gmail.com', 'login_success', 'Admin logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'admin', 'info', NULL, '2026-02-22 18:58:28'),
(98, NULL, NULL, 'ivssalmadrones@gmail.com', 'otp_verified', 'OTP verified successfully for purpose: registration', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', NULL, '2026-02-22 19:45:25'),
(99, 44, 'ivssalmadrones@gmail.com', 'ivssalmadrones@gmail.com', 'registration_completed', 'New user registered: ivssalmadrones@gmail.com (Permit: 1129)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', '{\"user_id\":\"44\",\"test_permit\":\"1129\",\"email\":\"ivssalmadrones@gmail.com\"}', '2026-02-22 19:45:37'),
(100, NULL, NULL, 'leeivanalmadrones6@gmail.com', 'login_failed', 'Invalid email or test permit', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'warning', NULL, '2026-02-22 19:52:53'),
(101, 1, NULL, 'leeivanalmadrones6@gmail.com', 'login_failed', 'Admin login failed - Incorrect password', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'admin', 'warning', NULL, '2026-02-22 19:53:15'),
(102, NULL, NULL, 'sSAsa@GMAIL.COM', 'login_failed', 'Admin login failed - Email not found', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'admin', 'warning', NULL, '2026-02-22 19:53:31'),
(103, 1, 'Admin', 'leeivanalmadrones6@gmail.com', 'login_success', 'Admin logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'admin', 'info', NULL, '2026-02-22 19:53:37'),
(104, 44, 'Lee Ivan', 'ivssalmadrones@gmail.com', 'login_success', 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', NULL, '2026-02-22 19:54:10'),
(105, 1, 'Admin', 'leeivanalmadrones6@gmail.com', 'login_success', 'Admin logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'admin', 'info', NULL, '2026-02-23 08:34:13'),
(106, 1, NULL, NULL, 'logout', 'Admin logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'admin', 'info', NULL, '2026-02-23 08:34:38'),
(107, 1, 'Admin', 'leeivanalmadrones6@gmail.com', 'login_success', 'Admin logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'admin', 'info', NULL, '2026-02-23 08:34:40'),
(108, NULL, NULL, 'leeivanalmadrones2004@gmail.com', 'otp_verified', 'OTP verified successfully for purpose: registration', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', NULL, '2026-02-23 08:38:04'),
(109, 45, 'leeivanalmadrones2004@gmail.com', 'leeivanalmadrones2004@gmail.com', 'registration_completed', 'New user registered: leeivanalmadrones2004@gmail.com (Permit: PMMA-1122)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', '{\"user_id\":\"45\",\"test_permit\":\"PMMA-1122\",\"email\":\"leeivanalmadrones2004@gmail.com\"}', '2026-02-23 08:38:20'),
(110, NULL, NULL, 'leeivanalmadrones2004@gmail.com', 'otp_verified', 'OTP verified successfully for purpose: registration', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', NULL, '2026-02-23 08:44:58'),
(111, 46, 'leeivanalmadrones2004@gmail.com', 'leeivanalmadrones2004@gmail.com', 'registration_completed', 'New user registered: leeivanalmadrones2004@gmail.com (Permit: PMMA-01)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', '{\"user_id\":\"46\",\"test_permit\":\"PMMA-01\",\"email\":\"leeivanalmadrones2004@gmail.com\"}', '2026-02-23 08:45:07'),
(112, 46, 'Admin', '', 'admin_schedule_deleted', 'Admin deleted schedule #30', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'admin', 'warning', '{\"schedule_id\":30}', '2026-02-23 08:47:26'),
(113, 46, 'Admin', '', 'admin_schedule_deleted', 'Admin deleted schedule #24', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'admin', 'warning', '{\"schedule_id\":24}', '2026-02-23 08:47:29'),
(114, 46, 'Admin', '', 'admin_schedule_deleted', 'Admin deleted schedule #45', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'admin', 'warning', '{\"schedule_id\":45}', '2026-02-23 08:47:36'),
(115, NULL, NULL, 'leeivanalmadrones2004@gmail.com', 'otp_verified', 'OTP verified successfully for purpose: registration', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', NULL, '2026-02-23 08:54:32'),
(116, 47, 'leeivanalmadrones2004@gmail.com', 'leeivanalmadrones2004@gmail.com', 'registration_completed', 'New user registered: leeivanalmadrones2004@gmail.com (Permit: PMMA-02)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', '{\"user_id\":\"47\",\"test_permit\":\"PMMA-02\",\"email\":\"leeivanalmadrones2004@gmail.com\"}', '2026-02-23 08:54:39'),
(117, NULL, NULL, 'leeivanalmadrones2004@gmail.com', 'otp_verified', 'OTP verified successfully for purpose: registration', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', NULL, '2026-02-23 09:11:08'),
(118, 48, 'leeivanalmadrones2004@gmail.com', 'leeivanalmadrones2004@gmail.com', 'registration_completed', 'New user registered: leeivanalmadrones2004@gmail.com (Permit: PMMA-03)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', '{\"user_id\":\"48\",\"test_permit\":\"PMMA-03\",\"email\":\"leeivanalmadrones2004@gmail.com\"}', '2026-02-23 09:11:16'),
(119, NULL, NULL, 'leeivanalmadrones2004@gmail.com', 'otp_verified', 'OTP verified successfully for purpose: registration', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', NULL, '2026-02-23 09:57:21'),
(120, 49, 'leeivanalmadrones2004@gmail.com', 'leeivanalmadrones2004@gmail.com', 'registration_completed', 'New user registered: leeivanalmadrones2004@gmail.com (Permit: PMMA-04)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', '{\"user_id\":\"49\",\"test_permit\":\"PMMA-04\",\"email\":\"leeivanalmadrones2004@gmail.com\"}', '2026-02-23 09:57:28'),
(121, NULL, NULL, 'leeivanalmadrones2004@gmail.com', 'otp_verified', 'OTP verified successfully for purpose: registration', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', NULL, '2026-02-23 10:20:24'),
(122, 50, 'leeivanalmadrones2004@gmail.com', 'leeivanalmadrones2004@gmail.com', 'registration_completed', 'New user registered: leeivanalmadrones2004@gmail.com (Permit: PMMA-05)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', '{\"user_id\":\"50\",\"test_permit\":\"PMMA-05\",\"email\":\"leeivanalmadrones2004@gmail.com\"}', '2026-02-23 10:20:34'),
(123, NULL, NULL, 'leeivanalmadrones2004@gmail.com', 'otp_verified', 'OTP verified successfully for purpose: registration', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', NULL, '2026-02-23 10:30:31'),
(124, 51, 'leeivanalmadrones2004@gmail.com', 'leeivanalmadrones2004@gmail.com', 'registration_completed', 'New user registered: leeivanalmadrones2004@gmail.com (Permit: PMMA-06)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', '{\"user_id\":\"51\",\"test_permit\":\"PMMA-06\",\"email\":\"leeivanalmadrones2004@gmail.com\"}', '2026-02-23 10:30:39'),
(125, NULL, NULL, 'leeivanalmadrones2004@gmail.com', 'otp_verified', 'OTP verified successfully for purpose: registration', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', NULL, '2026-02-23 10:48:58'),
(126, 52, 'leeivanalmadrones2004@gmail.com', 'leeivanalmadrones2004@gmail.com', 'registration_completed', 'New user registered: leeivanalmadrones2004@gmail.com (Permit: PMMA-07)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', '{\"user_id\":\"52\",\"test_permit\":\"PMMA-07\",\"email\":\"leeivanalmadrones2004@gmail.com\"}', '2026-02-23 10:49:06'),
(127, NULL, NULL, 'ivssalmadrones@gmail.com', 'otp_verified', 'OTP verified successfully for purpose: registration', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', NULL, '2026-02-23 11:13:34'),
(128, 53, 'ivssalmadrones@gmail.com', 'ivssalmadrones@gmail.com', 'registration_completed', 'New user registered: ivssalmadrones@gmail.com (Permit: PMAA-08)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', '{\"user_id\":\"53\",\"test_permit\":\"PMAA-08\",\"email\":\"ivssalmadrones@gmail.com\"}', '2026-02-23 11:13:41'),
(129, NULL, NULL, 'ivssalmadrones@gmail.com', 'otp_verified', 'OTP verified successfully for purpose: registration', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', NULL, '2026-02-23 11:44:16'),
(130, 54, 'ivssalmadrones@gmail.com', 'ivssalmadrones@gmail.com', 'registration_completed', 'New user registered: ivssalmadrones@gmail.com (Permit: PMMA-010)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', '{\"user_id\":\"54\",\"test_permit\":\"PMMA-010\",\"email\":\"ivssalmadrones@gmail.com\"}', '2026-02-23 11:44:40'),
(131, 54, NULL, NULL, 'logout', 'Admin logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'admin', 'info', NULL, '2026-02-23 11:54:09'),
(132, 1, 'Admin', 'leeivanalmadrones6@gmail.com', 'login_success', 'Admin logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'admin', 'info', NULL, '2026-02-23 11:55:01'),
(133, 55, NULL, 'verzosajanna7@gmail.com', 'login_failed', 'Admin login failed - Unauthorized access attempt', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'admin', 'error', NULL, '2026-02-23 13:42:41'),
(134, 55, NULL, 'verzosajanna7@gmail.com', 'login_failed', 'Admin login failed - Unauthorized access attempt', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'admin', 'error', NULL, '2026-02-23 13:42:54'),
(135, 55, 'Accountant', 'verzosajanna7@gmail.com', 'login_success', 'Accountant logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'admin', 'info', NULL, '2026-02-23 13:47:32'),
(136, 55, NULL, 'verzosajanna7@gmail.com', 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '', 'info', NULL, '2026-02-23 14:03:09'),
(137, 55, NULL, 'verzosajanna7@gmail.com', 'logout', 'Accountant logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '', 'info', NULL, '2026-02-23 14:24:10'),
(138, 55, 'Accountant', 'verzosajanna7@gmail.com', 'login_success', 'Accountant logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'admin', 'info', NULL, '2026-02-23 14:24:33'),
(139, 55, NULL, 'verzosajanna7@gmail.com', 'logout', 'Accountant logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '', 'info', NULL, '2026-02-23 14:24:51'),
(140, 55, NULL, 'verzosajanna7@gmail.com', 'logout', 'Admin logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '', 'info', NULL, '2026-02-23 14:31:11'),
(141, 55, 'Accountant', 'verzosajanna7@gmail.com', 'login_success', 'Accountant logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'admin', 'info', NULL, '2026-02-23 14:31:14'),
(142, 55, NULL, 'verzosajanna7@gmail.com', 'logout', 'Admin logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '', 'info', NULL, '2026-02-23 14:31:21'),
(143, 1, 'Admin', 'leeivanalmadrones6@gmail.com', 'login_success', 'Admin logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'admin', 'info', NULL, '2026-02-23 14:31:24'),
(144, 1, NULL, 'leeivanalmadrones6@gmail.com', 'logout', 'Admin logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'admin', 'info', NULL, '2026-02-23 14:32:16'),
(145, 54, NULL, 'ivssalmadrones@gmail.com', 'login_failed', 'Login failed - Unauthorized access attempt', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'admin', 'error', NULL, '2026-02-23 14:35:01'),
(146, 55, 'Accountant', 'verzosajanna7@gmail.com', 'login_success', 'Accountant logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'admin', 'info', NULL, '2026-02-23 14:35:06'),
(147, 55, NULL, 'verzosajanna7@gmail.com', 'logout', 'Accountant logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '', 'info', NULL, '2026-02-23 14:41:50'),
(148, 55, 'Accountant', 'verzosajanna7@gmail.com', 'login_success', 'Accountant logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'admin', 'info', NULL, '2026-02-23 14:41:52'),
(149, 1, 'Admin', 'leeivanalmadrones6@gmail.com', 'login_success', 'Admin logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'admin', 'info', NULL, '2026-02-23 14:42:29'),
(150, NULL, NULL, 'ivssalmadrones@gmail.com', 'otp_verified', 'OTP verified successfully for purpose: registration', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', NULL, '2026-02-23 14:51:37'),
(151, 56, 'ivssalmadrones@gmail.com', 'ivssalmadrones@gmail.com', 'registration_completed', 'New user registered: ivssalmadrones@gmail.com (Permit: PMMA-0978)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', '{\"user_id\":\"56\",\"test_permit\":\"PMMA-0978\",\"email\":\"ivssalmadrones@gmail.com\"}', '2026-02-23 14:51:45'),
(152, 56, 'Jay Lee Ivan', 'ivssalmadrones@gmail.com', 'login_success', 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', NULL, '2026-02-23 14:52:58'),
(153, 55, 'Maria Dela Cruz', 'verzosajanna7@gmail.com', 'login_success', 'Accountant logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '', 'info', NULL, '2026-02-23 15:11:54'),
(154, 1, 'Admin', 'leeivanalmadrones6@gmail.com', 'login_success', 'Admin logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'admin', 'info', NULL, '2026-02-23 15:15:17'),
(155, 1, 'Jay Lee Ivan', 'ivssalmadrones@gmail.com', 'logout', 'Admin logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'admin', 'info', NULL, '2026-02-23 15:17:18'),
(156, 1, 'Admin', 'leeivanalmadrones6@gmail.com', 'login_success', 'Admin logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'admin', 'info', NULL, '2026-02-23 15:17:20'),
(157, 55, 'Maria Dela Cruz', 'verzosajanna7@gmail.com', 'logout', 'Accountant logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '', 'info', NULL, '2026-02-23 15:18:51'),
(158, 1, NULL, 'leeivanalmadrones6@gmail.com', 'login_failed', 'Accountant login failed - Unauthorized access attempt', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'admin', 'error', NULL, '2026-02-23 15:18:53'),
(159, 55, 'Maria Dela Cruz', 'verzosajanna7@gmail.com', 'login_success', 'Accountant logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '', 'info', NULL, '2026-02-23 15:18:58'),
(160, 1, NULL, NULL, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'admin', 'info', NULL, '2026-02-23 15:22:50'),
(161, 1, NULL, NULL, 'logout', 'Admin logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'admin', 'info', NULL, '2026-02-23 16:26:56'),
(162, 55, NULL, 'verzosajanna7@gmail.com', 'login_failed', 'Admin login failed - Unauthorized access attempt', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'admin', 'error', NULL, '2026-02-23 16:26:58'),
(163, 1, 'Admin', 'leeivanalmadrones6@gmail.com', 'login_success', 'Admin logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'admin', 'info', NULL, '2026-02-23 16:27:01');
INSERT INTO `activity_logs` (`log_id`, `user_id`, `username`, `email`, `activity_type`, `description`, `ip_address`, `user_agent`, `role`, `severity`, `metadata`, `created_at`) VALUES
(164, 55, 'Maria Dela Cruz', 'verzosajanna7@gmail.com', 'logout', 'Accountant logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '', 'info', NULL, '2026-02-23 16:27:13'),
(165, 1, NULL, 'leeivanalmadrones6@gmail.com', 'login_failed', 'Accountant login failed - Unauthorized access attempt', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'admin', 'error', NULL, '2026-02-23 16:27:15'),
(166, 55, 'Maria Dela Cruz', 'verzosajanna7@gmail.com', 'login_success', 'Accountant logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '', 'info', NULL, '2026-02-23 16:27:19'),
(167, 55, 'Maria Dela Cruz', 'verzosajanna7@gmail.com', 'logout', 'Accountant logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '', 'info', NULL, '2026-02-23 16:27:27'),
(168, 55, 'Maria Dela Cruz', 'verzosajanna7@gmail.com', 'login_success', 'Accountant logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '', 'info', NULL, '2026-02-23 16:27:44'),
(169, 55, 'Maria Dela Cruz', 'verzosajanna7@gmail.com', 'logout', 'Accountant logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '', 'info', NULL, '2026-02-23 16:27:51'),
(170, 55, 'Maria Dela Cruz', 'verzosajanna7@gmail.com', 'login_success', 'Accountant logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '', 'info', NULL, '2026-02-23 16:28:04'),
(171, 55, 'Maria Dela Cruz', 'verzosajanna7@gmail.com', 'logout', 'Accountant logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '', 'info', NULL, '2026-02-23 16:48:25'),
(172, 55, 'Maria Dela Cruz', 'verzosajanna7@gmail.com', 'login_success', 'Accountant logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '', 'info', NULL, '2026-02-23 16:49:05'),
(173, 1, NULL, NULL, 'logout', 'Admin logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'admin', 'info', NULL, '2026-02-23 18:51:43'),
(174, 55, 'Maria Dela Cruz', 'verzosajanna7@gmail.com', 'logout', 'Accountant logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '', 'info', NULL, '2026-02-23 18:51:49'),
(175, 1, 'Admin', 'leeivanalmadrones6@gmail.com', 'login_success', 'Admin logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'admin', 'info', NULL, '2026-02-24 08:28:08'),
(176, 54, 'Don Sir', 'dasdasdas@gmail.com', 'login_success', 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', NULL, '2026-02-24 09:34:17'),
(177, 54, 'Admin', 'dasdasdas@gmail.com', 'admin_examinee_updated', 'Admin updated examinee #30 status to Completed', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'admin', 'info', '{\"examinee_id\":30,\"new_status\":\"Completed\",\"action\":\"complete\"}', '2026-02-24 10:06:25'),
(178, 55, 'Maria Dela Cruz', 'verzosajanna7@gmail.com', 'login_success', 'Accountant logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '', 'info', NULL, '2026-02-24 11:37:52'),
(179, 55, 'Maria Dela Cruz', 'verzosajanna7@gmail.com', 'login_success', 'Accountant logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '', 'info', NULL, '2026-02-24 11:41:34'),
(180, 54, 'Don Sir', 'dasdasdas@gmail.com', 'logout', 'Admin logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'info', NULL, '2026-02-24 13:54:22'),
(181, 55, 'Maria Dela Cruz', 'verzosajanna7@gmail.com', 'login_success', 'Accountant logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '', 'info', NULL, '2026-02-24 13:54:25'),
(182, 55, 'Maria Dela Cruz', 'verzosajanna7@gmail.com', 'logout', 'Accountant logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '', 'info', NULL, '2026-02-24 14:01:42'),
(183, 55, 'Maria Dela Cruz', 'verzosajanna7@gmail.com', 'login_success', 'Accountant logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '', 'info', NULL, '2026-02-24 14:05:27'),
(184, 55, 'Maria Dela Cruz', 'verzosajanna7@gmail.com', 'logout', 'Accountant logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '', 'info', NULL, '2026-02-24 14:20:14'),
(185, 55, 'Maria Dela Cruz', 'verzosajanna7@gmail.com', 'login_success', 'Accountant logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '', 'info', NULL, '2026-02-24 14:28:13'),
(186, 55, NULL, 'verzosajanna7@gmail.com', 'login_failed', 'Admin login failed - Unauthorized access attempt', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'admin', 'error', NULL, '2026-02-24 14:29:17'),
(187, 1, 'Admin', 'leeivanalmadrones6@gmail.com', 'login_success', 'Admin logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'admin', 'info', NULL, '2026-02-24 14:29:24'),
(188, 50, 'Don Sir', 'don@gmail.com', 'login_failed', 'Incorrect password', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'warning', NULL, '2026-02-24 14:43:01'),
(189, 50, 'Don Sir', 'don@gmail.com', 'login_failed', 'Incorrect password', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'warning', NULL, '2026-02-24 14:43:03'),
(190, 50, 'Don Sir', 'don@gmail.com', 'login_failed', 'Incorrect password', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'warning', NULL, '2026-02-24 14:43:04'),
(191, 50, 'Don Sir', 'don@gmail.com', 'login_failed', 'Incorrect password', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'warning', NULL, '2026-02-24 14:43:08'),
(192, 50, 'Don Sir', 'don@gmail.com', 'login_failed', 'Incorrect password', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'warning', NULL, '2026-02-24 14:43:10'),
(193, 50, 'Don Sir', 'don@gmail.com', 'login_failed', 'Incorrect password', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'warning', NULL, '2026-02-24 14:43:11'),
(194, 50, 'Don Sir', 'don@gmail.com', 'login_failed', 'Incorrect password', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'warning', NULL, '2026-02-24 14:43:12'),
(195, 50, 'Don Sir', 'don@gmail.com', 'login_failed', 'Incorrect password', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'examinee', 'warning', NULL, '2026-02-24 14:43:14'),
(196, 55, 'Maria Dela Cruz', 'verzosajanna7@gmail.com', 'logout', 'Accountant logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '', 'info', NULL, '2026-02-24 15:09:30'),
(197, 1, NULL, 'leeivanalmadrones6@gmail.com', 'login_failed', 'Accountant login failed - Unauthorized access attempt', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'admin', 'error', NULL, '2026-02-24 15:09:32'),
(198, 55, 'Maria Dela Cruz', 'verzosajanna7@gmail.com', 'login_success', 'Accountant logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '', 'info', NULL, '2026-02-24 15:09:38'),
(199, 55, 'Maria Dela Cruz', 'verzosajanna7@gmail.com', 'logout', 'Accountant logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'accountant', 'info', NULL, '2026-02-24 15:14:03'),
(200, 55, 'Maria Dela Cruz', 'verzosajanna7@gmail.com', 'login_success', 'Accountant logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'accountant', 'info', NULL, '2026-02-24 15:14:04');

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
(26, 27, '456', 49, 'Scheduled', '2026-02-19 09:20:11', '2026-02-20 10:21:32', 'Registered', '2026-02-18 10:21:17'),
(27, 28, '223', NULL, 'Pending', '2026-02-19 09:24:51', '2026-02-19 09:24:51', NULL, NULL),
(28, 29, '0909', 33, 'Scheduled', '2026-02-19 11:19:02', '2026-02-19 14:44:10', 'Completed', NULL),
(29, 30, '34', 52, 'Scheduled', '2026-02-19 16:00:07', '2026-02-19 16:17:12', 'Registered', NULL),
(30, 31, '2121', 53, 'Scheduled', '2026-02-19 16:23:40', '2026-02-24 10:06:25', 'Completed', '2026-02-24 10:06:25'),
(31, 32, '2222', 48, 'Scheduled', '2026-02-19 16:35:05', '2026-02-20 10:22:46', 'Completed', '2026-02-03 10:22:43'),
(32, 33, '9999', 32, 'Scheduled', '2026-02-19 16:45:43', '2026-02-20 09:39:49', 'Registered', NULL),
(33, 34, 'TP-2026-0105', 48, 'Scheduled', '2026-02-20 11:16:15', '2026-02-20 11:20:56', 'Registered', NULL),
(34, 35, '0000', 51, 'Scheduled', '2026-02-20 13:53:46', '2026-02-20 13:55:41', 'Registered', NULL),
(35, 36, '7777', 47, 'Scheduled', '2026-02-20 13:57:16', '2026-02-20 13:58:07', 'Registered', NULL),
(36, 37, '1111', 54, 'Scheduled', '2026-02-20 14:13:41', '2026-02-20 14:14:32', 'Registered', NULL),
(37, 38, 'TP-2026-0101', 53, 'Scheduled', '2026-02-20 15:55:13', '2026-02-20 15:56:11', 'Registered', NULL),
(38, 39, '777-8888', 52, 'Scheduled', '2026-02-20 16:15:15', '2026-02-20 16:15:54', 'Registered', NULL),
(39, 40, '434343', 54, 'Scheduled', '2026-02-20 16:51:02', '2026-02-20 16:52:04', 'Registered', NULL),
(40, 41, '4444', 55, 'Scheduled', '2026-02-22 10:51:58', '2026-02-22 10:55:00', 'Registered', NULL),
(41, 42, '101010', 55, 'Scheduled', '2026-02-22 11:34:18', '2026-02-22 11:35:20', 'Registered', NULL),
(42, 43, '1100', 56, 'Scheduled', '2026-02-22 18:42:26', '2026-02-22 18:43:51', 'Registered', NULL),
(43, 44, '1129', 56, 'Scheduled', '2026-02-22 19:45:37', '2026-02-22 19:48:06', 'Registered', NULL),
(44, 45, 'PMMA-1122', NULL, 'Pending', '2026-02-23 08:38:20', '2026-02-23 08:38:20', NULL, NULL),
(45, 46, 'PMMA-01', 51, 'Awaiting Payment', '2026-02-23 08:45:07', '2026-02-23 08:48:48', NULL, NULL),
(46, 47, 'PMMA-02', 48, 'Scheduled', '2026-02-23 08:54:39', '2026-02-23 09:03:17', 'Registered', NULL),
(47, 48, 'PMMA-03', 55, 'Scheduled', '2026-02-23 09:11:16', '2026-02-23 09:52:06', 'Registered', NULL),
(48, 49, 'PMMA-04', 48, 'Scheduled', '2026-02-23 09:57:28', '2026-02-23 10:10:28', 'Registered', NULL),
(49, 50, 'PMMA-05', 48, 'Scheduled', '2026-02-23 10:20:34', '2026-02-23 10:21:51', 'Registered', NULL),
(50, 51, 'PMMA-06', 56, 'Scheduled', '2026-02-23 10:30:39', '2026-02-23 10:31:43', 'Registered', NULL),
(51, 52, 'PMMA-07', 56, 'Scheduled', '2026-02-23 10:49:06', '2026-02-23 10:50:14', 'Registered', NULL),
(52, 53, 'PMAA-08', 56, 'Scheduled', '2026-02-23 11:13:41', '2026-02-23 11:14:16', 'Registered', NULL),
(53, 54, 'PMMA-010', 51, 'Scheduled', '2026-02-23 11:44:40', '2026-02-23 11:51:33', 'Registered', NULL),
(54, 56, 'PMMA-0978', 55, 'Scheduled', '2026-02-23 14:51:45', '2026-02-23 14:52:19', 'Registered', NULL);

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
(11, '123', 'Bautista', 'Angela', 'Rose', 'nlfknasklnfas', 1, 25, '2026-02-18 00:19:18'),
(12, '666-7777', 'Navarro', 'Michael', 'James', 'michael.navarro@example.com', 0, NULL, '2026-02-18 00:19:18'),
(13, '777-8888', 'Villanueva', 'Sarah', 'Joy', 'sarah.villanueva@example.com', 1, 39, '2026-02-18 00:19:18'),
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
(27, '12345', 'dasdasd', 'dasdas', 'dasdasd', 'sdasdasda@gmail.com', 1, 26, '2026-02-19 00:59:26'),
(28, '456', 'dfsdfds', 'fdsfsdf', 'fsdfsdf', 'dfsdfdsf@gmail.com', 1, 27, '2026-02-19 01:17:07'),
(29, '223', 'qwer', 'qwerqwe', 'qwewew', 'juan@gmail.com', 1, 28, '2026-02-19 01:23:32'),
(30, '0909', 'John', 'Zuelos', '6', 'cezaralmadrones6@gmail.com', 1, 29, '2026-02-19 03:17:47'),
(31, '34', 'dsfd', 'fsdfsd', 'fsdfsdf', 'cezaralmadrones@gmail.com', 1, 30, '2026-02-19 07:59:06'),
(32, '2121', 'Sir', 'John', 'Go', 'ivanivan@gmail.com', 1, 31, '2026-02-19 08:22:41'),
(33, '2222', 'rere', 'rere', 'rere', 'fdfdfd@gmail.com', 1, 32, '2026-02-19 08:33:57'),
(34, '9999', 'Jade', 'Juan', 'A', 'susanalmadrones6@gmail.com', 1, 33, '2026-02-19 08:43:10'),
(35, 'TP-2026-0001', 'Santos', 'Miguel', 'Andres', 'miguel.santos@email.com', 0, NULL, '2026-02-20 03:03:41'),
(36, 'TP-2026-0002', 'Cruz', 'Angela', 'Marie', 'angela.cruz@email.com', 0, NULL, '2026-02-20 03:03:41'),
(37, 'TP-2026-0003', 'Reyes', 'John', 'Carlo', 'john.reyes@email.com', 0, NULL, '2026-02-20 03:03:41'),
(38, 'TP-2026-0004', 'Garcia', 'Sophia', 'Isabel', 'sophia.garcia@email.com', 0, NULL, '2026-02-20 03:03:41'),
(39, 'TP-2026-0005', 'Mendoza', 'Daniel', 'Luis', 'daniel.mendoza@email.com', 0, NULL, '2026-02-20 03:03:41'),
(50, 'TP-2026-0101', 'Lopez', 'Adrian', 'Marcelo', 'adrian.lopez@email.com', 1, 38, '2026-02-20 03:06:26'),
(51, '1111', 'Bautista', 'Clarisse', 'Anne', 'clarisse.bautista@email.com', 1, 37, '2026-02-20 03:06:26'),
(52, '7777', 'Navarro', 'Ethan', 'Rafael', 'ethan.navarro@email.com', 1, 36, '2026-02-20 03:06:26'),
(53, '0000', 'Flores', 'Bianca', 'Louise', 'bianca.flores@email.com', 1, 35, '2026-02-20 03:06:26'),
(54, 'TP-2026-0105', 'Ramos', 'Jericho', 'Paul', 'jericho.ramos@email.com', 1, 34, '2026-02-20 03:06:26'),
(55, '434343', 'John', 'Doe', '', 'Doe@gmail.com', 1, 40, '2026-02-20 08:50:09'),
(56, '4444', 'Mike', 'John', 'Paylan', 'mikejohn@gmail.com', 1, 41, '2026-02-22 02:44:31'),
(57, '101010', 'Almadrones', 'Lee Ivan', 'Oliva', 'ljdlkas', 1, 42, '2026-02-22 03:23:52'),
(58, '323232', 'dasdas', 'asdsa', 'dasds', 'das@gmail.com', 0, NULL, '2026-02-22 03:51:43'),
(59, '1100', 'Jen', 'Boi', 'G', 'tertre', 1, 43, '2026-02-22 10:40:10'),
(60, 'TP-2026-001', 'Dela Cruz', 'Juan', 'Santos', 'juan.delacruz@example.com', 0, NULL, '2026-02-22 11:25:35'),
(61, 'TP-2026-002', 'Reyes', 'Maria', 'Lopez', 'maria.reyes@example.com', 0, NULL, '2026-02-22 11:25:35'),
(62, 'TP-2026-003', 'Gonzales', 'Andrei', 'Martinez', 'andrei.gonzales@example.com', 0, NULL, '2026-02-22 11:25:35'),
(63, '1129', 'Ivan', 'Lee', 'Bog', 'lhgfjkj', 1, 44, '2026-02-22 11:43:14'),
(64, 'PMMA', 'Sir', 'Don', 'A.', '54354354', 1, 45, '2026-02-23 00:36:22'),
(65, 'PMMA-01', 'Sir', 'Don', 'A.', '423432', 1, 46, '2026-02-23 00:44:00'),
(66, 'PMMA-02', 'Sir', 'Don', 'A.', 'rtertert', 1, 47, '2026-02-23 00:53:38'),
(67, 'PMMA-03', 'Sir', 'Don', 'A.', 'jhgffhj', 1, 48, '2026-02-23 01:10:33'),
(68, 'PMMA-04', 'Sir', 'Don', 'A.', '8765', 1, 49, '2026-02-23 01:56:13'),
(69, 'PMMA-05', 'Sir', 'Don', 'A.', '6576', 1, 50, '2026-02-23 02:19:38'),
(70, 'PMMA-06', 'Sir', 'Don', 'A.', 'dsddsd', 1, 51, '2026-02-23 02:29:59'),
(71, 'PMMA-07', 'Sir', 'Don', 'A.', '787', 1, 52, '2026-02-23 02:48:04'),
(72, 'PMAA-08', 'Sir', 'Don', 'A.', '56456', 1, 53, '2026-02-23 03:12:20'),
(73, 'PMMA-010', 'Sir', 'Don', 'A.', 'asdasda', 1, 54, '2026-02-23 03:43:36'),
(74, 'PMMA77676', 'Sir Don Par', 'Don Par', 'A.', 'Don@gmail.com', 0, NULL, '2026-02-23 03:59:45'),
(75, 'PMMA-0978', 'Lee Ivan', 'Jay', 'A.', 'dkansdks', 1, 56, '2026-02-23 06:50:48');

-- --------------------------------------------------------

--
-- Table structure for table `faqs`
--

CREATE TABLE `faqs` (
  `faq_id` int(11) NOT NULL,
  `category` varchar(50) NOT NULL,
  `question` varchar(255) NOT NULL,
  `answer` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faqs`
--

INSERT INTO `faqs` (`faq_id`, `category`, `question`, `answer`, `created_at`, `updated_at`) VALUES
(1, 'registered', 'What happens after I complete the online registration?', 'Once your online registration is confirmed, you will receive a confirmation email. You may then Login to generate your Test Permit for verification. Please do not forget to check your scheduled date and venue. Also, wait for further announcements in case there are any updates.', '2026-02-22 03:01:53', '2026-02-23 08:31:44'),
(2, 'registered', 'Do I need to bring anything for the onsite exam?', 'Yes. Bring 2 pencils, eraser, sharpener, your ID, your printed test permit provided after registration. These will be scanned at the entrance to verify your registration.', '2026-02-22 03:01:53', '2026-02-23 08:31:53'),
(3, 'registered', 'How do I download my Test Permit?', 'To download your Test Permit, go to Account Settings > View Test Permit > Generate Test Permit. After clicking, the Test Permit will download to your device. Please print it, as it will serve as your verification before the examination.', '2026-02-23 02:59:17', '2026-02-23 08:31:57'),
(4, 'registered', 'What if I lose my QR code or test permit?', 'You can log in to your account at any time to download your QR code or test permit again. If you encounter issues, contact the support team before your exam date.', '2026-02-22 03:01:53', '2026-02-23 08:32:00'),
(5, 'registered', 'Can I reschedule my onsite examination?', 'Rescheduling depends on availability. Contact the site administrator for assistance.', '2026-02-24 00:41:28', '2026-02-24 00:42:00'),
(7, 'unregistered', 'Who can access this portal?', 'Only officially registered PMMA examination applicants with a valid test permit and registered email address may access this portal.', '2026-02-23 08:34:56', '2026-02-23 08:34:56'),
(8, 'unregistered', 'What credentials are required to log in?\r\n\r\n', 'You must use your registered email address and official test permit number provided during your application process.', '2026-02-23 08:34:56', '2026-02-23 08:34:56'),
(9, 'unregistered', 'Is my personal data secure?\r\n', 'Yes. All personal information is securely stored and processed in compliance with applicable data privacy and protection laws.', '2026-02-23 08:36:14', '2026-02-23 08:36:14'),
(10, 'unregistered', 'I forgot my test permit number. What should I do?', 'If you have forgotten your test permit number, please contact the admissions or examination office using the Contact Us section for verification assistance.', '2026-02-23 08:36:14', '2026-02-23 08:36:14'),
(11, 'unregistered', 'Can I access the portal using a mobile device?\r\n', 'Yes. The portal is fully responsive and can be accessed using mobile phones, tablets, or desktop computers with an internet connection.', '2026-02-23 08:37:34', '2026-02-23 08:37:34'),
(12, 'unregistered', 'Can I update my personal information after registration?\r\n', 'Some personal information may be restricted from editing after submission. For corrections, please coordinate with the examination administration office.', '2026-02-23 08:37:34', '2026-02-23 08:37:34'),
(13, 'unregistered', 'Is there a deadline for portal access?', 'Yes. Portal access is only available within the official examination schedule. Access may be disabled once the examination period has ended.', '2026-02-23 08:38:22', '2026-02-24 00:40:34');

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
(35, 'susanalmadrones6@gmail.com', '145883', 0, 0, 'registration', '2026-02-19 16:45:09', '2026-02-19 09:55:09', NULL),
(36, 'jericho.ramos@email.com', '887841', 0, 0, 'registration', '2026-02-20 11:15:48', '2026-02-20 04:25:48', NULL),
(37, 'bianca.flores@email.com', '981316', 0, 0, 'registration', '2026-02-20 13:53:25', '2026-02-20 07:03:25', NULL),
(38, 'ethan.navarro@email.com', '425712', 0, 0, 'registration', '2026-02-20 13:56:56', '2026-02-20 07:06:56', NULL),
(39, 'clarisse.bautista@email.com', '440440', 0, 0, 'registration', '2026-02-20 14:13:24', '2026-02-20 07:23:24', NULL),
(40, 'adrian.lopez@email.com', '105583', 0, 0, 'registration', '2026-02-20 15:54:45', '2026-02-20 09:04:45', NULL),
(41, 'sarah.villanueva@example.com', '923650', 0, 0, 'registration', '2026-02-20 16:14:58', '2026-02-20 09:24:58', NULL),
(42, 'Doe@gmail.com', '458644', 0, 0, 'registration', '2026-02-20 16:50:38', '2026-02-20 10:00:38', NULL),
(43, 'mikejohn@gmail.com', '264077', 0, 0, 'registration', '2026-02-22 10:47:15', '2026-02-22 03:57:15', NULL),
(44, 'ivssalmadrones@gmail.com', '647705', 0, 0, 'registration', '2026-02-22 11:24:53', '2026-02-22 04:34:53', NULL),
(45, 'ivssalmadrones@gmail.com', '807082', 0, 0, 'registration', '2026-02-22 11:26:33', '2026-02-22 04:36:33', NULL),
(46, 'ivssalmadrones@gmail.com', '117266', 0, 0, 'registration', '2026-02-22 11:27:18', '2026-02-22 04:37:18', NULL),
(47, 'ivssalmadrones@gmail.com', '781796', 0, 1, 'registration', '2026-02-22 11:33:27', '2026-02-22 04:43:27', '2026-02-22 11:33:54'),
(48, 'ivssalmadrones@gmail.com', '960870', 0, 1, 'registration', '2026-02-22 18:41:47', '2026-02-22 11:51:47', '2026-02-22 18:42:16'),
(49, 'ivssalmadrones@gmail.com', '217393', 0, 1, 'registration', '2026-02-22 19:45:04', '2026-02-22 12:55:04', '2026-02-22 19:45:25'),
(50, 'leeivanalmadrones2004@gmail.com', '672478', 0, 1, 'registration', '2026-02-23 08:37:30', '2026-02-23 01:47:30', '2026-02-23 08:38:04'),
(51, 'leeivanalmadrones2004@gmail.com', '705951', 0, 1, 'registration', '2026-02-23 08:44:37', '2026-02-23 01:54:37', '2026-02-23 08:44:58'),
(52, 'leeivanalmadrones2004@gmail.com', '100321', 0, 1, 'registration', '2026-02-23 08:54:16', '2026-02-23 02:04:16', '2026-02-23 08:54:32'),
(53, 'leeivanalmadrones2004@gmail.com', '130250', 0, 1, 'registration', '2026-02-23 09:10:55', '2026-02-23 02:20:55', '2026-02-23 09:11:08'),
(54, 'leeivanalmadrones2004@gmail.com', '369545', 0, 1, 'registration', '2026-02-23 09:57:04', '2026-02-23 03:07:04', '2026-02-23 09:57:21'),
(55, 'leeivanalmadrones2004@gmail.com', '213203', 0, 1, 'registration', '2026-02-23 10:20:07', '2026-02-23 03:30:07', '2026-02-23 10:20:24'),
(56, 'leeivanalmadrones2004@gmail.com', '390440', 0, 1, 'registration', '2026-02-23 10:30:17', '2026-02-23 03:40:17', '2026-02-23 10:30:31'),
(57, 'leeivanalmadrones2004@gmail.com', '981183', 0, 1, 'registration', '2026-02-23 10:48:43', '2026-02-23 03:58:43', '2026-02-23 10:48:58'),
(58, 'ivssalmadrones@gmail.com', '290436', 0, 1, 'registration', '2026-02-23 11:13:12', '2026-02-23 04:23:12', '2026-02-23 11:13:34'),
(59, 'ivssalmadrones@gmail.com', '850216', 0, 1, 'registration', '2026-02-23 11:43:57', '2026-02-23 04:53:57', '2026-02-23 11:44:16'),
(60, 'ivssalmadrones@gmail.com', '862440', 0, 1, 'registration', '2026-02-23 14:51:09', '2026-02-23 08:01:09', '2026-02-23 14:51:37');

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
(3, 33, 'susanalmadrones6@gmail.com', '318179', 0, 1, '2026-02-19 16:46:37', '2026-02-19 09:56:37', '2026-02-19 16:47:12'),
(4, 42, 'ivssalmadrones@gmail.com', '217530', 0, 1, '2026-02-22 17:56:08', '2026-02-22 11:06:08', '2026-02-22 17:56:41');

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
(29, '2026-02-19 16:45:54', 33, 32, 'PAID', '2026-02-19 16:46:14', '2026-02-19 16:45:54', '2026-02-19 16:46:14', '6996cdc153b9bc3482162467', 'PMMA_9999_1771490752', 130.00, 0, '{\"id\":\"6996cdc153b9bc3482162467\",\"external_id\":\"PMMA_9999_1771490752\",\"status\":\"PAID\",\"amount\":130,\"paid_at\":\"2026-02-19T08:46:14.915Z\",\"created\":\"2026-02-19T08:46:14.915Z\",\"updated\":\"2026-02-19T08:46:14.915Z\"}'),
(30, '2026-02-20 11:18:58', 34, 33, 'PAID', '2026-02-20 11:20:56', '2026-02-20 11:18:58', '2026-02-20 11:20:56', '6997d2a05b4b3bdc07b1f3df', 'PMMA_TP-2026-0105_1771557536', 100.00, 0, '{\"id\":\"6997d2a05b4b3bdc07b1f3df\",\"external_id\":\"PMMA_TP-2026-0105_1771557536\",\"status\":\"PAID\",\"amount\":100,\"paid_at\":\"2026-02-20T03:20:56.611Z\",\"created\":\"2026-02-20T03:20:56.613Z\",\"updated\":\"2026-02-20T03:20:56.613Z\"}'),
(31, '2026-02-20 13:55:20', 35, 34, 'PAID', '2026-02-20 13:55:41', '2026-02-20 13:55:20', '2026-02-20 13:55:41', '6997f74753b9bc348217b9be', 'PMMA_0000_1771566919', 130.00, 0, '{\"id\":\"6997f74753b9bc348217b9be\",\"external_id\":\"PMMA_0000_1771566919\",\"status\":\"PAID\",\"amount\":130,\"paid_at\":\"2026-02-20T05:55:41.726Z\",\"created\":\"2026-02-20T05:55:41.727Z\",\"updated\":\"2026-02-20T05:55:41.727Z\"}'),
(32, '2026-02-20 13:57:48', 36, 35, 'PAID', '2026-02-20 13:58:07', '2026-02-20 13:57:48', '2026-02-20 13:58:07', '6997f7da5b4b3bdc07b230e5', 'PMMA_7777_1771567066', 1000.00, 0, '{\"id\":\"6997f7da5b4b3bdc07b230e5\",\"external_id\":\"PMMA_7777_1771567066\",\"status\":\"PAID\",\"amount\":1000,\"paid_at\":\"2026-02-20T05:58:07.580Z\",\"created\":\"2026-02-20T05:58:07.581Z\",\"updated\":\"2026-02-20T05:58:07.581Z\"}'),
(33, '2026-02-20 14:14:04', 37, 36, 'PAID', '2026-02-20 14:14:32', '2026-02-20 14:14:04', '2026-02-20 14:14:32', '6997fbab5b4b3bdc07b23715', 'PMMA_1111_1771568042', 40.00, 0, '{\"id\":\"6997fbab5b4b3bdc07b23715\",\"external_id\":\"PMMA_1111_1771568042\",\"status\":\"PAID\",\"amount\":40,\"paid_at\":\"2026-02-20T06:14:32.798Z\",\"created\":\"2026-02-20T06:14:32.799Z\",\"updated\":\"2026-02-20T06:14:32.799Z\"}'),
(34, '2026-02-20 15:55:49', 38, 37, 'PAID', '2026-02-20 15:56:11', '2026-02-20 15:55:49', '2026-02-20 15:56:11', '6998138453b9bc348217ec05', 'PMMA_TP-2026-0101_1771574147', 160.00, 0, '{\"id\":\"6998138453b9bc348217ec05\",\"external_id\":\"PMMA_TP-2026-0101_1771574147\",\"status\":\"PAID\",\"amount\":160,\"paid_at\":\"2026-02-20T07:56:11.178Z\",\"created\":\"2026-02-20T07:56:11.179Z\",\"updated\":\"2026-02-20T07:56:11.179Z\"}'),
(35, '2026-02-20 16:15:28', 39, 38, 'PAID', '2026-02-20 16:15:54', '2026-02-20 16:15:28', '2026-02-20 16:15:54', '6998181f53b9bc348217f400', 'PMMA_777-8888_1771575327', 130.00, 0, '{\"id\":\"6998181f53b9bc348217f400\",\"external_id\":\"PMMA_777-8888_1771575327\",\"status\":\"PAID\",\"amount\":130,\"paid_at\":\"2026-02-20T08:15:54.403Z\",\"created\":\"2026-02-20T08:15:54.403Z\",\"updated\":\"2026-02-20T08:15:54.403Z\"}'),
(36, '2026-02-20 16:51:43', 40, 39, 'PAID', '2026-02-20 16:52:04', '2026-02-20 16:51:43', '2026-02-20 16:52:04', '6998209d5b4b3bdc07b2785b', 'PMMA_434343_1771577501', 40.00, 0, '{\"id\":\"6998209d5b4b3bdc07b2785b\",\"external_id\":\"PMMA_434343_1771577501\",\"status\":\"PAID\",\"amount\":40,\"paid_at\":\"2026-02-20T08:52:04.032Z\",\"created\":\"2026-02-20T08:52:04.032Z\",\"updated\":\"2026-02-20T08:52:04.032Z\"}'),
(37, '2026-02-22 10:53:08', 41, 40, 'PAID', '2026-02-22 10:55:00', '2026-02-22 10:53:08', '2026-02-22 10:55:00', '699a6f90e9101c255598418c', 'PMMA_4444_1771728786', 500.00, 0, '{\"id\":\"699a6f90e9101c255598418c\",\"external_id\":\"PMMA_4444_1771728786\",\"status\":\"PAID\",\"amount\":500,\"paid_at\":\"2026-02-22T02:55:00.164Z\",\"created\":\"2026-02-22T02:55:00.164Z\",\"updated\":\"2026-02-22T02:55:00.164Z\"}'),
(38, '2026-02-22 11:34:51', 42, 41, 'PAID', '2026-02-22 11:35:20', '2026-02-22 11:34:51', '2026-02-22 11:35:20', '699a7958e9101c2555984ef0', 'PMMA_101010_1771731289', 500.00, 0, '{\"id\":\"699a7958e9101c2555984ef0\",\"external_id\":\"PMMA_101010_1771731289\",\"status\":\"PAID\",\"amount\":500,\"paid_at\":\"2026-02-22T03:35:20.689Z\",\"created\":\"2026-02-22T03:35:20.689Z\",\"updated\":\"2026-02-22T03:35:20.689Z\"}'),
(39, '2026-02-22 18:42:52', 43, 42, 'PAID', '2026-02-22 18:43:51', '2026-02-22 18:42:52', '2026-02-22 18:43:51', '699addaae9101c255598d766', 'PMMA_1100_1771756971', 100.00, 0, '{\"id\":\"699addaae9101c255598d766\",\"external_id\":\"PMMA_1100_1771756971\",\"status\":\"PAID\",\"amount\":100,\"paid_at\":\"2026-02-22T10:43:51.840Z\",\"created\":\"2026-02-22T10:43:51.840Z\",\"updated\":\"2026-02-22T10:43:51.840Z\"}'),
(40, '2026-02-22 19:46:58', 44, 43, 'PAID', '2026-02-22 19:48:06', '2026-02-22 19:46:58', '2026-02-22 19:48:06', '699aecaff8f7a6a8204656d7', 'PMMA_1129_1771760816', 100.00, 0, '{\"id\":\"699aecaff8f7a6a8204656d7\",\"external_id\":\"PMMA_1129_1771760816\",\"status\":\"PAID\",\"amount\":100,\"paid_at\":\"2026-02-22T11:48:06.248Z\",\"created\":\"2026-02-22T11:48:06.248Z\",\"updated\":\"2026-02-22T11:48:06.248Z\"}'),
(41, '2026-02-23 08:56:26', 47, 46, 'PAID', '2026-02-23 09:03:17', '2026-02-23 08:56:26', '2026-02-23 09:03:17', '699ba5b7f8f7a6a820472415', 'PMMA_PMMA-02_1771808185', 100.00, 0, '{\"id\":\"699ba5b7f8f7a6a820472415\",\"external_id\":\"PMMA_PMMA-02_1771808185\",\"status\":\"PAID\",\"amount\":100,\"paid_at\":\"2026-02-23T01:03:17.051Z\",\"created\":\"2026-02-23T01:03:17.061Z\",\"updated\":\"2026-02-23T01:03:17.061Z\"}'),
(42, '2026-02-23 09:11:37', 48, 47, 'PAID', '2026-02-23 09:52:06', '2026-02-23 09:11:37', '2026-02-23 09:52:06', '699ba946f8f7a6a820472b85', 'PMMA_PMMA-03_1771809095', 500.00, 0, '{\"id\":\"699ba946f8f7a6a820472b85\",\"external_id\":\"PMMA_PMMA-03_1771809095\",\"status\":\"PAID\",\"amount\":500,\"paid_at\":\"2026-02-23T01:52:05.968Z\",\"created\":\"2026-02-23T01:52:05.969Z\",\"updated\":\"2026-02-23T01:52:05.969Z\"}'),
(43, '2026-02-23 10:09:49', 49, 48, 'PAID', '2026-02-23 10:10:28', '2026-02-23 10:09:49', '2026-02-23 10:10:28', '699bb6eaf8f7a6a82047493e', 'PMMA_PMMA-04_1771812587', 100.00, 0, '{\"id\":\"699bb6eaf8f7a6a82047493e\",\"external_id\":\"PMMA_PMMA-04_1771812587\",\"status\":\"PAID\",\"amount\":100,\"paid_at\":\"2026-02-23T02:10:28.751Z\",\"created\":\"2026-02-23T02:10:28.751Z\",\"updated\":\"2026-02-23T02:10:28.751Z\"}'),
(44, '2026-02-23 10:21:25', 50, 49, 'PAID', '2026-02-23 10:21:51', '2026-02-23 10:21:25', '2026-02-23 10:21:51', '699bb9a2f8f7a6a820474ff4', 'PMMA_PMMA-05_1771813283', 100.00, 0, '{\"id\":\"699bb9a2f8f7a6a820474ff4\",\"external_id\":\"PMMA_PMMA-05_1771813283\",\"status\":\"PAID\",\"amount\":100,\"paid_at\":\"2026-02-23T02:21:51.522Z\",\"created\":\"2026-02-23T02:21:51.523Z\",\"updated\":\"2026-02-23T02:21:51.523Z\"}'),
(45, '2026-02-23 10:31:00', 51, 50, 'PAID', '2026-02-23 10:31:43', '2026-02-23 10:31:00', '2026-02-23 10:31:43', '699bbbe1e9101c255599e7f9', 'PMMA_PMMA-06_1771813858', 100.00, 0, '{\"id\":\"699bbbe1e9101c255599e7f9\",\"external_id\":\"PMMA_PMMA-06_1771813858\",\"status\":\"PAID\",\"amount\":100,\"paid_at\":\"2026-02-23T02:31:43.629Z\",\"created\":\"2026-02-23T02:31:43.629Z\",\"updated\":\"2026-02-23T02:31:43.629Z\"}'),
(46, '2026-02-23 10:49:21', 52, 51, 'PAID', '2026-02-23 10:50:14', '2026-02-23 10:49:21', '2026-02-23 10:50:14', '699bc02ee9101c255599f1d6', 'PMMA_PMMA-07_1771814959', 100.00, 0, '{\"id\":\"699bc02ee9101c255599f1d6\",\"external_id\":\"PMMA_PMMA-07_1771814959\",\"status\":\"PAID\",\"amount\":100,\"paid_at\":\"2026-02-23T02:50:14.858Z\",\"created\":\"2026-02-23T02:50:14.858Z\",\"updated\":\"2026-02-23T02:50:14.858Z\"}'),
(47, '2026-02-23 11:13:55', 53, 52, 'PAID', '2026-02-23 11:14:16', '2026-02-23 11:13:55', '2026-02-23 11:14:16', '699bc5f0f8f7a6a820476cce', 'PMMA_PMAA-08_1771816433', 100.00, 0, '{\"id\":\"699bc5f0f8f7a6a820476cce\",\"external_id\":\"PMMA_PMAA-08_1771816433\",\"status\":\"PAID\",\"amount\":100,\"paid_at\":\"2026-02-23T03:14:16.335Z\",\"created\":\"2026-02-23T03:14:16.336Z\",\"updated\":\"2026-02-23T03:14:16.336Z\"}'),
(48, '2026-02-23 11:44:57', 54, 53, 'PAID', '2026-02-23 11:45:22', '2026-02-23 11:44:57', '2026-02-23 11:45:22', '699bcd36e9101c25559a1215', 'PMMA_PMMA-010_1771818295', 100.00, 0, '{\"id\":\"699bcd36e9101c25559a1215\",\"external_id\":\"PMMA_PMMA-010_1771818295\",\"status\":\"PAID\",\"amount\":100,\"paid_at\":\"2026-02-23T03:45:22.234Z\",\"created\":\"2026-02-23T03:45:22.238Z\",\"updated\":\"2026-02-23T03:45:22.238Z\"}'),
(49, '2026-02-23 11:51:05', 54, 53, 'PAID', '2026-02-23 11:51:33', '2026-02-23 11:51:05', '2026-02-23 11:51:33', '699bcea6e9101c25559a15ea', 'PMMA_PMMA-010_1771818663', 130.00, 0, '{\"id\":\"699bcea6e9101c25559a15ea\",\"external_id\":\"PMMA_PMMA-010_1771818663\",\"status\":\"PAID\",\"amount\":130,\"paid_at\":\"2026-02-23T03:51:33.564Z\",\"created\":\"2026-02-23T03:51:33.564Z\",\"updated\":\"2026-02-23T03:51:33.564Z\"}'),
(50, '2026-02-23 14:51:59', 56, 54, 'PAID', '2026-02-23 14:52:19', '2026-02-23 14:51:59', '2026-02-23 14:52:19', '699bf90cf8f7a6a82047ef04', 'PMMA_PMMA-0978_1771829517', 500.00, 0, '{\"id\":\"699bf90cf8f7a6a82047ef04\",\"external_id\":\"PMMA_PMMA-0978_1771829517\",\"status\":\"PAID\",\"amount\":500,\"paid_at\":\"2026-02-23T06:52:19.941Z\",\"created\":\"2026-02-23T06:52:19.943Z\",\"updated\":\"2026-02-23T06:52:19.943Z\"}');

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
(26, 'login', '::1', 8, '2026-02-24 14:43:00', '2026-02-24 14:43:14', '2026-02-24 14:58:00');

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
(25, 3, '2026-02-23', 0, 0.00, 'Incoming', 34, 300),
(27, 2, '2026-10-23', 0, 0.00, 'Incoming', 108, 200),
(28, 4, '2026-02-26', 0, 0.00, 'Incoming', 59, 250),
(29, 1, '2026-03-04', 0, 0.00, 'Completed', 38, 200),
(31, 1, '2026-03-12', 0, 0.00, 'Incoming', 209, 400),
(32, 1, '2026-04-04', 0, 0.00, 'Incoming', 41, 250),
(33, 1, '2026-03-04', 0, 0.00, 'Incoming', 1, 3000),
(34, 5, '2026-03-01', 0, 0.00, 'Incoming', 378, 500),
(36, 7, '2026-03-20', 0, 0.00, 'Incoming', 582, 1000),
(37, 8, '2026-03-30', 0, 0.00, 'Incoming', 78, 100),
(42, 3, '2026-03-10', 0, 0.00, 'Incoming', 1, 150),
(44, 3, '2026-02-04', 0, 1500.00, 'Incoming', 1, 150),
(46, 11, '2026-02-19', 0, 1500.00, 'Incoming', 6, 190),
(47, 11, '2026-02-25', 0, 1000.00, 'Incoming', 9, 200),
(48, 3, '2026-02-27', 0, 100.00, 'Incoming', 7, 150),
(49, 3, '2026-02-05', 0, 200.00, 'Incoming', 2, 150),
(50, 3, '2026-02-19', 0, 200.00, 'Incoming', 1, 20),
(51, 12, '2026-05-01', 0, 130.00, 'Incoming', 3, 100),
(52, 12, '2026-05-01', 0, 130.00, 'Incoming', 2, 100),
(53, 14, '2026-05-02', 0, 160.00, 'Incoming', 2, 100),
(54, 11, '2026-05-03', 0, 40.00, 'Incoming', 2, 100),
(55, 15, '2026-08-01', 0, 500.00, 'Incoming', 4, 20),
(56, 16, '2026-09-01', 0, 100.00, 'Incoming', 5, 10);

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
  `last_profile_update` datetime DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `email_verified` tinyint(1) DEFAULT 0,
  `status` enum('incomplete','active','blocked') DEFAULT 'incomplete',
  `role` enum('examinee','admin','accountant') DEFAULT 'examinee',
  `failed_login_attempts` int(11) DEFAULT 0,
  `last_login` datetime DEFAULT NULL,
  `date_of_registration` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `test_permit`, `last_name`, `first_name`, `middle_name`, `email`, `contact_number`, `date_of_birth`, `age`, `gender`, `school`, `address`, `nationality`, `profile_picture`, `last_profile_update`, `password`, `email_verified`, `status`, `role`, `failed_login_attempts`, `last_login`, `date_of_registration`, `updated_at`) VALUES
(1, '-806', 'Almadrones', 'Lee Ivan', NULL, 'leeivanalmadrones6@gmail.com', '09123456789', '1995-01-01', NULL, 'Male', 'System Administrator', 'Philippines', 'Filipino', NULL, NULL, '$2y$10$GPOcbTTUMe20YIg4sFHhd.GOP7s4Qr0h/.SxvaMHOENJU4W9j9OV2', 1, 'active', 'admin', 0, NULL, '2026-02-18 08:16:17', '2026-02-18 14:10:37'),
(2, '22-0828', 'Gabo', 'John Paul', 'Oliva', 'gabo@gmail.com', '0923232323', '2004-02-10', 22, 'Male', 'PMMA', '', '', NULL, NULL, '$2y$10$u69CjToEaHpzDPwlQ1fvDe.PRuYzbmTlWGP9svOfDq9uBjfBywNZS', 1, 'incomplete', 'examinee', 0, NULL, '2026-02-18 08:47:01', '2026-02-18 14:10:37'),
(3, '890-1234', 'Ortiz', 'Brandon', 'Keith', 'keith@gmail.com', '09086687599', '2004-02-18', 21, 'Male', 'PMMA', '', '', NULL, NULL, '$2y$10$YGCdciLlVvkAiE24N8tSFeOifWgakGs9nPEQNFmaZ92ZBM2bzdG0S', 1, 'incomplete', 'examinee', 0, NULL, '2026-02-18 11:38:47', '2026-02-18 14:10:37'),
(4, '789-0123', 'Castro', 'Nicole', 'Faith', 'faith@gmail.com', '09086687599', '2005-02-03', 21, 'Female', 'PMMA', '', '', NULL, NULL, '$2y$10$ZCeoG0R/lpzOTaBnw1HIw.PJZwUNgebxyuTwVoNFk4tQvrqrA36km', 1, 'incomplete', 'examinee', 0, NULL, '2026-02-18 11:57:00', '2026-02-18 14:10:37'),
(5, '678-9012', 'Morales', 'Adrian', 'Joseph', 'morales@gmail.com', '09086687599', '2004-02-12', 22, 'Male', 'PMMA', '', '', NULL, NULL, '$2y$10$wvZc.vf5yN4g6VnvDFfaJOh9MTLm5atOQrJbA63dkIYZxg2C5hcPW', 1, 'incomplete', 'examinee', 0, NULL, '2026-02-18 13:25:59', '2026-02-18 14:10:37'),
(6, '567-8901', 'Torres', 'Jessica', 'Claire', 'claire@gmail.com', '09086687599', '2004-03-11', 21, 'Male', 'PMMA', '', '', NULL, NULL, '$2y$10$MT6Q6Bjm29RaCbucfYBhI.F9imZJcnoqj7RBY8j8NPbxnL88XcuSm', 1, 'incomplete', 'examinee', 0, NULL, '2026-02-18 13:44:08', '2026-02-18 14:10:37'),
(7, '456-7890', 'Mendoza', 'Ryan', 'Patrick', 'ryan@gmail.com', '09086687599', '2004-02-12', 22, 'Male', 'PMMA', '', '', NULL, NULL, '$2y$10$Mu3v5QMx0VGd6pOM.7Qz5.P0Kb9vqDgM4sZvu0P9Z1bRubvIuWy.S', 1, 'incomplete', 'examinee', 0, NULL, '2026-02-18 14:11:40', '2026-02-18 14:14:47'),
(8, '345-6789', 'Flores', 'Karen', 'Grace', 'gdfg', '09086687599', '2004-02-10', 22, 'Male', 'PMMA', '', '', NULL, NULL, '$2y$10$5jhXZa/GRGCJ4drm07jVFOG14dsytEih6yTcbIEp74BPTypB9mF1u', 1, 'incomplete', 'examinee', 0, NULL, '2026-02-18 14:16:21', '2026-02-18 17:01:16'),
(9, '901-2345', 'Diaz', 'Kimberly', 'Louise', 'louissse@gmail.com', '09086687599', '2008-02-07', 18, 'Male', 'PMMA', '', '', NULL, NULL, '$2y$10$yzU7K0dFhnYI9EAkzDdIte6dn9Q0EiVFUBI7.jAGFYMoJUkQLpZYG', 1, 'incomplete', 'examinee', 0, NULL, '2026-02-18 14:54:15', '2026-02-18 15:56:30'),
(10, '123-4567', 'Lim', 'Christine', 'Mae', 'dasd', '09086687599', '2004-02-10', 22, 'Male', 'PMMA', '', '', NULL, NULL, '$2y$10$4qGgx6WXd/5dwN0rbuuk8.AV4XDSLi8o6INUd6WY8I6dIO3pekWDi', 1, 'incomplete', 'examinee', 0, NULL, '2026-02-18 15:58:10', '2026-02-18 16:21:08'),
(11, '000-1111', 'Ramirez', 'Joshua', 'Paul', 'wq', '09086687599', '2004-02-10', 22, 'Male', 'PMMA', '', '', NULL, NULL, '$2y$10$r6DSetxXyG3u3fhBQiedauSUB551ew0AvPoFv.NMfA0Ya2E63G7Li', 1, 'incomplete', 'examinee', 0, NULL, '2026-02-18 16:22:18', '2026-02-18 16:39:09'),
(12, '999-0002', 'Gomez', 'Patricia', 'Anne', 'sdas', '09086687599', '2004-02-17', 22, 'Male', 'PMMA', '', '', NULL, NULL, '$2y$10$lv6axW/P/F98Bf0jUzEXT..Oaen19/5e8jJr42Bw/Cv3Bgfhv1vv2', 1, 'active', 'examinee', 0, NULL, '2026-02-18 16:40:33', '2026-02-18 17:01:12'),
(13, '888-0001', 'Cruz', 'Daniel', 'Lee', 'das', '09086687599', '2004-02-11', 22, 'Male', 'PMMA', '', '', NULL, NULL, '$2y$10$9pmNOO13A/C5CSnXTJagp.hTVW/iTavdgCB.4j4KcTKFW1AcFdUme', 1, 'incomplete', 'examinee', 0, NULL, '2026-02-18 17:06:43', '2026-02-18 17:17:23'),
(14, '112-2334', 'Valdez', 'Christian', 'Mark', 'fsdfds', '09086687599', '2004-02-17', 22, 'Male', 'PMMA', '', '', NULL, NULL, '$2y$10$yXOLNagFLqStXnCzq5PzEeypbDMZXir/q8jK/mI.bGe7teZYCaSVK', 1, 'incomplete', 'examinee', 0, NULL, '2026-02-18 17:21:08', '2026-02-18 17:26:44'),
(15, '223-3445', 'Alvarez', 'Samantha', 'Jane', 'fsdf', '09086687599', '2004-02-10', 22, 'Male', 'PMMA', '', '', NULL, NULL, '$2y$10$Id9JPIuvnlgFd62XA89FBeIcMQac/P/KVvghwjsvj60uteAv9.hq.', 1, 'incomplete', 'examinee', 0, NULL, '2026-02-18 17:27:41', '2026-02-18 17:33:23'),
(16, '2026-102', 'Dela Cruz', 'Maria', 'Jane', 'fsfds', '09086687599', '2005-02-17', 21, 'Male', 'PMMA', '', '', NULL, NULL, '$2y$10$JK6Of2hEdB5RDXS0kr/jvOOodMwBZpi2A.UDqv65qr4wRmG1wJR96', 1, 'active', 'examinee', 0, NULL, '2026-02-18 17:34:34', '2026-02-18 17:43:23'),
(17, '2026-103', 'De Guzman', 'Carlos', 'Miguel', 'uty', '09086687599', '2004-02-12', 22, 'Male', 'PMMA', '', '', NULL, NULL, '$2y$10$58iP4..pAfcXiE98buodJ.avcQ3YK9b7lM0sQw88iDJRs4N1IpojO', 1, 'incomplete', 'examinee', 0, NULL, '2026-02-18 17:45:06', '2026-02-18 17:47:26'),
(18, '2026-104', 'San Juan', 'Ana', 'Sofia', 'dsdas', '09086687599', '2003-02-06', 23, 'Male', 'PMMA', '', '', NULL, NULL, '$2y$10$bnyVx50rXbcFwl.gxhLIT.jZnXdInKL9QNy9yChjnjezZGUU9wk3G', 1, 'active', 'examinee', 0, NULL, '2026-02-18 17:48:15', '2026-02-18 18:20:10'),
(19, '2026-105', 'Dela Peña', 'Pedro', 'Sofia', 'dasdsa', '09086687599', '2005-02-10', 21, 'Male', 'PMMA', '', '', NULL, NULL, '$2y$10$2Ox1nfeuHtKvdzqXFxHriOvaSX1BxUewa4EElNwMN.P3OU3Um9.yu', 1, 'active', 'examinee', 0, NULL, '2026-02-18 18:21:55', '2026-02-18 21:30:57'),
(20, '222-3333', 'Martinez', 'Luis', 'Antonio', 'sdfdf', '09086687599', '2004-02-10', 22, 'Male', 'PMMA', '', '', NULL, NULL, '$2y$10$8LNd5bl9AIAS6JyOrewMOO3oI1Bw3WpXMyUV1.vHyR1rYIjHJ/NaO', 1, 'incomplete', 'examinee', 0, NULL, '2026-02-18 21:36:38', '2026-02-18 21:48:50'),
(21, '333-4444', 'Ramos', 'Isabella', 'Marie', 'werew', '09086687599', '2004-02-10', 22, 'Male', 'PMMA', '', '', NULL, NULL, '$2y$10$if6/bhP18edr9k5rB9dOz.BYVj2O4AZi3r799eM/YMVtD7XkhZMyq', 1, 'active', 'examinee', 0, NULL, '2026-02-18 21:50:13', '2026-02-18 22:14:31'),
(22, '444-5555', 'Gonzales', 'Mark', 'Anthony', 'eeee', '09086687599', '2024-02-13', 2, 'Male', 'PMMA', '', '', NULL, NULL, '$2y$10$v0v90izYgWk.5bmsUH1luORP563GM0LMaF9oZxY79LTLBj396E6ZK', 1, 'incomplete', 'examinee', 0, NULL, '2026-02-18 22:16:43', '2026-02-19 08:05:33'),
(23, '22', 'Gonzales', 'Mark', 'Anthony', 'sasa', '09086687599', '2004-02-10', 22, 'Male', 'PMMA', '', '', NULL, NULL, '$2y$10$gmg6OuRGowPhuED31MrmSuSJ.eGq0NzpOp8KC5Ss8ydVDkn86enFC', 1, 'active', 'examinee', 0, NULL, '2026-02-19 08:15:23', '2026-02-19 08:36:58'),
(24, '555-6666', 'Bautista', 'Angela', 'Rose', 'fsdfd', '09086687599', '2004-02-10', 22, 'Male', 'PMMA', '', '', NULL, NULL, '$2y$10$Ora7UzFOUYxsvSe0EFkR5.cX/MNIZRsNlM8PR62ZCQLwOhojxiLPm', 1, 'active', 'examinee', 0, NULL, '2026-02-19 08:39:11', '2026-02-19 08:51:17'),
(25, '123', 'Bautista', 'Angela', 'Rose', 'uytuy', '09086687599', '2004-02-10', 22, 'Male', 'PMMA', '', '', NULL, NULL, '$2y$10$jFhTIn80rrZbI2YuGfHSYem0PUmuVEN1hHGfG5tAspG4.WH/Qxrcm', 1, 'active', 'examinee', 0, NULL, '2026-02-19 08:52:50', '2026-02-22 18:32:51'),
(26, '12345', 'dasdasd', 'dasdas', 'dasdasd', 'sdasdasda@gmail.com', '09086687599', '2026-02-11', 0, 'Male', 'PMMA', '', '', NULL, NULL, '$2y$10$39s06LG6zMOsQvzoQVXYWuZ5MKgqUUcWOfKZ7T626ehss6zCDOFIu', 1, 'active', 'examinee', 0, NULL, '2026-02-19 09:00:04', '2026-02-19 09:02:50'),
(27, '456', 'dfsdfds', 'fdsfsdf', 'fsdfsdf', 'dfsdfdsf@gmail.com', '09086687599', '2026-02-11', 0, 'Male', 'PMMA', '', '', NULL, NULL, '$2y$10$0BfUAx.3hQrYKGCy2O6ejuq9gToUOWC/bn9Sp9dvafH7YxkABtWZ6', 1, 'incomplete', 'examinee', 0, NULL, '2026-02-19 09:20:11', '2026-02-19 09:20:11'),
(28, '223', 'qwer', 'qwerqwe', 'qwewew', 'laarnialmadrones@gmail.com', '09086687599', '2004-02-19', 21, 'Male', 'PMMA', '', '', NULL, NULL, '$2y$10$CZ/lDJ/YcQm94qO1BNsxS.DRm3Of8FDyVuskZ6ZFwnTs0wdzrMfJ6', 1, 'incomplete', 'examinee', 0, NULL, '2026-02-19 09:24:51', '2026-02-19 09:26:59'),
(29, '0909', 'John', 'Zuelos', '6', 'dasdasdasd', '09086687599', '2004-02-10', 22, 'Male', 'PMMA', '', '', NULL, NULL, '$2y$10$pYLa/Udj13GRbPA8H/cW2u.OyhZG0Byyq3TK7HoLo7IDBD0DVpF/.', 1, 'active', 'examinee', 0, NULL, '2026-02-19 11:19:02', '2026-02-22 11:21:24'),
(30, '34', 'dsfd', 'fsdfsd', 'fsdfsdf', 'dasdasfsfdsfdsfds', '09086687599', '2004-02-17', 22, 'Male', 'PMMA', '', '', NULL, NULL, '$2y$10$Adlqy6MyNkVGD5cH2xHuOeoJENgtN.VUGMbukNYWM4ZVsbEO97U1S', 1, 'active', 'examinee', 0, NULL, '2026-02-19 16:00:07', '2026-02-22 11:21:30'),
(31, '2121', 'Sir', 'John', 'Go', 'ivanivan@gmail.com', '09086687599', '2004-02-10', 22, 'Female', 'PMMA', '', '', NULL, NULL, '$2y$10$dlY5S9zsFtUON0OOpcIcEejOeWl.oOrMjhBlHfeEq0..d1BcGv74K', 1, 'active', 'examinee', 0, NULL, '2026-02-19 16:23:40', '2026-02-19 16:24:14'),
(32, '2222', 'rere', 'rere', 'rere', 'fdfdfd@gmail.com', '09086687599', '2026-02-11', 0, 'Male', 'PMMA', '', '', NULL, NULL, '$2y$10$QseWW6erujTSQzurxPBkj.KUuMGdQzx7RWEVYOsFdRolEOIm0dYjm', 1, 'active', 'examinee', 0, NULL, '2026-02-19 16:35:05', '2026-02-19 16:35:37'),
(33, '9999', 'Jade', 'Juan', 'A', 'gfsdasfsf', '09086687599', '2004-02-09', 22, 'Male', 'PMMA', '', '', NULL, NULL, '$2y$10$baXPI3UznRy8tbGWV9b/vecAOx3nVN/BkLd9udcoHdlwtUxYel0tC', 1, 'active', 'examinee', 0, NULL, '2026-02-19 16:45:43', '2026-02-22 11:21:48'),
(34, '8888', 'Ramos', 'Lee Ivan', 'Paul', 'jericho.ramos@email.com', '09111111111', '2001-01-10', 25, 'Male', 'PMMA', '', '', NULL, NULL, '$2y$10$6pXlofxYnsYSgoTvjyT9eu5DIgxxgLcSmLOkn4xFQgnk9JYD/j986', 1, 'active', 'examinee', 0, NULL, '2026-02-20 11:16:15', '2026-02-20 15:22:22'),
(35, '0000', 'Flores', 'Bianca', 'Louise', 'bianca.flores@email.com', '09086687599', '2004-02-10', 22, 'Male', 'PMMA', '', '', NULL, NULL, '$2y$10$HZVvAKDWyhANNoE3/XRIMeByFjB35bmwPjxjQMb37GEJXi5dZSTp2', 1, 'active', 'examinee', 0, NULL, '2026-02-20 13:53:46', '2026-02-20 13:55:41'),
(36, '7777', 'Navarro', 'Ethan', 'Rafael', 'ethan.navarro@email.com', '09086687599', '2004-02-10', 22, 'Female', 'PMMA', '', '', NULL, NULL, '$2y$10$zLQX6sk5znbKTZXUv/nd1u799xhmE92EX1PZnbgBPLgP6McLDa2y2', 1, 'active', 'examinee', 0, NULL, '2026-02-20 13:57:16', '2026-02-20 13:58:07'),
(37, '1111', 'Bautista', 'Clarisse', 'Anne', 'clarisse.bautista@email.com', '09086687599', '2004-02-29', 21, 'Male', 'PMMA', '', '', NULL, NULL, '$2y$10$e7DtYAwjp/fbITeMsElScel32TZRQFV9liQbkIRRld3v9S5/sunUG', 1, 'active', 'examinee', 0, NULL, '2026-02-20 14:13:41', '2026-02-20 14:14:32'),
(38, 'TP-2026-0101', 'Almadrones', 'Lee Ivan', 'Oliva', 'adrian.lopez@email.com', '09086687599', '2004-02-12', 22, 'Male', 'PMMA', '', 'Filipino', NULL, '2026-02-20 16:08:26', '$2y$10$T8VCIGuNYEZ76ZMD2xYyOOTSL0QopNVsfDiLo2yHmiZqD.G42HpmG', 1, 'active', 'examinee', 0, NULL, '2026-02-20 15:55:13', '2026-02-20 16:08:26'),
(39, '777-8888', 'Villanueva', 'Sarah', 'Joy', 'sarah.villanueva@example.com', '09086687599', '2026-02-12', 0, 'Male', 'PMMA', '', 'Filipino', NULL, NULL, '$2y$10$Op1r2KzM9MDDylA8HNwNv.SbZ4aCUTevqVL5VgD5vFlksuBQZl6jS', 1, 'active', 'examinee', 0, NULL, '2026-02-20 16:15:15', '2026-02-20 16:15:54'),
(40, '434343', 'John', 'Doe', 'Joy', 'Doe@gmail.com', '09086687599', '2005-02-10', 21, 'Male', 'PMMA', '', 'Filipino', NULL, NULL, '$2y$10$6.xEB0J0IL2quCWep5.TFumMwxAotRqCFPMYy6ogBwpxZVH/t8M/a', 1, 'active', 'examinee', 0, NULL, '2026-02-20 16:51:02', '2026-02-20 16:52:04'),
(41, '4444', 'Mike', 'John', 'Paylan', 'mikejohn@gmail.com', '09086687599', '2005-02-23', 20, 'Male', 'PMMA', '', 'Filipino', NULL, NULL, '$2y$10$nrTtzfO8q8F6jyrNr9R/v.kUTiFnGid/nfWwNqFIZgKKkvF8WL/ai', 1, 'active', 'examinee', 0, NULL, '2026-02-22 10:51:58', '2026-02-22 10:55:00'),
(42, '101010', 'Almadrones', 'Lee Ivan', 'Oliva', 'adasdasdasdsa', '09086687599', '2026-02-11', 0, 'Male', 'PMMA', '', 'Filipino', NULL, NULL, '$2y$10$OnebTobIDokvU0JUGfGBquMv15VcVHJ4oIgSUwRTEzdxSAXw6GOHC', 1, 'active', 'examinee', 0, NULL, '2026-02-22 11:34:18', '2026-02-22 18:41:45'),
(43, '1100', 'Jen', 'Boi', 'G', 'eqwewqe', '09086687599', '2026-02-11', 0, 'Male', 'PMMA', '', 'Filipino', NULL, NULL, '$2y$10$KsURYCIoyj6EsZ4xO7ZGFeG9kqmS9qNC5qG9256NHw2KjNBnH8tDO', 1, 'active', 'examinee', 0, NULL, '2026-02-22 18:42:26', '2026-02-22 19:40:51'),
(44, '1129', 'Ivan', 'Lee', 'Bog', 'gdfgdfgdf', '09108987920', '2004-02-10', 22, 'Male', 'Pmma', '', 'Filipino', NULL, NULL, '$2y$10$B4Ikv2BN2kOiC9kQ0BBaJOoR9cPPoYNvNb152YoSzlxm/l4m6qHK2', 1, 'active', 'examinee', 0, NULL, '2026-02-22 19:45:37', '2026-02-23 08:02:33'),
(45, 'PMMA', 'Sir', 'Don', 'A.', '312321', '09108987920', '2004-02-10', 22, 'Male', 'PMAA', '', 'Filipino', NULL, NULL, '$2y$10$KNsAQd3B4DnPNbh.pRn8zepWouxv9Xz/HW3s9wkfSQvo5EBmLvZo6', 1, 'incomplete', 'examinee', 0, NULL, '2026-02-23 08:38:20', '2026-02-23 08:43:24'),
(46, 'PMMA-01', 'Sir', 'Don', 'A.', 'jgh', '09108987920', '2004-02-10', 22, 'Male', 'PMAA', '', 'Filipino', NULL, NULL, '$2y$10$/XtWs9uNhsqEPpaTZoBOEe7ufiZubR36i.DkifbI8MmNFPlEc/NPe', 1, 'incomplete', 'examinee', 0, NULL, '2026-02-23 08:45:07', '2026-02-23 08:53:18'),
(47, 'PMMA-02', 'Sir', 'Don', 'A.', 'ytutyu', '09108987920', '2026-02-18', 0, 'Male', 'PMAA', '', 'Filipino', NULL, NULL, '$2y$10$yyX4d04C.kkdSEqv0H/iyedxKDHjeRzIyyuEGwUg/7gR6j/Qs8KuW', 1, 'active', 'examinee', 0, NULL, '2026-02-23 08:54:39', '2026-02-23 09:10:31'),
(48, 'PMMA-03', 'Sir', 'Don', 'A.', 'ffghgg', '09108987920', '2004-02-24', 21, 'Male', 'PMAA', '', 'Filipino', NULL, NULL, '$2y$10$h4ho1dlo4qfuyO0vF4MgBeF8U6Yl0cyFpUAgvt8g9HeescHsRVFv2', 1, 'active', 'examinee', 0, NULL, '2026-02-23 09:11:16', '2026-02-23 09:55:59'),
(49, 'PMMA-04', 'Sir', 'Don', 'A.', '98765', '09108987920', '2005-02-10', 21, 'Male', 'PMAA', '', 'Filipino', NULL, NULL, '$2y$10$Dx/y8zxEPnHpNjkPNXC/AeqzCBN3M1WbiuuXLHiB8N0V.y2sTkU4q', 1, 'active', 'examinee', 0, NULL, '2026-02-23 09:57:28', '2026-02-23 10:18:46'),
(50, 'PMMA-05', 'Sir', 'Don', 'A.', 'don@gmail.com', '09108987920', '2005-02-14', 21, 'Male', 'PMAA', '', 'Filipino', NULL, NULL, '$2y$10$5UA49zbeoSIZiZl8xOuvAu5S2du6Dynt0mJ1mrUAYwfT3fPzvBxka', 1, 'active', 'examinee', 0, NULL, '2026-02-23 10:20:34', '2026-02-24 14:42:50'),
(51, 'PMMA-06', 'Sir', 'Don', 'A.', '21212121', '09108987920', '2026-02-18', 0, 'Male', 'PMAA', '', 'Filipino', NULL, NULL, '$2y$10$URN0M5MSsMVGsfJAZV93d.XQabb3LS4Td69Kzikw0r/ubWQvXRxPK', 1, 'active', 'examinee', 0, NULL, '2026-02-23 10:30:39', '2026-02-23 10:47:27'),
(52, 'PMMA-07', 'Sir', 'Don', 'A.', '97887', '09108987920', '2026-02-18', 0, 'Female', 'PMAA', '', 'Filipino', NULL, NULL, '$2y$10$G1lWemQNkfv1inPWAQF9EeaZnpCkvMLUZg639t2Yc9U2Gcy6S.Ct2', 1, 'active', 'examinee', 0, NULL, '2026-02-23 10:49:06', '2026-02-23 11:43:30'),
(53, 'PMAA-08', 'Sir', 'Don', 'A.', '876867', '09108987920', '2026-02-19', 0, 'Male', 'PMAA', '', 'Filipino', NULL, NULL, '$2y$10$osJt0Zc7eqalDtiwNSwwOedPxfXXqzz1RFc76LZm5GmDIc9y7M1Oa', 1, 'active', 'examinee', 0, NULL, '2026-02-23 11:13:41', '2026-02-23 11:43:33'),
(54, 'PMMA-010', 'Sir', 'Don', 'A.', 'dasdasdas@gmail.com', '09108987920', '2026-02-19', 0, 'Male', 'PMAA', '', 'Filipino', NULL, NULL, '$2y$10$HhbKv3vkTkjdlNJ1hZOkHunXlkgVCo8Darx4V8JuXi1TqY0eh0/Py', 1, 'active', 'examinee', 0, NULL, '2026-02-23 11:44:40', '2026-02-24 09:34:05'),
(55, 'PSY-2026', 'Dela Cruz', 'Maria', 'Santos', 'verzosajanna7@gmail.com', '09171234567', '1990-05-15', 34, 'Female', 'PSY', 'Quezon City, Philippines', 'Filipino', NULL, NULL, '$2y$10$zLYmBxFD9QoLfQd9S8IkSeLcyS4OFoOUTImXWtOBc4ApoN0qhl0fm', 1, 'active', 'accountant', 0, NULL, '2026-02-23 13:41:52', '2026-02-23 13:41:52'),
(56, 'PMMA-0978', 'Lee Ivan', 'Jay', 'A.', 'dasdasd', '09108987920', '2004-02-10', 22, 'Male', 'PMAA', '', 'Filipino', NULL, NULL, '$2y$10$sW8UYzJm0O/YK.XjaFYQ..e8gWMXYkXHPZTjJKie/FRnEjMeiwjLW', 1, 'active', 'examinee', 0, NULL, '2026-02-23 14:51:45', '2026-02-23 15:28:04');

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
(14, 'Vinzons', 'Luzon'),
(15, 'Bagong Silang', 'Luzon'),
(16, 'Labo', 'Mindanao');

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
-- Indexes for table `faqs`
--
ALTER TABLE `faqs`
  ADD PRIMARY KEY (`faq_id`);

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
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=201;

--
-- AUTO_INCREMENT for table `examinees`
--
ALTER TABLE `examinees`
  MODIFY `examinee_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `examinee_masterlist`
--
ALTER TABLE `examinee_masterlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `faqs`
--
ALTER TABLE `faqs`
  MODIFY `faq_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `otp_verifications`
--
ALTER TABLE `otp_verifications`
  MODIFY `verification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `reset_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `rate_limits`
--
ALTER TABLE `rate_limits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `schedule_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `venue`
--
ALTER TABLE `venue`
  MODIFY `venue_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

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
