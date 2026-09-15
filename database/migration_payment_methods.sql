-- Migration: tabel payment_methods (status metode pembayaran dikontrol admin)
-- Jalankan manual sekali: mysql -u root mcm_db < database/migration_payment_methods.sql
-- (Runtime juga auto-migrate via pg_ensure_payment_methods(), jadi file ini opsional.)
CREATE TABLE IF NOT EXISTS `payment_methods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `method_key` varchar(50) NOT NULL,
  `method_name` varchar(100) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `note` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `method_key` (`method_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `payment_methods` (`method_key`,`method_name`,`is_active`,`note`) VALUES
('virtual_account','Virtual Account',0,'Masih dalam pengembangan'),
('qris','QRIS',0,'Masih dalam pengembangan'),
('e_wallet','E-Wallet',0,'Masih dalam pengembangan'),
('credit_card','Kartu Kredit/Debit',0,'Masih dalam pengembangan'),
('bank_transfer','Transfer Bank Manual',1,NULL);
