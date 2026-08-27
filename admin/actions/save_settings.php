<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['admin_logged_in'])) exit;
require_once '../../config/database.php';
require_once '../../includes/cloudinary.php';

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

// Form "Update Logo": upload ke Cloudinary, simpan URL di settings (logo_url)
if ($isLogoUpload) {
    $res = uploadImageToCloudinary($_FILES['logo'], 'mcm/logo');
    if (!$res['ok']) { echo json_encode(['status'=>'error','message'=>$res['error']]); exit; }
    $upsert->execute(['logo_url', $res['url']]);
}

echo json_encode(['status' => 'success', 'message' => 'Pengaturan website berhasil diperbarui.']);
