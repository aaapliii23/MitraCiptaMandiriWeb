<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']);
    exit;
}

require_once '../../config/database.php';

$action = $_POST['action'] ?? '';

if ($action === 'create' || $action === 'update') {
    $id = $_POST['id'] ?? '';
    $name = trim($_POST['name'] ?? '');
    $start_date = trim($_POST['start_date'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $features_raw = trim($_POST['features'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $price_online = trim($_POST['price_online'] ?? '');
    $price_offline = trim($_POST['price_offline'] ?? '');
    $mode_available = trim($_POST['mode_available'] ?? 'both');
    $description_online = trim($_POST['description_online'] ?? '');
    $description_offline = trim($_POST['description_offline'] ?? '');
    $whatsapp_group_link = trim($_POST['whatsapp_group_link'] ?? '');

    if (!in_array($mode_available, ['online','offline','both'], true)) $mode_available = 'both';

    // Fallback: jika form lama hanya kirim price tunggal
    if ($price_offline === '' && $price !== '') $price_offline = $price;
    if ($price_online === '' && $price !== '') $price_online = (string)round((int)$price * 0.8);
    if ($price === '' && $price_offline !== '') $price = $price_offline;

    // Validasi wajib
    if (empty($name) || empty($start_date) || empty($category) || empty($description) || empty($features_raw)) {
        echo json_encode(['status' => 'error', 'message' => 'Semua kolom wajib diisi.']);
        exit;
    }
    if ($mode_available === 'both' && (empty($price_offline) || empty($price_online))) {
        echo json_encode(['status' => 'error', 'message' => 'Harga Online & Offline wajib diisi untuk mode Keduanya.']);
        exit;
    }
    if ($mode_available === 'offline' && empty($price_offline)) {
        echo json_encode(['status' => 'error', 'message' => 'Harga Offline wajib diisi.']);
        exit;
    }
    if ($mode_available === 'online' && empty($price_online)) {
        echo json_encode(['status' => 'error', 'message' => 'Harga Online wajib diisi.']);
        exit;
    }
    // Jika salah satu kosong, fallback
    if ($mode_available === 'offline' && empty($price_online)) $price_online = $price_offline;
    if ($mode_available === 'online' && empty($price_offline)) $price_offline = $price_online;
    // Pastikan price legacy terisi
    if (empty($price)) $price = $price_offline;

    if (!is_numeric($price_online) || !is_numeric($price_offline) || !is_numeric($price) || (int)$price_online < 0 || (int)$price_offline < 0) {
        echo json_encode(['status' => 'error', 'message' => 'Harga harus angka valid.']);
        exit;
    }

    // Validasi link WA: harus https://chat.whatsapp.com/ jika diisi, dan wajib jika mode mencakup offline
    if ($whatsapp_group_link !== '' && strpos($whatsapp_group_link, 'https://chat.whatsapp.com/') !== 0) {
        echo json_encode(['status' => 'error', 'message' => 'Link Grup WhatsApp harus diawali https://chat.whatsapp.com/']);
        exit;
    }
    if (in_array($mode_available, ['offline','both'], true) && $whatsapp_group_link === '') {
        echo json_encode(['status' => 'error', 'message' => 'Link Grup WhatsApp wajib diisi untuk mode Offline (format https://chat.whatsapp.com/...).']);
        exit;
    }
    if ($mode_available === 'online') $whatsapp_group_link = null;
    if ($whatsapp_group_link === '') $whatsapp_group_link = null;

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

    // Cek kolom baru ada atau tidak (untuk backward compat)
    $hasNewCols = true;
    try {
        $pdo->query("SELECT price_online, price_offline, mode_available FROM classes LIMIT 1");
    } catch (PDOException $e) { $hasNewCols = false; }
    $hasDescCols = true;
    try {
        $pdo->query("SELECT description_online, description_offline FROM classes LIMIT 1");
    } catch (PDOException $e) { $hasDescCols = false; }
    $hasWaLink = true;
    try { $pdo->query("SELECT whatsapp_group_link FROM classes LIMIT 1"); } catch (PDOException $e) { $hasWaLink = false; }

    if (empty($description_online)) $description_online = $description;
    if (empty($description_offline)) $description_offline = $description;

    if ($action === 'create') {
        if (empty($imagePath)) {
            echo json_encode(['status' => 'error', 'message' => 'Gambar wajib diupload untuk kelas baru.']);
            exit;
        }
        if ($hasNewCols && $hasDescCols) {
            $stmt = $pdo->prepare("INSERT INTO classes (name, start_date, category, description, description_online, description_offline, image, features, price, price_online, price_offline, mode_available) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $ok = $stmt->execute([$name, $start_date, $category, $description, $description_online, $description_offline, $imagePath, $features_json, $price, $price_online, $price_offline, $mode_available]);
        } elseif ($hasNewCols) {
            $stmt = $pdo->prepare("INSERT INTO classes (name, start_date, category, description, image, features, price, price_online, price_offline, mode_available) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $ok = $stmt->execute([$name, $start_date, $category, $description, $imagePath, $features_json, $price, $price_online, $price_offline, $mode_available]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO classes (name, start_date, category, description, image, features, price) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $ok = $stmt->execute([$name, $start_date, $category, $description, $imagePath, $features_json, $price]);
        }
        if ($ok && $hasWaLink) {
            try {
                $newId = $pdo->lastInsertId();
                $pdo->prepare("UPDATE classes SET whatsapp_group_link = ? WHERE id = ?")->execute([$whatsapp_group_link, $newId]);
            } catch (PDOException $e) {}
        }
        if ($ok) {
            echo json_encode(['status' => 'success', 'message' => 'Kelas berhasil ditambahkan.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan kelas.']);
        }
    } else { // Update
        if (!empty($imagePath)) {
            if ($hasNewCols && $hasDescCols) {
                $stmt = $pdo->prepare("UPDATE classes SET name=?, start_date=?, category=?, description=?, description_online=?, description_offline=?, image=?, features=?, price=?, price_online=?, price_offline=?, mode_available=? WHERE id=?");
                $res = $stmt->execute([$name, $start_date, $category, $description, $description_online, $description_offline, $imagePath, $features_json, $price, $price_online, $price_offline, $mode_available, $id]);
            } elseif ($hasNewCols) {
                $stmt = $pdo->prepare("UPDATE classes SET name=?, start_date=?, category=?, description=?, image=?, features=?, price=?, price_online=?, price_offline=?, mode_available=? WHERE id=?");
                $res = $stmt->execute([$name, $start_date, $category, $description, $imagePath, $features_json, $price, $price_online, $price_offline, $mode_available, $id]);
            } else {
                $stmt = $pdo->prepare("UPDATE classes SET name=?, start_date=?, category=?, description=?, image=?, features=?, price=? WHERE id=?");
                $res = $stmt->execute([$name, $start_date, $category, $description, $imagePath, $features_json, $price, $id]);
            }
        } else {
            if ($hasNewCols && $hasDescCols) {
                $stmt = $pdo->prepare("UPDATE classes SET name=?, start_date=?, category=?, description=?, description_online=?, description_offline=?, features=?, price=?, price_online=?, price_offline=?, mode_available=? WHERE id=?");
                $res = $stmt->execute([$name, $start_date, $category, $description, $description_online, $description_offline, $features_json, $price, $price_online, $price_offline, $mode_available, $id]);
            } elseif ($hasNewCols) {
                $stmt = $pdo->prepare("UPDATE classes SET name=?, start_date=?, category=?, description=?, features=?, price=?, price_online=?, price_offline=?, mode_available=? WHERE id=?");
                $res = $stmt->execute([$name, $start_date, $category, $description, $features_json, $price, $price_online, $price_offline, $mode_available, $id]);
            } else {
                $stmt = $pdo->prepare("UPDATE classes SET name=?, start_date=?, category=?, description=?, features=?, price=? WHERE id=?");
                $res = $stmt->execute([$name, $start_date, $category, $description, $features_json, $price, $id]);
            }
        }
        if ($res && $hasWaLink) {
            try { $pdo->prepare("UPDATE classes SET whatsapp_group_link = ? WHERE id = ?")->execute([$whatsapp_group_link, $id]); } catch (PDOException $e) {}
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
