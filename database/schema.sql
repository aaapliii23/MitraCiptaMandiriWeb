-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 19 Agu 2026 pada 04.15
-- Versi server: 8.0.46-0ubuntu0.24.04.3
-- Versi PHP: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mcm_db`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admins`
--

CREATE TABLE IF NOT EXISTS `admins` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `admins`
--

INSERT IGNORE INTO `admins` (`id`, `username`, `password`, `created_at`) VALUES
(1, 'admin', '$2y$10$0z.Z5WjY0T/r7X8t5lO9l.O1uA8OaH7s0vM3N2f4E/7gR.aM5U.F6', '2026-08-14 04:19:29'),
(2, 'superadmin', '$2y$12$7LeSbK0vnibTerKEeGvCn.zjD53wMViSrg4BNk2YtknMrDbWag.F.', '2026-08-14 04:19:29');

-- --------------------------------------------------------

--
-- Struktur dari tabel `certificates`
--

CREATE TABLE IF NOT EXISTS `certificates` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `class_id` int NOT NULL,
  `cert_number` varchar(50) NOT NULL,
  `verify_token` varchar(64) NOT NULL DEFAULT '',
  `issued_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cert_number` (`cert_number`),
  UNIQUE KEY `uq_cert_user_class` (`user_id`,`class_id`),
  UNIQUE KEY `uq_cert_verify_token` (`verify_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `certificates`
--

INSERT IGNORE INTO `certificates` (`id`, `user_id`, `class_id`, `cert_number`, `verify_token`, `issued_at`) VALUES
(2, 5, 1, 'MCM-2026-0001', 'a1b2c3d4e5f6a7b8c9d0e1f2a3b4c5d6e7f8a9b0', '2026-08-17 12:29:43');

-- --------------------------------------------------------

--
-- Struktur dari tabel `certificate_templates`
--

CREATE TABLE IF NOT EXISTS `certificate_templates` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `class_id` int DEFAULT NULL,
  `layout` varchar(50) NOT NULL DEFAULT 'default',
  `bg_image` varchar(255) DEFAULT NULL,
  `accent_color` varchar(20) DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `certificate_templates`
--

INSERT IGNORE INTO `certificate_templates` (`id`, `name`, `class_id`, `layout`, `bg_image`, `accent_color`, `is_default`, `created_at`) VALUES
(1, 'Sertifikat Standar', NULL, 'default', NULL, '#1e40af', 1, '2026-08-19 02:37:16'),
(2, 'Sertifikat Elegant', NULL, 'elegant', NULL, '#7c3aed', 0, '2026-08-19 02:37:16'),
(3, 'Sertifikat Modern', NULL, 'modern', NULL, '#0ea5e9', 0, '2026-08-19 02:37:16'),
(4, 'Sertifikat Premium', NULL, 'premium', NULL, '#b45309', 0, '2026-08-19 02:37:16');

-- --------------------------------------------------------

--
-- Struktur dari tabel `certifications`
--

CREATE TABLE IF NOT EXISTS `certifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(150) NOT NULL,
  `description` text,
  `image` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `certifications`
--

INSERT IGNORE INTO `certifications` (`id`, `title`, `description`, `image`, `created_at`) VALUES
(1, 'Surat Keterangan Kemenkumham', 'Legalitas badan hukum Lembaga MCM terdaftar resmi di Kementerian Hukum dan HAM.', 'uploads/certs/sk_kemenkumham.jpg', '2026-08-14 07:16:05'),
(2, 'Sertifikat Akreditasi Lembaga', 'Sertifikat akreditasi lembaga pelatihan dari lembaga pengakreditasian resmi.', 'uploads/certs/akreditasi.jpg', '2026-08-14 07:16:05'),
(3, 'Piagam Penghargaan Pendidikan', 'Dokumen penghargaan atas kontribusi MCM dalam pendidikan dan pelatihan vokasi.', 'uploads/certs/piagam.jpg', '2026-08-14 07:16:05');

-- --------------------------------------------------------

--
-- Struktur dari tabel `chatbot_intents`
--

CREATE TABLE IF NOT EXISTS `chatbot_intents` (
  `id` int NOT NULL AUTO_INCREMENT,
  `intent` varchar(50) NOT NULL,
  `keywords` text NOT NULL,
  `reply` text NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `intent` (`intent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `chatbot_intents`
--

INSERT IGNORE INTO `chatbot_intents` (`id`, `intent`, `keywords`, `reply`, `enabled`, `created_at`) VALUES
(1, 'kursus', 'kursus, program, kelas, pelatihan, materi, modul, belajar', 'Program pelatihan MCM:\n- {classes}\n\nUntuk jadwal & harga, sebutkan program yang Anda minati.', 1, '2026-08-17 12:16:17'),
(2, 'jadwal', 'jadwal, mulai, kapan, tanggal, schedule', 'Jadwal pelatihan MCM tertera di masing-masing program di halaman Program. Untuk detail tanggal mulai, sebutkan program yang Anda minati.', 1, '2026-08-17 12:16:17'),
(3, 'harga', 'harga, biaya, bayar, investasi, berapa, price, tarif', 'Harga program MCM:\n- {prices}', 1, '2026-08-17 12:16:17'),
(4, 'pendaftaran', 'daftar, daftarkan, register, ikut, enroll', 'Cara daftar: pilih program di halaman Program, klik \'Daftar & Bayar\', isi data diri, lalu selesaikan pembayaran. Setelah lunas, akses LMS langsung terbuka.', 1, '2026-08-17 12:16:17'),
(5, 'kontak', 'alamat, lokasi, dimana, telepon, hubungi, kontak', 'Untuk info lebih lanjut, silakan hubungi admin via WhatsApp ini. Lokasi & detail MCM tersedia di halaman Tentang website kami.', 1, '2026-08-17 12:16:17'),
(6, 'penguji', 'penguji, asesor, guru, instruktur', 'Penguji MCM adalah asesor berpengalaman. Lihat latar belakang penguji di halaman Profil Penguji website kami.', 1, '2026-08-17 12:16:17');

-- --------------------------------------------------------

--
-- Struktur dari tabel `chat_messages`
--

CREATE TABLE IF NOT EXISTS `chat_messages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `wa_number` varchar(20) NOT NULL,
  `direction` enum('in','out') NOT NULL DEFAULT 'in',
  `message` text NOT NULL,
  `matched_intent` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `chat_messages`
--

INSERT IGNORE INTO `chat_messages` (`id`, `user_id`, `wa_number`, `direction`, `message`, `matched_intent`, `created_at`) VALUES
(43, NULL, '6281215614286', 'out', '*PEMBAYARAN LUNAS - MCM*\n\nHalo Gilang Erlangga, pembayaran Anda untuk *Make Up Artist* sudah kami terima. ✅\nNo. Order: ORD-6A82FC829F295-1786969218\nSilakan login ke LMS untuk mulai belajar: http://127.0.0.1:8000/lms/dashboard.php', 'pembayaran', '2026-08-17 12:20:20'),
(44, NULL, '6281215614286', 'out', 'hi', 'admin', '2026-08-17 12:25:44'),
(45, 5, 'web-86b23740a0a2', 'in', 'apa saja program pelatihan?', 'kursus', '2026-08-18 02:11:55'),
(46, NULL, 'web-86b23740a0a2', 'out', 'Program pelatihan MCM:\n- Make Up Artist\n- Tata Kecantikan\n- Pijat Terapis\n- Kursus Metodologi\n- Content Creator\n- Digital Marketing\n- Catering Pastry\n- Dasar-Dasar Pariwisata\n\nUntuk jadwal & harga, sebutkan program yang Anda minati.', 'kursus', '2026-08-18 02:11:55'),
(47, 5, 'web-86b23740a0a2', 'in', 'kapan jadwal pelatihan mulai?', 'jadwal', '2026-08-18 02:11:59'),
(48, NULL, 'web-86b23740a0a2', 'out', 'Jadwal pelatihan MCM tertera di masing-masing program di halaman Program. Untuk detail tanggal mulai, sebutkan program yang Anda minati.', 'jadwal', '2026-08-18 02:11:59'),
(49, NULL, '6281200000001', 'in', 'berapa harga kelas?', 'harga', '2026-08-18 02:30:37'),
(50, NULL, '6281200000001', 'out', 'Harga program MCM:\n- Make Up Artist - Rp 1.200.000\n- Tata Kecantikan - Rp 1.000.000\n- Pijat Terapis - Rp 1.100.000\n- Kursus Metodologi - Rp 950.000\n- Content Creator - Rp 1.300.000\n- Digital Marketing - Rp 1.400.000\n- Catering Pastry - Rp 1.150.000\n- Dasar-Dasar Pariwisata - Rp 900.000', 'harga', '2026-08-18 02:30:37'),
(51, NULL, 'web-d058d48f397b', 'in', 'berapa harga kelas?', 'harga', '2026-08-18 02:31:58'),
(52, NULL, 'web-d058d48f397b', 'out', 'Harga program MCM:\n- Make Up Artist - Rp 1.200.000\n- Tata Kecantikan - Rp 1.000.000\n- Pijat Terapis - Rp 1.100.000\n- Kursus Metodologi - Rp 950.000\n- Content Creator - Rp 1.300.000\n- Digital Marketing - Rp 1.400.000\n- Catering Pastry - Rp 1.150.000\n- Dasar-Dasar Pariwisata - Rp 900.000', 'harga', '2026-08-18 02:31:58');

-- --------------------------------------------------------

--
-- Struktur dari tabel `classes`
--

CREATE TABLE IF NOT EXISTS `classes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `start_date` date DEFAULT NULL,
  `category` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `description_online` text DEFAULT NULL,
  `description_offline` text DEFAULT NULL,
  `image` varchar(255) NOT NULL,
  `features` text NOT NULL,
  `price` int NOT NULL DEFAULT '500000',
  `price_online` int NOT NULL DEFAULT 0,
  `price_offline` int NOT NULL DEFAULT 0,
  `mode_available` enum('online','offline','both') NOT NULL DEFAULT 'both',
  `whatsapp_group_link` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `classes`
--

INSERT IGNORE INTO `classes` (`id`, `name`, `start_date`, `category`, `description`, `description_online`, `description_offline`, `image`, `features`, `price`, `price_online`, `price_offline`, `mode_available`, `whatsapp_group_link`, `created_at`) VALUES
(1, 'Make Up Artist', '2026-09-05', 'Kecantikan', 'Kuasai teknik make-up dari dasar hingga profesional. Program ini dirancang untuk mencetak Make-Up Artist (MUA) handal yang siap menangani klien rias panggung, wedding, hingga editorial.', 'Kuasai teknik make-up dari dasar hingga profesional. Program ini dirancang untuk mencetak Make-Up Artist (MUA) handal yang siap menangani klien rias panggung, wedding, hingga editorial.', 'Kuasai teknik make-up dari dasar hingga profesional. Program ini dirancang untuk mencetak Make-Up Artist (MUA) handal yang siap menangani klien rias panggung, wedding, hingga editorial.', 'uploads/classes/makeup.jpg', '[\"Pengenalan Alat & Produk Make-up\",\"Make-up Natural & Flawless\",\"Make-up Wedding & Formal\",\"Koreksi Bentuk Wajah\",\"Praktek Langsung dengan Model\",\"Sertifikat & Penyaluran Klien\"]', 1200000, 960000, 1200000, 'both', NULL, '2026-08-14 06:17:26'),
(2, 'Tata Kecantikan', '2026-09-12', 'Kecantikan', 'Pelajari perawatan kecantikan kulit, rambut, dan kuku mulai dari dasar hingga tingkat profesional. Cocok untuk Anda yang ingin berkarir di industri salon dan spa.', 'Pelajari perawatan kecantikan kulit, rambut, dan kuku mulai dari dasar hingga tingkat profesional. Cocok untuk Anda yang ingin berkarir di industri salon dan spa.', 'Pelajari perawatan kecantikan kulit, rambut, dan kuku mulai dari dasar hingga tingkat profesional. Cocok untuk Anda yang ingin berkarir di industri salon dan spa.', 'uploads/classes/kecantikan.jpg', '[\"Anatomi Kulit & Perawatan Dasar\",\"Perawatan Wajah (Facial)\",\"Hair Styling & Perawatan Rambut\",\"Manicure & Pedicure\",\"Manajemen Bisnis Salon\",\"Sertifikat Pelatihan\"]', 1000000, 800000, 1000000, 'both', NULL, '2026-08-14 06:17:26'),
(3, 'Pijat Terapis', '2026-09-19', 'Kesehatan', 'Kuasai anatomi tubuh, titik saraf, dan teknik pijat terapi yang benar. Program ini membekali Anda menjadi terapis profesional dengan standar kesehatan dan etika kerja.', 'Kuasai anatomi tubuh, titik saraf, dan teknik pijat terapi yang benar. Program ini membekali Anda menjadi terapis profesional dengan standar kesehatan dan etika kerja.', 'Kuasai anatomi tubuh, titik saraf, dan teknik pijat terapi yang benar. Program ini membekali Anda menjadi terapis profesional dengan standar kesehatan dan etika kerja.', 'uploads/classes/pijat.jpg', '[\"Anatomi Tubuh & Titik Saraf\",\"Teknik Pijat Relaksasi\",\"Teknik Pijat Terapi & Refleksi\",\"Pijat Tradisional Indonesia\",\"K3 & Etika Terapis\",\"Sertifikat Terapis\"]', 1100000, 880000, 1100000, 'both', NULL, '2026-08-14 06:17:26'),
(4, 'Kursus Metodologi', '2026-09-26', 'Metodologi', 'Program Training of Trainer (ToT) yang membekali Anda kemampuan menyusun kurikulum, menyampaikan materi, dan mengelola kelas pelatihan secara profesional.', 'Program Training of Trainer (ToT) yang membekali Anda kemampuan menyusun kurikulum, menyampaikan materi, dan mengelola kelas pelatihan secara profesional.', 'Program Training of Trainer (ToT) yang membekali Anda kemampuan menyusun kurikulum, menyampaikan materi, dan mengelola kelas pelatihan secara profesional.', 'uploads/classes/metodologi.jpg', '[\"Desain Kurikulum & Silabus\",\"Teknik Presentasi & Mengajar\",\"Evaluasi Pembelajaran\",\"Manajemen Kelas Pelatihan\",\"Praktek Microteaching\",\"Sertifikat Fasilitator\"]', 950000, 760000, 950000, 'both', NULL, '2026-08-14 06:17:26'),
(5, 'Content Creator', '2026-10-03', 'Digital', 'Belajar membuat konten menarik untuk media sosial dari riset ide, produksi video, hingga editing. Siapkan diri Anda menjadi kreator profesional yang menghasilkan.', 'Belajar membuat konten menarik untuk media sosial dari riset ide, produksi video, hingga editing. Siapkan diri Anda menjadi kreator profesional yang menghasilkan.', 'Belajar membuat konten menarik untuk media sosial dari riset ide, produksi video, hingga editing. Siapkan diri Anda menjadi kreator profesional yang menghasilkan.', 'uploads/classes/content.jpg', '[\"Strategi Konten & Riset Topik\",\"Videografi & Fotografi Dasar\",\"Editing Video (CapCut/PC)\",\"Copywriting & Caption\",\"Manajemen Akun & Algoritma\",\"Monetisasi Konten\"]', 1300000, 1040000, 1300000, 'both', NULL, '2026-08-14 06:17:26'),
(6, 'Digital Marketing', '2026-10-10', 'Digital', 'Kuasai strategi pemasaran digital dari SEO, iklan berbayar, hingga analitik. Program lengkap untuk Anda yang ingin mengembangkan bisnis atau berkarir sebagai digital marketer.', 'Kuasai strategi pemasaran digital dari SEO, iklan berbayar, hingga analitik. Program lengkap untuk Anda yang ingin mengembangkan bisnis atau berkarir sebagai digital marketer.', 'Kuasai strategi pemasaran digital dari SEO, iklan berbayar, hingga analitik. Program lengkap untuk Anda yang ingin mengembangkan bisnis atau berkarir sebagai digital marketer.', 'uploads/classes/digimar.jpg', '[\"Dasar-Dasar Digital Marketing\",\"SEO & SEM Dasar\",\"Iklan Facebook/Instagram Ads\",\"Google Ads & Analytics\",\"Email & WhatsApp Marketing\",\"Analitik & Laporan Kinerja\"]', 1400000, 1120000, 1400000, 'both', NULL, '2026-08-14 06:17:26'),
(7, 'Catering Pastry', '2026-10-17', 'Kuliner', 'Pelajari seni membuat kue dan pastry dari dasar hingga dekorasi tingkat profesional. Siapkan bisnis kue Anda dengan standar dapur yang higienis dan menguntungkan.', 'Pelajari seni membuat kue dan pastry dari dasar hingga dekorasi tingkat profesional. Siapkan bisnis kue Anda dengan standar dapur yang higienis dan menguntungkan.', 'Pelajari seni membuat kue dan pastry dari dasar hingga dekorasi tingkat profesional. Siapkan bisnis kue Anda dengan standar dapur yang higienis dan menguntungkan.', 'uploads/classes/pastry.jpg', '[\"Dasar-Dasar Pastry & Baking\",\"Roti & Kue Kering\",\"Kue Basah & Hidangan Penutup\",\"Teknik Dekorasi Kue\",\"Higienitas & Manajemen Dapur\",\"Manajemen Usaha Kue\"]', 1150000, 920000, 1150000, 'both', NULL, '2026-08-14 06:17:26'),
(8, 'Dasar-Dasar Pariwisata', '2026-10-24', 'Pariwisata', 'Kenali seluk-beluk industri pariwisata dari pemandu wisata, hospitality, hingga pengelolaan usaha wisata lokal. Pintu masuk menuju karir di sektor pariwisata.', 'Kenali seluk-beluk industri pariwisata dari pemandu wisata, hospitality, hingga pengelolaan usaha wisata lokal. Pintu masuk menuju karir di sektor pariwisata.', 'Kenali seluk-beluk industri pariwisata dari pemandu wisata, hospitality, hingga pengelolaan usaha wisata lokal. Pintu masuk menuju karir di sektor pariwisata.', 'uploads/classes/pariwisata.jpg', '[\"Pengenalan Industri Pariwisata\",\"Pemandu Wisata & Hospitality\",\"Komunikasi & Bahasa Asing Dasar\",\"Manajemen Perjalanan Wisata\",\"Homestay & Usaha Wisata Lokal\",\"Praktek Lapangan\"]', 900000, 720000, 900000, 'both', NULL, '2026-08-14 07:09:02');

-- --------------------------------------------------------

--
-- Struktur dari tabel `class_categories`
--

CREATE TABLE IF NOT EXISTS `class_categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `class_categories`
--

INSERT IGNORE INTO `class_categories` (`id`, `name`, `slug`, `created_at`) VALUES
(1, 'Kecantikan', 'kecantikan', '2026-08-14 06:17:03'),
(2, 'Kesehatan', 'kesehatan', '2026-08-14 06:17:03'),
(3, 'Metodologi', 'metodologi', '2026-08-14 06:17:03'),
(5, 'Kuliner', 'kuliner', '2026-08-14 06:17:03'),
(6, 'Pariwisata', 'pariwisata', '2026-08-14 06:17:03'),
(7, 'Digital Marketing', 'digital_marketing', '2026-08-14 06:17:16');

-- --------------------------------------------------------

--
-- Struktur dari tabel `enrollments`
--

CREATE TABLE IF NOT EXISTS `enrollments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `class_id` int NOT NULL,
  `order_id` int NOT NULL,
  `enrolled_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_id` (`order_id`),
  UNIQUE KEY `uq_enroll_user_class` (`user_id`,`class_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `enrollments`
--

INSERT IGNORE INTO `enrollments` (`id`, `user_id`, `class_id`, `order_id`, `enrolled_at`) VALUES
(4, 5, 1, 8, '2026-08-17 12:20:20'),
(8, 11, 5, 19, '2026-08-19 03:17:44'),
(9, 11, 3, 20, '2026-08-19 03:19:39');

-- --------------------------------------------------------

--
-- Struktur dari tabel `finance_transactions`
--

CREATE TABLE IF NOT EXISTS `finance_transactions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `type` enum('in','out') NOT NULL,
  `category` varchar(50) NOT NULL,
  `description` text,
  `amount` int NOT NULL,
  `transaction_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `gallery`
--

CREATE TABLE IF NOT EXISTS `gallery` (
  `id` int NOT NULL AUTO_INCREMENT,
  `category` varchar(50) NOT NULL,
  `title` varchar(100) NOT NULL,
  `image` varchar(255) NOT NULL,
  `show_on_home` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `gallery`
--

INSERT IGNORE INTO `gallery` (`id`, `category`, `title`, `image`, `show_on_home`, `created_at`) VALUES
(1, 'pelatihan_tim', 'Pelatihan & Diskusi Tim', 'uploads/gallery/6a86a1198143c_0.jpeg', 1, '2026-08-14 03:05:18'),
(2, 'tata_rias', 'Kelas Tata Rias', 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80', 1, '2026-08-14 03:05:18'),
(3, 'pijat', 'Pelatihan Pijat & Refleksi', 'https://images.unsplash.com/photo-1544161515-4ab6ce6db874?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80', 1, '2026-08-14 03:05:18'),
(4, 'barber', 'Praktek Barbershop', 'https://images.unsplash.com/photo-1503951914875-452162b0f3f1?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80', 0, '2026-08-14 03:05:18'),
(5, 'kuliner', 'Kelas Memasak & Catering', 'https://images.unsplash.com/photo-1555244162-803834f70033?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80', 1, '2026-08-14 06:52:38'),
(28, 'metodologi', 'Meto', 'uploads/gallery/6a86a185756d7_0.jpeg', 1, '2026-08-14 07:40:13');

-- --------------------------------------------------------

--
-- Struktur dari tabel `instructors`
--

CREATE TABLE IF NOT EXISTS `instructors` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `category` varchar(100) NOT NULL DEFAULT '',
  `specialization` varchar(150) NOT NULL,
  `bio` text DEFAULT NULL,
  `certifications` text DEFAULT NULL,
  `image` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `instructors`
--

INSERT IGNORE INTO `instructors` (`id`, `name`, `category`, `specialization`, `bio`, `certifications`, `image`, `created_at`) VALUES
(1, 'Bunga Lestari, S.Pd., C.MUA', 'Kecantikan', 'Make Up Artist & Bridal Specialist', 'Praktisi MUA profesional lebih dari 10 tahun pengalaman merias wedding tradisional, modern, editorial, dan panggung hiburan.', 'BNSP Level 4 Tata Rias, Certified International MUA, Asesor Kompetensi LSP', 'uploads/instructors/bunga.jpg', '2026-08-14 07:16:05'),
(2, 'Jessica Angelina, C.E.', 'Kecantikan', 'Senior Esthetician & Skin Care Specialist', 'Pakar estetika kulit dan perawatan wajah medis ringan dengan sertifikasi internasional CIDESCO.', 'CIDESCO International Diploma, BNSP Perawatan Kulit', 'assets/img/logo.png', '2026-08-19 02:20:00'),
(3, 'Maya Kartika', 'Kecantikan', 'Professional Hair Stylist & Colorist', 'Hair stylist salon ternama di Bandung, spesialis teknik rebonding, smoothing, coloring balayage, dan blow styling modern.', 'Certified Pivot Point International, Asesor Tata Kecantikan Rambut', 'assets/img/logo.png', '2026-08-19 02:20:00'),
(4, 'Rani Oktavia', 'Kecantikan', 'Nail Art & Eyelash Extension Artist', 'Trainer kecantikan kuku dan bulu mata dengan teknik semi-permanen higienis dan standar salon kecantikan Jepang.', 'Certified Russian Volume Lash, Professional Nail Art Specialist', 'assets/img/logo.png', '2026-08-19 02:20:00'),
(5, 'H. Supardi, A.Md.Fis', 'Kesehatan', 'Fisioterapi & Pijat Pengobatan Tradisional', 'Praktisi terapi pijat dan rehabilitasi fisik berpengalaman lebih dari 18 tahun, menguasai titik saraf motorik dan relaksasi.', 'Surat Izin Praktik Fisioterapi (SIPF), BNSP Pijat Tradisional Indonesia', 'assets/img/logo.png', '2026-08-19 02:20:00'),
(6, 'dr. Hendra Gunawan, Sp.Ak', 'Kesehatan', 'Akupunktur Medis & Titik Refleksi Tubuh', 'Dokter spesialis akupunktur yang memadukan teknik refleksi timur kuno dengan sains kedokteran modern untuk kebugaran tubuh.', 'Spesialis Akupunktur Medik IDI, Asesor Kesehatan Tradisional Kemenkes', 'assets/img/logo.png', '2026-08-19 02:20:00'),
(7, 'Siti Nurhaliza, S.Tr.Kes', 'Kesehatan', 'Spa Therapy & Aromaterapi Herbal', 'Konsultan spa hotel berbintang dan instruktur teknik massage lulur, body scrub, serta peracikan aromaterapi alami.', 'BNSP Spa Therapist Level 3, Certified Wellness Herbalist', 'assets/img/logo.png', '2026-08-19 02:20:00'),
(8, 'Bambang Prakoso, S.Or', 'Kesehatan', 'Sports Massage & Cedera Otot Atlit', 'Mantan fisioterapis tim olahraga profesional, ahli dalam penanganan ketegangan otot, trigger point, dan pemulihan performa tubuh.', 'Certified Sports Massage Specialist, Lisensi Pelatih Kebugaran', 'assets/img/logo.png', '2026-08-19 02:20:00'),
(9, 'Dr. Ir. H. Ahmad Jaelani, M.Pd', 'Metodologi', 'Master Trainer BNSP & Microteaching', 'Dosen dan instruktur senior bidang pedagogik vokasi, spesialis penyusunan silabus kompetensi kerja dan teknik pengajaran interaktif.', 'Master Asesor BNSP, Certified Master Trainer of Trainer (ToT)', 'assets/img/logo.png', '2026-08-19 02:20:00'),
(10, 'Dra. Ratna Kusuma, M.M.', 'Metodologi', 'Pengembangan Kurikulum Vokasi Berbasis SKKNI', 'Konsultan kurikulum pendidikan non-formal dan pelatihan kerja dengan pengalaman membina puluhan LPK terakreditasi nasional.', 'Asesor SKKNI Kemnaker, Certified Instructional Designer', 'assets/img/logo.png', '2026-08-19 02:20:00'),
(11, 'Fajar Nugraha, S.Psi., C.HRM', 'Metodologi', 'Komunikasi Publik & Manajemen Kelas Pelatihan', 'Praktisi psikologi industri yang berpengalaman melatih ribuan fasilitator dalam public speaking, Ice Breaking, dan dinamika kelompok.', 'Certified Facilitator BNSP, Certified NLP Practitioner', 'assets/img/logo.png', '2026-08-19 02:20:00'),
(12, 'Dewi Anggraini, S.I.Kom', 'Digital', 'Digital Strategist & Meta Ads Performance', 'Spesialis periklanan berbayar Facebook & Instagram Ads dengan rekam jejak mengelola budget iklan digital brand nasional.', 'Meta Certified Media Buying Professional, Google Ads Search Certification', 'uploads/instructors/6a7ec15a2ba99.jpeg', '2026-08-14 07:16:05'),
(13, 'Rizky Ramadhan', 'Digital', 'Senior Video Creator & CapCut Specialist', 'Content creator aktif dengan 500k+ followers, spesialis short-form storytelling video, color grading smartphone, dan audio mixing.', 'Certified Digital Content Producer, Adobe Premiere Certified Professional', 'assets/img/logo.png', '2026-08-19 02:20:00'),
(14, 'Dimas Pratama, S.Kom', 'Digital', 'SEO Specialist & Google Analytics Consultant', 'Konsultan optimasi mesin pencari (SEO On-Page & Off-Page) dan data analytics untuk mendongkrak penjualan organik bisnis online.', 'Google Analytics Individual Qualification (GA4), Hubspot Inbound Marketing', 'assets/img/logo.png', '2026-08-19 02:20:00'),
(15, 'Amanda Putri, B.A.', 'Digital', 'Social Media Organic & TikTok Live Strategy', 'Social media manager dan strategist live commerce TikTok Shop yang sukses meningkatkan traffic dan engagement akun UMKM.', 'Certified Social Media Strategist, TikTok E-commerce Specialist', 'assets/img/logo.png', '2026-08-19 02:20:00'),
(16, 'Chef Agus Wijaya', 'Kuliner', 'Executive Pastry Chef & Artisan Bakery', 'Head Baker bakery ternama, menguasai teknik pembuatan sourdough, croissant lamination, pastry klasik Perancis, dan aneka roti komersial.', 'BNSP Baker Profesional Level 4, Certified French Pastry Chef', 'uploads/instructors/6a7ecd58b66ce.jpeg', '2026-08-14 07:16:05'),
(17, 'Chef Nadia Salsabila', 'Kuliner', 'Modern Cake Decoration & Fondant Art', 'Spesialis wedding cake 3 dimensi, butter cream sculpture, serta dekorasi kue ulang tahun tematik tingkat mahir.', 'Certified Cake Decorator Wilton, Juri Kompetisi Kuliner Kreatif', 'assets/img/logo.png', '2026-08-19 02:20:00'),
(18, 'Chef Rudi Hartono, S.Tr.Par', 'Kuliner', 'Manajemen Usaha Catering & Sanitasi Dapur', 'Konsultan bisnis kuliner dan pemilik jasa catering event besar, ahli dalam standardisasi resep massal dan keamanan pangan.', 'Sertifikasi HACCP & Higiene Pangan, Asesor BNSP Tata Boga', 'assets/img/logo.png', '2026-08-19 02:20:00'),
(19, 'Bayu Samudra, S.Par', 'Pariwisata', 'Pemandu Wisata & Tour Leader Internasional', 'Pemandu wisata berpengalaman memimpin grup tur mancanegara dan domestik, menguasai teknik guiding, storytelling sejarah, dan etika wisata.', 'Lisensi HPI (Himpunan Pramuwisata Indonesia), Certified Eco-Tourism Guide', 'assets/img/logo.png', '2026-08-19 02:20:00'),
(20, 'Citra Kirana, M.Par', 'Pariwisata', 'Hospitality & Manajemen Layanan Homestay', 'Praktisi industri perhotelan bintang 5 dan trainer pelayanan prima (Service Excellence) serta pengelolaan desa wisata berkelanjutan.', 'Certified Hospitality Educator (CHE), BNSP Front Office & Housekeeping', 'assets/img/logo.png', '2026-08-19 02:20:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `materials`
--

CREATE TABLE IF NOT EXISTS `materials` (
  `id` int NOT NULL AUTO_INCREMENT,
  `class_id` int NOT NULL,
  `title` varchar(150) NOT NULL,
  `type` enum('video','pdf','text') NOT NULL DEFAULT 'text',
  `content` text NOT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `materials`
--

INSERT IGNORE INTO `materials` (`id`, `class_id`, `title`, `type`, `content`, `sort_order`, `created_at`) VALUES
(9, 1, 'Modul 1', 'pdf', 'uploads/materials/6a82fe8311488.pdf', 0, '2026-08-17 12:28:51'),
(10, 1, 'Modul 2 - Pengenalan Alat & Produk Make-up', 'text', 'Mengenal alat dan produk make-up merupakan fondasi penting sebelum mempraktikkan teknik rias. Seorang MUA profesional harus memahami fungsi setiap alat dan kandungan setiap produk agar hasil riasan optimal dan aman untuk kulit klien.\n\nAlat-alat dasar yang wajib dimiliki:\n- Kuas make-up (foundation, blush, eyeshadow, lip brush) — pilih bulu sintetis yang halus dan mudah dibersihkan.\n- Spons beauty blender untuk hasil foundation yang merata dan natural.\n- Sikat alis dan sisir alis untuk membentuk serta merapikan alis.\n- Pinset dan gunting bulu alis untuk grooming.\n- Palet warna dan cermin dengan lampu agar warna terlihat akurat.\n\nProduk yang perlu dikuasai sesuai urutan pemakaian:\n1. Primer — menyamarkan pori dan membuat make-up tahan lama.\n2. Foundation / BB cream — meratakan warna kulit.\n3. Concealer — menutup noda, bekas jerawat, dan lingkaran gelap mata.\n4. Bedak tabur / pressed — mengunci riasan.\n5. Blush, bronzer, dan highlighter — memberi dimensi pada wajah.\n6. Eyeshadow, eyeliner, maskara — mempertegas mata.\n7. Pensil alis dan brow gel — membingkai wajah.\n8. Lipstik, lip liner, dan lip gloss — menyempurnakan bibir.\n\nSebelum digunakan pada klien, selalu lakukan uji coba produk pada area kecil kulit dan pastikan alat bersih. Kebersihan alat adalah standar mutlak dalam industri make-up.', 2, '2026-08-17 12:31:47'),
(11, 1, 'Modul 3 - Make-up Natural & Flawless', 'text', 'Make-up natural bertujuan menonjolkan kecantikan alami tanpa terlihat tebal. Kunci utama adalah pemilihan warna yang mendekati warna kulit (skin tone) dan teknik layering yang tipis namun berlapis.\n\nLangkah make-up natural:\n1. Bersihkan wajah lalu aplikasikan pelembap dan primer ringan.\n2. Aplikasikan foundation tipis dengan spons, mulai dari area tengah wajah lalu ratakan ke tepi.\n3. Tutup area bermasalah dengan concealer secukupnya.\n4. Set dengan bedak tipis agar riasan matte dan tahan lama.\n5. Beri blush warna rose/pink lembut di pipi, bronzer ringan di bawah tulang pipi, dan highlighter halus di tulang pipi, cupid bow, dan ujung hidung.\n6. Rias mata dengan warna netral (brown, beige, peach). Gunakan maskara untuk membuka pandangan mata.\n7. Rapikan alis dengan brow gel bening.\n8. Selesaikan dengan lipstik nude/peach dan lip balm agar bibir tampak sehat.\n\nKesalahan umum yang harus dihindari:\n- Foundation terlalu tebal atau warna tidak sesuai (mismatch).\n- Blush terlalu pekat sehingga wajah tampak seperti boneka.\n- Highlighter berlebihan di bawah cahaya lampu.\n- Tidak menyet make-up sehingga cepat luntur.\n\nLatihan rutin dengan berbagai bentuk wajah akan membuat teknik ini semakin presisi.', 3, '2026-08-17 12:31:47'),
(12, 1, 'Modul 4 - Koreksi Bentuk Wajah', 'text', 'Koreksi bentuk wajah adalah teknik sculpting menggunakan warna gelap, netral, dan terang untuk menciptakan ilusi bentuk wajah yang proporsional. Prinsipnya: warna gelap mengecilkan/menenggelamkan, warna terang menonjolkan.\n\nBentuk wajah dan strategi koreksi:\n- Wajah bulat: shading di samping dahi, pipi, dan rahang; highlight di tengah dahi dan dagu agar wajah tampak lonjong.\n- Wajah persegi: shading di sudut rahang dan sudut dahi; highlight di tengah dahi, pipi, dan dagu untuk melunakkan sudut.\n- Wajah panjang: shading di garis rambut dan bawah dagu; highlight di dahi dan tulang pipi.\n- Wajah oval: relatif proporsional, cukup contour ringan di bawah tulang pipi.\n- Wajah hati: shading di pelipis dan rahang bawah; highlight di tengah dahi dan dagu.\n\nTeknik dasar:\n- Contour: gunakan warna 2 tingkat lebih gelap dari kulit, aplikasikan dengan kuas kecil lalu blend menyeluruh tanpa garis tegas.\n- Highlight: warna 1-2 tingkat lebih terang untuk area yang ingin ditonjolkan.\n- Blush: aplikasikan mengikuti arah tulang pipi untuk memberi kesegaran.\n\nKunci hasil natural adalah blending. Selalu cek hasil di bawah pencahayaan yang berbeda sebelum riasan dianggap selesai.', 4, '2026-08-17 12:31:47'),
(13, 1, 'Modul 5 - Make-up Wedding & Formal', 'text', 'Make-up wedding menuntut daya tahan lama (8-12 jam), tahan air, dan fotogenic di bawah cahaya kamera. Standar hasilnya flawless di mata langsung maupun melalui lensa.\n\nPersiapan khusus:\n- Kulit wajah dipersiapkan sejak H-7: perawatan dasar, istirahat cukup, dan hindari produk baru yang bisa menimbulkan iritasi.\n- Gunakan primer yang mengunci minyak dan produk long-wear/waterproof.\n\nTeknik riasan wedding:\n1. Base yang lebih solid: foundation full coverage + setting spray berlapis.\n2. Mata menjadi fokus utama: eyeshadow tahan lama dengan teknik cut crease atau soft smokey sesuai permintaan klien.\n3. Gunakan bulu mata palsu untuk kesan mata lebih besar dan dramatis.\n4. Alis yang tegas namun natural untuk membingkai wajah.\n5. Lipstik matte tahan lama dengan lip liner penuh (full lip lining) agar tidak luntur saat makan.\n6. Selesaikan dengan setting spray dan sisipkan bedak touch-up untuk sesi foto.\n\nPerbedaan dengan make-up natural: intensitas lebih tinggi, daya tahan lebih lama, dan detail lebih presisi. Pastikan diskusi dengan klien tentang tema dan model gaun sebelum eksekusi.', 5, '2026-08-17 12:31:47'),
(14, 1, 'Modul 6 - Praktek dengan Model & Penyaluran Klien', 'text', 'Modul ini melatih kemampuan menghadapi klien langsung dan membangun portofolio profesional. Kepercayaan diri, komunikasi, dan manajemen waktu adalah skill yang setara pentingnya dengan teknik rias.\n\nKesiapan sebelum praktek:\n- Bawa kit make-up lengkap yang sudah disterilkan dan pastikan kondisi produk layak pakai.\n- Siapkan moodboard/referensi sesuai permintaan klien.\n- Lakukan konsultasi singkat: jenis kulit, alergi, tema acara, dan durasi yang dibutuhkan.\n\nSaat praktek:\n- Jaga etika dan sopan santun; jelaskan langkah yang sedang dikerjakan.\n- Dokumentasikan proses dan hasil dengan kamera (dengan izin klien) untuk portofolio.\n- Lakukan touch-up terakhir sebelum klien meninggalkan kursi.\n\nSetelah menyelesaikan seluruh modul dan lulus uji praktek, lulusan MCM berkesempatan mendapatkan penyaluran klien melalui jaringan lembaga. Portofolio yang rapi, harga yang kompetitif, dan reputasi yang baik akan membuka peluang klien berulang.', 6, '2026-08-17 12:31:47');

-- --------------------------------------------------------

--
-- Struktur dari tabel `material_progress`
--

CREATE TABLE IF NOT EXISTS `material_progress` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `material_id` int NOT NULL,
  `completed` tinyint(1) NOT NULL DEFAULT '0',
  `completed_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_progress_user_material` (`user_id`,`material_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `material_progress`
--

INSERT IGNORE INTO `material_progress` (`id`, `user_id`, `material_id`, `completed`, `completed_at`) VALUES
(12, 5, 11, 1, '2026-08-17 12:49:29'),
(13, 5, 12, 1, '2026-08-18 06:41:56'),
(14, 5, 13, 1, '2026-08-18 06:43:06'),
(15, 5, 14, 1, '2026-08-18 06:43:35');

-- --------------------------------------------------------

--
-- Struktur dari tabel `orders`
--

CREATE TABLE IF NOT EXISTS `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `order_number` varchar(50) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_phone` varchar(20) NOT NULL,
  `customer_email` varchar(100) DEFAULT NULL,
  `customer_address` text,
  `customer_institution` varchar(100) DEFAULT NULL,
  `class_id` int NOT NULL,
  `class_mode` enum('online','offline') NOT NULL DEFAULT 'offline',
  `instructor_id` int DEFAULT NULL,
  `amount` int NOT NULL,
  `status` enum('pending','confirmed','cancelled') DEFAULT 'pending',
  `payment_status` enum('unpaid','pending','paid','failed','expired') DEFAULT 'unpaid',
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_gateway_ref` varchar(100) DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_number` (`order_number`),
  KEY `class_id` (`class_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `orders`
--

INSERT IGNORE INTO `orders` (`id`, `user_id`, `order_number`, `customer_name`, `customer_phone`, `customer_email`, `customer_address`, `customer_institution`, `class_id`, `class_mode`, `instructor_id`, `amount`, `status`, `payment_status`, `payment_method`, `payment_gateway_ref`, `paid_at`, `created_at`) VALUES
(2, NULL, 'ORD-6A82EEF5D9FDB-1786965749', 'Testi Payment E2E', '6281234567890', 'payment.e2e@test.com', 'Jl. Uji No.1', 'PT Uji', 1, 'offline', NULL, 1200000, 'confirmed', 'paid', 'bank_transfer', 'MOCK-ORD-6A82EEF5D9FDB-1786965749', '2026-08-17 11:22:31', '2026-08-17 11:22:29'),
(3, NULL, 'ORD-6A82EF26B8BFF-1786965798', 'Testi Gagal', '628987654321', 'gagal@test.com', 'Jl. Gagal 1', '-', 2, 'offline', NULL, 1000000, 'pending', 'failed', 'bank_transfer', 'MOCK-ORD-6A82EF26B8BFF-1786965798', NULL, '2026-08-17 11:23:18'),
(8, 5, 'ORD-6A82FC829F295-1786969218', 'Gilang Erlangga', '6281215614286', 'gilangerlangga1306@gmail.com', 'Legok Bandung Dua', '-', 1, 'offline', NULL, 1200000, 'confirmed', 'paid', 'bank_transfer', 'MOCK-ORD-6A82FC829F295-1786969218', '2026-08-17 12:20:20', '2026-08-17 12:20:18'),
(13, NULL, 'ORD-6A851E5BA0155-1787108955', 'Gilang Erlangga', '6281215614286', 'gilangerlangga1306@gmail.com', 'Legok Bandung Dua', '-', 5, 'offline', 3, 1300000, 'pending', 'failed', 'bank_transfer', 'MOCK-ORD-6A851E5BA0155-1787108955', NULL, '2026-08-19 03:09:15'),
(14, NULL, 'ORD-6A851EAED3409-1787109038', 'Gilang Erlangga', '6281215614286', 'gilangerlangga1306@gmail.com', 'Legok Bandung Dua', '-', 3, 'offline', 2, 1100000, 'confirmed', 'paid', 'bank_transfer', 'MOCK-ORD-6A851EAED3409-1787109038', '2026-08-19 03:10:40', '2026-08-19 03:10:38'),
(15, NULL, 'ORD-6A851EFC73071-1787109116', 'Gilang Erlangga', '6281215614286', 'gilangerlangga1306@gmail.com', 'Legok Bandung Dua', '-', 3, 'offline', 2, 1100000, 'confirmed', 'paid', 'bank_transfer', 'MOCK-ORD-6A851EFC73071-1787109116', '2026-08-19 03:12:00', '2026-08-19 03:11:56'),
(19, 11, 'ORD-6A852055BB346-1787109461', 'Gilang Erlangga', '6281215614286', 'gilangerlangga1306@gmail.com', 'Legok Bandung Dua', '-', 5, 'offline', 3, 1300000, 'confirmed', 'paid', 'bank_transfer', 'MOCK-ORD-6A852055BB346-1787109461', '2026-08-19 03:17:44', '2026-08-19 03:17:41'),
(20, 11, 'ORD-6A8520CA0DB57-1787109578', 'Gilang Erlangga', '6281215614286', 'gilangerlangga1306@gmail.com', 'Legok Bandung Dua', '-', 3, 'offline', NULL, 1100000, 'confirmed', 'paid', 'bank_transfer', 'MOCK-ORD-6A8520CA0DB57-1787109578', '2026-08-19 03:19:39', '2026-08-19 03:19:38');

-- --------------------------------------------------------

--
-- Struktur dari tabel `quiz_attempts`
--

CREATE TABLE IF NOT EXISTS `quiz_attempts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `material_id` int NOT NULL,
  `score` int NOT NULL,
  `total` int NOT NULL,
  `passed` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_material` (`user_id`,`material_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `quiz_attempts`
--

INSERT IGNORE INTO `quiz_attempts` (`id`, `user_id`, `material_id`, `score`, `total`, `passed`, `created_at`) VALUES
(5, 5, 11, 0, 3, 0, '2026-08-17 12:49:05'),
(6, 5, 11, 1, 3, 0, '2026-08-17 12:49:09'),
(7, 5, 11, 0, 3, 0, '2026-08-17 12:49:13'),
(8, 5, 11, 1, 3, 0, '2026-08-17 12:49:16'),
(9, 5, 11, 1, 3, 0, '2026-08-17 12:49:18'),
(10, 5, 11, 2, 3, 0, '2026-08-17 12:49:20'),
(11, 5, 11, 2, 3, 0, '2026-08-17 12:49:24'),
(12, 5, 11, 2, 3, 0, '2026-08-17 12:49:27'),
(13, 5, 11, 3, 3, 1, '2026-08-17 12:49:29'),
(19, 5, 13, 2, 3, 0, '2026-08-18 06:42:49'),
(20, 5, 13, 1, 3, 0, '2026-08-18 06:42:53'),
(21, 5, 13, 2, 3, 0, '2026-08-18 06:43:02'),
(22, 5, 13, 3, 3, 1, '2026-08-18 06:43:06'),
(28, 5, 11, 3, 3, 1, '2026-08-19 02:47:16'),
(29, 5, 12, 3, 3, 1, '2026-08-19 02:47:16'),
(30, 5, 13, 3, 3, 1, '2026-08-19 02:47:16'),
(31, 5, 14, 3, 3, 1, '2026-08-19 02:47:16');

-- --------------------------------------------------------

--
-- Struktur dari tabel `quiz_questions`
--

CREATE TABLE IF NOT EXISTS `quiz_questions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `material_id` int NOT NULL,
  `question_type` enum('mcq','essay') NOT NULL DEFAULT 'mcq',
  `question` text NOT NULL,
  `option_a` varchar(255) NOT NULL,
  `option_b` varchar(255) NOT NULL,
  `option_c` varchar(255) NOT NULL,
  `option_d` varchar(255) NOT NULL,
  `correct_option` enum('a','b','c','d') DEFAULT NULL,
  `essay_answer` text,
  `explanation` text,
  `sort_order` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `material_id` (`material_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `quiz_progress` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `question_id` INT NOT NULL,
  `is_correct` TINYINT(1) NOT NULL DEFAULT 0,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_user_question` (`user_id`,`question_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `quiz_questions`
--

INSERT IGNORE INTO `quiz_questions` (`id`, `material_id`, `question_type`, `question`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `essay_answer`, `sort_order`) VALUES
(1, 10, 'mcq', 'Untuk hasil foundation yang merata dan natural, alat yang paling tepat digunakan adalah...', 'Spons beauty blender', 'Kuas eyeshadow', 'Pinset', 'Gunting bulu alis', 'a', NULL, 0),
(2, 10, 'mcq', 'Produk yang berfungsi menyamarkan pori dan membuat make-up tahan lama adalah...', 'Foundation', 'Primer', 'Maskara', 'Lip gloss', 'b', NULL, 1),
(3, 10, 'mcq', 'Urutan pemakaian produk setelah foundation dan sebelum bedak tabur adalah...', 'Primer', 'Eyeliner', 'Concealer', 'Blush', 'c', NULL, 2),
(4, 11, 'mcq', 'Kunci utama make-up natural adalah...', 'Menggunakan warna gelap tebal', 'Warna mendekati skin tone dan layering tipis', 'Highlighter berlebihan', 'Foundation sangat tebal', 'b', NULL, 0),
(5, 11, 'mcq', 'Kesalahan umum yang harus dihindari dalam make-up natural adalah...', 'Blush lembut', 'Bedak tipis', 'Warna netral', 'Foundation terlalu tebal atau warna mismatch', 'd', NULL, 1),
(6, 11, 'mcq', 'Langkah akhir make-up natural untuk bibir adalah...', 'Lipstik nude/peach dan lip balm', 'Lipstik merah pekat', 'Tanpa lipstik', 'Lip liner hitam', 'a', NULL, 2),
(7, 12, 'mcq', 'Prinsip koreksi wajah: warna gelap berfungsi...', 'Menonjolkan bagian wajah', 'Membuat wajah terlihat besar', 'Mengecilkan/menenggelamkan bagian wajah', 'Menerangkan seluruh wajah', 'c', NULL, 0),
(8, 12, 'mcq', 'Untuk wajah bulat, shading sebaiknya ditempatkan di...', 'Samping dahi, pipi, dan rahang', 'Tengah dahi', 'Ujung hidung', 'Cupid bow', 'a', NULL, 1),
(9, 12, 'mcq', 'Kunci hasil contour yang natural adalah...', 'Garis tegas', 'Tanpa blending', 'Warna terang', 'Blending yang menyeluruh', 'd', NULL, 2),
(10, 13, 'mcq', 'Daya tahan make-up wedding yang diharapkan adalah...', '1-2 jam', '8-12 jam', '24 jam', 'Tidak perlu tahan lama', 'b', NULL, 0),
(11, 13, 'mcq', 'Untuk mencegah lipstik luntur saat makan, gunakan...', 'Lip liner penuh (full lip lining)', 'Lip gloss', 'Tanpa lipstik', 'Lip balm saja', 'a', NULL, 1),
(12, 13, 'mcq', 'Persiapan kulit wajah untuk klien wedding sebaiknya dimulai...', 'H-1', 'Saat hari H', 'H-7', 'H-30 menit', 'c', NULL, 2),
(13, 14, 'mcq', 'Sebelum praktek, MUA perlu melakukan konsultasi singkat tentang...', 'Harga rumah klien', 'Media sosial klien', 'Makanan favorit klien', 'Jenis kulit, alergi, tema acara, dan durasi', 'd', NULL, 0),
(14, 14, 'mcq', 'Dokumentasi proses riasan untuk portofolio dilakukan...', 'Tanpa izin klien', 'Dengan izin klien', 'Diam-diam', 'Tidak boleh dilakukan sama sekali', 'b', NULL, 1),
(15, 14, 'mcq', 'Peluang penyaluran klien terbuka bagi lulusan setelah...', 'Menyelesaikan seluruh modul dan lulus uji praktek', 'Membeli produk MCM', 'Memiliki banyak followers', 'Menjadi admin', 'a', NULL, 2);

-- --------------------------------------------------------

--
-- Struktur dari tabel `testimonials`
--

CREATE TABLE IF NOT EXISTS `testimonials` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `rating` tinyint(1) NOT NULL DEFAULT '5',
  `review` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `class_id` int DEFAULT NULL,
  `graduation_year` varchar(10) DEFAULT NULL,
  `job` varchar(150) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `class_id` (`class_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `testimonials`
--

INSERT IGNORE INTO `testimonials` (`id`, `user_id`, `name`, `rating`, `review`, `image`, `class_id`, `graduation_year`, `job`, `status`, `created_at`) VALUES
(1, NULL, 'Siti Rahma', 5, 'Pelatihan Make Up Artist di MCM sangat menyenangkan! Instrukturnya sabar dan materinya langsung bisa dipraktikkan.', NULL, 1, '2025', 'MUA Profesional', 'approved', '2026-08-15 03:22:22'),
(2, NULL, 'Budi Santoso', 5, 'Setelah ikut kursus Content Creator, saya langsung berani membuat konten profesional. Recommended!', NULL, 5, '2025', 'Content Creator', 'approved', '2026-08-15 03:22:22'),
(3, NULL, 'Dewi Lestari', 4, 'Kursus Catering Pastry-nya lengkap, dari dasar hingga teknik dekorasi kue. Fasilitasnya memadai.', NULL, 7, '2024', 'Pemilik Usaha Kue', 'approved', '2026-08-15 03:22:22'),
(5, NULL, 'Gilang Erlangga', 5, 'Bagus', 'uploads/testimonials/6a7fdfc5c989b.jpeg', 1, NULL, NULL, 'approved', '2026-08-15 03:40:53');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT IGNORE INTO `users` (`id`, `name`, `email`, `phone`, `password`, `created_at`) VALUES
(5, 'Gilang Erlangga', 'gilangerlangga1306@gmail.com', '6281215614286', '$2y$12$rVWekUkJ8qhWDJ817Cs0WOu1MiNVJHgrCsaxN.h.W15qfE1V0vKni', '2026-08-17 12:20:06'),
(11, 'Gilang Erlangga', 'gilangerlangga1306@gmail.com', '6281215614286', '$2y$12$1irkWTD3/dui3I3ukiFNruHYHmyI2fiVKmqv0pOhK/kWDP7ngfa66', '2026-08-19 03:16:20');

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`);

--
-- Ketidakleluasaan untuk tabel `testimonials`
--
ALTER TABLE `testimonials`
  ADD CONSTRAINT `testimonials_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
