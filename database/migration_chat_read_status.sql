-- Migration: Add is_read and read_at to chat_messages for read receipts (dilihat/dibaca)
ALTER TABLE `chat_messages`
  ADD COLUMN `is_read` TINYINT(1) NOT NULL DEFAULT 0 AFTER `matched_intent`,
  ADD COLUMN `read_at` DATETIME NULL AFTER `is_read`,
  ADD INDEX `idx_chat_is_read` (`is_read`);
