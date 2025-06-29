/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19-11.4.5-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: gms
-- ------------------------------------------------------
-- Server version	11.4.5-MariaDB-1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;

--
-- Temporary table structure for view `active_volunteers`
--

DROP TABLE IF EXISTS `active_volunteers`;
/*!50001 DROP VIEW IF EXISTS `active_volunteers`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8mb4;
/*!50001 CREATE VIEW `active_volunteers` AS SELECT
 1 AS `id`,
  1 AS `roll`,
  1 AS `firstname`,
  1 AS `middlename`,
  1 AS `lastname`,
  1 AS `contact`,
  1 AS `email`,
  1 AS `motivation`,
  1 AS `comment`,
  1 AS `status`,
  1 AS `date_created`,
  1 AS `date_updated` */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `activity_list`
--

DROP TABLE IF EXISTS `activity_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `activity_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `program_id` int(30) NOT NULL,
  `name` text NOT NULL,
  `description` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `delete_flag` tinyint(1) NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_list`
--

LOCK TABLES `activity_list` WRITE;
/*!40000 ALTER TABLE `activity_list` DISABLE KEYS */;
INSERT INTO `activity_list` VALUES
(1,1,'Back to School','Back to School marks a fresh start filled with new learning, capacity building, and opportunities to grow but with school materials.',1,0,'2025-04-18 15:00:06','2025-04-18 16:41:10'),
(2,1,'School Feeding','School feeding programs nourish young minds and bodies, boosting attendance, learning, and overall well-being.',1,0,'2025-04-18 15:08:33','2025-04-18 16:42:58'),
(3,2,'Counselling','Counselling offers a safe space for reflection, healing, and personal growth through guided support.',1,0,'2025-04-18 15:09:44','2025-04-18 16:43:16'),
(4,3,'Medical Insurance','Medical insurance provides financial protection and access to essential healthcare services when needed most.',1,0,'2025-04-18 15:11:01','2025-04-18 16:43:08'),
(5,4,'Bible Study','Bible study deepens spiritual understanding and strengthens faith through reflection on Godâ€™s word.\r\n',1,0,'2025-04-18 15:11:49','2025-04-18 16:43:19'),
(6,5,'Talent Development','Talent development is the process of nurturing and enhancing individuals\' skills, knowledge, and abilities to help them reach their full potential.',1,0,'2025-04-18 15:14:10','2025-04-18 16:43:25'),
(7,5,'Cultural Empowerment','Cultural empowerment is the process of promoting and preserving one\'s cultural identity, values, and practices to foster self-confidence and community strength.',1,0,'2025-04-18 15:16:13','2025-04-18 16:43:28'),
(8,5,'Birthday Celebration','A birthday celebration is a joyful occasion to honor and appreciate someone\'s life, marking another year of growth and memories.',1,0,'2025-04-18 15:17:06','2025-04-18 16:43:31'),
(9,4,'Bible Session','Bible Session',0,0,'2025-04-18 16:48:55','2025-04-18 16:51:08'),
(10,1,'Coaching','coaching is necessary...',1,0,'2025-06-09 22:18:06',NULL),
(11,5,'Abirebeye Abayo Sincere Aime Margot','bvbvbbvbv',1,0,'2025-06-27 15:02:53',NULL),
(12,2,'65rtfgfgfg','fgfgfgfg fgvc fdv',1,0,'2025-06-28 21:47:28',NULL),
(13,5,'Milliman','dfdfdf',1,0,'2025-06-29 01:27:13',NULL),
(14,1,'Abirebeye Abayo Sincere Aime Margot','aasasas',1,0,'2025-06-29 04:14:05',NULL);
/*!40000 ALTER TABLE `activity_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `blogs`
--

DROP TABLE IF EXISTS `blogs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `blogs` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
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
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `topic_id` (`topic_id`),
  KEY `topic_id_2` (`topic_id`),
  CONSTRAINT `blogs_ibfk_1` FOREIGN KEY (`topic_id`) REFERENCES `topics` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blogs`
--

LOCK TABLES `blogs` WRITE;
/*!40000 ALTER TABLE `blogs` DISABLE KEYS */;
/*!40000 ALTER TABLE `blogs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `donation_history`
--

DROP TABLE IF EXISTS `donation_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `donation_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `volunteer_id` int(11) NOT NULL,
  `donation_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('pending','completed','failed') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `volunteer_id` (`volunteer_id`),
  KEY `donation_id` (`donation_id`),
  CONSTRAINT `fk_donation_history_donation` FOREIGN KEY (`donation_id`) REFERENCES `donations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_donation_history_volunteer` FOREIGN KEY (`volunteer_id`) REFERENCES `volunteer_list` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Donation history for logged-in volunteers';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `donation_history`
--

LOCK TABLES `donation_history` WRITE;
/*!40000 ALTER TABLE `donation_history` DISABLE KEYS */;
INSERT INTO `donation_history` VALUES
(1,4,20,5000.00,'completed','2025-06-28 23:39:08'),
(2,4,21,10000.00,'completed','2025-06-29 00:00:38'),
(3,4,23,50000.00,'completed','2025-06-29 00:32:40'),
(4,4,28,13000.00,'completed','2025-06-29 01:48:53'),
(5,4,35,13000.00,'completed','2025-06-29 03:11:12'),
(6,4,36,5000.00,'completed','2025-06-29 03:23:07'),
(7,4,40,5000.00,'completed','2025-06-29 07:54:34'),
(8,4,41,10000.00,'completed','2025-06-29 07:57:01'),
(9,4,42,5000.00,'completed','2025-06-29 08:03:02');
/*!40000 ALTER TABLE `donation_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `donation_stats`
--

DROP TABLE IF EXISTS `donation_stats`;
/*!50001 DROP VIEW IF EXISTS `donation_stats`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8mb4;
/*!50001 CREATE VIEW `donation_stats` AS SELECT
 1 AS `total_donations`,
  1 AS `total_amount`,
  1 AS `avg_amount`,
  1 AS `completed_donations`,
  1 AS `completed_amount`,
  1 AS `logged_in_donations`,
  1 AS `guest_donations` */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `donations`
--

DROP TABLE IF EXISTS `donations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `donations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `donation_ref` varchar(50) NOT NULL COMMENT 'Unique donation reference',
  `volunteer_id` int(11) DEFAULT NULL COMMENT 'Linked volunteer ID if logged in',
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `original_currency` varchar(3) DEFAULT 'RWF',
  `original_amount` decimal(10,2) DEFAULT 0.00,
  `exchange_rate` decimal(10,4) DEFAULT 1.0000,
  `payment_method` enum('stripe','paypal','mtn','airtel','paypack') NOT NULL,
  `message` text DEFAULT NULL COMMENT 'Donor message',
  `status` enum('pending','processing','completed','failed','cancelled') NOT NULL DEFAULT 'pending',
  `payment_id` varchar(255) DEFAULT NULL COMMENT 'Payment gateway transaction ID',
  `email_sent` tinyint(1) NOT NULL DEFAULT 0,
  `sms_sent` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `donation_ref` (`donation_ref`),
  KEY `volunteer_id` (`volunteer_id`),
  KEY `status` (`status`),
  KEY `payment_method` (`payment_method`),
  KEY `created_at` (`created_at`),
  KEY `idx_email` (`email`),
  KEY `idx_phone` (`phone`),
  KEY `idx_payment_id` (`payment_id`),
  KEY `idx_currency` (`original_currency`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Enhanced donation records with payment integration';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `donations`
--

LOCK TABLES `donations` WRITE;
/*!40000 ALTER TABLE `donations` DISABLE KEYS */;
INSERT INTO `donations` VALUES
(6,'DON20250628037D6F',NULL,'Abirebeye Abayo Sincere Aime Margot','abayosincere11@gmail.com','0732286284',5000.00,'RWF',0.00,1.0000,'stripe','','pending',NULL,0,0,'2025-06-28 16:43:41',NULL),
(7,'DON2025062842B60D',NULL,'Abirebeye Abayo Sincere Aime Margot','abayosincere11@gmail.com','0732286284',10000.00,'RWF',0.00,1.0000,'stripe','','pending',NULL,0,0,'2025-06-28 16:45:30',NULL),
(8,'DON20250628AA5630',NULL,'Abirebeye Abayo Sincere Aime Margot','abayosincere11@gmail.com','0723527270',5000.00,'RWF',0.00,1.0000,'stripe','','pending',NULL,0,0,'2025-06-28 16:56:08',NULL),
(9,'DON202506282074B6',NULL,'Abirebeye Abayo Sincere Aime Margot','abayosincere11@gmail.com','0723527270',5000.00,'RWF',0.00,1.0000,'stripe','','pending',NULL,0,0,'2025-06-28 17:07:41',NULL),
(10,'DON20250628E6960A',NULL,'Abirebeye Abayo Sincere Aime Margot','abayosincere11@gmail.com','0723527270',5000.00,'RWF',0.00,1.0000,'stripe','','pending',NULL,0,0,'2025-06-28 17:20:10',NULL),
(11,'DON202506288210D3',NULL,'Abirebeye Abayo Sincere Aime Margot','abayosincere11@gmail.com','0732286284',10000.00,'RWF',0.00,1.0000,'stripe','','pending',NULL,0,0,'2025-06-28 17:48:55',NULL),
(12,'DON20250628C7AAD3',NULL,'Abirebeye Abayo Sincere Aime Margot','abayosincere11@gmail.com','0732286284',100000.00,'RWF',0.00,1.0000,'mtn','','pending',NULL,0,0,'2025-06-28 17:49:31',NULL),
(13,'DON20250628C7D4BE',NULL,'Abirebeye Abayo Sincere Aime Margot','abayosincere11@gmail.com','0732286284',10000.00,'RWF',0.00,1.0000,'airtel','','pending',NULL,0,0,'2025-06-28 17:49:50',NULL),
(14,'DON20250628580F41',NULL,'Abirebeye Abayo Sincere Aime Margot','abayosincere11@gmail.com','0723527270',100000.00,'RWF',0.00,1.0000,'stripe','','completed','pi_3Rf35uGfSt93katP0MjCFvJd',1,1,'2025-06-28 17:50:22','2025-06-28 20:19:24'),
(15,'DON202506285DB758',NULL,'Abirebeye Abayo Sincere Aime Margot','abayosincere11@gmail.com','0723527270',25000.00,'RWF',0.00,1.0000,'stripe','kindness','completed','pi_3Rf5PPGfSt93katP1iNYgkrg',1,1,'2025-06-28 20:27:36','2025-06-28 20:31:45'),
(16,'DON2025062851388D',NULL,'Abirebeye Abayo Sincere Aime Margot','abayosincere11@gmail.com','0723527270',100.00,'RWF',0.00,1.0000,'mtn','','completed','MTN17511474241744',1,1,'2025-06-28 21:50:21','2025-06-28 21:50:28'),
(17,'DON20250629E5D83B',NULL,'Abirebeye Abayo Sincere Aime Margot','abayosincere11@gmail.com','0786729283',10000.00,'RWF',0.00,1.0000,'paypack','Would you like me to make this fix for you now? Or do you want to test the flow and report any other issues?','processing','5fa85160-d227-4b10-9410-42f2b2ac0885',0,0,'2025-06-28 23:16:52','2025-06-28 23:17:08'),
(18,'DON20250629A8ADC0',NULL,'Abirebeye Abayo Sincere Aime Margot','abayosincere11@gmail.com','0786729283',100.00,'RWF',0.00,1.0000,'paypack','','completed','f1902fd3-50e6-433f-9c3d-3e2cd83baaa6',1,1,'2025-06-28 23:17:56','2025-06-28 23:26:39'),
(19,'DON20250629F6DB75',4,'Abirebeye Abayo Margot','abayosincere11@gmail.com','0723257270',100.00,'RWF',0.00,1.0000,'paypack','','pending',NULL,0,0,'2025-06-28 23:30:44',NULL),
(20,'DON20250629C09EB1',4,'Abirebeye Abayo Margot','abayosincere11@gmail.com','0723257270',5000.00,'RWF',0.00,1.0000,'stripe','','completed','pi_3Rf8KpGfSt93katP1C0s8q84',0,0,'2025-06-28 23:36:55','2025-06-28 23:39:08'),
(21,'DON20250629480493',4,'Abirebeye Abayo Margot','abayosincere11@gmail.com','0723257270',10000.00,'RWF',0.00,1.0000,'stripe','','completed','pi_3Rf8feGfSt93katP1ceAQLaY',1,1,'2025-06-29 00:00:21','2025-06-29 00:00:42'),
(22,'DON20250629C8A46E',4,'Abirebeye Abayo Margot','abayosincere11@gmail.com','0723257270',100.00,'RWF',0.00,1.0000,'paypack','','completed','d78f3265-3629-4ab6-9882-e27910c2b3d7',0,0,'2025-06-29 00:01:39','2025-06-29 00:02:54'),
(23,'DON20250629717D57',4,'Abirebeye Abayo Margot','abayosincere11@gmail.com','0786729283',50000.00,'RWF',0.00,1.0000,'stripe','','completed','pi_3Rf9AdGfSt93katP0VX7udXy',1,1,'2025-06-29 00:32:16','2025-06-29 00:32:44'),
(24,'DON20250629C424B5',4,'Abirebeye Abayo Margot','abayosincere11@gmail.com','0786729283',100.00,'RWF',0.00,1.0000,'paypack','','completed','7c16e9a0-ad93-4523-8c1e-af0d583820b2',1,1,'2025-06-29 00:58:24','2025-06-29 01:11:29'),
(25,'DON2025062965BB1C',4,'Abirebeye Abayo Margot','abayosincere11@gmail.com','0786729283',100.00,'RWF',0.00,1.0000,'paypack','','completed','760f794e-edc8-488d-a9e6-b3239527d1ae',1,1,'2025-06-29 01:12:47','2025-06-29 01:14:07'),
(26,'DON20250629261B2B',4,'Abirebeye Abayo Margot','abayosincere11@gmail.com','0723257270',1300.00,'USD',1.00,1300.0000,'paypack','','pending',NULL,0,0,'2025-06-29 01:42:19',NULL),
(27,'DON202506294985CF',4,'Abirebeye Abayo Margot','abayosincere11@gmail.com','0723257270',13000.00,'USD',10.00,1300.0000,'stripe','','pending',NULL,0,0,'2025-06-29 01:42:35',NULL),
(28,'DON20250629ED2B0A',4,'Abirebeye Abayo Margot','abayosincere11@gmail.com','0723257270',13000.00,'USD',10.00,1300.0000,'stripe','','completed','pi_3RfAMOGfSt93katP0qV2OvxA',1,1,'2025-06-29 01:48:33','2025-06-29 01:48:57'),
(29,'DON20250629CB63CC',4,'Abirebeye Abayo Margot','abayosincere11@gmail.com','0723257270',6500.00,'USD',5.00,1300.0000,'paypack','','pending',NULL,0,0,'2025-06-29 01:53:08',NULL),
(30,'DON20250629D4A2C2',4,'Abirebeye Abayo Margot','abayosincere11@gmail.com','0723257270',6500.00,'USD',5.00,1300.0000,'stripe','','pending',NULL,0,0,'2025-06-29 01:57:10',NULL),
(31,'DON20250629E4FD1B',4,'Abirebeye Abayo Margot','abayosincere11@gmail.com','0723257270',6500.00,'USD',5.00,1300.0000,'paypack','','processing','97319b56-8a95-4df3-83af-6df6461d6845',0,0,'2025-06-29 01:57:52','2025-06-29 01:58:23'),
(32,'DON20250629877380',4,'Abirebeye Abayo Margot','abayosincere11@gmail.com','0723257270',6500.00,'USD',5.00,1300.0000,'stripe','','pending',NULL,0,0,'2025-06-29 02:07:43',NULL),
(33,'DON20250629CD2F8D',4,'Abirebeye Abayo Margot','abayosincere11@gmail.com','0723257270',32500.00,'USD',25.00,1300.0000,'paypack','','pending',NULL,0,0,'2025-06-29 02:08:13',NULL),
(34,'DON2025062952C5ED',4,'Abirebeye Abayo Margot','abayosincere11@gmail.com','0723257270',13000.00,'USD',10.00,1300.0000,'stripe','','pending',NULL,0,0,'2025-06-29 02:14:08',NULL),
(35,'DON20250629459BF6',4,'Abirebeye Abayo Margot','abayosincere11@gmail.com','0723257270',13000.00,'USD',10.00,1300.0000,'stripe','','completed','pi_3RfBe3GfSt93katP0IwNdCJH',1,1,'2025-06-29 03:10:52','2025-06-29 03:11:16'),
(36,'DON202506299F82E2',4,'Abirebeye Abayo Margot','abayosincere11@gmail.com','0723527270',5000.00,'RWF',5000.00,1.0000,'stripe','','completed','pi_3RfBpbGfSt93katP1Xp8d0cm',1,1,'2025-06-29 03:22:26','2025-06-29 03:23:11'),
(37,'DON202506294CEBF8',NULL,'Abirebeye Abayo Sincere Aime Margot','abayosincere11@gmail.com','0723527270',32500.00,'USD',25.00,1300.0000,'stripe','','completed','pi_3RfDVkGfSt93katP14vHEl7C',1,1,'2025-06-29 05:10:23','2025-06-29 05:11:00'),
(38,'DON20250629D3AD38',4,'Abirebeye Abayo Margot','abayosincere11@gmail.com','0723527270',13000.00,'USD',10.00,1300.0000,'stripe','','pending',NULL,0,0,'2025-06-29 07:20:27',NULL),
(39,'DON20250629A63E0D',4,'Abirebeye Abayo Margot','abayosincere11@gmail.com','0723527270',1000.00,'RWF',1000.00,1.0000,'paypack','','processing','af37c507-dd62-4312-9cc3-bd44212c1b9c',0,0,'2025-06-29 07:36:47','2025-06-29 07:36:53'),
(40,'DON20250629D003D0',4,'Abirebeye Abayo Margot','abayosincere11@gmail.com','0723527270',5000.00,'RWF',5000.00,1.0000,'stripe','','completed','pi_3RfFwRGfSt93katP0MY30n43',1,0,'2025-06-29 07:37:26','2025-06-29 07:54:37'),
(41,'DON20250629B97543',4,'Abirebeye Abayo Margot','abayosincere11@gmail.com','0723527270',10000.00,'RWF',10000.00,1.0000,'stripe','','completed','pi_3RfG6eGfSt93katP0TuiEJUq',1,0,'2025-06-29 07:56:43','2025-06-29 07:57:04'),
(42,'DON20250629609BD4',4,'Abirebeye Abayo Margot','abayosincere11@gmail.com','0723527270',5000.00,'RWF',5000.00,1.0000,'stripe','','completed','pi_3RfGCUGfSt93katP0bVnqp1H',1,1,'2025-06-29 08:02:45','2025-06-29 08:03:06'),
(43,'DON2025062990A87C',4,'Abirebeye Abayo Margot','abayosincere11@gmail.com','0786729283',100.00,'RWF',100.00,1.0000,'paypack','','completed','3332c574-b024-4c7b-938b-120fa7d682c4',1,1,'2025-06-29 08:05:12','2025-06-29 08:06:34'),
(44,'DON20250629EC5296',4,'Abirebeye Abayo Margot','abayosincere11@gmail.com','0723527270',100.00,'RWF',100.00,1.0000,'paypack','','pending',NULL,0,0,'2025-06-29 10:23:53',NULL),
(45,'DON20250629C12390',NULL,'s','admin@mbims.com','0922222222',100.00,'RWF',100.00,1.0000,'paypack','','pending',NULL,0,0,'2025-06-29 10:36:19',NULL);
/*!40000 ALTER TABLE `donations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `email_templates`
--

DROP TABLE IF EXISTS `email_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `email_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template_name` varchar(100) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `html_content` text NOT NULL,
  `text_content` text NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `template_name` (`template_name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Email templates for donation notifications';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `email_templates`
--

LOCK TABLES `email_templates` WRITE;
/*!40000 ALTER TABLE `email_templates` DISABLE KEYS */;
INSERT INTO `email_templates` VALUES
(1,'donation_confirmation','Thank you for your donation - Dufatanye Charity Foundation','<!DOCTYPE html>\n<html>\n<head>\n    <meta charset=\"UTF-8\">\n    <title>Donation Confirmation</title>\n</head>\n<body style=\"font-family: Arial, sans-serif; line-height: 1.6; color: #333;\">\n    <div style=\"max-width: 600px; margin: 0 auto; padding: 20px;\">\n        <div style=\"text-align: center; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; border-radius: 10px;\">\n            <h1>Thank You for Your Donation!</h1>\n            <p>Your generosity makes a real difference in our community.</p>\n        </div>\n        \n        <div style=\"background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0;\">\n            <h3>Donation Details:</h3>\n            <p><strong>Reference:</strong> {donation_ref}</p>\n            <p><strong>Amount:</strong> {amount} RWF</p>\n            <p><strong>Date:</strong> {date}</p>\n            <p><strong>Payment Method:</strong> {payment_method}</p>\n        </div>\n        \n        <p>Your donation will be used to support our various programs including education, health, and community development initiatives.</p>\n        \n        <div style=\"text-align: center; margin: 30px 0;\">\n            <a href=\"{dashboard_url}\" style=\"background: #28a745; color: white; padding: 12px 30px; text-decoration: none; border-radius: 25px; display: inline-block;\">View Your Dashboard</a>\n        </div>\n        \n        <p>Thank you for your continued support!</p>\n        <p>Best regards,<br>Dufatanye Charity Foundation Team</p>\n    </div>\n</body>\n</html>','Thank you for your donation - Dufatanye Charity Foundation\n\nYour generosity makes a real difference in our community.\n\nDonation Details:\nReference: {donation_ref}\nAmount: {amount} RWF\nDate: {date}\nPayment Method: {payment_method}\n\nYour donation will be used to support our various programs including education, health, and community development initiatives.\n\nView your dashboard at: {dashboard_url}\n\nThank you for your continued support!\n\nBest regards,\nDufatanye Charity Foundation Team',1,'2025-06-28 13:18:19',NULL),
(2,'donation_receipt','Donation Receipt - Dufatanye Charity Foundation','<!DOCTYPE html>\n<html>\n<head>\n    <meta charset=\"UTF-8\">\n    <title>Donation Receipt</title>\n</head>\n<body style=\"font-family: Arial, sans-serif; line-height: 1.6; color: #333;\">\n    <div style=\"max-width: 600px; margin: 0 auto; padding: 20px;\">\n        <div style=\"text-align: center; background: #28a745; color: white; padding: 30px; border-radius: 10px;\">\n            <h1>Donation Receipt</h1>\n            <p>Dufatanye Charity Foundation</p>\n        </div>\n        \n        <div style=\"background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0;\">\n            <h3>Receipt Details:</h3>\n            <p><strong>Receipt Number:</strong> {donation_ref}</p>\n            <p><strong>Date:</strong> {date}</p>\n            <p><strong>Donor Name:</strong> {fullname}</p>\n            <p><strong>Email:</strong> {email}</p>\n            <p><strong>Phone:</strong> {phone}</p>\n            <p><strong>Amount:</strong> {amount} RWF</p>\n            <p><strong>Payment Method:</strong> {payment_method}</p>\n            <p><strong>Transaction ID:</strong> {payment_id}</p>\n        </div>\n        \n        <p>This receipt serves as proof of your charitable donation to Dufatanye Charity Foundation.</p>\n        \n        <p>Thank you for your generosity!</p>\n        <p>Best regards,<br>Dufatanye Charity Foundation Team</p>\n    </div>\n</body>\n</html>','Donation Receipt - Dufatanye Charity Foundation\n\nReceipt Details:\nReceipt Number: {donation_ref}\nDate: {date}\nDonor Name: {fullname}\nEmail: {email}\nPhone: {phone}\nAmount: {amount} RWF\nPayment Method: {payment_method}\nTransaction ID: {payment_id}\n\nThis receipt serves as proof of your charitable donation to Dufatanye Charity Foundation.\n\nThank you for your generosity!\n\nBest regards,\nDufatanye Charity Foundation Team',1,'2025-06-28 13:18:19',NULL);
/*!40000 ALTER TABLE `email_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `events` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `title` text NOT NULL,
  `description` text NOT NULL,
  `schedule` date NOT NULL,
  `img_path` text DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `events`
--

LOCK TABLES `events` WRITE;
/*!40000 ALTER TABLE `events` DISABLE KEYS */;
INSERT INTO `events` VALUES
(1,'Back 2 School','Kimisagara','2025-06-10','uploads/events/8.png','2024-03-11 21:24:32'),
(2,'Medical campaign 2025!','Join us for the Kids Charity Medical Campaign and help secure a healthier future for children in our community. Enjoy free body checkups and medical insurance support, with expert healthcare professionals providing comprehensive assessments and guidance. Letâ€™s make a lasting impactâ€”one child at a time!','2025-06-30','uploads/events/12.png','2025-04-18 15:21:54'),
(14,'tytyty','kklklkl;','2025-06-27',NULL,'2025-06-29 01:29:25'),
(15,'sd','sd','2025-06-19',NULL,'2025-06-29 03:11:17'),
(16,'You can now test the SMS notification again. If you still have issues, let me know!','You can now test the SMS notification again. If you still have issues, let me know!','2025-06-20',NULL,'2025-06-29 04:13:54');
/*!40000 ALTER TABLE `events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gallery`
--

DROP TABLE IF EXISTS `gallery`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `gallery` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `delete_flag` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=313 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gallery`
--

LOCK TABLES `gallery` WRITE;
/*!40000 ALTER TABLE `gallery` DISABLE KEYS */;
INSERT INTO `gallery` VALUES
(25,'Donation is Love Expression','1750715172.png',0,NULL,NULL),
(26,'Help ones in need','1750715211.png',0,NULL,NULL),
(28,'Donation ','1750715378.png',0,NULL,NULL),
(29,'Hand in hand','1750715300.png',0,NULL,NULL),
(30,'Together as a Team','1750715238.png',0,NULL,NULL),
(32,'Together we can','1750715411.png',0,NULL,NULL),
(34,'Giving is better than receiving','1750715545.png',0,NULL,NULL),
(302,'Your Partition counts','1750721085.png',0,NULL,NULL),
(311,'kkkklkl','1751175955.png',0,NULL,NULL),
(312,'ther enhancements (like drag-and-drop upload, loading spinners, or','1751176077.png',0,NULL,NULL);
/*!40000 ALTER TABLE `gallery` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_settings`
--

DROP TABLE IF EXISTS `payment_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payment_method` varchar(50) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_setting` (`payment_method`,`setting_key`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Payment gateway configuration settings';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_settings`
--

LOCK TABLES `payment_settings` WRITE;
/*!40000 ALTER TABLE `payment_settings` DISABLE KEYS */;
INSERT INTO `payment_settings` VALUES
(1,'stripe','publishable_key','0',1,'2025-06-28 13:18:19','2025-06-29 05:12:21'),
(2,'stripe','secret_key','0',1,'2025-06-28 13:18:19','2025-06-29 05:12:21'),
(3,'stripe','webhook_secret','0',1,'2025-06-28 13:18:19','2025-06-29 05:12:21'),
(4,'paypal','client_id','0',1,'2025-06-28 13:18:19','2025-06-29 05:12:21'),
(5,'paypal','client_secret','522',1,'2025-06-28 13:18:19','2025-06-29 05:12:21'),
(6,'paypal','mode','0',1,'2025-06-28 13:18:19','2025-06-29 05:12:21'),
(7,'sms','africas_talking_api_key','0',1,'2025-06-28 13:18:19','2025-06-29 05:12:21'),
(8,'sms','africas_talking_username','0',1,'2025-06-28 13:18:19','2025-06-29 05:12:21'),
(9,'sms','africas_talking_sender_id','0',1,'2025-06-28 13:18:19','2025-06-29 05:12:21'),
(19,'mtn','api_key','0',1,'2025-06-28 13:47:05','2025-06-29 05:12:21'),
(20,'mtn','api_secret','0',1,'2025-06-28 13:47:05','2025-06-29 05:12:21'),
(21,'airtel','api_key','0',1,'2025-06-28 13:47:05','2025-06-29 05:12:21'),
(22,'airtel','api_secret','0',1,'2025-06-28 13:47:05','2025-06-29 05:12:21');
/*!40000 ALTER TABLE `payment_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_transactions`
--

DROP TABLE IF EXISTS `payment_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_transactions` (
  `transaction_id` int(11) NOT NULL AUTO_INCREMENT,
  `donation_id` int(11) DEFAULT NULL,
  `payment_method` varchar(50) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(10) DEFAULT 'RWF',
  `status` enum('pending','processing','completed','failed') DEFAULT 'pending',
  `gateway_reference` varchar(255) DEFAULT NULL,
  `gateway_transaction_id` varchar(255) DEFAULT NULL,
  `gateway_response` text DEFAULT NULL,
  `failure_reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`transaction_id`),
  KEY `idx_payment_transactions_donation_id` (`donation_id`),
  KEY `idx_payment_transactions_status` (`status`),
  KEY `idx_payment_transactions_gateway_ref` (`gateway_reference`),
  CONSTRAINT `payment_transactions_ibfk_1` FOREIGN KEY (`donation_id`) REFERENCES `donations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_transactions`
--

LOCK TABLES `payment_transactions` WRITE;
/*!40000 ALTER TABLE `payment_transactions` DISABLE KEYS */;
INSERT INTO `payment_transactions` VALUES
(1,17,'paypack',10000.00,'RWF','processing','donation_17_1751152627','5fa85160-d227-4b10-9410-42f2b2ac0885','{\n  \"ref\": \"5fa85160-d227-4b10-9410-42f2b2ac0885\",\n  \"status\": \"pending\",\n  \"amount\": 10000,\n  \"provider\": \"mtn\",\n  \"kind\": \"CASHIN\",\n  \"created_at\": \"2025-06-28T23:17:08.325948268Z\"\n}\n',NULL,'2025-06-28 23:17:07','2025-06-28 23:17:08'),
(2,18,'paypack',100.00,'RWF','completed','donation_18_1751152685','f1902fd3-50e6-433f-9c3d-3e2cd83baaa6','{\"ref\":\"f1902fd3-50e6-433f-9c3d-3e2cd83baaa6\",\"limit\":20,\"total\":2,\"transactions\":[{\"event_id\":\"4d159398-5476-11f0-8f20-dead131a2dd9\",\"event_kind\":\"transaction:processed\",\"created_at\":\"2025-06-28T23:19:12.459618Z\",\"data\":{\"ref\":\"f1902fd3-50e6-433f-9c3d-3e2cd83baaa6\",\"user_ref\":\"QUARKSs3YxO27HRK\",\"kind\":\"CASHIN\",\"merchant\":\"3TXYBR\",\"client\":\"0786729283\",\"amount\":100,\"provider\":\"mtn\",\"status\":\"successful\",\"metadata\":null,\"created_at\":\"2025-06-28T23:18:06.206047Z\",\"processed_at\":\"2025-06-28T23:19:12.459667Z\"}},{\"event_id\":\"2598063e-5476-11f0-bedb-dead131a2dd9\",\"event_kind\":\"transaction:created\",\"created_at\":\"2025-06-28T23:18:06.206221Z\",\"data\":{\"ref\":\"f1902fd3-50e6-433f-9c3d-3e2cd83baaa6\",\"user_ref\":\"QUARKSs3YxO27HRK\",\"kind\":\"CASHIN\",\"merchant\":\"3TXYBR\",\"client\":\"0786729283\",\"amount\":100,\"provider\":\"mtn\",\"status\":\"pending\",\"metadata\":null,\"created_at\":\"2025-06-28T23:18:06.206047Z\"}}]}',NULL,'2025-06-28 23:18:05','2025-06-28 23:19:14'),
(3,22,'paypack',100.00,'RWF','completed','donation_22_1751155305','d78f3265-3629-4ab6-9882-e27910c2b3d7','{\"ref\":\"d78f3265-3629-4ab6-9882-e27910c2b3d7\",\"limit\":20,\"total\":2,\"transactions\":[{\"event_id\":\"66eecebe-547c-11f0-88b3-dead131a2dd9\",\"event_kind\":\"transaction:processed\",\"created_at\":\"2025-06-29T00:02:52.806485Z\",\"data\":{\"ref\":\"d78f3265-3629-4ab6-9882-e27910c2b3d7\",\"user_ref\":\"QUARKSIKSSNTjJjz\",\"kind\":\"CASHIN\",\"merchant\":\"3TXYBR\",\"client\":\"0786729283\",\"amount\":100,\"provider\":\"mtn\",\"status\":\"successful\",\"metadata\":null,\"created_at\":\"2025-06-29T00:01:46.215889Z\",\"processed_at\":\"2025-06-29T00:02:52.806499Z\"}},{\"event_id\":\"3f3dd16c-547c-11f0-9844-dead131a2dd9\",\"event_kind\":\"transaction:created\",\"created_at\":\"2025-06-29T00:01:46.215611Z\",\"data\":{\"ref\":\"d78f3265-3629-4ab6-9882-e27910c2b3d7\",\"user_ref\":\"QUARKSIKSSNTjJjz\",\"kind\":\"CASHIN\",\"merchant\":\"3TXYBR\",\"client\":\"0786729283\",\"amount\":100,\"provider\":\"mtn\",\"status\":\"pending\",\"metadata\":null,\"created_at\":\"2025-06-29T00:01:46.215889Z\"}}]}',NULL,'2025-06-29 00:01:45','2025-06-29 00:02:54'),
(4,24,'paypack',100.00,'RWF','completed','donation_24_1751158712','7c16e9a0-ad93-4523-8c1e-af0d583820b2','{\"ref\":\"7c16e9a0-ad93-4523-8c1e-af0d583820b2\",\"limit\":20,\"total\":2,\"transactions\":[{\"event_id\":\"5483bd36-5484-11f0-ac82-dead131a2dd9\",\"event_kind\":\"transaction:processed\",\"created_at\":\"2025-06-29T00:59:37.880391Z\",\"data\":{\"ref\":\"7c16e9a0-ad93-4523-8c1e-af0d583820b2\",\"user_ref\":\"QUARKSPOosGJBavk\",\"kind\":\"CASHIN\",\"merchant\":\"3TXYBR\",\"client\":\"0786729283\",\"amount\":100,\"provider\":\"mtn\",\"status\":\"successful\",\"metadata\":null,\"created_at\":\"2025-06-29T00:58:33.462158Z\",\"processed_at\":\"2025-06-29T00:59:37.880719Z\"}},{\"event_id\":\"2e1e4616-5484-11f0-a921-dead131a2dd9\",\"event_kind\":\"transaction:created\",\"created_at\":\"2025-06-29T00:58:33.46205Z\",\"data\":{\"ref\":\"7c16e9a0-ad93-4523-8c1e-af0d583820b2\",\"user_ref\":\"QUARKSPOosGJBavk\",\"kind\":\"CASHIN\",\"merchant\":\"3TXYBR\",\"client\":\"0786729283\",\"amount\":100,\"provider\":\"mtn\",\"status\":\"pending\",\"metadata\":null,\"created_at\":\"2025-06-29T00:58:33.462158Z\"}}]}',NULL,'2025-06-29 00:58:32','2025-06-29 00:59:38'),
(5,25,'paypack',100.00,'RWF','completed','donation_25_1751159574','760f794e-edc8-488d-a9e6-b3239527d1ae','{\"ref\":\"760f794e-edc8-488d-a9e6-b3239527d1ae\",\"limit\":20,\"total\":2,\"transactions\":[{\"event_id\":\"57e2ece8-5486-11f0-807e-dead131a2dd9\",\"event_kind\":\"transaction:processed\",\"created_at\":\"2025-06-29T01:14:02.530771Z\",\"data\":{\"ref\":\"760f794e-edc8-488d-a9e6-b3239527d1ae\",\"user_ref\":\"QUARKS3WVNCEQrHX\",\"kind\":\"CASHIN\",\"merchant\":\"3TXYBR\",\"client\":\"0786729283\",\"amount\":100,\"provider\":\"mtn\",\"status\":\"successful\",\"metadata\":null,\"created_at\":\"2025-06-29T01:12:55.23427Z\",\"processed_at\":\"2025-06-29T01:14:02.531397Z\"}},{\"event_id\":\"2fc64ee4-5486-11f0-807e-dead131a2dd9\",\"event_kind\":\"transaction:created\",\"created_at\":\"2025-06-29T01:12:55.23391Z\",\"data\":{\"ref\":\"760f794e-edc8-488d-a9e6-b3239527d1ae\",\"user_ref\":\"QUARKS3WVNCEQrHX\",\"kind\":\"CASHIN\",\"merchant\":\"3TXYBR\",\"client\":\"0786729283\",\"amount\":100,\"provider\":\"mtn\",\"status\":\"pending\",\"metadata\":null,\"created_at\":\"2025-06-29T01:12:55.23427Z\"}}]}',NULL,'2025-06-29 01:12:54','2025-06-29 01:14:03'),
(6,31,'paypack',6500.00,'RWF','processing','donation_31_1751162302','97319b56-8a95-4df3-83af-6df6461d6845','{\n  \"ref\": \"97319b56-8a95-4df3-83af-6df6461d6845\",\n  \"status\": \"pending\",\n  \"amount\": 6500,\n  \"provider\": \"mtn\",\n  \"kind\": \"CASHIN\",\n  \"created_at\": \"2025-06-29T01:58:23.03950607Z\"\n}\n',NULL,'2025-06-29 01:58:22','2025-06-29 01:58:23'),
(7,39,'paypack',1000.00,'RWF','processing','donation_39_1751182613','af37c507-dd62-4312-9cc3-bd44212c1b9c','{\n  \"ref\": \"af37c507-dd62-4312-9cc3-bd44212c1b9c\",\n  \"status\": \"pending\",\n  \"amount\": 1000,\n  \"provider\": \"mtn\",\n  \"kind\": \"CASHIN\",\n  \"created_at\": \"2025-06-29T07:36:53.789276952Z\"\n}\n',NULL,'2025-06-29 07:36:53','2025-06-29 07:36:53'),
(8,43,'paypack',100.00,'RWF','completed','donation_43_1751184322','3332c574-b024-4c7b-938b-120fa7d682c4','{\"ref\":\"3332c574-b024-4c7b-938b-120fa7d682c4\",\"limit\":20,\"total\":2,\"transactions\":[{\"event_id\":\"f5acf35e-54bf-11f0-90f3-dead131a2dd9\",\"event_kind\":\"transaction:processed\",\"created_at\":\"2025-06-29T08:06:28.570833Z\",\"data\":{\"ref\":\"3332c574-b024-4c7b-938b-120fa7d682c4\",\"user_ref\":\"QUARKS0P7EZBIPmt\",\"kind\":\"CASHIN\",\"merchant\":\"3TXYBR\",\"client\":\"0786729283\",\"amount\":100,\"provider\":\"mtn\",\"status\":\"successful\",\"metadata\":null,\"created_at\":\"2025-06-29T08:05:22.396451Z\",\"processed_at\":\"2025-06-29T08:06:28.570478Z\"}},{\"event_id\":\"ce3bb166-54bf-11f0-839d-dead131a2dd9\",\"event_kind\":\"transaction:created\",\"created_at\":\"2025-06-29T08:05:22.397096Z\",\"data\":{\"ref\":\"3332c574-b024-4c7b-938b-120fa7d682c4\",\"user_ref\":\"QUARKS0P7EZBIPmt\",\"kind\":\"CASHIN\",\"merchant\":\"3TXYBR\",\"client\":\"0786729283\",\"amount\":100,\"provider\":\"mtn\",\"status\":\"pending\",\"metadata\":null,\"created_at\":\"2025-06-29T08:05:22.396451Z\"}}]}',NULL,'2025-06-29 08:05:22','2025-06-29 08:06:30');
/*!40000 ALTER TABLE `payment_transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `pending_volunteers`
--

DROP TABLE IF EXISTS `pending_volunteers`;
/*!50001 DROP VIEW IF EXISTS `pending_volunteers`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8mb4;
/*!50001 CREATE VIEW `pending_volunteers` AS SELECT
 1 AS `id`,
  1 AS `roll`,
  1 AS `firstname`,
  1 AS `middlename`,
  1 AS `lastname`,
  1 AS `contact`,
  1 AS `email`,
  1 AS `motivation`,
  1 AS `comment`,
  1 AS `status`,
  1 AS `date_created`,
  1 AS `date_updated` */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `program_list`
--

DROP TABLE IF EXISTS `program_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `program_list` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `description` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `delete_flag` tinyint(1) NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `program_list`
--

LOCK TABLES `program_list` WRITE;
/*!40000 ALTER TABLE `program_list` DISABLE KEYS */;
INSERT INTO `program_list` VALUES
(1,'Education','Education is the foundation for personal growth and societal progress, empowering individuals with knowledge, skills, and critical thinking.',1,0,'2025-04-18 14:56:23',NULL),
(2,'Health','Health is the cornerstone of a fulfilling life, enabling individuals to thrive physically, mentally, and socially.',1,0,'2025-04-18 14:57:10',NULL),
(3,'Medical','In medicine, early diagnosis and timely intervention are key to effective treatment and improved patient outcomes.',1,1,'2025-04-18 14:58:10','2025-06-27 15:02:27'),
(4,'Religion','Faith provides spiritual guidance and purpose, fostering compassion, hope, and a sense of community.',0,0,'2025-04-18 14:58:51','2025-06-09 22:16:52'),
(5,'Entertainment','Entertainment enriches our lives by offering joy, creativity, and a meaningful escape from daily routines.',1,0,'2025-04-18 14:59:35',NULL),
(30,'wesdxcwes','ghghghg',1,0,'2025-06-27 15:02:43','2025-06-28 21:46:49'),
(31,'wewewewewe','fsdfdfdfdfdfdfdf fwesdfdfd',1,0,'2025-06-28 21:46:40',NULL),
(33,'Abirebeye Abayo Sincere Aime Margot','jjkjkjk',1,0,'2025-06-29 01:25:40',NULL),
(34,'Eric','assa',1,0,'2025-06-29 04:14:14',NULL);
/*!40000 ALTER TABLE `program_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sms_templates`
--

DROP TABLE IF EXISTS `sms_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sms_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template_name` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `template_name` (`template_name`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='SMS templates for donation notifications';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sms_templates`
--

LOCK TABLES `sms_templates` WRITE;
/*!40000 ALTER TABLE `sms_templates` DISABLE KEYS */;
INSERT INTO `sms_templates` VALUES
(1,'donation_confirmation','Thank you for your donation of {amount} RWF to Dufatanye Charity Foundation. Reference: {donation_ref}. Your generosity makes a difference!',1,'2025-06-28 13:18:19',NULL),
(2,'donation_receipt','Receipt for {amount} RWF donation. Ref: {donation_ref}. Date: {date}. Thank you for supporting Dufatanye Charity Foundation!',1,'2025-06-28 13:18:19',NULL),
(3,'volunteer_assignment','Hello {firstname}! You have been assigned to {activity_name} ({program_name}) on {date}. Session: {session}. Dufatanye Charity Foundation',1,'2025-06-29 05:00:57',NULL),
(4,'volunteer_registration','Hello {firstname}! Thank you for registering as a volunteer with Dufatanye Charity Foundation. Your application is under review. We will notify you of your status soon. Welcome to our community!',1,'2025-06-29 09:15:13',NULL),
(5,'volunteer_status_update','Hello {firstname}! Your volunteer application status has been updated to: {status}. Volunteer ID: {roll}. Date: {date}. Dufatanye Charity Foundation',1,'2025-06-29 09:15:13',NULL),
(6,'volunteer_approved','Congratulations {firstname}! Your volunteer application has been approved. Your Volunteer ID is {roll}. You can now login to your dashboard and start making a difference. Dufatanye Charity Foundation',1,'2025-06-29 09:15:13',NULL),
(7,'volunteer_denied','Hello {firstname}, thank you for your interest in volunteering with Dufatanye Charity Foundation. Unfortunately, your application was not approved at this time. You may reapply in the future. We appreciate your understanding.',1,'2025-06-29 09:15:13',NULL);
/*!40000 ALTER TABLE `sms_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_info`
--

DROP TABLE IF EXISTS `system_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `system_info` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `meta_field` text NOT NULL,
  `meta_value` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_info`
--

LOCK TABLES `system_info` WRITE;
/*!40000 ALTER TABLE `system_info` DISABLE KEYS */;
INSERT INTO `system_info` VALUES
(1,'name','Online Giveback Management System'),
(2,'short_name','OGMS'),
(3,'logo','uploads/GMS.png'),
(4,'user_avatar','uploads/user_avatar.jpg'),
(5,'cover','uploads/GMS.png'),
(6,'welcome_content','<h2 data-start=\"168\" data-end=\"229\">Welcome to the Online Giveback Management System (OGMS)</h2>\r\n<p data-start=\"231\" data-end=\"259\">We&apos;re glad to have you here!</p>\r\n<p data-start=\"261\" data-end=\"558\">The <strong data-start=\"265\" data-end=\"302\">Online Giveback Management System</strong> </p><p data-start=\"261\" data-end=\"558\">is your all-in-one platform for tracking, managing, and reporting on donations, and inventory givebacks. </p><p data-start=\"261\" data-end=\"558\">Whether you&apos;re an administrator, or contributor, </p><p data-start=\"261\" data-end=\"558\">OGMS is designed to make the giveback process seamless and accountable.</p>\r\n<hr data-start=\"560\" data-end=\"563\">\r\n<h3 data-start=\"565\" data-end=\"592\"><span style=\"color: inherit; font-family: inherit; font-size: 1.75rem;\">🔐 Secure & Transparent</span></h3>\r\n<p data-start=\"1273\" data-end=\"1395\">We prioritize <strong data-start=\"1287\" data-end=\"1331\">security, accountability, and simplicity</strong>, making it easy for every user to manage resources effectively.</p>\r\n\r\n<p data-start=\"1432\" data-end=\"1542\">Log in using your credentials with your organization to begin managing your givebacks responsibly.</p>\r\n<blockquote data-start=\"1544\" data-end=\"1627\">\r\n<p data-start=\"1546\" data-end=\"1627\">Let&apos;s reduce waste, promote reuse, and manage resources the smart way — together.</p>\r\n</blockquote>'),
(7,'home_quote','Come be part of this life-changing experience!');
/*!40000 ALTER TABLE `system_info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `topics`
--

DROP TABLE IF EXISTS `topics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `topics` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `description` text NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '0=Inactive, 1=Active',
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `topics`
--

LOCK TABLES `topics` WRITE;
/*!40000 ALTER TABLE `topics` DISABLE KEYS */;
/*!40000 ALTER TABLE `topics` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `uploads`
--

DROP TABLE IF EXISTS `uploads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `uploads` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `user_id` int(30) NOT NULL,
  `file_path` text NOT NULL,
  `dir_code` varchar(50) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `uploads`
--

LOCK TABLES `uploads` WRITE;
/*!40000 ALTER TABLE `uploads` DISABLE KEYS */;
INSERT INTO `uploads` VALUES
(1,1,'uploads/blog_uploads/gInV4MOSIc/1629172196_1.jpg','gInV4MOSIc','2021-08-17 11:49:56'),
(2,1,'uploads/blog_uploads/gInV4MOSIc/1629172196_download.jpg','gInV4MOSIc','2021-08-17 11:49:56'),
(3,1,'uploads/blog_uploads/qI8ZJiELzQ/1629172988_1.jpg','qI8ZJiELzQ','2021-08-17 12:03:08'),
(4,1,'uploads/blog_uploads/qI8ZJiELzQ/1629172988_download.jpg','qI8ZJiELzQ','2021-08-17 12:03:08'),
(5,1,'uploads/blog_uploads/vLLU8CyJZd/1629174024_1.jpg','vLLU8CyJZd','2021-08-17 12:20:24'),
(6,1,'uploads/blog_uploads/Zk1pDmHIo2/1629176073_1.jpg','Zk1pDmHIo2','2021-08-17 12:54:33'),
(7,1,'uploads/blog_uploads/K1dZZqq4SO/1629176614_warehouse-portrait.jpg','K1dZZqq4SO','2021-08-17 13:03:34'),
(8,1,'uploads/blog_uploads/YSzqldklKk/1629176691_warehouse-portrait.jpg','YSzqldklKk','2021-08-17 13:04:51'),
(10,1,'uploads/blog_uploads/Zk1pDmHIo2/1629176847_warehouse-portrait.jpg','Zk1pDmHIo2','2021-08-17 13:07:27'),
(12,8,'uploads/blog_uploads/No5xPqZ0w8/1708429677_Seal_of_the_United_States_Department_of_State40x40.png','No5xPqZ0w8','2024-02-20 13:47:57'),
(14,1,'uploads/blog_uploads/4zRYV4Kfdu/1709897693_Pack.jpeg','4zRYV4Kfdu','2024-03-08 13:34:53'),
(15,1,'uploads/blog_uploads/causes_uploads/1709907396_ISHIMWE Pacifique ed.jpg','causes_uploads','2024-03-08 16:16:36'),
(16,1,'uploads/blog_uploads/causes_uploads/1709970837_Seal_of_the_United_States_Department_of_State40x40.png','causes_uploads','2024-03-09 09:53:57'),
(17,1,'uploads/blog_uploads/causes_uploads/1710093352_rrms0.png','causes_uploads','2024-03-10 19:55:52'),
(18,1,'uploads/blog_uploads/causes_uploads/1744824856_gms.png','causes_uploads','2025-04-16 19:34:16');
/*!40000 ALTER TABLE `uploads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int(50) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(250) NOT NULL,
  `lastname` varchar(250) NOT NULL,
  `username` text NOT NULL,
  `password` text NOT NULL,
  `avatar` text DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `type` tinyint(1) NOT NULL DEFAULT 0,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
(1,'GMSgfgfgf','Mgmt','admin','0192023a7bbd73250516f069df18b500','uploads/1744930320_admin.png',NULL,1,'2021-01-20 14:02:37','2025-06-27 15:22:45'),
(2,'Ben','S','manager','0795151defba7a4b5dfa89170de46277','uploads/1712993580_Paci.jpeg',NULL,2,'2024-02-10 18:21:27','2025-06-29 03:01:24');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `volunteer_donations`
--

DROP TABLE IF EXISTS `volunteer_donations`;
/*!50001 DROP VIEW IF EXISTS `volunteer_donations`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8mb4;
/*!50001 CREATE VIEW `volunteer_donations` AS SELECT
 1 AS `volunteer_id`,
  1 AS `roll`,
  1 AS `firstname`,
  1 AS `lastname`,
  1 AS `email`,
  1 AS `donation_ref`,
  1 AS `amount`,
  1 AS `payment_method`,
  1 AS `status`,
  1 AS `created_at` */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `volunteer_history`
--

DROP TABLE IF EXISTS `volunteer_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `volunteer_history` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `volunteer_id` int(30) NOT NULL,
  `activity_id` int(30) NOT NULL,
  `s` varchar(200) NOT NULL,
  `year` varchar(200) NOT NULL,
  `years` text NOT NULL,
  `status` int(10) NOT NULL DEFAULT 1,
  `end_status` tinyint(3) NOT NULL DEFAULT 0,
  `email_sent` tinyint(1) NOT NULL DEFAULT 0,
  `sms_sent` tinyint(1) NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `volunteer_id` (`volunteer_id`),
  KEY `activity_id` (`activity_id`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `volunteer_history`
--

LOCK TABLES `volunteer_history` WRITE;
/*!40000 ALTER TABLE `volunteer_history` DISABLE KEYS */;
INSERT INTO `volunteer_history` VALUES
(1,1,1,'','2025-04-30','',1,0,0,0,'2025-04-18 15:27:16',NULL),
(27,1,4,'','2025-05-01','',1,0,0,0,'2025-04-18 16:44:43',NULL),
(28,6,5,'','2025-06-14','',1,0,0,0,'2025-06-09 22:06:04',NULL),
(36,4,5,'web camera','2025-07-16','',1,0,1,1,'2025-06-28 22:51:43','2025-06-29 01:05:57'),
(46,4,2,'booking','2025-07-09','hhhjhjjhhjhj',1,0,1,1,'2025-06-29 01:02:17','2025-06-29 01:02:34'),
(48,4,12,'You can verify this by checking the volunteer\'s phone','2025-07-03','You can verify this by checking the volunteer\'s phone',1,0,1,1,'2025-06-29 01:15:18','2025-06-29 01:15:23'),
(49,4,3,'loyal-advocates','2025-07-10','',1,0,1,0,'2025-06-29 04:41:10','2025-06-29 04:41:25'),
(50,4,3,'dirty angel','2025-07-11','',1,0,1,1,'2025-06-29 04:44:59','2025-06-29 04:45:04');
/*!40000 ALTER TABLE `volunteer_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `volunteer_list`
--

DROP TABLE IF EXISTS `volunteer_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `volunteer_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `roll` varchar(100) NOT NULL COMMENT 'Unique volunteer roll number (YYYY + 3-digit sequence)',
  `firstname` text NOT NULL COMMENT 'Volunteer first name',
  `middlename` text DEFAULT NULL COMMENT 'Volunteer middle name (optional)',
  `lastname` text NOT NULL COMMENT 'Volunteer last name',
  `contact` text NOT NULL COMMENT 'Volunteer phone number',
  `email` varchar(100) NOT NULL COMMENT 'Volunteer email address (unique)',
  `password` varchar(255) NOT NULL DEFAULT '' COMMENT 'Hashed password for volunteer login',
  `motivation` text NOT NULL COMMENT 'Volunteer motivation statement',
  `comment` text NOT NULL COMMENT 'Admin comments about volunteer',
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0=Pending, 1=Approved, 2=Rejected',
  `delete_flag` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0=Active, 1=Deleted (soft delete)',
  `date_created` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Registration date',
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp() COMMENT 'Last update date',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_email` (`email`),
  UNIQUE KEY `unique_roll` (`roll`),
  KEY `idx_email` (`email`),
  KEY `idx_roll` (`roll`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci COMMENT='Volunteer registration and management table';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `volunteer_list`
--

LOCK TABLES `volunteer_list` WRITE;
/*!40000 ALTER TABLE `volunteer_list` DISABLE KEYS */;
INSERT INTO `volunteer_list` VALUES
(4,'2025001','Abirebeye Abayo','Abayo','Margot','0723527270','abayosincere11@gmail.com','$2y$12$K8tfTZ.NHZLdDEFB1f5wx.gnous1mE2J6AN.4HrK0Pg9Dt27r4NeO','I want to show kindness','his guarantees the notification status on the receipt page will always be in sync for all future donations, regardless of how/when notifications are triggered.',1,0,'2025-06-28 16:42:00','2025-06-29 02:25:51'),
(10,'2025002','Abirebeye','Abayo Sincere Aime','Margot','0786729283','sincereabayo@gmail.com','','feruieruiweruiweui','l;kl.klklhjhjhjhjhj',1,0,'2025-06-29 05:21:16','2025-06-29 05:22:02');
/*!40000 ALTER TABLE `volunteer_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Final view structure for view `active_volunteers`
--

/*!50001 DROP VIEW IF EXISTS `active_volunteers`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb3 */;
/*!50001 SET character_set_results     = utf8mb3 */;
/*!50001 SET collation_connection      = utf8mb3_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `active_volunteers` AS select `volunteer_list`.`id` AS `id`,`volunteer_list`.`roll` AS `roll`,`volunteer_list`.`firstname` AS `firstname`,`volunteer_list`.`middlename` AS `middlename`,`volunteer_list`.`lastname` AS `lastname`,`volunteer_list`.`contact` AS `contact`,`volunteer_list`.`email` AS `email`,`volunteer_list`.`motivation` AS `motivation`,`volunteer_list`.`comment` AS `comment`,`volunteer_list`.`status` AS `status`,`volunteer_list`.`date_created` AS `date_created`,`volunteer_list`.`date_updated` AS `date_updated` from `volunteer_list` where `volunteer_list`.`delete_flag` = 0 and `volunteer_list`.`status` = 1 */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `donation_stats`
--

/*!50001 DROP VIEW IF EXISTS `donation_stats`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb3 */;
/*!50001 SET character_set_results     = utf8mb3 */;
/*!50001 SET collation_connection      = utf8mb3_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `donation_stats` AS select count(0) AS `total_donations`,sum(`donations`.`amount`) AS `total_amount`,avg(`donations`.`amount`) AS `avg_amount`,count(case when `donations`.`status` = 'completed' then 1 end) AS `completed_donations`,sum(case when `donations`.`status` = 'completed' then `donations`.`amount` else 0 end) AS `completed_amount`,count(case when `donations`.`volunteer_id` is not null then 1 end) AS `logged_in_donations`,count(case when `donations`.`volunteer_id` is null then 1 end) AS `guest_donations` from `donations` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `pending_volunteers`
--

/*!50001 DROP VIEW IF EXISTS `pending_volunteers`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb3 */;
/*!50001 SET character_set_results     = utf8mb3 */;
/*!50001 SET collation_connection      = utf8mb3_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `pending_volunteers` AS select `volunteer_list`.`id` AS `id`,`volunteer_list`.`roll` AS `roll`,`volunteer_list`.`firstname` AS `firstname`,`volunteer_list`.`middlename` AS `middlename`,`volunteer_list`.`lastname` AS `lastname`,`volunteer_list`.`contact` AS `contact`,`volunteer_list`.`email` AS `email`,`volunteer_list`.`motivation` AS `motivation`,`volunteer_list`.`comment` AS `comment`,`volunteer_list`.`status` AS `status`,`volunteer_list`.`date_created` AS `date_created`,`volunteer_list`.`date_updated` AS `date_updated` from `volunteer_list` where `volunteer_list`.`delete_flag` = 0 and `volunteer_list`.`status` = 0 */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `volunteer_donations`
--

/*!50001 DROP VIEW IF EXISTS `volunteer_donations`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb3 */;
/*!50001 SET character_set_results     = utf8mb3 */;
/*!50001 SET collation_connection      = utf8mb3_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `volunteer_donations` AS select `v`.`id` AS `volunteer_id`,`v`.`roll` AS `roll`,`v`.`firstname` AS `firstname`,`v`.`lastname` AS `lastname`,`v`.`email` AS `email`,`d`.`donation_ref` AS `donation_ref`,`d`.`amount` AS `amount`,`d`.`payment_method` AS `payment_method`,`d`.`status` AS `status`,`d`.`created_at` AS `created_at` from (`volunteer_list` `v` left join `donations` `d` on(`v`.`id` = `d`.`volunteer_id`)) where `d`.`id` is not null order by `d`.`created_at` desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2025-06-29  6:51:53
