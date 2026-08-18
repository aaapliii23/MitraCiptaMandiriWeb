<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['admin_logged_in'])) exit;

require_once '../../includes/db_config.php';

try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS `materials` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `class_id` int(11) NOT NULL,
      `title` varchar(150) NOT NULL,
      `type` ENUM('video','pdf','text') NOT NULL DEFAULT 'text',
      `content` text NOT NULL,
      `sort_order` int(11) NOT NULL DEFAULT 0,
      `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    $pdo->exec("CREATE TABLE IF NOT EXISTS `material_progress` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `user_id` int(11) NOT NULL,
      `material_id` int(11) NOT NULL,
      `completed` tinyint(1) NOT NULL DEFAULT 0,
      `completed_at` timestamp NULL DEFAULT NULL,
      PRIMARY KEY (`id`),
      UNIQUE KEY `uq_progress_user_material` (`user_id`, `material_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
} catch (PDOException $e) {}

$action = $_POST['action'] ?? '';

function uploadPdf($field)
{
    if (!isset($_FILES[$field]) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) return null;
    $ext = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
    if ($ext !== 'pdf') return null;
    $dest = 'uploads/materials/' . uniqid() . '.pdf';
    if (move_uploaded_file($_FILES[$field]['tmp_name'], '../../' . $dest)) {
        return $dest;
    }
    return null;
}

function deleteMaterialFile($content)
{
    if ($content && strpos($content, 'uploads/materials/') === 0) {
        $path = dirname(__DIR__, 2) . '/' . $content;
        if (is_file($path)) @unlink($path);
    }
}

if ($action === 'create') {
    $classId = (int)($_POST['class_id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $type = $_POST['type'] ?? 'text';
    $content = trim($_POST['content'] ?? '');
    $sortOrder = (int)($_POST['sort_order'] ?? 0);

    if (!$classId || empty($title) || !in_array($type, ['video', 'pdf', 'text'])) {
        echo json_encode(['status' => 'error', 'message' => 'Data materi tidak lengkap.']);
        exit;
    }

    if ($type === 'pdf') {
        $uploaded = uploadPdf('content_file');
        if ($uploaded) {
            $content = $uploaded;
        }
    }

    if (empty($content)) {
        echo json_encode(['status' => 'error', 'message' => 'Konten materi wajib diisi.']);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO materials (class_id, title, type, content, sort_order) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$classId, $title, $type, $content, $sortOrder]);
    echo json_encode(['status' => 'success', 'message' => 'Materi berhasil ditambahkan.']);
} elseif ($action === 'update') {
    $id = (int)($_POST['id'] ?? 0);
    $classId = (int)($_POST['class_id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $type = $_POST['type'] ?? 'text';
    $content = trim($_POST['content'] ?? '');
    $sortOrder = (int)($_POST['sort_order'] ?? 0);

    if (!$id || !$classId || empty($title) || !in_array($type, ['video', 'pdf', 'text'])) {
        echo json_encode(['status' => 'error', 'message' => 'Data materi tidak lengkap.']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT content FROM materials WHERE id = ?");
    $stmt->execute([$id]);
    $oldContent = $stmt->fetchColumn();

    if ($type === 'pdf') {
        $uploaded = uploadPdf('content_file');
        if ($uploaded) {
            deleteMaterialFile($oldContent);
            $content = $uploaded;
        }
    } elseif ($oldContent && strpos($oldContent, 'uploads/materials/') === 0) {
        deleteMaterialFile($oldContent);
    }

    if (empty($content)) {
        echo json_encode(['status' => 'error', 'message' => 'Konten materi wajib diisi.']);
        exit;
    }

    $stmt = $pdo->prepare("UPDATE materials SET class_id = ?, title = ?, type = ?, content = ?, sort_order = ? WHERE id = ?");
    $stmt->execute([$classId, $title, $type, $content, $sortOrder, $id]);
    echo json_encode(['status' => 'success', 'message' => 'Materi diperbarui.']);
} elseif ($action === 'delete') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id) {
        $stmt = $pdo->prepare("SELECT content FROM materials WHERE id = ?");
        $stmt->execute([$id]);
        deleteMaterialFile($stmt->fetchColumn());
        $pdo->prepare("DELETE FROM material_progress WHERE material_id = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM materials WHERE id = ?")->execute([$id]);
        echo json_encode(['status' => 'success', 'message' => 'Materi dihapus.']);
    }
}