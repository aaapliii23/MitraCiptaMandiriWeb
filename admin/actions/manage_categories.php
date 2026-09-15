<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['admin_logged_in'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
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

// Auto-create table if missing in user DB
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS `class_categories` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `name` varchar(100) NOT NULL,
      `slug` varchar(100) NOT NULL,
      `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      UNIQUE KEY `slug` (`slug`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
} catch (PDOException $e) {}

$action = $_POST['action'] ?? '';

if ($action === 'create') {
    $name = trim($_POST['name'] ?? '');
    $slug = strtolower(str_replace(' ', '_', $name));
    
    if (empty($name)) {
        echo json_encode(['status' => 'error', 'message' => 'Nama kategori wajib diisi']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO class_categories (name, slug) VALUES (?, ?)");
        $stmt->execute([$name, $slug]);
        echo json_encode(['status' => 'success', 'message' => 'Kategori berhasil ditambahkan']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan kategori: ' . $e->getMessage()]);
    }
    exit;
}

if ($action === 'update') {
    $id = $_POST['id'] ?? '';
    $name = $_POST['name'] ?? '';
    $slug = strtolower(str_replace(' ', '_', $name));

    if (empty($id) || empty($name)) {
        echo json_encode(['status' => 'error', 'message' => 'Nama kategori wajib diisi']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("UPDATE class_categories SET name = ?, slug = ? WHERE id = ?");
        $stmt->execute([$name, $slug, $id]);
        echo json_encode(['status' => 'success', 'message' => 'Kategori berhasil diperbarui']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui kategori: ' . $e->getMessage()]);
    }
}

if ($action === 'delete') {
    $id = $_POST['id'] ?? '';
    try {
        $stmt = $pdo->prepare("DELETE FROM class_categories WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['status' => 'success', 'message' => 'Kategori berhasil dihapus']);
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) { // Integrity constraint violation
            echo json_encode([
                'status' => 'error',
                'message' => 'Kategori tidak bisa dihapus karena masih memiliki data terkait. Pindahkan kelas ke kategori lain terlebih dahulu.'
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus kategori: ' . $e->getMessage()]);
        }
    }
    exit;
}
if ($action === 'bulk_delete') {
    $raw=$_POST['ids']??''; $ids=[]; if(is_array($raw))$ids=$raw; elseif(is_string($raw)&&$raw!==''){ $d=json_decode($raw,true); $ids=is_array($d)?$d:array_filter(array_map('trim',explode(',',$raw))); }
    $ids=array_values(array_unique(array_filter(array_map('intval',$ids))));
    if(empty($ids)){ echo json_encode(['status'=>'error','message'=>'Tidak ada data terpilih']); exit; }
    if(count($ids)>100){ echo json_encode(['status'=>'error','message'=>'Maksimal 100']); exit; }
    $deleted=0; $fail=[]; foreach($ids as $id){ try{ $stmt=$pdo->prepare("DELETE FROM class_categories WHERE id=?"); $stmt->execute([$id]); $deleted+=$stmt->rowCount(); }catch(PDOException $e){ $fail[]=$id; } }
    if($fail) echo json_encode(['status'=>'error','message'=> $deleted.' terhapus, '.count($fail).' gagal (masih dipakai kelas)']);
    else echo json_encode(['status'=>'success','message'=> $deleted.' kategori berhasil dihapus']);
    exit;
}
?>
