-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 14, 2025 at 07:00 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lms`
--

-- --------------------------------------------------------

--
-- Table structure for table `badges`
--

CREATE TABLE `badges` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `icon_path` varchar(255) DEFAULT NULL,
  `points_required` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `challenges`
--

CREATE TABLE `challenges` (
  `id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `challenges`
--

INSERT INTO `challenges` (`id`, `teacher_id`, `title`, `description`, `created_at`) VALUES
(1, 2, 'Science', 'Complete the diagram.', '2025-09-14 10:22:48'),
(2, 2, 'Maths', 'Solve the problems from chapter 1 by today.', '2025-09-14 12:36:59');

-- --------------------------------------------------------

--
-- Table structure for table `challenge_submissions`
--

CREATE TABLE `challenge_submissions` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `challenge_id` int(11) NOT NULL,
  `text_submission` text DEFAULT NULL,
  `file_submission` varchar(255) DEFAULT NULL,
  `points_awarded` int(11) DEFAULT 0,
  `status` enum('pending','completed','approved','rejected') DEFAULT 'pending',
  `teacher_remark` text DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `challenge_submissions`
--

INSERT INTO `challenge_submissions` (`id`, `student_id`, `challenge_id`, `text_submission`, `file_submission`, `points_awarded`, `status`, `teacher_remark`, `reviewed_at`, `submitted_at`) VALUES
(1, 1, 1, 'hiii', NULL, 10, 'approved', 'Good.', '2025-09-14 10:28:32', '2025-09-14 10:23:27'),
(2, 1, 2, 'here are my answers.', 'uploads/challenges/challenge_68c6b77c97ebc.png', 10, 'approved', 'well done', '2025-09-14 13:11:01', '2025-09-14 12:39:24');

-- --------------------------------------------------------

--
-- Table structure for table `leaderboard`
--

CREATE TABLE `leaderboard` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `points` int(11) DEFAULT 0,
  `rank` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `modules`
--

CREATE TABLE `modules` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `sequence` int(11) NOT NULL,
  `status` enum('locked','unlocked','completed') DEFAULT 'locked',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `teacher_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `modules`
--

INSERT INTO `modules` (`id`, `title`, `description`, `sequence`, `status`, `created_at`, `teacher_id`) VALUES
(1, 'Introduction to Disaster Management', 'Basics of disasters, preparedness strategies, and why awareness matters.', 1, 'locked', '2025-09-14 10:38:11', NULL),
(2, 'Flood Safety Guide', 'Understanding flood risks, safety protocols, and emergency response.', 2, 'locked', '2025-09-14 10:38:11', NULL),
(3, 'Earthquake Preparedness', 'How to stay safe during earthquakes, emergency planning and drills.', 3, 'locked', '2025-09-14 10:38:11', NULL),
(4, 'Fire Safety Essentials', 'Fire prevention, evacuation plans, and first aid basics.', 4, 'locked', '2025-09-14 10:38:11', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `module_contents`
--

CREATE TABLE `module_contents` (
  `id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text DEFAULT NULL,
  `type` enum('text','video','file','quiz') NOT NULL DEFAULT 'text',
  `file_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `sequence` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `module_contents`
--

INSERT INTO `module_contents` (`id`, `module_id`, `title`, `content`, `type`, `file_path`, `created_at`, `sequence`) VALUES
(29, 1, 'Introductory Video', NULL, 'video', 'uploads/disaster_intro.mp4', '2025-09-14 11:30:05', 1),
(30, 1, 'Disaster Management Overview', 'Disasters are unpredictable events that can cause damage...', 'text', NULL, '2025-09-14 11:30:05', 2),
(31, 1, 'Disaster Management Quiz', 'Test your knowledge on Disaster Management', 'quiz', NULL, '2025-09-14 11:30:05', 3),
(32, 1, 'Module 1 Result', 'You have completed Module 1', 'text', NULL, '2025-09-14 11:30:05', 4),
(33, 2, 'Flood Safety Video', NULL, 'video', 'uploads/flood_safety.mp4', '2025-09-14 11:30:05', 1),
(34, 2, 'Flood Basics', 'Floods occur due to excessive rain or river overflow...', 'text', NULL, '2025-09-14 11:30:05', 2),
(35, 2, 'Flood Safety Quiz', 'Test your knowledge on Flood Safety', 'quiz', NULL, '2025-09-14 11:30:05', 3),
(36, 2, 'Module 2 Result', 'You have completed Module 2', 'text', NULL, '2025-09-14 11:30:05', 4),
(37, 3, 'Earthquake Drill Video', NULL, 'video', 'uploads/earthquake_drill.mp4', '2025-09-14 11:30:05', 1),
(38, 3, 'Earthquake Safety Tips', 'During earthquakes, stay calm, avoid elevators...', 'text', NULL, '2025-09-14 11:30:05', 2),
(39, 3, 'Earthquake Quiz', 'Test your knowledge on Earthquake Preparedness', 'quiz', NULL, '2025-09-14 11:30:05', 3),
(40, 3, 'Module 3 Result', 'You have completed Module 3', 'text', NULL, '2025-09-14 11:30:05', 4),
(41, 4, 'Fire Safety Demo', NULL, 'video', 'uploads/fire_safety.mp4', '2025-09-14 11:30:05', 1),
(42, 4, 'Fire Safety Guidelines', 'Fire can spread rapidly; always have an exit plan...', 'text', NULL, '2025-09-14 11:30:05', 2),
(43, 4, 'Fire Quiz', 'Test your knowledge on Fire Safety', 'quiz', NULL, '2025-09-14 11:30:05', 3),
(44, 4, 'Module 4 Result', 'You have completed Module 4', 'text', NULL, '2025-09-14 11:30:05', 4);

-- --------------------------------------------------------

--
-- Table structure for table `module_progress`
--

CREATE TABLE `module_progress` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `status` enum('locked','unlocked','completed') DEFAULT 'locked',
  `score` int(11) DEFAULT 0,
  `best_score` int(11) DEFAULT 0,
  `quiz_passed` tinyint(1) DEFAULT 0,
  `points_awarded` int(11) DEFAULT 0,
  `completed_at` timestamp NULL DEFAULT NULL,
  `current_step` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `module_progress`
--

INSERT INTO `module_progress` (`id`, `student_id`, `module_id`, `status`, `score`, `best_score`, `quiz_passed`, `points_awarded`, `completed_at`, `current_step`) VALUES
(1, 1, 1, 'completed', 100, 100, 1, 4, '2025-09-14 11:48:56', 4),
(2, 1, 2, 'completed', 100, 100, 1, 4, '2025-09-14 11:44:52', 3),
(3, 1, 3, 'completed', 100, 100, 1, 4, '2025-09-14 11:32:31', 3),
(4, 1, 4, 'completed', 100, 100, 1, 4, '2025-09-14 11:32:55', 3);

-- --------------------------------------------------------

--
-- Table structure for table `module_videos`
--

CREATE TABLE `module_videos` (
  `id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `video_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `title`, `message`, `created_at`) VALUES
(1, NULL, 'Science', 'Complete the diagram.', '2025-09-14 10:22:48'),
(2, NULL, 'Maths', 'Solve the problems from chapter 1 by today.', '2025-09-14 12:36:59');

-- --------------------------------------------------------

--
-- Table structure for table `quizzes`
--

CREATE TABLE `quizzes` (
  `id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `passing_score` int(11) DEFAULT 50,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quizzes`
--

INSERT INTO `quizzes` (`id`, `module_id`, `title`, `passing_score`, `created_at`) VALUES
(1, 1, 'Disaster Management Quiz', 50, '2025-09-14 10:42:21'),
(2, 2, 'Flood Safety Quiz', 50, '2025-09-14 10:42:21'),
(3, 3, 'Earthquake Preparedness Quiz', 50, '2025-09-14 10:42:21'),
(4, 4, 'Fire Safety Essentials Quiz', 50, '2025-09-14 10:42:21');

-- --------------------------------------------------------

--
-- Table structure for table `quiz_attempts`
--

CREATE TABLE `quiz_attempts` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `score` int(11) DEFAULT 0,
  `passed` tinyint(1) DEFAULT 0,
  `attempted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quiz_questions`
--

CREATE TABLE `quiz_questions` (
  `id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `question` text NOT NULL,
  `option_a` varchar(255) NOT NULL,
  `option_b` varchar(255) NOT NULL,
  `option_c` varchar(255) NOT NULL,
  `option_d` varchar(255) NOT NULL,
  `answer` enum('a','b','c','d') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quiz_questions`
--

INSERT INTO `quiz_questions` (`id`, `quiz_id`, `question`, `option_a`, `option_b`, `option_c`, `option_d`, `answer`, `created_at`) VALUES
(1, 1, 'What is a disaster?', 'Predictable event', 'Unpredictable event', 'Minor event', 'Optional event', 'b', '2025-09-14 10:42:21'),
(2, 1, 'Disaster preparedness helps to?', 'Increase risk', 'Reduce damage', 'Ignore hazards', 'Panic', 'b', '2025-09-14 10:42:21'),
(3, 2, 'Floods are caused by?', 'Rain', 'River overflow', 'Both', 'None', 'c', '2025-09-14 10:42:21'),
(4, 2, 'During a flood, one should?', 'Stay indoors', 'Move to higher ground', 'Swim through water', 'Ignore warnings', 'b', '2025-09-14 10:42:21'),
(5, 3, 'During an earthquake, you should?', 'Stand under doorway', 'Use elevators', 'Stay calm and find safe spot', 'Run outside blindly', 'c', '2025-09-14 10:42:21'),
(6, 3, 'Emergency drills are important because?', 'They are fun', 'They prepare for real situations', 'They waste time', 'They confuse students', 'b', '2025-09-14 10:42:21'),
(7, 4, 'Fire can spread quickly, you should?', 'Panic', 'Know exit routes', 'Hide under furniture', 'Ignore alarms', 'b', '2025-09-14 10:42:21'),
(8, 4, 'First aid during fire includes?', 'Stop, drop, and roll', 'Run blindly', 'Throw water on electrical fire', 'Wait for instructions', 'a', '2025-09-14 10:42:21');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('student','teacher','admin') DEFAULT 'student',
  `points` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `role`, `points`, `created_at`) VALUES
(1, 'Kay', 'kk@gmail.com', '$2y$10$GQQ3bSI6rnt0GkmtARe9.uMxhYZDLHqOVnQCg2Pf6eXMe5PF2v9EO', 'student', 36, '2025-09-14 10:16:40'),
(2, 'John', 'j@gmail.com', '$2y$10$1LgYIsJFo2wJZQwbr2CvUu1.ylF7pkUrCAIuuYoSsJc.yVbbaRxvO', 'teacher', 0, '2025-09-14 10:17:43');

-- --------------------------------------------------------

--
-- Table structure for table `user_badges`
--

CREATE TABLE `user_badges` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `badge_id` int(11) NOT NULL,
  `awarded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `badges`
--
ALTER TABLE `badges`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `challenges`
--
ALTER TABLE `challenges`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_challenges_teacher` (`teacher_id`);

--
-- Indexes for table `challenge_submissions`
--
ALTER TABLE `challenge_submissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `challenge_id` (`challenge_id`);

--
-- Indexes for table `leaderboard`
--
ALTER TABLE `leaderboard`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `modules`
--
ALTER TABLE `modules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_teacher` (`teacher_id`);

--
-- Indexes for table `module_contents`
--
ALTER TABLE `module_contents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `module_id` (`module_id`);

--
-- Indexes for table `module_progress`
--
ALTER TABLE `module_progress`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_student_module` (`student_id`,`module_id`),
  ADD KEY `module_id` (`module_id`);

--
-- Indexes for table `module_videos`
--
ALTER TABLE `module_videos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `module_id` (`module_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `module_id` (`module_id`);

--
-- Indexes for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `quiz_id` (`quiz_id`),
  ADD KEY `module_id` (`module_id`);

--
-- Indexes for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quiz_id` (`quiz_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_badges`
--
ALTER TABLE `user_badges`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_user_badge` (`user_id`,`badge_id`),
  ADD KEY `badge_id` (`badge_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `badges`
--
ALTER TABLE `badges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `challenges`
--
ALTER TABLE `challenges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `challenge_submissions`
--
ALTER TABLE `challenge_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `leaderboard`
--
ALTER TABLE `leaderboard`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `modules`
--
ALTER TABLE `modules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `module_contents`
--
ALTER TABLE `module_contents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `module_progress`
--
ALTER TABLE `module_progress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `module_videos`
--
ALTER TABLE `module_videos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user_badges`
--
ALTER TABLE `user_badges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `challenges`
--
ALTER TABLE `challenges`
  ADD CONSTRAINT `fk_challenges_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `challenge_submissions`
--
ALTER TABLE `challenge_submissions`
  ADD CONSTRAINT `challenge_submissions_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `challenge_submissions_ibfk_2` FOREIGN KEY (`challenge_id`) REFERENCES `challenges` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `leaderboard`
--
ALTER TABLE `leaderboard`
  ADD CONSTRAINT `leaderboard_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `modules`
--
ALTER TABLE `modules`
  ADD CONSTRAINT `fk_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `module_contents`
--
ALTER TABLE `module_contents`
  ADD CONSTRAINT `module_contents_ibfk_1` FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `module_progress`
--
ALTER TABLE `module_progress`
  ADD CONSTRAINT `module_progress_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `module_progress_ibfk_2` FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `module_videos`
--
ALTER TABLE `module_videos`
  ADD CONSTRAINT `module_videos_ibfk_1` FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD CONSTRAINT `quizzes_ibfk_1` FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  ADD CONSTRAINT `quiz_attempts_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quiz_attempts_ibfk_2` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quiz_attempts_ibfk_3` FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  ADD CONSTRAINT `quiz_questions_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_badges`
--
ALTER TABLE `user_badges`
  ADD CONSTRAINT `user_badges_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_badges_ibfk_2` FOREIGN KEY (`badge_id`) REFERENCES `badges` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
