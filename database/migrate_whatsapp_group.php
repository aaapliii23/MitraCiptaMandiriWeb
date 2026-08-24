<?php
// Runner idempotent untuk migration_whatsapp_group.sql
require __DIR__ . '/../config/database.php';

function columnExists($pdo, $table, $column) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?");
    $stmt->execute([$table, $column]);
    return (int)$stmt->fetchColumn() > 0;
}

try {
    if (!columnExists($pdo, 'classes', 'whatsapp_group_link')) {
        $pdo->exec("ALTER TABLE `classes` ADD COLUMN `whatsapp_group_link` VARCHAR(255) NULL DEFAULT NULL AFTER `mode_available`");
        echo "Added classes.whatsapp_group_link\n";
    } else {
        echo "classes.whatsapp_group_link already exists\n";
    }

    // Pastikan orders.class_mode ada (dari fitur sebelumnya)
    if (!columnExists($pdo, 'orders', 'class_mode')) {
        $pdo->exec("ALTER TABLE `orders` ADD COLUMN `class_mode` ENUM('online','offline') NOT NULL DEFAULT 'offline' AFTER `class_id`");
        echo "Added orders.class_mode\n";
    } else {
        echo "orders.class_mode already exists\n";
    }

    // Seed contoh: set link dummy untuk 2 kelas offline sebagai contoh (opsional, hanya jika masih NULL)
    // Tidak overwrite link yang sudah diisi admin
    $seedLinks = [
        1 => 'https://chat.whatsapp.com/ExampleMakeUpOfflineGroup',
        3 => 'https://chat.whatsapp.com/ExamplePijatOfflineGroup',
    ];
    foreach ($seedLinks as $id => $link) {
        $stmt = $pdo->prepare("UPDATE classes SET whatsapp_group_link = ? WHERE id = ? AND (whatsapp_group_link IS NULL OR whatsapp_group_link = '')");
        $stmt->execute([$link, $id]);
        if ($stmt->rowCount() > 0) echo "Seeded whatsapp_group_link for class $id\n";
    }

    $rows = $pdo->query("SELECT id, name, mode_available, whatsapp_group_link FROM classes ORDER BY id")->fetchAll();
    foreach ($rows as $r) {
        echo sprintf("%d | %s | mode=%s | wa_link=%s\n", $r['id'], $r['name'], $r['mode_available'], $r['whatsapp_group_link'] ?: '(null)');
    }

    echo "Migration selesai.\n";
} catch (PDOException $e) {
    echo "ERR: " . $e->getMessage() . "\n";
    exit(1);
}
