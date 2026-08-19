<?php
session_start();
require_once '../config/database.php';
require_once '../includes/whatsapp_client.php';

header('Content-Type: application/json');

if (empty($_SESSION['chat_visitor_id'])) {
    $_SESSION['chat_visitor_id'] = substr(bin2hex(random_bytes(8)), 0, 12);
}
$visitorNumber = 'web-' . $_SESSION['chat_visitor_id'];
$userId = isset($_SESSION['user_logged_in']) ? (int)($_SESSION['user_id'] ?? 0) : null;
if ($userId < 1) $userId = null;

$action = $_GET['action'] ?? ($_POST['action'] ?? 'history');

if ($action === 'history') {
    try {
        $stmt = $pdo->prepare("SELECT direction, message, matched_intent, DATE_FORMAT(created_at, '%H:%i') AS time FROM chat_messages WHERE wa_number = ? ORDER BY id ASC LIMIT 100");
        $stmt->execute([$visitorNumber]);
        echo json_encode(['status' => 'success', 'messages' => $stmt->fetchAll()]);
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

        $reply = wa_intent_reply($pdo, $intent);
        wa_send_message($pdo, $visitorNumber, $reply, $intent);

        echo json_encode(['status' => 'success', 'reply' => $reply, 'intent' => $intent]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan sistem.']);
    }
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Aksi tidak valid.']);