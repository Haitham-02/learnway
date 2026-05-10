SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET NAMES utf8mb4;

DROP TABLE IF EXISTS academic_years;

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

INSERT INTO academic_years VALUES("6","2025-2026","2025-09-15","2026-05-31","0","","2026-04-26 12:18:18");
INSERT INTO academic_years VALUES("7","2026/2027 Academic Session","2026-08-20","2027-06-30","0","","2026-04-27 15:57:30");
INSERT INTO academic_years VALUES("8","2027-2028","2027-09-15","2028-06-01","1","","2026-05-04 21:22:09");



DROP TABLE IF EXISTS ai_chats;

CREATE TABLE `ai_chats` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  `user_id` bigint NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_71DF234AA76ED395` (`user_id`),
  CONSTRAINT `FK_71DF234AA76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO ai_chats VALUES("1","what class i am in ?...","2026-05-10 00:09:59","17");
INSERT INTO ai_chats VALUES("2","what class iam in...","2026-05-10 00:11:17","17");
INSERT INTO ai_chats VALUES("3","WHAT CLASS I AM IN ,...","2026-05-10 00:13:36","17");
INSERT INTO ai_chats VALUES("4","what class i am in ?...","2026-05-10 00:15:04","17");
INSERT INTO ai_chats VALUES("5","hi...","2026-05-10 01:42:57","17");
INSERT INTO ai_chats VALUES("6","hi...","2026-05-10 01:44:12","17");
INSERT INTO ai_chats VALUES("7","HI...","2026-05-10 01:45:16","17");
INSERT INTO ai_chats VALUES("8","What are my current lessons?...","2026-05-10 01:51:34","17");
INSERT INTO ai_chats VALUES("9","Any announcements for me?...","2026-05-10 01:54:31","17");
INSERT INTO ai_chats VALUES("10","what is my class...","2026-05-10 02:01:48","17");
INSERT INTO ai_chats VALUES("11","any forum posts in community f...","2026-05-10 02:06:33","17");
INSERT INTO ai_chats VALUES("12","can you tell me about the cont...","2026-05-10 02:10:22","17");
INSERT INTO ai_chats VALUES("13","What are my current lessons?...","2026-05-10 02:19:57","17");
INSERT INTO ai_chats VALUES("14","any forum post ?...","2026-05-10 02:23:52","17");
INSERT INTO ai_chats VALUES("15","How many active students do we...","2026-05-10 03:22:56","16");
INSERT INTO ai_chats VALUES("16","Are there any forum posts pend...","2026-05-10 03:23:31","16");
INSERT INTO ai_chats VALUES("17","Who are the most active teache...","2026-05-10 03:46:51","16");



DROP TABLE IF EXISTS ai_knowledge_base;

CREATE TABLE `ai_knowledge_base` (
  `id` int NOT NULL AUTO_INCREMENT,
  `source_type` varchar(50) NOT NULL,
  `source_id` bigint NOT NULL,
  `vector_id` varchar(255) NOT NULL,
  `indexed_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;




DROP TABLE IF EXISTS ai_messages;

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
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO ai_messages VALUES("1","user","WHAT CLASS I AM IN ,",NULL,"2026-05-10 00:13:42","3");
INSERT INTO ai_messages VALUES("2","assistant","Based on the provided context, you have **26 authorized classes**, but the specific names of these classes are not listed. Therefore, I do not know the exact names of the classes you are in.",NULL,"2026-05-10 00:13:42","3");
INSERT INTO ai_messages VALUES("3","user","what class i am in ?",NULL,"2026-05-10 00:15:06","4");
INSERT INTO ai_messages VALUES("4","assistant","You are in class 3A2.",NULL,"2026-05-10 00:15:06","4");
INSERT INTO ai_messages VALUES("5","user","what is the latest live stream published",NULL,"2026-05-10 00:15:22","4");
INSERT INTO ai_messages VALUES("6","assistant","I don\'t know. The provided context does not contain information about live streams.",NULL,"2026-05-10 00:15:22","4");
INSERT INTO ai_messages VALUES("7","user","what are the subjects i have in my class ?",NULL,"2026-05-10 00:17:09","4");
INSERT INTO ai_messages VALUES("8","assistant","Based on the provided context for your class **3A2**, the subject mentioned is:\n\n*   **Math Algebra** (identified from the \"Math Algebra Livestream for 3A2\").",NULL,"2026-05-10 00:17:09","4");
INSERT INTO ai_messages VALUES("9","user","any other subjects ?",NULL,"2026-05-10 00:17:33","4");
INSERT INTO ai_messages VALUES("10","assistant","Based on the provided context, the only specific subject mentioned is **Math Algebra**, which is associated with a livestream for class 3A2. There are no other subjects listed in your authorized classes or scheduled livestreams.",NULL,"2026-05-10 00:17:33","4");
INSERT INTO ai_messages VALUES("11","user","HI",NULL,"2026-05-10 01:45:19","7");
INSERT INTO ai_messages VALUES("12","assistant","Hello Zrafi Student! I am Learnway AI, your intelligent educational assistant. How can I help you with your studies in class 3A2 today?\n\nI see that there are several active livestreams for your class, including a **Math Algebra Livestream**. Let me know if you have questions about your classes or any of the recent messages!",NULL,"2026-05-10 01:45:19","7");
INSERT INTO ai_messages VALUES("13","user","What are my current lessons?",NULL,"2026-05-10 01:51:37","8");
INSERT INTO ai_messages VALUES("14","assistant","Based on the provided context, there are no specific lessons or subjects listed for your class (3A2).",NULL,"2026-05-10 01:51:37","8");
INSERT INTO ai_messages VALUES("15","user","Any announcements for me?",NULL,"2026-05-10 01:54:35","9");
INSERT INTO ai_messages VALUES("16","assistant","There are currently no formal announcements listed for you. However, you have three active livestreams for your class **3A2**:\n\n*   **Math Algebra Livestream for 3A2** (Started: 2026-05-05)\n*   **test** (Started: 2026-05-06)\n*   **test jitsi** (Started: 2026-05-09)",NULL,"2026-05-10 01:54:35","9");
INSERT INTO ai_messages VALUES("17","user","any resume",NULL,"2026-05-10 01:55:13","9");
INSERT INTO ai_messages VALUES("18","assistant","I\'m sorry, but I don\'t have any summaries or resumes available in the current context. Your \"Lessons & Subjects\" and \"Announcements\" sections are currently empty. \n\nHowever, I can see that there is a **Math Algebra Livestream** for your class (3A2) currently marked as LIVE. You may want to check that for current lesson content!",NULL,"2026-05-10 01:55:13","9");
INSERT INTO ai_messages VALUES("19","user","what is my class",NULL,"2026-05-10 02:01:51","10");
INSERT INTO ai_messages VALUES("20","assistant","Based on your dashboard data, your authorized class is **3A2**.",NULL,"2026-05-10 02:01:51","10");
INSERT INTO ai_messages VALUES("21","user","what is my name",NULL,"2026-05-10 02:02:00","10");
INSERT INTO ai_messages VALUES("22","assistant","Your name is Zrafi Student.",NULL,"2026-05-10 02:02:00","10");
INSERT INTO ai_messages VALUES("23","user","what is my latests messages",NULL,"2026-05-10 02:05:39","10");
INSERT INTO ai_messages VALUES("24","assistant","Based on your recent message history, here are your latest messages:\n\n*   **From Zrafi:** \"aqsq\"\n*   **From Zrafi:** \"aa\"\n*   **From You:** \"annn\"\n*   **From You:** \"test\"\n*   **From You:** \"hello\"",NULL,"2026-05-10 02:05:39","10");
INSERT INTO ai_messages VALUES("25","user","whats the lasst forum post and what is it about",NULL,"2026-05-10 02:06:03","10");
INSERT INTO ai_messages VALUES("26","assistant","I don\'t know, as there are no forum posts listed in your dashboard data at the moment.",NULL,"2026-05-10 02:06:03","10");
INSERT INTO ai_messages VALUES("27","user","any forum posts in community forum ?",NULL,"2026-05-10 02:06:36","11");
INSERT INTO ai_messages VALUES("28","assistant","Based on the information available in your dashboard, there are currently no forum posts in the community forum.",NULL,"2026-05-10 02:06:36","11");
INSERT INTO ai_messages VALUES("29","user","can u summerize a file ?",NULL,"2026-05-10 02:07:35","11");
INSERT INTO ai_messages VALUES("30","assistant","Hello Zrafi Student!\n\nYes, I am capable of summarizing files and documents that have been uploaded to the Learnway platform and are part of your authorized class materials (Class 3A2).\n\nHowever, based on the current context provided, there are no specific documents or PDF contents listed for me to summarize right now. If you have a specific lesson or document in mind, please let me know the name of the subject or chapter, and I will check the available materials to provide a summary for you!",NULL,"2026-05-10 02:07:35","11");
INSERT INTO ai_messages VALUES("31","user","uploads ?",NULL,"2026-05-10 02:07:55","11");
INSERT INTO ai_messages VALUES("32","assistant","Based on the information available in your dashboard, there are no records of uploaded documents, PDFs, or files at this time.",NULL,"2026-05-10 02:07:55","11");
INSERT INTO ai_messages VALUES("33","user","summerize ?",NULL,"2026-05-10 02:08:18","11");
INSERT INTO ai_messages VALUES("34","assistant","Hello Zrafi Student! Based on your Learnway dashboard for class **3A2**, here is a summary of your current activity:\n\n*   **Livestreams:** There are currently three active sessions marked as **LIVE**:\n    *   **Math Algebra Livestream for 3A2** (Started: 2026-05-05)\n    *   **test** (Started: 2026-05-06)\n    *   **test jitsi** (Started: 2026-05-09)\n    *   Additionally, one \"test livestream\" has already ended.\n*   **Messages:** There is a history of short recent messages between you and the system, including greetings like \"hello\" and \"test.\"\n*   **Lessons & Assignments:** There are currently no specific lessons, subjects, or upcoming assignments listed in your profile.\n*   **Announcements & Forums:** No recent announcements or forum posts have been recorded.\n\nWould you like me to help you join one of the live Math sessions?",NULL,"2026-05-10 02:08:18","11");
INSERT INTO ai_messages VALUES("35","user","yes",NULL,"2026-05-10 02:08:44","11");
INSERT INTO ai_messages VALUES("36","assistant","Hello Zrafi! How can I help you today? \n\nI see you\'re currently in class **3A2** and there are a few active livestreams, including a **Math Algebra Livestream**. Would you like more details on those, or do you need help with your lessons or assignments?",NULL,"2026-05-10 02:08:44","11");
INSERT INTO ai_messages VALUES("37","user","can you tell me about the content of forum post test forum",NULL,"2026-05-10 02:10:25","12");
INSERT INTO ai_messages VALUES("38","assistant","I\'m sorry, but I don\'t have any information regarding a forum post titled \"test forum.\" Based on the current data available to me, the forum posts section is empty.",NULL,"2026-05-10 02:10:25","12");
INSERT INTO ai_messages VALUES("39","user","What are my current lessons?",NULL,"2026-05-10 02:20:12","13");
INSERT INTO ai_messages VALUES("40","assistant","Based on your dashboard data, there are currently no lessons listed for your authorized subjects.",NULL,"2026-05-10 02:20:12","13");
INSERT INTO ai_messages VALUES("41","user","any forum post ?",NULL,"2026-05-10 02:24:04","14");
INSERT INTO ai_messages VALUES("42","assistant","Yes, there are a couple of forum posts:\n\n*   **[PENDING] test copilot** by Zrafi (May 10): \"testing copilot reach\"\n*   **[APPROVED] Test Forum** by Haitham (Apr 28): \"TestTestTestTestTest\"\n    *   Comments:\n        *   Test (Apr 28): \"gg\"\n        *   Zrafi (May 04): \"gg\"",NULL,"2026-05-10 02:24:04","14");
INSERT INTO ai_messages VALUES("43","user","How many active students do we have?",NULL,"2026-05-10 03:23:07","15");
INSERT INTO ai_messages VALUES("44","assistant","I\'m sorry, I don\'t know the exact number of active students. My current data indicates there are 7 total users on the platform.",NULL,"2026-05-10 03:23:07","15");
INSERT INTO ai_messages VALUES("45","user","Are there any forum posts pending approval?",NULL,"2026-05-10 03:23:39","16");
INSERT INTO ai_messages VALUES("46","assistant","Yes, there is currently one forum post pending approval:\n\n*   **Post:** \"test copilot\" by Zrafi (Date: May 10)",NULL,"2026-05-10 03:23:39","16");
INSERT INTO ai_messages VALUES("47","user","overall analystics",NULL,"2026-05-10 03:24:11","16");
INSERT INTO ai_messages VALUES("48","assistant","Hello Zrafi Abdeslem. Here is the high-level analytical overview for the Learnway platform:\n\n### **Platform Health & Engagement**\n*   **Total Users:** 7\n*   **Total Classes:** 12\n*   **Student Engagement/Mood:**\n    *   **Absent:** 149\n    *   **Neutral:** 20\n    *   **Happy:** 1\n\n### **System Moderation**\n*   **Pending Forum Posts:** There is 1 post awaiting your moderation:\n    *   *test copilot* by Zrafi (May 10).\n\n### **Academic Activity**\n*   **Active Livestreams:**\n    *   *test jitsi* (Class: 3A2) - Currently LIVE.\n    *   *test* (Class: 3A2) - Currently LIVE.\n    *   *Math Algebra Livestream* (Class: 3A2) - Currently LIVE.\n*   **Upcoming Assignments:**\n    *   **Quiz #1 (Introduction to Algebra):** Due 2026-04-27\n    *   **Quiz #1 (Introduction to Science):** Due 2026-04-28\n    *   **Quiz #1 (First Chapter):** Due 2026-04-29\n\n### **Recent Announcements**\n*   Test Grade Announcement (Grade visibility/CRUD test)\n*   Test Grade Announcement (Grades posted)\n*   Welcome to LearnWay (Global)\n\nPlease let me know if you need specific details on any of these categories.",NULL,"2026-05-10 03:24:11","16");
INSERT INTO ai_messages VALUES("49","user","Who are the most active teachers?",NULL,"2026-05-10 03:47:11","17");
INSERT INTO ai_messages VALUES("50","assistant","I\'m sorry, but I don\'t have information regarding the most active teachers in the provided data.",NULL,"2026-05-10 03:47:11","17");



DROP TABLE IF EXISTS announcements;

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

INSERT INTO announcements VALUES("1","12","Welcome to LearnWay","This is a global announcement.","CLASS","21","NORMAL","0","2026-04-27 16:58:00","","");
INSERT INTO announcements VALUES("2","12","Test Grade Announcement","This is a test for grade level target visibility and CRUD.","CLASS","19","HIGH","0","2026-04-27 17:05:00","","");
INSERT INTO announcements VALUES("3","12","Welcome to our new school year!","We are excited to have you all back. Let\'s make this year great!","SCHOOL","","NORMAL","0","2026-04-27 17:09:00","","");
INSERT INTO announcements VALUES("4","12","Test Grade Announcement","Grades for the recent test have been posted.","CLASS","19","HIGH","0","2026-04-27 17:11:00","","");



DROP TABLE IF EXISTS assignments;

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

INSERT INTO assignments VALUES("1","2","Quiz #1","Banana","2026-04-27 23:59:00","TEXT","0","DRAFT","2026-04-26 17:36:48","2026-04-26 17:36:48","quiz");
INSERT INTO assignments VALUES("2","4","Quiz #1","science quiz","2026-04-28 22:57:00","TEXT","0","DRAFT","2026-04-26 21:57:35","2026-04-26 21:57:35","quiz");
INSERT INTO assignments VALUES("3","7","Quiz #1","","2026-04-29 23:15:00","TEXT","0","PUBLISHED","2026-04-26 22:15:17","2026-04-26 22:15:17","quiz");



DROP TABLE IF EXISTS chapter_contents;

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

INSERT INTO chapter_contents VALUES("1","Summary","Blablablabla","13","2026-04-26 16:54:55","2026-04-26 16:54:55","2");



DROP TABLE IF EXISTS chapter_files;

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

INSERT INTO chapter_files VALUES("1","Atelier patrons de conception.docx","/uploads/chapters/2/Atelier-patrons-de-conception-69ee4a70844df.docx","application/vnd.openxmlformats-officedocument.wordprocessingml.document","8096","13","2026-04-26 17:25:04","2");
INSERT INTO chapter_files VALUES("2","Atelier patrons de conception.pdf","/uploads/chapters/2/Atelier-patrons-de-conception-69ee4a7b73f9d.pdf","application/pdf","170318","13","2026-04-26 17:25:15","2");



DROP TABLE IF EXISTS chapter_items;

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




DROP TABLE IF EXISTS chapter_progress;

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




DROP TABLE IF EXISTS chapters;

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

INSERT INTO chapters VALUES("2","Introduction to Algebra","An introductory chapter to basic algebra concepts.","1","1","2026-04-26 14:46:17","2026-05-10 03:21:19","26","18");
INSERT INTO chapters VALUES("3","First Chapter","First Chapter Of Algebra","2","1","2026-04-26 15:04:26","2026-05-10 03:21:47","26","18");
INSERT INTO chapters VALUES("4","Introduction to Science","Science","1","1","2026-04-26 21:57:12","2026-04-26 22:14:48","19","5");
INSERT INTO chapters VALUES("6","Second Chapter","2222","3","1","2026-04-26 22:11:04","2026-04-26 22:11:12","19","4");
INSERT INTO chapters VALUES("7","First Chapter","11111 science","1","1","2026-04-26 22:14:31","2026-04-26 22:14:39","19","5");
INSERT INTO chapters VALUES("8","Second Chapter","2222","2","1","2026-04-26 22:15:56","2026-04-26 22:15:56","19","5");



DROP TABLE IF EXISTS class_schedules;

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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO class_schedules VALUES("1","Monday","26","4","18","1","");
INSERT INTO class_schedules VALUES("2","Tuesday","26","4","18","3","");
INSERT INTO class_schedules VALUES("3","Wednesday","26","4","18","2","");
INSERT INTO class_schedules VALUES("4","Thursday","26","4","18","1","");
INSERT INTO class_schedules VALUES("5","Friday","26","4","18","1","");
INSERT INTO class_schedules VALUES("6","Saturday","26","4","18","2","");



DROP TABLE IF EXISTS classes;

CREATE TABLE `classes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `grade_level` varchar(255) NOT NULL,
  `section` varchar(255) DEFAULT NULL,
  `is_active` tinyint DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO classes VALUES("19","7A","Grade 7","A","1","2026-04-26 12:18:18");
INSERT INTO classes VALUES("20","7B","Grade 7","B","1","2026-04-26 12:18:18");
INSERT INTO classes VALUES("21","Grade 10A","Grade 10","A","1","2026-04-27 15:57:30");
INSERT INTO classes VALUES("22","Grade 10B","Grade 10","B","1","2026-04-27 15:57:30");
INSERT INTO classes VALUES("23","Grade 11A","Grade 11","A","1","2026-04-27 15:57:30");
INSERT INTO classes VALUES("24","Grade 12A","Grade 12","A","1","2026-04-27 15:57:30");
INSERT INTO classes VALUES("25","3A1","Grade 3","A","1","2026-05-04 21:22:09");
INSERT INTO classes VALUES("26","3A2","Grade 3","A","1","2026-05-04 21:22:09");
INSERT INTO classes VALUES("27","3A3","Grade 3","A","1","2026-05-04 21:22:09");
INSERT INTO classes VALUES("28","2A1","Grade 2","A","1","2026-05-04 21:22:09");
INSERT INTO classes VALUES("29","2B1","Grade 2","B","1","2026-05-04 21:22:09");
INSERT INTO classes VALUES("30","2B2","Grade 2","B","1","2026-05-04 21:22:09");



DROP TABLE IF EXISTS conversation_members;

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
) ENGINE=InnoDB AUTO_INCREMENT=104 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO conversation_members VALUES("86","32","14","","2026-04-28 08:31:42","");
INSERT INTO conversation_members VALUES("87","32","12","","2026-04-28 08:31:42","");
INSERT INTO conversation_members VALUES("88","33","14","","2026-04-30 14:40:35","");
INSERT INTO conversation_members VALUES("89","33","13","","2026-04-30 14:40:35","");
INSERT INTO conversation_members VALUES("90","34","16","","2026-05-04 21:25:07","");
INSERT INTO conversation_members VALUES("91","34","12","","2026-05-04 21:25:07","");
INSERT INTO conversation_members VALUES("92","35","18","","2026-05-04 21:27:54","");
INSERT INTO conversation_members VALUES("93","35","12","","2026-05-04 21:27:54","");
INSERT INTO conversation_members VALUES("94","36","18","","2026-05-04 21:28:06","");
INSERT INTO conversation_members VALUES("95","36","16","","2026-05-04 21:28:06","");
INSERT INTO conversation_members VALUES("96","37","17","","2026-05-04 21:38:44","");
INSERT INTO conversation_members VALUES("97","37","16","","2026-05-04 21:38:44","");
INSERT INTO conversation_members VALUES("98","38","17","","2026-05-04 22:31:21","");
INSERT INTO conversation_members VALUES("99","38","12","","2026-05-04 22:31:21","");
INSERT INTO conversation_members VALUES("100","39","18","MOD","2026-05-10 04:07:42","");
INSERT INTO conversation_members VALUES("101","39","12","MEMBER","2026-05-10 04:07:42","");
INSERT INTO conversation_members VALUES("102","39","13","MEMBER","2026-05-10 04:07:42","");
INSERT INTO conversation_members VALUES("103","39","16","MEMBER","2026-05-10 04:07:42","");



DROP TABLE IF EXISTS conversations;

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
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO conversations VALUES("32","DIRECT","","0cfc030daea3570f4a4b110c1ea5bdb0","","2026-04-28 08:31:42");
INSERT INTO conversations VALUES("33","DIRECT","","de91e90a15ee4ac6b67dd4588153263b","","2026-04-30 14:40:35");
INSERT INTO conversations VALUES("34","DIRECT","","6b8b4a871986a0b7ed9031e828933a03","","2026-05-04 21:25:07");
INSERT INTO conversations VALUES("35","DIRECT","","efb11e4218021913758b53a52abd157c","","2026-05-04 21:27:54");
INSERT INTO conversations VALUES("36","DIRECT","","72805c0a53d0f44b75f2fbc28100ab2e","","2026-05-04 21:28:06");
INSERT INTO conversations VALUES("37","DIRECT","","15731e513557734e026a504b0d73e204","","2026-05-04 21:38:44");
INSERT INTO conversations VALUES("38","DIRECT","","bf8e69b4a494e3f0377d80bea06d41cf","","2026-05-04 22:31:21");
INSERT INTO conversations VALUES("39","GROUP","Test group chat","","18","2026-05-10 04:07:42");



DROP TABLE IF EXISTS doctrine_migration_versions;

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO doctrine_migration_versions VALUES("DoctrineMigrations\\Version20260425111645","2026-04-26 10:32:40","1");
INSERT INTO doctrine_migration_versions VALUES("DoctrineMigrations\\Version20260425175630","2026-04-26 10:32:40","63");
INSERT INTO doctrine_migration_versions VALUES("DoctrineMigrations\\Version20260425180120","2026-04-26 10:32:40","8");
INSERT INTO doctrine_migration_versions VALUES("DoctrineMigrations\\Version20260426110000","2026-04-26 11:01:10","167");
INSERT INTO doctrine_migration_versions VALUES("DoctrineMigrations\\Version20260426150000","2026-04-26 11:33:22","155");



DROP TABLE IF EXISTS facial_analysis;

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
) ENGINE=InnoDB AUTO_INCREMENT=171 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO facial_analysis VALUES("1","4","17","neutral","1.0000","{\"samples\": 1, \"averageScore\": 60, \"presenceRate\": 1, \"emotionDistribution\": {\"neutral\": 1}}","2026-05-09 22:28:42");
INSERT INTO facial_analysis VALUES("2","4","17","neutral","1.0000","{\"samples\": 3, \"averageScore\": 60, \"presenceRate\": 1, \"emotionDistribution\": {\"neutral\": 3}}","2026-05-09 22:29:20");
INSERT INTO facial_analysis VALUES("3","4","17","neutral","1.0000","{\"samples\": 3, \"averageScore\": 60, \"presenceRate\": 1, \"emotionDistribution\": {\"neutral\": 3}}","2026-05-09 22:29:52");
INSERT INTO facial_analysis VALUES("4","4","17","neutral","1.0000","{\"samples\": 2, \"averageScore\": 60, \"presenceRate\": 1, \"emotionDistribution\": {\"neutral\": 2}}","2026-05-09 22:30:12");
INSERT INTO facial_analysis VALUES("5","4","17","neutral","1.0000","{\"samples\": 2, \"averageScore\": 60, \"presenceRate\": 1, \"emotionDistribution\": {\"neutral\": 2}}","2026-05-09 22:30:42");
INSERT INTO facial_analysis VALUES("6","4","17","neutral","1.0000","{\"samples\": 3, \"averageScore\": 60, \"presenceRate\": 1, \"emotionDistribution\": {\"neutral\": 3}}","2026-05-09 22:30:54");
INSERT INTO facial_analysis VALUES("7","4","17","neutral","1.0000","{\"samples\": 2, \"averageScore\": 60, \"presenceRate\": 1, \"emotionDistribution\": {\"neutral\": 2}}","2026-05-09 22:31:02");
INSERT INTO facial_analysis VALUES("8","4","17","neutral","1.0000","{\"samples\": 3, \"averageScore\": 60, \"presenceRate\": 1, \"emotionDistribution\": {\"neutral\": 3}}","2026-05-09 22:31:07");
INSERT INTO facial_analysis VALUES("9","4","17","neutral","1.0000","{\"samples\": 2, \"averageScore\": 60, \"presenceRate\": 1, \"emotionDistribution\": {\"neutral\": 2}}","2026-05-09 22:31:11");
INSERT INTO facial_analysis VALUES("10","4","17","absent","0.3333","{\"samples\": 3, \"averageScore\": 21, \"presenceRate\": 0.3333333333333333, \"emotionDistribution\": {\"absent\": 2, \"neutral\": 1}}","2026-05-09 22:31:20");
INSERT INTO facial_analysis VALUES("11","4","17","neutral","1.0000","{\"samples\": 2, \"averageScore\": 80, \"presenceRate\": 1, \"emotionDistribution\": {\"happy\": 1, \"neutral\": 1}}","2026-05-09 22:31:27");
INSERT INTO facial_analysis VALUES("12","4","17","neutral","1.0000","{\"samples\": 3, \"averageScore\": 60, \"presenceRate\": 1, \"emotionDistribution\": {\"neutral\": 3}}","2026-05-09 22:31:39");
INSERT INTO facial_analysis VALUES("13","4","17","neutral","1.0000","{\"samples\": 1, \"averageScore\": 60, \"presenceRate\": 1, \"emotionDistribution\": {\"neutral\": 1}}","2026-05-09 22:31:46");
INSERT INTO facial_analysis VALUES("14","4","17","neutral","1.0000","{\"samples\": 3, \"averageScore\": 60, \"presenceRate\": 1, \"emotionDistribution\": {\"neutral\": 3}}","2026-05-09 22:31:57");
INSERT INTO facial_analysis VALUES("15","4","17","neutral","1.0000","{\"samples\": 2, \"averageScore\": 60, \"presenceRate\": 1, \"emotionDistribution\": {\"neutral\": 2}}","2026-05-09 22:32:01");
INSERT INTO facial_analysis VALUES("16","4","17","happy","1.0000","{\"samples\": 3, \"averageScore\": 88, \"presenceRate\": 1, \"emotionDistribution\": {\"happy\": 3}}","2026-05-09 22:32:50");
INSERT INTO facial_analysis VALUES("17","4","17","neutral","1.0000","{\"samples\": 2, \"averageScore\": 60, \"presenceRate\": 1, \"emotionDistribution\": {\"neutral\": 2}}","2026-05-09 22:32:54");
INSERT INTO facial_analysis VALUES("18","4","17","neutral","1.0000","{\"samples\": 2, \"averageScore\": 60, \"presenceRate\": 1, \"emotionDistribution\": {\"neutral\": 2}}","2026-05-09 22:32:57");
INSERT INTO facial_analysis VALUES("19","4","17","neutral","1.0000","{\"samples\": 2, \"averageScore\": 61, \"presenceRate\": 1, \"emotionDistribution\": {\"neutral\": 2}}","2026-05-09 22:33:15");
INSERT INTO facial_analysis VALUES("20","4","17","neutral","1.0000","{\"samples\": 1, \"averageScore\": 60, \"presenceRate\": 1, \"emotionDistribution\": {\"neutral\": 1}}","2026-05-09 22:33:24");
INSERT INTO facial_analysis VALUES("21","4","17","neutral","1.0000","{\"samples\": 3, \"averageScore\": 60, \"presenceRate\": 1, \"emotionDistribution\": {\"neutral\": 3}}","2026-05-09 22:33:56");
INSERT INTO facial_analysis VALUES("22","4","17","neutral","1.0000","{\"samples\": 3, \"averageScore\": 60, \"presenceRate\": 1, \"emotionDistribution\": {\"neutral\": 3}}","2026-05-09 22:34:04");
INSERT INTO facial_analysis VALUES("23","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 22:34:08");
INSERT INTO facial_analysis VALUES("24","4","17","absent","0.0000","{\"samples\": 2, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 2}}","2026-05-09 22:34:12");
INSERT INTO facial_analysis VALUES("25","4","17","absent","0.0000","{\"samples\": 2, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 2}}","2026-05-09 22:34:17");
INSERT INTO facial_analysis VALUES("26","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 22:34:21");
INSERT INTO facial_analysis VALUES("27","4","17","absent","0.0000","{\"samples\": 2, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 2}}","2026-05-09 22:36:30");
INSERT INTO facial_analysis VALUES("28","4","17","absent","0.0000","{\"samples\": 1, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 1}}","2026-05-09 22:37:29");
INSERT INTO facial_analysis VALUES("29","4","17","absent","0.0000","{\"samples\": 1, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 1}}","2026-05-09 22:39:29");
INSERT INTO facial_analysis VALUES("30","4","17","absent","0.0000","{\"samples\": 1, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 1}}","2026-05-09 22:42:12");
INSERT INTO facial_analysis VALUES("31","4","17","absent","0.0000","{\"samples\": 1, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 1}}","2026-05-09 22:42:19");
INSERT INTO facial_analysis VALUES("32","4","17","absent","0.0000","{\"samples\": 2, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 2}}","2026-05-09 22:45:23");
INSERT INTO facial_analysis VALUES("33","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 22:45:26");
INSERT INTO facial_analysis VALUES("34","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 22:45:30");
INSERT INTO facial_analysis VALUES("35","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 22:45:46");
INSERT INTO facial_analysis VALUES("36","4","17","absent","0.0000","{\"samples\": 2, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 2}}","2026-05-09 22:46:25");
INSERT INTO facial_analysis VALUES("37","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 22:46:29");
INSERT INTO facial_analysis VALUES("38","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 22:46:40");
INSERT INTO facial_analysis VALUES("39","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 22:46:42");
INSERT INTO facial_analysis VALUES("40","4","17","absent","0.0000","{\"samples\": 2, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 2}}","2026-05-09 22:47:57");
INSERT INTO facial_analysis VALUES("41","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 22:48:05");
INSERT INTO facial_analysis VALUES("42","4","17","absent","0.0000","{\"samples\": 2, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 2}}","2026-05-09 22:48:12");
INSERT INTO facial_analysis VALUES("43","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 22:48:22");
INSERT INTO facial_analysis VALUES("44","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 22:48:36");
INSERT INTO facial_analysis VALUES("45","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 22:48:44");
INSERT INTO facial_analysis VALUES("46","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 22:48:53");
INSERT INTO facial_analysis VALUES("47","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 22:49:02");
INSERT INTO facial_analysis VALUES("48","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 22:49:09");
INSERT INTO facial_analysis VALUES("49","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 22:49:13");
INSERT INTO facial_analysis VALUES("50","4","17","absent","0.0000","{\"samples\": 4, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 4}}","2026-05-09 22:49:17");
INSERT INTO facial_analysis VALUES("51","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 22:49:28");
INSERT INTO facial_analysis VALUES("52","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 22:50:07");
INSERT INTO facial_analysis VALUES("53","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 22:50:13");
INSERT INTO facial_analysis VALUES("54","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 22:50:25");
INSERT INTO facial_analysis VALUES("55","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 22:50:32");
INSERT INTO facial_analysis VALUES("56","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 22:50:36");
INSERT INTO facial_analysis VALUES("57","4","17","absent","0.0000","{\"samples\": 4, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 4}}","2026-05-09 22:50:40");
INSERT INTO facial_analysis VALUES("58","4","17","absent","0.0000","{\"samples\": 2, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 2}}","2026-05-09 22:50:52");
INSERT INTO facial_analysis VALUES("59","4","17","absent","0.0000","{\"samples\": 4, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 4}}","2026-05-09 22:50:57");
INSERT INTO facial_analysis VALUES("60","4","17","absent","0.0000","{\"samples\": 2, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 2}}","2026-05-09 22:51:36");
INSERT INTO facial_analysis VALUES("61","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 22:51:45");
INSERT INTO facial_analysis VALUES("62","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 22:51:49");
INSERT INTO facial_analysis VALUES("63","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 22:51:53");
INSERT INTO facial_analysis VALUES("64","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 22:52:04");
INSERT INTO facial_analysis VALUES("65","4","17","absent","0.0000","{\"samples\": 1, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 1}}","2026-05-09 22:53:33");
INSERT INTO facial_analysis VALUES("66","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 22:53:49");
INSERT INTO facial_analysis VALUES("67","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 22:53:54");
INSERT INTO facial_analysis VALUES("68","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 22:54:30");
INSERT INTO facial_analysis VALUES("69","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 22:54:46");
INSERT INTO facial_analysis VALUES("70","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 22:54:52");
INSERT INTO facial_analysis VALUES("71","4","17","absent","0.0000","{\"samples\": 2, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 2}}","2026-05-09 22:54:59");
INSERT INTO facial_analysis VALUES("72","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 22:55:05");
INSERT INTO facial_analysis VALUES("73","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 22:55:11");
INSERT INTO facial_analysis VALUES("74","4","17","absent","0.0000","{\"samples\": 2, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 2}}","2026-05-09 22:55:17");
INSERT INTO facial_analysis VALUES("75","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 22:55:22");
INSERT INTO facial_analysis VALUES("76","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 22:55:27");
INSERT INTO facial_analysis VALUES("77","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 22:55:34");
INSERT INTO facial_analysis VALUES("78","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 22:55:42");
INSERT INTO facial_analysis VALUES("79","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 22:55:48");
INSERT INTO facial_analysis VALUES("80","4","17","absent","0.0000","{\"samples\": 2, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 2}}","2026-05-09 22:55:54");
INSERT INTO facial_analysis VALUES("81","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 22:55:59");
INSERT INTO facial_analysis VALUES("82","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 22:56:08");
INSERT INTO facial_analysis VALUES("83","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 22:56:24");
INSERT INTO facial_analysis VALUES("84","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 22:56:35");
INSERT INTO facial_analysis VALUES("85","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 22:56:57");
INSERT INTO facial_analysis VALUES("86","4","17","absent","0.0000","{\"samples\": 1, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 1}}","2026-05-09 22:58:45");
INSERT INTO facial_analysis VALUES("87","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 22:59:02");
INSERT INTO facial_analysis VALUES("88","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 22:59:07");
INSERT INTO facial_analysis VALUES("89","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 22:59:11");
INSERT INTO facial_analysis VALUES("90","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 22:59:16");
INSERT INTO facial_analysis VALUES("91","4","17","absent","0.0000","{\"samples\": 2, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 2}}","2026-05-09 22:59:20");
INSERT INTO facial_analysis VALUES("92","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 22:59:26");
INSERT INTO facial_analysis VALUES("93","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 22:59:31");
INSERT INTO facial_analysis VALUES("94","4","17","absent","0.0000","{\"samples\": 2, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 2}}","2026-05-09 22:59:38");
INSERT INTO facial_analysis VALUES("95","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 22:59:44");
INSERT INTO facial_analysis VALUES("96","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 22:59:51");
INSERT INTO facial_analysis VALUES("97","4","17","absent","0.0000","{\"samples\": 2, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 2}}","2026-05-09 22:59:56");
INSERT INTO facial_analysis VALUES("98","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 23:00:05");
INSERT INTO facial_analysis VALUES("99","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 23:00:16");
INSERT INTO facial_analysis VALUES("100","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 23:00:28");
INSERT INTO facial_analysis VALUES("101","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 23:00:36");
INSERT INTO facial_analysis VALUES("102","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 23:00:46");
INSERT INTO facial_analysis VALUES("103","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 23:00:57");
INSERT INTO facial_analysis VALUES("104","4","17","absent","0.0000","{\"samples\": 2, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 2}}","2026-05-09 23:01:06");
INSERT INTO facial_analysis VALUES("105","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 23:01:17");
INSERT INTO facial_analysis VALUES("106","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 23:01:27");
INSERT INTO facial_analysis VALUES("107","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 23:01:46");
INSERT INTO facial_analysis VALUES("108","4","17","absent","0.0000","{\"samples\": 2, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 2}}","2026-05-09 23:02:14");
INSERT INTO facial_analysis VALUES("109","4","17","absent","0.0000","{\"samples\": 4, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 4}}","2026-05-09 23:02:19");
INSERT INTO facial_analysis VALUES("110","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 23:02:22");
INSERT INTO facial_analysis VALUES("111","4","17","absent","0.0000","{\"samples\": 2, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 2}}","2026-05-09 23:07:25");
INSERT INTO facial_analysis VALUES("112","4","17","absent","0.0000","{\"samples\": 2, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 2}}","2026-05-09 23:07:38");
INSERT INTO facial_analysis VALUES("113","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 23:07:47");
INSERT INTO facial_analysis VALUES("114","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 23:07:55");
INSERT INTO facial_analysis VALUES("115","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 23:08:07");
INSERT INTO facial_analysis VALUES("116","4","17","absent","0.0000","{\"samples\": 2, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 2}}","2026-05-09 23:08:15");
INSERT INTO facial_analysis VALUES("117","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 23:08:24");
INSERT INTO facial_analysis VALUES("118","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 23:08:35");
INSERT INTO facial_analysis VALUES("119","4","17","absent","0.0000","{\"samples\": 2, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 2}}","2026-05-09 23:08:45");
INSERT INTO facial_analysis VALUES("120","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 23:08:58");
INSERT INTO facial_analysis VALUES("121","4","17","absent","0.0000","{\"samples\": 2, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 2}}","2026-05-09 23:09:09");
INSERT INTO facial_analysis VALUES("122","4","17","absent","0.0000","{\"samples\": 2, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 2}}","2026-05-09 23:09:17");
INSERT INTO facial_analysis VALUES("123","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 23:09:25");
INSERT INTO facial_analysis VALUES("124","4","17","absent","0.0000","{\"samples\": 2, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 2}}","2026-05-09 23:09:35");
INSERT INTO facial_analysis VALUES("125","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 23:09:44");
INSERT INTO facial_analysis VALUES("126","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 23:09:53");
INSERT INTO facial_analysis VALUES("127","4","17","absent","0.0000","{\"samples\": 2, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 2}}","2026-05-09 23:10:03");
INSERT INTO facial_analysis VALUES("128","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 23:10:12");
INSERT INTO facial_analysis VALUES("129","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 23:10:22");
INSERT INTO facial_analysis VALUES("130","4","17","absent","0.0000","{\"samples\": 4, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 4}}","2026-05-09 23:10:32");
INSERT INTO facial_analysis VALUES("131","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 23:10:45");
INSERT INTO facial_analysis VALUES("132","4","17","absent","0.0000","{\"samples\": 2, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 2}}","2026-05-09 23:11:00");
INSERT INTO facial_analysis VALUES("133","4","17","absent","0.0000","{\"samples\": 4, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 4}}","2026-05-09 23:11:20");
INSERT INTO facial_analysis VALUES("134","4","17","absent","0.0000","{\"samples\": 1, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 1}}","2026-05-09 23:13:17");
INSERT INTO facial_analysis VALUES("135","4","17","absent","0.0000","{\"samples\": 4, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 4}}","2026-05-09 23:13:31");
INSERT INTO facial_analysis VALUES("136","4","17","absent","0.0000","{\"samples\": 2, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 2}}","2026-05-09 23:13:36");
INSERT INTO facial_analysis VALUES("137","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 23:13:40");
INSERT INTO facial_analysis VALUES("138","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 23:13:43");
INSERT INTO facial_analysis VALUES("139","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 23:13:46");
INSERT INTO facial_analysis VALUES("140","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 23:13:55");
INSERT INTO facial_analysis VALUES("141","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 23:14:01");
INSERT INTO facial_analysis VALUES("142","4","17","absent","0.0000","{\"samples\": 2, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 2}}","2026-05-09 23:14:49");
INSERT INTO facial_analysis VALUES("143","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 23:14:57");
INSERT INTO facial_analysis VALUES("144","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 23:15:02");
INSERT INTO facial_analysis VALUES("145","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 23:15:08");
INSERT INTO facial_analysis VALUES("146","4","17","absent","0.0000","{\"samples\": 2, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 2}}","2026-05-09 23:15:13");
INSERT INTO facial_analysis VALUES("147","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 23:15:18");
INSERT INTO facial_analysis VALUES("148","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 23:15:25");
INSERT INTO facial_analysis VALUES("149","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 23:15:29");
INSERT INTO facial_analysis VALUES("150","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 23:15:36");
INSERT INTO facial_analysis VALUES("151","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 23:15:46");
INSERT INTO facial_analysis VALUES("152","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 23:15:57");
INSERT INTO facial_analysis VALUES("153","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 23:16:08");
INSERT INTO facial_analysis VALUES("154","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 23:16:17");
INSERT INTO facial_analysis VALUES("155","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 23:16:27");
INSERT INTO facial_analysis VALUES("156","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 23:16:45");
INSERT INTO facial_analysis VALUES("157","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 23:17:04");
INSERT INTO facial_analysis VALUES("158","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 23:17:13");
INSERT INTO facial_analysis VALUES("159","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 23:17:25");
INSERT INTO facial_analysis VALUES("160","4","17","absent","0.0000","{\"samples\": 2, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 2}}","2026-05-09 23:18:56");
INSERT INTO facial_analysis VALUES("161","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 23:19:03");
INSERT INTO facial_analysis VALUES("162","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 23:19:10");
INSERT INTO facial_analysis VALUES("163","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 23:19:16");
INSERT INTO facial_analysis VALUES("164","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 23:19:25");
INSERT INTO facial_analysis VALUES("165","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 23:19:35");
INSERT INTO facial_analysis VALUES("166","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 23:19:44");
INSERT INTO facial_analysis VALUES("167","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 23:19:53");
INSERT INTO facial_analysis VALUES("168","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 23:20:11");
INSERT INTO facial_analysis VALUES("169","4","17","absent","0.0000","{\"samples\": 3, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 3}}","2026-05-09 23:20:22");
INSERT INTO facial_analysis VALUES("170","4","17","absent","0.0000","{\"samples\": 2, \"averageScore\": 0, \"presenceRate\": 0, \"emotionDistribution\": {\"absent\": 2}}","2026-05-09 23:20:25");



DROP TABLE IF EXISTS forum_comments;

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
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO forum_comments VALUES("43","9","","12","gg","2026-04-28 08:16:10","","APPROVED");
INSERT INTO forum_comments VALUES("44","9","","16","gg","2026-05-04 21:25:00","","APPROVED");
INSERT INTO forum_comments VALUES("45","9","43","17","test reply","2026-05-10 02:34:18","","APPROVED");



DROP TABLE IF EXISTS forum_post_attachments;

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




DROP TABLE IF EXISTS forum_posts;

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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO forum_posts VALUES("9","","Test Forum","","","TestTestTestTestTest","","13","2026-04-28 08:12:19","APPROVED");
INSERT INTO forum_posts VALUES("10","","test bad words","","","****","","13","2026-04-28 11:08:27","REJECTED");
INSERT INTO forum_posts VALUES("11","26","test copilot","","","testing copilot reach","","17","2026-05-10 02:12:17","APPROVED");
INSERT INTO forum_posts VALUES("12","","test banner","","","banner test","esprim-logo-6a000308f0b34.png","18","2026-05-10 04:01:11","APPROVED");



DROP TABLE IF EXISTS forum_reviews;

CREATE TABLE `forum_reviews` (
  `id` int NOT NULL AUTO_INCREMENT,
  `post_id` bigint DEFAULT NULL,
  `student_id` bigint DEFAULT NULL,
  `rating` int NOT NULL,
  `review_text` longtext,
  `created_at` datetime DEFAULT NULL,
  `sync_uuid` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_98BCA67ECB944F1A` (`student_id`),
  KEY `IDX_98BCA67E4B89032C` (`post_id`),
  CONSTRAINT `FK_98BCA67E4B89032C` FOREIGN KEY (`post_id`) REFERENCES `forum_posts` (`id`),
  CONSTRAINT `FK_98BCA67ECB944F1A` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`),
  CONSTRAINT `forum_reviews_chk_1` CHECK ((`rating` between 1 and 5))
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO forum_reviews VALUES("18","9","17","5","","2026-05-10 02:54:09","");
INSERT INTO forum_reviews VALUES("19","12","16","4","","2026-05-10 04:14:33","");



DROP TABLE IF EXISTS livestream_chats;

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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO livestream_chats VALUES("1","3","18","test hello","2026-05-06 17:20:49");
INSERT INTO livestream_chats VALUES("2","3","17","chat","2026-05-06 17:27:23");
INSERT INTO livestream_chats VALUES("3","3","17","hi","2026-05-09 20:10:48");
INSERT INTO livestream_chats VALUES("4","3","17","ello ","2026-05-09 20:10:56");
INSERT INTO livestream_chats VALUES("5","4","18","aaaa","2026-05-09 20:17:00");
INSERT INTO livestream_chats VALUES("6","4","17","hi","2026-05-09 21:08:20");
INSERT INTO livestream_chats VALUES("7","4","18","hi test ai tools","2026-05-09 21:30:58");



DROP TABLE IF EXISTS livestream_participants;

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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO livestream_participants VALUES("1","1","18","TEACHER","2026-05-05 22:17:47","","2026-05-05 22:17:47");
INSERT INTO livestream_participants VALUES("2","1","17","STUDENT","2026-05-05 22:26:01","","2026-05-05 22:26:01");
INSERT INTO livestream_participants VALUES("3","2","18","TEACHER","2026-05-06 07:43:01","","2026-05-06 07:43:01");
INSERT INTO livestream_participants VALUES("4","3","18","TEACHER","2026-05-06 16:07:09","","2026-05-06 16:07:09");
INSERT INTO livestream_participants VALUES("5","3","17","STUDENT","2026-05-06 16:54:36","","2026-05-06 16:54:36");
INSERT INTO livestream_participants VALUES("6","4","18","TEACHER","2026-05-09 20:14:47","","2026-05-09 20:14:47");
INSERT INTO livestream_participants VALUES("7","4","17","STUDENT","2026-05-09 20:18:49","","2026-05-09 20:18:49");



DROP TABLE IF EXISTS livestream_qa;

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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO livestream_qa VALUES("1","3","18","ask ? \n","aaaa","18","2026-05-06 17:21:02","2026-05-06 17:21:10");
INSERT INTO livestream_qa VALUES("2","3","17","aaa\n","","","2026-05-09 20:10:40","");
INSERT INTO livestream_qa VALUES("3","4","18","1 +1 ?\n","2\n","17","2026-05-09 20:17:14","2026-05-09 20:25:19");
INSERT INTO livestream_qa VALUES("4","4","17","test ? \n","","","2026-05-09 21:32:44","");
INSERT INTO livestream_qa VALUES("5","4","18","test \n","","","2026-05-09 22:06:27","");



DROP TABLE IF EXISTS livestreams;

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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO livestreams VALUES("1","18","26","Math Algebra Livestream for 3A2","livestream for math subject chapter algebra for 3A2","lw_a07bebcc0405aa16","2026-05-05 23:17:00","2026-05-05 22:17:28","","LIVE","","2026-05-05 22:17:14","2026-05-05 23:17:28",NULL,NULL);
INSERT INTO livestreams VALUES("2","18","26","test ","test live","lw_2961797aeb306bba","2026-05-06 08:42:00","2026-05-06 07:42:55","","LIVE","","2026-05-06 07:42:36","2026-05-06 08:42:55",NULL,NULL);
INSERT INTO livestreams VALUES("3","18","26","test livestream","test","lw_e024b71d1bca3d25","2026-05-06 17:06:00","2026-05-06 16:07:04","2026-05-09 20:12:40","ENDED","","2026-05-06 16:06:57","2026-05-09 21:12:40",NULL,NULL);
INSERT INTO livestreams VALUES("4","18","26","test jitsi","test","lw_1f311f84bbeecffa","2026-05-09 21:14:00","2026-05-09 20:14:31","","LIVE","","2026-05-09 20:14:15","2026-05-09 21:14:31",NULL,NULL);



DROP TABLE IF EXISTS message_reads;

CREATE TABLE `message_reads` (
  `message_id` bigint NOT NULL,
  `user_id` bigint NOT NULL,
  PRIMARY KEY (`user_id`,`message_id`),
  KEY `IDX_37E6935AA76ED395` (`user_id`),
  KEY `IDX_37E6935A537A1329` (`message_id`),
  CONSTRAINT `FK_37E6935A537A1329` FOREIGN KEY (`message_id`) REFERENCES `messages` (`id`),
  CONSTRAINT `FK_37E6935AA76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;




DROP TABLE IF EXISTS messages;

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
) ENGINE=InnoDB AUTO_INCREMENT=122 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO messages VALUES("64","32","14","yo","","2026-04-28 08:31:47","SENT","","","","0");
INSERT INTO messages VALUES("65","32","12","yo","","2026-04-28 08:32:22","SENT","","","","0");
INSERT INTO messages VALUES("66","32","14","test","","2026-04-28 08:42:19","SENT","","","","0");
INSERT INTO messages VALUES("67","32","12","test","","2026-04-28 08:43:19","SENT","","","","0");
INSERT INTO messages VALUES("68","32","12","123","","2026-04-28 09:01:21","SENT","","","","0");
INSERT INTO messages VALUES("69","32","14","hi","","2026-04-28 09:04:04","SENT","","","","0");
INSERT INTO messages VALUES("70","32","12","hello","","2026-04-28 09:04:07","SENT","","","","0");
INSERT INTO messages VALUES("71","32","12","zzz","","2026-04-28 09:05:33","SENT","","","","0");
INSERT INTO messages VALUES("72","32","14","test","","2026-04-28 12:53:18","SENT","","","","0");
INSERT INTO messages VALUES("73","32","12","tttt","","2026-04-28 12:53:25","SENT","","","","0");
INSERT INTO messages VALUES("74","32","14","ggg","","2026-04-28 12:53:27","SENT","","","","0");
INSERT INTO messages VALUES("75","32","12","ff","","2026-04-28 12:53:35","SENT","","","","0");
INSERT INTO messages VALUES("76","32","14","aea","","2026-04-28 12:53:41","SENT","","","","0");
INSERT INTO messages VALUES("77","32","14","test","","2026-04-28 12:56:45","SENT","","","","0");
INSERT INTO messages VALUES("78","32","12","test","","2026-04-28 12:56:52","SENT","","","","0");
INSERT INTO messages VALUES("79","32","14","tt","","2026-04-28 12:56:54","SENT","","","","0");
INSERT INTO messages VALUES("80","32","12","gg","","2026-04-28 12:57:04","SENT","","","","0");
INSERT INTO messages VALUES("81","32","14","gg","","2026-04-28 12:57:09","SENT","","","","0");
INSERT INTO messages VALUES("82","32","12","gg","","2026-04-28 12:57:26","SENT","","","","0");
INSERT INTO messages VALUES("83","32","14","gg","","2026-04-28 12:57:28","SENT","","","","0");
INSERT INTO messages VALUES("84","32","14","ggf","","2026-04-28 12:57:36","SENT","","","","0");
INSERT INTO messages VALUES("85","32","14","jj","","2026-04-28 12:57:50","SENT","","","","0");
INSERT INTO messages VALUES("86","32","12","gg","","2026-04-28 12:57:53","SENT","","","","0");
INSERT INTO messages VALUES("87","32","14","test","","2026-04-28 12:57:55","SENT","","","","0");
INSERT INTO messages VALUES("88","33","14","hello teacher","","2026-04-30 14:43:12","SENT","","","","0");
INSERT INTO messages VALUES("89","38","17","aaaa","","2026-05-04 22:37:26","SENT","","","","0");
INSERT INTO messages VALUES("90","38","17","aaaaaaaaaaaaaaaa","","2026-05-04 22:37:30","SENT","","","","0");
INSERT INTO messages VALUES("91","38","17","aaaaaaaaaaaaaaaaaaaaa","","2026-05-04 22:37:36","SENT","","","","0");
INSERT INTO messages VALUES("92","38","17","SELEM","","2026-05-04 22:38:51","SENT","","","","0");
INSERT INTO messages VALUES("93","38","17","AAAA","","2026-05-04 22:38:57","SENT","","","","0");
INSERT INTO messages VALUES("94","38","17","selemu alaykum","","2026-05-04 22:39:05","SENT","","","","0");
INSERT INTO messages VALUES("95","38","17","aaa","","2026-05-04 22:46:35","SENT","","","","0");
INSERT INTO messages VALUES("96","37","17","aya wink ay","","2026-05-04 22:46:45","SENT","","","","0");
INSERT INTO messages VALUES("97","37","17","test","","2026-05-04 22:46:50","SENT","","","","0");
INSERT INTO messages VALUES("98","37","17","test 2","","2026-05-04 22:46:55","SENT","","","","0");
INSERT INTO messages VALUES("99","37","17","test 1000","","2026-05-04 22:47:00","SENT","","","","0");
INSERT INTO messages VALUES("100","37","17","ok","","2026-05-04 22:49:46","SENT","","","","0");
INSERT INTO messages VALUES("101","38","17","aaa","","2026-05-04 22:49:53","SENT","","","","0");
INSERT INTO messages VALUES("102","37","17","test debug","","2026-05-04 22:51:12","SENT","","","","0");
INSERT INTO messages VALUES("103","37","17","debug","","2026-05-04 22:53:08","SENT","","","","0");
INSERT INTO messages VALUES("104","37","17","test","","2026-05-04 22:53:19","SENT","","","","0");
INSERT INTO messages VALUES("105","37","17","hello","","2026-05-04 22:53:53","SENT","","","","0");
INSERT INTO messages VALUES("106","37","17","hello","","2026-05-04 22:54:10","SENT","","","","0");
INSERT INTO messages VALUES("107","37","17","hi","","2026-05-04 22:54:16","SENT","","","","0");
INSERT INTO messages VALUES("108","37","17","hello","","2026-05-04 22:54:21","SENT","","","","0");
INSERT INTO messages VALUES("109","37","17","test","","2026-05-04 22:54:23","SENT","","","","0");
INSERT INTO messages VALUES("110","37","17","annn","","2026-05-04 22:54:27","SENT","","","","0");
INSERT INTO messages VALUES("111","36","18","aa","","2026-05-05 21:40:35","SENT","","","","0");
INSERT INTO messages VALUES("112","36","18","non","","2026-05-05 21:40:43","SENT","","","2026-05-10 04:08:13","0");
INSERT INTO messages VALUES("113","36","18","aa","","2026-05-06 07:48:19","SENT","","","","0");
INSERT INTO messages VALUES("114","36","18","aa","","2026-05-06 07:48:22","SENT","","","","0");
INSERT INTO messages VALUES("115","36","18","qq","","2026-05-06 07:48:26","SENT","","","","0");
INSERT INTO messages VALUES("116","36","18","test","","2026-05-06 07:50:37","SENT","","","","0");
INSERT INTO messages VALUES("117","37","16","aa","","2026-05-06 16:29:08","SENT","","","","0");
INSERT INTO messages VALUES("118","37","16","aqsq","","2026-05-06 16:29:11","SENT","","","","0");
INSERT INTO messages VALUES("119","37","17","hi","","2026-05-10 02:29:34","SENT","","","","0");
INSERT INTO messages VALUES("120","38","17","aya wink ay","","2026-05-10 04:13:07","SENT","","","","1");
INSERT INTO messages VALUES("121","39","18","selemualaykom","","2026-05-10 05:42:29","SENT","","","","0");



DROP TABLE IF EXISTS messenger_messages;

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

INSERT INTO messenger_messages VALUES("1","O:36:\\\"Symfony\\\\Component\\\\Messenger\\\\Envelope\\\":2:{s:44:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0stamps\\\";a:1:{s:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\";a:1:{i:0;O:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\":1:{s:55:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\0busName\\\";s:21:\\\"messenger.bus.default\\\";}}}s:45:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0message\\\";O:51:\\\"Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\\":2:{s:60:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0message\\\";O:28:\\\"Symfony\\\\Component\\\\Mime\\\\Email\\\":6:{i:0;N;i:1;N;i:2;s:112:\\\"<p>Hello Student1,</p><p>A new chapter has been added to your Mathematics class.</p><p>Title: Second Chapter</p>\\\";i:3;s:5:\\\"utf-8\\\";i:4;a:0:{}i:5;a:2:{i:0;O:37:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\\":2:{s:46:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0headers\\\";a:3:{s:4:\\\"from\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:4:\\\"From\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:20:\\\"noreply@learnway.app\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:0:\\\"\\\";}}}}s:2:\\\"to\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:2:\\\"To\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:21:\\\"student1@learnway.com\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:0:\\\"\\\";}}}}s:7:\\\"subject\\\";a:1:{i:0;O:48:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:7:\\\"Subject\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:55:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\0value\\\";s:33:\\\"New Chapter Added: Second Chapter\\\";}}}s:49:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0lineLength\\\";i:76;}i:1;N;}}s:61:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0envelope\\\";N;}}","[]","default","2026-04-26 22:06:13","2026-04-26 22:06:13","");
INSERT INTO messenger_messages VALUES("2","O:36:\\\"Symfony\\\\Component\\\\Messenger\\\\Envelope\\\":2:{s:44:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0stamps\\\";a:1:{s:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\";a:1:{i:0;O:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\":1:{s:55:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\0busName\\\";s:21:\\\"messenger.bus.default\\\";}}}s:45:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0message\\\";O:51:\\\"Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\\":2:{s:60:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0message\\\";O:28:\\\"Symfony\\\\Component\\\\Mime\\\\Email\\\":6:{i:0;N;i:1;N;i:2;s:112:\\\"<p>Hello Student2,</p><p>A new chapter has been added to your Mathematics class.</p><p>Title: Second Chapter</p>\\\";i:3;s:5:\\\"utf-8\\\";i:4;a:0:{}i:5;a:2:{i:0;O:37:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\\":2:{s:46:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0headers\\\";a:3:{s:4:\\\"from\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:4:\\\"From\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:20:\\\"noreply@learnway.app\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:0:\\\"\\\";}}}}s:2:\\\"to\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:2:\\\"To\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:21:\\\"student2@learnway.com\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:0:\\\"\\\";}}}}s:7:\\\"subject\\\";a:1:{i:0;O:48:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:7:\\\"Subject\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:55:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\0value\\\";s:33:\\\"New Chapter Added: Second Chapter\\\";}}}s:49:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0lineLength\\\";i:76;}i:1;N;}}s:61:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0envelope\\\";N;}}","[]","default","2026-04-26 22:06:13","2026-04-26 22:06:13","");



DROP TABLE IF EXISTS roles;

CREATE TABLE `roles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `role_category` varchar(255) NOT NULL,
  `description` longtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6241 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO roles VALUES("1","ADMIN","ADMIN","");
INSERT INTO roles VALUES("2","TEACHER","TEACHER","");
INSERT INTO roles VALUES("3","STUDENT","STUDENT","");



DROP TABLE IF EXISTS student_enrollments;

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
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO student_enrollments VALUES("14","19","14","6");
INSERT INTO student_enrollments VALUES("15","21","15","6");
INSERT INTO student_enrollments VALUES("18","26","17","7");



DROP TABLE IF EXISTS subjects;

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

INSERT INTO subjects VALUES("4","MATH7","Mathematics","Grade 7 math","1","Grade 7","10");
INSERT INTO subjects VALUES("5","SCI7","Science","Grade 7 science","1","Grade 7","9");
INSERT INTO subjects VALUES("6","PHY10","Physics","Mechanics & Heat","1","Grade 10","11");
INSERT INTO subjects VALUES("7","PHY10","Physics","Mechanics & Heat","1","Grade 10","12");
INSERT INTO subjects VALUES("8","PHY10","Physics","Mechanics & Heat","1","Grade 10","13");
INSERT INTO subjects VALUES("9","CHEM10","Chemistry","Basic Inorganic","1","Grade 10","11");
INSERT INTO subjects VALUES("10","CHEM10","Chemistry","Basic Inorganic","1","Grade 10","12");
INSERT INTO subjects VALUES("11","CHEM10","Chemistry","Basic Inorganic","1","Grade 10","13");
INSERT INTO subjects VALUES("12","MATH11","Mathematics","Calculus I","1","Grade 11","11");
INSERT INTO subjects VALUES("13","MATH11","Mathematics","Calculus I","1","Grade 11","12");
INSERT INTO subjects VALUES("14","MATH11","Mathematics","Calculus I","1","Grade 11","13");
INSERT INTO subjects VALUES("15","ENG12","English Literature","Shakespearean Drama","1","Grade 12","11");
INSERT INTO subjects VALUES("16","ENG12","English Literature","Shakespearean Drama","1","Grade 12","12");
INSERT INTO subjects VALUES("17","ENG12","English Literature","Shakespearean Drama","1","Grade 12","13");
INSERT INTO subjects VALUES("18","MATH3","Mathematics","Essential Math","1","Grade 3","14");
INSERT INTO subjects VALUES("19","MATH3","Mathematics","Essential Math","1","Grade 3","15");
INSERT INTO subjects VALUES("20","SCI3","Science","Natural Sciences","1","Grade 3","14");
INSERT INTO subjects VALUES("21","SCI3","Science","Natural Sciences","1","Grade 3","15");
INSERT INTO subjects VALUES("22","INF3","Informatics","Symfony","1","Grade 3","14");
INSERT INTO subjects VALUES("23","INF3","Informatics","Symfony","1","Grade 3","15");
INSERT INTO subjects VALUES("24","MATH2","Mathematics","Advanced Algebra","1","Grade 2","14");
INSERT INTO subjects VALUES("25","MATH2","Mathematics","Advanced Algebra","1","Grade 2","15");
INSERT INTO subjects VALUES("26","SCI2","Science","Natural Sciences","1","Grade 2","14");
INSERT INTO subjects VALUES("27","SCI2","Science","Natural Sciences","1","Grade 2","15");
INSERT INTO subjects VALUES("28","INF2","Informatics","JavaFX","1","Grade 2","14");
INSERT INTO subjects VALUES("29","INF2","Informatics","JavaFX","1","Grade 2","15");



DROP TABLE IF EXISTS submission_files;

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




DROP TABLE IF EXISTS submissions;

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

INSERT INTO submissions VALUES("1","1","14","","2026-04-26 17:40:35","","Good","13","2026-04-26 21:56:53","graded","2026-04-26 17:40:35","");
INSERT INTO submissions VALUES("2","2","14","All correct 100/100","2026-04-26 21:58:05","","100/100","13","2026-04-26 21:58:49","graded","2026-04-26 21:58:05","");
INSERT INTO submissions VALUES("3","3","14","gg","2026-04-28 08:21:31","","","","","submitted","2026-04-28 08:21:31","");



DROP TABLE IF EXISTS teacher_assignments;

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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO teacher_assignments VALUES("1","13","4","19");
INSERT INTO teacher_assignments VALUES("3","13","4","20");
INSERT INTO teacher_assignments VALUES("2","13","5","19");
INSERT INTO teacher_assignments VALUES("5","13","6","21");
INSERT INTO teacher_assignments VALUES("6","13","9","21");
INSERT INTO teacher_assignments VALUES("4","13","12","21");
INSERT INTO teacher_assignments VALUES("7","13","15","21");
INSERT INTO teacher_assignments VALUES("8","18","4","26");



DROP TABLE IF EXISTS terms;

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

INSERT INTO terms VALUES("9","6","Term 1","2025-09-15","2026-01-31","0");
INSERT INTO terms VALUES("10","6","Term 2","2026-02-01","2026-05-31","1");
INSERT INTO terms VALUES("11","7","Autumn Term","2026-08-20","2026-12-18","1");
INSERT INTO terms VALUES("12","7","Spring Term","2027-01-05","2027-03-26","0");
INSERT INTO terms VALUES("13","7","Summer Term","2027-04-12","2027-06-30","0");
INSERT INTO terms VALUES("14","8","Term 1","2026-09-01","2026-12-20","1");
INSERT INTO terms VALUES("15","8","Term 2","2027-01-10","2027-05-20","0");



DROP TABLE IF EXISTS time_slots;

CREATE TABLE `time_slots` (
  `id` int NOT NULL AUTO_INCREMENT,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `type` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO time_slots VALUES("1","08:00:00","09:30:00","90MIN");
INSERT INTO time_slots VALUES("2","09:45:00","11:15:00","90MIN");
INSERT INTO time_slots VALUES("3","11:30:00","13:00:00","90MIN");
INSERT INTO time_slots VALUES("4","14:00:00","15:30:00","90MIN");
INSERT INTO time_slots VALUES("5","15:45:00","17:15:00","90MIN");
INSERT INTO time_slots VALUES("6","08:00:00","10:00:00","120MIN");
INSERT INTO time_slots VALUES("7","10:15:00","12:15:00","120MIN");
INSERT INTO time_slots VALUES("8","13:30:00","15:30:00","120MIN");
INSERT INTO time_slots VALUES("9","15:45:00","17:45:00","120MIN");



DROP TABLE IF EXISTS users;

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

INSERT INTO users VALUES("12","1","admin@learnway.com","$2y$13$HVs7ee5HSsOibKVi0hOAfeqmqHGakeXXqx.538mnTJVu.OycdtR/W","Test","Admin","","","","","","","1","","2026-03-31 20:06:09");
INSERT INTO users VALUES("13","2","haitham@learnway.com","$2y$13$eotvpvZ1jd5OapSrVvBWH.7HuVictJCakaUMXcV6b0Z9Zh7kbmXLK","Haitham","Harzallah","","","","","","","1","","2026-04-26 10:43:51");
INSERT INTO users VALUES("14","3","student1@learnway.com","$2y$13$c9tANac5d/bdgmHVobcTB.HKUeF28kquHK31mGWKPTSm0zpHfgYn2","Student1","Test","","","","","","","1","","2026-04-26 10:44:21");
INSERT INTO users VALUES("15","3","student2@learnway.com","$2y$13$vM1DHmktFY6ZFmJoc7Af7u5GREMjXVIDTB5HHyN0ADSnXQvonXmVq","Student2","Test","","","","","","","1","","2026-04-26 10:44:37");
INSERT INTO users VALUES("16","1","zrafiabdeslem@gmail.com","$2y$13$Grft3uUSaNZY5O.Kzuyk3O215pcxau.tU0jeruvMwYPn88A3L.A.K","Zrafi","Abdeslem","esprim-logo-69fffe82da168.png","","","","","","1","","2026-05-04 21:12:27");
INSERT INTO users VALUES("17","3","zrafistudent@gmail.com","$2y$13$hFEQY6QvuO.jmoKyL/Fy6ufD4uC8.2hqmfIubUUY1DkNUaURDksDi","Zrafi","Student","z2-69fff70bc4746.jpg","","","","","","1","","2026-05-04 21:26:40");
INSERT INTO users VALUES("18","2","zrafiteacher@gmail.com","$2y$13$p2u1/k8Uc5BQELJMt60HnOju6LxykYq9DLk0FVu2FSqB1okhy/C4G","Zrafi","teacher","","","","","","","1","","2026-05-04 21:27:11");

SET FOREIGN_KEY_CHECKS=1;



