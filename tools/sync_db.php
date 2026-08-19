<?php
require_once dirname(__DIR__) . '/config/database.php';

try {
    $sql = file_get_contents(dirname(__DIR__) . '/database/schema.sql');
    
    // Normalize collation for XAMPP / MariaDB compatibility
    $sql = str_replace('utf8mb4_0900_ai_ci', 'utf8mb4_unicode_ci', $sql);
    file_put_contents(dirname(__DIR__) . '/database/schema.sql', $sql);

    // Disable foreign keys, run transaction
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");

    // Drop old tables first to ensure clean state with all newest columns
    $tablesToDrop = [
        'certificates', 'certificate_templates', 'finance_transactions',
        'quiz_attempts', 'quiz_questions', 'material_progress', 'materials',
        'enrollments', 'orders', 'testimonials', 'chat_messages',
        'chatbot_intents', 'gallery', 'instructors', 'certifications',
        'class_categories', 'classes', 'users', 'admins', 'examiners', 'bookings'
    ];

    foreach ($tablesToDrop as $tbl) {
        $pdo->exec("DROP TABLE IF EXISTS `$tbl`;");
    }

    // Execute dump
    $pdo->exec($sql);
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

    // Verify all tables
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo "SUCCESS: Database successfully synced and connected with full schema!\n";
    echo "===============================================================\n";
    foreach ($tables as $t) {
        $count = $pdo->query("SELECT COUNT(*) FROM `$t`")->fetchColumn();
        echo sprintf("%-25s: %d rows\n", $t, $count);
    }
    echo "===============================================================\n";
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
