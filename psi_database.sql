-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 13, 2026 at 09:51 AM
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
-- Database: `psi_database`
--

-- --------------------------------------------------------

--
-- Table structure for table `examinees`
--

CREATE TABLE `examinees` (
  `test_id` int(50) NOT NULL,
  `user_id` int(50) NOT NULL,
  `date_of_test` datetime NOT NULL,
  `date_of_registration` date NOT NULL DEFAULT current_timestamp(),
  `venue` varchar(250) NOT NULL,
  `test_permit` varchar(50) NOT NULL,
  `purpose` varchar(255) NOT NULL,
  `status` varchar(100) NOT NULL,
  `schedule_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `examinees`
--

INSERT INTO `examinees` (`test_id`, `user_id`, `date_of_test`, `date_of_registration`, `venue`, `test_permit`, `purpose`, `status`, `schedule_id`) VALUES
(1, 1, '2026-02-15 12:00:00', '2026-02-11', 'Labo, Camarines Norte, Luzon', '90-8008-09', '', '0', NULL),
(2, 3, '2026-02-11 02:23:02', '2026-02-11', 'Muntinlupa', '676-0808', '', 'Pending', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `otp_verifications`
--

CREATE TABLE `otp_verifications` (
  `verification_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `otp` varchar(6) NOT NULL,
  `otp_attempts` int(3) DEFAULT 0,
  `is_used` tinyint(1) DEFAULT 0,
  `purpose` varchar(50) NOT NULL DEFAULT 'registration',
  `created_at` datetime DEFAULT current_timestamp(),
  `expires_at` datetime NOT NULL,
  `verified_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `otp_verifications`
--

INSERT INTO `otp_verifications` (`verification_id`, `email`, `otp`, `otp_attempts`, `is_used`, `purpose`, `created_at`, `expires_at`, `verified_at`) VALUES
(1, 'laarnialmadrones@gmail.com', '878865', 0, 0, 'registration', '2026-02-13 14:50:31', '2026-02-13 08:00:31', NULL);

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
(1, 2, 'leeivanalmadrones6@gmail.com', '461391', 0, 0, '2026-02-13 14:25:38', '2026-02-13 07:35:38', NULL),
(2, 2, 'leeivanalmadrones6@gmail.com', '353748', 0, 0, '2026-02-13 14:38:00', '2026-02-13 07:48:00', NULL),
(3, 2, 'leeivanalmadrones6@gmail.com', '968118', 1, 0, '2026-02-13 15:04:05', '2026-02-13 08:14:05', NULL),
(4, 2, 'leeivanalmadrones6@gmail.com', '103109', 0, 0, '2026-02-13 15:04:57', '2026-02-13 08:14:57', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `transaction_no` int(11) NOT NULL,
  `payment_date` date NOT NULL,
  `payment_amount` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `test_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `transaction_no`, `payment_date`, `payment_amount`, `user_id`, `test_id`) VALUES
(1, 123456789, '2026-02-11', 1600, 3, 2);

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

CREATE TABLE `schedules` (
  `schedule_id` int(11) NOT NULL,
  `venue_id` int(11) NOT NULL,
  `schedule_datetime` datetime NOT NULL,
  `num_of_examinees` int(11) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Incoming'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedules`
--

INSERT INTO `schedules` (`schedule_id`, `venue_id`, `schedule_datetime`, `num_of_examinees`, `status`) VALUES
(1, 1, '2026-02-17 00:00:00', 200, 'Incoming'),
(2, 2, '2026-02-19 00:00:00', 100, 'Incoming'),
(3, 3, '2026-02-23 00:00:00', 300, 'Incoming'),
(4, 3, '2026-02-23 00:00:00', 300, 'Incoming'),
(7, 2, '2026-02-16 00:00:00', 1000, 'Incoming'),
(8, 2, '2026-10-23 00:00:00', 200, 'Incoming'),
(9, 5, '2026-02-26 00:00:00', 250, 'Incoming'),
(10, 1, '2026-03-04 00:00:00', 200, 'Incoming'),
(11, 1, '2026-02-23 00:00:00', 300, 'Incoming'),
(15, 1, '2026-03-12 00:00:00', 400, 'Incoming'),
(16, 1, '2026-04-04 00:00:00', 250, 'Incoming'),
(17, 1, '2026-03-04 00:00:00', 3000, 'Incoming');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `test_permit` varchar(32) DEFAULT NULL,
  `pmma_student_id` varchar(32) DEFAULT NULL,
  `last_name` varchar(50) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `middle_name` varchar(1) NOT NULL,
  `email` varchar(255) NOT NULL,
  `contact_number` int(11) NOT NULL,
  `date_of_birth` date NOT NULL,
  `age` int(3) NOT NULL,
  `gender` varchar(6) NOT NULL,
  `school` varchar(255) DEFAULT NULL,
  `region` varchar(100) DEFAULT NULL,
  `exam_venue` varchar(255) DEFAULT NULL,
  `exam_date` date DEFAULT NULL,
  `email_verified` tinyint(1) DEFAULT 0,
  `address` varchar(50) NOT NULL,
  `nationality` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `role` enum('examinee','admin') DEFAULT 'examinee',
  `date_of_registration` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `test_permit`, `pmma_student_id`, `last_name`, `first_name`, `middle_name`, `email`, `contact_number`, `date_of_birth`, `age`, `gender`, `school`, `region`, `exam_venue`, `exam_date`, `email_verified`, `address`, `nationality`, `password`, `status`, `role`, `date_of_registration`) VALUES
(1, NULL, NULL, 'Bernandino', 'Cezar', 'I', 'ernandinocezar@gmail.com', 963288424, '2003-02-12', 22, '', NULL, NULL, NULL, NULL, 0, 'P-3 Lugui LCN', '', 'cezar1212', '', '', '2026-02-13 16:48:25'),
(2, NULL, NULL, 'Almadrones', 'Lee Ivan', 'O', 'leeivanalmadrones6@gmail.com', 2147483647, '2004-09-10', 21, '', NULL, NULL, NULL, NULL, 0, 'Purok 3 Lugui Labo Camarines Norte', '', '$2y$10$S.dQ1huZ/X25vFuRwsC8qOaGN1ISJ//t/lVRNxH8Zl5E0OuEGWdi6', '', '', '2026-02-13 16:48:25'),
(3, '09-0909-09', '22-1512', 'Bernandino', 'Cezar', 'I', 'cezarbernandino12@gmail.com', 90908777, '2026-02-03', 18, 'Male', NULL, NULL, NULL, NULL, 0, 'Labo CN', 'FIlipino', '$2y$10$aVHzKHAZFsXnhg/Qbu6FfO1p9zLwoyJ1Y7NlXbSSK9xnXK7/Dftj2', '', '', '2026-02-13 16:48:25'),
(6, NULL, NULL, 'Bernandino', 'Cezar', 's', 'bernandinoceza@gmail.com', 963234542, '2026-02-11', 0, 'male', NULL, NULL, NULL, NULL, 0, '', '', '$2y$10$hJJf7rVeavqbaEENsKOMEeZNQdE/buYQhXT5Rn6EUePaZUBy9hKOe', '', '', '2026-02-13 16:48:25'),
(15, NULL, NULL, 'Bernandino', 'Ceza', 's', 'bernandinocear@gmail.com', 963234542, '2026-02-11', 0, '', NULL, NULL, NULL, NULL, 0, '', '', '$2y$10$3/VyMuYldB8.vWPq1CCjd.1wL57Tj2GLUAPfth1WU9nIHJGT9SUXu', '', '', '2026-02-13 16:48:25'),
(18, NULL, NULL, 'Bernando', 'Cezar', 'I', 'bernandocezar@gmail.com', 963234542, '2026-02-11', 0, '', NULL, NULL, NULL, NULL, 0, '', '', '$2y$10$VP4STuI94nNvGvhUNuOFDu.I//pUPZDktMSVIgBa99OpaPsBlpU2.', '', '', '2026-02-13 16:48:25'),
(20, NULL, NULL, 'onidnanreb', 'Cezarrrr', 's', 'bernandinicezar@gmail.com', 963234542, '2026-02-11', 0, 'male', NULL, NULL, NULL, NULL, 0, '', '', '$2y$10$EMlfA8JAk.hKfo3PSWxwqeEmNJ8PHB0pKPSWqv7tE5Ik.g6P/hVNq', '', '', '2026-02-13 16:48:25'),
(21, NULL, NULL, 'onidnanreb', 'Cezarrrr', 's', 'nandinocezar@gmail.com', 963234542, '2026-02-12', 0, 'male', NULL, NULL, NULL, NULL, 0, '', '', '$2y$10$piPzS1RXw58S7Yep5ObS9.yPRveFNJ9sTKESPl.4eh/kuzyrQztt2', '', '', '2026-02-13 16:48:25'),
(23, 'ADMIN-0001', 'ADMIN', 'Administrator', 'System', 'O', 'ryanalmadrones6@gmail.com', 0, '1990-01-01', 30, 'Male', 'PMMA', 'NCR', 'Main Office', '2026-01-01', 1, 'PMMA Headquarters', 'Filipino', '$2y$10$3M4xPELGhhyz0wlEVt5pfuU4BRzj5tw3m13KoZnTtIU8wBwyyj4CS', 'active', 'admin', '2026-02-13 16:48:25'),
(24, 'PMMA-2026-0001', '2026-001', 'Dela Cruz', 'Juan', 'S', 'ivssalmadrones@gmail.com', 2147483647, '2004-05-15', 21, 'Male', 'PMMA', 'NCR', 'Manila Testing Center', '2026-03-15', 1, '123 Mabini Street, Manila', 'Filipino', '$2y$10$JgUZLxGMspH5xt44OACqMOwo/9r45L0Uzb2iWgUF7XLmI8cFxMXQG', 'active', 'examinee', '2026-02-13 16:48:25');

-- --------------------------------------------------------

--
-- Table structure for table `venue`
--

CREATE TABLE `venue` (
  `venue_id` int(11) NOT NULL,
  `venue_name` varchar(255) NOT NULL,
  `region` varchar(50) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `venue`
--

INSERT INTO `venue` (`venue_id`, `venue_name`, `region`) VALUES
(1, 'Manila', 'Luzon'),
(2, 'Cebu', 'Visayas'),
(3, 'Bicol', 'Luzon'),
(5, 'Davao', 'Mindanao');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `examinees`
--
ALTER TABLE `examinees`
  ADD PRIMARY KEY (`test_id`),
  ADD KEY `fk_test_user` (`user_id`),
  ADD KEY `fk_test_schedule` (`schedule_id`);

--
-- Indexes for table `otp_verifications`
--
ALTER TABLE `otp_verifications`
  ADD PRIMARY KEY (`verification_id`),
  ADD KEY `idx_email_purpose` (`email`,`purpose`),
  ADD KEY `idx_email_otp` (`email`,`otp`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`reset_id`),
  ADD KEY `fk_password_resets_user` (`user_id`),
  ADD KEY `idx_email_otp` (`email`,`otp`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`);

--
-- Indexes for table `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`schedule_id`),
  ADD KEY `fk_schedules_venue` (`venue_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `uq_users_email` (`email`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `unique_person` (`first_name`,`last_name`,`middle_name`,`date_of_birth`),
  ADD UNIQUE KEY `pmma_student_id` (`pmma_student_id`),
  ADD UNIQUE KEY `test_permit` (`test_permit`),
  ADD UNIQUE KEY `test_permit_2` (`test_permit`);

--
-- Indexes for table `venue`
--
ALTER TABLE `venue`
  ADD PRIMARY KEY (`venue_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `examinees`
--
ALTER TABLE `examinees`
  MODIFY `test_id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `otp_verifications`
--
ALTER TABLE `otp_verifications`
  MODIFY `verification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `reset_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `schedule_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `venue`
--
ALTER TABLE `venue`
  MODIFY `venue_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `examinees`
--
ALTER TABLE `examinees`
  ADD CONSTRAINT `fk_test_schedule` FOREIGN KEY (`schedule_id`) REFERENCES `schedules` (`schedule_id`),
  ADD CONSTRAINT `fk_test_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `fk_password_resets_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `schedules`
--
ALTER TABLE `schedules`
  ADD CONSTRAINT `fk_schedules_venue` FOREIGN KEY (`venue_id`) REFERENCES `venue` (`venue_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
