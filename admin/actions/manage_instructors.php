<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['admin_logged_in'])) exit;

require_once '../../includes/db_config.php';

$action = $_POST['action'] ?? '';

if ($action === 'create') {
    $name = trim($_POST['name'] ?? '');
    $spec = trim($_POST['specialization'] ?? '');
    
    if (empty($name) || empty($spec)) {
        echo json_encode(['status' => 'error', 'message' => 'Nama dan Spesialisasi wajib diisi.']);
        exit;
    }

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $dest = 'uploads/instructors/' . uniqid() . '.' . $ext;
            if (move_uploaded_file($_FILES['image']['tmp_name'], '../../' . $dest)) {
                $stmt = $pdo->prepare("INSERT INTO instructors (name, specialization, image) VALUES (?, ?, ?)");
                $stmt->execute([$name, $spec, $dest]);
                echo json_encode(['status' => 'success', 'message' => 'Instruktur berhasil ditambahkan.']);
                exit;
            }
        }
    }
    
    // Default fallback if no image or upload fails
    $stmt = $pdo->prepare("INSERT INTO instructors (name, specialization, image) VALUES (?, ?, ?)");
    $stmt->execute([$name, $spec, 'assets/img/logo.png']);
    echo json_encode(['status' => 'success', 'message' => 'Instruktur berhasil ditambahkan (tanpa foto).']);
} elseif ($action === 'update') {
    $id = $_POST['id'] ?? null;
    $name = trim($_POST['name'] ?? '');
    $spec = trim($_POST['specialization'] ?? '');

    if (!$id || empty($name) || empty($spec)) {
        echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap.']);
        exit;
    }

    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $dest = 'uploads/instructors/' . uniqid() . '.' . $ext;
            if (move_uploaded_file($_FILES['image']['tmp_name'], '../../' . $dest)) {
                $image = $dest;
            }
        }
    }

    if ($image) {
        $stmt = $pdo->prepare("UPDATE instructors SET name = ?, specialization = ?, image = ? WHERE id = ?");
        $stmt->execute([$name, $spec, $image, $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE instructors SET name = ?, specialization = ? WHERE id = ?");
        $stmt->execute([$name, $spec, $id]);
    }
    echo json_encode(['status' => 'success', 'message' => 'Data instruktur diperbarui.']);
} elseif ($action === 'delete') {
    $id = $_POST['id'] ?? null;
    if ($id) {
        $stmt = $pdo->prepare("DELETE FROM instructors WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['status' => 'success', 'message' => 'Data instruktur dihapus.']);
    }
}
