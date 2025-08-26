-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 21, 2025 at 03:42 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dccdocumentmanagementsystem`
--

-- --------------------------------------------------------

--
-- Table structure for table `base_folders`
--

CREATE TABLE `base_folders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('spira_cache_spatie.permission.cache', 'a:3:{s:5:\"alias\";a:4:{s:1:\"a\";s:2:\"id\";s:1:\"b\";s:4:\"name\";s:1:\"c\";s:10:\"guard_name\";s:1:\"r\";s:5:\"roles\";}s:11:\"permissions\";a:28:{i:0;a:4:{s:1:\"a\";i:1;s:1:\"b\";s:18:\"create basefolders\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:1;a:4:{s:1:\"a\";i:2;s:1:\"b\";s:16:\"edit basefolders\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:2;a:4:{s:1:\"a\";i:3;s:1:\"b\";s:18:\"delete basefolders\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:3;a:4:{s:1:\"a\";i:4;s:1:\"b\";s:16:\"view basefolders\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:4;a:4:{s:1:\"a\";i:5;s:1:\"b\";s:28:\"submit document for approval\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:5;a:4:{s:1:\"a\";i:6;s:1:\"b\";s:29:\"approve document registration\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:6;a:4:{s:1:\"a\";i:7;s:1:\"b\";s:28:\"reject document registration\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:7;a:4:{s:1:\"a\";i:8;s:1:\"b\";s:29:\"require revision for document\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:8;a:4:{s:1:\"a\";i:9;s:1:\"b\";s:28:\"withdraw document submission\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:9;a:4:{s:1:\"a\";i:10;s:1:\"b\";s:35:\"view pending document registrations\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:10;a:4:{s:1:\"a\";i:11;s:1:\"b\";s:31:\"view all document registrations\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:11;a:4:{s:1:\"a\";i:12;s:1:\"b\";s:34:\"edit document registration details\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:12;a:4:{s:1:\"a\";i:13;s:1:\"b\";s:35:\"bulk approve document registrations\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:13;a:4:{s:1:\"a\";i:14;s:1:\"b\";s:34:\"bulk reject document registrations\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:14;a:4:{s:1:\"a\";i:15;s:1:\"b\";s:26:\"reassign document approver\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:15;a:4:{s:1:\"a\";i:16;s:1:\"b\";s:25:\"override approval process\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:16;a:4:{s:1:\"a\";i:17;s:1:\"b\";s:12:\"view folders\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:17;a:4:{s:1:\"a\";i:18;s:1:\"b\";s:14:\"create folders\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:18;a:4:{s:1:\"a\";i:19;s:1:\"b\";s:12:\"edit folders\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:19;a:4:{s:1:\"a\";i:20;s:1:\"b\";s:14:\"delete folders\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:20;a:4:{s:1:\"a\";i:21;s:1:\"b\";s:13:\"share folders\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:21;a:4:{s:1:\"a\";i:22;s:1:\"b\";s:12:\"view reports\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:22;a:4:{s:1:\"a\";i:23;s:1:\"b\";s:14:\"view dashboard\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:23;a:4:{s:1:\"a\";i:24;s:1:\"b\";s:12:\"manage users\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:24;a:4:{s:1:\"a\";i:25;s:1:\"b\";s:12:\"manage roles\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:25;a:4:{s:1:\"a\";i:26;s:1:\"b\";s:15:\"view audit logs\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:26;a:4:{s:1:\"a\";i:27;s:1:\"b\";s:13:\"backup system\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:27;a:4:{s:1:\"a\";i:28;s:1:\"b\";s:14:\"restore system\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}}s:5:\"roles\";a:3:{i:0;a:3:{s:1:\"a\";i:1;s:1:\"b\";s:10:\"SuperAdmin\";s:1:\"c\";s:3:\"web\";}i:1;a:3:{s:1:\"a\";i:2;s:1:\"b\";s:8:\"DCCAdmin\";s:1:\"c\";s:3:\"web\";}i:2;a:3:{s:1:\"a\";i:3;s:1:\"b\";s:9:\"BasicRole\";s:1:\"c\";s:3:\"web\";}}}', 1755826787);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `folder_id` bigint(20) UNSIGNED DEFAULT NULL,
  `base_folder_id` bigint(20) UNSIGNED DEFAULT NULL,
  `filename` varchar(255) NOT NULL,
  `original_filename` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` varchar(255) NOT NULL,
  `file_size` varchar(255) NOT NULL,
  `mime_type` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `meta_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`meta_data`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `document_registration_entry_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `document_registration_entries`
--

CREATE TABLE `document_registration_entries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `document_title` varchar(255) NOT NULL,
  `document_no` varchar(255) NOT NULL,
  `revision_no` varchar(255) NOT NULL,
  `device_name` varchar(255) NOT NULL,
  `originator_name` varchar(255) NOT NULL,
  `customer` varchar(255) NOT NULL,
  `remarks` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `original_filename` varchar(255) DEFAULT NULL,
  `mime_type` varchar(255) DEFAULT NULL,
  `file_size` bigint(20) UNSIGNED DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `submitted_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `submitted_at` timestamp NULL DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `document_registration_entry_files`
--

CREATE TABLE `document_registration_entry_files` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `entry_id` bigint(20) UNSIGNED NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `original_filename` varchar(255) NOT NULL,
  `mime_type` varchar(255) DEFAULT NULL,
  `file_size` bigint(20) UNSIGNED DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `rejection_reason` text DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `folders`
--

CREATE TABLE `folders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `base_folder_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_05_30_051238_create_base_folders_table', 1),
(5, '2025_06_26_052619_create_permission_tables', 1),
(6, '2025_06_26_053219_create_folders_table', 1),
(7, '2025_06_26_053401_create_documents_table', 1),
(8, '2025_07_10_014537_create_department_in_folders_table', 1),
(9, '2025_07_10_022421_add_department_to_documents_table', 1),
(10, '2025_07_15_072403_create_document_registration_entries_table', 1),
(11, '2025_07_15_072519_add_fk_document_and_registry', 1),
(12, '2025_07_22_005930_add_document_related_colums_to_entries', 1),
(13, '2025_07_29_020254_create_document_registration_entry_files_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 367),
(2, 'App\\Models\\User', 64),
(3, 'App\\Models\\User', 1),
(3, 'App\\Models\\User', 4),
(3, 'App\\Models\\User', 5),
(3, 'App\\Models\\User', 6),
(3, 'App\\Models\\User', 7),
(3, 'App\\Models\\User', 8),
(3, 'App\\Models\\User', 9),
(3, 'App\\Models\\User', 10),
(3, 'App\\Models\\User', 11),
(3, 'App\\Models\\User', 12),
(3, 'App\\Models\\User', 13),
(3, 'App\\Models\\User', 14),
(3, 'App\\Models\\User', 15),
(3, 'App\\Models\\User', 16),
(3, 'App\\Models\\User', 17),
(3, 'App\\Models\\User', 18),
(3, 'App\\Models\\User', 19),
(3, 'App\\Models\\User', 20),
(3, 'App\\Models\\User', 21),
(3, 'App\\Models\\User', 22),
(3, 'App\\Models\\User', 23),
(3, 'App\\Models\\User', 24),
(3, 'App\\Models\\User', 25),
(3, 'App\\Models\\User', 26),
(3, 'App\\Models\\User', 27),
(3, 'App\\Models\\User', 28),
(3, 'App\\Models\\User', 29),
(3, 'App\\Models\\User', 30),
(3, 'App\\Models\\User', 31),
(3, 'App\\Models\\User', 32),
(3, 'App\\Models\\User', 33),
(3, 'App\\Models\\User', 34),
(3, 'App\\Models\\User', 35),
(3, 'App\\Models\\User', 36),
(3, 'App\\Models\\User', 37),
(3, 'App\\Models\\User', 38),
(3, 'App\\Models\\User', 39),
(3, 'App\\Models\\User', 40),
(3, 'App\\Models\\User', 41),
(3, 'App\\Models\\User', 42),
(3, 'App\\Models\\User', 43),
(3, 'App\\Models\\User', 44),
(3, 'App\\Models\\User', 45),
(3, 'App\\Models\\User', 46),
(3, 'App\\Models\\User', 47),
(3, 'App\\Models\\User', 48),
(3, 'App\\Models\\User', 49),
(3, 'App\\Models\\User', 50),
(3, 'App\\Models\\User', 51),
(3, 'App\\Models\\User', 52),
(3, 'App\\Models\\User', 53),
(3, 'App\\Models\\User', 54),
(3, 'App\\Models\\User', 55),
(3, 'App\\Models\\User', 56),
(3, 'App\\Models\\User', 57),
(3, 'App\\Models\\User', 58),
(3, 'App\\Models\\User', 59),
(3, 'App\\Models\\User', 60),
(3, 'App\\Models\\User', 61),
(3, 'App\\Models\\User', 62),
(3, 'App\\Models\\User', 63),
(3, 'App\\Models\\User', 65),
(3, 'App\\Models\\User', 66),
(3, 'App\\Models\\User', 67),
(3, 'App\\Models\\User', 68),
(3, 'App\\Models\\User', 69),
(3, 'App\\Models\\User', 70),
(3, 'App\\Models\\User', 71),
(3, 'App\\Models\\User', 72),
(3, 'App\\Models\\User', 73),
(3, 'App\\Models\\User', 74),
(3, 'App\\Models\\User', 75),
(3, 'App\\Models\\User', 76),
(3, 'App\\Models\\User', 77),
(3, 'App\\Models\\User', 78),
(3, 'App\\Models\\User', 79),
(3, 'App\\Models\\User', 80),
(3, 'App\\Models\\User', 81),
(3, 'App\\Models\\User', 82),
(3, 'App\\Models\\User', 83),
(3, 'App\\Models\\User', 84),
(3, 'App\\Models\\User', 85),
(3, 'App\\Models\\User', 86),
(3, 'App\\Models\\User', 87),
(3, 'App\\Models\\User', 88),
(3, 'App\\Models\\User', 89),
(3, 'App\\Models\\User', 90),
(3, 'App\\Models\\User', 91),
(3, 'App\\Models\\User', 93),
(3, 'App\\Models\\User', 94),
(3, 'App\\Models\\User', 95),
(3, 'App\\Models\\User', 96),
(3, 'App\\Models\\User', 97),
(3, 'App\\Models\\User', 98),
(3, 'App\\Models\\User', 99),
(3, 'App\\Models\\User', 100),
(3, 'App\\Models\\User', 101),
(3, 'App\\Models\\User', 102),
(3, 'App\\Models\\User', 103),
(3, 'App\\Models\\User', 104),
(3, 'App\\Models\\User', 105),
(3, 'App\\Models\\User', 106),
(3, 'App\\Models\\User', 107),
(3, 'App\\Models\\User', 108),
(3, 'App\\Models\\User', 109),
(3, 'App\\Models\\User', 110),
(3, 'App\\Models\\User', 111),
(3, 'App\\Models\\User', 113),
(3, 'App\\Models\\User', 115),
(3, 'App\\Models\\User', 116),
(3, 'App\\Models\\User', 117),
(3, 'App\\Models\\User', 118),
(3, 'App\\Models\\User', 119),
(3, 'App\\Models\\User', 120),
(3, 'App\\Models\\User', 121),
(3, 'App\\Models\\User', 344),
(3, 'App\\Models\\User', 345),
(3, 'App\\Models\\User', 346),
(3, 'App\\Models\\User', 347),
(3, 'App\\Models\\User', 348),
(3, 'App\\Models\\User', 349),
(3, 'App\\Models\\User', 353),
(3, 'App\\Models\\User', 356),
(3, 'App\\Models\\User', 359),
(3, 'App\\Models\\User', 360),
(3, 'App\\Models\\User', 363),
(3, 'App\\Models\\User', 364),
(3, 'App\\Models\\User', 365),
(3, 'App\\Models\\User', 368),
(3, 'App\\Models\\User', 369),
(3, 'App\\Models\\User', 370),
(3, 'App\\Models\\User', 371),
(3, 'App\\Models\\User', 372),
(3, 'App\\Models\\User', 373),
(3, 'App\\Models\\User', 374),
(3, 'App\\Models\\User', 375),
(3, 'App\\Models\\User', 377),
(3, 'App\\Models\\User', 378),
(3, 'App\\Models\\User', 379);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'create basefolders', 'web', '2025-08-20 17:31:45', '2025-08-20 17:31:45'),
(2, 'edit basefolders', 'web', '2025-08-20 17:31:45', '2025-08-20 17:31:45'),
(3, 'delete basefolders', 'web', '2025-08-20 17:31:45', '2025-08-20 17:31:45'),
(4, 'view basefolders', 'web', '2025-08-20 17:31:45', '2025-08-20 17:31:45'),
(5, 'submit document for approval', 'web', '2025-08-20 17:31:46', '2025-08-20 17:31:46'),
(6, 'approve document registration', 'web', '2025-08-20 17:31:46', '2025-08-20 17:31:46'),
(7, 'reject document registration', 'web', '2025-08-20 17:31:46', '2025-08-20 17:31:46'),
(8, 'require revision for document', 'web', '2025-08-20 17:31:46', '2025-08-20 17:31:46'),
(9, 'withdraw document submission', 'web', '2025-08-20 17:31:46', '2025-08-20 17:31:46'),
(10, 'view pending document registrations', 'web', '2025-08-20 17:31:46', '2025-08-20 17:31:46'),
(11, 'view all document registrations', 'web', '2025-08-20 17:31:46', '2025-08-20 17:31:46'),
(12, 'edit document registration details', 'web', '2025-08-20 17:31:46', '2025-08-20 17:31:46'),
(13, 'bulk approve document registrations', 'web', '2025-08-20 17:31:46', '2025-08-20 17:31:46'),
(14, 'bulk reject document registrations', 'web', '2025-08-20 17:31:46', '2025-08-20 17:31:46'),
(15, 'reassign document approver', 'web', '2025-08-20 17:31:46', '2025-08-20 17:31:46'),
(16, 'override approval process', 'web', '2025-08-20 17:31:46', '2025-08-20 17:31:46'),
(17, 'view folders', 'web', '2025-08-20 17:31:46', '2025-08-20 17:31:46'),
(18, 'create folders', 'web', '2025-08-20 17:31:46', '2025-08-20 17:31:46'),
(19, 'edit folders', 'web', '2025-08-20 17:31:46', '2025-08-20 17:31:46'),
(20, 'delete folders', 'web', '2025-08-20 17:31:46', '2025-08-20 17:31:46'),
(21, 'share folders', 'web', '2025-08-20 17:31:46', '2025-08-20 17:31:46'),
(22, 'view reports', 'web', '2025-08-20 17:31:46', '2025-08-20 17:31:46'),
(23, 'view dashboard', 'web', '2025-08-20 17:31:46', '2025-08-20 17:31:46'),
(24, 'manage users', 'web', '2025-08-20 17:31:46', '2025-08-20 17:31:46'),
(25, 'manage roles', 'web', '2025-08-20 17:31:46', '2025-08-20 17:31:46'),
(26, 'view audit logs', 'web', '2025-08-20 17:31:46', '2025-08-20 17:31:46'),
(27, 'backup system', 'web', '2025-08-20 17:31:46', '2025-08-20 17:31:46'),
(28, 'restore system', 'web', '2025-08-20 17:31:46', '2025-08-20 17:31:46');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'SuperAdmin', 'web', '2025-08-20 17:32:18', '2025-08-20 17:32:18'),
(2, 'DCCAdmin', 'web', '2025-08-20 17:32:18', '2025-08-20 17:32:18'),
(3, 'BasicRole', 'web', '2025-08-20 17:39:37', '2025-08-20 17:39:37');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(1, 2),
(2, 1),
(2, 2),
(3, 1),
(3, 2),
(4, 1),
(4, 2),
(5, 1),
(5, 2),
(6, 1),
(6, 2),
(7, 1),
(7, 2),
(8, 1),
(8, 2),
(9, 1),
(9, 2),
(10, 1),
(10, 2),
(11, 1),
(11, 2),
(12, 1),
(12, 2),
(13, 1),
(13, 2),
(14, 1),
(14, 2),
(15, 1),
(15, 2),
(16, 1),
(16, 2),
(17, 1),
(17, 2),
(17, 3),
(18, 1),
(18, 2),
(19, 1),
(19, 2),
(20, 1),
(20, 2),
(21, 1),
(21, 2),
(22, 1),
(22, 2),
(23, 1),
(23, 2),
(24, 1),
(24, 2),
(25, 1),
(25, 2),
(26, 1),
(26, 2),
(27, 1),
(27, 2),
(28, 1),
(28, 2);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('CTP9U7vvx8hPVMKtAcNFVVudDBZkXAKd6WfIlPzO', 367, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoic1FWV0tCNEVaV2ZORGhSekpoT3VUQWZuTnlXTW0yaGlFUWZHenptTiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzM6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi91c2VycyI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjM2NztzOjQ6ImF1dGgiO2E6MTp7czoyMToicGFzc3dvcmRfY29uZmlybWVkX2F0IjtpOjE3NTU3NDAwMzE7fX0=', 1755740387);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_no` varchar(15) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(60) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `middlename` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `address` text NOT NULL,
  `birthdate` date NOT NULL,
  `contact_info` varchar(100) NOT NULL,
  `gender` varchar(10) NOT NULL,
  `datehired` date DEFAULT NULL,
  `profile_image` varchar(200) NOT NULL,
  `created_on` date NOT NULL,
  `barcode` varchar(60) NOT NULL,
  `email` varchar(60) DEFAULT NULL,
  `separationdate` date DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `employee_no`, `username`, `password`, `firstname`, `middlename`, `lastname`, `address`, `birthdate`, `contact_info`, `gender`, `datehired`, `profile_image`, `created_on`, `barcode`, `email`, `separationdate`, `email_verified_at`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, '287', 'marksiaga', '$2y$10$n8emDUvVAZcWKDg.OAEwO.xOAwnH/Gk4zvoKth7AEmoXKAwdLkZJu', 'Mark Anthony', 'Palacpac', 'Siaga', 'CERIS1', '1978-03-01', '9190916730', 'Male', '2022-08-22', 'MarkAPS.jpg', '0000-00-00', '', 'markas@smartprobegroup.com', '0000-00-00', NULL, NULL, NULL, NULL),
(4, '0', 'RonaldSamaniego', '$2y$10$hk43YJoWjX/0kQwC/bJANO.eF4zMHAhCOxbva5Bpz.F...', 'Ronald', 'Camacho', 'Samaniego', 'SPI', '1955-09-18', '9178543382', 'Male', '2016-01-30', 'RonnieS.png', '0000-00-00', '0RONALCSAM', 'ronnies@smartprobegroup.com', '0000-00-00', NULL, NULL, NULL, NULL),
(5, '18', 'JhedRitual', '$2y$10$dVymjQbCWS1KbGxvS5uI2.3XKP.DZGUOXtrvsbdFPpzORlaO0Vh3K', 'Jennifer', 'Pagkalinawan', 'Ritual', 'SPI', '1988-02-15', '9328646111', 'Female', '2010-03-01', 'JenniferR.png', '0000-00-00', '18JENNIPRIT', 'jhedr@smartprobegroup.com', '0000-00-00', NULL, NULL, NULL, NULL),
(6, '20', 'jeanrosantina', '$2y$10$vNJIcpsZgEk3S1exahmeteRRp5KMnIXKIaxdgr15fF.wTD1pVojK.', 'Merry Jean', 'Gonzales', 'Rosantina', 'SPI', '1983-11-24', '9205183830', 'Female', '2009-11-05', 'JeanR.png', '0000-00-00', '20MERRYGROS', 'maryjeanr@smartprobegroup.com', '2025-02-04', NULL, NULL, NULL, NULL),
(7, '42', 'AllieCyreneGaerlan', '$2y$10$6jClxbPMF8QtLn/Cmba84.RANP64WaR3B2/aodCI/o06rTLgDi1GO', 'Allie Cyrene', 'San Juan', 'Gaerlan', 'SPI', '1990-02-04', '9434671016', 'Female', '2010-04-07', 'AllieG.png', '0000-00-00', '42ALLIESGAE', 'allieg@smartprobegroup.com', '0000-00-00', NULL, NULL, NULL, NULL),
(8, '63', 'CristianFabian', '$2y$10$tWLGVYUoOV0eX/PodEyUveJ8ykNxLfgUw4nSJHF1vJpM5HsyfqZp.', 'Cristian', 'Villa', 'Fabian', 'SPI', '1985-07-12', '9951367166', 'Male', '2011-02-23', 'CristianF.png', '0000-00-00', '63CRISTVFAB', 'ianf@smartprobegroup.com', '0000-00-00', NULL, NULL, NULL, NULL),
(9, '66', 'AntoninoLastimosa', '$2y$10$xIeaPRxqjf2B6Mfxzx22GOXWbwj5ULi0kAwYMz7U78RcgvhcSr5XS', 'Antonino', 'Hernandez', 'Lastimosa', 'SPI', '1969-12-28', '9328635043', 'Male', '2011-03-01', 'AntoninoL.png', '0000-00-00', '66ANTONHLAS', 'tontonl@smartprobegroup.com', '0000-00-00', NULL, NULL, NULL, NULL),
(10, '69', 'EugeneBaquir', '$2y$10$ya5q/kthb5xACTYdCJwF5es8whGh9RNNmMLzFBVuIZH8Bulsr0xkS', 'Eugene', 'Albaladejo', 'Baquir', 'SPI', '1969-12-15', '9178923703', 'Male', '2011-04-15', 'EugeneB.png', '0000-00-00', '69EUGENABAQ', 'geneb@smartprobegroup.com', '0000-00-00', NULL, NULL, NULL, NULL),
(11, '77', 'Edward Atienza', '$2y$10$03i0psbqz5tMRaeE0n1oQuNfCxnA.HVynkNEmK6wDyXfZHddTf7KG', 'Edward', 'Abarnas', 'Atienza', 'SPI', '1980-08-02', '9175934286', 'Male', '2011-10-11', 'EdwardA.png', '0000-00-00', '77EDWARAATI', 'edwarda@smartprobegroup.com', '0000-00-00', NULL, NULL, NULL, NULL),
(12, '92', 'HeroMax', '$2y$10$mW63YATm.gMizUYdRca09OtPTAq.o/D6oGL4vxgmnpOynZxWgHkJO', 'Bayani', 'Guevarra', 'Maxino', 'SPI', '1976-07-11', '9159973417', 'Male', '2012-05-22', 'BayaniM.png', '0000-00-00', '92BAYANGMAX', 'herom@smartprobegroup.com', '0000-00-00', NULL, NULL, NULL, NULL),
(13, '108', 'Ehsan', '$2y$10$dBEoyV2TAuCQo9GTp0Dny.BS/2FP7KainEI2JLEUYBZ1HiiV6mvpe', 'Ehsan', '', 'Hosseinzadeh Rooznamehchy', 'SPI', '1983-05-23', '9177021362', 'Male', '2013-04-29', 'EhsanR.png', '0000-00-00', '108EHSANHOS', 'ehsanhr@smartprobegroup.com', '0000-00-00', NULL, NULL, NULL, NULL),
(14, '123', 'GabrielGarcia', '$2y$10$ZCuGVHAqz7s4w5zQPrcxjON0r6M0.YS3B4NWJpmSmd.RONY0jq0Q.', 'Gabriel Joseph', 'Enriquez', 'Garcia', 'SPI', '1992-02-12', '9198844447', 'Male', '2014-07-24', 'GabrielG.png', '0000-00-00', '123GABRIEGAR', 'gabjg@smartprobegroup.com', '0000-00-00', NULL, NULL, NULL, NULL),
(15, '128', 'AbigailTe', '$2y$10$aWztJjpgMlEnwEokxOIm/e4.F0j6icwGfXD4IgMBq.Mv3xa1ci9TW', 'Abigail', 'Rollo', 'Te', 'SPI', '1989-04-26', '9083243666', 'Female', '2014-08-26', 'AbigailT.png', '0000-00-00', '128ABIGARTE', 'abigails@smartprobegroup.com', '0000-00-00', NULL, NULL, NULL, NULL),
(16, '134', 'MarkVillanueva', '$2y$10$VRQ9ZE16i.voOIHyAYOVLeQn.kxsw9R6zy0futTotsKKMU4wrbxpe', 'Mark', 'Mandap', 'Villanueva', 'SPI', '1985-03-19', '9192782965', 'Male', '2015-03-01', 'MarkV.png', '0000-00-00', '134MARKMVIL', 'mark.process@smartprobegroup.com', '2025-07-31', NULL, NULL, NULL, NULL),
(17, '140', 'Bet-BetCesario', '$2y$10$U7d3y3JaVI.Yww45XURmtOMyeD89xNfqVYaxDByD5c.aPlMZ.Ncq.', 'Bet-Bet', 'Posadas', 'Cesario', 'SPI', '1992-03-15', '9474575259', 'Female', '2015-01-26', 'BetbetC.png', '0000-00-00', '140BET-BPCES', 'spi_logistics@smartprobegroup.com', '0000-00-00', NULL, NULL, NULL, NULL),
(18, '143', 'MilletContreras', '$2y$10$V13pm9KNwW.RO1Dpe.FXjekcofI90l45tj.pkq3I49RabsecqYQ16', 'Millet', 'Mendoza', 'Contreras', 'SPI', '1982-11-10', '9305617611', 'Female', '2015-03-09', 'MilletC.png', '0000-00-00', '143MILLEMCON', 'millettem@smartprobegroup.com', '0000-00-00', NULL, NULL, NULL, NULL),
(19, '150', 'JoyDonato', '$2y$10$hk43YJoWjX/0kQwC/bJANO.eF4zMHAhCOxbva5Bpz.F...', 'Joy', 'Lupanggo', 'Donato', 'SPI', '1975-01-10', '9989792037', 'Female', '2015-05-04', 'JoyD.png', '0000-00-00', '150JOYLDON', 'joyd@smartprobegroup.com', '0000-00-00', NULL, NULL, NULL, NULL),
(20, '138', 'JharoldPlacencia', '$2y$10$3IVk86JfhPugcTS3Df1zh.qu/Ureva.wmNMynN1OnDXR9bv6RjgPy', 'Joseph Harold', 'Bonbon', 'Placencia', 'SPI', '1993-02-17', '9063439874', 'Male', '2015-10-15', 'JharoldP.png', '0000-00-00', '138JOSEPBPLA', '', '2025-05-07', NULL, NULL, NULL, NULL),
(21, '159', 'JosephinePerez', '$2y$10$uxloUrLvMU9y7iNgpZr0W.wq.VevJMUm74p1pcjxQ.7utFHrB3Wf6', 'Josephine', 'Balba', 'Perez', 'SPI', '1968-04-15', '9322754186', 'Female', '2015-12-15', 'JosephineP.png', '0000-00-00', '159JOSEPBPER', '', '0000-00-00', NULL, NULL, NULL, NULL),
(22, '160', 'DanielFeliciano', '$2y$10$hk43YJoWjX/0kQwC/bJANO.eF4zMHAhCOxbva5Bpz.F...', 'Daniel', 'Genteroni', 'Feliciano', 'SPI', '1970-10-26', '9326646853', 'Male', '2015-12-15', 'thanossmile.jpg', '0000-00-00', '160DANIEGFEL', '', '0000-00-00', NULL, NULL, NULL, NULL),
(23, '161', 'JhenetFernandez', '$2y$10$Kk9An3af2CPhm/6KZFlzuuvAut2G28a/nlpeudUAvMcxMMJ2.nLX2', 'Jhenet', 'Luistro', 'Fernandez', 'SPI', '1992-05-10', '9094919449', 'Female', '2016-01-11', 'JhennethF.png', '0000-00-00', '161JHENELFER', 'jhenetbl@smartprobegroup.com', '0000-00-00', NULL, NULL, NULL, NULL),
(24, '168', 'SoteroCenciljr', '$2y$10$8vH3ERClswQuM0CE.nk4k.7PR5Fzd6mnX8D7ndUvg0lWm2JdMUW4G', 'Sotero Jr.', 'Oliveros', 'Cencil', 'SPI', '1977-09-23', '9975329163', 'Male', '2016-08-01', 'Sotero.png', '0000-00-00', '168SOTEROCEN', '', '0000-00-00', NULL, NULL, NULL, NULL),
(25, '178', 'JeanIrinco', '$2y$10$itwIOHFUzjqVjs1pHw3jseaWzGCB1rPxjdz4.fKwcuxxU0XUVzH1e', 'Jean', 'Maranan', 'Irinco', 'SPI', '1988-06-18', '9463424446', 'Female', '2017-04-17', 'JeanI.png', '0000-00-00', '178JEANMIRI', '', '2025-03-28', NULL, NULL, NULL, NULL),
(26, '179', 'RoilPolancos', '$2y$10$hk43YJoWjX/0kQwC/bJANO.eF4zMHAhCOxbva5Bpz.F...', 'Roil', 'Alvarez', 'Polancos', 'SPI', '1973-03-25', '9065679995', 'Male', '2017-04-17', 'Roil.png', '0000-00-00', '179ROILAPOL', '', '0000-00-00', NULL, NULL, NULL, NULL),
(27, '180', 'JovinaAycocho', '$2y$10$A2Ussot5IpbSJvYKa6Wek.GlShW1WXhOvk9a3z62InhYSdr6IymYW', 'Jovina', 'Mestiola', 'Aycocho', 'SPI', '1985-06-08', '9501354819', 'Female', '2017-04-24', 'JovinaA.png', '0000-00-00', '180JOVINMAYC', '', '0000-00-00', NULL, NULL, NULL, NULL),
(28, '181', 'MelissaMacatangay', '$2y$10$VUkQ9rQBatc1nUwJ5n6EoetejxHPBcLGCac9vf4qk6gajK/8R04W2', 'Melissa', 'Capio', 'Macatangay', 'SPI', '1985-09-19', '9087338660', 'Female', '2017-04-24', 'MelissaM.png', '0000-00-00', '181MELISCMAC', '', '0000-00-00', NULL, NULL, NULL, NULL),
(29, '182', 'Romano Rodriguez', '$2y$10$N0nfDc9qRevaJPAvQOCHme9MCEOr1iHQuhPtKY/XG0kT/rAYEqEze', 'Romano', 'Cazon', 'Rodriguez', 'SPI', '1884-08-04', '9053265086', 'Male', '2017-05-02', 'RomanoR.png', '0000-00-00', '182ROMANCROD', 'romer@smartprobegroup.com', '0000-00-00', NULL, NULL, NULL, NULL),
(30, '194', 'NeilgeneCasilang', '$2y$10$LdeHrh0SwH24AZzOoB/fD.UaTwMIWDgdnF6.pYby66wigTscJhtTS', 'Neilgene', 'Manalo', 'Casilang', 'SPI', '1983-12-09', '9563884509', 'Male', '2017-09-27', 'NeilgeneC.png', '0000-00-00', '194NEILGMCAS', '', '0000-00-00', NULL, NULL, NULL, NULL),
(31, '198', 'ReaEufemio', '$2y$10$jCBoua3v/pJdAoXf1Yr70.EiWMRzKN3IELr0kR9Y50qEnsE07dEoS', 'Rea', 'Eufemio', 'Delos Santos', 'SPI', '1998-05-26', '9072825469', 'Female', '2017-11-27', 'ReaD.jpg', '0000-00-00', '198REACEUF', '', '0000-00-00', NULL, NULL, NULL, NULL),
(32, '200', 'ArvinGagaoin', '$2y$10$jMJvxbkEEZSqNu4QitlEHeWyBJHtbTj17CoqruN8pntIBhwmqA6ki', 'Arvin', 'Bulawan', 'Gagaoin', 'SPI', '1984-06-11', '9186141604', 'Male', '2018-02-12', 'ArvinG.png', '0000-00-00', '200ARVINBGAG', 'arving@smartprobegroup.com', '0000-00-00', NULL, NULL, NULL, NULL),
(33, '201', 'RoldanDelosSantos', '$2y$10$hk43YJoWjX/0kQwC/bJANO.eF4zMHAhCOxbva5Bpz.F...', 'Roldan', 'Castillo', 'Delos Santos', 'SPI', '1987-04-18', '9471000934', 'Male', '2018-02-18', 'RoldanD.png', '0000-00-00', '201ROLDACDEL', '', '0000-00-00', NULL, NULL, NULL, NULL),
(34, '202', 'jasze', '$2y$10$SBKpgV0ZehEwhLguxpHPUOlfM.DY8KrXcLPKpoPXedmu9hNjuOemK', 'Jasper', 'Cordero', 'Espital', 'SPI', '1976-06-18', '9189251262', 'Male', '2018-03-01', 'JaszE.png', '0000-00-00', '202JASPECESP', 'jaspere@smartprobegroup.com', '0000-00-00', NULL, NULL, NULL, NULL),
(35, '207', 'CharleneSachiSaito', '$2y$10$hk43YJoWjX/0kQwC/bJANO.eF4zMHAhCOxbva5Bpz.F...', 'Charlene Sachi', 'Corpuz', 'Saito', 'SPI', '1994-04-12', '9069714703', 'Female', '2018-05-02', 'profile.jpg', '0000-00-00', '207CHARLCSAI', 'acctg@smartprobegroup.com', '2024-05-15', NULL, NULL, NULL, NULL),
(36, '208', 'JescaBertodazo', '$2y$10$1wzpggQofqYlwHnUmno7mOtkzWXmTMSyZQXqX9o6tRuNsv0Fy7Pay', 'Jesca Camille', 'Marzan', 'Bertodazo', 'SPI', '1988-08-23', '9061855060', 'Female', '2018-05-07', 'JescaC.jpg', '0000-00-00', '208JESCAMBER', 'dcc@smartprobegroup.com', '0000-00-00', NULL, NULL, NULL, NULL),
(37, '215', 'NikkiTatoy', '$2y$10$kyrefHU9ghXUZeS.YV/4f.Tadf6Nl.ZTe3qmmOUGMqwP2srQJlI6C', 'Louie Frances Monique', 'Crisostomo', 'Tatoy', 'SPI', '1985-10-07', '9774929582', 'Female', '2018-11-12', 'NikkiT.png', '0000-00-00', '215LOUIECTAT', 'nikkit@smartprobegroup.com', '0000-00-00', NULL, NULL, NULL, NULL),
(38, '216', 'JerlynLuistro', '$2y$10$qf7J3RTFrEWTAezcDhVU9u2SvNhCWjsoI6Z0aozF9ciPeHi3C3VZq', 'Jerlyn', 'Canatuan', 'Luistro', 'SPI', '1993-02-03', '9205006183', 'Female', '2019-01-19', 'JerlynL.png', '0000-00-00', '216JERLYCLUI', '', '0000-00-00', NULL, NULL, NULL, NULL),
(39, '217', 'AllysaAbila', '$2y$10$X8Wwl8R7XHzz8V63JEhCvOAUpRyiA/RNK8YsPztYri4OEYVzbBFAi', 'Allysa Mariejoy', 'Dimaano', 'Abila', 'SPI', '1996-02-29', '9753312819', 'Female', '2019-02-11', 'AllysaA.png', '0000-00-00', '217ALLYSDABI', 'allysaa@smartprobegroup.com', '0000-00-00', NULL, NULL, NULL, NULL),
(40, '221', 'RosanaLariosa', '$2y$10$us6wVlo.HnMJYIiRgjIXLuaahZHH2erpVQRWZvqgH4TLwgYQZB11q', 'Rosana', 'Fabregar', 'Lariosa', 'SPI', '1984-01-06', '9286071793', 'Female', '2019-05-27', 'RosanaL.png', '0000-00-00', '221ROSANFLAR', '', '0000-00-00', NULL, NULL, NULL, NULL),
(41, '222', 'PeterVizarra', '$2y$10$PgBZzTANUwV2x9fZnYvAlOiARYKxImGJhApRNK8NcsKWI8wBuHzem', 'Peter', 'Calderon', 'Vizarra', 'SPI', '1995-04-16', '9753052140', 'Male', '2019-06-10', 'PeterV.png', '0000-00-00', '222PETERCVIZ', 'peterv@smartprobegroup.com', '0000-00-00', NULL, NULL, NULL, NULL),
(42, '224', 'EstelitaPanghulan', '$2y$10$xPRxsJ7e4aPM2dU48gU9tOP6iis6ztS64Sst/ttuozt5Vu1EKsB/m', 'Estelita', 'Caubalejo', 'Panghulan', 'SPI', '1979-01-05', '9777936251', 'Female', '2019-01-19', 'TheletP.png', '0000-00-00', '224ESTELCPAN', 'estelitap@smartprobegroup.com', '0000-00-00', NULL, NULL, NULL, NULL),
(43, '225', 'JaminicaBlancaPerculeza', '$2y$10$hk43YJoWjX/0kQwC/bJANO.eF4zMHAhCOxbva5Bpz.F...', 'Jaminica Blanca', 'Nedula', 'Perculeza', 'SPI', '1997-04-21', '9264957856', 'Female', '2019-08-01', 'profile.jpg', '0000-00-00', '225JAMINNPER', 'hr@smartprobegroup.com', '2024-07-03', NULL, NULL, NULL, NULL),
(44, '226', 'MarkMagno', '$2y$10$OO5/kZSPxNf/Mwye3ePcW.DsJndyXg5mDUjqLMjsI2X/lTdjSwnSi', 'Mark Anthony', 'Cornelio', 'Magno', 'SPI', '1993-10-18', '9101414923', 'Male', '2019-09-30', 'MarkM.png', '0000-00-00', '226MARKACMAG', 'markm@smartprobegroup.com', '0000-00-00', NULL, NULL, NULL, NULL),
(45, '228', 'ReShaneGonzales', '$2y$10$Nh87IXHiT.JWXc0bU09XZuzybijJplgGNOE7fbeCzH9B1xukReg1e', 'Re-Shane', 'Mortel', 'Gonzales', 'SPI', '1989-08-14', '9276664641', 'Female', '2019-04-25', 'ReshaneG.png', '0000-00-00', '228RE-SHMGON', 'reshaneg@smartprobegroup.com', '0000-00-00', NULL, NULL, NULL, NULL),
(46, '230', 'VincentHeredia', '$2y$10$Dzq3M6FNSYk9CJQ6G3k.seHer1hdOJNV1rWmuB9iY/mL3jBF4lad6', 'Vincent', 'Castillo', 'Heredia', 'SPI', '1988-04-05', '9752187032', 'Male', '2019-11-25', 'VincentH.png', '0000-00-00', '230VINCECHER', '', '2024-09-20', NULL, NULL, NULL, NULL),
(47, '231', 'Mervinno', '$2y$10$HOFffPqnO4sz0RN0..wCguasd2oLYqnCe4a0JUL21wxxktBuad0Im', 'Mervin Noel', 'Marfori', 'Ocampo', 'SPI', '1992-12-01', '9354340810', 'Male', '2019-12-04', 'MervinO.png', '0000-00-00', '231MERVIMOCA', 'mervinno@smartprobegroup.com', '2025-03-31', NULL, NULL, NULL, NULL),
(48, '239', 'Ken', '$2y$10$oUbEYfEhv/VN0gLXgtMZkOe8g8h.t3u2//5u29HiR90w2IaJqXlSO', 'Kenneth', 'Escueta', 'Sanchez', 'SPI', '1987-05-26', '9563572370', 'Male', '2020-10-19', 'KennethS.png', '0000-00-00', '239KENNEESAN', 'kenneths@smartprobegroup.com', '0000-00-00', NULL, NULL, NULL, NULL),
(49, '240', 'RenelynDelmo', '$2y$10$.KPFE.54NO91NpTtJn9euO8NabNvhtFJoTIMhn2W1GsUhGNWEeiGa', 'Renelyn', 'Gega', 'Delmo', 'SPI', '1999-10-29', '9067155748', 'Female', '2020-11-03', 'RenalynD.png', '0000-00-00', '240RENELGDEL', '', '2025-07-31', NULL, NULL, NULL, NULL),
(50, '241', 'JeffAgno', '$2y$10$4AtGAc55OOze0jI3mlz0sO9bEYa4Bqf85LCshTO4mhaueyt1wV.tS', 'Jeff', 'Bueno', 'Agno', 'SPI', '1995-10-30', '9054090511', 'Male', '2020-12-01', 'JeffA.png', '0000-00-00', '241JEFFBAGN', 'jeffba@smartprobegroup.com', '0000-00-00', NULL, NULL, NULL, NULL),
(51, '242', 'MaryJoyLucido', '$2y$10$hk43YJoWjX/0kQwC/bJANO.eF4zMHAhCOxbva5Bpz.F...', 'Mary Joy', 'Lumbres', 'Lucido', 'SPI', '1982-07-01', '9274245469', 'Female', '2021-03-01', 'MJL.png', '0000-00-00', '242MARYJLLUC', 'maryjoyl@smartprobegroup.com', '2024-08-31', NULL, NULL, NULL, NULL),
(52, '245', 'WilliamLacaña', '$2y$10$hk43YJoWjX/0kQwC/bJANO.eF4zMHAhCOxbva5Bpz.F...', 'William', 'Ponio', 'Lacaña', 'SPI', '1982-07-01', '9237431773', 'Male', '2021-07-07', 'thanossmile.jpg', '0000-00-00', '245WILLIPLAC', '', '0000-00-00', NULL, NULL, NULL, NULL),
(53, '248', 'EvelynCabanda', '$2y$10$Tl1oWqsrTT1R90N/9NYlQOU7OOMLf.vngw7xVQU5Z548U2XDzQQhS', 'Evelyn', 'Panal', 'Cabanda', 'SPI', '1995-03-08', '9655460843', 'Female', '2021-08-17', 'EvelynC.jpg', '0000-00-00', '248EVELYPCAB', '', '0000-00-00', NULL, NULL, NULL, NULL),
(54, '254', 'LeanaCardona', '$2y$10$d4n2PB/hh.m4n8lwKFq2W.p0mW1tgDgQwP7U72tgXIgPn29/bO2mm', 'Leana', 'Lacal', 'Cardona', 'SPI', '1979-04-10', '9754201799', 'Female', '2021-11-12', 'LeanaC.png', '0000-00-00', '254LEANALCAR', '', '0000-00-00', NULL, NULL, NULL, NULL),
(55, '257', 'MyleneIbabao', '$2y$10$p2qTMeDJQiv6rF6q4cv79..rh4NszgE02HsGzQkVT/6ntNWgkwWjq', 'Mylene', 'Mestidio', 'Fernandez', 'SPI', '1986-09-12', '9551448167', 'Female', '2022-01-31', 'MyleneF.png', '0000-00-00', '257MYLENMFER', 'mylenef@smartprobegroup.com', '2025-07-17', NULL, NULL, NULL, NULL),
(56, '262', 'GinaAguirre', '$2y$10$dYShc7R/Qt7Fmfo3Pvm30uzDPAfDTLtMmygx/2xty4edD9WJwyFnG', 'Gina', 'Olivarez', 'Aguirre', 'SPI', '1983-02-28', '9633442772', 'Female', '2017-02-17', 'GinaA.png', '0000-00-00', '262GINAOAGU', '', '0000-00-00', NULL, NULL, NULL, NULL),
(57, '266', 'CristinaOrgeno', '$2y$10$iUw2Zs/o/XNc4tn/pyx5u.pIGS1SehMM8K5vbULNACZlLsX3fFDHK', 'Cristina', 'Alcoreza', 'Orgeno', 'SPI', '1990-09-21', '9286279407', 'Female', '2022-02-23', 'ChristinaO.png', '0000-00-00', '266CRISTAORG', '', '0000-00-00', NULL, NULL, NULL, NULL),
(58, '267', 'DarrenCortez', '$2y$10$O0bVa.1MK6mWzfPsQ9C5I.udnrEI/SuYsMmmGJg.BjXpa61pwB3ZS', 'Darren Paul', 'Tuazon', 'Cortez', 'SPI', '1995-08-06', '9074137765', 'Male', '2022-03-07', 'DarrenC.jpg', '0000-00-00', '267DARRETCOR', '', '0000-00-00', NULL, NULL, NULL, NULL),
(59, '268', 'JustinHei', '$2y$10$fyAoDccwl6EVDaFkzNytDu5gHLhSlSWsZM7xRQ/r1m4njJODqnUoe', 'Justin Hei', 'Matalog', 'Eusebio', 'SPI', '1998-02-16', '9435051252', 'Male', '2022-04-04', '1x1.png', '0000-00-00', '268JUSTIMEUS', 'justine@smartprobegroup.com', '2025-07-11', NULL, NULL, NULL, NULL),
(60, '269', 'RaizelEbonia', '$2y$10$uxAS0yPOX5S3tuV5Rl77ae0oLCaM4lmeOa31K84VHLktKi3ewvPr2', 'Raizel', 'Ramos', 'Ebonia', 'SPI', '1992-01-22', '9666938980', 'Female', '2022-04-05', 'RaizelE.png', '0000-00-00', '269RAIZEREBO', '', '0000-00-00', NULL, NULL, NULL, NULL),
(61, '271', 'MaryKateCarpio', '$2y$10$hk43YJoWjX/0kQwC/bJANO.eF4zMHAhCOxbva5Bpz.F...', 'Mary Kate', 'De Luna', 'Carpio', 'SPI', '1998-12-26', '', 'Female', '0000-00-00', 'profile.jpg', '0000-00-00', '271MARYKDCAR', 'katedl@smartprobegroup.com', '2024-01-01', NULL, NULL, NULL, NULL),
(62, '273', 'ArmandoMalabanan', '$2y$10$JErsh1bcOqCpHBs4mxwiI.rbG5mftD2BgNJXquy63GcZ9DNNVkosq', 'Armando', 'Bermas', 'Malabanan', 'SPI', '1983-05-31', '9993633129', 'Male', '2022-05-10', 'ArmandoM.png', '0000-00-00', '273ARMANBMAL', '', '0000-00-00', NULL, NULL, NULL, NULL),
(63, '274', 'MariaKarizzTariga', '$2y$10$hk43YJoWjX/0kQwC/bJANO.eF4zMHAhCOxbva5Bpz.F...', 'Maria Karizz', 'Ancheta', 'Tariga', 'SPI', '2000-08-24', '9507989548', 'Female', '2022-05-11', 'KarrizT.jpg', '0000-00-00', '274MARIAATAR', '', '2024-05-03', NULL, NULL, NULL, NULL),
(64, '277', 'WinnieMandane', '$2y$10$qemPlMPwoDqhrabEza11DeAd.351T4xigOUKZFA4PC/WZeqCQ506i', 'Winnie', 'Ulep', 'Mandane', 'SPI', '1975-06-26', '9561567494', 'Female', '2022-05-31', 'WinnieM.png', '0000-00-00', '277WINNIUMAN', 'dcc@smartprobegroup.com', '0000-00-00', NULL, NULL, NULL, NULL),
(65, '278', 'CharleneLora', '$2y$10$Wn7QsvPMQX0nlKAp4xyD2ugQq9E2ammTriy0LzsZJmhjU9ZKw.riO', 'Charlene', 'Lacorte', 'Lora', 'SPI', '1982-01-07', '9466537221', 'Female', '2022-06-13', 'CharleneL.png', '0000-00-00', '278CHARLLLOR', 'charlenel@smartprobegroup.com', '0000-00-00', NULL, NULL, NULL, NULL),
(66, '281', 'RenanAmarante', '$2y$10$41ZzXetWYLLJvmHIkJShq.CdHJG.5h.2UG3XShSJsc8aP/LGifw.S', 'Renan', 'Punzalan', 'Amarante', 'SPI', '1979-12-23', '9058961763', 'Male', '2022-06-17', 'RenanA.png', '0000-00-00', '281RENANPAMA', '', '0000-00-00', NULL, NULL, NULL, NULL),
(67, '282', 'MaricarRemiter', '$2y$10$d9FZVDb5sjPcNx8Zs3Cy/OY/GyqzUYoLTBYv0uVXirqrLzgEhZKyW', 'Maricar', 'Lacsa', 'Remiter', 'SPI', '1955-08-18', '9997945584', 'Female', '2022-07-04', 'MaricarM.png', '0000-00-00', '282MARICLREM', '', '0000-00-00', NULL, NULL, NULL, NULL),
(68, '284', 'RaymondLai', '$2y$10$N3BfS/u5GJ8C9MCJnDQBWOdRlBUCDrLYVRziQzVOIhdyKAJZlH06y', 'Raymond', 'Escoriaga', 'Lai', 'SPI', '1983-06-07', '9163975188', 'Male', '2022-07-18', 'RaymundL.png', '0000-00-00', '284RAYMOELAI', 'raymondl@smartprobegroup.com', '0000-00-00', NULL, NULL, NULL, NULL),
(69, '290', 'AprilBernabe', '$2y$10$9PPH0TwaxCoDcrykd6.Ch.HYpr9qwNEw9BClN8h2pC0V.Ndeot70m', 'April', 'Narciso', 'Bernabe', 'SPI', '1984-04-07', '9309306119', 'Female', '2022-09-28', 'AprilB.png', '0000-00-00', '290APRILNBER', 'aprilb@smartprobegroup.com', '0000-00-00', NULL, NULL, NULL, NULL),
(70, '295', 'AldouzPaglinawan', '$2y$10$kTEH.Eh2UsNJuP0SbGsl9ukcNjOjYeW9U3E3on9Rs0bGBgj/k/Kea', 'Aldouz Yanz', 'Paglinawan', 'Paglinawan', 'SPI', '1994-06-30', '9151180489', 'Male', '2022-11-17', 'AldousP.png', '0000-00-00', '295ALDOUPPAG', '', '0000-00-00', NULL, NULL, NULL, NULL),
(71, '296', 'JeromeDeLeon', '$2y$10$kMfgZDQ2IASjiBj.F8oRzORYZmEr9IE3xjr5nwrjSXMr0O8mmpYUy', 'Jerome', 'Magsino', 'De Leon', 'SPI', '1996-06-27', '9918504235', 'Male', '2022-11-21', 'Jerome.png', '0000-00-00', '296JEROMMDEL', '', '0000-00-00', NULL, NULL, NULL, NULL),
(72, '297', 'ShielaDolar', '$2y$10$gOLVVYOCwffC6PUb/R2mN.46VZL32Iyh5aLKMpAUA1.A/YDrsS.1.', 'Shiela Rose', 'Rivera', 'Dolar', 'SPI', '1992-07-08', '9062187593', 'Female', '2022-12-01', 'ShielaD.png', '0000-00-00', '297SHIELRDOL', 'spi_logistics@smartprobegroup.com', '2025-07-31', NULL, NULL, NULL, NULL),
(73, '301', 'ArvinJunPerez', '$2y$10$hk43YJoWjX/0kQwC/bJANO.eF4zMHAhCOxbva5Bpz.F...', 'Arvin Jun', 'Odeste', 'Perez', 'SPI', '1998-06-12', '9482309261', 'Male', '2023-01-17', 'profile.jpg', '0000-00-00', '301ARVINOPER', '', '2024-05-10', NULL, NULL, NULL, NULL),
(74, '302', 'MaryElaineFuntanilla', '$2y$10$XO5FnyIyCytdihEw46o/G.pZ7BxNznb1XXkWzm2R24ksSQ/TA52NG', 'Mary Elaine', 'Sasota', 'Funtanilla', 'SPI', '1991-01-03', '9519792266', 'Female', '2023-02-13', 'ElaineF.png', '0000-00-00', '302MARYESFUN', '', '0000-00-00', NULL, NULL, NULL, NULL),
(75, '303', 'RowenaRobedizo', '$2y$10$PE5vtQUOm6q12C686Kbfm.18WtEG8lETw2KidaIkJGKF54kt74/HK', 'Rowena', 'Robredillo', 'Robedizo', 'SPI', '1986-11-24', '9953582301', 'Female', '2023-02-14', 'RowenaR.png', '0000-00-00', '303ROWENRROB', '', '2025-03-31', NULL, NULL, NULL, NULL),
(76, '308', '308SC', '$2y$10$lbyUlK/Wts0ci7p0Menv6eZLI2LJSELDA2TXKpFyoOpgaT6vEe0RW', 'Stanley', 'Afunggol', 'Cuesta', 'SPI', '1997-05-05', '9569223311', 'Male', '2023-03-08', 'StanleyA.png', '0000-00-00', '308STANLACUE', 'stanleyc@smartprobegroup.com', '2025-01-30', NULL, NULL, NULL, NULL),
(77, '309', 'ReynalynClaveria', '$2y$10$1RPEYInCZQYxytw5Yp83luCGdSCYO3sywllr5pfZCwycojH7HNJDG', 'Reynalyn', '', 'Claveria', 'SPI', '2001-06-20', '9513979701', 'Female', '2023-06-13', 'ReynalynC.png', '0000-00-00', '309REYNACLA', 'reynalync@smartprobegroup.com', '0000-00-00', NULL, NULL, NULL, NULL),
(78, '310', 'EngrKaloy', '$2y$10$rjz.KYPSNlBjwSeC65YThuE.8u73yBV.YZwk.TIyfsDxLH4hCxZPm', 'Carlo', 'Ragot', 'Polistico', 'SPI', '1998-01-07', '9995042147', 'Male', '2023-04-03', 'Polly.png', '0000-00-00', '310CARLORPOL', 'carlop@smartprobegroup.com', '0000-00-00', NULL, NULL, NULL, NULL),
(79, '311', 'MaryAnnBarrios', '$2y$10$hk43YJoWjX/0kQwC/bJANO.eF4zMHAhCOxbva5Bpz.F...', 'Mary Ann', 'Macatangay', 'Barrios', 'SPI', '1984-04-29', '9953582301', 'Female', '0000-00-00', 'profile.jpg', '0000-00-00', '311MARYAMBAR', 'maryannb@smartprobegroup.com', '2023-12-01', NULL, NULL, NULL, NULL),
(80, '312', 'MarifeDeTorres', '$2y$10$30iXBx/215w2z1txHkSGaORuyWMWF71eCUIM/zPG93hL5ALQgExM.', 'Marife', 'Vejano', 'De Torres', 'SPI', '1981-01-28', '9353543095', 'Female', '2023-04-19', 'MarifeDT.png', '0000-00-00', '312MARIFVDET', '', '0000-00-00', NULL, NULL, NULL, NULL),
(81, '314', 'EricJohnBurcer', '$2y$10$Rw4u8MCqUR2C9Wv9FTy9dewm7vtt0e/BfHx6g.DrKIivRfAI0Zy1u', 'Eric John', 'Barbacena', 'Burcer', 'SPI', '1990-11-08', '9295417603', 'Male', '2023-05-08', 'EricB.png', '0000-00-00', '314ERICJBBUR', 'spi_pcbr@smartprobegroup.com', '2025-03-25', NULL, NULL, NULL, NULL),
(82, '316', 'masocorrou', '$2y$10$ZMGb.nOlK2Bo9y60x9NbZ.yxj7rFKggMHXFg/yMSS1m6lGFq07zGK', 'Ma. Socorro', 'Prado', 'Uvero', 'SPI', '1983-09-29', '9088645568', 'Female', '2023-05-16', 'MaSocorroU.png', '0000-00-00', '', 'masocorrou@smartprobegroup.com', '0000-00-00', NULL, NULL, NULL, NULL),
(83, '317', 'JULIEANN MAGLINAO ', '$2y$10$RColaWWsN1AHOzX7nnD95.NrfujbbKnxNCbP.fwcN6QpbGUpT2ytS', 'Julie Ann', 'Samiano', 'Maglinao', 'SPI', '1985-07-05', '9164132310', 'Female', '2023-06-06', 'JulieM.jpg', '0000-00-00', '317JULIESMAG', '', '0000-00-00', NULL, NULL, NULL, NULL),
(84, '318', 'ReneEsperida', '$2y$10$vV60YxBRqfSACpfOSTz1M.czdCMMfiqfoACEKaHZttdBuIbZsq3Nm', 'Rene', 'Estares', 'Esperida', 'SPI', '1974-11-26', '9122538479', 'Male', '2023-06-08', 'ReneE.png', '0000-00-00', '318RENEEESP', '', '0000-00-00', NULL, NULL, NULL, NULL),
(85, '320', 'ElzaRamirez', '$2y$10$nHEd8zljzOki47QLU/K8N.6QV9wIETLy//zylsiX3f2qav6ZZ51U6', 'Elza', 'Tandang', 'Ramirez', 'SPI', '1993-09-17', '9957252522', 'Female', '2023-06-21', 'ElsaR.png', '0000-00-00', '320ELZATREM', '', '2025-08-06', NULL, NULL, NULL, NULL),
(86, '328', 'AlexisArguelles', '$2y$10$shLmGdaYGTjkNTQSs00FvOSeQC63BRgxDgOOat5fnnAZvuYBpiif.', 'Alexis', 'Mapula', 'Arguelles', 'SPI', '1991-06-09', 'no contact', 'Male', '2023-08-01', 'AlexisA.png', '0000-00-00', '328ALEXIMARG', '', '0000-00-00', NULL, NULL, NULL, NULL),
(87, '329', 'RodelPeñas', '$2y$10$hk43YJoWjX/0kQwC/bJANO.eF4zMHAhCOxbva5Bpz.F...', 'Rodel', 'Nagales', 'Peñas', 'SPI', '0000-00-00', '9665383447', 'Male', '2023-08-14', 'profile.jpg', '0000-00-00', '329RODELNPEÑ', '', '2024-05-20', NULL, NULL, NULL, NULL),
(88, '332', 'JayRobertJoson', '$2y$10$hk43YJoWjX/0kQwC/bJANO.eF4zMHAhCOxbva5Bpz.F...', 'Jay Robert', 'Aquino', 'Joson', 'SPI', '1997-02-18', '', 'Male', '0000-00-00', 'profile.jpg', '0000-00-00', '332JAYROAJOS', 'robertj@smartprobegroup.com', '2024-02-01', NULL, NULL, NULL, NULL),
(89, '334', 'JMDIMAFELIX', '$2y$10$gYh18MysWtSy/yYllUae2utW88VQbRFzaK82JdIxjj5.l3fWdCMaG', 'Jean Marie', 'Del Rosario', 'Dimafelix', 'SPI', '1989-03-04', '9913843462', 'Female', '2023-08-30', 'JeanMarieD.png', '0000-00-00', '334JEANMDDIM', '', '0000-00-00', NULL, NULL, NULL, NULL),
(90, '335', 'LeaManguiat', '$2y$10$PTQTXLbRK/M/n.cx9laG7OqP/4D6.fpiRRNcoSSs1uFcWT2oCI5A2', 'Lea', 'Paco', 'Manguiat', 'SPI', '1984-03-09', '9211636048', 'Female', '2023-09-11', 'LeaM.png', '0000-00-00', '335LEAPMAN', '', '0000-00-00', NULL, NULL, NULL, NULL),
(91, '338', 'DonnRusselCuevas', '$2y$10$hk43YJoWjX/0kQwC/bJANO.eF4zMHAhCOxbva5Bpz.F...', 'Donn Russel', 'Marquez', 'Cuevas', 'SPI', '1995-04-01', '9306865662', 'Male', '2023-12-18', 'profile.jpg', '0000-00-00', '338DONNRMCUE', '', '2024-05-31', NULL, NULL, NULL, NULL),
(93, '348', 'MarkAngeloLazarte', '$2y$10$89MaIIIsGl1xoOCD0sS7G.57oZJHFOOMqb9XueqViBGI4dXM4FTPy', 'Mark Angelo', 'Abdon', 'Lazarte', 'SPI', '0000-00-00', '9167498949', 'Male', '2024-01-29', 'AngeloL.png', '0000-00-00', '348MARKAALAZ', 'markl@smartprobegroup.com', '0000-00-00', NULL, NULL, NULL, NULL),
(94, '349', 'MadeleneDelLeola', '$2y$10$Hv4D4g6XhmOB.Wki4eZg0.AK0poboEkDftDddlmcIVUIZjbXx5Q4O', 'Madelene', 'Butaslac', 'Del Leola', 'SPI', '0000-00-00', '9169076707', 'Female', '2024-01-29', 'MadeleneD.png', '0000-00-00', '349MADELBDEL', 'madelenedl@smartprobegroup.com', '2025-05-13', NULL, NULL, NULL, NULL),
(95, '350', 'LeenethMica', '$2y$10$PffZObHOTRy/TtsOlpkjN.moQjRwcxSeEfGX/paKvahSlQpO6ca52', 'Leeneth Aizle', 'Nuñez', 'Mica', 'SPI', '0000-00-00', '9394919426', 'Female', '2024-02-05', 'LeenethM.png', '0000-00-00', '350LEENENMIC', 'leenethm@smartprobegroup.com', '2024-11-25', NULL, NULL, NULL, NULL),
(96, '351', 'JetEstefani', '$2y$10$BZzvLz0BD34gUEc8J4bMzeH5niAoXx3ch1fv6qBpB1/MVXE8f34kq', 'Jet Joaner', 'Ciruelos', 'Estefani', 'SPI', '0000-00-00', '9177126144', 'Male', '2024-02-05', 'JetE.png', '0000-00-00', '351JETJOCEST', 'joanere@smartprobegroup.com', '0000-00-00', NULL, NULL, NULL, NULL),
(97, '355', 'Chonadc', '$2y$10$7/qdOFK.wUUoi.iUkrHJtOhR7Vx0We.mItm/OIzDd8FZ4Uew2naFe', 'Chona', 'Rioja', 'De La Cruz', 'SPI', '1984-05-21', '9998025402', 'Female', '2024-03-19', 'ChonaD.png', '0000-00-00', '355CHONARDEL', 'chonadc@smartprobegroup.com', '0000-00-00', NULL, NULL, NULL, NULL),
(98, '356', 'JenileeSoria', '$2y$10$hk43YJoWjX/0kQwC/bJANO.eF4zMHAhCOxbva5Bpz.F...', 'Jenilee', 'Melendrez', 'Soria', 'SPI', '1991-09-21', '9952781957', 'Female', '2024-04-04', 'profile.jpg', '0000-00-00', '356JENILMSOR', 'jenilees@smartprobegroup.com', '2024-06-27', NULL, NULL, NULL, NULL),
(99, '357', 'LeanRoseOca', '$2y$10$DanmydSp.pI4F6mCIoR.juXjr4GxoJ/Hjm/NuwnAB4UVVXC./Qecm', 'Lean Rose', 'Malla', 'Oca', 'SPI', '1996-12-27', '9668439168', 'Female', '2024-04-16', 'LeanO.png', '0000-00-00', '357LEANRMOCA', 'leanro@smartprobegroup.com', '0000-00-00', NULL, NULL, NULL, NULL),
(100, '358', 'AngelicaMorales', '$2y$10$LMqRS49JwXj937C.GYyk1eZDjK2SBET.2p8D1Xn.RW63337i/Y5Vy', 'Angelica', 'Fernandez', 'Morales', 'SPI', '1992-12-23', '9691906172', 'Female', '2024-04-23', 'thanossmile.jpg', '0000-00-00', '358ANGELFMOR', '', '2025-01-07', NULL, NULL, NULL, NULL),
(101, '361', 'FlorendaArtista', '$2y$10$mJ9GuUiDwssaxLsWPMpWF.5GNtvOO/oBlS4AB44cZpzqM.IRv74km', 'Florenda', 'Bulante', 'Artista', 'SPI', '1958-09-04', '9668458948', 'Female', '2024-05-15', 'Flordeliza.png', '0000-00-00', '361FLOREBART', '', '0000-00-00', NULL, NULL, NULL, NULL),
(102, '364', 'JonalynTipan', '$2y$10$hk43YJoWjX/0kQwC/bJANO.eF4zMHAhCOxbva5Bpz.F...', 'Jonalyn', 'Tomonio', 'Tipan', 'SPI', '0000-00-00', '9487090701', 'Female', '2024-05-27', 'thanossmile.jpg', '0000-00-00', '364JONALTTIP', '', '2024-05-28', NULL, NULL, NULL, NULL),
(103, '362', 'Superficial', '$2y$10$5HWMojBby1/wUllACrxGgOxaGCj2CBC3hdc2J0WOJFJYI30lY2xua', 'Rachelle Ann', 'Zubia', 'Gonzales', 'SPI', '1994-06-04', '9230861724', 'Female', '2024-05-15', 'thanossmile.jpg', '0000-00-00', '362RACHEZGON', '', '2024-09-02', NULL, NULL, NULL, NULL),
(104, '363', 'RochelleBenjamin', '$2y$10$hk43YJoWjX/0kQwC/bJANO.eF4zMHAhCOxbva5Bpz.F...', 'Rochelle', 'Revilloza', 'Benjamin', 'SPI', '0000-00-00', '9297041630', 'Female', '2024-05-27', 'thanossmile.jpg', '0000-00-00', '363ROCHERBEN', '', '2024-07-15', NULL, NULL, NULL, NULL),
(105, '167', '', '', 'Angela', '', 'Mendoza', 'SPI', '0000-00-00', '', 'Female', '2010-01-01', 'profile.jpg', '0000-00-00', '', 'angelam@smartprobegroup.com', '2024-02-01', NULL, NULL, NULL, NULL),
(106, '188', '', '', 'Victor', '', 'Baldondo', 'SPI', '0000-00-00', '', 'Male', '2010-01-01', 'profile.jpg', '0000-00-00', '', '', '2023-12-30', NULL, NULL, NULL, NULL),
(107, '191', '', '', 'Theresa', '', 'Adarayan', 'SPI', '0000-00-00', '', 'Female', '2010-01-01', 'profile.jpg', '0000-00-00', '', '', '2024-01-01', NULL, NULL, NULL, NULL),
(108, '261', '', '', 'Myla', '', 'Raymundo', 'SPI', '0000-00-00', '', 'Female', '0000-00-00', 'profile.jpg', '0000-00-00', '', '', '2023-12-30', NULL, NULL, NULL, NULL),
(109, '283', '', '', 'Shannadine Mae', '', 'Garcia', 'SPI', '0000-00-00', '', 'Female', '0000-00-00', 'profile.jpg', '0000-00-00', '', '', '2024-01-01', NULL, NULL, NULL, NULL),
(110, '299', '', '', 'Jeuel Jhon', '', 'Aldaba', 'SPI', '0000-00-00', '', 'Male', '0000-00-00', 'profile.jpg', '0000-00-00', '', '', '2024-01-30', NULL, NULL, NULL, NULL),
(111, '321', '', '', 'Aristotle', '', 'Esclanda', 'SPI', '0000-00-00', '', 'Male', '0000-00-00', 'profile.jpg', '0000-00-00', '', '', '2023-12-30', NULL, NULL, NULL, NULL),
(113, '337', 'jeanne', '$2y$10$BmTvpgZT5JY.Iym6qnnli.lCIyut5G7N2HA64c2twt0LgPAn/UbU2', 'Jeanne Riona Daryl', '', 'Colar', 'SPI', '0000-00-00', '', 'Female', '2023-09-28', 'JeanneC.png', '0000-00-00', '', 'jeannec@smartprobegroup.com', '0000-00-00', NULL, NULL, NULL, NULL),
(115, '359', 'CamilleAnn', '$2y$10$8PiX7lMeY75RWbZl5ntFzO3BI6XYKHZt94u1cdo00iivhwzD4nDFe', 'Camille Ann', 'Mahilum', 'Caceres', '', '0000-00-00', '9922987190', 'Female', '2024-05-07', 'CamilleC.png', '0000-00-00', '', 'camillec@smartprobegroup.com', '0000-00-00', NULL, NULL, NULL, NULL),
(116, '365', 'AbigailJoy', '$2y$10$zPFNJc8hAtoC/bme/8/RsuUlYupEWM5h9fy07xL/sunafUxTa0FIq', 'Abigail Joy', 'Fechalin', 'Somera', '', '0000-00-00', '9561326795', 'Female', '2024-04-27', 'AbigailS.png', '0000-00-00', '', 'abigailjoys@smartprobegroup.com', '2025-07-11', NULL, NULL, NULL, NULL),
(117, '366', 'ArchieManao', '', 'Archie', 'Taboy', 'Manao', '', '0000-00-00', '9568814525', 'Male', '2024-06-11', 'thanossmile.jpg', '0000-00-00', '', 'archiem@smartprobegroup.com', '2024-07-25', NULL, NULL, NULL, NULL),
(118, '367', 'CarolinaManaga', '', 'Carolina', 'Facturan', 'Mañaga', '', '0000-00-00', '9919598044', 'Female', '2024-06-13', 'thanossmile.jpg', '0000-00-00', '', '', '2024-08-22', NULL, NULL, NULL, NULL),
(119, '368', 'mkcunanan', '$2y$10$sX271GYQXddsUH0RK/39hekWLzKa1iaPxl372SiJ2hDb/CdpJR0q2', 'Ma. Karmina', 'Concha', 'Cunanan', '', '0000-00-00', '9913699658', 'Female', '2024-06-24', 'KarminaC.png', '0000-00-00', '', 'karminac@smartprobegroup.com', '2024-11-27', NULL, NULL, NULL, NULL),
(120, '369', 'alexisagner', '$2y$10$b8lqJsUMsMr8WnVAyzweTeuo/Y.YvtWpk9m39qTbq1lssMEFndlaq', 'Alexis', 'Dividor', 'Agner', '', '0000-00-00', '9694354847', 'Male', '2024-07-09', 'AlexisAgner.png', '0000-00-00', '', '', '0000-00-00', NULL, NULL, NULL, NULL),
(121, '370', 'rosalindamolina', '$2y$10$6TAyx2xU2IWssby9DHVwp.PtSt1JGxH/jTk9AgHB6O3L9ifAXALpK', 'Rosalinda', 'Vispo', 'Molina', '', '0000-00-00', '9984849192', 'Female', '2024-07-09', 'RosalindaM.png', '0000-00-00', '', 'rosalindam@smartprobegroup.com', '0000-00-00', NULL, NULL, NULL, NULL),
(344, '371', 'roiosorio', '$2y$10$l.J8JcfWLvvPG.fUeq/YC.ximGL5xpMZrBbrEsg3gNg2Dgt9fYKwa', 'Roi', 'Garate', 'Osorio', 'SPI', '0000-00-00', '9668458948', 'Female', '2024-07-29', 'thanossmile.jpg', '2024-08-19', '', 'osorioroi050@gmal.com', '2024-09-10', NULL, NULL, NULL, NULL),
(345, '372', 'arjaydeguzman', '$2y$10$EI66QMhld.0pCim9lORoc.r2.2ZN9wRHHS3EhnDjemkY4VxnTZNju', 'Arjay', 'Montilla', 'De Guzman', 'SPI', '2004-09-27', '09815607830', 'Male', '2024-08-08', 'ArjayD.png', '2024-08-29', '', '', '0000-00-00', NULL, NULL, NULL, NULL),
(346, '373', 'chams', '$2y$10$BZ15Qd9tfqv6edA999z.0OEFjSXJ.rFzVuSH85bSQUpxrnj2wADuK', 'Emelita', 'Maimtim', 'Omayon', 'Blk 30 Lot 18 Casa Laguerta Calamba City, Laguna', '1985-09-06', '09491322243', 'Female', '2024-08-06', 'emelita.png', '2024-08-29', '', '', '2025-01-09', NULL, NULL, NULL, NULL),
(347, '375', '', '', 'John Carlo', 'Furio', 'Gisalan', 'J.P.Rizal Street, Barangay 1 Poblacion, Calamba City Laguna\r\n', '1988-10-06', '0921-957-4178', 'Male', '2024-08-14', 'thanossmile.jpg', '2024-11-21', '', '', '2024-10-31', NULL, NULL, NULL, NULL),
(348, '374', 'Leralynquitaneg', '$2y$10$F3AOSYGYmz9Mtat1u1SHp.hFCCqapsDPhAVFSc1kBW0XOEuCxNkgu', 'Leralyn', 'Sudweste', 'Quitaneg', 'Unit 6 Sitio Marangal Street, Real Calamba, Laguna', '1990-01-21', '0949-634-2027', 'Female', '2024-09-02', 'Leralyn.png', '2024-09-04', '', 'leralynq@smartprobegroup.com', '0000-00-00', NULL, NULL, NULL, NULL),
(349, '376', 'patrickvillanueva', '$2y$10$I/gOv.HuHme/AWW6fQ6.1eEA1kMvA5ZPgtOuav8/nFJZ8W5Tuq.nS', 'Patrick', 'Cutabao', 'Villanueva', 'B13B L3 Ph-2 Madrid St. Bgry. Barandal Calamba Hills Village, Calamba', '1997-04-23', '0905-464-2257', 'Male', '2024-08-02', 'thanossmile.jpg', '2024-09-04', '', '', '2025-02-28', NULL, NULL, NULL, NULL),
(353, '377', 'bonniejacinto', '$2y$10$H.X.MRVLQN7saWYeGoFjg.aKye8LOKJVIbDnF6vvsHmAVJpXBZRsi', 'Bonnie Boy', 'Ramos', 'Jacinto', 'P3B50L28 Pontevedra Tierra Solana, Gen Trias, Cavite', '1979-06-05', 'N/A', 'Male', '2024-08-11', 'Bonnie.png', '2024-09-17', '', 'bonniej@smartprobegroup.com', '2025-01-24', NULL, NULL, NULL, NULL),
(356, '378', '', '', 'Randy', 'Adorna', 'Ortega', '293 Purok 4 Brgy San Cristobal, Calamba Laguna\r\n', '1986-12-11', '0916-390-6958', 'Male', '2024-09-24', '', '2024-11-21', '', '', '2024-11-01', NULL, NULL, NULL, NULL),
(359, '379', 'jeanalyn', '$2y$10$eQZUt1vNJWCGTgFX87/kXujFhFiOzFySsxX88OHDKxj5/B6rQF./O', 'Jeanalyn', 'Pampang', 'Navarro', 'Banay Banay Cabuyao Laguna\r\n', '1980-08-03', '0908-494-2156', 'Female', '2024-10-21', 'Jeanalyn_Navarro.png', '2024-11-04', '', 'jeanalynn@smartprobegroup.com', '0000-00-00', NULL, NULL, NULL, NULL),
(360, '380', 'canlobocamille', '$2y$10$mhNtold.IFTTorvubFp1l.XOYOrckHO/x6PGtPl/TsN0y2zFOQK6u', 'Camille', 'Rivera', 'Canlobo', 'Prinza Calamba Laguna', '2003-01-05', '0938-6720-115', 'Female', '2024-04-11', 'Camille_Canlobo.png', '2024-11-04', '', 'canlobocamille@gmail.com', '2025-02-13', NULL, NULL, NULL, NULL),
(363, '382', 'princessa', '$2y$10$fOOptCB.ltaaaaMmYHnKN..WgyktlyPTOsuB4qWnnCsUYLM2H54Py', 'Princess Lorraine', 'Narvaez', 'Arellano', 'Blk 47 Lot 21 Daffodil St., La Aldea del Monte, Brgy. Santa Anastacia, Santo Tomas Ciity, Batangas', '1989-12-20', '09998871294', 'Female', '2024-11-21', 'Princess.png', '2024-11-27', '', 'princessa@smartprobegroup.com', '0000-00-00', NULL, NULL, NULL, NULL),
(364, '381', 'joshua', '$2y$10$PcYHqHez/LmPz1fOYmqZkOZa8YGpv.tWTuU9c44LO/jJSLsHnhR/O', 'Joshua', 'Ayson', 'Martez', 'Mayapa Calamba Laguna City', '1998-08-09', '09202765332', 'Male', '2024-11-18', 'Joshua.png', '2024-11-27', '', 'yeshuamartez09@gmail.com', '0000-00-00', NULL, NULL, NULL, NULL),
(365, '383', 'riana', '$2y$10$2ET6loySvg/t3vxvOvQa5uAmxhKQ.tRf/is.DSCfuQ0CbCHRf19Bq', 'Ria Nicole', 'Labitag', 'Atienza', '1029  Tindalo St. Looc, Calamba City, Laguna', '1997-07-31', '09978478999', 'Female', '2024-11-21', 'Ria.png', '2025-01-02', '', 'atienzarianicole@gmail.com', '2025-07-04', NULL, NULL, NULL, NULL),
(367, '390', 'healpardo', '$2y$12$aTiXeYV3e/8gxHKaMexFqO.VO.8ivCb9YmBacI.0.8vFscIs0X.Se', 'Heal Joshua', 'Cabrera', 'Pardo', 'Block 37 Lot 27 Makiling Calamba City Laguna', '2003-03-03', '09564870057', 'Male', '2025-06-16', 'Heal Joshua.png', '2025-01-20', '', 'healjoshuap@gmail.com', '0000-00-00', NULL, NULL, NULL, NULL),
(368, 'OJT001', 'leilakryss', '', 'Leila Kryss', 'Capitin', 'Bautista', 'Blk 4 lot 48 Southville 6 Brgy. Kay-Anlog Calamba City Laguna', '2002-07-23', '09926141721', 'Female', '2025-01-20', 'thanossmile.jpg', '2025-01-20', '', '', '2025-06-15', NULL, NULL, NULL, NULL),
(369, '384', 'shel', '$2y$10$wrF84cBzxvGu0eZzpMizLuN.u0Oy6DNn7OZffYRaHSd/5k0f7YmRi', 'Rachael', 'Superficial', 'Ballente', 'B2 L19 P1 Carlton Dita Sta Rosa', '1985-08-02', '09567703700', 'Female', '2025-01-20', 'thanossmile.jpg', '2025-01-20', '', 'rachaelb@smartprobegroup.com', '0000-00-00', NULL, NULL, NULL, NULL),
(370, 'SPI3120', 'Aiubrey', '', 'Aubrey', 'Dela Cruz', 'Tamondong', 'Purok 1, Kanluran. Palo Alto Calamba City Laguna', '2003-08-22', '09307611575', 'Female', '2025-02-10', 'thanossmile.jpg', '2025-02-11', '', '', '2025-05-01', NULL, NULL, NULL, NULL),
(371, 'OJT004', 'cha', '', 'charell ann', 'sobrepena', 'mendoza', 'sitio manggahan palo alto calamba city laguna', '2003-07-05', '09917468039', 'Female', '2025-02-10', 'thanossmile.jpg', '2025-02-11', '', '', '2025-04-01', NULL, NULL, NULL, NULL),
(372, '386', 'Tasha', '$2y$10$TvYEdl4WRJ20fwuUuy/Ne.4W1pusWV3dVeEptHULeIhobSf4kjkly', 'Tasha Isabel', 'Vitor', 'Villapando', '004 Villapando St., Luta Norte, Malvar, Batangas', '2001-11-07', '09923311002', 'Female', '2025-02-10', 'thanossmile.jpg', '2025-02-11', '', '', '2025-05-30', NULL, NULL, NULL, NULL),
(373, 'SPI0231', 'Chona Del Rio', '', 'Chona', 'Revota', 'Del Rio', 'Brgy. Barandal Clamba Laguna', '1995-08-09', '09053171281', 'Female', '2025-02-10', '', '2025-02-11', '', '', '2025-02-12', NULL, NULL, NULL, NULL),
(374, 'OJT005', 'ianvillanueva', '', 'Ian', 'Madrona', 'Villanueva', 'Punta, Calamba City Laguna', '2002-08-28', '09658275419', 'Male', '2025-03-02', 'thanossmile.jpg', '2025-02-11', '', '', '2025-06-30', NULL, NULL, NULL, NULL),
(375, 'OJT003', 'e3lrey', '', 'Erey', 'Pancipani', 'Permejo', 'Purok 4 Cmap  vVicente Lim Mayapa Calamba City', '2003-02-04', '09549932789', 'Male', '2025-02-04', 'thanossmile.jpg', '2025-02-11', '', '', '2025-05-01', NULL, NULL, NULL, NULL),
(377, '387', 'russell.loma', '$2y$10$fn9I1k5XzLUNr7rx9.pG.OsPqCze1H9Wuz0dyDST3M00tx7GythfO', 'Russell', 'Serrano', 'Loma', '0319 Acasia st. Amaia Scapes, Barandal Calamba Laguna', '2000-04-17', '09175511163', 'Male', '2025-04-01', 'Russel.png', '2025-04-03', '', '', '0000-00-00', NULL, NULL, NULL, NULL),
(378, '389', '', '', 'Aubrey', 'Dela Cruz', 'Tamondong', '', '2003-08-22', '09307611575', 'Female', '2025-05-05', 'thanossmile.jpg', '2025-05-06', '', '', '0000-00-00', NULL, NULL, NULL, NULL),
(379, '391', 'Niña', '', 'Niña Monica', 'Barte', 'Sta. Ana', 'Sitio Looban Banaba Kanluran, Batangas City', '2002-09-22', '0976-509-0563', 'Female', '2025-08-01', 'thanossmile.jpg', '2025-08-06', '', '', '0000-00-00', NULL, NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `base_folders`
--
ALTER TABLE `base_folders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `base_folders_name_unique` (`name`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `documents_user_id_foreign` (`user_id`),
  ADD KEY `documents_folder_id_foreign` (`folder_id`),
  ADD KEY `documents_base_folder_id_foreign` (`base_folder_id`),
  ADD KEY `documents_document_registration_entry_id_foreign` (`document_registration_entry_id`);

--
-- Indexes for table `document_registration_entries`
--
ALTER TABLE `document_registration_entries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `document_registration_entries_submitted_by_foreign` (`submitted_by`),
  ADD KEY `document_registration_entries_approved_by_foreign` (`approved_by`);

--
-- Indexes for table `document_registration_entry_files`
--
ALTER TABLE `document_registration_entry_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `document_registration_entry_files_entry_id_foreign` (`entry_id`),
  ADD KEY `document_registration_entry_files_approved_by_foreign` (`approved_by`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `folders`
--
ALTER TABLE `folders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `folders_user_id_foreign` (`user_id`),
  ADD KEY `folders_parent_id_foreign` (`parent_id`),
  ADD KEY `folders_base_folder_id_foreign` (`base_folder_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `base_folders`
--
ALTER TABLE `base_folders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `document_registration_entries`
--
ALTER TABLE `document_registration_entries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `document_registration_entry_files`
--
ALTER TABLE `document_registration_entry_files`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `folders`
--
ALTER TABLE `folders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=380;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `documents`
--
ALTER TABLE `documents`
  ADD CONSTRAINT `documents_base_folder_id_foreign` FOREIGN KEY (`base_folder_id`) REFERENCES `base_folders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `documents_document_registration_entry_id_foreign` FOREIGN KEY (`document_registration_entry_id`) REFERENCES `document_registration_entries` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `documents_folder_id_foreign` FOREIGN KEY (`folder_id`) REFERENCES `folders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `documents_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `document_registration_entries`
--
ALTER TABLE `document_registration_entries`
  ADD CONSTRAINT `document_registration_entries_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `document_registration_entries_submitted_by_foreign` FOREIGN KEY (`submitted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `document_registration_entry_files`
--
ALTER TABLE `document_registration_entry_files`
  ADD CONSTRAINT `document_registration_entry_files_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `document_registration_entry_files_entry_id_foreign` FOREIGN KEY (`entry_id`) REFERENCES `document_registration_entries` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `folders`
--
ALTER TABLE `folders`
  ADD CONSTRAINT `folders_base_folder_id_foreign` FOREIGN KEY (`base_folder_id`) REFERENCES `base_folders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `folders_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `folders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `folders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
