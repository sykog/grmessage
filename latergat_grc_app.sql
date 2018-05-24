CREATE TABLE IF NOT EXISTS `programs` (
  `programid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `program` varchar(30) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `programs` (`programid`, `program`) VALUES
(1, 'Bachelors - Software Develpoment'),
(2, 'Associates - Software Develpoment'),
(3, 'Bahelors - Networking'),
(4, 'Associates - Networking');

ALTER TABLE `instructors`
  ADD COLUMN fname VARCHAR(50) AFTER instructorid,
  ADD COLUMN lname VARCHAR(50) AFTER instructorid,
  ADD COLUMN verified VARCHAR(50) AFTER instructorid;

ALTER TABLE `messages`
  ADD FOREIGN KEY (instructorid) REFERENCES instructors(instructorid);

ALTER TABLE `students`
  ADD COLUMN program VARCHAR(50) AFTER carrier,
  ADD COLUMN verifiedStudent VARCHAR(50),
  ADD COLUMN verifiedPersonal VARCHAR(50),
  ADD COLUMN verifiedPhone VARCHAR(50),
  ADD FOREIGN KEY (carrier) REFERENCES carriers(carrier),
  ADD FOREIGN KEY (program) REFERENCES programs(program);
