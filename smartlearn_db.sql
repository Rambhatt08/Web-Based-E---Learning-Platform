-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 14, 2026 at 07:57 AM
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
-- Database: `smartlearn_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `subject`, `message`, `submitted_at`) VALUES
(1, 'RamBhatt', 'rambhatt23092021@gmail.com', 'DBMS', 'can you please uplaod the notes for the DBMS', '2026-03-13 16:29:16');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `level` varchar(50) DEFAULT 'Beginner',
  `thumbnail` varchar(255) DEFAULT NULL,
  `total_enrolled` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `title`, `description`, `level`, `thumbnail`, `total_enrolled`, `created_at`) VALUES
(8, 'Database Management System (DBMS)', 'Introduction to database concepts, relational models, SQL queries, and techniques for storing and managing data efficiently.', 'Beginner', 'uploads/courses/1773400410_course-dbms.png', 2, '2026-03-13 11:13:30'),
(10, 'Java Programming', 'Learn Java programming basics including classes, objects, inheritance, polymorphism, and application development concepts.', 'Beginner', 'uploads/courses/1773401315_java.png', 0, '2026-03-13 11:28:35'),
(11, 'Web Development', 'Learn web development basics including HTML, CSS, JavaScript, and designing modern responsive websites.', 'Intermediate', 'uploads/courses/1773401486_Web-Development-1.png', 0, '2026-03-13 11:31:26'),
(13, 'Python programming', 'Learn Python fundamentals including variables, loops, functions, and simple application development concepts.', 'Beginner', 'uploads/courses/1773401841_python.jpg', 2, '2026-03-13 11:37:21');

-- --------------------------------------------------------

--
-- Table structure for table `course_lectures`
--

CREATE TABLE `course_lectures` (
  `id` int(11) UNSIGNED NOT NULL,
  `course_id` int(11) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `video_url` text NOT NULL,
  `duration` varchar(50) DEFAULT NULL,
  `lecture_order` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course_lectures`
--

INSERT INTO `course_lectures` (`id`, `course_id`, `title`, `video_url`, `duration`, `lecture_order`) VALUES
(20, 8, 'Lec-1: DBMS Syllabus for GATE, UGCNET, NIELIT, DSSSB etc.| Full DBMS for College/University Students', 'https://youtu.be/kBdlM6hNDAE?si=H3WEqRkfHumGP3oP', '18:04', 1),
(21, 8, 'Lec-2: Introduction to DBMS (Database Management System) With Real life examples | What is DBMS', 'https://youtu.be/3EJlovevfcA?si=dQcXVgI_V2ly3HCh', '12:00', 2),
(22, 8, ' Lec-3: File System vs DBMS | Disadvantages of File System | DBMS Advantages', 'uploads/videos/1773400556_Lec_3_File_System_vs_DBMS__Disadvantages_of_File_System__DBMS_Advantages.mp4', '13:00', 3),
(25, 10, ' Java Tutorial for Beginners | Learn Java in 2 Hours', 'https://youtu.be/UmnCZ7-9yDY?si=8xLaOIxO9HkLQKDs', '2:04:35', 1),
(26, 11, 'Installing VS Code & How Websites Work | Sigma Web Development Course - Tutorial #1', 'https://youtu.be/tVzUXW6siu0?si=aXbDn38CdI2508b_', '31:19', 1),
(27, 11, 'Your First HTML Website | Sigma Web Development Course - Tutorial #2', 'https://youtu.be/kJEsTjH5mVg?si=Mco2jUSz_haqzRxr', '28:31', 2),
(28, 13, 'Introduction to Programming & Python | Python Tutorial - Day #1', 'uploads/videos/1773401918_Introduction_to_Programming___Python__Python_Tutorial___Day__1.mp4', '11:49', 1),
(29, 13, ' Some Amazing Python Programs - The Power of Python | Python Tutorial - Day #2', 'uploads/videos/1773402197_Some_Amazing_Python_Programs___The_Power_of_Python__Python_Tutorial___Day__2.mp4', '8:37', 2);

-- --------------------------------------------------------

--
-- Table structure for table `course_qa`
--

CREATE TABLE `course_qa` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `course_id` int(11) UNSIGNED NOT NULL,
  `question_text` text NOT NULL,
  `admin_reply` text DEFAULT NULL,
  `status` enum('pending','answered') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `replied_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course_qa`
--

INSERT INTO `course_qa` (`id`, `user_id`, `course_id`, `question_text`, `admin_reply`, `status`, `created_at`, `replied_at`) VALUES
(5, 1, 8, 'I have question about DBMS, is there any possibilty of new videos in future', 'Yes new video is coming soon, Stay Tuned', 'answered', '2026-03-13 12:08:28', '2026-03-13 12:09:40'),
(6, 3, 8, 'New video', 'Soon', 'answered', '2026-03-14 06:25:21', '2026-03-14 06:36:51');

-- --------------------------------------------------------

--
-- Table structure for table `ebooks`
--

CREATE TABLE `ebooks` (
  `id` int(11) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `branch` varchar(100) DEFAULT NULL,
  `year_level` varchar(50) DEFAULT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ebooks`
--

INSERT INTO `ebooks` (`id`, `title`, `branch`, `year_level`, `cover_image`, `file_path`, `uploaded_at`) VALUES
(1, 'Data Structures and Algorithms (DSA) E - Book', 'CE', '2nd Year', 'uploads/thumbnails/1773397321_cover_DSA E - book.png', 'uploads/ebooks/1773397321_DSA E book.pdf', '2026-03-13 10:22:01'),
(2, 'Introduction to Algorithms, Third Edition', 'CE', '3rd Year', 'uploads/thumbnails/1773402628_cover_algorithms.png', 'uploads/ebooks/1773402628_Reference Books-20250609T122124Z-1-001.zip', '2026-03-13 11:50:28');

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `course_id` int(11) UNSIGNED NOT NULL,
  `enrolled_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('active','completed') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enrollments`
--

INSERT INTO `enrollments` (`id`, `user_id`, `course_id`, `enrolled_at`, `status`) VALUES
(1, 1, 1, '2026-02-08 07:31:07', 'active'),
(2, 1, 2, '2026-03-11 07:15:09', 'completed'),
(3, 1, 3, '2026-03-11 07:29:33', 'active'),
(4, 3, 3, '2026-03-12 09:01:22', 'active'),
(5, 3, 1, '2026-03-12 15:52:34', 'active'),
(6, 1, 6, '2026-03-13 10:29:58', 'active'),
(7, 1, 4, '2026-03-13 10:30:31', 'active'),
(8, 1, 7, '2026-03-13 10:54:51', 'active'),
(9, 1, 13, '2026-03-13 11:46:56', 'active'),
(10, 1, 8, '2026-03-13 11:47:07', 'completed'),
(11, 3, 13, '2026-03-14 06:20:39', 'completed'),
(12, 3, 8, '2026-03-14 06:23:35', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `login_logs`
--

CREATE TABLE `login_logs` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `login_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login_logs`
--

INSERT INTO `login_logs` (`id`, `user_id`, `user_name`, `login_time`) VALUES
(1, 1, 'rambhatt08', '2026-02-08 05:24:55'),
(2, 2, 'admin', '2026-02-08 05:29:55'),
(3, 1, 'rambhatt08', '2026-02-08 05:44:03'),
(4, 1, 'rambhatt08', '2026-02-08 06:55:57'),
(5, 2, 'admin', '2026-02-08 07:09:03'),
(6, 1, 'rambhatt08', '2026-02-08 07:30:41'),
(7, 2, 'admin', '2026-02-08 07:31:48'),
(8, 1, 'rambhatt08', '2026-02-08 07:44:48'),
(9, 1, 'rambhatt08', '2026-02-14 05:43:46'),
(10, 2, 'admin', '2026-02-14 05:46:34'),
(11, 1, 'rambhatt08', '2026-02-14 05:49:49'),
(12, 2, 'admin', '2026-02-14 06:07:47'),
(13, 1, 'rambhatt08', '2026-02-14 06:22:33'),
(14, 2, 'admin', '2026-03-10 04:44:47'),
(15, 1, 'rambhatt08', '2026-03-10 04:48:11'),
(16, 2, 'admin', '2026-03-10 05:07:58'),
(17, 1, 'rambhatt08', '2026-03-10 05:13:46'),
(18, 2, 'admin', '2026-03-10 05:21:13'),
(19, 1, 'rambhatt08', '2026-03-10 05:23:34'),
(20, 2, 'admin', '2026-03-10 05:57:25'),
(21, 1, 'rambhatt08', '2026-03-10 06:04:20'),
(22, 2, 'admin', '2026-03-10 06:14:39'),
(23, 1, 'rambhatt08', '2026-03-10 06:18:09'),
(24, 2, 'admin', '2026-03-10 06:18:48'),
(25, 1, 'rambhatt08', '2026-03-10 06:22:36'),
(26, 2, 'admin', '2026-03-10 08:15:25'),
(27, 1, 'rambhatt08', '2026-03-10 08:16:50'),
(28, 2, 'admin', '2026-03-10 08:28:38'),
(29, 1, 'rambhatt08', '2026-03-10 08:34:28'),
(30, 2, 'admin', '2026-03-10 08:34:54'),
(31, 2, 'admin', '2026-03-11 07:11:04'),
(32, 1, 'rambhatt08', '2026-03-11 07:13:43'),
(33, 2, 'admin', '2026-03-11 07:17:02'),
(34, 1, 'rambhatt08', '2026-03-11 07:19:07'),
(35, 2, 'admin', '2026-03-11 07:20:55'),
(36, 1, 'rambhatt08', '2026-03-11 07:22:09'),
(37, 2, 'admin', '2026-03-11 07:27:20'),
(38, 1, 'rambhatt08', '2026-03-11 07:29:26'),
(39, 2, 'admin', '2026-03-11 07:42:47'),
(40, 1, 'rambhatt08', '2026-03-11 07:45:54'),
(41, 2, 'admin', '2026-03-11 08:05:11'),
(42, 1, 'rambhatt08', '2026-03-11 08:05:56'),
(43, 2, 'admin', '2026-03-11 08:07:12'),
(44, 1, 'rambhatt08', '2026-03-12 08:57:40'),
(45, 3, 'demologin', '2026-03-12 09:01:05'),
(46, 2, 'admin', '2026-03-12 09:03:45'),
(47, 1, 'rambhatt08', '2026-03-12 15:51:25'),
(48, 3, 'demologin', '2026-03-12 15:52:16'),
(49, 2, 'admin', '2026-03-13 09:44:12'),
(50, 1, 'rambhatt08', '2026-03-13 10:27:51'),
(51, 2, 'admin', '2026-03-13 10:39:37'),
(52, 1, 'rambhatt08', '2026-03-13 10:42:02'),
(53, 2, 'admin', '2026-03-13 10:52:13'),
(54, 1, 'rambhatt08', '2026-03-13 10:52:37'),
(55, 2, 'admin', '2026-03-13 10:52:51'),
(56, 1, 'rambhatt08', '2026-03-13 10:54:47'),
(57, 2, 'admin', '2026-03-13 10:55:47'),
(58, 1, 'rambhatt08', '2026-03-13 11:01:34'),
(59, 2, 'admin', '2026-03-13 11:11:52'),
(60, 1, 'rambhatt08', '2026-03-13 11:16:38'),
(61, 2, 'admin', '2026-03-13 11:21:55'),
(62, 1, 'rambhatt08', '2026-03-13 11:32:48'),
(63, 2, 'admin', '2026-03-13 11:33:16'),
(64, 1, 'rambhatt08', '2026-03-13 11:46:44'),
(65, 2, 'admin', '2026-03-13 11:49:37'),
(66, 1, 'rambhatt08', '2026-03-13 11:51:03'),
(67, 1, 'rambhatt08', '2026-03-13 11:53:09'),
(68, 1, 'rambhatt08', '2026-03-13 11:53:49'),
(69, 1, 'rambhatt08', '2026-03-13 12:02:58'),
(70, 2, 'admin', '2026-03-13 12:08:51'),
(71, 2, 'admin', '2026-03-13 14:37:14'),
(72, 1, 'rambhatt08', '2026-03-13 14:55:02'),
(73, 2, 'admin', '2026-03-13 14:55:47'),
(74, 2, 'admin', '2026-03-13 14:56:58'),
(75, 1, 'rambhatt08', '2026-03-13 14:57:15'),
(76, 2, 'admin', '2026-03-13 14:58:17'),
(77, 1, 'rambhatt08', '2026-03-13 15:01:54'),
(78, 2, 'admin', '2026-03-13 15:02:52'),
(79, 1, 'rambhatt08', '2026-03-13 15:07:31'),
(80, 2, 'admin', '2026-03-13 15:08:52'),
(81, 1, 'rambhatt08', '2026-03-13 15:21:30'),
(82, 2, 'admin', '2026-03-13 15:22:16'),
(83, 1, 'rambhatt08', '2026-03-13 15:22:58'),
(84, 2, 'admin', '2026-03-13 15:23:44'),
(85, 2, 'admin', '2026-03-13 15:37:46'),
(86, 1, 'rambhatt08', '2026-03-13 15:59:00'),
(87, 2, 'admin', '2026-03-13 16:00:11'),
(88, 2, 'admin', '2026-03-13 16:29:49'),
(89, 1, 'rambhatt08', '2026-03-14 05:32:05'),
(90, 2, 'admin', '2026-03-14 05:35:37'),
(91, 3, 'demologin', '2026-03-14 06:14:00'),
(92, 3, 'demologin', '2026-03-14 06:19:02'),
(93, 2, 'admin', '2026-03-14 06:32:40');

-- --------------------------------------------------------

--
-- Table structure for table `newsletter`
--

CREATE TABLE `newsletter` (
  `id` int(11) UNSIGNED NOT NULL,
  `email` varchar(100) NOT NULL,
  `subscribed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `newsletter`
--

INSERT INTO `newsletter` (`id`, `email`, `subscribed_at`) VALUES
(1, 'rambhatt23092021@gmail.com', '2026-02-08 05:35:59');

-- --------------------------------------------------------

--
-- Table structure for table `notes`
--

CREATE TABLE `notes` (
  `id` int(11) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `branch` varchar(100) NOT NULL,
  `year_level` varchar(50) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notes`
--

INSERT INTO `notes` (`id`, `title`, `branch`, `year_level`, `subject`, `file_path`, `thumbnail`, `uploaded_at`) VALUES
(1, '1 - Introduction', 'CE', '2nd Year', 'Data Structures and Algorithms (DSA)', 'uploads/notes/1773397034_1 - Introduction.pdf', 'uploads/thumbnails/1773397034_course-dsa.png', '2026-03-13 10:17:14'),
(2, '2 - Data Structures', 'CE', '2nd Year', 'Data Structures and Algorithms (DSA)', 'uploads/notes/1773397130_2 - Data Structures.pdf', 'uploads/thumbnails/1773397130_course-dsa.png', '2026-03-13 10:18:50');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) UNSIGNED NOT NULL,
  `email` varchar(100) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quizzes`
--

CREATE TABLE `quizzes` (
  `id` int(11) UNSIGNED NOT NULL,
  `course_id` int(11) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quizzes`
--

INSERT INTO `quizzes` (`id`, `course_id`, `title`, `created_at`) VALUES
(7, 8, 'DBMS Fundamentals Quiz', '2026-03-13 11:43:51');

-- --------------------------------------------------------

--
-- Table structure for table `quiz_attempts`
--

CREATE TABLE `quiz_attempts` (
  `id` int(11) UNSIGNED NOT NULL,
  `quiz_id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `score` int(11) NOT NULL,
  `total_questions` int(11) NOT NULL,
  `attempted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quiz_attempts`
--

INSERT INTO `quiz_attempts` (`id`, `quiz_id`, `user_id`, `score`, `total_questions`, `attempted_at`) VALUES
(17, 7, 1, 2, 5, '2026-03-13 15:23:17'),
(18, 7, 3, 3, 6, '2026-03-14 06:24:32');

-- --------------------------------------------------------

--
-- Table structure for table `quiz_questions`
--

CREATE TABLE `quiz_questions` (
  `id` int(11) UNSIGNED NOT NULL,
  `quiz_id` int(11) UNSIGNED NOT NULL,
  `question_text` text NOT NULL,
  `option_a` varchar(255) NOT NULL,
  `option_b` varchar(255) NOT NULL,
  `option_c` varchar(255) NOT NULL,
  `option_d` varchar(255) NOT NULL,
  `correct_option` enum('A','B','C','D') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quiz_questions`
--

INSERT INTO `quiz_questions` (`id`, `quiz_id`, `question_text`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`) VALUES
(14, 7, 'What does DBMS stand for?', 'Data Backup Management System', 'Database Management System', 'Digital Base Management System', 'Data Binary Management System', 'B'),
(15, 7, 'Which of the following is used to uniquely identify a record in a table?', 'Foreign Key', 'Primary Key', 'Candidate Key', 'Composite Key', 'B'),
(16, 7, 'Which language is used to interact with databases?', 'HTML', 'SQL', 'Python', 'Java', 'B'),
(17, 7, 'Which SQL command is used to retrieve data from a database?', 'INSERT', 'UPDATE', 'SELECT', 'DELETE', 'C'),
(18, 7, 'What is the main purpose of normalization?', 'Increase data redundancy', 'Remove data redundancy', 'Delete tables', 'Improve hardware performance', 'B'),
(19, 7, 'Which DBMS model organizes data in the form of tables?', 'Hierarchical Model', 'Network Model', 'Relational Model', 'Object Model', 'C');

-- --------------------------------------------------------

--
-- Table structure for table `quiz_responses`
--

CREATE TABLE `quiz_responses` (
  `id` int(11) NOT NULL,
  `attempt_id` int(11) DEFAULT NULL,
  `question_id` int(11) DEFAULT NULL,
  `selected_option` varchar(2) DEFAULT NULL,
  `is_correct` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quiz_responses`
--

INSERT INTO `quiz_responses` (`id`, `attempt_id`, `question_id`, `selected_option`, `is_correct`) VALUES
(31, 17, 14, 'C', 0),
(32, 17, 15, 'B', 1),
(33, 17, 16, 'D', 0),
(34, 17, 17, 'C', 1),
(35, 17, 18, 'D', 0),
(36, 18, 14, 'B', 1),
(37, 18, 15, 'A', 0),
(38, 18, 16, 'A', 0),
(39, 18, 17, 'A', 0),
(40, 18, 18, 'B', 1),
(41, 18, 19, 'C', 1);

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `course_id` int(11) UNSIGNED NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `user_id`, `course_id`, `rating`, `comment`, `created_at`) VALUES
(2, 1, 8, 5, 'This is very good course ', '2026-03-13 12:03:43'),
(3, 3, 8, 4, 'This is good', '2026-03-14 06:26:10');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL,
  `First_Name` varchar(50) NOT NULL,
  `Last_Name` varchar(50) NOT NULL,
  `User_Name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) DEFAULT 'student',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `phone` varchar(20) DEFAULT '-',
  `skill` varchar(100) DEFAULT '-',
  `bio` text DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `cover_photo` varchar(255) DEFAULT NULL,
  `facebook` varchar(255) DEFAULT NULL,
  `twitter` varchar(255) DEFAULT NULL,
  `linkedin` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `github` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `First_Name`, `Last_Name`, `User_Name`, `email`, `password`, `role`, `created_at`, `phone`, `skill`, `bio`, `profile_pic`, `cover_photo`, `facebook`, `twitter`, `linkedin`, `website`, `github`) VALUES
(1, 'RAM', 'BHATT', 'rambhatt08', 'rambhatt@gmail.com', '$2y$10$6UnyRG9503hCpXVmJ2q3oO.UqXmtMHZc36HJU9Q4xMWk20n/.kM82', 'student', '2026-02-08 05:24:23', '0123456789', 'Student', 'I am Engineering Student studying in VSITR,kadi in Computer Engineering ', 'uploads/1773403502_WhatsApp Image 2025-03-28 at 13.31.26_fee6dff0.jpg', 'uploads/1773403516_cover_focuswallpaper.png', NULL, NULL, NULL, NULL, NULL),
(2, 'admin', 'smartlearn', 'admin', 'smartlearnhelp@gmail.com', '$2y$10$hvwzboBcgk7G2/74iCpYkOFIMwTfejfAbobvx3pTwDETvLK5TqgZ6', 'admin', '2026-02-08 05:29:37', '-', '-', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'demo', 'login', 'demologin', 'demo2481632@gmail.com', '$2y$10$82ydW1nN3KkEbqXdHOFgDuD6P2aO1ESMw3EJtmhIbBAbmsmyeO/bK', 'student', '2026-03-12 09:00:38', '-', '-', 'VSITR', NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `video_progress`
--

CREATE TABLE `video_progress` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `course_id` int(11) UNSIGNED NOT NULL,
  `video_id` int(11) UNSIGNED NOT NULL,
  `is_completed` tinyint(1) DEFAULT 0,
  `watched_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `video_progress`
--

INSERT INTO `video_progress` (`id`, `user_id`, `course_id`, `video_id`, `is_completed`, `watched_at`) VALUES
(1, 1, 3, 6, 1, '2026-03-11 07:47:44'),
(2, 1, 3, 7, 1, '2026-03-11 07:47:58'),
(3, 1, 1, 1, 1, '2026-03-11 07:59:13'),
(4, 1, 1, 2, 1, '2026-03-11 07:59:32'),
(5, 1, 1, 5, 1, '2026-03-11 07:59:49'),
(6, 3, 3, 6, 1, '2026-03-12 09:01:48'),
(7, 1, 8, 20, 1, '2026-03-13 11:47:32'),
(8, 3, 13, 28, 1, '2026-03-14 06:21:16');

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `course_id` int(11) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`id`, `user_id`, `course_id`, `created_at`) VALUES
(5, 3, 11, '2026-03-14 06:23:12');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `course_lectures`
--
ALTER TABLE `course_lectures`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `course_qa`
--
ALTER TABLE `course_qa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `ebooks`
--
ALTER TABLE `ebooks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_enrollment` (`user_id`,`course_id`);

--
-- Indexes for table `login_logs`
--
ALTER TABLE `login_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `newsletter`
--
ALTER TABLE `newsletter`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `notes`
--
ALTER TABLE `notes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quiz_id` (`quiz_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quiz_id` (`quiz_id`);

--
-- Indexes for table `quiz_responses`
--
ALTER TABLE `quiz_responses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `User_Name` (`User_Name`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `video_progress`
--
ALTER TABLE `video_progress`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_video` (`user_id`,`video_id`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `course_id` (`course_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `course_lectures`
--
ALTER TABLE `course_lectures`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `course_qa`
--
ALTER TABLE `course_qa`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `ebooks`
--
ALTER TABLE `ebooks`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `login_logs`
--
ALTER TABLE `login_logs`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=94;

--
-- AUTO_INCREMENT for table `newsletter`
--
ALTER TABLE `newsletter`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `notes`
--
ALTER TABLE `notes`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `quiz_responses`
--
ALTER TABLE `quiz_responses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `video_progress`
--
ALTER TABLE `video_progress`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `course_lectures`
--
ALTER TABLE `course_lectures`
  ADD CONSTRAINT `course_lectures_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `course_qa`
--
ALTER TABLE `course_qa`
  ADD CONSTRAINT `course_qa_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `course_qa_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD CONSTRAINT `quizzes_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  ADD CONSTRAINT `quiz_attempts_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quiz_attempts_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  ADD CONSTRAINT `quiz_questions_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
