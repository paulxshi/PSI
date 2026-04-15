-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 10, 2026 at 06:59 AM
-- Server version: 11.8.6-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u679207742_pmma`
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
(584, 1, 'Lee Ivan Almadrones', 'admin@psi-services.net', 'login_success', 'Admin logged in successfully', '161.248.59.10', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'admin', 'info', NULL, '2026-04-09 20:40:36'),
(585, NULL, NULL, 'bernandinocezar@gmail.com', 'login_failed', 'Examinee login failed - Invalid email or test permit', '2405:8d40:4078:1174:f8c1:1e7a:da21:bf72', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 'examinee', 'warning', NULL, '2026-04-09 20:46:06'),
(586, 1, 'Admin', 'admin@psi-services.net', 'admin_schedule_created', 'Admin created new schedule for ROSARIO PAVILLION, Rosario Stript, Limketkai Center, Cagayan De Oro, Mindanao on 2026-04-19', '161.248.59.10', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'admin', 'info', '{\"schedule_id\":\"25\",\"venue\":\"ROSARIO PAVILLION, Rosario Stript, Limketkai Center, Cagayan De Oro\",\"region\":\"Mindanao\",\"date\":\"2026-04-19\",\"capacity\":250,\"price\":1500,\"meals_count\":0}', '2026-04-09 20:49:46'),
(587, 1, 'Admin', 'admin@psi-services.net', 'admin_schedule_edited', 'Admin edited schedule #24: Test only, Luzon on 2026-04-10', '161.248.59.10', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'admin', 'info', '{\"schedule_id\":24,\"venue\":\"Test only\",\"region\":\"Luzon\",\"date\":\"2026-04-10\",\"capacity\":2,\"price\":0.5,\"status\":\"Closed\"}', '2026-04-09 20:52:30'),
(588, 3, 'Admin', 'cezarbernandino2003@gmail.com', 'admin_schedule_deleted', 'Admin deleted schedule #24', '2405:8d40:4078:1174:f8c1:1e7a:da21:bf72', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'admin', 'warning', '{\"schedule_id\":24}', '2026-04-09 20:53:22'),
(589, 1, 'Admin', 'admin@psi-services.net', 'admin_schedule_created', 'Admin created new schedule for ROSARIO PAVILLION, Rosario Stript, Limketkai Center, Cagayan De Oro, Mindanao on 2026-04-20', '161.248.59.10', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'admin', 'info', '{\"schedule_id\":\"26\",\"venue\":\"ROSARIO PAVILLION, Rosario Stript, Limketkai Center, Cagayan De Oro\",\"region\":\"Mindanao\",\"date\":\"2026-04-20\",\"capacity\":250,\"price\":1500,\"meals_count\":0}', '2026-04-09 20:56:19'),
(590, 1, 'Admin', 'admin@psi-services.net', 'admin_schedule_created', 'Admin created new schedule for LA CASA DE ALMA EVENT PAVILLION, Villa Lizares Subd., Tabuc Suba, Jaro, Iloilo City, Visayas on 2026-04-23', '161.248.59.10', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'admin', 'info', '{\"schedule_id\":\"27\",\"venue\":\"LA CASA DE ALMA EVENT PAVILLION, Villa Lizares Subd., Tabuc Suba, Jaro, Iloilo City\",\"region\":\"Visayas\",\"date\":\"2026-04-23\",\"capacity\":250,\"price\":1500,\"meals_count\":1}', '2026-04-09 21:00:16'),
(591, 1, 'Admin', 'admin@psi-services.net', 'admin_schedule_created', 'Admin created new schedule for LA CASA DE ALMA EVENT PAVILLION, Villa Lizares Subd., Tabuc Suba, Jaro, Iloilo City, Visayas on 2026-04-24', '161.248.59.10', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'admin', 'info', '{\"schedule_id\":\"28\",\"venue\":\"LA CASA DE ALMA EVENT PAVILLION, Villa Lizares Subd., Tabuc Suba, Jaro, Iloilo City\",\"region\":\"Visayas\",\"date\":\"2026-04-24\",\"capacity\":250,\"price\":1500,\"meals_count\":1}', '2026-04-09 21:01:44'),
(592, 3, 'Admin', 'cezarbernandino2003@gmail.com', 'admin_schedule_created', 'Admin created new schedule for Test, Luzon on 2026-04-10', '2405:8d40:4078:1174:f8c1:1e7a:da21:bf72', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'admin', 'info', '{\"schedule_id\":\"29\",\"venue\":\"Test\",\"region\":\"Luzon\",\"date\":\"2026-04-10\",\"capacity\":1,\"price\":0.5,\"meals_count\":1}', '2026-04-09 21:10:11'),
(593, 1, 'Admin', 'admin@psi-services.net', 'admin_schedule_created', 'Admin created new schedule for YWCA Founder\'s of the Philippines, Inc - 1144 General Luna St., Paco, Manila, Luzon on 2026-04-27', '161.248.59.10', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'admin', 'info', '{\"schedule_id\":\"30\",\"venue\":\"YWCA Founder\'s of the Philippines, Inc - 1144 General Luna St., Paco, Manila\",\"region\":\"Luzon\",\"date\":\"2026-04-27\",\"capacity\":500,\"price\":1500,\"meals_count\":1}', '2026-04-09 21:10:42'),
(594, NULL, NULL, 'bernandinocezar@gmail.com', 'otp_verified', 'OTP verified successfully for purpose: registration', '2405:8d40:4078:1174:f8c1:1e7a:da21:bf72', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 'examinee', 'info', NULL, '2026-04-09 21:11:48'),
(595, 1, 'Admin', 'admin@psi-services.net', 'admin_schedule_created', 'Admin created new schedule for YWCA Founder\'s of the Philippines, Inc - 1144 General Luna St., Paco, Manila, Luzon on 2026-04-28', '161.248.59.10', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'admin', 'info', '{\"schedule_id\":\"31\",\"venue\":\"YWCA Founder\'s of the Philippines, Inc - 1144 General Luna St., Paco, Manila\",\"region\":\"Luzon\",\"date\":\"2026-04-28\",\"capacity\":500,\"price\":1500,\"meals_count\":1}', '2026-04-09 21:13:20'),
(596, 1, 'Admin', 'admin@psi-services.net', 'admin_schedule_created', 'Admin created new schedule for YWCA Founder\'s of the Philippines, Inc - 1144 General Luna St., Paco, Manila, Luzon on 2026-04-29', '161.248.59.10', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'admin', 'info', '{\"schedule_id\":\"32\",\"venue\":\"YWCA Founder\'s of the Philippines, Inc - 1144 General Luna St., Paco, Manila\",\"region\":\"Luzon\",\"date\":\"2026-04-29\",\"capacity\":500,\"price\":1500,\"meals_count\":1}', '2026-04-09 21:14:26'),
(597, NULL, NULL, 'bernandinocezar@gmail.com', 'otp_verified', 'OTP verified successfully for purpose: registration', '2405:8d40:4078:1174:f8c1:1e7a:da21:bf72', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 'examinee', 'info', NULL, '2026-04-09 21:15:21'),
(598, NULL, 'bernandinocezar@gmail.com', 'bernandinocezar@gmail.com', 'registration_completed', 'New user registered: bernandinocezar@gmail.com (Permit: TP-2026-2023)', '2405:8d40:4078:1174:f8c1:1e7a:da21:bf72', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 'examinee', 'info', '{\"user_id\":\"55\",\"test_permit\":\"TP-2026-2023\",\"email\":\"bernandinocezar@gmail.com\"}', '2026-04-09 21:15:36'),
(599, 3, 'Admin', 'cezarbernandino2003@gmail.com', 'admin_schedule_edited', 'Admin edited schedule #29: Test, Luzon on 2026-04-10', '2405:8d40:4078:1174:f8c1:1e7a:da21:bf72', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'admin', 'info', '{\"schedule_id\":29,\"venue\":\"Test\",\"region\":\"Luzon\",\"date\":\"2026-04-10\",\"capacity\":2,\"price\":0.5,\"status\":\"Closed\"}', '2026-04-09 21:26:27'),
(600, NULL, NULL, 'bernandinocezar@gmail.com', 'otp_failed', 'OTP verification failed for purpose: registration. Attempts: 1/3', '2405:8d40:4078:1174:f8c1:1e7a:da21:bf72', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 'examinee', 'warning', NULL, '2026-04-09 21:29:53'),
(601, NULL, NULL, 'bernandinocezar@gmail.com', 'otp_verified', 'OTP verified successfully for purpose: registration', '2405:8d40:4078:1174:f8c1:1e7a:da21:bf72', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 'examinee', 'info', NULL, '2026-04-09 21:30:16'),
(602, NULL, 'bernandinocezar@gmail.com', 'bernandinocezar@gmail.com', 'registration_completed', 'New user registered: bernandinocezar@gmail.com (Permit: TP-2026-2023)', '2405:8d40:4078:1174:f8c1:1e7a:da21:bf72', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 'examinee', 'info', '{\"user_id\":\"56\",\"test_permit\":\"TP-2026-2023\",\"email\":\"bernandinocezar@gmail.com\"}', '2026-04-09 21:30:28'),
(603, 3, 'Admin', 'cezarbernandino2003@gmail.com', 'admin_schedule_edited', 'Admin edited schedule #29: Test, Luzon on 2026-04-10', '2405:8d40:4078:1174:f8c1:1e7a:da21:bf72', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'admin', 'info', '{\"schedule_id\":29,\"venue\":\"Test\",\"region\":\"Luzon\",\"date\":\"2026-04-10\",\"capacity\":2,\"price\":0.5,\"status\":\"Incoming\"}', '2026-04-09 21:31:02'),
(604, 1, 'Lee Ivan Almadrones', 'admin@psi-services.net', 'login_success', 'Admin logged in successfully', '120.28.180.150', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Mobile Safari/537.36', 'admin', 'info', NULL, '2026-04-09 22:46:44'),
(605, NULL, NULL, 'bernandinocezar@gmail.com', 'otp_verified', 'OTP verified successfully for purpose: registration', '2405:8d40:4078:1174:f8c1:1e7a:da21:bf72', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 'examinee', 'info', NULL, '2026-04-09 22:56:25'),
(606, NULL, 'bernandinocezar@gmail.com', 'bernandinocezar@gmail.com', 'registration_completed', 'New user registered: bernandinocezar@gmail.com (Permit: TP-2026-2023)', '2405:8d40:4078:1174:f8c1:1e7a:da21:bf72', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 'examinee', 'info', '{\"user_id\":\"57\",\"test_permit\":\"TP-2026-2023\",\"email\":\"bernandinocezar@gmail.com\"}', '2026-04-09 22:56:41'),
(607, NULL, NULL, 'bernandinocezar@gmail.com', 'otp_verified', 'OTP verified successfully for purpose: registration', '2405:8d40:4078:1174:f8c1:1e7a:da21:bf72', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 'examinee', 'info', NULL, '2026-04-09 23:12:50'),
(608, NULL, 'bernandinocezar@gmail.com', 'bernandinocezar@gmail.com', 'registration_completed', 'New user registered: bernandinocezar@gmail.com (Permit: TP-2026-2023)', '2405:8d40:4078:1174:f8c1:1e7a:da21:bf72', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 'examinee', 'info', '{\"user_id\":\"58\",\"test_permit\":\"TP-2026-2023\",\"email\":\"bernandinocezar@gmail.com\"}', '2026-04-09 23:13:08'),
(609, NULL, NULL, 'bernandinocezar@gmail.com', 'otp_verified', 'OTP verified successfully for purpose: registration', '2405:8d40:4078:1174:f8c1:1e7a:da21:bf72', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 'examinee', 'info', NULL, '2026-04-09 23:23:23'),
(610, NULL, 'bernandinocezar@gmail.com', 'bernandinocezar@gmail.com', 'registration_completed', 'New user registered: bernandinocezar@gmail.com (Permit: TP-2026-2023)', '2405:8d40:4078:1174:f8c1:1e7a:da21:bf72', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 'examinee', 'info', '{\"user_id\":\"59\",\"test_permit\":\"TP-2026-2023\",\"email\":\"bernandinocezar@gmail.com\"}', '2026-04-09 23:23:37'),
(611, 3, 'Admin', 'cezarbernandino2003@gmail.com', 'admin_schedule_edited', 'Admin edited schedule #29: Test, Luzon on 2026-04-10', '2405:8d40:4078:1174:f8c1:1e7a:da21:bf72', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'admin', 'info', '{\"schedule_id\":29,\"venue\":\"Test\",\"region\":\"Luzon\",\"date\":\"2026-04-10\",\"capacity\":5,\"price\":0.5,\"status\":\"Closed\"}', '2026-04-09 23:27:17'),
(612, NULL, NULL, 'bernandinocezar@gmail.com', 'otp_verified', 'OTP verified successfully for purpose: registration', '2405:8d40:4078:1174:2512:556d:2f64:492d', 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_4_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.4.1 Mobile/15E148 Safari/604.1', 'examinee', 'info', NULL, '2026-04-09 23:30:41'),
(613, 60, 'bernandinocezar@gmail.com', 'bernandinocezar@gmail.com', 'registration_completed', 'New user registered: bernandinocezar@gmail.com (Permit: TP-2026-2023)', '2405:8d40:4078:1174:2512:556d:2f64:492d', 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_4_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.4.1 Mobile/15E148 Safari/604.1', 'examinee', 'info', '{\"user_id\":\"60\",\"test_permit\":\"TP-2026-2023\",\"email\":\"bernandinocezar@gmail.com\"}', '2026-04-09 23:31:05'),
(614, 3, 'Admin', 'cezarbernandino2003@gmail.com', 'admin_schedule_edited', 'Admin edited schedule #29: Test, Luzon on 2026-04-10', '2405:8d40:4078:1174:f8c1:1e7a:da21:bf72', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'admin', 'info', '{\"schedule_id\":29,\"venue\":\"Test\",\"region\":\"Luzon\",\"date\":\"2026-04-10\",\"capacity\":5,\"price\":0.5,\"status\":\"Incoming\"}', '2026-04-09 23:31:32'),
(615, 60, 'Cezar Bernandino', 'bernandinocezar@gmail.com', 'login_success', 'Examinee logged in successfully', '2405:8d40:4078:1174:2512:556d:2f64:492d', 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_4_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.4.1 Mobile/15E148 Safari/604.1', 'examinee', 'info', NULL, '2026-04-09 23:37:05'),
(616, 3, 'Admin', 'cezarbernandino2003@gmail.com', 'admin_schedule_edited', 'Admin edited schedule #29: Test, Luzon on 2026-04-10', '2405:8d40:4078:1174:f8c1:1e7a:da21:bf72', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'admin', 'info', '{\"schedule_id\":29,\"venue\":\"Test\",\"region\":\"Luzon\",\"date\":\"2026-04-10\",\"capacity\":5,\"price\":0.5,\"status\":\"Closed\"}', '2026-04-09 23:40:44'),
(617, 3, 'Admin', 'cezarbernandino2003@gmail.com', 'admin_schedule_deleted', 'Admin deleted schedule #29', '2405:8d40:4078:1174:f8c1:1e7a:da21:bf72', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'admin', 'warning', '{\"schedule_id\":29}', '2026-04-09 23:40:50'),
(618, 3, 'Cezar Bernandino', 'cezarbernandino2003@gmail.com', 'login_success', 'Admin logged in successfully', '136.158.7.23', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'admin', 'info', NULL, '2026-04-10 08:03:30'),
(619, 1, 'Lee Ivan Almadrones', 'admin@psi-services.net', 'login_success', 'Admin logged in successfully', '120.28.180.150', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 'admin', 'info', NULL, '2026-04-10 08:10:30'),
(620, 1, 'Lee Ivan Almadrones', 'admin@psi-services.net', 'login_success', 'Admin logged in successfully', '180.193.215.226', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'admin', 'info', NULL, '2026-04-10 08:55:14'),
(621, 1, 'Lee Ivan Almadrones', 'admin@psi-services.net', 'login_success', 'Admin logged in successfully', '120.28.180.150', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 'admin', 'info', NULL, '2026-04-10 08:55:22'),
(622, 1, 'Lee Ivan Almadrones', 'admin@psi-services.net', 'logout', 'Admin logged out', '120.28.180.150', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 'admin', 'info', NULL, '2026-04-10 08:55:29'),
(623, 3, 'Admin', 'cezarbernandino2003@gmail.com', 'admin_schedule_created', 'Admin created new schedule for For Testing, Luzon on 2026-04-11', '136.158.7.23', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'admin', 'info', '{\"schedule_id\":\"33\",\"venue\":\"For Testing\",\"region\":\"Luzon\",\"date\":\"2026-04-11\",\"capacity\":100,\"price\":0.25,\"meals_count\":1}', '2026-04-10 09:00:40'),
(624, 1, 'Lee Ivan Almadrones', 'admin@psi-services.net', 'logout', 'Admin logged out', '180.193.215.226', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'admin', 'info', NULL, '2026-04-10 09:04:44'),
(625, 3, 'Admin', 'cezarbernandino2003@gmail.com', 'admin_schedule_edited', 'Admin edited schedule #33: For Testing, Luzon on 2026-04-11', '136.158.7.23', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'admin', 'info', '{\"schedule_id\":33,\"venue\":\"For Testing\",\"region\":\"Luzon\",\"date\":\"2026-04-11\",\"capacity\":100,\"price\":0.25,\"status\":\"Closed\"}', '2026-04-10 09:48:49'),
(626, 3, 'Admin', 'cezarbernandino2003@gmail.com', 'admin_schedule_deleted', 'Admin deleted schedule #33', '136.158.7.23', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'admin', 'warning', '{\"schedule_id\":33}', '2026-04-10 09:48:56'),
(627, 1, 'Lee Ivan Almadrones', 'admin@psi-services.net', 'login_success', 'Admin logged in successfully', '120.28.180.150', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 'admin', 'info', NULL, '2026-04-10 11:20:35'),
(628, 1, 'Lee Ivan Almadrones', 'admin@psi-services.net', 'logout', 'Admin logged out', '120.28.180.150', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 'admin', 'info', NULL, '2026-04-10 11:28:46'),
(629, 1, 'Lee Ivan Almadrones', 'admin@psi-services.net', 'login_success', 'Admin logged in successfully', '120.28.180.150', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 'admin', 'info', NULL, '2026-04-10 11:28:48'),
(630, 1, 'Lee Ivan Almadrones', 'admin@psi-services.net', 'login_success', 'Admin logged in successfully', '120.28.180.150', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 'admin', 'info', NULL, '2026-04-10 13:07:28'),
(631, 1, 'Lee Ivan Almadrones', 'admin@psi-services.net', 'logout', 'Admin logged out', '120.28.180.150', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 'admin', 'info', NULL, '2026-04-10 13:08:09'),
(632, 1, 'Lee Ivan Almadrones', 'admin@psi-services.net', 'login_success', 'Admin logged in successfully', '120.28.180.150', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 'admin', 'info', NULL, '2026-04-10 13:08:11'),
(633, NULL, NULL, 'susanalmadrones6@gmail.com', 'otp_verified', 'OTP verified successfully for purpose: registration', '120.28.180.150', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 'examinee', 'info', NULL, '2026-04-10 13:34:38'),
(634, 61, 'susanalmadrones6@gmail.com', 'susanalmadrones6@gmail.com', 'registration_completed', 'New user registered: susanalmadrones6@gmail.com (Permit: 2025-07-00517)', '120.28.180.150', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 'examinee', 'info', '{\"user_id\":\"61\",\"test_permit\":\"2025-07-00517\",\"email\":\"susanalmadrones6@gmail.com\"}', '2026-04-10 13:35:47');

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
(49, 61, '2025-07-00517', 30, 'Awaiting Payment', '2026-04-10 13:35:47', '2026-04-10 13:36:01', NULL, NULL, NULL);

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
-- Table structure for table `examinee_meals`
--

CREATE TABLE `examinee_meals` (
  `examinee_meal_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `meal_id` int(11) NOT NULL,
  `selected_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `examinee_meals`
--

INSERT INTO `examinee_meals` (`examinee_meal_id`, `user_id`, `meal_id`, `selected_at`) VALUES
(17, 61, 15, '2026-04-10 05:36:01');

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
-- Table structure for table `meals`
--

CREATE TABLE `meals` (
  `meal_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `schedule_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `meals`
--

INSERT INTO `meals` (`meal_id`, `name`, `price`, `schedule_id`) VALUES
(12, 'Rice Chicken Afritada Pork Lumpia Bottled Water', 170.00, 27),
(13, 'Rice Chicken Afritada Pork Lumpia Bottled Water', 170.00, 28),
(15, 'Chicken-Silog with water', 170.00, 30),
(16, 'Chicken - Silog', 170.00, 31),
(17, 'Chicken - Silog with water', 170.00, 32);

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
(63, 'leeivanalmadrones6@gmail.com', '568289', 0, 1, 'registration', '2026-03-28 16:51:42', '2026-03-28 17:01:42', '2026-03-28 16:52:01'),
(64, 'psi.albertlimpin@gmail.com', '209072', 0, 1, 'registration', '2026-03-30 16:15:27', '2026-03-30 16:25:27', '2026-03-30 16:15:57'),
(65, 'johnpaulgabo464@gmail.com', '377810', 0, 1, 'registration', '2026-03-30 16:27:54', '2026-03-30 16:37:54', '2026-03-30 16:28:39'),
(66, 'leeivanalmadrones6@gmail.com', '679299', 0, 1, 'registration', '2026-03-30 16:31:00', '2026-03-30 16:41:00', '2026-03-30 16:31:57'),
(67, 'bernandinocezar@gmail.com', '255442', 0, 1, 'registration', '2026-03-30 16:31:37', '2026-03-30 16:41:37', '2026-03-30 16:32:34'),
(68, 'joy.llaguno022@gmail.com', '393892', 0, 1, 'registration', '2026-03-30 16:37:07', '2026-03-30 16:47:07', '2026-03-30 16:37:24'),
(69, 'gjptibay22@gmail.com', '572956', 0, 1, 'registration', '2026-03-30 16:39:09', '2026-03-30 16:49:09', '2026-03-30 16:39:32'),
(70, 'ernestoreontoyjr@gmail.com', '186258', 0, 1, 'registration', '2026-03-30 16:39:53', '2026-03-30 16:49:53', '2026-03-30 16:40:34'),
(71, 'desamerocamille@gmail.com', '424613', 0, 0, 'registration', '2026-03-30 16:41:12', '2026-03-30 16:51:12', NULL),
(72, 'desamerocamille@gmail.com', '349899', 0, 1, 'registration', '2026-03-30 16:42:49', '2026-03-30 16:52:49', '2026-03-30 16:43:58'),
(73, 'lerrykawabata@gmail.com', '215908', 0, 1, 'registration', '2026-03-30 16:48:49', '2026-03-30 16:58:49', '2026-03-30 16:49:15'),
(74, 'elthallium@gmail.com', '785485', 0, 1, 'registration', '2026-03-30 16:54:26', '2026-03-30 17:04:26', '2026-03-30 16:54:49'),
(75, 'ksarmiento2025@gmail.com', '790884', 0, 1, 'registration', '2026-03-30 16:55:13', '2026-03-30 17:05:13', '2026-03-30 16:55:32'),
(76, 'juliahmarieochoa@gmail.com', '311610', 0, 1, 'registration', '2026-03-30 16:56:43', '2026-03-30 17:06:43', '2026-03-30 16:57:12'),
(77, 'bernandinocezar@gmail.com', '125187', 0, 1, 'registration', '2026-04-08 16:38:25', '2026-04-08 16:48:25', '2026-04-08 16:38:47'),
(78, 'bernandinocezar@gmail.com', '907403', 0, 1, 'registration', '2026-04-09 21:11:14', '2026-04-09 21:21:14', '2026-04-09 21:11:48'),
(79, 'bernandinocezar@gmail.com', '937122', 0, 1, 'registration', '2026-04-09 21:14:56', '2026-04-09 21:24:56', '2026-04-09 21:15:21'),
(80, 'bernandinocezar@gmail.com', '470759', 0, 0, 'registration', '2026-04-09 21:28:08', '2026-04-09 21:38:08', NULL),
(81, 'bernandinocezar@gmail.com', '709888', 1, 1, 'registration', '2026-04-09 21:29:17', '2026-04-09 21:39:17', '2026-04-09 21:30:16'),
(82, 'bernandinocezar@gmail.com', '972072', 0, 1, 'registration', '2026-04-09 22:56:03', '2026-04-09 23:06:03', '2026-04-09 22:56:25'),
(83, 'bernandinocezar@gmail.com', '978241', 0, 1, 'registration', '2026-04-09 23:12:27', '2026-04-09 23:22:27', '2026-04-09 23:12:50'),
(84, 'bernandinocezar@gmail.com', '304984', 0, 1, 'registration', '2026-04-09 23:22:44', '2026-04-09 23:32:44', '2026-04-09 23:23:23'),
(85, 'bernandinocezar@gmail.com', '261923', 0, 1, 'registration', '2026-04-09 23:30:21', '2026-04-09 23:40:21', '2026-04-09 23:30:41'),
(86, 'susanalmadrones6@gmail.com', '290074', 0, 1, 'registration', '2026-04-10 13:34:17', '2026-04-10 13:44:17', '2026-04-10 13:34:38');

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
(3, 41, 'johnpaulgabo464@gmail.com', '523322', 0, 1, '2026-03-30 16:32:01', '2026-03-30 16:42:01', '2026-03-30 16:32:56'),
(4, 50, 'ksarmiento2025@gmail.com', '870013', 0, 0, '2026-03-30 17:04:35', '2026-03-30 17:14:35', NULL),
(5, 50, 'ksarmiento2025@gmail.com', '380797', 0, 1, '2026-03-30 17:06:19', '2026-03-30 17:16:19', '2026-03-30 17:08:53');

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
(39, 'registration', '120.28.180.150', 2, '2026-04-10 13:34:54', '2026-04-10 13:35:47', NULL);

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
  `num_of_examinees` int(11) NOT NULL,
  `archived` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedules`
--

INSERT INTO `schedules` (`schedule_id`, `venue_id`, `scheduled_date`, `capacity`, `price`, `status`, `num_registered`, `num_of_examinees`, `archived`) VALUES
(25, 17, '2026-04-19', 0, 1500.00, 'Incoming', 0, 250, NULL),
(26, 17, '2026-04-20', 0, 1500.00, 'Incoming', 0, 250, NULL),
(27, 18, '2026-04-23', 0, 1500.00, 'Incoming', 0, 250, NULL),
(28, 18, '2026-04-24', 0, 1500.00, 'Incoming', 0, 250, NULL),
(30, 20, '2026-04-27', 0, 1500.00, 'Incoming', 1, 500, NULL),
(31, 20, '2026-04-28', 0, 1500.00, 'Incoming', 0, 500, NULL),
(32, 20, '2026-04-29', 0, 1500.00, 'Incoming', 0, 500, NULL);

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
(1, 'ADMIN-001', 'Almadrones', 'Lee Ivan', 'Oliva', 'admin@psi-services.net', '09123456789', '1990-01-01', 35, 'Male', 'System Administrator', 'Manila, Philippines', 'Filipino', NULL, 0, NULL, '$2y$10$rwgLrAahli.qCoZ0nAdR0.GnWzQGs63Bpm8cuR0qX85CUF7yCjUMe', 1, 'active', 'admin', 0, NULL, '2026-02-27 09:25:10', '2026-04-09 20:26:06', NULL, NULL),
(3, 'ADMIN-002', 'Bernandino', 'Cezar', 'Ibusag', 'cezarbernandino2003@gmail.com', '09632884241', '1990-01-01', 35, 'Male', 'System Administrator', 'Manila, Philippines', 'Filipino', NULL, 0, NULL, '$2y$10$KL3eyxD0hQmQjESCuq7P2OOIgOCL3sd.O5nODZfnSAsdVyHArGhJG', 1, 'active', 'admin', 0, NULL, '2026-02-27 09:32:57', '2026-03-02 14:44:50', NULL, NULL),
(29, 'TP-2025-007', 'Macalanda', 'Darwin', 'Campos', 'darwinmacalanda@gmail.com', '09630257890', '2003-02-14', 23, 'Male', 'CNSC', '', 'Filipino', 'uploads/profile_pictures/user_29_1772583349.jpg', NULL, '2026-03-04 08:15:49', '$2y$10$DC9k7fPsIRvtyMAtW3mreuz.Qe6DRuNrekq.JwLtAfag79wJUOQxy', 1, 'active', 'examinee', 0, NULL, '2026-03-03 13:29:50', '2026-03-04 08:15:49', NULL, NULL),
(32, 'TP-2026-0000', 'Almadrones', 'Lee Ivan', 'Oliva', 'leeivanalmadrones2004@gmail.com', '09108987920', '2026-02-10', 0, 'Male', 'PMAA', '', 'Filipino', 'uploads/profile_pictures/user_32_1773649533.jpeg', 1, '2026-03-16 16:25:33', '$2y$10$4K/PTo/GB7fh15bLTewcgeve.HmvBSd37cQqaGjqzkqvmhgO.CtjG', 1, 'active', 'examinee', 0, NULL, '2026-03-16 10:38:37', '2026-03-18 02:53:38', NULL, NULL),
(34, 'psi12345', 'Tibigar', 'Mary Jane', 'M.', 'marketing@psi-services.net', '0992-684-4472', '2002-12-17', 23, 'Female', 'pmma', '', 'Filipino', 'uploads/profile_pictures/user_34_1773648251.jpg', 1, '2026-03-16 16:04:11', '$2y$10$aV6l.zxxWOSySCOgwAEBm.JTF0f5WaCTGN.7lFl.ITaVbiVJVCftW', 1, 'active', 'examinee', 0, NULL, '2026-03-16 15:52:21', '2026-03-16 16:04:11', NULL, NULL),
(35, 'ACC-00020', 'Bernandino', 'Cezar', 'Oliva', 'accountant@psi-services.net', '09123456789', '1990-01-01', 35, 'Male', 'System Administrator', 'Manila, Philippines', 'Filipino', NULL, 0, NULL, '$2y$10$rwgLrAahli.qCoZ0nAdR0.GnWzQGs63Bpm8cuR0qX85CUF7yCjUMe', 1, 'active', 'accountant', 0, NULL, '2026-03-16 08:12:46', '2026-03-16 08:12:46', NULL, NULL),
(36, 'TP-2026-0001', 'Bernandino', 'Cezar', '', 'bernandinoceza@gmail.comm', '09632884241', '2003-12-12', 22, 'Male', 'CNSC', '', 'Filipino', 'uploads/profile_pictures/user_36_1773714528.jpeg', 1, '2026-03-17 10:28:48', '$2y$10$ewXMIIAapfGLJ0bl577gMOZwrbtLdlEmAgzQuVks4JaTFrPLn6Lqe', 1, 'active', 'examinee', 1, NULL, '2026-03-17 10:19:40', '2026-04-09 13:12:44', '2026-03-28 16:48:16', NULL),
(37, 'TP-2026-000011', 'Ivan', 'Lee', 'O.', 'ivssalmadrones@gmail.com', '09108987920', '2004-02-10', 22, 'Male', 'PMAA', '', 'Filipino', 'uploads/profile_pictures/user_37_1774077171.PNG', 2, '2026-03-21 15:12:51', '$2y$10$4K/PTo/GB7fh15bLTewcgeve.HmvBSd37cQqaGjqzkqvmhgO.CtjG', 1, 'active', 'examinee', 0, NULL, '2026-03-20 10:54:06', '2026-03-21 15:12:51', NULL, NULL),
(38, 'TP-2025-0010', 'Parane', 'Mikaela', 'L', 'mamikaelaparane22@gmail.com', '09999999999', '2002-09-22', 23, 'Female', 'RTU', '', 'Filipino', 'uploads/profile_pictures/user_38_1774080693.PNG', 1, '2026-03-21 16:11:33', '$2y$10$3vWyNdkV2BluchFqr/32SuKMrjmXPGWiNAmg8hzi4bhz5./WpTOz6', 1, 'active', 'examinee', 0, NULL, '2026-03-21 16:08:09', '2026-03-21 16:11:33', NULL, NULL),
(39, '12345', 'Almadrones', 'Lee Ivan', 'Oliva', 'leeivanalmadrones@gmail.com', '280', '2026-03-27', 0, 'Male', 'hnn', '', 'Filipino', 'uploads/profile_pictures/user_39_1774761621.jpg', 2, '2026-03-29 13:20:21', '$2y$10$0wGpyzNF/po/6KYH.kIuxOIOPdupPfWPVqLgSLMYBimfa5rI0wyme', 1, 'active', 'examinee', 0, NULL, '2026-03-28 16:52:17', '2026-03-30 08:30:32', NULL, NULL),
(40, 'PSI-00013', 'Limpin', 'Albert', 'R.', 'psi.albertlimpin@gmail.com', '09926813321', '1987-06-09', 38, 'Male', 'PSI University', '', 'Filipino', 'uploads/profile_pictures/user_40_1774859752.png', 1, '2026-03-30 16:35:52', '$2y$10$XdOAxHXu4BZB3lI1y1IemumLW5rBG.CkJxJlIPktxyqGzXB8slumS', 1, 'active', 'examinee', 0, NULL, '2026-03-30 16:16:22', '2026-03-30 16:35:52', NULL, NULL),
(41, 'PSI-00025', 'Gabo', 'John Paul', 'J.', 'johnpaulgabo464@gmail.com', '09630257890', '2003-11-21', 22, 'Male', 'Camarines Norte State College', '', 'Filipino', 'uploads/profile_pictures/user_41_1774859615.png', 1, '2026-03-30 16:33:35', '$2y$10$yF7o/4dJbyvvgQy0NdH0oeLs49nH4JoZfdnVyYR4ho8Ma9m4aKIJ6', 1, 'active', 'examinee', 0, NULL, '2026-03-30 16:28:55', '2026-03-30 16:33:35', NULL, NULL),
(42, 'PSI-00027', 'Almadrones', 'Lee Ivan', 'O.', 'leeivanalmadrones6@gmail.com', '09108987920', '2004-09-10', 21, 'Male', 'PMMA', '', 'Filipino', 'uploads/profile_pictures/user_42_1774859630.png', 1, '2026-03-30 16:33:50', '$2y$10$HXC/SFftbITLRw4rzLLlreWwwPMgVRbrLhLzke3SSs9w4LD6fIU/m', 1, 'active', 'examinee', 0, NULL, '2026-03-30 16:32:06', '2026-03-30 16:33:50', NULL, NULL),
(43, 'PSI-00026', 'Bernandino', 'Cezar', 'I.', 'bernandinocezar@gmail.comm', '09632884241', '2003-12-12', 22, 'Male', 'CNSC', '', 'Filipino', 'uploads/profile_pictures/user_43_1774859875.PNG', 1, '2026-03-30 16:37:55', '$2y$10$W8iFlYJxK8LhibtlIAzTT.gxMciRK99MZAI37v6GXiBKqYwvKXgFO', 1, 'active', 'examinee', 0, NULL, '2026-03-30 16:32:44', '2026-04-08 08:37:37', NULL, NULL),
(44, 'PSI-00024', 'Tipon', 'Mary Joy', 'L.', 'joy.llaguno022@gmail.com', '09478894310', '2000-08-03', 25, 'Female', 'PUP', '', 'Filipino', 'uploads/profile_pictures/user_44_1774860877.jpeg', 1, '2026-03-30 16:54:37', '$2y$10$LaUudVdfKPme/q3mE82BF.ywEIZ0aFoAdTqmcuKH3z7vvNpOHjPDy', 1, 'active', 'examinee', 0, NULL, '2026-03-30 16:37:43', '2026-03-30 16:54:37', NULL, NULL),
(45, 'PSI-00004', 'Tibay', 'Gracelyn Joy', 'P.', 'gjptibay22@gmail.com', '09701543749', '2001-12-22', 24, 'Female', 'University of Makati', '', 'Filipino', 'uploads/profile_pictures/user_45_1774861274.jpg', 1, '2026-03-30 17:01:14', '$2y$10$nNCdswAZHjO7DY0HYkv1POSn.g0A06JGWGzZWBsyt3SkOB2dcu9Y6', 1, 'active', 'examinee', 0, NULL, '2026-03-30 16:39:59', '2026-03-30 17:01:14', NULL, NULL),
(46, 'PSI-00005', 'Reontoy Jr.', 'Ernesto', 'D.', 'ernestoreontoyjr@gmail.com', '09654402244', '1995-05-04', 30, 'Male', 'Pinagbuhatan High School', '', 'Filipino', 'uploads/profile_pictures/user_46_1774860614.jpg', 2, '2026-03-30 16:50:14', '$2y$10$r8OD5gZstbHHzAN4FcCNJetB0Q10UVyO1k1LjMGQffj4jQmJzBaL2', 1, 'active', 'examinee', 0, NULL, '2026-03-30 16:40:59', '2026-03-30 16:50:14', NULL, NULL),
(47, 'PSI-00008', 'Desamero', 'Camille', 'B.', 'desamerocamille@gmail.com', '09159156382', '2000-12-26', 25, 'Female', 'Centro Escolar University - Manila', '', 'Filipino', 'uploads/profile_pictures/user_47_1774860972.JPG', 1, '2026-03-30 16:56:12', '$2y$10$9RnfrZbxybVP21WAi0I/U.EJcofS05ur.MLuxAuC1DNPftJmxAVf2', 1, 'active', 'examinee', 0, NULL, '2026-03-30 16:44:15', '2026-03-30 16:56:12', NULL, NULL),
(48, 'PSI-00009', 'Kawabata', 'Lerry Ann', '', 'lerrykawabata@gmail.com', '09762908277', '2002-06-25', 23, 'Female', 'Rizal Technological University', '', 'Filipino', 'uploads/profile_pictures/user_48_1774861435.jpg', 2, '2026-03-30 17:03:55', '$2y$10$yN8EQqtREoX.3KoP8sEka.tdfi0VfawhSwjiyUfnSc5.fl3UYp0bu', 1, 'active', 'examinee', 0, NULL, '2026-03-30 16:50:56', '2026-03-30 17:03:55', NULL, NULL),
(49, 'PSI-00021', 'Parane', 'Ma. Mikaela', 'L.', 'elthallium@gmail.com', '09166516837', '2002-09-22', 23, 'Female', 'Rizal Technological University', '', 'Filipino', 'uploads/profile_pictures/user_49_1774861293.jpeg', 1, '2026-03-30 17:01:33', '$2y$10$o2U5V2pwJ1rfsbXqAlWIn.8hFrB1hswZtpYgBvKwIDCU7ZHdCQNvW', 1, 'active', 'examinee', 0, NULL, '2026-03-30 16:55:10', '2026-03-30 17:01:33', NULL, NULL),
(50, 'PSI-00011', 'Sarmiento', 'Kristine', 'D.', 'ksarmiento2025@gmail.com', '09917800300', '2003-02-10', 23, 'Female', 'PSI', '', 'Filipino', 'uploads/profile_pictures/user_50_1774861917.jpg', 1, '2026-03-30 17:11:57', '$2y$10$Xx17n.w6o0FAhtOAYnPJ8udAw6iy25vlqEuOyguTlg3vd7.BMtK1e', 1, 'active', 'examinee', 0, NULL, '2026-03-30 16:56:44', '2026-03-30 17:11:57', NULL, NULL),
(51, 'PSI-00012', 'Ochoa', 'Juliah Marie', 'D.', 'juliahmarieochoa@gmail.com', '09953491727', '2003-05-10', 22, 'Female', 'PSI', '', 'Filipino', 'uploads/profile_pictures/user_51_1774861823.jpeg', 1, '2026-03-30 17:10:23', '$2y$10$vu83gayrRhAlcY2tXbqA7O90XRbgGJEsYZEsRmuvarclG2EQXChPi', 1, 'active', 'examinee', 0, NULL, '2026-03-30 16:58:50', '2026-03-30 17:10:23', NULL, NULL),
(60, 'TP-2026-2023', 'Bernandino', 'Cezar', '', 'bernandinocezar@gmail.com', '09632884241', '2026-04-09', 0, 'Male', 'Cnsc', '', 'Filipino', NULL, 0, NULL, '$2y$10$phqrhKg7UEaFOinK2olGQOAadUq0qWfEAg44w49UPnWDvMTDh5mtK', 1, 'active', 'examinee', 0, NULL, '2026-04-09 23:31:05', '2026-04-09 23:34:35', NULL, NULL),
(61, '2025-07-00517', 'NIETO', 'YUAN KRISTOFF', 'SANTIAGO', 'susanalmadrones6@gmail.com', '09108987920', '2026-02-19', 0, 'Male', 'CNSC', '', 'Filipino', NULL, 0, NULL, '$2y$10$Uiz4sAjTSin98sfglw7QyuuAhD7BSOD2tjYdRqhU5evzaevI0GNHq', 1, 'incomplete', 'examinee', 0, NULL, '2026-04-10 13:35:47', '2026-04-10 13:35:47', NULL, NULL);

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
(5, 'Metro Manila (Intramuros, Manila)', 'Luzon'),
(6, 'Baguio City  (Teachers\' Camp, Pacdal, Baguio City)', 'Luzon'),
(7, 'Pasig City', 'Luzon'),
(8, 'Dohera Hotel (Cebu City)', 'Visayas'),
(9, 'Pasig City', 'Visayas'),
(10, 'mind', 'Mindanao'),
(11, 'Daet', 'Luzon'),
(12, 'YWCA - Manila', 'Luzon'),
(13, 'Ilo-ilo University', 'Visayas'),
(14, 'Xentro Hotel - Cagayan De Oro', 'Mindanao'),
(15, 'Labo', 'Luzon'),
(16, 'Test only', 'Luzon'),
(17, 'ROSARIO PAVILLION, Rosario Stript, Limketkai Center, Cagayan De Oro', 'Mindanao'),
(18, 'LA CASA DE ALMA EVENT PAVILLION, Villa Lizares Subd., Tabuc Suba, Jaro, Iloilo City', 'Visayas'),
(19, 'Test', 'Luzon'),
(20, 'YWCA Founder\'s of the Philippines, Inc - 1144 General Luna St., Paco, Manila', 'Luzon'),
(21, 'For Testing', 'Luzon');

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
-- Indexes for table `examinee_meals`
--
ALTER TABLE `examinee_meals`
  ADD PRIMARY KEY (`examinee_meal_id`),
  ADD UNIQUE KEY `uq_user_meal` (`user_id`,`meal_id`),
  ADD KEY `fk_examinee_meals_meal` (`meal_id`);

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
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=635;

--
-- AUTO_INCREMENT for table `examinees`
--
ALTER TABLE `examinees`
  MODIFY `examinee_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `examinee_masterlist`
--
ALTER TABLE `examinee_masterlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6947;

--
-- AUTO_INCREMENT for table `examinee_meals`
--
ALTER TABLE `examinee_meals`
  MODIFY `examinee_meal_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `faqs`
--
ALTER TABLE `faqs`
  MODIFY `faq_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `meals`
--
ALTER TABLE `meals`
  MODIFY `meal_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `otp_verifications`
--
ALTER TABLE `otp_verifications`
  MODIFY `verification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `reset_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `rate_limits`
--
ALTER TABLE `rate_limits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `schedule_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `venue`
--
ALTER TABLE `venue`
  MODIFY `venue_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

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
-- Constraints for table `examinee_meals`
--
ALTER TABLE `examinee_meals`
  ADD CONSTRAINT `fk_examinee_meals_meal` FOREIGN KEY (`meal_id`) REFERENCES `meals` (`meal_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_examinee_meals_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
