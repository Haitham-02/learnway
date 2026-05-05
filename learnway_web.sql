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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `academic_years`
--

LOCK TABLES `academic_years` WRITE;
/*!40000 ALTER TABLE `academic_years` DISABLE KEYS */;
INSERT INTO `academic_years` VALUES (6,'2025-2026','2025-09-15','2026-05-31',0,NULL,'2026-04-26 12:18:18'),(7,'2026/2027 Academic Session','2026-08-20','2027-06-30',0,NULL,'2026-04-27 15:57:30'),(8,'2027-2028','2027-09-15','2028-06-01',1,NULL,'2026-05-04 21:22:09');
/*!40000 ALTER TABLE `academic_years` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `announcements`
--

LOCK TABLES `announcements` WRITE;
/*!40000 ALTER TABLE `announcements` DISABLE KEYS */;
INSERT INTO `announcements` VALUES (1,12,'Welcome to LearnWay','This is a global announcement.','CLASS',21,'NORMAL',0,'2026-04-27 16:58:00',NULL,NULL),(2,12,'Test Grade Announcement','This is a test for grade level target visibility and CRUD.','CLASS',19,'HIGH',0,'2026-04-27 17:05:00',NULL,NULL),(3,12,'Welcome to our new school year!','We are excited to have you all back. Let\'s make this year great!','SCHOOL',NULL,'NORMAL',0,'2026-04-27 17:09:00',NULL,NULL),(4,12,'Test Grade Announcement','Grades for the recent test have been posted.','CLASS',19,'HIGH',0,'2026-04-27 17:11:00',NULL,NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assignments`
--

LOCK TABLES `assignments` WRITE;
/*!40000 ALTER TABLE `assignments` DISABLE KEYS */;
INSERT INTO `assignments` VALUES (1,2,'Quiz #1','Banana','2026-04-27 23:59:00','TEXT',0,'DRAFT','2026-04-26 17:36:48','2026-04-26 17:36:48','quiz'),(2,4,'Quiz #1','science quiz','2026-04-28 22:57:00','TEXT',0,'DRAFT','2026-04-26 21:57:35','2026-04-26 21:57:35','quiz'),(3,7,'Quiz #1',NULL,'2026-04-29 23:15:00','TEXT',0,'PUBLISHED','2026-04-26 22:15:17','2026-04-26 22:15:17','quiz');
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
INSERT INTO `chapter_contents` VALUES (1,'Summary','Blablablabla',13,'2026-04-26 16:54:55','2026-04-26 16:54:55',2);
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chapter_files`
--

LOCK TABLES `chapter_files` WRITE;
/*!40000 ALTER TABLE `chapter_files` DISABLE KEYS */;
INSERT INTO `chapter_files` VALUES (1,'Atelier patrons de conception.docx','/uploads/chapters/2/Atelier-patrons-de-conception-69ee4a70844df.docx','application/vnd.openxmlformats-officedocument.wordprocessingml.document',8096,13,'2026-04-26 17:25:04',2),(2,'Atelier patrons de conception.pdf','/uploads/chapters/2/Atelier-patrons-de-conception-69ee4a7b73f9d.pdf','application/pdf',170318,13,'2026-04-26 17:25:15',2);
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chapters`
--

LOCK TABLES `chapters` WRITE;
/*!40000 ALTER TABLE `chapters` DISABLE KEYS */;
INSERT INTO `chapters` VALUES (2,'Introduction to Algebra','An introductory chapter to basic algebra concepts.',1,1,'2026-04-26 14:46:17','2026-04-26 15:04:07',19,4),(3,'First Chapter','First Chapter Of Algebra',2,1,'2026-04-26 15:04:26','2026-04-26 15:04:29',19,4),(4,'Introduction to Science','Science',1,1,'2026-04-26 21:57:12','2026-04-26 22:14:48',19,5),(6,'Second Chapter','2222',3,1,'2026-04-26 22:11:04','2026-04-26 22:11:12',19,4),(7,'First Chapter','11111 science',1,1,'2026-04-26 22:14:31','2026-04-26 22:14:39',19,5),(8,'Second Chapter','2222',2,1,'2026-04-26 22:15:56','2026-04-26 22:15:56',19,5);
/*!40000 ALTER TABLE `chapters` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `classes`
--

LOCK TABLES `classes` WRITE;
/*!40000 ALTER TABLE `classes` DISABLE KEYS */;
INSERT INTO `classes` VALUES (19,'7A','Grade 7','A',1,'2026-04-26 12:18:18'),(20,'7B','Grade 7','B',1,'2026-04-26 12:18:18'),(21,'Grade 10A','Grade 10','A',1,'2026-04-27 15:57:30'),(22,'Grade 10B','Grade 10','B',1,'2026-04-27 15:57:30'),(23,'Grade 11A','Grade 11','A',1,'2026-04-27 15:57:30'),(24,'Grade 12A','Grade 12','A',1,'2026-04-27 15:57:30'),(25,'3A1','Grade 3','A',1,'2026-05-04 21:22:09'),(26,'3A2','Grade 3','A',1,'2026-05-04 21:22:09'),(27,'3A3','Grade 3','A',1,'2026-05-04 21:22:09'),(28,'2A1','Grade 2','A',1,'2026-05-04 21:22:09'),(29,'2B1','Grade 2','B',1,'2026-05-04 21:22:09'),(30,'2B2','Grade 2','B',1,'2026-05-04 21:22:09');
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
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `conversation_members`
--

LOCK TABLES `conversation_members` WRITE;
/*!40000 ALTER TABLE `conversation_members` DISABLE KEYS */;
INSERT INTO `conversation_members` VALUES (86,32,14,NULL,'2026-04-28 08:31:42',NULL),(87,32,12,NULL,'2026-04-28 08:31:42',NULL),(88,33,14,NULL,'2026-04-30 14:40:35',NULL),(89,33,13,NULL,'2026-04-30 14:40:35',NULL),(90,34,16,NULL,'2026-05-04 21:25:07',NULL),(91,34,12,NULL,'2026-05-04 21:25:07',NULL),(92,35,18,NULL,'2026-05-04 21:27:54',NULL),(93,35,12,NULL,'2026-05-04 21:27:54',NULL),(94,36,18,NULL,'2026-05-04 21:28:06',NULL),(95,36,16,NULL,'2026-05-04 21:28:06',NULL),(96,37,17,NULL,'2026-05-04 21:38:44',NULL),(97,37,16,NULL,'2026-05-04 21:38:44',NULL),(98,38,17,NULL,'2026-05-04 22:31:21',NULL),(99,38,12,NULL,'2026-05-04 22:31:21',NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `conversations`
--

LOCK TABLES `conversations` WRITE;
/*!40000 ALTER TABLE `conversations` DISABLE KEYS */;
INSERT INTO `conversations` VALUES (32,'DIRECT',NULL,'0cfc030daea3570f4a4b110c1ea5bdb0',NULL,'2026-04-28 08:31:42'),(33,'DIRECT',NULL,'de91e90a15ee4ac6b67dd4588153263b',NULL,'2026-04-30 14:40:35'),(34,'DIRECT',NULL,'6b8b4a871986a0b7ed9031e828933a03',NULL,'2026-05-04 21:25:07'),(35,'DIRECT',NULL,'efb11e4218021913758b53a52abd157c',NULL,'2026-05-04 21:27:54'),(36,'DIRECT',NULL,'72805c0a53d0f44b75f2fbc28100ab2e',NULL,'2026-05-04 21:28:06'),(37,'DIRECT',NULL,'15731e513557734e026a504b0d73e204',NULL,'2026-05-04 21:38:44'),(38,'DIRECT',NULL,'bf8e69b4a494e3f0377d80bea06d41cf',NULL,'2026-05-04 22:31:21');
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
INSERT INTO `doctrine_migration_versions` VALUES ('DoctrineMigrations\\Version20260425111645','2026-04-26 10:32:40',1),('DoctrineMigrations\\Version20260425175630','2026-04-26 10:32:40',63),('DoctrineMigrations\\Version20260425180120','2026-04-26 10:32:40',8),('DoctrineMigrations\\Version20260426110000','2026-04-26 11:01:10',167),('DoctrineMigrations\\Version20260426150000','2026-04-26 11:33:22',155);
/*!40000 ALTER TABLE `doctrine_migration_versions` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forum_comments`
--

LOCK TABLES `forum_comments` WRITE;
/*!40000 ALTER TABLE `forum_comments` DISABLE KEYS */;
INSERT INTO `forum_comments` VALUES (43,9,NULL,12,'gg','2026-04-28 08:16:10',NULL,'APPROVED'),(44,9,NULL,16,'gg','2026-05-04 21:25:00',NULL,'APPROVED');
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forum_posts`
--

LOCK TABLES `forum_posts` WRITE;
/*!40000 ALTER TABLE `forum_posts` DISABLE KEYS */;
INSERT INTO `forum_posts` VALUES (9,NULL,'Test Forum',NULL,NULL,'TestTestTestTestTest',NULL,13,'2026-04-28 08:12:19','APPROVED'),(10,NULL,'test bad words',NULL,NULL,'****',NULL,13,'2026-04-28 11:08:27','REJECTED');
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
  UNIQUE KEY `UNIQ_98BCA67E4B89032C` (`post_id`),
  UNIQUE KEY `UNIQ_98BCA67ECB944F1A` (`student_id`),
  CONSTRAINT `FK_98BCA67E4B89032C` FOREIGN KEY (`post_id`) REFERENCES `forum_posts` (`id`),
  CONSTRAINT `FK_98BCA67ECB944F1A` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`),
  CONSTRAINT `forum_reviews_chk_1` CHECK ((`rating` between 1 and 5))
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forum_reviews`
--

LOCK TABLES `forum_reviews` WRITE;
/*!40000 ALTER TABLE `forum_reviews` DISABLE KEYS */;
/*!40000 ALTER TABLE `forum_reviews` ENABLE KEYS */;
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
  PRIMARY KEY (`id`),
  KEY `IDX_DB021E96F624B39D` (`sender_id`),
  KEY `IDX_DB021E969AC0396` (`conversation_id`),
  CONSTRAINT `FK_DB021E969AC0396` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`),
  CONSTRAINT `FK_DB021E96F624B39D` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=111 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messages`
--

LOCK TABLES `messages` WRITE;
/*!40000 ALTER TABLE `messages` DISABLE KEYS */;
INSERT INTO `messages` VALUES (64,32,14,'yo',NULL,'2026-04-28 08:31:47','SENT',NULL,NULL),(65,32,12,'yo',NULL,'2026-04-28 08:32:22','SENT',NULL,NULL),(66,32,14,'test',NULL,'2026-04-28 08:42:19','SENT',NULL,NULL),(67,32,12,'test',NULL,'2026-04-28 08:43:19','SENT',NULL,NULL),(68,32,12,'123',NULL,'2026-04-28 09:01:21','SENT',NULL,NULL),(69,32,14,'hi',NULL,'2026-04-28 09:04:04','SENT',NULL,NULL),(70,32,12,'hello',NULL,'2026-04-28 09:04:07','SENT',NULL,NULL),(71,32,12,'zzz',NULL,'2026-04-28 09:05:33','SENT',NULL,NULL),(72,32,14,'test',NULL,'2026-04-28 12:53:18','SENT',NULL,NULL),(73,32,12,'tttt',NULL,'2026-04-28 12:53:25','SENT',NULL,NULL),(74,32,14,'ggg',NULL,'2026-04-28 12:53:27','SENT',NULL,NULL),(75,32,12,'ff',NULL,'2026-04-28 12:53:35','SENT',NULL,NULL),(76,32,14,'aea',NULL,'2026-04-28 12:53:41','SENT',NULL,NULL),(77,32,14,'test',NULL,'2026-04-28 12:56:45','SENT',NULL,NULL),(78,32,12,'test',NULL,'2026-04-28 12:56:52','SENT',NULL,NULL),(79,32,14,'tt',NULL,'2026-04-28 12:56:54','SENT',NULL,NULL),(80,32,12,'gg',NULL,'2026-04-28 12:57:04','SENT',NULL,NULL),(81,32,14,'gg',NULL,'2026-04-28 12:57:09','SENT',NULL,NULL),(82,32,12,'gg',NULL,'2026-04-28 12:57:26','SENT',NULL,NULL),(83,32,14,'gg',NULL,'2026-04-28 12:57:28','SENT',NULL,NULL),(84,32,14,'ggf',NULL,'2026-04-28 12:57:36','SENT',NULL,NULL),(85,32,14,'jj',NULL,'2026-04-28 12:57:50','SENT',NULL,NULL),(86,32,12,'gg',NULL,'2026-04-28 12:57:53','SENT',NULL,NULL),(87,32,14,'test',NULL,'2026-04-28 12:57:55','SENT',NULL,NULL),(88,33,14,'hello teacher',NULL,'2026-04-30 14:43:12','SENT',NULL,NULL),(89,38,17,'aaaa',NULL,'2026-05-04 22:37:26','SENT',NULL,NULL),(90,38,17,'aaaaaaaaaaaaaaaa',NULL,'2026-05-04 22:37:30','SENT',NULL,NULL),(91,38,17,'aaaaaaaaaaaaaaaaaaaaa',NULL,'2026-05-04 22:37:36','SENT',NULL,NULL),(92,38,17,'SELEM',NULL,'2026-05-04 22:38:51','SENT',NULL,NULL),(93,38,17,'AAAA',NULL,'2026-05-04 22:38:57','SENT',NULL,NULL),(94,38,17,'selemu alaykum',NULL,'2026-05-04 22:39:05','SENT',NULL,NULL),(95,38,17,'aaa',NULL,'2026-05-04 22:46:35','SENT',NULL,NULL),(96,37,17,'aya wink ay',NULL,'2026-05-04 22:46:45','SENT',NULL,NULL),(97,37,17,'test',NULL,'2026-05-04 22:46:50','SENT',NULL,NULL),(98,37,17,'test 2',NULL,'2026-05-04 22:46:55','SENT',NULL,NULL),(99,37,17,'test 1000',NULL,'2026-05-04 22:47:00','SENT',NULL,NULL),(100,37,17,'ok',NULL,'2026-05-04 22:49:46','SENT',NULL,NULL),(101,38,17,'aaa',NULL,'2026-05-04 22:49:53','SENT',NULL,NULL),(102,37,17,'test debug',NULL,'2026-05-04 22:51:12','SENT',NULL,NULL),(103,37,17,'debug',NULL,'2026-05-04 22:53:08','SENT',NULL,NULL),(104,37,17,'test',NULL,'2026-05-04 22:53:19','SENT',NULL,NULL),(105,37,17,'hello',NULL,'2026-05-04 22:53:53','SENT',NULL,NULL),(106,37,17,'hello',NULL,'2026-05-04 22:54:10','SENT',NULL,NULL),(107,37,17,'hi',NULL,'2026-05-04 22:54:16','SENT',NULL,NULL),(108,37,17,'hello',NULL,'2026-05-04 22:54:21','SENT',NULL,NULL),(109,37,17,'test',NULL,'2026-05-04 22:54:23','SENT',NULL,NULL),(110,37,17,'annn',NULL,'2026-05-04 22:54:27','SENT',NULL,NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=6241 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'ADMIN','ADMIN',NULL),(2,'TEACHER','TEACHER',NULL),(3,'STUDENT','STUDENT',NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_enrollments`
--

LOCK TABLES `student_enrollments` WRITE;
/*!40000 ALTER TABLE `student_enrollments` DISABLE KEYS */;
INSERT INTO `student_enrollments` VALUES (14,19,14,6),(15,21,15,6);
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
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subjects`
--

LOCK TABLES `subjects` WRITE;
/*!40000 ALTER TABLE `subjects` DISABLE KEYS */;
INSERT INTO `subjects` VALUES (4,'MATH7','Mathematics','Grade 7 math',1,'Grade 7',10),(5,'SCI7','Science','Grade 7 science',1,'Grade 7',9),(6,'PHY10','Physics','Mechanics & Heat',1,'Grade 10',11),(7,'PHY10','Physics','Mechanics & Heat',1,'Grade 10',12),(8,'PHY10','Physics','Mechanics & Heat',1,'Grade 10',13),(9,'CHEM10','Chemistry','Basic Inorganic',1,'Grade 10',11),(10,'CHEM10','Chemistry','Basic Inorganic',1,'Grade 10',12),(11,'CHEM10','Chemistry','Basic Inorganic',1,'Grade 10',13),(12,'MATH11','Mathematics','Calculus I',1,'Grade 11',11),(13,'MATH11','Mathematics','Calculus I',1,'Grade 11',12),(14,'MATH11','Mathematics','Calculus I',1,'Grade 11',13),(15,'ENG12','English Literature','Shakespearean Drama',1,'Grade 12',11),(16,'ENG12','English Literature','Shakespearean Drama',1,'Grade 12',12),(17,'ENG12','English Literature','Shakespearean Drama',1,'Grade 12',13),(18,'MATH3','Mathematics','Essential Math',1,'Grade 3',14),(19,'MATH3','Mathematics','Essential Math',1,'Grade 3',15),(20,'SCI3','Science','Natural Sciences',1,'Grade 3',14),(21,'SCI3','Science','Natural Sciences',1,'Grade 3',15),(22,'INF3','Informatics','Symfony',1,'Grade 3',14),(23,'INF3','Informatics','Symfony',1,'Grade 3',15),(24,'MATH2','Mathematics','Advanced Algebra',1,'Grade 2',14),(25,'MATH2','Mathematics','Advanced Algebra',1,'Grade 2',15),(26,'SCI2','Science','Natural Sciences',1,'Grade 2',14),(27,'SCI2','Science','Natural Sciences',1,'Grade 2',15),(28,'INF2','Informatics','JavaFX',1,'Grade 2',14),(29,'INF2','Informatics','JavaFX',1,'Grade 2',15);
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `submissions`
--

LOCK TABLES `submissions` WRITE;
/*!40000 ALTER TABLE `submissions` DISABLE KEYS */;
INSERT INTO `submissions` VALUES (1,1,14,NULL,'2026-04-26 16:40:35',NULL,'Good',13,'2026-04-26 20:56:53','graded','2026-04-26 17:40:35',NULL),(2,2,14,'All correct 100/100','2026-04-26 20:58:05',NULL,'100/100',13,'2026-04-26 20:58:49','graded','2026-04-26 21:58:05',NULL),(3,3,14,'gg','2026-04-28 07:21:31',NULL,NULL,NULL,NULL,'submitted','2026-04-28 08:21:31',NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `teacher_assignments`
--

LOCK TABLES `teacher_assignments` WRITE;
/*!40000 ALTER TABLE `teacher_assignments` DISABLE KEYS */;
INSERT INTO `teacher_assignments` VALUES (1,13,4,19),(3,13,4,20),(2,13,5,19),(5,13,6,21),(6,13,9,21),(4,13,12,21),(7,13,15,21);
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
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `terms`
--

LOCK TABLES `terms` WRITE;
/*!40000 ALTER TABLE `terms` DISABLE KEYS */;
INSERT INTO `terms` VALUES (9,6,'Term 1','2025-09-15','2026-01-31',0),(10,6,'Term 2','2026-02-01','2026-05-31',1),(11,7,'Autumn Term','2026-08-20','2026-12-18',1),(12,7,'Spring Term','2027-01-05','2027-03-26',0),(13,7,'Summer Term','2027-04-12','2027-06-30',0),(14,8,'Term 1','2026-09-01','2026-12-20',1),(15,8,'Term 2','2027-01-10','2027-05-20',0);
/*!40000 ALTER TABLE `terms` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (12,1,'admin@learnway.com','$2y$13$HVs7ee5HSsOibKVi0hOAfeqmqHGakeXXqx.538mnTJVu.OycdtR/W','Test','Admin',NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'2026-03-31 20:06:09'),(13,2,'haitham@learnway.com','$2y$13$eotvpvZ1jd5OapSrVvBWH.7HuVictJCakaUMXcV6b0Z9Zh7kbmXLK','Haitham','Harzallah',NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'2026-04-26 10:43:51'),(14,3,'student1@learnway.com','$2y$13$c9tANac5d/bdgmHVobcTB.HKUeF28kquHK31mGWKPTSm0zpHfgYn2','Student1','Test',NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'2026-04-26 10:44:21'),(15,3,'student2@learnway.com','$2y$13$vM1DHmktFY6ZFmJoc7Af7u5GREMjXVIDTB5HHyN0ADSnXQvonXmVq','Student2','Test',NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'2026-04-26 10:44:37'),(16,1,'zrafiabdeslem@gmail.com','$2y$13$Grft3uUSaNZY5O.Kzuyk3O215pcxau.tU0jeruvMwYPn88A3L.A.K','Zrafi','Abdeslem',NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'2026-05-04 21:12:27'),(17,3,'zrafistudent@gmail.com','$2y$13$hFEQY6QvuO.jmoKyL/Fy6ufD4uC8.2hqmfIubUUY1DkNUaURDksDi','Zrafi','Student',NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'2026-05-04 21:26:40'),(18,2,'zrafiteacher@gmail.com','$2y$13$p2u1/k8Uc5BQELJMt60HnOju6LxykYq9DLk0FVu2FSqB1okhy/C4G','Zrafi','teacher',NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'2026-05-04 21:27:11');
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

-- Dump completed on 2026-05-05 21:14:57
