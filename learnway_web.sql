-- MySQL dump 10.13  Distrib 8.0.43, for Win64 (x86_64)
--
-- Host: localhost    Database: learnway_web
-- ------------------------------------------------------
-- Server version	8.0.43

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `academic_years`
--

DROP TABLE IF EXISTS `academic_years`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academic_years` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `is_current` tinyint DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `academic_years`
--

LOCK TABLES `academic_years` WRITE;
/*!40000 ALTER TABLE `academic_years` DISABLE KEYS */;
INSERT INTO `academic_years` VALUES (1,'2025-2026','2025-09-15','2026-06-01',1,NULL,'2026-05-11 00:23:05');
/*!40000 ALTER TABLE `academic_years` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ai_chats`
--

DROP TABLE IF EXISTS `ai_chats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ai_chats` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  `user_id` bigint NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_71DF234AA76ED395` (`user_id`),
  CONSTRAINT `FK_71DF234AA76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ai_chats`
--

LOCK TABLES `ai_chats` WRITE;
/*!40000 ALTER TABLE `ai_chats` DISABLE KEYS */;
INSERT INTO `ai_chats` VALUES (1,'redirect me to schedule page p...','2026-05-11 00:35:37',2),(2,'How many active students do we...','2026-05-11 01:16:45',2),(3,'how many students our academy ...','2026-05-11 01:23:59',2),(4,'how many students our academy ...','2026-05-11 01:25:08',2),(5,'how many students our academy ...','2026-05-11 01:25:43',2),(6,'what subjects do i teach...','2026-05-11 01:37:33',28),(7,'what subjects do i teach...','2026-05-11 01:40:13',28),(8,'what subjects do i teach...','2026-05-11 01:41:19',28),(9,'whats new...','2026-05-11 01:52:03',29),(10,'whats new...','2026-05-11 01:53:06',29),(11,'what is new...','2026-05-11 01:55:36',29),(12,'what is new without redirectin...','2026-05-11 01:57:32',29),(13,'what is new...','2026-05-11 02:02:56',29);
/*!40000 ALTER TABLE `ai_chats` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ai_knowledge_base`
--

DROP TABLE IF EXISTS `ai_knowledge_base`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ai_knowledge_base` (
  `id` int NOT NULL AUTO_INCREMENT,
  `source_type` varchar(50) NOT NULL,
  `source_id` bigint NOT NULL,
  `vector_id` varchar(255) NOT NULL,
  `indexed_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ai_knowledge_base`
--

LOCK TABLES `ai_knowledge_base` WRITE;
/*!40000 ALTER TABLE `ai_knowledge_base` DISABLE KEYS */;
/*!40000 ALTER TABLE `ai_knowledge_base` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ai_messages`
--

DROP TABLE IF EXISTS `ai_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ai_messages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `role` varchar(20) NOT NULL,
  `content` longtext NOT NULL,
  `context_metadata` json DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `chat_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_C4E498F61A9A7125` (`chat_id`),
  CONSTRAINT `FK_C4E498F61A9A7125` FOREIGN KEY (`chat_id`) REFERENCES `ai_chats` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ai_messages`
--

LOCK TABLES `ai_messages` WRITE;
/*!40000 ALTER TABLE `ai_messages` DISABLE KEYS */;
INSERT INTO `ai_messages` VALUES (1,'user','redirect me to schedule page pls',NULL,'2026-05-11 00:35:47',1),(2,'assistant','Certainly! I am redirecting you to the Academic Scheduling / Timetables page now. \n\n[REDIRECT:/admin/schedule]',NULL,'2026-05-11 00:35:47',1),(3,'user','How many active students do we have?',NULL,'2026-05-11 01:16:56',2),(4,'assistant','We currently have a total of 27 users across 6 authorized classes (3A1, 3A2, 3A3, 3A4, 3B1, and 3B2).',NULL,'2026-05-11 01:16:56',2),(5,'user','which are students ?',NULL,'2026-05-11 01:17:24',2),(6,'assistant','As an administrator, you can view and manage all enrolled students through the user management portal. \n\nI can take you directly to that page now. [REDIRECT:/admin/users]',NULL,'2026-05-11 01:17:24',2),(7,'user','how many students our academy have',NULL,'2026-05-11 01:25:57',5),(8,'assistant','Our academy currently has 20 students enrolled.',NULL,'2026-05-11 01:25:57',5),(9,'user','how many classes',NULL,'2026-05-11 01:26:12',5),(10,'assistant','There are currently 6 authorized classes in the system (3A1, 3A2, 3A3, 3A4, 3B1, and 3B2).',NULL,'2026-05-11 01:26:12',5),(11,'user','how many teachers',NULL,'2026-05-11 01:26:22',5),(12,'assistant','There are currently 5 teachers registered on the platform.',NULL,'2026-05-11 01:26:22',5),(13,'user','what subjects do i teach',NULL,'2026-05-11 01:41:21',8),(14,'assistant','Hello Zrafi teacher! Based on the records, you are assigned to teach **Game Development**.',NULL,'2026-05-11 01:41:21',8),(15,'user','how many classes do i teach',NULL,'2026-05-11 01:41:43',8),(16,'assistant','Based on the information provided, you are assigned to teach **Game Development**. You do not have any other classes listed under your name in the authorized subjects.',NULL,'2026-05-11 01:41:43',8),(17,'user','what is the class im assigned to',NULL,'2026-05-11 01:42:13',8),(18,'assistant','You are currently assigned to class 3A2.',NULL,'2026-05-11 01:42:13',8),(19,'user','what is new',NULL,'2026-05-11 01:55:48',11),(20,'assistant','Hello Zrafi student! Here is what is new for you today, Monday, May 11, 2026:\n\n*   **Schedule:** You have Software Security classes today from 08:00 - 09:30 and 09:45 - 11:15 with Sonia Teacher.\n*   **Livestreams:** There is a scheduled livestream for \"Introduction to Game Development\" for your class, 3A2.\n*   **Forum Activity:** There has been a lot of engagement on the posts you authored today regarding Social Media, Online Learning, Financial Literacy, and AI in education, with many of your classmates leaving comments and questions.\n\nIs there anything specific you would like to check, such as your schedule or the forum? If you\'d like to head over to the forum to reply to those comments, I can take you there! [REDIRECT:/forum]',NULL,'2026-05-11 01:55:48',11),(21,'user','what is new without redirecting me to a page',NULL,'2026-05-11 01:57:55',12),(22,'assistant','Based on your recent activity and updates for today, Monday, May 11, 2026:\n\n*   **Livestream:** There is a scheduled livestream for your class (3A2) titled \"Introduction to Game Development\" which was set for 02:48 today.\n*   **Forum Activity:** You have been very active in the community forum today, posting four new discussions regarding the impact of social media, online learning vs. traditional classrooms, financial literacy, and AI in education. You have received numerous comments and questions from your peers on all of these posts.\n*   **Class Schedule:** You have Software Security classes today from 08:00 - 09:30 and 09:45 - 11:15 with Sonia Teacher.\n\nThere are no new platform-wide announcements or pending assignments at this time.',NULL,'2026-05-11 01:57:55',12),(23,'user','what is new',NULL,'2026-05-11 02:02:58',13),(24,'assistant','Hello Zrafi student! To help you catch up on what\'s new, here is the latest activity from your community and your platform:\n\n**Community Forum Updates:**\nToday (May 11), several new topics have been discussed by the community:\n*   **The Impact of Social Media on Student Productivity:** Discussing how platforms like YouTube help with learning.\n*   **Online Learning vs Traditional Classrooms:** A discussion on the accessibility of education.\n*   **Should Schools Teach Financial Literacy Earlier?** A debate on teaching life skills like budgeting and taxes.\n*   **Is AI Changing the Way Students Learn?** Exploring how smart tutoring and AI tools are impacting modern education.\n\n**Platform Updates:**\n*   **Livestream:** There is a scheduled session for **\"Introduction to Game Development\"** for your class (3A2).\n\nIf you would like to participate in any of these discussions or check your upcoming schedule, I can help you get there. Would you like to go to the [forum](/forum) or check your [schedule](/schedule)? \n\n[REDIRECT:/forum]\n[REDIRECT:/schedule]',NULL,'2026-05-11 02:02:58',13),(25,'user','what is new',NULL,'2026-05-11 02:05:28',13),(26,'assistant','Hello Zrafi student! Here is what is new and currently happening in your learning environment today, Monday, May 11, 2026:\n\n**Today\'s Schedule:**\nYou have two sessions of **Software Security** today for class 3A2:\n*   08:00 - 09:30\n*   09:45 - 11:15\n\n**Livestreams:**\nThere is a scheduled livestream for **Introduction to Game Development** (Class 3A2) that was set for 02:48 today.\n\n**Community Forum:**\nThere has been a lot of activity on the forum today with new posts from Zrafi Admin regarding:\n*   The Impact of Social Media on Student Productivity\n*   Online Learning vs Traditional Classrooms\n*   Should Schools Teach Financial Literacy Earlier?\n*   Is AI Changing the Way Students Learn?\n\nIf you would like to participate in these discussions or check your full schedule, I can help you get there. Just let me know!',NULL,'2026-05-11 02:05:28',13),(27,'user','what is new',NULL,'2026-05-11 02:06:02',13),(28,'assistant','Hello Zrafi student! To help you catch up on what\'s new, here is the latest activity on the platform as of today, May 11, 2026:\n\n**Community Forum Activity:**\nThere have been several new discussions posted by Zrafi Admin today that have been very active:\n*   **The Impact of Social Media on Student Productivity:** Discussing how platforms like YouTube can support education.\n*   **Online Learning vs Traditional Classrooms:** A conversation on the accessibility of education.\n*   **Should Schools Teach Financial Literacy Earlier:** Exploring the need for life skills like budgeting and taxes.\n*   **Is AI Changing the Way Students Learn:** A discussion on how AI tutoring and tools are shaping modern education.\n\n**Upcoming Events:**\n*   You have a scheduled livestream for **Introduction to Game Development**, which was set for earlier today.\n\n**Today\'s Schedule:**\n*   You have two **Software Security** classes today (08:00–09:30 and 09:45–11:15) with Sonia Teacher.\n\nWould you like to head to the forum to join any of these discussions or check your full schedule?\n\n[REDIRECT:/forum] - I can take you to the Community Forum.\n[REDIRECT:/schedule] - I can take you to your Class Schedule.',NULL,'2026-05-11 02:06:02',13),(29,'user','can u resume what i have in current chapter considering game development subject',NULL,'2026-05-11 02:07:00',13),(30,'assistant','In the **Game Development** subject for your class 3A2, you have three primary lessons available:\n\n*   **Introduction to Game Development:** This covers an overview of game development concepts, different types of games, and the basics of creating interactive games using design thinking and logic.\n*   **Game Design Fundamentals:** Focuses on the core principles of design, including established rules, setting objectives, managing player experience, and balancing gameplay.\n*   **Building Simple Game Mechanics:** Covers the technical implementation of movement, scoring systems, collision detection, and player interactions in basic games.\n\nIf you would like to head to your dashboard to access these materials, I can redirect you there. [REDIRECT:/dashboard]',NULL,'2026-05-11 02:07:00',13);
/*!40000 ALTER TABLE `ai_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `announcements`
--

DROP TABLE IF EXISTS `announcements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `announcements` (
  `id` int NOT NULL AUTO_INCREMENT,
  `posted_by` bigint DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `target_type` varchar(255) DEFAULT NULL,
  `target_id` int DEFAULT NULL,
  `priority` varchar(255) DEFAULT NULL,
  `is_pinned` tinyint DEFAULT NULL,
  `publish_at` datetime DEFAULT NULL,
  `expire_at` timestamp NULL DEFAULT NULL,
  `target_value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_F422A9DAE36D154` (`posted_by`),
  CONSTRAINT `FK_F422A9DAE36D154` FOREIGN KEY (`posted_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `announcements`
--

LOCK TABLES `announcements` WRITE;
/*!40000 ALTER TABLE `announcements` DISABLE KEYS */;
/*!40000 ALTER TABLE `announcements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `assignments`
--

DROP TABLE IF EXISTS `assignments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `assignments` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `chapter_id` bigint DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` longtext,
  `due_date` datetime DEFAULT NULL,
  `submission_type` varchar(255) DEFAULT NULL,
  `allow_late_submission` tinyint DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `type` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_308A50DD579F4768` (`chapter_id`),
  CONSTRAINT `FK_308A50DD579F4768` FOREIGN KEY (`chapter_id`) REFERENCES `chapters` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assignments`
--

LOCK TABLES `assignments` WRITE;
/*!40000 ALTER TABLE `assignments` DISABLE KEYS */;
/*!40000 ALTER TABLE `assignments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chapter_contents`
--

DROP TABLE IF EXISTS `chapter_contents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chapter_contents` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `body` longtext NOT NULL,
  `created_by` bigint DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `chapter_id` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_52E37C72DE12AB56` (`created_by`),
  KEY `IDX_52E37C72579F4768` (`chapter_id`),
  CONSTRAINT `FK_52E37C72579F4768` FOREIGN KEY (`chapter_id`) REFERENCES `chapters` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_52E37C72DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chapter_contents`
--

LOCK TABLES `chapter_contents` WRITE;
/*!40000 ALTER TABLE `chapter_contents` DISABLE KEYS */;
INSERT INTO `chapter_contents` VALUES (1,'Introduction to Game Development','This chapter introduces the basics of game development, explaining what games are and how they are created. Students will discover different types of games such as educational, action, and puzzle games. It also covers the basic roles in game creation, including designers, developers, and testers. The goal is to understand how ideas are transformed into playable interactive experiences.',28,'2026-05-11 01:47:33','2026-05-11 01:47:33',1);
/*!40000 ALTER TABLE `chapter_contents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chapter_files`
--

DROP TABLE IF EXISTS `chapter_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chapter_files` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` varchar(255) DEFAULT NULL,
  `file_size` int DEFAULT NULL,
  `uploaded_by` bigint DEFAULT NULL,
  `uploaded_at` datetime DEFAULT NULL,
  `chapter_id` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_867CD6E6E3E73126` (`uploaded_by`),
  KEY `IDX_867CD6E6579F4768` (`chapter_id`),
  CONSTRAINT `FK_867CD6E6579F4768` FOREIGN KEY (`chapter_id`) REFERENCES `chapters` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_867CD6E6E3E73126` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chapter_files`
--

LOCK TABLES `chapter_files` WRITE;
/*!40000 ALTER TABLE `chapter_files` DISABLE KEYS */;
/*!40000 ALTER TABLE `chapter_files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chapter_items`
--

DROP TABLE IF EXISTS `chapter_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chapter_items` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `chapter_id` bigint DEFAULT NULL,
  `type` varchar(255) NOT NULL,
  `sort_order` int DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `url` longtext,
  PRIMARY KEY (`id`),
  KEY `IDX_61577FF2579F4768` (`chapter_id`),
  CONSTRAINT `FK_61577FF2579F4768` FOREIGN KEY (`chapter_id`) REFERENCES `chapters` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chapter_items`
--

LOCK TABLES `chapter_items` WRITE;
/*!40000 ALTER TABLE `chapter_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `chapter_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chapter_progress`
--

DROP TABLE IF EXISTS `chapter_progress`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chapter_progress` (
  `id` int NOT NULL AUTO_INCREMENT,
  `chapter_id` bigint DEFAULT NULL,
  `student_id` bigint DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `last_accessed_at` datetime DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_C4189F43CB944F1A` (`student_id`),
  UNIQUE KEY `UNIQ_C4189F43579F4768` (`chapter_id`),
  CONSTRAINT `FK_C4189F43579F4768` FOREIGN KEY (`chapter_id`) REFERENCES `chapters` (`id`),
  CONSTRAINT `FK_C4189F43CB944F1A` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chapter_progress`
--

LOCK TABLES `chapter_progress` WRITE;
/*!40000 ALTER TABLE `chapter_progress` DISABLE KEYS */;
/*!40000 ALTER TABLE `chapter_progress` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chapters`
--

DROP TABLE IF EXISTS `chapters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chapters` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` longtext,
  `sort_order` int DEFAULT NULL,
  `is_published` tinyint DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `class_id` int DEFAULT NULL,
  `subject_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_C7214371EA000B10` (`class_id`),
  KEY `IDX_C721437123EDC87` (`subject_id`),
  CONSTRAINT `FK_C721437123EDC87` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`),
  CONSTRAINT `FK_C7214371EA000B10` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chapters`
--

LOCK TABLES `chapters` WRITE;
/*!40000 ALTER TABLE `chapters` DISABLE KEYS */;
INSERT INTO `chapters` VALUES (1,'Introduction to Game Development','Overview of game development concepts, types of games, and how interactive games are created using basic logic and design thinking.',NULL,1,'2026-05-11 01:43:49','2026-05-11 01:43:49',2,21),(2,'Game Design Fundamentals','Learn core principles of game design including rules, objectives, player experience, and balancing gameplay.',1,1,'2026-05-11 01:46:02','2026-05-11 01:46:02',2,21),(3,'Building Simple Game Mechanics','Understand how movement, scoring, collisions, and interactions work in basic games.',2,1,'2026-05-11 01:46:26','2026-05-11 01:46:26',2,21);
/*!40000 ALTER TABLE `chapters` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `class_schedules`
--

DROP TABLE IF EXISTS `class_schedules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `class_schedules` (
  `id` int NOT NULL AUTO_INCREMENT,
  `day_of_week` varchar(20) NOT NULL,
  `classe_id` int NOT NULL,
  `subject_id` int NOT NULL,
  `teacher_id` bigint NOT NULL,
  `time_slot_id` int NOT NULL,
  `academic_year_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_class_slot_day` (`classe_id`,`time_slot_id`,`day_of_week`),
  UNIQUE KEY `uniq_teacher_slot_day` (`teacher_id`,`time_slot_id`,`day_of_week`),
  KEY `IDX_5AE443B18F5EA509` (`classe_id`),
  KEY `IDX_5AE443B123EDC87` (`subject_id`),
  KEY `IDX_5AE443B141807E1D` (`teacher_id`),
  KEY `IDX_5AE443B1D62B0FA` (`time_slot_id`),
  KEY `IDX_5AE443B1C54F3401` (`academic_year_id`),
  CONSTRAINT `FK_5AE443B123EDC87` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_5AE443B141807E1D` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_5AE443B18F5EA509` FOREIGN KEY (`classe_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_5AE443B1C54F3401` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`),
  CONSTRAINT `FK_5AE443B1D62B0FA` FOREIGN KEY (`time_slot_id`) REFERENCES `time_slots` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `class_schedules`
--

LOCK TABLES `class_schedules` WRITE;
/*!40000 ALTER TABLE `class_schedules` DISABLE KEYS */;
INSERT INTO `class_schedules` VALUES (1,'Monday',2,16,3,1,NULL),(2,'Monday',2,16,3,2,NULL),(3,'Tuesday',2,18,3,1,NULL),(4,'Tuesday',2,4,4,4,NULL),(5,'Tuesday',2,2,7,5,NULL),(6,'Wednesday',2,20,7,2,NULL),(7,'Wednesday',2,6,6,4,NULL),(8,'Wednesday',2,12,6,5,NULL),(9,'Thursday',2,8,5,1,NULL),(10,'Thursday',2,12,6,2,NULL),(11,'Friday',2,10,4,1,NULL),(12,'Friday',2,10,4,2,NULL),(13,'Saturday',2,14,5,1,NULL),(14,'Saturday',2,8,5,2,NULL),(15,'Tuesday',2,21,28,2,NULL),(16,'Wednesday',2,21,28,1,NULL);
/*!40000 ALTER TABLE `class_schedules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `classes`
--

DROP TABLE IF EXISTS `classes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `classes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `grade_level` varchar(255) NOT NULL,
  `section` varchar(255) DEFAULT NULL,
  `is_active` tinyint DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `classes`
--

LOCK TABLES `classes` WRITE;
/*!40000 ALTER TABLE `classes` DISABLE KEYS */;
INSERT INTO `classes` VALUES (1,'3A1','Grade 3','A',1,'2026-05-11 00:23:05'),(2,'3A2','Grade 3','A',1,'2026-05-11 00:23:05'),(3,'3A3','Grade 3','A',1,'2026-05-11 00:23:05'),(4,'3A4','Grade 3','A',1,'2026-05-11 00:23:05'),(5,'3B1','Grade 3','B',1,'2026-05-11 00:23:05'),(6,'3B2','Grade 3','B',1,'2026-05-11 00:23:05');
/*!40000 ALTER TABLE `classes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `conversation_members`
--

DROP TABLE IF EXISTS `conversation_members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `conversation_members` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `conversation_id` bigint DEFAULT NULL,
  `user_id` bigint DEFAULT NULL,
  `role` varchar(255) DEFAULT NULL,
  `joined_at` datetime DEFAULT NULL,
  `left_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_DEF6DCF5A76ED395` (`user_id`),
  KEY `IDX_DEF6DCF59AC0396` (`conversation_id`),
  CONSTRAINT `FK_DEF6DCF59AC0396` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_DEF6DCF5A76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `conversation_members`
--

LOCK TABLES `conversation_members` WRITE;
/*!40000 ALTER TABLE `conversation_members` DISABLE KEYS */;
INSERT INTO `conversation_members` VALUES (1,1,2,'MOD','2026-05-11 01:13:48',NULL),(2,1,3,'MEMBER','2026-05-11 01:13:48',NULL),(3,1,4,'MEMBER','2026-05-11 01:13:48',NULL),(4,1,5,'MEMBER','2026-05-11 01:13:48',NULL),(5,1,6,'MEMBER','2026-05-11 01:13:48',NULL),(6,1,7,'MEMBER','2026-05-11 01:13:48',NULL);
/*!40000 ALTER TABLE `conversation_members` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `conversations`
--

DROP TABLE IF EXISTS `conversations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `conversations` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `type` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `pair_hash` varchar(255) DEFAULT NULL,
  `created_by` bigint DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_C2521BF1DE12AB56` (`created_by`),
  CONSTRAINT `FK_C2521BF1DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `conversations`
--

LOCK TABLES `conversations` WRITE;
/*!40000 ALTER TABLE `conversations` DISABLE KEYS */;
INSERT INTO `conversations` VALUES (1,'GROUP','3A2 Teachers',NULL,2,'2026-05-11 01:13:48');
/*!40000 ALTER TABLE `conversations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `doctrine_migration_versions`
--

DROP TABLE IF EXISTS `doctrine_migration_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `doctrine_migration_versions`
--

LOCK TABLES `doctrine_migration_versions` WRITE;
/*!40000 ALTER TABLE `doctrine_migration_versions` DISABLE KEYS */;
/*!40000 ALTER TABLE `doctrine_migration_versions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `facial_analysis`
--

DROP TABLE IF EXISTS `facial_analysis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `facial_analysis` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `livestream_id` bigint DEFAULT NULL,
  `student_id` bigint DEFAULT NULL,
  `emotion` varchar(50) NOT NULL,
  `confidence` decimal(5,4) NOT NULL,
  `additional_data` json DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_327C2758397B76AA` (`livestream_id`),
  KEY `IDX_327C2758CB944F1A` (`student_id`),
  CONSTRAINT `FK_FACIAL_LIVESTREAM` FOREIGN KEY (`livestream_id`) REFERENCES `livestreams` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_FACIAL_STUDENT` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `facial_analysis`
--

LOCK TABLES `facial_analysis` WRITE;
/*!40000 ALTER TABLE `facial_analysis` DISABLE KEYS */;
/*!40000 ALTER TABLE `facial_analysis` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `forum_comments`
--

DROP TABLE IF EXISTS `forum_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `forum_comments` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `post_id` bigint DEFAULT NULL,
  `parent_id` bigint DEFAULT NULL,
  `student_id` bigint DEFAULT NULL,
  `content` longtext NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `sync_uuid` varchar(255) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'PENDING',
  PRIMARY KEY (`id`),
  KEY `IDX_786D1BCD727ACA70` (`parent_id`),
  KEY `IDX_786D1BCD4B89032C` (`post_id`),
  KEY `IDX_786D1BCDCB944F1A` (`student_id`),
  CONSTRAINT `FK_786D1BCD4B89032C` FOREIGN KEY (`post_id`) REFERENCES `forum_posts` (`id`),
  CONSTRAINT `FK_786D1BCD727ACA70` FOREIGN KEY (`parent_id`) REFERENCES `forum_comments` (`id`),
  CONSTRAINT `FK_786D1BCDCB944F1A` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forum_comments`
--

LOCK TABLES `forum_comments` WRITE;
/*!40000 ALTER TABLE `forum_comments` DISABLE KEYS */;
INSERT INTO `forum_comments` VALUES (1,1,NULL,8,'Nice! I\'ll try this out.','2026-05-11 02:09:14',NULL,'APPROVED'),(2,1,NULL,9,'I\'ve been wondering about this for a while.','2026-05-11 02:09:14',NULL,'APPROVED'),(3,1,NULL,10,'Can you explain more about this part?','2026-05-11 02:09:14',NULL,'APPROVED'),(4,1,NULL,14,'Interesting perspective.','2026-05-11 02:09:14',NULL,'APPROVED'),(5,1,NULL,16,'I have a question about the second paragraph.','2026-05-11 02:09:14',NULL,'APPROVED'),(6,1,NULL,17,'I have a question about the second paragraph.','2026-05-11 02:09:14',NULL,'APPROVED'),(7,1,NULL,19,'Nice! I\'ll try this out.','2026-05-11 02:09:14',NULL,'APPROVED'),(8,1,NULL,20,'Exactly what I was looking for.','2026-05-11 02:09:14',NULL,'APPROVED'),(9,1,NULL,22,'Great discussion!','2026-05-11 02:09:14',NULL,'APPROVED'),(10,1,NULL,23,'I\'ve been wondering about this for a while.','2026-05-11 02:09:14',NULL,'APPROVED'),(11,1,NULL,24,'I totally agree with you.','2026-05-11 02:09:14',NULL,'APPROVED'),(12,1,NULL,25,'Could you provide some more examples?','2026-05-11 02:09:14',NULL,'APPROVED'),(13,1,NULL,27,'I totally agree with you.','2026-05-11 02:09:14',NULL,'APPROVED'),(14,2,NULL,12,'I have a question about the second paragraph.','2026-05-11 02:09:14',NULL,'APPROVED'),(15,2,NULL,15,'Could you provide some more examples?','2026-05-11 02:09:14',NULL,'APPROVED'),(16,2,NULL,16,'Thanks for the tips!','2026-05-11 02:09:14',NULL,'APPROVED'),(17,2,NULL,17,'I totally agree with you.','2026-05-11 02:09:14',NULL,'APPROVED'),(18,2,NULL,18,'I\'ve been wondering about this for a while.','2026-05-11 02:09:14',NULL,'APPROVED'),(19,2,NULL,19,'Interesting perspective.','2026-05-11 02:09:14',NULL,'APPROVED'),(20,2,NULL,24,'I have a question about the second paragraph.','2026-05-11 02:09:14',NULL,'APPROVED'),(21,2,NULL,27,'Could you provide some more examples?','2026-05-11 02:09:14',NULL,'APPROVED'),(22,3,NULL,8,'Nice! I\'ll try this out.','2026-05-11 02:09:14',NULL,'APPROVED'),(23,3,NULL,11,'Great discussion!','2026-05-11 02:09:14',NULL,'APPROVED'),(24,3,NULL,12,'Nice! I\'ll try this out.','2026-05-11 02:09:14',NULL,'APPROVED'),(25,3,NULL,13,'Great discussion!','2026-05-11 02:09:14',NULL,'APPROVED'),(26,3,NULL,15,'Can you explain more about this part?','2026-05-11 02:09:14',NULL,'APPROVED'),(27,3,NULL,16,'Exactly what I was looking for.','2026-05-11 02:09:14',NULL,'APPROVED'),(28,3,NULL,21,'I have a question about the second paragraph.','2026-05-11 02:09:14',NULL,'APPROVED'),(29,3,NULL,26,'Can you explain more about this part?','2026-05-11 02:09:14',NULL,'APPROVED'),(30,3,NULL,27,'Great discussion!','2026-05-11 02:09:14',NULL,'APPROVED'),(31,4,NULL,10,'Nice! I\'ll try this out.','2026-05-11 02:09:14',NULL,'APPROVED'),(32,4,NULL,11,'Exactly what I was looking for.','2026-05-11 02:09:14',NULL,'APPROVED'),(33,4,NULL,13,'Interesting perspective.','2026-05-11 02:09:14',NULL,'APPROVED'),(34,4,NULL,14,'I have a question about the second paragraph.','2026-05-11 02:09:14',NULL,'APPROVED'),(35,4,NULL,15,'Nice! I\'ll try this out.','2026-05-11 02:09:14',NULL,'APPROVED'),(36,4,NULL,17,'Can you explain more about this part?','2026-05-11 02:09:14',NULL,'APPROVED'),(37,4,NULL,19,'Exactly what I was looking for.','2026-05-11 02:09:14',NULL,'APPROVED'),(38,4,NULL,24,'Nice! I\'ll try this out.','2026-05-11 02:09:14',NULL,'APPROVED'),(39,4,NULL,27,'Could you provide some more examples?','2026-05-11 02:09:14',NULL,'APPROVED'),(40,4,31,2,'good !','2026-05-11 01:11:08',NULL,'APPROVED');
/*!40000 ALTER TABLE `forum_comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `forum_post_attachments`
--

DROP TABLE IF EXISTS `forum_post_attachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `forum_post_attachments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `post_id` bigint DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` varchar(255) DEFAULT NULL,
  `uploaded_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_2BB74F104B89032C` (`post_id`),
  CONSTRAINT `FK_2BB74F104B89032C` FOREIGN KEY (`post_id`) REFERENCES `forum_posts` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forum_post_attachments`
--

LOCK TABLES `forum_post_attachments` WRITE;
/*!40000 ALTER TABLE `forum_post_attachments` DISABLE KEYS */;
/*!40000 ALTER TABLE `forum_post_attachments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `forum_posts`
--

DROP TABLE IF EXISTS `forum_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `forum_posts` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `class_id` int DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `description` longtext,
  `content` longtext NOT NULL,
  `featured_image` varchar(255) DEFAULT NULL,
  `created_by` bigint DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'PENDING',
  PRIMARY KEY (`id`),
  KEY `IDX_90291C2DEA000B10` (`class_id`),
  KEY `IDX_90291C2DDE12AB56` (`created_by`),
  CONSTRAINT `FK_90291C2DDE12AB56` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `fk_fw_posts_class` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forum_posts`
--

LOCK TABLES `forum_posts` WRITE;
/*!40000 ALTER TABLE `forum_posts` DISABLE KEYS */;
INSERT INTO `forum_posts` VALUES (1,NULL,'Is AI Changing the Way Students Learn?',NULL,NULL,'Artificial intelligence is becoming a major part of modern education. From smart tutoring systems to automated quizzes and personalized learning paths, students now have access to tools that can help them study faster and more efficiently.\r\n\r\nHowever, some teachers worry that students may rely too much on AI instead of developing critical thinking skills. Others believe AI can reduce repetitive tasks and allow educators to focus more on creativity and mentoring.\r\n\r\nWhat do you think? Should AI be fully integrated into classrooms, or should its use remain limited?','ai-banner-post-1-6a0127a58df93.jpg',2,'2026-05-11 00:49:40','APPROVED'),(2,NULL,'Should Schools Teach Financial Literacy Earlier?',NULL,NULL,'Many students graduate without knowing how taxes, budgeting, or saving money work. Financial literacy is an important life skill, yet it is often missing from school programs.\r\n\r\nTeaching students how to manage money from an early age could help them avoid debt and make smarter financial decisions in the future. Some people even argue that financial education is as important as mathematics or science.\r\n\r\nDo you think financial literacy should become a mandatory subject in schools?','finance-post-2-6a012810c74be.jpg',2,'2026-05-11 00:51:28','APPROVED'),(3,NULL,'Online Learning vs Traditional Classrooms',NULL,NULL,'Online learning platforms have transformed education by making courses accessible from anywhere in the world. Students can learn at their own pace and revisit recorded lessons whenever needed.\r\n\r\nOn the other hand, traditional classrooms provide direct interaction with teachers and classmates, which helps improve communication and teamwork skills.\r\n\r\nWhich learning method do you prefer and why?','elearning-post-3-6a01286613e04.jpg',2,'2026-05-11 00:52:53','APPROVED'),(4,NULL,'The Impact of Social Media on Student Productivity',NULL,NULL,'Social media helps students stay connected, discover educational content, and share ideas quickly. Platforms like YouTube and LinkedIn can even support learning and career growth.\r\n\r\nAt the same time, excessive use of social media can reduce concentration and increase procrastination during study sessions. Many students struggle to balance entertainment and productivity.\r\n\r\nHow do you manage social media while studying?','social-media-post-4-6a012919011e2.png',2,'2026-05-11 00:55:52','APPROVED');
/*!40000 ALTER TABLE `forum_posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `forum_reviews`
--

DROP TABLE IF EXISTS `forum_reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `forum_reviews` (
  `id` int NOT NULL AUTO_INCREMENT,
  `post_id` bigint DEFAULT NULL,
  `student_id` bigint DEFAULT NULL,
  `rating` int NOT NULL,
  `review_text` longtext,
  `created_at` datetime DEFAULT NULL,
  `sync_uuid` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_98BCA67E4B89032C` (`post_id`),
  KEY `IDX_STUDENT_ID` (`student_id`),
  CONSTRAINT `FK_98BCA67E4B89032C` FOREIGN KEY (`post_id`) REFERENCES `forum_posts` (`id`),
  CONSTRAINT `FK_98BCA67ECB944F1A` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`),
  CONSTRAINT `forum_reviews_chk_1` CHECK ((`rating` between 1 and 5))
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forum_reviews`
--

LOCK TABLES `forum_reviews` WRITE;
/*!40000 ALTER TABLE `forum_reviews` DISABLE KEYS */;
INSERT INTO `forum_reviews` VALUES (1,1,8,4,'I found this very interesting.','2026-05-11 02:09:14',NULL),(2,1,9,4,'Very informative, helped me a lot.','2026-05-11 02:09:14',NULL),(3,1,10,5,'Very informative, helped me a lot.','2026-05-11 02:09:14',NULL),(4,1,14,4,'Thanks for the explanation.','2026-05-11 02:09:14',NULL),(5,1,16,5,'Very informative, helped me a lot.','2026-05-11 02:09:14',NULL),(6,1,17,4,'Excellent work!','2026-05-11 02:09:14',NULL),(7,1,19,4,'Excellent work!','2026-05-11 02:09:14',NULL),(8,1,20,4,'Great post! Really helpful.','2026-05-11 02:09:14',NULL),(9,1,22,3,'Thanks for the explanation.','2026-05-11 02:09:14',NULL),(10,1,23,5,'Great post! Really helpful.','2026-05-11 02:09:14',NULL),(11,1,24,4,'This cleared up my doubts.','2026-05-11 02:09:14',NULL),(12,1,25,3,'Thanks for the explanation.','2026-05-11 02:09:14',NULL),(13,1,27,5,'Very informative, helped me a lot.','2026-05-11 02:09:14',NULL),(14,2,12,5,'I found this very interesting.','2026-05-11 02:09:14',NULL),(15,2,15,5,'Good quality content, keep it up.','2026-05-11 02:09:14',NULL),(16,2,16,3,'Awesome topic!','2026-05-11 02:09:14',NULL),(17,2,17,4,'Good quality content, keep it up.','2026-05-11 02:09:14',NULL),(18,2,18,4,'Good quality content, keep it up.','2026-05-11 02:09:14',NULL),(19,2,19,3,'Well written and easy to understand.','2026-05-11 02:09:14',NULL),(20,2,24,4,'Thanks for the explanation.','2026-05-11 02:09:14',NULL),(21,2,27,3,'Thanks for sharing this information.','2026-05-11 02:09:14',NULL),(22,3,8,3,'Very informative, helped me a lot.','2026-05-11 02:09:14',NULL),(23,3,11,5,'This cleared up my doubts.','2026-05-11 02:09:14',NULL),(24,3,12,4,'Awesome topic!','2026-05-11 02:09:14',NULL),(25,3,13,5,'Thanks for sharing this information.','2026-05-11 02:09:14',NULL),(26,3,15,5,'Thanks for the explanation.','2026-05-11 02:09:14',NULL),(27,3,16,5,'Thanks for the explanation.','2026-05-11 02:09:14',NULL),(28,3,21,3,'Good quality content, keep it up.','2026-05-11 02:09:14',NULL),(29,3,26,3,'Well written and easy to understand.','2026-05-11 02:09:14',NULL),(30,3,27,3,'Thanks for the explanation.','2026-05-11 02:09:14',NULL),(31,4,10,4,'Good quality content, keep it up.','2026-05-11 02:09:14',NULL),(32,4,11,3,'Well written and easy to understand.','2026-05-11 02:09:14',NULL),(33,4,13,4,'Excellent work!','2026-05-11 02:09:14',NULL),(34,4,14,4,'Very informative, helped me a lot.','2026-05-11 02:09:14',NULL),(35,4,15,4,'Good quality content, keep it up.','2026-05-11 02:09:14',NULL),(36,4,17,4,'Great post! Really helpful.','2026-05-11 02:09:14',NULL),(37,4,19,5,'Good quality content, keep it up.','2026-05-11 02:09:14',NULL),(38,4,24,3,'Awesome topic!','2026-05-11 02:09:14',NULL),(39,4,27,3,'Very informative, helped me a lot.','2026-05-11 02:09:14',NULL),(40,4,2,5,'','2026-05-11 01:10:22',NULL);
/*!40000 ALTER TABLE `forum_reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `livestream_chats`
--

DROP TABLE IF EXISTS `livestream_chats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `livestream_chats` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `livestream_id` bigint DEFAULT NULL,
  `user_id` bigint DEFAULT NULL,
  `message` longtext NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_5E30ED23397B76AA` (`livestream_id`),
  KEY `IDX_5E30ED23A76ED395` (`user_id`),
  CONSTRAINT `FK_CHAT_LIVESTREAM` FOREIGN KEY (`livestream_id`) REFERENCES `livestreams` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_CHAT_USER` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `livestream_chats`
--

LOCK TABLES `livestream_chats` WRITE;
/*!40000 ALTER TABLE `livestream_chats` DISABLE KEYS */;
/*!40000 ALTER TABLE `livestream_chats` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `livestream_participants`
--

DROP TABLE IF EXISTS `livestream_participants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `livestream_participants` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `livestream_id` bigint DEFAULT NULL,
  `user_id` bigint DEFAULT NULL,
  `role` varchar(50) NOT NULL,
  `joined_at` datetime NOT NULL,
  `left_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_PARTICIPANT_SESSION` (`livestream_id`,`user_id`),
  KEY `IDX_4DC220A8397B76AA` (`livestream_id`),
  KEY `IDX_4DC220A8A76ED395` (`user_id`),
  CONSTRAINT `FK_PARTICIPANTS_LIVESTREAM` FOREIGN KEY (`livestream_id`) REFERENCES `livestreams` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_PARTICIPANTS_USER` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `livestream_participants`
--

LOCK TABLES `livestream_participants` WRITE;
/*!40000 ALTER TABLE `livestream_participants` DISABLE KEYS */;
/*!40000 ALTER TABLE `livestream_participants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `livestream_qa`
--

DROP TABLE IF EXISTS `livestream_qa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `livestream_qa` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `livestream_id` bigint DEFAULT NULL,
  `student_id` bigint DEFAULT NULL,
  `question` longtext NOT NULL,
  `answer` longtext,
  `answered_by` bigint DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `answered_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_CB0A1A7C397B76AA` (`livestream_id`),
  KEY `IDX_CB0A1A7CCB944F1A` (`student_id`),
  KEY `IDX_CB0A1A7C3948559F` (`answered_by`),
  CONSTRAINT `FK_QA_ANSWERED_BY` FOREIGN KEY (`answered_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `FK_QA_LIVESTREAM` FOREIGN KEY (`livestream_id`) REFERENCES `livestreams` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_QA_STUDENT` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `livestream_qa`
--

LOCK TABLES `livestream_qa` WRITE;
/*!40000 ALTER TABLE `livestream_qa` DISABLE KEYS */;
/*!40000 ALTER TABLE `livestream_qa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `livestreams`
--

DROP TABLE IF EXISTS `livestreams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `livestreams` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `teacher_id` bigint DEFAULT NULL,
  `class_id` int DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` longtext,
  `meeting_room` varchar(255) NOT NULL,
  `scheduled_at` datetime DEFAULT NULL,
  `started_at` datetime DEFAULT NULL,
  `ended_at` datetime DEFAULT NULL,
  `status` varchar(50) NOT NULL,
  `recording_url` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `engagement_summary` json DEFAULT NULL,
  `subject_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_959A2BAD9E6EA949` (`meeting_room`),
  KEY `IDX_959A2BAD41807E1D` (`teacher_id`),
  KEY `IDX_959A2BADEA000B10` (`class_id`),
  KEY `IDX_959A2BAD23EDC87` (`subject_id`),
  CONSTRAINT `FK_959A2BAD23EDC87` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_LIVESTREAMS_CLASS` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_LIVESTREAMS_TEACHER` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `livestreams`
--

LOCK TABLES `livestreams` WRITE;
/*!40000 ALTER TABLE `livestreams` DISABLE KEYS */;
INSERT INTO `livestreams` VALUES (1,28,2,'Introduction to Game Development','Introduction to Game Development Live Class','lw_6b9d81aa8c2bf646','2026-05-11 02:48:00',NULL,NULL,'SCHEDULED',NULL,'2026-05-11 01:48:43','2026-05-11 01:48:43',NULL,21);
/*!40000 ALTER TABLE `livestreams` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `message_reads`
--

DROP TABLE IF EXISTS `message_reads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `message_reads` (
  `message_id` bigint NOT NULL,
  `user_id` bigint NOT NULL,
  PRIMARY KEY (`user_id`,`message_id`),
  KEY `IDX_37E6935AA76ED395` (`user_id`),
  KEY `IDX_37E6935A537A1329` (`message_id`),
  CONSTRAINT `FK_37E6935A537A1329` FOREIGN KEY (`message_id`) REFERENCES `messages` (`id`),
  CONSTRAINT `FK_37E6935AA76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `message_reads`
--

LOCK TABLES `message_reads` WRITE;
/*!40000 ALTER TABLE `message_reads` DISABLE KEYS */;
/*!40000 ALTER TABLE `message_reads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `messages` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `conversation_id` bigint DEFAULT NULL,
  `sender_id` bigint DEFAULT NULL,
  `content` longtext NOT NULL,
  `is_deleted` tinyint DEFAULT NULL,
  `sent_at` datetime DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `delivered_at` timestamp NULL DEFAULT NULL,
  `seen_at` timestamp NULL DEFAULT NULL,
  `edited_at` datetime DEFAULT NULL,
  `is_forwarded` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `IDX_DB021E96F624B39D` (`sender_id`),
  KEY `IDX_DB021E969AC0396` (`conversation_id`),
  CONSTRAINT `FK_DB021E969AC0396` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`),
  CONSTRAINT `FK_DB021E96F624B39D` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messages`
--

LOCK TABLES `messages` WRITE;
/*!40000 ALTER TABLE `messages` DISABLE KEYS */;
INSERT INTO `messages` VALUES (1,1,2,'We welcome you to our Learnway academic year 2025-2026',NULL,'2026-05-11 01:14:34','SENT',NULL,NULL,NULL,0),(2,1,2,'make sure to let us know of any inconveniences',NULL,'2026-05-11 01:14:56','SENT',NULL,NULL,NULL,0);
/*!40000 ALTER TABLE `messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messenger_messages`
--

DROP TABLE IF EXISTS `messenger_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `messenger_messages` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `body` longtext NOT NULL,
  `headers` longtext NOT NULL,
  `queue_name` varchar(190) NOT NULL,
  `created_at` datetime NOT NULL,
  `available_at` datetime NOT NULL,
  `delivered_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750` (`queue_name`,`available_at`,`delivered_at`,`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messenger_messages`
--

LOCK TABLES `messenger_messages` WRITE;
/*!40000 ALTER TABLE `messenger_messages` DISABLE KEYS */;
INSERT INTO `messenger_messages` VALUES (1,'O:36:\\\"Symfony\\\\Component\\\\Messenger\\\\Envelope\\\":2:{s:44:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0stamps\\\";a:1:{s:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\";a:1:{i:0;O:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\":1:{s:55:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\0busName\\\";s:21:\\\"messenger.bus.default\\\";}}}s:45:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0message\\\";O:51:\\\"Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\\":2:{s:60:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0message\\\";O:28:\\\"Symfony\\\\Component\\\\Mime\\\\Email\\\":6:{i:0;N;i:1;N;i:2;s:112:\\\"<p>Hello Student1,</p><p>A new chapter has been added to your Mathematics class.</p><p>Title: Second Chapter</p>\\\";i:3;s:5:\\\"utf-8\\\";i:4;a:0:{}i:5;a:2:{i:0;O:37:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\\":2:{s:46:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0headers\\\";a:3:{s:4:\\\"from\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:4:\\\"From\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:20:\\\"noreply@learnway.app\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:0:\\\"\\\";}}}}s:2:\\\"to\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:2:\\\"To\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:21:\\\"student1@learnway.com\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:0:\\\"\\\";}}}}s:7:\\\"subject\\\";a:1:{i:0;O:48:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:7:\\\"Subject\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:55:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\0value\\\";s:33:\\\"New Chapter Added: Second Chapter\\\";}}}s:49:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0lineLength\\\";i:76;}i:1;N;}}s:61:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0envelope\\\";N;}}','[]','default','2026-04-26 22:06:13','2026-04-26 22:06:13',NULL),(2,'O:36:\\\"Symfony\\\\Component\\\\Messenger\\\\Envelope\\\":2:{s:44:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0stamps\\\";a:1:{s:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\";a:1:{i:0;O:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\":1:{s:55:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\0busName\\\";s:21:\\\"messenger.bus.default\\\";}}}s:45:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0message\\\";O:51:\\\"Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\\":2:{s:60:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0message\\\";O:28:\\\"Symfony\\\\Component\\\\Mime\\\\Email\\\":6:{i:0;N;i:1;N;i:2;s:112:\\\"<p>Hello Student2,</p><p>A new chapter has been added to your Mathematics class.</p><p>Title: Second Chapter</p>\\\";i:3;s:5:\\\"utf-8\\\";i:4;a:0:{}i:5;a:2:{i:0;O:37:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\\":2:{s:46:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0headers\\\";a:3:{s:4:\\\"from\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:4:\\\"From\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:20:\\\"noreply@learnway.app\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:0:\\\"\\\";}}}}s:2:\\\"to\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:2:\\\"To\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:21:\\\"student2@learnway.com\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:0:\\\"\\\";}}}}s:7:\\\"subject\\\";a:1:{i:0;O:48:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:7:\\\"Subject\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:55:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\0value\\\";s:33:\\\"New Chapter Added: Second Chapter\\\";}}}s:49:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0lineLength\\\";i:76;}i:1;N;}}s:61:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0envelope\\\";N;}}','[]','default','2026-04-26 22:06:13','2026-04-26 22:06:13',NULL);
/*!40000 ALTER TABLE `messenger_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `role_category` varchar(255) NOT NULL,
  `description` longtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'ADMIN','Administration','System Administrator'),(2,'TEACHER','Academic','Teacher'),(3,'STUDENT','Academic','Student');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_enrollments`
--

DROP TABLE IF EXISTS `student_enrollments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `student_enrollments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `class_id` int DEFAULT NULL,
  `user_id` bigint DEFAULT NULL,
  `academic_year_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_user_academic_year` (`user_id`,`academic_year_id`),
  KEY `IDX_1B38CC31A76ED395` (`user_id`),
  KEY `IDX_1B38CC31C54F3401` (`academic_year_id`),
  KEY `IDX_1B38CC31EA000B10` (`class_id`),
  CONSTRAINT `FK_1B38CC31A76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_1B38CC31C54F3401` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE CASCADE,
  CONSTRAINT `student_enrollments_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_enrollments`
--

LOCK TABLES `student_enrollments` WRITE;
/*!40000 ALTER TABLE `student_enrollments` DISABLE KEYS */;
INSERT INTO `student_enrollments` VALUES (1,2,8,1),(2,2,9,1),(3,2,10,1),(4,2,11,1),(5,2,12,1),(6,2,13,1),(7,2,14,1),(8,2,15,1),(9,2,16,1),(10,2,17,1),(11,2,18,1),(12,2,19,1),(13,2,20,1),(14,2,21,1),(15,2,22,1),(16,2,23,1),(17,2,24,1),(18,2,25,1),(19,2,26,1),(20,2,27,1),(21,2,29,1);
/*!40000 ALTER TABLE `student_enrollments` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `trg_student_enrollments_student_only_insert` BEFORE INSERT ON `student_enrollments` FOR EACH ROW BEGIN
                DECLARE v_role_name VARCHAR(255);
                SELECT UPPER(r.name) INTO v_role_name
                FROM users u
                LEFT JOIN roles r ON r.id = u.role_id
                WHERE u.id = NEW.user_id
                LIMIT 1;

                IF v_role_name IS NULL OR v_role_name <> 'STUDENT' THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Only users with STUDENT role can be enrolled';
                END IF;
            END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `trg_student_enrollments_student_only_update` BEFORE UPDATE ON `student_enrollments` FOR EACH ROW BEGIN
                DECLARE v_role_name VARCHAR(255);
                SELECT UPPER(r.name) INTO v_role_name
                FROM users u
                LEFT JOIN roles r ON r.id = u.role_id
                WHERE u.id = NEW.user_id
                LIMIT 1;

                IF v_role_name IS NULL OR v_role_name <> 'STUDENT' THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Only users with STUDENT role can be enrolled';
                END IF;
            END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `subjects`
--

DROP TABLE IF EXISTS `subjects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subjects` (
  `id` int NOT NULL AUTO_INCREMENT,
  `subject_code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` longtext,
  `is_active` tinyint DEFAULT NULL,
  `grade_level` varchar(255) DEFAULT NULL,
  `term_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_AB259917E2C35FC` (`term_id`),
  CONSTRAINT `FK_SUBJECTS_TERM` FOREIGN KEY (`term_id`) REFERENCES `terms` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subjects`
--

LOCK TABLES `subjects` WRITE;
/*!40000 ALTER TABLE `subjects` DISABLE KEYS */;
INSERT INTO `subjects` VALUES (1,'WEB3','Web Development','Frontend and Backend Development',1,'Grade 3',1),(2,'WEB3','Web Development','Frontend and Backend Development',1,'Grade 3',2),(3,'JAVA3','Java Programming','Object-Oriented Programming',1,'Grade 3',1),(4,'JAVA3','Java Programming','Object-Oriented Programming',1,'Grade 3',2),(5,'DB3','Database Systems','SQL and Data Modeling',1,'Grade 3',1),(6,'DB3','Database Systems','SQL and Data Modeling',1,'Grade 3',2),(7,'SPR3','Spring Framework','Enterprise Java Development',1,'Grade 3',1),(8,'SPR3','Spring Framework','Enterprise Java Development',1,'Grade 3',2),(9,'REACT3','Frontend Engineering','React and UI Development',1,'Grade 3',1),(10,'REACT3','Frontend Engineering','React and UI Development',1,'Grade 3',2),(11,'API3','API Development','RESTful Services',1,'Grade 3',1),(12,'API3','API Development','RESTful Services',1,'Grade 3',2),(13,'DEVOPS3','DevOps','CI/CD and Deployment',1,'Grade 3',1),(14,'DEVOPS3','DevOps','CI/CD and Deployment',1,'Grade 3',2),(15,'SEC3','Software Security','Secure Application Development',1,'Grade 3',1),(16,'SEC3','Software Security','Secure Application Development',1,'Grade 3',2),(17,'UML3','Software Modeling','UML and System Design',1,'Grade 3',1),(18,'UML3','Software Modeling','UML and System Design',1,'Grade 3',2),(19,'TEST3','Software Testing','Unit and Integration Testing',1,'Grade 3',1),(20,'TEST3','Software Testing','Unit and Integration Testing',1,'Grade 3',2),(21,'GAME3','Game Development','Interactive Game Design',1,'Grade 3',2);
/*!40000 ALTER TABLE `subjects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `submission_files`
--

DROP TABLE IF EXISTS `submission_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `submission_files` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `submission_id` bigint DEFAULT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` varchar(255) DEFAULT NULL,
  `file_size` int DEFAULT NULL,
  `uploaded_by` bigint DEFAULT NULL,
  `uploaded_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_DBAA3AFBE1FD4933` (`submission_id`),
  KEY `IDX_DBAA3AFBE3E73126` (`uploaded_by`),
  CONSTRAINT `FK_DBAA3AFBE1FD4933` FOREIGN KEY (`submission_id`) REFERENCES `submissions` (`id`),
  CONSTRAINT `FK_DBAA3AFBE3E73126` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `submission_files`
--

LOCK TABLES `submission_files` WRITE;
/*!40000 ALTER TABLE `submission_files` DISABLE KEYS */;
/*!40000 ALTER TABLE `submission_files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `submissions`
--

DROP TABLE IF EXISTS `submissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `submissions` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `assignment_id` bigint DEFAULT NULL,
  `student_id` bigint DEFAULT NULL,
  `submission_text` longtext,
  `submitted_at` timestamp NULL DEFAULT NULL,
  `is_late` tinyint DEFAULT NULL,
  `feedback` longtext,
  `reviewed_by` bigint DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_3F6169F7CB944F1A` (`student_id`),
  KEY `IDX_3F6169F785D7FB47` (`reviewed_by`),
  KEY `IDX_3F6169F7D19302F8` (`assignment_id`),
  CONSTRAINT `FK_3F6169F785D7FB47` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_3F6169F7CB944F1A` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_3F6169F7D19302F8` FOREIGN KEY (`assignment_id`) REFERENCES `assignments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `submissions`
--

LOCK TABLES `submissions` WRITE;
/*!40000 ALTER TABLE `submissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `submissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `teacher_assignments`
--

DROP TABLE IF EXISTS `teacher_assignments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `teacher_assignments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `teacher_id` bigint NOT NULL,
  `subject_id` int NOT NULL,
  `class_id` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_teacher_subject_class` (`teacher_id`,`subject_id`,`class_id`),
  KEY `IDX_E6D6EC9741807E1D` (`teacher_id`),
  KEY `IDX_E6D6EC9723EDC87` (`subject_id`),
  KEY `IDX_E6D6EC97EA000B10` (`class_id`),
  CONSTRAINT `FK_E6D6EC9741807E1D` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_ta_class` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_ta_subject` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `teacher_assignments`
--

LOCK TABLES `teacher_assignments` WRITE;
/*!40000 ALTER TABLE `teacher_assignments` DISABLE KEYS */;
INSERT INTO `teacher_assignments` VALUES (1,3,16,2),(2,3,18,2),(3,4,4,2),(4,4,10,2),(5,5,8,2),(6,5,14,2),(8,6,6,2),(7,6,12,2),(10,7,2,2),(9,7,20,2),(11,28,21,2);
/*!40000 ALTER TABLE `teacher_assignments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `terms`
--

DROP TABLE IF EXISTS `terms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `terms` (
  `id` int NOT NULL AUTO_INCREMENT,
  `academic_year_id` int DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `is_current` tinyint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_88A23F71C54F3401` (`academic_year_id`),
  CONSTRAINT `terms_ibfk_1` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `terms`
--

LOCK TABLES `terms` WRITE;
/*!40000 ALTER TABLE `terms` DISABLE KEYS */;
INSERT INTO `terms` VALUES (1,1,'Term 1','2026-09-01','2026-12-20',0),(2,1,'Term 2','2027-01-10','2027-05-20',1);
/*!40000 ALTER TABLE `terms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `time_slots`
--

DROP TABLE IF EXISTS `time_slots`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `time_slots` (
  `id` int NOT NULL AUTO_INCREMENT,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `type` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `time_slots`
--

LOCK TABLES `time_slots` WRITE;
/*!40000 ALTER TABLE `time_slots` DISABLE KEYS */;
INSERT INTO `time_slots` VALUES (1,'08:00:00','09:30:00','90MIN'),(2,'09:45:00','11:15:00','90MIN'),(3,'11:30:00','13:00:00','90MIN'),(4,'14:00:00','15:30:00','90MIN'),(5,'15:45:00','17:15:00','90MIN'),(6,'08:00:00','10:00:00','120MIN'),(7,'10:15:00','12:15:00','120MIN'),(8,'13:30:00','15:30:00','120MIN'),(9,'15:45:00','17:45:00','120MIN');
/*!40000 ALTER TABLE `time_slots` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `role_id` int DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `employee_id` varchar(255) DEFAULT NULL,
  `student_id` varchar(255) DEFAULT NULL,
  `is_active` tinyint DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_1483A5E9D60322AC` (`role_id`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,1,'admin@learnway.com','$2y$12$2Y/HJVJOs36trrY6Z/aJ6exXLvZDKnn6aWga/ku2e6vbF/kYReHXS','Admin','User',NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'2026-05-11 01:02:24'),(2,1,'zrafiadmin@gmail.com','$2y$12$xX5tPJSExgqPQO3gKJyq2ungYiMt4xGx9cYiIARED103ORfH8UAfi','Zrafi','Admin','Zrafi-portrait-6a0129d1a812f.png',NULL,NULL,'+21621260087',NULL,NULL,1,NULL,'2026-05-11 01:03:57'),(3,2,'soniateacher@learnway.com','$2y$12$pO6THH2Re.75EN7Tc5qW4e76UJ.rJzHH2y8gLyDcjt/TYZPbMw.zK','Sonia','Teacher',NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'2026-05-11 01:27:48'),(4,2,'abdelkarimteacher@learnway.com','$2y$12$pO6THH2Re.75EN7Tc5qW4e76UJ.rJzHH2y8gLyDcjt/TYZPbMw.zK','Abdelkarim','Teacher',NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'2026-05-11 01:27:48'),(5,2,'hibateacher@learnway.com','$2y$12$pO6THH2Re.75EN7Tc5qW4e76UJ.rJzHH2y8gLyDcjt/TYZPbMw.zK','Hiba','Teacher',NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'2026-05-11 01:27:48'),(6,2,'mbarkateacher@learnway.com','$2y$12$pO6THH2Re.75EN7Tc5qW4e76UJ.rJzHH2y8gLyDcjt/TYZPbMw.zK','Mbarka','Teacher',NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'2026-05-11 01:27:48'),(7,2,'emirteacher@learnway.com','$2y$12$pO6THH2Re.75EN7Tc5qW4e76UJ.rJzHH2y8gLyDcjt/TYZPbMw.zK','Emir','Teacher',NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'2026-05-11 01:27:48'),(8,3,'tariqstudent@gmail.com','$2y$12$xPcDOKUYl8QMJvNDJ3.CJeW1nc6dmL0UbD7XxM3K5qEBnw.vZzUK.','Tariq','Student',NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'2026-05-11 01:33:32'),(9,3,'fatimastudent@gmail.com','$2y$12$xPcDOKUYl8QMJvNDJ3.CJeW1nc6dmL0UbD7XxM3K5qEBnw.vZzUK.','Fatima','Student',NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'2026-05-11 01:33:32'),(10,3,'omarstudent@gmail.com','$2y$12$xPcDOKUYl8QMJvNDJ3.CJeW1nc6dmL0UbD7XxM3K5qEBnw.vZzUK.','Omar','Student',NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'2026-05-11 01:33:32'),(11,3,'youssefstudent@gmail.com','$2y$12$xPcDOKUYl8QMJvNDJ3.CJeW1nc6dmL0UbD7XxM3K5qEBnw.vZzUK.','Youssef','Student',NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'2026-05-11 01:33:32'),(12,3,'laylastudent@gmail.com','$2y$12$xPcDOKUYl8QMJvNDJ3.CJeW1nc6dmL0UbD7XxM3K5qEBnw.vZzUK.','Layla','Student',NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'2026-05-11 01:33:32'),(13,3,'alistudent@gmail.com','$2y$12$xPcDOKUYl8QMJvNDJ3.CJeW1nc6dmL0UbD7XxM3K5qEBnw.vZzUK.','Ali','Student',NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'2026-05-11 01:33:32'),(14,3,'hassanstudent@gmail.com','$2y$12$xPcDOKUYl8QMJvNDJ3.CJeW1nc6dmL0UbD7XxM3K5qEBnw.vZzUK.','Hassan','Student',NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'2026-05-11 01:33:32'),(15,3,'zainabstudent@gmail.com','$2y$12$xPcDOKUYl8QMJvNDJ3.CJeW1nc6dmL0UbD7XxM3K5qEBnw.vZzUK.','Zainab','Student',NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'2026-05-11 01:33:32'),(16,3,'khalidstudent@gmail.com','$2y$12$xPcDOKUYl8QMJvNDJ3.CJeW1nc6dmL0UbD7XxM3K5qEBnw.vZzUK.','Khalid','Student',NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'2026-05-11 01:33:32'),(17,3,'maryamstudent@gmail.com','$2y$12$xPcDOKUYl8QMJvNDJ3.CJeW1nc6dmL0UbD7XxM3K5qEBnw.vZzUK.','Maryam','Student',NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'2026-05-11 01:33:32'),(18,3,'ahmedstudent@gmail.com','$2y$12$xPcDOKUYl8QMJvNDJ3.CJeW1nc6dmL0UbD7XxM3K5qEBnw.vZzUK.','Ahmed','Student',NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'2026-05-11 01:33:32'),(19,3,'salmastudent@gmail.com','$2y$12$xPcDOKUYl8QMJvNDJ3.CJeW1nc6dmL0UbD7XxM3K5qEBnw.vZzUK.','Salma','Student',NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'2026-05-11 01:33:32'),(20,3,'bilalstudent@gmail.com','$2y$12$xPcDOKUYl8QMJvNDJ3.CJeW1nc6dmL0UbD7XxM3K5qEBnw.vZzUK.','Bilal','Student',NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'2026-05-11 01:33:32'),(21,3,'nourastudent@gmail.com','$2y$12$xPcDOKUYl8QMJvNDJ3.CJeW1nc6dmL0UbD7XxM3K5qEBnw.vZzUK.','Noura','Student',NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'2026-05-11 01:33:32'),(22,3,'samirstudent@gmail.com','$2y$12$xPcDOKUYl8QMJvNDJ3.CJeW1nc6dmL0UbD7XxM3K5qEBnw.vZzUK.','Samir','Student',NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'2026-05-11 01:33:32'),(23,3,'raniastudent@gmail.com','$2y$12$xPcDOKUYl8QMJvNDJ3.CJeW1nc6dmL0UbD7XxM3K5qEBnw.vZzUK.','Rania','Student',NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'2026-05-11 01:33:32'),(24,3,'karimstudent@gmail.com','$2y$12$xPcDOKUYl8QMJvNDJ3.CJeW1nc6dmL0UbD7XxM3K5qEBnw.vZzUK.','Karim','Student',NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'2026-05-11 01:33:32'),(25,3,'aminastudent@gmail.com','$2y$12$xPcDOKUYl8QMJvNDJ3.CJeW1nc6dmL0UbD7XxM3K5qEBnw.vZzUK.','Amina','Student',NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'2026-05-11 01:33:32'),(26,3,'zaydstudent@gmail.com','$2y$12$xPcDOKUYl8QMJvNDJ3.CJeW1nc6dmL0UbD7XxM3K5qEBnw.vZzUK.','Zayd','Student',NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'2026-05-11 01:33:32'),(27,3,'safiyastudent@gmail.com','$2y$12$xPcDOKUYl8QMJvNDJ3.CJeW1nc6dmL0UbD7XxM3K5qEBnw.vZzUK.','Safiya','Student',NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'2026-05-11 01:33:32'),(28,2,'zrafiteacher@gmail.com','$2y$13$MOae/.0tdKz37z5YQgRXs.Vi5BTiaKAeqcKO7EE3U/Xoe4hB67r2m','Zrafi','teacher','z2-6a0132c79451a.jpg',NULL,NULL,'',NULL,NULL,1,NULL,'2026-05-11 01:27:36'),(29,3,'zrafistudent@gmail.com','$2y$13$sj5WrS6lnZgOpwfgG7iad.c7WX2Y6ljfc96rYYDvQ33q/SdJKq.96','Zrafi','student',NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'2026-05-11 01:28:15');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-11  8:10:03
