<?php
require_once dirname(__DIR__) . '/config/database.php';

$classes = $pdo->query('SELECT id, name, category FROM classes ORDER BY id ASC')->fetchAll();
foreach ($classes as $c) {
    $stmt = $pdo->prepare('SELECT id, name, category, specialization FROM instructors WHERE LOWER(TRIM(category)) = LOWER(TRIM(?)) ORDER BY name ASC');
    $stmt->execute([$c['category']]);
    $insList = $stmt->fetchAll();
    echo "=== Class: [" . $c['id'] . "] " . $c['name'] . " (Kategori: " . $c['category'] . ") ===\n";
    foreach ($insList as $ins) {
        echo "  [ID " . $ins['id'] . "] " . $ins['name'] . " — " . $ins['specialization'] . "\n";
    }
    echo "\n";
}
