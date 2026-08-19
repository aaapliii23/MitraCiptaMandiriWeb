<?php
require_once dirname(__DIR__) . '/config/database.php';
$stmt = $pdo->query('SELECT id, title, category, image, show_on_home FROM gallery ORDER BY id ASC');
$rows = $stmt->fetchAll();
foreach ($rows as $r) {
    echo "[{$r['id']}] {$r['title']} ({$r['category']}) -> {$r['image']} (home: {$r['show_on_home']})\n";
}
