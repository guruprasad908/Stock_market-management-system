-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 09, 2024 at 11:43 PM
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
-- Database: `stock_market`
--

-- --------------------------------------------------------

--
-- Table structure for table `stocks`
--

CREATE TABLE `stocks` (
  `id` int(11) NOT NULL,
  `symbol` varchar(10) NOT NULL,
  `name` varchar(100) NOT NULL,
  `current_price` decimal(10,2) NOT NULL,
  `future_prediction` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stocks`
--

INSERT INTO `stocks` (`id`, `symbol`, `name`, `current_price`, `future_prediction`) VALUES
(1, 'RELIANCE', 'Reliance Industries Limited', 2500.00, '0.00'),
(2, 'INFY', 'Infosys Limited', 1800.00, '0.00'),
(3, 'HDFCBANK', 'HDFC Bank Limited', 1400.00, '0.00'),
(4, 'TCS', 'Tata Consultancy Services Limited', 3200.00, '0.00'),
(5, 'HUL', 'Hindustan Unilever Limited', 2400.00, '0.00'),
(6, 'ICICIBANK', 'ICICI Bank Limited', 600.00, '0.00'),
(7, 'WIPRO', 'Wipro Limited', 500.00, '0.00'),
(8, 'ADANIGREEN', 'Adani Green Energy Limited', 150.00, '0.00'),
(9, 'BAJFINANCE', 'Bajaj Finance Limited', 2500.00, '0.00'),
(10, 'AXISBANK', 'Axis Bank Limited', 800.00, '0.00');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `stock_id` int(11) NOT NULL,
  `type` enum('buy','sell') NOT NULL,
  `quantity` int(11) NOT NULL,
  `price_per_unit` decimal(10,2) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `user_id`, `stock_id`, `type`, `quantity`, `price_per_unit`, `amount`, `date`) VALUES
(1, 1, 2, 'sell', 100, 1800.00, 180000.00, '2024-07-09 19:29:07'),
(2, 1, 1, 'buy', 200, 2500.00, 500000.00, '2024-07-09 19:34:17'),
(3, 1, 3, 'buy', 200, 1400.00, 280000.00, '2024-07-09 19:35:18'),
(4, 1, 7, 'buy', 20000, 500.00, 10000000.00, '2024-07-09 19:35:34'),
(5, 1, 4, 'sell', 200, 3200.00, 640000.00, '2024-07-09 21:12:10');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `balance` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `balance`) VALUES
(1, 'nandish', '$2y$10$zHXD7G2rpvxyhjwiBTj2a.v/1ssl.PIS9RV8Tb.0JNUTV1hFUzpou', 36820000.00),
(2, 'guruprasad', '$2y$10$obE5rn3V9i0r8lOc.y8WD.Knue7pTckn2w7uJoSQRHdY7zphuQE6a', 0.00),
(3, 'sankirna', '$2y$10$ttBm/Z1vjB/4HIO3rVI1Huu5Nh1RcV26KS23hUWdq2XN7RajCPAAG', 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `user_stocks`
--

CREATE TABLE `user_stocks` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `stock_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `stock_symbol` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_stocks`
--

INSERT INTO `user_stocks` (`id`, `user_id`, `stock_id`, `quantity`, `stock_symbol`) VALUES
(5, 1, 4, 400, ''),
(6, 1, 4, 400, ''),
(7, 1, 4, 400, ''),
(8, 1, 4, 400, ''),
(9, 1, 4, 400, ''),
(11, 1, 2, 1800, 'INFY'),
(12, 1, 5, 2000, 'HUL'),
(14, 1, 3, 200, 'HDFCBANK'),
(15, 1, 7, 20000, 'WIPRO');

-- --------------------------------------------------------

--
-- Table structure for table `watchlist`
--

CREATE TABLE `watchlist` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `stock_id` int(11) NOT NULL,
  `stock_symbol` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `stocks`
--
ALTER TABLE `stocks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `symbol` (`symbol`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `user_stocks`
--
ALTER TABLE `user_stocks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `stock_id` (`stock_id`);

--
-- Indexes for table `watchlist`
--
ALTER TABLE `watchlist`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `stock_id` (`stock_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `stocks`
--
ALTER TABLE `stocks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_stocks`
--
ALTER TABLE `user_stocks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `watchlist`
--
ALTER TABLE `watchlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `user_stocks`
--
ALTER TABLE `user_stocks`
  ADD CONSTRAINT `user_stocks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `user_stocks_ibfk_2` FOREIGN KEY (`stock_id`) REFERENCES `stocks` (`id`);

--
-- Constraints for table `watchlist`
--
ALTER TABLE `watchlist`
  ADD CONSTRAINT `watchlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `watchlist_ibfk_2` FOREIGN KEY (`stock_id`) REFERENCES `stocks` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
