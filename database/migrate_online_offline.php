<?php
// Runner idempotent untuk migration_online_offline.sql
// Jalankan: php database/migrate_online_offline.php
require __DIR__ . '/../config/database.php';

function columnExists($pdo, $table, $column) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?");
    $stmt->execute([$table, $column]);
    return (int)$stmt->fetchColumn() > 0;
}

try {
    // classes.price_online
    if (!columnExists($pdo, 'classes', 'price_online')) {
        $pdo->exec("ALTER TABLE `classes` ADD COLUMN `price_online` INT NOT NULL DEFAULT 0 AFTER `price`");
        echo "Added classes.price_online\n";
    } else echo "classes.price_online already exists\n";

    if (!columnExists($pdo, 'classes', 'price_offline')) {
        $pdo->exec("ALTER TABLE `classes` ADD COLUMN `price_offline` INT NOT NULL DEFAULT 0 AFTER `price_online`");
        echo "Added classes.price_offline\n";
    } else echo "classes.price_offline already exists\n";

    if (!columnExists($pdo, 'classes', 'mode_available')) {
        $pdo->exec("ALTER TABLE `classes` ADD COLUMN `mode_available` ENUM('online','offline','both') NOT NULL DEFAULT 'both' AFTER `price_offline`");
        echo "Added classes.mode_available\n";
    } else echo "classes.mode_available already exists\n";

    if (!columnExists($pdo, 'classes', 'description_online')) {
        $pdo->exec("ALTER TABLE `classes` ADD COLUMN `description_online` TEXT NULL AFTER `description`");
        echo "Added classes.description_online\n";
    } else echo "classes.description_online already exists\n";

    if (!columnExists($pdo, 'classes', 'description_offline')) {
        $pdo->exec("ALTER TABLE `classes` ADD COLUMN `description_offline` TEXT NULL AFTER `description_online`");
        echo "Added classes.description_offline\n";
    } else echo "classes.description_offline already exists\n";

    if (!columnExists($pdo, 'orders', 'class_mode')) {
        $pdo->exec("ALTER TABLE `orders` ADD COLUMN `class_mode` ENUM('online','offline') NOT NULL DEFAULT 'offline' AFTER `class_id`");
        echo "Added orders.class_mode\n";
    } else echo "orders.class_mode already exists\n";

    // Data migration
    $affected = $pdo->exec("UPDATE `classes` SET `price_offline` = `price`, `price_online` = ROUND(`price` * 0.8) WHERE `price_offline` = 0 AND `price_online` = 0");
    echo "Migrated $affected classes price (offline=price, online=price*0.8)\n";

    $pdo->exec("UPDATE `classes` SET `description_online` = `description` WHERE `description_online` IS NULL");
    $pdo->exec("UPDATE `classes` SET `description_offline` = `description` WHERE `description_offline` IS NULL");
    echo "Migrated description_online/offline\n";

    // Verify
    $rows = $pdo->query("SELECT id,name,price,price_online,price_offline,mode_available FROM classes ORDER BY id")->fetchAll();
    foreach ($rows as $r) {
        echo sprintf("%d | %s | price=%d online=%d offline=%d mode=%s\n", $r['id'], $r['name'], $r['price'], $r['price_online'], $r['price_offline'], $r['mode_available']);
    }
    $oc = $pdo->query("DESCRIBE orders")->fetchAll();
    $has = false; foreach($oc as $c) if($c['Field']==='class_mode') $has=true;
    echo $has ? "orders.class_mode verified\n" : "orders.class_mode MISSING\n";

    echo "Migration selesai.\n";
} catch (PDOException $e) {
    echo "ERR: " . $e->getMessage() . "\n";
    exit(1);
}
