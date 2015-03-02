-- phpMyAdmin SQL Dump
-- version 4.2.10
-- http://www.phpmyadmin.net
--
-- Host: localhost:8889
-- Generation Time: Mar 01, 2015 at 06:24 PM
-- Server version: 5.5.38
-- PHP Version: 5.6.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `Rapportbook`
--

-- --------------------------------------------------------

--
-- Table structure for table `Media`
--

CREATE TABLE `Media` (
  `ID` bigint(20) NOT NULL,
  `Member_ID` int(11) NOT NULL,
  `Post_ID` bigint(20) NOT NULL,
  `MediaName` varchar(255) NOT NULL,
  `MediaType` varchar(50) NOT NULL,
  `MediaDate` date NOT NULL,
  `IsDeleted` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Members`
--

CREATE TABLE `Members` (
`ID` bigint(20) NOT NULL,
  `FirstName` varchar(50) NOT NULL,
  `LastName` varchar(50) NOT NULL,
  `Email` varchar(50) NOT NULL,
  `Gender` smallint(6) NOT NULL,
  `BirthMonth` smallint(6) NOT NULL,
  `BirthDay` smallint(6) NOT NULL,
  `BirthYear` int(11) NOT NULL,
  `Username` varchar(255) NOT NULL,
  `Password` varchar(10) NOT NULL,
  `IsActive` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Posts`
--

CREATE TABLE `Posts` (
`ID` bigint(20) NOT NULL,
  `Member_ID` bigint(20) NOT NULL,
  `Post` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Members`
--
ALTER TABLE `Members`
 ADD PRIMARY KEY (`ID`), ADD UNIQUE KEY `ID` (`ID`);

--
-- Indexes for table `Posts`
--
ALTER TABLE `Posts`
 ADD PRIMARY KEY (`ID`), ADD UNIQUE KEY `ID` (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Members`
--
ALTER TABLE `Members`
MODIFY `ID` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `Posts`
--
ALTER TABLE `Posts`
MODIFY `ID` bigint(20) NOT NULL AUTO_INCREMENT;