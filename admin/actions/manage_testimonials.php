<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['admin_logged_in'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}
require_once '../../config/database.php';

// Auto-create table if missing in user DB
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS `testimonials` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `user_id` int(11) DEFAULT NULL,
      `name` varchar(100) NOT NULL,
      `rating` tinyint(1) NOT NULL DEFAULT 5,
      `review` text NOT NULL,
      `image` varchar(255) DEFAULT NULL,
      `class_id` int(11) DEFAULT NULL,
      `graduation_year` varchar(10) DEFAULT NULL,
      `job` varchar(150) DEFAULT NULL,
      `status` ENUM('pending','approved','rejected') DEFAULT 'pending',
      `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
} catch (PDOException $e) {}

$action = $_POST['action'] ?? '';

if ($action === 'update') {
    $id = $_POST['id'] ?? '';
    $name = trim($_POST['name'] ?? '');
    $rating = (int)($_POST['rating'] ?? 5);
    $review = trim($_POST['review'] ?? '');
    $status = $_POST['status'] ?? 'pending';
    $graduationYear = trim($_POST['graduation_year'] ?? '');
    $job = trim($_POST['job'] ?? '');

    if ($rating < 1 || $rating > 5) $rating = 5;
    if (!in_array($status, ['pending', 'approved', 'rejected'])) $status = 'pending';

    if (empty($id) || empty($name) || empty($review)) {
        echo json_encode(['status' => 'error', 'message' => 'Nama dan ulasan wajib diisi']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("UPDATE testimonials SET name = ?, rating = ?, review = ?, status = ?, graduation_year = ?, job = ? WHERE id = ?");
        $stmt->execute([$name, $rating, $review, $status, $graduationYear ?: null, $job ?: null, $id]);
        echo json_encode(['status' => 'success', 'message' => 'Testimoni berhasil diperbarui']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui testimoni']);
    }
    exit;
}

if ($action === 'approve' || $action === 'reject') {
    $id = $_POST['id'] ?? '';
    $newStatus = ($action === 'approve') ? 'approved' : 'rejected';
    try {
        $stmt = $pdo->prepare("UPDATE testimonials SET status = ? WHERE id = ?");
        $stmt->execute([$newStatus, $id]);
        $msg = ($action === 'approve') ? 'Testimoni disetujui dan kini tampil di website.' : 'Testimoni ditolak.';
        echo json_encode(['status' => 'success', 'message' => $msg]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal mengubah status testimoni']);
    }
    exit;
}

if ($action === 'delete') {
    $id = $_POST['id'] ?? '';
    try {
        $stmt = $pdo->prepare("DELETE FROM testimonials WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['status' => 'success', 'message' => 'Testimoni berhasil dihapus']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus testimoni']);
    }
    exit;
}
if ($action === 'bulk_delete') {
    $raw = $_POST['ids'] ?? '';
    $ids = [];
    if (is_array($raw)) $ids=$raw;
    elseif (is_string($raw) && $raw!==''){ $d=json_decode($raw,true); $ids=is_array($d)?$d:array_filter(array_map('trim',explode(',',$raw))); }
    $ids=array_values(array_unique(array_filter(array_map('intval',$ids))));
    if(empty($ids)){ echo json_encode(['status'=>'error','message'=>'Tidak ada data terpilih']); exit; }
    if(count($ids)>100){ echo json_encode(['status'=>'error','message'=>'Maksimal 100']); exit; }
    try{ $ph=implode(',',array_fill(0,count($ids),'?')); $stmt=$pdo->prepare("DELETE FROM testimonials WHERE id IN ($ph)"); $stmt->execute($ids); echo json_encode(['status'=>'success','message'=>$stmt->rowCount().' testimoni berhasil dihapus']); }catch(PDOException $e){ echo json_encode(['status'=>'error','message'=>'Gagal hapus massal']); }
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Aksi tidak dikenali']);
?>