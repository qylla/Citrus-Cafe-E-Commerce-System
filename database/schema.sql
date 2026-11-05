-- Structure and menu only. No customer accounts or orders.

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `receipt_no` varchar(20) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `items` text NOT NULL,
  `order_type` varchar(20) NOT NULL,
  `table_number` varchar(20) DEFAULT NULL,
  `payment_method` varchar(50) NOT NULL,
  `payment_status` varchar(20) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `order_status` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `email` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_status` varchar(50) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock_quantity` int(11) NOT NULL,
  `status` varchar(20) NOT NULL,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `birthday` date DEFAULT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `products` (`product_id`, `name`, `description`, `price`, `stock_quantity`, `status`, `image`) VALUES
(1, 'Mixed Yakult Juice', 'Orange mixed with yakult', 12.90, 15, 'Available', 'air2.jpg'),
(2, 'Sparkling Orange Juice', 'Updated desc', 13.90, 12, 'Available', 'air1.jpg'),
(3, 'Fresh Orange Juice', '100% natural freshly squeezed juice', 10.00, 20, 'Available', 'air3.jpg'),
(4, 'Tiramisu OG Soft Cookies ', 'Rich tiramisu flavour with soft texture', 12.50, 10, 'Available', 'cookiesTiramisu.jpg'),
(5, 'Chocolate Chip Soft Cookies ', 'Classic chocolate chip with soft center', 12.50, 19, 'Available', 'cookiesChocolatechip.jpg'),
(6, 'Red Velvet Soft Cookies', 'Soft baked cookies with creamy filling', 12.50, 10, 'Available', 'cookiesRedvelvet.jpg'),
(7, 'Chicken Cordon Bleu', 'Crispy chicken stuffed withh ham and cheese', 22.90, 11, 'Available', 'GordonBleu.png'),
(8, 'Fish and Chips', 'Golden fried fish served with fries', 17.90, 10, 'Available', 'FishNchip.png'),
(9, 'Beef Lasagna', 'Classic baked lasagna with rich beef sauce', 16.90, 15, 'Available', 'BeefChicLasagna.png');

ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`);

ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`);

ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE;
