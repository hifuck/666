-- phpMyAdmin SQL Dump
-- version 4.4.15.10
-- https://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2018-09-12 21:04:13
-- 服务器版本： 5.5.57-log
-- PHP Version: 5.4.45

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `srgx_com`
--

-- --------------------------------------------------------

--
-- 表的结构 `d_log`
--

CREATE TABLE IF NOT EXISTS `d_log` (
  `id` int(11) NOT NULL,
  `user_name` varchar(255) DEFAULT '',
  `login_time` datetime DEFAULT NULL,
  `login_ip` varchar(255) DEFAULT ''
) ENGINE=InnoDB AUTO_INCREMENT=162 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `d_log`
--

INSERT INTO `d_log` (`id`, `user_name`, `login_time`, `login_ip`) VALUES
(161, 'admin', '2018-09-12 21:00:49', '202.124.41.207');

-- --------------------------------------------------------

--
-- 表的结构 `d_manage`
--

CREATE TABLE IF NOT EXISTS `d_manage` (
  `id` int(11) NOT NULL,
  `username` varchar(20) DEFAULT NULL,
  `nickname` varchar(100) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `level` int(4) DEFAULT NULL,
  `lastlogin` datetime DEFAULT NULL,
  `lastip` varchar(30) DEFAULT NULL,
  `secondpwd` varchar(255) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `d_manage`
--

INSERT INTO `d_manage` (`id`, `username`, `nickname`, `password`, `level`, `lastlogin`, `lastip`, `secondpwd`) VALUES
(1, 'bmw999', 'ADMIN', '84baa327ddd71115c87c1ed700bd49d4', 1, '2017-05-23 20:39:10', '127.0.0.1', ''),
(3, 'admin', 'admin', '69672478ebf97c17d5d8b63578054645', 2, '2018-09-12 20:56:57', NULL, NULL);

-- --------------------------------------------------------

--
-- 表的结构 `d_noorder`
--

CREATE TABLE IF NOT EXISTS `d_noorder` (
  `id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `ordertime` int(11) NOT NULL DEFAULT '0',
  `wechatid` varchar(255) DEFAULT NULL,
  `userid` varchar(255) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=460 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `d_order`
--

CREATE TABLE IF NOT EXISTS `d_order` (
  `id` int(11) NOT NULL,
  `orderid` varchar(50) NOT NULL,
  `tradeno` varchar(255) DEFAULT NULL,
  `account` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `realamount` decimal(10,2) DEFAULT NULL,
  `ordertime` int(11) NOT NULL DEFAULT '0',
  `systime` int(11) DEFAULT '0',
  `state` int(11) NOT NULL DEFAULT '0',
  `notifystatus` int(2) NOT NULL,
  `orderip` varchar(255) DEFAULT NULL,
  `returnurl` varchar(200) NOT NULL,
  `notifyurl` varchar(200) NOT NULL,
  `wechatid` varchar(255) DEFAULT NULL,
  `paytype` int(2) DEFAULT '1'
) ENGINE=InnoDB AUTO_INCREMENT=8772 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `d_set`
--

CREATE TABLE IF NOT EXISTS `d_set` (
  `id` int(11) NOT NULL,
  `is_auto` int(11) NOT NULL DEFAULT '0',
  `interval` int(11) NOT NULL DEFAULT '10',
  `extra_int` int(11) NOT NULL DEFAULT '0',
  `extra_str` varchar(255) NOT NULL,
  `page_size` int(11) NOT NULL DEFAULT '20'
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `d_set`
--

INSERT INTO `d_set` (`id`, `is_auto`, `interval`, `extra_int`, `extra_str`, `page_size`) VALUES
(1, 0, 100, 0, '', 30);

-- --------------------------------------------------------

--
-- 表的结构 `d_solidsupp`
--

CREATE TABLE IF NOT EXISTS `d_solidsupp` (
  `id` int(11) NOT NULL,
  `wechatid` varchar(255) DEFAULT NULL,
  `urls` varchar(255) NOT NULL,
  `isopen` int(2) DEFAULT '0',
  `desc` varchar(255) DEFAULT NULL,
  `iswx` int(1) DEFAULT '0',
  `isali` int(1) DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8 COMMENT='接口商列表';

--
-- Indexes for dumped tables
--

--
-- Indexes for table `d_log`
--
ALTER TABLE `d_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `d_manage`
--
ALTER TABLE `d_manage`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `d_noorder`
--
ALTER TABLE `d_noorder`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `d_order`
--
ALTER TABLE `d_order`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `d_set`
--
ALTER TABLE `d_set`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `d_solidsupp`
--
ALTER TABLE `d_solidsupp`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `d_log`
--
ALTER TABLE `d_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=162;
--
-- AUTO_INCREMENT for table `d_manage`
--
ALTER TABLE `d_manage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `d_noorder`
--
ALTER TABLE `d_noorder`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=460;
--
-- AUTO_INCREMENT for table `d_order`
--
ALTER TABLE `d_order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8772;
--
-- AUTO_INCREMENT for table `d_set`
--
ALTER TABLE `d_set`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `d_solidsupp`
--
ALTER TABLE `d_solidsupp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=23;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
