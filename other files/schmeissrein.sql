-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 22, 2020 at 10:01 AM
-- Server version: 10.4.11-MariaDB
-- PHP Version: 7.2.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `schwarzesbrett`
--
CREATE DATABASE IF NOT EXISTS `schmeissrein` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `schmeissrein`;

-- --------------------------------------------------------

--
-- Table structure for table `benutzerkonto`
--

CREATE TABLE `benutzerkonto` (
  `BenutzerkontoID` int(10) UNSIGNED NOT NULL,
  `Vorname` varchar(20) NOT NULL,
  `Nachname` varchar(20) NOT NULL,
  `Benutzername` varchar(20) NOT NULL,
  `Email` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `benutzerkonto`
--

INSERT INTO `benutzerkonto` (`BenutzerkontoID`, `Vorname`, `Nachname`, `Benutzername`, `Email`) VALUES
(3, '', '', 'SuperHans', 'hmueller@gmx.de'),
(4, '', '', 'MegaJens', 'jmeier@hotmail.com'),
(5, '', '', 'Lord Rexon The Destr', 'stardestroyer999@gmail.com'),
(6, '', '', 'mk222', 'mk222@email.net'),
(7, '', '', 'SuperSonic', 'smeiners@oldenburg.de'),
(8, '', '', 'JaJaJaJa', 'ufuk361@gmail.com'),
(9, '', '', 'C', 'a@b.de');

-- --------------------------------------------------------

--
-- Table structure for table `kontaktdaten`
--

CREATE TABLE `kontaktdaten` (
  `Email` varchar(50) NOT NULL,
  `Telefonnummer` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `kontaktdaten`
--

INSERT INTO `kontaktdaten` (`Email`, `Telefonnummer`) VALUES
('a@b.de', NULL),
('hmueller@gmx.de', NULL),
('jmeier@hotmail.com', NULL),
('mk222@email.net', NULL),
('smeiners@oldenburg.de', NULL),
('stardestroyer999@gmail.com', NULL),
('ufuk361@gmail.com', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `rezepte`
--

CREATE TABLE `rezepte` (
  `rezepteid` int(10) UNSIGNED NOT NULL,
  `titel` longtext NOT NULL,
  `zutaten` longtext NOT NULL,
  `beschreibung` longtext NOT NULL,
  `BenutzerkontoID` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



--
-- Table structure for table `zugangsberechtigung`
--

CREATE TABLE `zugangsberechtigung` (
  `Benutzername` varchar(20) NOT NULL,
  `Passwort` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `zugangsberechtigung`
--

INSERT INTO `zugangsberechtigung` (`Benutzername`, `Passwort`) VALUES
('C', '$2y$10$pV00IgTWHAgVrUSH1Lr1qeaezyA771F.x3SAUwvRQU5ADbOqHclHe'),
('JaJaJaJa', '$2y$10$bt8EQZVKKopn3c/EufTepeWUXelitYerMLU1TgW/5q1.r66/Lblx2'),
('Lord Rexon The Destr', '$2y$10$ujrLjpLDQmCz5ax7rV3ZgemUsX2ukvPPbhxxOZS1Z0Vl/xlex7QMC'),
('MegaJens', '$2y$10$NAENRotYrHloEJew1q58YOgb.5WzdNxzq/.FvqQngReA0Ls.5iIf2'),
('mk222', '$2y$10$K/Yzymwm4dqSNiRbMsVUWut5dzKoda4bBlq0fHsQlPytvNtqNr09G'),
('SuperHans', '$2y$10$xRKPhH8qnrWWyRDUh46GmOcrSX1FhkZkGdYHU4EQBC9liwqGtJUYq'),
('SuperSonic', '$2y$10$1oAXAb8Nd5g6DbfGNSCbJeOtlVRs7XsZzOT8aCJL7SsVS6/lkL5jO');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `benutzerkonto`
--
ALTER TABLE `benutzerkonto`
  ADD PRIMARY KEY (`BenutzerkontoID`),
  ADD KEY `´fk_BenutzernameInZugangsberechtigung´` (`Benutzername`),
  ADD KEY `´fk_EmailInKontaktdaten´` (`Email`);

--
-- Indexes for table `kontaktdaten`
--
ALTER TABLE `kontaktdaten`
  ADD PRIMARY KEY (`Email`);

--
-- Indexes for table `rezepte`
--
ALTER TABLE `rezepte`
  ADD PRIMARY KEY (`rezepteid`),
  ADD KEY `´fk_BenutzerkontoIDInBenutzerkonto´` (`BenutzerkontoID`);

--
-- Indexes for table `zugangsberechtigung`
--
ALTER TABLE `zugangsberechtigung`
  ADD PRIMARY KEY (`Benutzername`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `benutzerkonto`
--
ALTER TABLE `benutzerkonto`
  MODIFY `BenutzerkontoID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `rezepte`
--
ALTER TABLE `rezepte`
  MODIFY `rezepteid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `benutzerkonto`
--
ALTER TABLE `benutzerkonto`
  ADD CONSTRAINT `´fk_BenutzernameInZugangsberechtigung´` FOREIGN KEY (`Benutzername`) REFERENCES `zugangsberechtigung` (`Benutzername`) ON DELETE CASCADE,
  ADD CONSTRAINT `´fk_EmailInKontaktdaten´` FOREIGN KEY (`Email`) REFERENCES `kontaktdaten` (`Email`) ON DELETE CASCADE;

--
-- Constraints for table `rezepte`
--
ALTER TABLE `rezepte`
  ADD CONSTRAINT `´fk_BenutzerkontoIDInBenutzerkonto´` FOREIGN KEY (`BenutzerkontoID`) REFERENCES `benutzerkonto` (`BenutzerkontoID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
