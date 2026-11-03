-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 21, 2026 at 08:05 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET FOREIGN_KEY_CHECKS = 0;
START TRANSACTION;
SET time_zone = "+00:00";

-- Database dibuat otomatis bila belum ada, sehingga berkas ini bisa langsung diimpor
-- dari phpMyAdmin (tab Import di beranda, tanpa memilih database lebih dulu).
CREATE DATABASE IF NOT EXISTS `spp_ku` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `spp_ku`;


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `spp_ku`
--

-- --------------------------------------------------------

--
-- Table structure for table `bukus`
--

DROP TABLE IF EXISTS `bukus`;
CREATE TABLE `bukus` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nomor_buku` varchar(40) NOT NULL,
  `judul` varchar(200) NOT NULL,
  `pengarang` varchar(120) DEFAULT NULL,
  `penerbit` varchar(120) DEFAULT NULL,
  `tahun_terbit` smallint(5) UNSIGNED DEFAULT NULL,
  `kategori_id` bigint(20) UNSIGNED NOT NULL,
  `jumlah_total` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `jumlah_tersedia` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `rak` varchar(30) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `cover` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bukus`
--

INSERT INTO `bukus` (`id`, `nomor_buku`, `judul`, `pengarang`, `penerbit`, `tahun_terbit`, `kategori_id`, `jumlah_total`, `jumlah_tersedia`, `rak`, `deskripsi`, `cover`, `created_at`, `updated_at`) VALUES
(1, 'BK-101', 'Buku Pendidikan Agama Islam dan Budi Pekerti Kelas X', '-', '-', 2024, 6, 10, 10, 'A-1', NULL, NULL, NULL, '2026-10-20 01:58:38'),
(2, 'BK-102', 'Buku Pendidikan Agama Katolik dan Budi Pekerti Kelas X', '-', '-', 2024, 6, 10, 10, 'A-1', NULL, NULL, NULL, '2026-10-20 01:58:44'),
(3, 'BK-103', 'Buku Pendidikan Agama Kristen dan Budi Pekerti Kelas X', '-', '-', 2024, 6, 10, 10, 'A-1', NULL, NULL, NULL, '2026-10-20 01:58:55'),
(4, 'BK-104', 'Buku Pendidikan Pancasila Kelas X', '-', '-', 2024, 2, 10, 10, 'A-2', NULL, NULL, NULL, NULL),
(5, 'BK-105', 'Buku Sejarah Kelas X', '-', '-', 2024, 2, 10, 10, 'A-2', NULL, NULL, NULL, NULL),
(6, 'BK-106', 'Buku Bahasa Indonesia Kelas X', '-', '-', 2024, 2, 10, 10, 'A-3', NULL, NULL, NULL, NULL),
(7, 'BK-107', 'Buku Bahasa Inggris Kelas X', '-', '-', 2024, 2, 10, 10, 'A-3', NULL, NULL, NULL, NULL),
(8, 'BK-108', 'Buku Matematika Kelas X', '-', '-', 2024, 2, 10, 10, 'A-4', NULL, NULL, NULL, NULL),
(9, 'BK-109', 'Buku Pendidikan Jasmani, Olahraga, dan Kesehatan (PJOK) Kelas X', '-', '-', 2024, 2, 10, 10, 'A-4', NULL, NULL, NULL, NULL),
(10, 'BK-201', 'Buku Pendidikan Agama Islam dan Budi Pekerti Kelas XI', '-', '-', 2024, 6, 10, 10, 'B-1', NULL, NULL, NULL, '2026-10-20 01:59:08'),
(11, 'BK-202', 'Buku Pendidikan Agama Katolik dan Budi Pekerti Kelas XI', '-', '-', 2024, 6, 10, 10, 'B-1', NULL, NULL, NULL, '2026-10-20 01:59:26'),
(12, 'BK-203', 'Buku Pendidikan Agama Kristen dan Budi Pekerti Kelas XI', '-', '-', 2024, 6, 10, 10, 'B-1', NULL, NULL, NULL, '2026-10-20 01:59:40'),
(13, 'BK-204', 'Buku Pendidikan Pancasila Kelas XI', '-', '-', 2024, 2, 10, 10, 'B-2', NULL, NULL, NULL, NULL),
(14, 'BK-205', 'Buku Sejarah Kelas XI', '-', '-', 2024, 2, 10, 10, 'B-2', NULL, NULL, NULL, NULL),
(15, 'BK-206', 'Buku Bahasa Indonesia Kelas XI', '-', '-', 2024, 2, 10, 10, 'B-3', NULL, NULL, NULL, NULL),
(16, 'BK-207', 'Buku Bahasa Inggris Kelas XI', '-', '-', 2024, 2, 10, 10, 'B-3', NULL, NULL, NULL, NULL),
(17, 'BK-208', 'Buku Matematika Kelas XI', '-', '-', 2024, 2, 10, 10, 'B-4', NULL, NULL, NULL, NULL),
(18, 'BK-209', 'Buku Pendidikan Jasmani, Olahraga, dan Kesehatan (PJOK) Kelas XI', '-', '-', 2024, 2, 10, 10, 'B-4', NULL, NULL, NULL, NULL),
(19, 'BK-301', 'Buku Pendidikan Agama Islam dan Budi Pekerti Kelas XII', '-', '-', 2024, 6, 10, 10, 'C-1', NULL, NULL, NULL, '2026-10-20 02:00:13'),
(20, 'BK-302', 'Buku Pendidikan Agama Katolik dan Budi Pekerti Kelas XII', '-', '-', 2024, 6, 10, 10, 'C-1', NULL, NULL, NULL, '2026-10-20 02:00:23'),
(21, 'BK-303', 'Buku Pendidikan Agama Kristen dan Budi Pekerti Kelas XII', '-', '-', 2024, 6, 10, 10, 'C-1', NULL, NULL, NULL, '2026-10-20 02:00:35'),
(22, 'BK-304', 'Buku Pendidikan Pancasila Kelas XII', '-', '-', 2024, 2, 10, 10, 'C-2', NULL, NULL, NULL, NULL),
(23, 'BK-305', 'Buku Sejarah Kelas XII', '-', '-', 2024, 2, 10, 10, 'C-2', NULL, NULL, NULL, NULL),
(24, 'BK-306', 'Buku Bahasa Indonesia Kelas XII', '-', '-', 2024, 2, 10, 10, 'C-3', NULL, NULL, NULL, NULL),
(25, 'BK-307', 'Buku Bahasa Inggris Kelas XII', '-', '-', 2024, 2, 10, 10, 'C-3', NULL, NULL, NULL, NULL),
(26, 'BK-308', 'Buku Matematika Kelas XII', '-', '-', 2024, 2, 10, 10, 'C-4', NULL, NULL, NULL, NULL),
(27, 'BK-309', 'Buku Pendidikan Jasmani, Olahraga, dan Kesehatan (PJOK) Kelas XII', '-', '-', 2024, 2, 10, 10, 'C-4', NULL, NULL, NULL, NULL),
(28, 'BK-TKA-01', 'Buku X-Press TKA Bahasa Indonesia', '-', '-', 2024, 7, 10, 10, 'D-1', NULL, NULL, NULL, NULL),
(29, 'BK-TKA-02', 'Buku X-Press TKA Bahasa Inggris', '-', '-', 2024, 7, 10, 10, 'D-1', NULL, NULL, NULL, NULL),
(30, 'BK-TKA-03', 'Buku X-Press TKA Matematika', '-', '-', 2024, 7, 10, 10, 'D-2', NULL, NULL, NULL, NULL),
(31, 'BK-TKA-04', 'Buku X-Press TKA Sejarah', '-', '-', 2024, 7, 10, 10, 'D-2', NULL, NULL, NULL, NULL),
(32, 'BK-TKA-05', 'Buku X-Press TKA Pendidikan Pancasila', '-', '-', 2024, 7, 10, 10, 'D-3', NULL, NULL, NULL, NULL),
(33, 'BK-TKA-06', 'Buku X-Press TKA Ekonomi', '-', '-', 2024, 7, 10, 10, 'D-3', NULL, NULL, NULL, NULL),
(34, 'BK-TKA-07', 'Buku X-Press TKA Produk/Projek Kreatif dan Kewirausahaan', '-', '-', 2024, 7, 10, 10, 'D-4', NULL, NULL, NULL, NULL),
(35, 'BK-TKA-08', 'Buku Siaga TKA', '-', '-', 2024, 7, 10, 9, 'D-4', NULL, NULL, NULL, '2026-10-20 06:38:58');

-- --------------------------------------------------------

--
-- Table structure for table `kategoris`
--

DROP TABLE IF EXISTS `kategoris`;
CREATE TABLE `kategoris` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(80) NOT NULL,
  `slug` varchar(90) NOT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kategoris`
--

INSERT INTO `kategoris` (`id`, `nama`, `slug`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, 'Rekayasa Perangkat Lunak', 'rekayasa-perangkat-lunak', 'Buku pemrograman, basis data, dan jaringan.', '2026-09-16 12:40:09', '2026-09-16 12:40:09'),
(2, 'Pelajaran Umum', 'pelajaran-umum', 'Buku mata pelajaran wajib kurikulum.', '2026-09-16 12:40:09', '2026-09-16 12:40:09'),
(3, 'Novel & Sastra', 'novel-sastra', 'Fiksi, kumpulan cerpen, dan puisi.', '2026-09-16 12:40:09', '2026-09-16 12:40:09'),
(4, 'Referensi', 'referensi', 'Kamus, ensiklopedia, dan atlas.', '2026-09-16 12:40:09', '2026-09-16 12:40:09'),
(6, 'Agama', 'agama', 'Pendidikan agama dan budi pekerti.', '2026-09-16 12:40:09', '2026-09-16 12:40:09'),
(7, 'TKA', 'tka', 'Buku untuk persiapan TKA', '2026-10-20 01:36:19', '2026-10-20 01:36:19');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '2026_09_01_000001_create_kategoris_table', 1),
(3, '2026_09_01_000002_create_bukus_table', 1),
(4, '2026_09_01_000003_create_peminjamans_table', 1),
(5, '2026_10_21_000001_add_telepon_to_users_table', 1),
(6, '2026_10_22_000001_create_eksemplars_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(191) NOT NULL,
  `token` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `peminjamans`
--

DROP TABLE IF EXISTS `peminjamans`;
CREATE TABLE `peminjamans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kode_trx` varchar(30) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `buku_id` bigint(20) UNSIGNED NOT NULL,
  `tanggal_pengajuan` date NOT NULL,
  `tanggal_jatuh_tempo` date NOT NULL,
  `tanggal_kembali` date DEFAULT NULL,
  `status` enum('pending','dipinjam','ditolak','selesai') NOT NULL DEFAULT 'pending',
  `catatan_siswa` text DEFAULT NULL,
  `catatan_admin` text DEFAULT NULL,
  `denda` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `jumlah_perpanjangan` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `diverifikasi_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `diverifikasi_pada` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `peminjamans`
--

INSERT INTO `peminjamans` (`id`, `kode_trx`, `user_id`, `buku_id`, `tanggal_pengajuan`, `tanggal_jatuh_tempo`, `tanggal_kembali`, `status`, `catatan_siswa`, `catatan_admin`, `denda`, `jumlah_perpanjangan`, `diverifikasi_oleh`, `diverifikasi_pada`, `created_at`, `updated_at`) VALUES
(1, 'TRX-000000001', 2, 35, '2026-10-20', '2026-11-03', NULL, 'dipinjam', NULL, 'Pengajuan disetujui. Ambil buku di meja sirkulasi.', 0, 1, 1, '2026-10-20 06:38:58', '2026-10-20 06:37:45', '2026-10-20 06:43:27');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` varchar(191) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kode` varchar(30) NOT NULL COMMENT 'NIS siswa atau kode petugas',
  `nama` varchar(120) NOT NULL,
  `email` varchar(120) DEFAULT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  `kelas` varchar(30) DEFAULT NULL,
  `role` enum('admin','siswa') NOT NULL DEFAULT 'siswa',
  `password` varchar(191) NOT NULL,
  `aktif` tinyint(1) NOT NULL DEFAULT 1,
  `harus_ganti_password` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1 = masih memakai kata sandi sementara, wajib diganti saat login',
  `password_diganti_pada` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `kode`, `nama`, `email`, `kelas`, `role`, `password`, `aktif`, `harus_ganti_password`, `password_diganti_pada`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'ADM001', 'Adiputra Arya Sena', 'petugas@smkn5solo.sch.id', NULL, 'admin', '$2y$10$f/YhZ4aVh/knULVkZ7eDleBPSdF4DnENsVEQbS0sTg5dJ/EPVtj0.', 1, 0, NULL, NULL, '2026-09-24 00:29:29', '2026-09-24 00:36:40'),
(2, '0085192025', 'Abdul Aziz Nurrahman', NULL, 'XII RPL C', 'siswa', '$2y$10$0Fq.vbOfD1bi9OB8kURFMervgI1LiFJBX8keiJxjBf/9LrrqwU/Fq', 1, 0, '2026-10-20 06:36:47', NULL, '2026-09-24 00:29:29', '2026-10-20 06:36:48'),
(3, '0099677881', 'Adiputra Arya Sena', NULL, 'XII RPL C', 'siswa', '$2y$10$D.ZsSQ.4M/CKYRh44PTigOWsE.AAgCU1b0HF/GINV94BXGzoHV81S', 1, 0, '2026-10-20 00:35:10', NULL, '2026-09-24 00:29:29', '2026-10-20 00:35:10'),
(4, '0085189232', 'Alvin Yourry Putra Mentari', NULL, 'XII RPL C', 'siswa', '$2y$10$57wc676dU7fooGNEn6vb8.hLtNDamxpVyancjVnj0.oqXHQICGTX2', 1, 1, NULL, NULL, '2026-09-24 00:29:29', '2026-09-24 00:36:41'),
(5, '0082552856', 'Amadeo Brian Ranastya Putra', NULL, 'XII RPL C', 'siswa', '$2y$10$4mfCCTlWEkcHEcnQSlLHpeG.8bls.ar1qOFjBIwTtqqAkwShXDNCa', 1, 1, NULL, NULL, '2026-09-24 00:29:29', '2026-09-24 00:36:41'),
(6, '0091140341', 'Andreas Kelvin Anasta Putra', NULL, 'XII RPL C', 'siswa', '$2y$10$amWRUfJPl3etm7NP1VrgiubaUp52nejyEfkLBAMCy/B7RdfaEeEya', 1, 1, NULL, NULL, '2026-09-24 00:29:29', '2026-09-24 00:36:41'),
(7, '0081528255', 'Anjeli Afifah Darajati', NULL, 'XII RPL C', 'siswa', '$2y$10$CiqM8m.TpGjY3P.kz3LLoeAYNJmOLKxnCbX0HyUNwzQg/X3B2F..C', 1, 1, NULL, NULL, '2026-09-24 00:29:29', '2026-09-24 00:36:41'),
(8, '0094655031', 'Azriel Aida Nurochim', NULL, 'XII RPL C', 'siswa', '$2y$10$kTfmE5QdkPiFTLnPYakfU.oQyIp2XQ8Rbp3fXA79Jc2MILgmGaqfa', 1, 1, NULL, NULL, '2026-09-24 00:29:29', '2026-09-24 00:36:41'),
(9, '0089632086', 'Bintang Fahri Hascarya', NULL, 'XII RPL C', 'siswa', '$2y$10$Iz33MQTJhEhQJt0M743iH.fr6Xawb7SjmArSaw70ZYZyu7Bvy6oY2', 1, 1, NULL, NULL, '2026-09-24 00:29:29', '2026-09-24 00:36:41'),
(10, '0082793500', 'Carisha Amira Insani', NULL, 'XII RPL C', 'siswa', '$2y$10$MZ1Y4p8LbFmijMz4cYTtSulKn1vyeKQNME80rZGVSCO6pQBBxAUFe', 1, 1, NULL, NULL, '2026-09-24 00:29:29', '2026-09-24 00:36:41'),
(11, '0096404873', 'Chelsea Muhammad Adli', NULL, 'XII RPL C', 'siswa', '$2y$10$Xs0UFbxVInuomhZOlCzNXO.twZBivaf/52nDPRzWJ3knb1zJhnrbG', 1, 1, NULL, NULL, '2026-09-24 00:29:29', '2026-09-24 00:36:41'),
(12, '0085226056', 'Christian Wardana', NULL, 'XII RPL C', 'siswa', '$2y$10$deVM6wqcfMs2rdUcAsOiWu7Xsn5eTyLOM5oSFld2E24R5eun9/AVG', 1, 1, NULL, NULL, '2026-09-24 00:29:29', '2026-09-24 00:36:41'),
(13, '0099325275', 'Cleodora Bertha Fortuna', NULL, 'XII RPL C', 'siswa', '$2y$10$aizzMnTcMbUJRHvV/7Pnce7XRsg85NWGXZLtDggn6O3ps2WI5EIMy', 1, 1, NULL, NULL, '2026-09-24 00:29:29', '2026-09-24 00:36:41'),
(14, '0099196067', 'Cristiano Vieli Kaligis', NULL, 'XII RPL C', 'siswa', '$2y$10$bz9IwjI6ZhKmVv/6OEHdMOaHD9L8TfYAI/14SkPzwwTrBQBWN7Aau', 1, 1, NULL, NULL, '2026-09-24 00:29:29', '2026-09-24 00:36:41'),
(15, '0093922303', 'Daffa Farih Irfanudin', NULL, 'XII RPL C', 'siswa', '$2y$10$cWaybjsYGReRSeHudfQnd.pWHiQfIp0cVanwjMRZjscMNR8bfEW7W', 1, 1, NULL, NULL, '2026-09-24 00:29:29', '2026-09-24 00:36:41'),
(16, '0087475927', 'Desta Tiar Redriana', NULL, 'XII RPL C', 'siswa', '$2y$10$X16t0qiwVzSb9lrsLS.KGuG6FuN081tStGuiCrZjVuLK3HaqcjfI2', 1, 1, NULL, NULL, '2026-09-24 00:29:29', '2026-09-24 00:36:41'),
(17, '0091553307', 'Farrel Brilliant Nugroho', NULL, 'XII RPL C', 'siswa', '$2y$10$0AZTWJCcBBtc0QjPW/uFvOcNcGd0eWGp/.a7tvlzfSWcMxng0OIsy', 1, 1, NULL, NULL, '2026-09-24 00:29:29', '2026-09-24 00:36:41'),
(18, '0086726378', 'Fida Qurottu Aini', NULL, 'XII RPL C', 'siswa', '$2y$10$vzj0rNCxlm.mrn96h/CLpun2iaQBfcpZ0LgdhvBWUivak4s1xem2m', 1, 1, NULL, NULL, '2026-09-24 00:29:29', '2026-09-24 00:36:41'),
(19, '0098225927', 'Ghozi Mumtaza Hanif', NULL, 'XII RPL C', 'siswa', '$2y$10$1277giVaXujGto4GmFzrd.bff7pyQzHk3FY5CQFogkctlqoOUYO.K', 1, 1, NULL, NULL, '2026-09-24 00:29:29', '2026-09-24 00:36:41'),
(20, '0083318812', 'Irfan Aji Wardana', NULL, 'XII RPL C', 'siswa', '$2y$10$x0CoI3g7YWG4EbFBzusTbeU6IijV8KN/pRHxGq11NNkP9Gfh4/zby', 1, 1, NULL, NULL, '2026-09-24 00:29:29', '2026-09-24 00:36:42'),
(21, '0098813285', 'Manestiko Banyubiru', NULL, 'XII RPL C', 'siswa', '$2y$10$qqIJRvVxUKzMuwNhnaP2wu6uh1PSDmYRTvtH/cDNePwglzJk3gE7i', 1, 1, NULL, NULL, '2026-09-24 00:29:29', '2026-09-24 00:36:42'),
(22, '0082434559', 'Michael Aditya', NULL, 'XII RPL C', 'siswa', '$2y$10$fK2G6yUcZCYk8zg2XtOYHOZ59gldi/5OlD8w8vICgc2BdrgG8oTSu', 1, 1, NULL, NULL, '2026-09-24 00:29:29', '2026-09-24 00:36:42'),
(23, '0099030536', 'Moses Adi Saputra', NULL, 'XII RPL C', 'siswa', '$2y$10$z5wimy4eAeq4U0KhaqLpNed3RVbHheipKAbBJC2z0i412fPJK95nq', 1, 1, NULL, NULL, '2026-09-24 00:29:29', '2026-09-24 00:36:42'),
(24, '0097488229', 'Muhammad Anshori', NULL, 'XII RPL C', 'siswa', '$2y$10$BeNNb6Jgps0rrRGx/C.JkeZ0e0FS4l7Y.L2aWOvG4sHesaZuSsxlG', 1, 1, NULL, NULL, '2026-09-24 00:29:29', '2026-09-24 00:36:42'),
(25, '3096322062', 'Muhammad Fahri Rahman Arrafi', NULL, 'XII RPL C', 'siswa', '$2y$10$qu/gpKU2kpZ6lnUQsMOZku3YrqA7hv4Kx3XQOSELVLtAd25s0ISh.', 1, 1, NULL, NULL, '2026-09-24 00:29:29', '2026-09-24 00:36:42'),
(26, '0098352062', 'Muhammad Rosyid Muryanto', NULL, 'XII RPL C', 'siswa', '$2y$10$4mxs9AWoXgwuRw4WYAqLaui08furDALS3DsJxOq6BAc2chKAlNDBm', 1, 1, NULL, NULL, '2026-09-24 00:29:29', '2026-09-24 00:36:42'),
(27, '3098883861', 'Nicholas Gusti Saputra', NULL, 'XII RPL C', 'siswa', '$2y$10$Ni79OMao/1EgXDueFPDn.ub1H23XNuyaXsUhwxIxTUyKm704c3SpG', 1, 1, NULL, NULL, '2026-09-24 00:29:29', '2026-09-24 00:36:42'),
(28, '0099821145', 'Pranaja Abi Praya Widyatamaka', NULL, 'XII RPL C', 'siswa', '$2y$10$4FvGLPxlDnkUsAopEX1KU.Quv2VpGsRzkaKiT7dCctJbl8orntk6i', 1, 1, NULL, NULL, '2026-09-24 00:29:29', '2026-09-24 00:36:42'),
(29, '0097645030', 'Ramaditya Surya Putra', NULL, 'XII RPL C', 'siswa', '$2y$10$v4rEZpkvIiaPucDtZHVumunyjKT8eY1F6jC4rZTq4XrdHq/b8ugZ.', 1, 1, NULL, NULL, '2026-09-24 00:29:29', '2026-09-24 00:36:42'),
(30, '0082793082', 'Sabian Apta Arira', NULL, 'XII RPL C', 'siswa', '$2y$10$NhAjIIT7/PmiIHrRd3bgdOKPt4TYgT6M8.Es.Q7dJPznm32bDBMkS', 1, 1, NULL, NULL, '2026-09-24 00:29:29', '2026-09-24 00:36:42'),
(31, '0098491439', 'Satria Rahmansyah', NULL, 'XII RPL C', 'siswa', '$2y$10$s5opuWgf/TjRjZ21eWUN7.JHHn8m8kZKRh60twuvSrVdjAcufMnxW', 1, 1, NULL, NULL, '2026-09-24 00:29:29', '2026-09-24 00:36:42'),
(32, '0094722484', 'Theodorick Bakuh Panggalih', NULL, 'XII RPL C', 'siswa', '$2y$10$sp7TDYqqiUzu7iWVkxgbkefgW9uxgKV4XTLQu82zXTMk93AGGZ3Yi', 1, 1, NULL, NULL, '2026-09-24 00:29:29', '2026-09-24 00:36:42'),
(33, '0082122132', 'X-lan Adi Nugroho', NULL, 'XII RPL C', 'siswa', '$2y$10$3mrJCmrCf/p/6xnSNIHq2uK4bjqBe2scnnJiak7CtgBCA9kir6/F2', 1, 1, NULL, NULL, '2026-09-24 00:29:29', '2026-09-24 00:36:42');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bukus`
--
ALTER TABLE `bukus`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bukus_nomor_buku_unique` (`nomor_buku`),
  ADD KEY `bukus_judul_index` (`judul`),
  ADD KEY `bukus_kategori_id_foreign` (`kategori_id`);

--
-- Indexes for table `kategoris`
--
ALTER TABLE `kategoris`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kategoris_nama_unique` (`nama`),
  ADD UNIQUE KEY `kategoris_slug_unique` (`slug`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `peminjamans`
--
ALTER TABLE `peminjamans`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `peminjamans_kode_trx_unique` (`kode_trx`),
  ADD KEY `peminjamans_status_index` (`status`),
  ADD KEY `peminjamans_user_id_foreign` (`user_id`),
  ADD KEY `peminjamans_buku_id_foreign` (`buku_id`),
  ADD KEY `peminjamans_diverifikasi_oleh_foreign` (`diverifikasi_oleh`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_kode_unique` (`kode`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bukus`
--
ALTER TABLE `bukus`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `kategoris`
--
ALTER TABLE `kategoris`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `peminjamans`
--
ALTER TABLE `peminjamans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bukus`
--
ALTER TABLE `bukus`
  ADD CONSTRAINT `bukus_kategori_id_foreign` FOREIGN KEY (`kategori_id`) REFERENCES `kategoris` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `peminjamans`
--
ALTER TABLE `peminjamans`
  ADD CONSTRAINT `peminjamans_buku_id_foreign` FOREIGN KEY (`buku_id`) REFERENCES `bukus` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `peminjamans_diverifikasi_oleh_foreign` FOREIGN KEY (`diverifikasi_oleh`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `peminjamans_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

-- --------------------------------------------------------

--
-- Table structure for table `eksemplars` (kode unik per fisik buku)
--

DROP TABLE IF EXISTS `eksemplars`;
CREATE TABLE `eksemplars` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `buku_id` bigint(20) UNSIGNED NOT NULL,
  `kode` varchar(60) NOT NULL,
  `prefix` varchar(60) NOT NULL DEFAULT '',
  `urutan` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `status` enum('tersedia','dipinjam','nonaktif') NOT NULL DEFAULT 'tersedia',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `eksemplars_kode_unique` (`kode`),
  KEY `eksemplars_buku_id_status_prefix_urutan_index` (`buku_id`,`status`,`prefix`,`urutan`),
  CONSTRAINT `eksemplars_buku_id_foreign` FOREIGN KEY (`buku_id`) REFERENCES `bukus` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Kode eksemplar dibuat otomatis dari nomor buku (NOMOR-BUKU-01, -02, ...).
-- Ubah/tambah rentang kodenya dari menu Master data buku -> tombol "Kode".
--

INSERT INTO `eksemplars` (`id`, `buku_id`, `kode`, `prefix`, `urutan`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'BK-101-01', 'BK-101-', 1, 'tersedia', NOW(), NOW()),
(2, 1, 'BK-101-02', 'BK-101-', 2, 'tersedia', NOW(), NOW()),
(3, 1, 'BK-101-03', 'BK-101-', 3, 'tersedia', NOW(), NOW()),
(4, 1, 'BK-101-04', 'BK-101-', 4, 'tersedia', NOW(), NOW()),
(5, 1, 'BK-101-05', 'BK-101-', 5, 'tersedia', NOW(), NOW()),
(6, 1, 'BK-101-06', 'BK-101-', 6, 'tersedia', NOW(), NOW()),
(7, 1, 'BK-101-07', 'BK-101-', 7, 'tersedia', NOW(), NOW()),
(8, 1, 'BK-101-08', 'BK-101-', 8, 'tersedia', NOW(), NOW()),
(9, 1, 'BK-101-09', 'BK-101-', 9, 'tersedia', NOW(), NOW()),
(10, 1, 'BK-101-10', 'BK-101-', 10, 'tersedia', NOW(), NOW()),
(11, 2, 'BK-102-01', 'BK-102-', 1, 'tersedia', NOW(), NOW()),
(12, 2, 'BK-102-02', 'BK-102-', 2, 'tersedia', NOW(), NOW()),
(13, 2, 'BK-102-03', 'BK-102-', 3, 'tersedia', NOW(), NOW()),
(14, 2, 'BK-102-04', 'BK-102-', 4, 'tersedia', NOW(), NOW()),
(15, 2, 'BK-102-05', 'BK-102-', 5, 'tersedia', NOW(), NOW()),
(16, 2, 'BK-102-06', 'BK-102-', 6, 'tersedia', NOW(), NOW()),
(17, 2, 'BK-102-07', 'BK-102-', 7, 'tersedia', NOW(), NOW()),
(18, 2, 'BK-102-08', 'BK-102-', 8, 'tersedia', NOW(), NOW()),
(19, 2, 'BK-102-09', 'BK-102-', 9, 'tersedia', NOW(), NOW()),
(20, 2, 'BK-102-10', 'BK-102-', 10, 'tersedia', NOW(), NOW()),
(21, 3, 'BK-103-01', 'BK-103-', 1, 'tersedia', NOW(), NOW()),
(22, 3, 'BK-103-02', 'BK-103-', 2, 'tersedia', NOW(), NOW()),
(23, 3, 'BK-103-03', 'BK-103-', 3, 'tersedia', NOW(), NOW()),
(24, 3, 'BK-103-04', 'BK-103-', 4, 'tersedia', NOW(), NOW()),
(25, 3, 'BK-103-05', 'BK-103-', 5, 'tersedia', NOW(), NOW()),
(26, 3, 'BK-103-06', 'BK-103-', 6, 'tersedia', NOW(), NOW()),
(27, 3, 'BK-103-07', 'BK-103-', 7, 'tersedia', NOW(), NOW()),
(28, 3, 'BK-103-08', 'BK-103-', 8, 'tersedia', NOW(), NOW()),
(29, 3, 'BK-103-09', 'BK-103-', 9, 'tersedia', NOW(), NOW()),
(30, 3, 'BK-103-10', 'BK-103-', 10, 'tersedia', NOW(), NOW()),
(31, 4, 'BK-104-01', 'BK-104-', 1, 'tersedia', NOW(), NOW()),
(32, 4, 'BK-104-02', 'BK-104-', 2, 'tersedia', NOW(), NOW()),
(33, 4, 'BK-104-03', 'BK-104-', 3, 'tersedia', NOW(), NOW()),
(34, 4, 'BK-104-04', 'BK-104-', 4, 'tersedia', NOW(), NOW()),
(35, 4, 'BK-104-05', 'BK-104-', 5, 'tersedia', NOW(), NOW()),
(36, 4, 'BK-104-06', 'BK-104-', 6, 'tersedia', NOW(), NOW()),
(37, 4, 'BK-104-07', 'BK-104-', 7, 'tersedia', NOW(), NOW()),
(38, 4, 'BK-104-08', 'BK-104-', 8, 'tersedia', NOW(), NOW()),
(39, 4, 'BK-104-09', 'BK-104-', 9, 'tersedia', NOW(), NOW()),
(40, 4, 'BK-104-10', 'BK-104-', 10, 'tersedia', NOW(), NOW()),
(41, 5, 'BK-105-01', 'BK-105-', 1, 'tersedia', NOW(), NOW()),
(42, 5, 'BK-105-02', 'BK-105-', 2, 'tersedia', NOW(), NOW()),
(43, 5, 'BK-105-03', 'BK-105-', 3, 'tersedia', NOW(), NOW()),
(44, 5, 'BK-105-04', 'BK-105-', 4, 'tersedia', NOW(), NOW()),
(45, 5, 'BK-105-05', 'BK-105-', 5, 'tersedia', NOW(), NOW()),
(46, 5, 'BK-105-06', 'BK-105-', 6, 'tersedia', NOW(), NOW()),
(47, 5, 'BK-105-07', 'BK-105-', 7, 'tersedia', NOW(), NOW()),
(48, 5, 'BK-105-08', 'BK-105-', 8, 'tersedia', NOW(), NOW()),
(49, 5, 'BK-105-09', 'BK-105-', 9, 'tersedia', NOW(), NOW()),
(50, 5, 'BK-105-10', 'BK-105-', 10, 'tersedia', NOW(), NOW()),
(51, 6, 'BK-106-01', 'BK-106-', 1, 'tersedia', NOW(), NOW()),
(52, 6, 'BK-106-02', 'BK-106-', 2, 'tersedia', NOW(), NOW()),
(53, 6, 'BK-106-03', 'BK-106-', 3, 'tersedia', NOW(), NOW()),
(54, 6, 'BK-106-04', 'BK-106-', 4, 'tersedia', NOW(), NOW()),
(55, 6, 'BK-106-05', 'BK-106-', 5, 'tersedia', NOW(), NOW()),
(56, 6, 'BK-106-06', 'BK-106-', 6, 'tersedia', NOW(), NOW()),
(57, 6, 'BK-106-07', 'BK-106-', 7, 'tersedia', NOW(), NOW()),
(58, 6, 'BK-106-08', 'BK-106-', 8, 'tersedia', NOW(), NOW()),
(59, 6, 'BK-106-09', 'BK-106-', 9, 'tersedia', NOW(), NOW()),
(60, 6, 'BK-106-10', 'BK-106-', 10, 'tersedia', NOW(), NOW()),
(61, 7, 'BK-107-01', 'BK-107-', 1, 'tersedia', NOW(), NOW()),
(62, 7, 'BK-107-02', 'BK-107-', 2, 'tersedia', NOW(), NOW()),
(63, 7, 'BK-107-03', 'BK-107-', 3, 'tersedia', NOW(), NOW()),
(64, 7, 'BK-107-04', 'BK-107-', 4, 'tersedia', NOW(), NOW()),
(65, 7, 'BK-107-05', 'BK-107-', 5, 'tersedia', NOW(), NOW()),
(66, 7, 'BK-107-06', 'BK-107-', 6, 'tersedia', NOW(), NOW()),
(67, 7, 'BK-107-07', 'BK-107-', 7, 'tersedia', NOW(), NOW()),
(68, 7, 'BK-107-08', 'BK-107-', 8, 'tersedia', NOW(), NOW()),
(69, 7, 'BK-107-09', 'BK-107-', 9, 'tersedia', NOW(), NOW()),
(70, 7, 'BK-107-10', 'BK-107-', 10, 'tersedia', NOW(), NOW()),
(71, 8, 'BK-108-01', 'BK-108-', 1, 'tersedia', NOW(), NOW()),
(72, 8, 'BK-108-02', 'BK-108-', 2, 'tersedia', NOW(), NOW()),
(73, 8, 'BK-108-03', 'BK-108-', 3, 'tersedia', NOW(), NOW()),
(74, 8, 'BK-108-04', 'BK-108-', 4, 'tersedia', NOW(), NOW()),
(75, 8, 'BK-108-05', 'BK-108-', 5, 'tersedia', NOW(), NOW()),
(76, 8, 'BK-108-06', 'BK-108-', 6, 'tersedia', NOW(), NOW()),
(77, 8, 'BK-108-07', 'BK-108-', 7, 'tersedia', NOW(), NOW()),
(78, 8, 'BK-108-08', 'BK-108-', 8, 'tersedia', NOW(), NOW()),
(79, 8, 'BK-108-09', 'BK-108-', 9, 'tersedia', NOW(), NOW()),
(80, 8, 'BK-108-10', 'BK-108-', 10, 'tersedia', NOW(), NOW()),
(81, 9, 'BK-109-01', 'BK-109-', 1, 'tersedia', NOW(), NOW()),
(82, 9, 'BK-109-02', 'BK-109-', 2, 'tersedia', NOW(), NOW()),
(83, 9, 'BK-109-03', 'BK-109-', 3, 'tersedia', NOW(), NOW()),
(84, 9, 'BK-109-04', 'BK-109-', 4, 'tersedia', NOW(), NOW()),
(85, 9, 'BK-109-05', 'BK-109-', 5, 'tersedia', NOW(), NOW()),
(86, 9, 'BK-109-06', 'BK-109-', 6, 'tersedia', NOW(), NOW()),
(87, 9, 'BK-109-07', 'BK-109-', 7, 'tersedia', NOW(), NOW()),
(88, 9, 'BK-109-08', 'BK-109-', 8, 'tersedia', NOW(), NOW()),
(89, 9, 'BK-109-09', 'BK-109-', 9, 'tersedia', NOW(), NOW()),
(90, 9, 'BK-109-10', 'BK-109-', 10, 'tersedia', NOW(), NOW()),
(91, 10, 'BK-201-01', 'BK-201-', 1, 'tersedia', NOW(), NOW()),
(92, 10, 'BK-201-02', 'BK-201-', 2, 'tersedia', NOW(), NOW()),
(93, 10, 'BK-201-03', 'BK-201-', 3, 'tersedia', NOW(), NOW()),
(94, 10, 'BK-201-04', 'BK-201-', 4, 'tersedia', NOW(), NOW()),
(95, 10, 'BK-201-05', 'BK-201-', 5, 'tersedia', NOW(), NOW()),
(96, 10, 'BK-201-06', 'BK-201-', 6, 'tersedia', NOW(), NOW()),
(97, 10, 'BK-201-07', 'BK-201-', 7, 'tersedia', NOW(), NOW()),
(98, 10, 'BK-201-08', 'BK-201-', 8, 'tersedia', NOW(), NOW()),
(99, 10, 'BK-201-09', 'BK-201-', 9, 'tersedia', NOW(), NOW()),
(100, 10, 'BK-201-10', 'BK-201-', 10, 'tersedia', NOW(), NOW()),
(101, 11, 'BK-202-01', 'BK-202-', 1, 'tersedia', NOW(), NOW()),
(102, 11, 'BK-202-02', 'BK-202-', 2, 'tersedia', NOW(), NOW()),
(103, 11, 'BK-202-03', 'BK-202-', 3, 'tersedia', NOW(), NOW()),
(104, 11, 'BK-202-04', 'BK-202-', 4, 'tersedia', NOW(), NOW()),
(105, 11, 'BK-202-05', 'BK-202-', 5, 'tersedia', NOW(), NOW()),
(106, 11, 'BK-202-06', 'BK-202-', 6, 'tersedia', NOW(), NOW()),
(107, 11, 'BK-202-07', 'BK-202-', 7, 'tersedia', NOW(), NOW()),
(108, 11, 'BK-202-08', 'BK-202-', 8, 'tersedia', NOW(), NOW()),
(109, 11, 'BK-202-09', 'BK-202-', 9, 'tersedia', NOW(), NOW()),
(110, 11, 'BK-202-10', 'BK-202-', 10, 'tersedia', NOW(), NOW()),
(111, 12, 'BK-203-01', 'BK-203-', 1, 'tersedia', NOW(), NOW()),
(112, 12, 'BK-203-02', 'BK-203-', 2, 'tersedia', NOW(), NOW()),
(113, 12, 'BK-203-03', 'BK-203-', 3, 'tersedia', NOW(), NOW()),
(114, 12, 'BK-203-04', 'BK-203-', 4, 'tersedia', NOW(), NOW()),
(115, 12, 'BK-203-05', 'BK-203-', 5, 'tersedia', NOW(), NOW()),
(116, 12, 'BK-203-06', 'BK-203-', 6, 'tersedia', NOW(), NOW()),
(117, 12, 'BK-203-07', 'BK-203-', 7, 'tersedia', NOW(), NOW()),
(118, 12, 'BK-203-08', 'BK-203-', 8, 'tersedia', NOW(), NOW()),
(119, 12, 'BK-203-09', 'BK-203-', 9, 'tersedia', NOW(), NOW()),
(120, 12, 'BK-203-10', 'BK-203-', 10, 'tersedia', NOW(), NOW()),
(121, 13, 'BK-204-01', 'BK-204-', 1, 'tersedia', NOW(), NOW()),
(122, 13, 'BK-204-02', 'BK-204-', 2, 'tersedia', NOW(), NOW()),
(123, 13, 'BK-204-03', 'BK-204-', 3, 'tersedia', NOW(), NOW()),
(124, 13, 'BK-204-04', 'BK-204-', 4, 'tersedia', NOW(), NOW()),
(125, 13, 'BK-204-05', 'BK-204-', 5, 'tersedia', NOW(), NOW()),
(126, 13, 'BK-204-06', 'BK-204-', 6, 'tersedia', NOW(), NOW()),
(127, 13, 'BK-204-07', 'BK-204-', 7, 'tersedia', NOW(), NOW()),
(128, 13, 'BK-204-08', 'BK-204-', 8, 'tersedia', NOW(), NOW()),
(129, 13, 'BK-204-09', 'BK-204-', 9, 'tersedia', NOW(), NOW()),
(130, 13, 'BK-204-10', 'BK-204-', 10, 'tersedia', NOW(), NOW()),
(131, 14, 'BK-205-01', 'BK-205-', 1, 'tersedia', NOW(), NOW()),
(132, 14, 'BK-205-02', 'BK-205-', 2, 'tersedia', NOW(), NOW()),
(133, 14, 'BK-205-03', 'BK-205-', 3, 'tersedia', NOW(), NOW()),
(134, 14, 'BK-205-04', 'BK-205-', 4, 'tersedia', NOW(), NOW()),
(135, 14, 'BK-205-05', 'BK-205-', 5, 'tersedia', NOW(), NOW()),
(136, 14, 'BK-205-06', 'BK-205-', 6, 'tersedia', NOW(), NOW()),
(137, 14, 'BK-205-07', 'BK-205-', 7, 'tersedia', NOW(), NOW()),
(138, 14, 'BK-205-08', 'BK-205-', 8, 'tersedia', NOW(), NOW()),
(139, 14, 'BK-205-09', 'BK-205-', 9, 'tersedia', NOW(), NOW()),
(140, 14, 'BK-205-10', 'BK-205-', 10, 'tersedia', NOW(), NOW()),
(141, 15, 'BK-206-01', 'BK-206-', 1, 'tersedia', NOW(), NOW()),
(142, 15, 'BK-206-02', 'BK-206-', 2, 'tersedia', NOW(), NOW()),
(143, 15, 'BK-206-03', 'BK-206-', 3, 'tersedia', NOW(), NOW()),
(144, 15, 'BK-206-04', 'BK-206-', 4, 'tersedia', NOW(), NOW()),
(145, 15, 'BK-206-05', 'BK-206-', 5, 'tersedia', NOW(), NOW()),
(146, 15, 'BK-206-06', 'BK-206-', 6, 'tersedia', NOW(), NOW()),
(147, 15, 'BK-206-07', 'BK-206-', 7, 'tersedia', NOW(), NOW()),
(148, 15, 'BK-206-08', 'BK-206-', 8, 'tersedia', NOW(), NOW()),
(149, 15, 'BK-206-09', 'BK-206-', 9, 'tersedia', NOW(), NOW()),
(150, 15, 'BK-206-10', 'BK-206-', 10, 'tersedia', NOW(), NOW()),
(151, 16, 'BK-207-01', 'BK-207-', 1, 'tersedia', NOW(), NOW()),
(152, 16, 'BK-207-02', 'BK-207-', 2, 'tersedia', NOW(), NOW()),
(153, 16, 'BK-207-03', 'BK-207-', 3, 'tersedia', NOW(), NOW()),
(154, 16, 'BK-207-04', 'BK-207-', 4, 'tersedia', NOW(), NOW()),
(155, 16, 'BK-207-05', 'BK-207-', 5, 'tersedia', NOW(), NOW()),
(156, 16, 'BK-207-06', 'BK-207-', 6, 'tersedia', NOW(), NOW()),
(157, 16, 'BK-207-07', 'BK-207-', 7, 'tersedia', NOW(), NOW()),
(158, 16, 'BK-207-08', 'BK-207-', 8, 'tersedia', NOW(), NOW()),
(159, 16, 'BK-207-09', 'BK-207-', 9, 'tersedia', NOW(), NOW()),
(160, 16, 'BK-207-10', 'BK-207-', 10, 'tersedia', NOW(), NOW()),
(161, 17, 'BK-208-01', 'BK-208-', 1, 'tersedia', NOW(), NOW()),
(162, 17, 'BK-208-02', 'BK-208-', 2, 'tersedia', NOW(), NOW()),
(163, 17, 'BK-208-03', 'BK-208-', 3, 'tersedia', NOW(), NOW()),
(164, 17, 'BK-208-04', 'BK-208-', 4, 'tersedia', NOW(), NOW()),
(165, 17, 'BK-208-05', 'BK-208-', 5, 'tersedia', NOW(), NOW()),
(166, 17, 'BK-208-06', 'BK-208-', 6, 'tersedia', NOW(), NOW()),
(167, 17, 'BK-208-07', 'BK-208-', 7, 'tersedia', NOW(), NOW()),
(168, 17, 'BK-208-08', 'BK-208-', 8, 'tersedia', NOW(), NOW()),
(169, 17, 'BK-208-09', 'BK-208-', 9, 'tersedia', NOW(), NOW()),
(170, 17, 'BK-208-10', 'BK-208-', 10, 'tersedia', NOW(), NOW()),
(171, 18, 'BK-209-01', 'BK-209-', 1, 'tersedia', NOW(), NOW()),
(172, 18, 'BK-209-02', 'BK-209-', 2, 'tersedia', NOW(), NOW()),
(173, 18, 'BK-209-03', 'BK-209-', 3, 'tersedia', NOW(), NOW()),
(174, 18, 'BK-209-04', 'BK-209-', 4, 'tersedia', NOW(), NOW()),
(175, 18, 'BK-209-05', 'BK-209-', 5, 'tersedia', NOW(), NOW()),
(176, 18, 'BK-209-06', 'BK-209-', 6, 'tersedia', NOW(), NOW()),
(177, 18, 'BK-209-07', 'BK-209-', 7, 'tersedia', NOW(), NOW()),
(178, 18, 'BK-209-08', 'BK-209-', 8, 'tersedia', NOW(), NOW()),
(179, 18, 'BK-209-09', 'BK-209-', 9, 'tersedia', NOW(), NOW()),
(180, 18, 'BK-209-10', 'BK-209-', 10, 'tersedia', NOW(), NOW()),
(181, 19, 'BK-301-01', 'BK-301-', 1, 'tersedia', NOW(), NOW()),
(182, 19, 'BK-301-02', 'BK-301-', 2, 'tersedia', NOW(), NOW()),
(183, 19, 'BK-301-03', 'BK-301-', 3, 'tersedia', NOW(), NOW()),
(184, 19, 'BK-301-04', 'BK-301-', 4, 'tersedia', NOW(), NOW()),
(185, 19, 'BK-301-05', 'BK-301-', 5, 'tersedia', NOW(), NOW()),
(186, 19, 'BK-301-06', 'BK-301-', 6, 'tersedia', NOW(), NOW()),
(187, 19, 'BK-301-07', 'BK-301-', 7, 'tersedia', NOW(), NOW()),
(188, 19, 'BK-301-08', 'BK-301-', 8, 'tersedia', NOW(), NOW()),
(189, 19, 'BK-301-09', 'BK-301-', 9, 'tersedia', NOW(), NOW()),
(190, 19, 'BK-301-10', 'BK-301-', 10, 'tersedia', NOW(), NOW()),
(191, 20, 'BK-302-01', 'BK-302-', 1, 'tersedia', NOW(), NOW()),
(192, 20, 'BK-302-02', 'BK-302-', 2, 'tersedia', NOW(), NOW()),
(193, 20, 'BK-302-03', 'BK-302-', 3, 'tersedia', NOW(), NOW()),
(194, 20, 'BK-302-04', 'BK-302-', 4, 'tersedia', NOW(), NOW()),
(195, 20, 'BK-302-05', 'BK-302-', 5, 'tersedia', NOW(), NOW()),
(196, 20, 'BK-302-06', 'BK-302-', 6, 'tersedia', NOW(), NOW()),
(197, 20, 'BK-302-07', 'BK-302-', 7, 'tersedia', NOW(), NOW()),
(198, 20, 'BK-302-08', 'BK-302-', 8, 'tersedia', NOW(), NOW()),
(199, 20, 'BK-302-09', 'BK-302-', 9, 'tersedia', NOW(), NOW()),
(200, 20, 'BK-302-10', 'BK-302-', 10, 'tersedia', NOW(), NOW()),
(201, 21, 'BK-303-01', 'BK-303-', 1, 'tersedia', NOW(), NOW()),
(202, 21, 'BK-303-02', 'BK-303-', 2, 'tersedia', NOW(), NOW()),
(203, 21, 'BK-303-03', 'BK-303-', 3, 'tersedia', NOW(), NOW()),
(204, 21, 'BK-303-04', 'BK-303-', 4, 'tersedia', NOW(), NOW()),
(205, 21, 'BK-303-05', 'BK-303-', 5, 'tersedia', NOW(), NOW()),
(206, 21, 'BK-303-06', 'BK-303-', 6, 'tersedia', NOW(), NOW()),
(207, 21, 'BK-303-07', 'BK-303-', 7, 'tersedia', NOW(), NOW()),
(208, 21, 'BK-303-08', 'BK-303-', 8, 'tersedia', NOW(), NOW()),
(209, 21, 'BK-303-09', 'BK-303-', 9, 'tersedia', NOW(), NOW()),
(210, 21, 'BK-303-10', 'BK-303-', 10, 'tersedia', NOW(), NOW()),
(211, 22, 'BK-304-01', 'BK-304-', 1, 'tersedia', NOW(), NOW()),
(212, 22, 'BK-304-02', 'BK-304-', 2, 'tersedia', NOW(), NOW()),
(213, 22, 'BK-304-03', 'BK-304-', 3, 'tersedia', NOW(), NOW()),
(214, 22, 'BK-304-04', 'BK-304-', 4, 'tersedia', NOW(), NOW()),
(215, 22, 'BK-304-05', 'BK-304-', 5, 'tersedia', NOW(), NOW()),
(216, 22, 'BK-304-06', 'BK-304-', 6, 'tersedia', NOW(), NOW()),
(217, 22, 'BK-304-07', 'BK-304-', 7, 'tersedia', NOW(), NOW()),
(218, 22, 'BK-304-08', 'BK-304-', 8, 'tersedia', NOW(), NOW()),
(219, 22, 'BK-304-09', 'BK-304-', 9, 'tersedia', NOW(), NOW()),
(220, 22, 'BK-304-10', 'BK-304-', 10, 'tersedia', NOW(), NOW()),
(221, 23, 'BK-305-01', 'BK-305-', 1, 'tersedia', NOW(), NOW()),
(222, 23, 'BK-305-02', 'BK-305-', 2, 'tersedia', NOW(), NOW()),
(223, 23, 'BK-305-03', 'BK-305-', 3, 'tersedia', NOW(), NOW()),
(224, 23, 'BK-305-04', 'BK-305-', 4, 'tersedia', NOW(), NOW()),
(225, 23, 'BK-305-05', 'BK-305-', 5, 'tersedia', NOW(), NOW()),
(226, 23, 'BK-305-06', 'BK-305-', 6, 'tersedia', NOW(), NOW()),
(227, 23, 'BK-305-07', 'BK-305-', 7, 'tersedia', NOW(), NOW()),
(228, 23, 'BK-305-08', 'BK-305-', 8, 'tersedia', NOW(), NOW()),
(229, 23, 'BK-305-09', 'BK-305-', 9, 'tersedia', NOW(), NOW()),
(230, 23, 'BK-305-10', 'BK-305-', 10, 'tersedia', NOW(), NOW()),
(231, 24, 'BK-306-01', 'BK-306-', 1, 'tersedia', NOW(), NOW()),
(232, 24, 'BK-306-02', 'BK-306-', 2, 'tersedia', NOW(), NOW()),
(233, 24, 'BK-306-03', 'BK-306-', 3, 'tersedia', NOW(), NOW()),
(234, 24, 'BK-306-04', 'BK-306-', 4, 'tersedia', NOW(), NOW()),
(235, 24, 'BK-306-05', 'BK-306-', 5, 'tersedia', NOW(), NOW()),
(236, 24, 'BK-306-06', 'BK-306-', 6, 'tersedia', NOW(), NOW()),
(237, 24, 'BK-306-07', 'BK-306-', 7, 'tersedia', NOW(), NOW()),
(238, 24, 'BK-306-08', 'BK-306-', 8, 'tersedia', NOW(), NOW()),
(239, 24, 'BK-306-09', 'BK-306-', 9, 'tersedia', NOW(), NOW()),
(240, 24, 'BK-306-10', 'BK-306-', 10, 'tersedia', NOW(), NOW()),
(241, 25, 'BK-307-01', 'BK-307-', 1, 'tersedia', NOW(), NOW()),
(242, 25, 'BK-307-02', 'BK-307-', 2, 'tersedia', NOW(), NOW()),
(243, 25, 'BK-307-03', 'BK-307-', 3, 'tersedia', NOW(), NOW()),
(244, 25, 'BK-307-04', 'BK-307-', 4, 'tersedia', NOW(), NOW()),
(245, 25, 'BK-307-05', 'BK-307-', 5, 'tersedia', NOW(), NOW()),
(246, 25, 'BK-307-06', 'BK-307-', 6, 'tersedia', NOW(), NOW()),
(247, 25, 'BK-307-07', 'BK-307-', 7, 'tersedia', NOW(), NOW()),
(248, 25, 'BK-307-08', 'BK-307-', 8, 'tersedia', NOW(), NOW()),
(249, 25, 'BK-307-09', 'BK-307-', 9, 'tersedia', NOW(), NOW()),
(250, 25, 'BK-307-10', 'BK-307-', 10, 'tersedia', NOW(), NOW()),
(251, 26, 'BK-308-01', 'BK-308-', 1, 'tersedia', NOW(), NOW()),
(252, 26, 'BK-308-02', 'BK-308-', 2, 'tersedia', NOW(), NOW()),
(253, 26, 'BK-308-03', 'BK-308-', 3, 'tersedia', NOW(), NOW()),
(254, 26, 'BK-308-04', 'BK-308-', 4, 'tersedia', NOW(), NOW()),
(255, 26, 'BK-308-05', 'BK-308-', 5, 'tersedia', NOW(), NOW()),
(256, 26, 'BK-308-06', 'BK-308-', 6, 'tersedia', NOW(), NOW()),
(257, 26, 'BK-308-07', 'BK-308-', 7, 'tersedia', NOW(), NOW()),
(258, 26, 'BK-308-08', 'BK-308-', 8, 'tersedia', NOW(), NOW()),
(259, 26, 'BK-308-09', 'BK-308-', 9, 'tersedia', NOW(), NOW()),
(260, 26, 'BK-308-10', 'BK-308-', 10, 'tersedia', NOW(), NOW()),
(261, 27, 'BK-309-01', 'BK-309-', 1, 'tersedia', NOW(), NOW()),
(262, 27, 'BK-309-02', 'BK-309-', 2, 'tersedia', NOW(), NOW()),
(263, 27, 'BK-309-03', 'BK-309-', 3, 'tersedia', NOW(), NOW()),
(264, 27, 'BK-309-04', 'BK-309-', 4, 'tersedia', NOW(), NOW()),
(265, 27, 'BK-309-05', 'BK-309-', 5, 'tersedia', NOW(), NOW()),
(266, 27, 'BK-309-06', 'BK-309-', 6, 'tersedia', NOW(), NOW()),
(267, 27, 'BK-309-07', 'BK-309-', 7, 'tersedia', NOW(), NOW()),
(268, 27, 'BK-309-08', 'BK-309-', 8, 'tersedia', NOW(), NOW()),
(269, 27, 'BK-309-09', 'BK-309-', 9, 'tersedia', NOW(), NOW()),
(270, 27, 'BK-309-10', 'BK-309-', 10, 'tersedia', NOW(), NOW()),
(271, 28, 'BK-TKA-01-01', 'BK-TKA-01-', 1, 'tersedia', NOW(), NOW()),
(272, 28, 'BK-TKA-01-02', 'BK-TKA-01-', 2, 'tersedia', NOW(), NOW()),
(273, 28, 'BK-TKA-01-03', 'BK-TKA-01-', 3, 'tersedia', NOW(), NOW()),
(274, 28, 'BK-TKA-01-04', 'BK-TKA-01-', 4, 'tersedia', NOW(), NOW()),
(275, 28, 'BK-TKA-01-05', 'BK-TKA-01-', 5, 'tersedia', NOW(), NOW()),
(276, 28, 'BK-TKA-01-06', 'BK-TKA-01-', 6, 'tersedia', NOW(), NOW()),
(277, 28, 'BK-TKA-01-07', 'BK-TKA-01-', 7, 'tersedia', NOW(), NOW()),
(278, 28, 'BK-TKA-01-08', 'BK-TKA-01-', 8, 'tersedia', NOW(), NOW()),
(279, 28, 'BK-TKA-01-09', 'BK-TKA-01-', 9, 'tersedia', NOW(), NOW()),
(280, 28, 'BK-TKA-01-10', 'BK-TKA-01-', 10, 'tersedia', NOW(), NOW()),
(281, 29, 'BK-TKA-02-01', 'BK-TKA-02-', 1, 'tersedia', NOW(), NOW()),
(282, 29, 'BK-TKA-02-02', 'BK-TKA-02-', 2, 'tersedia', NOW(), NOW()),
(283, 29, 'BK-TKA-02-03', 'BK-TKA-02-', 3, 'tersedia', NOW(), NOW()),
(284, 29, 'BK-TKA-02-04', 'BK-TKA-02-', 4, 'tersedia', NOW(), NOW()),
(285, 29, 'BK-TKA-02-05', 'BK-TKA-02-', 5, 'tersedia', NOW(), NOW()),
(286, 29, 'BK-TKA-02-06', 'BK-TKA-02-', 6, 'tersedia', NOW(), NOW()),
(287, 29, 'BK-TKA-02-07', 'BK-TKA-02-', 7, 'tersedia', NOW(), NOW()),
(288, 29, 'BK-TKA-02-08', 'BK-TKA-02-', 8, 'tersedia', NOW(), NOW()),
(289, 29, 'BK-TKA-02-09', 'BK-TKA-02-', 9, 'tersedia', NOW(), NOW()),
(290, 29, 'BK-TKA-02-10', 'BK-TKA-02-', 10, 'tersedia', NOW(), NOW()),
(291, 30, 'BK-TKA-03-01', 'BK-TKA-03-', 1, 'tersedia', NOW(), NOW()),
(292, 30, 'BK-TKA-03-02', 'BK-TKA-03-', 2, 'tersedia', NOW(), NOW()),
(293, 30, 'BK-TKA-03-03', 'BK-TKA-03-', 3, 'tersedia', NOW(), NOW()),
(294, 30, 'BK-TKA-03-04', 'BK-TKA-03-', 4, 'tersedia', NOW(), NOW()),
(295, 30, 'BK-TKA-03-05', 'BK-TKA-03-', 5, 'tersedia', NOW(), NOW()),
(296, 30, 'BK-TKA-03-06', 'BK-TKA-03-', 6, 'tersedia', NOW(), NOW()),
(297, 30, 'BK-TKA-03-07', 'BK-TKA-03-', 7, 'tersedia', NOW(), NOW()),
(298, 30, 'BK-TKA-03-08', 'BK-TKA-03-', 8, 'tersedia', NOW(), NOW()),
(299, 30, 'BK-TKA-03-09', 'BK-TKA-03-', 9, 'tersedia', NOW(), NOW()),
(300, 30, 'BK-TKA-03-10', 'BK-TKA-03-', 10, 'tersedia', NOW(), NOW()),
(301, 31, 'BK-TKA-04-01', 'BK-TKA-04-', 1, 'tersedia', NOW(), NOW()),
(302, 31, 'BK-TKA-04-02', 'BK-TKA-04-', 2, 'tersedia', NOW(), NOW()),
(303, 31, 'BK-TKA-04-03', 'BK-TKA-04-', 3, 'tersedia', NOW(), NOW()),
(304, 31, 'BK-TKA-04-04', 'BK-TKA-04-', 4, 'tersedia', NOW(), NOW()),
(305, 31, 'BK-TKA-04-05', 'BK-TKA-04-', 5, 'tersedia', NOW(), NOW()),
(306, 31, 'BK-TKA-04-06', 'BK-TKA-04-', 6, 'tersedia', NOW(), NOW()),
(307, 31, 'BK-TKA-04-07', 'BK-TKA-04-', 7, 'tersedia', NOW(), NOW()),
(308, 31, 'BK-TKA-04-08', 'BK-TKA-04-', 8, 'tersedia', NOW(), NOW()),
(309, 31, 'BK-TKA-04-09', 'BK-TKA-04-', 9, 'tersedia', NOW(), NOW()),
(310, 31, 'BK-TKA-04-10', 'BK-TKA-04-', 10, 'tersedia', NOW(), NOW()),
(311, 32, 'BK-TKA-05-01', 'BK-TKA-05-', 1, 'tersedia', NOW(), NOW()),
(312, 32, 'BK-TKA-05-02', 'BK-TKA-05-', 2, 'tersedia', NOW(), NOW()),
(313, 32, 'BK-TKA-05-03', 'BK-TKA-05-', 3, 'tersedia', NOW(), NOW()),
(314, 32, 'BK-TKA-05-04', 'BK-TKA-05-', 4, 'tersedia', NOW(), NOW()),
(315, 32, 'BK-TKA-05-05', 'BK-TKA-05-', 5, 'tersedia', NOW(), NOW()),
(316, 32, 'BK-TKA-05-06', 'BK-TKA-05-', 6, 'tersedia', NOW(), NOW()),
(317, 32, 'BK-TKA-05-07', 'BK-TKA-05-', 7, 'tersedia', NOW(), NOW()),
(318, 32, 'BK-TKA-05-08', 'BK-TKA-05-', 8, 'tersedia', NOW(), NOW()),
(319, 32, 'BK-TKA-05-09', 'BK-TKA-05-', 9, 'tersedia', NOW(), NOW()),
(320, 32, 'BK-TKA-05-10', 'BK-TKA-05-', 10, 'tersedia', NOW(), NOW()),
(321, 33, 'BK-TKA-06-01', 'BK-TKA-06-', 1, 'tersedia', NOW(), NOW()),
(322, 33, 'BK-TKA-06-02', 'BK-TKA-06-', 2, 'tersedia', NOW(), NOW()),
(323, 33, 'BK-TKA-06-03', 'BK-TKA-06-', 3, 'tersedia', NOW(), NOW()),
(324, 33, 'BK-TKA-06-04', 'BK-TKA-06-', 4, 'tersedia', NOW(), NOW()),
(325, 33, 'BK-TKA-06-05', 'BK-TKA-06-', 5, 'tersedia', NOW(), NOW()),
(326, 33, 'BK-TKA-06-06', 'BK-TKA-06-', 6, 'tersedia', NOW(), NOW()),
(327, 33, 'BK-TKA-06-07', 'BK-TKA-06-', 7, 'tersedia', NOW(), NOW()),
(328, 33, 'BK-TKA-06-08', 'BK-TKA-06-', 8, 'tersedia', NOW(), NOW()),
(329, 33, 'BK-TKA-06-09', 'BK-TKA-06-', 9, 'tersedia', NOW(), NOW()),
(330, 33, 'BK-TKA-06-10', 'BK-TKA-06-', 10, 'tersedia', NOW(), NOW()),
(331, 34, 'BK-TKA-07-01', 'BK-TKA-07-', 1, 'tersedia', NOW(), NOW()),
(332, 34, 'BK-TKA-07-02', 'BK-TKA-07-', 2, 'tersedia', NOW(), NOW()),
(333, 34, 'BK-TKA-07-03', 'BK-TKA-07-', 3, 'tersedia', NOW(), NOW()),
(334, 34, 'BK-TKA-07-04', 'BK-TKA-07-', 4, 'tersedia', NOW(), NOW()),
(335, 34, 'BK-TKA-07-05', 'BK-TKA-07-', 5, 'tersedia', NOW(), NOW()),
(336, 34, 'BK-TKA-07-06', 'BK-TKA-07-', 6, 'tersedia', NOW(), NOW()),
(337, 34, 'BK-TKA-07-07', 'BK-TKA-07-', 7, 'tersedia', NOW(), NOW()),
(338, 34, 'BK-TKA-07-08', 'BK-TKA-07-', 8, 'tersedia', NOW(), NOW()),
(339, 34, 'BK-TKA-07-09', 'BK-TKA-07-', 9, 'tersedia', NOW(), NOW()),
(340, 34, 'BK-TKA-07-10', 'BK-TKA-07-', 10, 'tersedia', NOW(), NOW()),
(341, 35, 'BK-TKA-08-01', 'BK-TKA-08-', 1, 'dipinjam', NOW(), NOW()),
(342, 35, 'BK-TKA-08-02', 'BK-TKA-08-', 2, 'tersedia', NOW(), NOW()),
(343, 35, 'BK-TKA-08-03', 'BK-TKA-08-', 3, 'tersedia', NOW(), NOW()),
(344, 35, 'BK-TKA-08-04', 'BK-TKA-08-', 4, 'tersedia', NOW(), NOW()),
(345, 35, 'BK-TKA-08-05', 'BK-TKA-08-', 5, 'tersedia', NOW(), NOW()),
(346, 35, 'BK-TKA-08-06', 'BK-TKA-08-', 6, 'tersedia', NOW(), NOW()),
(347, 35, 'BK-TKA-08-07', 'BK-TKA-08-', 7, 'tersedia', NOW(), NOW()),
(348, 35, 'BK-TKA-08-08', 'BK-TKA-08-', 8, 'tersedia', NOW(), NOW()),
(349, 35, 'BK-TKA-08-09', 'BK-TKA-08-', 9, 'tersedia', NOW(), NOW()),
(350, 35, 'BK-TKA-08-10', 'BK-TKA-08-', 10, 'tersedia', NOW(), NOW());

ALTER TABLE `peminjamans`
  ADD COLUMN `eksemplar_id` bigint(20) UNSIGNED DEFAULT NULL AFTER `buku_id`,
  ADD CONSTRAINT `peminjamans_eksemplar_id_foreign` FOREIGN KEY (`eksemplar_id`) REFERENCES `eksemplars` (`id`) ON DELETE SET NULL;
UPDATE `peminjamans` SET `eksemplar_id` = 341 WHERE `id` = 1;

COMMIT;
SET FOREIGN_KEY_CHECKS = 1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
