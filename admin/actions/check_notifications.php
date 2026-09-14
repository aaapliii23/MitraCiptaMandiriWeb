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
        $unreadChats = (int)$pdo->query(
            "SELECT COUNT(*) FROM (SELECT wa_number, MAX(id) AS mid FROM chat_messages WHERE sender_type='visitor' GROUP BY wa_number) t JOIN chat_messages m ON m.id=t.mid WHERE m.direction='in' AND m.is_read=0"
        )->fetchColumn();
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
            $isUser = str_starts_with($row['wa_number'], 'user-');
            $senderLabel = $row['wa_number'];
            if ($isAnon) {
                $senderLabel = 'Pengunjung Web (' . substr($row['wa_number'], 4, 6) . ')';
            } elseif ($isUser) {
                $uid = (int)substr($row['wa_number'], 5);
                try {
                    $st = $pdo->prepare("SELECT name FROM users WHERE id = ?");
                    $st->execute([$uid]);
                    $uName = $st->fetchColumn();
                    $senderLabel = $uName ? ($uName . ' (Siswa)') : ('Siswa #' . $uid);
                } catch (Exception $e) {
                    $senderLabel = 'Siswa #' . $uid;
                }
            }
            $newChats[] = [
                'id' => (int)$row['id'],
                'wa_number' => $row['wa_number'],
                'sender_label' => $senderLabel,
                'message' => mb_strimwidth(strip_tags($row['message']), 0, 80, '...'),
                'time' => date('H:i', strtotime($row['created_at']))
            ];
        }
    }

    // Recent lists for dropdowns
    $recentOrders = [];
    $stmtRecentOrders = $pdo->query("SELECT o.id, o.order_number, o.customer_name, o.amount, o.created_at, c.name AS class_name 
                                    FROM orders o 
                                    LEFT JOIN classes c ON o.class_id = c.id 
                                    WHERE o.payment_status IN ('unpaid','pending') 
                                    ORDER BY o.created_at DESC LIMIT 5");
    if ($stmtRecentOrders) {
        while ($row = $stmtRecentOrders->fetch(PDO::FETCH_ASSOC)) {
            $recentOrders[] = [
                'id' => (int)$row['id'],
                'order_number' => $row['order_number'],
                'customer_name' => $row['customer_name'] ?: 'Pelanggan',
                'class_name' => $row['class_name'] ?: 'Pelatihan MCM',
                'amount_formatted' => 'Rp ' . number_format((int)$row['amount'], 0, ',', '.'),
                'time' => date('H:i', strtotime($row['created_at']))
            ];
        }
    }

    $recentChats = [];
    try {
        $stmtRecentChats = $pdo->query(
            "SELECT m.id, m.wa_number, m.message, m.created_at, u.name AS user_name 
             FROM (SELECT wa_number, MAX(id) AS mid FROM chat_messages WHERE sender_type='visitor' GROUP BY wa_number) t 
             JOIN chat_messages m ON m.id=t.mid 
             LEFT JOIN users u ON (m.user_id=u.id OR (m.wa_number LIKE 'user-%' AND SUBSTRING(m.wa_number, 6) = CAST(u.id AS CHAR)))
             WHERE m.direction='in' 
             ORDER BY m.id DESC LIMIT 5"
        );
        if ($stmtRecentChats) {
            while ($row = $stmtRecentChats->fetch(PDO::FETCH_ASSOC)) {
                $isAnon = str_starts_with($row['wa_number'], 'web-');
                $isUser = str_starts_with($row['wa_number'], 'user-');
                if ($isAnon) {
                    $sLabel = 'Pengunjung Web (' . substr($row['wa_number'], 4, 6) . ')';
                } elseif ($isUser) {
                    $sLabel = !empty($row['user_name']) ? ($row['user_name'] . ' (Siswa)') : ('Siswa #' . substr($row['wa_number'], 5));
                } else {
                    $sLabel = $row['wa_number'];
                }
                $recentChats[] = [
                    'id' => (int)$row['id'],
                    'wa_number' => $row['wa_number'],
                    'sender_label' => $sLabel,
                    'message' => mb_strimwidth(strip_tags($row['message']), 0, 65, '...'),
                    'time' => date('H:i', strtotime($row['created_at']))
                ];
            }
        }
    } catch (Exception $e) {}

    echo json_encode([
        'status' => 'success',
        'latest_order_id' => $latestOrderId,
        'latest_chat_id' => $latestChatId,
        'pending_orders' => $pendingOrders,
        'unread_chats' => $unreadChats,
        'new_orders' => $newOrders,
        'new_chats' => $newChats,
        'recent_orders' => $recentOrders,
        'recent_chats' => $recentChats,
        'timestamp' => time()
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Gagal memeriksa notifikasi: ' . $e->getMessage()
    ]);
}
