<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['admin_logged_in'])) exit;
require_once '../../config/database.php';

// Pastikan tabel settings ada
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS `settings` (
      `key` varchar(100) NOT NULL,
      `value` text NULL,
      PRIMARY KEY (`key`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
} catch (PDOException $e) {}

$upsert = $pdo->prepare("INSERT INTO settings (`key`,`value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)");

// Form "Simpan Konfigurasi" (teks)
$textKeys = ['admin_whatsapp', 'admin_email', 'admin_address', 'social_ig', 'social_fb', 'social_tt', 'maps_url'];
$isLogoUpload = isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK;

foreach ($textKeys as $k) {
    if (array_key_exists($k, $_POST)) {
        $upsert->execute([$k, trim($_POST[$k])]);
    }
}

// Form "Update Logo": timpa file logo statis agar header/footer/favicon publik ikut berubah
if ($isLogoUpload) {
    $ext = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['png', 'jpg', 'jpeg', 'webp'], true)) {
        echo json_encode(['status' => 'error', 'message' => 'Format logo harus PNG/JPG/WEBP.']);
        exit;
    }
    // ponytail: timpa selalu assets/img/logo.png (browser sniff konten, ekstensi tetap konsisten dengan semua template)
    $dest = dirname(__DIR__, 2) . '/assets/img/logo.png';
    if (!move_uploaded_file($_FILES['logo']['tmp_name'], $dest)) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan file logo.']);
        exit;
    }
}

echo json_encode(['status' => 'success', 'message' => 'Pengaturan website berhasil diperbarui.']);
