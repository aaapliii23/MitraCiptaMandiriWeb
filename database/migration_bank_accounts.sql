-- Migration: tabel bank_accounts (multi-rekening Transfer Bank Manual)
-- Jalankan manual sekali: mysql -u root mcm_db < database/migration_bank_accounts.sql
-- (Runtime juga auto-migrate via pg_ensure_bank_accounts(), termasuk migrasi
--  1 rekening lama dari tabel settings — jadi file ini opsional.)
CREATE TABLE IF NOT EXISTS `bank_accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bank_name` varchar(50) NOT NULL,
  `account_number` varchar(50) NOT NULL,
  `account_holder` varchar(100) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
