-- phpMyAdmin SQL Dump
-- version 4.0.10.7
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 02, 2015 at 03:18 PM
-- Server version: 5.5.42-cll
-- PHP Version: 5.4.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `rapportb_rapportbook`
--

-- --------------------------------------------------------

--
-- Table structure for table `Media`
--

CREATE TABLE IF NOT EXISTS `Media` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `Member_ID` int(11) NOT NULL,
  `Post_ID` bigint(20) DEFAULT NULL,
  `MediaName` varchar(255) NOT NULL,
  `MediaType` varchar(50) NOT NULL,
  `MediaDate` date NOT NULL,
  `Private` tinyint(1) NOT NULL,
  `WasProfilePhoto` tinyint(1) NOT NULL,
  `IsDeleted` tinyint(1) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID` (`ID`),
  UNIQUE KEY `Post_ID` (`Post_ID`),
  UNIQUE KEY `Post_ID_2` (`Post_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=113 ;

-- --------------------------------------------------------

--
-- Table structure for table `Members`
--

CREATE TABLE IF NOT EXISTS `Members` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `FirstName` varchar(50) NOT NULL,
  `LastName` varchar(50) NOT NULL,
  `Email` varchar(50) NOT NULL,
  `Gender` smallint(6) NOT NULL,
  `DOB` date NOT NULL,
  `Username` varchar(255) NOT NULL,
  `Password` varchar(32) NOT NULL,
  `SignupDate` date NOT NULL,
  `IsActive` tinyint(1) NOT NULL DEFAULT '1',
  `EmailActive` tinyint(1) NOT NULL,
  `IsSuspended` tinyint(1) NOT NULL,
  `LastLogin` date NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID` (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Table structure for table `Messages`
--

CREATE TABLE IF NOT EXISTS `Messages` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `ThreadOwner_ID` bigint(20) NOT NULL,
  `Sender_ID` bigint(20) NOT NULL,
  `Receiver_ID` bigint(20) NOT NULL,
  `Subject` varchar(255) NOT NULL,
  `Message` longtext NOT NULL,
  `InitialMessage` tinyint(1) NOT NULL,
  `New` tinyint(1) NOT NULL,
  `IsDeleted` tinyint(1) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=105 ;

-- --------------------------------------------------------

--
-- Table structure for table `PostApprovals`
--

CREATE TABLE IF NOT EXISTS `PostApprovals` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `Member_ID` bigint(20) NOT NULL,
  `Post_ID` bigint(20) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=170 ;

-- --------------------------------------------------------

--
-- Table structure for table `PostComments`
--

CREATE TABLE IF NOT EXISTS `PostComments` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `Member_ID` bigint(20) NOT NULL,
  `Post_ID` bigint(20) NOT NULL,
  `Comment` longtext NOT NULL,
  `IsDeleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID` (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=60 ;

-- --------------------------------------------------------

--
-- Table structure for table `Posts`
--

CREATE TABLE IF NOT EXISTS `Posts` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `Member_ID` bigint(20) NOT NULL,
  `Post` longtext NOT NULL,
  `Category` varchar(255) NOT NULL,
  `PostDate` date NOT NULL,
  `IsDeleted` tinyint(1) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID` (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=127 ;

-- --------------------------------------------------------

--
-- Table structure for table `Profile`
--

CREATE TABLE IF NOT EXISTS `Profile` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `Member_ID` bigint(20) NOT NULL,
  `ProfilePhoto` varchar(255) NOT NULL DEFAULT 'default_photo.png',
  `ProfileVideo` varchar(255) NOT NULL DEFAULT 'default_video.png',
  `HomeCity` varchar(255) NOT NULL,
  `HomeState` varchar(2) NOT NULL,
  `CurrentCity` varchar(255) NOT NULL,
  `CurrentState` varchar(2) NOT NULL,
  `Interests` longtext NOT NULL,
  `Books` longtext NOT NULL,
  `Movies` longtext NOT NULL,
  `Food` longtext NOT NULL,
  `Dislikes` longtext NOT NULL,
  `Plan` longtext NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `Member_ID` (`Member_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Table structure for table `State`
--

CREATE TABLE IF NOT EXISTS `State` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `State` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=53 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
