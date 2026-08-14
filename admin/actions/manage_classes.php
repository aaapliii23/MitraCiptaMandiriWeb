<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']);
    exit;
}

require_once '../../includes/db_config.php';

$action = $_POST['action'] ?? '';

if ($action === 'create' || $action === 'update') {
    $id = $_POST['id'] ?? '';
    $name = trim($_POST['name'] ?? '');
    $start_date = trim($_POST['start_date'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $features_raw = trim($_POST['features'] ?? '');
    $price = trim($_POST['price'] ?? '');
    
    if (empty($name) || empty($start_date) || empty($category) || empty($description) || empty($features_raw) || empty($price)) {
        echo json_encode(['status' => 'error', 'message' => 'Semua kolom wajib diisi (termasuk harga).']);
        exit;
    }

    // Convert features comma-separated string to JSON array
    $features_array = array_map('trim', explode(',', $features_raw));
    $features_json = json_encode($features_array);

    // Image Upload Handling
    $imagePath = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $filename = $_FILES['image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (!in_array($ext, $allowed)) {
            echo json_encode(['status' => 'error', 'message' => 'Format file tidak didukung.']);
            exit;
        }
        
        $newFilename = uniqid() . '.' . $ext;
        $destination = 'uploads/classes/' . $newFilename;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], '../../' . $destination)) {
            $imagePath = $destination;
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal mengupload gambar.']);
            exit;
        }
    }

    if ($action === 'create') {
        if (empty($imagePath)) {
            echo json_encode(['status' => 'error', 'message' => 'Gambar wajib diupload untuk kelas baru.']);
            exit;
        }
        $stmt = $pdo->prepare("INSERT INTO classes (name, start_date, category, description, image, features, price) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$name, $start_date, $category, $description, $imagePath, $features_json, $price])) {
            echo json_encode(['status' => 'success', 'message' => 'Kelas berhasil ditambahkan.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan kelas.']);
        }
    } else { // Update
        if (!empty($imagePath)) {
            $stmt = $pdo->prepare("UPDATE classes SET name=?, start_date=?, category=?, description=?, image=?, features=?, price=? WHERE id=?");
            $res = $stmt->execute([$name, $start_date, $category, $description, $imagePath, $features_json, $price, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE classes SET name=?, start_date=?, category=?, description=?, features=?, price=? WHERE id=?");
            $res = $stmt->execute([$name, $start_date, $category, $description, $features_json, $price, $id]);
        }
        
        if ($res) echo json_encode(['status' => 'success', 'message' => 'Kelas berhasil diperbarui.']);
        else echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui kelas.']);
    }

} elseif ($action === 'delete') {
    $id = $_POST['id'] ?? '';
    if (empty($id)) {
        echo json_encode(['status' => 'error', 'message' => 'ID tidak valid.']);
        exit;
    }
    
    // Get image to delete from disk
    $stmt = $pdo->prepare("SELECT image FROM classes WHERE id=?");
    $stmt->execute([$id]);
    $class = $stmt->fetch();
    
    $del = $pdo->prepare("DELETE FROM classes WHERE id=?");
    if ($del->execute([$id])) {
        if ($class && strpos($class['image'], 'http') !== 0 && file_exists('../../' . $class['image'])) {
            unlink('../../' . $class['image']);
        }
        echo json_encode(['status' => 'success', 'message' => 'Kelas berhasil dihapus.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus kelas.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Aksi tidak valid.']);
}
