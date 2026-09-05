<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

require_once '../../config/database.php';
require_once '../../includes/security.php';
mcm_cors_headers();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!mcm_csrf_verify($_POST['csrf_token'] ?? $_POST['_token'] ?? '')) {
        echo json_encode(['status' => 'error', 'message' => 'CSRF token tidak valid. Muat ulang halaman.']);
        exit;
    }
    $orderId = $_POST['order_id'] ?? '';
    $status = $_POST['status'] ?? '';
    
    if (empty($orderId) || empty($status)) {
        echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap.']);
        exit;
    }
    
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    if ($stmt->execute([$status, $orderId])) {
        echo json_encode(['status' => 'success', 'message' => 'Status pesanan berhasil diperbarui.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui status pesanan.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
}
