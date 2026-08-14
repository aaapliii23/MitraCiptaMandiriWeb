CREATE TABLE IF NOT EXISTS `bookings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `whatsapp` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `service` varchar(100) NOT NULL,
  `booking_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
  `category` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `features` text NOT NULL,
  `price` int(11) NOT NULL DEFAULT 500000,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
  `customer_name` varchar(100) NOT NULL,
  `customer_phone` varchar(20) NOT NULL,
  `customer_email` varchar(100) DEFAULT NULL,
  `customer_address` text DEFAULT NULL,
  `customer_institution` varchar(100) DEFAULT NULL,
  `class_id` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `status` ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`class_id`) REFERENCES `classes`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `gallery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(50) NOT NULL,
  `title` varchar(100) NOT NULL,
  `image` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert initial classes
INSERT IGNORE INTO `classes` (`id`, `name`, `category`, `description`, `image`, `features`) VALUES
(1, 'Public Speaking', 'Public Speaking', 'Tingkatkan kepercayaan diri dan asah kemampuan komunikasi edukatif serta persuasif Anda. Sangat cocok untuk MC, Presenter, maupun Profesional.', 'https://images.unsplash.com/photo-1475721025592-720a442142cb?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', '["Teknik Komunikasi Dasar", "Mengatasi Demam Panggung", "Praktek Langsung (MC/Presenter)", "Personal Branding", "Sertifikat Pelatihan", "Penyaluran Kerja (Opsional)"]'),
(2, 'Tata Rias', 'Tata Rias', 'Pelajari teknik make-up dari dasar hingga profesional. Wujudkan impian Anda menjadi Make-Up Artist (MUA) yang handal.', 'https://images.unsplash.com/photo-1487412720507-e7ab37603c6f?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', '["Pengenalan Alat & Produk Make-up", "Make-up Natural & Flawless", "Make-up Wedding / Pengantin", "Koreksi Bentuk Wajah", "Sertifikat Pelatihan", "Pemasaran Bisnis MUA"]'),
(3, 'Pijat / Refleksi', 'Pijat', 'Kuasai anatomi dasar dan teknik pijat refleksi yang tepat untuk kesehatan dan kebugaran tubuh.', 'https://images.unsplash.com/photo-1544161515-4ab6ce6db874?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', '["Anatomi Tubuh & Titik Saraf", "Teknik Pijat Relaksasi Dasar", "Teknik Refleksi Lanjutan", "Penanganan Titik Penyakit", "Sertifikat Pelatihan", "Manajemen Usaha Panti Pijat"]'),
(4, 'Barbershop', 'Barber', 'Jadilah kapster handal dengan menguasai teknik potong rambut pria modern dan klasik.', 'https://images.unsplash.com/photo-1503951914875-452162b0f3f1?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', '["Pengenalan Alat Cukur Profesional", "Teknik Dasar Memotong Rambut", "Teknik Fade & Styling Modern", "Hair Tattoo Dasar", "Sertifikat Pelatihan", "Manajemen Bisnis Barbershop"]'),
(5, 'Catering / Kuliner', 'Catering', 'Pelajari manajemen dapur, penyajian makanan, dan resep masakan komersial untuk bisnis kuliner.', 'https://images.unsplash.com/photo-1555244162-803834f70033?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', '["Resep Dasar & Racikan Bumbu", "Standar Higienitas Dapur", "Memasak Menu Skala Besar", "Manajemen Porsi & Harga", "Sertifikat Pelatihan", "Strategi Bisnis Katering"]');

-- Insert initial gallery
INSERT IGNORE INTO `gallery` (`id`, `category`, `title`, `image`) VALUES
(1, 'public_speaking', 'Praktek Public Speaking', 'https://images.unsplash.com/photo-1475721027785-f74eccf877e2?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80'),
(2, 'tata_rias', 'Kelas Tata Rias', 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80'),
(3, 'pijat', 'Pelatihan Pijat & Refleksi', 'https://images.unsplash.com/photo-1544161515-4ab6ce6db874?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80'),
(4, 'barber', 'Praktek Barbershop', 'https://images.unsplash.com/photo-1503951914875-452162b0f3f1?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80'),
(5, 'catering', 'Kelas Memasak & Catering', 'https://images.unsplash.com/photo-1555244162-803834f70033?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80');
