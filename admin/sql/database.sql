-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th4 08, 2026 lúc 05:06 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.0.30

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
(1, 1, 'Coca-Cola 330ml', 'Nước ngọt có gas hương cola đặc trưng của thương hiệu Coca-Cola, đóng lon 330ml, phù hợp uống lạnh kèm bữa ăn hoặc giải khát hằng ngày.', 11000, 10000, 'product_1774955750.jpg', 1, '2026-03-11 05:54:56', '2026-04-03 05:33:26', 'C001', 'Lon', 10),
(2, 1, 'Pepsi 330ml', 'Nước ngọt có gas vị cola nhẹ nhàng của thương hiệu Pepsi, đóng lon 330ml, mang lại cảm giác sảng khoái và tươi mát.', 11000, 10000, 'product_1774955765.jpg', 1, '2026-03-11 05:54:56', '2026-04-03 05:33:33', 'PS001', 'Lon', 10),
(3, 1, 'Sprite 330ml', 'Nước ngọt có gas hương chanh tươi mát của thương hiệu Sprite, đóng lon 330ml, thích hợp giải nhiệt trong những ngày nắng nóng.', 10084, 9167, 'product_1774955776.jpg', 0, '2026-03-11 05:54:56', '2026-04-03 05:33:39', 'S001', 'Chai', 10),
(4, 1, 'Fanta Orange 330ml', 'Nước ngọt có gas hương cam tự nhiên của thương hiệu Fanta, đóng lon 330ml, vị ngọt dịu cùng hương thơm trái cây đặc trưng.', 10058, 9144, 'product_1774955802.jpg', 1, '2026-03-11 05:54:56', '2026-04-03 05:33:47', 'FO330', 'Chai', 10),
(5, 2, 'Tropicana Orange Juice 1L', 'Nước ép cam tươi nguyên chất thương hiệu Tropicana, dung tích 1 lít, giàu vitamin C, không chất bảo quản, thích hợp cho cả gia đình.', 15905, 14459, 'product_1774955819.jpg', 0, '2026-03-11 05:54:56', '2026-04-03 05:33:53', 'TOJ1L', 'Chai', 10),
(6, 2, 'Apple Juice 500ml', 'Nước ép táo tươi nguyên chất, dung tích 500ml, vị ngọt thanh tự nhiên, bổ sung vitamin và khoáng chất thiết yếu cho cơ thể.', 12280, 11164, 'product_1774955837.jpg', 1, '2026-03-11 05:54:56', '2026-04-03 05:34:03', 'AJ500', 'Chai', 10),
(7, 3, 'Red Bull 250ml', 'Nước tăng lực Red Bull lon 250ml, chứa caffeine và taurine giúp tăng cường sự tỉnh táo, tập trung và năng lượng cho người dùng.', 15400, 14000, 'product_1775188182.jpg', 1, '2026-03-11 05:54:56', '2026-04-03 05:34:12', 'RED250', 'Chai', 10),
(8, 3, 'Monster Energy 500ml', 'Nước tăng lực Monster Energy lon 500ml, hàm lượng caffeine cao cùng hỗn hợp vitamin B, phù hợp cho người cần duy trì năng lượng trong thời gian dài.', 16500, 15000, 'product_1774955865.jpg', 0, '2026-03-11 05:54:56', '2026-04-03 05:34:22', 'ME500', 'Chai', 10),
(9, 4, 'Evian Water 500ml', 'Nước khoáng thiên nhiên Evian chai 500ml, được lọc qua các tầng đá núi Alps, giàu khoáng chất tự nhiên, tinh khiết và thanh mát.', 8800, 8000, 'product_1774955877.jpg', 1, '2026-03-11 05:54:56', '2026-04-03 05:34:29', 'EVW500', 'Chai', 10),
(10, 5, 'Lipton Ice Tea 500ml', 'Trà Lipton chai 500ml, vị trà đen pha lẫn hương chanh dịu nhẹ, không chứa chất bảo quản, giải khát nhanh chóng và tiện lợi.', 11000, 10000, 'product_1774955888.jpg', 1, '2026-03-11 05:54:56', '2026-04-03 05:35:15', 'LIP500', 'Chai', 10),
(11, 5, 'Black Coffee 330ml', 'Cà phê đen nguyên bản dành cho những ai muốn trải nghiệm cảm giác mà không cần phải ra các quán cà phê.', 9900, 9000, 'product_1775135576.jpg', 1, '2026-03-28 01:12:00', '2026-04-03 05:36:28', 'CF012', 'Lon', 10),
(14, 6, 'Tiger Crystal 330ml', 'Đồ uống có cồn, sự lựa chọn không thể hợp lí hơn dành cho những ai đang muốn giải khuây hay tụ tập bạn bè', 0, 0, 'product_1775188490.jpg', 0, '2026-04-03 03:54:42', '2026-04-03 05:43:04', 'TGC330', 'Lon', 10),
(19, 2, 'Nước Hoa Thanh Quế', '', 0, 0, 'product_1775658809.jpg', 0, '2026-04-08 14:25:33', '2026-04-08 14:33:29', 'Sp5', 'Chai', 20);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
