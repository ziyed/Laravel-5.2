-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Aug 23, 2016 at 09:12 AM
-- Server version: 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `laravel`
--

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE IF NOT EXISTS `migrations` (
  `migration` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`migration`, `batch`) VALUES
('2014_10_12_000000_create_users_table', 1),
('2014_10_12_100000_create_password_resets_table', 1),
('2016_08_19_061825_roles', 2),
('2016_08_19_063323_role_id', 3);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE IF NOT EXISTS `password_resets` (
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL,
  KEY `password_resets_email_index` (`email`),
  KEY `password_resets_token_index` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Admin', NULL, NULL),
(2, 'User', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `role_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=10 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `remember_token`, `created_at`, `updated_at`, `role_id`) VALUES
(1, 'ziyed', 'ziyed@pwcart.com', '$2y$10$a.sUEDTsQAJgXfA02CpsgOwGc4fnT6F4Qu6rS1AafpRXrAkK5NkIm', 'JJmIHQsi6MZzMYZ6Rho8iVh7PWFE3CAIRr65EoUJR9pMgFc1MryZhwmpRLap', '2016-08-10 03:45:37', '2016-08-19 00:48:58', 2),
(2, 'sadmin', 'ziyed@gmail.com', '$2y$10$a.sUEDTsQAJgXfA02CpsgOwGc4fnT6F4Qu6rS1AafpRXrAkK5NkIm', 'kJA3GxzmPBz4PSMfFwPqvGOQQP5NAnb7eb9rS0DJYeuvpD1MpRocWRNdbAlr', '2016-08-11 01:06:06', '2016-08-19 06:39:43', 1),
(4, 'zakir', 'zakir@gmail.com', '$2y$10$mgLtCEnnt1o6y806AtJT1u6hsymmPZ.5SMSzWsEFhVzlaqUTKUD9e', 'TPoku86KKupS3UkMEdKk9VXdODe2sdTsOmTjz5Qn4cR8Yd5BgWL6qbU3TEqU', '2016-08-19 00:54:27', '2016-08-19 00:55:00', 2),
(7, 'admin', 'admin@pwcart.com', '$2y$10$KF1Xq/o9c/.bghcfDvNrIuakPLKEamLBKIiaxIGenj5xc3qGDLPCS', 'ovqNBKudTs3LPl4vq694pCXAoKg6nCLZzgVsOg6Kt342wOTR8pAvsPZBKvCM', '2016-08-19 05:16:41', '2016-08-19 05:45:10', 2),
(9, 'jack', 'jack@pwcart.com', '$2y$10$ge0aM04RPZIEy36dZnQxUO8xhirlPVDcRsBCLHcUWTPUwIAvGnALO', NULL, '2016-08-19 05:19:07', '2016-08-19 05:19:07', 1);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
