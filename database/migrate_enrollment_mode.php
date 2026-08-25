<?php
// Migration runner: enrollments per-mode (online/offline)
// Usage: php database/migrate_enrollment_mode.php
require_once __DIR__ . '/../config/database.php';

echo "=== Migration: enrollments.class_mode ===\n";

try {
    $cols = $pdo->query("SHOW COLUMNS FROM enrollments LIKE 'class_mode'")->fetchAll();
    if (empty($cols)) {
        $pdo->exec("ALTER TABLE enrollments ADD COLUMN class_mode ENUM('online','offline') NOT NULL DEFAULT 'offline' AFTER class_id");
        echo "[OK] kolom class_mode ditambahkan\n";
    } else {
        echo "[SKIP] kolom class_mode sudah ada\n";
    }

    $affected = $pdo->exec("UPDATE enrollments e JOIN orders o ON e.order_id = o.id SET e.class_mode = o.class_mode");
    echo "[OK] backfill class_mode dari orders: $affected baris\n";

    $idx = $pdo->query("SHOW INDEX FROM enrollments WHERE Key_name = 'uq_enroll_user_class'")->fetchAll();
    if (!empty($idx)) {
        $pdo->exec("ALTER TABLE enrollments DROP INDEX uq_enroll_user_class");
        echo "[OK] index lama uq_enroll_user_class dihapus\n";
    } else {
        echo "[SKIP] index uq_enroll_user_class tidak ada\n";
    }

    $idx2 = $pdo->query("SHOW INDEX FROM enrollments WHERE Key_name = 'uq_enroll_user_class_mode'")->fetchAll();
    if (empty($idx2)) {
        $pdo->exec("ALTER TABLE enrollments ADD UNIQUE KEY uq_enroll_user_class_mode (user_id, class_id, class_mode)");
        echo "[OK] unique key baru uq_enroll_user_class_mode dibuat\n";
    } else {
        echo "[SKIP] unique key uq_enroll_user_class_mode sudah ada\n";
    }

    echo "\nSelesai.\n";
} catch (PDOException $e) {
    echo "[ERROR] " . $e->getMessage() . "\n";
    exit(1);
}
