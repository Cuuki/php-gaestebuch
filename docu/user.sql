-- phpMyAdmin SQL Dump
-- version 4.1.12
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 30, 2015 at 03:30 PM
-- Server version: 5.6.16
-- PHP Version: 5.5.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `gaestebuch`
--

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL,
  `useremail` varchar(50) NOT NULL,
  `password` char(128) NOT NULL,
  `role` enum('Administrator','Editor') NOT NULL DEFAULT 'Editor',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `useremail` (`useremail`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `useremail`, `password`, `role`) VALUES
(13, 'sudo', 'sudo@example.de', '$2y$10$/IC3bxENuxuMB8vYDcn5o.grK5PcLQs4z79FGNE7N7kNSSeB396Sa', 'Administrator'),
(16, 'adm', 'adm@example.de', '$2y$10$BLLibsVJ/UxYSHLOwov87e2ZNDflgBOeAes5OnPx2GFSYDq3OHHVq', 'Editor'),
(17, 'Patrick', 'patrick.soellner@publicis.de', '$2y$10$N6F4zNGUbPJnBwfGC6cuiOtCH9EgICba93hyYwO1ppKJtZRyL5mcW', 'Editor');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
