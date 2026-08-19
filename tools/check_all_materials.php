<?php
require_once dirname(__DIR__) . '/config/database.php';
$classes = $pdo->query('SELECT id, name FROM classes')->fetchAll();
foreach ($classes as $c) {
    $materials = $pdo->prepare('SELECT id, title, type, sort_order FROM materials WHERE class_id = ? ORDER BY sort_order ASC, id ASC');
    $materials->execute([$c['id']]);
    $mList = $materials->fetchAll();
    echo "Class [{$c['id']}] {$c['name']} (Total: " . count($mList) . ")\n";
    foreach ($mList as $m) {
        echo "  - {$m['title']}\n";
    }
}
