-- phpMyAdmin SQL Dump
-- version 3.3.8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 05, 2010 at 12:26 AM
-- Server version: 5.1.41
-- PHP Version: 5.3.2-1ubuntu4.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `deals`
--

-- --------------------------------------------------------

--
-- Table structure for table `deals`
--

CREATE TABLE IF NOT EXISTS `deals` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `site_code` varchar(20) NOT NULL,
  `src_uniq_id` varchar(100) NOT NULL,
  `title` text,
  `description` text,
  `image` text,
  `actual_price` int(11) DEFAULT NULL,
  `sold_price` int(11) DEFAULT NULL,
  `discount_percentage` int(11) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `valid` tinyint(1) NOT NULL DEFAULT '1',
  `expiry_time` datetime DEFAULT NULL,
  `added_time` datetime DEFAULT NULL,
  `updated_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `click_url` text NOT NULL,
  `credit` varchar(100) DEFAULT NULL,
  `deal_type` varchar(20) DEFAULT NULL,
  `city_code` varchar(50) DEFAULT NULL,
  `sub_locations` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=72 ;
