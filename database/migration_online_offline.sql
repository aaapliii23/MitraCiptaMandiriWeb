-- Migration: Tambah fitur Online/Offline ke MitraCiptaMandiriWeb
-- Jalankan: mysql -u root mcm_db < database/migration_online_offline.sql
-- atau via PHP: php database/migrate_online_offline.php
-- Idempotent untuk DB yang sudah ada 8 kelas — set price_offline = price lama, price_online = price * 0.8

-- 1. Tambah kolom ke classes
ALTER TABLE `classes`
  ADD COLUMN `price_online` INT NOT NULL DEFAULT 0 AFTER `price`,
  ADD COLUMN `price_offline` INT NOT NULL DEFAULT 0 AFTER `price_online`,
  ADD COLUMN `mode_available` ENUM('online','offline','both') NOT NULL DEFAULT 'both' AFTER `price_offline`,
  ADD COLUMN `description_online` TEXT NULL AFTER `description`,
  ADD COLUMN `description_offline` TEXT NULL AFTER `description_online`;

-- 2. Tambah kolom ke orders
ALTER TABLE `orders`
  ADD COLUMN `class_mode` ENUM('online','offline') NOT NULL DEFAULT 'offline' AFTER `class_id`;

-- 3. Migrasi data: 8 kelas existing
UPDATE `classes` SET `price_offline` = `price`, `price_online` = ROUND(`price` * 0.8) WHERE `price_offline` = 0 AND `price_online` = 0;
UPDATE `classes` SET `description_online` = `description` WHERE `description_online` IS NULL;
UPDATE `classes` SET `description_offline` = `description` WHERE `description_offline` IS NULL;
