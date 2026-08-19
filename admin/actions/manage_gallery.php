<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']);
    exit;
}

require_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST['action'])) {
    $maxPost = (int)ini_get('post_max_size');
    if (isset($_SERVER['CONTENT_LENGTH']) && (int)$_SERVER['CONTENT_LENGTH'] > $maxPost * 1024 * 1024) {
        echo json_encode(['status' => 'error', 'message' => 'Total ukuran foto terlalu besar (batas maksimal ' . $maxPost . 'MB). Kurangi jumlah atau ukuran foto.']);
        exit;
    }
}

$action = $_POST['action'] ?? '';

if ($action === 'create') {
    $title = trim($_POST['title'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $showOnHome = isset($_POST['show_on_home']) ? 1 : 0;

    $maxPost = (int)ini_get('post_max_size');
    if (isset($_SERVER['CONTENT_LENGTH']) && (int)$_SERVER['CONTENT_LENGTH'] > $maxPost * 1024 * 1024) {
        echo json_encode(['status' => 'error', 'message' => 'Total ukuran foto terlalu besar (batas maksimal ' . $maxPost . 'MB). Kurangi jumlah atau ukuran foto.']);
        exit;
    }
    
    if (empty($title) || empty($category)) {
        echo json_encode(['status' => 'error', 'message' => 'Semua kolom wajib diisi.']);
        exit;
    }

    if (!isset($_FILES['images']) || empty($_FILES['images']['name'][0])) {
        echo json_encode(['status' => 'error', 'message' => 'Minimal satu gambar wajib diupload.']);
        exit;
    }

    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    $maxUpload = ini_get('upload_max_filesize');
    $successCount = 0;
    $totalFiles = count($_FILES['images']['name']);
    $errors = [];

    $uploadDir = '../../uploads/gallery';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    for ($i = 0; $i < $totalFiles; $i++) {
        $filename = $_FILES['images']['name'][$i];
        $errCode = $_FILES['images']['error'][$i];

        if ($errCode === UPLOAD_ERR_INI_SIZE || $errCode === UPLOAD_ERR_FORM_SIZE) {
            $errors[] = "'$filename' melebihi batas ukuran upload ($maxUpload per file).";
            continue;
        }
        if ($errCode === UPLOAD_ERR_PARTIAL) {
            $errors[] = "'$filename' terupload tidak lengkap, coba lagi.";
            continue;
        }
        if ($errCode === UPLOAD_ERR_NO_FILE) {
            continue;
        }
        if ($errCode !== UPLOAD_ERR_OK) {
            $errors[] = "'$filename' gagal terupload (kode error $errCode).";
            continue;
        }

        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $newFilename = uniqid() . '_' . $i . '.' . $ext;
            $destination = 'uploads/gallery/' . $newFilename;
            
            if (move_uploaded_file($_FILES['images']['tmp_name'][$i], '../../' . $destination)) {
                $itemTitle = ($totalFiles > 1) ? ($title . ' (' . ($i + 1) . ')') : $title;
                $stmt = $pdo->prepare("INSERT INTO gallery (category, title, image, show_on_home) VALUES (?, ?, ?, ?)");
                if ($stmt->execute([$category, $itemTitle, $destination, $showOnHome])) {
                    $successCount++;
                }
            }
        } else {
            $errors[] = "'$filename' format tidak didukung (hanya jpg/jpeg/png/webp).";
        }
    }

    if ($successCount > 0) {
        $msg = "$successCount foto berhasil ditambahkan.";
        if (!empty($errors)) $msg .= ' ' . implode(' ', $errors);
        echo json_encode(['status' => 'success', 'message' => $msg]);
    } elseif (!empty($errors)) {
        echo json_encode(['status' => 'error', 'message' => implode(' ', $errors)]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal mengupload foto. Pastikan format file sesuai.']);
    }

} elseif ($action === 'update') {
    $id = trim($_POST['id'] ?? '');
    $title = trim($_POST['title'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $showOnHome = isset($_POST['show_on_home']) ? 1 : 0;

    if (empty($id) || empty($title) || empty($category)) {
        echo json_encode(['status' => 'error', 'message' => 'Semua kolom wajib diisi.']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT image FROM gallery WHERE id=?");
    $stmt->execute([$id]);
    $photo = $stmt->fetch();
    if (!$photo) {
        echo json_encode(['status' => 'error', 'message' => 'Foto tidak ditemukan.']);
        exit;
    }

    $image = $photo['image'];

    if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
        $filename = $_FILES['images']['name'][0];
        $errCode = $_FILES['images']['error'][0];

        if ($errCode === UPLOAD_ERR_INI_SIZE || $errCode === UPLOAD_ERR_FORM_SIZE) {
            echo json_encode(['status' => 'error', 'message' => 'File melebihi batas ukuran upload (' . ini_get('upload_max_filesize') . ' per file).']);
            exit;
        }
        if ($errCode === UPLOAD_ERR_NO_FILE || $errCode === UPLOAD_ERR_PARTIAL) {
            echo json_encode(['status' => 'error', 'message' => 'File gagal terupload, coba lagi.']);
            exit;
        }

        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            echo json_encode(['status' => 'error', 'message' => 'Format gambar tidak didukung (hanya jpg/jpeg/png/webp).']);
            exit;
        }

        $newFilename = uniqid() . '_0.' . $ext;
        $destination = 'uploads/gallery/' . $newFilename;

        if (move_uploaded_file($_FILES['images']['tmp_name'][0], '../../' . $destination)) {
            if (strpos($photo['image'], 'http') !== 0 && file_exists('../../' . $photo['image'])) {
                unlink('../../' . $photo['image']);
            }
            $image = $destination;
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan gambar baru.']);
            exit;
        }
    }

    $upd = $pdo->prepare("UPDATE gallery SET title = ?, category = ?, image = ?, show_on_home = ? WHERE id = ?");
    if ($upd->execute([$title, $category, $image, $showOnHome, $id])) {
        echo json_encode(['status' => 'success', 'message' => 'Foto berhasil diperbarui.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui foto.']);
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
