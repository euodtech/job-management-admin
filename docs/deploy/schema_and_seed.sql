-- MariaDB dump 10.19  Distrib 10.4.27-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: Efms
-- ------------------------------------------------------
-- Server version	10.4.27-MariaDB

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
-- Table structure for table `AuditLog`
--

DROP TABLE IF EXISTS `AuditLog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AuditLog` (
  `AuditLogID` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `ip` varchar(45) NOT NULL,
  `details` text DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`AuditLogID`),
  KEY `idx_action` (`action`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ci_sessions`
--

DROP TABLE IF EXISTS `ci_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_sessions` (
  `id` varchar(128) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` int(10) unsigned NOT NULL DEFAULT 0,
  `data` blob NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ci_sessions_timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Customer`
--

DROP TABLE IF EXISTS `Customer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Customer` (
  `CustomerID` int(11) NOT NULL AUTO_INCREMENT,
  `ListCompanyID` int(11) DEFAULT NULL,
  `CustomerName` varchar(255) NOT NULL,
  `CustomerEmail` varchar(100) NOT NULL,
  `AccountNumber` varchar(255) NOT NULL DEFAULT '',
  `PhoneNumber` varchar(255) NOT NULL,
  `Address` text NOT NULL,
  `Latitude` varchar(255) DEFAULT NULL,
  `Longitude` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`CustomerID`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `DataRaw`
--

DROP TABLE IF EXISTS `DataRaw`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `DataRaw` (
  `RawID` int(11) NOT NULL AUTO_INCREMENT,
  `Data` text NOT NULL,
  PRIMARY KEY (`RawID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `HistoryCancelJob`
--

DROP TABLE IF EXISTS `HistoryCancelJob`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `HistoryCancelJob` (
  `HistoryCancelJobID` int(11) NOT NULL AUTO_INCREMENT,
  `JobID` int(11) NOT NULL,
  `UserBefore` int(11) NOT NULL,
  `Reason` text NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`HistoryCancelJobID`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ListCompany`
--

DROP TABLE IF EXISTS `ListCompany`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ListCompany` (
  `ListCompanyID` int(11) NOT NULL AUTO_INCREMENT,
  `CompanyLogo` varchar(255) NOT NULL,
  `CompanyCode` varchar(255) NOT NULL,
  `CompanyName` varchar(255) NOT NULL,
  `CompanyPhone` varchar(255) DEFAULT NULL,
  `CompanyEmail` varchar(255) NOT NULL,
  `UserLoginID` int(11) NOT NULL,
  `CompanySubscribe` int(11) NOT NULL COMMENT '1 = Basic, 2 = Pro',
  `OrgName` varchar(100) DEFAULT NULL,
  `TimeZone` varchar(100) DEFAULT NULL,
  `Geocoder` varchar(100) DEFAULT NULL,
  `Language` varchar(100) DEFAULT NULL,
  `username_traxroot` varchar(500) DEFAULT NULL,
  `password_traxroot` varchar(500) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`ListCompanyID`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ListJob`
--

DROP TABLE IF EXISTS `ListJob`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ListJob` (
  `JobID` int(11) NOT NULL AUTO_INCREMENT,
  `JobName` varchar(255) NOT NULL,
  `UserID` int(11) DEFAULT NULL,
  `CustomerID` int(11) NOT NULL,
  `CompanyID` int(11) DEFAULT NULL,
  `TypeJob` int(11) NOT NULL COMMENT '1 = Line Interrupt, 2 = Reconnection, 3 = Short Circuit',
  `CreatedBy` int(11) DEFAULT NULL,
  `JobDate` datetime DEFAULT NULL,
  `Status` int(11) DEFAULT NULL COMMENT '1 = Ongoing, 2 = Finished Job, 3 = Reschedule Job',
  `Notes` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `AssignWhen` datetime DEFAULT NULL,
  `FinishWhen` datetime DEFAULT NULL,
  PRIMARY KEY (`JobID`)
) ENGINE=InnoDB AUTO_INCREMENT=131 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ListJobDetail`
--

DROP TABLE IF EXISTS `ListJobDetail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ListJobDetail` (
  `ListDetailID` int(11) NOT NULL AUTO_INCREMENT,
  `ListJobID` int(11) NOT NULL,
  `Photo` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ListDetailID`)
) ENGINE=InnoDB AUTO_INCREMENT=133 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ListUser`
--

DROP TABLE IF EXISTS `ListUser`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ListUser` (
  `UserID` int(11) NOT NULL AUTO_INCREMENT,
  `Fullname` varchar(255) NOT NULL,
  `ListCompanyID` int(11) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `PhoneNumber` varchar(100) NOT NULL,
  `StatusActive` int(11) NOT NULL COMMENT '0 = Active 1 = Non Active',
  `UserLoginID` int(11) DEFAULT NULL,
  `Category` varchar(100) DEFAULT NULL,
  `Rank` varchar(100) DEFAULT NULL,
  `License` varchar(100) DEFAULT NULL,
  `LicenseValidUntil` varchar(100) DEFAULT NULL,
  `InternalID` varchar(100) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `UserRole` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`UserID`)
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `MasterVehicle`
--

DROP TABLE IF EXISTS `MasterVehicle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `MasterVehicle` (
  `MasterVehicleID` int(11) NOT NULL AUTO_INCREMENT,
  `TraxrootID` int(11) NOT NULL,
  `VehicleName` varchar(255) NOT NULL,
  `CompanyCode` varchar(255) NOT NULL,
  PRIMARY KEY (`MasterVehicleID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Notif`
--

DROP TABLE IF EXISTS `Notif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Notif` (
  `NotifID` int(11) NOT NULL AUTO_INCREMENT,
  `PendingDate` datetime NOT NULL,
  `DeliveryDate` datetime DEFAULT NULL,
  `UserLoginID` int(11) DEFAULT NULL,
  `FirebaseToken` varchar(200) DEFAULT NULL,
  `NotifTitle` varchar(250) NOT NULL,
  `NotifMessage` varchar(250) DEFAULT NULL,
  `NotifImage` varchar(250) NOT NULL,
  `NotifStatus` varchar(250) NOT NULL,
  `Type` varchar(150) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`NotifID`),
  KEY `CustomerID` (`UserLoginID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `RescheduledJob`
--

DROP TABLE IF EXISTS `RescheduledJob`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `RescheduledJob` (
  `RescheduledID` int(11) NOT NULL AUTO_INCREMENT,
  `JobID` int(11) NOT NULL,
  `CurrentDateJob` date NOT NULL,
  `RescheduledDateJob` date NOT NULL,
  `Reason` text NOT NULL,
  `ReasonReject` text DEFAULT NULL,
  `StatusApproved` int(11) NOT NULL COMMENT '1 = Pending, 2 = Approve = 3 = Reject',
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`RescheduledID`)
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `UserLogin`
--

DROP TABLE IF EXISTS `UserLogin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `UserLogin` (
  `UserLoginID` int(11) NOT NULL AUTO_INCREMENT,
  `Fullname` varchar(255) NOT NULL,
  `Email` varchar(150) NOT NULL,
  `Password` varchar(150) NOT NULL,
  `Role` text DEFAULT NULL COMMENT '1 = Superuser, 2 = User, 3 = Admin User',
  `ApiKey` varchar(255) DEFAULT NULL,
  `FirebaseToken` varchar(255) DEFAULT NULL,
  `key_resetpassword` varchar(255) DEFAULT NULL,
  `LastLogin` datetime DEFAULT NULL,
  `LastActivity` datetime DEFAULT NULL,
  PRIMARY KEY (`UserLoginID`)
) ENGINE=InnoDB AUTO_INCREMENT=98 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-02-28 20:44:39

-- =============================================
-- SEED: Superadmin user for fresh deployment
-- Email: admin@euodoo.com.ph
-- Default password: Admin@2026! (change after first login!)
-- Role: 1 (superadmin — sees all companies)
-- =============================================
INSERT INTO `UserLogin` (`Fullname`, `Email`, `Password`, `Role`)
VALUES ('Super Admin', 'admin@euodoo.com.ph', '$2y$10$uMcRulnW6IWLfjIOqVZlcOzaSFTNd1YvX0P0uNZ3/zXQmPwv5B6VS', 1);
