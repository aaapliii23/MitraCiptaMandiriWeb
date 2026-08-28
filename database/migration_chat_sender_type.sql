-- Migrasi untuk widget Chat MCM yang stuck di "Memuat percakapan..."
-- Perubahan di chat_api.php & whatsapp_client.php membutuhkan kolom sender_type
-- Jika database lokal masih dari schema lama (tanpa sender_type), jalankan:
--   mysql -u root mcm_db < database/migration_chat_sender_type.sql
-- atau import via phpMyAdmin. Auto-migrate di config/database.php juga akan mencoba menambah otomatis.

ALTER TABLE chat_messages
  ADD COLUMN sender_type ENUM('visitor','bot','admin') NOT NULL DEFAULT 'visitor' AFTER direction;

-- Index untuk performa history & unread count
ALTER TABLE chat_messages ADD INDEX idx_chat_wa_number (wa_number);
ALTER TABLE chat_messages ADD INDEX idx_chat_sender_type (sender_type);
