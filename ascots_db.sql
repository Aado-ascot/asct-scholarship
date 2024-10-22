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

-- Dumping structure for table ascots_db.tblattachments
CREATE TABLE IF NOT EXISTS `tblattachments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `userId` int NOT NULL DEFAULT '0',
  `reqType` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `fileName` longtext NOT NULL,
  `fileSize` bigint NOT NULL DEFAULT '0',
  `status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `uploadFile` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `remarks` longtext NOT NULL,
  `createdDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table ascots_db.tblattachments: ~0 rows (approximately)
DELETE FROM `tblattachments`;

-- Dumping structure for table ascots_db.tblcourses
CREATE TABLE IF NOT EXISTS `tblcourses` (
  `id` int NOT NULL AUTO_INCREMENT,
  `codeName` varchar(255) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `status` int NOT NULL DEFAULT '1',
  `createdDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `createdBy` int DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table ascots_db.tblcourses: ~0 rows (approximately)
DELETE FROM `tblcourses`;
INSERT INTO `tblcourses` (`id`, `codeName`, `title`, `status`, `createdDate`, `createdBy`) VALUES
	(1, 'BSCE', 'Civil Engineering', 1, '2024-10-19 23:20:28', 0),
	(2, 'BSEE', 'Electrical Engineering', 1, '2024-10-19 23:20:42', 0),
	(3, 'BSF', 'Forestry', 1, '2024-10-19 23:21:20', 0),
	(4, 'BSINT', 'Industrial Technology', 1, '2024-10-19 23:22:04', 0),
	(5, 'BSIT', 'Information Technology', 1, '2024-10-19 23:23:04', 0),
	(6, 'BSME', 'Mechanical Engineer', 1, '2024-10-19 23:23:28', 0),
	(7, 'BSE', 'Secondary Education, Major in Mathematics', 1, '2024-10-19 23:24:24', 0),
	(8, 'BSE', 'Secondary Education, Major in Science', 1, '2024-10-19 23:24:34', 0),
	(9, 'BSICT', 'TLE, Major in Information and Communication Technology', 1, '2024-10-19 23:25:49', 0);

-- Dumping structure for table ascots_db.tblscholarships
CREATE TABLE IF NOT EXISTS `tblscholarships` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` text NOT NULL,
  `qualification` longtext NOT NULL,
  `coveredCourses` text NOT NULL,
  `otherDetailsLink` text NOT NULL,
  `requirements` longtext NOT NULL,
  `createdDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dueDate` text NOT NULL,
  `createdBy` int NOT NULL DEFAULT '0',
  `status` int NOT NULL DEFAULT '0',
  `slot` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table ascots_db.tblscholarships: ~0 rows (approximately)
DELETE FROM `tblscholarships`;

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
  `courseId` int NOT NULL DEFAULT '0',
  `yrLvl` varchar(255) NOT NULL DEFAULT '',
  `schoolAttended` text NOT NULL,
  `schoolAddress` text NOT NULL,
  `userType` int NOT NULL,
  `status` int NOT NULL DEFAULT '1',
  `isDeleted` int NOT NULL DEFAULT '0',
  `createdAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table ascots_db.tblusers: ~3 rows (approximately)
DELETE FROM `tblusers`;
INSERT INTO `tblusers` (`id`, `username`, `password`, `firstName`, `lastName`, `middleName`, `suffix`, `sex`, `civilStatus`, `email`, `contact`, `address`, `placeOfBirth`, `dateOfBirth`, `courseId`, `yrLvl`, `schoolAttended`, `schoolAddress`, `userType`, `status`, `isDeleted`, `createdAt`, `updatedAt`) VALUES
	(1, 'admin', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 'Super', 'Admin', '', '', 'Male', 'Single', 'ascot.admin@mail.com', '09876543211', 'test address', 'Aurora', '1997-06-13', 5, 'Admin', '', '', 1, 1, 0, '2024-09-16 01:09:13', '2024-09-16 01:09:13'),
	(3, '11-11-1111', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 'asd', 'asd', '', '', 'Male', 'Single', 'asdasd', '21321321321', 'asdasd', 'asdasd', '2024-10-31', 0, '', 'asdasdsa', 'asdsadsa', 2, 1, 0, '2024-10-20 12:30:51', '2024-10-20 12:30:51'),
	(4, 's.unit', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 'Unit', 'Scholarship', ' ', ' ', 'Male', 'Single', 'test@mail.com', '0987654321', 'test', 'test', '2024-10-31', 0, 'Scholar Unit', '  ', '  ', 3, 1, 0, '2024-10-22 06:43:51', '2024-10-22 06:43:51');

-- Dumping structure for table ascots_db.tblusertypes
CREATE TABLE IF NOT EXISTS `tblusertypes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `description` varchar(255) NOT NULL,
  `modules` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table ascots_db.tblusertypes: ~3 rows (approximately)
DELETE FROM `tblusertypes`;
INSERT INTO `tblusertypes` (`id`, `description`, `modules`) VALUES
	(1, 'Super Admin', '201,202,203'),
	(2, 'Student', '101,102'),
	(3, 'Scholarship Unit', '201,203');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
