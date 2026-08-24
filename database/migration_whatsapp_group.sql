-- Migration: Tambah fitur Link Grup WhatsApp untuk kelas Offline
-- Jalankan: mysql -u root mcm_db < database/migration_whatsapp_group.sql
-- atau via PHP: php database/migrate_whatsapp_group.php

-- Tambah kolom whatsapp_group_link ke classes
ALTER TABLE `classes`
  ADD COLUMN `whatsapp_group_link` VARCHAR(255) NULL DEFAULT NULL AFTER `mode_available`;

-- Pastikan orders sudah punya class_mode (dari fitur online/offline sebelumnya)
-- Jika belum, tambahkan (idempotent check manual sebelum jalankan)
-- ALTER TABLE `orders` ADD COLUMN `class_mode` ENUM('online','offline') NOT NULL DEFAULT 'offline' AFTER `class_id`;
