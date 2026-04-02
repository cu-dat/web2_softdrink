-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th4 02, 2026 lúc 03:13 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `soft_drink_store`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `status`, `created_at`) VALUES
(1, 'Carbonated Drinks', NULL, 1, '2026-03-31 10:03:59'),
(2, 'Juice', NULL, 1, '2026-03-31 10:03:59'),
(3, 'Energy Drinks', NULL, 1, '2026-03-31 10:03:59'),
(4, 'Water', NULL, 1, '2026-03-31 10:03:59'),
(5, 'Tea & Coffee', NULL, 1, '2026-03-31 10:03:59');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `imports`
--

CREATE TABLE `imports` (
  `id` int(11) NOT NULL,
  `import_code` varchar(50) DEFAULT NULL,
  `import_date` datetime DEFAULT current_timestamp(),
  `supplier_name` varchar(150) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `status` enum('draft','completed') DEFAULT 'draft',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `imports`
--

INSERT INTO `imports` (`id`, `import_code`, `import_date`, `supplier_name`, `note`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(1, NULL, '2026-03-31 18:24:01', NULL, NULL, 'completed', NULL, '2026-03-31 11:24:01', '2026-03-31 11:26:34'),
(2, NULL, '2026-03-31 18:49:17', NULL, NULL, 'completed', NULL, '2026-03-05 02:00:00', '2026-03-31 11:49:17'),
(3, NULL, '2026-03-31 18:49:17', NULL, NULL, 'completed', NULL, '2026-03-10 03:00:00', '2026-03-31 11:49:17'),
(4, NULL, '2026-03-31 18:49:53', NULL, NULL, 'completed', NULL, '2026-03-31 11:49:53', '2026-04-02 10:51:10'),
(5, NULL, '2026-03-31 18:58:31', NULL, NULL, 'completed', NULL, '2026-03-31 11:58:31', '2026-04-02 10:51:11'),
(6, NULL, '2026-04-02 17:50:06', NULL, NULL, 'completed', NULL, '2026-04-02 10:50:06', '2026-04-02 10:51:13'),
(7, NULL, '2026-04-02 17:51:28', NULL, NULL, 'completed', NULL, '2026-04-02 10:51:28', '2026-04-02 10:51:59'),
(8, NULL, '2026-04-02 17:56:16', NULL, NULL, 'completed', NULL, '2026-04-02 10:56:16', '2026-04-02 10:57:01'),
(9, NULL, '2026-04-02 17:57:04', NULL, NULL, 'draft', NULL, '2026-04-02 10:57:04', '2026-04-02 10:57:04'),
(10, NULL, '2026-04-02 17:58:07', NULL, NULL, 'completed', NULL, '2026-04-02 10:58:07', '2026-04-02 10:58:15'),
(11, NULL, '2026-04-02 17:59:32', NULL, NULL, 'draft', NULL, '2026-04-02 10:59:32', '2026-04-02 10:59:32'),
(12, NULL, '2026-04-02 18:07:59', NULL, NULL, 'draft', NULL, '2026-04-02 11:07:59', '2026-04-02 11:07:59'),
(13, NULL, '2026-04-02 18:09:14', NULL, NULL, 'completed', NULL, '2026-04-02 11:09:14', '2026-04-02 11:41:49'),
(14, NULL, '2026-04-02 18:41:24', NULL, NULL, 'draft', NULL, '2026-04-02 11:41:24', '2026-04-02 11:41:24'),
(15, NULL, '2026-04-02 18:42:08', NULL, NULL, 'completed', NULL, '2026-04-02 11:42:08', '2026-04-02 11:42:21'),
(16, NULL, '2026-04-02 18:42:44', NULL, NULL, 'completed', NULL, '2026-04-02 11:42:44', '2026-04-02 11:42:54'),
(17, NULL, '2026-04-02 19:07:30', NULL, NULL, 'completed', NULL, '2026-04-02 12:07:30', '2026-04-02 12:09:03'),
(18, NULL, '2026-04-02 19:07:48', NULL, NULL, 'completed', NULL, '2026-04-02 12:07:48', '2026-04-02 12:08:01'),
(19, NULL, '2026-04-02 19:09:18', NULL, NULL, 'completed', NULL, '2026-04-02 12:09:18', '2026-04-02 12:09:58'),
(20, NULL, '2026-04-02 19:49:50', NULL, NULL, 'draft', NULL, '2026-04-02 12:49:50', '2026-04-02 12:49:50');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `import_details`
--

CREATE TABLE `import_details` (
  `id` int(11) NOT NULL,
  `import_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `import_price` decimal(10,2) NOT NULL,
  `profit_margin` decimal(5,2) DEFAULT 0.00,
  `selling_price` decimal(10,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `import_details`
--

INSERT INTO `import_details` (`id`, `import_id`, `product_id`, `quantity`, `import_price`, `profit_margin`, `selling_price`, `created_at`) VALUES
(1, 1, 1, 10, 10000.00, 0.00, 0.00, '2026-03-31 11:26:27'),
(2, 1, 2, 20, 10000.00, 0.00, 0.00, '2026-03-31 11:53:20'),
(3, 1, 2, 20, 10000.00, 0.00, 0.00, '2026-03-31 11:58:29'),
(4, 5, 9, 30, 7000.00, 0.00, 0.00, '2026-03-31 11:58:42'),
(5, 7, 4, 15, 10000.00, 0.00, 0.00, '2026-04-02 10:51:34'),
(6, 8, 7, 40, 12000.00, 0.00, 0.00, '2026-04-02 10:56:59'),
(7, 10, 8, 30, 9000.00, 0.00, 0.00, '2026-04-02 10:58:14'),
(8, 11, 10, 15, 8000.00, 0.00, 0.00, '2026-04-02 11:40:00'),
(9, 15, 3, 10, 8000.00, 0.00, 0.00, '2026-04-02 11:42:17'),
(10, 16, 10, 10, 8000.00, 0.00, 0.00, '2026-04-02 11:42:51'),
(11, 9, 8, 5, 10000.00, 0.00, 0.00, '2026-04-02 11:57:55'),
(12, 12, 5, 12, 12000.00, 0.00, 0.00, '2026-04-02 11:58:21'),
(13, 14, 11, 50, 11500.00, 0.00, 0.00, '2026-04-02 11:58:37'),
(14, 9, 3, 13, 8000.00, 0.00, 0.00, '2026-04-02 12:07:11'),
(15, 17, 5, 10, 8000.00, 0.00, 0.00, '2026-04-02 12:07:36'),
(16, 18, 6, 15, 9500.00, 0.00, 0.00, '2026-04-02 12:07:53'),
(17, 19, 1, 15, 19500.00, 0.00, 0.00, '2026-04-02 12:09:25'),
(18, 19, 11, 15, 11000.00, 0.00, 0.00, '2026-04-02 12:09:47'),
(19, 19, 2, 10, 9500.00, 0.00, 0.00, '2026-04-02 12:09:55');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `inventory`
--

CREATE TABLE `inventory` (
  `product_id` int(11) NOT NULL,
  `stock` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `inventory`
--

INSERT INTO `inventory` (`product_id`, `stock`) VALUES
(1, 15),
(2, 10),
(3, 10),
(4, 15),
(5, 10),
(6, 15),
(7, 40),
(8, 30),
(9, 30),
(10, 10),
(11, 15);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT 0.00,
  `status` enum('pending','confirmed','completed','cancelled') DEFAULT 'pending',
  `note` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`id`, `customer_id`, `total_amount`, `status`, `note`, `created_at`) VALUES
(1, 1, 150000.00, 'pending', NULL, '2026-03-25 03:15:00'),
(2, 2, 220000.00, 'confirmed', NULL, '2026-03-26 07:30:00'),
(3, 3, 180000.00, 'completed', NULL, '2026-03-27 02:45:00'),
(4, 1, 95000.00, 'cancelled', NULL, '2026-03-28 09:20:00'),
(5, 2, 300000.00, 'pending', NULL, '2026-03-29 04:10:00'),
(6, 3, 125000.00, 'confirmed', NULL, '2026-03-30 01:00:00');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_details`
--

CREATE TABLE `order_details` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `price` decimal(10,2) DEFAULT NULL,
  `subtotal` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `order_details`
--

INSERT INTO `order_details` (`id`, `order_id`, `product_id`, `quantity`, `price`, `subtotal`) VALUES
(1, 1, 1, 2, 15000.00, 30000.00),
(2, 1, 2, 3, 20000.00, 60000.00),
(3, 1, 3, 2, 30000.00, 60000.00),
(4, 2, 2, 5, 20000.00, 100000.00),
(5, 2, 4, 4, 30000.00, 120000.00),
(6, 3, 1, 3, 15000.00, 45000.00),
(7, 3, 5, 3, 45000.00, 135000.00),
(8, 4, 3, 1, 30000.00, 30000.00),
(9, 4, 2, 2, 20000.00, 40000.00),
(10, 5, 4, 5, 30000.00, 150000.00),
(11, 5, 5, 3, 50000.00, 150000.00),
(12, 6, 1, 5, 15000.00, 75000.00),
(13, 6, 2, 2, 25000.00, 50000.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `price` int(11) NOT NULL DEFAULT 0,
  `cost_price` int(11) DEFAULT 0,
  `stock_quantity` int(11) DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `sku` varchar(50) DEFAULT NULL,
  `unit` varchar(50) DEFAULT NULL,
  `profit_margin` float DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `description`, `price`, `cost_price`, `stock_quantity`, `image`, `status`, `created_at`, `updated_at`, `sku`, `unit`, `profit_margin`) VALUES
(1, 1, 'Coca-Cola 330ml', 'Classic Coca-Cola can', 11500, 10000, 25, 'product_1774955750.jpg', 1, '2026-03-11 05:54:56', '2026-04-02 10:55:13', 'C001', 'Lon', 10),
(2, 1, 'Pepsi 330ml', 'Pepsi Cola can', 11000, 10000, 28, 'product_1774955765.jpg', 1, '2026-03-11 05:54:56', '2026-03-31 11:43:17', 'PS001', 'Chai', 10),
(3, 1, 'Sprite 330ml', 'Lemon-lime flavored drink', 9900, 9000, 58, 'product_1774955776.jpg', 0, '2026-03-11 05:54:56', '2026-03-31 11:43:17', 'S001', 'Chai', 10),
(4, 1, 'Fanta Orange 330ml', 'Orange flavored carbonated drink', 9900, 9000, 50, 'product_1774955802.jpg', 1, '2026-03-11 05:54:56', '2026-03-31 11:43:17', 'FO330', 'Chai', 10),
(5, 2, 'Tropicana Orange Juice 1L', 'Pure orange juice', 16500, 15000, 72, 'product_1774955819.jpg', 0, '2026-03-11 05:54:56', '2026-03-31 11:43:17', 'TOJ1L', 'Chai', 10),
(6, 2, 'Apple Juice 500ml', 'Natural apple juice', 13200, 12000, 52, 'product_1774955837.jpg', 1, '2026-03-11 05:54:56', '2026-03-31 11:43:17', 'AJ500', 'Chai', 10),
(7, 3, 'Red Bull 250ml', 'Energy drink', 15400, 14000, 41, 'product_1774955856.jpg', 1, '2026-03-11 05:54:56', '2026-03-31 11:43:17', 'RED250', 'Chai', 10),
(8, 3, 'Monster Energy 500ml', 'Energy drink', 16500, 15000, 43, 'product_1774955865.jpg', 1, '2026-03-11 05:54:56', '2026-03-31 11:43:17', 'ME500', 'Chai', 10),
(9, 4, 'Evian Water 500ml', 'Natural mineral water', 8800, 8000, 14, 'product_1774955877.jpg', 1, '2026-03-11 05:54:56', '2026-03-31 11:43:17', 'EVW500', 'Chai', 10),
(10, 5, 'Lipton Ice Tea 500ml', 'Lemon iced tea', 11000, 10000, 10, 'product_1774955888.jpg', 0, '2026-03-11 05:54:56', '2026-03-31 11:43:17', 'LIP500', 'Chai', 10),
<<<<<<< HEAD
(11, 5, 'Black Coffee 330ml', '', 9900, 9000, 6, 'product_1775135576.jpg', 1, '2026-03-28 01:12:00', '2026-04-02 13:12:56', 'CF012', 'Lon', 10);
=======
(11, 5, 'Black Coffee 330ml', '', 9900, 9000, 6, '', 1, '2026-03-28 01:12:00', '2026-03-31 11:43:17', 'CF012', 'Lon', 10);
>>>>>>> origin/frontend

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `role` enum('super_admin','admin','staff','customer') DEFAULT 'customer',
  `status` tinyint(1) DEFAULT 1,
  `provider` varchar(20) DEFAULT 'local',
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `email`, `phone`, `address`, `role`, `status`, `provider`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$0VrOwUdXDZkrkZ7U7F6LF.vYaxPMU7X/dCvgyWd31I9UXqnV66YMi', 'Administrator', 'admin@softdrink.com', NULL, NULL, 'admin', 1, 'local', NULL, '2026-03-31 10:03:59', '2026-03-31 10:12:08'),
(2, NULL, NULL, 'Gia Hunwg', 'ngogiahung.hcm@gmail.com', '0786566862', '333', NULL, 1, 'google', NULL, '2026-03-31 10:03:59', '2026-03-31 10:17:28'),
(3, NULL, '$2y$10$x0G8HzZUwV8DkKwcy0NL2un9HZgISCuUrRnY9Je27apWVDYN5.jKO', 'Hin', 'giahung10092004@gmail.com', '0786566862', '4444', 'customer', 1, 'local', NULL, '2026-03-31 10:03:59', '2026-03-31 10:03:59');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `imports`
--
ALTER TABLE `imports`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `import_code` (`import_code`);

--
-- Chỉ mục cho bảng `import_details`
--
ALTER TABLE `import_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_import` (`import_id`),
  ADD KEY `idx_product` (`product_id`);
<<<<<<< HEAD

--
-- Chỉ mục cho bảng `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`product_id`);
=======
>>>>>>> origin/frontend

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Chỉ mục cho bảng `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `imports`
--
ALTER TABLE `imports`
<<<<<<< HEAD
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT cho bảng `import_details`
--
ALTER TABLE `import_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
=======
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
>>>>>>> origin/frontend

--
-- AUTO_INCREMENT cho bảng `import_details`
--
ALTER TABLE `import_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `order_details`
--
ALTER TABLE `order_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `import_details`
--
ALTER TABLE `import_details`
  ADD CONSTRAINT `fk_import` FOREIGN KEY (`import_id`) REFERENCES `imports` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL;

--
<<<<<<< HEAD
-- Các ràng buộc cho bảng `inventory`
--
ALTER TABLE `inventory`
  ADD CONSTRAINT `inventory_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
=======
>>>>>>> origin/frontend
-- Các ràng buộc cho bảng `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
