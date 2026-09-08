<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak']);
    exit;
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/security.php';

$lastOrderId = (int)($_GET['last_order_id'] ?? 0);
$lastChatId = (int)($_GET['last_chat_id'] ?? 0);
$isInit = !empty($_GET['init']);

try {
    // Current maximum IDs
    $latestOrderId = (int)$pdo->query("SELECT COALESCE(MAX(id), 0) FROM orders")->fetchColumn();
    $latestChatId = (int)$pdo->query("SELECT COALESCE(MAX(id), 0) FROM chat_messages WHERE direction = 'in' AND sender_type = 'visitor'")->fetchColumn();

    // Summary counts for badges
    $pendingOrders = (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE payment_status IN ('unpaid','pending')")->fetchColumn();
    
    $unreadChats = 0;
    try {
        $unreadChats = (int)$pdo->query("SELECT COUNT(*) FROM (SELECT MAX(id) AS mid FROM chat_messages GROUP BY wa_number) t JOIN chat_messages m ON m.id=t.mid WHERE m.direction='in' AND m.sender_type='visitor'")->fetchColumn();
    } catch (Exception $e) {}

    $newOrders = [];
    $newChats = [];

    // If not first initialization, fetch new items
    if (!$isInit && $lastOrderId > 0 && $latestOrderId > $lastOrderId) {
        $stmt = $pdo->prepare("SELECT o.id, o.order_number, o.customer_name, o.customer_phone, o.amount, o.payment_status, o.created_at, c.name AS class_name 
                               FROM orders o 
                               LEFT JOIN classes c ON o.class_id = c.id 
                               WHERE o.id > ? 
                               ORDER BY o.id ASC 
                               LIMIT 5");
        $stmt->execute([$lastOrderId]);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $newOrders[] = [
                'id' => (int)$row['id'],
                'order_number' => $row['order_number'],
                'customer_name' => $row['customer_name'] ?: 'Pelanggan Baru',
                'customer_phone' => $row['customer_phone'] ?: '',
                'class_name' => $row['class_name'] ?: 'Pelatihan MCM',
                'amount' => (int)$row['amount'],
                'amount_formatted' => 'Rp ' . number_format((int)$row['amount'], 0, ',', '.'),
                'payment_status' => $row['payment_status'],
                'time' => date('H:i', strtotime($row['created_at']))
            ];
        }
    }

    if (!$isInit && $lastChatId > 0 && $latestChatId > $lastChatId) {
        $stmt = $pdo->prepare("SELECT id, wa_number, message, created_at 
                               FROM chat_messages 
                               WHERE id > ? AND direction = 'in' AND sender_type = 'visitor' 
                               ORDER BY id ASC 
                               LIMIT 5");
        $stmt->execute([$lastChatId]);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $isAnon = str_starts_with($row['wa_number'], 'web-');
            $newChats[] = [
                'id' => (int)$row['id'],
                'wa_number' => $row['wa_number'],
                'sender_label' => $isAnon ? 'Pengunjung Web (' . substr($row['wa_number'], 4, 6) . ')' : $row['wa_number'],
                'message' => mb_strimwidth(strip_tags($row['message']), 0, 80, '...'),
                'time' => date('H:i', strtotime($row['created_at']))
            ];
        }
    }

    echo json_encode([
        'status' => 'success',
        'latest_order_id' => $latestOrderId,
        'latest_chat_id' => $latestChatId,
        'pending_orders' => $pendingOrders,
        'unread_chats' => $unreadChats,
        'new_orders' => $newOrders,
        'new_chats' => $newChats,
        'timestamp' => time()
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Gagal memeriksa notifikasi: ' . $e->getMessage()
    ]);
}
