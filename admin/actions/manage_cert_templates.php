<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['admin_logged_in'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

require_once '../../config/database.php';

try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS `certificate_templates` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `name` varchar(150) NOT NULL,
      `class_id` int(11) DEFAULT NULL,
      `layout` varchar(50) NOT NULL DEFAULT 'default',
      `bg_image` varchar(255) DEFAULT NULL,
      `accent_color` varchar(20) DEFAULT NULL,
      `is_default` tinyint(1) NOT NULL DEFAULT 0,
      `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
} catch (PDOException $e) {}

$action = $_POST['action'] ?? '';

if ($action === 'create' || $action === 'update') {
    $id = $_POST['id'] ?? '';
    $name = trim($_POST['name'] ?? '');
    $classId = !empty($_POST['class_id']) ? (int)$_POST['class_id'] : null;
    $layout = $_POST['layout'] ?? 'default';
    $accentColor = trim($_POST['accent_color'] ?? '');
    $isDefault = (int)($_POST['is_default'] ?? 0);

    if (!in_array($layout, ['default', 'elegant', 'modern', 'premium'])) $layout = 'default';
    if (!preg_match('/^#[0-9a-fA-F]{6}$/', $accentColor)) $accentColor = null;

    if (empty($name)) {
        echo json_encode(['status' => 'error', 'message' => 'Nama template wajib diisi']);
        exit;
    }

    $bgImage = null;
    $existing = null;
    if ($action === 'update') {
        try {
            $stmt = $pdo->prepare("SELECT bg_image FROM certificate_templates WHERE id = ?");
            $stmt->execute([$id]);
            $existing = $stmt->fetch();
        } catch (PDOException $e) {}
    }

    if (isset($_FILES['bg_image']) && $_FILES['bg_image']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo($_FILES['bg_image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $dir = dirname(__DIR__, 2) . '/uploads/certs/';
            if (!is_dir($dir)) mkdir($dir, 0775, true);
            $dest = $dir . uniqid() . '.' . $ext;
            if (move_uploaded_file($_FILES['bg_image']['tmp_name'], $dest)) {
                $bgImage = 'uploads/certs/' . basename($dest);
            }
        }
    } elseif ($existing && !empty($existing['bg_image'])) {
        $bgImage = $existing['bg_image'];
    }

    if ($isDefault) {
        try {
            $pdo->exec("UPDATE certificate_templates SET is_default = 0");
        } catch (PDOException $e) {}
    }

    try {
        if ($action === 'create') {
            $stmt = $pdo->prepare("INSERT INTO certificate_templates (name, class_id, layout, bg_image, accent_color, is_default) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $classId, $layout, $bgImage, $accentColor, $isDefault]);
        } else {
            $stmt = $pdo->prepare("UPDATE certificate_templates SET name = ?, class_id = ?, layout = ?, bg_image = ?, accent_color = ?, is_default = ? WHERE id = ?");
            $stmt->execute([$name, $classId, $layout, $bgImage, $accentColor, $isDefault, $id]);
        }
        echo json_encode(['status' => 'success', 'message' => $action === 'create' ? 'Template sertifikat berhasil ditambahkan' : 'Template sertifikat berhasil diperbarui']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan template sertifikat']);
    }
    exit;
}

if ($action === 'delete') {
    $id = $_POST['id'] ?? '';
    if (empty($id)) {
        echo json_encode(['status' => 'error', 'message' => 'ID tidak valid']);
        exit;
    }
    try {
        $stmt = $pdo->prepare("DELETE FROM certificate_templates WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['status' => 'success', 'message' => 'Template sertifikat berhasil dihapus']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus template sertifikat']);
    }
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Aksi tidak dikenali']);