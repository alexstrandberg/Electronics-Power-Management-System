-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb2+deb7u1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 17, 2015 at 09:48 AM
-- Server version: 5.5.44
-- PHP Version: 5.4.41-0+deb7u1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `power_manager`
--
CREATE DATABASE `power_manager` DEFAULT CHARACTER SET latin1 COLLATE latin1_general_ci;
USE `power_manager`;

-- --------------------------------------------------------

--
-- Table structure for table `appliances`
--

CREATE TABLE IF NOT EXISTS `appliances` (
  `id` int(11) NOT NULL,
  `name` varchar(30) COLLATE latin1_general_ci NOT NULL,
  `state` tinyint(1) NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Dumping data for table `appliances`
--

INSERT INTO `appliances` (`id`, `name`, `state`, `enabled`) VALUES
(0, 'Power Source', 0, 1),
(1, 'Appliance 1', 0, 1),
(2, 'Appliance 2', 0, 1),
(3, 'Appliance 3', 0, 1),
(4, 'Appliance 4', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE IF NOT EXISTS `login_attempts` (
  `user_id` int(11) NOT NULL,
  `when` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip` int(10) unsigned DEFAULT NULL,
  `user_agent` varchar(512) COLLATE latin1_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `password` char(128) COLLATE latin1_general_ci NOT NULL,
  `salt` char(128) COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `salt`) VALUES
(1, 'admin', '3973ed8d0087bacb93f579bd35e38bd28ac275cd684b83597de4668af49c21718ff1dc1d45b03c5a0286b8f212a04a7c97870ad7d7125ffc629e835b15782394', '063d39079a091242a91c9949d9f6a13c3278a44bb1771091af2bd6ccb81264ffb0ddcef6f7dc684ecaf0aae8cc3eea75d401539976549ae5bde2fc010eac28d8'),
(2, 'sender', 'c90909c813dcb840ab5730cfd160aa97ab50632bc7e9d5ebda552c772d38eee8c2d99bb7c99cff0f785e8e4b3654c0d4f883f335a33df64e95ba8453b74b019a', '40dc07547e807ff997dd8c5147b6630e66cc5b8e85285d1ab94b6e52755cf4db4795cff1ece3706dd0c0774dc12a51ba110a4a816dc28686acfb814875253573');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
