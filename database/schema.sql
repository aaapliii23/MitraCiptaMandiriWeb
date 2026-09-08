-- Schema & Data Mitra Cipta Mandiri (MCM)
-- Terfokus pada Program: Tata Rias & Public Speaking
-- Generated: 2026-09-07 09:39:11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------

-- Struktur dari tabel `admins`
--

CREATE TABLE IF NOT EXISTS `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data untuk tabel `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `created_at`) VALUES
('1', 'admin', '$2y$12$rLK80Z6gYha13b0HLuUhYuLIOPC5pzCxRaS0tnU3rcCzT9vTrT/gK', '2026-08-14 11:19:29'),
('2', 'superadmin', '$2y$12$7LeSbK0vnibTerKEeGvCn.zjD53wMViSrg4BNk2YtknMrDbWag.F.', '2026-08-14 11:19:29'),
('3', 'dizasa', '$2y$10$50VO374X7cSPa1ccl.nzNuplER3YjdZm01ZWmvGbq4Rn2bhmosKpq', '2026-08-21 12:16:54');

-- --------------------------------------------------------

-- Struktur dari tabel `certificate_templates`
--

CREATE TABLE IF NOT EXISTS `certificate_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `class_id` int(11) DEFAULT NULL,
  `layout` varchar(50) NOT NULL DEFAULT 'default',
  `bg_image` varchar(255) DEFAULT NULL,
  `accent_color` varchar(20) DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `bg_image_public_id` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data untuk tabel `certificate_templates`
--

INSERT INTO `certificate_templates` (`id`, `name`, `class_id`, `layout`, `bg_image`, `accent_color`, `is_default`, `created_at`, `bg_image_public_id`) VALUES
('1', 'Sertifikat Standar', NULL, 'default', NULL, '#1e40af', '1', '2026-08-19 09:37:16', NULL),
('2', 'Sertifikat Elegant', NULL, 'elegant', NULL, '#7c3aed', '0', '2026-08-19 09:37:16', NULL),
('3', 'Sertifikat Modern', NULL, 'modern', NULL, '#0ea5e9', '0', '2026-08-19 09:37:16', NULL),
('4', 'Sertifikat Premium', NULL, 'premium', NULL, '#b45309', '0', '2026-08-19 09:37:16', NULL);

-- --------------------------------------------------------

-- Struktur dari tabel `certificates`
--

CREATE TABLE IF NOT EXISTS `certificates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `cert_number` varchar(50) NOT NULL,
  `verify_token` varchar(64) NOT NULL DEFAULT '',
  `issued_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `cert_number` (`cert_number`),
  UNIQUE KEY `uq_cert_user_class` (`user_id`,`class_id`),
  UNIQUE KEY `uq_cert_verify_token` (`verify_token`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data untuk tabel `certificates`
--

INSERT INTO `certificates` (`id`, `user_id`, `class_id`, `cert_number`, `verify_token`, `issued_at`) VALUES
('2', '5', '1', 'MCM-2026-0001', '63528e7c8b70e86f39d8174042b4cdc60ccd8a21', '2026-08-17 19:29:43'),
('3', '8', '1', 'MCM-2026-0002', '06f7e9fc5b28cd0629f90f0963920f5a4bfc07f1', '2026-08-19 12:10:37'),
('4', '9', '1', 'MCM-2026-0003', 'ac801f239268e6abcdee97dc4eede0710d984f83', '2026-08-24 09:26:07'),
('5', '9', '9', 'MCM-2026-0004', '1cfd6dec7ce282ff1fa89a615bbf8b00b5737b4b', '2026-08-24 11:41:11'),
('7', '10', '9', 'MCM-2026-0005', '7a0b92ab127a8ec5c8597ef605b702a7b855c15e', '2026-08-27 11:02:07'),
('8', '5', '9', 'MCM-2026-0006', '4318223f4da4ca4b478897aaa31b81c4036d5c9d', '2026-08-31 09:54:45');

-- --------------------------------------------------------

-- Struktur dari tabel `certifications`
--

CREATE TABLE IF NOT EXISTS `certifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `image_public_id` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data untuk tabel `certifications`
--

INSERT INTO `certifications` (`id`, `title`, `description`, `image`, `created_at`, `image_public_id`) VALUES
('1', 'Surat Keterangan Kemenkumham', 'Legalitas badan hukum Lembaga MCM terdaftar resmi di Kementerian Hukum dan HAM.', 'uploads/certs/sk_kemenkumham.jpg', '2026-08-14 14:16:05', NULL),
('2', 'Sertifikat Akreditasi Lembaga', 'Sertifikat akreditasi lembaga pelatihan dari lembaga pengakreditasian resmi.', 'uploads/certs/akreditasi.jpg', '2026-08-14 14:16:05', NULL),
('3', 'Piagam Penghargaan Pendidikan', 'Dokumen penghargaan atas kontribusi MCM dalam pendidikan dan pelatihan vokasi.', 'uploads/certs/piagam.jpg', '2026-08-14 14:16:05', NULL);

-- --------------------------------------------------------

-- Struktur dari tabel `chat_messages`
--

CREATE TABLE IF NOT EXISTS `chat_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `wa_number` varchar(20) NOT NULL,
  `direction` enum('in','out') NOT NULL DEFAULT 'in',
  `sender_type` enum('visitor','bot','admin') NOT NULL DEFAULT 'visitor',
  `message` text NOT NULL,
  `matched_intent` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_chat_wa_number` (`wa_number`),
  KEY `idx_chat_sender_type` (`sender_type`)
) ENGINE=InnoDB AUTO_INCREMENT=131 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data untuk tabel `chat_messages`
--

INSERT INTO `chat_messages` (`id`, `user_id`, `wa_number`, `direction`, `sender_type`, `message`, `matched_intent`, `created_at`) VALUES
('43', NULL, '6281215614286', 'out', 'visitor', '*PEMBAYARAN LUNAS - MCM*\n\nHalo Gilang Erlangga, pembayaran Anda untuk *Make Up Artist* sudah kami terima. ???\nNo. Order: ORD-6A82FC829F295-1786969218\nSilakan login ke LMS untuk mulai belajar: http://127.0.0.1:8000/lms/dashboard.php', 'pembayaran', '2026-08-17 19:20:20'),
('44', NULL, '6281215614286', 'out', 'visitor', 'hi', 'admin', '2026-08-17 19:25:44'),
('45', '5', 'web-86b23740a0a2', 'in', 'visitor', 'apa saja program pelatihan?', 'kursus', '2026-08-18 09:11:55'),
('46', NULL, 'web-86b23740a0a2', 'out', 'visitor', 'Program pelatihan MCM:\n- Make Up Artist\n- Tata Kecantikan\n- Pijat Terapis\n- Kursus Metodologi\n- Content Creator\n- Digital Marketing\n- Catering Pastry\n- Dasar-Dasar Pariwisata\n\nUntuk jadwal & harga, sebutkan program yang Anda minati.', 'kursus', '2026-08-18 09:11:55'),
('47', '5', 'web-86b23740a0a2', 'in', 'visitor', 'kapan jadwal pelatihan mulai?', 'jadwal', '2026-08-18 09:11:59'),
('48', NULL, 'web-86b23740a0a2', 'out', 'visitor', 'Jadwal pelatihan MCM tertera di masing-masing program di halaman Program. Untuk detail tanggal mulai, sebutkan program yang Anda minati.', 'jadwal', '2026-08-18 09:11:59'),
('49', NULL, '6281200000001', 'in', 'visitor', 'berapa harga kelas?', 'harga', '2026-08-18 09:30:37'),
('50', NULL, '6281200000001', 'out', 'visitor', 'Harga program MCM:\n- Make Up Artist - Rp 1.200.000\n- Tata Kecantikan - Rp 1.000.000\n- Pijat Terapis - Rp 1.100.000\n- Kursus Metodologi - Rp 950.000\n- Content Creator - Rp 1.300.000\n- Digital Marketing - Rp 1.400.000\n- Catering Pastry - Rp 1.150.000\n- Dasar-Dasar Pariwisata - Rp 900.000', 'harga', '2026-08-18 09:30:37'),
('51', NULL, 'web-d058d48f397b', 'in', 'visitor', 'berapa harga kelas?', 'harga', '2026-08-18 09:31:58'),
('52', NULL, 'web-d058d48f397b', 'out', 'visitor', 'Harga program MCM:\n- Make Up Artist - Rp 1.200.000\n- Tata Kecantikan - Rp 1.000.000\n- Pijat Terapis - Rp 1.100.000\n- Kursus Metodologi - Rp 950.000\n- Content Creator - Rp 1.300.000\n- Digital Marketing - Rp 1.400.000\n- Catering Pastry - Rp 1.150.000\n- Dasar-Dasar Pariwisata - Rp 900.000', 'harga', '2026-08-18 09:31:58'),
('53', NULL, 'web-3cceb29fcf5e', 'in', 'visitor', 'apa saja program pelatihan?', 'kursus', '2026-08-19 11:34:14'),
('54', NULL, 'web-3cceb29fcf5e', 'out', 'visitor', 'Program pelatihan MCM:\n- Make Up Artist\n- Tata Kecantikan\n- Pijat Terapis\n- Kursus Metodologi\n- Content Creator\n- Digital Marketing\n- Catering Pastry\n- Dasar-Dasar Pariwisata\n\nUntuk jadwal & harga, sebutkan program yang Anda minati.', 'kursus', '2026-08-19 11:34:14'),
('55', NULL, 'web-3cceb29fcf5e', 'in', 'visitor', 'fgdd', NULL, '2026-08-19 11:34:34'),
('56', NULL, 'web-3cceb29fcf5e', 'out', 'visitor', 'Terima kasih! Pertanyaan Anda akan kami teruskan ke admin untuk dijawab lebih lanjut.', NULL, '2026-08-19 11:34:34'),
('57', NULL, '6285881327923', 'out', 'visitor', '*PEMBAYARAN LUNAS - MCM*\n\nHalo Abdul Rapli, pembayaran Anda untuk *Make Up Artist* sudah kami terima. ???\nNo. Order: ORD-6A8533C466287-1787114436\nSilakan login ke LMS untuk mulai belajar: http://localhost/Mitra Citra Mandiri/lms/dashboard.php', 'pembayaran', '2026-08-19 11:40:41'),
('58', NULL, '6285881327923', 'out', 'visitor', '*PEMBAYARAN LUNAS - MCM*\n\nHalo Alvina Marva Dearsy, pembayaran Anda untuk *Make Up Artist* sudah kami terima. ???\nNo. Order: ORD-6A853900215EA-1787115776\nSilakan login ke LMS untuk mulai belajar: http://localhost/Mitra Citra Mandiri/lms/dashboard.php', 'pembayaran', '2026-08-19 12:03:02'),
('59', NULL, 'web-18fcbd89b82a', 'in', 'visitor', 'kapan jadwal pelatihan mulai?', 'jadwal', '2026-08-19 12:14:23'),
('60', NULL, 'web-18fcbd89b82a', 'out', 'visitor', 'Jadwal pelatihan MCM tertera di masing-masing program di halaman Program. Untuk detail tanggal mulai, sebutkan program yang Anda minati.', 'jadwal', '2026-08-19 12:14:23'),
('61', NULL, 'web-18fcbd89b82a', 'in', 'visitor', 'apakah hari ini ada kelas?', 'kursus', '2026-08-19 12:14:38'),
('62', NULL, 'web-18fcbd89b82a', 'out', 'visitor', 'Program pelatihan MCM:\n- Make Up Artist\n- Tata Kecantikan\n- Pijat Terapis\n- Kursus Metodologi\n- Content Creator\n- Digital Marketing\n- Catering Pastry\n- Dasar-Dasar Pariwisata\n\nUntuk jadwal & harga, sebutkan program yang Anda minati.', 'kursus', '2026-08-19 12:14:38'),
('63', NULL, 'web-18fcbd89b82a', 'in', 'visitor', 'dimana letak candi borobudur', 'kontak', '2026-08-19 12:15:04'),
('64', NULL, 'web-18fcbd89b82a', 'out', 'visitor', 'Untuk info lebih lanjut, silakan hubungi admin via WhatsApp ini. Lokasi & detail MCM tersedia di halaman Tentang website kami.', 'kontak', '2026-08-19 12:15:04'),
('65', NULL, 'web-18fcbd89b82a', 'in', 'visitor', 'dsohaobf', NULL, '2026-08-19 12:15:13'),
('66', NULL, 'web-18fcbd89b82a', 'out', 'visitor', 'Terima kasih! Pertanyaan Anda akan kami teruskan ke admin untuk dijawab lebih lanjut.', NULL, '2026-08-19 12:15:13'),
('67', NULL, '6285881327923', 'out', 'visitor', '*PEMBAYARAN LUNAS - MCM*\n\nHalo Alvina Marva Dearsy, pembayaran Anda untuk *Pijat Terapis* sudah kami terima. ???\nNo. Order: ORD-6A85419B78D68-1787117979\nSilakan login ke LMS untuk mulai belajar: http://localhost/Mitra Citra Mandiri/lms/dashboard.php', 'pembayaran', '2026-08-19 12:39:41'),
('68', NULL, 'web-b7a889ae2b96', 'in', 'visitor', 'berapa harga kelas?', 'harga', '2026-08-19 15:06:06'),
('69', NULL, 'web-b7a889ae2b96', 'out', 'visitor', 'Harga program MCM:\n- Make Up Artist - Rp 1.200.000\n- Tata Kecantikan - Rp 1.000.000\n- Pijat Terapis - Rp 1.100.000\n- Kursus Metodologi - Rp 950.000\n- Content Creator - Rp 1.300.000\n- Digital Marketing - Rp 1.400.000\n- Catering Pastry - Rp 1.150.000\n- Dasar-Dasar Pariwisata - Rp 900.000', 'harga', '2026-08-19 15:06:06'),
('70', NULL, 'web-b7a889ae2b96', 'in', 'visitor', 'berapa harga kelas?', 'harga', '2026-08-19 15:33:28'),
('71', NULL, 'web-b7a889ae2b96', 'out', 'visitor', 'Harga program MCM:\n- Make Up Artist - Rp 1.200.000\n- Tata Kecantikan - Rp 1.000.000\n- Pijat Terapis - Rp 1.100.000\n- Kursus Metodologi - Rp 950.000\n- Content Creator - Rp 1.300.000\n- Digital Marketing - Rp 1.400.000\n- Catering Pastry - Rp 1.150.000\n- Dasar-Dasar Pariwisata - Rp 900.000', 'harga', '2026-08-19 15:33:28'),
('72', NULL, 'web-b7a889ae2b96', 'in', 'visitor', 'kapan jadwal pelatihan mulai?', 'jadwal', '2026-08-19 15:33:30'),
('73', NULL, 'web-b7a889ae2b96', 'out', 'visitor', 'Jadwal pelatihan MCM tertera di masing-masing program di halaman Program. Untuk detail tanggal mulai, sebutkan program yang Anda minati.', 'jadwal', '2026-08-19 15:33:30'),
('74', NULL, 'web-b7a889ae2b96', 'in', 'visitor', 'apa saja program pelatihan?', 'kursus', '2026-08-19 15:33:31'),
('75', NULL, 'web-b7a889ae2b96', 'out', 'visitor', 'Program pelatihan MCM:\n- Make Up Artist\n- Tata Kecantikan\n- Pijat Terapis\n- Kursus Metodologi\n- Content Creator\n- Digital Marketing\n- Catering Pastry\n- Dasar-Dasar Pariwisata\n\nUntuk jadwal & harga, sebutkan program yang Anda minati.', 'kursus', '2026-08-19 15:33:31'),
('76', NULL, 'web-b7a889ae2b96', 'in', 'visitor', 'dimana alamat MCM?', 'kontak', '2026-08-19 15:33:33'),
('77', NULL, 'web-b7a889ae2b96', 'out', 'visitor', 'Untuk info lebih lanjut, silakan hubungi admin via WhatsApp ini. Lokasi & detail MCM tersedia di halaman Tentang website kami.', 'kontak', '2026-08-19 15:33:33'),
('78', NULL, '6289289829839', 'out', 'visitor', '*PEMBAYARAN LUNAS - MCM*\n\nHalo Diza Syaichul Adilla, pembayaran Anda untuk *Make Up Artist* sudah kami terima. ???\nNo. Order: ORD-6A87E9A836FF2-1787292072\nSilakan login ke LMS untuk mulai belajar: http://mitraciptamandiriweb.test/lms/dashboard.php', 'pembayaran', '2026-08-21 13:01:13'),
('79', NULL, '6289289829839', 'out', 'visitor', '*PEMBAYARAN LUNAS - MCM*\n\nHalo Diza Syaichul Adilla, pembayaran Anda untuk *Make Up Artist* (Offline) sudah kami terima. ???\nNo. Order: ORD-6A87ED7CF36F2-1787293052\n\nSilakan gabung ke grup WhatsApp kelas untuk info jadwal & lokasi pelatihan:\nhttps://chat.whatsapp.com/ExampleMakeUpOfflineGroup', 'pembayaran', '2026-08-21 13:17:34'),
('80', NULL, '6289289829839', 'out', 'visitor', '*PEMBAYARAN LUNAS - MCM*\n\nHalo Diza Syaichul Adilla, pembayaran Anda untuk *Make Up Artist* (Online) sudah kami terima. ???\nNo. Order: ORD-6A88119499807-1787302292\nSilakan login ke LMS untuk mulai belajar: http://mitraciptamandiriweb.test/lms/dashboard.php', 'pembayaran', '2026-08-21 15:51:34'),
('81', NULL, '6289289829839', 'out', 'visitor', '*PEMBAYARAN LUNAS - MCM*\n\nHalo Diza Syaichul Adilla, pembayaran Anda untuk *Make Up Artist* (Offline) sudah kami terima. ???\nNo. Order: ORD-6A8ACD91BB689-1787481489\n\nSilakan gabung ke grup WhatsApp kelas untuk info jadwal & lokasi pelatihan:\nhttps://chat.whatsapp.com/ExampleMakeUpOfflineGroup', 'pembayaran', '2026-08-23 17:38:11'),
('82', NULL, '6289289829839', 'out', 'visitor', '*PEMBAYARAN LUNAS - MCM*\n\nHalo Diza Syaichul Adilla, pembayaran Anda untuk *Make Up Artist* (Offline) sudah kami terima. ???\nNo. Order: ORD-6A8AD8C74A21C-1787484359\n\nSilakan gabung ke grup WhatsApp kelas untuk info jadwal & lokasi pelatihan:\nhttps://chat.whatsapp.com/ExampleMakeUpOfflineGroup', 'pembayaran', '2026-08-23 18:26:04'),
('83', NULL, '6289289829839', 'out', 'visitor', '*PEMBAYARAN LUNAS - MCM*\n\nHalo Diza Syaichul Adilla, pembayaran Anda untuk *Tata Kecantikan* (Online) sudah kami terima. ???\nNo. Order: ORD-6A8AD9ED74C2C-1787484653\nSilakan login ke LMS untuk mulai belajar: http://mitraciptamandiriweb.test/lms/dashboard.php', 'pembayaran', '2026-08-23 18:30:54'),
('84', NULL, '6289289829839', 'out', 'visitor', '*PEMBAYARAN LUNAS - MCM*\n\nHalo Diza Syaichul Adilla, pembayaran Anda untuk *Pijat Terapis* (Offline) sudah kami terima. ???\nNo. Order: ORD-6A8ADB4F9EF1B-1787485007\n\nSilakan gabung ke grup WhatsApp kelas untuk info jadwal & lokasi pelatihan:\nhttps://chat.whatsapp.com/ExamplePijatOfflineGroup', 'pembayaran', '2026-08-23 18:36:49'),
('85', NULL, '6289289829839', 'out', 'visitor', '*PEMBAYARAN LUNAS - MCM*\n\nHalo Diza Syaichul Adilla, pembayaran Anda untuk *Content Creator* (Online) sudah kami terima. ???\nNo. Order: ORD-6A8ADC1BBBBF2-1787485211\nSilakan login ke LMS untuk mulai belajar: http://mitraciptamandiriweb.test/lms/dashboard.php', 'pembayaran', '2026-08-23 18:40:13'),
('86', NULL, '6289289829839', 'out', 'visitor', '*PEMBAYARAN LUNAS - MCM*\n\nHalo Diza Syaichul Adilla, pembayaran Anda untuk *Content Creator* (Offline) sudah kami terima. ???\nNo. Order: ORD-6A8ADC41E13D2-1787485249\n\nAdmin kami akan segera menghubungi Anda untuk info grup WhatsApp kelas & jadwal pelatihan.', 'pembayaran', '2026-08-23 18:40:51'),
('87', NULL, '6285793935707', 'out', 'visitor', '[NOTIF OFFLINE] Link WA kosong ??? Kelas: Content Creator (ID 5), Order: ORD-6A8ADC41E13D2-1787485249, Peserta: Diza Syaichul Adilla (6289289829839) ??? segera hubungi peserta.', 'admin_notif', '2026-08-23 18:40:51'),
('88', NULL, '6289289829839', 'out', 'visitor', '*PEMBAYARAN LUNAS - MCM*\n\nHalo Diza Syaichul Adilla, pembayaran Anda untuk *Tata Kecantikan* (Offline) sudah kami terima. ???\nNo. Order: ORD-6A8ADED05565E-1787485904\n\nSilakan gabung ke grup WhatsApp kelas untuk info jadwal & lokasi pelatihan:\nhttps://chat.whatsapp.com/ExampleMakeUpOfflineGroup', 'pembayaran', '2026-08-23 18:51:45'),
('89', NULL, 'web-6bc42b824b08', 'in', 'visitor', 'kapan jadwal pelatihan mulai?', 'jadwal', '2026-08-23 19:00:47'),
('90', NULL, 'web-6bc42b824b08', 'out', 'visitor', 'Jadwal pelatihan MCM tertera di masing-masing program di halaman Program. Untuk detail tanggal mulai, sebutkan program yang Anda minati.', 'jadwal', '2026-08-23 19:00:47'),
('91', NULL, 'web-6bc42b824b08', 'in', 'visitor', 'berapa harga kelas?', 'harga', '2026-08-23 19:00:48'),
('92', NULL, 'web-6bc42b824b08', 'out', 'visitor', 'Harga program MCM:\n- Make Up Artist - Mulai Rp 960.000 (Offline Rp 1.200.000 / Online Rp 960.000)\n- Tata Kecantikan - Mulai Rp 800.000 (Offline Rp 1.000.000 / Online Rp 800.000)\n- Pijat Terapis - Mulai Rp 880.000 (Offline Rp 1.100.000 / Online Rp 880.000)\n- Kursus Metodologi - Mulai Rp 760.000 (Offline Rp 950.000 / Online Rp 760.000)\n- Content Creator - Mulai Rp 1.040.000 (Offline Rp 1.300.000 / Online Rp 1.040.000)\n- Digital Marketing - Mulai Rp 1.120.000 (Offline Rp 1.400.000 / Online Rp 1.120.000)\n- Dasar-Dasar Pariwisata - Mulai Rp 720.000 (Offline Rp 900.000 / Online Rp 720.000)\n- Public Speaking - Mulai Rp 700.000 (Offline Rp 1.000.000 / Online Rp 700.000)', 'harga', '2026-08-23 19:00:48'),
('93', NULL, 'web-6bc42b824b08', 'in', 'visitor', 'apa saja program pelatihan?', 'kursus', '2026-08-23 19:00:49'),
('94', NULL, 'web-6bc42b824b08', 'out', 'visitor', 'Program pelatihan MCM:\n- Make Up Artist\n- Tata Kecantikan\n- Pijat Terapis\n- Kursus Metodologi\n- Content Creator\n- Digital Marketing\n- Dasar-Dasar Pariwisata\n- Public Speaking\n\nUntuk jadwal & harga, sebutkan program yang Anda minati.', 'kursus', '2026-08-23 19:00:49'),
('95', NULL, 'web-6bc42b824b08', 'in', 'visitor', 'dimana alamat MCM?', 'kontak', '2026-08-23 19:00:51'),
('96', NULL, 'web-6bc42b824b08', 'out', 'visitor', 'Untuk info lebih lanjut, silakan hubungi admin via WhatsApp ini. Lokasi & detail MCM tersedia di halaman Tentang website kami.', 'kontak', '2026-08-23 19:00:51'),
('97', NULL, '6289289829839', 'out', 'visitor', '*PEMBAYARAN LUNAS - MCM*\n\nHalo Diza Syaichul Adilla, pembayaran Anda untuk *Pijat Terapis* (Offline) sudah kami terima. ???\nNo. Order: ORD-6A8AE19055866-1787486608\n\nSilakan gabung ke grup WhatsApp kelas untuk info jadwal & lokasi pelatihan:\nhttps://chat.whatsapp.com/ExamplePijatOfflineGroup', 'pembayaran', '2026-08-23 19:03:30'),
('98', NULL, '6289289829839', 'out', 'visitor', '*PEMBAYARAN LUNAS - MCM*\n\nHalo Diza Syaichul Adilla, pembayaran Anda untuk *Tata Kecantikan* (Offline) sudah kami terima. ???\nNo. Order: ORD-6A8AE23FA5EA8-1787486783\n\nSilakan gabung ke grup WhatsApp kelas untuk info jadwal & lokasi pelatihan:\nhttps://chat.whatsapp.com/ExampleMakeUpOfflineGroup', 'pembayaran', '2026-08-23 19:06:26'),
('99', NULL, '6289289829839', 'out', 'visitor', '*PEMBAYARAN LUNAS - MCM*\n\nHalo Diza Syaichul Adilla, pembayaran Anda untuk *Make Up Artist* (Online) sudah kami terima. ???\nNo. Order: ORD-6A8BA7913D523-1787537297\nSilakan login ke LMS untuk mulai belajar: http://mitraciptamandiriweb.test/lms/dashboard.php', 'pembayaran', '2026-08-24 09:08:19'),
('100', NULL, '6289289829839', 'out', 'visitor', '*PEMBAYARAN LUNAS - MCM*\n\nHalo Diza Syaichul Adilla, pembayaran Anda untuk *Make Up Artist* (Online) sudah kami terima. ???\nNo. Order: ORD-6A8BA7ECDE5F5-1787537388\nSilakan login ke LMS untuk mulai belajar: http://mitraciptamandiriweb.test/lms/dashboard.php', 'pembayaran', '2026-08-24 09:09:50'),
('101', NULL, '6289289829839', 'out', 'visitor', '*PEMBAYARAN LUNAS - MCM*\n\nHalo Diza Syaichul Adilla, pembayaran Anda untuk *Tata Kecantikan* (Online) sudah kami terima. ???\nNo. Order: ORD-6A8BABD9E44A2-1787538393\nSilakan login ke LMS untuk mulai belajar: http://mitraciptamandiriweb.test/lms/dashboard.php', 'pembayaran', '2026-08-24 09:26:34'),
('102', NULL, '6289289829839', 'out', 'visitor', '*PEMBAYARAN LUNAS - MCM*\n\nHalo Diza Syaichul Adilla, pembayaran Anda untuk *Public Speaking* (Online) sudah kami terima. ???\nNo. Order: ORD-6A8BC828401CB-1787545640\nSilakan login ke LMS untuk mulai belajar: http://mitraciptamandiriweb.test/lms/dashboard.php', 'pembayaran', '2026-08-24 11:27:22'),
('103', NULL, 'web-54614a2e13b1', 'in', 'visitor', 'berapa harga kelas?', 'harga', '2026-08-25 09:25:15'),
('104', NULL, 'web-54614a2e13b1', 'out', 'visitor', 'Harga program MCM:\n- Make Up Artist - Mulai Rp 960.000 (Offline Rp 1.200.000 / Online Rp 960.000)\n- Tata Kecantikan - Mulai Rp 800.000 (Offline Rp 1.000.000 / Online Rp 800.000)\n- Pijat Terapis - Mulai Rp 880.000 (Offline Rp 1.100.000 / Online Rp 880.000)\n- Kursus Metodologi - Mulai Rp 760.000 (Offline Rp 950.000 / Online Rp 760.000)\n- Content Creator - Mulai Rp 1.040.000 (Offline Rp 1.300.000 / Online Rp 1.040.000)\n- Digital Marketing - Mulai Rp 1.120.000 (Offline Rp 1.400.000 / Online Rp 1.120.000)\n- Dasar-Dasar Pariwisata - Mulai Rp 720.000 (Offline Rp 900.000 / Online Rp 720.000)\n- Public Speaking - Mulai Rp 700.000 (Offline Rp 1.000.000 / Online Rp 700.000)', 'harga', '2026-08-25 09:25:15'),
('105', NULL, 'web-54614a2e13b1', 'in', 'visitor', 'kapan jadwal pelatihan mulai?', 'jadwal', '2026-08-25 09:25:16'),
('106', NULL, 'web-54614a2e13b1', 'out', 'visitor', 'Jadwal pelatihan MCM tertera di masing-masing program di halaman Program. Untuk detail tanggal mulai, sebutkan program yang Anda minati.', 'jadwal', '2026-08-25 09:25:16'),
('107', NULL, 'web-54614a2e13b1', 'in', 'visitor', 'apa saja program pelatihan?', 'kursus', '2026-08-25 09:25:17'),
('108', NULL, 'web-54614a2e13b1', 'out', 'visitor', 'Program pelatihan MCM:\n- Make Up Artist\n- Tata Kecantikan\n- Pijat Terapis\n- Kursus Metodologi\n- Content Creator\n- Digital Marketing\n- Dasar-Dasar Pariwisata\n- Public Speaking\n\nUntuk jadwal & harga, sebutkan program yang Anda minati.', 'kursus', '2026-08-25 09:25:17'),
('109', NULL, 'web-54614a2e13b1', 'in', 'visitor', 'dimana alamat MCM?', 'kontak', '2026-08-25 09:25:18'),
('110', NULL, 'web-54614a2e13b1', 'out', 'visitor', 'Untuk info lebih lanjut, silakan hubungi admin via WhatsApp ini. Lokasi & detail MCM tersedia di halaman Tentang website kami.', 'kontak', '2026-08-25 09:25:18'),
('111', NULL, '6289289829839', 'out', 'visitor', '*PEMBAYARAN LUNAS - MCM*\n\nHalo Diza, pembayaran Anda untuk *Make Up Artist* (Offline) sudah kami terima. ???\nNo. Order: ORD-6A8CFD6A51A18-1787624810\n\nSilakan gabung ke grup WhatsApp kelas untuk info jadwal & lokasi pelatihan:\nhttps://chat.whatsapp.com/ExampleMakeUpOfflineGroup', 'pembayaran', '2026-08-25 09:26:53'),
('112', NULL, '6289289829839', 'out', 'visitor', '*PEMBAYARAN LUNAS - MCM*\n\nHalo Diza, pembayaran Anda untuk *Make Up Artist* (Online) sudah kami terima. ???\nNo. Order: ORD-6A8CFD97E51E4-1787624855\nSilakan login ke LMS untuk mulai belajar: http://mitraciptamandiriweb.test/lms/dashboard.php', 'pembayaran', '2026-08-25 09:27:38'),
('113', NULL, '6289289829839', 'out', 'visitor', '*PEMBAYARAN LUNAS - MCM*\n\nHalo Diza, pembayaran Anda untuk *Make Up Artist* (Offline) sudah kami terima. ???\nNo. Order: ORD-6A8D007BD85FB-1787625595\n\nSilakan gabung ke grup WhatsApp kelas untuk info jadwal & lokasi pelatihan:\nhttps://chat.whatsapp.com/ExampleMakeUpOfflineGroup', 'pembayaran', '2026-08-25 09:39:57'),
('114', NULL, '6289289829839', 'out', 'visitor', '*PEMBAYARAN LUNAS - MCM*\n\nHalo Diza, pembayaran Anda untuk *Public Speaking* (Online) sudah kami terima. ???\nNo. Order: ORD-6A8D023A9F9F8-1787626042\nSilakan login ke LMS untuk mulai belajar: http://mitraciptamandiriweb.test/lms/dashboard.php', 'pembayaran', '2026-08-25 09:47:23'),
('115', NULL, 'web-0fd32e7fb708', 'in', 'visitor', 'berapa harga kelas?', 'harga', '2026-08-27 09:58:56'),
('116', NULL, 'web-0fd32e7fb708', 'out', 'visitor', 'Harga program MCM:\n- Make Up Artist - Mulai Rp 960.000 (Offline Rp 1.200.000 / Online Rp 960.000)\n- Tata Kecantikan - Mulai Rp 800.000 (Offline Rp 1.000.000 / Online Rp 800.000)\n- Pijat Terapis - Mulai Rp 880.000 (Offline Rp 1.100.000 / Online Rp 880.000)\n- Kursus Metodologi - Mulai Rp 760.000 (Offline Rp 950.000 / Online Rp 760.000)\n- Content Creator - Mulai Rp 1.040.000 (Offline Rp 1.300.000 / Online Rp 1.040.000)\n- Digital Marketing - Mulai Rp 1.120.000 (Offline Rp 1.400.000 / Online Rp 1.120.000)\n- Dasar-Dasar Pariwisata - Mulai Rp 720.000 (Offline Rp 900.000 / Online Rp 720.000)\n- Public Speaking - Mulai Rp 700.000 (Offline Rp 1.000.000 / Online Rp 700.000)', 'harga', '2026-08-27 09:58:56'),
('117', NULL, 'web-0fd32e7fb708', 'in', 'visitor', 'kapan jadwal pelatihan mulai?', 'jadwal', '2026-08-27 09:58:57'),
('118', NULL, 'web-0fd32e7fb708', 'out', 'visitor', 'Jadwal pelatihan MCM tertera di masing-masing program di halaman Program. Untuk detail tanggal mulai, sebutkan program yang Anda minati.', 'jadwal', '2026-08-27 09:58:57'),
('119', NULL, 'web-0fd32e7fb708', 'in', 'visitor', 'apa saja program pelatihan?', 'kursus', '2026-08-27 09:58:58'),
('120', NULL, 'web-0fd32e7fb708', 'out', 'visitor', 'Program pelatihan MCM:\n- Make Up Artist\n- Tata Kecantikan\n- Pijat Terapis\n- Kursus Metodologi\n- Content Creator\n- Digital Marketing\n- Dasar-Dasar Pariwisata\n- Public Speaking\n\nUntuk jadwal & harga, sebutkan program yang Anda minati.', 'kursus', '2026-08-27 09:58:58'),
('121', NULL, 'web-0fd32e7fb708', 'in', 'visitor', 'dimana alamat MCM?', 'kontak', '2026-08-27 09:58:59'),
('122', NULL, 'web-0fd32e7fb708', 'out', 'visitor', 'Untuk info lebih lanjut, silakan hubungi admin via WhatsApp ini. Lokasi & detail MCM tersedia di halaman Tentang website kami.', 'kontak', '2026-08-27 09:58:59'),
('123', NULL, 'web-0b9e459fc658', 'in', 'visitor', 'kapan jadwal pelatihan mulai?', 'jadwal', '2026-08-27 14:00:44'),
('124', NULL, 'web-0b9e459fc658', 'out', 'visitor', 'Jadwal pelatihan MCM tertera di masing-masing program di halaman Program. Untuk detail tanggal mulai, sebutkan program yang Anda minati.', 'jadwal', '2026-08-27 14:00:44'),
('125', NULL, '6285881327923', 'out', 'visitor', 'hai selamat sore', 'admin', '2026-08-27 14:02:36'),
('126', NULL, '6285881327923', 'out', 'visitor', 'hai selamat sore', 'admin', '2026-08-27 14:10:22'),
('127', NULL, '6285881327923', 'out', 'visitor', 'hai selamat sore', 'admin', '2026-08-27 14:10:32'),
('128', NULL, '6285793935707', 'out', 'bot', '*PESANAN BARU - MCM*\n\nOrder: ORD-6A9906EEEEA18-1788413678\nPeserta: Abdul Rapli (6285881327923)\nKelas: Tata Kecantikan (Offline)\nNominal: Rp 1.000.000\nStatus: Menunggu pembayaran', 'admin_notif', '2026-09-03 12:34:39'),
('129', NULL, '6285793935707', 'out', 'bot', '*PESANAN BARU - MCM*\n\nOrder: ORD-6A990972D75E7-1788414322\nPeserta: Abdul Rapli (6285881327923)\nKelas: Tata Kecantikan (Offline)\nNominal: Rp 1.000.000\nStatus: Menunggu pembayaran', 'admin_notif', '2026-09-03 12:45:22'),
('130', NULL, '6285793935707', 'out', 'bot', '*PESANAN BARU - MCM*\n\nOrder: ORD-6A990B9531226-1788414869\nPeserta: Abdul Rapli (6285881327923)\nKelas: Tata Kecantikan (Offline)\nNominal: Rp 1.000.000\nStatus: Menunggu pembayaran', 'admin_notif', '2026-09-03 12:54:29');

-- --------------------------------------------------------

-- Struktur dari tabel `chatbot_intents`
--

CREATE TABLE IF NOT EXISTS `chatbot_intents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `intent` varchar(50) NOT NULL,
  `keywords` text NOT NULL,
  `reply` text NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `intent` (`intent`)
) ENGINE=InnoDB AUTO_INCREMENT=97 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data untuk tabel `chatbot_intents`
--

INSERT INTO `chatbot_intents` (`id`, `intent`, `keywords`, `reply`, `enabled`, `created_at`) VALUES
('1', 'kursus', 'kursus, program, kelas, pelatihan, materi, modul, belajar', 'Program pelatihan unggulan MCM:\n- {classes}\n\nUntuk jadwal & informasi pendaftaran kelas Tata Rias maupun Public Speaking, silakan tanyakan langsung ke admin.', '1', '2026-08-17 19:16:17'),
('2', 'jadwal', 'jadwal, mulai, kapan, tanggal, schedule', 'Jadwal pelatihan MCM tertera di masing-masing program di halaman Program. Untuk detail tanggal mulai, sebutkan program yang Anda minati.', '1', '2026-08-17 19:16:17'),
('3', 'harga', 'harga, biaya, bayar, investasi, berapa, price, tarif', 'Harga program MCM:\n- {prices}', '1', '2026-08-17 19:16:17'),
('4', 'pendaftaran', 'daftar, daftarkan, register, ikut, enroll', 'Cara daftar: pilih program di halaman Program, klik \'Daftar & Bayar\', isi data diri, lalu selesaikan pembayaran. Setelah lunas, akses LMS langsung terbuka.', '1', '2026-08-17 19:16:17'),
('5', 'kontak', 'alamat, lokasi, dimana, telepon, hubungi, kontak', 'Untuk info lebih lanjut, silakan hubungi admin via WhatsApp ini. Lokasi & detail MCM tersedia di halaman Tentang website kami.', '1', '2026-08-17 19:16:17'),
('6', 'penguji', 'penguji, asesor, guru, instruktur', 'Penguji MCM adalah asesor berpengalaman. Lihat latar belakang penguji di halaman Profil Penguji website kami.', '1', '2026-08-17 19:16:17');

-- --------------------------------------------------------

-- Struktur dari tabel `class_categories`
--

CREATE TABLE IF NOT EXISTS `class_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data untuk tabel `class_categories`
--

INSERT INTO `class_categories` (`id`, `name`, `slug`, `created_at`) VALUES
('1', 'Tata Rias', 'tata_rias', '2026-09-07 14:38:52'),
('2', 'Public Speaking', 'public_speaking', '2026-09-07 14:38:52');

-- --------------------------------------------------------

-- Struktur dari tabel `classes`
--

CREATE TABLE IF NOT EXISTS `classes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `start_date` date DEFAULT NULL,
  `category` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `description_online` text DEFAULT NULL,
  `description_offline` text DEFAULT NULL,
  `image` varchar(255) NOT NULL,
  `features` text NOT NULL,
  `price` int(11) NOT NULL DEFAULT 500000,
  `price_online` int(11) NOT NULL DEFAULT 0,
  `price_offline` int(11) NOT NULL DEFAULT 0,
  `mode_available` enum('online','offline','both') NOT NULL DEFAULT 'both',
  `whatsapp_group_link` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `image_public_id` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data untuk tabel `classes`
--

INSERT INTO `classes` (`id`, `name`, `start_date`, `category`, `description`, `description_online`, `description_offline`, `image`, `features`, `price`, `price_online`, `price_offline`, `mode_available`, `whatsapp_group_link`, `created_at`, `image_public_id`) VALUES
('1', 'Make Up Artist', '2026-09-05', 'Tata Rias', 'Kuasai teknik make-up dari dasar hingga profesional. Program ini dirancang untuk mencetak Make-Up Artist (MUA) handal yang siap menangani klien rias panggung, wedding, hingga editorial.', 'Kuasai teknik make-up dari dasar hingga profesional. Program ini dirancang untuk mencetak Make-Up Artist (MUA) handal yang siap menangani klien rias panggung, wedding, hingga editorial.', 'Kuasai teknik make-up dari dasar hingga profesional. Program ini dirancang untuk mencetak Make-Up Artist (MUA) handal yang siap menangani klien rias panggung, wedding, hingga editorial.', 'uploads/classes/makeup.jpg', '[\"Pengenalan Alat & Produk Make-up\",\"Make-up Natural & Flawless\",\"Make-up Wedding & Formal\",\"Koreksi Bentuk Wajah\",\"Praktek Langsung dengan Model\",\"Sertifikat & Penyaluran Klien\"]', '1200000', '960000', '1200000', 'both', 'https://chat.whatsapp.com/ExampleMakeUpOfflineGroup', '2026-08-14 13:17:26', NULL),
('2', 'Tata Kecantikan', '2026-09-12', 'Tata Rias', 'Pelajari perawatan kecantikan kulit, rambut, dan kuku mulai dari dasar hingga tingkat profesional. Cocok untuk Anda yang ingin berkarir di industri salon dan spa.', 'Pelajari perawatan kecantikan kulit, rambut, dan kuku mulai dari dasar hingga tingkat profesional. Cocok untuk Anda yang ingin berkarir di industri salon dan spa.', 'Pelajari perawatan kecantikan kulit, rambut, dan kuku mulai dari dasar hingga tingkat profesional. Cocok untuk Anda yang ingin berkarir di industri salon dan spa.', 'uploads/classes/kecantikan.jpg', '[\"Anatomi Kulit & Perawatan Dasar\",\"Perawatan Wajah (Facial)\",\"Hair Styling & Perawatan Rambut\",\"Manicure & Pedicure\",\"Manajemen Bisnis Salon\",\"Sertifikat Pelatihan\"]', '1000000', '800000', '1000000', 'both', 'https://chat.whatsapp.com/ExampleMakeUpOfflineGroup', '2026-08-14 13:17:26', NULL),
('9', 'Public Speaking', '2026-08-24', 'Public Speaking', 'Public Speaking adalah program pelatihan yang dirancang untuk membekali peserta dengan kemampuan berkomunikasi secara efektif dan percaya diri di depan umum. Anda akan mempelajari teknik olah vokal dan intonasi, bahasa tubuh yang meyakinkan, cara mengatasi rasa gugup (stage fright), menyusun struktur presentasi yang menarik, hingga teknik improvisasi saat menghadapi pertanyaan mendadak. Program ini cocok untuk pelajar, mahasiswa, karyawan, hingga calon profesional yang ingin tampil lebih percaya diri saat presentasi, MC acara, wawancara kerja, maupun berbicara di forum publik.', 'Public Speaking adalah program pelatihan yang dirancang untuk membekali peserta dengan kemampuan berkomunikasi secara efektif dan percaya diri di depan umum. Anda akan mempelajari teknik olah vokal dan intonasi, bahasa tubuh yang meyakinkan, cara mengatasi rasa gugup (stage fright), menyusun struktur presentasi yang menarik, hingga teknik improvisasi saat menghadapi pertanyaan mendadak. Program ini cocok untuk pelajar, mahasiswa, karyawan, hingga calon profesional yang ingin tampil lebih percaya diri saat presentasi, MC acara, wawancara kerja, maupun berbicara di forum publik.', 'Public Speaking adalah program pelatihan yang dirancang untuk membekali peserta dengan kemampuan berkomunikasi secara efektif dan percaya diri di depan umum. Anda akan mempelajari teknik olah vokal dan intonasi, bahasa tubuh yang meyakinkan, cara mengatasi rasa gugup (stage fright), menyusun struktur presentasi yang menarik, hingga teknik improvisasi saat menghadapi pertanyaan mendadak. Program ini cocok untuk pelajar, mahasiswa, karyawan, hingga calon profesional yang ingin tampil lebih percaya diri saat presentasi, MC acara, wawancara kerja, maupun berbicara di forum publik.', 'uploads/classes/6a8bb5accc138.jpg', '[\"Teknik Vokal & Bahasa Tubuh\",\"Mengatasi Demam Panggung\",\"Menyusun Presentasi yang Memukau\",\"Simulasi & Praktik Presentasi\"]', '1000000', '700000', '1000000', 'both', 'https://chat.whatsapp.com/ExampleMakeUpOfflineGroup', '2026-08-24 10:08:28', NULL),
('10', 'Tata Rias Pengantin', '2026-10-15', 'Tata Rias', 'Kuasai teknik tata rias pengantin dari dasar hingga profesional, mencakup konsep rias modern, tradisional, dan internasional. Program ini dirancang untuk mencetak MUA pengantin handal yang siap menerima klien dan membangun usaha rias sendiri.', 'Kuasai teknik tata rias pengantin dari dasar hingga profesional, mencakup konsep rias modern, tradisional, dan internasional. Program ini dirancang untuk mencetak MUA pengantin handal yang siap menerima klien dan membangun usaha rias sendiri.', 'Kuasai teknik tata rias pengantin dari dasar hingga profesional, mencakup konsep rias modern, tradisional, dan internasional. Program ini dirancang untuk mencetak MUA pengantin handal yang siap menerima klien dan membangun usaha rias sendiri.', 'https://res.cloudinary.com/fso6loxl/image/upload/v1788623410/mcm/classes/frocajmwqzibrozuwpak.jpg', '[\"Analisis Bentuk Wajah\",\"Teknik Foundation & Contouring\",\"Rias Pengantin Tradisional\",\"Rias Pengantin Modern/Internasional\",\"Penataan Sanggul Dasar\",\"Manajemen Klien & Portofolio\"]', '1500000', '1200000', '1500000', 'both', 'https://chat.whatsapp.com/ExampleGroupLinkTataRias2026', '2026-09-07 14:38:52', 'mcm/classes/frocajmwqzibrozuwpak');

-- --------------------------------------------------------

-- Struktur dari tabel `enrollments`
--

CREATE TABLE IF NOT EXISTS `enrollments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `class_mode` enum('online','offline') NOT NULL DEFAULT 'offline',
  `order_id` int(11) NOT NULL,
  `enrolled_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_id` (`order_id`),
  UNIQUE KEY `uq_enroll_user_class_mode` (`user_id`,`class_id`,`class_mode`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data untuk tabel `enrollments`
--

INSERT INTO `enrollments` (`id`, `user_id`, `class_id`, `class_mode`, `order_id`, `enrolled_at`) VALUES
('4', '5', '1', 'offline', '8', '2026-08-17 19:20:20'),
('10', '7', '1', 'offline', '22', '2026-08-19 11:40:41'),
('11', '8', '1', 'offline', '23', '2026-08-19 12:03:02'),
('13', '9', '1', 'online', '40', '2026-08-21 13:01:13'),
('18', '9', '2', 'online', '41', '2026-08-23 18:30:54'),
('31', '9', '9', 'online', '42', '2026-08-24 11:27:22'),
('32', '10', '1', 'online', '44', '2026-08-25 09:26:52'),
('36', '10', '1', 'offline', '45', '2026-08-25 09:39:57'),
('37', '10', '9', 'online', '46', '2026-08-25 09:47:23');

-- --------------------------------------------------------

-- Struktur dari tabel `facility_categories`
--

CREATE TABLE IF NOT EXISTS `facility_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `slug` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `icon` varchar(50) NOT NULL DEFAULT 'fa-building',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data untuk tabel `facility_categories`
--

INSERT INTO `facility_categories` (`id`, `slug`, `name`, `icon`, `created_at`) VALUES
('1', 'alat_pemadam', 'Alat Pemadam', 'fa-fire-extinguisher', '2026-08-21 12:18:58'),
('2', 'balkon', 'Balkon', 'fa-door-open', '2026-08-21 12:18:58'),
('3', 'kantor', 'Kantor', 'fa-building', '2026-08-21 12:18:58'),
('4', 'kelas', 'Kelas', 'fa-chalkboard-teacher', '2026-08-21 12:18:58'),
('5', 'lobby', 'Lobby', 'fa-couch', '2026-08-21 12:18:58'),
('6', 'mushola', 'Mushola', 'fa-mosque', '2026-08-21 12:18:58'),
('7', 'parkiran', 'Parkiran', 'fa-parking', '2026-08-21 12:18:58'),
('8', 'toilet', 'Toilet', 'fa-restroom', '2026-08-21 12:18:58');

-- --------------------------------------------------------

-- Struktur dari tabel `facility_locations`
--

CREATE TABLE IF NOT EXISTS `facility_locations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `image_public_id` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data untuk tabel `facility_locations`
--

INSERT INTO `facility_locations` (`id`, `category`, `title`, `image`, `description`, `created_at`, `image_public_id`) VALUES
('1', 'alat_pemadam', 'Foto Alat Pemadam MCM #1', 'assets/img/fasilitas/alat_pemadam/IMG_4145.jpg', 'Dokumentasi area alat pemadam LPK Mitra Cipta Mandiri', '2026-08-21 12:18:58', NULL),
('2', 'alat_pemadam', 'Foto Alat Pemadam MCM #2', 'assets/img/fasilitas/alat_pemadam/IMG_4146.jpg', 'Dokumentasi area alat pemadam LPK Mitra Cipta Mandiri', '2026-08-21 12:18:58', NULL),
('3', 'alat_pemadam', 'Foto Alat Pemadam MCM #3', 'assets/img/fasilitas/alat_pemadam/IMG_4147.jpg', 'Dokumentasi area alat pemadam LPK Mitra Cipta Mandiri', '2026-08-21 12:18:58', NULL),
('4', 'alat_pemadam', 'Foto Alat Pemadam MCM #4', 'assets/img/fasilitas/alat_pemadam/IMG_4148.jpg', 'Dokumentasi area alat pemadam LPK Mitra Cipta Mandiri', '2026-08-21 12:18:58', NULL),
('5', 'alat_pemadam', 'Foto Alat Pemadam MCM #5', 'assets/img/fasilitas/alat_pemadam/IMG_4158.jpg', 'Dokumentasi area alat pemadam LPK Mitra Cipta Mandiri', '2026-08-21 12:18:58', NULL),
('6', 'balkon', 'Foto Balkon MCM #1', 'assets/img/fasilitas/balkon/IMG_4134.jpg', 'Dokumentasi area balkon LPK Mitra Cipta Mandiri', '2026-08-21 12:18:58', NULL),
('7', 'balkon', 'Foto Balkon MCM #2', 'assets/img/fasilitas/balkon/IMG_4135.jpg', 'Dokumentasi area balkon LPK Mitra Cipta Mandiri', '2026-08-21 12:18:58', NULL),
('8', 'balkon', 'Foto Balkon MCM #3', 'assets/img/fasilitas/balkon/IMG_4136.jpg', 'Dokumentasi area balkon LPK Mitra Cipta Mandiri', '2026-08-21 12:18:58', NULL),
('9', 'balkon', 'Foto Balkon MCM #4', 'assets/img/fasilitas/balkon/IMG_4138.jpg', 'Dokumentasi area balkon LPK Mitra Cipta Mandiri', '2026-08-21 12:18:58', NULL),
('10', 'balkon', 'Foto Balkon MCM #5', 'assets/img/fasilitas/balkon/IMG_4139.jpg', 'Dokumentasi area balkon LPK Mitra Cipta Mandiri', '2026-08-21 12:18:58', NULL),
('11', 'balkon', 'Foto Balkon MCM #6', 'assets/img/fasilitas/balkon/IMG_4140.jpg', 'Dokumentasi area balkon LPK Mitra Cipta Mandiri', '2026-08-21 12:18:58', NULL),
('12', 'kantor', 'Foto Kantor MCM #1', 'assets/img/fasilitas/kantor/IMG_4149.jpg', 'Dokumentasi area kantor LPK Mitra Cipta Mandiri', '2026-08-21 12:18:58', NULL),
('13', 'kantor', 'Foto Kantor MCM #2', 'assets/img/fasilitas/kantor/IMG_4150.jpg', 'Dokumentasi area kantor LPK Mitra Cipta Mandiri', '2026-08-21 12:18:58', NULL),
('14', 'kantor', 'Foto Kantor MCM #3', 'assets/img/fasilitas/kantor/IMG_4151.jpg', 'Dokumentasi area kantor LPK Mitra Cipta Mandiri', '2026-08-21 12:18:58', NULL),
('15', 'kantor', 'Foto Kantor MCM #4', 'assets/img/fasilitas/kantor/IMG_4152.jpg', 'Dokumentasi area kantor LPK Mitra Cipta Mandiri', '2026-08-21 12:18:58', NULL),
('16', 'kelas', 'Foto Kelas MCM #1', 'assets/img/fasilitas/kelas/IMG_4141.jpg', 'Dokumentasi area kelas LPK Mitra Cipta Mandiri', '2026-08-21 12:18:58', NULL),
('17', 'kelas', 'Foto Kelas MCM #2', 'assets/img/fasilitas/kelas/IMG_4142.jpg', 'Dokumentasi area kelas LPK Mitra Cipta Mandiri', '2026-08-21 12:18:58', NULL),
('18', 'kelas', 'Foto Kelas MCM #3', 'assets/img/fasilitas/kelas/IMG_4143.jpg', 'Dokumentasi area kelas LPK Mitra Cipta Mandiri', '2026-08-21 12:18:58', NULL),
('19', 'kelas', 'Foto Kelas MCM #4', 'assets/img/fasilitas/kelas/IMG_4144.jpg', 'Dokumentasi area kelas LPK Mitra Cipta Mandiri', '2026-08-21 12:18:58', NULL),
('20', 'lobby', 'Foto Lobby MCM #1', 'assets/img/fasilitas/lobby/IMG_4163.jpg', 'Dokumentasi area lobby LPK Mitra Cipta Mandiri', '2026-08-21 12:18:58', NULL),
('21', 'lobby', 'Foto Lobby MCM #2', 'assets/img/fasilitas/lobby/IMG_4164.jpg', 'Dokumentasi area lobby LPK Mitra Cipta Mandiri', '2026-08-21 12:18:58', NULL),
('22', 'lobby', 'Foto Lobby MCM #3', 'assets/img/fasilitas/lobby/IMG_4165.jpg', 'Dokumentasi area lobby LPK Mitra Cipta Mandiri', '2026-08-21 12:18:58', NULL),
('23', 'lobby', 'Foto Lobby MCM #4', 'assets/img/fasilitas/lobby/IMG_4166.jpg', 'Dokumentasi area lobby LPK Mitra Cipta Mandiri', '2026-08-21 12:18:58', NULL),
('24', 'lobby', 'Foto Lobby MCM #5', 'assets/img/fasilitas/lobby/IMG_4167.jpg', 'Dokumentasi area lobby LPK Mitra Cipta Mandiri', '2026-08-21 12:18:58', NULL),
('25', 'lobby', 'Foto Lobby MCM #6', 'assets/img/fasilitas/lobby/IMG_4168.jpg', 'Dokumentasi area lobby LPK Mitra Cipta Mandiri', '2026-08-21 12:18:58', NULL),
('26', 'lobby', 'Foto Lobby MCM #7', 'assets/img/fasilitas/lobby/IMG_4169.jpg', 'Dokumentasi area lobby LPK Mitra Cipta Mandiri', '2026-08-21 12:18:58', NULL),
('27', 'lobby', 'Foto Lobby MCM #8', 'assets/img/fasilitas/lobby/IMG_4170.jpg', 'Dokumentasi area lobby LPK Mitra Cipta Mandiri', '2026-08-21 12:18:58', NULL),
('28', 'mushola', 'Foto Mushola MCM #1', 'assets/img/fasilitas/mushola/IMG_4153.jpg', 'Dokumentasi area mushola LPK Mitra Cipta Mandiri', '2026-08-21 12:18:58', NULL),
('29', 'mushola', 'Foto Mushola MCM #2', 'assets/img/fasilitas/mushola/IMG_4154.jpg', 'Dokumentasi area mushola LPK Mitra Cipta Mandiri', '2026-08-21 12:18:58', NULL),
('30', 'mushola', 'Foto Mushola MCM #3', 'assets/img/fasilitas/mushola/IMG_4155.jpg', 'Dokumentasi area mushola LPK Mitra Cipta Mandiri', '2026-08-21 12:18:58', NULL),
('31', 'mushola', 'Foto Mushola MCM #4', 'assets/img/fasilitas/mushola/IMG_4156.jpg', 'Dokumentasi area mushola LPK Mitra Cipta Mandiri', '2026-08-21 12:18:58', NULL),
('32', 'mushola', 'Foto Mushola MCM #5', 'assets/img/fasilitas/mushola/IMG_4157.jpg', 'Dokumentasi area mushola LPK Mitra Cipta Mandiri', '2026-08-21 12:18:58', NULL),
('33', 'parkiran', 'Foto Parkiran MCM #1', 'assets/img/fasilitas/parkiran/IMG_4171.jpg', 'Dokumentasi area parkiran LPK Mitra Cipta Mandiri', '2026-08-21 12:18:58', NULL),
('34', 'parkiran', 'Foto Parkiran MCM #2', 'assets/img/fasilitas/parkiran/IMG_4172.jpg', 'Dokumentasi area parkiran LPK Mitra Cipta Mandiri', '2026-08-21 12:18:58', NULL),
('35', 'parkiran', 'Foto Parkiran MCM #3', 'assets/img/fasilitas/parkiran/IMG_4173.jpg', 'Dokumentasi area parkiran LPK Mitra Cipta Mandiri', '2026-08-21 12:18:58', NULL),
('36', 'parkiran', 'Foto Parkiran MCM #4', 'assets/img/fasilitas/parkiran/IMG_4174.jpg', 'Dokumentasi area parkiran LPK Mitra Cipta Mandiri', '2026-08-21 12:18:58', NULL),
('37', 'parkiran', 'Foto Parkiran MCM #5', 'assets/img/fasilitas/parkiran/IMG_4175.jpg', 'Dokumentasi area parkiran LPK Mitra Cipta Mandiri', '2026-08-21 12:18:58', NULL),
('38', 'parkiran', 'Foto Parkiran MCM #6', 'assets/img/fasilitas/parkiran/IMG_4176.jpg', 'Dokumentasi area parkiran LPK Mitra Cipta Mandiri', '2026-08-21 12:18:58', NULL),
('39', 'toilet', 'Foto Toilet MCM #1', 'assets/img/fasilitas/toilet/IMG_4159.jpg', 'Dokumentasi area toilet LPK Mitra Cipta Mandiri', '2026-08-21 12:18:58', NULL),
('40', 'toilet', 'Foto Toilet MCM #2', 'assets/img/fasilitas/toilet/IMG_4160.jpg', 'Dokumentasi area toilet LPK Mitra Cipta Mandiri', '2026-08-21 12:18:58', NULL),
('41', 'toilet', 'Foto Toilet MCM #3', 'assets/img/fasilitas/toilet/IMG_4161.jpg', 'Dokumentasi area toilet LPK Mitra Cipta Mandiri', '2026-08-21 12:18:58', NULL),
('42', 'toilet', 'Foto Toilet MCM #4', 'assets/img/fasilitas/toilet/IMG_4162.jpg', 'Dokumentasi area toilet LPK Mitra Cipta Mandiri', '2026-08-21 12:18:58', NULL);

-- --------------------------------------------------------

-- Struktur dari tabel `finance_transactions`
--

CREATE TABLE IF NOT EXISTS `finance_transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` enum('in','out') NOT NULL,
  `category` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `amount` int(11) NOT NULL,
  `transaction_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `item_name` varchar(255) DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `unit_price` int(11) NOT NULL DEFAULT 0,
  `receipt_image` varchar(255) DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  `receipt_public_id` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data untuk tabel `finance_transactions`
--

INSERT INTO `finance_transactions` (`id`, `type`, `category`, `description`, `amount`, `transaction_date`, `created_at`, `item_name`, `quantity`, `unit_price`, `receipt_image`, `order_id`, `receipt_public_id`) VALUES
('1', 'in', 'pemasukan_kursus', 'Pemasukan pembayaran kursus no. order ORD-6A82EEF5D9FDB-1786965749', '1200000', '2026-08-17', '2026-08-24 08:52:02', 'Pendaftaran Make Up Artist (Testi Payment E2E)', '1', '1200000', NULL, '2', NULL),
('2', 'in', 'pemasukan_kursus', 'Pemasukan pembayaran kursus no. order ORD-6A82FC829F295-1786969218', '1200000', '2026-08-17', '2026-08-24 08:52:02', 'Pendaftaran Make Up Artist (Gilang Erlangga)', '1', '1200000', NULL, '8', NULL),
('7', 'in', 'pemasukan_kursus', 'Pemasukan pembayaran kursus no. order ORD-6A8533C466287-1787114436', '1200000', '2026-08-19', '2026-08-24 08:52:02', 'Pendaftaran Make Up Artist (Abdul Rapli)', '1', '1200000', NULL, '22', NULL),
('8', 'in', 'pemasukan_kursus', 'Pemasukan pembayaran kursus no. order ORD-6A853900215EA-1787115776', '1200000', '2026-08-19', '2026-08-24 08:52:02', 'Pendaftaran Make Up Artist (Alvina Marva Dearsy)', '1', '1200000', NULL, '23', NULL),
('10', 'in', 'pemasukan_kursus', 'Pemasukan pembayaran kursus no. order ORD-6A87E9A836FF2-1787292072', '1200000', '2026-08-21', '2026-08-24 08:52:02', 'Pendaftaran Make Up Artist (Diza Syaichul Adilla)', '1', '1200000', NULL, '26', NULL),
('11', 'in', 'pemasukan_kursus', 'Pemasukan pembayaran kursus no. order ORD-6A87ED7CF36F2-1787293052', '1200000', '2026-08-21', '2026-08-24 08:52:02', 'Pendaftaran Make Up Artist (Diza Syaichul Adilla)', '1', '1200000', NULL, '28', NULL),
('12', 'in', 'pemasukan_kursus', 'Pemasukan pembayaran kursus no. order ORD-6A88119499807-1787302292', '960000', '2026-08-21', '2026-08-24 08:52:02', 'Pendaftaran Make Up Artist (Diza Syaichul Adilla)', '1', '960000', NULL, '29', NULL),
('13', 'in', 'pemasukan_kursus', 'Pemasukan pembayaran kursus no. order ORD-6A8ACD91BB689-1787481489', '1200000', '2026-08-23', '2026-08-24 08:52:02', 'Pendaftaran Make Up Artist (Diza Syaichul Adilla)', '1', '1200000', NULL, '30', NULL),
('14', 'in', 'pemasukan_kursus', 'Pemasukan pembayaran kursus no. order ORD-6A8AD8C74A21C-1787484359', '1200000', '2026-08-23', '2026-08-24 08:52:02', 'Pendaftaran Make Up Artist (Diza Syaichul Adilla)', '1', '1200000', NULL, '31', NULL),
('15', 'in', 'pemasukan_kursus', 'Pemasukan pembayaran kursus no. order ORD-6A8AD9ED74C2C-1787484653', '800000', '2026-08-23', '2026-08-24 08:52:02', 'Pendaftaran Tata Kecantikan (Diza Syaichul Adilla)', '1', '800000', NULL, '32', NULL),
('19', 'in', 'pemasukan_kursus', 'Pemasukan pembayaran kursus no. order ORD-6A8ADED05565E-1787485904', '1000000', '2026-08-23', '2026-08-24 08:52:02', 'Pendaftaran Tata Kecantikan (Diza Syaichul Adilla)', '1', '1000000', NULL, '36', NULL),
('21', 'in', 'pemasukan_kursus', 'Pemasukan pembayaran kursus no. order ORD-6A8AE23FA5EA8-1787486783', '1000000', '2026-08-23', '2026-08-24 08:52:02', 'Pendaftaran Tata Kecantikan (Diza Syaichul Adilla)', '1', '1000000', NULL, '38', NULL),
('22', 'in', 'pemasukan_kursus', 'Pemasukan pembayaran kursus no. order ORD-6A8BA7913D523-1787537297', '960000', '2026-08-24', '2026-08-24 09:08:19', 'Pendaftaran Make Up Artist (Diza Syaichul Adilla)', '1', '960000', NULL, '39', NULL),
('23', 'in', 'pemasukan_kursus', 'Pemasukan pembayaran kursus no. order ORD-6A8BA7ECDE5F5-1787537388', '960000', '2026-08-24', '2026-08-24 09:09:50', 'Pendaftaran Make Up Artist (Diza Syaichul Adilla)', '1', '960000', NULL, '40', NULL),
('24', 'in', 'pemasukan_kursus', 'Pemasukan pembayaran kursus no. order ORD-6A8BABD9E44A2-1787538393', '800000', '2026-08-24', '2026-08-24 09:26:34', 'Pendaftaran Tata Kecantikan (Diza Syaichul Adilla)', '1', '800000', NULL, '41', NULL),
('25', 'in', 'pemasukan_kursus', 'Pemasukan pembayaran kursus no. order ORD-6A8BC828401CB-1787545640', '700000', '2026-08-24', '2026-08-24 11:27:22', 'Pendaftaran Public Speaking (Diza Syaichul Adilla)', '1', '700000', NULL, '42', NULL),
('26', 'in', 'pemasukan_kursus', 'Pemasukan pembayaran kursus no. order ORD-6A8CFD6A51A18-1787624810', '1200000', '2026-08-25', '2026-08-25 09:26:53', 'Pendaftaran Make Up Artist (Diza)', '1', '1200000', NULL, '43', NULL),
('27', 'in', 'pemasukan_kursus', 'Pemasukan pembayaran kursus no. order ORD-6A8CFD97E51E4-1787624855', '960000', '2026-08-25', '2026-08-25 09:27:38', 'Pendaftaran Make Up Artist (Diza)', '1', '960000', NULL, '44', NULL),
('28', 'in', 'pemasukan_kursus', 'Pemasukan pembayaran kursus no. order ORD-6A8D007BD85FB-1787625595', '1200000', '2026-08-25', '2026-08-25 09:39:57', 'Pendaftaran Make Up Artist (Diza)', '1', '1200000', NULL, '45', NULL),
('29', 'in', 'pemasukan_kursus', 'Pemasukan pembayaran kursus no. order ORD-6A8D023A9F9F8-1787626042', '700000', '2026-08-25', '2026-08-25 09:47:23', 'Pendaftaran Public Speaking (Diza)', '1', '700000', NULL, '46', NULL);

-- --------------------------------------------------------

-- Struktur dari tabel `gallery`
--

CREATE TABLE IF NOT EXISTS `gallery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(50) NOT NULL,
  `title` varchar(100) NOT NULL,
  `image` varchar(255) NOT NULL,
  `show_on_home` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `image_public_id` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=86 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data untuk tabel `gallery`
--

INSERT INTO `gallery` (`id`, `category`, `title`, `image`, `show_on_home`, `created_at`, `image_public_id`) VALUES
('29', 'kecantikan', 'Foto Dokumentasi Pelatihan Kecantikan', 'uploads/gallery/6a8ad83894620_0.jpeg', '1', '2026-08-23 18:23:36', NULL),
('30', 'kecantikan', 'Foto Dokumentasi Pelatihan Kecantikan', 'uploads/gallery/6a8ad83899f78_0.jpeg', '1', '2026-08-23 18:23:36', NULL),
('31', 'kecantikan', 'Foto Dokumentasi Pelatihan Kecantikan', 'uploads/gallery/6a8ad8389eb29_0.jpeg', '1', '2026-08-23 18:23:36', NULL),
('32', 'kecantikan', 'Foto Dokumentasi Pelatihan Kecantikan', 'uploads/gallery/6a8ad838a395c_0.jpeg', '1', '2026-08-23 18:23:36', NULL),
('33', 'kecantikan', 'Foto Dokumentasi Pelatihan Kecantikan', 'uploads/gallery/6a8ad838a8f7e_0.jpeg', '1', '2026-08-23 18:23:36', NULL),
('34', 'kecantikan', 'Foto Dokumentasi Pelatihan Kecantikan', 'uploads/gallery/6a8ad838b5a10_0.jpeg', '1', '2026-08-23 18:23:36', NULL),
('35', 'kecantikan', 'Foto Dokumentasi Pelatihan Kecantikan', 'uploads/gallery/6a8ad838c11c8_0.jpeg', '1', '2026-08-23 18:23:36', NULL),
('36', 'kecantikan', 'Foto Dokumentasi Pelatihan Kecantikan', 'uploads/gallery/6a8ad838cd961_0.jpeg', '1', '2026-08-23 18:23:36', NULL),
('37', 'kecantikan', 'Foto Dokumentasi Pelatihan Kecantikan', 'uploads/gallery/6a8ad838da990_0.jpeg', '1', '2026-08-23 18:23:36', NULL),
('38', 'kecantikan', 'Foto Dokumentasi Pelatihan Kecantikan', 'uploads/gallery/6a8ad838eada6_0.jpeg', '1', '2026-08-23 18:23:36', NULL),
('39', 'kecantikan', 'Foto Dokumentasi Pelatihan Kecantikan', 'uploads/gallery/6a8ad83901aea_0.jpeg', '1', '2026-08-23 18:23:37', NULL),
('40', 'kecantikan', 'Foto Dokumentasi Pelatihan Kecantikan', 'uploads/gallery/6a8ad8390d3fc_0.jpeg', '1', '2026-08-23 18:23:37', NULL),
('41', 'kecantikan', 'Foto Dokumentasi Pelatihan Kecantikan', 'uploads/gallery/6a8ad83917b52_0.jpeg', '1', '2026-08-23 18:23:37', NULL),
('42', 'kecantikan', 'Foto Dokumentasi Pelatihan Kecantikan', 'uploads/gallery/6a8ad83927f0d_0.jpeg', '1', '2026-08-23 18:23:37', NULL),
('43', 'kecantikan', 'Foto Dokumentasi Pelatihan Kecantikan', 'uploads/gallery/6a8ad8393457b_0.jpeg', '1', '2026-08-23 18:23:37', NULL),
('44', 'kecantikan', 'Foto Dokumentasi Pelatihan Kecantikan', 'uploads/gallery/6a8ad83943450_0.jpeg', '1', '2026-08-23 18:23:37', NULL),
('45', 'kecantikan', 'Foto Dokumentasi Pelatihan Kecantikan', 'uploads/gallery/6a8ad8395500f_0.jpeg', '1', '2026-08-23 18:23:37', NULL),
('46', 'kecantikan', 'Foto Dokumentasi Pelatihan Kecantikan', 'uploads/gallery/6a8ad839603e4_0.jpeg', '1', '2026-08-23 18:23:37', NULL),
('47', 'kecantikan', 'Foto Dokumentasi Pelatihan Kecantikan', 'uploads/gallery/6a8ad83964578_0.jpeg', '1', '2026-08-23 18:23:37', NULL),
('48', 'kecantikan', 'Foto Dokumentasi Pelatihan Kecantikan', 'uploads/gallery/6a8ad8396b92f_0.jpeg', '1', '2026-08-23 18:23:37', NULL),
('49', 'kecantikan', 'Foto Dokumentasi Pelatihan Kecantikan', 'uploads/gallery/6a8ad83972d42_0.jpeg', '1', '2026-08-23 18:23:37', NULL),
('50', 'kecantikan', 'Foto Dokumentasi Pelatihan Kecantikan', 'uploads/gallery/6a8ad83978e05_0.jpeg', '1', '2026-08-23 18:23:37', NULL),
('51', 'kecantikan', 'Foto Dokumentasi Pelatihan Kecantikan', 'uploads/gallery/6a8ad8397d8f9_0.jpeg', '1', '2026-08-23 18:23:37', NULL),
('52', 'kecantikan', 'Foto Dokumentasi Pelatihan Kecantikan', 'uploads/gallery/6a8ad83986716_0.jpeg', '1', '2026-08-23 18:23:37', NULL),
('53', 'kecantikan', 'Foto Dokumentasi Pelatihan Kecantikan', 'uploads/gallery/6a8ad83990a81_0.jpeg', '1', '2026-08-23 18:23:37', NULL),
('54', 'kecantikan', 'Foto Dokumentasi Pelatihan Kecantikan', 'uploads/gallery/6a8ad8399d340_0.jpeg', '1', '2026-08-23 18:23:37', NULL),
('55', 'kecantikan', 'Foto Dokumentasi Pelatihan Kecantikan', 'uploads/gallery/6a8ad839a545a_0.jpeg', '1', '2026-08-23 18:23:37', NULL),
('56', 'kecantikan', 'Foto Dokumentasi Pelatihan Kecantikan', 'uploads/gallery/6a8ad839afd60_0.jpeg', '1', '2026-08-23 18:23:37', NULL),
('57', 'kecantikan', 'Foto Dokumentasi Pelatihan Kecantikan', 'uploads/gallery/6a8ad839b72bb_0.jpeg', '1', '2026-08-23 18:23:37', NULL),
('58', 'kecantikan', 'Foto Dokumentasi Pelatihan Kecantikan', 'uploads/gallery/6a8ad839bef7b_0.jpeg', '1', '2026-08-23 18:23:37', NULL),
('59', 'kecantikan', 'Foto Dokumentasi Pelatihan Kecantikan', 'uploads/gallery/6a8ad839c4443_0.jpeg', '1', '2026-08-23 18:23:37', NULL),
('60', 'kecantikan', 'Foto Dokumentasi Pelatihan Kecantikan', 'uploads/gallery/6a8ad839c96d9_0.jpeg', '1', '2026-08-23 18:23:37', NULL),
('61', 'kecantikan', 'Foto Dokumentasi Pelatihan Kecantikan', 'uploads/gallery/6a8ad839d16e3_0.jpeg', '1', '2026-08-23 18:23:37', NULL),
('62', 'kecantikan', 'Foto Dokumentasi Pelatihan Kecantikan', 'uploads/gallery/6a8ad839d946e_0.jpeg', '1', '2026-08-23 18:23:37', NULL),
('63', 'kecantikan', 'Foto Dokumentasi Pelatihan Kecantikan', 'uploads/gallery/6a8ad839de898_0.jpeg', '1', '2026-08-23 18:23:37', NULL),
('64', 'pijat_terapis', 'Foto Dokumentasi Pelatihan Pijat Terapis', 'uploads/gallery/6a8ad87c3c852_0.jpeg', '1', '2026-08-23 18:24:44', NULL),
('65', 'pijat_terapis', 'Foto Dokumentasi Pelatihan Pijat Terapis', 'uploads/gallery/6a8ad87c51cf3_0.jpeg', '1', '2026-08-23 18:24:44', NULL),
('66', 'pijat_terapis', 'Foto Dokumentasi Pelatihan Pijat Terapis', 'uploads/gallery/6a8ad87c57749_0.jpeg', '1', '2026-08-23 18:24:44', NULL),
('67', 'pijat_terapis', 'Foto Dokumentasi Pelatihan Pijat Terapis', 'uploads/gallery/6a8ad87c61042_0.jpeg', '1', '2026-08-23 18:24:44', NULL),
('68', 'pijat_terapis', 'Foto Dokumentasi Pelatihan Pijat Terapis', 'uploads/gallery/6a8ad87c687f5_0.jpeg', '1', '2026-08-23 18:24:44', NULL),
('69', 'pijat_terapis', 'Foto Dokumentasi Pelatihan Pijat Terapis', 'uploads/gallery/6a8ad87c6fee1_0.jpeg', '1', '2026-08-23 18:24:44', NULL),
('70', 'pijat_terapis', 'Foto Dokumentasi Pelatihan Pijat Terapis', 'uploads/gallery/6a8ad87c77fc9_0.jpeg', '1', '2026-08-23 18:24:44', NULL),
('71', 'pijat_terapis', 'Foto Dokumentasi Pelatihan Pijat Terapis', 'uploads/gallery/6a8ad87c830f7_0.jpeg', '1', '2026-08-23 18:24:44', NULL),
('72', 'pijat_terapis', 'Foto Dokumentasi Pelatihan Pijat Terapis', 'uploads/gallery/6a8ad87c898b6_0.jpeg', '1', '2026-08-23 18:24:44', NULL),
('73', 'pijat_terapis', 'Foto Dokumentasi Pelatihan Pijat Terapis', 'uploads/gallery/6a8ad87c8e154_0.jpeg', '1', '2026-08-23 18:24:44', NULL),
('74', 'pijat_terapis', 'Foto Dokumentasi Pelatihan Pijat Terapis', 'uploads/gallery/6a8ad87c91c0e_0.jpeg', '1', '2026-08-23 18:24:44', NULL),
('75', 'pijat_terapis', 'Foto Dokumentasi Pelatihan Pijat Terapis', 'uploads/gallery/6a8ad87c96059_0.jpeg', '1', '2026-08-23 18:24:44', NULL),
('76', 'pijat_terapis', 'Foto Dokumentasi Pelatihan Pijat Terapis', 'uploads/gallery/6a8ad87c9a098_0.jpeg', '1', '2026-08-23 18:24:44', NULL),
('77', 'umum', 'Foto Dokumentasi Umum', 'uploads/gallery/6a8ad89804766_0.jpeg', '1', '2026-08-23 18:25:12', NULL),
('78', 'umum', 'Foto Dokumentasi Umum', 'uploads/gallery/6a8ad8980c269_0.jpeg', '1', '2026-08-23 18:25:12', NULL),
('79', 'umum', 'Foto Dokumentasi Umum', 'uploads/gallery/6a8ad898174b4_0.jpeg', '1', '2026-08-23 18:25:12', NULL),
('80', 'umum', 'Foto Dokumentasi Umum', 'uploads/gallery/6a8ad89822cbf_0.jpeg', '1', '2026-08-23 18:25:12', NULL),
('81', 'umum', 'PELATIHAN BIDANG KEHUMASAN DAN KESEKRETARIATAN', 'uploads/gallery/6a8fbbd0f2c9b_0.jpeg', '1', '2026-08-27 11:23:44', NULL),
('82', 'umum', 'PELATIHAN BIDANG KEHUMASAN DAN KESEKRETARIATAN', 'uploads/gallery/6a8fbbd1136f2_0.jpeg', '1', '2026-08-27 11:23:45', NULL),
('83', 'umum', 'PELATIHAN BIDANG KEHUMASAN DAN KESEKRETARIATAN', 'uploads/gallery/6a8fbbd1219d3_0.jpeg', '1', '2026-08-27 11:23:45', NULL),
('84', 'umum', 'PELATIHAN BIDANG KEHUMASAN DAN KESEKRETARIATAN', 'uploads/gallery/6a8fbbd12ef0b_0.jpeg', '1', '2026-08-27 11:23:45', NULL),
('85', 'umum', 'PELATIHAN BIDANG KEHUMASAN DAN KESEKRETARIATAN', 'uploads/gallery/6a8fbbd13f6b5_0.jpeg', '1', '2026-08-27 11:23:45', NULL);

-- --------------------------------------------------------

-- Struktur dari tabel `gallery_locations`
--

CREATE TABLE IF NOT EXISTS `gallery_locations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(100) NOT NULL,
  `image` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `category_image` (`category`,`image`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data untuk tabel `gallery_locations`
--

INSERT INTO `gallery_locations` (`id`, `category`, `image`, `created_at`) VALUES
('1', 'Alat Pemadam', 'assets/img/galeri-lokasi/Alat Pemadam/01.jpg', '2026-08-20 13:43:01'),
('2', 'Alat Pemadam', 'assets/img/galeri-lokasi/Alat Pemadam/02.jpg', '2026-08-20 13:43:01'),
('3', 'Alat Pemadam', 'assets/img/galeri-lokasi/Alat Pemadam/03.jpg', '2026-08-20 13:43:01'),
('4', 'Alat Pemadam', 'assets/img/galeri-lokasi/Alat Pemadam/04.jpg', '2026-08-20 13:43:01'),
('5', 'Alat Pemadam', 'assets/img/galeri-lokasi/Alat Pemadam/05.jpg', '2026-08-20 13:43:01'),
('6', 'Balkon', 'assets/img/galeri-lokasi/Balkon/01.jpg', '2026-08-20 13:43:01'),
('7', 'Balkon', 'assets/img/galeri-lokasi/Balkon/02.jpg', '2026-08-20 13:43:01'),
('8', 'Balkon', 'assets/img/galeri-lokasi/Balkon/03.jpg', '2026-08-20 13:43:01'),
('9', 'Balkon', 'assets/img/galeri-lokasi/Balkon/04.jpg', '2026-08-20 13:43:01'),
('10', 'Balkon', 'assets/img/galeri-lokasi/Balkon/05.jpg', '2026-08-20 13:43:01'),
('11', 'Balkon', 'assets/img/galeri-lokasi/Balkon/06.jpg', '2026-08-20 13:43:01'),
('12', 'Kantor', 'assets/img/galeri-lokasi/Kantor/01.jpg', '2026-08-20 13:43:01'),
('13', 'Kantor', 'assets/img/galeri-lokasi/Kantor/02.jpg', '2026-08-20 13:43:01'),
('14', 'Kantor', 'assets/img/galeri-lokasi/Kantor/03.jpg', '2026-08-20 13:43:01'),
('15', 'Kantor', 'assets/img/galeri-lokasi/Kantor/04.jpg', '2026-08-20 13:43:01'),
('16', 'Kelas', 'assets/img/galeri-lokasi/Kelas/01.jpg', '2026-08-20 13:43:01'),
('17', 'Kelas', 'assets/img/galeri-lokasi/Kelas/02.jpg', '2026-08-20 13:43:01'),
('18', 'Kelas', 'assets/img/galeri-lokasi/Kelas/03.jpg', '2026-08-20 13:43:01'),
('19', 'Kelas', 'assets/img/galeri-lokasi/Kelas/04.jpg', '2026-08-20 13:43:01'),
('20', 'Lobby', 'assets/img/galeri-lokasi/Lobby/01.jpg', '2026-08-20 13:43:01'),
('21', 'Lobby', 'assets/img/galeri-lokasi/Lobby/02.jpg', '2026-08-20 13:43:01'),
('22', 'Lobby', 'assets/img/galeri-lokasi/Lobby/03.jpg', '2026-08-20 13:43:01'),
('23', 'Lobby', 'assets/img/galeri-lokasi/Lobby/04.jpg', '2026-08-20 13:43:01'),
('24', 'Lobby', 'assets/img/galeri-lokasi/Lobby/05.jpg', '2026-08-20 13:43:01'),
('25', 'Lobby', 'assets/img/galeri-lokasi/Lobby/06.jpg', '2026-08-20 13:43:01'),
('26', 'Lobby', 'assets/img/galeri-lokasi/Lobby/07.jpg', '2026-08-20 13:43:01'),
('27', 'Lobby', 'assets/img/galeri-lokasi/Lobby/08.jpg', '2026-08-20 13:43:01'),
('28', 'Mushola', 'assets/img/galeri-lokasi/Mushola/01.jpg', '2026-08-20 13:43:01'),
('29', 'Mushola', 'assets/img/galeri-lokasi/Mushola/02.jpg', '2026-08-20 13:43:01'),
('30', 'Mushola', 'assets/img/galeri-lokasi/Mushola/03.jpg', '2026-08-20 13:43:01'),
('31', 'Mushola', 'assets/img/galeri-lokasi/Mushola/04.jpg', '2026-08-20 13:43:01'),
('32', 'Mushola', 'assets/img/galeri-lokasi/Mushola/05.jpg', '2026-08-20 13:43:01'),
('33', 'Parkir', 'assets/img/galeri-lokasi/Parkir/01.jpg', '2026-08-20 13:43:01'),
('34', 'Parkir', 'assets/img/galeri-lokasi/Parkir/02.jpg', '2026-08-20 13:43:01'),
('35', 'Parkir', 'assets/img/galeri-lokasi/Parkir/03.jpg', '2026-08-20 13:43:01'),
('36', 'Parkir', 'assets/img/galeri-lokasi/Parkir/04.jpg', '2026-08-20 13:43:01'),
('37', 'Parkir', 'assets/img/galeri-lokasi/Parkir/05.jpg', '2026-08-20 13:43:01'),
('38', 'Parkir', 'assets/img/galeri-lokasi/Parkir/06.jpg', '2026-08-20 13:43:01'),
('39', 'Toilet', 'assets/img/galeri-lokasi/Toilet/01.jpg', '2026-08-20 13:43:01'),
('40', 'Toilet', 'assets/img/galeri-lokasi/Toilet/02.jpg', '2026-08-20 13:43:01'),
('41', 'Toilet', 'assets/img/galeri-lokasi/Toilet/03.jpg', '2026-08-20 13:43:01'),
('42', 'Toilet', 'assets/img/galeri-lokasi/Toilet/04.jpg', '2026-08-20 13:43:01');

-- --------------------------------------------------------

-- Struktur dari tabel `instructors`
--

CREATE TABLE IF NOT EXISTS `instructors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `category` varchar(100) NOT NULL DEFAULT '',
  `specialization` varchar(150) NOT NULL,
  `bio` text DEFAULT NULL,
  `certifications` text DEFAULT NULL,
  `image` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `image_public_id` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data untuk tabel `instructors`
--

INSERT INTO `instructors` (`id`, `name`, `category`, `specialization`, `bio`, `certifications`, `image`, `created_at`, `image_public_id`) VALUES
('1', 'Bunga Lestari, S.Pd., C.MUA', 'Tata Rias', 'Make Up Artist & Bridal Specialist', 'Praktisi MUA profesional lebih dari 10 tahun pengalaman merias wedding tradisional, modern, editorial, dan panggung hiburan.', 'BNSP Level 4 Tata Rias, Certified International MUA, Asesor Kompetensi LSP', 'uploads/instructors/bunga.jpg', '2026-08-19 11:48:24', NULL),
('2', 'Jessica Angelina, C.E.', 'Tata Rias', 'Senior Esthetician & Skin Care Specialist', 'Pakar estetika kulit dan perawatan wajah medis ringan dengan sertifikasi internasional CIDESCO.', 'CIDESCO International Diploma, BNSP Perawatan Kulit', 'assets/img/logo.png', '2026-08-19 11:48:24', NULL),
('3', 'Maya Kartika', 'Tata Rias', 'Professional Hair Stylist & Colorist', 'Hair stylist salon ternama di Bandung, spesialis teknik rebonding, smoothing, coloring balayage, dan blow styling modern.', 'Certified Pivot Point International, Asesor Tata Kecantikan Rambut', 'assets/img/logo.png', '2026-08-19 11:48:24', NULL),
('4', 'Rani Oktavia', 'Tata Rias', 'Nail Art & Eyelash Extension Artist', 'Trainer kecantikan kuku dan bulu mata dengan teknik semi-permanen higienis dan standar salon kecantikan Jepang.', 'Certified Russian Volume Lash, Professional Nail Art Specialist', 'assets/img/logo.png', '2026-08-19 11:48:24', NULL),
('9', 'Dr. Ir. H. Ahmad Jaelani, M.Pd', 'Public Speaking', 'Master Trainer & Public Speaking', 'Dosen dan instruktur senior bidang pedagogik vokasi, spesialis penyusunan silabus kompetensi kerja dan teknik pengajaran interaktif.', 'Master Asesor BNSP, Certified Master Trainer of Trainer (ToT)', 'assets/img/logo.png', '2026-08-19 11:48:24', NULL),
('10', 'Dra. Ratna Kusuma, M.M.', 'Public Speaking', 'Pengembangan Kurikulum Vokasi Berbasis SKKNI', 'Konsultan kurikulum pendidikan non-formal dan pelatihan kerja dengan pengalaman membina puluhan LPK terakreditasi nasional.', 'Asesor SKKNI Kemnaker, Certified Instructional Designer', 'assets/img/logo.png', '2026-08-19 11:48:24', NULL),
('11', 'Fajar Nugraha, S.Psi., C.HRM', 'Public Speaking', 'Komunikasi Publik & Presentasi Efektif', 'Praktisi psikologi industri yang berpengalaman melatih ribuan fasilitator dalam public speaking, Ice Breaking, dan dinamika kelompok.', 'Certified Facilitator BNSP, Certified NLP Practitioner', 'assets/img/logo.png', '2026-08-19 11:48:24', NULL);

-- --------------------------------------------------------

-- Struktur dari tabel `material_progress`
--

CREATE TABLE IF NOT EXISTS `material_progress` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `material_id` int(11) NOT NULL,
  `completed` tinyint(1) NOT NULL DEFAULT 0,
  `completed_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_progress_user_material` (`user_id`,`material_id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data untuk tabel `material_progress`
--

INSERT INTO `material_progress` (`id`, `user_id`, `material_id`, `completed`, `completed_at`) VALUES
('12', '5', '11', '1', '2026-08-17 19:49:29'),
('13', '5', '12', '1', '2026-08-18 13:41:56'),
('14', '5', '13', '1', '2026-08-18 13:43:06'),
('15', '5', '14', '1', '2026-08-18 13:43:35'),
('16', '8', '9', '1', '2026-08-19 07:07:58'),
('17', '8', '10', '1', '2026-08-19 12:08:41'),
('18', '8', '11', '1', '2026-08-19 12:09:21'),
('19', '8', '12', '1', '2026-08-19 12:09:58'),
('20', '8', '13', '1', '2026-08-19 12:10:13'),
('21', '8', '14', '1', '2026-08-19 12:10:29'),
('22', '9', '9', '1', '2026-08-24 02:24:31'),
('23', '9', '10', '1', '2026-08-24 09:24:55'),
('24', '9', '11', '1', '2026-08-24 09:25:22'),
('25', '9', '12', '1', '2026-08-24 09:25:47'),
('26', '9', '13', '1', '2026-08-24 09:25:55'),
('27', '9', '14', '1', '2026-08-24 09:26:03'),
('28', '9', '15', '1', '2026-08-24 11:27:35'),
('29', '9', '16', '1', '2026-08-24 11:41:03'),
('30', '10', '15', '1', '2026-08-25 09:47:37'),
('35', '10', '16', '1', '2026-08-27 11:02:01');

-- --------------------------------------------------------

-- Struktur dari tabel `materials`
--

CREATE TABLE IF NOT EXISTS `materials` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `class_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `type` enum('video','pdf','text') NOT NULL DEFAULT 'text',
  `content` text NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data untuk tabel `materials`
--

INSERT INTO `materials` (`id`, `class_id`, `title`, `type`, `content`, `sort_order`, `created_at`) VALUES
('9', '1', 'Modul 1', 'pdf', 'uploads/materials/6a82fe8311488.pdf', '0', '2026-08-17 19:28:51'),
('10', '1', 'Modul 2 - Pengenalan Alat & Produk Make-up', 'text', 'Mengenal alat dan produk make-up merupakan fondasi penting sebelum mempraktikkan teknik rias. Seorang MUA profesional harus memahami fungsi setiap alat dan kandungan setiap produk agar hasil riasan optimal dan aman untuk kulit klien.\n\nAlat-alat dasar yang wajib dimiliki:\n- Kuas make-up (foundation, blush, eyeshadow, lip brush) ??? pilih bulu sintetis yang halus dan mudah dibersihkan.\n- Spons beauty blender untuk hasil foundation yang merata dan natural.\n- Sikat alis dan sisir alis untuk membentuk serta merapikan alis.\n- Pinset dan gunting bulu alis untuk grooming.\n- Palet warna dan cermin dengan lampu agar warna terlihat akurat.\n\nProduk yang perlu dikuasai sesuai urutan pemakaian:\n1. Primer ??? menyamarkan pori dan membuat make-up tahan lama.\n2. Foundation / BB cream ??? meratakan warna kulit.\n3. Concealer ??? menutup noda, bekas jerawat, dan lingkaran gelap mata.\n4. Bedak tabur / pressed ??? mengunci riasan.\n5. Blush, bronzer, dan highlighter ??? memberi dimensi pada wajah.\n6. Eyeshadow, eyeliner, maskara ??? mempertegas mata.\n7. Pensil alis dan brow gel ??? membingkai wajah.\n8. Lipstik, lip liner, dan lip gloss ??? menyempurnakan bibir.\n\nSebelum digunakan pada klien, selalu lakukan uji coba produk pada area kecil kulit dan pastikan alat bersih. Kebersihan alat adalah standar mutlak dalam industri make-up.', '2', '2026-08-17 19:31:47'),
('11', '1', 'Modul 3 - Make-up Natural & Flawless', 'text', 'Make-up natural bertujuan menonjolkan kecantikan alami tanpa terlihat tebal. Kunci utama adalah pemilihan warna yang mendekati warna kulit (skin tone) dan teknik layering yang tipis namun berlapis.\n\nLangkah make-up natural:\n1. Bersihkan wajah lalu aplikasikan pelembap dan primer ringan.\n2. Aplikasikan foundation tipis dengan spons, mulai dari area tengah wajah lalu ratakan ke tepi.\n3. Tutup area bermasalah dengan concealer secukupnya.\n4. Set dengan bedak tipis agar riasan matte dan tahan lama.\n5. Beri blush warna rose/pink lembut di pipi, bronzer ringan di bawah tulang pipi, dan highlighter halus di tulang pipi, cupid bow, dan ujung hidung.\n6. Rias mata dengan warna netral (brown, beige, peach). Gunakan maskara untuk membuka pandangan mata.\n7. Rapikan alis dengan brow gel bening.\n8. Selesaikan dengan lipstik nude/peach dan lip balm agar bibir tampak sehat.\n\nKesalahan umum yang harus dihindari:\n- Foundation terlalu tebal atau warna tidak sesuai (mismatch).\n- Blush terlalu pekat sehingga wajah tampak seperti boneka.\n- Highlighter berlebihan di bawah cahaya lampu.\n- Tidak menyet make-up sehingga cepat luntur.\n\nLatihan rutin dengan berbagai bentuk wajah akan membuat teknik ini semakin presisi.', '3', '2026-08-17 19:31:47'),
('12', '1', 'Modul 4 - Koreksi Bentuk Wajah', 'text', 'Koreksi bentuk wajah adalah teknik sculpting menggunakan warna gelap, netral, dan terang untuk menciptakan ilusi bentuk wajah yang proporsional. Prinsipnya: warna gelap mengecilkan/menenggelamkan, warna terang menonjolkan.\n\nBentuk wajah dan strategi koreksi:\n- Wajah bulat: shading di samping dahi, pipi, dan rahang; highlight di tengah dahi dan dagu agar wajah tampak lonjong.\n- Wajah persegi: shading di sudut rahang dan sudut dahi; highlight di tengah dahi, pipi, dan dagu untuk melunakkan sudut.\n- Wajah panjang: shading di garis rambut dan bawah dagu; highlight di dahi dan tulang pipi.\n- Wajah oval: relatif proporsional, cukup contour ringan di bawah tulang pipi.\n- Wajah hati: shading di pelipis dan rahang bawah; highlight di tengah dahi dan dagu.\n\nTeknik dasar:\n- Contour: gunakan warna 2 tingkat lebih gelap dari kulit, aplikasikan dengan kuas kecil lalu blend menyeluruh tanpa garis tegas.\n- Highlight: warna 1-2 tingkat lebih terang untuk area yang ingin ditonjolkan.\n- Blush: aplikasikan mengikuti arah tulang pipi untuk memberi kesegaran.\n\nKunci hasil natural adalah blending. Selalu cek hasil di bawah pencahayaan yang berbeda sebelum riasan dianggap selesai.', '4', '2026-08-17 19:31:47'),
('13', '1', 'Modul 5 - Make-up Wedding & Formal', 'text', 'Make-up wedding menuntut daya tahan lama (8-12 jam), tahan air, dan fotogenic di bawah cahaya kamera. Standar hasilnya flawless di mata langsung maupun melalui lensa.\n\nPersiapan khusus:\n- Kulit wajah dipersiapkan sejak H-7: perawatan dasar, istirahat cukup, dan hindari produk baru yang bisa menimbulkan iritasi.\n- Gunakan primer yang mengunci minyak dan produk long-wear/waterproof.\n\nTeknik riasan wedding:\n1. Base yang lebih solid: foundation full coverage + setting spray berlapis.\n2. Mata menjadi fokus utama: eyeshadow tahan lama dengan teknik cut crease atau soft smokey sesuai permintaan klien.\n3. Gunakan bulu mata palsu untuk kesan mata lebih besar dan dramatis.\n4. Alis yang tegas namun natural untuk membingkai wajah.\n5. Lipstik matte tahan lama dengan lip liner penuh (full lip lining) agar tidak luntur saat makan.\n6. Selesaikan dengan setting spray dan sisipkan bedak touch-up untuk sesi foto.\n\nPerbedaan dengan make-up natural: intensitas lebih tinggi, daya tahan lebih lama, dan detail lebih presisi. Pastikan diskusi dengan klien tentang tema dan model gaun sebelum eksekusi.', '5', '2026-08-17 19:31:47'),
('14', '1', 'Modul 6 - Praktek dengan Model & Penyaluran Klien', 'text', 'Modul ini melatih kemampuan menghadapi klien langsung dan membangun portofolio profesional. Kepercayaan diri, komunikasi, dan manajemen waktu adalah skill yang setara pentingnya dengan teknik rias.\n\nKesiapan sebelum praktek:\n- Bawa kit make-up lengkap yang sudah disterilkan dan pastikan kondisi produk layak pakai.\n- Siapkan moodboard/referensi sesuai permintaan klien.\n- Lakukan konsultasi singkat: jenis kulit, alergi, tema acara, dan durasi yang dibutuhkan.\n\nSaat praktek:\n- Jaga etika dan sopan santun; jelaskan langkah yang sedang dikerjakan.\n- Dokumentasikan proses dan hasil dengan kamera (dengan izin klien) untuk portofolio.\n- Lakukan touch-up terakhir sebelum klien meninggalkan kursi.\n\nSetelah menyelesaikan seluruh modul dan lulus uji praktek, lulusan MCM berkesempatan mendapatkan penyaluran klien melalui jaringan lembaga. Portofolio yang rapi, harga yang kompetitif, dan reputasi yang baik akan membuka peluang klien berulang.', '6', '2026-08-17 19:31:47'),
('15', '9', 'Modul 1 - Dasar-Dasar Public Speaking', 'text', '1.1 Apa itu Public Speaking?\r\nPublic speaking adalah kemampuan menyampaikan pesan, ide, atau informasi secara lisan di depan audiens dengan jelas, percaya diri, dan meyakinkan. Kemampuan ini dibutuhkan dalam berbagai situasi: presentasi kerja, MC acara, wawancara kerja, hingga berbicara di forum publik.\r\n\r\n1.2 Mengapa Orang Takut Berbicara di Depan Umum?\r\n- Takut dinilai atau dikritik (glossophobia)\r\n- Kurang persiapan\r\n- Trauma pengalaman buruk sebelumnya\r\n- Kurangnya jam terbang/latihan\r\n\r\n1.3 Teknik Mengatasi Demam Panggung (Stage Fright)\r\n- Teknik pernapasan diafragma sebelum tampil\r\n- Power posing 2 menit sebelum naik panggung\r\n- Reframing rasa gugup menjadi energi (bukan menghilangkannya)\r\n- Latihan visualisasi keberhasilan\r\n- Kenali venue dan audiens sebelum tampil\r\n\r\n1.4 Dasar Olah Vokal\r\n- Artikulasi: pengucapan kata yang jelas\r\n- Intonasi: naik-turun nada untuk penekanan makna\r\n- Tempo: kecepatan bicara yang pas (tidak terlalu cepat/lambat)\r\n- Volume: menyesuaikan besar ruangan dan jumlah audiens\r\n- Jeda (pause): memberi waktu audiens mencerna informasi\r\n\r\nLatihan Praktik:\r\nRekam diri sendiri membacakan sebuah paragraf singkat selama 1 menit, lalu dengarkan kembali dan catat: apakah tempo bicara sudah pas? apakah ada kata yang kurang jelas?\r\n\r\nSoal Evaluasi:\r\n1. Apa yang dimaksud dengan glossophobia?\r\n2. Sebutkan 3 teknik untuk mengatasi demam panggung sebelum tampil!\r\n3. Jelaskan perbedaan antara intonasi dan tempo dalam berbicara!\r\n4. Mengapa jeda (pause) penting dalam sebuah presentasi?\r\n5. Praktik: Rekam video diri Anda berbicara selama 1 menit tentang topik bebas, lalu evaluasi artikulasi dan tempo bicara Anda sendiri.', '1', '2026-08-24 11:20:27'),
('16', '9', 'Modul 2 - Bahasa Tubuh & Penyampaian Pesan', 'video', 'https://www.youtube.com/watch?v=OegVTDR6eyw', '2', '2026-08-24 11:24:50');

-- --------------------------------------------------------

-- Struktur dari tabel `orders`
--

CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `order_number` varchar(50) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_phone` varchar(20) NOT NULL,
  `customer_email` varchar(100) DEFAULT NULL,
  `customer_address` text DEFAULT NULL,
  `customer_institution` varchar(100) DEFAULT NULL,
  `class_id` int(11) NOT NULL,
  `class_mode` enum('online','offline') NOT NULL DEFAULT 'offline',
  `instructor_id` int(11) DEFAULT NULL,
  `amount` int(11) NOT NULL,
  `status` enum('pending','confirmed','cancelled') DEFAULT 'pending',
  `payment_status` enum('unpaid','pending','paid','failed','expired') DEFAULT 'unpaid',
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_gateway_ref` varchar(100) DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_number` (`order_number`),
  KEY `class_id` (`class_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data untuk tabel `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `order_number`, `customer_name`, `customer_phone`, `customer_email`, `customer_address`, `customer_institution`, `class_id`, `class_mode`, `instructor_id`, `amount`, `status`, `payment_status`, `payment_method`, `payment_gateway_ref`, `paid_at`, `created_at`) VALUES
('2', NULL, 'ORD-6A82EEF5D9FDB-1786965749', 'Testi Payment E2E', '6281234567890', 'payment.e2e@test.com', 'Jl. Uji No.1', 'PT Uji', '1', 'offline', NULL, '1200000', 'confirmed', 'paid', 'bank_transfer', 'MOCK-ORD-6A82EEF5D9FDB-1786965749', '2026-08-17 18:22:31', '2026-08-17 18:22:29'),
('3', NULL, 'ORD-6A82EF26B8BFF-1786965798', 'Testi Gagal', '628987654321', 'gagal@test.com', 'Jl. Gagal 1', '-', '2', 'offline', NULL, '1000000', 'pending', 'failed', 'bank_transfer', 'MOCK-ORD-6A82EF26B8BFF-1786965798', NULL, '2026-08-17 18:23:18'),
('8', '5', 'ORD-6A82FC829F295-1786969218', 'Gilang Erlangga', '6281215614286', 'gilangerlangga1306@gmail.com', 'Legok Bandung Dua', '-', '1', 'offline', NULL, '1200000', 'confirmed', 'paid', 'bank_transfer', 'MOCK-ORD-6A82FC829F295-1786969218', '2026-08-17 19:20:20', '2026-08-17 19:20:18'),
('21', '7', 'ORD-6A853363AB9D4-1787114339', 'Abdul Rapli', '6285881327923', 'abdulrapli@gmail.com', 'Jl Kubang', '-', '1', 'offline', '1', '1200000', 'pending', 'unpaid', NULL, 'MOCK-ORD-6A853363AB9D4-1787114339', NULL, '2026-08-19 11:38:59'),
('22', '7', 'ORD-6A8533C466287-1787114436', 'Abdul Rapli', '6285881327923', 'abdulrapli@gmail.com', 'Jl Kubang', '-', '1', 'offline', '1', '1200000', 'confirmed', 'paid', 'bank_transfer', 'MOCK-ORD-6A8533C466287-1787114436', '2026-08-19 11:40:41', '2026-08-19 11:40:36'),
('23', '8', 'ORD-6A853900215EA-1787115776', 'Alvina Marva Dearsy', '6285881327923', 'Alvina99@gmail.com', 'Jl. Muararajeun', '-', '1', 'offline', '2', '1200000', 'confirmed', 'paid', 'bank_transfer', 'MOCK-ORD-6A853900215EA-1787115776', '2026-08-19 12:03:02', '2026-08-19 12:02:56'),
('26', '9', 'ORD-6A87E9A836FF2-1787292072', 'Diza Syaichul Adilla', '6289289829839', 'dizasyaichul7@gmail.com', 'Jl.Kadupugur', '-', '1', 'offline', '1', '1200000', 'confirmed', 'paid', 'bank_transfer', 'MOCK-ORD-6A87E9A836FF2-1787292072', '2026-08-21 13:01:13', '2026-08-21 13:01:12'),
('28', '9', 'ORD-6A87ED7CF36F2-1787293052', 'Diza Syaichul Adilla', '6289289829839', 'dizasyaichul7@gmail.com', 'Jl.KAdupugur', '-', '1', 'offline', '1', '1200000', 'confirmed', 'paid', 'bank_transfer', 'MOCK-ORD-6A87ED7CF36F2-1787293052', '2026-08-21 13:17:34', '2026-08-21 13:17:32'),
('29', '9', 'ORD-6A88119499807-1787302292', 'Diza Syaichul Adilla', '6289289829839', 'dizasyaichul7@gmail.com', 'Jl.KAdupugur', '-', '1', 'online', '1', '960000', 'confirmed', 'paid', 'bank_transfer', 'MOCK-ORD-6A88119499807-1787302292', '2026-08-21 15:51:34', '2026-08-21 15:51:32'),
('30', '9', 'ORD-6A8ACD91BB689-1787481489', 'Diza Syaichul Adilla', '6289289829839', 'dizasyaichul7@gmail.com', 'JL.Bojong', '-', '1', 'offline', '1', '1200000', 'confirmed', 'paid', 'bank_transfer', 'MOCK-ORD-6A8ACD91BB689-1787481489', '2026-08-23 17:38:11', '2026-08-23 17:38:09'),
('31', '9', 'ORD-6A8AD8C74A21C-1787484359', 'Diza Syaichul Adilla', '6289289829839', 'dizasyaichul7@gmail.com', 'Jl.Kadupugur', '-', '1', 'offline', '1', '1200000', 'confirmed', 'paid', 'bank_transfer', 'MOCK-ORD-6A8AD8C74A21C-1787484359', '2026-08-23 18:26:04', '2026-08-23 18:25:59'),
('32', '9', 'ORD-6A8AD9ED74C2C-1787484653', 'Diza Syaichul Adilla', '6289289829839', 'dizasyaichul7@gmail.com', 'Jl.Kembali', '-', '2', 'online', '2', '800000', 'confirmed', 'paid', 'bank_transfer', 'MOCK-ORD-6A8AD9ED74C2C-1787484653', '2026-08-23 18:30:54', '2026-08-23 18:30:53'),
('36', '9', 'ORD-6A8ADED05565E-1787485904', 'Diza Syaichul Adilla', '6289289829839', 'dizasyaichul7@gmail.com', 'Jl.Sudirman', '-', '2', 'offline', '1', '1000000', 'confirmed', 'paid', 'bank_transfer', 'MOCK-ORD-6A8ADED05565E-1787485904', '2026-08-23 18:51:45', '2026-08-23 18:51:44'),
('38', '9', 'ORD-6A8AE23FA5EA8-1787486783', 'Diza Syaichul Adilla', '6289289829839', 'dizasyaichul7@gmail.com', 'dwqawdawd', '-', '2', 'offline', '3', '1000000', 'confirmed', 'paid', 'bank_transfer', 'MOCK-ORD-6A8AE23FA5EA8-1787486783', '2026-08-23 19:06:26', '2026-08-23 19:06:23'),
('39', '9', 'ORD-6A8BA7913D523-1787537297', 'Diza Syaichul Adilla', '6289289829839', 'dizasyaichul7@gmail.com', 'Jl.Kadupugur', '-', '1', 'online', '2', '960000', 'confirmed', 'paid', 'bank_transfer', 'MOCK-ORD-6A8BA7913D523-1787537297', '2026-08-24 09:08:19', '2026-08-24 09:08:17'),
('40', '9', 'ORD-6A8BA7ECDE5F5-1787537388', 'Diza Syaichul Adilla', '6289289829839', 'dizasyaichul7@gmail.com', 'Jl.Kadupugur', '-', '1', 'online', '2', '960000', 'confirmed', 'paid', 'bank_transfer', 'MOCK-ORD-6A8BA7ECDE5F5-1787537388', '2026-08-24 09:09:50', '2026-08-24 09:09:48'),
('41', '9', 'ORD-6A8BABD9E44A2-1787538393', 'Diza Syaichul Adilla', '6289289829839', 'dizasyaichul7@gmail.com', 'Jl.Aweaw', '-', '2', 'online', '1', '800000', 'confirmed', 'paid', 'bank_transfer', 'MOCK-ORD-6A8BABD9E44A2-1787538393', '2026-08-24 09:26:34', '2026-08-24 09:26:33'),
('42', '9', 'ORD-6A8BC828401CB-1787545640', 'Diza Syaichul Adilla', '6289289829839', 'dizasyaichul7@gmail.com', 'Jl.Bojong', '-', '9', 'online', '9', '700000', 'confirmed', 'paid', 'bank_transfer', 'MOCK-ORD-6A8BC828401CB-1787545640', '2026-08-24 11:27:22', '2026-08-24 11:27:20'),
('43', '10', 'ORD-6A8CFD6A51A18-1787624810', 'Diza', '6289289829839', 'dizasyaichul8@gmail.com', 'Jl.Kadupugur', '-', '1', 'offline', '3', '1200000', 'confirmed', 'paid', 'bank_transfer', 'MOCK-ORD-6A8CFD6A51A18-1787624810', '2026-08-25 09:26:52', '2026-08-25 09:26:50'),
('44', '10', 'ORD-6A8CFD97E51E4-1787624855', 'Diza', '6289289829839', 'dizasyaichul8@gmail.com', 'Jl.Kadupugur', '-', '1', 'online', '2', '960000', 'confirmed', 'paid', 'bank_transfer', 'MOCK-ORD-6A8CFD97E51E4-1787624855', '2026-08-25 09:27:38', '2026-08-25 09:27:35'),
('45', '10', 'ORD-6A8D007BD85FB-1787625595', 'Diza', '6289289829839', 'dizasyaichul8@gmail.com', 'Jl.Bjong', '-', '1', 'offline', '1', '1200000', 'confirmed', 'paid', 'bank_transfer', 'MOCK-ORD-6A8D007BD85FB-1787625595', '2026-08-25 09:39:57', '2026-08-25 09:39:55'),
('46', '10', 'ORD-6A8D023A9F9F8-1787626042', 'Diza', '6289289829839', 'dizasyaichul8@gmail.com', 'Jl.Kadupugur', '-', '9', 'online', '9', '700000', 'confirmed', 'paid', 'bank_transfer', 'MOCK-ORD-6A8D023A9F9F8-1787626042', '2026-08-25 09:47:23', '2026-08-25 09:47:22'),
('47', '6', 'ORD-6A9906EEEEA18-1788413678', 'Abdul Rapli', '6285881327923', 'abdulrapli05@gmail.com', 'jk', '-', '2', 'offline', '2', '1000000', 'pending', 'unpaid', NULL, '0aed969a9d11425db51b177989e277d720263403123441322', NULL, '2026-09-03 12:34:38'),
('48', '6', 'ORD-6A990972D75E7-1788414322', 'Abdul Rapli', '6285881327923', 'abdulrapli05@gmail.com', 'jl', '-', '2', 'offline', '2', '1000000', 'pending', 'unpaid', NULL, '0add6865af0649c4802c74b3386bfd2a20264503124524862', NULL, '2026-09-03 12:45:22'),
('49', '6', 'ORD-6A990B9531226-1788414869', 'Abdul Rapli', '6285881327923', 'abdulrapli05@gmail.com', 'ng', '-', '2', 'offline', '2', '1000000', 'pending', 'unpaid', NULL, '8921449c649349ff96890b2da4b1c56f20265403125430946', NULL, '2026-09-03 12:54:29');

-- --------------------------------------------------------

-- Struktur dari tabel `quiz_attempts`
--

CREATE TABLE IF NOT EXISTS `quiz_attempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `material_id` int(11) NOT NULL,
  `score` int(11) NOT NULL,
  `total` int(11) NOT NULL,
  `passed` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_material` (`user_id`,`material_id`)
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data untuk tabel `quiz_attempts`
--

INSERT INTO `quiz_attempts` (`id`, `user_id`, `material_id`, `score`, `total`, `passed`, `created_at`) VALUES
('5', '5', '11', '0', '3', '0', '2026-08-17 19:49:05'),
('6', '5', '11', '1', '3', '0', '2026-08-17 19:49:09'),
('7', '5', '11', '0', '3', '0', '2026-08-17 19:49:13'),
('8', '5', '11', '1', '3', '0', '2026-08-17 19:49:16'),
('9', '5', '11', '1', '3', '0', '2026-08-17 19:49:18'),
('10', '5', '11', '2', '3', '0', '2026-08-17 19:49:20'),
('11', '5', '11', '2', '3', '0', '2026-08-17 19:49:24'),
('12', '5', '11', '2', '3', '0', '2026-08-17 19:49:27'),
('13', '5', '11', '3', '3', '1', '2026-08-17 19:49:29'),
('19', '5', '13', '2', '3', '0', '2026-08-18 13:42:49'),
('20', '5', '13', '1', '3', '0', '2026-08-18 13:42:53'),
('21', '5', '13', '2', '3', '0', '2026-08-18 13:43:02'),
('22', '5', '13', '3', '3', '1', '2026-08-18 13:43:06'),
('28', '5', '11', '3', '3', '1', '2026-08-19 09:47:16'),
('29', '5', '12', '3', '3', '1', '2026-08-19 09:47:16'),
('30', '5', '13', '3', '3', '1', '2026-08-19 09:47:16'),
('31', '5', '14', '3', '3', '1', '2026-08-19 09:47:16'),
('32', '8', '10', '2', '3', '0', '2026-08-19 12:08:15'),
('33', '8', '10', '2', '3', '0', '2026-08-19 12:08:32'),
('34', '8', '10', '2', '3', '0', '2026-08-19 12:08:37'),
('35', '8', '10', '3', '3', '1', '2026-08-19 12:08:41'),
('36', '8', '11', '1', '3', '0', '2026-08-19 12:09:02'),
('37', '8', '11', '1', '3', '0', '2026-08-19 12:09:03'),
('38', '8', '11', '1', '3', '0', '2026-08-19 12:09:08'),
('39', '8', '11', '2', '3', '0', '2026-08-19 12:09:12'),
('40', '8', '11', '2', '3', '0', '2026-08-19 12:09:17'),
('41', '8', '11', '3', '3', '1', '2026-08-19 12:09:21'),
('42', '8', '12', '1', '3', '0', '2026-08-19 12:09:38'),
('43', '8', '12', '0', '3', '0', '2026-08-19 12:09:43'),
('44', '8', '12', '1', '3', '0', '2026-08-19 12:09:48'),
('45', '8', '12', '2', '3', '0', '2026-08-19 12:09:53'),
('46', '8', '12', '3', '3', '1', '2026-08-19 12:09:58'),
('47', '8', '13', '3', '3', '1', '2026-08-19 12:10:13'),
('48', '8', '14', '3', '3', '1', '2026-08-19 12:10:29'),
('49', '9', '10', '1', '3', '0', '2026-08-24 09:24:43'),
('50', '9', '10', '2', '3', '0', '2026-08-24 09:24:47'),
('51', '9', '10', '3', '3', '1', '2026-08-24 09:24:55'),
('52', '9', '11', '3', '3', '1', '2026-08-24 09:25:22'),
('53', '9', '12', '3', '3', '1', '2026-08-24 09:25:47'),
('54', '9', '13', '3', '3', '1', '2026-08-24 09:25:55'),
('55', '9', '14', '3', '3', '1', '2026-08-24 09:26:03'),
('56', '9', '15', '2', '2', '1', '2026-08-24 11:27:35'),
('57', '9', '16', '2', '2', '1', '2026-08-24 11:41:03'),
('58', '10', '15', '1', '2', '0', '2026-08-25 09:47:35'),
('59', '10', '15', '2', '2', '1', '2026-08-25 09:47:37'),
('64', '10', '16', '3', '3', '1', '2026-08-27 11:02:01');

-- --------------------------------------------------------

-- Struktur dari tabel `quiz_progress`
--

CREATE TABLE IF NOT EXISTS `quiz_progress` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `is_correct` tinyint(1) NOT NULL DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_user_question` (`user_id`,`question_id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data untuk tabel `quiz_progress`
--

INSERT INTO `quiz_progress` (`id`, `user_id`, `question_id`, `is_correct`, `updated_at`) VALUES
('14', '10', '18', '1', '2026-08-27 11:01:09'),
('15', '10', '19', '1', '2026-08-27 11:02:01'),
('16', '10', '20', '1', '2026-08-27 11:02:01');

-- --------------------------------------------------------

-- Struktur dari tabel `quiz_questions`
--

CREATE TABLE IF NOT EXISTS `quiz_questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `material_id` int(11) NOT NULL,
  `question_type` enum('mcq','essay') NOT NULL DEFAULT 'mcq',
  `question` text NOT NULL,
  `option_a` varchar(255) NOT NULL,
  `option_b` varchar(255) NOT NULL,
  `option_c` varchar(255) NOT NULL,
  `option_d` varchar(255) NOT NULL,
  `correct_option` enum('a','b','c','d') DEFAULT NULL,
  `essay_answer` text DEFAULT NULL,
  `explanation` text DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `material_id` (`material_id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data untuk tabel `quiz_questions`
--

INSERT INTO `quiz_questions` (`id`, `material_id`, `question_type`, `question`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `essay_answer`, `explanation`, `sort_order`) VALUES
('1', '10', 'mcq', 'Untuk hasil foundation yang merata dan natural, alat yang paling tepat digunakan adalah...', 'Spons beauty blender', 'Kuas eyeshadow', 'Pinset', 'Gunting bulu alis', 'a', NULL, NULL, '0'),
('2', '10', 'mcq', 'Produk yang berfungsi menyamarkan pori dan membuat make-up tahan lama adalah...', 'Foundation', 'Primer', 'Maskara', 'Lip gloss', 'b', NULL, NULL, '1'),
('3', '10', 'mcq', 'Urutan pemakaian produk setelah foundation dan sebelum bedak tabur adalah...', 'Primer', 'Eyeliner', 'Concealer', 'Blush', 'c', NULL, NULL, '2'),
('4', '11', 'mcq', 'Kunci utama make-up natural adalah...', 'Menggunakan warna gelap tebal', 'Warna mendekati skin tone dan layering tipis', 'Highlighter berlebihan', 'Foundation sangat tebal', 'b', NULL, NULL, '0'),
('5', '11', 'mcq', 'Kesalahan umum yang harus dihindari dalam make-up natural adalah...', 'Blush lembut', 'Bedak tipis', 'Warna netral', 'Foundation terlalu tebal atau warna mismatch', 'd', NULL, NULL, '1'),
('6', '11', 'mcq', 'Langkah akhir make-up natural untuk bibir adalah...', 'Lipstik nude/peach dan lip balm', 'Lipstik merah pekat', 'Tanpa lipstik', 'Lip liner hitam', 'a', NULL, NULL, '2'),
('7', '12', 'mcq', 'Prinsip koreksi wajah: warna gelap berfungsi...', 'Menonjolkan bagian wajah', 'Membuat wajah terlihat besar', 'Mengecilkan/menenggelamkan bagian wajah', 'Menerangkan seluruh wajah', 'c', NULL, NULL, '0'),
('8', '12', 'mcq', 'Untuk wajah bulat, shading sebaiknya ditempatkan di...', 'Samping dahi, pipi, dan rahang', 'Tengah dahi', 'Ujung hidung', 'Cupid bow', 'a', NULL, NULL, '1'),
('9', '12', 'mcq', 'Kunci hasil contour yang natural adalah...', 'Garis tegas', 'Tanpa blending', 'Warna terang', 'Blending yang menyeluruh', 'd', NULL, NULL, '2'),
('10', '13', 'mcq', 'Daya tahan make-up wedding yang diharapkan adalah...', '1-2 jam', '8-12 jam', '24 jam', 'Tidak perlu tahan lama', 'b', NULL, NULL, '0'),
('11', '13', 'mcq', 'Untuk mencegah lipstik luntur saat makan, gunakan...', 'Lip liner penuh (full lip lining)', 'Lip gloss', 'Tanpa lipstik', 'Lip balm saja', 'a', NULL, NULL, '1'),
('12', '13', 'mcq', 'Persiapan kulit wajah untuk klien wedding sebaiknya dimulai...', 'H-1', 'Saat hari H', 'H-7', 'H-30 menit', 'c', NULL, NULL, '2'),
('13', '14', 'mcq', 'Sebelum praktek, MUA perlu melakukan konsultasi singkat tentang...', 'Harga rumah klien', 'Media sosial klien', 'Makanan favorit klien', 'Jenis kulit, alergi, tema acara, dan durasi', 'd', NULL, NULL, '0'),
('14', '14', 'mcq', 'Dokumentasi proses riasan untuk portofolio dilakukan...', 'Tanpa izin klien', 'Dengan izin klien', 'Diam-diam', 'Tidak boleh dilakukan sama sekali', 'b', NULL, NULL, '1'),
('15', '14', 'mcq', 'Peluang penyaluran klien terbuka bagi lulusan setelah...', 'Menyelesaikan seluruh modul dan lulus uji praktek', 'Membeli produk MCM', 'Memiliki banyak followers', 'Menjadi admin', 'a', NULL, NULL, '2'),
('16', '15', 'mcq', 'Apa istilah untuk rasa takut berbicara di depan umum?', 'Glossophobia', 'Claustrophobia', 'Acrophobia', 'Xenophobia', 'a', NULL, NULL, '0'),
('17', '15', 'mcq', 'Teknik pernapasan apa yang dianjurkan sebelum tampil untuk meredakan gugup?', 'Pernapasan dada cepat', 'Pernapasan diafragma', 'Volume suara', 'Jeda antar kalimat', 'b', NULL, NULL, '2'),
('18', '16', 'mcq', 'Mengapa kontak mata penting dalam public speaking?', 'Agar audiens takut', 'Membangun kepercayaan dan koneksi dengan audiens', 'Tidak ada pengaruhnya', 'Tidak ada pengaruhnya', 'b', NULL, NULL, '1'),
('19', '16', 'essay', 'Apa yang dimaksud dengan glossophobia, dan sebutkan minimal 2 penyebab umum seseorang mengalaminya?', '', '', '', '', NULL, 'Glossophobia adalah istilah untuk rasa takut atau cemas berlebihan saat berbicara di depan umum. Penyebab umumnya antara lain: takut dinilai/dikritik oleh audiens, kurang persiapan, trauma dari pengalaman buruk sebelumnya, dan kurangnya jam terbang atau latihan berbicara di depan umum.', NULL, '2'),
('20', '16', 'mcq', 'Siapa goat sepak bola?', 'Messi', 'Ronaldo', 'Neymar', 'Mbappe', 'a', NULL, NULL, '3');

-- --------------------------------------------------------

-- Struktur dari tabel `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `key` varchar(100) NOT NULL,
  `value` text DEFAULT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data untuk tabel `settings`
--

INSERT INTO `settings` (`key`, `value`) VALUES
('admin_address', 'Jl. Khp Hasan Mustopa No.57, Neglasari, Kec. Cibeunying Kaler, Kota Bandung, Jawa Barat 40122'),
('admin_email', 'info@mcm.com'),
('admin_whatsapp', '628978902864'),
('logo_url', 'https://res.cloudinary.com/ijpgxnt4/image/upload/v1787808470/mcm/logo/ibtezggiehzyadbgimvc.png'),
('maps_url', 'http://google.com/maps/place/LPK%2FLKP+Mitra+Cipta+Mandiri+(+kursus+make+up+,+kursus+kecantikan+,+kursus+salon+dan+kursus+Public+Speaking+)+di+Bandung/@-6.8994878,107.6423009,197m/data=!3m2!1e3!4b1!4m6!3m5!1s0x2e68e7da18125975:0xd96880f9f2089c69!8m2!3d-6.8994891!4d107.6429446!16s%2Fg%2F11ry25_5y8?entry=ttu&g_ep=EgoyMDI2MDgyNS4wIKXMDSoASAFQAw%3D%3D'),
('social_fb', '#sbjs'),
('social_ig', '#hsajhsjah'),
('social_tt', '#ahkshas');

-- --------------------------------------------------------

-- Struktur dari tabel `testimonials`
--

CREATE TABLE IF NOT EXISTS `testimonials` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `rating` tinyint(1) NOT NULL DEFAULT 5,
  `review` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `class_id` int(11) DEFAULT NULL,
  `graduation_year` varchar(10) DEFAULT NULL,
  `job` varchar(150) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `image_public_id` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `class_id` (`class_id`),
  CONSTRAINT `testimonials_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data untuk tabel `testimonials`
--

INSERT INTO `testimonials` (`id`, `user_id`, `name`, `rating`, `review`, `image`, `class_id`, `graduation_year`, `job`, `status`, `created_at`, `image_public_id`) VALUES
('1', NULL, 'Siti Rahma', '5', 'Pelatihan Make Up Artist di MCM sangat menyenangkan! Instrukturnya sabar dan materinya langsung bisa dipraktikkan.', NULL, '1', '2025', 'MUA Profesional', 'approved', '2026-08-15 10:22:22', NULL),
('5', NULL, 'Gilang Erlangga', '5', 'Bagus', 'uploads/testimonials/6a7fdfc5c989b.jpeg', '1', NULL, NULL, 'approved', '2026-08-15 10:40:53', NULL),
('6', '8', 'Alvina Marva Dearsy', '5', 'Waduh Bagus Banget Kelas Nya', NULL, '1', '2026', 'Asesor', 'approved', '2026-08-19 12:04:19', NULL),
('10', '9', 'Diza Syaichul Adilla', '5', 'Bagus', 'uploads/testimonials/6a8be144b6144.jpg', '9', '2026', 'Lowongan', 'approved', '2026-08-24 13:14:28', NULL);

-- --------------------------------------------------------

-- Struktur dari tabel `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `password`, `created_at`) VALUES
('5', 'Gilang Erlangga', 'gilangerlangga1306@gmail.com', '6281215614286', '$2y$12$rVWekUkJ8qhWDJ817Cs0WOu1MiNVJHgrCsaxN.h.W15qfE1V0vKni', '2026-08-17 19:20:06'),
('6', 'Abdul Rapli', 'abdulrapli05@gmail.com', '6285673493489', '$2y$10$l7sEzUNuHVAlidZ/ensLQ.nJ6tLKnrOmVJ./n1gpfY.05YPlXbKJe', '2026-08-19 11:35:24'),
('7', 'Abdul Rapli', 'abdulrapli@gmail.com', '6285673493489', '$2y$10$4hxocsOlVM79FqGX5p125ulqkRbk/sPNJ/rrEwdpcMirgoi3ZdGO.', '2026-08-19 11:37:42'),
('8', 'Alvina Marva Dearsy', 'Alvina99@gmail.com', '6285673493489', '$2y$10$RzEfMKn9Xk8lwgILl9NjkexNTcBtysYOD4Ai9DSxkeZ58HCSB.PC2', '2026-08-19 12:02:06'),
('9', 'Diza Syaichul Adilla', 'dizasyaichul7@gmail.com', '6289289829839', '$2y$10$njHK8Aiy2JTJK.dxzM6S4eTsgGW6ltA6PqpFsbFYEEEcRKWwlXXNy', '2026-08-21 12:36:29'),
('10', 'Diza', 'dizasyaichul8@gmail.com', '6289289829839', '$2y$10$hi30YklCemYPCnmm0Z3yWuJOLswUCY5L5S47iY38lpG/9jg9hjPz2', '2026-08-24 13:16:02');

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
