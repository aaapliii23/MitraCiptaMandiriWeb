<?php
require_once dirname(__DIR__) . '/config/database.php';
try {
    $cols = $pdo->query('DESCRIBE enrollments')->fetchAll();
    foreach ($cols as $c) echo $c['Field'] . ' | ' . $c['Type'] . "\n";
} catch (PDOException $e) { echo "No enrollments table: " . $e->getMessage() . "\n"; }
