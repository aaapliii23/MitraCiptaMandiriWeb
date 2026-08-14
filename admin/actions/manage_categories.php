<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['admin_logged_in'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}
require_once '../../includes/db_config.php';

// Auto-create table if missing in user DB
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS `class_categories` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `name` varchar(100) NOT NULL,
      `slug` varchar(100) NOT NULL,
      `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      UNIQUE KEY `slug` (`slug`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
} catch (PDOException $e) {}

$action = $_POST['action'] ?? '';

if ($action === 'create') {
    $name = trim($_POST['name'] ?? '');
    $slug = strtolower(str_replace(' ', '_', $name));
    
    if (empty($name)) {
        echo json_encode(['status' => 'error', 'message' => 'Nama kategori wajib diisi']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO class_categories (name, slug) VALUES (?, ?)");
        $stmt->execute([$name, $slug]);
        echo json_encode(['status' => 'success', 'message' => 'Kategori berhasil ditambahkan']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan kategori: ' . $e->getMessage()]);
    }
    exit;
}

if ($action === 'update') {
    $id = $_POST['id'] ?? '';
    $name = $_POST['name'] ?? '';
    $slug = strtolower(str_replace(' ', '_', $name));

    if (empty($id) || empty($name)) {
        echo json_encode(['status' => 'error', 'message' => 'Nama kategori wajib diisi']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("UPDATE class_categories SET name = ?, slug = ? WHERE id = ?");
        $stmt->execute([$name, $slug, $id]);
        echo json_encode(['status' => 'success', 'message' => 'Kategori berhasil diperbarui']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui kategori: ' . $e->getMessage()]);
    }
}

if ($action === 'delete') {
    $id = $_POST['id'] ?? '';
    try {
        $stmt = $pdo->prepare("DELETE FROM class_categories WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['status' => 'success', 'message' => 'Kategori berhasil dihapus']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus kategori']);
    }
    exit;
}
?>
