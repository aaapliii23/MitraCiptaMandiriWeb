<?php
if (file_exists(__DIR__ . '/../includes/config_secrets.php')) {
    require_once __DIR__ . '/../includes/config_secrets.php';
}

$from = $_GET['from'] ?? ($argv[1] ?? '6281200000001');
$text = $_GET['message'] ?? ($argv[2] ?? 'berapa harga kelas?');

if ($text === '' || $from === '') {
    echo "Usage:\n  CLI : php tools/send_chatbot.php 6281200000001 \"berapa harga kelas?\"\n  Web : tools/send_chatbot.php?from=6281200000001&message=berapa+harga\n";
    exit(1);
}

$body = json_encode(['from' => $from, 'message' => $text]);
$signature = '';
$secret = defined('MCM_WA_WEBHOOK_SECRET') ? MCM_WA_WEBHOOK_SECRET : '';
if ($secret !== '') {
    $signature = 'sha256=' . hash_hmac('sha256', $body, $secret);
}

$headers = ['Content-Type: application/json'];
if ($signature !== '') {
    $headers[] = 'X-Hub-Signature-256: ' . $signature;
}

$ch = curl_init('http://127.0.0.1:8000/chat/chatbot_webhook.php');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => $headers,
    CURLOPT_POSTFIELDS => $body,
]);
$resp = curl_exec($ch);
curl_close($ch);

echo $resp . "\n";