<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']);
    exit;
}

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

    // Image Upload Handling -> Cloudinary
    $imagePath = '';
    $imagePublicId = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $res = uploadImageToCloudinary($_FILES['image'], 'mcm/classes');
        if (!$res['ok']) { echo json_encode(['status'=>'error','message'=>$res['error']]); exit; }
        $imagePath = $res['url'];
        $imagePublicId = $res['public_id'] ?? cloudinaryPublicIdFromUrl($res['url']);
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
    $hasImgPubId = true;
    try { $pdo->query("SELECT image_public_id FROM classes LIMIT 1"); } catch (PDOException $e) { $hasImgPubId = false; try { $pdo->exec("ALTER TABLE classes ADD COLUMN image_public_id VARCHAR(255) DEFAULT NULL AFTER image"); $hasImgPubId = true; } catch (PDOException $e2) {} }

    if (empty($description_online)) $description_online = $description;
    if (empty($description_offline)) $description_offline = $description;

    if ($action === 'create') {
        if (empty($imagePath)) {
            echo json_encode(['status' => 'error', 'message' => 'Gambar wajib diupload untuk kelas baru.']);
            exit;
        }
        if ($hasNewCols && $hasDescCols) {
            if ($hasImgPubId) {
                $stmt = $pdo->prepare("INSERT INTO classes (name, start_date, category, description, description_online, description_offline, image, image_public_id, features, price, price_online, price_offline, mode_available) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $ok = $stmt->execute([$name, $start_date, $category, $description, $description_online, $description_offline, $imagePath, $imagePublicId, $features_json, $price, $price_online, $price_offline, $mode_available]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO classes (name, start_date, category, description, description_online, description_offline, image, features, price, price_online, price_offline, mode_available) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $ok = $stmt->execute([$name, $start_date, $category, $description, $description_online, $description_offline, $imagePath, $features_json, $price, $price_online, $price_offline, $mode_available]);
            }
        } elseif ($hasNewCols) {
            if ($hasImgPubId) {
                $stmt = $pdo->prepare("INSERT INTO classes (name, start_date, category, description, image, image_public_id, features, price, price_online, price_offline, mode_available) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $ok = $stmt->execute([$name, $start_date, $category, $description, $imagePath, $imagePublicId, $features_json, $price, $price_online, $price_offline, $mode_available]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO classes (name, start_date, category, description, image, features, price, price_online, price_offline, mode_available) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $ok = $stmt->execute([$name, $start_date, $category, $description, $imagePath, $features_json, $price, $price_online, $price_offline, $mode_available]);
            }
        } else {
            if ($hasImgPubId) {
                $stmt = $pdo->prepare("INSERT INTO classes (name, start_date, category, description, image, image_public_id, features, price) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $ok = $stmt->execute([$name, $start_date, $category, $description, $imagePath, $imagePublicId, $features_json, $price]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO classes (name, start_date, category, description, image, features, price) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $ok = $stmt->execute([$name, $start_date, $category, $description, $imagePath, $features_json, $price]);
            }
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
                if ($hasImgPubId) {
                    $stmt = $pdo->prepare("UPDATE classes SET name=?, start_date=?, category=?, description=?, description_online=?, description_offline=?, image=?, image_public_id=?, features=?, price=?, price_online=?, price_offline=?, mode_available=? WHERE id=?");
                    $res = $stmt->execute([$name, $start_date, $category, $description, $description_online, $description_offline, $imagePath, $imagePublicId, $features_json, $price, $price_online, $price_offline, $mode_available, $id]);
                } else {
                    $stmt = $pdo->prepare("UPDATE classes SET name=?, start_date=?, category=?, description=?, description_online=?, description_offline=?, image=?, features=?, price=?, price_online=?, price_offline=?, mode_available=? WHERE id=?");
                    $res = $stmt->execute([$name, $start_date, $category, $description, $description_online, $description_offline, $imagePath, $features_json, $price, $price_online, $price_offline, $mode_available, $id]);
                }
            } elseif ($hasNewCols) {
                if ($hasImgPubId) {
                    $stmt = $pdo->prepare("UPDATE classes SET name=?, start_date=?, category=?, description=?, image=?, image_public_id=?, features=?, price=?, price_online=?, price_offline=?, mode_available=? WHERE id=?");
                    $res = $stmt->execute([$name, $start_date, $category, $description, $imagePath, $imagePublicId, $features_json, $price, $price_online, $price_offline, $mode_available, $id]);
                } else {
                    $stmt = $pdo->prepare("UPDATE classes SET name=?, start_date=?, category=?, description=?, image=?, features=?, price=?, price_online=?, price_offline=?, mode_available=? WHERE id=?");
                    $res = $stmt->execute([$name, $start_date, $category, $description, $imagePath, $features_json, $price, $price_online, $price_offline, $mode_available, $id]);
                }
            } else {
                if ($hasImgPubId) {
                    $stmt = $pdo->prepare("UPDATE classes SET name=?, start_date=?, category=?, description=?, image=?, image_public_id=?, features=?, price=? WHERE id=?");
                    $res = $stmt->execute([$name, $start_date, $category, $description, $imagePath, $imagePublicId, $features_json, $price, $id]);
                } else {
                    $stmt = $pdo->prepare("UPDATE classes SET name=?, start_date=?, category=?, description=?, image=?, features=?, price=? WHERE id=?");
                    $res = $stmt->execute([$name, $start_date, $category, $description, $imagePath, $features_json, $price, $id]);
                }
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
    try {
        $stmt = $pdo->prepare("SELECT image, image_public_id FROM classes WHERE id=?");
        $stmt->execute([$id]);
        $class = $stmt->fetch();
        if ($class && !array_key_exists('image_public_id', $class)) $class['image_public_id'] = '';
    } catch (PDOException $e) {
        $stmt = $pdo->prepare("SELECT image FROM classes WHERE id=?");
        $stmt->execute([$id]);
        $class = $stmt->fetch();
        if ($class) $class['image_public_id'] = '';
    }

    try {
        $del = $pdo->prepare("DELETE FROM classes WHERE id=?");
        $del->execute([$id]);
        if ($class && (!empty($class['image_public_id']) || str_contains($class['image'] ?? '', 'res.cloudinary.com'))) { $pid = $class['image_public_id'] ?: $class['image']; $delRes = deleteImageFromCloudinary($pid); if (!$delRes['ok']) error_log("[Cloudinary delete classes $id] ".$delRes['error']); }
        if ($class && strpos($class['image'], 'http') !== 0 && file_exists('../../' . $class['image'])) {
            unlink('../../' . $class['image']);
        }
        echo json_encode(['status' => 'success', 'message' => 'Kelas berhasil dihapus.']);
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) { // Integrity constraint violation
            echo json_encode([
                'status' => 'error',
                'message' => 'Kelas ini tidak bisa dihapus karena masih memiliki data terkait (pesanan/testimoni/enrollment). Nonaktifkan atau arsipkan kelas ini alih-alih menghapusnya, atau hapus dulu data pesanan/testimoni terkait.'
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus kelas: ' . $e->getMessage()]);
        }
    }

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
            try { $st=$pdo->prepare("SELECT image, image_public_id FROM classes WHERE id=?"); $st->execute([$id]); $cls=$st->fetch(); } catch (PDOException $e) { $st=$pdo->prepare("SELECT image FROM classes WHERE id=?"); $st->execute([$id]); $cls=$st->fetch(); if($cls) $cls['image_public_id']=''; }
            $del=$pdo->prepare("DELETE FROM classes WHERE id=?"); $del->execute([$id]);
            if($del->rowCount()>0){
                $deleted++;
                if($cls && (!empty($cls['image_public_id']) || str_contains($cls['image']??'','res.cloudinary.com'))){ $pid=$cls['image_public_id']?:$cls['image']; $res=deleteImageFromCloudinary($pid); if(!$res['ok']) error_log("[Cloudinary bulk classes $id] ".$res['error']); }
                if($cls && !empty($cls['image']) && strpos($cls['image'],'http')!==0 && file_exists('../../'.$cls['image'])) @unlink('../../'.$cls['image']);
            }
        } catch (PDOException $e) { if($e->getCode()==23000) $fail[]=$id; }
    }
    if($fail) echo json_encode(['status'=>'error','message'=> $deleted.' terhapus, '.count($fail).' gagal (masih punya relasi)']);
    else echo json_encode(['status'=>'success','message'=> $deleted.' data berhasil dihapus']);
    exit;
} else {
    echo json_encode(['status' => 'error', 'message' => 'Aksi tidak valid.']);
}
