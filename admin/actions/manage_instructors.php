<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['admin_logged_in'])) exit;

require_once '../../config/database.php';
require_once '../../includes/security.php';
mcm_cors_headers();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!mcm_csrf_verify($_POST['csrf_token'] ?? $_POST['_token'] ?? '')) {
        echo json_encode(['status'=>'error','message'=>'CSRF token tidak valid. Muat ulang halaman.']); exit;
    }
    $rateKey = 'admin_' . basename(__FILE__, '.php') . '_' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
    $rl = mcm_rate_limit($rateKey, 30, 60);
    if (!$rl['allowed']) { echo json_encode(['status'=>'error','message'=>$rl['message']]); exit; }
}
require_once '../../includes/cloudinary.php';
try { $cols = $pdo->query("SHOW COLUMNS FROM instructors")->fetchAll(PDO::FETCH_COLUMN); if (!in_array('image_public_id', $cols)) $pdo->exec("ALTER TABLE instructors ADD COLUMN image_public_id VARCHAR(255) DEFAULT NULL AFTER image"); } catch (Throwable $e) {}

$action = $_POST['action'] ?? '';

if ($action === 'create') {
    $name = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $spec = trim($_POST['specialization'] ?? '');
    
    if (empty($name) || empty($category) || empty($spec)) {
        echo json_encode(['status' => 'error', 'message' => 'Nama, Kategori, dan Spesialisasi wajib diisi.']);
        exit;
    }

    if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            $tmpErr = $_FILES['image']['error'];
            $msg = $tmpErr===UPLOAD_ERR_INI_SIZE||$tmpErr===UPLOAD_ERR_FORM_SIZE?'File terlalu besar.':'Upload gagal.';
            echo json_encode(['status'=>'error','message'=>$msg]); exit;
        }
        $res = uploadImageToCloudinary($_FILES['image'], 'mcm/instructors');
        if (!$res['ok']) { echo json_encode(['status'=>'error','message'=>$res['error']]); exit; }
        $imagePublicId = $res['public_id'] ?? cloudinaryPublicIdFromUrl($res['url']);
        $stmt = $pdo->prepare("INSERT INTO instructors (name, category, specialization, image, image_public_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $category, $spec, $res['url'], $imagePublicId]);
        echo json_encode(['status'=>'success','message'=>'Instruktur berhasil ditambahkan.']); exit;
    }
    
    // Default fallback if no image selected
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

    $image = null; $imagePublicId = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['status'=>'error','message'=>'Upload gagal.']); exit;
        }
        $res = uploadImageToCloudinary($_FILES['image'], 'mcm/instructors');
        if (!$res['ok']) { echo json_encode(['status'=>'error','message'=>$res['error']]); exit; }
        $image = $res['url']; $imagePublicId = $res['public_id'] ?? cloudinaryPublicIdFromUrl($res['url']);
    }

    if ($image) {
        $stmt = $pdo->prepare("UPDATE instructors SET name = ?, category = ?, specialization = ?, image = ?, image_public_id = ? WHERE id = ?");
        $stmt->execute([$name, $category, $spec, $image, $imagePublicId, $id]);
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
        try { $stmt = $pdo->prepare("SELECT image, image_public_id FROM instructors WHERE id = ?"); $stmt->execute([$id]); $row = $stmt->fetch(); } catch (PDOException $e) { $stmt = $pdo->prepare("SELECT image FROM instructors WHERE id = ?"); $stmt->execute([$id]); $row = $stmt->fetch(); if ($row) $row['image_public_id'] = ''; }
        $stmt = $pdo->prepare("DELETE FROM instructors WHERE id = ?");
        $stmt->execute([$id]);
        if ($row && (!empty($row['image_public_id']) || str_contains($row['image'] ?? '', 'res.cloudinary.com'))) { $pid = $row['image_public_id'] ?: $row['image']; $delRes = deleteImageFromCloudinary($pid); if (!$delRes['ok']) error_log("[Cloudinary delete instructors $id] ".$delRes['error']); }
        if ($row && strpos($row['image'] ?? '', 'http') !== 0 && !empty($row['image']) && file_exists('../../' . $row['image'])) { @unlink('../../' . $row['image']); }
        echo json_encode(['status' => 'success', 'message' => 'Data instruktur dihapus.']);
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) { // Integrity constraint violation
            echo json_encode([
                'status' => 'error',
                'message' => 'Instruktur ini tidak bisa dihapus karena masih dipakai oleh data lain (mis. pesanan kelas). Ganti instruktur pada kelas/pesanan terkait terlebih dahulu.'
            ]);
        
} elseif ($action === 'bulk_delete') {
    $raw = $_POST['ids'] ?? '';
    $ids = [];
    if (is_array($raw)) $ids = $raw;
    elseif (is_string($raw) && $raw !== '') { $d=json_decode($raw,true); $ids=is_array($d)?$d:array_filter(array_map('trim',explode(',',$raw))); }
    $ids = array_values(array_unique(array_filter(array_map('intval',$ids))));
    if (empty($ids)) { echo json_encode(['status'=>'error','message'=>'Tidak ada data terpilih']); exit; }
    if (count($ids)>100) { echo json_encode(['status'=>'error','message'=>'Maksimal 100']); exit; }
    $deleted=0; $fail=[]; foreach($ids as $id) {
        try {
            // ambil image untuk cleanup
            try { $st=$pdo->prepare("SELECT image, image_public_id FROM instructors WHERE id=?"); $st->execute([$id]); $cls=$st->fetch(); } catch (PDOException $e) { $st=$pdo->prepare("SELECT image FROM instructors WHERE id=?"); $st->execute([$id]); $cls=$st->fetch(); if($cls) $cls['image_public_id']=''; }
            $del=$pdo->prepare("DELETE FROM instructors WHERE id=?"); $del->execute([$id]);
            if($del->rowCount()>0){
                $deleted++;
                if($cls && (!empty($cls['image_public_id']) || str_contains($cls['image']??'','res.cloudinary.com'))){ $pid=$cls['image_public_id']?:$cls['image']; $res=deleteImageFromCloudinary($pid); if(!$res['ok']) error_log("[Cloudinary bulk instructors $id] ".$res['error']); }
                if($cls && !empty($cls['image']) && strpos($cls['image'],'http')!==0 && file_exists('../../'.$cls['image'])) @unlink('../../'.$cls['image']);
            }
        } catch (PDOException $e) { if($e->getCode()==23000) $fail[]=$id; }
    }
    if($fail) echo json_encode(['status'=>'error','message'=> $deleted.' terhapus, '.count($fail).' gagal (masih punya relasi)']);
    else echo json_encode(['status'=>'success','message'=> $deleted.' data berhasil dihapus']);
    exit;
} else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus instruktur: ' . $e->getMessage()]);
        }
    }
}
