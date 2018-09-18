-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 18, 2018 at 04:16 PM
-- Server version: 5.6.39-83.1
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
-- Database: `messagin_database`
--

-- --------------------------------------------------------

--
-- Table structure for table `carriers`
--

CREATE TABLE `carriers` (
  `carrier` varchar(50) NOT NULL,
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
  `instructorid` int(11) NOT NULL,
  `verified` varchar(50) DEFAULT NULL,
  `fname` varchar(50) NOT NULL,
  `lname` varchar(50) NOT NULL,
  `password` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `verifiedTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `messageid` int(11) NOT NULL,
  `instructorEmail` varchar(50) DEFAULT NULL,
  `content` varchar(250) DEFAULT NULL,
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `recipient` varchar(140) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `programs`
--

CREATE TABLE `programs` (
  `programid` int(11) NOT NULL,
  `program` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `programs`
--

INSERT INTO `programs` (`programid`, `program`) VALUES
(1, 'Bachelors - Software Development'),
(2, 'Associates - Software Development'),
(3, 'Bachelors - Networking'),
(4, 'Associates - Networking'),
(5, 'Bachelors - Aeronautical Science'),
(6, 'Associates - Aviation');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `studentid` int(11) NOT NULL,
  `studentEmail` varchar(100) NOT NULL,
  `optOutUrl` varchar(100) NOT NULL,
  `password` varchar(80) NOT NULL,
  `personalEmail` varchar(100) DEFAULT NULL,
  `phone` varchar(10) DEFAULT NULL,
  `fname` varchar(50) DEFAULT NULL,
  `lname` varchar(50) DEFAULT NULL,
  `carrier` varchar(50) NOT NULL,
  `program` varchar(50) NOT NULL,
  `getTexts` char(1) DEFAULT 'n',
  `getStudentEmails` char(1) DEFAULT 'y',
  `getPersonalEmails` char(1) DEFAULT 'n',
  `verifiedStudent` varchar(50) DEFAULT NULL,
  `verifiedPersonal` varchar(50) DEFAULT NULL,
  `verifiedPhone` varchar(50) DEFAULT NULL,
  `verifiedStudentTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `verifiedPersonalTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `verifiedPhoneTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`studentid`, `studentEmail`, `optOutUrl`, `password`, `personalEmail`, `phone`, `fname`, `lname`, `carrier`, `program`, `getTexts`, `getStudentEmails`, `getPersonalEmails`, `verifiedStudent`, `verifiedPersonal`, `verifiedPhone`, `verifiedStudentTime`, `verifiedPersonalTime`, `verifiedPhoneTime`) VALUES
(29, 'asuarez2@mail.greenriver.edu', '9ced43ccc33ced5d7c2241743f77359eede3a6b6', 'd438d79897f5895ad4b5a29881dc42779d08025c', 'sykog@yahoo.com', '2536531125', 'Antonio', 'Suarez', 'Verizon', 'Bachelors - Software Development', 'y', 'y', 'y', 'y', 'y', 'y', '2018-08-15 23:10:47', '2018-08-15 23:20:16', '2018-08-15 23:19:58'),
(30, 'amelhaff2@mail.greenriver.edu', '', '1e7dc56a71c332358f3797e302b8fb0c5be59387', NULL, '2535451740', 'Aaron', 'Melhaff', 'Verizon', 'Bachelors - Software Development', 'n', 'y', 'n', 'y', NULL, 'y', '2018-08-20 19:10:31', '2018-08-20 19:09:35', '2018-08-20 19:14:47');

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
  ADD PRIMARY KEY (`messageid`),
  ADD KEY `instructorEmail` (`instructorEmail`);

--
-- Indexes for table `programs`
--
ALTER TABLE `programs`
  ADD PRIMARY KEY (`programid`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`studentid`),
  ADD KEY `carrier` (`carrier`),
  ADD KEY `program` (`program`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `instructors`
--
ALTER TABLE `instructors`
  MODIFY `instructorid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `messageid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `programs`
--
ALTER TABLE `programs`
  MODIFY `programid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `studentid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
