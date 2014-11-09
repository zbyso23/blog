-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 09, 2014 at 08:27 PM
-- Server version: 5.5.40-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `blog`
--

-- --------------------------------------------------------

--
-- Table structure for table `blog`
--

CREATE TABLE IF NOT EXISTS `blog` (
  `blog_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `url` varchar(200) NOT NULL,
  `created` datetime NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`blog_id`),
  UNIQUE KEY `url` (`url`),
  KEY `created` (`created`,`active`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `blog_blo
--
-- Constraints for table `blog_blog_item`
--
ALTER TABLE `blog_blog_item`
  ADD CONSTRAINT `blog_blog_item_ibfk_2` FOREIGN KEY (`blog_item_id`) REFERENCES `blog_item` (`blog_item_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `blog_blog_item_ibfk_1` FOREIGN KEY (`blog_id`) REFERENCES `blog` (`blog_id`) ON DELETE CASCADE ON UPDATE CASCADE;g_item`
--

CREATE TABLE IF NOT EXISTS `blog_blog_item` (
  `blog_id` int(11) NOT NULL,
  `blog_item_id` int(11) NOT NULL,
  `sort` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `blog_id` (`blog_id`,`blog_item_id`),
  KEY `sort` (`sort`),
  KEY `blog_id_2` (`blog_id`),
  KEY `blog_item_id` (`blog_item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `blog_item`
--

CREATE TABLE IF NOT EXISTS `blog_item` (
  `blog_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `content` text NOT NULL,
  PRIMARY KEY (`blog_item_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `blog_blog_item`
--
ALTER TABLE `blog_blog_item`
  ADD CONSTRAINT `blog_blog_item_ibfk_2` FOREIGN KEY (`blog_item_id`) REFERENCES `blog_item` (`blog_item_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `blog_blog_item_ibfk_1` FOREIGN KEY (`blog_id`) REFERENCES `blog` (`blog_id`) ON DELETE CASCADE ON UPDATE CASCADE;
