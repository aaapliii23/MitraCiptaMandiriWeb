-- Migration: enrollments per-mode (online/offline terpisah)
-- Bug: UNIQUE(user_id, class_id) membuat pembelian mode kedua untuk kelas yang sama
--      diabaikan INSERT IGNORE, sehingga enrollment online/offline saling menggantikan.
-- Fix: tambah kolom class_mode + unique key baru (user_id, class_id, class_mode).
--
-- PENTING: BACKUP DATABASE SEBELUM MENJALANKAN MIGRASI INI.
-- Jalankan manual: mysql -u root mcm_db < database/migration_enrollment_mode.sql

-- 1. Kolom class_mode pada enrollments
ALTER TABLE enrollments
    ADD COLUMN class_mode ENUM('online','offline') NOT NULL DEFAULT 'offline' AFTER class_id;

-- 2. Backfill: isi dari orders.class_mode via order_id (fallback tetap 'offline')
UPDATE enrollments e
JOIN orders o ON e.order_id = o.id
SET e.class_mode = o.class_mode;

-- 3. Ganti unique key lama dengan yang mempertimbangkan mode
ALTER TABLE enrollments DROP INDEX uq_enroll_user_class;
ALTER TABLE enrollments
    ADD UNIQUE KEY uq_enroll_user_class_mode (user_id, class_id, class_mode);
