-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Aug 31, 2024 at 11:35 PM
-- Server version: 5.7.39
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `Gemify`
--

-- --------------------------------------------------------

--
-- Table structure for table `black_diamonds`
--

CREATE TABLE `black_diamonds` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `photo_certificate` varchar(255) NOT NULL,
  `photo_diamond` varchar(255) NOT NULL,
  `video_diamond` varchar(255) DEFAULT NULL,
  `shape` varchar(255) NOT NULL,
  `weight` decimal(10,2) NOT NULL,
  `price/ct` double(10,0) NOT NULL,
  `user_id` int(11) NOT NULL,
  `is_approved` enum('Decline','Accept','Pending') NOT NULL DEFAULT 'Pending',
  `boost` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `contact_us`
--

CREATE TABLE `contact_us` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `read` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `contact_us`
--

INSERT INTO `contact_us` (`id`, `name`, `email`, `subject`, `message`, `submitted_at`, `read`) VALUES
(3, 'dqdqwdqdqw', 'dwqdqw@dqdqwd', 'dqwqwd', 'dqwdqw', '2024-07-02 14:23:56', 1),
(4, 'Elio', 'hairzone@gmail.com', 'yes man', 'qwdqdwdwqd', '2024-07-04 13:29:18', 1);

-- --------------------------------------------------------

--
-- Table structure for table `diamond`
--

CREATE TABLE `diamond` (
  `id` int(11) NOT NULL,
  `nature` varchar(255) NOT NULL,
  `photo_certificate` varchar(255) NOT NULL,
  `photo_diamond` varchar(255) NOT NULL,
  `video_diamond` varchar(255) DEFAULT NULL,
  `shape` varchar(255) NOT NULL,
  `certificate` varchar(255) NOT NULL,
  `weight` decimal(10,2) NOT NULL,
  `clarity` varchar(255) NOT NULL,
  `color` varchar(255) NOT NULL,
  `cut_type` varchar(255) NOT NULL,
  `fluorescence_type` varchar(255) NOT NULL,
  `discount_type` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `is_approved` enum('Decline','Accept','Pending') NOT NULL DEFAULT 'Pending',
  `boost` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `diamond`
--

INSERT INTO `diamond` (`id`, `nature`, `photo_certificate`, `photo_diamond`, `video_diamond`, `shape`, `certificate`, `weight`, `clarity`, `color`, `cut_type`, `fluorescence_type`, `discount_type`, `user_id`, `is_approved`, `boost`, `is_active`) VALUES
(2, 'Natural', 'Screenshot 2024-06-29 at 1.25.16 PM.png', 'Screenshot 2024-06-29 at 1.25.16 PM.png', '', 'round', 'GIA', '12.00', 'VVS1', 'White D', '12', '12', '12', 3, 'Pending', 0, 1),
(3, 'CVD / Lab-grown', '66828005dd894-Screenshot 2024-06-29 at 1.05.35 PM.png', 'Screenshot 2024-06-29 at 1.05.35 PM.png', '66828005ddbe3-Screenshot 2024-06-29 at 1.05.35 PM.png', 'Princess', 'HRD', '12.00', 'VVS2', 'Fancy 12', '12', '12', '312', 3, 'Accept', 0, 1),
(4, 'Natural', '6682a3532162f-Screenshot 2024-06-29 at 1.05.35 PM.png', '6682a35321842-Screenshot 2024-06-29 at 1.05.35 PM.png', '', 'Princess', 'sds', '12.00', 'VVS1', 'White D', '121', '12', '12', 3, 'Accept', 0, 1),
(5, 'Natural', '668e902896cad-wallpaper2.jpg', '668e902896f49-wallpaper.jpg', '', 'round', 'GIA', '55.00', 'VVS2', 'White D', '111', '111', '111', 3, 'Accept', 0, 1),
(8, 'Natural', '66a26732054ab-Rick.jpeg', '66a26732059a8-Rick.jpeg', 'Screen Recording 2024-08-21 at 8.13.59 PM.mov', 'round', 'gia', '22.00', 'if', 'White d', 'excellent', 'none', 'none', 1, 'Pending', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `gadgets`
--

CREATE TABLE `gadgets` (
  `id` int(11) NOT NULL,
  `title` varchar(120) NOT NULL,
  `photo_gadget` varchar(255) NOT NULL,
  `video_gadget` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `user_id` int(11) NOT NULL,
  `is_approved` enum('Decline','Accept','Pending') NOT NULL DEFAULT 'Pending',
  `boost` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `gemstone`
--

CREATE TABLE `gemstone` (
  `id` int(11) NOT NULL,
  `gemstone_name` varchar(255) NOT NULL,
  `photo_certificate` varchar(255) NOT NULL,
  `photo_gemstone` varchar(255) NOT NULL,
  `video_gemstone` varchar(255) DEFAULT NULL,
  `weight` decimal(10,2) NOT NULL,
  `cut` varchar(255) NOT NULL,
  `shape` varchar(255) NOT NULL,
  `color` varchar(255) NOT NULL,
  `type` char(9) NOT NULL,
  `certificate` varchar(255) NOT NULL,
  `comment` text,
  `price/ct` double NOT NULL,
  `user_id` int(11) NOT NULL,
  `is_approved` enum('Decline','Accept','Pending') NOT NULL DEFAULT 'Pending',
  `boost` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `gemstone`
--

INSERT INTO `gemstone` (`id`, `gemstone_name`, `photo_certificate`, `photo_gemstone`, `video_gemstone`, `weight`, `cut`, `shape`, `color`, `type`, `certificate`, `comment`, `price/ct`, `user_id`, `is_approved`, `boost`, `is_active`) VALUES
(1, 'sd lane', 'Screenshot 2024-06-29 at 1.25.16 PM.png', 'Screenshot 2024-06-29 at 1.25.16 PM.png', '', '12.00', '12', 'Round', '12', '', 'SSEF', '12', 0, 2, 'Pending', 0, 1),
(2, 'sd', '66829a9145d38-Screenshot 2024-06-29 at 1.05.35 PM.png', '66829a9145eb4-Screenshot 2024-06-29 at 1.05.35 PM.png', '', '12.00', '12', 'Round', '12', '', 'SSEF', '12', 0, 2, 'Pending', 0, 1),
(3, 'sd', '66829ab2995fe-Screenshot 2024-06-29 at 1.05.35 PM.png', '66829ab299790-Screenshot 2024-06-29 at 1.05.35 PM.png', '', '12.00', '12', 'Round', '12', '', 'SSEF', '12', 0, 2, 'Accept', 0, 1),
(4, 'sd', '66829ad059fca-Screenshot 2024-06-29 at 1.05.35 PM.png', '66829ad05a115-Screenshot 2024-06-29 at 1.05.35 PM.png', '', '12.00', '12', 'Round', '12', '', 'SSEF', '12', 0, 2, 'Decline', 0, 1),
(5, 'lane', '66829ae2f0154-Screenshot 2024-07-01 at 1.17.38 PM.png', '66829ae2f0397-Screenshot 2024-07-01 at 1.47.47 PM.png', '66829ae2f050f-Screenshot 2024-07-01 at 1.17.38 PM.png', '12.00', '12', 'Round', '12', '', 'SSEF', '12', 0, 2, 'Decline', 0, 1),
(6, 'sdsds', 'Screenshot 2024-06-29 at 1.05.35 PM.png', '66829b2a62433-Screenshot 2024-07-01 at 1.47.47 PM.png', '66829b2a62608-Screenshot 2024-07-01 at 1.47.47 PM.png', '12.00', 'sds', 'Round', 'sds', '', 'Gübelin', 'sds', 0, 2, 'Decline', 0, 1),
(7, 'dqwdqw', '668e8fca9a19c-wallpaper2.jpg', '668e8fca9a49d-wallpaper.jpg', '', '70.00', '122', 'Princess', 'd', '', 'Gübelin', 'qwdqwqwdqwdqdw', 0, 3, 'Accept', 0, 1),
(8, 'dqwdqw', '6697b2b2b2108-wallpaper.jpg', '6697b2b2b24d6-wallpaper2.jpg', '', '70.00', '122', 'Round', '111', '', 'SSEF', 'test1 7/17/2024', 0, 1, 'Accept', 0, 0),
(9, 'dqwdqw', '6697b56ccc7e5-night-mountains-minimalist-8k-wo-3840x2400.jpg', '6697b56cccd6e-Minimalist-Wallpaper.jpg', '', '70.00', '122', 'Round', 'd', '', 'SSEF', 'qwdqdqdqdw', 0, 1, 'Accept', 0, 0),
(10, 'dqwdqw', '6697b6aaf1446-Minimalist-Wallpaper.jpg', '6697b6aaf1a02-night-mountains-minimalist-8k-wo-3840x2400.jpg', '', '70.00', '122', 'Round', 'd', '', 'SSEF', 'qdqwwqd', 0, 4, 'Accept', 0, 1),
(11, 'qwdqd', '66a26be6925de-Chill.jfif', '66a26be692af0-Chill.jfif', 'File upload error: 4', '2.00', '2', 'Round', 'd', '', 'SSEF', 'dqdq', 0, 1, 'Pending', 0, 1),
(12, 'qdqd', '66a26cc62d5d3-pexels-pixabay-220201.jpg', '66a26cc62dbd0-pexels-pixabay-220201.jpg', 'File upload error: 4', '22.00', '22', 'Round', 'd', '', 'SSEF', 'dqdqdq', 0, 1, 'Pending', 0, 1),
(13, 'ramy', '66a2a6343dffd-Screenshot 2024-07-25 at 5.46.32 PM.png', '66a2a6343e655-Screenshot 2024-07-25 at 5.46.32 PM.png', 'File upload error: 4', '88.00', 'ff', 'Round', 'green', '', 'Gübelin', 'yggg', 0, 1, 'Accept', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `jewelry`
--

CREATE TABLE `jewelry` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `photo_jewelry` varchar(255) DEFAULT NULL,
  `photo_certificate` varchar(255) NOT NULL,
  `video` varchar(255) DEFAULT NULL,
  `type` varchar(255) NOT NULL,
  `description` text,
  `price` decimal(10,0) NOT NULL,
  `boost` tinyint(1) NOT NULL DEFAULT '0',
  `is_approved` enum('Pending','Accept','Decline') DEFAULT 'Pending',
  `is_active` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `jewelry`
--

INSERT INTO `jewelry` (`id`, `user_id`, `title`, `photo_jewelry`, `photo_certificate`, `video`, `type`, `description`, `price`, `boost`, `is_approved`, `is_active`) VALUES
(1, 4, 'wwd', 'Error uploading file.', 'Error uploading file.', '', 'ddqwwdqwdq', 'dwqqwqqd', '0', 0, 'Pending', 1),
(2, 1, 'sds', '6697b76706788-Screenshot 2024-07-17 at 2.13.04 PM.png', '6697b767068c0-Screenshot 2024-07-17 at 2.13.04 PM.png', '6697b767069c0-Screenshot 2024-07-17 at 2.13.04 PM.png', 'sdsd', 'sdsd', '0', 0, 'Pending', 1),
(3, 4, 'wwd', '6697b7a0a9cab-Minimalist-Wallpaper.jpg', '6697b7a0aa02e-night-mountains-minimalist-8k-wo-3840x2400.jpg', '', 'ddqwwdqwdq', 'dwqqwqqd', '0', 0, 'Pending', 1),
(4, 1, 'f8u', '66a2646d3fd1a-wallpaper.jpg', '66a2646d4061a-wallpaper2.jpg', '', 'dqwdqw', 'dqwdqwdq', '0', 0, 'Pending', 1),
(5, 1, 'igy8s', '66a264c1dd6c7-wallpaper.jpg', '66a264c1dddb6-wallpaper2.jpg', '', 'dqwdq', 'qdqd', '0', 0, 'Pending', 1);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `Sender` int(11) NOT NULL,
  `Receiver` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `read_status` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `Sender`, `Receiver`, `message`, `created_at`, `read_status`) VALUES
(1, 2, 1, 'Hello my nigga', '2024-07-04 13:13:59', 1),
(2, 2, 1, 'hello 2', '2024-07-04 13:15:07', 1),
(3, 2, 1, 'hello world', '2024-07-04 14:07:58', 0),
(4, 1, 3, 'Your Diamond post with ID 7 has been approved.', '2024-07-25 14:35:50', 0),
(5, 1, 1, 'Your Gemstone post with ID 9 has been approved.', '2024-07-25 14:35:51', 0),
(6, 1, 1, 'Your Gemstone post with ID 8 has been approved.', '2024-07-25 14:35:52', 0),
(7, 1, 3, 'Your Gemstone post with ID 7 has been approved.', '2024-07-25 14:35:52', 0),
(8, 1, 2, 'Your Diamond post with ID 6 has been approved.', '2024-07-25 14:35:52', 0),
(9, 1, 2, 'Your Diamond post with ID 5 has been approved.', '2024-07-25 14:35:52', 0),
(10, 1, 1, 'Your Gemstone post with ID 13 has been approved.', '2024-07-25 19:24:05', 0);

-- --------------------------------------------------------

--
-- Table structure for table `rapaport`
--

CREATE TABLE `rapaport` (
  `id` int(11) NOT NULL,
  `report_type` enum('Round','Pear') NOT NULL,
  `pdf_url` varchar(255) NOT NULL,
  `posted_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone_number` varchar(255) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `passport_photo` varchar(255) DEFAULT NULL,
  `role` enum('personal','business','admin') NOT NULL,
  `business_certificate` varchar(255) DEFAULT NULL,
  `is_approved` tinyint(1) DEFAULT '0',
  `num_posts` int(11) DEFAULT '0',
  `can_see_rapaport` tinyint(1) DEFAULT '0',
  `front_id_photo` varchar(255) DEFAULT NULL,
  `back_id_photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `full_name`, `username`, `password`, `phone_number`, `profile_picture`, `passport_photo`, `role`, `business_certificate`, `is_approved`, `num_posts`, `can_see_rapaport`, `front_id_photo`, `back_id_photo`) VALUES
(1, 'hairzone@gmail.com', 'user1', 'user1', '$2y$10$YGDLG8YWpGZLnYZcZUFukeIdB9kdFxKKPQSM2BIFDIzuEak.jB.t.', '12345678', '66d3a4078a688-Screenshot 2024-09-01 at 2.15.10 AM.png', '66869fcfef59d-wallpaper.jpg', 'personal', NULL, 1, 3, 0, '66d3a41da20af-Screenshot 2024-09-01 at 2.15.08 AM.png', '66d3a41106ddd-Screenshot 2024-09-01 at 2.15.08 AM (2).png'),
(2, 'user@gmail.com', 'user2', 'user2', '$2y$10$QES8mkeTicmt0QpNG6u1y.tYsJ8scz4u8g2RMV.H44K3AfkOtMR8S', '87654321', '6686a00c9b53b-wallpaper2.jpg', '6686a00c9b787-wallpaper.jpg', 'admin', NULL, 0, 0, 0, NULL, NULL),
(3, 'cak@gmail.com', 'cak', 'cak', '$2y$10$BcjYXLi22PzKKrLMW0rLeeedwH3Vsfd7WJmnCiBKzobguM7KAq46C', '11111111', '668e8ee3b6226-wallpaper2.jpg', '668e8ee3b66d9-wallpaper.jpg', 'personal', NULL, 0, 0, 0, NULL, NULL),
(4, 'elio@gmail.com', 'elio', 'elio', '$2y$10$enhk.YQAw007eEdLe5hx8euWd2zNriwEj2tLm86ve15y5TjbrTYxO', '3123123312', '6697b609e3b86-Minimalist-Wallpaper.jpg', '6697b609e3e20-night-mountains-minimalist-8k-wo-3840x2400.jpg', 'personal', NULL, 0, 0, 0, NULL, NULL),
(5, 'cbf@gmail.com', 'cbf', 'cbf', '$2y$10$6Q0fTkmOZZNUsnwpslynGOlYpTtVipa2LrLk1z0qR.oU.obxogRvy', '+96136492087', '66b4a62ee7f68-Rick.jpeg', '66b4a62ee8568-Rick.jpeg', 'personal', NULL, 0, 0, 0, NULL, NULL),
(11, 'test@gmail.com', 'test', 'test', '$2y$10$srI0fUp7.7hAWjchY9a8PuIiZt.4NbcQcq079oYXHtz1ZZoahfhVK', '+96136677229', '66b4a89cae81d-Peppa.jpeg', NULL, 'personal', NULL, 0, 0, 0, '66b4a89cae8c8-Peppa.jpeg', '66b4a89cae91f-Peppa.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `user_favorites`
--

CREATE TABLE `user_favorites` (
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_type` enum('diamond','gemstone','jewelry','black_diamond','gadget','watch') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user_favorites`
--

INSERT INTO `user_favorites` (`user_id`, `product_id`, `product_type`) VALUES
(1, 1, 'watch'),
(1, 3, 'diamond'),
(1, 7, 'gemstone'),
(1, 8, 'gemstone'),
(1, 9, 'gemstone'),
(1, 10, 'gemstone');

-- --------------------------------------------------------

--
-- Table structure for table `watches`
--

CREATE TABLE `watches` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `photo_watch` varchar(255) DEFAULT NULL,
  `photo_certificate` varchar(255) NOT NULL,
  `video` varchar(255) DEFAULT NULL,
  `brand` varchar(255) NOT NULL,
  `description` text,
  `price` decimal(10,0) NOT NULL,
  `boost` tinyint(1) NOT NULL DEFAULT '0',
  `is_approved` enum('Pending','Accept','Decline') DEFAULT 'Pending',
  `is_active` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `watches`
--

INSERT INTO `watches` (`id`, `user_id`, `title`, `photo_watch`, `photo_certificate`, `video`, `brand`, `description`, `price`, `boost`, `is_approved`, `is_active`) VALUES
(2, 1, 'qdqdd', 'Error uploading file.', '669fa1ec97458-night-mountains-minimalist-8k-wo-3840x2400.jpg', '', 'qdqdqd', 'dqwdqdqdq', '0', 0, 'Accept', 0),
(3, 1, 'iy8ftu', 'Error uploading file.', '66a265532c803-Chill.jfif', '', 'dwddq', 'dwqdq', '0', 0, 'Accept', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `contact_us`
--
ALTER TABLE `contact_us`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `diamond`
--
ALTER TABLE `diamond`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `gadgets`
--
ALTER TABLE `gadgets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `gemstone`
--
ALTER TABLE `gemstone`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `jewelry`
--
ALTER TABLE `jewelry`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_jewelry_id` (`user_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_ofSender` (`Sender`),
  ADD KEY `id_ofReceiver` (`Receiver`);

--
-- Indexes for table `rapaport`
--
ALTER TABLE `rapaport`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `phone_number` (`phone_number`);

--
-- Indexes for table `user_favorites`
--
ALTER TABLE `user_favorites`
  ADD PRIMARY KEY (`user_id`,`product_id`,`product_type`);

--
-- Indexes for table `watches`
--
ALTER TABLE `watches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_watch_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `contact_us`
--
ALTER TABLE `contact_us`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `diamond`
--
ALTER TABLE `diamond`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `gadgets`
--
ALTER TABLE `gadgets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gemstone`
--
ALTER TABLE `gemstone`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `jewelry`
--
ALTER TABLE `jewelry`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `rapaport`
--
ALTER TABLE `rapaport`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `watches`
--
ALTER TABLE `watches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `gemstone`
--
ALTER TABLE `gemstone`
  ADD CONSTRAINT `gemstone_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `jewelry`
--
ALTER TABLE `jewelry`
  ADD CONSTRAINT `fk_user_jewelry_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`Sender`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`Receiver`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_favorites`
--
ALTER TABLE `user_favorites`
  ADD CONSTRAINT `user_favorites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `watches`
--
ALTER TABLE `watches`
  ADD CONSTRAINT `fk_user_watch_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
