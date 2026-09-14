-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: stationery
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admins`
--

LOCK TABLES `admins` WRITE;
/*!40000 ALTER TABLE `admins` DISABLE KEYS */;
INSERT INTO `admins` VALUES (1,'Admin','station','$2y$10$MS2VCux/KePJswC5OIaoCuQ792FsQJMvLfT2gi7d2cnflApclvzZq','0781234567','2026-07-20 19:34:01');
/*!40000 ALTER TABLE `admins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `book_classes`
--

DROP TABLE IF EXISTS `book_classes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `book_classes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `class_name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `book_classes`
--

LOCK TABLES `book_classes` WRITE;
/*!40000 ALTER TABLE `book_classes` DISABLE KEYS */;
INSERT INTO `book_classes` VALUES (1,'Nursery'),(2,'P1'),(3,'P2'),(4,'P3'),(5,'P4'),(6,'P5'),(7,'P6'),(8,'P7'),(9,'S1'),(10,'S2'),(11,'S3'),(12,'S4'),(13,'S5'),(14,'S6'),(15,'University');
/*!40000 ALTER TABLE `book_classes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cart`
--

DROP TABLE IF EXISTS `cart`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `customer_id` (`customer_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cart`
--

LOCK TABLES `cart` WRITE;
/*!40000 ALTER TABLE `cart` DISABLE KEYS */;
/*!40000 ALTER TABLE `cart` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (8,'Calculators','Scientific and basic calculators',NULL),(9,'Geometry Sets','Mathematical sets for students',NULL),(10,'Scissors & Cutters','Paper cutting tools',NULL),(11,'Glue & Tape','Paper glue, stick glue, cello tape, masking tape',NULL),(12,'Erasers','Rubber erasers',NULL),(13,'Crayons & Art Supplies','Water colors, crayons, paint brushes',NULL),(14,'Printing Papers','A4 Reams, A3, Photocopy papers',NULL),(15,'Envelopes','Brown envelopes, white envelopes',NULL),(16,'Office Supplies','General office materials','stapler_1784665182448.png'),(17,'School Bags','Bags for students','bag_large_1784665220432.png'),(18,'Books & Notebooks','All kinds of Books & Notebooks','notebook_1784665161965.png'),(19,'Writing Instruments','All kinds of Writing Instruments','pens_1784665171601.png'),(20,'Calculators & Electronics','All kinds of Calculators & Electronics','calculator_1784665209970.png'),(21,'Books','All kinds of Books',NULL),(22,'Pens','All kinds of Pens',NULL),(23,'Markers','All kinds of Markers',NULL),(24,'Bags','All kinds of Bags',NULL),(25,'Office','All kinds of Office',NULL);
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fullname` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customers`
--

LOCK TABLES `customers` WRITE;
/*!40000 ALTER TABLE `customers` DISABLE KEYS */;
INSERT INTO `customers` VALUES (1,'AYESIGA DAVIN','0771018311','ayesigadavin@gmail.com','$2y$10$g5Qjqf8F695B49JA0pHnluRMwFqi1/UcpIjVvx0FDQQ.TsiZfZlY2','mbarara','2026-07-21 19:48:05');
/*!40000 ALTER TABLE `customers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` VALUES (2,2,31,55000.00,1),(3,2,30,49000.00,2),(4,2,25,40000.00,1),(5,2,26,48000.00,1);
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `order_number` varchar(50) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_method` enum('Online','Pickup') NOT NULL,
  `payment_status` enum('Pending','Paid','Failed') NOT NULL DEFAULT 'Pending',
  `order_status` enum('Pending','Approved','Rejected','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
  `pickup_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `customer_id` (`customer_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,1,'ORD-20260721-6342',400000.00,'Pickup','Failed','Rejected','2026-07-22','2026-07-21 19:49:54'),(2,1,'ORD-20260721-1257',241000.00,'Pickup','Pending','Pending','2026-07-23','2026-07-21 21:26:14');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_settings`
--

DROP TABLE IF EXISTS `payment_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payment_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `phone_number` varchar(20) NOT NULL,
  `account_name` varchar(100) NOT NULL,
  `payment_type` varchar(50) NOT NULL,
  `instructions` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_settings`
--

LOCK TABLES `payment_settings` WRITE;
/*!40000 ALTER TABLE `payment_settings` DISABLE KEYS */;
INSERT INTO `payment_settings` VALUES (1,'0781234567','John Stationery','MTN Mobile Money','Send Payment To MTN Mobile Money and upload screenshot.');
/*!40000 ALTER TABLE `payment_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `class_id` int(11) DEFAULT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `product_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `wholesale_price` decimal(10,2) NOT NULL,
  `retail_price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `class_id` (`class_id`),
  KEY `subject_id` (`subject_id`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `products_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `book_classes` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `products_ibfk_3` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (3,18,NULL,NULL,'Premium Hardcover Notebook','High-quality, elegant hardcover notebook with thick pages, perfect for journaling or professional note-taking.',15000.00,20000.00,50,'notebook_1784665161965.png','Active','2026-07-21 20:37:15'),(4,19,NULL,NULL,'Elegant Pastel Gel Pens (Set of 6)','Smooth-writing gel pens in beautiful pastel shades, aesthetic and long-lasting.',8000.00,12000.00,100,'pens_1784665171601.png','Active','2026-07-21 20:37:15'),(5,19,NULL,NULL,'Colorful Highlighter Markers Set','Vibrant, non-bleeding highlighters for studying and organizing notes.',10000.00,15000.00,75,'markers_1784665192179.png','Active','2026-07-21 20:37:15'),(6,16,NULL,NULL,'Sleek Modern Office Stapler','Heavy-duty modern stapler for home, school, or office use. Jam-free design.',18000.00,25000.00,30,'stapler_1784665182448.png','Active','2026-07-21 20:37:15'),(7,20,NULL,NULL,'Advanced Scientific Calculator','Perfect for high school and college mathematics, durable and feature-packed.',45000.00,60000.00,20,'calculator_1784665209970.png','Active','2026-07-21 20:37:15'),(8,17,NULL,NULL,'Large Stylish Student Backpack','Spacious, water-resistant backpack suitable for high school or college students.',60000.00,85000.00,15,'bag_large_1784665220432.png','Active','2026-07-21 20:37:15'),(9,17,NULL,NULL,'Cute Kids Mini Backpack','Vibrant and adorable small backpack perfect for kindergarten and primary students.',40000.00,55000.00,25,'bag_small_1784665234479.png','Active','2026-07-21 20:37:15'),(10,21,NULL,NULL,'High School Mathematics Textbook','Comprehensive mathematics textbook with geometric designs, suitable for high school.',25000.00,35000.00,120,'book_math_1784667026687.png','Active','2026-07-21 20:58:04'),(11,21,NULL,NULL,'Advanced Biology & Chemistry Textbook','High school science textbook with biology and chemistry motifs.',28000.00,38000.00,80,'book_science_1784667036807.png','Active','2026-07-21 20:58:04'),(12,21,NULL,NULL,'World History Textbook','High school history textbook with vintage maps on the cover.',22000.00,30000.00,90,'book_history_1784667047086.png','Active','2026-07-21 20:58:04'),(13,21,NULL,NULL,'English Literature Textbook','English Literature high school textbook.',20000.00,28000.00,150,'book_english_1784667075385.png','Active','2026-07-21 20:58:04'),(14,22,NULL,NULL,'Luxury Gold-Trimmed Fountain Pen','Elegant luxury gold-trimmed fountain pen.',50000.00,75000.00,25,'pen_fountain_1784667057201.png','Active','2026-07-21 20:58:04'),(15,22,NULL,NULL,'Standard Office Ballpoint Pens (Pack)','Pack of standard office ballpoint pens.',5000.00,8000.00,200,'pen_ballpoint_1784667086394.png','Active','2026-07-21 20:58:04'),(20,8,NULL,NULL,'Basic Desktop Calculator','Simple and reliable calculator for daily use.',15000.00,20000.00,40,'calc_basic.jpg','Active','2026-07-21 21:01:36'),(21,8,NULL,NULL,'Financial Professional Calculator','Advanced calculator for accounting and finance professionals.',55000.00,80000.00,15,'calc_financial.jpg','Active','2026-07-21 21:01:36'),(23,21,NULL,NULL,'Advanced Biology Textbook Vol 1','Comprehensive textbook for high school biology students focusing on cellular biology.',30000.00,45000.00,50,'book_bio_1.jpg','Active','2026-07-21 21:05:02'),(24,21,NULL,NULL,'Human Anatomy & Physiology','Detailed textbook on human anatomy and physiology.',35000.00,50000.00,40,'book_bio_2.jpg','Active','2026-07-21 21:05:02'),(25,21,NULL,NULL,'Ecology & Environment Textbook','Exploring ecological principles and environmental biology.',28000.00,40000.00,59,'book_bio_3.jpg','Active','2026-07-21 21:05:02'),(26,21,NULL,NULL,'Fundamentals of Physics','Core concepts of classical and modern physics.',32000.00,48000.00,44,'book_physics_1.jpg','Active','2026-07-21 21:05:02'),(27,21,NULL,NULL,'Quantum Mechanics Introduction','An introductory guide to quantum mechanics and atomic physics.',40000.00,60000.00,25,'book_physics_2.jpg','Active','2026-07-21 21:05:02'),(28,21,NULL,NULL,'Physics: Optics and Waves','In-depth study of light, optics, and wave mechanics.',30000.00,42000.00,55,'book_physics_3.jpg','Active','2026-07-21 21:05:02'),(29,21,NULL,NULL,'Organic Chemistry Masterclass','Advanced textbook focusing on organic compounds and reactions.',35000.00,52000.00,35,'book_chem_1.jpg','Active','2026-07-21 21:05:02'),(30,21,NULL,NULL,'Inorganic Chemistry Guide','Comprehensive guide to inorganic chemistry principles.',33000.00,49000.00,38,'book_chem_2.jpg','Active','2026-07-21 21:05:02'),(31,21,NULL,NULL,'Physical Chemistry Textbook','Bridging the gap between physics and chemistry.',36000.00,55000.00,29,'book_chem_3.jpg','Active','2026-07-21 21:05:02');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subjects`
--

DROP TABLE IF EXISTS `subjects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subjects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject_name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subjects`
--

LOCK TABLES `subjects` WRITE;
/*!40000 ALTER TABLE `subjects` DISABLE KEYS */;
INSERT INTO `subjects` VALUES (1,'Mathematics'),(2,'English'),(3,'Physics'),(4,'Chemistry'),(5,'Biology'),(6,'ICT'),(7,'History'),(8,'Geography'),(9,'CRE'),(10,'Entrepreneurship'),(11,'Agriculture'),(12,'Literature'),(13,'Art');
/*!40000 ALTER TABLE `subjects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `reference` varchar(100) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` varchar(50) NOT NULL,
  `proof_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `customer_id` (`customer_id`),
  CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transactions`
--

LOCK TABLES `transactions` WRITE;
/*!40000 ALTER TABLE `transactions` DISABLE KEYS */;
/*!40000 ALTER TABLE `transactions` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-07-22 12:52:08
