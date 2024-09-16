-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 16, 2024 at 11:35 AM
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
-- Database: `web`
--

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `title`, `description`, `created_at`) VALUES
(13, 'Drinks', 'we need juices', '2024-09-15 12:16:00'),
(14, 'books', 'we need books\r\n', '2024-09-15 12:16:13'),
(15, 'medicine', 'we need 5 nurofen', '2024-09-15 13:51:38'),
(16, 'clothes', 'we need 4 jackets', '2024-09-15 13:53:42'),
(17, 'drinks', 'we need 2 coca colas', '2024-09-15 13:54:35'),
(18, 'medicine', 'we need 3 emetostop', '2024-09-15 13:57:03'),
(19, 'medicine', 'we need algofren ', '2024-09-16 08:58:00');

-- --------------------------------------------------------

--
-- Table structure for table `announcement_items`
--

CREATE TABLE `announcement_items` (
  `announcement_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcement_items`
--

INSERT INTO `announcement_items` (`announcement_id`, `item_id`) VALUES
(13, 134),
(14, 287),
(15, 177),
(16, 67),
(17, 85),
(18, 179),
(19, 186);

-- --------------------------------------------------------

--
-- Table structure for table `base_location`
--

CREATE TABLE `base_location` (
  `id` int(11) NOT NULL,
  `latitude` double DEFAULT NULL,
  `longitude` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `base_location`
--

INSERT INTO `base_location` (`id`, `latitude`, `longitude`) VALUES
(1, 37.989259046798, 23.733904764973);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `created_at`) VALUES
(5, 'Food', NULL, '2024-09-15 15:18:37'),
(6, 'Beverages', NULL, '2024-09-14 22:26:09'),
(7, 'Clothing', NULL, '2024-09-14 22:12:26'),
(42, 'Μedicines', NULL, '2024-09-15 13:41:48'),
(59, 'Books', NULL, '2024-09-14 22:10:59');

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `location` point DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`id`, `category_id`, `name`, `description`, `quantity`, `location`, `created_at`, `updated_at`) VALUES
(16, 6, 'Water', 'volume: 1.5l, pack size: 6', 4, NULL, '2024-09-14 22:26:09', '2024-09-16 09:04:31'),
(17, 6, 'Orange juice', 'volume: 250ml, pack size: 12', 1, NULL, '2024-09-15 13:42:55', '2024-09-15 13:42:55'),
(20, 5, 'Bread', 'weight: 1kg', 3, NULL, '2024-09-15 15:21:03', '2024-09-16 08:52:09'),
(22, 7, 'Men Sneakers', 'size: 44', 1, NULL, '2024-09-15 13:42:55', '2024-09-15 13:42:55'),
(36, 7, 'Blanket', 'size: 50\" x 60\"', 1, NULL, '2024-09-15 13:42:55', '2024-09-15 13:42:55'),
(65, 7, 'Underwear', ':', 1, NULL, '2024-09-15 13:42:55', '2024-09-15 13:42:55'),
(66, 7, 'Socks', ':', 1, NULL, '2024-09-15 13:42:55', '2024-09-15 13:42:55'),
(67, 7, 'Warm Jacket', ':', 0, NULL, '2024-09-15 13:42:55', '2024-09-16 09:16:17'),
(68, 7, 'Raincoat', ':', 1, NULL, '2024-09-15 13:42:55', '2024-09-15 13:42:55'),
(69, 7, 'Gloves', ':', 1, NULL, '2024-09-15 13:42:55', '2024-09-15 13:42:55'),
(70, 7, 'Pants', ':', 1, NULL, '2024-09-15 13:42:55', '2024-09-15 13:42:55'),
(71, 7, 'Boots', ':', 1, NULL, '2024-09-15 13:42:55', '2024-09-15 13:42:55'),
(85, 6, 'Coca Cola', 'Volume: 500ml', 1, NULL, '2024-09-15 13:42:56', '2024-09-15 13:42:56'),
(90, 5, 'Condensed milk', 'weight: 400gr', 1, NULL, '2024-09-15 15:18:37', '2024-09-15 15:18:37'),
(100, 6, 'Tea', 'volume: 500ml', 1, NULL, '2024-09-15 13:42:56', '2024-09-15 13:42:56'),
(134, 6, 'Juice', 'volume: 500ml', 2, NULL, '2024-09-14 22:59:27', '2024-09-16 09:04:31'),
(177, 42, 'nurofen', ': 10', 1, NULL, '2024-09-15 13:41:48', '2024-09-15 13:41:48'),
(178, 42, 'imodium', ': 5', 1, NULL, '2024-09-15 13:41:48', '2024-09-15 13:41:48'),
(179, 42, 'emetostop', ': 5', 3, NULL, '2024-09-15 13:41:48', '2024-09-16 09:16:41'),
(180, 42, 'xanax', ': 5', 1, NULL, '2024-09-15 13:41:48', '2024-09-15 13:41:48'),
(181, 42, 'saflutan', ': 2', 1, NULL, '2024-09-15 13:41:48', '2024-09-15 13:41:48'),
(182, 42, 'sadolin', ': 3', 1, NULL, '2024-09-15 13:41:48', '2024-09-15 13:41:48'),
(183, 42, 'depon', ': 20', 1, NULL, '2024-09-15 13:41:48', '2024-09-15 13:41:48'),
(184, 42, 'panadol', ': 6', 1, NULL, '2024-09-15 13:41:48', '2024-09-15 13:41:48'),
(185, 42, 'ponstan ', ': 10', 1, NULL, '2024-09-15 13:41:48', '2024-09-15 13:41:48'),
(186, 42, 'algofren', '10: 600ml, :', 1, NULL, '2024-09-15 13:41:48', '2024-09-15 13:41:48'),
(188, 6, 'cold coffee', '10: 330ml', 1, NULL, '2024-09-15 13:42:56', '2024-09-15 13:42:56'),
(204, 6, 'Club Soda', 'volume: 500ml', 0, NULL, '2024-09-15 11:56:17', '2024-09-15 16:41:17'),
(216, 7, 'Hoodie', ':', 1, NULL, '2024-09-14 22:12:26', '2024-09-14 22:12:26'),
(287, 59, 'Love me again ', 'pages: 654', 1, NULL, '2024-09-14 22:10:59', '2024-09-14 22:10:59'),
(305, 59, 'The Great Gatsby', ':', 1, NULL, '2024-09-16 08:43:39', '2024-09-16 08:43:39');

-- --------------------------------------------------------

--
-- Table structure for table `offers`
--

CREATE TABLE `offers` (
  `id` int(11) NOT NULL,
  `citizen_username` varchar(255) NOT NULL,
  `offer_date` datetime NOT NULL DEFAULT current_timestamp(),
  `announcement_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `status` enum('pending','active','completed') DEFAULT 'pending',
  `vehicle_id` int(11) DEFAULT NULL,
  `assignment_date` datetime DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `offers`
--

INSERT INTO `offers` (`id`, `citizen_username`, `offer_date`, `announcement_id`, `item_id`, `quantity`, `status`, `vehicle_id`, `assignment_date`, `latitude`, `longitude`) VALUES
(2, 'citizen1', '2024-09-15 15:24:21', 13, 134, 1, 'completed', 1, NULL, 23.21540000, 23.72770000),
(3, 'citizen4', '2024-09-15 16:46:42', 13, 134, 2, 'pending', NULL, NULL, 37.98269400, 23.76527100),
(4, 'citizen4', '2024-09-15 16:46:53', 14, 287, 1, 'pending', NULL, NULL, 37.98269400, 23.76527100),
(5, 'citizen4', '2024-09-15 16:55:18', 15, 177, 5, 'active', 4, NULL, 37.98269400, 23.76527100),
(6, 'citizen5', '2024-09-15 16:55:49', 16, 67, 2, 'pending', NULL, NULL, 37.97624900, 23.75213700),
(7, 'citizen5', '2024-09-15 16:56:24', 17, 85, 2, 'completed', 1, NULL, 37.97624900, 23.75213700),
(8, 'citizen5', '2024-09-15 16:57:34', 18, 179, 3, 'completed', 1, NULL, 37.97624900, 23.75213700),
(9, 'citizen1', '2024-09-15 20:06:30', 16, 67, 3, 'pending', NULL, NULL, 37.97674000, 23.75604000);

-- --------------------------------------------------------

--
-- Table structure for table `requests`
--

CREATE TABLE `requests` (
  `id` int(11) NOT NULL,
  `citizen_username` varchar(100) NOT NULL,
  `request_date` datetime NOT NULL DEFAULT current_timestamp(),
  `item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `status` enum('pending','active','completed') DEFAULT 'pending',
  `vehicle_id` int(11) DEFAULT NULL,
  `assignment_date` datetime DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `requests`
--

INSERT INTO `requests` (`id`, `citizen_username`, `request_date`, `item_id`, `quantity`, `status`, `vehicle_id`, `assignment_date`, `latitude`, `longitude`) VALUES
(1, 'citizen1', '2024-09-15 15:24:35', 16, 2, 'pending', NULL, NULL, 37.97674000, 23.75604000),
(2, 'citizen1', '2024-09-15 16:38:56', 287, 1, 'pending', NULL, NULL, 37.97674000, 23.75604000),
(3, 'citizen1', '2024-09-15 16:39:11', 134, 2, 'pending', NULL, NULL, 37.97674000, 23.75604000),
(4, 'citizen1', '2024-09-15 16:39:33', 204, 4, 'completed', 1, NULL, 37.97674000, 23.75604000),
(5, 'citizen2 ', '2024-09-15 16:40:15', 216, 1, 'pending', NULL, NULL, 37.97983500, 23.76290400),
(6, 'citizen2 ', '2024-09-15 16:43:52', 36, 3, 'pending', NULL, NULL, 37.97983500, 23.76290400),
(7, 'citizen2 ', '2024-09-15 16:44:38', 66, 2, 'pending', NULL, NULL, 37.97983500, 23.76290400),
(8, 'citizen2 ', '2024-09-15 16:44:49', 177, 5, 'pending', NULL, NULL, 37.97983500, 23.76290400),
(9, 'citizen3', '2024-09-15 16:45:26', 100, 2, 'pending', NULL, NULL, 37.97495000, 23.75475000),
(10, 'citizen3', '2024-09-15 16:45:36', 134, 2, 'pending', NULL, NULL, 37.97495000, 23.75475000),
(11, 'citizen3', '2024-09-15 16:45:43', 85, 2, 'pending', NULL, NULL, 37.97495000, 23.75475000),
(12, 'citizen3', '2024-09-15 16:45:55', 67, 4, 'completed', 1, NULL, 37.97495000, 23.75475000),
(13, 'citizen6', '2024-09-15 20:01:56', 179, 2, 'completed', 1, NULL, 37.97072600, 23.75175500);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('administrator','rescuer','citizen') NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `latitude` decimal(9,6) DEFAULT NULL,
  `longitude` decimal(9,6) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password_hash`, `role`, `name`, `phone`, `latitude`, `longitude`, `created_at`) VALUES
(1, 'admin1', 'admin1', 'administrator', 'sotiris', '6972639852', 37.983800, 23.727500, '2024-09-14 20:40:05'),
(2, 'citizen1', 'citizen1', 'citizen', 'jo', '6975741236', 37.976740, 23.756040, '2024-09-14 20:40:05'),
(3, 'rescuer1', 'rescuer1', 'rescuer', 'paulos', '6985478521', 37.970624, 23.751755, '2024-09-14 20:40:05'),
(4, 'rescuer2', 'rescuer2', 'rescuer', 'kalliopi', '6985321475', 37.977805, 23.747760, '2024-09-14 20:45:36'),
(5, 'citizen2 ', 'citizen2', 'citizen', 'vaso', '6984698980', 37.979835, 23.762904, '2024-09-15 13:36:31'),
(6, 'citizen3', 'citizen3', 'citizen', 'vaggelis', '6975741237', 37.974950, 23.754750, '2024-09-15 13:36:31'),
(7, 'citizen4', 'citizen4', 'citizen', 'manos', '6985478521', 37.982694, 23.765271, '2024-09-15 13:36:31'),
(8, 'citizen5', 'citizen5', 'citizen', 'nikos', '6985427410', 37.976249, 23.752137, '2024-09-15 13:36:31'),
(9, 'rescuer3', 'rescuer3', 'rescuer', 'ioanna', '6985326874', 37.994136, 23.771560, '2024-09-15 14:00:02'),
(11, 'citizen6', 'citizen6 ', 'citizen', 'andreas', '6973633841', 37.970726, 23.751755, '2024-09-15 16:54:39'),
(12, 'citizen7', 'citizen7', 'rescuer', 'clara', '6985236541', NULL, NULL, '2024-09-16 08:56:24');

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

CREATE TABLE `vehicles` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `cargo` varchar(255) DEFAULT NULL,
  `status` enum('available','busy','unavailable') DEFAULT 'available',
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `rescuer_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicles`
--

INSERT INTO `vehicles` (`id`, `username`, `cargo`, `status`, `latitude`, `longitude`, `rescuer_id`) VALUES
(1, 'paulos', 'test cargo', 'available', 37.97062429, 23.75175478, 3),
(2, 'paulos', 'test cargo', 'available', 37.99799000, 23.74013700, 3),
(3, 'ioanna', 'cargo3', 'available', 37.99413600, 23.77156000, 9),
(4, 'kalliopi', 'cargo2', 'busy', 37.97492836, 23.75483000, 4),
(5, 'ioanna', 'cargo3', 'available', 37.99413600, 23.77156000, 9),
(6, 'kalliopi', 'cargo2', 'available', 37.97492836, 23.75483000, 4);

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_loads`
--

CREATE TABLE `vehicle_loads` (
  `id` int(11) NOT NULL,
  `vehicle_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicle_loads`
--

INSERT INTO `vehicle_loads` (`id`, `vehicle_id`, `item_id`, `quantity`) VALUES
(6, 1, 179, 4),
(7, 1, 85, 2),
(8, 1, 67, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `announcement_items`
--
ALTER TABLE `announcement_items`
  ADD PRIMARY KEY (`announcement_id`,`item_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `base_location`
--
ALTER TABLE `base_location`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_inventory_category_id` (`category_id`);

--
-- Indexes for table `offers`
--
ALTER TABLE `offers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `citizen_username` (`citizen_username`),
  ADD KEY `announcement_id` (`announcement_id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `vehicle_id` (`vehicle_id`),
  ADD KEY `idx_offers_status` (`status`);

--
-- Indexes for table `requests`
--
ALTER TABLE `requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `citizen_username` (`citizen_username`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `vehicle_id` (`vehicle_id`),
  ADD KEY `idx_requests_status` (`status`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_vehicles_rescuer_id` (`rescuer_id`);

--
-- Indexes for table `vehicle_loads`
--
ALTER TABLE `vehicle_loads`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vehicle_id` (`vehicle_id`,`item_id`),
  ADD KEY `item_id` (`item_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `base_location`
--
ALTER TABLE `base_location`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=306;

--
-- AUTO_INCREMENT for table `offers`
--
ALTER TABLE `offers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `requests`
--
ALTER TABLE `requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `vehicle_loads`
--
ALTER TABLE `vehicle_loads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `announcement_items`
--
ALTER TABLE `announcement_items`
  ADD CONSTRAINT `announcement_items_ibfk_1` FOREIGN KEY (`announcement_id`) REFERENCES `announcements` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `announcement_items_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `inventory` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `inventory`
--
ALTER TABLE `inventory`
  ADD CONSTRAINT `inventory_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `offers`
--
ALTER TABLE `offers`
  ADD CONSTRAINT `offers_ibfk_1` FOREIGN KEY (`citizen_username`) REFERENCES `users` (`username`) ON DELETE CASCADE,
  ADD CONSTRAINT `offers_ibfk_2` FOREIGN KEY (`announcement_id`) REFERENCES `announcements` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `offers_ibfk_3` FOREIGN KEY (`item_id`) REFERENCES `inventory` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `offers_ibfk_4` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `requests`
--
ALTER TABLE `requests`
  ADD CONSTRAINT `requests_ibfk_1` FOREIGN KEY (`citizen_username`) REFERENCES `users` (`username`) ON DELETE CASCADE,
  ADD CONSTRAINT `requests_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `inventory` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `requests_ibfk_3` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD CONSTRAINT `vehicles_ibfk_1` FOREIGN KEY (`rescuer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `vehicle_loads`
--
ALTER TABLE `vehicle_loads`
  ADD CONSTRAINT `vehicle_loads_ibfk_1` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`),
  ADD CONSTRAINT `vehicle_loads_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `inventory` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
