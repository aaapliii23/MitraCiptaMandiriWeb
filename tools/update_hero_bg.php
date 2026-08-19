<?php
require_once dirname(__DIR__) . '/config/database.php';

$src = 'C:/Users/ASUS/.gemini/antigravity-ide/brain/8595c64b-1bd0-45ce-ac7f-f1cc1b22d9f9/.user_uploaded/media_1787122641243.jpg';
$dest1 = dirname(__DIR__) . '/uploads/gallery/hero_workspace.jpg';
$dest2 = dirname(__DIR__) . '/assets/img/hero_workspace.jpg';

if (!is_dir(dirname(__DIR__) . '/uploads/gallery')) {
    mkdir(dirname(__DIR__) . '/uploads/gallery', 0777, true);
}

if (file_exists($src)) {
    copy($src, $dest1);
    copy($src, $dest2);
    echo "Image copied to: " . $dest1 . " (" . filesize($dest1) . " bytes)\n";
} else {
    echo "Source image not found at: " . $src . "\n";
}

// Update the gallery table where the mic image is (id = 1 or title LIKE '%Public Speaking%')
try {
    $stmt = $pdo->prepare("UPDATE gallery SET image = 'uploads/gallery/hero_workspace.jpg', title = 'Pelatihan & Diskusi Tim' WHERE id = 1 OR image LIKE '%photo-1475721027785%'");
    $stmt->execute();
    echo "Updated " . $stmt->rowCount() . " row(s) in gallery table.\n";
} catch (PDOException $e) {
    echo "DB Error: " . $e->getMessage() . "\n";
}
