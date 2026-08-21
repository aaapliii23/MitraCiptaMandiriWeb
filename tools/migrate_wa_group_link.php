<?php
require_once __DIR__ . '/../config/database.php';
try {
    $pdo->exec("ALTER TABLE classes ADD COLUMN wa_group_link VARCHAR(500) DEFAULT NULL");
    echo "OK: kolom wa_group_link ditambahkan ke tabel classes.\n";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column') !== false) {
        echo "SKIP: kolom wa_group_link sudah ada.\n";
    } else {
        echo "ERROR: " . $e->getMessage() . "\n";
    }
}
