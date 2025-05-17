-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: mysql-server
-- Generation Time: May 17, 2025 at 04:07 PM
-- Server version: 8.4.2
-- PHP Version: 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `noteapp`
--

-- --------------------------------------------------------

--
-- Table structure for table `label`
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
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `note`
--

CREATE TABLE `note` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `tieu_de` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `noi_dung` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `note_image`
--

CREATE TABLE `note_image` (
  `id` int NOT NULL,
  `note_id` int NOT NULL,
  `path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `note_label`
--

CREATE TABLE `note_label` (
  `note_id` int NOT NULL,
  `label_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `note_share`
--

CREATE TABLE `note_share` (
  `id` int NOT NULL,
  `note_id` int NOT NULL,
  `shared_with_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `can_edit` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_activated` tinyint(1) NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `reset_token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `email`, `display_name`, `password`, `is_activated`, `token`, `reset_token`, `reset_expires`) VALUES
(1, 'admin@gmail.com', 'admin', 'admin123', 1, '', '', NULL),
(3, 'nsfjnv@gmail.com', 'minhtam1', 'minhtam', 0, '', '', NULL),
(4, 'sfc@gmail.com', 'minhtam2', 'minhtam2', 0, '', '', NULL),
(5, 'sfccccc@gmail.com', 'minhtam3', 'minhtam', 0, '', '', NULL),
(6, 'fbhljhbf@d.com', 'minhtamvvvv', 'a', 0, '', '', NULL),
(7, 'ad@d.com', 'm', 'm', 0, '', '', NULL),
(8, 'advv@d.com', 'mbhb', 'a', 0, '', '', NULL),
(9, 'advvfcf@d.com', 'huhu', 'h', 0, '', '', NULL),
(10, 'cb@dc.com', 't', '$2y$12$3O5TDYcbV/.ABfyRF/vYc.iN192H5Kp700HB9S61P3hPA5Eg5gY.a', 0, '', '', NULL),
(11, 'fff@g.co', 'afff', '$2y$12$UvdBDFryFlhEd780/axu3.iD9WAdtWDI4mZzkvB.pHcDwTvrJ8c3m', 0, '', '', NULL),
(12, 'aaaaaa@d.com', 'n', '$2y$12$HfdVssUt7rjp2JIJhZDjr.ryn0Hw5MIncZM1VJXihSIIMwXecIZHC', 0, '', '', NULL),
(15, 'vvv@gmail.com', 'bhh', '$2y$12$cUwhx/7bn111Kap53uGw.uv6TrBX.aHqg5EMHqQZGVqrQKCHVAs72', 0, 'pVX2LI', '', NULL),
(16, 'Yennhi081205@gmail.com', 'yennhi', '$2y$12$Uc8JxyjHrAI/7VKpp9JWl.dGGmNSKm/7rDfrSs1uj2imOLUuKH36C', 0, 'EvsiINFIaEakZDVlCZ6LCGYQi7Vogg7S', '', NULL),
(19, '26112005ngominhtam@gmail.com', 'minhtam', '$2y$12$PhCYv9DSpHDYzGi11X9uiuAkso1g9M5IdeB5aZyN4oNFiBSjUSuPm', 1, '', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `label`
--
ALTER TABLE `label`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `note`
--
ALTER TABLE `note`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `note_image`
--
ALTER TABLE `note_image`
  ADD PRIMARY KEY (`id`),
  ADD KEY `note_id` (`note_id`);

--
-- Indexes for table `note_label`
--
ALTER TABLE `note_label`
  ADD PRIMARY KEY (`note_id`,`label_id`),
  ADD KEY `label_id` (`label_id`);

--
-- Indexes for table `note_share`
--
ALTER TABLE `note_share`
  ADD PRIMARY KEY (`id`),
  ADD KEY `note_id` (`note_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `label`
--
ALTER TABLE `label`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `note`
--
ALTER TABLE `note`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `note_image`
--
ALTER TABLE `note_image`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `note_share`
--
ALTER TABLE `note_share`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `label`
--
ALTER TABLE `label`
  ADD CONSTRAINT `label_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `note`
--
ALTER TABLE `note`
  ADD CONSTRAINT `note_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `note_image`
--
ALTER TABLE `note_image`
  ADD CONSTRAINT `note_image_ibfk_1` FOREIGN KEY (`note_id`) REFERENCES `note` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `note_label`
--
ALTER TABLE `note_label`
  ADD CONSTRAINT `note_label_ibfk_1` FOREIGN KEY (`note_id`) REFERENCES `note` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `note_label_ibfk_2` FOREIGN KEY (`label_id`) REFERENCES `label` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `note_share`
--
ALTER TABLE `note_share`
  ADD CONSTRAINT `note_share_ibfk_1` FOREIGN KEY (`note_id`) REFERENCES `note` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
