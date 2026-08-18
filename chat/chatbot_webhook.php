<?php
require_once '../includes/db_config.php';
require_once '../includes/whatsapp_client.php';

header('Content-Type: application/json');

function wa_verify_signature($body, $signature)
{
    $secret = defined('MCM_WA_WEBHOOK_SECRET') ? MCM_WA_WEBHOOK_SECRET : '';
    if ($secret === '') {
        return true;
    }
    $computed = 'sha256=' . hash_hmac('sha256', $body, $secret);
    return hash_equals($computed, $signature);
}

// --- Verifikasi webhook (GET) sesuai pola Meta WhatsApp Cloud API ---
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $mode = $_GET['hub_mode'] ?? '';
    $token = $_GET['hub_verify_token'] ?? '';
    $challenge = $_GET['hub_challenge'] ?? '';
    if ($mode === 'subscribe' && defined('WA_VERIFY_TOKEN') && hash_equals(WA_VERIFY_TOKEN, $token)) {
        header('Content-Type: text/plain');
        echo $challenge;
        exit;
    }
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Verification failed']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

// --- Verifikasi signature POST ---
$body = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';
if (!wa_verify_signature($body, $signature)) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Invalid signature']);
    exit;
}

$payload = json_decode($body, true);
if (!is_array($payload)) {
    $payload = $_POST;
}

// Ekstrak pesan (dukung format Meta Cloud API + format sederhana)
$from = $payload['entry'][0]['changes'][0]['value']['messages'][0]['from'] ?? null;
$text = $payload['entry'][0]['changes'][0]['value']['messages'][0]['text']['body'] ?? null;
if ($from === null && isset($payload['from'])) {
    $from = $payload['from'];
    $text = $payload['message'] ?? $payload['text'] ?? null;
}

if ($from === null) {
    echo json_encode(['status' => 'ok', 'message' => 'No message']);
    exit;
}

$from = preg_replace('/\D+/', '', (string)$from);
$text = trim((string)$text);
if ($text === '') {
    echo json_encode(['status' => 'ok', 'message' => 'Empty text']);
    exit;
}

try {
    $intent = wa_match_intent($text, $pdo);
    wa_log_inbound($pdo, $from, $text, $intent);

    $reply = wa_intent_reply($pdo, $intent);
    wa_send_message($pdo, $from, $reply, $intent);

    echo json_encode(['status' => 'ok', 'intent' => $intent, 'reply' => $reply]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Gagal memproses pesan']);
}