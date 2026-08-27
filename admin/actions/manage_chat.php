<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['admin_logged_in'])) exit;

require_once '../../config/database.php';
require_once '../../includes/whatsapp_client.php';

$action = $_POST['action'] ?? '';

if ($action === 'send_reply') {
    $waNumber = trim($_POST['wa_number'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (strpos($waNumber, 'web-') !== 0) {
        $digits = preg_replace('/\D+/', '', $waNumber);
        if (strpos($digits, '0') === 0) {
            $digits = '62' . substr($digits, 1);
        } elseif (strpos($digits, '8') === 0) {
            $digits = '62' . $digits;
        }
        $waNumber = $digits;
    }

    if (empty($waNumber) || empty($message)) {
        echo json_encode(['status' => 'error', 'message' => 'Nomor dan pesan wajib diisi.']);
        exit;
    }

    try {
        wa_send_message($pdo, $waNumber, $message, 'admin', 'admin');
        echo json_encode(['status' => 'success', 'message' => 'Balasan terkirim.']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal mengirim balasan.']);
    }
} elseif ($action === 'delete_thread') {
    $waNumber = trim($_POST['wa_number'] ?? '');
    if ($waNumber) {
        $stmt = $pdo->prepare("DELETE FROM chat_messages WHERE wa_number = ?");
        $stmt->execute([$waNumber]);
        echo json_encode(['status' => 'success', 'message' => 'Percakapan dihapus.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Nomor tidak valid.']);
    }
}