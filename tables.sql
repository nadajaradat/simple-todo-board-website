-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Aug 16, 2023 at 11:47 AM
-- Server version: 8.0.31
-- PHP Version: 8.0.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hello`
--

-- --------------------------------------------------------

--
-- Table structure for table `kaban_board`
--

DROP TABLE IF EXISTS `kaban_board`;
CREATE TABLE IF NOT EXISTS `kaban_board` (
  `id` int NOT NULL AUTO_INCREMENT,
  `task` varchar(65) DEFAULT NULL,
  `type` varchar(45) DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `edit_id` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=182 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `kaban_board`
--

INSERT INTO `kaban_board` (`id`, `task`, `type`, `user_id`, `edit_id`) VALUES
(181, 'hi hala you can do every thing you want', 'completed', 10, 6);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `admin_id` int NOT NULL,
  `isAdmin` varchar(3) NOT NULL DEFAULT 'NO',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `admin_id`, `isAdmin`) VALUES
(3, 'Nada Jaradat', 'nada@gmail.com', '123', 1, 'NO'),
(2, 'muna', 'nada2@gmail.com', '123', 1, 'NO'),
(1, 'admin', 'admin@gmail.com', '123', 1, 'YES'),
(6, 'sajeda', 'sajeda@gmail.com', '123', 6, 'YES'),
(10, 'hala', 'hala@gmail.com', '123', 6, 'NO');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
