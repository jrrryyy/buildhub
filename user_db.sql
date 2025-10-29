-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 29, 2025 at 10:14 AM
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
  `status` enum('pending','accepted','completed','cancelled') NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `buyer_id`, `supplier_id`, `recipient_name`, `address_line`, `province`, `phone`, `schedule_date`, `product_name`, `total_amount`, `ordered_at`, `status`, `updated_at`) VALUES
(35, 73, 74, 'johann', '#221 Boulivard st.', 'pangasinan', '09484694167', '2025-10-29', 'cement', 300, '2025-10-23 19:58:00', 'cancelled', '2025-10-27 12:53:09'),
(36, 73, 74, 'Red', '123 Street, Mangaldan', 'Pangasinan', '09999123456', '2025-10-30', 'Mahogany Wood', 830, '2025-10-27 14:08:22', 'cancelled', '2025-10-27 14:09:14'),
(37, 73, 74, 'Red', '123 Street, Mangaldan', 'Pangasinan', '09999123456', '2025-11-13', 'Gravel Bato', 450, '2025-10-27 14:08:59', 'accepted', '2025-10-27 14:09:19');

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
(51, 74, 0, 0, 'cement', 'good quality', 250, 100, '20251023_120011_ef2de7f8.jpg', 25000, '2025-10-23 18:00:11', '2025-10-23 18:00:11'),
(52, 74, 0, 0, 'Hollow Blocks', 'hollow blocks per piece', 4, 999, '20251027_064738_f6066a01.webp', 3996, '2025-10-27 13:47:38', '2025-10-27 13:47:38'),
(53, 74, 0, 0, 'Galvanized Square Steel', '1.5 Meter length, thin tube', 100, 666, '20251027_065141_903a355d.jpg', 66600, '2025-10-27 13:51:41', '2025-10-27 13:51:41'),
(54, 74, 0, 0, 'Concrete Nails', '1 Kilo per order', 105, 999, '20251027_065608_3f5bf2c7.jpg', 104895, '2025-10-27 13:56:09', '2025-10-27 13:56:09'),
(55, 74, 0, 0, 'Red Bricks', 'sold per brick', 25, 99999, '20251027_065800_11200184.jpg', 2499975, '2025-10-27 13:58:00', '2025-10-27 13:58:00'),
(56, 74, 0, 0, 'Gravel Bato', 'sold per kilo', 20, 99999, '20251027_070010_2b96a4de.webp', 1999980, '2025-10-27 14:00:10', '2025-10-27 14:00:10'),
(57, 74, 0, 0, 'Mahogany Wood', '1-500bd.ft.', 195, 999999, '20251027_070309_547b57ad.jpg', 194999805, '2025-10-27 14:03:09', '2025-10-27 14:03:09');

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
  `role` enum('buyer','seller','admin') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fname`, `lname`, `email`, `profile_picture`, `password`, `role`) VALUES
(73, 'bea', 'yulo', 'buyer@gmail.com', 'profile_73_1761213585.jpg', '$2y$10$pzFcyYTqrImY2rtpqCOGWul4MtHVBD3hJm5ydKdzRAUbFtAWkQAPe', 'buyer'),
(74, 'jed', 'yulo', 'seller@gmail.com', 'profile_74_1761213558.jpg', '$2y$10$AEt6q3mv/dbSHHT1CGvF4.9CQ11DNpeKK1aHPIwcr9ovp1beY7nei', 'seller'),
(77, 'Admin', 'admin', 'admin@gmail.com', 'profile_77_1761218048.jpg', '$2y$10$/4KpltRyZLJ5a8jmX0N5DOU6bXt71omR2rsdD6o0QEnuLVpyamisy', 'admin');

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
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

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
