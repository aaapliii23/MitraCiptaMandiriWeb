<?php
session_start();
require_once '../config/database.php';
require_once '../includes/whatsapp_client.php';

header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

$userId = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true ? (int)($_SESSION['user_id'] ?? 0) : null;
if ($userId !== null && $userId < 1) $userId = null;

if ($userId) {
    // Pengguna terdaftar & login (Siswa LMS) -> thread khusus user-{id}
    $threadNumber = 'user-' . $userId;
    $isUser = true;
} else {
    // Pengunjung umum belum login (Anonim) -> thread khusus web-{vid}
    if (empty($_SESSION['chat_visitor_id'])) {
        $_SESSION['chat_visitor_id'] = substr(bin2hex(random_bytes(8)), 0, 12);
    }
    $clientVid = preg_replace('/[^a-f0-9]/', '', strtolower($_POST['visitor_id'] ?? $_GET['visitor_id'] ?? $_COOKIE['mcmChatVid'] ?? ''));
    if ($clientVid !== '' && strlen($clientVid) === 12) {
        $_SESSION['chat_visitor_id'] = $clientVid;
    }
    setcookie('mcmChatVid', $_SESSION['chat_visitor_id'], time() + 90 * 24 * 60 * 60, '/');
    $threadNumber = 'web-' . $_SESSION['chat_visitor_id'];
    $isUser = false;
}

$action = $_GET['action'] ?? ($_POST['action'] ?? 'history');

if ($action === 'history') {
    try {
        // Tandai pesan keluar dari admin/bot sebagai sudah dibaca oleh pengguna
        $pdo->prepare("UPDATE chat_messages SET is_read = 1, read_at = NOW() WHERE wa_number = ? AND direction = 'out' AND is_read = 0")->execute([$threadNumber]);

        $selectSql = "SELECT id, direction, sender_type, message, matched_intent, is_read, read_at, 
                             DATE_FORMAT(created_at, '%H:%i') AS time, 
                             DATE_FORMAT(read_at, '%H:%i') AS read_time, 
                             DATE_FORMAT(read_at, '%d %b %Y %H:%i') AS read_full_time 
                      FROM chat_messages WHERE wa_number = ? ORDER BY id ASC LIMIT 100";

        if ($isUser) {
            $stmt = $pdo->prepare($selectSql);
            $stmt->execute([$threadNumber]);
            $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode([
                'status' => 'success',
                'messages' => $messages,
                'is_user' => true,
                'user_id' => $userId,
                'user_name' => $_SESSION['user_name'] ?? 'Siswa MCM'
            ]);
        } else {
            $stmt = $pdo->prepare($selectSql);
            $stmt->execute([$threadNumber]);
            $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode([
                'status' => 'success',
                'messages' => $messages,
                'is_user' => false,
                'visitor_id' => $_SESSION['chat_visitor_id'] ?? ''
            ]);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal memuat percakapan.']);
    }
    exit;
}

if ($action === 'send') {
    $message = trim($_POST['message'] ?? '');
    $token = $_POST['csrf_token'] ?? '';
    $csrf = $_SESSION['csrf_token'] ?? '';

    if (empty($csrf) || !hash_equals($csrf, $token)) {
        echo json_encode(['status' => 'error', 'message' => 'Sesi tidak valid. Silakan muat ulang halaman.']);
        exit;
    }
    if ($message === '') {
        echo json_encode(['status' => 'error', 'message' => 'Pesan tidak boleh kosong.']);
        exit;
    }

    try {
        $intent = wa_match_intent($message, $pdo);
        wa_log_inbound($pdo, $threadNumber, $message, $intent, $userId);

        // Handover: jika admin balas dalam 30 menit terakhir, bot diam hanya untuk pesan bebas (intent null)
        $handover = false;
        if ($intent === null) {
            try {
                if ($isUser) {
                    $st = $pdo->prepare("SELECT id FROM chat_messages WHERE (wa_number=? OR user_id=?) AND sender_type='admin' AND created_at > DATE_SUB(NOW(), INTERVAL 30 MINUTE) ORDER BY id DESC LIMIT 1");
                    $st->execute([$threadNumber, $userId]);
                } else {
                    $st = $pdo->prepare("SELECT id FROM chat_messages WHERE wa_number=? AND sender_type='admin' AND created_at > DATE_SUB(NOW(), INTERVAL 30 MINUTE) ORDER BY id DESC LIMIT 1");
                    $st->execute([$threadNumber]);
                }
                if ($st->fetchColumn()) $handover = true;
            } catch (Exception $e) {}
        }
        if ($handover) {
            echo json_encode([
                'status' => 'success',
                'reply' => null,
                'intent' => $intent,
                'handover' => true,
                'is_user' => $isUser
            ]);
            exit;
        }

        $reply = wa_intent_reply($pdo, $intent);
        wa_send_message($pdo, $threadNumber, $reply, $intent, 'bot', $userId);

        echo json_encode([
            'status' => 'success',
            'reply' => $reply,
            'intent' => $intent,
            'is_user' => $isUser
        ]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan sistem.']);
    }
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Aksi tidak valid.']);