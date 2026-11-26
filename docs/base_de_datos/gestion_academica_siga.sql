-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: 127.0.0.1    Database: gestion_academica
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
-- Current Database: `gestion_academica`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `gestion_academica` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;

USE `gestion_academica`;

--
-- Table structure for table `activity_logs`
--

DROP TABLE IF EXISTS `activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activity_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `action` varchar(60) NOT NULL,
  `description` varchar(255) NOT NULL,
  `subject_type` varchar(100) DEFAULT NULL,
  `subject_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `activity_logs_user_id_foreign` (`user_id`),
  CONSTRAINT `activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_logs`
--

LOCK TABLES `activity_logs` WRITE;
/*!40000 ALTER TABLE `activity_logs` DISABLE KEYS */;
INSERT INTO `activity_logs` VALUES (1,7,'inscripcion_creada','Alexis Morales Vargas se inscribió en MAT-101 (Intento #1).','App\\Models\\Enrollment',1,'2026-11-26 03:32:52','2026-11-26 03:32:52'),(2,8,'inscripcion_creada','Camila Torrez Justiniano se inscribió en MAT-101 (Intento #1).','App\\Models\\Enrollment',2,'2026-11-26 03:32:52','2026-11-26 03:32:52'),(3,9,'inscripcion_creada','Diego Fernández Roca se inscribió en MAT-101 (Intento #1).','App\\Models\\Enrollment',3,'2026-11-26 03:32:52','2026-11-26 03:32:52'),(4,7,'inscripcion_creada','Alexis Morales Vargas se inscribió en SIS-101 (Intento #1).','App\\Models\\Enrollment',4,'2026-11-26 03:32:52','2026-11-26 03:32:52'),(5,8,'inscripcion_creada','Camila Torrez Justiniano se inscribió en SIS-101 (Intento #1).','App\\Models\\Enrollment',5,'2026-11-26 03:32:52','2026-11-26 03:32:52'),(6,9,'inscripcion_creada','Diego Fernández Roca se inscribió en SIS-101 (Intento #1).','App\\Models\\Enrollment',6,'2026-11-26 03:32:52','2026-11-26 03:32:52'),(7,7,'inscripcion_creada','Alexis Morales Vargas se inscribió en LIN-101 (Intento #1).','App\\Models\\Enrollment',7,'2026-11-26 03:32:52','2026-11-26 03:32:52'),(8,8,'inscripcion_creada','Camila Torrez Justiniano se inscribió en LIN-101 (Intento #1).','App\\Models\\Enrollment',8,'2026-11-26 03:32:52','2026-11-26 03:32:52'),(9,7,'inscripcion_creada','Alexis Morales Vargas se inscribió en FIS-101 (Intento #1).','App\\Models\\Enrollment',9,'2026-11-26 03:32:53','2026-11-26 03:32:53'),(10,8,'inscripcion_creada','Camila Torrez Justiniano se inscribió en FIS-101 (Intento #1).','App\\Models\\Enrollment',10,'2026-11-26 03:32:53','2026-11-26 03:32:53'),(11,7,'inscripcion_creada','Alexis Morales Vargas se inscribió en INV-101 (Intento #1).','App\\Models\\Enrollment',11,'2026-11-26 03:32:53','2026-11-26 03:32:53'),(12,8,'inscripcion_creada','Camila Torrez Justiniano se inscribió en INV-101 (Intento #1).','App\\Models\\Enrollment',12,'2026-11-26 03:32:53','2026-11-26 03:32:53'),(13,7,'inscripcion_creada','Alexis Morales Vargas se inscribió en SIS-102 (Intento #1).','App\\Models\\Enrollment',13,'2026-11-26 03:32:53','2026-11-26 03:32:53'),(14,8,'inscripcion_creada','Camila Torrez Justiniano se inscribió en MAT-102 (Intento #1).','App\\Models\\Enrollment',14,'2026-11-26 03:32:53','2026-11-26 03:32:53'),(15,9,'inscripcion_creada','Diego Fernández Roca se inscribió en MAT-102 (Intento #1).','App\\Models\\Enrollment',15,'2026-11-26 03:32:53','2026-11-26 03:32:53'),(16,12,'inscripcion_creada','Lucía Chávez Vaca se inscribió en ADM-E101 (Intento #1).','App\\Models\\Enrollment',16,'2026-11-26 03:32:53','2026-11-26 03:32:53'),(17,13,'inscripcion_creada','Sebastián Ortiz Paz se inscribió en DER-J101 (Intento #1).','App\\Models\\Enrollment',17,'2026-11-26 03:32:53','2026-11-26 03:32:53'),(18,11,'inscripcion_creada','Sofía Zambrana Méndez se inscribió en GAS-101 (Intento #1).','App\\Models\\Enrollment',18,'2026-11-26 03:32:53','2026-11-26 03:32:53'),(19,1,'sistema_inicializado','Sistema modular configurado con 4 facultades, 8 carreras (48 materias c/u), horarios por turno y soporte de doble carrera.',NULL,NULL,'2026-11-26 03:32:53','2026-11-26 03:32:53'),(20,1,'inicio_sesion','Lic. Carlos Mendoza (Administrador) inició sesión.',NULL,NULL,'2026-11-26 03:36:42','2026-11-26 03:36:42'),(21,1,'cierre_sesion','Lic. Carlos Mendoza cerró sesión.',NULL,NULL,'2026-11-26 03:36:57','2026-11-26 03:36:57'),(22,7,'inicio_sesion','Alexis Morales Vargas (Estudiante) inició sesión.',NULL,NULL,'2026-11-26 03:37:00','2026-11-26 03:37:00'),(23,7,'cierre_sesion','Alexis Morales Vargas cerró sesión.',NULL,NULL,'2026-11-26 03:37:08','2026-11-26 03:37:08'),(24,2,'inicio_sesion','Ph.D. Luigi Antequera Tamari (Docente) inició sesión.',NULL,NULL,'2026-11-26 03:37:10','2026-11-26 03:37:10'),(25,1,'inicio_sesion','Lic. Carlos Mendoza (Administrador) inició sesión.',NULL,NULL,'2026-11-26 03:46:31','2026-11-26 03:46:31'),(26,1,'cierre_sesion','Lic. Carlos Mendoza cerró sesión.',NULL,NULL,'2026-11-26 04:08:24','2026-11-26 04:08:24'),(27,2,'inicio_sesion','Ph.D. Luigi Antequera Tamari (Docente) inició sesión.',NULL,NULL,'2026-11-26 04:08:33','2026-11-26 04:08:33');
/*!40000 ALTER TABLE `activity_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `app_notifications`
--

DROP TABLE IF EXISTS `app_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `app_notifications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `title` varchar(160) NOT NULL,
  `message` text NOT NULL,
  `type` varchar(30) NOT NULL DEFAULT 'info',
  `link` varchar(255) DEFAULT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `app_notifications_user_id_read_at_index` (`user_id`,`read_at`),
  CONSTRAINT `app_notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `app_notifications`
--

LOCK TABLES `app_notifications` WRITE;
/*!40000 ALTER TABLE `app_notifications` DISABLE KEYS */;
INSERT INTO `app_notifications` VALUES (1,7,'Inscripción confirmada','Te inscribiste en MAT-101 - Cálculo I (Intento #1, Enero · turno Mañana, Aula 101).','inscripcion','http://127.0.0.1:8000/estudiante/mis-materias/1',NULL,'2026-11-26 03:32:52','2026-11-26 03:32:52'),(2,2,'Nuevo estudiante inscrito','Alexis Morales Vargas se inscribió en tu oferta de MAT-101 (Enero · Mañana).','inscripcion','http://127.0.0.1:8000/docente/ofertas/1',NULL,'2026-11-26 03:32:52','2026-11-26 03:32:52'),(3,8,'Inscripción confirmada','Te inscribiste en MAT-101 - Cálculo I (Intento #1, Enero · turno Mañana, Aula 101).','inscripcion','http://127.0.0.1:8000/estudiante/mis-materias/2',NULL,'2026-11-26 03:32:52','2026-11-26 03:32:52'),(4,2,'Nuevo estudiante inscrito','Camila Torrez Justiniano se inscribió en tu oferta de MAT-101 (Enero · Mañana).','inscripcion','http://127.0.0.1:8000/docente/ofertas/1',NULL,'2026-11-26 03:32:52','2026-11-26 03:32:52'),(5,9,'Inscripción confirmada','Te inscribiste en MAT-101 - Cálculo I (Intento #1, Enero · turno Mañana, Aula 101).','inscripcion','http://127.0.0.1:8000/estudiante/mis-materias/3',NULL,'2026-11-26 03:32:52','2026-11-26 03:32:52'),(6,2,'Nuevo estudiante inscrito','Diego Fernández Roca se inscribió en tu oferta de MAT-101 (Enero · Mañana).','inscripcion','http://127.0.0.1:8000/docente/ofertas/1',NULL,'2026-11-26 03:32:52','2026-11-26 03:32:52'),(7,7,'Inscripción confirmada','Te inscribiste en SIS-101 - Introducción a la Programación (Intento #1, Febrero · turno Mañana, Aula 102).','inscripcion','http://127.0.0.1:8000/estudiante/mis-materias/4',NULL,'2026-11-26 03:32:52','2026-11-26 03:32:52'),(8,2,'Nuevo estudiante inscrito','Alexis Morales Vargas se inscribió en tu oferta de SIS-101 (Febrero · Mañana).','inscripcion','http://127.0.0.1:8000/docente/ofertas/2',NULL,'2026-11-26 03:32:52','2026-11-26 03:32:52'),(9,8,'Inscripción confirmada','Te inscribiste en SIS-101 - Introducción a la Programación (Intento #1, Febrero · turno Mañana, Aula 102).','inscripcion','http://127.0.0.1:8000/estudiante/mis-materias/5',NULL,'2026-11-26 03:32:52','2026-11-26 03:32:52'),(10,2,'Nuevo estudiante inscrito','Camila Torrez Justiniano se inscribió en tu oferta de SIS-101 (Febrero · Mañana).','inscripcion','http://127.0.0.1:8000/docente/ofertas/2',NULL,'2026-11-26 03:32:52','2026-11-26 03:32:52'),(11,9,'Inscripción confirmada','Te inscribiste en SIS-101 - Introducción a la Programación (Intento #1, Febrero · turno Mañana, Aula 102).','inscripcion','http://127.0.0.1:8000/estudiante/mis-materias/6',NULL,'2026-11-26 03:32:52','2026-11-26 03:32:52'),(12,2,'Nuevo estudiante inscrito','Diego Fernández Roca se inscribió en tu oferta de SIS-101 (Febrero · Mañana).','inscripcion','http://127.0.0.1:8000/docente/ofertas/2',NULL,'2026-11-26 03:32:52','2026-11-26 03:32:52'),(13,7,'Inscripción confirmada','Te inscribiste en LIN-101 - Inglés Técnico I (Intento #1, Marzo · turno Tarde, Aula 103).','inscripcion','http://127.0.0.1:8000/estudiante/mis-materias/7',NULL,'2026-11-26 03:32:52','2026-11-26 03:32:52'),(14,3,'Nuevo estudiante inscrito','Alexis Morales Vargas se inscribió en tu oferta de LIN-101 (Marzo · Tarde).','inscripcion','http://127.0.0.1:8000/docente/ofertas/3',NULL,'2026-11-26 03:32:52','2026-11-26 03:32:52'),(15,8,'Inscripción confirmada','Te inscribiste en LIN-101 - Inglés Técnico I (Intento #1, Marzo · turno Tarde, Aula 103).','inscripcion','http://127.0.0.1:8000/estudiante/mis-materias/8',NULL,'2026-11-26 03:32:52','2026-11-26 03:32:52'),(16,3,'Nuevo estudiante inscrito','Camila Torrez Justiniano se inscribió en tu oferta de LIN-101 (Marzo · Tarde).','inscripcion','http://127.0.0.1:8000/docente/ofertas/3',NULL,'2026-11-26 03:32:52','2026-11-26 03:32:52'),(17,7,'Inscripción confirmada','Te inscribiste en FIS-101 - Física General I (Intento #1, Abril · turno Mañana, Aula 101).','inscripcion','http://127.0.0.1:8000/estudiante/mis-materias/9',NULL,'2026-11-26 03:32:53','2026-11-26 03:32:53'),(18,3,'Nuevo estudiante inscrito','Alexis Morales Vargas se inscribió en tu oferta de FIS-101 (Abril · Mañana).','inscripcion','http://127.0.0.1:8000/docente/ofertas/4',NULL,'2026-11-26 03:32:53','2026-11-26 03:32:53'),(19,8,'Inscripción confirmada','Te inscribiste en FIS-101 - Física General I (Intento #1, Abril · turno Mañana, Aula 101).','inscripcion','http://127.0.0.1:8000/estudiante/mis-materias/10',NULL,'2026-11-26 03:32:53','2026-11-26 03:32:53'),(20,3,'Nuevo estudiante inscrito','Camila Torrez Justiniano se inscribió en tu oferta de FIS-101 (Abril · Mañana).','inscripcion','http://127.0.0.1:8000/docente/ofertas/4',NULL,'2026-11-26 03:32:53','2026-11-26 03:32:53'),(21,7,'Inscripción confirmada','Te inscribiste en INV-101 - Metodología de la Investigación (Intento #1, Mayo · turno Noche, Aula 104).','inscripcion','http://127.0.0.1:8000/estudiante/mis-materias/11',NULL,'2026-11-26 03:32:53','2026-11-26 03:32:53'),(22,3,'Nuevo estudiante inscrito','Alexis Morales Vargas se inscribió en tu oferta de INV-101 (Mayo · Noche).','inscripcion','http://127.0.0.1:8000/docente/ofertas/5',NULL,'2026-11-26 03:32:53','2026-11-26 03:32:53'),(23,8,'Inscripción confirmada','Te inscribiste en INV-101 - Metodología de la Investigación (Intento #1, Mayo · turno Noche, Aula 104).','inscripcion','http://127.0.0.1:8000/estudiante/mis-materias/12',NULL,'2026-11-26 03:32:53','2026-11-26 03:32:53'),(24,3,'Nuevo estudiante inscrito','Camila Torrez Justiniano se inscribió en tu oferta de INV-101 (Mayo · Noche).','inscripcion','http://127.0.0.1:8000/docente/ofertas/5',NULL,'2026-11-26 03:32:53','2026-11-26 03:32:53'),(25,7,'Inscripción confirmada','Te inscribiste en SIS-102 - Programación Orientada a Objetos (Intento #1, Noviembre · turno Mañana, Aula 201).','inscripcion','http://127.0.0.1:8000/estudiante/mis-materias/13',NULL,'2026-11-26 03:32:53','2026-11-26 03:32:53'),(26,2,'Nuevo estudiante inscrito','Alexis Morales Vargas se inscribió en tu oferta de SIS-102 (Noviembre · Mañana).','inscripcion','http://127.0.0.1:8000/docente/ofertas/6',NULL,'2026-11-26 03:32:53','2026-11-26 03:32:53'),(27,8,'Inscripción confirmada','Te inscribiste en MAT-102 - Álgebra Lineal (Intento #1, Noviembre · turno Tarde, Aula 201).','inscripcion','http://127.0.0.1:8000/estudiante/mis-materias/14',NULL,'2026-11-26 03:32:53','2026-11-26 03:32:53'),(28,2,'Nuevo estudiante inscrito','Camila Torrez Justiniano se inscribió en tu oferta de MAT-102 (Noviembre · Tarde).','inscripcion','http://127.0.0.1:8000/docente/ofertas/7',NULL,'2026-11-26 03:32:53','2026-11-26 03:32:53'),(29,9,'Inscripción confirmada','Te inscribiste en MAT-102 - Álgebra Lineal (Intento #1, Noviembre · turno Tarde, Aula 201).','inscripcion','http://127.0.0.1:8000/estudiante/mis-materias/15',NULL,'2026-11-26 03:32:53','2026-11-26 03:32:53'),(30,2,'Nuevo estudiante inscrito','Diego Fernández Roca se inscribió en tu oferta de MAT-102 (Noviembre · Tarde).','inscripcion','http://127.0.0.1:8000/docente/ofertas/7',NULL,'2026-11-26 03:32:53','2026-11-26 03:32:53'),(31,12,'Inscripción confirmada','Te inscribiste en ADM-E101 - Administración General I (Intento #1, Noviembre · turno Mañana, Aula 401).','inscripcion','http://127.0.0.1:8000/estudiante/mis-materias/16',NULL,'2026-11-26 03:32:53','2026-11-26 03:32:53'),(32,4,'Nuevo estudiante inscrito','Lucía Chávez Vaca se inscribió en tu oferta de ADM-E101 (Noviembre · Mañana).','inscripcion','http://127.0.0.1:8000/docente/ofertas/9',NULL,'2026-11-26 03:32:53','2026-11-26 03:32:53'),(33,13,'Inscripción confirmada','Te inscribiste en DER-J101 - Introducción al Derecho y Estado (Intento #1, Noviembre · turno Tarde, Aula 501).','inscripcion','http://127.0.0.1:8000/estudiante/mis-materias/17',NULL,'2026-11-26 03:32:53','2026-11-26 03:32:53'),(34,5,'Nuevo estudiante inscrito','Sebastián Ortiz Paz se inscribió en tu oferta de DER-J101 (Noviembre · Tarde).','inscripcion','http://127.0.0.1:8000/docente/ofertas/11',NULL,'2026-11-26 03:32:53','2026-11-26 03:32:53'),(35,11,'Inscripción confirmada','Te inscribiste en GAS-101 - Técnicas Culinarias Básicas I (Intento #1, Noviembre · turno Mañana, Aula 301).','inscripcion','http://127.0.0.1:8000/estudiante/mis-materias/18',NULL,'2026-11-26 03:32:53','2026-11-26 03:32:53'),(36,6,'Nuevo estudiante inscrito','Sofía Zambrana Méndez se inscribió en tu oferta de GAS-101 (Noviembre · Mañana).','inscripcion','http://127.0.0.1:8000/docente/ofertas/12',NULL,'2026-11-26 03:32:53','2026-11-26 03:32:53');
/*!40000 ALTER TABLE `app_notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` bigint(20) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` bigint(20) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `careers`
--

DROP TABLE IF EXISTS `careers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `careers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `faculty_id` bigint(20) unsigned NOT NULL,
  `code` varchar(20) NOT NULL,
  `name` varchar(160) NOT NULL,
  `shifts` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`shifts`)),
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `careers_code_unique` (`code`),
  KEY `careers_faculty_id_foreign` (`faculty_id`),
  CONSTRAINT `careers_faculty_id_foreign` FOREIGN KEY (`faculty_id`) REFERENCES `faculties` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `careers`
--

LOCK TABLES `careers` WRITE;
/*!40000 ALTER TABLE `careers` DISABLE KEYS */;
INSERT INTO `careers` VALUES (1,1,'SIS','Ingeniería de Sistemas','[\"manana\",\"tarde\",\"noche\"]','Plan modular de 48 materias en desarrollo y arquitectura de software.',1,'2026-11-26 03:32:37','2026-11-26 03:32:37'),(2,1,'TEL','Ingeniería de Telecomunicaciones','[\"manana\",\"tarde\",\"noche\"]','Plan modular de 48 materias en redes y telecomunicaciones.',1,'2026-11-26 03:32:37','2026-11-26 03:32:37'),(3,1,'IND','Ingeniería Industrial','[\"manana\",\"tarde\",\"noche\"]','Plan modular de 48 materias en producción, calidad y logística.',1,'2026-11-26 03:32:37','2026-11-26 03:32:37'),(4,2,'ADM','Administración de Empresas','[\"manana\",\"tarde\",\"noche\"]','Plan modular de 48 materias en gestión estratégica y negocios.',1,'2026-11-26 03:32:37','2026-11-26 03:32:37'),(5,2,'CPA','Contaduría Pública','[\"manana\",\"tarde\",\"noche\"]','Plan modular de 48 materias en auditoría, tributación y finanzas.',1,'2026-11-26 03:32:37','2026-11-26 03:32:37'),(6,3,'DER','Derecho','[\"manana\",\"tarde\",\"noche\"]','Plan modular de 48 materias en ciencias jurídicas.',1,'2026-11-26 03:32:37','2026-11-26 03:32:37'),(7,3,'PSI','Psicología','[\"manana\",\"tarde\"]','Plan modular de 48 materias en psicología clínica y organizacional.',1,'2026-11-26 03:32:37','2026-11-26 03:32:37'),(8,4,'GAS','Gastronomía','[\"manana\",\"tarde\"]','Plan modular de 48 materias en alta cocina y nutrición.',1,'2026-11-26 03:32:37','2026-11-26 03:32:37');
/*!40000 ALTER TABLE `careers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `curriculum_items`
--

DROP TABLE IF EXISTS `curriculum_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `curriculum_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `career_id` bigint(20) unsigned NOT NULL,
  `subject_id` bigint(20) unsigned NOT NULL,
  `level` tinyint(3) unsigned NOT NULL,
  `month_in_level` tinyint(3) unsigned NOT NULL,
  `order_in_curriculum` tinyint(3) unsigned NOT NULL,
  `is_elective` tinyint(1) NOT NULL DEFAULT 0,
  `elective_number` tinyint(3) unsigned DEFAULT NULL,
  `requires_full_level` tinyint(3) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `curriculum_career_subject_unique` (`career_id`,`subject_id`),
  KEY `curriculum_items_subject_id_foreign` (`subject_id`),
  KEY `curriculum_items_career_id_level_month_in_level_index` (`career_id`,`level`,`month_in_level`),
  CONSTRAINT `curriculum_items_career_id_foreign` FOREIGN KEY (`career_id`) REFERENCES `careers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `curriculum_items_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=385 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `curriculum_items`
--

LOCK TABLES `curriculum_items` WRITE;
/*!40000 ALTER TABLE `curriculum_items` DISABLE KEYS */;
INSERT INTO `curriculum_items` VALUES (1,1,1,1,1,1,0,NULL,NULL,'2026-11-26 03:32:37','2026-11-26 03:32:37'),(2,1,2,1,2,2,0,NULL,NULL,'2026-11-26 03:32:37','2026-11-26 03:32:37'),(3,1,3,1,3,3,0,NULL,NULL,'2026-11-26 03:32:37','2026-11-26 03:32:37'),(4,1,4,1,4,4,0,NULL,NULL,'2026-11-26 03:32:37','2026-11-26 03:32:37'),(5,1,5,1,5,5,0,NULL,NULL,'2026-11-26 03:32:37','2026-11-26 03:32:37'),(6,1,6,1,6,6,0,NULL,NULL,'2026-11-26 03:32:37','2026-11-26 03:32:37'),(7,1,7,2,1,7,0,NULL,NULL,'2026-11-26 03:32:37','2026-11-26 03:32:37'),(8,1,8,2,2,8,0,NULL,NULL,'2026-11-26 03:32:37','2026-11-26 03:32:37'),(9,1,9,2,3,9,0,NULL,NULL,'2026-11-26 03:32:37','2026-11-26 03:32:37'),(10,1,10,2,4,10,0,NULL,NULL,'2026-11-26 03:32:37','2026-11-26 03:32:37'),(11,1,11,2,5,11,0,NULL,NULL,'2026-11-26 03:32:37','2026-11-26 03:32:37'),(12,1,12,2,6,12,0,NULL,NULL,'2026-11-26 03:32:37','2026-11-26 03:32:37'),(13,1,13,3,1,13,0,NULL,NULL,'2026-11-26 03:32:37','2026-11-26 03:32:37'),(14,1,14,3,2,14,0,NULL,NULL,'2026-11-26 03:32:37','2026-11-26 03:32:37'),(15,1,15,3,3,15,0,NULL,NULL,'2026-11-26 03:32:37','2026-11-26 03:32:37'),(16,1,16,3,4,16,0,NULL,NULL,'2026-11-26 03:32:37','2026-11-26 03:32:37'),(17,1,17,3,5,17,0,NULL,NULL,'2026-11-26 03:32:37','2026-11-26 03:32:37'),(18,1,18,3,6,18,0,NULL,NULL,'2026-11-26 03:32:37','2026-11-26 03:32:37'),(19,1,19,4,1,19,0,NULL,NULL,'2026-11-26 03:32:37','2026-11-26 03:32:37'),(20,1,20,4,2,20,0,NULL,NULL,'2026-11-26 03:32:37','2026-11-26 03:32:37'),(21,1,21,4,3,21,0,NULL,NULL,'2026-11-26 03:32:37','2026-11-26 03:32:37'),(22,1,22,4,4,22,0,NULL,NULL,'2026-11-26 03:32:37','2026-11-26 03:32:37'),(23,1,23,4,5,23,0,NULL,NULL,'2026-11-26 03:32:37','2026-11-26 03:32:37'),(24,1,24,4,6,24,0,NULL,NULL,'2026-11-26 03:32:37','2026-11-26 03:32:37'),(25,1,25,5,1,25,0,NULL,NULL,'2026-11-26 03:32:37','2026-11-26 03:32:37'),(26,1,26,5,2,26,0,NULL,NULL,'2026-11-26 03:32:37','2026-11-26 03:32:37'),(27,1,27,5,3,27,0,NULL,NULL,'2026-11-26 03:32:37','2026-11-26 03:32:37'),(28,1,28,5,4,28,0,NULL,NULL,'2026-11-26 03:32:37','2026-11-26 03:32:37'),(29,1,29,5,5,29,0,NULL,NULL,'2026-11-26 03:32:37','2026-11-26 03:32:37'),(30,1,30,5,6,30,0,NULL,NULL,'2026-11-26 03:32:38','2026-11-26 03:32:38'),(31,1,31,6,1,31,0,NULL,NULL,'2026-11-26 03:32:38','2026-11-26 03:32:38'),(32,1,32,6,2,32,0,NULL,NULL,'2026-11-26 03:32:38','2026-11-26 03:32:38'),(33,1,33,6,3,33,0,NULL,NULL,'2026-11-26 03:32:38','2026-11-26 03:32:38'),(34,1,34,6,4,34,0,NULL,NULL,'2026-11-26 03:32:38','2026-11-26 03:32:38'),(35,1,35,6,5,35,0,NULL,NULL,'2026-11-26 03:32:38','2026-11-26 03:32:38'),(36,1,36,6,6,36,1,1,NULL,'2026-11-26 03:32:38','2026-11-26 03:32:38'),(37,1,37,7,1,37,0,NULL,NULL,'2026-11-26 03:32:38','2026-11-26 03:32:38'),(38,1,38,7,2,38,0,NULL,NULL,'2026-11-26 03:32:38','2026-11-26 03:32:38'),(39,1,39,7,3,39,0,NULL,NULL,'2026-11-26 03:32:38','2026-11-26 03:32:38'),(40,1,40,7,4,40,0,NULL,NULL,'2026-11-26 03:32:38','2026-11-26 03:32:38'),(41,1,41,7,5,41,1,2,NULL,'2026-11-26 03:32:38','2026-11-26 03:32:38'),(42,1,42,7,6,42,1,3,NULL,'2026-11-26 03:32:38','2026-11-26 03:32:38'),(43,1,43,8,1,43,0,NULL,NULL,'2026-11-26 03:32:38','2026-11-26 03:32:38'),(44,1,44,8,2,44,0,NULL,NULL,'2026-11-26 03:32:38','2026-11-26 03:32:38'),(45,1,45,8,3,45,0,NULL,NULL,'2026-11-26 03:32:38','2026-11-26 03:32:38'),(46,1,46,8,4,46,1,4,NULL,'2026-11-26 03:32:38','2026-11-26 03:32:38'),(47,1,47,8,5,47,0,NULL,6,'2026-11-26 03:32:38','2026-11-26 03:32:38'),(48,1,48,8,6,48,0,NULL,7,'2026-11-26 03:32:38','2026-11-26 03:32:38'),(49,2,1,1,1,1,0,NULL,NULL,'2026-11-26 03:32:38','2026-11-26 03:32:38'),(50,2,6,1,2,2,0,NULL,NULL,'2026-11-26 03:32:38','2026-11-26 03:32:38'),(51,2,3,1,3,3,0,NULL,NULL,'2026-11-26 03:32:38','2026-11-26 03:32:38'),(52,2,4,1,4,4,0,NULL,NULL,'2026-11-26 03:32:38','2026-11-26 03:32:38'),(53,2,5,1,5,5,0,NULL,NULL,'2026-11-26 03:32:38','2026-11-26 03:32:38'),(54,2,2,1,6,6,0,NULL,NULL,'2026-11-26 03:32:38','2026-11-26 03:32:38'),(55,2,7,2,1,7,0,NULL,NULL,'2026-11-26 03:32:38','2026-11-26 03:32:38'),(56,2,49,2,2,8,0,NULL,NULL,'2026-11-26 03:32:38','2026-11-26 03:32:38'),(57,2,9,2,3,9,0,NULL,NULL,'2026-11-26 03:32:38','2026-11-26 03:32:38'),(58,2,10,2,4,10,0,NULL,NULL,'2026-11-26 03:32:38','2026-11-26 03:32:38'),(59,2,11,2,5,11,0,NULL,NULL,'2026-11-26 03:32:38','2026-11-26 03:32:38'),(60,2,50,2,6,12,0,NULL,NULL,'2026-11-26 03:32:38','2026-11-26 03:32:38'),(61,2,13,3,1,13,0,NULL,NULL,'2026-11-26 03:32:38','2026-11-26 03:32:38'),(62,2,51,3,2,14,0,NULL,NULL,'2026-11-26 03:32:38','2026-11-26 03:32:38'),(63,2,52,3,3,15,0,NULL,NULL,'2026-11-26 03:32:38','2026-11-26 03:32:38'),(64,2,16,3,4,16,0,NULL,NULL,'2026-11-26 03:32:38','2026-11-26 03:32:38'),(65,2,17,3,5,17,0,NULL,NULL,'2026-11-26 03:32:38','2026-11-26 03:32:38'),(66,2,18,3,6,18,0,NULL,NULL,'2026-11-26 03:32:38','2026-11-26 03:32:38'),(67,2,19,4,1,19,0,NULL,NULL,'2026-11-26 03:32:38','2026-11-26 03:32:38'),(68,2,53,4,2,20,0,NULL,NULL,'2026-11-26 03:32:38','2026-11-26 03:32:38'),(69,2,54,4,3,21,0,NULL,NULL,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(70,2,22,4,4,22,0,NULL,NULL,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(71,2,55,4,5,23,0,NULL,NULL,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(72,2,56,4,6,24,0,NULL,NULL,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(73,2,57,5,1,25,0,NULL,NULL,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(74,2,58,5,2,26,0,NULL,NULL,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(75,2,27,5,3,27,0,NULL,NULL,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(76,2,59,5,4,28,0,NULL,NULL,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(77,2,60,5,5,29,0,NULL,NULL,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(78,2,30,5,6,30,0,NULL,NULL,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(79,2,61,6,1,31,0,NULL,NULL,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(80,2,62,6,2,32,0,NULL,NULL,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(81,2,33,6,3,33,0,NULL,NULL,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(82,2,63,6,4,34,0,NULL,NULL,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(83,2,35,6,5,35,0,NULL,NULL,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(84,2,36,6,6,36,1,1,NULL,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(85,2,64,7,1,37,0,NULL,NULL,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(86,2,65,7,2,38,0,NULL,NULL,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(87,2,66,7,3,39,0,NULL,NULL,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(88,2,40,7,4,40,0,NULL,NULL,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(89,2,41,7,5,41,1,2,NULL,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(90,2,42,7,6,42,1,3,NULL,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(91,2,67,8,1,43,0,NULL,NULL,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(92,2,68,8,2,44,0,NULL,NULL,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(93,2,69,8,3,45,0,NULL,NULL,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(94,2,46,8,4,46,1,4,NULL,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(95,2,47,8,5,47,0,NULL,6,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(96,2,48,8,6,48,0,NULL,7,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(97,3,1,1,1,1,0,NULL,NULL,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(98,3,70,1,2,2,0,NULL,NULL,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(99,3,3,1,3,3,0,NULL,NULL,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(100,3,4,1,4,4,0,NULL,NULL,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(101,3,5,1,5,5,0,NULL,NULL,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(102,3,2,1,6,6,0,NULL,NULL,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(103,3,7,2,1,7,0,NULL,NULL,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(104,3,71,2,2,8,0,NULL,NULL,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(105,3,9,2,3,9,0,NULL,NULL,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(106,3,10,2,4,10,0,NULL,NULL,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(107,3,11,2,5,11,0,NULL,NULL,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(108,3,72,2,6,12,0,NULL,NULL,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(109,3,13,3,1,13,0,NULL,NULL,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(110,3,73,3,2,14,0,NULL,NULL,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(111,3,74,3,3,15,0,NULL,NULL,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(112,3,75,3,4,16,0,NULL,NULL,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(113,3,17,3,5,17,0,NULL,NULL,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(114,3,18,3,6,18,0,NULL,NULL,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(115,3,76,4,1,19,0,NULL,NULL,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(116,3,77,4,2,20,0,NULL,NULL,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(117,3,78,4,3,21,0,NULL,NULL,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(118,3,79,4,4,22,0,NULL,NULL,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(119,3,80,4,5,23,0,NULL,NULL,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(120,3,81,4,6,24,0,NULL,NULL,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(121,3,82,5,1,25,0,NULL,NULL,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(122,3,83,5,2,26,0,NULL,NULL,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(123,3,84,5,3,27,0,NULL,NULL,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(124,3,85,5,4,28,0,NULL,NULL,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(125,3,86,5,5,29,0,NULL,NULL,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(126,3,87,5,6,30,0,NULL,NULL,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(127,3,88,6,1,31,0,NULL,NULL,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(128,3,89,6,2,32,0,NULL,NULL,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(129,3,90,6,3,33,0,NULL,NULL,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(130,3,91,6,4,34,0,NULL,NULL,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(131,3,92,6,5,35,0,NULL,NULL,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(132,3,93,6,6,36,1,1,NULL,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(133,3,94,7,1,37,0,NULL,NULL,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(134,3,95,7,2,38,0,NULL,NULL,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(135,3,96,7,3,39,0,NULL,NULL,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(136,3,97,7,4,40,0,NULL,NULL,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(137,3,98,7,5,41,1,2,NULL,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(138,3,99,7,6,42,1,3,NULL,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(139,3,100,8,1,43,0,NULL,NULL,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(140,3,101,8,2,44,0,NULL,NULL,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(141,3,102,8,3,45,0,NULL,NULL,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(142,3,103,8,4,46,1,4,NULL,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(143,3,47,8,5,47,0,NULL,6,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(144,3,48,8,6,48,0,NULL,7,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(145,4,104,1,1,1,0,NULL,NULL,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(146,4,105,1,2,2,0,NULL,NULL,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(147,4,106,1,3,3,0,NULL,NULL,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(148,4,107,1,4,4,0,NULL,NULL,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(149,4,108,1,5,5,0,NULL,NULL,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(150,4,109,1,6,6,0,NULL,NULL,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(151,4,110,2,1,7,0,NULL,NULL,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(152,4,111,2,2,8,0,NULL,NULL,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(153,4,112,2,3,9,0,NULL,NULL,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(154,4,113,2,4,10,0,NULL,NULL,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(155,4,114,2,5,11,0,NULL,NULL,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(156,4,115,2,6,12,0,NULL,NULL,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(157,4,116,3,1,13,0,NULL,NULL,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(158,4,117,3,2,14,0,NULL,NULL,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(159,4,118,3,3,15,0,NULL,NULL,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(160,4,119,3,4,16,0,NULL,NULL,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(161,4,120,3,5,17,0,NULL,NULL,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(162,4,121,3,6,18,0,NULL,NULL,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(163,4,122,4,1,19,0,NULL,NULL,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(164,4,123,4,2,20,0,NULL,NULL,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(165,4,124,4,3,21,0,NULL,NULL,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(166,4,125,4,4,22,0,NULL,NULL,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(167,4,126,4,5,23,0,NULL,NULL,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(168,4,127,4,6,24,0,NULL,NULL,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(169,4,128,5,1,25,0,NULL,NULL,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(170,4,129,5,2,26,0,NULL,NULL,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(171,4,130,5,3,27,0,NULL,NULL,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(172,4,131,5,4,28,0,NULL,NULL,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(173,4,132,5,5,29,0,NULL,NULL,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(174,4,133,5,6,30,0,NULL,NULL,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(175,4,134,6,1,31,0,NULL,NULL,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(176,4,135,6,2,32,0,NULL,NULL,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(177,4,136,6,3,33,0,NULL,NULL,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(178,4,137,6,4,34,0,NULL,NULL,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(179,4,138,6,5,35,0,NULL,NULL,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(180,4,139,6,6,36,1,1,NULL,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(181,4,140,7,1,37,0,NULL,NULL,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(182,4,141,7,2,38,0,NULL,NULL,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(183,4,142,7,3,39,0,NULL,NULL,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(184,4,143,7,4,40,0,NULL,NULL,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(185,4,144,7,5,41,1,2,NULL,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(186,4,145,7,6,42,1,3,NULL,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(187,4,146,8,1,43,0,NULL,NULL,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(188,4,147,8,2,44,0,NULL,NULL,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(189,4,148,8,3,45,0,NULL,NULL,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(190,4,149,8,4,46,1,4,NULL,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(191,4,150,8,5,47,0,NULL,6,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(192,4,151,8,6,48,0,NULL,7,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(193,5,104,1,1,1,0,NULL,NULL,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(194,5,105,1,2,2,0,NULL,NULL,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(195,5,106,1,3,3,0,NULL,NULL,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(196,5,107,1,4,4,0,NULL,NULL,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(197,5,108,1,5,5,0,NULL,NULL,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(198,5,109,1,6,6,0,NULL,NULL,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(199,5,110,2,1,7,0,NULL,NULL,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(200,5,111,2,2,8,0,NULL,NULL,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(201,5,112,2,3,9,0,NULL,NULL,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(202,5,113,2,4,10,0,NULL,NULL,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(203,5,114,2,5,11,0,NULL,NULL,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(204,5,115,2,6,12,0,NULL,NULL,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(205,5,116,3,1,13,0,NULL,NULL,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(206,5,117,3,2,14,0,NULL,NULL,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(207,5,118,3,3,15,0,NULL,NULL,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(208,5,119,3,4,16,0,NULL,NULL,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(209,5,120,3,5,17,0,NULL,NULL,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(210,5,121,3,6,18,0,NULL,NULL,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(211,5,152,4,1,19,0,NULL,NULL,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(212,5,153,4,2,20,0,NULL,NULL,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(213,5,154,4,3,21,0,NULL,NULL,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(214,5,155,4,4,22,0,NULL,NULL,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(215,5,156,4,5,23,0,NULL,NULL,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(216,5,157,4,6,24,0,NULL,NULL,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(217,5,158,5,1,25,0,NULL,NULL,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(218,5,159,5,2,26,0,NULL,NULL,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(219,5,160,5,3,27,0,NULL,NULL,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(220,5,161,5,4,28,0,NULL,NULL,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(221,5,162,5,5,29,0,NULL,NULL,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(222,5,163,5,6,30,0,NULL,NULL,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(223,5,164,6,1,31,0,NULL,NULL,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(224,5,165,6,2,32,0,NULL,NULL,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(225,5,166,6,3,33,0,NULL,NULL,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(226,5,167,6,4,34,0,NULL,NULL,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(227,5,168,6,5,35,0,NULL,NULL,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(228,5,139,6,6,36,1,1,NULL,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(229,5,169,7,1,37,0,NULL,NULL,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(230,5,170,7,2,38,0,NULL,NULL,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(231,5,171,7,3,39,0,NULL,NULL,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(232,5,172,7,4,40,0,NULL,NULL,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(233,5,144,7,5,41,1,2,NULL,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(234,5,145,7,6,42,1,3,NULL,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(235,5,173,8,1,43,0,NULL,NULL,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(236,5,174,8,2,44,0,NULL,NULL,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(237,5,175,8,3,45,0,NULL,NULL,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(238,5,149,8,4,46,1,4,NULL,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(239,5,150,8,5,47,0,NULL,6,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(240,5,151,8,6,48,0,NULL,7,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(241,6,176,1,1,1,0,NULL,NULL,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(242,6,177,1,2,2,0,NULL,NULL,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(243,6,178,1,3,3,0,NULL,NULL,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(244,6,179,1,4,4,0,NULL,NULL,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(245,6,180,1,5,5,0,NULL,NULL,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(246,6,181,1,6,6,0,NULL,NULL,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(247,6,182,2,1,7,0,NULL,NULL,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(248,6,183,2,2,8,0,NULL,NULL,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(249,6,184,2,3,9,0,NULL,NULL,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(250,6,185,2,4,10,0,NULL,NULL,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(251,6,186,2,5,11,0,NULL,NULL,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(252,6,187,2,6,12,0,NULL,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(253,6,188,3,1,13,0,NULL,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(254,6,189,3,2,14,0,NULL,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(255,6,190,3,3,15,0,NULL,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(256,6,191,3,4,16,0,NULL,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(257,6,192,3,5,17,0,NULL,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(258,6,193,3,6,18,0,NULL,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(259,6,194,4,1,19,0,NULL,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(260,6,195,4,2,20,0,NULL,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(261,6,196,4,3,21,0,NULL,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(262,6,197,4,4,22,0,NULL,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(263,6,198,4,5,23,0,NULL,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(264,6,199,4,6,24,0,NULL,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(265,6,200,5,1,25,0,NULL,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(266,6,201,5,2,26,0,NULL,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(267,6,202,5,3,27,0,NULL,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(268,6,203,5,4,28,0,NULL,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(269,6,204,5,5,29,0,NULL,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(270,6,205,5,6,30,0,NULL,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(271,6,206,6,1,31,0,NULL,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(272,6,207,6,2,32,0,NULL,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(273,6,208,6,3,33,0,NULL,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(274,6,209,6,4,34,0,NULL,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(275,6,210,6,5,35,0,NULL,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(276,6,211,6,6,36,1,1,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(277,6,212,7,1,37,0,NULL,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(278,6,213,7,2,38,0,NULL,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(279,6,214,7,3,39,0,NULL,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(280,6,215,7,4,40,0,NULL,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(281,6,216,7,5,41,1,2,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(282,6,217,7,6,42,1,3,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(283,6,218,8,1,43,0,NULL,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(284,6,219,8,2,44,0,NULL,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(285,6,220,8,3,45,0,NULL,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(286,6,221,8,4,46,1,4,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(287,6,222,8,5,47,0,NULL,6,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(288,6,223,8,6,48,0,NULL,7,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(289,7,176,1,1,1,0,NULL,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(290,7,177,1,2,2,0,NULL,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(291,7,178,1,3,3,0,NULL,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(292,7,179,1,4,4,0,NULL,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(293,7,180,1,5,5,0,NULL,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(294,7,181,1,6,6,0,NULL,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(295,7,182,2,1,7,0,NULL,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(296,7,183,2,2,8,0,NULL,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(297,7,184,2,3,9,0,NULL,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(298,7,185,2,4,10,0,NULL,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(299,7,186,2,5,11,0,NULL,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(300,7,187,2,6,12,0,NULL,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(301,7,188,3,1,13,0,NULL,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(302,7,189,3,2,14,0,NULL,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(303,7,190,3,3,15,0,NULL,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(304,7,191,3,4,16,0,NULL,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(305,7,192,3,5,17,0,NULL,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(306,7,193,3,6,18,0,NULL,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(307,7,224,4,1,19,0,NULL,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(308,7,225,4,2,20,0,NULL,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(309,7,226,4,3,21,0,NULL,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(310,7,227,4,4,22,0,NULL,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(311,7,228,4,5,23,0,NULL,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(312,7,229,4,6,24,0,NULL,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(313,7,230,5,1,25,0,NULL,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(314,7,231,5,2,26,0,NULL,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(315,7,232,5,3,27,0,NULL,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(316,7,233,5,4,28,0,NULL,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(317,7,234,5,5,29,0,NULL,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(318,7,235,5,6,30,0,NULL,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(319,7,236,6,1,31,0,NULL,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(320,7,237,6,2,32,0,NULL,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(321,7,238,6,3,33,0,NULL,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(322,7,239,6,4,34,0,NULL,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(323,7,240,6,5,35,0,NULL,NULL,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(324,7,211,6,6,36,1,1,NULL,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(325,7,241,7,1,37,0,NULL,NULL,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(326,7,242,7,2,38,0,NULL,NULL,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(327,7,243,7,3,39,0,NULL,NULL,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(328,7,244,7,4,40,0,NULL,NULL,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(329,7,216,7,5,41,1,2,NULL,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(330,7,217,7,6,42,1,3,NULL,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(331,7,245,8,1,43,0,NULL,NULL,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(332,7,246,8,2,44,0,NULL,NULL,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(333,7,247,8,3,45,0,NULL,NULL,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(334,7,221,8,4,46,1,4,NULL,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(335,7,222,8,5,47,0,NULL,6,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(336,7,223,8,6,48,0,NULL,7,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(337,8,248,1,1,1,0,NULL,NULL,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(338,8,249,1,2,2,0,NULL,NULL,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(339,8,250,1,3,3,0,NULL,NULL,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(340,8,251,1,4,4,0,NULL,NULL,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(341,8,252,1,5,5,0,NULL,NULL,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(342,8,253,1,6,6,0,NULL,NULL,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(343,8,254,2,1,7,0,NULL,NULL,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(344,8,255,2,2,8,0,NULL,NULL,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(345,8,256,2,3,9,0,NULL,NULL,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(346,8,257,2,4,10,0,NULL,NULL,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(347,8,258,2,5,11,0,NULL,NULL,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(348,8,259,2,6,12,0,NULL,NULL,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(349,8,260,3,1,13,0,NULL,NULL,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(350,8,261,3,2,14,0,NULL,NULL,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(351,8,262,3,3,15,0,NULL,NULL,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(352,8,263,3,4,16,0,NULL,NULL,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(353,8,264,3,5,17,0,NULL,NULL,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(354,8,265,3,6,18,0,NULL,NULL,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(355,8,266,4,1,19,0,NULL,NULL,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(356,8,267,4,2,20,0,NULL,NULL,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(357,8,268,4,3,21,0,NULL,NULL,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(358,8,269,4,4,22,0,NULL,NULL,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(359,8,270,4,5,23,0,NULL,NULL,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(360,8,271,4,6,24,0,NULL,NULL,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(361,8,272,5,1,25,0,NULL,NULL,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(362,8,273,5,2,26,0,NULL,NULL,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(363,8,274,5,3,27,0,NULL,NULL,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(364,8,275,5,4,28,0,NULL,NULL,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(365,8,276,5,5,29,0,NULL,NULL,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(366,8,277,5,6,30,0,NULL,NULL,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(367,8,278,6,1,31,0,NULL,NULL,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(368,8,279,6,2,32,0,NULL,NULL,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(369,8,280,6,3,33,0,NULL,NULL,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(370,8,281,6,4,34,0,NULL,NULL,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(371,8,282,6,5,35,0,NULL,NULL,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(372,8,283,6,6,36,1,1,NULL,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(373,8,284,7,1,37,0,NULL,NULL,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(374,8,285,7,2,38,0,NULL,NULL,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(375,8,286,7,3,39,0,NULL,NULL,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(376,8,287,7,4,40,0,NULL,NULL,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(377,8,288,7,5,41,1,2,NULL,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(378,8,289,7,6,42,1,3,NULL,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(379,8,290,8,1,43,0,NULL,NULL,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(380,8,291,8,2,44,0,NULL,NULL,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(381,8,292,8,3,45,0,NULL,NULL,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(382,8,293,8,4,46,1,4,NULL,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(383,8,294,8,5,47,0,NULL,6,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(384,8,295,8,6,48,0,NULL,7,'2026-11-26 03:32:43','2026-11-26 03:32:43');
/*!40000 ALTER TABLE `curriculum_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `curriculum_prerequisites`
--

DROP TABLE IF EXISTS `curriculum_prerequisites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `curriculum_prerequisites` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `curriculum_item_id` bigint(20) unsigned NOT NULL,
  `prerequisite_subject_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `curr_prereq_unique` (`curriculum_item_id`,`prerequisite_subject_id`),
  KEY `curriculum_prerequisites_prerequisite_subject_id_foreign` (`prerequisite_subject_id`),
  CONSTRAINT `curriculum_prerequisites_curriculum_item_id_foreign` FOREIGN KEY (`curriculum_item_id`) REFERENCES `curriculum_items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `curriculum_prerequisites_prerequisite_subject_id_foreign` FOREIGN KEY (`prerequisite_subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=151 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `curriculum_prerequisites`
--

LOCK TABLES `curriculum_prerequisites` WRITE;
/*!40000 ALTER TABLE `curriculum_prerequisites` DISABLE KEYS */;
INSERT INTO `curriculum_prerequisites` VALUES (1,4,1,NULL,NULL),(2,7,1,NULL,NULL),(3,8,2,NULL,NULL),(4,9,3,NULL,NULL),(5,10,4,NULL,NULL),(6,12,1,NULL,NULL),(7,13,1,NULL,NULL),(8,14,8,NULL,NULL),(9,15,8,NULL,NULL),(10,16,6,NULL,NULL),(11,18,9,NULL,NULL),(12,19,13,NULL,NULL),(13,20,15,NULL,NULL),(14,21,14,NULL,NULL),(15,22,16,NULL,NULL),(16,23,16,NULL,NULL),(17,24,15,NULL,NULL),(18,25,21,NULL,NULL),(19,25,20,NULL,NULL),(20,26,24,NULL,NULL),(21,27,22,NULL,NULL),(22,28,21,NULL,NULL),(23,29,19,NULL,NULL),(24,31,26,NULL,NULL),(25,32,25,NULL,NULL),(26,32,26,NULL,NULL),(27,33,27,NULL,NULL),(28,34,19,NULL,NULL),(29,34,14,NULL,NULL),(30,35,17,NULL,NULL),(31,37,31,NULL,NULL),(32,38,32,NULL,NULL),(33,38,27,NULL,NULL),(34,39,34,NULL,NULL),(35,40,5,NULL,NULL),(36,43,31,NULL,NULL),(37,43,35,NULL,NULL),(38,44,33,NULL,NULL),(39,45,38,NULL,NULL),(40,47,40,NULL,NULL),(41,48,47,NULL,NULL),(42,52,1,NULL,NULL),(43,55,1,NULL,NULL),(44,56,4,NULL,NULL),(45,57,3,NULL,NULL),(46,58,4,NULL,NULL),(47,60,6,NULL,NULL),(48,61,1,NULL,NULL),(49,62,49,NULL,NULL),(50,63,49,NULL,NULL),(51,64,6,NULL,NULL),(52,66,9,NULL,NULL),(53,67,13,NULL,NULL),(54,68,10,NULL,NULL),(55,68,13,NULL,NULL),(56,69,13,NULL,NULL),(57,70,16,NULL,NULL),(58,71,52,NULL,NULL),(59,72,53,NULL,NULL),(60,73,54,NULL,NULL),(61,74,56,NULL,NULL),(62,75,22,NULL,NULL),(63,76,56,NULL,NULL),(64,77,54,NULL,NULL),(65,79,57,NULL,NULL),(66,80,58,NULL,NULL),(67,81,27,NULL,NULL),(68,82,58,NULL,NULL),(69,83,17,NULL,NULL),(70,85,27,NULL,NULL),(71,85,59,NULL,NULL),(72,86,27,NULL,NULL),(73,87,55,NULL,NULL),(74,87,27,NULL,NULL),(75,88,5,NULL,NULL),(76,91,62,NULL,NULL),(77,92,64,NULL,NULL),(78,93,68,NULL,NULL),(79,95,40,NULL,NULL),(80,96,47,NULL,NULL),(81,100,1,NULL,NULL),(82,103,1,NULL,NULL),(83,105,3,NULL,NULL),(84,106,4,NULL,NULL),(85,109,1,NULL,NULL),(86,110,70,NULL,NULL),(87,111,10,NULL,NULL),(88,112,72,NULL,NULL),(89,114,9,NULL,NULL),(90,144,47,NULL,NULL),(91,151,104,NULL,NULL),(92,152,105,NULL,NULL),(93,153,106,NULL,NULL),(94,154,107,NULL,NULL),(95,155,108,NULL,NULL),(96,157,111,NULL,NULL),(97,158,110,NULL,NULL),(98,159,113,NULL,NULL),(99,160,111,NULL,NULL),(100,161,112,NULL,NULL),(101,162,110,NULL,NULL),(102,192,150,NULL,NULL),(103,199,104,NULL,NULL),(104,200,105,NULL,NULL),(105,201,106,NULL,NULL),(106,202,107,NULL,NULL),(107,203,108,NULL,NULL),(108,205,111,NULL,NULL),(109,206,110,NULL,NULL),(110,207,113,NULL,NULL),(111,208,111,NULL,NULL),(112,209,112,NULL,NULL),(113,210,110,NULL,NULL),(114,240,150,NULL,NULL),(115,247,176,NULL,NULL),(116,248,177,NULL,NULL),(117,250,179,NULL,NULL),(118,251,180,NULL,NULL),(119,252,178,NULL,NULL),(120,253,182,NULL,NULL),(121,254,183,NULL,NULL),(122,255,184,NULL,NULL),(123,256,181,NULL,NULL),(124,257,185,NULL,NULL),(125,258,187,NULL,NULL),(126,288,222,NULL,NULL),(127,295,176,NULL,NULL),(128,296,177,NULL,NULL),(129,298,179,NULL,NULL),(130,299,180,NULL,NULL),(131,300,178,NULL,NULL),(132,301,182,NULL,NULL),(133,302,183,NULL,NULL),(134,303,184,NULL,NULL),(135,304,181,NULL,NULL),(136,305,185,NULL,NULL),(137,306,187,NULL,NULL),(138,336,222,NULL,NULL),(139,343,248,NULL,NULL),(140,344,249,NULL,NULL),(141,345,248,NULL,NULL),(142,346,251,NULL,NULL),(143,347,250,NULL,NULL),(144,348,253,NULL,NULL),(145,349,254,NULL,NULL),(146,350,256,NULL,NULL),(147,351,255,NULL,NULL),(148,352,254,NULL,NULL),(149,354,259,NULL,NULL),(150,384,294,NULL,NULL);
/*!40000 ALTER TABLE `curriculum_prerequisites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `enrollments`
--

DROP TABLE IF EXISTS `enrollments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `enrollments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `career_id` bigint(20) unsigned DEFAULT NULL,
  `offering_id` bigint(20) unsigned NOT NULL,
  `subject_id` bigint(20) unsigned NOT NULL,
  `semester_id` bigint(20) unsigned NOT NULL,
  `attempt_number` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `final_score` tinyint(3) unsigned DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'en_curso',
  `enrolled_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `closed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `enrollments_student_offering_unique` (`student_id`,`offering_id`),
  KEY `enrollments_career_id_foreign` (`career_id`),
  KEY `enrollments_offering_id_foreign` (`offering_id`),
  KEY `enrollments_subject_id_foreign` (`subject_id`),
  KEY `enrollments_semester_id_foreign` (`semester_id`),
  KEY `enrollments_student_id_subject_id_attempt_number_index` (`student_id`,`subject_id`,`attempt_number`),
  KEY `enrollments_student_id_semester_id_status_index` (`student_id`,`semester_id`,`status`),
  CONSTRAINT `enrollments_career_id_foreign` FOREIGN KEY (`career_id`) REFERENCES `careers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `enrollments_offering_id_foreign` FOREIGN KEY (`offering_id`) REFERENCES `offerings` (`id`),
  CONSTRAINT `enrollments_semester_id_foreign` FOREIGN KEY (`semester_id`) REFERENCES `semesters` (`id`),
  CONSTRAINT `enrollments_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `enrollments_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `enrollments`
--

LOCK TABLES `enrollments` WRITE;
/*!40000 ALTER TABLE `enrollments` DISABLE KEYS */;
INSERT INTO `enrollments` VALUES (1,7,1,1,1,3,1,84,'aprobada','2026-01-01 13:00:00','2026-02-01 00:00:00','2026-11-26 03:32:52','2026-11-26 03:32:52'),(2,8,2,1,1,3,1,78,'aprobada','2026-01-01 13:00:00','2026-02-01 00:00:00','2026-11-26 03:32:52','2026-11-26 03:32:52'),(3,9,3,1,1,3,1,81,'aprobada','2026-01-01 13:00:00','2026-02-01 00:00:00','2026-11-26 03:32:52','2026-11-26 03:32:52'),(4,7,1,2,2,3,1,91,'aprobada','2026-02-01 13:00:00','2026-03-01 00:00:00','2026-11-26 03:32:52','2026-11-26 03:32:52'),(5,8,2,2,2,3,1,82,'aprobada','2026-02-01 13:00:00','2026-03-01 00:00:00','2026-11-26 03:32:52','2026-11-26 03:32:52'),(6,9,3,2,2,3,1,75,'aprobada','2026-02-01 13:00:00','2026-03-01 00:00:00','2026-11-26 03:32:52','2026-11-26 03:32:52'),(7,7,1,3,3,3,1,76,'aprobada','2026-03-01 13:00:00','2026-04-01 00:00:00','2026-11-26 03:32:52','2026-11-26 03:32:52'),(8,8,2,3,3,3,1,88,'aprobada','2026-03-01 13:00:00','2026-04-01 00:00:00','2026-11-26 03:32:52','2026-11-26 03:32:52'),(9,7,1,4,4,3,1,44,'reprobada','2026-04-01 13:00:00','2026-05-01 00:00:00','2026-11-26 03:32:52','2026-11-26 03:32:52'),(10,8,2,4,4,3,1,65,'aprobada','2026-04-01 13:00:00','2026-05-01 00:00:00','2026-11-26 03:32:53','2026-11-26 03:32:53'),(11,7,1,5,5,3,1,85,'aprobada','2026-05-01 13:00:00','2026-06-01 00:00:00','2026-11-26 03:32:53','2026-11-26 03:32:53'),(12,8,2,5,5,3,1,90,'aprobada','2026-05-01 13:00:00','2026-06-01 00:00:00','2026-11-26 03:32:53','2026-11-26 03:32:53'),(13,7,1,6,8,4,1,NULL,'en_curso','2026-11-25 03:32:53',NULL,'2026-11-26 03:32:53','2026-11-26 03:32:53'),(14,8,2,7,7,4,1,NULL,'en_curso','2026-11-26 03:32:53',NULL,'2026-11-26 03:32:53','2026-11-26 03:32:53'),(15,9,3,7,7,4,1,NULL,'en_curso','2026-11-26 03:32:53',NULL,'2026-11-26 03:32:53','2026-11-26 03:32:53'),(16,12,5,9,104,4,1,NULL,'en_curso','2026-11-26 03:32:53',NULL,'2026-11-26 03:32:53','2026-11-26 03:32:53'),(17,13,6,11,176,4,1,NULL,'en_curso','2026-11-26 03:32:53',NULL,'2026-11-26 03:32:53','2026-11-26 03:32:53'),(18,11,8,12,248,4,1,NULL,'en_curso','2026-11-26 03:32:53',NULL,'2026-11-26 03:32:53','2026-11-26 03:32:53');
/*!40000 ALTER TABLE `enrollments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `faculties`
--

DROP TABLE IF EXISTS `faculties`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `faculties` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `faculties_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `faculties`
--

LOCK TABLES `faculties` WRITE;
/*!40000 ALTER TABLE `faculties` DISABLE KEYS */;
INSERT INTO `faculties` VALUES (1,'ING','Facultad de Ingeniería','Ingeniería de Sistemas, Telecomunicaciones e Industrial.',1,'2026-11-26 03:32:37','2026-11-26 03:32:37'),(2,'EMP','Facultad de Ciencias Empresariales','Administración de Empresas y Contaduría Pública.',1,'2026-11-26 03:32:37','2026-11-26 03:32:37'),(3,'JUR','Facultad de Ciencias Jurídicas y Sociales','Derecho y Psicología.',1,'2026-11-26 03:32:37','2026-11-26 03:32:37'),(4,'GAS','Facultad de Gastronomía','Artes culinarias, nutrición y gestión gastronómica.',1,'2026-11-26 03:32:37','2026-11-26 03:32:37');
/*!40000 ALTER TABLE `faculties` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` varchar(255) NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `grade_adjustments`
--

DROP TABLE IF EXISTS `grade_adjustments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `grade_adjustments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `enrollment_id` bigint(20) unsigned NOT NULL,
  `adjusted_by` bigint(20) unsigned NOT NULL,
  `previous_score` tinyint(3) unsigned NOT NULL,
  `new_score` tinyint(3) unsigned NOT NULL,
  `previous_status` varchar(20) NOT NULL,
  `new_status` varchar(20) NOT NULL,
  `reason` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `grade_adjustments_enrollment_id_foreign` (`enrollment_id`),
  KEY `grade_adjustments_adjusted_by_foreign` (`adjusted_by`),
  CONSTRAINT `grade_adjustments_adjusted_by_foreign` FOREIGN KEY (`adjusted_by`) REFERENCES `users` (`id`),
  CONSTRAINT `grade_adjustments_enrollment_id_foreign` FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `grade_adjustments`
--

LOCK TABLES `grade_adjustments` WRITE;
/*!40000 ALTER TABLE `grade_adjustments` DISABLE KEYS */;
/*!40000 ALTER TABLE `grade_adjustments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` smallint(5) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_11_19_000001_create_academic_schema',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `offering_tasks`
--

DROP TABLE IF EXISTS `offering_tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `offering_tasks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `offering_id` bigint(20) unsigned NOT NULL,
  `title` varchar(160) NOT NULL,
  `description` text DEFAULT NULL,
  `max_points` tinyint(3) unsigned NOT NULL,
  `due_date` date NOT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `offering_tasks_offering_id_foreign` (`offering_id`),
  KEY `offering_tasks_created_by_foreign` (`created_by`),
  CONSTRAINT `offering_tasks_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `offering_tasks_offering_id_foreign` FOREIGN KEY (`offering_id`) REFERENCES `offerings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `offering_tasks`
--

LOCK TABLES `offering_tasks` WRITE;
/*!40000 ALTER TABLE `offering_tasks` DISABLE KEYS */;
INSERT INTO `offering_tasks` VALUES (1,6,'Práctica 1: Clases y Encapsulamiento','Modelado de clases en PHP.',30,'2026-12-02',2,'2026-11-26 03:32:53','2026-11-26 03:32:53'),(2,6,'Evaluación Parcial: Polimorfismo','Aplicación de interfaces y SOLID.',30,'2026-12-10',2,'2026-11-26 03:32:53','2026-11-26 03:32:53'),(3,6,'Proyecto Integrador de Módulo','Entrega práctica final.',40,'2026-12-22',2,'2026-11-26 03:32:53','2026-11-26 03:32:53'),(4,7,'Guía de Matrices y Determinantes',NULL,40,'2026-12-05',2,'2026-11-26 03:32:53','2026-11-26 03:32:53'),(5,7,'Evaluación Final de Espacios Vectoriales',NULL,60,'2026-12-22',2,'2026-11-26 03:32:53','2026-11-26 03:32:53');
/*!40000 ALTER TABLE `offering_tasks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `offerings`
--

DROP TABLE IF EXISTS `offerings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `offerings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `subject_id` bigint(20) unsigned NOT NULL,
  `semester_id` bigint(20) unsigned NOT NULL,
  `month` tinyint(3) unsigned NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `shift` varchar(15) NOT NULL,
  `start_time` varchar(5) NOT NULL DEFAULT '07:30',
  `end_time` varchar(5) NOT NULL DEFAULT '11:30',
  `days_label` varchar(40) NOT NULL DEFAULT 'Lunes a Viernes',
  `max_capacity` tinyint(3) unsigned NOT NULL DEFAULT 50,
  `teacher_id` bigint(20) unsigned DEFAULT NULL,
  `classroom` smallint(5) unsigned DEFAULT NULL,
  `is_disabled` tinyint(1) NOT NULL DEFAULT 0,
  `closed_at` timestamp NULL DEFAULT NULL,
  `closed_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `offerings_subject_id_foreign` (`subject_id`),
  KEY `offerings_closed_by_foreign` (`closed_by`),
  KEY `offerings_semester_id_month_shift_index` (`semester_id`,`month`,`shift`),
  KEY `offerings_teacher_id_semester_id_month_shift_index` (`teacher_id`,`semester_id`,`month`,`shift`),
  CONSTRAINT `offerings_closed_by_foreign` FOREIGN KEY (`closed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `offerings_semester_id_foreign` FOREIGN KEY (`semester_id`) REFERENCES `semesters` (`id`),
  CONSTRAINT `offerings_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`),
  CONSTRAINT `offerings_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `offerings`
--

LOCK TABLES `offerings` WRITE;
/*!40000 ALTER TABLE `offerings` DISABLE KEYS */;
INSERT INTO `offerings` VALUES (1,1,3,1,'2026-01-01','2026-01-31','manana','07:30','11:30','Lunes a Viernes',50,2,101,0,'2026-02-01 00:00:00',2,'2026-11-26 03:32:52','2026-11-26 03:32:52'),(2,2,3,2,'2026-02-01','2026-02-28','manana','07:30','11:30','Lunes a Viernes',50,2,102,0,'2026-03-01 00:00:00',2,'2026-11-26 03:32:52','2026-11-26 03:32:52'),(3,3,3,3,'2026-03-01','2026-03-31','tarde','14:00','18:00','Lunes a Viernes',50,3,103,0,'2026-04-01 00:00:00',3,'2026-11-26 03:32:52','2026-11-26 03:32:52'),(4,4,3,4,'2026-04-01','2026-04-30','manana','07:30','11:30','Lunes a Viernes',50,3,101,0,'2026-05-01 00:00:00',3,'2026-11-26 03:32:52','2026-11-26 03:32:52'),(5,5,3,5,'2026-05-01','2026-05-31','noche','18:45','22:15','Lunes a Viernes',50,3,104,0,'2026-06-01 00:00:00',3,'2026-11-26 03:32:53','2026-11-26 03:32:53'),(6,8,4,11,'2026-11-24','2026-12-22','manana','07:30','11:30','Lunes a Viernes',50,2,201,0,NULL,NULL,'2026-11-26 03:32:53','2026-11-26 03:32:53'),(7,7,4,11,'2026-11-25','2026-12-22','tarde','14:00','18:00','Lunes a Viernes',50,2,201,0,NULL,NULL,'2026-11-26 03:32:53','2026-11-26 03:32:53'),(8,11,4,11,'2026-11-25','2026-12-22','noche','18:45','22:15','Lunes a Viernes',45,3,205,0,NULL,NULL,'2026-11-26 03:32:53','2026-11-26 03:32:53'),(9,104,4,11,'2026-11-25','2026-12-22','manana','07:30','11:30','Lunes a Viernes',50,4,401,0,NULL,NULL,'2026-11-26 03:32:53','2026-11-26 03:32:53'),(10,107,4,11,'2026-11-25','2026-12-22','noche','18:45','22:15','Lunes a Viernes',50,4,402,0,NULL,NULL,'2026-11-26 03:32:53','2026-11-26 03:32:53'),(11,176,4,11,'2026-11-25','2026-12-22','tarde','14:00','18:00','Lunes a Viernes',50,5,501,0,NULL,NULL,'2026-11-26 03:32:53','2026-11-26 03:32:53'),(12,248,4,11,'2026-11-25','2026-12-22','manana','07:30','11:30','Lunes a Viernes',30,6,301,0,NULL,NULL,'2026-11-26 03:32:53','2026-11-26 03:32:53');
/*!40000 ALTER TABLE `offerings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `semesters`
--

DROP TABLE IF EXISTS `semesters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `semesters` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `year` smallint(5) unsigned NOT NULL,
  `period` tinyint(3) unsigned NOT NULL,
  `code` varchar(15) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `semesters_year_period_unique` (`year`,`period`),
  UNIQUE KEY `semesters_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `semesters`
--

LOCK TABLES `semesters` WRITE;
/*!40000 ALTER TABLE `semesters` DISABLE KEYS */;
INSERT INTO `semesters` VALUES (1,2025,1,'2025-I','2025-01-01','2025-06-30','2026-11-26 03:32:37','2026-11-26 03:32:37'),(2,2025,2,'2025-II','2025-07-01','2025-12-31','2026-11-26 03:32:37','2026-11-26 03:32:37'),(3,2026,1,'2026-I','2026-01-01','2026-06-30','2026-11-26 03:32:37','2026-11-26 03:32:37'),(4,2026,2,'2026-II','2026-07-01','2026-12-31','2026-11-26 03:32:37','2026-11-26 03:32:37'),(5,2027,1,'2027-I','2027-01-01','2027-06-30','2026-11-26 03:32:37','2026-11-26 03:32:37'),(6,2027,2,'2027-II','2027-07-01','2027-12-31','2026-11-26 03:32:37','2026-11-26 03:32:37');
/*!40000 ALTER TABLE `semesters` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('wclFMUgpfqbakvniKJQIWEK4kHk8fHxI3WU6nVCI',2,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/158.0.0.0 Safari/537.36 Edg/158.0.0.0','eyJfdG9rZW4iOiJCcDBWcDNkS2Y4YUhVbUR2VmdMa0IyaXkxdExQbFlhd0w3VDFrUjZyIiwiX2ZsYXNoIjp7Im5ldyI6W10sIm9sZCI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDBcL3JlcG9ydGVzIiwicm91dGUiOiJyZXBvcnRzLmluZGV4In0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoyLCJwYXNzd29yZF9oYXNoX3dlYiI6IjNlNzhhM2I5YzYxZjVjODM4MjdiOWE5MGY5YjkyZDMzODE5ZjliMWZlNzE4ZDA5OWMwMzI0MDM4ZTI3MWQ0OWIifQ==',1795657124),('z1lGs76Zw0Ei6NuLL1mF8P491TOjB8dOM0Wlpg2u',2,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/158.0.0.0 Safari/537.36 Edg/158.0.0.0','eyJfdG9rZW4iOiJTdVFIVkg2WDJGVGQzTXN4aXQ0WTVjUU9kalFGNjZvcjFxZ1hTT0xJIiwiX2ZsYXNoIjp7Im5ldyI6W10sIm9sZCI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDBcL2RvY2VudGVcL29mZXJ0YXNcLzc/dGFiPWluc2NyaXRvcyIsInJvdXRlIjoidGVhY2hlci5vZmZlcmluZ3Muc2hvdyJ9LCJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6MiwicGFzc3dvcmRfaGFzaF93ZWIiOiIzZTc4YTNiOWM2MWY1YzgzODI3YjlhOTBmOWI5MmQzMzgxOWY5YjFmZTcxOGQwOTljMDMyNDAzOGUyNzFkNDliIn0=',1795649836);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_careers`
--

DROP TABLE IF EXISTS `student_careers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `student_careers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `career_id` bigint(20) unsigned NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `enrolled_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `student_careers_unique` (`student_id`,`career_id`),
  KEY `student_careers_career_id_foreign` (`career_id`),
  CONSTRAINT `student_careers_career_id_foreign` FOREIGN KEY (`career_id`) REFERENCES `careers` (`id`),
  CONSTRAINT `student_careers_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_careers`
--

LOCK TABLES `student_careers` WRITE;
/*!40000 ALTER TABLE `student_careers` DISABLE KEYS */;
INSERT INTO `student_careers` VALUES (1,7,1,1,1,'2026-11-25 23:32:48','2026-11-26 03:32:48','2026-11-26 03:32:48'),(2,7,4,0,1,'2026-11-25 23:32:48','2026-11-26 03:32:48','2026-11-26 03:32:48'),(3,8,2,1,1,'2026-11-25 23:32:49','2026-11-26 03:32:49','2026-11-26 03:32:49'),(4,9,3,1,1,'2026-11-25 23:32:50','2026-11-26 03:32:50','2026-11-26 03:32:50'),(5,10,1,1,1,'2026-11-25 23:32:50','2026-11-26 03:32:50','2026-11-26 03:32:50'),(6,11,8,1,1,'2026-11-25 23:32:51','2026-11-26 03:32:51','2026-11-26 03:32:51'),(7,12,5,1,1,'2026-11-25 23:32:52','2026-11-26 03:32:52','2026-11-26 03:32:52'),(8,13,6,1,1,'2026-11-25 23:32:52','2026-11-26 03:32:52','2026-11-26 03:32:52');
/*!40000 ALTER TABLE `student_careers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subjects`
--

DROP TABLE IF EXISTS `subjects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subjects` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `faculty_id` bigint(20) unsigned NOT NULL,
  `code` varchar(25) NOT NULL,
  `area_prefix` varchar(10) NOT NULL,
  `name` varchar(160) NOT NULL,
  `credits` tinyint(3) unsigned NOT NULL DEFAULT 4,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `subjects_code_unique` (`code`),
  KEY `subjects_faculty_id_foreign` (`faculty_id`),
  KEY `subjects_area_prefix_index` (`area_prefix`),
  CONSTRAINT `subjects_faculty_id_foreign` FOREIGN KEY (`faculty_id`) REFERENCES `faculties` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=296 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subjects`
--

LOCK TABLES `subjects` WRITE;
/*!40000 ALTER TABLE `subjects` DISABLE KEYS */;
INSERT INTO `subjects` VALUES (1,1,'MAT-101','MAT','Cálculo I',4,1,'2026-11-26 03:32:37','2026-11-26 03:32:37'),(2,1,'SIS-101','SIS','Introducción a la Programación',4,1,'2026-11-26 03:32:37','2026-11-26 03:32:37'),(3,1,'LIN-101','LIN','Inglés Técnico I',4,1,'2026-11-26 03:32:37','2026-11-26 03:32:37'),(4,1,'FIS-101','FIS','Física General I',4,1,'2026-11-26 03:32:37','2026-11-26 03:32:37'),(5,1,'INV-101','INV','Metodología de la Investigación',4,1,'2026-11-26 03:32:37','2026-11-26 03:32:37'),(6,1,'ELC-101','ELC','Sistemas Digitales I',4,1,'2026-11-26 03:32:37','2026-11-26 03:32:37'),(7,1,'MAT-102','MAT','Álgebra Lineal',4,1,'2026-11-26 03:32:37','2026-11-26 03:32:37'),(8,1,'SIS-102','SIS','Programación Orientada a Objetos',4,1,'2026-11-26 03:32:37','2026-11-26 03:32:37'),(9,1,'LIN-102','LIN','Inglés Técnico II',4,1,'2026-11-26 03:32:37','2026-11-26 03:32:37'),(10,1,'FIS-102','FIS','Física General II',4,1,'2026-11-26 03:32:37','2026-11-26 03:32:37'),(11,1,'CPA-101','CPA','Contabilidad Básica',4,1,'2026-11-26 03:32:37','2026-11-26 03:32:37'),(12,1,'SIS-103','SIS','Estructuras Discretas',4,1,'2026-11-26 03:32:37','2026-11-26 03:32:37'),(13,1,'MAT-201','MAT','Cálculo II',4,1,'2026-11-26 03:32:37','2026-11-26 03:32:37'),(14,1,'SIS-201','SIS','Estructuras de Datos',4,1,'2026-11-26 03:32:37','2026-11-26 03:32:37'),(15,1,'SIS-202','SIS','Base de Datos I',4,1,'2026-11-26 03:32:37','2026-11-26 03:32:37'),(16,1,'ELC-201','ELC','Arquitectura de Computadoras',4,1,'2026-11-26 03:32:37','2026-11-26 03:32:37'),(17,1,'ADM-101','ADM','Administración General',4,1,'2026-11-26 03:32:37','2026-11-26 03:32:37'),(18,1,'LIN-201','LIN','Inglés Técnico III',4,1,'2026-11-26 03:32:37','2026-11-26 03:32:37'),(19,1,'MAT-202','MAT','Probabilidad y Estadística',4,1,'2026-11-26 03:32:37','2026-11-26 03:32:37'),(20,1,'SIS-203','SIS','Base de Datos II',4,1,'2026-11-26 03:32:37','2026-11-26 03:32:37'),(21,1,'SIS-204','SIS','Desarrollo Web I',4,1,'2026-11-26 03:32:37','2026-11-26 03:32:37'),(22,1,'RED-201','RED','Redes de Computadoras I',4,1,'2026-11-26 03:32:37','2026-11-26 03:32:37'),(23,1,'SIS-205','SIS','Sistemas Operativos',4,1,'2026-11-26 03:32:37','2026-11-26 03:32:37'),(24,1,'SIS-206','SIS','Análisis y Diseño de Sistemas',4,1,'2026-11-26 03:32:37','2026-11-26 03:32:37'),(25,1,'SIS-301','SIS','Desarrollo Web II (Frameworks)',4,1,'2026-11-26 03:32:37','2026-11-26 03:32:37'),(26,1,'SIS-302','SIS','Ingeniería de Software I',4,1,'2026-11-26 03:32:37','2026-11-26 03:32:37'),(27,1,'RED-301','RED','Redes de Computadoras II',4,1,'2026-11-26 03:32:37','2026-11-26 03:32:37'),(28,1,'SIS-303','SIS','Programación Móvil',4,1,'2026-11-26 03:32:37','2026-11-26 03:32:37'),(29,1,'MAT-301','MAT','Investigación Operativa',4,1,'2026-11-26 03:32:37','2026-11-26 03:32:37'),(30,1,'ADM-201','ADM','Deontología y Ética Profesional',4,1,'2026-11-26 03:32:38','2026-11-26 03:32:38'),(31,1,'SIS-304','SIS','Ingeniería de Software II',4,1,'2026-11-26 03:32:38','2026-11-26 03:32:38'),(32,1,'SIS-305','SIS','Arquitectura de Software',4,1,'2026-11-26 03:32:38','2026-11-26 03:32:38'),(33,1,'RED-302','RED','Seguridad Informática',4,1,'2026-11-26 03:32:38','2026-11-26 03:32:38'),(34,1,'SIS-306','SIS','Inteligencia Artificial',4,1,'2026-11-26 03:32:38','2026-11-26 03:32:38'),(35,1,'ADM-301','ADM','Emprendedurismo Tecnológico',4,1,'2026-11-26 03:32:38','2026-11-26 03:32:38'),(36,1,'ELE-301','ELE','Electiva 1 (Ingeniería)',4,1,'2026-11-26 03:32:38','2026-11-26 03:32:38'),(37,1,'SIS-401','SIS','Calidad y Pruebas de Software',4,1,'2026-11-26 03:32:38','2026-11-26 03:32:38'),(38,1,'SIS-402','SIS','Sistemas Distribuidos y Cloud',4,1,'2026-11-26 03:32:38','2026-11-26 03:32:38'),(39,1,'SIS-403','SIS','Ciencia de Datos',4,1,'2026-11-26 03:32:38','2026-11-26 03:32:38'),(40,1,'INV-301','INV','Taller de Investigación Aplicada',4,1,'2026-11-26 03:32:38','2026-11-26 03:32:38'),(41,1,'ELE-401','ELE','Electiva 2 (Ingeniería)',4,1,'2026-11-26 03:32:38','2026-11-26 03:32:38'),(42,1,'ELE-402','ELE','Electiva 3 (Ingeniería)',4,1,'2026-11-26 03:32:38','2026-11-26 03:32:38'),(43,1,'SIS-404','SIS','Gestión de Proyectos TI',4,1,'2026-11-26 03:32:38','2026-11-26 03:32:38'),(44,1,'SIS-405','SIS','Auditoría de Sistemas',4,1,'2026-11-26 03:32:38','2026-11-26 03:32:38'),(45,1,'SIS-406','SIS','DevOps y Despliegue Continuo',4,1,'2026-11-26 03:32:38','2026-11-26 03:32:38'),(46,1,'ELE-403','ELE','Electiva 4 (Ingeniería)',4,1,'2026-11-26 03:32:38','2026-11-26 03:32:38'),(47,1,'INV-401','INV','Seminario de Grado I',4,1,'2026-11-26 03:32:38','2026-11-26 03:32:38'),(48,1,'INV-402','INV','Proyecto de Grado II',4,1,'2026-11-26 03:32:38','2026-11-26 03:32:38'),(49,1,'ELC-102','ELC','Circuitos Eléctricos I',4,1,'2026-11-26 03:32:38','2026-11-26 03:32:38'),(50,1,'TEL-101','TEL','Fundamentos de Telecomunicaciones',4,1,'2026-11-26 03:32:38','2026-11-26 03:32:38'),(51,1,'ELC-202','ELC','Circuitos Eléctricos II',4,1,'2026-11-26 03:32:38','2026-11-26 03:32:38'),(52,1,'ELC-203','ELC','Electrónica Analógica I',4,1,'2026-11-26 03:32:38','2026-11-26 03:32:38'),(53,1,'TEL-201','TEL','Teoría Electromagnética',4,1,'2026-11-26 03:32:38','2026-11-26 03:32:38'),(54,1,'TEL-202','TEL','Señales y Sistemas',4,1,'2026-11-26 03:32:38','2026-11-26 03:32:38'),(55,1,'ELC-204','ELC','Microcontroladores',4,1,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(56,1,'TEL-203','TEL','Líneas de Transmisión',4,1,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(57,1,'TEL-301','TEL','Sistemas de Comunicaciones I',4,1,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(58,1,'TEL-302','TEL','Antenas y Propagación',4,1,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(59,1,'TEL-303','TEL','Comunicaciones Ópticas',4,1,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(60,1,'ELC-301','ELC','Procesamiento Digital de Señales',4,1,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(61,1,'TEL-304','TEL','Sistemas de Comunicaciones II',4,1,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(62,1,'TEL-305','TEL','Comunicaciones Móviles y 5G',4,1,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(63,1,'TEL-306','TEL','Comunicaciones Satelitales',4,1,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(64,1,'TEL-401','TEL','Redes de Banda Ancha',4,1,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(65,1,'TEL-402','TEL','Telefonía IP y Multimedia',4,1,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(66,1,'TEL-403','TEL','Internet de las Cosas (IoT)',4,1,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(67,1,'TEL-404','TEL','Regulación de Telecomunicaciones',4,1,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(68,1,'TEL-405','TEL','Planificación de Infraestructura',4,1,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(69,1,'TEL-406','TEL','Gestión y Monitoreo de Redes',4,1,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(70,1,'IND-101','IND','Introducción a la Ingeniería Industrial',4,1,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(71,1,'IND-102','IND','Dibujo Industrial CAD',4,1,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(72,1,'IND-103','IND','Química Industrial',4,1,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(73,1,'IND-201','IND','Estudio del Trabajo y Métodos',4,1,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(74,1,'IND-202','IND','Termodinámica Aplicada',4,1,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(75,1,'IND-203','IND','Ciencia de los Materiales',4,1,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(76,1,'IND-IND401','IND','Ingeniería Industrial 1 — Nivel 4',4,1,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(77,1,'IND-IND402','IND','Ingeniería Industrial 2 — Nivel 4',4,1,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(78,1,'IND-IND403','IND','Ingeniería Industrial 3 — Nivel 4',4,1,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(79,1,'IND-IND404','IND','Ingeniería Industrial 4 — Nivel 4',4,1,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(80,1,'IND-IND405','IND','Ingeniería Industrial 5 — Nivel 4',4,1,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(81,1,'IND-IND406','IND','Ingeniería Industrial 6 — Nivel 4',4,1,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(82,1,'IND-IND501','IND','Ingeniería Industrial 7 — Nivel 5',4,1,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(83,1,'IND-IND502','IND','Ingeniería Industrial 8 — Nivel 5',4,1,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(84,1,'IND-IND503','IND','Ingeniería Industrial 9 — Nivel 5',4,1,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(85,1,'IND-IND504','IND','Ingeniería Industrial 10 — Nivel 5',4,1,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(86,1,'IND-IND505','IND','Ingeniería Industrial 11 — Nivel 5',4,1,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(87,1,'IND-IND506','IND','Ingeniería Industrial 12 — Nivel 5',4,1,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(88,1,'IND-IND601','IND','Ingeniería Industrial 13 — Nivel 6',4,1,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(89,1,'IND-IND602','IND','Ingeniería Industrial 14 — Nivel 6',4,1,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(90,1,'IND-IND603','IND','Ingeniería Industrial 15 — Nivel 6',4,1,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(91,1,'IND-IND604','IND','Ingeniería Industrial 16 — Nivel 6',4,1,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(92,1,'IND-IND605','IND','Ingeniería Industrial 17 — Nivel 6',4,1,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(93,1,'ELE-601','ELE','Electiva 1 (ING)',4,1,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(94,1,'IND-IND701','IND','Ingeniería Industrial 18 — Nivel 7',4,1,'2026-11-26 03:32:39','2026-11-26 03:32:39'),(95,1,'IND-IND702','IND','Ingeniería Industrial 19 — Nivel 7',4,1,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(96,1,'IND-IND703','IND','Ingeniería Industrial 20 — Nivel 7',4,1,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(97,1,'IND-IND704','IND','Ingeniería Industrial 21 — Nivel 7',4,1,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(98,1,'ELE-702','ELE','Electiva 2 (ING)',4,1,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(99,1,'ELE-703','ELE','Electiva 3 (ING)',4,1,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(100,1,'IND-IND801','IND','Ingeniería Industrial 22 — Nivel 8',4,1,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(101,1,'IND-IND802','IND','Ingeniería Industrial 23 — Nivel 8',4,1,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(102,1,'IND-IND803','IND','Ingeniería Industrial 24 — Nivel 8',4,1,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(103,1,'ELE-804','ELE','Electiva 4 (ING)',4,1,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(104,2,'ADM-E101','ADM','Administración General I',4,1,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(105,2,'CPA-E101','CPA','Contabilidad Básica I',4,1,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(106,2,'MAT-E101','MAT','Matemática Financiera I',4,1,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(107,2,'ECO-E101','ECO','Principios de Economía',4,1,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(108,2,'LIN-E101','LIN','Inglés Empresarial I',4,1,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(109,2,'INV-E101','INV','Metodología de la Investigación (EMP)',4,1,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(110,2,'ADM-E102','ADM','Administración General II',4,1,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(111,2,'CPA-E102','CPA','Contabilidad Intermedia',4,1,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(112,2,'MAT-E102','MAT','Estadística Empresarial I',4,1,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(113,2,'ECO-E102','ECO','Microeconomía',4,1,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(114,2,'LIN-E102','LIN','Inglés Empresarial II',4,1,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(115,2,'DER-E101','DER','Derecho Comercial y Laboral',4,1,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(116,2,'FIN-E201','FIN','Finanzas Corporativas I',4,1,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(117,2,'MKT-E201','MKT','Fundamentos de Marketing',4,1,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(118,2,'ECO-E201','ECO','Macroeconomía',4,1,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(119,2,'CPA-E201','CPA','Contabilidad de Costos I',4,1,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(120,2,'MAT-E201','MAT','Estadística Empresarial II',4,1,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(121,2,'ADM-E201','ADM','Comportamiento Organizacional',4,1,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(122,2,'ADM-ADM401','ADM','Gestión Empresarial 1 — Nivel 4',4,1,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(123,2,'ADM-ADM402','ADM','Gestión Empresarial 2 — Nivel 4',4,1,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(124,2,'ADM-ADM403','ADM','Gestión Empresarial 3 — Nivel 4',4,1,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(125,2,'ADM-ADM404','ADM','Gestión Empresarial 4 — Nivel 4',4,1,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(126,2,'ADM-ADM405','ADM','Gestión Empresarial 5 — Nivel 4',4,1,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(127,2,'ADM-ADM406','ADM','Gestión Empresarial 6 — Nivel 4',4,1,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(128,2,'ADM-ADM501','ADM','Gestión Empresarial 7 — Nivel 5',4,1,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(129,2,'ADM-ADM502','ADM','Gestión Empresarial 8 — Nivel 5',4,1,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(130,2,'ADM-ADM503','ADM','Gestión Empresarial 9 — Nivel 5',4,1,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(131,2,'ADM-ADM504','ADM','Gestión Empresarial 10 — Nivel 5',4,1,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(132,2,'ADM-ADM505','ADM','Gestión Empresarial 11 — Nivel 5',4,1,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(133,2,'ADM-ADM506','ADM','Gestión Empresarial 12 — Nivel 5',4,1,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(134,2,'ADM-ADM601','ADM','Gestión Empresarial 13 — Nivel 6',4,1,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(135,2,'ADM-ADM602','ADM','Gestión Empresarial 14 — Nivel 6',4,1,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(136,2,'ADM-ADM603','ADM','Gestión Empresarial 15 — Nivel 6',4,1,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(137,2,'ADM-ADM604','ADM','Gestión Empresarial 16 — Nivel 6',4,1,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(138,2,'ADM-ADM605','ADM','Gestión Empresarial 17 — Nivel 6',4,1,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(139,2,'ELE-EMP601','ELE','Electiva 1 (EMP)',4,1,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(140,2,'ADM-ADM701','ADM','Gestión Empresarial 18 — Nivel 7',4,1,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(141,2,'ADM-ADM702','ADM','Gestión Empresarial 19 — Nivel 7',4,1,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(142,2,'ADM-ADM703','ADM','Gestión Empresarial 20 — Nivel 7',4,1,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(143,2,'ADM-ADM704','ADM','Gestión Empresarial 21 — Nivel 7',4,1,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(144,2,'ELE-EMP702','ELE','Electiva 2 (EMP)',4,1,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(145,2,'ELE-EMP703','ELE','Electiva 3 (EMP)',4,1,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(146,2,'ADM-ADM801','ADM','Gestión Empresarial 22 — Nivel 8',4,1,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(147,2,'ADM-ADM802','ADM','Gestión Empresarial 23 — Nivel 8',4,1,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(148,2,'ADM-ADM803','ADM','Gestión Empresarial 24 — Nivel 8',4,1,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(149,2,'ELE-EMP804','ELE','Electiva 4 (EMP)',4,1,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(150,2,'INV-EMP401','INV','Seminario de Grado I (ADM)',4,1,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(151,2,'INV-EMP402','INV','Proyecto de Grado II (ADM)',4,1,'2026-11-26 03:32:40','2026-11-26 03:32:40'),(152,2,'CPA-CPA401','CPA','Contaduría y Auditoría 1 — Nivel 4',4,1,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(153,2,'CPA-CPA402','CPA','Contaduría y Auditoría 2 — Nivel 4',4,1,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(154,2,'CPA-CPA403','CPA','Contaduría y Auditoría 3 — Nivel 4',4,1,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(155,2,'CPA-CPA404','CPA','Contaduría y Auditoría 4 — Nivel 4',4,1,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(156,2,'CPA-CPA405','CPA','Contaduría y Auditoría 5 — Nivel 4',4,1,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(157,2,'CPA-CPA406','CPA','Contaduría y Auditoría 6 — Nivel 4',4,1,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(158,2,'CPA-CPA501','CPA','Contaduría y Auditoría 7 — Nivel 5',4,1,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(159,2,'CPA-CPA502','CPA','Contaduría y Auditoría 8 — Nivel 5',4,1,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(160,2,'CPA-CPA503','CPA','Contaduría y Auditoría 9 — Nivel 5',4,1,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(161,2,'CPA-CPA504','CPA','Contaduría y Auditoría 10 — Nivel 5',4,1,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(162,2,'CPA-CPA505','CPA','Contaduría y Auditoría 11 — Nivel 5',4,1,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(163,2,'CPA-CPA506','CPA','Contaduría y Auditoría 12 — Nivel 5',4,1,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(164,2,'CPA-CPA601','CPA','Contaduría y Auditoría 13 — Nivel 6',4,1,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(165,2,'CPA-CPA602','CPA','Contaduría y Auditoría 14 — Nivel 6',4,1,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(166,2,'CPA-CPA603','CPA','Contaduría y Auditoría 15 — Nivel 6',4,1,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(167,2,'CPA-CPA604','CPA','Contaduría y Auditoría 16 — Nivel 6',4,1,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(168,2,'CPA-CPA605','CPA','Contaduría y Auditoría 17 — Nivel 6',4,1,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(169,2,'CPA-CPA701','CPA','Contaduría y Auditoría 18 — Nivel 7',4,1,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(170,2,'CPA-CPA702','CPA','Contaduría y Auditoría 19 — Nivel 7',4,1,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(171,2,'CPA-CPA703','CPA','Contaduría y Auditoría 20 — Nivel 7',4,1,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(172,2,'CPA-CPA704','CPA','Contaduría y Auditoría 21 — Nivel 7',4,1,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(173,2,'CPA-CPA801','CPA','Contaduría y Auditoría 22 — Nivel 8',4,1,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(174,2,'CPA-CPA802','CPA','Contaduría y Auditoría 23 — Nivel 8',4,1,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(175,2,'CPA-CPA803','CPA','Contaduría y Auditoría 24 — Nivel 8',4,1,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(176,3,'DER-J101','DER','Introducción al Derecho y Estado',4,1,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(177,3,'SOC-J101','SOC','Sociología General',4,1,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(178,3,'FIL-J101','FIL','Filosofía y Lógica Jurídica',4,1,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(179,3,'COM-J101','COM','Expresión Oral y Redacción',4,1,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(180,3,'LIN-J101','LIN','Inglés Técnico Social I',4,1,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(181,3,'INV-J101','INV','Metodología de la Investigación (JUR)',4,1,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(182,3,'DER-J102','DER','Derecho Constitucional I',4,1,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(183,3,'SOC-J102','SOC','Antropología y Sociedad Boliviana',4,1,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(184,3,'PSI-J101','PSI','Fundamentos de Psicología General',4,1,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(185,3,'COM-J102','COM','Argumentación y Oratoria',4,1,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(186,3,'LIN-J102','LIN','Inglés Técnico Social II',4,1,'2026-11-26 03:32:41','2026-11-26 03:32:41'),(187,3,'FIL-J102','FIL','Ética y Deontología Social',4,1,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(188,3,'DER-J201','DER','Derechos Humanos y Garantías',4,1,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(189,3,'SOC-J201','SOC','Resolución Alternativa de Conflictos',4,1,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(190,3,'PSI-J201','PSI','Psicología Social y Comunitaria',4,1,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(191,3,'INV-J201','INV','Investigación Cualitativa',4,1,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(192,3,'COM-J201','COM','Comunicación Institucional',4,1,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(193,3,'FIL-J201','FIL','Epistemología de Ciencias Sociales',4,1,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(194,3,'DER-DER401','DER','Ciencias Jurídicas 1 — Nivel 4',4,1,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(195,3,'DER-DER402','DER','Ciencias Jurídicas 2 — Nivel 4',4,1,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(196,3,'DER-DER403','DER','Ciencias Jurídicas 3 — Nivel 4',4,1,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(197,3,'DER-DER404','DER','Ciencias Jurídicas 4 — Nivel 4',4,1,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(198,3,'DER-DER405','DER','Ciencias Jurídicas 5 — Nivel 4',4,1,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(199,3,'DER-DER406','DER','Ciencias Jurídicas 6 — Nivel 4',4,1,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(200,3,'DER-DER501','DER','Ciencias Jurídicas 7 — Nivel 5',4,1,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(201,3,'DER-DER502','DER','Ciencias Jurídicas 8 — Nivel 5',4,1,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(202,3,'DER-DER503','DER','Ciencias Jurídicas 9 — Nivel 5',4,1,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(203,3,'DER-DER504','DER','Ciencias Jurídicas 10 — Nivel 5',4,1,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(204,3,'DER-DER505','DER','Ciencias Jurídicas 11 — Nivel 5',4,1,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(205,3,'DER-DER506','DER','Ciencias Jurídicas 12 — Nivel 5',4,1,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(206,3,'DER-DER601','DER','Ciencias Jurídicas 13 — Nivel 6',4,1,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(207,3,'DER-DER602','DER','Ciencias Jurídicas 14 — Nivel 6',4,1,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(208,3,'DER-DER603','DER','Ciencias Jurídicas 15 — Nivel 6',4,1,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(209,3,'DER-DER604','DER','Ciencias Jurídicas 16 — Nivel 6',4,1,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(210,3,'DER-DER605','DER','Ciencias Jurídicas 17 — Nivel 6',4,1,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(211,3,'ELE-JUR601','ELE','Electiva 1 (JUR)',4,1,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(212,3,'DER-DER701','DER','Ciencias Jurídicas 18 — Nivel 7',4,1,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(213,3,'DER-DER702','DER','Ciencias Jurídicas 19 — Nivel 7',4,1,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(214,3,'DER-DER703','DER','Ciencias Jurídicas 20 — Nivel 7',4,1,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(215,3,'DER-DER704','DER','Ciencias Jurídicas 21 — Nivel 7',4,1,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(216,3,'ELE-JUR702','ELE','Electiva 2 (JUR)',4,1,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(217,3,'ELE-JUR703','ELE','Electiva 3 (JUR)',4,1,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(218,3,'DER-DER801','DER','Ciencias Jurídicas 22 — Nivel 8',4,1,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(219,3,'DER-DER802','DER','Ciencias Jurídicas 23 — Nivel 8',4,1,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(220,3,'DER-DER803','DER','Ciencias Jurídicas 24 — Nivel 8',4,1,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(221,3,'ELE-JUR804','ELE','Electiva 4 (JUR)',4,1,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(222,3,'INV-JUR401','INV','Seminario de Grado I (DER)',4,1,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(223,3,'INV-JUR402','INV','Proyecto de Grado II (DER)',4,1,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(224,3,'PSI-PSI401','PSI','Psicología Aplicada 1 — Nivel 4',4,1,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(225,3,'PSI-PSI402','PSI','Psicología Aplicada 2 — Nivel 4',4,1,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(226,3,'PSI-PSI403','PSI','Psicología Aplicada 3 — Nivel 4',4,1,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(227,3,'PSI-PSI404','PSI','Psicología Aplicada 4 — Nivel 4',4,1,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(228,3,'PSI-PSI405','PSI','Psicología Aplicada 5 — Nivel 4',4,1,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(229,3,'PSI-PSI406','PSI','Psicología Aplicada 6 — Nivel 4',4,1,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(230,3,'PSI-PSI501','PSI','Psicología Aplicada 7 — Nivel 5',4,1,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(231,3,'PSI-PSI502','PSI','Psicología Aplicada 8 — Nivel 5',4,1,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(232,3,'PSI-PSI503','PSI','Psicología Aplicada 9 — Nivel 5',4,1,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(233,3,'PSI-PSI504','PSI','Psicología Aplicada 10 — Nivel 5',4,1,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(234,3,'PSI-PSI505','PSI','Psicología Aplicada 11 — Nivel 5',4,1,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(235,3,'PSI-PSI506','PSI','Psicología Aplicada 12 — Nivel 5',4,1,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(236,3,'PSI-PSI601','PSI','Psicología Aplicada 13 — Nivel 6',4,1,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(237,3,'PSI-PSI602','PSI','Psicología Aplicada 14 — Nivel 6',4,1,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(238,3,'PSI-PSI603','PSI','Psicología Aplicada 15 — Nivel 6',4,1,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(239,3,'PSI-PSI604','PSI','Psicología Aplicada 16 — Nivel 6',4,1,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(240,3,'PSI-PSI605','PSI','Psicología Aplicada 17 — Nivel 6',4,1,'2026-11-26 03:32:42','2026-11-26 03:32:42'),(241,3,'PSI-PSI701','PSI','Psicología Aplicada 18 — Nivel 7',4,1,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(242,3,'PSI-PSI702','PSI','Psicología Aplicada 19 — Nivel 7',4,1,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(243,3,'PSI-PSI703','PSI','Psicología Aplicada 20 — Nivel 7',4,1,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(244,3,'PSI-PSI704','PSI','Psicología Aplicada 21 — Nivel 7',4,1,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(245,3,'PSI-PSI801','PSI','Psicología Aplicada 22 — Nivel 8',4,1,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(246,3,'PSI-PSI802','PSI','Psicología Aplicada 23 — Nivel 8',4,1,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(247,3,'PSI-PSI803','PSI','Psicología Aplicada 24 — Nivel 8',4,1,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(248,4,'GAS-101','GAS','Técnicas Culinarias Básicas I',4,1,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(249,4,'NUT-101','NUT','Higiene e Inocuidad Alimentaria',4,1,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(250,4,'MAT-G101','MAT','Matemática Aplicada a Gastronomía',4,1,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(251,4,'LIN-G101','LIN','Inglés Gastronómico I',4,1,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(252,4,'INV-G101','INV','Metodología de la Investigación (GAS)',4,1,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(253,4,'GAS-102','GAS','Historia de la Gastronomía',4,1,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(254,4,'GAS-103','GAS','Técnicas Culinarias Básicas II',4,1,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(255,4,'NUT-102','NUT','Nutrición y Dietética I',4,1,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(256,4,'GAS-104','GAS','Panadería Básica',4,1,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(257,4,'LIN-G102','LIN','Inglés Gastronómico II',4,1,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(258,4,'CPA-G101','CPA','Contabilidad Gastronómica',4,1,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(259,4,'GAS-105','GAS','Gastronomía Boliviana Regional',4,1,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(260,4,'GAS-201','GAS','Cocina Caliente I',4,1,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(261,4,'GAS-202','GAS','Pastelería y Repostería I',4,1,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(262,4,'NUT-201','NUT','Química de los Alimentos',4,1,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(263,4,'GAS-203','GAS','Garde Manger y Cocina Fría',4,1,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(264,4,'ADM-G101','ADM','Administración de Restaurantes',4,1,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(265,4,'GAS-204','GAS','Cocina Internacional I',4,1,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(266,4,'GAS-GAS401','GAS','Alta Cocina y Gestión 1 — Nivel 4',4,1,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(267,4,'GAS-GAS402','GAS','Alta Cocina y Gestión 2 — Nivel 4',4,1,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(268,4,'GAS-GAS403','GAS','Alta Cocina y Gestión 3 — Nivel 4',4,1,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(269,4,'GAS-GAS404','GAS','Alta Cocina y Gestión 4 — Nivel 4',4,1,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(270,4,'GAS-GAS405','GAS','Alta Cocina y Gestión 5 — Nivel 4',4,1,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(271,4,'GAS-GAS406','GAS','Alta Cocina y Gestión 6 — Nivel 4',4,1,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(272,4,'GAS-GAS501','GAS','Alta Cocina y Gestión 7 — Nivel 5',4,1,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(273,4,'GAS-GAS502','GAS','Alta Cocina y Gestión 8 — Nivel 5',4,1,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(274,4,'GAS-GAS503','GAS','Alta Cocina y Gestión 9 — Nivel 5',4,1,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(275,4,'GAS-GAS504','GAS','Alta Cocina y Gestión 10 — Nivel 5',4,1,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(276,4,'GAS-GAS505','GAS','Alta Cocina y Gestión 11 — Nivel 5',4,1,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(277,4,'GAS-GAS506','GAS','Alta Cocina y Gestión 12 — Nivel 5',4,1,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(278,4,'GAS-GAS601','GAS','Alta Cocina y Gestión 13 — Nivel 6',4,1,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(279,4,'GAS-GAS602','GAS','Alta Cocina y Gestión 14 — Nivel 6',4,1,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(280,4,'GAS-GAS603','GAS','Alta Cocina y Gestión 15 — Nivel 6',4,1,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(281,4,'GAS-GAS604','GAS','Alta Cocina y Gestión 16 — Nivel 6',4,1,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(282,4,'GAS-GAS605','GAS','Alta Cocina y Gestión 17 — Nivel 6',4,1,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(283,4,'ELE-GAS601','ELE','Electiva 1 (GAS)',4,1,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(284,4,'GAS-GAS701','GAS','Alta Cocina y Gestión 18 — Nivel 7',4,1,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(285,4,'GAS-GAS702','GAS','Alta Cocina y Gestión 19 — Nivel 7',4,1,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(286,4,'GAS-GAS703','GAS','Alta Cocina y Gestión 20 — Nivel 7',4,1,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(287,4,'GAS-GAS704','GAS','Alta Cocina y Gestión 21 — Nivel 7',4,1,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(288,4,'ELE-GAS702','ELE','Electiva 2 (GAS)',4,1,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(289,4,'ELE-GAS703','ELE','Electiva 3 (GAS)',4,1,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(290,4,'GAS-GAS801','GAS','Alta Cocina y Gestión 22 — Nivel 8',4,1,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(291,4,'GAS-GAS802','GAS','Alta Cocina y Gestión 23 — Nivel 8',4,1,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(292,4,'GAS-GAS803','GAS','Alta Cocina y Gestión 24 — Nivel 8',4,1,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(293,4,'ELE-GAS804','ELE','Electiva 4 (GAS)',4,1,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(294,4,'INV-GAS401','INV','Seminario de Grado I (GAS)',4,1,'2026-11-26 03:32:43','2026-11-26 03:32:43'),(295,4,'INV-GAS402','INV','Proyecto de Grado II (GAS)',4,1,'2026-11-26 03:32:43','2026-11-26 03:32:43');
/*!40000 ALTER TABLE `subjects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `task_grades`
--

DROP TABLE IF EXISTS `task_grades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `task_grades` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `offering_task_id` bigint(20) unsigned NOT NULL,
  `enrollment_id` bigint(20) unsigned NOT NULL,
  `submitted_at` timestamp NULL DEFAULT NULL,
  `submission_note` varchar(255) DEFAULT NULL,
  `score` tinyint(3) unsigned DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `graded_by` bigint(20) unsigned DEFAULT NULL,
  `graded_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `task_grades_unique` (`offering_task_id`,`enrollment_id`),
  KEY `task_grades_enrollment_id_foreign` (`enrollment_id`),
  KEY `task_grades_graded_by_foreign` (`graded_by`),
  CONSTRAINT `task_grades_enrollment_id_foreign` FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `task_grades_graded_by_foreign` FOREIGN KEY (`graded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `task_grades_offering_task_id_foreign` FOREIGN KEY (`offering_task_id`) REFERENCES `offering_tasks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `task_grades`
--

LOCK TABLES `task_grades` WRITE;
/*!40000 ALTER TABLE `task_grades` DISABLE KEYS */;
INSERT INTO `task_grades` VALUES (1,1,13,'2026-11-25 09:32:53','Práctica entregada en clase',27,'Excelente estructura orientada a objetos.',2,'2026-11-25 17:32:53','2026-11-26 03:32:53','2026-11-26 03:32:53'),(2,2,13,'2026-11-25 19:32:53','Examen parcial presentado',26,'Buen uso de interfaces.',2,'2026-11-25 23:32:53','2026-11-26 03:32:53','2026-11-26 03:32:53'),(3,3,13,'2026-11-26 02:32:53','Proyecto final presentado hoy',NULL,NULL,NULL,NULL,'2026-11-26 03:32:53','2026-11-26 03:32:53'),(4,4,14,'2026-11-26 00:32:53','Guía resuelta',36,'Correcto',2,'2026-11-26 02:32:53','2026-11-26 03:32:53','2026-11-26 03:32:53'),(5,4,15,'2026-11-26 01:32:53','Entregado en aula',NULL,NULL,NULL,NULL,'2026-11-26 03:32:53','2026-11-26 03:32:53');
/*!40000 ALTER TABLE `task_grades` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `teacher_faculties`
--

DROP TABLE IF EXISTS `teacher_faculties`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `teacher_faculties` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `teacher_id` bigint(20) unsigned NOT NULL,
  `faculty_id` bigint(20) unsigned NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `teacher_faculties_unique` (`teacher_id`,`faculty_id`),
  KEY `teacher_faculties_faculty_id_foreign` (`faculty_id`),
  CONSTRAINT `teacher_faculties_faculty_id_foreign` FOREIGN KEY (`faculty_id`) REFERENCES `faculties` (`id`) ON DELETE CASCADE,
  CONSTRAINT `teacher_faculties_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `teacher_faculties`
--

LOCK TABLES `teacher_faculties` WRITE;
/*!40000 ALTER TABLE `teacher_faculties` DISABLE KEYS */;
INSERT INTO `teacher_faculties` VALUES (1,2,1,1,'2026-11-26 03:32:45','2026-11-26 03:32:45'),(2,3,1,1,'2026-11-26 03:32:46','2026-11-26 03:32:46'),(3,3,2,0,'2026-11-26 03:32:46','2026-11-26 03:32:46'),(4,4,2,1,'2026-11-26 03:32:46','2026-11-26 03:32:46'),(5,5,3,1,'2026-11-26 03:32:47','2026-11-26 03:32:47'),(6,6,4,1,'2026-11-26 03:32:47','2026-11-26 03:32:47');
/*!40000 ALTER TABLE `teacher_faculties` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tuitions`
--

DROP TABLE IF EXISTS `tuitions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tuitions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `career_id` bigint(20) unsigned NOT NULL,
  `semester_id` bigint(20) unsigned NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `max_subjects` tinyint(3) unsigned NOT NULL DEFAULT 6,
  `activated_by` bigint(20) unsigned DEFAULT NULL,
  `activated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tuitions_student_career_semester_unique` (`student_id`,`career_id`,`semester_id`),
  KEY `tuitions_career_id_foreign` (`career_id`),
  KEY `tuitions_semester_id_foreign` (`semester_id`),
  KEY `tuitions_activated_by_foreign` (`activated_by`),
  CONSTRAINT `tuitions_activated_by_foreign` FOREIGN KEY (`activated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tuitions_career_id_foreign` FOREIGN KEY (`career_id`) REFERENCES `careers` (`id`),
  CONSTRAINT `tuitions_semester_id_foreign` FOREIGN KEY (`semester_id`) REFERENCES `semesters` (`id`),
  CONSTRAINT `tuitions_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tuitions`
--

LOCK TABLES `tuitions` WRITE;
/*!40000 ALTER TABLE `tuitions` DISABLE KEYS */;
INSERT INTO `tuitions` VALUES (1,7,1,4,1,6,1,'2026-11-18 03:32:52','2026-11-26 03:32:52','2026-11-26 03:32:52'),(2,7,4,4,1,6,1,'2026-11-18 03:32:52','2026-11-26 03:32:52','2026-11-26 03:32:52'),(3,8,2,4,1,6,1,'2026-11-18 03:32:52','2026-11-26 03:32:52','2026-11-26 03:32:52'),(4,9,3,4,1,6,1,'2026-11-18 03:32:52','2026-11-26 03:32:52','2026-11-26 03:32:52'),(5,10,1,4,0,6,NULL,NULL,'2026-11-26 03:32:52','2026-11-26 03:32:52'),(6,11,8,4,1,6,1,'2026-11-18 03:32:52','2026-11-26 03:32:52','2026-11-26 03:32:52'),(7,12,5,4,1,6,1,'2026-11-18 03:32:52','2026-11-26 03:32:52','2026-11-26 03:32:52'),(8,13,6,4,1,6,1,'2026-11-18 03:32:52','2026-11-26 03:32:52','2026-11-26 03:32:52');
/*!40000 ALTER TABLE `tuitions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'estudiante',
  `code` varchar(30) DEFAULT NULL,
  `document_number` varchar(30) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `faculty_id` bigint(20) unsigned DEFAULT NULL,
  `career_id` bigint(20) unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_code_unique` (`code`),
  KEY `users_faculty_id_foreign` (`faculty_id`),
  KEY `users_career_id_foreign` (`career_id`),
  KEY `users_role_index` (`role`),
  CONSTRAINT `users_career_id_foreign` FOREIGN KEY (`career_id`) REFERENCES `careers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `users_faculty_id_foreign` FOREIGN KEY (`faculty_id`) REFERENCES `faculties` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Lic. Carlos Mendoza','admin@upds.edu.bo',NULL,'$2y$12$PzKxSmFfVNBEP0hlH8/.meJ63nC.pZGXd6/ekcNIcxqCwnfWrhF7i','administrador','ADM-001','4589120 SC','70012345',NULL,NULL,1,NULL,'2026-11-26 03:32:44','2026-11-26 03:32:44'),(2,'Ph.D. Luigi Antequera Tamari','docente@upds.edu.bo',NULL,'$2y$12$egMxq2jKjCOdEzEbL3Qq.e8uiVkK7drWVIBxwAGbzcl3H/KKTk0nK','docente','DOC-101',NULL,'71023456',1,NULL,1,NULL,'2026-11-26 03:32:45','2026-11-26 03:32:45'),(3,'Ing. Valeria Rojas Quiroga','valeria.rojas@upds.edu.bo',NULL,'$2y$12$ThygdtQtOcvhXv/eBH95NO.Vr1tpc1AvGbnuJ1Z1XAdAWPXvsnYx6','docente','DOC-102',NULL,'72034567',1,NULL,1,NULL,'2026-11-26 03:32:46','2026-11-26 03:32:46'),(4,'Mgr. Ricardo Paredes Luna','ricardo.paredes@upds.edu.bo',NULL,'$2y$12$e0tgC.mhXJM0EJBY7Wplqe6c3mqlxlygXsp0DJiUxHw/gMcDt5Zr.','docente','DOC-201',NULL,'72145678',2,NULL,1,NULL,'2026-11-26 03:32:46','2026-11-26 03:32:46'),(5,'Dra. Mónica Arteaga Soliz','monica.arteaga@upds.edu.bo',NULL,'$2y$12$CusYF7VarrMbKaveTbERrOSCaoP85x6uPMMsamryNSl9DN5h6C4ea','docente','DOC-301',NULL,'72256789',3,NULL,1,NULL,'2026-11-26 03:32:47','2026-11-26 03:32:47'),(6,'Chef Fernando Salazar Paredes','chef.salazar@upds.edu.bo',NULL,'$2y$12$XAhnNdiP6YXBf4TFhUP7FuHoKLtGYOOhhWNLlWVYNFfet/80n997e','docente','DOC-401',NULL,'73045678',4,NULL,1,NULL,'2026-11-26 03:32:47','2026-11-26 03:32:47'),(7,'Alexis Morales Vargas','estudiante@upds.edu.bo',NULL,'$2y$12$G6UKEfhsn8q5maAwoYBAh.Asw./TuW9GaGpnB/ZVdMTKCYTlH/oBq','estudiante','EST-202601',NULL,'76011223',NULL,1,1,NULL,'2026-11-26 03:32:48','2026-11-26 03:32:48'),(8,'Camila Torrez Justiniano','camila.torrez@upds.edu.bo',NULL,'$2y$12$NAWWA/YE4HCYjMWQIbTbp.OE1w87ful0B7ISp5tu0jWkQh3vOjRWK','estudiante','EST-202602',NULL,'76122334',NULL,2,1,NULL,'2026-11-26 03:32:49','2026-11-26 03:32:49'),(9,'Diego Fernández Roca','diego.fernandez@upds.edu.bo',NULL,'$2y$12$1nXr9176sVno2bmQPQsCceRNXdWn1TgLUWFJc5VXcsRzQMkKY0KIa','estudiante','EST-202605',NULL,'76455667',NULL,3,1,NULL,'2026-11-26 03:32:50','2026-11-26 03:32:50'),(10,'Mateo Gutiérrez Flores','mateo.gutierrez@upds.edu.bo',NULL,'$2y$12$TcnLGFqczZp6Z1ZzWqSEH.JhIm55B8gCetT3tyPDdJlUrj7AHH802','estudiante','EST-202603',NULL,'76233445',NULL,1,1,NULL,'2026-11-26 03:32:50','2026-11-26 03:32:50'),(11,'Sofía Zambrana Méndez','sofia.zambrana@upds.edu.bo',NULL,'$2y$12$UBz0rxq4BuVGbKu5/mGje.gC4fx86FTr2zFhRQiOAGp2WNk9fZ9g.','estudiante','EST-202604',NULL,'76344556',NULL,8,1,NULL,'2026-11-26 03:32:51','2026-11-26 03:32:51'),(12,'Lucía Chávez Vaca','lucia.chavez@upds.edu.bo',NULL,'$2y$12$SpSzJuyOulBeCAkRV3Vu3.dTHVfSvnpvlxlW4f3dLtRxIoZQ6XrQW','estudiante','EST-202606',NULL,'76566778',NULL,5,1,NULL,'2026-11-26 03:32:52','2026-11-26 03:32:52'),(13,'Sebastián Ortiz Paz','sebastian.ortiz@upds.edu.bo',NULL,'$2y$12$tN.O3Z9n0ufeccLgO0CnGehIdsgyyQwQUzP7sx3hgZqoHH34liQaG','estudiante','EST-202607',NULL,'76677889',NULL,6,1,NULL,'2026-11-26 03:32:52','2026-11-26 03:32:52');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'gestion_academica'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-11-25 22:17:24
