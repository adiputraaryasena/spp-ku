-- ==========================================================================
--  Khusus bila database spp_ku SUDAH terlanjur diimpor sebelumnya dan Anda
--  tidak ingin menimpa datanya. Menambahkan kolom nomor telepon ke tabel users.
--  Jalankan sekali lewat phpMyAdmin: pilih database spp_ku -> tab SQL -> Go.
--  (Tidak perlu dijalankan bila Anda mengimpor database/spp_ku.sql yang baru.)
-- ==========================================================================
USE `spp_ku`;

ALTER TABLE `users` ADD COLUMN `telepon` varchar(20) DEFAULT NULL AFTER `email`;

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_10_21_000001_add_telepon_to_users_table', 1
WHERE NOT EXISTS (SELECT 1 FROM `migrations` WHERE `migration` = '2026_10_21_000001_add_telepon_to_users_table');
