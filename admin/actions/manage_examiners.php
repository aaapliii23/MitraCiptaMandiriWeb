<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['admin_logged_in'])) exit;

require_once '../../includes/db_config.php';

// Auto-create table if missing in user DB
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS `examiners` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `name` varchar(100) NOT NULL,
      `specialization` varchar(150) NOT NULL,
      `bio` text,
      `certifications` text,
      `image` varchar(255) DEFAULT NULL,
      `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
} catch (PDOException $e) {}

$action = $_POST['action'] ?? '';

if ($action === 'create') {
    $name = trim($_POST['name'] ?? '');
    $spec = trim($_POST['specialization'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    $certs = trim($_POST['certifications'] ?? '');

    if (empty($name) || empty($spec)) {
        echo json_encode(['status' => 'error', 'message' => 'Nama dan Spesialisasi wajib diisi.']);
        exit;
    }

    $image = 'assets/img/logo.png';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $dest = 'uploads/examiners/' . uniqid() . '.' . $ext;
            if (move_uploaded_file($_FILES['image']['tmp_name'], '../../' . $dest)) {
                $image = $dest;
            }
        }
    }

    $stmt = $pdo->prepare("INSERT INTO examiners (name, specialization, bio, certifications, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$name, $spec, $bio, $certs, $image]);
    echo json_encode(['status' => 'success', 'message' => 'Penguji berhasil ditambahkan.']);
} elseif ($action === 'update') {
    $id = $_POST['id'] ?? null;
    $name = trim($_POST['name'] ?? '');
    $spec = trim($_POST['specialization'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    $certs = trim($_POST['certifications'] ?? '');

    if (!$id || empty($name) || empty($spec)) {
        echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap.']);
        exit;
    }

    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $dest = 'uploads/examiners/' . uniqid() . '.' . $ext;
            if (move_uploaded_file($_FILES['image']['tmp_name'], '../../' . $dest)) {
                $image = $dest;
            }
        }
    }

    if ($image) {
        $stmt = $pdo->prepare("UPDATE examiners SET name = ?, specialization = ?, bio = ?, certifications = ?, image = ? WHERE id = ?");
        $stmt->execute([$name, $spec, $bio, $certs, $image, $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE examiners SET name = ?, specialization = ?, bio = ?, certifications = ? WHERE id = ?");
        $stmt->execute([$name, $spec, $bio, $certs, $id]);
    }
    echo json_encode(['status' => 'success', 'message' => 'Data penguji diperbarui.']);
} elseif ($action === 'delete') {
    $id = $_POST['id'] ?? null;
    if ($id) {
        $stmt = $pdo->prepare("DELETE FROM examiners WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['status' => 'success', 'message' => 'Data penguji dihapus.']);
    }
}
?>