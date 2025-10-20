-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 20, 2025 at 07:38 AM
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
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) UNSIGNED NOT NULL,
  `buyer_id` int(10) UNSIGNED DEFAULT NULL,
  `supplier_id` int(10) UNSIGNED DEFAULT NULL,
  `recipient_name` varchar(100) NOT NULL,
  `address_line` varchar(255) NOT NULL,
  `province` varchar(50) DEFAULT NULL,
  `phone` varchar(20) NOT NULL,
  `schedule_date` date NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `total_amount` int(11) NOT NULL DEFAULT 0,
  `ordered_at` datetime NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','accepted','completed') NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `buyer_id`, `supplier_id`, `recipient_name`, `address_line`, `province`, `phone`, `schedule_date`, `product_name`, `total_amount`, `ordered_at`, `status`, `updated_at`) VALUES
(17, 67, 69, 'johann', 'Bari mangaldan', 'pangasinan', '90909090', '2025-10-18', 'cement', 51, '0000-00-00 00:00:00', 'completed', '2025-10-18 01:20:31'),
(18, 67, 69, 'johann', 'Bari mangaldan', 'pangasinan', '90909090', '2025-10-18', 'cement', 51, '0000-00-00 00:00:00', 'accepted', '2025-10-18 01:21:41'),
(19, 67, 69, 'johann', 'Bari mangaldan', 'pangasinan', '90909090', '2025-10-18', 'cement', 51, '0000-00-00 00:00:00', 'accepted', '2025-10-18 01:27:43'),
(20, 67, 69, 'johann', 'Bari mangaldan', 'pangasinan', '90909090', '2025-10-18', 'haha', 51, '0000-00-00 00:00:00', 'accepted', '2025-10-18 01:40:02'),
(21, 67, 69, 'johann', 'Bari mangaldan', 'pangasinan', '90909090', '2025-10-18', 'haha', 51, '0000-00-00 00:00:00', 'pending', '2025-10-18 01:54:44'),
(22, 67, 69, 'johann', 'Bari mangaldan', 'pangasinan', '90909090', '2025-10-18', 'haha', 51, '0000-00-00 00:00:00', 'pending', '2025-10-18 01:56:29');

-- --------------------------------------------------------

--
-- Table structure for table `order_reschedules`
--

CREATE TABLE `order_reschedules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `requested_by` enum('buyer','supplier') NOT NULL,
  `proposed_scheduled_at` datetime NOT NULL,
  `status` enum('pending','accepted','declined') NOT NULL DEFAULT 'pending',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `order_id` int(11) UNSIGNED NOT NULL,
  `product_id` int(11) UNSIGNED NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `unit_price` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `file` varchar(255) NOT NULL,
  `line_total` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `user_id`, `order_id`, `product_id`, `product_name`, `description`, `unit_price`, `quantity`, `file`, `line_total`, `created_at`, `updated_at`) VALUES
(47, 71, 0, 0, 'cement', 'good quality', 300, 100, '20251017_164745_0579b542.jpg', 30000, '2025-10-17 22:47:45', '2025-10-17 22:47:58'),
(49, 69, 0, 0, 'cement', '', 1, 1, '20251017_184424_9a23637260.jpg', 1, '2025-10-18 00:44:09', '2025-10-18 00:44:24'),
(50, 69, 0, 0, 'haha', '', 1, 1, '20251017_184549_c95d1a14.jpg', 1, '2025-10-18 00:45:49', '2025-10-18 01:37:05');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL,
  `fname` varchar(255) NOT NULL,
  `lname` varchar(2255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('buyer','seller') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fname`, `lname`, `email`, `profile_picture`, `password`, `role`) VALUES
(67, 'jed', 'yulo', 'jed@gmail.com', NULL, '$2y$10$Lw4an8bcBHW89h97t1IJK.pVqdJykA8dq2o/uxgkIHojF1/qhbQpu', 'buyer'),
(69, 'jed', 'yulo', 'seller@gmail.com', NULL, '$2y$10$LLu6WieWQ5TpswuXBvTOzO0z1KTdrbZEedtgmW6k176BsaIieBRRG', 'seller'),
(70, 'status', 'Yulo', 'buyer@gmail.com', NULL, '$2y$10$SeU/G07lw.bSw9BDcab5f.8BQI2693U7mzXGOZ118aRhqKbnwAOk6', 'buyer'),
(71, 'johann', 'ba kamo?', 'ken@gmail.com', NULL, '$2y$10$EGWjlEjc/hDYEAuW5dzuyOhJCsTJr7LbdDERSnfsVuUSrA3OwRUW2', 'seller');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_orders_buyer` (`buyer_id`),
  ADD KEY `idx_orders_supplier` (`supplier_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_order_items_user_id` (`user_id`);

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
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_order_items_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
