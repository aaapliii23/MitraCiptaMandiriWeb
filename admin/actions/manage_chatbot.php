<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']);
    exit;
}

require_once '../../includes/db_config.php';
require_once '../../includes/whatsapp_client.php';

try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS `chatbot_intents` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `intent` varchar(50) NOT NULL UNIQUE,
      `keywords` text NOT NULL,
      `reply` text NOT NULL,
      `enabled` tinyint(1) NOT NULL DEFAULT 1,
      `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    wa_seed_intent_defaults($pdo);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan sistem.']);
    exit;
}

$action = $_POST['action'] ?? '';

if ($action === 'create' || $action === 'update') {
    $id = (int)($_POST['id'] ?? 0);
    $intent = strtolower(trim($_POST['intent'] ?? ''));
    $keywords = trim($_POST['keywords'] ?? '');
    $reply = trim($_POST['reply'] ?? '');
    $enabled = !empty($_POST['enabled']) ? 1 : 0;

    if (!preg_match('/^[a-z0-9_]{1,50}$/', $intent) || empty($keywords) || empty($reply)) {
        echo json_encode(['status' => 'error', 'message' => 'Data intent tidak lengkap.']);
        exit;
    }

    try {
        if ($action === 'create') {
            $stmt = $pdo->prepare("INSERT INTO chatbot_intents (intent, keywords, reply, enabled) VALUES (?, ?, ?, ?)");
            $stmt->execute([$intent, $keywords, $reply, $enabled]);
            echo json_encode(['status' => 'success', 'message' => 'Intent berhasil ditambahkan.']);
        } else {
            if (!$id) {
                echo json_encode(['status' => 'error', 'message' => 'Data tidak valid.']);
                exit;
            }
            $stmt = $pdo->prepare("UPDATE chatbot_intents SET intent = ?, keywords = ?, reply = ?, enabled = ? WHERE id = ?");
            $stmt->execute([$intent, $keywords, $reply, $enabled, $id]);
            echo json_encode(['status' => 'success', 'message' => 'Intent berhasil diperbarui.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Intent sudah ada atau terjadi kesalahan.']);
    }
} elseif ($action === 'delete') {
    $id = (int)($_POST['id'] ?? 0);
    if (!$id) {
        echo json_encode(['status' => 'error', 'message' => 'Data tidak valid.']);
        exit;
    }
    try {
        $pdo->prepare("DELETE FROM chatbot_intents WHERE id = ?")->execute([$id]);
        echo json_encode(['status' => 'success', 'message' => 'Intent dihapus.']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan sistem.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Metode request tidak diizinkan.']);
}