-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 05, 2025 at 11:44 PM
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
-- Database: `si_wallet_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `p2p_transactions`
--

CREATE TABLE `p2p_transactions` (
  `p2p_id` int(11) NOT NULL,
  `transaction_id` int(11) NOT NULL,
  `to_wallet_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `p2p_transactions`
--

INSERT INTO `p2p_transactions` (`p2p_id`, `transaction_id`, `to_wallet_id`) VALUES
(4, 10, 2),
(5, 13, 2),
(6, 14, 2);

-- --------------------------------------------------------

--
-- Table structure for table `scheduled_transactions`
--

CREATE TABLE `scheduled_transactions` (
  `schedule_id` int(11) NOT NULL,
  `transaction_id` int(11) NOT NULL,
  `execute_date` datetime NOT NULL,
  `completed` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `scheduled_transactions`
--

INSERT INTO `scheduled_transactions` (`schedule_id`, `transaction_id`, `execute_date`, `completed`) VALUES
(2, 14, '2025-03-06 00:00:00', 0);

-- --------------------------------------------------------

--
-- Table structure for table `system_logs`
--

CREATE TABLE `system_logs` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE `tickets` (
  `ticket_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `status` enum('open','in_progress','resolved') DEFAULT 'open',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tickets`
--

INSERT INTO `tickets` (`ticket_id`, `user_id`, `subject`, `message`, `status`, `created_at`) VALUES
(1, 4, 'test', 'testtest', 'resolved', '2025-03-05 19:31:58'),
(2, 4, 'testing again', 'test test test', 'in_progress', '2025-03-05 20:09:39'),
(3, 4, 'still testing', 'testing testing testing testinhg', 'resolved', '2025-03-05 20:19:55');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `transaction_id` int(11) NOT NULL,
  `wallet_id` int(11) NOT NULL,
  `note` varchar(100) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `transaction_type` enum('deposit','withdraw','p2p') NOT NULL,
  `status` enum('pending','completed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`transaction_id`, `wallet_id`, `note`, `amount`, `transaction_type`, `status`, `created_at`) VALUES
(1, 2, 'testing', 60.00, 'deposit', 'completed', '2025-03-03 15:26:04'),
(2, 2, 'testing', 60.00, 'deposit', 'completed', '2025-03-03 15:26:09'),
(3, 2, 'testing', 60.00, 'p2p', 'completed', '2025-03-03 15:27:38'),
(4, 2, 'testing', 60.00, 'p2p', 'completed', '2025-03-03 15:27:49'),
(5, 2, 'testing', 60.00, 'deposit', 'completed', '2025-03-03 16:57:39'),
(6, 2, 'testing', 60.00, 'p2p', 'completed', '2025-03-03 16:57:47'),
(7, 10, 'dsad', 40.00, 'deposit', 'completed', '2025-03-05 01:36:28'),
(8, 10, 'fsdf', 30.00, 'withdraw', 'completed', '2025-03-05 01:37:09'),
(9, 10, 'fdsdf', 3.00, 'withdraw', 'completed', '2025-03-05 01:37:46'),
(10, 10, 'testing', 6.00, 'p2p', 'completed', '2025-03-05 07:33:10'),
(11, 10, 'test', 30.00, 'deposit', 'completed', '2025-03-05 07:33:28'),
(12, 10, 'test', 40.00, 'deposit', 'completed', '2025-03-05 10:07:26'),
(13, 10, 'test schedule', 30.00, 'p2p', 'pending', '2025-03-05 13:40:27'),
(14, 10, '', 40.00, 'p2p', 'pending', '2025-03-05 13:50:38');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `email_token` varchar(50) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `tier` int(11) NOT NULL DEFAULT 1,
  `max_transaction_amount` decimal(10,2) DEFAULT 50.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `username`, `email`, `email_token`, `password_hash`, `tier`, `max_transaction_amount`, `created_at`) VALUES
(1, 'test', 'user', 'testuser', 'testuser@gmail.com', NULL, '0', 1, 50.00, '2025-03-01 23:51:48'),
(2, 'test1', 'user', 'test1user', 'corp.msam@gmail.com', NULL, '$2y$10$60GENL1WklUst1dUHuOLL.Epf8elrvqmC7jtzXjRbNUmmCRbP2Rx2', 2, 50.00, '2025-03-01 23:54:54'),
(3, 'slei', 'yah', 'sleiyah', 'shy300602@gmail.com', NULL, '$2y$10$gvhggZVfT99T9a26AQJZVe5skopvaeVZHWqU7UXB0zvAtbfc7cBgW', 2, 50.00, '2025-03-02 15:35:54'),
(4, 'sleiman', 'el yahfoufi', 'yahslei', 'sleiyah02@gmail.com', NULL, '$2y$10$prdloiSYdW2sawvztrWYF.A/jVc22PHmZeu3wgp/l4o5tlXZmLlB6', 3, 50.00, '2025-03-02 19:00:25'),
(5, 'Ali', 'Abdo', 'AliAbdo', 'AliAbdo@gmail.com', NULL, '$2y$10$FbWvS..TS9iSTvC7de0qy.DOEIRKu5kQWExHIiIulmPlITjsP9j16', 1, 50.00, '2025-03-05 06:31:34'),
(7, 'admin', 'admin', 'admin', 'admin@admin.admin', NULL, '$2y$10$.agj.Ox5e.6ncsHmuQjkXOnaannEKornyio37RCqtQ3bnvjBET5r.', 0, 50.00, '2025-03-05 15:14:51');

-- --------------------------------------------------------

--
-- Table structure for table `verifications`
--

CREATE TABLE `verifications` (
  `verification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `passport_image` text DEFAULT NULL,
  `selfie_image` text DEFAULT NULL,
  `verified` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wallets`
--

CREATE TABLE `wallets` (
  `wallet_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `card_number` varchar(19) NOT NULL,
  `card_type` enum('Visa','Mastercard','Amex','Discover') DEFAULT NULL,
  `cvv` varchar(3) NOT NULL,
  `expiry_date` varchar(5) NOT NULL,
  `balance` decimal(10,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wallets`
--

INSERT INTO `wallets` (`wallet_id`, `user_id`, `card_number`, `card_type`, `cvv`, `expiry_date`, `balance`, `created_at`) VALUES
(2, 2, '2449299927384464', 'Visa', '818', '04/25', 6.00, '2025-03-03 15:21:54'),
(10, 4, '1231231231231231', 'Visa', '121', '12/43', 31.00, '2025-03-05 01:12:06');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `p2p_transactions`
--
ALTER TABLE `p2p_transactions`
  ADD PRIMARY KEY (`p2p_id`),
  ADD KEY `transaction_id` (`transaction_id`),
  ADD KEY `p2p_transactions_to_wallet` (`to_wallet_id`);

--
-- Indexes for table `scheduled_transactions`
--
ALTER TABLE `scheduled_transactions`
  ADD PRIMARY KEY (`schedule_id`),
  ADD KEY `transaction_id` (`transaction_id`);

--
-- Indexes for table `system_logs`
--
ALTER TABLE `system_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`ticket_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `transactions_wallet_fk` (`wallet_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `email_tkn` (`email_token`);

--
-- Indexes for table `verifications`
--
ALTER TABLE `verifications`
  ADD PRIMARY KEY (`verification_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `wallets`
--
ALTER TABLE `wallets`
  ADD PRIMARY KEY (`wallet_id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `p2p_transactions`
--
ALTER TABLE `p2p_transactions`
  MODIFY `p2p_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `scheduled_transactions`
--
ALTER TABLE `scheduled_transactions`
  MODIFY `schedule_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `system_logs`
--
ALTER TABLE `system_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `ticket_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `verifications`
--
ALTER TABLE `verifications`
  MODIFY `verification_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wallets`
--
ALTER TABLE `wallets`
  MODIFY `wallet_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `p2p_transactions`
--
ALTER TABLE `p2p_transactions`
  ADD CONSTRAINT `p2p_transactions_ibfk_1` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`transaction_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `p2p_transactions_to_wallet` FOREIGN KEY (`to_wallet_id`) REFERENCES `wallets` (`wallet_id`) ON DELETE CASCADE;

--
-- Constraints for table `scheduled_transactions`
--
ALTER TABLE `scheduled_transactions`
  ADD CONSTRAINT `scheduled_transactions_ibfk_1` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`transaction_id`) ON DELETE CASCADE;

--
-- Constraints for table `system_logs`
--
ALTER TABLE `system_logs`
  ADD CONSTRAINT `system_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_wallet_fk` FOREIGN KEY (`wallet_id`) REFERENCES `wallets` (`wallet_id`) ON DELETE CASCADE;

--
-- Constraints for table `verifications`
--
ALTER TABLE `verifications`
  ADD CONSTRAINT `verifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `wallets`
--
ALTER TABLE `wallets`
  ADD CONSTRAINT `wallets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
