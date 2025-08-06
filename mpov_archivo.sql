/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.6.22-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: mpv_archivo
-- ------------------------------------------------------
-- Server version	10.6.22-MariaDB-0ubuntu0.22.04.1

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
-- Table structure for table `documentos_adjuntos`
--

DROP TABLE IF EXISTS `documentos_adjuntos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `documentos_adjuntos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `registro_documental_id` bigint(20) unsigned NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `ruta` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `documentos_adjuntos_registro_documental_id_foreign` (`registro_documental_id`),
  CONSTRAINT `documentos_adjuntos_registro_documental_id_foreign` FOREIGN KEY (`registro_documental_id`) REFERENCES `registros_documentales` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `documentos_adjuntos`
--

LOCK TABLES `documentos_adjuntos` WRITE;
/*!40000 ALTER TABLE `documentos_adjuntos` DISABLE KEYS */;
/*!40000 ALTER TABLE `documentos_adjuntos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `entidades`
--

DROP TABLE IF EXISTS `entidades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `entidades` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `tipo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `entidades`
--

LOCK TABLES `entidades` WRITE;
/*!40000 ALTER TABLE `entidades` DISABLE KEYS */;
INSERT INTO `entidades` VALUES (1,'MUNICIPALIDAD DISTRTITAL DE COLQUEMARCA','1',NULL,NULL);
/*!40000 ALTER TABLE `entidades` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
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
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2014_10_12_000000_create_users_table',1),(2,'2014_10_12_100000_create_password_reset_tokens_table',1),(3,'2019_08_19_000000_create_failed_jobs_table',1),(4,'2019_12_14_000001_create_personal_access_tokens_table',1),(5,'2021_07_30_161218_create_entidades_table',1),(6,'2022_07_30_161218_create_oficinas_table',1),(7,'2024_07_30_161218_create_registros_documentales_table',1),(8,'2025_07_30_161218_create_documentos_adjuntos_table',1),(9,'2025_07_30_161219_create_movimientos_table',1),(10,'2024_10_21_102814_create_pagosonline_table',2);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `movimientos`
--

DROP TABLE IF EXISTS `movimientos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `movimientos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `registro_documental_id` bigint(20) unsigned NOT NULL,
  `tipo` enum('PRESTAMO','DEVOLUCION') NOT NULL,
  `usuario` varchar(255) NOT NULL,
  `comentario` text DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `movimientos_registro_documental_id_foreign` (`registro_documental_id`),
  CONSTRAINT `movimientos_registro_documental_id_foreign` FOREIGN KEY (`registro_documental_id`) REFERENCES `registros_documentales` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `movimientos`
--

LOCK TABLES `movimientos` WRITE;
/*!40000 ALTER TABLE `movimientos` DISABLE KEYS */;
/*!40000 ALTER TABLE `movimientos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `oficinas`
--

DROP TABLE IF EXISTS `oficinas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `oficinas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `entidad_id` bigint(20) unsigned NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `seccion` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oficinas_entidad_id_foreign` (`entidad_id`),
  CONSTRAINT `oficinas_entidad_id_foreign` FOREIGN KEY (`entidad_id`) REFERENCES `entidades` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `oficinas`
--

LOCK TABLES `oficinas` WRITE;
/*!40000 ALTER TABLE `oficinas` DISABLE KEYS */;
INSERT INTO `oficinas` VALUES (2,1,'GERENCIA DE DESARROLLO TERRITORIAL E INFRAESTRUCTURA','GERENCIA DE DESARROLLO TERRITORIAL E INFRAESTRUCTURA','2025-07-31 00:09:34','2025-08-04 10:30:27'),(3,1,'GERENCIA DE DESARROLLO SOCIAL','GERENCIA DE DESARROLLO SOCIAL','2025-07-31 00:09:45','2025-08-04 10:14:04'),(4,1,'CONTABILIDAD','CONTABILIDAD','2025-07-31 00:11:15','2025-08-04 10:10:27'),(6,1,'SECRETARIA GENERAL','SECRETARIA GENERAL','2025-08-04 10:09:39','2025-08-04 10:09:39'),(7,1,'UNIDAD DE PATRIMONIO','UNIDAD DE PATRIMONIO','2025-08-04 10:10:10','2025-08-04 10:10:10'),(8,1,'GERENCIA DE DESARROLLO ECONOMICO LOCAL','GERENCIA DE DESARROLLO ECONOMICO LOCAL','2025-08-04 10:31:12','2025-08-04 10:31:12'),(9,1,'GERENCIA DE GESTION AMBIENTAL Y SERVICIOS PUBLICOS','GERENCIA DE GESTION AMBIENTAL Y SERVICIOS PUBLICOS','2025-08-04 10:31:59','2025-08-04 10:31:59'),(10,1,'ALCALDÍA','ALCALDÍA','2025-08-04 10:32:36','2025-08-04 10:32:36'),(11,1,'GERENCIA MUNICIPAL','GERENCIA MUNICIPAL','2025-08-04 10:33:02','2025-08-04 10:33:02'),(12,1,'UNIDAD DE ARCHIVO CENTRAL','UNIDAD DE ARCHIVO CENTRAL','2025-08-05 14:51:54','2025-08-05 14:51:54'),(13,1,'RECURSOS HUMANOS','RECURSOS HUMANOS','2025-08-05 14:53:13','2025-08-05 14:53:13'),(14,1,'OFICINA DE CONTRATACIONES','OFICINA DE CONTRATACIONES','2025-08-05 14:55:27','2025-08-05 14:55:27'),(15,1,'UNIDAD DE ALMACEN','UNIDAD DE ALMACEN','2025-08-05 14:55:56','2025-08-05 14:55:56'),(16,1,'OFICINA DE TESORERÍA','OFICINA DE TESORERÍA','2025-08-05 14:57:38','2025-08-05 14:57:38'),(17,1,'OFICINA GENERAL DE PLANEAMIENTO Y PRESUPUESTO','OFICINA GENERAL DE PLANEAMIENTO Y PRESUPUESTO','2025-08-05 14:58:43','2025-08-05 14:58:43'),(18,1,'OFICINA DE PROGRAMACIÓN MULTIANUAL DE INVERSIONES (OPMI)','OFICINA DE PROGRAMACIÓN MULTIANUAL DE INVERSIONES (OPMI)','2025-08-05 15:00:00','2025-08-05 15:00:00'),(19,1,'OFICINA DE GESTIÓN DE RIESGOS DE DESASTRES','OFICINA DE GESTIÓN DE RIESGOS DE DESASTRES','2025-08-05 15:01:27','2025-08-05 15:01:27'),(20,1,'ASESORIA JURIDICA','ASESORIA JURIDICA','2025-08-05 15:01:51','2025-08-05 15:01:51'),(21,1,'UNIDAD DE EQUIPO MECANICO Y MAESTRANZA','UNIDAD DE EQUIPO MECANICO Y MAESTRANZA','2025-08-05 15:03:55','2025-08-05 15:03:55'),(22,1,'UNIDAD DE PVL','UNIDAD DE PVL','2025-08-05 15:04:29','2025-08-05 15:04:29'),(23,1,'UNIDAD DE DEMUNA','UNIDAD DE DEMUNA','2025-08-05 15:04:48','2025-08-05 15:04:48'),(24,1,'OMAPED','OMAPED','2025-08-05 15:05:04','2025-08-05 15:05:04'),(25,1,'UNIDAD LOCAL DE EMPADRONAMIENTO-ULE','UNIDAD LOCAL DE EMPADRONAMIENTO-ULE','2025-08-05 15:06:05','2025-08-05 15:06:05'),(26,1,'CENTRO INTEGRAL DE ATENCIÓN AL ADULTO MAYOR-CIAM','CENTRO INTEGRAL DE ATENCIÓN AL ADULTO MAYOR-CIAM','2025-08-05 15:07:25','2025-08-05 15:07:25'),(27,1,'AREA TECNICA MUNICIPAL','AREA TECNICA MUNICIPAL','2025-08-05 15:07:53','2025-08-05 15:07:53'),(28,1,'UNIDAD DE REGISTRO CIVIL','UNIDAD DE REGISTRO CIVIL','2025-08-05 15:08:52','2025-08-05 15:08:52');
/*!40000 ALTER TABLE `oficinas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pagosonline`
--

DROP TABLE IF EXISTS `pagosonline`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `pagosonline` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `usuario_id` bigint(20) unsigned NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `token` varchar(255) NOT NULL,
  `fecha_pago` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_vigencia` timestamp NOT NULL DEFAULT current_timestamp(),
  `estado` enum('pendiente','pagado') NOT NULL DEFAULT 'pendiente',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pagosonline`
--

LOCK TABLES `pagosonline` WRITE;
/*!40000 ALTER TABLE `pagosonline` DISABLE KEYS */;
INSERT INTO `pagosonline` VALUES (1,1,6.00,'sssss','2025-02-10 21:29:17','2026-02-10 21:29:17','pagado',NULL,NULL);
/*!40000 ALTER TABLE `pagosonline` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
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
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `registros_documentales`
--

DROP TABLE IF EXISTS `registros_documentales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `registros_documentales` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `oficina_id` bigint(20) unsigned NOT NULL,
  `entidad_id` bigint(20) unsigned NOT NULL,
  `periodo` varchar(255) NOT NULL,
  `anio_elaboracion` year(4) NOT NULL,
  `seccion` varchar(255) NOT NULL,
  `fechas_extremos` varchar(255) NOT NULL,
  `item` varchar(255) NOT NULL,
  `nro_archivo` varchar(255) NOT NULL,
  `unidad_conservacion` varchar(255) NOT NULL,
  `serie_documental` varchar(255) NOT NULL,
  `nro_comprobantes` varchar(255) NOT NULL,
  `ubicacion_estante` varchar(255) NOT NULL,
  `valor_serie_documental` enum('T','P') NOT NULL,
  `folios` varchar(255) NOT NULL,
  `soporte_papel` varchar(255) NOT NULL,
  `es_copia_original` tinyint(1) NOT NULL,
  `anio_extremo_inicio` varchar(255) NOT NULL,
  `anio_extremo_fin` varchar(255) NOT NULL,
  `color` varchar(255) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `estado_archivador` enum('B','M','R') NOT NULL,
  `ubicacion_actual` enum('A.C','PRESTAMO','DEVUELTO') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `registros_documentales_oficina_id_foreign` (`oficina_id`),
  KEY `registros_documentales_entidad_id_foreign` (`entidad_id`),
  CONSTRAINT `registros_documentales_entidad_id_foreign` FOREIGN KEY (`entidad_id`) REFERENCES `entidades` (`id`) ON DELETE CASCADE,
  CONSTRAINT `registros_documentales_oficina_id_foreign` FOREIGN KEY (`oficina_id`) REFERENCES `oficinas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `registros_documentales`
--

LOCK TABLES `registros_documentales` WRITE;
/*!40000 ALTER TABLE `registros_documentales` DISABLE KEYS */;
INSERT INTO `registros_documentales` VALUES (16,6,1,'2023-2026',2025,'SECRETARIA GENERAL','2025-08-04','2','01','ARCHIVADOR DE PALANCA','RESOLUCIÓN DE ALCALDÍA (TOMO I)','001-037','SEC.G-19','P','001-384','PAPEL',1,'2023','2023','ROJO','N/A','R','A.C','2025-08-04 14:10:08','2025-08-05 12:13:00'),(17,6,1,'2023-2026',2025,'SECRETARIA GENERAL','2025-08-04','3','02','ARCHIVADOR DE PALANCA','RESOLUCIÓN DE ALCALDÍA (TOMO II)','038-067','SEC.G-19','P','001-525','PAPEL',1,'2023','2023','ROJO','N/A','R','A.C','2025-08-04 16:39:20','2025-08-05 12:14:20'),(18,6,1,'2023-2026',2025,'SECRETARIA GENERAL','2025-08-05','4','03','ARCHIVADOR DE PALANCA','RESOLUCIÓN DE ALCALDÍA (TOMO III)','068-094','SEC.G-19','P','001-436','PAPEL',1,'2023','2023','ROJO','N/A','R','A.C','2025-08-05 08:34:11','2025-08-05 12:14:36'),(19,6,1,'2023-2026',2025,'SECRETARIA GENERAL','2025-08-05','5','04','ARCHIVADOR DE PALANCA','RESOLUCIÓN DE ALCALDÍA (TOMO IV)','095-143','SEC.G-19','P','001-455','PAPEL',1,'2023','2023','ROJO','RESOLUCION DE ALCALDIA N 104, 135 NO SE EMITIÓ','R','A.C','2025-08-05 12:19:57','2025-08-05 12:19:57'),(20,6,1,'2023-2026',2025,'SECRETARIA GENERAL','2025-08-05','6','05','ARCHIVADOR DE PALANCA','RESOLUCIÓN DE ALCALDÍA (TOMO V)','144-197','SEC.G-19','P','001-530','PAPEL',1,'2023','2023','ROJO',NULL,'R','A.C','2025-08-05 15:45:03','2025-08-05 15:45:03'),(21,6,1,'2023-2026',2025,'SECRETARIA GENERAL','2025-08-05','7','06','ARCHIVADOR DE PALANCA','RESOLUCIÓN DE ALCALDÍA (TOMO VI)','199-220','SEC.G-19','P','001-489','PAPEL',1,'2023','2023','ROJO','N/A','R','A.C','2025-08-05 15:55:28','2025-08-05 15:55:28');
/*!40000 ALTER TABLE `registros_documentales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'FRANCISCO LEO CHAMBI','grupo90pr@gmail.com',NULL,'$2y$12$5hBP1WeN1HriDB.1oHBL5eSF6xPM3MF.pJtgmg/uRX943v3ylXDju',NULL,NULL,NULL),(2,'RAMIRO TINTAYA SALAS','ramirotintayasalas1997@gmail.com',NULL,'$2y$12$ARaiTMi5yOLi.EC.QPkfzuxC.unHNMtqEMW.nTD0V50dnznrv78G.',NULL,'2025-08-04 09:47:54','2025-08-04 09:47:54');
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

-- Dump completed on 2025-08-06  2:47:21
