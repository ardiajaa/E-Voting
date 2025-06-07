-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 07, 2025 at 07:25 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `voting_osis`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `email`, `password`, `created_at`) VALUES
(1, 'admin@admin.com', '$2y$10$fqJmjXN0KmceFJphGDPcVe1gp8W.ELGQ7/g9a/ryd5iPLrIx4LeOO', '2025-06-06 08:59:23');

-- --------------------------------------------------------

--
-- Table structure for table `candidates`
--

CREATE TABLE `candidates` (
  `id` int NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `kelas` varchar(10) DEFAULT NULL,
  `absen` int DEFAULT NULL,
  `visi` text,
  `misi` text,
  `foto` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `candidates`
--

INSERT INTO `candidates` (`id`, `nama`, `kelas`, `absen`, `visi`, `misi`, `foto`, `created_at`) VALUES
(1, 'Achmad Rizky Putra Ardianto', 'XII TKJ 1', 1, 'Mewujudkan sekolah yang unggul dalam prestasi akademik dan non-akademik, berkarakter, dan berwawasan global', '1. Meningkatkan prestasi akademik melalui program bimbingan belajar dan kompetisi\r\n2. Mengembangkan bakat dan minat siswa melalui ekstrakurikuler\r\n3. Menjalin kerjasama dengan sekolah lain untuk pertukaran pelajar\r\n4. Meningkatkan fasilitas sekolah untuk mendukung kegiatan belajar mengajar', 'tkj.jpg', '2025-06-06 08:59:23'),
(2, 'Uzumaki Naruto', 'XII TKI 1', 1, 'Menciptakan lingkungan sekolah yang nyaman, aman, dan kondusif untuk belajar serta mengembangkan kreativitas siswa', '1. Memperbaiki dan merawat fasilitas sekolah\r\n2. Mengadakan program anti-bullying dan konseling\r\n3. Mengembangkan program seni dan budaya\r\n4. Meningkatkan partisipasi siswa dalam kegiatan sekolah', 'gunung-bromo.jpg', '2025-06-06 08:59:23'),
(3, 'Shikamaru Nara', 'XII TKJ 1', 2, 'Membangun sekolah yang berwawasan lingkungan dan peduli terhadap masyarakat', '1. Mengadakan program go green di sekolah\r\n2. Melakukan kegiatan sosial ke masyarakat sekitar\r\n3. Mengembangkan program kesehatan dan kebersihan\r\n4. Meningkatkan kesadaran siswa akan pentingnya menjaga lingkungan', 'ani.jpg', '2025-06-06 08:59:23');

-- --------------------------------------------------------

--
-- Table structure for table `login_history`
--

CREATE TABLE `login_history` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `user_type` enum('admin','user') NOT NULL,
  `login_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(45) NOT NULL,
  `location` varchar(255) NOT NULL,
  `device` varchar(50) NOT NULL,
  `user_agent` text NOT NULL,
  `attempted_email` varchar(255) NOT NULL,
  `status` enum('success','failed') NOT NULL,
  `reason` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `login_history`
--

INSERT INTO `login_history` (`id`, `user_id`, `user_type`, `login_time`, `ip_address`, `location`, `device`, `user_agent`, `attempted_email`, `status`, `reason`) VALUES
(1, 1, 'admin', '2025-06-07 12:34:45', '127.0.0.1', 'Localhost', 'Desktop', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'admin@admin.com', 'success', NULL),
(14, NULL, 'user', '2025-06-07 12:47:11', '10.20.30.1', 'Unknown Location', 'Mobile', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Mobile Safari/537.36', '1818', 'failed', 'NIS tidak ditemukan'),
(16, 4, 'user', '2025-06-07 13:22:00', '127.0.0.1', 'Localhost', 'Desktop', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '1', 'success', NULL),
(17, 1, 'admin', '2025-06-07 13:30:31', '127.0.0.1', 'Localhost', 'Mobile', 'Mozilla/5.0 (iPhone; CPU iPhone OS 15_0 like Mac OS X) AppleWebKit/603.1.30 (KHTML, like Gecko) Version/17.5 Mobile/15A5370a Safari/602.1', 'admin@admin.com', 'success', NULL),
(18, 1, 'admin', '2025-06-07 13:45:47', '127.0.0.1', 'Localhost', 'Desktop', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'admin@admin.com', 'success', NULL),
(19, 15, 'user', '2025-06-07 13:47:00', '127.0.0.1', 'Localhost', 'Desktop', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '11928/1170.066', 'success', NULL),
(20, 1, 'admin', '2025-06-07 13:47:25', '127.0.0.1', 'Localhost', 'Desktop', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'admin@admin.com', 'success', NULL),
(21, 8, 'user', '2025-06-07 13:48:41', '127.0.0.1', 'Localhost', 'Desktop', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '5', 'success', NULL),
(22, 6, 'user', '2025-06-07 13:48:58', '127.0.0.1', 'Localhost', 'Desktop', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '3', 'success', NULL),
(23, 1, 'admin', '2025-06-07 13:49:30', '127.0.0.1', 'Localhost', 'Desktop', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'admin@admin.com', 'success', NULL),
(24, 25, 'user', '2025-06-07 14:00:54', '127.0.0.1', 'Localhost', 'Desktop', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '20', 'success', NULL),
(25, 20, 'user', '2025-06-07 14:01:41', '127.0.0.1', 'Localhost', 'Desktop', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '15', 'success', NULL),
(26, NULL, 'admin', '2025-06-07 14:01:53', '127.0.0.1', 'Localhost', 'Desktop', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'admin@admin.com', 'failed', 'Password salah'),
(27, 1, 'admin', '2025-06-07 14:02:00', '127.0.0.1', 'Localhost', 'Desktop', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'admin@admin.com', 'success', NULL),
(28, 32, 'user', '2025-06-07 14:12:23', '127.0.0.1', 'Localhost', 'Desktop', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '11901/1101.005', 'success', NULL),
(29, 1, 'admin', '2025-06-07 14:13:42', '127.0.0.1', 'Localhost', 'Desktop', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'admin@admin.com', 'success', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int NOT NULL,
  `nama_sekolah` varchar(100) DEFAULT NULL,
  `tahun_ajaran` varchar(20) DEFAULT NULL,
  `visi` text,
  `misi` text,
  `logo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `background` varchar(255) DEFAULT NULL,
  `default_password` varchar(255) DEFAULT 'rahasia'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `nama_sekolah`, `tahun_ajaran`, `visi`, `misi`, `logo`, `created_at`, `background`, `default_password`) VALUES
(1, 'MTsN Gresik', '2019', NULL, NULL, NULL, '2025-06-06 09:09:00', NULL, 'rahasia'),
(2, 'MTsN Gresik', '2019', NULL, NULL, NULL, '2025-06-06 09:12:02', NULL, 'rahasia'),
(3, 'MTsN Gresik', '2019', NULL, NULL, 'logo_1749201578.png', '2025-06-06 09:19:38', NULL, 'rahasia'),
(4, 'MTsN Gresik', '2019', NULL, NULL, 'logo_1749203645.png', '2025-06-06 09:54:05', NULL, 'rahasia'),
(5, 'MTsN Gresik', '2025', NULL, NULL, 'logo_1749203645.png', '2025-06-06 11:16:19', NULL, 'rahasia'),
(6, 'MTsN Gresik', '2025', '\"Meningkatkan mutu MTsN Gresik dan membentuk karakter siswa-siswi agar menjadi SMART (Sigap, Musyawarah, Adil, Religius, Teliti).\"', '1. Meningkatkan mutu prestasi, baik akademik maupun non akademik, melalui wadah organisasi.\r\n2. Menjadikan OSIS sebagai tempat untuk menyuarakan aspirasi siswa.\r\n3. Memperbaiki kedisiplinan dengan patuh pada peraturan yang berlaku.', 'logo_1749203645.png', '2025-06-06 11:21:25', NULL, 'rahasia'),
(7, 'MTsN Gresik', '2025/2026', '\"Meningkatkan mutu MTsN Gresik dan membentuk karakter siswa-siswi agar menjadi SMART (Sigap, Musyawarah, Adil, Religius, Teliti).\"', '1. Meningkatkan mutu prestasi, baik akademik maupun non akademik, melalui wadah organisasi.\r\n2. Menjadikan OSIS sebagai tempat untuk menyuarakan aspirasi siswa.\r\n3. Memperbaiki kedisiplinan dengan patuh pada peraturan yang berlaku.', 'logo_1749203645.png', '2025-06-06 11:24:12', NULL, 'rahasia'),
(8, 'SMKN 1 CERME', '2025/2026', '\"Meningkatkan mutu MTsN Gresik dan membentuk karakter siswa-siswi agar menjadi SMART (Sigap, Musyawarah, Adil, Religius, Teliti).\"', '1. Meningkatkan mutu prestasi, baik akademik maupun non akademik, melalui wadah organisasi.\r\n2. Menjadikan OSIS sebagai tempat untuk menyuarakan aspirasi siswa.\r\n3. Memperbaiki kedisiplinan dengan patuh pada peraturan yang berlaku.', 'logo_1749203645.png', '2025-06-06 13:15:46', NULL, 'rahasia'),
(9, 'SMKN 1 CERME', '2025/2026', '\"Meningkatkan mutu MTsN Gresik dan membentuk karakter siswa-siswi agar menjadi SMART (Sigap, Musyawarah, Adil, Religius, Teliti).\"', '1. Meningkatkan mutu prestasi, baik akademik maupun non akademik, melalui wadah organisasi.\r\n2. Menjadikan OSIS sebagai tempat untuk menyuarakan aspirasi siswa.\r\n3. Memperbaiki kedisiplinan dengan patuh pada peraturan yang berlaku.', 'logo_1749215994.png', '2025-06-06 13:19:54', NULL, 'rahasia'),
(10, 'SMKN 1 CERME', '2025/2026', '\"Meningkatkan mutu MTsN Gresik dan membentuk karakter siswa-siswi agar menjadi SMART (Sigap, Musyawarah, Adil, Religius, Teliti).\"', '1. Meningkatkan mutu prestasi, baik akademik maupun non akademik, melalui wadah organisasi.\r\n2. Menjadikan OSIS sebagai tempat untuk menyuarakan aspirasi siswa.\r\n3. Memperbaiki kedisiplinan dengan patuh pada peraturan yang berlaku.', 'logo_1749216015.png', '2025-06-06 13:20:15', NULL, 'rahasia'),
(11, 'SMKN 1 CERME', '2025/2026', '\"Meningkatkan mutu MTsN Gresik dan membentuk karakter siswa-siswi agar menjadi SMART (Sigap, Musyawarah, Adil, Religius, Teliti).\"', '1. Meningkatkan mutu prestasi, baik akademik maupun non akademik, melalui wadah organisasi.\r\n2. Menjadikan OSIS sebagai tempat untuk menyuarakan aspirasi siswa.\r\n3. Memperbaiki kedisiplinan dengan patuh pada peraturan yang berlaku.', 'logo_1749216025.png', '2025-06-06 13:20:25', NULL, 'rahasia'),
(12, 'SMKN 1 CERME', '2025/2026', '\"Meningkatkan mutu MTsN Gresik dan membentuk karakter siswa-siswi agar menjadi SMART (Sigap, Musyawarah, Adil, Religius, Teliti).\"', '1. Meningkatkan mutu prestasi, baik akademik maupun non akademik, melalui wadah organisasi.\r\n2. Menjadikan OSIS sebagai tempat untuk menyuarakan aspirasi siswa.\r\n3. Memperbaiki kedisiplinan dengan patuh pada peraturan yang berlaku.', 'logo_1749216025.png', '2025-06-07 04:56:57', '', 'rahasia'),
(13, 'SMKN 1 CERME', '2025/2026', '\"Meningkatkan mutu MTsN Gresik dan membentuk karakter siswa-siswi agar menjadi SMART (Sigap, Musyawarah, Adil, Religius, Teliti).\"', '1. Meningkatkan mutu prestasi, baik akademik maupun non akademik, melalui wadah organisasi.\r\n2. Menjadikan OSIS sebagai tempat untuk menyuarakan aspirasi siswa.\r\n3. Memperbaiki kedisiplinan dengan patuh pada peraturan yang berlaku.', 'logo_1749216025.png', '2025-06-07 04:58:31', 'bg_1749272311.jpg', 'rahasia'),
(14, 'SMKN 1 CERME', '2025/2026', '\"Meningkatkan mutu MTsN Gresik dan membentuk karakter siswa-siswi agar menjadi SMART (Sigap, Musyawarah, Adil, Religius, Teliti).\"', '1. Meningkatkan mutu prestasi, baik akademik maupun non akademik, melalui wadah organisasi.\r\n2. Menjadikan OSIS sebagai tempat untuk menyuarakan aspirasi siswa.\r\n3. Memperbaiki kedisiplinan dengan patuh pada peraturan yang berlaku.', 'logo_1749216025.png', '2025-06-07 07:00:07', 'bg_1749272311.jpg', 'mahameru');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `nis` varchar(20) DEFAULT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `kelas` varchar(10) DEFAULT NULL,
  `absen` int DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `has_voted` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `role` enum('admin','user') DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nis`, `nama_lengkap`, `kelas`, `absen`, `password`, `has_voted`, `created_at`, `role`) VALUES
(4, '1', 'Ardi 1', 'X TKJ 1', 1, '$2y$10$AsQ.k.eoCrTsjXC4cZ73Me5mWtWP7ezz1x8FzettpLBFwYww79Ea.', 1, '2025-06-06 12:11:28', 'user'),
(5, '2', 'Ardi 2', 'X TKJ 1', 2, '$2y$10$xbwmjhMQGXdDosoam1VO7ukJzQymOx8poLsl.2DqfMawSY0Lge4FG', 1, '2025-06-06 12:11:28', 'user'),
(6, '3', 'Ardi 3', 'X TKJ 1', 3, '$2y$10$01HrmIzJjh6a7S4qlRDeFuCD1FKWMz0AstKumL.pVnYQxl62MuBMe', 1, '2025-06-06 12:11:28', 'user'),
(8, '5', 'Ardi 5', 'X TKJ 1', 5, '$2y$10$Dotth9nww7tb.du56m.ND.WL6QLUdpHkNVjoPyxbZaWx9quxw3hbe', 1, '2025-06-06 12:11:29', 'user'),
(10, '7', 'Ardi 7', 'X TKJ 1', 7, '$2y$10$jwgUfdjkScoXjwmxm3i5.uZDaWGEiV2ETTmKhAG86Y6hqsOGjgTf.', 1, '2025-06-06 12:11:29', 'user');

-- --------------------------------------------------------

--
-- Table structure for table `user_sessions`
--

CREATE TABLE `user_sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` int NOT NULL,
  `role` enum('admin','user') NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text NOT NULL,
  `last_activity` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `votes`
--

CREATE TABLE `votes` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `candidate_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `votes`
--

INSERT INTO `votes` (`id`, `user_id`, `candidate_id`, `created_at`) VALUES
(4, 4, 1, '2025-06-06 13:28:43'),
(5, 5, 1, '2025-06-06 13:34:51'),
(6, 10, 2, '2025-06-06 13:39:27'),
(7, 8, 3, '2025-06-07 06:48:49'),
(8, 6, 1, '2025-06-07 06:49:18');

-- --------------------------------------------------------

--
-- Table structure for table `voting_time`
--

CREATE TABLE `voting_time` (
  `id` int NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `voting_time`
--

INSERT INTO `voting_time` (`id`, `start_time`, `end_time`, `created_at`) VALUES
(1, '2025-06-05 17:41:00', '2025-06-10 17:41:00', '2025-06-06 10:41:59');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `candidates`
--
ALTER TABLE `candidates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `login_history`
--
ALTER TABLE `login_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `user_type` (`user_type`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nis` (`nis`);

--
-- Indexes for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `votes`
--
ALTER TABLE `votes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `candidate_id` (`candidate_id`);

--
-- Indexes for table `voting_time`
--
ALTER TABLE `voting_time`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `candidates`
--
ALTER TABLE `candidates`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `login_history`
--
ALTER TABLE `login_history`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `votes`
--
ALTER TABLE `votes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `voting_time`
--
ALTER TABLE `voting_time`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD CONSTRAINT `user_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `votes`
--
ALTER TABLE `votes`
  ADD CONSTRAINT `votes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `votes_ibfk_2` FOREIGN KEY (`candidate_id`) REFERENCES `candidates` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
