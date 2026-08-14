<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']);
    exit;
}

require_once '../../includes/db_config.php';

$action = $_POST['action'] ?? '';

if ($action === 'create') {
    $title = trim($_POST['title'] ?? '');
    $category = trim($_POST['category'] ?? '');
    
    if (empty($title) || empty($category)) {
        echo json_encode(['status' => 'error', 'message' => 'Semua kolom wajib diisi.']);
        exit;
    }

    if (!isset($_FILES['images']) || empty($_FILES['images']['name'][0])) {
        echo json_encode(['status' => 'error', 'message' => 'Minimal satu gambar wajib diupload.']);
        exit;
    }

    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    $successCount = 0;
    $totalFiles = count($_FILES['images']['name']);

    for ($i = 0; $i < $totalFiles; $i++) {
        if ($_FILES['images']['error'][$i] === UPLOAD_ERR_OK) {
            $filename = $_FILES['images']['name'][$i];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowed)) {
                $newFilename = uniqid() . '_' . $i . '.' . $ext;
                $destination = 'uploads/gallery/' . $newFilename;
                
                if (move_uploaded_file($_FILES['images']['tmp_name'][$i], '../../' . $destination)) {
                    $stmt = $pdo->prepare("INSERT INTO gallery (category, title, image) VALUES (?, ?, ?)");
                    if ($stmt->execute([$category, $title, $destination])) {
                        $successCount++;
                    }
                }
            }
        }
    }

    if ($successCount > 0) {
        echo json_encode(['status' => 'success', 'message' => "$successCount foto berhasil ditambahkan."]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal mengupload foto. Pastikan format file sesuai.']);
    }

} elseif ($action === 'delete') {
    $id = $_POST['id'] ?? '';
    if (empty($id)) {
        echo json_encode(['status' => 'error', 'message' => 'ID tidak valid.']);
        exit;
    }
    
    $stmt = $pdo->prepare("SELECT image FROM gallery WHERE id=?");
    $stmt->execute([$id]);
    $photo = $stmt->fetch();
    
    $del = $pdo->prepare("DELETE FROM gallery WHERE id=?");
    if ($del->execute([$id])) {
        if ($photo && strpos($photo['image'], 'http') !== 0 && file_exists('../../' . $photo['image'])) {
            unlink('../../' . $photo['image']);
        }
        echo json_encode(['status' => 'success', 'message' => 'Foto berhasil dihapus.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus foto.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Aksi tidak valid.']);
}
