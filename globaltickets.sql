-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Jun 18, 2024 at 05:38 AM
-- Server version: 5.7.39
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `globaltickets`
--

-- --------------------------------------------------------

--
-- Table structure for table `shortened_urls`
--

CREATE TABLE `shortened_urls` (
  `id` int(11) NOT NULL,
  `description` text,
  `user_id` int(11) NOT NULL,
  `full_url` varchar(2048) NOT NULL,
  `shortened_url` varchar(255) NOT NULL,
  `comments` text,
  `status` varchar(255) DEFAULT NULL,
  `created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `shortened_urls`
--

INSERT INTO `shortened_urls` (`id`, `description`, `user_id`, `full_url`, `shortened_url`, `comments`, `status`, `created`, `modified`) VALUES
(16, 'Google Homepage', 22, 'https://www.google.com', 'https://goo.gl/abc123', 'Most popular search engine in the universe.', 'active', '2024-06-18 02:09:51', '2024-06-18 02:31:09'),
(17, 'GitHub Homepage', 19, 'https://www.github.com', 'https://bit.ly/gh123', 'Code hosting platform', 'active', '2024-06-18 02:35:27', '2024-06-18 02:35:27'),
(18, 'Stack Overflow', 21, 'https://stackoverflow.com', 'https://tinyurl.com/so123', 'Programming Q&A', 'active', '2024-06-18 02:37:39', '2024-06-18 02:37:39');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `state` varchar(255) DEFAULT NULL,
  `user_name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `middle_name`, `last_name`, `email`, `state`, `user_name`, `password`, `created`, `modified`) VALUES
(18, 'Test First Name', 'Test M Name', 'Test L Name', 'test@globaltickets.nl', 'active', 'admin', '$2y$10$0oSrIF0YfdXnAKfCyeJP8uujqb1ANyKYeuCcrL7.QT0LErQkjUqje', '2024-06-18 04:19:08', '2024-06-18 04:19:08'),
(19, 'Stan', 'K', 'Swamy', 'stan.swamy@example.com', 'active', 'stanswamy', '$2y$10$POBcFGr8LjPXFoPKs8KUkuCSHZ8IUuRf07SVVlERnpSwz3gNYnX6C', '2024-06-18 04:31:25', '2024-06-18 04:31:25'),
(20, 'John', 'Kumar', 'Doe', 'john.k.doe@example.com', 'active', 'johnkdoe', '$2y$10$rKwlUP8yOw5oz9RhZEck.uMcex6FdS6yWsFMS6fzS38CZgRLoGzxu', '2024-06-18 04:43:03', '2024-06-18 04:43:03'),
(21, 'Sean', 'Bhai', 'Donnely', 'sean.k.donnely@example.com', 'active', 'seanbdonnely', '$2y$10$dlmimPMoRUSth9bcChChK.aQXyPRXJmxC93Qy9HArQvJiu9xyi5HC', '2024-06-18 04:44:26', '2024-06-18 04:44:26'),
(22, 'David', 'Lal', 'Gupta', 'david.lal.gupta@example.com', 'active', 'davidlalgupta', '$2y$10$UhGAg48..PgkkLFEShS4qukKFzf.Jqu/jyPTOBhf6KSkMMsk6xvlW', '2024-06-18 04:45:49', '2024-06-18 04:45:49');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `shortened_urls`
--
ALTER TABLE `shortened_urls`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `shortened_urls`
--
ALTER TABLE `shortened_urls`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `shortened_urls`
--
ALTER TABLE `shortened_urls`
  ADD CONSTRAINT `shortened_urls_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
