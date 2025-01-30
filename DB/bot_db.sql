-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 12, 2024 at 06:21 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bot_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `faqs`
--

CREATE TABLE `faqs` (
  `id` int(11) NOT NULL,
  `question` varchar(255) DEFAULT NULL,
  `answer` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faqs`
--

INSERT INTO `faqs` (`id`, `question`, `answer`) VALUES
(1, 'What are your shipping options?', 'We offer standard and express shipping options. Standard shipping usually takes 5-7 business days, while express shipping takes 2-3 business days.'),
(2, 'How can I return an item?', 'To return an item, please contact our customer support at 380 (112)-3489 or email us at support@example.com. We will guide you through the return process.'),
(3, 'Do you offer international shipping?', 'Yes, we offer international shipping to most countries. Shipping costs and delivery times may vary depending on the destination.'),
(4, 'What payment methods do you accept?', 'We accept major credit cards, PayPal, and bank transfers as payment methods.'),
(5, 'How can I track my order?', 'Once your order is shipped, you will receive a tracking number via email. You can use this tracking number to track your order on our website.'),
(6, 'Are your products environmentally friendly?', 'Yes, we are committed to sustainability and eco-friendly practices. Our products are made from recycled materials and we strive to minimize our environmental impact.'),
(7, 'Do you offer discounts for bulk orders?', 'Yes, we offer discounts for bulk orders. Please contact our sales team for more information.'),
(8, 'Can I modify or cancel my order after it\'s placed?', 'Once an order is placed, it enters processing immediately. However, you may contact us to request modifications or cancellations, and we\'ll do our best to accommodate your request.');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `rating` varchar(10) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `feedback` text NOT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `rating`, `customer_name`, `email`, `feedback`, `submitted_at`) VALUES
(7, '4', 'test', 'test@gmail.com', 'vzdv', '2024-03-09 07:19:07'),
(8, 'Bad', 'test', 'root@gmail.com', 'szc', '2024-03-09 07:19:42'),
(9, 'Very Good', 'test', 'ajink6169@gmail.com', 'azff', '2024-03-09 07:20:44'),
(10, 'Good', 'sdg', 'sgd@e.com', 'afsfasfca', '2024-03-09 07:22:40'),
(11, 'Average', 'test', 'dxcfgvhb@gmail.com', 'affdaf', '2024-03-09 07:24:09'),
(12, 'Good', 'affs', 'afs@hh.com', 'efa', '2024-03-09 07:31:17'),
(13, 'Average', 'test', 'sdvfamily.18@gmail.com', 'sgd', '2024-03-09 07:42:29'),
(14, 'Average', 'sdg', 'angel@gmail.com', 'xbxf', '2024-03-09 07:43:00'),
(15, 'Good', 'test', 'root@gmail.com', 'sgdg', '2024-03-09 07:43:56');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `order_id` varchar(10) DEFAULT NULL,
  `product_name` varchar(100) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `shipping_address` varchar(255) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_id`, `product_name`, `quantity`, `total_price`, `customer_name`, `shipping_address`, `status`) VALUES
(1, 'FMCC001', 'Organic Apples', 2, 5.98, 'John Doe', '123 Farm Road, City, Country', 'Shipped'),
(2, 'FMCC002', 'Grass-fed Beef', 1, 9.99, 'Jane Smith', '456 Farm Lane, City, Country', 'Dispatched'),
(3, 'FMCC003', 'Free-range Eggs', 3, 10.47, 'Michael Johnson', '789 Farm Street, City, Country', 'Awaiting Payment'),
(4, 'FMCC004', 'Raw Honey', 2, 11.98, 'Emily Brown', '101 Farm Avenue, City, Country', 'Delivered'),
(5, 'FMCC005', 'Organic Kale', 1, 1.99, 'Sarah Wilson', '202 Farm Boulevard, City, Country', 'Refunded');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `category`, `image`) VALUES
(2, 'Organic Tomato & Pepper Fertilizer', 'Made from natural compost and enriched with essential nutrients. Promotes healthy growth and fruit development.', 29.99, 'Fertilizers', 'https://5.imimg.com/data5/SELLER/Default/2021/12/CF/TP/XA/25464873/organic-manure-500x500.jpg'),
(3, 'Fresh Apples', 'Juicy and crisp apples picked from the orchard. Perfect for snacking or baking.', 1.99, 'Fruits', 'https://images.unsplash.com/photo-1567306226416-28f0efdc88ce?q'),
(4, 'Organic Spinach', 'Tender and nutrient-rich spinach leaves, grown organically for maximum freshness.', 2.49, 'Vegetables', 'https://freshji.in/wp-content/uploads/2020/09/Spinach-1-600x600.jpg'),
(5, 'Grass-fed Beef', 'Premium quality beef from grass-fed cattle, known for its tenderness and rich flavor.', 12.99, 'Meat', 'https://images-cdn.ubuy.co.in/64faf82e6714e86a450f8938-marketside-organic-grass-fed-ground.jpg'),
(6, 'Organic Milk', 'Creamy and wholesome milk sourced from organically raised cows. Rich in calcium and nutrients.', 3.99, 'Dairy', 'https://www.strausfamilycreamery.com/wp-content/uploads/2023/06/category-milk2.png.webp'),
(7, 'Pure Ghee', 'Traditional clarified butter made from pure cow\'s milk. Ideal for cooking and adding flavor to dishes.', 9.99, 'Dairy', 'https://m.media-amazon.com/images/I/41diIEPUlnL._AC_UF1000,1000_QL80_.jpg'),
(8, 'Fresh Strawberries', 'Plump and sweet strawberries picked at peak ripeness. Perfect for desserts or snacking.', 3.49, 'Fruits', 'https://www.debon.co.in/cdn/shop/products/strawberries-shutterstock_198933395.jpg'),
(9, 'Natural Yogurt', 'Smooth and creamy yogurt made from fresh milk and active cultures. A healthy and delicious snack.', 2.99, 'Dairy', 'https://cdn-prod.medicalnewstoday.com/content/images/articles/323/323169/greek-yoghurt-in-bowl.jpg'),
(10, 'Free-range Eggs', 'Farm-fresh eggs from free-range chickens, with vibrant yolks and firm whites.', 4.99, 'Dairy', 'https://farmmadefoods.com/cdn/shop/products/12-eggs.png'),
(11, 'Organic Avocado', 'Buttery and nutritious avocados grown organically. Perfect for salads, sandwiches, or guacamole.', 1.79, 'Fruits', 'https://www.orgpick.com/cdn/shop/products/Organic_Avocado_3dfa8f81-f467-43a9-9f9e-cda5a12fac32.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `username` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `faqs`
--
ALTER TABLE `faqs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_id` (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `faqs`
--
ALTER TABLE `faqs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
