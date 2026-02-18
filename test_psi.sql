-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 18, 2026 at 02:10 AM
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
-- Table structure for table `examinees`
--

CREATE TABLE `examinees` (
  `examinee_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `test_permit` varchar(50) NOT NULL,
  `schedule_id` int(11) DEFAULT NULL,
  `status` enum('Pending','Awaiting Payment','Scheduled','Completed') DEFAULT 'Pending',
  `date_of_registration` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `examinees`
--

INSERT INTO `examinees` (`examinee_id`, `user_id`, `test_permit`, `schedule_id`, `status`, `date_of_registration`) VALUES
(1, 2, '22-0828', NULL, 'Pending', '2026-02-18 08:47:01');

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
(1, '901-2345', 'Diaz', 'Kimberly', 'Louise', 'kimberly.diaz@example.com', 0, NULL, '2026-02-18 00:19:18'),
(2, '112-2334', 'Valdez', 'Christian', 'Mark', 'christian.valdez@example.com', 0, NULL, '2026-02-18 00:19:18'),
(3, '223-3445', 'Alvarez', 'Samantha', 'Jane', 'samantha.alvarez@example.com', 0, NULL, '2026-02-18 00:19:18'),
(4, '2026-102', 'Dela Cruz', 'Maria', '', 'maria.delacruz@example.com', 0, NULL, '2026-02-18 00:19:18'),
(5, '2026-103', 'De Guzman', 'Carlos', 'Miguel', 'carlos.deguzman@example.com', 0, NULL, '2026-02-18 00:19:18'),
(6, '2026-104', 'San Juan', 'Ana', 'Sofia', 'ana.sanjuan@example.com', 0, NULL, '2026-02-18 00:19:18'),
(7, '2026-105', 'Dela Peña', 'Pedro', '', 'pedro.delapena@example.com', 0, NULL, '2026-02-18 00:19:18'),
(8, '222-3333', 'Martinez', 'Luis', 'Antonio', 'luis.martinez@example.com', 0, NULL, '2026-02-18 00:19:18'),
(9, '333-4444', 'Ramos', 'Isabella', 'Marie', 'isabella.ramos@example.com', 0, NULL, '2026-02-18 00:19:18'),
(10, '444-5555', 'Gonzales', 'Mark', 'Anthony', 'mark.gonzales@example.com', 0, NULL, '2026-02-18 00:19:18'),
(11, '555-6666', 'Bautista', 'Angela', 'Rose', 'angela.bautista@example.com', 0, NULL, '2026-02-18 00:19:18'),
(12, '666-7777', 'Navarro', 'Michael', 'James', 'michael.navarro@example.com', 0, NULL, '2026-02-18 00:19:18'),
(13, '777-8888', 'Villanueva', 'Sarah', 'Joy', 'sarah.villanueva@example.com', 0, NULL, '2026-02-18 00:19:18'),
(14, '888-0001', 'Cruz', 'Daniel', 'Lee', 'daniel.cruz@example.com', 0, NULL, '2026-02-18 00:19:18'),
(15, '999-0002', 'Gomez', 'Patricia', 'Anne', 'patricia.gomez@example.com', 0, NULL, '2026-02-18 00:19:18'),
(16, '000-1111', 'Ramirez', 'Joshua', 'Paul', 'joshua.ramirez@example.com', 0, NULL, '2026-02-18 00:19:18'),
(17, '123-4567', 'Lim', 'Christine', 'Mae', 'christine.lim@example.com', 0, NULL, '2026-02-18 00:19:18'),
(18, '234-5678', 'Aquino', 'Kevin', 'John', 'kevin.aquino@example.com', 0, NULL, '2026-02-18 00:19:18'),
(19, '345-6789', 'Flores', 'Karen', 'Grace', 'karen.flores@example.com', 0, NULL, '2026-02-18 00:19:18'),
(20, '456-7890', 'Mendoza', 'Ryan', 'Patrick', 'ryan.mendoza@example.com', 0, NULL, '2026-02-18 00:19:18'),
(21, '567-8901', 'Torres', 'Jessica', 'Claire', 'jessica.torres@example.com', 0, NULL, '2026-02-18 00:19:18'),
(22, '678-9012', 'Morales', 'Adrian', 'Joseph', 'adrian.morales@example.com', 0, NULL, '2026-02-18 00:19:18'),
(23, '789-0123', 'Castro', 'Nicole', 'Faith', 'nicole.castro@example.com', 0, NULL, '2026-02-18 00:19:18'),
(24, '890-1234', 'Ortiz', 'Brandon', 'Keith', 'brandon.ortiz@example.com', 0, NULL, '2026-02-18 00:19:18'),
(25, '22-0828', 'Gabo', 'John Paul', '', 'ivssalmadrones@gmail.com', 1, 2, '2026-02-18 00:20:49');

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
(2, 'ivssalmadrones@gmail.com', '404588', 0, 0, 'registration', '2026-02-18 08:46:14', '2026-02-18 01:56:14', NULL);

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
  `transaction_no` varchar(50) NOT NULL,
  `payment_amount` decimal(10,2) NOT NULL,
  `payment_date` datetime DEFAULT current_timestamp(),
  `user_id` int(11) NOT NULL,
  `examinee_id` int(11) NOT NULL,
  `status` enum('PENDING','PAID','FAILED','EXPIRED') DEFAULT 'PENDING',
  `xendit_invoice_id` varchar(100) NOT NULL,
  `email_sent` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

CREATE TABLE `schedules` (
  `schedule_id` int(11) NOT NULL,
  `venue_id` int(11) NOT NULL,
  `schedule_datetime` datetime NOT NULL,
  `capacity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `status` enum('Incoming','Closed','Completed') DEFAULT 'Incoming',
  `num_registered` int(11) NOT NULL DEFAULT 0,
  `num_of_examinees` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedules`
--

INSERT INTO `schedules` (`schedule_id`, `venue_id`, `schedule_datetime`, `capacity`, `price`, `status`, `num_registered`, `num_of_examinees`) VALUES
(22, 1, '2026-02-17 00:00:00', 0, 0.00, 'Incoming', 45, 200),
(23, 2, '2026-02-19 00:00:00', 0, 0.00, 'Incoming', 0, 100),
(24, 3, '2026-02-23 00:00:00', 0, 0.00, 'Incoming', 0, 300),
(25, 3, '2026-02-23 00:00:00', 0, 0.00, 'Incoming', 34, 300),
(26, 2, '2026-02-16 00:00:00', 0, 0.00, 'Incoming', 0, 1000),
(27, 2, '2026-10-23 00:00:00', 0, 0.00, 'Incoming', 108, 200),
(28, 4, '2026-02-26 00:00:00', 0, 0.00, 'Incoming', 59, 250),
(29, 1, '2026-03-04 00:00:00', 0, 0.00, 'Incoming', 38, 200),
(30, 1, '2026-02-23 00:00:00', 0, 0.00, 'Incoming', 0, 300),
(31, 1, '2026-03-12 00:00:00', 0, 0.00, 'Incoming', 209, 400),
(32, 1, '2026-04-04 00:00:00', 0, 0.00, 'Incoming', 40, 250),
(33, 1, '2026-03-04 00:00:00', 0, 0.00, 'Incoming', 0, 3000),
(34, 5, '2026-03-01 00:00:00', 0, 0.00, 'Incoming', 378, 500),
(35, 6, '2026-03-10 00:00:00', 0, 0.00, 'Incoming', 0, 300),
(36, 7, '2026-03-20 00:00:00', 0, 0.00, 'Incoming', 582, 1000),
(37, 8, '2026-03-30 00:00:00', 0, 0.00, 'Incoming', 78, 100),
(38, 9, '2026-04-01 00:00:00', 0, 0.00, 'Incoming', 0, 200),
(39, 10, '2026-04-10 00:00:00', 0, 0.00, 'Incoming', 0, 250),
(40, 11, '2026-04-20 00:00:00', 0, 0.00, 'Incoming', 0, 300),
(41, 3, '2026-02-17 00:00:00', 0, 0.00, 'Incoming', 0, 100),
(42, 3, '2026-03-10 00:00:00', 0, 0.00, 'Incoming', 0, 150),
(43, 3, '2026-03-10 00:00:00', 0, 0.00, 'Incoming', 0, 15);

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
  `date_of_registration` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `test_permit`, `last_name`, `first_name`, `middle_name`, `email`, `contact_number`, `date_of_birth`, `age`, `gender`, `school`, `address`, `nationality`, `password`, `email_verified`, `status`, `role`, `failed_login_attempts`, `last_login`, `date_of_registration`) VALUES
(1, '-806', 'Almadrones', 'Lee Ivan', NULL, 'leeivanalmadrones6@gmail.com', '09123456789', '1995-01-01', NULL, 'Male', 'System Administrator', 'Philippines', 'Filipino', '$2y$10$GPOcbTTUMe20YIg4sFHhd.GOP7s4Qr0h/.SxvaMHOENJU4W9j9OV2', 1, 'active', 'admin', 0, NULL, '2026-02-18 08:16:17'),
(2, '22-0828', 'Gabo', 'John Paul', 'Oliva', 'ivssalmadrones@gmail.com', '0923232323', '2004-02-10', 22, 'Male', 'PMMA', '', '', '$2y$10$u69CjToEaHpzDPwlQ1fvDe.PRuYzbmTlWGP9svOfDq9uBjfBywNZS', 1, 'incomplete', 'examinee', 0, NULL, '2026-02-18 08:47:01');

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
(11, 'Tagum City', 'Mindanao');

--
-- Indexes for dumped tables
--

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
  ADD UNIQUE KEY `transaction_no` (`transaction_no`),
  ADD UNIQUE KEY `xendit_invoice_id` (`xendit_invoice_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `examinee_id` (`examinee_id`);

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
-- AUTO_INCREMENT for table `examinees`
--
ALTER TABLE `examinees`
  MODIFY `examinee_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `examinee_masterlist`
--
ALTER TABLE `examinee_masterlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `otp_verifications`
--
ALTER TABLE `otp_verifications`
  MODIFY `verification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `reset_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `schedule_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `venue`
--
ALTER TABLE `venue`
  MODIFY `venue_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

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
