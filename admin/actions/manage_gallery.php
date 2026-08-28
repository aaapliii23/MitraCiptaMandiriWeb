<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    error_log("PHP Error [$errno]: $errstr in $errfile:$errline");
    return true;
});
register_shutdown_function(function () {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        while (ob_get_level() > 0) { ob_end_clean(); }
        if (!headers_sent()) {
            header('Content-Type: application/json');
        }
        echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan sistem saat upload. Coba lagi atau hubungi admin.']);
    }
});
function parseIniSize($val) {
    $val = trim($val);
    $last = strtolower(substr($val, -1));
    $num = (float)$val;
    switch ($last) {
        case 'g': return (int)($num * 1024 * 1024 * 1024);
        case 'm': return (int)($num * 1024 * 1024);
        case 'k': return (int)($num * 1024);
        default: return (int)$num;
    }
}
function respondJson($data) {
    while (ob_get_level() > 0) { ob_end_clean(); }
    if (!headers_sent()) {
        header('Content-Type: application/json');
    }
    echo json_encode($data);
    exit;
}
session_start();
if (!headers_sent()) {
    header('Content-Type: application/json');
}

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    respondJson(['status' => 'error', 'message' => 'Akses ditolak.']);
}

require_once '../../config/database.php';
require_once '../../includes/cloudinary.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST['action'])) {
    $maxPost = parseIniSize(ini_get('post_max_size'));
    if (isset($_SERVER['CONTENT_LENGTH']) && (int)$_SERVER['CONTENT_LENGTH'] > $maxPost) {
        $maxPostMb = round($maxPost / 1024 / 1024);
        respondJson(['status' => 'error', 'message' => 'Total ukuran foto terlalu besar (batas maksimal ' . $maxPostMb . 'MB). Kurangi jumlah atau ukuran foto.']);
    }
}

$action = $_POST['action'] ?? '';

if ($action === 'create') {
    try {
        $title = trim($_POST['title'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $showOnHome = isset($_POST['show_on_home']) ? 1 : 0;

        $maxPost = parseIniSize(ini_get('post_max_size'));
        if (isset($_SERVER['CONTENT_LENGTH']) && (int)$_SERVER['CONTENT_LENGTH'] > $maxPost) {
            $maxPostMb = round($maxPost / 1024 / 1024);
            respondJson(['status' => 'error', 'message' => 'Total ukuran foto terlalu besar (batas maksimal ' . $maxPostMb . 'MB). Kurangi jumlah atau ukuran foto.']);
        }
        
        if (empty($title) || empty($category)) {
            respondJson(['status' => 'error', 'message' => 'Semua kolom wajib diisi.']);
        }

        if (!isset($_FILES['images']) || empty($_FILES['images']['name'][0])) {
            respondJson(['status' => 'error', 'message' => 'Minimal satu gambar wajib diupload.']);
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

            $file = ['name'=>$_FILES['images']['name'][$i],'type'=>$_FILES['images']['type'][$i],'tmp_name'=>$_FILES['images']['tmp_name'][$i],'error'=>$_FILES['images']['error'][$i],'size'=>$_FILES['images']['size'][$i]];
            $res = uploadImageToCloudinary($file, 'mcm/gallery');
            if ($res['ok']) {
                $destination = $res['url'];
                $publicId = $res['public_id'] ?? cloudinaryPublicIdFromUrl($res['url']);
                $itemTitle = ($totalFiles > 1) ? ($title . ' (' . ($i + 1) . ')') : $title;
                $stmt = $pdo->prepare("INSERT INTO gallery (category, title, image, image_public_id, show_on_home) VALUES (?, ?, ?, ?, ?)");
                if ($stmt->execute([$category, $itemTitle, $destination, $publicId, $showOnHome])) {
                    $successCount++;
                }
            } else {
                $errors[] = "'$filename' " . $res['error'];
            }
        }

        if ($successCount > 0) {
            $msg = "$successCount foto berhasil ditambahkan.";
            if (!empty($errors)) $msg .= ' ' . implode(' ', $errors);
            respondJson(['status' => 'success', 'message' => $msg]);
        } elseif (!empty($errors)) {
            respondJson(['status' => 'error', 'message' => implode(' ', $errors)]);
        } else {
            respondJson(['status' => 'error', 'message' => 'Gagal mengupload foto. Pastikan format file sesuai.']);
        }
    } catch (Throwable $e) {
        error_log('manage_gallery create error: '.$e->getMessage().' in '.$e->getFile().':'.$e->getLine());
        respondJson(['status' => 'error', 'message' => 'Terjadi kesalahan saat memproses upload: ' . $e->getMessage()]);
    }

} elseif ($action === 'update') {
    try {
        $id = trim($_POST['id'] ?? '');
        $title = trim($_POST['title'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $showOnHome = isset($_POST['show_on_home']) ? 1 : 0;

        if (empty($id) || empty($title) || empty($category)) {
            respondJson(['status' => 'error', 'message' => 'Semua kolom wajib diisi.']);
        }

        $stmt = $pdo->prepare("SELECT image, image_public_id FROM gallery WHERE id=?");
        $stmt->execute([$id]);
        $photo = $stmt->fetch();
        if (!$photo) {
            respondJson(['status' => 'error', 'message' => 'Foto tidak ditemukan.']);
        }

        $image = $photo['image'];
        $imagePublicId = $photo['image_public_id'] ?? '';

        if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
            $filename = $_FILES['images']['name'][0];
            $errCode = $_FILES['images']['error'][0];

            if ($errCode === UPLOAD_ERR_INI_SIZE || $errCode === UPLOAD_ERR_FORM_SIZE) {
                respondJson(['status' => 'error', 'message' => 'File melebihi batas ukuran upload (' . ini_get('upload_max_filesize') . ' per file).']);
            }
            if ($errCode === UPLOAD_ERR_NO_FILE || $errCode === UPLOAD_ERR_PARTIAL) {
                respondJson(['status' => 'error', 'message' => 'File gagal terupload, coba lagi.']);
            }

            $file = ['name'=>$_FILES['images']['name'][0],'type'=>$_FILES['images']['type'][0],'tmp_name'=>$_FILES['images']['tmp_name'][0],'error'=>$_FILES['images']['error'][0],'size'=>$_FILES['images']['size'][0]];
            $res = uploadImageToCloudinary($file, 'mcm/gallery');
            if ($res['ok']) {
                $image = $res['url']; $imagePublicId = $res['public_id'] ?? cloudinaryPublicIdFromUrl($res['url']);
            } else {
                respondJson(['status' => 'error', 'message' => $res['error']]);
            }
        }

        $upd = $pdo->prepare("UPDATE gallery SET title=?, category=?, image=?, image_public_id=?, show_on_home=? WHERE id=?");
        if ($upd->execute([$title, $category, $image, $imagePublicId, $showOnHome, $id])) {
            respondJson(['status' => 'success', 'message' => 'Foto berhasil diperbarui.']);
        } else {
            respondJson(['status' => 'error', 'message' => 'Gagal memperbarui foto.']);
        }
    } catch (Throwable $e) {
        error_log('manage_gallery update error: '.$e->getMessage().' in '.$e->getFile().':'.$e->getLine());
        respondJson(['status' => 'error', 'message' => 'Terjadi kesalahan saat memproses upload: ' . $e->getMessage()]);
    }

} elseif ($action === 'delete') {
    $id = $_POST['id'] ?? '';
    if (empty($id)) {
        respondJson(['status' => 'error', 'message' => 'ID tidak valid.']);
    }
    
    $stmt = $pdo->prepare("SELECT image, image_public_id FROM gallery WHERE id=?");
    $stmt->execute([$id]);
    $photo = $stmt->fetch();
    
    $del = $pdo->prepare("DELETE FROM gallery WHERE id=?");
    if ($del->execute([$id])) {
        if (!empty($photo['image_public_id']) || str_contains($photo['image'], 'res.cloudinary.com')) { $pid = $photo['image_public_id'] ?: $photo['image']; $delRes = deleteImageFromCloudinary($pid); if (!$delRes['ok']) error_log("[Cloudinary delete gallery $id] ".$delRes['error']); }
        if ($photo && strpos($photo['image'], 'http') !== 0 && file_exists('../../' . $photo['image'])) {
            unlink('../../' . $photo['image']);
        }
        respondJson(['status' => 'success', 'message' => 'Foto berhasil dihapus.']);
    } else {
        respondJson(['status' => 'error', 'message' => 'Gagal menghapus foto.']);
    }
} else {
    respondJson(['status' => 'error', 'message' => 'Aksi tidak valid.']);
}
