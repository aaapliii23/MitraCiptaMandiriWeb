<?php
require_once dirname(__DIR__) . '/config/database.php';

$src = 'C:/Users/ASUS/.gemini/antigravity-ide/brain/8595c64b-1bd0-45ce-ac7f-f1cc1b22d9f9/.user_uploaded/media_1787122641243.jpg';
$dest = dirname(__DIR__) . '/assets/img/hero-bg.jpg';

if (!is_dir(dirname($dest))) {
    mkdir(dirname($dest), 0777, true);
}

if (file_exists($src)) {
    copy($src, $dest);
    echo "Copied successfully to: " . $dest . " (" . filesize($dest) . " bytes)\n";
} else {
    echo "Source not found: " . $src . "\n";
}

// Also update gallery if needed
try {
    $stmt = $pdo->prepare("UPDATE gallery SET image = 'assets/img/hero-bg.jpg' WHERE id = 1 OR image LIKE '%hero%' OR image LIKE '%photo-1475721027785%'");
    $stmt->execute();
    echo "Updated gallery table.\n";
} catch (PDOException $e) {
    echo "DB: " . $e->getMessage() . "\n";
}
