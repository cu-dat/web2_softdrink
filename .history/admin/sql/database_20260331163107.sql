-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th3 28, 2026 lúc 09:31 AM
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
(1, 'Carbonated Drinks', 'Fizzy soft drinks with carbonation', 1, '2026-03-11 12:54:56'),
(2, 'Juice', 'Natural and artificial fruit juices', 1, '2026-03-11 12:54:56'),
(3, 'Energy Drinks', 'High caffeine energy beverages', 1, '2026-03-11 12:54:56'),
(4, 'Water', 'Bottled water and flavored water', 1, '2026-03-11 12:54:56'),
(5, 'Tea & Coffee', 'Ready to drink tea and coffee', 1, '2026-03-11 12:54:56');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` enum('admin','staff','customer') DEFAULT 'customer',
  `status` tinyint(4) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `customers`
--

INSERT INTO `customers` (`id`, `full_name`, `password`, `email`, `phone`, `address`, `created_at`, `role`, `status`) VALUES
(1, 'Cư Đạt', '$2y$10$nKLtWgRSC6MCP.30YlXzfOcn1Ukk1B0bXs5yD7uVes.5grHUsx7C2', 'cudat@example.com', '0123456789', '123 Lien Khu 16-18, HCM', '2026-03-18 11:59:04', 'admin', 1),
(2, 'Gia Hưng', '$2y$10$LOO7ukln7zK.jzsNv.AWr.H.lAHPo8km4LYjUxpY/3j.hBIEJGiUG', 'giahung@example.com', '0987654321', '456 Hau Giang, HCM', '2026-03-18 11:59:04', 'customer', 1),
(3, 'Ngọc Sơn', '$2y$10$n5mlPF/iBgOKkpzOM5Y.NOKKUsf50Cy0NziU0xL3CtMnDeb4bmqrS', 'ngocson@example.com', '0112233445', '789 Hong Bang, HCM', '2026-03-18 11:59:04', 'customer', 0),
(5, 'Ái Linh', '$2y$10$R2Qbq3H0czm1Bu7yaKb0p.5MCjtyYS09H09Jk..2KdPZlJPR.iSRu', 'ailinhxinhdep@gmail.com', '0339182273', '275 Quang Trung, HCM', '2026-03-18 12:06:42', 'admin', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT 0.00,
  `status` enum('pending','processing','completed','cancelled') DEFAULT 'pending',
  `note` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`id`, `customer_id`, `total_amount`, `status`, `note`, `created_at`) VALUES
(1, NULL, 12.50, 'completed', NULL, '2026-03-11 12:54:56'),
(2, NULL, 8.99, 'processing', NULL, '2026-03-11 12:54:56'),
(3, NULL, 25.00, 'pending', NULL, '2026-03-11 12:54:56');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_details`
--

CREATE TABLE `order_details` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `order_details`
--

INSERT INTO `order_details` (`id`, `order_id`, `product_id`, `quantity`, `price`, `subtotal`) VALUES
(1, 1, 1, 5, 1.50, 7.50),
(2, 1, 5, 1, 3.99, 3.99),
(3, 1, 9, 1, 1.00, 1.00),
(4, 2, 7, 3, 2.99, 8.97),
(5, 3, 2, 10, 1.50, 15.00),
(6, 3, 10, 5, 2.00, 10.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
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

INSERT INTO `products` (`id`, `category_id`, `name`, `description`, `price`, `stock_quantity`, `image`, `status`, `created_at`, `updated_at`, `sku`, `unit`, `profit_margin`) VALUES
(1, 1, 'Coca-Cola 330ml', 'Classic Coca-Cola can', 1.50, 198, 'product_1774686632.jpg', 1, '2026-03-11 12:54:56', '2026-03-28 08:30:32', 'C001', 'Lon', 15),
(2, 1, 'Pepsi 330ml', 'Pepsi Cola can', 1.50, 180, NULL, 1, '2026-03-11 12:54:56', '2026-03-11 12:54:56', NULL, NULL, 0),
(3, 1, 'Sprite 330ml', 'Lemon-lime flavored drink', 1.50, 150, NULL, 0, '2026-03-11 12:54:56', '2026-03-28 07:29:41', NULL, NULL, 0),
(4, 1, 'Fanta Orange 330ml', 'Orange flavored carbonated drink', 1.50, 160, NULL, 1, '2026-03-11 12:54:56', '2026-03-11 12:54:56', NULL, NULL, 0),
(5, 2, 'Tropicana Orange Juice 1L', 'Pure orange juice', 3.99, 80, NULL, 0, '2026-03-11 12:54:56', '2026-03-28 07:29:42', NULL, NULL, 0),
(6, 2, 'Apple Juice 500ml', 'Natural apple juice', 2.50, 100, NULL, 1, '2026-03-11 12:54:56', '2026-03-11 12:54:56', NULL, NULL, 0),
(7, 3, 'Red Bull 250ml', 'Energy drink', 2.99, 120, NULL, 1, '2026-03-11 12:54:56', '2026-03-11 12:54:56', NULL, NULL, 0),
(8, 3, 'Monster Energy 500ml', 'Energy drink', 3.50, 90, NULL, 1, '2026-03-11 12:54:56', '2026-03-11 12:54:56', NULL, NULL, 0),
(9, 4, 'Evian Water 500ml', 'Natural mineral water', 1.00, 300, NULL, 1, '2026-03-11 12:54:56', '2026-03-11 12:54:56', NULL, NULL, 0),
(10, 5, 'Lipton Ice Tea 500ml', 'Lemon iced tea', 2.00, 140, NULL, 0, '2026-03-11 12:54:56', '2026-03-28 08:09:38', NULL, NULL, 0),
(11, 5, 'Black Coffee 330ml', NULL, 1.20, 31, '', 1, '2026-03-28 08:12:00', '2026-03-28 08:16:16', 'CF012', 'Lon', 0);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Chỉ mục cho bảng `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

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
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD KEY `category_id` (`category_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `order_details`
--
ALTER TABLE `order_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_details_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
