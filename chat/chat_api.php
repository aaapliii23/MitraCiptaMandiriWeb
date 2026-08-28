<?php
session_start();
require_once '../config/database.php';
require_once '../includes/whatsapp_client.php';

header('Content-Type: application/json');

if (empty($_SESSION['chat_visitor_id'])) {
    $_SESSION['chat_visitor_id'] = substr(bin2hex(random_bytes(8)), 0, 12);
}
// ponytail: persist anon thread via localStorage+cookie vid (90 hari, survive PHP session GC)
$clientVid = preg_replace('/[^a-f0-9]/', '', strtolower($_POST['visitor_id'] ?? $_GET['visitor_id'] ?? $_COOKIE['mcmChatVid'] ?? ''));
if ($clientVid !== '' && strlen($clientVid) === 12) {
    $_SESSION['chat_visitor_id'] = $clientVid;
}
$visitorNumber = 'web-' . $_SESSION['chat_visitor_id'];
// set cookie 90 hari agar anonim kembali beberapa jam tetap terhubung
setcookie('mcmChatVid', $_SESSION['chat_visitor_id'], time()+90*24*60*60, '/');
// ponytail: lazy migrasi anonim -> user (jika sudah login, hubungkan riwayat lama)
if ($userId) {
    try { $pdo->prepare("UPDATE chat_messages SET user_id=? WHERE wa_number=? AND (user_id IS NULL OR user_id=0)")->execute([$userId, $visitorNumber]); } catch (Exception $e) {}
}
$userId = isset($_SESSION['user_logged_in']) ? (int)($_SESSION['user_id'] ?? 0) : null;
if ($userId < 1) $userId = null;

$action = $_GET['action'] ?? ($_POST['action'] ?? 'history');

if ($action === 'history') {
    try {
        if ($userId) {
            $stmt = $pdo->prepare("SELECT direction, sender_type, message, matched_intent, DATE_FORMAT(created_at, '%H:%i') AS time FROM chat_messages WHERE wa_number = ? OR (user_id = ? AND user_id IS NOT NULL) ORDER BY id ASC LIMIT 100");
            $stmt->execute([$visitorNumber, $userId]);
        } else {
            $stmt = $pdo->prepare("SELECT direction, sender_type, message, matched_intent, DATE_FORMAT(created_at, '%H:%i') AS time FROM chat_messages WHERE wa_number = ? ORDER BY id ASC LIMIT 100");
            $stmt->execute([$visitorNumber]);
        }
        echo json_encode(['status' => 'success', 'messages' => $stmt->fetchAll(), 'visitor_id' => $_SESSION['chat_visitor_id']]);
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
        wa_log_inbound($pdo, $visitorNumber, $message, $intent, $userId);

        // handover: jika admin balas <30 menit terakhir, bot diam hanya untuk pesan bebas (intent null)
        // ponytail: quick-reply (harga/jadwal/program/lokasi) tetap dibalas bot agar visitor dapat jawaban instan
        $handover = false;
        if ($intent === null) {
            try {
                $st = $pdo->prepare("SELECT id FROM chat_messages WHERE wa_number=? AND sender_type='admin' AND created_at > DATE_SUB(NOW(), INTERVAL 30 MINUTE) ORDER BY id DESC LIMIT 1");
                $st->execute([$visitorNumber]);
                if ($st->fetchColumn()) $handover = true;
            } catch (Exception $e) {}
        }
        if ($handover) {
            echo json_encode(['status' => 'success', 'reply' => null, 'intent' => $intent, 'handover' => true, 'visitor_id' => $_SESSION['chat_visitor_id']]);
            exit;
        }

        $reply = wa_intent_reply($pdo, $intent);
        wa_send_message($pdo, $visitorNumber, $reply, $intent, 'bot');
        // eskalasi WA dinonaktifkan: anonim 100% tidak sentuh API WA (hanya DB + dashboard)


        echo json_encode(['status' => 'success', 'reply' => $reply, 'intent' => $intent, 'visitor_id' => $_SESSION['chat_visitor_id']]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan sistem.']);
    }
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Aksi tidak valid.']);