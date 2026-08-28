<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['admin_logged_in'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

require_once '../../config/database.php';
require_once '../../includes/cloudinary.php';

try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS `certificate_templates` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `name` varchar(150) NOT NULL,
      `class_id` int(11) DEFAULT NULL,
      `layout` varchar(50) NOT NULL DEFAULT 'default',
      `bg_image` varchar(255) DEFAULT NULL,
      `accent_color` varchar(20) DEFAULT NULL,
      `is_default` tinyint(1) NOT NULL DEFAULT 0,
      `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    $cols = $pdo->query("SHOW COLUMNS FROM certificate_templates")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('bg_image_public_id', $cols)) $pdo->exec("ALTER TABLE certificate_templates ADD COLUMN bg_image_public_id VARCHAR(255) DEFAULT NULL AFTER bg_image");
} catch (PDOException $e) {}
try { $cols2 = $pdo->query("SHOW COLUMNS FROM certificate_templates")->fetchAll(PDO::FETCH_COLUMN); if (!in_array('bg_image_public_id', $cols2)) $pdo->exec("ALTER TABLE certificate_templates ADD COLUMN bg_image_public_id VARCHAR(255) DEFAULT NULL AFTER bg_image"); } catch (Throwable $e) {}

$action = $_POST['action'] ?? '';

if ($action === 'create' || $action === 'update') {
    $id = $_POST['id'] ?? '';
    $name = trim($_POST['name'] ?? '');
    $classId = !empty($_POST['class_id']) ? (int)$_POST['class_id'] : null;
    $layout = $_POST['layout'] ?? 'default';
    $accentColor = trim($_POST['accent_color'] ?? '');
    $isDefault = (int)($_POST['is_default'] ?? 0);

    if (!in_array($layout, ['default', 'elegant', 'modern', 'premium'])) $layout = 'default';
    if (!preg_match('/^#[0-9a-fA-F]{6}$/', $accentColor)) $accentColor = null;

    if (empty($name)) {
        echo json_encode(['status' => 'error', 'message' => 'Nama template wajib diisi']);
        exit;
    }

    $bgImage = null; $bgImagePublicId = null;
    $existing = null;
    if ($action === 'update') {
        try {
            $stmt = $pdo->prepare("SELECT bg_image, bg_image_public_id FROM certificate_templates WHERE id = ?");
            $stmt->execute([$id]);
            $existing = $stmt->fetch();
        } catch (PDOException $e) {}
    }

    if (isset($_FILES['bg_image']) && $_FILES['bg_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['bg_image']['error'] !== UPLOAD_ERR_OK) { echo json_encode(['status'=>'error','message'=>'Upload gagal.']); exit; }
        $res = uploadImageToCloudinary($_FILES['bg_image'], 'mcm/certs');
        if (!$res['ok']) { echo json_encode(['status'=>'error','message'=>$res['error']]); exit; }
        $bgImage = $res['url']; $bgImagePublicId = $res['public_id'] ?? cloudinaryPublicIdFromUrl($res['url']);
    } elseif ($existing && !empty($existing['bg_image'])) {
        $bgImage = $existing['bg_image'];
        $bgImagePublicId = $existing['bg_image_public_id'] ?? null;
    }

    if ($isDefault) {
        try {
            $pdo->exec("UPDATE certificate_templates SET is_default = 0");
        } catch (PDOException $e) {}
    }

    try {
        if ($action === 'create') {
            $stmt = $pdo->prepare("INSERT INTO certificate_templates (name, class_id, layout, bg_image, bg_image_public_id, accent_color, is_default) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $classId, $layout, $bgImage, $bgImagePublicId, $accentColor, $isDefault]);
        } else {
            $stmt = $pdo->prepare("UPDATE certificate_templates SET name = ?, class_id = ?, layout = ?, bg_image = ?, bg_image_public_id = ?, accent_color = ?, is_default = ? WHERE id = ?");
            $stmt->execute([$name, $classId, $layout, $bgImage, $bgImagePublicId, $accentColor, $isDefault, $id]);
        }
        echo json_encode(['status' => 'success', 'message' => $action === 'create' ? 'Template sertifikat berhasil ditambahkan' : 'Template sertifikat berhasil diperbarui']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan template sertifikat']);
    }
    exit;
}

if ($action === 'delete') {
    $id = $_POST['id'] ?? '';
    if (empty($id)) {
        echo json_encode(['status' => 'error', 'message' => 'ID tidak valid']);
        exit;
    }
    try {
        try { $stmt = $pdo->prepare("SELECT bg_image, bg_image_public_id FROM certificate_templates WHERE id = ?"); $stmt->execute([$id]); $row = $stmt->fetch(); } catch (PDOException $e) { $stmt = $pdo->prepare("SELECT bg_image FROM certificate_templates WHERE id = ?"); $stmt->execute([$id]); $row = $stmt->fetch(); if ($row) $row['bg_image_public_id'] = ''; }
        $stmt = $pdo->prepare("DELETE FROM certificate_templates WHERE id = ?");
        $stmt->execute([$id]);
        if ($row && (!empty($row['bg_image_public_id']) || str_contains($row['bg_image'] ?? '', 'res.cloudinary.com'))) { $pid = $row['bg_image_public_id'] ?: $row['bg_image']; $delRes = deleteImageFromCloudinary($pid); if (!$delRes['ok']) error_log("[Cloudinary delete certificate_templates $id] ".$delRes['error']); }
        if ($row && strpos($row['bg_image'] ?? '', 'http') !== 0 && !empty($row['bg_image']) && file_exists('../../' . $row['bg_image'])) { @unlink('../../' . $row['bg_image']); }
        echo json_encode(['status' => 'success', 'message' => 'Template sertifikat berhasil dihapus']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus template sertifikat']);
    }
    exit;
}
if ($action === 'bulk_delete') {
    $raw=$_POST['ids']??''; $ids=[]; if(is_array($raw))$ids=$raw; elseif(is_string($raw)&&$raw!==''){ $d=json_decode($raw,true); $ids=is_array($d)?$d:array_filter(array_map('trim',explode(',',$raw))); }
    $ids=array_values(array_unique(array_filter(array_map('intval',$ids))));
    if(empty($ids)){ echo json_encode(['status'=>'error','message'=>'Tidak ada data terpilih']); exit; }
    if(count($ids)>100){ echo json_encode(['status'=>'error','message'=>'Maksimal 100']); exit; }
    try{
        $ph=implode(',',array_fill(0,count($ids),'?'));
        // cleanup images per row
        $stmt=$pdo->prepare("SELECT bg_image, bg_image_public_id FROM certificate_templates WHERE id IN ($ph)");
        $stmt->execute($ids); $rows=$stmt->fetchAll();
        $del=$pdo->prepare("DELETE FROM certificate_templates WHERE id IN ($ph)"); $del->execute($ids);
        foreach($rows as $row){
            if(!empty($row['bg_image_public_id'])||str_contains($row['bg_image']??'','res.cloudinary.com')){ $pid=$row['bg_image_public_id']?:$row['bg_image']; $r=deleteImageFromCloudinary($pid); if(!$r['ok']) error_log("[Cloudinary bulk cert_tpl] ".$r['error']); }
            if(!empty($row['bg_image']) && strpos($row['bg_image'],'http')!==0 && file_exists('../../'.$row['bg_image'])) @unlink('../../'.$row['bg_image']);
        }
        echo json_encode(['status'=>'success','message'=>$del->rowCount().' template dihapus']);
    }catch(PDOException $e){ echo json_encode(['status'=>'error','message'=>'Gagal hapus massal']); }
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Aksi tidak dikenali']);