<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['admin_logged_in'])) exit;

require_once '../../config/database.php';

$action = $_POST['action'] ?? '';

if ($action === 'create') {
    $title = trim($_POST['title'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    
    if (empty($title)) {
        echo json_encode(['status' => 'error', 'message' => 'Judul sertifikasi wajib diisi.']);
        exit;
    }

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $dest = 'uploads/certs/' . uniqid() . '.' . $ext;
            if (move_uploaded_file($_FILES['image']['tmp_name'], '../../' . $dest)) {
                $stmt = $pdo->prepare("INSERT INTO certifications (title, description, image) VALUES (?, ?, ?)");
                $stmt->execute([$title, $desc, $dest]);
                echo json_encode(['status' => 'success', 'message' => 'Sertifikasi berhasil ditambahkan.']);
                exit;
            }
        }
    }
    
    // Default fallback
    $stmt = $pdo->prepare("INSERT INTO certifications (title, description, image) VALUES (?, ?, ?)");
    $stmt->execute([$title, $desc, 'assets/img/logo.png']);
    echo json_encode(['status' => 'success', 'message' => 'Sertifikasi berhasil ditambahkan (tanpa foto).']);
} elseif ($action === 'update') {
    $id = $_POST['id'] ?? null;
    $title = trim($_POST['title'] ?? '');
    $desc = trim($_POST['description'] ?? '');

    if (!$id || empty($title)) {
        echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap.']);
        exit;
    }

    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $dest = 'uploads/certs/' . uniqid() . '.' . $ext;
            if (move_uploaded_file($_FILES['image']['tmp_name'], '../../' . $dest)) {
                $image = $dest;
            }
        }
    }

    if ($image) {
        $stmt = $pdo->prepare("UPDATE certifications SET title = ?, description = ?, image = ? WHERE id = ?");
        $stmt->execute([$title, $desc, $image, $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE certifications SET title = ?, description = ? WHERE id = ?");
        $stmt->execute([$title, $desc, $id]);
    }
    echo json_encode(['status' => 'success', 'message' => 'Data sertifikasi diperbarui.']);
} elseif ($action === 'delete') {
    $id = $_POST['id'] ?? null;
    if ($id) {
        $stmt = $pdo->prepare("DELETE FROM certifications WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['status' => 'success', 'message' => 'Data sertifikasi dihapus.']);
    }
}
