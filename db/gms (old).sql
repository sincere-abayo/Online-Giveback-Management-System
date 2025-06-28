-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 18, 2025 at 04:22 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gms`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_list`
--

CREATE TABLE `activity_list` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `program_id` int(30) NOT NULL,
  `name` text NOT NULL,
  `description` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `delete_flag` tinyint(1) NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `activity_list`
--

INSERT INTO `activity_list` (`id`, `program_id`, `name`, `description`, `status`, `delete_flag`, `date_created`, `date_updated`) VALUES
(1, 25, 'Back to School', 'Back to School marks a fresh start filled with new learning, capacity building, and opportunities to grow but with school materials.', 1, 0, '2025-04-18 15:00:06', '2025-04-18 15:07:23'),
(2, 25, 'School Feeding', 'School feeding programs nourish young minds and bodies, boosting attendance, learning, and overall well-being.', 1, 0, '2025-04-18 15:08:33', NULL),
(3, 26, 'Counselling', 'Counselling offers a safe space for reflection, healing, and personal growth through guided support.', 1, 0, '2025-04-18 15:09:44', NULL),
(4, 27, 'Medical Insurance', 'Medical insurance provides financial protection and access to essential healthcare services when needed most.', 1, 0, '2025-04-18 15:11:01', NULL),
(5, 28, 'Bible Study', 'Bible study deepens spiritual understanding and strengthens faith through reflection on Godâ€™s word.\r\n', 1, 0, '2025-04-18 15:11:49', NULL),
(6, 29, 'Talent Development', 'Talent development is the process of nurturing and enhancing individuals\' skills, knowledge, and abilities to help them reach their full potential.', 1, 0, '2025-04-18 15:14:10', NULL),
(7, 29, 'Cultural Empowerment', 'Cultural empowerment is the process of promoting and preserving one\'s cultural identity, values, and practices to foster self-confidence and community strength.', 1, 0, '2025-04-18 15:16:13', NULL),
(8, 29, 'Birthday Celebration', 'A birthday celebration is a joyful occasion to honor and appreciate someone\'s life, marking another year of growth and memories.', 1, 0, '2025-04-18 15:17:06', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `blogs`
--

CREATE TABLE `blogs` (
  `id` int(30) NOT NULL,
  `title` text NOT NULL,
  `topic_id` int(30) DEFAULT NULL,
  `content` text NOT NULL,
  `keywords` text NOT NULL,
  `meta_description` text NOT NULL,
  `banner_path` text NOT NULL,
  `upload_dir_code` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 = unpublished ,1= published',
  `blog_url` text NOT NULL,
  `author_id` int(30) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(30) NOT NULL,
  `title` text NOT NULL,
  `description` text NOT NULL,
  `schedule` date NOT NULL,
  `img_path` text DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `title`, `description`, `schedule`, `img_path`, `date_created`) VALUES
(1, 'Back 2 School', 'Kimisagara', '2025-05-31', 'uploads/events/8.png', '2024-03-11 21:24:32'),
(2, 'Medical campaign 2025!', 'Join us for the Kids Charity Medical Campaign and help secure a healthier future for children in our community. Enjoy free body checkups and medical insurance support, with expert healthcare professionals providing comprehensive assessments and guidance. Letâ€™s make a lasting impactâ€”one child at a time!', '2025-05-01', 'uploads/events/12.png', '2025-04-18 15:21:54');

-- --------------------------------------------------------

--
-- Table structure for table `gallery`
--

CREATE TABLE `gallery` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `delete_flag` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `program_list`
--

CREATE TABLE `program_list` (
  `id` int(30) NOT NULL,
  `name` text NOT NULL,
  `description` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `delete_flag` tinyint(1) NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `program_list`
--

INSERT INTO `program_list` (`id`, `name`, `description`, `status`, `delete_flag`, `date_created`, `date_updated`) VALUES
(1, 'Education', 'Education is the foundation for personal growth and societal progress, empowering individuals with knowledge, skills, and critical thinking.', 1, 0, '2025-04-18 14:56:23', NULL),
(2, 'Health', 'Health is the cornerstone of a fulfilling life, enabling individuals to thrive physically, mentally, and socially.', 1, 0, '2025-04-18 14:57:10', NULL),
(3, 'Medical', 'In medicine, early diagnosis and timely intervention are key to effective treatment and improved patient outcomes.', 1, 0, '2025-04-18 14:58:10', NULL),
(4, 'Religion', 'Faith provides spiritual guidance and purpose, fostering compassion, hope, and a sense of community.', 1, 0, '2025-04-18 14:58:51', NULL),
(5, 'Entertainment', 'Entertainment enriches our lives by offering joy, creativity, and a meaningful escape from daily routines.', 1, 0, '2025-04-18 14:59:35', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `system_info`
--

CREATE TABLE `system_info` (
  `id` int(30) NOT NULL,
  `meta_field` text NOT NULL,
  `meta_value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_info`
--

INSERT INTO `system_info` (`id`, `meta_field`, `meta_value`) VALUES
(1, 'name', 'Giveback Management System'),
(2, 'short_name', 'Giveback M S'),
(3, 'logo', 'uploads/1744929240_GMS ICO.png'),
(4, 'user_avatar', 'uploads/user_avatar.jpg'),
(5, 'cover', 'uploads/1744930740_ICO.png'),
(6, 'welcome_content', '<div class=\"framer-1l9hbw0\" data-framer-component-type=\"RichTextContainer\" style=\"-webkit-font-smoothing: inherit; position: relative; --framer-link-text-color: #0099ff; --framer-link-text-decoration: underline; flex: 0 0 auto; height: auto; white-space-collapse: preserve; width: 1151px; word-break: break-word; overflow-wrap: break-word; color: rgb(0, 0, 0); font-family: sans-serif; font-size: 12px; outline: none; display: flex; flex-direction: column; justify-content: flex-start; transform: none;\"><div class=\"framer-1l9hbw0\" data-framer-component-type=\"RichTextContainer\" style=\"-webkit-font-smoothing: inherit; position: relative; --framer-link-text-color: #0099ff; --framer-link-text-decoration: underline; flex: 0 0 auto; height: auto; width: 1151px; word-break: break-word; overflow-wrap: break-word; outline: none; display: flex; flex-direction: column; justify-content: flex-start; transform: none;\"><p class=\"framer-text\" style=\"-webkit-font-smoothing: inherit; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; padding: 0px; font-family: Manrope, &quot;Manrope Placeholder&quot;, sans-serif; font-weight: 800; color: rgb(56, 89, 166); font-size: 64px; text-transform: capitalize; text-decoration-style: solid; text-decoration-color: rgb(56, 89, 166); line-height: 76.8px; -webkit-text-stroke-color: rgb(56, 89, 166); font-feature-settings: normal; font-variation-settings: normal; text-wrap-style: initial; --font-selector: RlM7TWFucm9wZS1leHRyYWJvbGQ=; --framer-font-family: &quot;Manrope&quot;, &quot;Manrope Placeholder&quot;, sans-serif; --framer-font-size: 64px; --framer-font-weight: 800; --framer-text-color: rgb(56, 89, 166); --framer-text-transform: capitalize;\"><span class=\"framer-text\" style=\"-webkit-font-smoothing: inherit; font-variation-settings: normal;\">A story behind what we do</span></p></div><div class=\"framer-169sy4a\" data-framer-component-type=\"RichTextContainer\" style=\"-webkit-font-smoothing: inherit; position: relative; --framer-link-text-color: #0099ff; --framer-link-text-decoration: underline; flex: 0 0 auto; height: auto; width: 1151px; word-break: break-word; overflow-wrap: break-word; outline: none; display: flex; flex-direction: column; justify-content: flex-start; transform: none;\"><p class=\"framer-text\" style=\"-webkit-font-smoothing: inherit; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; padding: 0px; font-family: Manrope, &quot;Manrope Placeholder&quot;, sans-serif; color: rgba(56, 89, 166, 0.5); font-size: 20px; text-decoration-style: solid; text-decoration-color: rgba(56, 89, 166, 0.5); line-height: 30px; -webkit-text-stroke-color: rgba(56, 89, 166, 0.5); font-feature-settings: normal; font-variation-settings: normal; text-wrap-style: initial; --font-selector: RlM7TWFucm9wZS1yZWd1bGFy; --framer-font-family: &quot;Manrope&quot;, &quot;Manrope Placeholder&quot;, sans-serif; --framer-font-size: 20px; --framer-line-height: 1.5em; --framer-text-alignment: left; --framer-text-color: rgba(56, 89, 166, 0.5); --framer-text-transform: inherit;\"><span data-text-fill=\"true\" class=\"framer-text\" style=\"-webkit-font-smoothing: inherit; display: inline-block; background-clip: text; -webkit-text-fill-color: transparent; padding: 0px; margin: 0px; background-image: linear-gradient(278deg, rgb(0, 0, 0) 0%, rgba(56, 89, 166, 0.7) 119%);\">In a small community filled with dreams and challenges, a group of compassionate hearts came together to bring change. Children no longer went to bed hungry, finding the energy to learn and grow. Healthcare became accessible to all, bringing peace of mind to families and ensuring every child received the care they deserved.</span></p></div></div>'),
(7, 'home_quote', 'Come be part of this life-changing experience!');

-- --------------------------------------------------------

--
-- Table structure for table `topics`
--

CREATE TABLE `topics` (
  `id` int(30) NOT NULL,
  `name` text NOT NULL,
  `description` text NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '0=Inactive, 1=Active',
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `uploads`
--

CREATE TABLE `uploads` (
  `id` int(30) NOT NULL,
  `user_id` int(30) NOT NULL,
  `file_path` text NOT NULL,
  `dir_code` varchar(50) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `uploads`
--

INSERT INTO `uploads` (`id`, `user_id`, `file_path`, `dir_code`, `date_created`) VALUES
(1, 1, 'uploads/blog_uploads/gInV4MOSIc/1629172196_1.jpg', 'gInV4MOSIc', '2021-08-17 11:49:56'),
(2, 1, 'uploads/blog_uploads/gInV4MOSIc/1629172196_download.jpg', 'gInV4MOSIc', '2021-08-17 11:49:56'),
(3, 1, 'uploads/blog_uploads/qI8ZJiELzQ/1629172988_1.jpg', 'qI8ZJiELzQ', '2021-08-17 12:03:08'),
(4, 1, 'uploads/blog_uploads/qI8ZJiELzQ/1629172988_download.jpg', 'qI8ZJiELzQ', '2021-08-17 12:03:08'),
(5, 1, 'uploads/blog_uploads/vLLU8CyJZd/1629174024_1.jpg', 'vLLU8CyJZd', '2021-08-17 12:20:24'),
(6, 1, 'uploads/blog_uploads/Zk1pDmHIo2/1629176073_1.jpg', 'Zk1pDmHIo2', '2021-08-17 12:54:33'),
(7, 1, 'uploads/blog_uploads/K1dZZqq4SO/1629176614_warehouse-portrait.jpg', 'K1dZZqq4SO', '2021-08-17 13:03:34'),
(8, 1, 'uploads/blog_uploads/YSzqldklKk/1629176691_warehouse-portrait.jpg', 'YSzqldklKk', '2021-08-17 13:04:51'),
(10, 1, 'uploads/blog_uploads/Zk1pDmHIo2/1629176847_warehouse-portrait.jpg', 'Zk1pDmHIo2', '2021-08-17 13:07:27'),
(12, 8, 'uploads/blog_uploads/No5xPqZ0w8/1708429677_Seal_of_the_United_States_Department_of_State40x40.png', 'No5xPqZ0w8', '2024-02-20 13:47:57'),
(14, 1, 'uploads/blog_uploads/4zRYV4Kfdu/1709897693_Pack.jpeg', '4zRYV4Kfdu', '2024-03-08 13:34:53'),
(15, 1, 'uploads/blog_uploads/causes_uploads/1709907396_ISHIMWE Pacifique ed.jpg', 'causes_uploads', '2024-03-08 16:16:36'),
(16, 1, 'uploads/blog_uploads/causes_uploads/1709970837_Seal_of_the_United_States_Department_of_State40x40.png', 'causes_uploads', '2024-03-09 09:53:57'),
(17, 1, 'uploads/blog_uploads/causes_uploads/1710093352_rrms0.png', 'causes_uploads', '2024-03-10 19:55:52'),
(18, 1, 'uploads/blog_uploads/causes_uploads/1744824856_gms.png', 'causes_uploads', '2025-04-16 19:34:16');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(50) NOT NULL,
  `firstname` varchar(250) NOT NULL,
  `lastname` varchar(250) NOT NULL,
  `username` text NOT NULL,
  `password` text NOT NULL,
  `avatar` text DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `type` tinyint(1) NOT NULL DEFAULT 0,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `firstname`, `lastname`, `username`, `password`, `avatar`, `last_login`, `type`, `date_added`, `date_updated`) VALUES
(1, 'GMS', 'Mgmt', 'admin', '0192023a7bbd73250516f069df18b500', 'uploads/1744930320_admin.png', NULL, 1, '2021-01-20 14:02:37', '2025-04-18 00:52:05'),
(2, 'Pack', 'man', 'packman@gmail.com', '202cb962ac59075b964b07152d234b70', 'uploads/1712993580_Paci.jpeg', NULL, 0, '2024-02-10 18:21:27', '2024-04-13 09:33:03');

-- --------------------------------------------------------

--
-- Table structure for table `volunteer_history`
--

CREATE TABLE `volunteer_history` (
  `id` int(30) NOT NULL,
  `volunteer_id` int(30) NOT NULL,
  `activity_id` int(30) NOT NULL,
  `s` varchar(200) NOT NULL,
  `year` varchar(200) NOT NULL,
  `years` text NOT NULL,
  `status` int(10) NOT NULL DEFAULT 1,
  `end_status` tinyint(3) NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `volunteer_history`
--

INSERT INTO `volunteer_history` (`id`, `volunteer_id`, `activity_id`, `s`, `year`, `years`, `status`, `end_status`, `date_created`, `date_updated`) VALUES
(1, 1, 1, '', '2025-04-30', '', 1, 0, '2025-04-18 15:27:16', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `volunteer_list`
--

CREATE TABLE `volunteer_list` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `roll` varchar(100) NOT NULL,
  `firstname` text NOT NULL,
  `middlename` text DEFAULT NULL,
  `lastname` text NOT NULL,
  `contact` text NOT NULL,
  `email` varchar(100) NOT NULL,
  `motivation` text NOT NULL,
  `comment` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `delete_flag` tinyint(1) NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `volunteer_list`
--

INSERT INTO `volunteer_list` (`id`, `roll`, `firstname`, `middlename`, `lastname`, `contact`, `email`, `motivation`, `comment`, `status`, `delete_flag`, `date_created`, `date_updated`) VALUES
(1, '202501', 'Pack', '', 'man', '0788108109', 'techpackman30@gmail.com', 'x', '', 1, 0, '2025-04-18 00:42:40', '2025-04-18 16:17:21'),
(2, '202502', 'Pack', '', 'man', '0788108109', 'techpackman30@gmail.com', 'x', '', 1, 0, '2025-04-18 16:11:50', '2025-04-18 16:17:21'),
(3, '202503', 'q', '', 'w', '0788108109', 'qw@gmail.com', 'test', '', 0, 0, '2025-04-18 16:15:59', '2025-04-18 16:17:21');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `blogs`
--
ALTER TABLE `blogs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `topic_id` (`topic_id`),
  ADD KEY `topic_id_2` (`topic_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `program_list`
--
ALTER TABLE `program_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `system_info`
--
ALTER TABLE `system_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `topics`
--
ALTER TABLE `topics`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `topics`
--
ALTER TABLE `gallery`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `uploads`
--
ALTER TABLE `uploads`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `volunteer_history`
--
ALTER TABLE `volunteer_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `volunteer_id` (`volunteer_id`),
  ADD KEY `activity_id` (`activity_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `blogs`
--
ALTER TABLE `blogs`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `gallery`
--
ALTER TABLE `gallery`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `program_list`
--
ALTER TABLE `program_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `system_info`
--
ALTER TABLE `system_info`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `topics`
--
ALTER TABLE `topics`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `uploads`
--
ALTER TABLE `uploads`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `volunteer_history`
--
ALTER TABLE `volunteer_history`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `blogs`
--
ALTER TABLE `blogs`
  ADD CONSTRAINT `blogs_ibfk_1` FOREIGN KEY (`topic_id`) REFERENCES `topics` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
