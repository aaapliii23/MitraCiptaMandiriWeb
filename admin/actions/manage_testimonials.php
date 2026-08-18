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
    $pdo->exec("CREATE TABLE IF NOT EXISTS `testimonials` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `name` varchar(100) NOT NULL,
      `rating` tinyint(1) NOT NULL DEFAULT 5,
      `review` text NOT NULL,
      `image` varchar(255) DEFAULT NULL,
      `class_id` int(11) DEFAULT NULL,
      `status` ENUM('pending','approved','rejected') DEFAULT 'pending',
      `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
} catch (PDOException $e) {}

$action = $_POST['action'] ?? '';

if ($action === 'update') {
    $id = $_POST['id'] ?? '';
    $name = trim($_POST['name'] ?? '');
    $rating = (int)($_POST['rating'] ?? 5);
    $review = trim($_POST['review'] ?? '');
    $status = $_POST['status'] ?? 'pending';

    if ($rating < 1 || $rating > 5) $rating = 5;
    if (!in_array($status, ['pending', 'approved', 'rejected'])) $status = 'pending';

    if (empty($id) || empty($name) || empty($review)) {
        echo json_encode(['status' => 'error', 'message' => 'Nama dan ulasan wajib diisi']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("UPDATE testimonials SET name = ?, rating = ?, review = ?, status = ? WHERE id = ?");
        $stmt->execute([$name, $rating, $review, $status, $id]);
        echo json_encode(['status' => 'success', 'message' => 'Testimoni berhasil diperbarui']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui testimoni']);
    }
    exit;
}

if ($action === 'approve' || $action === 'reject') {
    $id = $_POST['id'] ?? '';
    $newStatus = ($action === 'approve') ? 'approved' : 'rejected';
    try {
        $stmt = $pdo->prepare("UPDATE testimonials SET status = ? WHERE id = ?");
        $stmt->execute([$newStatus, $id]);
        $msg = ($action === 'approve') ? 'Testimoni disetujui dan kini tampil di website.' : 'Testimoni ditolak.';
        echo json_encode(['status' => 'success', 'message' => $msg]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal mengubah status testimoni']);
    }
    exit;
}

if ($action === 'delete') {
    $id = $_POST['id'] ?? '';
    try {
        $stmt = $pdo->prepare("DELETE FROM testimonials WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['status' => 'success', 'message' => 'Testimoni berhasil dihapus']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus testimoni']);
    }
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Aksi tidak dikenali']);
?>