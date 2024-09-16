-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping structure for table ascots_db.tblscholar_application
CREATE TABLE IF NOT EXISTS `tblscholar_application` (
  `id` int NOT NULL AUTO_INCREMENT,
  `studentId` int NOT NULL,
  `scholarId` int NOT NULL,
  `dateApplied` varchar(50) NOT NULL DEFAULT '',
  `requirementId` text NOT NULL,
  `evaluatedBy` int NOT NULL,
  `dateEvaluated` varchar(50) NOT NULL DEFAULT '',
  `remarks` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table ascots_db.tblscholar_application: ~0 rows (approximately)
DELETE FROM `tblscholar_application`;

-- Dumping structure for table ascots_db.tblusers
CREATE TABLE IF NOT EXISTS `tblusers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Student Number',
  `password` text NOT NULL,
  `firstName` varchar(255) NOT NULL,
  `lastName` varchar(255) NOT NULL,
  `middleName` varchar(255) NOT NULL,
  `suffix` varchar(255) NOT NULL,
  `sex` varchar(255) NOT NULL,
  `civilStatus` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `contact` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `placeOfBirth` text NOT NULL,
  `dateOfBirth` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `schoolAttended` text NOT NULL,
  `schoolAddress` text NOT NULL,
  `userType` int NOT NULL,
  `status` int NOT NULL DEFAULT '1',
  `isDeleted` int NOT NULL DEFAULT '0',
  `createdAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table ascots_db.tblusers: ~0 rows (approximately)
DELETE FROM `tblusers`;
INSERT INTO `tblusers` (`id`, `username`, `password`, `firstName`, `lastName`, `middleName`, `suffix`, `sex`, `civilStatus`, `email`, `contact`, `address`, `placeOfBirth`, `dateOfBirth`, `schoolAttended`, `schoolAddress`, `userType`, `status`, `isDeleted`, `createdAt`, `updatedAt`) VALUES
	(1, '1300183200', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 'asd', 'asd', 'a', '', 'Male', 'Single', 'asdasd@mail.com', '09876543211', 'test address', 'Aurora', '1997-06-13', 'Aurora State Collage Of Technology', 'Baler Address', 2, 1, 0, '2024-09-16 01:09:13', '2024-09-16 01:09:13'),
	(2, '130032500', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 'ccx', 'xczx', 'ccc', '', 'Female', 'Single', 'sdadsa@mail.com', '09123456789', 'adsdasdasdasdasd', 'Cabanatuan', '1999-06-17', 'Aurora State College Of Technology', 'Aurora Address ', 2, 1, 0, '2024-09-16 01:39:02', '2024-09-16 01:39:02');

-- Dumping structure for table ascots_db.tblusertypes
CREATE TABLE IF NOT EXISTS `tblusertypes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `description` varchar(255) NOT NULL,
  `modules` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table ascots_db.tblusertypes: ~2 rows (approximately)
DELETE FROM `tblusertypes`;
INSERT INTO `tblusertypes` (`id`, `description`, `modules`) VALUES
	(1, 'Super Admin', '101,102'),
	(2, 'Operation', '101,102');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
