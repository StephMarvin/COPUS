-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 26, 2026 at 08:13 PM
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
-- Database: `f_copus`
--

-- --------------------------------------------------------

--
-- Table structure for table `academic_years_tbl`
--

CREATE TABLE `academic_years_tbl` (
  `academic_year_id` int(11) NOT NULL,
  `academic_year` varchar(20) NOT NULL,
  `status` varchar(20) DEFAULT 'Inactive',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `academic_years_tbl`
--

INSERT INTO `academic_years_tbl` (`academic_year_id`, `academic_year`, `status`, `created_at`, `modified_at`) VALUES
(119, '2025-2026', 'Active', '2025-11-01 05:12:38', '2025-11-01 05:13:21');

-- --------------------------------------------------------

--
-- Table structure for table `admin_credentials_tbl`
--

CREATE TABLE `admin_credentials_tbl` (
  `admin_id` int(11) NOT NULL,
  `id_number` varchar(50) NOT NULL,
  `admin_password` varchar(255) NOT NULL,
  `generated_password` enum('Yes','No') DEFAULT 'Yes',
  `two_factor_authentication` enum('Enabled','Disabled') DEFAULT 'Enabled',
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) NOT NULL,
  `email_address` varchar(150) NOT NULL,
  `role` enum('Super Admin') DEFAULT 'Super Admin',
  `otp_code` int(6) DEFAULT NULL,
  `otp_code_expiry` datetime DEFAULT NULL,
  `password_reset_token` varchar(255) DEFAULT NULL,
  `reset_token_expiry` datetime DEFAULT NULL,
  `is_archived` enum('Yes','No') DEFAULT 'No',
  `locked_account` enum('Yes','No') DEFAULT 'No',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_credentials_tbl`
--

INSERT INTO `admin_credentials_tbl` (`admin_id`, `id_number`, `admin_password`, `generated_password`, `two_factor_authentication`, `first_name`, `middle_name`, `last_name`, `email_address`, `role`, `otp_code`, `otp_code_expiry`, `password_reset_token`, `reset_token_expiry`, `is_archived`, `locked_account`, `created_at`, `updated_at`, `last_login`) VALUES
(1004, '04-1234-123456', '$2y$10$kguT4wip309y91URA4bGoeXWzpnyTg3bIYs.qr4kcMqq7hXUlBikW', 'No', 'Disabled', 'Marvin', 'Morales', 'Agudines', 'stephmarvin30@gmail.com', 'Super Admin', 443046, '2026-02-16 10:19:13', 'LAFhCvcYLqmvnVyX', '2026-02-28 10:00:36', 'No', 'No', '2025-04-15 08:22:09', '2026-04-14 02:43:51', '2026-04-14 02:43:51'),
(1015, '04-2223-033125', '$2y$10$EHEYHL/PfKuiiU1go5yhp.xHeeXJX69i/feB6V1hFuCcDWkZLCC8K', 'No', 'Disabled', 'Kishia', 'Ortencio', 'Laubenia', 'kior.laubenia.ui@phinmaed.com', 'Super Admin', 649324, '2026-02-16 11:36:35', NULL, NULL, 'No', 'No', '2026-02-16 03:30:18', '2026-02-27 11:50:05', '2026-02-27 11:50:05'),
(1019, 'UI-20-069-P', '$2y$10$vWlSK0CVqIMEhiSVeSyAy.NaPgokmAwF4LH3vsCy.Qf30W82hadOu', 'No', 'Enabled', 'Zesty Kein', '', 'Mondia', 'zgmondia.ui@phinmaed.com', 'Super Admin', 356137, '2026-02-26 22:51:28', NULL, NULL, 'No', 'No', '2026-02-16 05:09:28', '2026-02-26 14:51:44', '2026-02-26 14:46:49');

-- --------------------------------------------------------

--
-- Table structure for table `admin_info_tbl`
--

CREATE TABLE `admin_info_tbl` (
  `admin_info_id` int(11) NOT NULL,
  `id_number` varchar(50) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `phone_number` varchar(50) NOT NULL,
  `telephone_number` varchar(50) DEFAULT NULL,
  `temporary_address` varchar(255) DEFAULT NULL,
  `permanent_address` varchar(255) NOT NULL,
  `gender` enum('Male','Female','Others') DEFAULT 'Others',
  `marital_status` enum('Single','Married','Divorced','Widowed') DEFAULT 'Single',
  `facebook_link` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_info_tbl`
--

INSERT INTO `admin_info_tbl` (`admin_info_id`, `id_number`, `profile_picture`, `date_of_birth`, `phone_number`, `telephone_number`, `temporary_address`, `permanent_address`, `gender`, `marital_status`, `facebook_link`, `updated_at`) VALUES
(1004, '04-1234-123456', 'admin_04-1234-123456_68edbb08eb59a.jpg', '2004-05-13', '09927415812', NULL, 'J.C. Zulueta Street, Oton, Iloilo', 'Brgy. Poblacion Oton, Iloilo', 'Male', 'Married', 'https://www.facebook.com/marvzz.moralesagudines', '2026-02-19 01:55:53'),
(1015, '04-2223-033125', 'admin_04-2223-033125_699291396211a.jpg', '2004-09-07', '09279183369', NULL, '', 'Mambusao, Capiz', 'Female', 'Single', 'https://www.facebook.com/share/1HGLovAEEd/', '2026-02-16 03:40:06'),
(1019, 'UI-20-069-P', NULL, NULL, '', NULL, '', '', 'Female', 'Single', NULL, '2026-02-26 14:51:44');

-- --------------------------------------------------------

--
-- Table structure for table `copus_forms_tbl`
--

CREATE TABLE `copus_forms_tbl` (
  `pdf_id` int(11) NOT NULL,
  `observation_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `deans_credentials_tbl`
--

CREATE TABLE `deans_credentials_tbl` (
  `deans_id` int(11) NOT NULL,
  `id_number` varchar(100) NOT NULL,
  `dean_password` varchar(255) NOT NULL,
  `generated_password` enum('Yes','No') DEFAULT 'Yes',
  `two_factor_authentication` enum('Enabled','Disabled') DEFAULT 'Enabled',
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) NOT NULL,
  `email_address` varchar(150) NOT NULL,
  `role` varchar(100) NOT NULL DEFAULT 'Dean',
  `department_id` int(11) DEFAULT NULL,
  `otp_code` int(6) DEFAULT NULL,
  `otp_code_expiry` datetime DEFAULT NULL,
  `password_reset_token` varchar(255) DEFAULT NULL,
  `reset_token_expiry` datetime DEFAULT NULL,
  `is_archived` enum('Yes','No') DEFAULT 'No',
  `locked_account` enum('Yes','No') DEFAULT 'No',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `deans_credentials_tbl`
--

INSERT INTO `deans_credentials_tbl` (`deans_id`, `id_number`, `dean_password`, `generated_password`, `two_factor_authentication`, `first_name`, `middle_name`, `last_name`, `email_address`, `role`, `department_id`, `otp_code`, `otp_code_expiry`, `password_reset_token`, `reset_token_expiry`, `is_archived`, `locked_account`, `created_at`, `updated_at`, `last_login`) VALUES
(2019, 'UI-24-205-F', '$2y$10$RjJdBsm/z84m19DGSR94WOMVoBlaakYcpUOrYR14.UHmvBvj3NBS6', 'Yes', 'Enabled', 'Mark Jexter', 'A.', 'Sibayan', 'masibayan.ui@phinmaed.com', 'Dean', 113, NULL, NULL, NULL, NULL, 'No', 'No', '2025-11-17 10:22:14', '2025-11-17 10:22:14', NULL),
(2020, 'UI-15-017-A', '$2y$10$BTjmSKY43j5sNeRJsZZSTe7ko6baECHnpNpJZw93D4VJLJ4h1PM5a', 'Yes', 'Enabled', 'Michael', 'H.', 'Rioga', 'mhrioga.ui@phinmaed.com', 'Dean', 114, NULL, NULL, NULL, NULL, 'No', 'No', '2025-11-17 10:20:57', '2025-11-17 10:20:57', NULL),
(2021, 'UI-17-041-P', '$2y$10$bGtxiN9gnOl6TzwDOK.Dp./NH9Ttk.ZDLvaZoZpEuSxVFZVT13QQO', 'Yes', 'Enabled', 'Maybelle', '', 'Payopilin', 'mapayopilin.ui@phinmaed.com', 'Dean', 115, NULL, NULL, NULL, NULL, 'No', 'No', '2025-11-17 10:25:06', '2025-11-17 10:25:06', NULL),
(2022, 'UI-09-139-F', '$2y$10$A/w9ktx.1wTpQ4QrS4yg.Ocadl6XDZ2z3R5WKPdDnMgSK.sRxPBL.', 'Yes', 'Enabled', 'Seth', '', 'Nono', 'sdnono.ui@phinmaed.com', 'Dean', 111, NULL, NULL, NULL, NULL, 'No', 'No', '2025-12-04 02:46:38', '2025-12-04 02:46:38', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `deans_info_tbl`
--

CREATE TABLE `deans_info_tbl` (
  `dean_info_id` int(11) NOT NULL,
  `id_number` varchar(100) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `phone_number` varchar(50) NOT NULL,
  `telephone_number` varchar(50) DEFAULT NULL,
  `temporary_address` varchar(255) DEFAULT NULL,
  `permanent_address` varchar(255) NOT NULL,
  `gender` enum('Male','Female','Others') DEFAULT 'Others',
  `marital_status` enum('Single','Married','Divorced','Widowed') DEFAULT 'Single',
  `facebook_link` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `deans_info_tbl`
--

INSERT INTO `deans_info_tbl` (`dean_info_id`, `id_number`, `profile_picture`, `date_of_birth`, `phone_number`, `telephone_number`, `temporary_address`, `permanent_address`, `gender`, `marital_status`, `facebook_link`, `updated_at`) VALUES
(2019, 'UI-24-205-F', NULL, NULL, '', NULL, '', '', 'Male', 'Single', NULL, '2026-04-14 02:58:54'),
(2020, 'UI-15-017-A', NULL, NULL, '', NULL, '', '', 'Male', 'Single', NULL, '2026-04-14 03:00:57'),
(2021, 'UI-17-041-P', NULL, NULL, '', NULL, '', '', 'Female', 'Single', NULL, '2026-04-14 03:02:08'),
(2022, 'UI-09-139-F', NULL, NULL, '', NULL, '', '', 'Male', 'Single', NULL, '2026-04-14 03:06:38');

-- --------------------------------------------------------

--
-- Table structure for table `departments_tbl`
--

CREATE TABLE `departments_tbl` (
  `department_id` int(11) NOT NULL,
  `department_code` varchar(50) NOT NULL,
  `department_name` varchar(255) NOT NULL,
  `department_status` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments_tbl`
--

INSERT INTO `departments_tbl` (`department_id`, `department_code`, `department_name`, `department_status`, `created_at`, `updated_at`) VALUES
(111, 'CITE', 'College of Information Technology Education', 'Active', '2025-11-15 02:47:04', '2025-11-15 02:47:04'),
(113, 'COE', 'College of Engineering', 'Active', '2025-11-15 02:49:20', '2025-11-15 02:49:20'),
(114, 'CTHM', 'College of Tourism and Hospitality Management', 'Active', '2025-11-15 02:51:02', '2025-11-15 02:51:02'),
(115, 'COA', 'College of Accountancy', 'Active', '2025-11-15 02:52:12', '2025-11-15 02:52:12');

-- --------------------------------------------------------

--
-- Table structure for table `engagement_logs_tbl`
--

CREATE TABLE `engagement_logs_tbl` (
  `engagement_id` int(11) NOT NULL,
  `observation_id` int(11) NOT NULL,
  `engagement` varchar(150) NOT NULL,
  `tally` int(11) NOT NULL DEFAULT 0,
  `minutes` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `events_tbl`
--

CREATE TABLE `events_tbl` (
  `event_id` int(11) NOT NULL,
  `event_type` enum('Single-day','Multi-day') DEFAULT 'Single-day',
  `semester_id` int(11) NOT NULL,
  `observation_id` int(11) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `event_title` varchar(100) DEFAULT NULL,
  `start_date` date DEFAULT curdate(),
  `end_date` date DEFAULT NULL,
  `added_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `events_tbl`
--

INSERT INTO `events_tbl` (`event_id`, `event_type`, `semester_id`, `observation_id`, `department_id`, `event_title`, `start_date`, `end_date`, `added_at`) VALUES
(1, 'Multi-day', 1022, NULL, NULL, 'Prelim Exam', '2026-01-05', '2026-01-10', '2026-02-26 04:26:17'),
(2, 'Multi-day', 1022, NULL, NULL, 'Midterm Exam', '2026-02-09', '2026-02-14', '2026-02-26 04:26:37'),
(3, 'Multi-day', 1022, NULL, NULL, 'Finals Exam', '2026-03-23', '2026-03-28', '2026-02-26 04:26:53');

-- --------------------------------------------------------

--
-- Table structure for table `observations_tbl`
--

CREATE TABLE `observations_tbl` (
  `observation_id` int(11) NOT NULL,
  `copus_type` enum('COPUS 1','COPUS 2','COPUS 3','Summative') NOT NULL,
  `semester_id` int(11) DEFAULT NULL,
  `teacher_id` varchar(100) DEFAULT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `observer_id` varchar(100) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `year_level` enum('First Year','Second Year','Third Year','Fourth Year','Fifth Year') NOT NULL,
  `modality` enum('FLEX (Face-to-Face)','RAD (Online Class)') DEFAULT 'FLEX (Face-to-Face)',
  `observe_status` enum('Complete','Incomplete') DEFAULT 'Incomplete',
  `observed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `observation_comments_tbl`
--

CREATE TABLE `observation_comments_tbl` (
  `comment_id` int(11) NOT NULL,
  `observation_id` int(11) NOT NULL,
  `observation_comment` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `observers_credentials_tbl`
--

CREATE TABLE `observers_credentials_tbl` (
  `observer_id` int(11) NOT NULL,
  `id_number` varchar(100) NOT NULL,
  `observer_password` varchar(255) NOT NULL,
  `generated_password` enum('Yes','No') DEFAULT 'Yes',
  `two_factor_authentication` enum('Enabled','Disabled') DEFAULT 'Enabled',
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) NOT NULL,
  `email_address` varchar(150) NOT NULL,
  `role` varchar(100) NOT NULL DEFAULT 'Observer',
  `designation` enum('Dean','Asst. Dean','Program Head','Faculty','Active Learning Coach (ALC)') DEFAULT 'Faculty',
  `department_id` int(11) DEFAULT NULL,
  `otp_code` int(6) DEFAULT NULL,
  `otp_code_expiry` datetime DEFAULT NULL,
  `password_reset_token` varchar(255) DEFAULT NULL,
  `reset_token_expiry` datetime DEFAULT NULL,
  `is_archived` enum('Yes','No') DEFAULT 'No',
  `locked_account` enum('Yes','No') DEFAULT 'No',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `observers_info_tbl`
--

CREATE TABLE `observers_info_tbl` (
  `observer_info_id` int(11) NOT NULL,
  `id_number` varchar(100) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `phone_number` varchar(50) NOT NULL,
  `telephone_number` varchar(50) DEFAULT NULL,
  `temporary_address` varchar(255) DEFAULT NULL,
  `permanent_address` varchar(255) NOT NULL,
  `gender` enum('Male','Female','Others') DEFAULT 'Others',
  `marital_status` enum('Single','Married','Divorced','Widowed') DEFAULT 'Single',
  `facebook_link` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `semesters_tbl`
--

CREATE TABLE `semesters_tbl` (
  `semester_id` int(11) NOT NULL,
  `academic_year_id` int(11) NOT NULL,
  `semester` varchar(50) NOT NULL,
  `semester_status` varchar(50) NOT NULL DEFAULT 'Inactive'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `semesters_tbl`
--

INSERT INTO `semesters_tbl` (`semester_id`, `academic_year_id`, `semester`, `semester_status`) VALUES
(1021, 119, '1st Semester', 'Inactive'),
(1022, 119, '2nd Semester', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `student_actions_tbl`
--

CREATE TABLE `student_actions_tbl` (
  `action_id` int(11) NOT NULL,
  `action_code` varchar(50) NOT NULL,
  `action_name` varchar(100) NOT NULL,
  `is_active_learning` enum('Yes','No') DEFAULT 'No',
  `action_status` varchar(50) DEFAULT 'Active',
  `modified_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_actions_tbl`
--

INSERT INTO `student_actions_tbl` (`action_id`, `action_code`, `action_name`, `is_active_learning`, `action_status`, `modified_at`) VALUES
(1, 'L', 'Listening', 'No', 'Active', '2025-07-16 12:28:20'),
(2, 'Ind', 'Individual Thinking', 'Yes', 'Active', '2025-10-13 13:49:36'),
(3, 'Grp', 'Group Activity', 'Yes', 'Active', '2025-10-13 13:49:43'),
(4, 'AnQ', 'Answer Questions', 'Yes', 'Active', '2025-10-13 13:49:56'),
(5, 'AsQ', 'Ask Questions', 'Yes', 'Active', '2025-10-13 13:50:01'),
(6, 'WC', 'Whole Class Discussion', 'Yes', 'Active', '2025-10-13 13:50:13'),
(7, 'SP', 'Student Presentations', 'Yes', 'Active', '2025-10-13 13:50:29'),
(8, 'T/Q', 'Test/Quiz', 'Yes', 'Active', '2025-10-13 13:50:35'),
(9, 'W', 'Waiting', 'No', 'Active', '2025-04-25 13:27:25'),
(10, 'O', 'Other', 'No', 'Active', '2025-04-25 13:27:31');

-- --------------------------------------------------------

--
-- Table structure for table `student_action_log_tbl`
--

CREATE TABLE `student_action_log_tbl` (
  `log_in` int(11) NOT NULL,
  `observation_id` int(11) NOT NULL,
  `action_name` varchar(150) NOT NULL,
  `tally` int(11) NOT NULL DEFAULT 0,
  `minutes` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_feedback_tbl`
--

CREATE TABLE `student_feedback_tbl` (
  `feedback_id` int(11) NOT NULL,
  `observation_id` int(11) NOT NULL,
  `feedback_rating` decimal(11,2) DEFAULT 0.00,
  `net_promoter_score` decimal(11,2) DEFAULT 0.00,
  `feedback_form` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subjects_tbl`
--

CREATE TABLE `subjects_tbl` (
  `subject_id` int(111) NOT NULL,
  `subject_code` varchar(20) NOT NULL,
  `subject_name` varchar(100) NOT NULL,
  `subject_units` int(4) NOT NULL,
  `semester` enum('1st Semester','2nd Semester') NOT NULL,
  `department_id` int(11) DEFAULT NULL,
  `subject_status` varchar(20) DEFAULT 'Active',
  `modified_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects_tbl`
--

INSERT INTO `subjects_tbl` (`subject_id`, `subject_code`, `subject_name`, `subject_units`, `semester`, `department_id`, `subject_status`, `modified_at`) VALUES
(1041, 'DEV 1', 'Test Subject 1', 4, '1st Semester', NULL, 'Active', '2026-02-16 03:46:39'),
(1042, 'DEV 2', 'Test Subject 2', 4, '2nd Semester', NULL, 'Active', '2026-02-16 03:47:04'),
(1043, 'DEV 3', 'Test Subject 3', 4, '1st Semester', NULL, 'Active', '2026-02-16 03:48:11'),
(1044, 'DEV 4', 'Test Subject 4', 4, '2nd Semester', NULL, 'Active', '2026-02-16 03:48:34'),
(1045, 'T4', 'Test 4', 2, '2nd Semester', NULL, 'Active', '2026-04-08 11:08:56'),
(1046, 'T1', 'Test 1', 3, '1st Semester', NULL, 'Active', '2026-04-08 11:12:36'),
(1047, 'T2', 'Test 2', 2, '2nd Semester', NULL, 'Active', '2026-04-08 11:12:36'),
(1048, 'T3', 'Test 3', 3, '1st Semester', NULL, 'Active', '2026-04-08 11:12:36');

-- --------------------------------------------------------

--
-- Table structure for table `summative_observations_tbl`
--

CREATE TABLE `summative_observations_tbl` (
  `summative_id` int(11) NOT NULL,
  `observation_id` int(11) NOT NULL,
  `high_count` int(11) DEFAULT 0,
  `medium_count` int(11) DEFAULT 0,
  `low_count` int(11) DEFAULT 0,
  `no_count` int(11) DEFAULT 0,
  `high_percentage` decimal(11,2) DEFAULT 0.00,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `teacher_actions_tbl`
--

CREATE TABLE `teacher_actions_tbl` (
  `action_id` int(11) NOT NULL,
  `action_code` varchar(50) NOT NULL,
  `action_name` varchar(100) NOT NULL,
  `is_active_learning` enum('Yes','No') DEFAULT 'No',
  `action_status` varchar(20) DEFAULT 'Active',
  `modified_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teacher_actions_tbl`
--

INSERT INTO `teacher_actions_tbl` (`action_id`, `action_code`, `action_name`, `is_active_learning`, `action_status`, `modified_at`) VALUES
(2001, 'L', 'Lecture', 'No', 'Active', '2025-07-16 12:21:28'),
(2002, 'RtW', 'Realtime Writing', 'No', 'Active', '2025-04-25 13:01:27'),
(2003, 'M/G', 'Moving/Guiding', 'Yes', 'Active', '2025-10-13 13:31:02'),
(2004, 'AnQ', 'Answer Question', 'Yes', 'Active', '2025-10-13 13:31:11'),
(2005, 'PQ', 'Pose Question', 'Yes', 'Active', '2025-10-13 13:31:40'),
(2006, 'FUp', 'Follow-up Question', 'Yes', 'Active', '2025-10-13 13:31:55'),
(2007, '1o1', '1-on-1 Discussion', 'Yes', 'Active', '2025-10-13 13:32:12'),
(2008, 'D/V', 'Demonstrate/Video', 'Yes', 'Active', '2025-10-13 13:32:42'),
(2009, 'Adm', 'Administrative Task', 'Yes', 'Active', '2025-10-13 13:32:50'),
(2010, 'W', 'Waiting', 'No', 'Active', '2025-04-25 13:25:08'),
(2011, 'O', 'Other', 'No', 'Active', '2025-04-25 13:25:19');

-- --------------------------------------------------------

--
-- Table structure for table `teacher_action_log_tbl`
--

CREATE TABLE `teacher_action_log_tbl` (
  `log_id` int(11) NOT NULL,
  `observation_id` int(11) NOT NULL,
  `action_name` varchar(150) NOT NULL,
  `tally` int(11) NOT NULL DEFAULT 0,
  `minutes` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `teacher_credentials_tbl`
--

CREATE TABLE `teacher_credentials_tbl` (
  `teacher_id` int(11) NOT NULL,
  `id_number` varchar(100) NOT NULL,
  `teacher_password` varchar(255) NOT NULL,
  `generated_password` enum('Yes','No') DEFAULT 'Yes',
  `two_factor_authentication` enum('Enabled','Disabled') DEFAULT 'Enabled',
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) NOT NULL,
  `email_address` varchar(150) NOT NULL,
  `role` varchar(100) NOT NULL DEFAULT 'Teacher',
  `department_id` int(11) DEFAULT NULL,
  `employment_status` enum('Contractual','Full-Time','Part-Time') NOT NULL,
  `teacher_rank` enum('Instructor','Assistant Professor','Associate Professor','Professor','Exemplary Teacher','Master Teacher','N/A') DEFAULT 'Instructor',
  `otp_code` int(6) DEFAULT NULL,
  `otp_code_expiry` datetime DEFAULT NULL,
  `password_reset_token` varchar(255) DEFAULT NULL,
  `password_reset_expiry` datetime DEFAULT NULL,
  `is_archived` enum('Yes','No') DEFAULT 'No',
  `locked_account` enum('Yes','No') DEFAULT 'No',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `teacher_info_tbl`
--

CREATE TABLE `teacher_info_tbl` (
  `teacher_info_id` int(11) NOT NULL,
  `id_number` varchar(100) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `phone_number` varchar(50) NOT NULL,
  `telephone_number` varchar(50) DEFAULT NULL,
  `temporary_address` varchar(255) DEFAULT NULL,
  `permanent_address` varchar(255) NOT NULL,
  `gender` enum('Male','Female','Others') DEFAULT 'Others',
  `marital_status` enum('Single','Married','Divorced','Widowed') DEFAULT 'Single',
  `facebook_link` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `academic_years_tbl`
--
ALTER TABLE `academic_years_tbl`
  ADD PRIMARY KEY (`academic_year_id`),
  ADD UNIQUE KEY `academic_year` (`academic_year`);

--
-- Indexes for table `admin_credentials_tbl`
--
ALTER TABLE `admin_credentials_tbl`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `id_number` (`id_number`),
  ADD UNIQUE KEY `email_address` (`email_address`);

--
-- Indexes for table `admin_info_tbl`
--
ALTER TABLE `admin_info_tbl`
  ADD PRIMARY KEY (`admin_info_id`),
  ADD UNIQUE KEY `id_number` (`id_number`);

--
-- Indexes for table `copus_forms_tbl`
--
ALTER TABLE `copus_forms_tbl`
  ADD PRIMARY KEY (`pdf_id`),
  ADD KEY `observation_id` (`observation_id`);

--
-- Indexes for table `deans_credentials_tbl`
--
ALTER TABLE `deans_credentials_tbl`
  ADD PRIMARY KEY (`deans_id`),
  ADD UNIQUE KEY `id_number` (`id_number`),
  ADD UNIQUE KEY `email_address` (`email_address`),
  ADD KEY `fk_dean_department` (`department_id`);

--
-- Indexes for table `deans_info_tbl`
--
ALTER TABLE `deans_info_tbl`
  ADD PRIMARY KEY (`dean_info_id`),
  ADD KEY `id_number` (`id_number`);

--
-- Indexes for table `departments_tbl`
--
ALTER TABLE `departments_tbl`
  ADD PRIMARY KEY (`department_id`),
  ADD UNIQUE KEY `department_code` (`department_code`),
  ADD UNIQUE KEY `department_name` (`department_name`);

--
-- Indexes for table `engagement_logs_tbl`
--
ALTER TABLE `engagement_logs_tbl`
  ADD PRIMARY KEY (`engagement_id`),
  ADD KEY `observation_id` (`observation_id`);

--
-- Indexes for table `events_tbl`
--
ALTER TABLE `events_tbl`
  ADD PRIMARY KEY (`event_id`),
  ADD KEY `semester_id` (`semester_id`),
  ADD KEY `observation_id` (`observation_id`),
  ADD KEY `fk_department` (`department_id`);

--
-- Indexes for table `observations_tbl`
--
ALTER TABLE `observations_tbl`
  ADD PRIMARY KEY (`observation_id`),
  ADD KEY `semester_id` (`semester_id`),
  ADD KEY `teacher_id` (`teacher_id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `observer_id` (`observer_id`),
  ADD KEY `fk_observation_department` (`department_id`);

--
-- Indexes for table `observation_comments_tbl`
--
ALTER TABLE `observation_comments_tbl`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `observation_id` (`observation_id`);

--
-- Indexes for table `observers_credentials_tbl`
--
ALTER TABLE `observers_credentials_tbl`
  ADD PRIMARY KEY (`observer_id`),
  ADD UNIQUE KEY `id_number` (`id_number`),
  ADD UNIQUE KEY `email_address` (`email_address`),
  ADD KEY `fk_observers_department` (`department_id`);

--
-- Indexes for table `observers_info_tbl`
--
ALTER TABLE `observers_info_tbl`
  ADD PRIMARY KEY (`observer_info_id`),
  ADD KEY `id_number` (`id_number`);

--
-- Indexes for table `semesters_tbl`
--
ALTER TABLE `semesters_tbl`
  ADD PRIMARY KEY (`semester_id`),
  ADD KEY `academic_year_id` (`academic_year_id`);

--
-- Indexes for table `student_actions_tbl`
--
ALTER TABLE `student_actions_tbl`
  ADD PRIMARY KEY (`action_id`),
  ADD UNIQUE KEY `action_code` (`action_code`);

--
-- Indexes for table `student_action_log_tbl`
--
ALTER TABLE `student_action_log_tbl`
  ADD PRIMARY KEY (`log_in`),
  ADD KEY `observation_id` (`observation_id`);

--
-- Indexes for table `student_feedback_tbl`
--
ALTER TABLE `student_feedback_tbl`
  ADD PRIMARY KEY (`feedback_id`),
  ADD KEY `observation_id` (`observation_id`);

--
-- Indexes for table `subjects_tbl`
--
ALTER TABLE `subjects_tbl`
  ADD PRIMARY KEY (`subject_id`),
  ADD UNIQUE KEY `subject_code` (`subject_code`),
  ADD KEY `fk_subject_department` (`department_id`);

--
-- Indexes for table `summative_observations_tbl`
--
ALTER TABLE `summative_observations_tbl`
  ADD PRIMARY KEY (`summative_id`),
  ADD KEY `observation_id` (`observation_id`);

--
-- Indexes for table `teacher_actions_tbl`
--
ALTER TABLE `teacher_actions_tbl`
  ADD PRIMARY KEY (`action_id`),
  ADD UNIQUE KEY `action_code` (`action_code`);

--
-- Indexes for table `teacher_action_log_tbl`
--
ALTER TABLE `teacher_action_log_tbl`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `observation_id` (`observation_id`);

--
-- Indexes for table `teacher_credentials_tbl`
--
ALTER TABLE `teacher_credentials_tbl`
  ADD PRIMARY KEY (`teacher_id`),
  ADD UNIQUE KEY `id_number` (`id_number`),
  ADD UNIQUE KEY `email_address` (`email_address`),
  ADD KEY `fk_teacher_department` (`department_id`);

--
-- Indexes for table `teacher_info_tbl`
--
ALTER TABLE `teacher_info_tbl`
  ADD PRIMARY KEY (`teacher_info_id`),
  ADD KEY `teacher_info__tbl_ibfk_1` (`id_number`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `academic_years_tbl`
--
ALTER TABLE `academic_years_tbl`
  MODIFY `academic_year_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=120;

--
-- AUTO_INCREMENT for table `admin_credentials_tbl`
--
ALTER TABLE `admin_credentials_tbl`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1020;

--
-- AUTO_INCREMENT for table `admin_info_tbl`
--
ALTER TABLE `admin_info_tbl`
  MODIFY `admin_info_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1020;

--
-- AUTO_INCREMENT for table `copus_forms_tbl`
--
ALTER TABLE `copus_forms_tbl`
  MODIFY `pdf_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `deans_credentials_tbl`
--
ALTER TABLE `deans_credentials_tbl`
  MODIFY `deans_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2023;

--
-- AUTO_INCREMENT for table `deans_info_tbl`
--
ALTER TABLE `deans_info_tbl`
  MODIFY `dean_info_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2023;

--
-- AUTO_INCREMENT for table `departments_tbl`
--
ALTER TABLE `departments_tbl`
  MODIFY `department_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=116;

--
-- AUTO_INCREMENT for table `engagement_logs_tbl`
--
ALTER TABLE `engagement_logs_tbl`
  MODIFY `engagement_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1112;

--
-- AUTO_INCREMENT for table `events_tbl`
--
ALTER TABLE `events_tbl`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `observations_tbl`
--
ALTER TABLE `observations_tbl`
  MODIFY `observation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1067;

--
-- AUTO_INCREMENT for table `observation_comments_tbl`
--
ALTER TABLE `observation_comments_tbl`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1073;

--
-- AUTO_INCREMENT for table `observers_credentials_tbl`
--
ALTER TABLE `observers_credentials_tbl`
  MODIFY `observer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `observers_info_tbl`
--
ALTER TABLE `observers_info_tbl`
  MODIFY `observer_info_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `semesters_tbl`
--
ALTER TABLE `semesters_tbl`
  MODIFY `semester_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1023;

--
-- AUTO_INCREMENT for table `student_actions_tbl`
--
ALTER TABLE `student_actions_tbl`
  MODIFY `action_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `student_action_log_tbl`
--
ALTER TABLE `student_action_log_tbl`
  MODIFY `log_in` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1599;

--
-- AUTO_INCREMENT for table `student_feedback_tbl`
--
ALTER TABLE `student_feedback_tbl`
  MODIFY `feedback_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1022;

--
-- AUTO_INCREMENT for table `subjects_tbl`
--
ALTER TABLE `subjects_tbl`
  MODIFY `subject_id` int(111) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1062;

--
-- AUTO_INCREMENT for table `summative_observations_tbl`
--
ALTER TABLE `summative_observations_tbl`
  MODIFY `summative_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10017;

--
-- AUTO_INCREMENT for table `teacher_actions_tbl`
--
ALTER TABLE `teacher_actions_tbl`
  MODIFY `action_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2013;

--
-- AUTO_INCREMENT for table `teacher_action_log_tbl`
--
ALTER TABLE `teacher_action_log_tbl`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1650;

--
-- AUTO_INCREMENT for table `teacher_credentials_tbl`
--
ALTER TABLE `teacher_credentials_tbl`
  MODIFY `teacher_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3023;

--
-- AUTO_INCREMENT for table `teacher_info_tbl`
--
ALTER TABLE `teacher_info_tbl`
  MODIFY `teacher_info_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3022;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_info_tbl`
--
ALTER TABLE `admin_info_tbl`
  ADD CONSTRAINT `admin_info_tbl_ibfk_1` FOREIGN KEY (`id_number`) REFERENCES `admin_credentials_tbl` (`id_number`) ON DELETE CASCADE;

--
-- Constraints for table `copus_forms_tbl`
--
ALTER TABLE `copus_forms_tbl`
  ADD CONSTRAINT `copus_forms_tbl_ibfk_1` FOREIGN KEY (`observation_id`) REFERENCES `observations_tbl` (`observation_id`) ON DELETE CASCADE;

--
-- Constraints for table `deans_credentials_tbl`
--
ALTER TABLE `deans_credentials_tbl`
  ADD CONSTRAINT `fk_dean_department` FOREIGN KEY (`department_id`) REFERENCES `departments_tbl` (`department_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `deans_info_tbl`
--
ALTER TABLE `deans_info_tbl`
  ADD CONSTRAINT `deans_info_tbl_ibfk_1` FOREIGN KEY (`id_number`) REFERENCES `deans_credentials_tbl` (`id_number`) ON DELETE CASCADE;

--
-- Constraints for table `engagement_logs_tbl`
--
ALTER TABLE `engagement_logs_tbl`
  ADD CONSTRAINT `engagement_logs_tbl_ibfk_1` FOREIGN KEY (`observation_id`) REFERENCES `observations_tbl` (`observation_id`) ON DELETE CASCADE;

--
-- Constraints for table `events_tbl`
--
ALTER TABLE `events_tbl`
  ADD CONSTRAINT `events_tbl_ibfk_1` FOREIGN KEY (`semester_id`) REFERENCES `semesters_tbl` (`semester_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `events_tbl_ibfk_2` FOREIGN KEY (`observation_id`) REFERENCES `observations_tbl` (`observation_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_department` FOREIGN KEY (`department_id`) REFERENCES `departments_tbl` (`department_id`) ON DELETE SET NULL;

--
-- Constraints for table `observations_tbl`
--
ALTER TABLE `observations_tbl`
  ADD CONSTRAINT `fk_observation_department` FOREIGN KEY (`department_id`) REFERENCES `departments_tbl` (`department_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `observations_tbl_ibfk_1` FOREIGN KEY (`semester_id`) REFERENCES `semesters_tbl` (`semester_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `observations_tbl_ibfk_2` FOREIGN KEY (`teacher_id`) REFERENCES `teacher_credentials_tbl` (`id_number`) ON DELETE SET NULL,
  ADD CONSTRAINT `observations_tbl_ibfk_3` FOREIGN KEY (`subject_id`) REFERENCES `subjects_tbl` (`subject_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `observations_tbl_ibfk_4` FOREIGN KEY (`observer_id`) REFERENCES `observers_credentials_tbl` (`id_number`) ON DELETE SET NULL;

--
-- Constraints for table `observation_comments_tbl`
--
ALTER TABLE `observation_comments_tbl`
  ADD CONSTRAINT `observation_comments_tbl_ibfk_1` FOREIGN KEY (`observation_id`) REFERENCES `observations_tbl` (`observation_id`) ON DELETE CASCADE;

--
-- Constraints for table `observers_credentials_tbl`
--
ALTER TABLE `observers_credentials_tbl`
  ADD CONSTRAINT `fk_observers_department` FOREIGN KEY (`department_id`) REFERENCES `departments_tbl` (`department_id`) ON DELETE SET NULL;

--
-- Constraints for table `observers_info_tbl`
--
ALTER TABLE `observers_info_tbl`
  ADD CONSTRAINT `observers_info_tbl_ibfk_1` FOREIGN KEY (`id_number`) REFERENCES `observers_credentials_tbl` (`id_number`) ON DELETE CASCADE;

--
-- Constraints for table `semesters_tbl`
--
ALTER TABLE `semesters_tbl`
  ADD CONSTRAINT `semesters_tbl_ibfk_1` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years_tbl` (`academic_year_id`) ON DELETE CASCADE;

--
-- Constraints for table `student_action_log_tbl`
--
ALTER TABLE `student_action_log_tbl`
  ADD CONSTRAINT `student_action_log_tbl_ibfk_1` FOREIGN KEY (`observation_id`) REFERENCES `observations_tbl` (`observation_id`) ON DELETE CASCADE;

--
-- Constraints for table `student_feedback_tbl`
--
ALTER TABLE `student_feedback_tbl`
  ADD CONSTRAINT `student_feedback_tbl_ibfk_1` FOREIGN KEY (`observation_id`) REFERENCES `observations_tbl` (`observation_id`) ON DELETE CASCADE;

--
-- Constraints for table `subjects_tbl`
--
ALTER TABLE `subjects_tbl`
  ADD CONSTRAINT `fk_subject_department` FOREIGN KEY (`department_id`) REFERENCES `departments_tbl` (`department_id`) ON DELETE SET NULL;

--
-- Constraints for table `summative_observations_tbl`
--
ALTER TABLE `summative_observations_tbl`
  ADD CONSTRAINT `summative_observations_tbl_ibfk_1` FOREIGN KEY (`observation_id`) REFERENCES `observations_tbl` (`observation_id`) ON DELETE CASCADE;

--
-- Constraints for table `teacher_action_log_tbl`
--
ALTER TABLE `teacher_action_log_tbl`
  ADD CONSTRAINT `teacher_action_log_tbl_ibfk_1` FOREIGN KEY (`observation_id`) REFERENCES `observations_tbl` (`observation_id`) ON DELETE CASCADE;

--
-- Constraints for table `teacher_credentials_tbl`
--
ALTER TABLE `teacher_credentials_tbl`
  ADD CONSTRAINT `fk_teacher_department` FOREIGN KEY (`department_id`) REFERENCES `departments_tbl` (`department_id`) ON DELETE SET NULL;

--
-- Constraints for table `teacher_info_tbl`
--
ALTER TABLE `teacher_info_tbl`
  ADD CONSTRAINT `teacher_info__tbl_ibfk_1` FOREIGN KEY (`id_number`) REFERENCES `teacher_credentials_tbl` (`id_number`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
