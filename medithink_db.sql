-- MySQL dump 10.13  Distrib 8.4.13, for Linux (x86_64)
--
-- Host: localhost    Database: medithink_db
-- ------------------------------------------------------
-- Server version	8.4.13-0ubuntu0.26.04.2

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
-- Table structure for table `game_records`
--

DROP TABLE IF EXISTS `game_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `game_records` (
  `game_record_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `game_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `score` int NOT NULL,
  `played_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`game_record_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `game_records_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `game_records`
--

LOCK TABLES `game_records` WRITE;
/*!40000 ALTER TABLE `game_records` DISABLE KEYS */;
INSERT INTO `game_records` VALUES (18,7,'suuzisagashi',740,'2026-11-26 10:32:22'),(19,7,'suuzisagashi',855,'2026-11-26 11:38:28'),(20,7,'suuzisagashi',920,'2026-11-26 11:38:43'),(21,7,'suuzisagashi',1000,'2026-11-26 12:33:31'),(22,7,'suuzisagashi',7,'2026-11-26 12:33:58'),(23,7,'suuzisagashi',1000,'2026-11-26 12:33:59'),(24,7,'suuzisagashi',965,'2026-11-26 12:38:23'),(25,7,'suuzisagashi',920,'2026-11-27 10:01:56'),(26,7,'suuzisagashi',7,'2026-11-27 10:15:10'),(27,7,'suuzisagashi',1000,'2026-11-27 10:15:10');
/*!40000 ALTER TABLE `game_records` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `game_stats`
--

DROP TABLE IF EXISTS `game_stats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `game_stats` (
  `stat_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `game_name` varchar(50) NOT NULL,
  `best_score` int NOT NULL DEFAULT '0',
  `play_count` int NOT NULL DEFAULT '0',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`stat_id`),
  UNIQUE KEY `uq_user_game` (`user_id`,`game_name`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `game_stats`
--

LOCK TABLES `game_stats` WRITE;
/*!40000 ALTER TABLE `game_stats` DISABLE KEYS */;
INSERT INTO `game_stats` VALUES (1,7,'suuzisagashi',1000,25,'2026-11-27 10:15:10');
/*!40000 ALTER TABLE `game_stats` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `medication_logs`
--

DROP TABLE IF EXISTS `medication_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `medication_logs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `taken_date` date NOT NULL,
  `medication_timing` varchar(20) NOT NULL,
  `taken_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_user_date_timing` (`user_id`,`taken_date`,`medication_timing`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `medication_logs`
--

LOCK TABLES `medication_logs` WRITE;
/*!40000 ALTER TABLE `medication_logs` DISABLE KEYS */;
INSERT INTO `medication_logs` VALUES (1,7,'2026-11-26','evening','2026-11-26 03:38:23'),(2,7,'2026-11-27','noon','2026-11-27 01:01:57');
/*!40000 ALTER TABLE `medication_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `medication_schedules`
--

DROP TABLE IF EXISTS `medication_schedules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `medication_schedules` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `day_of_week` tinyint unsigned NOT NULL,
  `medication_timing` varchar(20) NOT NULL,
  `scheduled_time` time NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_user_day_timing` (`user_id`,`day_of_week`,`medication_timing`),
  KEY `idx_user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=391 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `medication_schedules`
--

LOCK TABLES `medication_schedules` WRITE;
/*!40000 ALTER TABLE `medication_schedules` DISABLE KEYS */;
INSERT INTO `medication_schedules` VALUES (383,7,1,'morning','08:00:00','2026-11-26 03:33:17'),(384,7,4,'morning','08:00:00','2026-11-26 03:33:17'),(385,7,3,'noon','08:00:00','2026-11-26 03:33:17'),(386,7,5,'noon','14:42:00','2026-11-26 03:33:17'),(387,7,2,'evening','12:00:00','2026-11-26 03:33:17'),(388,7,3,'evening','15:38:00','2026-11-26 03:33:17'),(389,7,4,'evening','21:13:00','2026-11-26 03:33:17'),(390,7,2,'bedtime','14:00:00','2026-11-26 03:33:17');
/*!40000 ALTER TABLE `medication_schedules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pazul_game_records`
--

DROP TABLE IF EXISTS `pazul_game_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pazul_game_records` (
  `game_record_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `game_name` varchar(50) NOT NULL DEFAULT 'pazul',
  `score` int NOT NULL,
  `played_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`game_record_id`),
  KEY `idx_pazul_records_user_game` (`user_id`,`game_name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pazul_game_records`
--

LOCK TABLES `pazul_game_records` WRITE;
/*!40000 ALTER TABLE `pazul_game_records` DISABLE KEYS */;
INSERT INTO `pazul_game_records` VALUES (1,7,'pazul',820,'2026-11-27 10:15:46');
/*!40000 ALTER TABLE `pazul_game_records` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pazul_game_stats`
--

DROP TABLE IF EXISTS `pazul_game_stats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pazul_game_stats` (
  `stat_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `game_name` varchar(50) NOT NULL DEFAULT 'pazul',
  `best_score` int NOT NULL DEFAULT '0',
  `play_count` int NOT NULL DEFAULT '0',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`stat_id`),
  UNIQUE KEY `uq_pazul_stats_user_game` (`user_id`,`game_name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pazul_game_stats`
--

LOCK TABLES `pazul_game_stats` WRITE;
/*!40000 ALTER TABLE `pazul_game_stats` DISABLE KEYS */;
INSERT INTO `pazul_game_stats` VALUES (1,7,'pazul',820,1,'2026-11-27 10:15:46');
/*!40000 ALTER TABLE `pazul_game_stats` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_best_scores`
--

DROP TABLE IF EXISTS `user_best_scores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_best_scores` (
  `best_score_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `game_name` varchar(50) NOT NULL,
  `best_score` int NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`best_score_id`),
  UNIQUE KEY `uq_user_game` (`user_id`,`game_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_best_scores`
--

LOCK TABLES `user_best_scores` WRITE;
/*!40000 ALTER TABLE `user_best_scores` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_best_scores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_schedules`
--

DROP TABLE IF EXISTS `user_schedules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_schedules` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `day_of_week` tinyint NOT NULL COMMENT '1:月, 2:火, 3:水, 4:木, 5:金, 6:土, 7:日',
  `time_slot` varchar(50) NOT NULL COMMENT 'morning, lunch, evening, bedtime など',
  `notification_time` time NOT NULL COMMENT '設定時刻',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_user_day_slot` (`user_id`,`day_of_week`,`time_slot`),
  CONSTRAINT `fk_user_schedules_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_schedules`
--

LOCK TABLES `user_schedules` WRITE;
/*!40000 ALTER TABLE `user_schedules` DISABLE KEYS */;
INSERT INTO `user_schedules` VALUES (1,7,1,'morning','08:00:00','2026-11-12 01:49:22','2026-11-12 01:49:22');
/*!40000 ALTER TABLE `user_schedules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `notification_time` time DEFAULT NULL,
  `morning_time` time DEFAULT NULL,
  `lunch_time` time DEFAULT NULL,
  `evening_time` time DEFAULT NULL,
  `bedtime_time` time DEFAULT NULL,
  `secret_question` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `secret_answer` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_first_login` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (7,'test@gmail.com','$2y$12$JT9JGGzXuTVGK3hHgv5gmO4J8IZtKug0Gur1A5jgfMbUxP8Um9BGS','2026-10-30 05:28:51','13:45:00','03:23:00','10:15:00','20:08:00','23:25:00','生年月日8桁','20060215',0),(8,'test@example.com','$2y$12$LeecozVwP01KcAGUxZXrn.llxOQXbm8R.lQxfN5aS90OY55/oW86i','2026-11-01 02:06:32','13:35:00',NULL,NULL,NULL,NULL,'生年月日8桁','20060215',0),(9,'test12345@gmail.com','$2y$12$7tHX8vwRoOmQeRRQ5Xj9Z.FbryGNMEKkNShAYdwckgqCcaSIFYFOy','2026-11-26 03:40:38',NULL,NULL,NULL,NULL,NULL,'生年月日8桁','11111111',0),(10,'test123451@gmail.com','$2y$12$YBpEPO1kULdm55jJIgH1W.G7wA6FjtGBC/SdfuuCz9mkQIlfmyb6a','2026-11-27 00:41:42',NULL,NULL,NULL,NULL,NULL,'生年月日8桁','20060215',1),(11,'test12@gmail.com','$2y$12$uGs/aP.W0GzYiWilpf5gpeZpdAgoI8ngwQN5.2dN4Fi.lhin3Prr.','2026-11-27 00:42:37',NULL,NULL,NULL,NULL,NULL,'生年月日8桁','20060215',1),(12,'test1@gmail.com','$2y$12$ZNe9J/IggnFmJFEuRaSbeusPfP7pEQonjO4NIo0yOGS90GpO7.2Se','2026-11-27 00:45:29',NULL,NULL,NULL,NULL,NULL,'生年月日8桁','11111111',1),(13,'test111@gmail.com','$2y$12$a12HZ4JD8/w3Y0uZMhaSh.0dxjAngZSTOhApmKtccmfEAYOJoBZ2C','2026-11-27 00:53:58',NULL,NULL,NULL,NULL,NULL,'生年月日8桁','20060215',0);
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

-- Dump completed on 2026-11-27 10:47:33
