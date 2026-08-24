<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['admin_logged_in'])) exit;

require_once '../../config/database.php';

$action = $_POST['action'] ?? '';

if ($action === 'create') {
    $name = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $spec = trim($_POST['specialization'] ?? '');
    
    if (empty($name) || empty($category) || empty($spec)) {
        echo json_encode(['status' => 'error', 'message' => 'Nama, Kategori, dan Spesialisasi wajib diisi.']);
        exit;
    }

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $dest = 'uploads/instructors/' . uniqid() . '.' . $ext;
            if (move_uploaded_file($_FILES['image']['tmp_name'], '../../' . $dest)) {
                $stmt = $pdo->prepare("INSERT INTO instructors (name, category, specialization, image) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $category, $spec, $dest]);
                echo json_encode(['status' => 'success', 'message' => 'Instruktur berhasil ditambahkan.']);
                exit;
            }
        }
    }
    
    // Default fallback if no image or upload fails
    $stmt = $pdo->prepare("INSERT INTO instructors (name, category, specialization, image) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $category, $spec, 'assets/img/logo.png']);
    echo json_encode(['status' => 'success', 'message' => 'Instruktur berhasil ditambahkan (tanpa foto).']);
} elseif ($action === 'update') {
    $id = $_POST['id'] ?? null;
    $name = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $spec = trim($_POST['specialization'] ?? '');

    if (!$id || empty($name) || empty($category) || empty($spec)) {
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
        $stmt = $pdo->prepare("UPDATE instructors SET name = ?, category = ?, specialization = ?, image = ? WHERE id = ?");
        $stmt->execute([$name, $category, $spec, $image, $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE instructors SET name = ?, category = ?, specialization = ? WHERE id = ?");
        $stmt->execute([$name, $category, $spec, $id]);
    }
    echo json_encode(['status' => 'success', 'message' => 'Data instruktur diperbarui.']);
} elseif ($action === 'delete') {
    $id = $_POST['id'] ?? null;
    if (!$id) {
        echo json_encode(['status' => 'error', 'message' => 'ID tidak valid.']);
        exit;
    }
    try {
        $stmt = $pdo->prepare("DELETE FROM instructors WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['status' => 'success', 'message' => 'Data instruktur dihapus.']);
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) { // Integrity constraint violation
            echo json_encode([
                'status' => 'error',
                'message' => 'Instruktur ini tidak bisa dihapus karena masih dipakai oleh data lain (mis. pesanan kelas). Ganti instruktur pada kelas/pesanan terkait terlebih dahulu.'
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus instruktur: ' . $e->getMessage()]);
        }
    }
}
