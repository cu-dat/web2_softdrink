-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th4 09, 2026 lúc 03:25 PM
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
(1, 'Nước ngọt', NULL, 1, '2026-03-31 10:03:59'),
(2, 'Nước trái cây', NULL, 1, '2026-03-31 10:03:59'),
(3, 'Nước tăng lực', NULL, 1, '2026-03-31 10:03:59'),
(4, 'Nước lọc', NULL, 1, '2026-03-31 10:03:59'),
(5, 'Trà & Cà phê', NULL, 1, '2026-03-31 10:03:59'),
(6, 'Bia', 'Đồ uống có cồn dành cho đàn ông!', 1, '2026-04-03 03:51:24'),
(7, 'qq', '', 1, '2026-04-03 07:30:53');

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
(20, NULL, '2026-04-02 19:49:50', NULL, NULL, 'completed', NULL, '2026-04-02 12:49:50', '2026-04-02 13:59:18'),
(21, NULL, '2026-04-02 21:13:58', NULL, NULL, 'draft', NULL, '2026-04-02 14:13:58', '2026-04-02 14:13:58'),
(22, NULL, '2026-04-02 21:14:07', NULL, NULL, 'completed', NULL, '2026-04-02 14:14:07', '2026-04-02 14:14:14'),
(23, NULL, '2026-04-02 21:14:47', NULL, NULL, 'completed', NULL, '2026-04-02 14:14:47', '2026-04-02 14:14:53'),
(24, NULL, '2026-04-02 21:50:03', NULL, NULL, 'completed', NULL, '2026-04-02 14:50:03', '2026-04-02 14:50:10'),
(25, NULL, '2026-04-02 21:55:36', NULL, NULL, 'completed', NULL, '2026-04-02 14:55:36', '2026-04-02 14:55:44'),
(26, NULL, '2026-04-02 22:07:55', NULL, NULL, 'completed', NULL, '2026-04-02 15:07:55', '2026-04-02 15:08:08'),
(27, NULL, '2026-04-03 10:34:53', NULL, NULL, 'completed', NULL, '2026-04-03 03:34:53', '2026-04-03 03:35:25'),
(28, NULL, '2026-04-03 12:25:20', NULL, NULL, 'draft', NULL, '2026-04-03 05:25:20', '2026-04-03 05:25:20'),
(29, NULL, '2026-04-03 12:25:56', NULL, NULL, 'completed', NULL, '2026-04-03 05:25:56', '2026-04-03 05:26:26'),
(30, NULL, '2026-04-03 12:28:54', NULL, NULL, 'completed', NULL, '2026-04-03 05:28:54', '2026-04-03 05:29:12'),
(31, NULL, '2026-04-03 12:30:38', NULL, NULL, 'draft', NULL, '2026-04-03 05:30:38', '2026-04-03 05:30:38'),
(32, NULL, '2026-04-03 12:30:59', NULL, NULL, 'completed', NULL, '2026-04-03 05:30:59', '2026-04-03 05:31:18'),
(33, NULL, '2026-04-03 14:32:55', NULL, NULL, 'completed', NULL, '2026-04-03 07:32:55', '2026-04-03 07:33:51'),
(34, NULL, '2026-06-03 14:36:07', NULL, NULL, 'completed', NULL, '2026-06-03 07:36:07', '2026-06-03 07:36:24'),
(35, NULL, '2026-04-09 17:02:16', NULL, NULL, 'completed', NULL, '2026-04-09 10:02:16', '2026-04-09 10:02:27'),
(36, NULL, '2026-04-10 00:00:00', NULL, NULL, 'completed', NULL, '2026-04-09 10:04:39', '2026-04-09 10:14:17'),
(37, NULL, '2026-04-09 00:00:00', NULL, NULL, 'draft', NULL, '2026-04-09 10:33:51', '2026-04-09 10:33:51'),
(38, NULL, '2026-04-09 00:00:00', NULL, NULL, 'completed', NULL, '2026-04-09 10:37:09', '2026-04-09 10:37:23'),
(39, NULL, '2026-04-09 00:00:00', NULL, NULL, 'completed', NULL, '2026-04-09 10:44:13', '2026-04-09 10:44:23'),
(40, NULL, '2026-04-18 00:00:00', NULL, NULL, 'draft', NULL, '2026-04-18 11:17:39', '2026-04-18 11:17:39'),
(41, NULL, '2026-04-18 00:00:00', NULL, NULL, 'completed', NULL, '2026-04-18 11:18:12', '2026-04-18 11:18:28'),
(42, NULL, '2026-04-09 00:00:00', NULL, NULL, 'draft', NULL, '2026-04-09 13:02:50', '2026-04-09 13:02:50'),
(43, NULL, '2026-04-09 00:00:00', NULL, NULL, 'draft', NULL, '2026-04-09 13:04:43', '2026-04-09 13:04:43'),
(44, NULL, '2026-04-09 00:00:00', NULL, NULL, 'draft', NULL, '2026-04-09 13:20:12', '2026-04-09 13:20:12');

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
(19, 19, 2, 10, 9500.00, 0.00, 0.00, '2026-04-02 12:09:55'),
(20, 20, 1, 10, 11500.00, 0.00, 0.00, '2026-04-02 13:59:10'),
(21, 22, 1, 10, 11500.00, 0.00, 0.00, '2026-04-02 14:14:10'),
(22, 23, 1, 10, 11500.00, 0.00, 0.00, '2026-04-02 14:14:51'),
(23, 24, 1, 10, 10000.00, 0.00, 0.00, '2026-04-02 14:50:07'),
(24, 25, 6, 10, 10000.00, 0.00, 0.00, '2026-04-02 14:55:42'),
(25, 26, 3, 20, 9500.00, 0.00, 0.00, '2026-04-02 15:08:04'),
(26, 27, 6, 5, 11000.00, 0.00, 0.00, '2026-04-03 03:35:00'),
(27, 21, 6, 5, 10500.00, 0.00, 0.00, '2026-04-03 03:35:19'),
(28, 29, 4, 3, 9900.00, 0.00, 0.00, '2026-04-03 05:26:16'),
(29, 30, 4, 1, 9900.00, 0.00, 0.00, '2026-04-03 05:29:00'),
(30, 32, 5, 5, 11000.00, 0.00, 0.00, '2026-04-03 05:31:14'),
(31, 33, 15, 10, 1000.00, 0.00, 0.00, '2026-04-03 07:33:42'),
(32, 34, 15, 10, 200.00, 0.00, 0.00, '2026-06-03 07:36:18'),
(33, 35, NULL, 1, 10000.00, 0.00, 0.00, '2026-04-09 10:02:22'),
(34, 36, 15, 10, 10000.00, 0.00, 0.00, '2026-04-09 10:12:47'),
(35, 28, 1, 10, 10000.00, 0.00, 0.00, '2026-04-09 10:13:38'),
(36, 31, 9, 15, 8500.00, 0.00, 0.00, '2026-04-09 10:13:48'),
(37, 37, 1, 5, 9700.00, 0.00, 0.00, '2026-04-09 10:35:29'),
(38, 38, NULL, 1, 1000.00, 0.00, 0.00, '2026-04-09 10:37:17'),
(39, 39, 15, 3, 9800.00, 0.00, 0.00, '2026-04-09 10:44:20'),
(40, 41, 1, 3, 9800.00, 0.00, 0.00, '2026-04-18 11:18:20');

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
(1, 120),
(2, 166),
(3, 60),
(4, 25),
(5, 37),
(6, 28),
(7, 40),
(8, 35),
(9, 27),
(10, 22),
(11, 62),
(14, 0),
(15, 26);

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
(1, 1, 150000.00, 'cancelled', NULL, '2026-03-25 03:15:00'),
(2, 2, 220000.00, 'completed', NULL, '2026-03-26 07:30:00'),
(3, 3, 180000.00, 'completed', NULL, '2026-03-27 02:45:00'),
(4, 1, 95000.00, 'cancelled', NULL, '2026-03-28 09:20:00'),
(5, 2, 300000.00, 'cancelled', NULL, '2026-03-29 04:10:00'),
(6, 3, 125000.00, 'cancelled', NULL, '2026-03-30 01:00:00'),
(7, 3, 55000.00, 'cancelled', 'cod', '2026-04-02 18:38:38'),
(8, 3, 55000.00, 'completed', 'cod', '2026-04-02 18:42:47'),
(9, 3, 12320.00, 'completed', 'cod', '2026-04-03 03:33:34'),
(10, 3, 207900.00, 'cancelled', 'cod', '2026-04-03 04:24:07'),
(11, 3, 26400.00, 'cancelled', 'cod', '2026-04-03 04:28:56'),
(12, 3, 33000.00, 'cancelled', 'cod', '2026-04-03 05:06:43'),
(13, 3, 44000.00, 'cancelled', 'cod', '2026-04-03 05:13:26'),
(14, 3, 33000.00, 'completed', 'cod', '2026-04-03 05:23:17'),
(15, 3, 30800.00, 'pending', 'cod', '2026-04-03 05:24:56'),
(16, 3, 19800.00, 'completed', 'cod', '2026-04-03 05:38:45'),
(17, 3, 3900.00, 'completed', 'cod', '2026-05-03 07:35:08'),
(18, 4, 17764.00, 'completed', 'cod', '2026-04-09 10:26:00'),
(19, 4, 9900.00, 'completed', 'cod', '2026-04-18 11:19:06');

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
(13, 6, 2, 2, 25000.00, 50000.00),
(14, 7, 8, 2, 16500.00, 33000.00),
(15, 7, 10, 2, 11000.00, 22000.00),
(16, 8, 1, 5, 11000.00, 55000.00),
(17, 9, 6, 1, 12320.00, 12320.00),
(18, 10, 4, 21, 9900.00, 207900.00),
(19, 11, 9, 3, 8800.00, 26400.00),
(20, 12, 1, 3, 11000.00, 33000.00),
(21, 13, 2, 4, 11000.00, 44000.00),
(22, 14, 10, 3, 11000.00, 33000.00),
(23, 15, 7, 2, 15400.00, 30800.00),
(24, 16, 11, 2, 9900.00, 19800.00),
(25, 17, 15, 3, 1300.00, 3900.00),
(26, 18, 15, 4, 4441.00, 17764.00),
(27, 19, 11, 1, 9900.00, 9900.00);

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

INSERT INTO `products` (`id`, `category_id`, `name`, `description`, `price`, `cost_price`, `image`, `status`, `created_at`, `updated_at`, `sku`, `unit`, `profit_margin`) VALUES
(1, 1, 'Coca-Cola 330ml', 'Giới thiệu: Nước giải khát có gas nổi tiếng với hương vị đậm đà, sảng khoái, giúp giải nhiệt nhanh chóng.\r\nXuất xứ: Việt Nam (theo tiêu chuẩn Mỹ)\r\nThương hiệu: Coca-Cola\r\nDung tích: 330ml\r\nThành phần: Nước có gas, đường, màu caramel, caffeine\r\nHướng dẫn sử dụng: Uống trực tiếp, ngon hơn khi lạnh\r\nBảo quản: Nơi khô ráo, tránh ánh nắng\r\nHạn sử dụng: 12 tháng', 10994, 9995, 'product_1774955750.jpg', 1, '2026-03-11 05:54:56', '2026-04-09 13:22:05', 'C330', 'Lon', 10),
(2, 1, 'Pepsi 330ml', 'Giới thiệu: Nước ngọt có gas với vị cola nhẹ, ít ngọt hơn, mang lại cảm giác sảng khoái.\r\nXuất xứ: Việt Nam\r\nThương hiệu: Pepsi\r\nDung tích: 330ml\r\nThành phần: Nước có gas, đường, caffeine\r\nHướng dẫn sử dụng: Uống lạnh để ngon hơn\r\nBảo quản: Nơi thoáng mát\r\nHạn sử dụng: 12 tháng', 11000, 10000, 'product_1774955765.jpg', 1, '2026-03-11 05:54:56', '2026-04-09 13:22:50', 'PS330', 'Lon', 10),
(3, 1, 'Sprite 330ml', 'Giới thiệu: Nước giải khát vị chanh tươi mát, không chứa caffeine.\r\nXuất xứ: Việt Nam\r\nThương hiệu: Sprite\r\nDung tích: 330ml\r\nThành phần: Nước có gas, hương chanh tự nhiên\r\nHướng dẫn sử dụng: Uống trực tiếp, dùng lạnh\r\nBảo quản: Tránh ánh nắng\r\nHạn sử dụng: 12 tháng', 10542, 9167, 'product_1774955776.jpg', 0, '2026-03-11 05:54:56', '2026-04-09 13:23:02', 'S330', 'Chai', 15),
(4, 1, 'Fanta Orange 330ml', 'Giới thiệu: Nước ngọt có gas vị cam ngọt dịu, phù hợp mọi lứa tuổi.\r\nXuất xứ: Việt Nam\r\nThương hiệu: Fanta\r\nDung tích: 330ml\r\nThành phần: Nước, đường, hương cam\r\nHướng dẫn sử dụng: Uống lạnh\r\nBảo quản: Nơi khô ráo\r\nHạn sử dụng: 12 tháng', 10058, 9144, 'product_1774955802.jpg', 1, '2026-03-11 05:54:56', '2026-04-09 13:23:16', 'FO330', 'Chai', 10),
(5, 2, 'Tropicana Orange Juice 1L', 'Giới thiệu: Nước ép cam nguyên chất, giàu vitamin C, tốt cho sức khỏe.\r\nXuất xứ: Mỹ / nhập khẩu\r\nThương hiệu: Tropicana\r\nDung tích: 1L\r\nThành phần: 100% nước cam ép\r\nHướng dẫn sử dụng: Lắc đều trước khi uống\r\nBảo quản: Bảo quản lạnh sau khi mở\r\nHạn sử dụng: 6–9 tháng', 15905, 14459, 'product_1774955819.jpg', 0, '2026-03-11 05:54:56', '2026-04-09 13:23:28', 'TOJ1L', 'Chai', 10),
(6, 2, 'Apple Juice 500ml', 'Giới thiệu: Nước ép táo tự nhiên, vị ngọt nhẹ, bổ sung vitamin.\r\nXuất xứ: Việt Nam / nhập khẩu\r\nDung tích: 500ml\r\nThành phần: Nước ép táo\r\nHướng dẫn sử dụng: Uống trực tiếp\r\nBảo quản: Nơi mát\r\nHạn sử dụng: 6–12 tháng', 12280, 11164, 'product_1774955837.jpg', 1, '2026-03-11 05:54:56', '2026-04-09 13:23:38', 'AJ500', 'Chai', 10),
(7, 3, 'Red Bull 250ml', 'Giới thiệu: Nước tăng lực giúp tỉnh táo, tăng cường năng lượng.\r\nXuất xứ: Thái Lan / Việt Nam\r\nThương hiệu: Red Bull\r\nDung tích: 250ml\r\nThành phần: Taurine, caffeine, vitamin B\r\nHướng dẫn sử dụng: Uống khi cần tỉnh táo\r\nBảo quản: Tránh nhiệt độ cao\r\nHạn sử dụng: 18 tháng', 15400, 14000, 'product_1775188182.jpg', 1, '2026-03-11 05:54:56', '2026-04-09 13:23:49', 'RED250', 'Chai', 10),
(8, 3, 'Monster Energy 500ml', 'Giới thiệu: Nước tăng lực mạnh, phù hợp cho người hoạt động cường độ cao.\r\nXuất xứ: Mỹ\r\nThương hiệu: Monster\r\nDung tích: 500ml\r\nThành phần: Caffeine, taurine, vitamin\r\nHướng dẫn sử dụng: Uống trực tiếp\r\nBảo quản: Nơi thoáng mát\r\nHạn sử dụng: 18 tháng', 16500, 15000, 'product_1774955865.jpg', 0, '2026-03-11 05:54:56', '2026-04-09 13:24:32', 'ME500', 'Chai', 10),
(9, 4, 'Evian Water 500ml', 'Giới thiệu: Nước khoáng thiên nhiên cao cấp, tinh khiết từ dãy Alps.\r\nXuất xứ: Pháp\r\nThương hiệu: Evian\r\nDung tích: 500ml\r\nThành phần: Nước khoáng tự nhiên\r\nHướng dẫn sử dụng: Uống trực tiếp\r\nBảo quản: Nơi sạch, mát\r\nHạn sử dụng: 24 tháng', 8800, 8000, 'product_1774955877.jpg', 1, '2026-03-11 05:54:56', '2026-04-09 13:24:37', 'EVW500', 'Chai', 10),
(10, 5, 'Lipton Ice Tea 500ml', 'Giới thiệu: Trà đóng chai vị chanh, thanh mát, giải nhiệt hiệu quả.\r\nXuất xứ: Việt Nam\r\nThương hiệu: Lipton\r\nDung tích: 500ml\r\nThành phần: Trà, đường, hương chanh\r\nHướng dẫn sử dụng: Uống lạnh\r\nBảo quản: Tránh ánh nắng\r\nHạn sử dụng: 12 tháng', 11000, 10000, 'product_1774955888.jpg', 1, '2026-03-11 05:54:56', '2026-04-09 13:24:45', 'LIP500', 'Chai', 10),
(11, 5, 'Black Coffee 330ml', 'Giới thiệu: Cà phê đen đóng lon tiện lợi, giúp tỉnh táo nhanh chóng.\r\nXuất xứ: Việt Nam\r\nDung tích: 330ml\r\nThành phần: Cà phê, nước\r\nHướng dẫn sử dụng: Uống trực tiếp\r\nBảo quản: Nơi mát\r\nHạn sử dụng: 12 tháng', 9900, 9000, 'product_1775135576.jpg', 1, '2026-03-28 01:12:00', '2026-04-09 13:24:57', 'CF012', 'Lon', 10),
(14, 6, 'Tiger Crystal 330ml', 'Giới thiệu: Bia lager cao cấp, vị nhẹ, dễ uống, phù hợp tiệc tùng.\r\nXuất xứ: Việt Nam / Singapore\r\nThương hiệu: Tiger\r\nDung tích: 330ml\r\nThành phần: Nước, lúa mạch, hoa bia\r\nHướng dẫn sử dụng: Uống lạnh\r\nBảo quản: Tránh ánh nắng\r\nHạn sử dụng: 12 tháng', 0, 0, 'product_1775188490.jpg', 0, '2026-04-03 03:54:42', '2026-04-09 13:25:06', 'TGC330', 'Lon', 10),
(15, 7, 'qq', '', 5407, 4702, 'product_1775201554.png', 1, '2026-04-03 07:31:20', '2026-04-09 11:02:59', 'qq', 'Chai', 15);

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
(1, 'admin', '$2y$10$0VrOwUdXDZkrkZ7U7F6LF.vYaxPMU7X/dCvgyWd31I9UXqnV66YMi', 'Administrator', 'admin@softdrink.com', '0000000000', '275 Quang Trung, HCM', 'admin', 1, 'local', NULL, '2026-03-31 10:03:59', '2026-04-09 12:12:32'),
(2, NULL, NULL, 'Gia Hunwg', 'ngogiahung.hcm@gmail.com', '0786566862', '333', 'customer', 1, 'google', NULL, '2026-03-31 10:03:59', '2026-04-09 12:12:20'),
(3, NULL, '$2y$10$x0G8HzZUwV8DkKwcy0NL2un9HZgISCuUrRnY9Je27apWVDYN5.jKO', 'Hưn', 'giahung10092004@gmail.com', '0786566862', '4123', 'customer', 1, 'local', NULL, '2026-03-31 10:03:59', '2026-04-03 05:53:49'),
(4, NULL, '$2y$10$nuYpsjtNBx3QaDHfhSoSm.xCttwKc8nSCToHZLe8odUIE/XdrvQWm', 'Ngọc Sơn', 'sonvt@gmail.com', '0111144455', '12 Vũng Tàu', 'customer', 0, 'local', NULL, '2026-04-03 04:16:12', '2026-04-09 12:14:30'),
(13, NULL, '$2y$10$OHf8iEX/9yb.S/.kcijGEuXYhaUQ.LADb3yWxfe6JbnoYSaVIYCse', 'cư đạt', 'dat@gmail.com', '', '72-2b liên khu 16-18', 'customer', 1, 'local', NULL, '2026-04-09 12:30:48', '2026-04-09 12:32:25');

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

--
-- Chỉ mục cho bảng `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`product_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT cho bảng `imports`
--
ALTER TABLE `imports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT cho bảng `import_details`
--
ALTER TABLE `import_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT cho bảng `order_details`
--
ALTER TABLE `order_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

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
-- Các ràng buộc cho bảng `inventory`
--
ALTER TABLE `inventory`
  ADD CONSTRAINT `inventory_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
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
