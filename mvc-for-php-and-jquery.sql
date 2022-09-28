-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 30, 2022 at 04:17 AM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 8.1.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mvc-for-php-and-jquery`
--

-- --------------------------------------------------------

--
-- Table structure for table `addresses`
--

CREATE TABLE `addresses` (
  `address_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `phone` varchar(45) DEFAULT NULL,
  `fax` varchar(45) DEFAULT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `address` varchar(255) NOT NULL,
  `address_two` varchar(255) DEFAULT NULL,
  `city` varchar(100) NOT NULL,
  `province` varchar(45) NOT NULL,
  `postal_code` varchar(16) NOT NULL,
  `country` varchar(64) NOT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `addresses`
--

INSERT INTO `addresses` (`address_id`, `name`, `contact_email`, `website`, `phone`, `fax`, `company_name`, `address`, `address_two`, `city`, `province`, `postal_code`, `country`, `notes`) VALUES
(1, 'Admin', 'admin@admin.com', '', '1-204-204-2044', '', 'Company Name', '555 Street', 'Suite 2', 'Winnipeg', 'MB', 'R3G 3G3', 'Canada', 'notesd'),
(2, 'Jarad Spinella', 'jspinella0@hao123.com', '156.184.191.216', '617-899-9891', '', 'Trilith', '60 Dawn Way', '', 'Toulouse', 'MB', 'R3G 3G3', 'Canada', 'Test notes');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `slug` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `title`, `description`, `slug`) VALUES
(1, 'Cameras', '&lt;p&gt;Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam placerat magna dignissim tortor varius, semper ultricies arcu semper. Praesent viverra gravida sapien quis suscipit. Integer sed nisl ultricies, hendrerit justo in, condimentum nulla. Maecenas ut egestas mauris, sed ornare tellus. Donec fringilla pharetra mauris viverra finibus. Cras pulvinar sapien ut consectetur mattis. Suspendisse egestas orci in ex facilisis egestas. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Sed tortor libero, luctus in eros ut, laoreet tincidunt erat. Sed at laoreet diam&lt;/p&gt;', 'cameras'),
(2, 'Lenses', '&lt;p&gt;Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam placerat magna dignissim tortor varius, semper ultricies arcu semper. Praesent viverra gravida sapien quis suscipit. Integer sed nisl ultricies, hendrerit justo in, condimentum nulla. Maecenas ut egestas mauris, sed ornare tellus. Donec fringilla pharetra mauris viverra finibus. Cras pulvinar sapien ut consectetur mattis. Suspendisse egestas orci in ex facilisis egestas. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Sed tortor libero, luctus in eros ut, laoreet tincidunt erat. Sed at laoreet diam.&lt;/p&gt;', 'lenses');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `sku` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `detailed_description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `sale_price` decimal(20,4) DEFAULT NULL,
  `purchase_price` decimal(20,4) DEFAULT NULL,
  `quantity` int(16) DEFAULT NULL,
  `bin` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `reorder` int(11) DEFAULT NULL,
  `type` varchar(45) NOT NULL,
  `enabled` bit(1) DEFAULT NULL,
  `slug` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `category_id`, `title`, `sku`, `description`, `detailed_description`, `image`, `sale_price`, `purchase_price`, `quantity`, `bin`, `notes`, `reorder`, `type`, `enabled`, `slug`) VALUES
(1, 1, 'Camera 1', 'sku1', 'Sample Camera Title 1', '&lt;p&gt;Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam placerat magna dignissim tortor varius, semper ultricies arcu semper. Praesent viverra gravida sapien quis suscipit. Integer sed nisl ultricies, hendrerit justo in, condimentum nulla. Maecenas ut egestas mauris, sed ornare tellus. Donec fringilla pharetra mauris viverra finibus. Cras pulvinar sapien ut consectetur mattis. Suspendisse egestas orci in ex facilisis egestas. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Sed tortor libero, luctus in eros ut, laoreet tincidunt erat. Sed at laoreet diam.&lt;/p&gt;\r\n&lt;p&gt;Aenean in lorem vel enim congue maximus et non purus. Praesent pulvinar nisl elit, et rhoncus libero ultrices sed. Sed egestas interdum ipsum sed cursus. Sed suscipit placerat nisl id convallis. Donec ullamcorper odio arcu, vel aliquet erat tincidunt et. Etiam tincidunt eros ac ultrices vulputate. Aliquam eget urna ut neque tincidunt facilisis non et enim. Praesent pretium dictum metus, non lacinia dolor. Nam at libero leo. Vestibulum non nisi eget odio consequat interdum ut et felis. Donec vel lobortis ligula. Integer eu euismod felis. Vestibulum at sapien pellentesque, tincidunt nibh vitae, commodo nibh. Phasellus finibus erat mauris, a volutpat ante vehicula id. Donec eleifend blandit nulla, a ultricies ex vehicula eu.&lt;/p&gt;\r\n&lt;p&gt;Sed blandit ligula in elementum varius. Ut mattis augue ut lobortis blandit. Ut accumsan, neque finibus finibus malesuada, nulla mi lobortis dui, sed ultrices felis arcu ac tortor. Ut facilisis convallis quam, eget ultricies nibh imperdiet at. Ut et neque eget orci tincidunt tempor. Curabitur tortor urna, convallis et libero vel, condimentum sollicitudin mauris. Fusce nec leo vel mauris malesuada bibendum sed id risus. Pellentesque ullamcorper sed diam vel ornare. Vivamus facilisis felis sit amet laoreet ultrices. Duis a arcu eget enim dignissim volutpat sed in quam. In sodales at felis vel rhoncus. Nunc ut quam vitae eros egestas mattis vel vel urna. Pellentesque maximus et lectus non venenatis. Nullam id lectus fermentum, sodales libero quis, egestas ligula. Proin non ornare est.&lt;/p&gt;\r\n&lt;p&gt;Aenean metus ex, pulvinar nec elit vel, malesuada volutpat libero. Pellentesque vitae quam in arcu interdum facilisis. Vivamus lacinia sit amet ipsum eget dictum. Sed tempor sagittis neque nec luctus. Fusce id accumsan magna. Vestibulum posuere erat ante, sed dapibus erat pretium eget. Aenean auctor leo in pharetra suscipit. Fusce pellentesque tempus quam, ac consectetur libero dictum vitae. Nunc placerat sem varius, elementum enim vel, ornare lacus. Vestibulum mauris elit, ornare et malesuada non, semper et justo. Curabitur ut elit ut nibh placerat luctus. Aliquam erat volutpat. Pellentesque eu mauris id mi bibendum egestas in in odio. Sed dui tortor, congue in ipsum vel, iaculis maximus magna.&lt;/p&gt;', '8626-1661819680.jpg', '499.9600', '0.0000', NULL, 'BIN2-1', 'This is a test of the product notes.', 4, 'Inventory', b'1', 'camera-1'),
(2, 2, 'Sample Lens', 'sku2', 'Sample Camera Lens', '&lt;p&gt;Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam placerat magna dignissim tortor varius, semper ultricies arcu semper. Praesent viverra gravida sapien quis suscipit. Integer sed nisl ultricies, hendrerit justo in, condimentum nulla. Maecenas ut egestas mauris, sed ornare tellus. Donec fringilla pharetra mauris viverra finibus. Cras pulvinar sapien ut consectetur mattis. Suspendisse egestas orci in ex facilisis egestas. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Sed tortor libero, luctus in eros ut, laoreet tincidunt erat. Sed at laoreet diam.&lt;/p&gt;\r\n&lt;p&gt;Aenean in lorem vel enim congue maximus et non purus. Praesent pulvinar nisl elit, et rhoncus libero ultrices sed. Sed egestas interdum ipsum sed cursus. Sed suscipit placerat nisl id convallis. Donec ullamcorper odio arcu, vel aliquet erat tincidunt et. Etiam tincidunt eros ac ultrices vulputate. Aliquam eget urna ut neque tincidunt facilisis non et enim. Praesent pretium dictum metus, non lacinia dolor. Nam at libero leo. Vestibulum non nisi eget odio consequat interdum ut et felis. Donec vel lobortis ligula. Integer eu euismod felis. Vestibulum at sapien pellentesque, tincidunt nibh vitae, commodo nibh. Phasellus finibus erat mauris, a volutpat ante vehicula id. Donec eleifend blandit nulla, a ultricies ex vehicula eu.&lt;/p&gt;\r\n&lt;p&gt;Sed blandit ligula in elementum varius. Ut mattis augue ut lobortis blandit. Ut accumsan, neque finibus finibus malesuada, nulla mi lobortis dui, sed ultrices felis arcu ac tortor. Ut facilisis convallis quam, eget ultricies nibh imperdiet at. Ut et neque eget orci tincidunt tempor. Curabitur tortor urna, convallis et libero vel, condimentum sollicitudin mauris. Fusce nec leo vel mauris malesuada bibendum sed id risus. Pellentesque ullamcorper sed diam vel ornare. Vivamus facilisis felis sit amet laoreet ultrices. Duis a arcu eget enim dignissim volutpat sed in quam. In sodales at felis vel rhoncus. Nunc ut quam vitae eros egestas mattis vel vel urna. Pellentesque maximus et lectus non venenatis. Nullam id lectus fermentum, sodales libero quis, egestas ligula. Proin non ornare est.&lt;/p&gt;\r\n&lt;p&gt;Aenean metus ex, pulvinar nec elit vel, malesuada volutpat libero. Pellentesque vitae quam in arcu interdum facilisis. Vivamus lacinia sit amet ipsum eget dictum. Sed tempor sagittis neque nec luctus. Fusce id accumsan magna. Vestibulum posuere erat ante, sed dapibus erat pretium eget. Aenean auctor leo in pharetra suscipit. Fusce pellentesque tempus quam, ac consectetur libero dictum vitae. Nunc placerat sem varius, elementum enim vel, ornare lacus. Vestibulum mauris elit, ornare et malesuada non, semper et justo. Curabitur ut elit ut nibh placerat luctus. Aliquam erat volutpat. Pellentesque eu mauris id mi bibendum egestas in in odio. Sed dui tortor, congue in ipsum vel, iaculis maximus magna.&lt;/p&gt;', '1659-1661819694.jpg', '599.0000', '0.0000', NULL, 'BIN2-1', '', 4, 'Kit', b'1', 'camera-2');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `setting_id` int(11) NOT NULL,
  `setting` varchar(32) NOT NULL,
  `value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`setting_id`, `setting`, `value`) VALUES
(1, 'dark_mode', 'dark'),
(2, 'page_title', 'Sample'),
(3, 'company_name', 'Sample Name'),
(4, 'copyright', '(C) 2022'),
(5, 'company_address1', '4321 Main St.'),
(6, 'company_address2', ''),
(7, 'company_city', 'Winnipeg'),
(8, 'company_prov', 'MB'),
(9, 'company_postalcode', 'R2Z 2X7'),
(10, 'company_country', 'Canada'),
(11, 'company_phone', '1 (204) 555-5555'),
(12, 'company_email', 'test@test-email.com'),
(14, 'homepage_body', 'Edit this text in the settings database table'),
(16, 'homepage_header', 'Edit this header in the setting database table'),
(17, 'price_decimals', '2'),
(18, 'homepage_title', 'Welcome!'),
(19, 'about_title', 'About Page'),
(20, 'about_body', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam facilisis, est eget gravida scelerisque, augue diam sodales eros, sit amet dapibus mi massa ut magna. Cras varius est quis nisl eleifend, in venenatis leo tempor. Phasellus id est in turpis bibendum dictum. Fusce eget tempor ante. Proin sit amet rhoncus nunc, malesuada ultricies nibh. Praesent a ultrices tellus. Aliquam feugiat tincidunt blandit.\n\nNullam molestie posuere lectus, quis elementum est placerat facilisis. Ut egestas ante at urna sagittis ultricies. Quisque id dolor blandit ipsum posuere varius quis nec metus. Nam at quam nisi. Vivamus finibus nisi vel quam ornare, consectetur pellentesque augue posuere. Ut facilisis velit eu dui consectetur commodo. Etiam dignissim, nunc vel sodales accumsan, mauris tortor lobortis eros, non ultricies est nunc sit amet quam.\n\nSed ex augue, aliquet sed porta eu, fringilla a tortor. Nunc porta eget augue in tempus. Sed faucibus nisl ac ligula aliquet fermentum. Aliquam euismod malesuada facilisis. Nam pellentesque consequat nibh eu iaculis. Morbi rhoncus ligula non dolor tincidunt, elementum mollis ex mattis. Vestibulum sit amet diam quis magna fermentum posuere ac accumsan elit. Proin gravida orci lacus, a porttitor massa volutpat quis. Fusce eu elementum erat.\n\nMaecenas ornare malesuada nibh et ornare. Curabitur id odio in nibh suscipit luctus. Morbi sed rhoncus metus. Cras fermentum orci risus, non posuere velit pretium nec. Curabitur tempus nisi eget commodo ultricies. Vivamus efficitur ullamcorper diam, sed euismod tortor ornare eget. Duis lacinia pellentesque sapien vel varius. Duis consequat nisi hendrerit vestibulum viverra. Donec gravida facilisis diam quis vestibulum. Maecenas porttitor eu neque id rhoncus. Phasellus fermentum velit eget libero pharetra tempor. Vestibulum et elementum nisl. Nunc porta dolor lorem, nec dignissim mi sollicitudin et.\n\nCras eget risus at diam elementum efficitur. In egestas vitae sapien sagittis faucibus. Praesent vel cursus urna, non fermentum augue. Ut mattis metus nulla, id malesuada sapien tincidunt vitae. Proin fringilla, velit et tincidunt consectetur, ex ex elementum ipsum, vel vehicula ligula urna vitae arcu. Vivamus luctus non dolor accumsan cursus. Aliquam dui eros, convallis ac sapien et, commodo hendrerit ligula. Nullam facilisis congue augue, non dapibus purus imperdiet quis. Phasellus elementum tellus non ligula venenatis posuere. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Donec porttitor urna non ligula elementum congue. Duis egestas, lorem ut sodales finibus, lorem ex venenatis neque, vel accumsan odio risus eget velit.'),
(21, 'currency', '$'),
(22, 'default_sort', 'title'),
(24, 'columns_products', 'sku,title,category,sale_price,purchase_price,quantity,reorder,bin,description,type,image'),
(25, 'columns_catalog', 'title,sale_price,sku,category'),
(26, 'columns_products_default', 'sku,title,category,sale_price,purchase_price,quantity,reorder,bin,description,type,image'),
(28, 'columns_users_default', 'email,type,name,phone,city,notes'),
(29, 'columns_users', 'email,type,name,phone,city,notes'),
(30, 'columns_categories', 'title,product_count,slug'),
(31, 'columns_categories_default', 'title,product_count,slug'),
(33, 'columns_adjustments', 'date,reference_number,sku,title,quantity,notes'),
(34, 'columns_adjustments_default', 'date,reference_number,sku,title,quantity,notes'),
(35, 'guest_catalog_access', 'True'),
(37, 'guests_can_register', 'True');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `address_id` int(11) DEFAULT NULL,
  `type` varchar(8) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`address_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD UNIQUE KEY `title` (`title`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `fk_Products_Categories` (`category_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`setting_id`),
  ADD UNIQUE KEY `setting` (`setting`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `fk_Users_Addresses` (`address_id`) USING BTREE;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `addresses`
--
ALTER TABLE `addresses`
  MODIFY `address_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `setting_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
