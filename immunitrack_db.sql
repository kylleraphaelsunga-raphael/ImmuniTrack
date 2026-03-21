-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 20, 2026 at 06:05 AM
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
-- Database: `immunitrack_db`
--
CREATE DATABASE IF NOT EXISTS `immunitrack_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `immunitrack_db`;

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

DROP TABLE IF EXISTS `bookings`;
CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_email` varchar(100) NOT NULL,
  `vax_category` varchar(50) DEFAULT NULL,
  `vaccine_type` varchar(100) DEFAULT NULL,
  `dose_number` int(11) DEFAULT NULL,
  `booking_date` date DEFAULT NULL,
  `booking_time` time DEFAULT NULL,
  `clinic` varchar(150) DEFAULT NULL,
  `medical_condition` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('Pending','Completed','Missed','Cancelled') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_email`, `vax_category`, `vaccine_type`, `dose_number`, `booking_date`, `booking_time`, `clinic`, `medical_condition`, `notes`, `status`, `created_at`) VALUES
(17, 'tester@gmail.com', 'Chickenpox', 'Varivax', 1, '2026-04-10', '09:00:00', 'Mining Health Center', '', 'can have side effects', 'Cancelled', '2026-03-19 14:32:49'),
(19, 'tester@gmail.com', 'Rabies', 'Rabipur', 1, '2026-03-20', '11:30:00', 'Pampang Medical Center', 'Diabetes', '', 'Missed', '2026-03-19 14:35:47'),
(20, 'tester@gmail.com', 'Dengue', 'Dengvaxia', 1, '2026-03-24', '13:30:00', 'Sto. Rosario Vaccination Clinic', '', '', 'Pending', '2026-03-19 14:36:18'),
(21, 'tester@gmail.com', 'Flu', 'Vaxigrip', 2, '2026-03-20', '13:00:00', 'Mining Health Center', 'Diabetes', '', 'Completed', '2026-03-19 14:38:14');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `user_email` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_email`, `username`, `password`, `created_at`) VALUES
('tester@gmail.com', 'Tester', '$2y$10$KVnxDMb6iwjVYM9BOsfPueC2OGQavgB3Gx1..mY2NsHt5fskzJ0t6', '2026-03-07 13:48:23');

-- --------------------------------------------------------

--
-- Table structure for table `user_profile`
--

DROP TABLE IF EXISTS `user_profile`;
CREATE TABLE `user_profile` (
  `user_email` varchar(100) NOT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `middle_initial` varchar(5) DEFAULT NULL,
  `suffix` varchar(10) DEFAULT NULL,
  `sex` enum('Male','Female') DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `house_number` varchar(20) DEFAULT NULL,
  `barangay` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `province` varchar(100) DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_profile`
--

INSERT INTO `user_profile` (`user_email`, `last_name`, `first_name`, `middle_initial`, `suffix`, `sex`, `contact_number`, `date_of_birth`, `house_number`, `barangay`, `city`, `province`, `profile_pic`) VALUES
('tester@gmail.com', 'Sunga', 'Kylle Raphael', 'Y', '', 'Male', '+639999999999', '2006-09-01', '23', 'Barangay Camachiles', 'Mabalacat City', 'Pampanga', 'profile_8c3fe1ad25e6d5f47512ea7365419966.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `vaccination_history`
--

DROP TABLE IF EXISTS `vaccination_history`;
CREATE TABLE `vaccination_history` (
  `id` int(11) NOT NULL,
  `user_email` varchar(100) NOT NULL,
  `vax_category` varchar(50) DEFAULT NULL,
  `vaccine_type` varchar(100) DEFAULT NULL,
  `dose_number` int(11) DEFAULT NULL,
  `vax_date` date DEFAULT NULL,
  `status` enum('Done','Pending') DEFAULT 'Done',
  `completed` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vaccination_history`
--

INSERT INTO `vaccination_history` (`id`, `user_email`, `vax_category`, `vaccine_type`, `dose_number`, `vax_date`, `status`, `completed`) VALUES
(24, 'tester@gmail.com', 'Covid', 'Moderna', 1, '2026-03-19', 'Done', 1),
(25, 'tester@gmail.com', 'Covid', 'Moderna', 2, '2020-06-24', 'Done', 1),
(26, 'tester@gmail.com', 'Covid', 'Pfizer', 3, '2020-12-28', 'Done', 1),
(28, 'tester@gmail.com', 'Flu', 'Vaxigrip', 2, '2026-03-20', 'Done', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_booking_email` (`user_email`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_email`);

--
-- Indexes for table `user_profile`
--
ALTER TABLE `user_profile`
  ADD PRIMARY KEY (`user_email`);

--
-- Indexes for table `vaccination_history`
--
ALTER TABLE `vaccination_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_vax_email` (`user_email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `vaccination_history`
--
ALTER TABLE `vaccination_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `fk_booking_email` FOREIGN KEY (`user_email`) REFERENCES `users` (`user_email`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_profile`
--
ALTER TABLE `user_profile`
  ADD CONSTRAINT `fk_profile_email` FOREIGN KEY (`user_email`) REFERENCES `users` (`user_email`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `vaccination_history`
--
ALTER TABLE `vaccination_history`
  ADD CONSTRAINT `fk_vax_email` FOREIGN KEY (`user_email`) REFERENCES `users` (`user_email`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
