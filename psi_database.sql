-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 11, 2026 at 08:21 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- Database: `psi_database`

-- --------------------------------------------------------

-- Table structure for table `payments`

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `transaction_no` int(11) NOT NULL,
  `payment_date` date NOT NULL,
  `payment_amount` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `test_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `payments`

INSERT INTO `payments` (`payment_id`, `transaction_no`, `payment_date`, `payment_amount`, `user_id`, `test_id`) VALUES
(1, 123456789, '2026-02-11', 1600, 3, 2);

-- --------------------------------------------------------

--
-- Table structure for table `test`
--

CREATE TABLE `test` (
  `test_id` int(50) NOT NULL,
  `user_id` int(50) NOT NULL,
  `date_of_test` datetime NOT NULL,
  `date_of_registration` date NOT NULL DEFAULT current_timestamp(),
  `venue` varchar(250) NOT NULL,
  `test_permit` varchar(50) NOT NULL,
  `purpose` varchar(255) NOT NULL,
  `status` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `test`
--

INSERT INTO `test` (`test_id`, `user_id`, `date_of_test`, `date_of_registration`, `venue`, `test_permit`, `purpose`, `status`) VALUES
(1, 1, '2026-02-15 12:00:00', '2026-02-11', 'Labo, Camarines Norte, Luzon', '90-8008-09', '', '0'),
(2, 3, '2026-02-11 02:23:02', '2026-02-11', 'Muntinlupa', '676-0808', '', 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `middle_name` varchar(1) NOT NULL,
  `email` varchar(255) NOT NULL,
  `date_of_birth` date NOT NULL,
  `age` int(3) NOT NULL,
  `gender` varchar(6) NOT NULL,
  `address` varchar(50) NOT NULL,
  `nationality` varchar(50) NOT NULL,
  `contact_number` int(11) NOT NULL,
  `password` varchar(255) NOT NULL,
  `pmma_student_id` varchar(32) DEFAULT NULL,
  `test_permit` varchar(32) DEFAULT NULL,
  `otp` varchar(6) DEFAULT NULL,
  `otp_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `last_name`, `first_name`, `middle_name`, `email`, `date_of_birth`, `age`, `gender`, `address`, `nationality`, `contact_number`, `password`, `pmma_student_id`, `test_permit`) VALUES
(1, 'Bernandino', 'Cezar', 'I', 'bernandinocezar@gmail.com', '2003-02-12', 22, '', 'P-3 Lugui LCN', '', 963288424, 'cezar1212', NULL, NULL),
(2, 'Almadrones', 'Lee Ivan', 'O', 'leeivanalmadrones6@gmail.com', '2004-09-10', 21, '', 'Purok 3 Lugui Labo Camarines Norte', '', 2147483647, '$2y$10$S.dQ1huZ/X25vFuRwsC8qOaGN1ISJ//t/lVRNxH8Zl5E0OuEGWdi6', NULL, NULL),
(3, 'Bernandino', 'Cezar', 'I', 'cezarbernandino12@gmail.com', '2026-02-03', 0, 'Male', 'Labo CN', 'FIlipino', 90908777, '$2y$10$aVHzKHAZFsXnhg/Qbu6FfO1p9zLwoyJ1Y7NlXbSSK9xnXK7/Dftj2', '22-1512', '09-0909-09');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`);

--
-- Indexes for table `test`
--
ALTER TABLE `test`
  ADD PRIMARY KEY (`test_id`),
  ADD KEY `fk_test_user` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `uq_users_email` (`email`),
  ADD UNIQUE KEY `pmma_student_id` (`pmma_student_id`),
  ADD UNIQUE KEY `test_permit` (`test_permit`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `test`
--
ALTER TABLE `test`
  MODIFY `test_id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `test`
--
ALTER TABLE `test`
  ADD CONSTRAINT `fk_test_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
