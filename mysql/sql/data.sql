-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Máy chủ: mysql-server
-- Thời gian đã tạo: Th5 16, 2025 lúc 02:48 PM
-- Phiên bản máy phục vụ: 8.4.2
-- Phiên bản PHP: 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `noteapp`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `label`
--
CREATE DATABASE IF NOT EXISTS noteapp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE noteapp;

DROP TABLE IF EXISTS note_share;
DROP TABLE IF EXISTS note_image;
DROP TABLE IF EXISTS note_label;
DROP TABLE IF EXISTS label;
DROP TABLE IF EXISTS note;
DROP TABLE IF EXISTS user;


CREATE TABLE `label` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `note`
--

CREATE TABLE `note` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `tieu_de` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `noi_dung` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `note_image`
--

CREATE TABLE `note_image` (
  `id` int NOT NULL,
  `note_id` int NOT NULL,
  `path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `note_label`
--

CREATE TABLE `note_label` (
  `note_id` int NOT NULL,
  `label_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `note_share`
--

CREATE TABLE `note_share` (
  `id` int NOT NULL,
  `note_id` int NOT NULL,
  `shared_with_email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `can_edit` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `user`
--

CREATE TABLE `user` (
  `id` int NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_activated` tinyint(1) NOT NULL,
  `token` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `user`
--

INSERT INTO `user` (`id`, `email`, `display_name`, `password`, `is_activated`, `token`) VALUES
(1, 'admin@gmail.com', 'admin', 'admin', 0, ''),
(3, 'nsfjnv@gmail.com', 'minhtam1', 'minhtam', 0, ''),
(4, 'sfc@gmail.com', 'minhtam2', 'minhtam2', 0, ''),
(5, 'sfccccc@gmail.com', 'minhtam3', 'minhtam', 0, ''),
(6, 'fbhljhbf@d.com', 'minhtamvvvv', 'a', 0, ''),
(7, 'ad@d.com', 'm', 'm', 0, ''),
(8, 'advv@d.com', 'mbhb', 'a', 0, ''),
(9, 'advvfcf@d.com', 'huhu', 'h', 0, ''),
(10, 'cb@dc.com', 't', '$2y$12$3O5TDYcbV/.ABfyRF/vYc.iN192H5Kp700HB9S61P3hPA5Eg5gY.a', 0, ''),
(11, 'fff@g.co', 'afff', '$2y$12$UvdBDFryFlhEd780/axu3.iD9WAdtWDI4mZzkvB.pHcDwTvrJ8c3m', 0, ''),
(12, 'aaaaaa@d.com', 'n', '$2y$12$HfdVssUt7rjp2JIJhZDjr.ryn0Hw5MIncZM1VJXihSIIMwXecIZHC', 0, ''),
(14, '26112005ngominhtam@gmail.com', 'ab', '$2y$12$oUpHSH5CJUIQ2zc/TIMmRO8z6D9bbkn70hTsTn1JRa4YFV5BQYGAK', 0, 'x92UJs'),
(15, 'vvv@gmail.com', 'bhh', '$2y$12$cUwhx/7bn111Kap53uGw.uv6TrBX.aHqg5EMHqQZGVqrQKCHVAs72', 0, 'pVX2LI');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `label`
--
ALTER TABLE `label`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `note`
--
ALTER TABLE `note`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `note_image`
--
ALTER TABLE `note_image`
  ADD PRIMARY KEY (`id`),
  ADD KEY `note_id` (`note_id`);

--
-- Chỉ mục cho bảng `note_label`
--
ALTER TABLE `note_label`
  ADD PRIMARY KEY (`note_id`,`label_id`),
  ADD KEY `label_id` (`label_id`);

--
-- Chỉ mục cho bảng `note_share`
--
ALTER TABLE `note_share`
  ADD PRIMARY KEY (`id`),
  ADD KEY `note_id` (`note_id`);

--
-- Chỉ mục cho bảng `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `label`
--
ALTER TABLE `label`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `note`
--
ALTER TABLE `note`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `note_image`
--
ALTER TABLE `note_image`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `note_share`
--
ALTER TABLE `note_share`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `user`
--
ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Ràng buộc đối với các bảng kết xuất
--

--
-- Ràng buộc cho bảng `label`
--
ALTER TABLE `label`
  ADD CONSTRAINT `label_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `note`
--
ALTER TABLE `note`
  ADD CONSTRAINT `note_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `note_image`
--
ALTER TABLE `note_image`
  ADD CONSTRAINT `note_image_ibfk_1` FOREIGN KEY (`note_id`) REFERENCES `note` (`id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `note_label`
--
ALTER TABLE `note_label`
  ADD CONSTRAINT `note_label_ibfk_1` FOREIGN KEY (`note_id`) REFERENCES `note` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `note_label_ibfk_2` FOREIGN KEY (`label_id`) REFERENCES `label` (`id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `note_share`
--
ALTER TABLE `note_share`
  ADD CONSTRAINT `note_share_ibfk_1` FOREIGN KEY (`note_id`) REFERENCES `note` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
