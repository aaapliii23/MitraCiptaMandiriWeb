<?php
require_once dirname(__DIR__) . '/config/database.php';
$cols = $pdo->query('DESCRIBE materials')->fetchAll();
foreach ($cols as $c) {
    echo $c['Field'] . ' | ' . $c['Type'] . "\n";
}
echo "\n--- Materials rows ---\n";
$stmt = $pdo->query('SELECT * FROM materials LIMIT 10');
foreach ($stmt->fetchAll() as $r) {
    echo $r['id'] . ' | class: ' . $r['class_id'] . ' | title: ' . $r['title'] . "\n";
}
