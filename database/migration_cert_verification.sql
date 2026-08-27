-- Migration: Tambah token verifikasi publik untuk sertifikat (QR verifikasi)
-- Jalankan: mysql -u root mcm_db < database/migration_cert_verification.sql
-- atau via phpMyAdmin: import file ini.
-- DB baru tidak perlu migration (schema.sql sudah ada verify_token).
-- Langkah dipisah agar UNIQUE tidak gagal karena duplikat '' pada data lama.

ALTER TABLE `certificates`
  ADD COLUMN `verify_token` VARCHAR(64) NOT NULL DEFAULT '' AFTER `cert_number`;

-- Backfill token untuk sertifikat lama yang masih kosong (40 hex, kompatibel dengan bin2hex(random_bytes(20)) di PHP).
-- Tiap baris dapat token unik via UUID()+RAND().
UPDATE `certificates`
SET `verify_token` = LEFT(SHA2(CONCAT(UUID(), RAND(), id), 256), 40)
WHERE `verify_token` = '' OR `verify_token` IS NULL;

ALTER TABLE `certificates`
  ADD UNIQUE KEY `uq_cert_verify_token` (`verify_token`);

-- Fallback untuk MySQL/MariaDB tanpa SHA2 (uncomment jika error di atas):
-- UPDATE `certificates` SET `verify_token` = LEFT(SHA1(CONCAT(UUID(), RAND(), id)), 40) WHERE `verify_token` = '' OR `verify_token` IS NULL;
