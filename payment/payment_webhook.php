<?php
require_once '../config/database.php';
require_once '../includes/payment_gateway.php';
require_once '../includes/whatsapp_client.php';

header('Content-Type: application/json');

$payload = json_decode(file_get_contents('php://input'), true);
if (!is_array($payload)) {
    $payload = $_POST;
}

if (!pg_verify_webhook($payload)) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Invalid signature']);
    exit;
}

$orderNumber = (string)($payload['order_number'] ?? '');
$newStatus = (string)($payload['payment_status'] ?? '');
$method = isset($payload['payment_method']) ? (string)$payload['payment_method'] : null;
$gatewayRef = isset($payload['gateway_ref']) ? (string)$payload['gateway_ref'] : null;

$allowed = ['paid', 'failed', 'expired'];
if ($orderNumber === '' || !in_array($newStatus, $allowed)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Payload tidak valid']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE order_number = ?");
    $stmt->execute([$orderNumber]);
    $order = $stmt->fetch();
    if (!$order) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Order tidak ditemukan']);
        exit;
    }

    if ($order['payment_status'] === 'paid') {
        echo json_encode(['status' => 'success', 'message' => 'Already processed', 'order_number' => $orderNumber, 'payment_status' => $order['payment_status']]);
        exit;
    }

    $pdo->beginTransaction();

    if ($newStatus === 'paid') {
        $stmt = $pdo->prepare("UPDATE orders SET payment_status = 'paid', payment_method = ?, payment_gateway_ref = COALESCE(payment_gateway_ref, ?), paid_at = NOW(), status = 'confirmed' WHERE id = ?");
        $stmt->execute([$method, $gatewayRef, $order['id']]);

        if ($order['user_id']) {
            // ponytail: UNIQUE(user_id,class_id) membuat INSERT IGNORE diam-diam skip saat user
            // membeli ulang kelas sama dengan mode berbeda; upsert order_id agar mode terakhir menang.
            $stmt = $pdo->prepare("INSERT INTO enrollments (user_id, class_id, order_id) VALUES (?, ?, ?)
                                   ON DUPLICATE KEY UPDATE order_id = VALUES(order_id)");
            $stmt->execute([$order['user_id'], $order['class_id'], $order['id']]);
        }

        // Ambil nama kelas & link WA (jika ada)
        $className = '';
        $waLink = null;
        try {
            $hasWaCol = true;
            try { $pdo->query("SELECT whatsapp_group_link FROM classes LIMIT 1"); } catch (Exception $e) { $hasWaCol = false; }
            if ($hasWaCol) {
                $stmt = $pdo->prepare("SELECT name, whatsapp_group_link FROM classes WHERE id = ?");
                $stmt->execute([$order['class_id']]);
                $cls = $stmt->fetch();
                $className = (string)($cls['name'] ?? '');
                $waLink = $cls['whatsapp_group_link'] ?? null;
            } else {
                $stmt = $pdo->prepare("SELECT name FROM classes WHERE id = ?");
                $stmt->execute([$order['class_id']]);
                $className = (string)$stmt->fetchColumn();
            }
        } catch (Exception $e) {
            $stmt = $pdo->prepare("SELECT name FROM classes WHERE id = ?");
            $stmt->execute([$order['class_id']]);
            $className = (string)$stmt->fetchColumn();
        }
        if ($className === '') $className = 'Kelas MCM';

        // Auto-record into finance_transactions
        try {
            $checkFin = $pdo->prepare("SELECT COUNT(*) FROM finance_transactions WHERE order_id = ?");
            $checkFin->execute([$order['id']]);
            if ($checkFin->fetchColumn() == 0) {
                $insFin = $pdo->prepare("INSERT INTO finance_transactions (type, category, item_name, quantity, unit_price, amount, description, order_id, transaction_date) 
                                          VALUES ('in', 'pemasukan_kursus', ?, 1, ?, ?, ?, ?, CURDATE())");
                $itemName = "Pendaftaran " . $className . " (" . $order['customer_name'] . ")";
                $desc = "Pemasukan pembayaran kursus no. order " . $orderNumber;
                $insFin->execute([$itemName, $order['amount'], $order['amount'], $desc, $order['id']]);
            }
        } catch (Exception $fe) {}

        // Kirim pesan sesuai mode: offline -> link WA grup, online -> LMS
        // ponytail: try-catch di sini — gagal kirim notifikasi TIDAK boleh rollback enrollment & payment_status
        try {
            $classMode = 'offline';
            try { $classMode = strtolower($order['class_mode'] ?? 'offline'); } catch (Exception $e) {}
            if (!in_array($classMode, ['online','offline'], true)) $classMode = 'offline';
            if ($classMode === 'offline') {
                if (!empty($waLink) && strpos($waLink, 'https://chat.whatsapp.com/') === 0) {
                    $msg = "*PEMBAYARAN LUNAS - MCM*\n\n";
                    $msg .= "Halo " . $order['customer_name'] . ", pembayaran Anda untuk *" . $className . "* (Offline) sudah kami terima. ✅\n";
                    $msg .= "No. Order: " . $orderNumber . "\n\n";
                    $msg .= "Silakan gabung ke grup WhatsApp kelas untuk info jadwal & lokasi pelatihan:\n" . $waLink;
                    wa_send_message($pdo, $order['customer_phone'], $msg, 'pembayaran');
                } else {
                    $msg = "*PEMBAYARAN LUNAS - MCM*\n\n";
                    $msg .= "Halo " . $order['customer_name'] . ", pembayaran Anda untuk *" . $className . "* (Offline) sudah kami terima. ✅\n";
                    $msg .= "No. Order: " . $orderNumber . "\n\n";
                    $msg .= "Admin kami akan segera menghubungi Anda untuk info grup WhatsApp kelas & jadwal pelatihan.";
                    wa_send_message($pdo, $order['customer_phone'], $msg, 'pembayaran');
                    // Notifikasi ke admin karena link kosong
                    try {
                        $adminNumber = '6285793935707';
                        $adminMsg = "[NOTIF OFFLINE] Link WA kosong — Kelas: $className (ID {$order['class_id']}), Order: $orderNumber, Peserta: {$order['customer_name']} ({$order['customer_phone']}) — segera hubungi peserta.";
                        wa_send_message($pdo, $adminNumber, $adminMsg, 'admin_notif');
                    } catch (Exception $ae) {}
                }
            } else {
                $msg = "*PEMBAYARAN LUNAS - MCM*\n\n";
                $msg .= "Halo " . $order['customer_name'] . ", pembayaran Anda untuk *" . $className . "* (Online) sudah kami terima. ✅\n";
                $msg .= "No. Order: " . $orderNumber . "\n";
                $msg .= "Silakan login ke LMS untuk mulai belajar: " . pg_base_url() . "/lms/dashboard.php";
                wa_send_message($pdo, $order['customer_phone'], $msg, 'pembayaran');
            }
        } catch (Exception $we) {}
    } else {
        $stmt = $pdo->prepare("UPDATE orders SET payment_status = ?, payment_method = ? WHERE id = ?");
        $stmt->execute([$newStatus, $method, $order['id']]);
    }

    $pdo->commit();
    echo json_encode(['status' => 'success', 'order_number' => $orderNumber, 'payment_status' => $newStatus]);
} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Gagal memproses webhook']);
}