-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 07, 2026 at 01:37 AM
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
-- Database: `ticket_event_sekolah`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `nama`, `username`, `password`) VALUES
(1, 'atmin', 'admin', '$2y$10$ZKPl9itlPOwmYUJgh096Le9Q4jZMM8UXY70HPbSIyq9RoM0XiUVHm'),
(2, '1', '1', '$2y$10$oAZGtoH2uZiMcaVTJadxzOMDJ9p.TxzAelXejht..noO6IK5aFdQO'),
(3, '2', '2', '$2y$10$3gZv01SUTmYSf070rNG7hOhVF1Hmy9gSBv7PgbHrCWf7jz34P8LcG');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `judul` varchar(150) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `harga` int(11) DEFAULT NULL,
  `slot_tiket` int(11) DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `tanggal_akhir` date DEFAULT NULL,
  `lokasi` varchar(100) DEFAULT NULL,
  `jam_mulai` time DEFAULT NULL,
  `jam_selesai` time DEFAULT NULL,
  `sorotan` enum('yes','no') DEFAULT 'no'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `judul`, `deskripsi`, `harga`, `slot_tiket`, `gambar`, `tanggal`, `tanggal_akhir`, `lokasi`, `jam_mulai`, `jam_selesai`, `sorotan`) VALUES
(18, 'Nopal Selection`s', 'Meet and greet with Nopal, The Greatest Musician Teacher`s In The World', 15000, 100, '1779327260_Screenshot 2025-10-08 073739.png', '2026-07-20', NULL, 'Kelas A1 01', '12:00:00', '15:00:00', 'no'),
(19, 'Festival Seni & Musik', 'Acara pentas seni siswa yang menampilkan band sekolah, tari tradisional, dan modern dance.', 5000, 113, '1779327880_download.jpg', '2026-08-21', '2026-09-01', 'Aula Sekolah', '12:00:00', NULL, 'yes'),
(20, 'Kompetisi Futsal Antar Sekolah', 'Turnamen futsal tingkat SMP/SMA dengan sistem grup dan final.', 10000, 48, '1779668583_download (1).png', '2026-05-22', '2026-05-23', 'Aula Sekolah', '14:00:00', '10:00:00', 'yes'),
(21, 'Class Meeting 2025', 'Perlombaan olahraga dan hiburan setelah ujian semester antar kelas.', 0, 40, '1779630205_images.png', '2026-07-01', NULL, 'Aula Sekolah', '11:00:00', '12:00:00', 'yes'),
(22, 'wxsfx', 'udfiav', 50000, 9, '1788493531_download.jpg', '2026-10-06', '2026-11-07', 'hfcou', '12:00:00', '15:00:00', 'yes'),
(23, 'Class Meeting 2026', 'lasdjyu', 123456, 123, '1788493601_download.png', '2026-10-04', '2026-11-04', 'scywiu', '12:00:00', '15:00:00', 'no');

-- --------------------------------------------------------

--
-- Table structure for table `siswa`
--

CREATE TABLE `siswa` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `nisn` varchar(30) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `gender` varchar(20) DEFAULT 'Belum Diatur',
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `siswa`
--

INSERT INTO `siswa` (`id`, `nama`, `nisn`, `password`, `gender`, `foto`) VALUES
(6, 'am', '123456', '$2y$10$AU4xALu0ax8F1MupSC1fCeXLMeBU5KCDXvMKDnEVe0waaQzABAmIi', 'Perempuan', 'siswa_6_1779629198.jpg'),
(7, 'ael', '123455', '$2y$10$/p5agwpKunIsjoRktvn/GuAFWxpA7I87Ezqbf2lvOLWMY0a1ZbjZ6', 'Perempuan', NULL),
(12, '2', '222222', '$2y$10$qsioKNpyFslsjWu0Yk65y.7jhfkVjQvCvfciph9jfQWYYizKg9i6u', 'Belum Diatur', NULL),
(13, 'amar', '555555', '$2y$10$DGm6ODPYpxlpQJ6JVwSOj.bGpKl1s3Dxd.1IppmsJ7HPnXQTpN7Yu', 'Perempuan', 'siswa_13_1780504749.png'),
(14, 'aa', '666666', '$2y$10$ycazqQZk3OzKDjFYOe2u4.XZSNP1fy.FT3iSs1jV/I.YKnAkfoyC.', 'Laki-laki', 'siswa_14_1780543245.png');

-- --------------------------------------------------------

--
-- Table structure for table `tiket`
--

CREATE TABLE `tiket` (
  `id` int(11) NOT NULL,
  `siswa_id` int(11) DEFAULT NULL,
  `event_id` int(11) DEFAULT NULL,
  `jumlah` int(11) DEFAULT NULL,
  `total_harga` int(11) DEFAULT NULL,
  `kode_unik` varchar(10) DEFAULT NULL,
  `waktu_pesan` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `metode_bayar` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tiket`
--

INSERT INTO `tiket` (`id`, `siswa_id`, `event_id`, `jumlah`, `total_harga`, `kode_unik`, `waktu_pesan`, `created_at`, `metode_bayar`) VALUES
(22, 6, 18, 5, 75000, 'F65234A9', '2026-05-21 01:34:46', '2026-05-21 01:34:46', NULL),
(23, 6, 19, 5, 75000, '6CB4357C', '2026-05-21 01:47:22', '2026-05-21 01:47:22', NULL),
(24, 6, 19, 2, 30000, '8B982901', '2026-05-21 01:52:20', '2026-05-21 01:52:20', NULL),
(25, 6, 19, 3, 45000, '5E0FFB83', '2026-05-21 02:01:41', '2026-05-21 02:01:41', NULL),
(26, 6, 19, 5, 75000, 'FD5F85BC', '2026-05-21 02:02:32', '2026-05-21 02:02:32', NULL),
(27, 6, 19, 5, 75000, '8BD1A379', '2026-05-21 02:06:10', '2026-05-21 02:06:10', NULL),
(28, 6, 20, 1, 13000, 'C7C984A7', '2026-05-21 02:19:13', '2026-05-21 02:19:13', NULL),
(29, 6, 18, 1, 15000, '6073830B', '2026-05-21 02:29:29', '2026-05-21 02:29:29', NULL),
(30, 12, 18, 5, 75000, 'E7A5BB57', '2026-05-21 05:33:20', '2026-05-21 05:33:20', NULL),
(31, 6, 21, 4, 0, '27A016E1', '2026-05-24 14:10:39', '2026-05-24 14:10:39', NULL),
(32, 6, 21, 1, 0, '7B4EBD7A', '2026-05-24 14:19:52', '2026-05-24 14:19:52', NULL),
(33, 6, 18, 1, 15000, '40180677', '2026-05-24 14:27:04', '2026-05-24 14:27:04', NULL),
(34, 6, 21, 1, 0, 'D9990672', '2026-05-24 14:28:04', '2026-05-24 14:28:04', NULL),
(35, 6, 18, 1, 15000, 'C463A3D1', '2026-05-24 14:29:00', '2026-05-24 14:29:00', NULL),
(36, 6, 18, 1, 15000, '9C318E77', '2026-05-24 14:32:03', '2026-05-24 14:32:03', NULL),
(37, 6, 18, 1, 15000, '256F2186', '2026-05-24 14:32:38', '2026-05-24 14:32:38', NULL),
(38, 6, 18, 1, 15000, '0DB54D5E', '2026-05-24 14:36:00', '2026-05-24 14:36:00', NULL),
(39, 6, 18, 1, 15000, '7C6272D1', '2026-05-24 14:37:32', '2026-05-24 14:37:32', 'QRIS'),
(40, 6, 18, 1, 15000, 'FB971C9F', '2026-05-24 14:37:43', '2026-05-24 14:37:43', 'QRIS'),
(41, 6, 18, 1, 15000, '275CCAA0', '2026-05-24 14:38:46', '2026-05-24 14:38:46', 'QRIS'),
(42, 6, 18, 1, 15000, '642A9BBC', '2026-05-24 14:39:37', '2026-05-24 14:39:37', 'QRIS'),
(43, 6, 18, 5, 75000, 'C4DD7F7D', '2026-05-24 14:41:28', '2026-05-24 14:41:28', 'QRIS'),
(44, 6, 18, 5, 75000, '040CC303', '2026-05-24 14:42:57', '2026-05-24 14:42:57', 'Transfer Bank'),
(45, 6, 18, 5, 75000, '6552A114', '2026-05-24 14:43:11', '2026-05-24 14:43:11', 'Transfer Bank'),
(46, 6, 18, 10, 150000, '2754A720', '2026-05-24 14:43:36', '2026-05-24 14:43:36', 'QRIS'),
(47, 6, 18, 10, 150000, '374ACF6A', '2026-05-24 14:43:49', '2026-05-24 14:43:49', NULL),
(48, 6, 18, 1, 15000, 'F973BCA1', '2026-05-24 23:50:10', '2026-05-24 23:50:10', NULL),
(49, 6, 18, 1, 15000, 'F3F02437', '2026-05-24 23:50:31', '2026-05-24 23:50:31', NULL),
(50, 6, 18, 1, 15000, 'E3AF39AA', '2026-05-24 23:58:40', '2026-05-24 23:58:40', NULL),
(51, 6, 18, 1, 15000, '8A84C70A', '2026-05-24 23:59:27', '2026-05-24 23:59:27', NULL),
(52, 6, 18, 1, 15000, '0E43D9A3', '2026-05-25 00:02:25', '2026-05-25 00:02:25', NULL),
(53, 6, 18, 1, 15000, 'DFCB6F8A', '2026-05-25 00:03:01', '2026-05-25 00:03:01', '-'),
(54, 6, 18, 1, 15000, '70CCB24F', '2026-05-25 00:04:10', '2026-05-25 00:04:10', '-'),
(55, 6, 18, 1, 15000, 'F5071868', '2026-05-25 00:04:44', '2026-05-25 00:04:44', NULL),
(56, 6, 18, 1, 15000, '8A56FC6A', '2026-05-25 00:04:56', '2026-05-25 00:04:56', '-'),
(57, 6, 18, 1, 15000, 'C1CA93E7', '2026-05-25 00:06:05', '2026-05-25 00:06:05', '-'),
(58, 6, 18, 1, 15000, '0A2EA96F', '2026-05-25 00:06:16', '2026-05-25 00:06:16', NULL),
(59, 6, 18, 1, 15000, '71084E07', '2026-05-25 00:07:07', '2026-05-25 00:07:07', NULL),
(60, 6, 18, 1, 15000, '0624F984', '2026-05-25 00:09:53', '2026-05-25 00:09:53', NULL),
(61, 6, 18, 1, 15000, 'C2056B64', '2026-05-25 00:10:03', '2026-05-25 00:10:03', NULL),
(62, 6, 18, 1, 15000, '61A4A25E', '2026-05-25 00:17:01', '2026-05-25 00:17:01', NULL),
(63, 6, 21, 1, 0, '016CA81B', '2026-05-25 00:18:06', '2026-05-25 00:18:06', NULL),
(64, 12, 20, 1, 10000, '74DC887A', '2026-05-25 01:18:25', '2026-05-25 01:18:25', NULL),
(65, 6, 20, 1, 10000, '1673CDFC', '2026-06-01 11:24:57', '2026-06-01 11:24:57', NULL),
(66, 6, 19, 1, 5000, '584EF911', '2026-06-01 11:30:25', '2026-06-01 11:30:25', NULL),
(67, 6, 21, 1, 0, 'FFE3A240', '2026-06-03 09:13:26', '2026-06-03 09:13:26', NULL),
(68, 6, 19, 1, 5000, '48645C02', '2026-06-03 09:18:17', '2026-06-03 09:18:17', NULL),
(69, 6, 19, 1, 5000, 'F9295C8B', '2026-06-03 09:37:46', '2026-06-03 09:37:46', NULL),
(70, 6, 21, 1, 0, '73BFBAAA', '2026-06-03 09:38:13', '2026-06-03 09:38:13', NULL),
(71, 6, 19, 1, 5000, 'A063293C', '2026-06-03 09:39:18', '2026-06-03 09:39:18', NULL),
(72, 6, 20, 5, 50000, '5E4E44F1', '2026-06-03 16:27:46', '2026-06-03 16:27:46', NULL),
(73, 13, 19, 6, 30000, '6E362247', '2026-06-03 16:38:07', '2026-06-03 16:38:07', NULL),
(74, 13, 19, 5, 25000, 'FECC5B33', '2026-06-03 16:43:32', '2026-06-03 16:43:32', NULL),
(75, 13, 21, 3, 0, '0538B10C', '2026-06-03 16:43:40', '2026-06-03 16:43:40', NULL),
(76, 14, 21, 5, 0, '600B5851', '2026-06-04 03:19:47', '2026-06-04 03:19:47', NULL),
(77, 14, 19, 5, 25000, '0CFD3284', '2026-06-04 03:20:02', '2026-06-04 03:20:02', NULL),
(78, 6, 19, 1, 5000, '9BC91B67', '2026-07-23 01:13:40', '2026-07-23 01:13:40', NULL),
(79, 6, 19, 1, 5000, '1BCBED99', '2026-07-23 01:13:55', '2026-07-23 01:13:55', NULL),
(80, 6, 22, 1, 50000, '5DA962CF', '2026-09-04 03:53:09', '2026-09-04 03:53:09', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `siswa`
--
ALTER TABLE `siswa`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nisn` (`nisn`);

--
-- Indexes for table `tiket`
--
ALTER TABLE `tiket`
  ADD PRIMARY KEY (`id`),
  ADD KEY `siswa_id` (`siswa_id`),
  ADD KEY `fk_tiket_event` (`event_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `siswa`
--
ALTER TABLE `siswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `tiket`
--
ALTER TABLE `tiket`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tiket`
--
ALTER TABLE `tiket`
  ADD CONSTRAINT `fk_tiket_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tiket_ibfk_1` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`id`),
  ADD CONSTRAINT `tiket_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
