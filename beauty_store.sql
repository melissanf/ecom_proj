-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 16, 2026 at 03:18 AM
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
-- Database: `beauty_store`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(80) NOT NULL,
  `slug` varchar(80) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`) VALUES
(1, 'Serums', 'serums'),
(2, 'Moisturizers', 'moisturizers'),
(3, 'Cleansers', 'cleansers'),
(4, 'Sun Care', 'sun-care');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_email` varchar(150) NOT NULL,
  `shipping_address` text NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `customer_name`, `customer_email`, `shipping_address`, `total`, `status`, `created_at`) VALUES
(1, NULL, 'Fadi Ayad', 'ayadfadi10@gmail.com', '04 rue announa', 9.90, 'pending', '2026-05-15 17:17:37'),
(2, NULL, 'Fadi Ayad', 'ayadfadi10@gmail.com', '04 rue announa', 10.80, 'processing', '2026-05-15 17:31:55'),
(3, NULL, 'Fadi Ayad', 'ayadfadi10@gmail.com', '04 rue announa', 27.90, 'pending', '2026-05-15 23:26:22'),
(4, NULL, 'Fadi Ayad', 'ayadfadi10@gmail.com', '04 rue announa', 7.80, 'delivered', '2026-05-15 23:28:55'),
(5, 2, 'TESTING', 'test@beauty.com', 'test', 11.90, 'cancelled', '2026-05-15 23:32:04'),
(6, NULL, 'Fadi Ayad', 'ayadfadi10@gmail.com', '04 rue announa', 27.80, 'shipped', '2026-05-15 23:44:02'),
(7, NULL, 'Fadi Ayad', 'ayadfadi10@gmail.com', '04 rue announa', 3600.00, 'pending', '2026-05-16 00:13:13'),
(8, 1, 'Admin', 'admin@beauty.com', '04 rue announa', 3000.00, 'pending', '2026-05-16 00:17:19');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(200) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `quantity`, `unit_price`) VALUES
(1, 1, 1, 'Niacinamide 10 + Zinc 1', 1, 9.90),
(2, 2, 6, 'Glycolic Acid 7 Toning Solution', 1, 10.80),
(3, 3, 1, 'Niacinamide 10 + Zinc 1', 1, 9.90),
(5, 4, 3, 'Caffeine Solution 5 + EGCG', 1, 7.80),
(6, 5, 2, 'Hyaluronic Acid 2 + B5', 1, 11.90),
(7, 6, 2, 'Hyaluronic Acid 2 + B5', 1, 11.90),
(8, 6, 7, 'Mineral UV Filters SPF 30', 1, 15.90),
(9, 7, 13, 'Natural Moisturizing Factors + HA', 3, 1200.00),
(10, 8, 17, 'test24', 2, 1500.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(200) NOT NULL,
  `slug` varchar(220) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `sale_price` decimal(10,2) DEFAULT NULL,
  `image` varchar(255) DEFAULT 'placeholder.svg',
  `image2` varchar(255) DEFAULT NULL,
  `image3` varchar(255) DEFAULT NULL,
  `stock` int(11) NOT NULL DEFAULT 100,
  `on_sale` tinyint(1) NOT NULL DEFAULT 0,
  `featured` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `description`, `price`, `sale_price`, `image`, `image2`, `image3`, `stock`, `on_sale`, `featured`, `created_at`) VALUES
(1, 1, 'Niacinamide 10 + Zinc 1', 'niacinamide-10-zinc-1', 'High-strength vitamin and mineral blemish formula.', 12.90, 9.90, 'placeholder.svg', NULL, NULL, 98, 1, 1, '2026-05-15 16:35:46'),
(2, 1, 'Hyaluronic Acid 2 + B5', 'hyaluronic-acid-2-b5', 'Hydrating formula with hyaluronic acid for plumped skin.', 14.90, 11.90, 'products/prod_6a07a1e868d3e9.66345926.png', NULL, NULL, 98, 1, 1, '2026-05-15 16:35:46'),
(3, 1, 'Caffeine Solution 5 + EGCG', 'caffeine-solution-5-egcg', 'Reduces eye contour pigmentation and puffiness.', 9.80, 7.80, 'placeholder.svg', NULL, NULL, 99, 1, 1, '2026-05-15 16:35:46'),
(6, 3, 'Glycolic Acid 7 Toning Solution', 'glycolic-acid-7-toning', 'Exfoliating toner for improved radiance.', 10.80, NULL, 'placeholder.svg', '', '', 99, 1, 1, '2026-05-15 16:35:46'),
(7, 4, 'Mineral UV Filters SPF 30', 'mineral-uv-spf30', 'Broad-spectrum mineral sunscreen for daily protection.', 18.90, 15.90, 'placeholder.svg', NULL, NULL, 99, 1, 0, '2026-05-15 16:35:46'),
(12, 2, 'Moisturizer', 'moisturizer', 'Moisturizer', 2500.00, 1900.00, 'products/prod_6a07b2cb030521.49558743.png', 'products/prod_6a07b2cb032722.38242227.png', '', 100, 1, 1, '2026-05-15 23:56:59'),
(13, 2, 'Natural Moisturizing Factors + HA', 'natural-moisturizing-factors-ha', 'Natural Moisturizing Factors + HA', 1600.00, 1200.00, 'products/prod_6a07b335391db7.14395801.png', 'products/prod_6a07b335393640.05454607.png', '', 97, 1, 1, '2026-05-15 23:58:45'),
(17, 4, 'test24', 'test24', 'test', 1900.00, 1500.00, 'products/prod_6a07b7662907c1.76440460.png', 'products/prod_6a07b766292aa0.29627144.png', 'products/prod_6a07b766294415.12862150.png', 98, 1, 1, '2026-05-16 00:16:38');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('customer','admin') NOT NULL DEFAULT 'customer',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Admin', 'admin@beauty.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '2026-05-15 16:35:46'),
(2, 'TESTING', 'test@beauty.com', '$2y$10$ibvSD/I6Lu1V9wQNosLeX.PEGNX0KNZYG.mSoDByd8Te956DJHVK.', 'customer', '2026-05-15 23:30:51'),
(3, 'Fadi Ayad', 'ayadfadi10@gmail.com', '$2y$10$MTuPrV1D88dk.BK1iw33n./4cPBXyk.b7m0wDbwX27R3Vhi8nJOTe', 'customer', '2026-05-15 23:45:41');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_order` (`order_id`),
  ADD KEY `idx_product` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_category` (`category_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
