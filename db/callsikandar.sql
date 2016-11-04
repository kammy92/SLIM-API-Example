-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Nov 04, 2016 at 08:39 AM
-- Server version: 10.1.9-MariaDB
-- PHP Version: 5.6.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `callsikandar`
--

-- --------------------------------------------------------

--
-- Table structure for table `accepted_request`
--

CREATE TABLE `accepted_request` (
  `id` int(11) NOT NULL,
  `service_request_id` int(11) NOT NULL,
  `service_provider_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `accepted_request`
--

INSERT INTO `accepted_request` (`id`, `service_request_id`, `service_provider_id`, `employee_id`, `created_at`) VALUES
(1, 2, 23, 116, '2015-11-20 15:05:52'),
(2, 3, 23, 121, '2015-11-20 15:07:44'),
(3, 4, 23, 118, '2015-11-20 15:08:02'),
(4, 5, 23, 125, '2015-11-20 15:11:54'),
(5, 6, 23, 124, '2015-11-20 15:12:45'),
(6, 7, 23, 123, '2015-11-20 15:13:22'),
(7, 8, 23, 119, '2015-11-20 15:15:56'),
(8, 15, 23, 116, '2015-11-23 21:33:31'),
(9, 51, 26, 127, '2016-02-23 17:59:43'),
(10, 50, 26, 127, '2016-02-24 11:50:35');

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `recNo` int(14) NOT NULL,
  `name` varchar(100) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `address` text COLLATE latin1_general_ci NOT NULL,
  `email` varchar(100) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `mobile` varchar(100) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `type` varchar(100) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `userType` varchar(100) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `status` varchar(100) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `userId` varchar(100) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `userPassword` varchar(100) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `createDate` date NOT NULL DEFAULT '0000-00-00',
  `user_type` varchar(15) COLLATE latin1_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`recNo`, `name`, `address`, `email`, `mobile`, `type`, `userType`, `status`, `userId`, `userPassword`, `createDate`, `user_type`) VALUES
(19, 'Administrator', '', 'admin@callsikandar.com', '9910735879', 'User', 'Modify/View', 'Active', 'administrator', 'callsikandar@2015', '2012-08-23', 'admin'),
(22, 'CCE', 'ACTIKNOW ', 'cce@callsikandar.com', '9971453148', 'User', '', 'Active', '', 'callsikandar@2015', '0000-00-00', 'cce');

-- --------------------------------------------------------

--
-- Table structure for table `api_key`
--

CREATE TABLE `api_key` (
  `id` int(11) NOT NULL,
  `code` varchar(255) NOT NULL,
  `is_active` int(11) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `api_key`
--

INSERT INTO `api_key` (`id`, `code`, `is_active`, `created_at`) VALUES
(1, '9852eee04de8548ac88f6d63e73bf0a8', 1, '2015-10-29 13:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `area_serviced`
--

CREATE TABLE `area_serviced` (
  `id` int(11) NOT NULL,
  `latitude` double NOT NULL,
  `longitude` double NOT NULL,
  `radius` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `area_serviced`
--

INSERT INTO `area_serviced` (`id`, `latitude`, `longitude`, `radius`) VALUES
(1, 28.6390693, 77.0867741, 30);

-- --------------------------------------------------------

--
-- Table structure for table `business_type`
--

CREATE TABLE `business_type` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) NOT NULL,
  `features` varchar(100) NOT NULL,
  `image` varchar(255) NOT NULL,
  `flow` int(11) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `business_type`
--

INSERT INTO `business_type` (`id`, `name`, `description`, `features`, `image`, `flow`, `created_at`) VALUES
(1, 'Driver', 'driver on hourly basis', 'intracity and outstaion', 'driver.jpg', 1, '2015-09-04 13:15:00'),
(2, 'Network Engineer', '', '', 'network_engineer.jpg', 2, '2016-01-09 11:29:00'),
(3, 'Physician', '', '', 'physician.jpg', 2, '2016-01-09 11:29:00'),
(4, 'Physiotherapist', '', '', 'physiotherapist.jpg', 2, '2016-01-09 13:24:00'),
(5, 'Repair Engineer', '', '', 'repair_engineer.jpg', 2, '2016-01-09 14:36:00'),
(6, 'Maid', '', '', 'network_engineer.jpg', 1, '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `cities`
--

CREATE TABLE `cities` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cities`
--

INSERT INTO `cities` (`id`, `name`) VALUES
(1, 'Delhi'),
(2, 'Gurgaon'),
(3, 'Chandausi'),
(4, 'Lucknow');

-- --------------------------------------------------------

--
-- Table structure for table `declined_request`
--

CREATE TABLE `declined_request` (
  `id` int(11) NOT NULL,
  `service_request_id` int(11) NOT NULL,
  `service_provider_id` int(11) NOT NULL,
  `reason_id` int(11) NOT NULL,
  `reason_text` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `declined_request`
--

INSERT INTO `declined_request` (`id`, `service_request_id`, `service_provider_id`, `reason_id`, `reason_text`, `created_at`) VALUES
(1, 1, 23, 2, 'All Drivers are booked', '2015-11-20 12:12:46');

-- --------------------------------------------------------

--
-- Table structure for table `driver_price`
--

CREATE TABLE `driver_price` (
  `id` int(11) NOT NULL,
  `fixed_hour` int(11) NOT NULL,
  `flat_rate` int(11) NOT NULL,
  `hour_charges` int(11) NOT NULL,
  `day_charges` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `driver_price`
--

INSERT INTO `driver_price` (`id`, `fixed_hour`, `flat_rate`, `hour_charges`, `day_charges`) VALUES
(1, 4, 300, 50, 900);

-- --------------------------------------------------------

--
-- Table structure for table `employee`
--

CREATE TABLE `employee` (
  `id` int(11) NOT NULL,
  `service_provider_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `mobile` bigint(20) NOT NULL,
  `idnumber` varchar(100) NOT NULL,
  `address` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `is_available` int(11) NOT NULL,
  `is_default` int(11) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `employee`
--

INSERT INTO `employee` (`id`, `service_provider_id`, `name`, `mobile`, `idnumber`, `address`, `city`, `is_available`, `is_default`, `created_at`) VALUES
(0, 0, '', 0, '', '', '', 0, 0, '2015-10-11 12:54:13'),
(110, 23, 'Drivers 24X7', 9643108086, '', '', '', 0, 1, '0000-00-00 00:00:00'),
(112, 23, 'monu', 9698521470, '', '', '', 0, 0, '2015-11-19 16:50:53'),
(116, 23, 'amit', 7503853966, '', '', '', 0, 0, '2015-11-20 13:05:33'),
(117, 23, 'raj', 7503853966, '', '', '', 0, 0, '2015-11-20 13:06:11'),
(118, 23, 'gourav', 7503853966, '', '', '', 0, 0, '2015-11-20 13:06:40'),
(119, 23, 'lokesh', 7503853966, '', '', '', 0, 0, '2015-11-20 13:07:19'),
(120, 23, 'yuvraj', 7503853966, '', '', '', 0, 0, '2015-11-20 13:07:54'),
(121, 23, 'anshul', 7503853966, '', '', '', 0, 0, '2015-11-20 13:08:43'),
(122, 23, 'vishal', 7503853966, '', '', '', 0, 0, '2015-11-20 13:09:27'),
(123, 23, 'jatin', 7503853966, '', '', '', 0, 0, '2015-11-20 13:09:55'),
(125, 23, 'harsh', 7503853966, '', '', '', 0, 0, '2015-11-20 13:19:04'),
(126, 25, 'ramesh', 9971453148, '', '', '', 0, 1, '0000-00-00 00:00:00'),
(127, 26, 'kammy', 9898989898, '', '', '', 0, 0, '2016-02-23 17:59:40');

-- --------------------------------------------------------

--
-- Table structure for table `localities`
--

CREATE TABLE `localities` (
  `id` int(11) NOT NULL,
  `city_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `localities`
--

INSERT INTO `localities` (`id`, `city_id`, `name`) VALUES
(1, 1, 'Dwarka'),
(2, 1, 'Janakpuri'),
(3, 2, 'Old Gurgaon'),
(4, 2, 'Manesar'),
(5, 3, 'Chandausi Nagar'),
(6, 4, 'Gomti Nagar'),
(7, 4, 'Charbagh');

-- --------------------------------------------------------

--
-- Table structure for table `otp`
--

CREATE TABLE `otp` (
  `id` int(11) NOT NULL,
  `mobile` bigint(20) NOT NULL,
  `random_otp` int(11) NOT NULL,
  `is_used` int(11) NOT NULL,
  `expiry_time` datetime NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `pricing`
--

CREATE TABLE `pricing` (
  `id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `service_subscription_id` int(11) NOT NULL,
  `pricing` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `pricing`
--

INSERT INTO `pricing` (`id`, `service_id`, `service_subscription_id`, `pricing`) VALUES
(1, 1, 1, '1 to 4 Hours - Rs. 300'),
(2, 1, 1, 'Above 4 Hours - Rs. 50 per hour'),
(3, 1, 1, 'Night Charge - 50% extra will be charged above the starting to closing time charge.');

-- --------------------------------------------------------

--
-- Table structure for table `service`
--

CREATE TABLE `service` (
  `id` int(11) NOT NULL,
  `business_type_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `service`
--

INSERT INTO `service` (`id`, `business_type_id`, `name`, `created_at`) VALUES
(1, 1, 'Intracity', '2015-09-04 13:15:00'),
(2, 1, 'Outstation', '2015-09-04 13:15:00');

-- --------------------------------------------------------

--
-- Table structure for table `service_level`
--

CREATE TABLE `service_level` (
  `id` int(11) NOT NULL,
  `service_subsription_id` int(11) NOT NULL,
  `levelname` varchar(100) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `service_level`
--

INSERT INTO `service_level` (`id`, `service_subsription_id`, `levelname`, `created_at`) VALUES
(1, 1, 'Standard', '2015-09-04 13:27:00');

-- --------------------------------------------------------

--
-- Table structure for table `service_provider`
--

CREATE TABLE `service_provider` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `mobile` bigint(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `login_key` varchar(100) NOT NULL,
  `address` varchar(255) NOT NULL,
  `latitude` varchar(255) NOT NULL,
  `longitude` varchar(255) NOT NULL,
  `status` varchar(1) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `service_provider`
--

INSERT INTO `service_provider` (`id`, `name`, `mobile`, `email`, `password`, `login_key`, `address`, `latitude`, `longitude`, `status`, `created_at`) VALUES
(0, '', 0, '', '', '', '', '', '', '', '0000-00-00 00:00:00'),
(26, 'Karman Singh', 9873684678, 'karman.singh@actiknowbi.com', 'c93ccd78b2076528346216b3b2f701e6', 'c93ccd78b2076528346216b3b2f701e6', '', '', '', '', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `service_provider_regions`
--

CREATE TABLE `service_provider_regions` (
  `service_provider_region_id` int(11) NOT NULL,
  `region_id` int(11) NOT NULL,
  `service_provider_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `service_provider_regions`
--

INSERT INTO `service_provider_regions` (`service_provider_region_id`, `region_id`, `service_provider_id`) VALUES
(22, 1, 23),
(23, 2, 23),
(24, 3, 23),
(25, 4, 23),
(26, 6, 25),
(27, 7, 25),
(28, 9, 25),
(29, 10, 25),
(30, 6, 23),
(31, 7, 23),
(32, 9, 23),
(33, 10, 23);

-- --------------------------------------------------------

--
-- Table structure for table `service_provider_regions_arae_covered`
--

CREATE TABLE `service_provider_regions_arae_covered` (
  `s_area_covered_id` int(11) NOT NULL,
  `tagged_area_id` int(11) NOT NULL,
  `service_provider_region_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `service_provider_regions_areas`
--

CREATE TABLE `service_provider_regions_areas` (
  `id` int(11) NOT NULL,
  `region_id` int(11) NOT NULL,
  `area_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `service_rating`
--

CREATE TABLE `service_rating` (
  `id` int(11) NOT NULL,
  `service_request_id` int(11) NOT NULL,
  `service_rating` int(11) NOT NULL,
  `comments` varchar(255) NOT NULL,
  `first_time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `service_rating`
--

INSERT INTO `service_rating` (`id`, `service_request_id`, `service_rating`, `comments`, `first_time`) VALUES
(1, 1, 5, '', 0),
(2, 8, 5, '', 0),
(3, 12, 5, '', 0),
(4, 13, 5, '', 0),
(5, 18, 4, '', 0),
(6, 27, 5, '', 0),
(7, 33, 5, '', 0),
(8, 23, 5, '', 0),
(9, 43, 5, '', 0),
(10, 37, 5, '', 0),
(11, 36, 5, '', 0),
(12, 47, 5, '', 0),
(13, 44, 4, '', 0),
(14, 4, 3, 'nice pasdfdasf', 0),
(15, 49, 5, '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `service_region`
--

CREATE TABLE `service_region` (
  `id` int(11) NOT NULL,
  `region_name` varchar(100) NOT NULL,
  `region_zone_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `service_region`
--

INSERT INTO `service_region` (`id`, `region_name`, `region_zone_id`, `created_at`) VALUES
(1, 'NORTH DELHI', 1, '2015-09-04 13:16:00'),
(2, 'EAST DELHI', 1, '2015-09-04 13:17:00'),
(3, 'SOUTH DELHI', 1, '2015-09-04 13:17:00'),
(4, 'WEST DELHI', 1, '2015-09-04 13:17:00'),
(6, 'Noida', 5, '0000-00-00 00:00:00'),
(7, 'Gurgaon', 5, '0000-00-00 00:00:00'),
(9, 'Faridabad', 5, '0000-00-00 00:00:00'),
(10, 'Ghaziabad', 5, '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table ` service_region_zones`
--

CREATE TABLE ` service_region_zones` (
  `region_zone_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table ` service_region_zones`
--

INSERT INTO ` service_region_zones` (`region_zone_id`, `name`) VALUES
(1, 'Delhi'),
(5, 'NCR');

-- --------------------------------------------------------

--
-- Table structure for table `service_request`
--

CREATE TABLE `service_request` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `address_id` int(11) NOT NULL,
  `service_time` time NOT NULL,
  `service_date` date NOT NULL,
  `cartype` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `service_provider_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `progress` int(11) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `service_request`
--

INSERT INTO `service_request` (`id`, `user_id`, `service_id`, `address_id`, `service_time`, `service_date`, `cartype`, `status`, `service_provider_id`, `employee_id`, `progress`, `created_at`) VALUES
(1, 1, 0, 1, '13:10:00', '2015-11-20', 1, 4, 0, 0, 0, '2015-11-20 12:12:21'),
(2, 2, 0, 2, '16:00:00', '2015-11-20', 1, 4, 23, 116, 1, '2015-11-20 15:05:01'),
(3, 3, 0, 3, '18:25:00', '2015-11-20', 2, 4, 23, 121, 1, '2015-11-20 15:07:03'),
(4, 5, 0, 4, '16:05:00', '2015-11-21', 2, 4, 23, 118, 1, '2015-11-20 15:07:25'),
(5, 8, 0, 5, '21:00:00', '2015-11-21', 1, 2, 23, 125, 1, '2015-11-20 15:11:24'),
(6, 5, 0, 6, '16:11:00', '2015-11-22', 2, 4, 23, 124, 1, '2015-11-20 15:12:20'),
(7, 6, 0, 7, '16:11:00', '2015-11-20', 1, 4, 23, 123, 1, '2015-11-20 15:13:06'),
(8, 6, 0, 8, '16:14:00', '2015-11-22', 2, 4, 23, 119, 1, '2015-11-20 15:15:09'),
(9, 9, 0, 9, '17:02:00', '2015-11-20', 2, 4, 0, 0, 0, '2015-11-20 16:05:47'),
(10, 58, 1, 10, '21:37:00', '2015-11-25', 1, 5, 0, 0, 0, '2015-11-21 16:37:57'),
(11, 1, 1, 11, '15:27:00', '2015-11-24', 1, 4, 0, 0, 0, '2015-11-23 15:27:04'),
(12, 1, 0, 12, '11:13:00', '2015-11-23', 1, 4, 0, 0, 0, '2015-11-23 16:46:36'),
(13, 11, 0, 13, '18:18:00', '2015-11-23', 1, 4, 0, 0, 0, '2015-11-23 17:19:39'),
(14, 1, 1, 15, '18:43:00', '2015-11-24', 1, 4, 0, 0, 0, '2015-11-23 18:43:10'),
(15, 11, 1, 16, '21:32:00', '2015-11-24', 1, 2, 23, 116, 1, '2015-11-23 21:32:19'),
(16, 11, 1, 17, '10:29:00', '2015-11-25', 2, 4, 0, 0, 0, '2015-11-24 10:29:48'),
(17, 11, 1, 18, '10:29:00', '2015-11-25', 1, 4, 0, 0, 0, '2015-11-24 12:30:05'),
(18, 11, 0, 19, '13:43:00', '2015-11-24', 2, 4, 0, 0, 0, '2015-11-24 12:43:48'),
(19, 5, 0, 20, '13:53:00', '2015-11-24', 2, 4, 0, 0, 0, '2015-11-24 12:53:33'),
(20, 5, 0, 21, '13:55:00', '2015-11-24', 2, 1, 0, 0, 0, '2015-11-24 12:55:55'),
(21, 11, 1, 22, '12:56:00', '2015-11-25', 1, 4, 0, 0, 0, '2015-11-24 12:56:25'),
(22, 5, 0, 23, '14:03:00', '2015-11-26', 2, 4, 0, 0, 0, '2015-11-24 13:05:32'),
(23, 12, 1, 24, '19:28:00', '2015-11-24', 2, 4, 0, 0, 0, '2015-11-24 15:28:51'),
(24, 12, 1, 25, '20:28:00', '2015-11-24', 2, 5, 0, 0, 0, '2015-11-24 15:31:41'),
(25, 11, 1, 26, '15:39:00', '2015-11-25', 1, 4, 0, 0, 0, '2015-11-24 15:39:13'),
(26, 5, 0, 27, '17:02:00', '2015-11-24', 2, 4, 0, 0, 0, '2015-11-24 16:02:37'),
(27, 5, 0, 28, '17:05:00', '2015-11-24', 2, 4, 0, 0, 0, '2015-11-24 16:05:23'),
(28, 11, 0, 29, '17:07:00', '2015-11-24', 1, 4, 0, 0, 0, '2015-11-24 16:08:25'),
(29, 5, 0, 30, '18:14:00', '2015-11-26', 2, 4, 0, 0, 0, '2015-11-24 17:15:50'),
(30, 11, 1, 31, '18:18:00', '2015-11-25', 1, 4, 0, 0, 0, '2015-11-24 18:18:43'),
(31, 7, 0, 32, '19:09:00', '2015-11-24', 1, 4, 0, 0, 0, '2015-11-24 18:21:24'),
(32, 11, 0, 33, '19:22:00', '2015-11-24', 1, 4, 0, 0, 0, '2015-11-24 18:22:39'),
(33, 11, 0, 34, '19:23:00', '2015-11-24', 1, 4, 0, 0, 0, '2015-11-24 18:24:36'),
(34, 11, 1, 35, '18:25:00', '2015-11-25', 1, 4, 0, 0, 0, '2015-11-24 18:25:29'),
(35, 11, 1, 36, '18:52:00', '2015-11-25', 2, 4, 0, 0, 0, '2015-11-24 18:52:50'),
(36, 13, 1, 37, '09:46:00', '2015-11-26', 2, 4, 0, 0, 0, '2015-11-25 09:46:47'),
(37, 16, 1, 38, '10:50:00', '2015-11-26', 2, 4, 0, 0, 0, '2015-11-25 10:50:42'),
(38, 5, 1, 39, '10:53:00', '2015-11-26', 2, 4, 0, 0, 0, '2015-11-25 10:53:40'),
(39, 11, 1, 40, '10:54:00', '2015-11-26', 1, 4, 0, 0, 0, '2015-11-25 10:54:20'),
(40, 15, 1, 42, '10:56:00', '2015-11-26', 1, 4, 0, 0, 0, '2015-11-25 10:56:24'),
(41, 14, 1, 43, '13:57:00', '2015-11-25', 2, 4, 0, 0, 0, '2015-11-25 11:05:59'),
(42, 21, 1, 44, '11:13:00', '2015-11-26', 1, 4, 0, 0, 0, '2015-11-25 11:13:38'),
(43, 17, 1, 45, '16:18:00', '2015-11-26', 2, 4, 0, 0, 0, '2015-11-25 16:20:34'),
(44, 25, 1, 46, '18:55:00', '2015-12-03', 2, 4, 0, 0, 0, '2015-12-02 17:26:42'),
(45, 12, 1, 47, '17:00:00', '2015-12-08', 2, 4, 0, 0, 0, '2015-12-07 12:43:16'),
(46, 12, 1, 48, '17:00:00', '2015-12-07', 2, 4, 0, 0, 0, '2015-12-07 13:10:15'),
(47, 12, 1, 49, '18:00:00', '2015-12-07', 2, 4, 0, 0, 0, '2015-12-07 13:42:48'),
(48, 26, 1, 50, '10:00:00', '2015-12-13', 1, 4, 0, 0, 0, '2015-12-12 08:52:17'),
(49, 12, 1, 52, '17:00:00', '2015-12-17', 2, 4, 0, 0, 0, '2015-12-16 14:22:24'),
(50, 4, 1, 53, '10:40:00', '2016-01-14', 2, 1, 26, 127, 2, '2016-01-13 10:40:29'),
(51, 4, 1, 55, '13:21:00', '2016-01-14', 2, 1, 26, 127, 2, '2016-01-13 13:21:07');

-- --------------------------------------------------------

--
-- Table structure for table `service_subscription`
--

CREATE TABLE `service_subscription` (
  `id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `service_provider_id` int(11) NOT NULL,
  `service_region_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `service_subscription`
--

INSERT INTO `service_subscription` (`id`, `service_id`, `service_provider_id`, `service_region_id`, `created_at`) VALUES
(1, 1, 1, 1, '2015-09-04 13:15:00');

-- --------------------------------------------------------

--
-- Table structure for table `service_timing`
--

CREATE TABLE `service_timing` (
  `id` int(11) NOT NULL,
  `service_subscription_id` int(11) NOT NULL,
  `start_date_time` datetime NOT NULL,
  `end_date_time` datetime NOT NULL,
  `break_start_date_time` datetime NOT NULL,
  `break_end_date_time` datetime NOT NULL,
  `day` int(11) NOT NULL,
  `is_available` int(11) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `terms`
--

CREATE TABLE `terms` (
  `id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `service_subscription_id` int(11) NOT NULL,
  `terms` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `terms`
--

INSERT INTO `terms` (`id`, `service_id`, `service_subscription_id`, `terms`) VALUES
(1, 1, 1, 'A grace period of 10 minutes is given before the charges of next hour or next slab is applied.'),
(2, 1, 1, 'Night charge period commences from 10:30 PM and ends at 05:00 AM.'),
(3, 1, 1, 'If the service is extended over two calendar dates then Rs. 300 will be again charged on second day from 5 AM to 9 AM and there after Rs. 50 will be charged per hour.'),
(4, 1, 1, 'No cancellation charges applicable if job is cancelled before driver has reached.'),
(5, 1, 1, 'Rs. 100 will be charged if a job is cancelled within 10 minutes after job starting time.'),
(6, 1, 1, 'Cancellation charges after 10 minutes of job starting time will be Rs. 300.');

-- --------------------------------------------------------

--
-- Table structure for table `testing_employee_profile`
--

CREATE TABLE `testing_employee_profile` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `mobile` bigint(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `fees` varchar(255) NOT NULL,
  `rating` double NOT NULL,
  `experience` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `id_proof_type` varchar(255) NOT NULL,
  `id_proof_no` varchar(255) NOT NULL,
  `start_time` varchar(255) NOT NULL,
  `end_time` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `testing_employee_profile`
--

INSERT INTO `testing_employee_profile` (`id`, `name`, `mobile`, `email`, `address`, `title`, `fees`, `rating`, `experience`, `category`, `id_proof_type`, `id_proof_no`, `start_time`, `end_time`, `image`) VALUES
(1, 'Ankit', 9898989898, 'abc@cdv.com', '4/5005 anndkdjf, dsjsjsdk', 'MBBS', '500', 3.5, '3', 'Physician', 'Licence', 'DL87687687687', '10', '18', 'http://pickaface.net/includes/themes/clean/img/slide4.png'),
(2, 'Shantanu', 9999999999, 'abc@cdv.com', '4/5005 anndkdjf, dsjsjsdk', 'MD', '1000', 2, '5', 'Physician', 'Voter Id', 'ACD344D', '12', '20', 'http://pickaface.net/includes/themes/clean/img/slide2.png'),
(3, 'Rahul', 8888888888, 'abc@cdv.com', '4/5005 anndkdjf, dsjsjsdk', 'BD', '500', 2.5, '4', 'Network Engineer', 'Passport', '65G657', '14', '21', 'http://www.exaholics.com/wp-content/uploads/avatars/11637/avatar11637-bpthumb.jpg'),
(4, 'Sudhanshu', 7777777777, 'abc@cdv.com', '4/5005 anndkdjf, dsjsjsdk', 'MBBS', '400', 3.8, '3', 'Physiotherapist', 'Adhaar Card', '765476547654', '9', '15', 'http://pickaface.net/avatar/Opi51c7dccf270e0.png'),
(5, 'Madhu', 9879879879, 'abc@cdv.com', '4/5005 anndkdjf, dsjsjsdk', 'MD, MBBS', '400', 4.7, '8', 'Physician', 'Licence', 'DL98776587', '10', '18', 'http://pickaface.net/includes/themes/clean/img/slide1.png');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `mobile` bigint(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `req_counter` tinyint(4) NOT NULL,
  `forget_pass_req_date` date NOT NULL,
  `created_at` datetime NOT NULL,
  `status` tinyint(4) NOT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `name`, `mobile`, `email`, `req_counter`, `forget_pass_req_date`, `created_at`, `status`, `password`) VALUES
(2, 'TestUser', 7838031931, '', 0, '0000-00-00', '2015-11-20 15:01:45', 2, 'test123'),
(3, 'chandan kumar', 9718240464, '', 0, '0000-00-00', '2015-11-20 15:04:30', 2, 'actchandan'),
(4, 'Karman', 9873684678, '', 0, '0000-00-00', '2015-11-20 15:06:06', 2, '123456'),
(5, 'ramesh', 9971453148, '', 1, '2015-12-31', '2015-11-20 15:06:19', 2, 'admin'),
(6, 'rrr', 8527875036, '', 0, '0000-00-00', '2015-11-20 15:07:38', 2, '123456789'),
(7, 'sudhanshu', 9911039216, '', 1, '2015-11-24', '2015-11-20 15:09:50', 2, '77492652'),
(8, 'Anil', 9818607692, '', 0, '0000-00-00', '2015-11-20 15:10:08', 2, 'anil123'),
(9, 'ramesh', 9654870824, '', 0, '0000-00-00', '2015-11-20 16:03:39', 2, 'admin'),
(10, 'Sudeep Mathur', 9810376424, 'sudeep_mathur@yahoo.com', 0, '0000-00-00', '2015-11-21 22:40:16', 0, NULL),
(11, 'Ankit', 7503853966, '', 0, '0000-00-00', '2015-11-23 17:13:49', 2, '123456'),
(12, 'kamal', 9810153606, 'Rastogikamal@yahoo.com', 0, '0000-00-00', '2015-11-24 15:27:05', 2, 'PXHL8h'),
(13, 'dhruv rastogi ', 9643108085, 'dhruvrastogi22@gmail.com', 0, '0000-00-00', '2015-11-24 20:36:42', 0, NULL),
(14, 'Abhineet Ranjan', 9717379026, 'abhineetranjan7@gmail.com', 0, '0000-00-00', '2015-11-25 10:45:19', 0, NULL),
(15, 'Praveen', 9871070056, '29praveensingh@gmail.com', 0, '0000-00-00', '2015-11-25 10:47:40', 0, NULL),
(16, 'Ashutosh Kumar ', 9560414855, 'ashutosh.kumar@actiknow.com', 0, '0000-00-00', '2015-11-25 10:50:23', 0, NULL),
(17, 'Jitendra Kushwah ', 9716113903, 'jitendra.kushwah@actiknow.com', 0, '0000-00-00', '2015-11-25 10:54:25', 0, NULL),
(18, 'shivani', 9958954051, 'paliwal.Shivani@gmail.com', 0, '0000-00-00', '2015-11-25 10:55:57', 0, NULL),
(19, 'Sugansh Gupta', 9560425219, 'sugandh.gupta@actiknow.com', 0, '0000-00-00', '2015-11-25 11:00:08', 0, NULL),
(20, 'Prateek Tyagi', 9953539985, 'prateik.tyagi@gmail.com', 0, '0000-00-00', '2015-11-25 11:01:17', 0, NULL),
(21, 'sudhanshu', 9628936806, 'sudhanshu.sharma@actiknowbi.com', 0, '0000-00-00', '2015-11-25 11:12:10', 0, NULL),
(22, 'govind khanna ', 8506960315, '', 0, '0000-00-00', '2015-11-25 12:12:03', 0, NULL),
(23, 'Jai Kaushik ', 9999775657, 'Kaushik.jai1994@gmail.com', 0, '0000-00-00', '2015-11-25 13:36:44', 0, NULL),
(24, 'kazi wasique', 9932845092, 'kaziwasique99@gmail.com', 0, '0000-00-00', '2015-11-27 00:09:40', 0, NULL),
(25, 'Sumit Jhunjhunwala', 9873445121, 'sumit341@gmail.com', 0, '0000-00-00', '2015-12-02 17:25:52', 0, NULL),
(26, 'prashant', 9811226692, '', 0, '0000-00-00', '2015-12-11 19:10:00', 0, NULL),
(27, 'manish', 9654513595, '', 0, '0000-00-00', '2015-12-16 11:56:14', 0, NULL),
(28, 'Abhishek', 9015044485, '', 0, '0000-00-00', '2015-12-16 13:18:23', 0, NULL),
(29, 'sanjay sehgal', 9313594125, '', 0, '0000-00-00', '2015-12-23 23:10:55', 0, NULL),
(30, 'sanchari', 9873380589, '', 0, '0000-00-00', '2015-12-31 13:09:34', 1, 'admin'),
(31, 'Sanil Dubey', 7840055066, 'dubeysanil56@yahoo.com', 0, '0000-00-00', '2016-01-06 22:15:23', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_address`
--

CREATE TABLE `user_address` (
  `id` int(11) NOT NULL,
  `addressname` varchar(100) NOT NULL,
  `user_id` int(11) NOT NULL,
  `latitude` varchar(50) NOT NULL,
  `longitude` varchar(50) NOT NULL,
  `addressline0` varchar(255) NOT NULL,
  `addressline1` varchar(255) NOT NULL,
  `addressline2` varchar(255) NOT NULL,
  `addressline3` varchar(255) NOT NULL,
  `locality` varchar(100) NOT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `pincode` int(11) NOT NULL,
  `country` varchar(100) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_address`
--

INSERT INTO `user_address` (`id`, `addressname`, `user_id`, `latitude`, `longitude`, `addressline0`, `addressline1`, `addressline2`, `addressline3`, `locality`, `city`, `state`, `pincode`, `country`, `created_at`) VALUES
(1, '', 0, '28.5744479', '77.0651709', 'b3', 'Dwarka Sector 9, Dwarka, New Delhi, Delhi, India', '', '', '', 'Delhi', 'Delhi', 0, '', '2015-11-20 12:12:21'),
(2, '', 1, '28.619284', '77.03315599999999', 'A49 sector 8 dwarka', 'Dwarka Mor Metro Station, Sewak Park, New Delhi, India', '', '', '', 'Delhi', 'Delhi', 0, '', '2015-11-20 15:05:01'),
(3, '', 1, '28.5715292', '77.0729954', 'white house', 'Bagdola Village, Dwarka, New Delhi, India', '', '', '', 'Delhi', 'Delhi', 0, '', '2015-11-20 15:07:03'),
(4, '', 0, '28.6184207', '77.01392349999999', '', 'Najafgarh Road, Ranaji Enclave, New Delhi, India', '', '', '', 'Delhi', 'Delhi', 0, '', '2015-11-20 15:07:25'),
(5, '', 0, '28.624851', '77.065269', 'A-5F', 'Uttam Nagar East Metro Station, Uttam Nagar, New Delhi, Delhi, India', '', '', '', 'Delhi', 'Delhi', 0, '', '2015-11-20 15:11:24'),
(6, '', 0, '36.0872749', '-88.51052279999999', '', 'D-Block Community Centre, Dwarka, New Delhi, India', '', '', '', 'Delhi', 'Delhi', 0, '', '2015-11-20 15:12:20'),
(7, '', 0, '28.619284', '77.03315599999999', '32233', 'Dwarka Mor Metro Station, Sewak Park, New Delhi, Delhi, India', '', '', '', 'Delhi', 'Delhi', 0, '', '2015-11-20 15:13:06'),
(8, '', 0, '28.6426635', '77.21680359999999', '566', 'Ratan Lal Market, New Delhi, Delhi, India', '', '', '', 'Delhi', 'Delhi', 0, '', '2015-11-20 15:15:09'),
(9, '', 0, '27.0238036', '74.21793260000001', '', 'Rajasthan, India', '', '', '', 'Delhi', 'Delhi', 0, '', '2015-11-20 16:05:47'),
(10, '', 0, '28.5949598', '77.0414543', '47, Road Number 203', 'Pocket 8, Block B, Sector 12 Dwarka, Dwarka', 'New Delhi, Delhi 110075', 'India', 'Pocket 8, Block B', 'New Delhi', 'Delhi', 110075, 'India', '2015-11-21 16:37:57'),
(11, '', 0, '28.574973', '77.0709251', 'A50', 'Block A, Sector 8 Dwarka, Dwarka', 'New Delhi, Delhi 110077', 'India', 'Dwarka', 'New Delhi', 'Delhi', 110077, 'India', '2015-11-23 15:27:04'),
(12, '', 0, '28.5744479', '77.0651709', 'B2 ', 'Dwarka Sector 9, Dwarka, New Delhi, Delhi, India', '', '', '', 'Delhi', 'Delhi', 0, '', '2015-11-23 16:46:35'),
(13, '', 0, '28.5744479', '77.0651709', 'B2 ', 'Dwarka Sector 9, Dwarka, New Delhi, Delhi, India', '', '', '', 'Delhi', 'Delhi', 0, '', '2015-11-23 17:19:39'),
(14, '', 6, '0.0', '0.0', '', '', '', '', '', '', '', 0, '', '2015-11-23 18:21:21'),
(15, '', 0, '28.5750699', '77.0709587', 'A49', 'Block A, Sector 8 Dwarka, Dwarka', 'New Delhi, Delhi 110077', 'India', 'Dwarka', 'New Delhi', 'Delhi', 110077, 'India', '2015-11-23 18:43:10'),
(16, '', 0, '28.5883267', '77.0735868', 'WZ 1200, Road Number 224', 'Harijan Basti, Palam Extension, Palam Colony', 'New Delhi, Delhi 110075', 'India', 'Palam Colony', 'New Delhi', 'Delhi', 110075, 'India', '2015-11-23 21:32:19'),
(17, '', 0, '28.574973', '77.0709251', 'A50', 'Block A, Sector 8 Dwarka, Dwarka', 'New Delhi, Delhi 110077', 'India', 'Dwarka', 'New Delhi', 'Delhi', 110077, 'India', '2015-11-24 10:29:48'),
(18, '', 0, '28.5756151', '77.0714627', 'A2', 'Block A, Sector 8 Dwarka, Dwarka', 'New Delhi, Delhi 110077', 'India', 'Dwarka', 'New Delhi', 'Delhi', 110077, 'India', '2015-11-24 12:30:05'),
(19, '', 0, '28.6090126', '76.9854526', 'fgf', 'Najafgarh, New Delhi, Delhi, India', '', '', '', 'Delhi', 'Delhi', 0, '', '2015-11-24 12:43:48'),
(20, '', 0, '28.5744479', '77.0651709', '', 'Dwarka Sector 9, Dwarka, New Delhi, India', '', '', '', 'Delhi', 'Delhi', 0, '', '2015-11-24 12:53:33'),
(21, '', 0, '28.57816', '77.317764', '', 'Noida Sector 16 Metro Station, Sector 3, Noida, Uttar Pradesh, India', '', '', '', 'Noida', 'Noida', 0, '', '2015-11-24 12:55:55'),
(22, '', 0, '28.5889152', '77.0711967', 'Block A, Palam Extension, Palam Colony', 'New Delhi, Delhi 110075', 'India', '', 'Palam Colony', 'New Delhi', 'Delhi', 110075, 'India', '2015-11-24 12:56:25'),
(23, '', 0, '28.4594965', '77.0266383', '', 'Gurgaon, Haryana, India', '', '', '', 'Delhi', 'Delhi', 0, '', '2015-11-24 13:05:32'),
(24, '', 0, '28.5849492', '77.05828439999999', '50 , cosmos app, plot 28, sector 10, dwarka near venkateshwara school ', 'Dwarka Sector 10, Delhi, India', '', '', '', 'Delhi', '', 0, '', '2015-11-24 15:28:51'),
(25, '', 0, '28.574973', '77.0709251', 'A50', 'Block A, Sector 8 Dwarka, Dwarka', 'New Delhi, Delhi 110077', 'India', 'Dwarka', 'New Delhi', 'Delhi', 110077, 'India', '2015-11-24 15:31:41'),
(26, '', 0, '28.579363', '77.070299', '66 TO 90', 'Pocket 2, Dwarka Sector 9, Dwarka', 'New Delhi, Delhi 110077', 'India', 'Dwarka', 'New Delhi', 'Delhi', 110077, 'India', '2015-11-24 15:39:13'),
(27, '', 0, '28.57816', '77.317764', '', 'Noida Sector 16 Metro Station, Sector 3, Noida, Uttar Pradesh, India', '', '', '', 'Noida', 'Noida', 0, '', '2015-11-24 16:02:37'),
(28, '', 0, '28.6691565', '77.45375779999999', 'House No 10', 'Ghaziabad, Uttar Pradesh, India', '', '', '', 'Ghaziabad', 'Ghaziabad', 0, '', '2015-11-24 16:05:23'),
(29, '', 0, '28.641485', '77.3713856', 'B2 ', 'Indirapuram, Ghaziabad, Uttar Pradesh, India', '', '', '', 'Ghaziabad', 'Ghaziabad', 0, '', '2015-11-24 16:08:25'),
(30, '', 0, '28.57816', '77.317764', '', 'Noida Sector 16 Metro Station, Sector 3, Noida, Uttar Pradesh, India', '', '', '', 'Delhi', 'Delhi', 0, '', '2015-11-24 17:15:50'),
(31, '', 0, '28.620505', '77.362565', 'B 19', 'Block B, Industrial Area, Sector 62', 'Noida, Uttar Pradesh 201309', 'India', 'Sector 62', 'Noida', 'Uttar Pradesh', 201309, 'India', '2015-11-24 18:18:43'),
(32, '', 0, '28.4945045', '77.0692827', 'b3', 'Sector 18, Gurgaon, Haryana, India', '', '', '', 'Gurgaon', 'Gurgaon', 0, '', '2015-11-24 18:21:24'),
(33, '', 0, '28.5679882', '77.3137756', 'b3', 'Sector 16A, Noida, Uttar Pradesh, India', '', '', '', 'Noida', 'Noida', 0, '', '2015-11-24 18:22:39'),
(34, '', 0, '28.4931605', '77.30291509999999', 'sec 2', 'Badarpur Metro Station, Badarpur, Faridabad, Delhi, India', '', '', '', 'Faridabad', 'Faridabad', 0, '', '2015-11-24 18:24:36'),
(35, '', 0, '28.5805453', '77.0662971', '157 TO 164', 'Pocket 2, Dwarka Sector 9, Dwarka', 'New Delhi, Delhi 110077', 'India', 'Dwarka', 'New Delhi', 'Delhi', 110077, 'India', '2015-11-24 18:25:29'),
(36, '', 0, '28.6523234', '77.297191', '371', 'A G C R Enclave, Delhi, India', '', '', '', 'Delhi', '', 0, '', '2015-11-24 18:52:50'),
(37, '', 0, '28.5907047', '77.1289654', 'Thimayya Marg', 'Kabul Lines, Delhi Cantonment', 'New Delhi, Delhi 110010', 'India', 'Kabul Lines', 'New Delhi', 'Delhi', 110010, 'India', '2015-11-25 09:46:47'),
(38, '', 0, '28.574973', '77.0709251', 'A50', 'Block A, Sector 8 Dwarka, Dwarka', 'New Delhi, Delhi 110077', 'India', 'Dwarka', 'New Delhi', 'Delhi', 110077, 'India', '2015-11-25 10:50:42'),
(39, '', 0, '28.584736', '77.315945', 'C-109', 'C Block, Sector 2', 'Noida, Uttar Pradesh 201301', 'India', 'Sector 2', 'Noida', 'Uttar Pradesh', 201301, 'India', '2015-11-25 10:53:40'),
(40, '', 0, '28.574973', '77.0709251', 'A50', 'Block A, Sector 8 Dwarka, Dwarka', 'New Delhi, Delhi 110077', 'India', 'Dwarka', 'New Delhi', 'Delhi', 110077, 'India', '2015-11-25 10:54:20'),
(41, 'office', 11, '28.574973', '77.0709251', 'A50', 'Block A, Sector 8 Dwarka, Dwarka', 'New Delhi, Delhi 110077', 'India', 'Dwarka', 'New Delhi', 'Delhi', 110077, 'India', '2015-11-25 10:55:17'),
(42, '', 0, '28.6107882', '77.2345068', 'Shershah Rd', 'Delhi High Court, India Gate', 'New Delhi, Delhi 110002', 'India', 'India Gate', 'New Delhi', 'Delhi', 110002, 'India', '2015-11-25 10:56:24'),
(43, '', 0, '28.641485', '77.3713856', 'b2', 'Indirapuram, Ghaziabad, Uttar Pradesh, India', '', '', '', 'Ghaziabad', '', 0, '', '2015-11-25 11:05:59'),
(44, '', 0, '28.5844058', '77.0591462', '26', 'Sector 10 Dwarka, Dwarka', 'New Delhi, Delhi 110075', 'India', 'Dwarka', 'New Delhi', 'Delhi', 110075, 'India', '2015-11-25 11:13:38'),
(45, '', 0, '28.5572355', '77.058678', 'Road Number 210', 'Ranjit Vihar-I, Sector 22, Dwarka', 'New Delhi, Delhi 110077', 'India', 'Dwarka', 'New Delhi', 'Delhi', 110077, 'India', '2015-11-25 16:20:34'),
(46, '', 0, '28.5750699', '77.0709587', 'A49', 'Block A, Sector 8 Dwarka, Dwarka', 'New Delhi, Delhi 110077', 'India', 'Dwarka', 'New Delhi', 'Delhi', 110077, 'India', '2015-12-02 17:26:42'),
(47, '', 0, '28.4787481', '77.1939062', 'cskm public school', 'Satbari, Delhi, India', '', '', '', 'Delhi', '', 0, '', '2015-12-07 12:43:16'),
(48, '', 0, '28.4787481', '77.1939062', 'cskm public school ', 'Satbari, Delhi, India', '', '', '', 'Delhi', '', 0, '', '2015-12-07 13:10:15'),
(49, '', 0, '28.716631', '77.13934689999999', 'c 1/ 15 , prashant vihar, ', 'Prashant Vihar, Outer Ring Road, Rohini, Delhi, India', '', '', '', 'Delhi', '', 0, '', '2015-12-07 13:42:48'),
(50, '', 0, '28.5995805', '77.0762781', 'RZG-60, Mandir Marg', 'Block G, Mahavir Enclave I, Vijay Enclave, Mahavir Enclave', 'New Delhi, Delhi 110046', 'India', 'Mahavir Enclave', 'New Delhi', 'Delhi', 110046, 'India', '2015-12-12 08:52:17'),
(51, 'office', 5, '0.0', '0.0', '', '', '', '', '', '', '', 0, '', '2015-12-16 07:29:36'),
(52, '', 0, '28.5921401', '77.0460481', 'a 49 , 3rd floor, sector 8, near shamrock public school, dwarka, new delhi ', 'Dwarka, Delhi, India', 'New Delhi, Delhi 110075', 'India', 'Pocket 8, Block B', 'Delhi', 'Delhi', 110075, 'India', '2015-12-16 14:22:24'),
(53, '', 0, '28.5750699', '77.0709587', 'A49', 'Block A, Sector 8 Dwarka, Dwarka', 'New Delhi, Delhi 110077', 'India', 'Dwarka', 'New Delhi', 'Delhi', 110077, 'India', '2016-01-13 10:40:29'),
(54, 'Office', 4, '28.5750699', '77.0709587', 'A49', 'Block A, Sector 8 Dwarka, Dwarka', 'New Delhi, Delhi 110077', 'India', 'Dwarka', 'New Delhi', 'Delhi', 110077, 'India', '2016-01-13 10:43:55'),
(55, '', 0, '28.5750699', '77.0709587', 'A49', 'Block A, Sector 8 Dwarka, Dwarka', 'New Delhi, Delhi 110077', 'India', 'Dwarka', 'New Delhi', 'Delhi', 110077, 'India', '2016-01-13 13:21:07');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accepted_request`
--
ALTER TABLE `accepted_request`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`recNo`);

--
-- Indexes for table `api_key`
--
ALTER TABLE `api_key`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `area_serviced`
--
ALTER TABLE `area_serviced`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `business_type`
--
ALTER TABLE `business_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `declined_request`
--
ALTER TABLE `declined_request`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `driver_price`
--
ALTER TABLE `driver_price`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employee`
--
ALTER TABLE `employee`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `localities`
--
ALTER TABLE `localities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `otp`
--
ALTER TABLE `otp`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pricing`
--
ALTER TABLE `pricing`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `service`
--
ALTER TABLE `service`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `service_level`
--
ALTER TABLE `service_level`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `service_provider`
--
ALTER TABLE `service_provider`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `service_provider_regions`
--
ALTER TABLE `service_provider_regions`
  ADD PRIMARY KEY (`service_provider_region_id`);

--
-- Indexes for table `service_provider_regions_arae_covered`
--
ALTER TABLE `service_provider_regions_arae_covered`
  ADD PRIMARY KEY (`s_area_covered_id`);

--
-- Indexes for table `service_provider_regions_areas`
--
ALTER TABLE `service_provider_regions_areas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `service_rating`
--
ALTER TABLE `service_rating`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `service_region`
--
ALTER TABLE `service_region`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table ` service_region_zones`
--
ALTER TABLE ` service_region_zones`
  ADD PRIMARY KEY (`region_zone_id`);

--
-- Indexes for table `service_request`
--
ALTER TABLE `service_request`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `service_subscription`
--
ALTER TABLE `service_subscription`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `service_timing`
--
ALTER TABLE `service_timing`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `terms`
--
ALTER TABLE `terms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `testing_employee_profile`
--
ALTER TABLE `testing_employee_profile`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_address`
--
ALTER TABLE `user_address`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accepted_request`
--
ALTER TABLE `accepted_request`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `recNo` int(14) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
--
-- AUTO_INCREMENT for table `api_key`
--
ALTER TABLE `api_key`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `area_serviced`
--
ALTER TABLE `area_serviced`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `business_type`
--
ALTER TABLE `business_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `cities`
--
ALTER TABLE `cities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `declined_request`
--
ALTER TABLE `declined_request`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `driver_price`
--
ALTER TABLE `driver_price`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `employee`
--
ALTER TABLE `employee`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=128;
--
-- AUTO_INCREMENT for table `localities`
--
ALTER TABLE `localities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `otp`
--
ALTER TABLE `otp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `pricing`
--
ALTER TABLE `pricing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `service`
--
ALTER TABLE `service`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `service_level`
--
ALTER TABLE `service_level`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `service_provider`
--
ALTER TABLE `service_provider`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;
--
-- AUTO_INCREMENT for table `service_provider_regions`
--
ALTER TABLE `service_provider_regions`
  MODIFY `service_provider_region_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;
--
-- AUTO_INCREMENT for table `service_provider_regions_arae_covered`
--
ALTER TABLE `service_provider_regions_arae_covered`
  MODIFY `s_area_covered_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `service_provider_regions_areas`
--
ALTER TABLE `service_provider_regions_areas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `service_rating`
--
ALTER TABLE `service_rating`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
--
-- AUTO_INCREMENT for table `service_region`
--
ALTER TABLE `service_region`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table ` service_region_zones`
--
ALTER TABLE ` service_region_zones`
  MODIFY `region_zone_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `service_request`
--
ALTER TABLE `service_request`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;
--
-- AUTO_INCREMENT for table `service_subscription`
--
ALTER TABLE `service_subscription`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `service_timing`
--
ALTER TABLE `service_timing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `terms`
--
ALTER TABLE `terms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `testing_employee_profile`
--
ALTER TABLE `testing_employee_profile`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;
--
-- AUTO_INCREMENT for table `user_address`
--
ALTER TABLE `user_address`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
