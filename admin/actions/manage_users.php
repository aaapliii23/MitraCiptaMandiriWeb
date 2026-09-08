<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['admin_logged_in'])) { echo json_encode(['status'=>'error','message'=>'Unauthorized']); exit; }

require_once '../../config/database.php';
require_once '../../includes/security.php';
mcm_cors_headers();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!mcm_csrf_verify($_POST['csrf_token'] ?? $_POST['_token'] ?? '')) {
        echo json_encode(['status'=>'error','message'=>'CSRF token tidak valid. Muat ulang halaman.']); exit;
    }
    $rateKey = 'admin_manage_users_' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
    $rl = mcm_rate_limit($rateKey, 30, 60);
    if (!$rl['allowed']) { echo json_encode(['status'=>'error','message'=>$rl['message']]); exit; }
}

$action = $_POST['action'] ?? '';

if ($action === 'delete') {
    $id = (int)($_POST['id'] ?? 0);
    if (!$id) { echo json_encode(['status'=>'error','message'=>'ID tidak valid.']); exit; }

    try {
        // Hapus data relasi dulu agar tidak ada constraint violation
        $pdo->prepare("DELETE FROM enrollments WHERE user_id = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM orders WHERE user_id = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM certificates WHERE user_id = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM material_progress WHERE user_id = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM testimonials WHERE user_id = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);
        echo json_encode(['status'=>'success','message'=>'Peserta berhasil dihapus.']);
    } catch (PDOException $e) {
        echo json_encode(['status'=>'error','message'=>'Gagal menghapus peserta: ' . $e->getMessage()]);
    }

} elseif ($action === 'bulk_delete') {
    $raw = $_POST['ids'] ?? '';
    $ids = [];
    if (is_array($raw)) {
        $ids = $raw;
    } elseif (is_string($raw) && $raw !== '') {
        $d = json_decode($raw, true);
        $ids = is_array($d) ? $d : array_filter(array_map('trim', explode(',', $raw)));
    }
    $ids = array_values(array_unique(array_filter(array_map('intval', $ids))));
    if (empty($ids)) { echo json_encode(['status'=>'error','message'=>'Tidak ada data terpilih.']); exit; }
    if (count($ids) > 100) { echo json_encode(['status'=>'error','message'=>'Maksimal 100 data sekaligus.']); exit; }

    $deleted = 0;
    foreach ($ids as $id) {
        try {
            $pdo->prepare("DELETE FROM enrollments WHERE user_id = ?")->execute([$id]);
            $pdo->prepare("DELETE FROM orders WHERE user_id = ?")->execute([$id]);
            $pdo->prepare("DELETE FROM certificates WHERE user_id = ?")->execute([$id]);
            $pdo->prepare("DELETE FROM material_progress WHERE user_id = ?")->execute([$id]);
            $pdo->prepare("DELETE FROM testimonials WHERE user_id = ?")->execute([$id]);
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$id]);
            if ($stmt->rowCount() > 0) $deleted++;
        } catch (PDOException $e) {
            // log dan lanjut
            error_log("[manage_users bulk_delete $id] " . $e->getMessage());
        }
    }
    echo json_encode(['status'=>'success','message'=>$deleted . ' peserta berhasil dihapus.']);
    exit;

} else {
    echo json_encode(['status'=>'error','message'=>'Aksi tidak dikenal.']);
}
