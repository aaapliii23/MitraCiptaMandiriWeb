<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['admin_logged_in'])) exit;

require_once '../../config/database.php';
require_once '../../includes/cloudinary.php';
try { $cols = $pdo->query("SHOW COLUMNS FROM certifications")->fetchAll(PDO::FETCH_COLUMN); if (!in_array('image_public_id', $cols)) $pdo->exec("ALTER TABLE certifications ADD COLUMN image_public_id VARCHAR(255) DEFAULT NULL AFTER image"); } catch (Throwable $e) {}

$action = $_POST['action'] ?? '';

if ($action === 'create') {
    $title = trim($_POST['title'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    
    if (empty($title)) {
        echo json_encode(['status' => 'error', 'message' => 'Judul sertifikasi wajib diisi.']);
        exit;
    }

    if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) { echo json_encode(['status'=>'error','message'=>'Upload gagal.']); exit; }
        $res = uploadImageToCloudinary($_FILES['image'], 'mcm/certs');
        if (!$res['ok']) { echo json_encode(['status'=>'error','message'=>$res['error']]); exit; }
        $imagePublicId = $res['public_id'] ?? cloudinaryPublicIdFromUrl($res['url']);
        $stmt = $pdo->prepare("INSERT INTO certifications (title, description, image, image_public_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([$title, $desc, $res['url'], $imagePublicId]);
        echo json_encode(['status'=>'success','message'=>'Sertifikasi berhasil ditambahkan.']); exit;
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

    $image = null; $imagePublicId = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) { echo json_encode(['status'=>'error','message'=>'Upload gagal.']); exit; }
        $res = uploadImageToCloudinary($_FILES['image'], 'mcm/certs');
        if (!$res['ok']) { echo json_encode(['status'=>'error','message'=>$res['error']]); exit; }
        $image = $res['url']; $imagePublicId = $res['public_id'] ?? cloudinaryPublicIdFromUrl($res['url']);
    }

    if ($image) {
        $stmt = $pdo->prepare("UPDATE certifications SET title = ?, description = ?, image = ?, image_public_id = ? WHERE id = ?");
        $stmt->execute([$title, $desc, $image, $imagePublicId, $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE certifications SET title = ?, description = ? WHERE id = ?");
        $stmt->execute([$title, $desc, $id]);
    }
    echo json_encode(['status' => 'success', 'message' => 'Data sertifikasi diperbarui.']);
} elseif ($action === 'delete') {
    $id = $_POST['id'] ?? null;
    if ($id) {
        try { $stmt = $pdo->prepare("SELECT image, image_public_id FROM certifications WHERE id = ?"); $stmt->execute([$id]); $row = $stmt->fetch(); } catch (PDOException $e) { $stmt = $pdo->prepare("SELECT image FROM certifications WHERE id = ?"); $stmt->execute([$id]); $row = $stmt->fetch(); if ($row) $row['image_public_id'] = ''; }
        $stmt = $pdo->prepare("DELETE FROM certifications WHERE id = ?");
        $stmt->execute([$id]);
        if ($row && (!empty($row['image_public_id']) || str_contains($row['image'] ?? '', 'res.cloudinary.com'))) { $pid = $row['image_public_id'] ?: $row['image']; $delRes = deleteImageFromCloudinary($pid); if (!$delRes['ok']) error_log("[Cloudinary delete certifications $id] ".$delRes['error']); }
        if ($row && strpos($row['image'] ?? '', 'http') !== 0 && !empty($row['image']) && file_exists('../../' . $row['image'])) { @unlink('../../' . $row['image']); }
        echo json_encode(['status' => 'success', 'message' => 'Data sertifikasi dihapus.']);
    }
}
