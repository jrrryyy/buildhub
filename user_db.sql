-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 15, 2025 at 05:06 PM
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
  `id` bigint(20) UNSIGNED NOT NULL,
  `buyer_id` int(10) UNSIGNED NOT NULL,
  `supplier_id` int(10) UNSIGNED NOT NULL,
  `recipient_name` varchar(100) NOT NULL,
  `address_line` varchar(255) NOT NULL,
  `province` varchar(50) DEFAULT NULL,
  `phone` varchar(20) NOT NULL,
  `status` enum('pending','accepted','delivered','cancelled') NOT NULL DEFAULT 'pending',
  `scheduled_at` date NOT NULL,
  `total_amount` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
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
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `user_id`, `order_id`, `product_id`, `product_name`, `description`, `unit_price`, `quantity`, `file`, `line_total`, `created_at`, `updated_at`) VALUES
(13, 57, 0, 0, 'chocolate', 'asdasd', 100, 100, 'umayno.jpg', 10000, '2025-10-15 20:30:42', '2025-10-15 20:30:42'),
(14, 57, 0, 0, 'chocolate', 'dad', 100, 100, 'yeseo.jpg', 10000, '2025-10-15 20:30:56', '2025-10-15 20:30:56');

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
  `id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `weight` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `delivery` int(11) NOT NULL,
  `file` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `product_name`, `weight`, `price`, `quantity`, `delivery`, `file`) VALUES
(39, 'pogi', 69, 69, 69, 0, '3 layer switch.png'),
(42, 'asd', 234, 324, 234, 0, 'day6.mp4'),
(43, 'asd', 234, 324, 234, 0, 'umayno.jpg'),
(44, 'Asd', 45, 53, 554, 0, 'wp.jpg'),
(45, '', 0, 0, 0, 0, '136134480_104490174948609_970177375971885239_n.jpg'),
(46, 'pogi', 69, 69, 69, 22, '119037952_756511421589148_2267977214469719398_n.jp'),
(47, '', 0, 0, 0, 0, 'wp.jpg'),
(48, 'name', 2, 100, 2, 2, '168428271_279456313625537_7799711334461686546_n.jp'),
(49, '', 0, 23, 0, 23, '136134480_104490174948609_970177375971885239_n.jpg');

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
(55, 'buyer', 'Yulo', 'seller@gmail.com', '$2y$10$dWmnuHRwGoLl/zplPOpBle9DizlCGaLMtwe5kzAMwLZgIuy2segPe', 'seller'),
(56, 'buyer', '', 'haha@haha', '$2y$10$f7WqZLyyODKCymKl7tS..e3NenNVc/RWN2LkbROM26hrSGecdvovO', 'buyer'),
(57, '', '', 'mama@mama', '$2y$10$0yF3VLvlsWHO/jtOGxbVqejh1IVS54GK.jKl2iM9sygRG/Nx0vJvi', 'seller'),
(58, 'seller', '', 'jaja@jaja', '$2y$10$H0y01HNNInfnlDh5cDWX7.LZbWOMw.YK3vVjR52QBIHU8RpHd13gy', 'seller');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_order_items_user_id` (`user_id`);

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
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_order_items_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
