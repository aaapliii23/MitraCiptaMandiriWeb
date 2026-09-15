-- Migration: Transfer Bank Manual (bukti transfer)
-- Jalankan manual sekali: mysql -u root mcm_db < database/migration_manual_transfer.sql
-- (Runtime juga auto-migrate via pg_ensure_payment_columns(), jadi file ini opsional.)
ALTER TABLE orders
  ADD COLUMN transfer_proof varchar(255) DEFAULT NULL,
  ADD COLUMN proof_uploaded_at datetime DEFAULT NULL;
