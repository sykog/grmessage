-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 24, 2018 at 02:53 PM
-- Server version: 10.1.32-MariaDB
-- PHP Version: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `latergat_grc_app`
--

-- --------------------------------------------------------

--
-- Table structure for table `carriers`
--

CREATE TABLE `carriers` (
  `carrier` varchar(50) NOT NULL PRIMARY KEY,
  `carrierEmail` varchar(50) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `carriers`
--

INSERT INTO `carriers` (`carrier`, `carrierEmail`) VALUES
('Verizon', 'vtext.com'),
('AT&T', 'txt.att.net'),
('Sprint', 'messaging.sprintpcs.com'),
('T-Mobile', 'tmomail.net'),
('Boost Mobile', 'myboostmobile.com'),
('Cricket Wireless', 'mms.cricketwireless.net'),
('Virgin Mobile', 'vmobl.com'),
('Republic Wireless', 'text.republicwireless.com'),
('U.S. Cellular', 'email.uscc.net'),
('Alltel', 'message.alltel.com');

-- --------------------------------------------------------

--
-- Table structure for table `instructors`
--

CREATE TABLE `instructors` (
  `instructorid` int(11) NOT NULL PRIMARY KEY,
  `password` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `messageid` int(11) NOT NULL PRIMARY KEY,
  `instructorid` int(11) DEFAULT NULL,
  `content` varchar(250) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `studentid` int(11) NOT NULL PRIMARY KEY,
  `studentEmail` varchar(100) NOT NULL,
  `password` varchar(80) NOT NULL,
  `personalEmail` varchar(100) DEFAULT NULL,
  `phone` varchar(10) DEFAULT NULL,
  `fname` varchar(50) DEFAULT NULL,
  `lname` varchar(50) DEFAULT NULL,
  `carrier` varchar(50) NOT NULL,
  `getTexts` char(1) DEFAULT 'n',
  `getStudentEmails` char(1) DEFAULT 'y',
  `getPersonalEmails` char(1) DEFAULT 'n'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`studentid`, `studentEmail`, `password`, `personalEmail`, `phone`, `fname`, `lname`, `carrier`, `getTexts`, `getStudentEmails`, `getPersonalEmails`) VALUES
(1, 'sourn@mail.greenriver.edu', '3b954a7dff64be46c80cf6986a9a9b', 'sam.ourn@yahoo.com', '2532235095', 'Samantha', 'Ourn', 'Verizon', 'n', 'y', 'n'),
(2, 'asuarez2@mail.greenriver.edu', 'daee98f5b9012d7453d06e941d6c6da161af26c8', 'sykog@yahoo.com', '2536531125', 'Antonio', 'Suarez', 'Verizon', 'n', 'y', 'n');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `carriers`
--
ALTER TABLE `carriers`
  ADD PRIMARY KEY (`carrier`);

--
-- Indexes for table `instructors`
--
ALTER TABLE `instructors`
  ADD PRIMARY KEY (`instructorid`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`messageid`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`studentid`),
  ADD KEY `carrier` (`carrier`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `instructors`
--
ALTER TABLE `instructors`
  MODIFY `instructorid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `messageid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `studentid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
