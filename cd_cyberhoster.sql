-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 14, 2025 at 10:24 PM
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
-- Database: `cd_cyberhoster`
--

-- --------------------------------------------------------

--
-- Table structure for table `attributes`
--

CREATE TABLE `attributes` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attributes`
--

INSERT INTO `attributes` (`id`, `name`, `slug`, `created_at`) VALUES
(2, 'Dimension', 'sizes', '2025-04-09 13:59:07'),
(5, 'Color', 'colors', '2025-04-09 15:24:56');

-- --------------------------------------------------------

--
-- Table structure for table `attribute_term`
--

CREATE TABLE `attribute_term` (
  `attribute_term_id` int(11) NOT NULL,
  `term_name` varchar(255) DEFAULT NULL,
  `attribute_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attribute_term`
--

INSERT INTO `attribute_term` (`attribute_term_id`, `term_name`, `attribute_id`) VALUES
(6, 'Small', 1),
(7, 'Medium', 2),
(9, 'Medium', 2),
(25, 'Blue', 5),
(26, 'Red', 5);

-- --------------------------------------------------------

--
-- Table structure for table `attribute_values`
--

CREATE TABLE `attribute_values` (
  `id` int(11) NOT NULL,
  `attribute_id` int(11) NOT NULL,
  `value` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `display` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `name`, `description`, `image`, `parent_id`, `created_at`, `display`) VALUES
(1, 'Sword', '', 'slide_1.jpg', 0, '2025-04-14 19:16:12', 1),
(2, 'Sword 1', '', 'Category_3.jpg', 1, '2025-04-14 19:23:00', 0),
(3, 'Sword 2', 'This is Sword 2', 'Category_1.jpg', 1, '2025-04-14 19:23:30', 0),
(4, 'Sword 3', 'this is Sword 3', 'Category_2.jpg', 1, '2025-04-14 19:23:55', 0),
(5, 'Sword 4', 'Sword 4', 'Category_3.jpg', 1, '2025-04-14 19:24:54', 0),
(6, 'Knife', '', 'slide_3.jpg', 0, '2025-04-14 19:27:17', 0),
(7, 'Knife 1', 'Knife 1', 'Category_1.jpg', 6, '2025-04-14 19:27:45', 0),
(8, 'Knife 2', 'Knife 2', 'Category_3.jpg', 6, '2025-04-14 19:28:00', 0),
(9, 'Knife 3', '', 'Category_2.jpg', 6, '2025-04-14 19:28:13', 0),
(10, 'Knife 4', 'Knife 4', 'Category_1.jpg', 6, '2025-04-14 19:28:30', 0),
(11, 'Knife 5', 'Knife 5', 'Category_2.jpg', 6, '2025-04-14 19:28:45', 0),
(12, 'Shirt', '', 'Category_3.jpg', 0, '2025-04-14 19:31:58', 0);

-- --------------------------------------------------------

--
-- Table structure for table `contact_information`
--

CREATE TABLE `contact_information` (
  `id` int(11) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `whatsapp` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_information`
--

INSERT INTO `contact_information` (`id`, `phone`, `whatsapp`, `email`, `address`, `updated_at`) VALUES
(1, '03122844751', '034452457', 'shahrukhaptech5@gmail.com', 'ABC 12315', '2025-04-11 18:54:54');

-- --------------------------------------------------------

--
-- Table structure for table `credentials`
--

CREATE TABLE `credentials` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_token_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `credentials`
--

INSERT INTO `credentials` (`id`, `name`, `email`, `password`, `created_at`, `reset_token`, `reset_token_expiry`) VALUES
(1, 'shahrukh', 'shahrukhaptech5@gmail.com', '$2y$10$VQWxhAL2oe9Q3SBVYNeYY.yOiI1/4eSMggY9nWacfZqIHupxIQbey', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `footer`
--

CREATE TABLE `footer` (
  `id` int(11) NOT NULL,
  `footer_logo` varchar(255) NOT NULL,
  `footer_content` text DEFAULT NULL,
  `map_location` varchar(255) NOT NULL DEFAULT '0',
  `copyright_text` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `footer`
--

INSERT INTO `footer` (`id`, `footer_logo`, `footer_content`, `map_location`, `copyright_text`, `created_at`, `updated_at`) VALUES
(1, 'Assets/uploads/Footer_logos/67fd2dd7af4ce.png', 'Now Welcome to Cyber Hotinger – your trusted partner in cutting-edge web hosting services. We specialize in providing fast, reliable, and secure hosting solutions tailored to meet the diverse needs of businesses, entrepreneurs, and developers', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3609.4042450756847!2d55.15473057270105!3d25.22330598136635!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3e5f4094ade2dca3%3A0xef80338ddb88cf7f!2sUnited%20Kingdom!5e0!3m2!1sen!2s!4v1744136805222!5', 'Copyright © 2025 CyberHostinger. All rights reserved', '2025-04-08 18:01:52', '2025-04-14 20:14:36');

-- --------------------------------------------------------

--
-- Table structure for table `footer_menu`
--

CREATE TABLE `footer_menu` (
  `id` int(11) NOT NULL,
  `footer_id` int(11) NOT NULL,
  `menu_title` varchar(255) NOT NULL,
  `menu_link` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `menu_group` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `footer_menu`
--

INSERT INTO `footer_menu` (`id`, `footer_id`, `menu_title`, `menu_link`, `is_active`, `created_at`, `updated_at`, `menu_group`) VALUES
(83, 1, 'Swords 1', 'https://chatgpt.com/', 1, '2025-04-14 20:14:36', '2025-04-14 20:14:36', 'Swords'),
(84, 1, 'Swords 2', 'https://chatgpt.com/', 1, '2025-04-14 20:14:36', '2025-04-14 20:14:36', 'Swords'),
(85, 1, 'Swords 3', 'https://chatgpt.com/', 1, '2025-04-14 20:14:36', '2025-04-14 20:14:36', 'Swords'),
(86, 1, 'Knife 1', 'https://Home.com/', 1, '2025-04-14 20:14:36', '2025-04-14 20:14:36', 'Knife'),
(87, 1, 'knife 2', 'https://chatgpt.com/', 1, '2025-04-14 20:14:36', '2025-04-14 20:14:36', 'Knife'),
(88, 1, 'Knife 3', 'https://chatgpt.com/', 1, '2025-04-14 20:14:36', '2025-04-14 20:14:36', 'Knife'),
(89, 1, 'Privacy Policy', 'https://chatgpt.com/', 1, '2025-04-14 20:14:36', '2025-04-14 20:14:36', 'Information'),
(90, 1, 'Return Policy', 'https://chatgpt.com/', 1, '2025-04-14 20:14:36', '2025-04-14 20:14:36', 'Information'),
(91, 1, 'Term & Condition', 'https://chatgpt.com/', 1, '2025-04-14 20:14:36', '2025-04-14 20:14:36', 'Information');

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE `sections` (
  `section_id` int(11) NOT NULL,
  `heading` varchar(255) DEFAULT NULL,
  `sub_heading` varchar(255) DEFAULT NULL,
  `content` varchar(1500) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sections`
--

INSERT INTO `sections` (`section_id`, `heading`, `sub_heading`, `content`, `image`) VALUES
(1, '<h1>Discover the Finest Collection of Swords</h1>', '<h2>Handcrafted Swords for Every One</h2>', '<p>Our collection features a variety of handcrafted swords, each designed with precision and skill. From historical replicas to modern designs, we offer swords that blend tradition with innovation. Each blade is crafted with the finest materials to ensure both durability and aesthetic appeal. Whether you\'re a seasoned collector materials to ensure both durability materials to ensure both durability and aesthetic appeal materials to ensure both durability and aesthetic appeal</p>', '1744649499_section_image.webp');

-- --------------------------------------------------------

--
-- Table structure for table `site_assets`
--

CREATE TABLE `site_assets` (
  `id` int(11) NOT NULL,
  `favicon_icon` varchar(255) DEFAULT NULL,
  `desktop_logo` varchar(255) DEFAULT NULL,
  `mobile_logo` varchar(255) DEFAULT NULL,
  `og_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `site_assets`
--

INSERT INTO `site_assets` (`id`, `favicon_icon`, `desktop_logo`, `mobile_logo`, `og_image`, `created_at`, `updated_at`) VALUES
(2, 'Assets/uploads/favicon/67fd2b1f85d93.jpg', 'Assets/uploads/desktop_logo/67fd2b08ef17a.jpg', 'Assets/uploads/mobile_logo/67fd2b703174d.png', 'Assets/uploads/og_image/67fd2b6801a9b.png', '2025-04-08 11:08:39', '2025-04-14 15:36:16');

-- --------------------------------------------------------

--
-- Table structure for table `site_settings`
--

CREATE TABLE `site_settings` (
  `id` int(11) NOT NULL,
  `sitemap_url` varchar(255) DEFAULT NULL,
  `robots_txt` text DEFAULT NULL,
  `header_text` text DEFAULT NULL,
  `body_text` text DEFAULT NULL,
  `social_link_1` varchar(255) DEFAULT NULL,
  `social_link_2` varchar(255) DEFAULT NULL,
  `social_link_3` varchar(255) DEFAULT NULL,
  `social_link_4` varchar(255) DEFAULT NULL,
  `social_link_5` varchar(255) DEFAULT NULL,
  `social_link_6` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `site_settings`
--

INSERT INTO `site_settings` (`id`, `sitemap_url`, `robots_txt`, `header_text`, `body_text`, `social_link_1`, `social_link_2`, `social_link_3`, `social_link_4`, `social_link_5`, `social_link_6`, `created_at`, `updated_at`) VALUES
(1, 'https://www.sitemap.com', 'Robots text is here', 'Headers text', 'I am body text i am coming from your Dashboard you can change me from your dashboard as well.', 'https://www.youtube.com/', 'https://www.pintrest.com/', 'https://www.facebook.com', '', '', '', '2025-04-08 09:56:41', '2025-04-11 15:20:53');

-- --------------------------------------------------------

--
-- Table structure for table `slides`
--

CREATE TABLE `slides` (
  `id` int(11) NOT NULL,
  `heading` varchar(255) DEFAULT NULL,
  `paragraph` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `slides`
--

INSERT INTO `slides` (`id`, `heading`, `paragraph`, `image`, `created_at`) VALUES
(1, '<h1><strong>Mastercrafted Blades</strong></h1>', '<p>Our collection showcases mastercrafted swords inspired by historical legends and timeless traditions. Each blade is forged by expert artisans, blending strength, beauty, and balance to create weapons worthy of warriors. Whether you\'re a collector, enthusiast, or martial artist, these swords reflect a true passion for craftsmanship.</p>', '1744646174_slide_1.jpg', '2025-04-14 15:56:14'),
(2, '<h1><strong>A Legacy of Honor</strong></h1>', '<p>Every sword in our collection carries a story — from the battlefields of ancient empires to the ceremonial halls of royalty. We honor the warriors and traditions of the past by preserving their legacy in every detail, material, and design element. These are more than blades; they are a testament to honor, courage, and history.</p>', '1744646216_slide_2.jpg', '2025-04-14 15:56:56'),
(3, '<h1><strong>Custom Sword Creations</strong></h1>', '<p>Looking for a sword that’s truly one of a kind? Our custom sword service lets you bring your vision to life. From blade shape to hilt material, engravings to balance — every element is tailored to your style and purpose. Whether for display, collection, or combat training, we craft swords that are as personal as they are powerful.</p>', '1744646245_slide_3.jpg', '2025-04-14 15:57:25');

-- --------------------------------------------------------

--
-- Table structure for table `top_menu`
--

CREATE TABLE `top_menu` (
  `top_menu_id` int(11) NOT NULL,
  `menu_name` varchar(255) DEFAULT NULL,
  `menu_link` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `top_menu`
--

INSERT INTO `top_menu` (`top_menu_id`, `menu_name`, `menu_link`) VALUES
(1, 'Refund Policy', '../refundpolicy'),
(2, 'Delivery Policy', './deleverypolicy'),
(3, 'Privacy Policy', './privacypolicy'),
(4, 'Terms & Conditions', './termcondition');

-- --------------------------------------------------------

--
-- Table structure for table `website_menus`
--

CREATE TABLE `website_menus` (
  `id` int(11) NOT NULL,
  `menu_name` varchar(255) NOT NULL,
  `menu_link` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `website_menus`
--

INSERT INTO `website_menus` (`id`, `menu_name`, `menu_link`) VALUES
(2, 'Home', '../Home.php'),
(10, 'Contact', 'contact.php'),
(15, 'About', '../About.php'),
(16, 'Cart', '../carts.php');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attributes`
--
ALTER TABLE `attributes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `attribute_term`
--
ALTER TABLE `attribute_term`
  ADD PRIMARY KEY (`attribute_term_id`);

--
-- Indexes for table `attribute_values`
--
ALTER TABLE `attribute_values`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attribute_id` (`attribute_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `contact_information`
--
ALTER TABLE `contact_information`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `credentials`
--
ALTER TABLE `credentials`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `footer`
--
ALTER TABLE `footer`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `footer_menu`
--
ALTER TABLE `footer_menu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `footer_id` (`footer_id`);

--
-- Indexes for table `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`section_id`);

--
-- Indexes for table `site_assets`
--
ALTER TABLE `site_assets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `slides`
--
ALTER TABLE `slides`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `top_menu`
--
ALTER TABLE `top_menu`
  ADD PRIMARY KEY (`top_menu_id`);

--
-- Indexes for table `website_menus`
--
ALTER TABLE `website_menus`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attributes`
--
ALTER TABLE `attributes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `attribute_term`
--
ALTER TABLE `attribute_term`
  MODIFY `attribute_term_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `attribute_values`
--
ALTER TABLE `attribute_values`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `contact_information`
--
ALTER TABLE `contact_information`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `credentials`
--
ALTER TABLE `credentials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `footer`
--
ALTER TABLE `footer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `footer_menu`
--
ALTER TABLE `footer_menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `section_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `site_assets`
--
ALTER TABLE `site_assets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `site_settings`
--
ALTER TABLE `site_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `slides`
--
ALTER TABLE `slides`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `top_menu`
--
ALTER TABLE `top_menu`
  MODIFY `top_menu_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `website_menus`
--
ALTER TABLE `website_menus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attribute_values`
--
ALTER TABLE `attribute_values`
  ADD CONSTRAINT `attribute_values_ibfk_1` FOREIGN KEY (`attribute_id`) REFERENCES `attributes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `footer_menu`
--
ALTER TABLE `footer_menu`
  ADD CONSTRAINT `footer_menu_ibfk_1` FOREIGN KEY (`footer_id`) REFERENCES `footer` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
