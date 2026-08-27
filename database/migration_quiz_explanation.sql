-- Migration: Tambah pembahasan quiz & state perbaikan 100%
-- Jalankan: mysql -u root mcm_db < database/migration_quiz_explanation.sql
-- atau via phpMyAdmin: import file ini. DB baru sudah ada di schema.sql.

ALTER TABLE `quiz_questions`
  ADD COLUMN `explanation` TEXT NULL AFTER `essay_answer`;

CREATE TABLE IF NOT EXISTS `quiz_progress` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `question_id` INT NOT NULL,
  `is_correct` TINYINT(1) NOT NULL DEFAULT 0,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_user_question` (`user_id`,`question_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
