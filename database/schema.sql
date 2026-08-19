CREATE TABLE IF NOT EXISTS `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default admin: username=admin, password=admin
INSERT IGNORE INTO `admins` (`username`, `password`) VALUES ('admin', '$2y$10$0z.Z5WjY0T/r7X8t5lO9l.O1uA8OaH7s0vM3N2f4E/7gR.aM5U.F6');

CREATE TABLE IF NOT EXISTS `classes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `start_date` date DEFAULT NULL,
  `category` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `features` text NOT NULL,
  `price` int(11) NOT NULL DEFAULT 500000,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `class_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `class_categories` (`name`, `slug`) VALUES
('Kecantikan', 'kecantikan'),
('Kesehatan', 'kesehatan'),
('Metodologi', 'metodologi'),
('Digital', 'digital'),
('Kuliner', 'kuliner'),
('Pariwisata', 'pariwisata');

CREATE TABLE IF NOT EXISTS `instructors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `category` varchar(100) NOT NULL,
  `specialization` varchar(150) NOT NULL,
  `bio` text,
  `certifications` text,
  `image` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `certifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(150) NOT NULL,
  `description` text,
  `image` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `instructors` (`name`, `category`, `specialization`, `image`) VALUES
('Bunga Lestari', 'Kecantikan', 'Make Up Artist & Tata Rias', 'uploads/instructors/bunga.jpg'),
('Agus Wijaya', 'Kuliner', 'Instruktur Kuliner & Pastry', 'uploads/instructors/agus.jpg'),
('Dewi Anggraini', 'Digital', 'Digital Marketing & Content', 'uploads/instructors/dewi.jpg');

INSERT IGNORE INTO `certifications` (`title`, `description`, `image`) VALUES
('Surat Keterangan Kemenkumham', 'Legalitas badan hukum Lembaga MCM terdaftar resmi di Kementerian Hukum dan HAM.', 'uploads/certs/sk_kemenkumham.jpg'),
('Sertifikat Akreditasi Lembaga', 'Sertifikat akreditasi lembaga pelatihan dari lembaga pengakreditasian resmi.', 'uploads/certs/akreditasi.jpg'),
('Piagam Penghargaan Pendidikan', 'Dokumen penghargaan atas kontribusi MCM dalam pendidikan dan pelatihan vokasi.', 'uploads/certs/piagam.jpg');

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL UNIQUE,
  `phone` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_number` varchar(50) NOT NULL UNIQUE,
  `user_id` int(11) DEFAULT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_phone` varchar(20) NOT NULL,
  `customer_email` varchar(100) DEFAULT NULL,
  `customer_address` text DEFAULT NULL,
  `customer_institution` varchar(100) DEFAULT NULL,
  `class_id` int(11) NOT NULL,
  `instructor_id` int(11) DEFAULT NULL,
  `amount` int(11) NOT NULL,
  `status` ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
  `payment_status` ENUM('unpaid', 'pending', 'paid', 'failed', 'expired') DEFAULT 'unpaid',
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_gateway_ref` varchar(100) DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`class_id`) REFERENCES `classes`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `gallery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(50) NOT NULL,
  `title` varchar(100) NOT NULL,
  `image` varchar(255) NOT NULL,
  `show_on_home` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert initial classes
INSERT IGNORE INTO `classes` (`id`, `name`, `start_date`, `category`, `description`, `image`, `features`, `price`) VALUES
(1, 'Make Up Artist', '2026-09-05', 'Kecantikan', 'Kuasai teknik make-up dari dasar hingga profesional. Program ini dirancang untuk mencetak Make-Up Artist (MUA) handal yang siap menangani klien rias panggung, wedding, hingga editorial.', 'uploads/classes/makeup.jpg', '["Pengenalan Alat & Produk Make-up", "Make-up Natural & Flawless", "Make-up Wedding & Formal", "Koreksi Bentuk Wajah", "Praktek Langsung dengan Model", "Sertifikat & Penyaluran Klien"]', 1200000),
(2, 'Tata Kecantikan', '2026-09-12', 'Kecantikan', 'Pelajari perawatan kecantikan kulit, rambut, dan kuku mulai dari dasar hingga tingkat profesional. Cocok untuk Anda yang ingin berkarir di industri salon dan spa.', 'uploads/classes/kecantikan.jpg', '["Anatomi Kulit & Perawatan Dasar", "Perawatan Wajah (Facial)", "Hair Styling & Perawatan Rambut", "Manicure & Pedicure", "Manajemen Bisnis Salon", "Sertifikat Pelatihan"]', 1000000),
(3, 'Pijat Terapis', '2026-09-19', 'Kesehatan', 'Kuasai anatomi tubuh, titik saraf, dan teknik pijat terapi yang benar. Program ini membekali Anda menjadi terapis profesional dengan standar kesehatan dan etika kerja.', 'uploads/classes/pijat.jpg', '["Anatomi Tubuh & Titik Saraf", "Teknik Pijat Relaksasi", "Teknik Pijat Terapi & Refleksi", "Pijat Tradisional Indonesia", "K3 & Etika Terapis", "Sertifikat Terapis"]', 1100000),
(4, 'Kursus Metodologi', '2026-09-26', 'Metodologi', 'Program Training of Trainer (ToT) yang membekali Anda kemampuan menyusun kurikulum, menyampaikan materi, dan mengelola kelas pelatihan secara profesional.', 'uploads/classes/metodologi.jpg', '["Desain Kurikulum & Silabus", "Teknik Presentasi & Mengajar", "Evaluasi Pembelajaran", "Manajemen Kelas Pelatihan", "Praktek Microteaching", "Sertifikat Fasilitator"]', 950000),
(5, 'Content Creator', '2026-10-03', 'Digital', 'Belajar membuat konten menarik untuk media sosial dari riset ide, produksi video, hingga editing. Siapkan diri Anda menjadi kreator profesional yang menghasilkan.', 'uploads/classes/content.jpg', '["Strategi Konten & Riset Topik", "Videografi & Fotografi Dasar", "Editing Video (CapCut/PC)", "Copywriting & Caption", "Manajemen Akun & Algoritma", "Monetisasi Konten"]', 1300000),
(6, 'Digital Marketing', '2026-10-10', 'Digital', 'Kuasai strategi pemasaran digital dari SEO, iklan berbayar, hingga analitik. Program lengkap untuk Anda yang ingin mengembangkan bisnis atau berkarir sebagai digital marketer.', 'uploads/classes/digimar.jpg', '["Dasar-Dasar Digital Marketing", "SEO & SEM Dasar", "Iklan Facebook/Instagram Ads", "Google Ads & Analytics", "Email & WhatsApp Marketing", "Analitik & Laporan Kinerja"]', 1400000),
(7, 'Catering Pastry', '2026-10-17', 'Kuliner', 'Pelajari seni membuat kue dan pastry dari dasar hingga dekorasi tingkat profesional. Siapkan bisnis kue Anda dengan standar dapur yang higienis dan menguntungkan.', 'uploads/classes/pastry.jpg', '["Dasar-Dasar Pastry & Baking", "Roti & Kue Kering", "Kue Basah & Hidangan Penutup", "Teknik Dekorasi Kue", "Higienitas & Manajemen Dapur", "Manajemen Usaha Kue"]', 1150000),
(8, 'Dasar-Dasar Pariwisata', '2026-10-24', 'Pariwisata', 'Kenali seluk-beluk industri pariwisata dari pemandu wisata, hospitality, hingga pengelolaan usaha wisata lokal. Pintu masuk menuju karir di sektor pariwisata.', 'uploads/classes/pariwisata.jpg', '["Pengenalan Industri Pariwisata", "Pemandu Wisata & Hospitality", "Komunikasi & Bahasa Asing Dasar", "Manajemen Perjalanan Wisata", "Homestay & Usaha Wisata Lokal", "Praktek Lapangan"]', 900000);

-- Insert initial gallery
INSERT IGNORE INTO `gallery` (`id`, `category`, `title`, `image`, `show_on_home`) VALUES
(1, 'public_speaking', 'Praktek Public Speaking', 'https://images.unsplash.com/photo-1475721027785-f74eccf877e2?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80', 1),
(2, 'tata_rias', 'Kelas Tata Rias', 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80', 1),
(3, 'pijat', 'Pelatihan Pijat & Refleksi', 'https://images.unsplash.com/photo-1544161515-4ab6ce6db874?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80', 1),
(4, 'barber', 'Praktek Barbershop', 'https://images.unsplash.com/photo-1503951914875-452162b0f3f1?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80', 0),
(5, 'catering', 'Kelas Memasak & Catering', 'https://images.unsplash.com/photo-1555244162-803834f70033?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80', 0);

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
  `status` ENUM('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`class_id`) REFERENCES `classes`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed approved testimonials
INSERT IGNORE INTO `testimonials` (`id`, `name`, `rating`, `review`, `image`, `class_id`, `graduation_year`, `job`, `status`) VALUES
(1, 'Siti Rahma', 5, 'Pelatihan Make Up Artist di MCM sangat menyenangkan! Instrukturnya sabar dan materinya langsung bisa dipraktikkan.', NULL, 1, '2025', 'MUA Profesional', 'approved'),
(2, 'Budi Santoso', 5, 'Setelah ikut kursus Content Creator, saya langsung berani membuat konten profesional. Recommended!', NULL, 5, '2025', 'Content Creator', 'approved'),
(3, 'Dewi Lestari', 4, 'Kursus Catering Pastry-nya lengkap, dari dasar hingga teknik dekorasi kue. Fasilitasnya memadai.', NULL, 7, '2024', 'Pemilik Usaha Kue', 'approved');

CREATE TABLE IF NOT EXISTS `enrollments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL UNIQUE,
  `enrolled_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_enroll_user_class` (`user_id`, `class_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `materials` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `class_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `type` ENUM('video','pdf','text') NOT NULL DEFAULT 'text',
  `content` text NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `material_progress` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `material_id` int(11) NOT NULL,
  `completed` tinyint(1) NOT NULL DEFAULT 0,
  `completed_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_progress_user_material` (`user_id`, `material_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `certificates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `cert_number` varchar(50) NOT NULL UNIQUE,
  `issued_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_cert_user_class` (`user_id`, `class_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `certificate_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `class_id` int(11) DEFAULT NULL,
  `layout` varchar(50) NOT NULL DEFAULT 'default',
  `bg_image` varchar(255) DEFAULT NULL,
  `accent_color` varchar(20) DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `certificate_templates` (`id`, `name`, `class_id`, `layout`, `bg_image`, `accent_color`, `is_default`) VALUES
(1, 'Sertifikat Standar', NULL, 'default', NULL, '#1e40af', 1),
(2, 'Sertifikat Elegant', NULL, 'elegant', NULL, '#7c3aed', 0),
(3, 'Sertifikat Modern', NULL, 'modern', NULL, '#0ea5e9', 0),
(4, 'Sertifikat Premium', NULL, 'premium', NULL, '#b45309', 0);

CREATE TABLE IF NOT EXISTS `quiz_questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `material_id` int(11) NOT NULL,
  `question_type` ENUM('mcq','essay') NOT NULL DEFAULT 'mcq',
  `question` text NOT NULL,
  `option_a` varchar(255) NOT NULL,
  `option_b` varchar(255) NOT NULL,
  `option_c` varchar(255) NOT NULL,
  `option_d` varchar(255) NOT NULL,
  `correct_option` ENUM('a','b','c','d') DEFAULT NULL,
  `essay_answer` text DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `material_id` (`material_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `quiz_attempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `material_id` int(11) NOT NULL,
  `score` int(11) NOT NULL,
  `total` int(11) NOT NULL,
  `passed` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_material` (`user_id`, `material_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `chat_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `wa_number` varchar(20) NOT NULL,
  `direction` ENUM('in','out') NOT NULL DEFAULT 'in',
  `message` text NOT NULL,
  `matched_intent` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `chatbot_intents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `intent` varchar(50) NOT NULL UNIQUE,
  `keywords` text NOT NULL,
  `reply` text NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `finance_transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` ENUM('in','out') NOT NULL,
  `category` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `amount` int(11) NOT NULL,
  `transaction_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
