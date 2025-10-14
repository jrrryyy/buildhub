-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 14, 2025 at 11:06 AM
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
-- Database: `user_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `weight` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `file` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `product_name`, `weight`, `price`, `quantity`, `file`) VALUES
(39, 'pogi', 69, 69, 69, '3 layer switch.png'),
(42, 'asd', 234, 324, 234, 'day6.mp4'),
(43, 'asd', 234, 324, 234, 'umayno.jpg'),
(44, 'Asd', 45, 53, 554, 'wp.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL,
  `fname` varchar(255) NOT NULL,
  `lname` varchar(2255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('buyer','seller') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fname`, `lname`, `email`, `password`, `role`) VALUES
(41, 'seller', 'Yulo', 'seller@gamil.com', '$2y$10$JohMxuOKGp.yIJbNoUQIB.jW47ufi2C7lHwK6jTElW4Hm1FBQ21Ti', 'seller'),
(42, 'Jed Roven', 'Yulo', 'roven.yulo@gmail.com', '$2y$10$H4loCTftQLT1qBstUiqT..90jfvLaW5L/DreSdqNAaaPYN2KHLQR.', 'buyer'),
(43, 'Jed Roven', 'Yulo', 'buyer@gamil.com', '$2y$10$cCNrD58T2HI8POOuTt893OZyT26GouqiDUqcw6KA3.aze11hDnuZG', 'buyer'),
(44, 'tae', 'emman', 'tae@gmail.com', '$2y$10$g.HJDdHQYEdvLty2ynOHgO3EDPRTfDJQGV4gI0gIvXhss5QI8uNO6', 'buyer'),
(45, 'asd', 'Yulo', 'buyerr@gmail.com', '$2y$10$Iz5nlyfXFfF9u6vbGiDYjOLIlumDCHrKmMpQS3/l.qHPiTU3v4Sq2', 'buyer'),
(46, 'ken', 'estayo', 'ken@gmail.com', '$2y$10$1BKOx.dvYWIxFs.R03cAOeWkjDiXjtVOwx9eFyshfEJvlJelhsJy6', 'buyer'),
(47, '', '', '', '$2y$10$/T6HHRsJHyAQQNXJIU6dmuNK7RtEKHsXWDzPdmcq5XCLnRMbN49vK', 'buyer');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQUE` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
