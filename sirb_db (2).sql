-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Apr 30, 2026 at 03:34 PM
-- Server version: 5.7.24
-- PHP Version: 8.3.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sirb_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `email`, `password`) VALUES
(1, 'admin@ksu.edu.sa', '123456');

-- --------------------------------------------------------

--
-- Table structure for table `pastproject`
--

CREATE TABLE `pastproject` (
  `project_ID` int(11) NOT NULL,
  `URL` varchar(2048) NOT NULL,
  `courseName` varchar(100) DEFAULT NULL,
  `student_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `pastproject`
--

INSERT INTO `pastproject` (`project_ID`, `URL`, `courseName`, `student_ID`) VALUES
(1, 'https://drive.google.com/drive/folders/14xnSHZjsSsNpYvtObZFGCMZ205ytDiGB?usp=drive_link', 'swe', 2228),
(2, 'https://drive.google.com/drive/folders/14xnSHZjsSsNpYvtObZFGCMZ205ytDiGB?usp=drive_link', 'Software', 2229),
(5, 'https://drive.google.com/drive/folders/14xnSHZjsSsNpYvtObZFGCMZ205ytDiGB?usp=drive_link', 'Software', 2231),
(11, 'https://drive.google.com/drive/folders/14xnSHZjsSsNpYvtObZFGCMZ205ytDiGB?usp=drive_link', 'Mushroom Clasification', 2232),
(12, 'https://drive.google.com/drive/folders/14xnSHZjsSsNpYvtObZFGCMZ205ytDiGB?usp=drive_link', NULL, 2232),
(13, 'https://drive.google.com/drive/folders/14xnSHZjsSsNpYvtObZFGCMZ205ytDiGB?usp=drive_link', 'Network', 2227),
(14, 'https://drive.google.com/drive/folders/14xnSHZjsSsNpYvtObZFGCMZ205ytDiGB?usp=drive_link', 'Data Structures', 2227),
(15, 'https://drive.google.com/drive/folders/14xnSHZjsSsNpYvtObZFGCMZ205ytDiGB?usp=drive_link', 'Web 2', 2227),
(16, 'https://drive.google.com/drive/folders/14xnSHZjsSsNpYvtObZFGCMZ205ytDiGB?usp=drive_link', 'SWE', 2226),
(17, 'https://drive.google.com/drive/folders/14xnSHZjsSsNpYvtObZFGCMZ205ytDiGB?usp=drive_link', 'Java 1', 2226),
(18, 'https://drive.google.com/drive/folders/14xnSHZjsSsNpYvtObZFGCMZ205ytDiGB?usp=drive_link', 'Java 2', 2226),
(19, 'https://drive.google.com/drive/folders/14xnSHZjsSsNpYvtObZFGCMZ205ytDiGB?usp=drive_link', 'OS', 2226),
(20, 'https://drive.google.com/drive/folders/14xnSHZjsSsNpYvtObZFGCMZ205ytDiGB?usp=drive_link', 'Practical Software Engineering', 2233),
(21, 'https://drive.google.com/drive/folders/14xnSHZjsSsNpYvtObZFGCMZ205ytDiGB?usp=drive_link', 'App Security', 2234),
(22, 'https://drive.google.com/drive/folders/14xnSHZjsSsNpYvtObZFGCMZ205ytDiGB?usp=drive_link', 'SWE', 2235),
(23, 'https://drive.google.com/drive/folders/14xnSHZjsSsNpYvtObZFGCMZ205ytDiGB?usp=drive_link', 'UX_Qurrba', 2236),
(24, 'https://drive.google.com/drive/folders/14xnSHZjsSsNpYvtObZFGCMZ205ytDiGB?usp=drive_link', 'Web_Riwaq', 2236),
(25, 'https://drive.google.com/drive/folders/14xnSHZjsSsNpYvtObZFGCMZ205ytDiGB?usp=drive_link', 'UX_Qurrba', 2237);

-- --------------------------------------------------------

--
-- Table structure for table `peerrate`
--

CREATE TABLE `peerrate` (
  `rate_ID` int(11) NOT NULL,
  `starRate` int(11) DEFAULT NULL,
  `Rated_student_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `peerrate`
--

INSERT INTO `peerrate` (`rate_ID`, `starRate`, `Rated_student_ID`) VALUES
(22, 5, 2229),
(23, 5, 2231),
(24, 5, 2228),
(26, 5, 2229),
(27, 3, 2226),
(28, 5, 2226),
(29, 1, 2226),
(30, 4, 2226),
(31, 5, 2227),
(32, 4, 2227),
(33, 4, 2227),
(34, 5, 2232),
(35, 5, 2229),
(36, 4, 2231),
(37, 5, 2228),
(38, 3, 2224),
(39, 2, 2224),
(40, 5, 2224),
(41, 5, 2233),
(42, 4, 2233),
(43, 5, 2233),
(44, 5, 2234),
(45, 5, 2234),
(46, 5, 2234),
(47, 4, 2234),
(48, 4, 2224),
(49, 5, 2232),
(50, 5, 2232),
(51, 5, 2235),
(52, 4, 2235),
(53, 5, 2235),
(54, 4, 2235),
(55, 5, 2236),
(56, 5, 2236),
(57, 5, 2236),
(58, 5, 2236),
(59, 5, 2237),
(60, 5, 2237),
(61, 5, 2237);

-- --------------------------------------------------------

--
-- Table structure for table `peerratetag`
--

CREATE TABLE `peerratetag` (
  `rate_ID` int(11) NOT NULL,
  `tag_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `peerratetag`
--

INSERT INTO `peerratetag` (`rate_ID`, `tag_ID`) VALUES
(22, 1),
(23, 1),
(26, 1),
(29, 1),
(31, 1),
(32, 1),
(34, 1),
(35, 1),
(44, 1),
(45, 1),
(49, 1),
(55, 1),
(56, 1),
(57, 1),
(27, 2),
(29, 2),
(30, 2),
(51, 2),
(59, 2),
(61, 2),
(22, 3),
(23, 3),
(24, 3),
(26, 3),
(31, 3),
(36, 3),
(38, 3),
(40, 3),
(41, 3),
(42, 3),
(51, 3),
(55, 3),
(59, 3),
(24, 4),
(26, 4),
(28, 4),
(33, 4),
(35, 4),
(37, 4),
(41, 4),
(46, 4),
(49, 4),
(50, 4),
(53, 4),
(59, 4),
(60, 4),
(22, 5),
(23, 5),
(24, 5),
(26, 5),
(27, 5),
(33, 5),
(34, 5),
(37, 5),
(45, 5),
(47, 5),
(56, 5),
(58, 5),
(60, 5),
(22, 6),
(23, 6),
(24, 6),
(26, 6),
(34, 6),
(35, 6),
(40, 6),
(43, 6),
(48, 6),
(60, 6);

-- --------------------------------------------------------

--
-- Table structure for table `ratetag`
--

CREATE TABLE `ratetag` (
  `tag_ID` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ratetag`
--

INSERT INTO `ratetag` (`tag_ID`, `name`) VALUES
(1, 'Leadership'),
(2, 'Research'),
(3, 'Coding'),
(4, 'UI/UX'),
(5, 'Teamwork'),
(6, 'Communication');

-- --------------------------------------------------------

--
-- Table structure for table `skill`
--

CREATE TABLE `skill` (
  `skill_ID` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `skill`
--

INSERT INTO `skill` (`skill_ID`, `name`) VALUES
(1, 'Leadership'),
(2, 'Research'),
(3, 'Innovation'),
(4, 'Communication'),
(5, 'Problem Solving'),
(6, 'Programming'),
(7, 'Teamwork'),
(8, 'Data Analysis'),
(9, 'Coding'),
(10, 'UI/UX');

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `student_ID` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `major` varchar(100) NOT NULL,
  `registrationDate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`student_ID`, `email`, `password`, `name`, `major`, `registrationDate`) VALUES
(2224, 'nora@ksu.edu.sa', '$2y$10$t5iY3XQCkWtyIzuPr88wnewd5ziO4MZuD3E6QxF8QySuEESGxWlyO', 'Nora Ali', 'IT', '2017-05-10'),
(2226, 'sara@ksu.edu.sa', '$2y$10$t5iY3XQCkWtyIzuPr88wnewd5ziO4MZuD3E6QxF8QySuEESGxWlyO', 'Sara Khalid', 'CS', '2018-02-15'),
(2227, 'lama@ksu.edu.sa', '$2y$10$t5iY3XQCkWtyIzuPr88wnewd5ziO4MZuD3E6QxF8QySuEESGxWlyO', 'Lama Ahmed', 'IS', '2016-11-20'),
(2228, 'danaff@gmail.com', '$2y$10$ooLsRAKwlRgCIUGdHCtLMOt12f89fYAgKssOXEpGUNcN0WIcXoW2y', 'Dana Fahad', 'IT', '2026-04-29'),
(2229, 'Aroub@gmail.com', '$2y$10$t5iY3XQCkWtyIzuPr88wnewd5ziO4MZuD3E6QxF8QySuEESGxWlyO', 'Aroub Alswayyed', 'IT', '2026-04-29'),
(2231, 'dalia@gmail.com', '$2y$10$uLiA3l..D9EhwV2p8xj8JeC63jmKV80FNq17PC1VS/XoLZ4O7.PQS', 'Dalia Alotaibi', 'IT', '2026-04-29'),
(2232, 'aliyahoww@gmail.com', '$2y$10$0tQLCHxYImNYUGjNihNaeOYr3ucAJ1CMtdRLaIRuYlM80BF3ql3n.', 'Aliyah Alharbi', 'IT', '2026-04-29'),
(2233, 'joudfaris@gmail.com', '$2y$10$d8yZufGa27XEmZGCjPLUoeFPLOTeZrK4Oy5QnPj1RcM.oS1ZCaYGO', 'Joud BinFaris', 'IT', '2026-04-30'),
(2234, 'Bodur@gmail.com', '$2y$10$j.BXkBbUY/q0SgchE6fJJuvSDMctfbUdLFT27zVxXNVWRal26VKSq', 'Bodur Albarqi', 'IT', '2026-04-30'),
(2235, 'joudmutrid@gmail.com', '$2y$10$ij7sVt6819OXaBbsY9JdqulCaHE7up6fAvrxhBSchAsQxIIse21c6', 'Joud Mutrid', 'IT', '2026-04-30'),
(2236, 'hawraalhammad@gmail.com', '$2y$10$0OI3dCDVUncrRf5cVd0cA.rq8FGcE/.IT32tsk.8uw6w4a7HjNV0e', 'Hawra Alhammad', 'IT', '2026-04-30'),
(2237, 'fatima@gmail.com', '$2y$10$OlM.tchpr6XTB9.R073pGOh8oHcKExpwWw8c90JnypnXrj3MEIACW', 'Fatimah Alhabshi', 'IT', '2026-04-30');

-- --------------------------------------------------------

--
-- Table structure for table `studentskill`
--

CREATE TABLE `studentskill` (
  `student_ID` int(11) NOT NULL,
  `skill_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `studentskill`
--

INSERT INTO `studentskill` (`student_ID`, `skill_ID`) VALUES
(2224, 1),
(2226, 1),
(2227, 1),
(2228, 1),
(2231, 1),
(2234, 1),
(2228, 2),
(2232, 2),
(2235, 2),
(2229, 3),
(2231, 3),
(2233, 3),
(2229, 4),
(2231, 4),
(2232, 4),
(2226, 5),
(2229, 5),
(2232, 5),
(2237, 5),
(2233, 6),
(2236, 6),
(2237, 6),
(2228, 7),
(2234, 7),
(2236, 7),
(2237, 7),
(2224, 8),
(2226, 8),
(2235, 8),
(2227, 9);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `pastproject`
--
ALTER TABLE `pastproject`
  ADD PRIMARY KEY (`project_ID`),
  ADD KEY `student_ID` (`student_ID`);

--
-- Indexes for table `peerrate`
--
ALTER TABLE `peerrate`
  ADD PRIMARY KEY (`rate_ID`),
  ADD KEY `fk_rated_student` (`Rated_student_ID`);

--
-- Indexes for table `peerratetag`
--
ALTER TABLE `peerratetag`
  ADD PRIMARY KEY (`rate_ID`,`tag_ID`),
  ADD KEY `tag_ID` (`tag_ID`);

--
-- Indexes for table `ratetag`
--
ALTER TABLE `ratetag`
  ADD PRIMARY KEY (`tag_ID`);

--
-- Indexes for table `skill`
--
ALTER TABLE `skill`
  ADD PRIMARY KEY (`skill_ID`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`student_ID`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `studentskill`
--
ALTER TABLE `studentskill`
  ADD PRIMARY KEY (`student_ID`,`skill_ID`),
  ADD KEY `skill_ID` (`skill_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `pastproject`
--
ALTER TABLE `pastproject`
  MODIFY `project_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `peerrate`
--
ALTER TABLE `peerrate`
  MODIFY `rate_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `ratetag`
--
ALTER TABLE `ratetag`
  MODIFY `tag_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `skill`
--
ALTER TABLE `skill`
  MODIFY `skill_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `student`
--
ALTER TABLE `student`
  MODIFY `student_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2238;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `pastproject`
--
ALTER TABLE `pastproject`
  ADD CONSTRAINT `pastproject_ibfk_1` FOREIGN KEY (`student_ID`) REFERENCES `student` (`student_ID`) ON DELETE CASCADE;

--
-- Constraints for table `peerrate`
--
ALTER TABLE `peerrate`
  ADD CONSTRAINT `fk_rated_student` FOREIGN KEY (`Rated_student_ID`) REFERENCES `student` (`student_ID`);

--
-- Constraints for table `peerratetag`
--
ALTER TABLE `peerratetag`
  ADD CONSTRAINT `peerratetag_ibfk_1` FOREIGN KEY (`rate_ID`) REFERENCES `peerrate` (`rate_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `peerratetag_ibfk_2` FOREIGN KEY (`tag_ID`) REFERENCES `ratetag` (`tag_ID`) ON DELETE CASCADE;

--
-- Constraints for table `studentskill`
--
ALTER TABLE `studentskill`
  ADD CONSTRAINT `studentskill_ibfk_1` FOREIGN KEY (`student_ID`) REFERENCES `student` (`student_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `studentskill_ibfk_2` FOREIGN KEY (`skill_ID`) REFERENCES `skill` (`skill_ID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
