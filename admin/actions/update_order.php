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
    if (!in_array($status, ['pending', 'confirmed', 'cancelled'], true)) {
        echo json_encode(['status' => 'error', 'message' => 'Status tidak valid.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? LIMIT 1");
        $stmt->execute([$orderId]);
        $order = $stmt->fetch();
        if (!$order) {
            echo json_encode(['status' => 'error', 'message' => 'Pesanan tidak ditemukan.']);
            exit;
        }
        $pdo->beginTransaction();
        $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?")->execute([$status, $orderId]);

        // Konfirmasi admin = lunas: samakan efeknya dengan webhook paid
        // (payment_status, enrollment LMS, rekap finance) agar kelas langsung masuk LMS.
        if ($status === 'confirmed') {
            $pdo->prepare("UPDATE orders SET payment_status = 'paid', paid_at = COALESCE(paid_at, NOW()) WHERE id = ?")->execute([$orderId]);
            if (!empty($order['user_id'])) {
                $orderMode = strtolower($order['class_mode'] ?? 'offline');
                if (!in_array($orderMode, ['online', 'offline'], true)) $orderMode = 'offline';
                try {
                    $pdo->prepare("INSERT INTO enrollments (user_id, class_id, class_mode, order_id) VALUES (?, ?, ?, ?)
                                   ON DUPLICATE KEY UPDATE order_id = VALUES(order_id)")
                        ->execute([$order['user_id'], $order['class_id'], $orderMode, $orderId]);
                } catch (PDOException $eOld) {
                    if ($eOld->getCode() === '42S22') {
                        $pdo->prepare("INSERT IGNORE INTO enrollments (user_id, class_id, order_id) VALUES (?, ?, ?)")
                            ->execute([$order['user_id'], $order['class_id'], $orderId]);
                    } else { throw $eOld; }
                }
            }
            try {
                $checkFin = $pdo->prepare("SELECT COUNT(*) FROM finance_transactions WHERE order_id = ?");
                $checkFin->execute([$orderId]);
                if ($checkFin->fetchColumn() == 0) {
                    $className = (string)$pdo->query("SELECT name FROM classes WHERE id = " . (int)$order['class_id'])->fetchColumn();
                    if ($className === '') $className = 'Kelas MCM';
                    $pdo->prepare("INSERT INTO finance_transactions (type, category, item_name, quantity, unit_price, amount, description, order_id, transaction_date)
                                   VALUES ('in', 'pemasukan_kursus', ?, 1, ?, ?, ?, ?, CURDATE())")
                        ->execute(["Pendaftaran " . $className . " (" . $order['customer_name'] . ")", $order['amount'], $order['amount'], "Pemasukan pembayaran kursus no. order " . $order['order_number'], $orderId]);
                }
            } catch (Exception $fe) {}
        }

        $pdo->commit();
        echo json_encode(['status' => 'success', 'message' => 'Status pesanan berhasil diperbarui.']);
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        error_log('[update_order] ' . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui status pesanan.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
}
