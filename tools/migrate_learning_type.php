<?php
require_once __DIR__ . '/../config/database.php';

echo "Running migration...\n";

// Add price_offline and price_online to classes
try {
    $pdo->exec("ALTER TABLE `classes` ADD COLUMN `price_offline` INT NOT NULL DEFAULT 0 AFTER `price`");
    echo "Added classes.price_offline\n";
} catch (Exception $e) {
    echo "classes.price_offline already exists or: " . $e->getMessage() . "\n";
}

try {
    $pdo->exec("ALTER TABLE `classes` ADD COLUMN `price_online` INT NOT NULL DEFAULT 0 AFTER `price_offline`");
    echo "Added classes.price_online\n";
} catch (Exception $e) {
    echo "classes.price_online already exists or: " . $e->getMessage() . "\n";
}

// Populate default prices for existing classes
try {
    $pdo->exec("UPDATE `classes` SET `price_offline` = `price` WHERE `price_offline` = 0 OR `price_offline` IS NULL");
    $pdo->exec("UPDATE `classes` SET `price_online` = ROUND(`price` * 0.75) WHERE `price_online` = 0 OR `price_online` IS NULL");
    echo "Populated price_offline and price_online values.\n";
} catch (Exception $e) {
    echo "Error updating prices: " . $e->getMessage() . "\n";
}

// Add learning_type to orders
try {
    $pdo->exec("ALTER TABLE `orders` ADD COLUMN `learning_type` ENUM('online', 'offline') NOT NULL DEFAULT 'offline' AFTER `class_id`");
    echo "Added orders.learning_type\n";
} catch (Exception $e) {
    echo "orders.learning_type already exists or: " . $e->getMessage() . "\n";
}

// Add learning_type to enrollments
try {
    $pdo->exec("ALTER TABLE `enrollments` ADD COLUMN `learning_type` ENUM('online', 'offline') NOT NULL DEFAULT 'offline' AFTER `class_id`");
    echo "Added enrollments.learning_type\n";
} catch (Exception $e) {
    echo "enrollments.learning_type already exists or: " . $e->getMessage() . "\n";
}

echo "Migration finished.\n";
