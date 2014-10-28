-- phpMyAdmin SQL Dump
-- version 4.1.12
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 28, 2014 at 09:22 AM
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
-- Table structure for table `guestbook`
--
-- Creation: May 08, 2014 at 07:23 AM
--

CREATE TABLE IF NOT EXISTS `guestbook` (
  `id_entry` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(30) NOT NULL,
  `lastname` varchar(30) NOT NULL,
  `email` varchar(60) NOT NULL,
  `content` text NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_entry`),
  KEY `created` (`created`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

--
-- Dumping data for table `guestbook`
--

INSERT INTO `guestbook` (`id_entry`, `firstname`, `lastname`, `email`, `content`, `created`) VALUES
(2, 'Test2', 'Test2', 'test2@example.com', 'Testbeitrag 2', '2014-09-11 11:19:54'),
(3, 'Test3', 'Test3', 'test3@example.com', 'Testbeitrag 3', '2014-09-11 11:19:55'),
(4, 'Test4', 'Test4', 'test4@example.com', 'Testbeitrag 4', '2014-09-11 11:19:56'),
(5, 'Test5', 'Test5', 'test5@example.com', 'Testbeitrag 5', '2014-09-11 11:19:57'),
(7, 'Test7', 'Test7', 'test7@example.com', 'Testbeitrag 6', '2014-09-11 11:19:59'),
(8, 'Test8', 'Test8', 'test8@example.com', 'Testbeitrag 7', '2014-09-11 11:20:00'),
(11, 'Ulrich', 'Lübke', 'asd@asd.de', 'dIch bade gerne in Milch und spiele Müsli.asd', '2014-09-11 11:20:02');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
