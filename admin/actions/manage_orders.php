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
} else {
    echo json_encode(['status' => 'error', 'message' => 'Aksi tidak diizinkan.']);
}
