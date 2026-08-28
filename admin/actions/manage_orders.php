<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['admin_logged_in'])) exit;

require_once '../../config/database.php';

$action = $_POST['action'] ?? '';

if ($action === 'delete') {
    $id = $_POST['id'] ?? null;
    if ($id) {
        try {
            $stmt = $pdo->prepare("DELETE FROM orders WHERE id = ?");
            if ($stmt->execute([$id])) {
                echo json_encode(['status' => 'success', 'message' => 'Data pesanan berhasil dihapus.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus data pesanan.']);
            }
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Kesalahan database.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'ID tidak valid.']);
    }
} elseif ($action === 'bulk_delete') {
    $raw = $_POST['ids'] ?? '';
    $ids = [];
    if (is_array($raw)) $ids = $raw;
    elseif (is_string($raw) && $raw !== '') { $decoded = json_decode($raw, true); $ids = is_array($decoded) ? $decoded : array_filter(array_map('trim', explode(',', $raw))); }
    $ids = array_values(array_unique(array_filter(array_map('intval', $ids))));
    if (empty($ids)) { echo json_encode(['status'=>'error','message'=>'Tidak ada data terpilih.']); exit; }
    if (count($ids) > 100) { echo json_encode(['status'=>'error','message'=>'Maksimal 100 data sekaligus.']); exit; }
    try {
        $ph = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $pdo->prepare("DELETE FROM orders WHERE id IN ($ph)");
        $stmt->execute($ids);
        $deleted = $stmt->rowCount();
        echo json_encode(['status'=>'success','message'=> $deleted.' pesanan berhasil dihapus.']);
    } catch (PDOException $e) { echo json_encode(['status'=>'error','message'=>'Gagal hapus massal: '.$e->getMessage()]); }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Aksi tidak diizinkan.']);
}
