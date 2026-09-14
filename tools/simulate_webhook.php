<?php
// Simulator notifikasi DOKU (format asli Jokul Direct) ke payment_webhook.php lokal.
// Tanda tangan HMAC-SHA256 dihitung dengan Secret Key asli sehingga lolos verifikasi.
//   CLI : php tools/simulate_webhook.php ORD-XXXX paid [VIRTUAL_ACCOUNT_BNI]
//   Web : tools/simulate_webhook.php?order=ORD-XXXX&status=paid
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/payment_gateway.php';

$orderNumber = $_GET['order'] ?? ($argv[1] ?? '');
$status = strtolower($_GET['status'] ?? ($argv[2] ?? 'paid'));
$channel = strtoupper($_GET['channel'] ?? ($argv[3] ?? 'VIRTUAL_ACCOUNT_BNI'));

$map = ['paid' => 'SUCCESS', 'failed' => 'FAILED', 'expired' => 'EXPIRED'];
if ($orderNumber === '' || !isset($map[$status])) {
    echo "Usage:\n  CLI : php tools/simulate_webhook.php ORD-XXXX paid [VIRTUAL_ACCOUNT_BNI]\n  Web : tools/simulate_webhook.php?order=ORD-XXXX&status=paid\n";
    exit(1);
}

$stmt = $pdo->prepare("SELECT amount FROM orders WHERE order_number = ?");
$stmt->execute([$orderNumber]);
$amount = $stmt->fetchColumn();
if ($amount === false) {
    echo "Order tidak ditemukan: $orderNumber\n";
    exit(1);
}

$cfg = pg_config();
$body = json_encode([
    'service' => ['id' => 'VIRTUAL_ACCOUNT'],
    'acquirer' => ['id' => 'BNI'],
    'channel' => ['id' => $channel],
    'transaction' => ['status' => $map[$status], 'date' => gmdate("Y-m-d\TH:i:s\Z"), 'original_request_id' => 'SIM-' . strtoupper(uniqid())],
    'order' => ['invoice_number' => $orderNumber, 'amount' => (int)$amount],
    'virtual_account_info' => ['virtual_account_number' => 'SIM' . substr(preg_replace('/\D/', '', $orderNumber), -8)],
]);
// Request-Target = path webhook di dev server root (router.php serve dari repo root)
$target = '/payment/payment_webhook.php';
$reqId = bin2hex(random_bytes(8));
$reqTs = gmdate("Y-m-d\TH:i:s\Z");
$digest = base64_encode(hash('sha256', $body, true));
$sig = 'HMACSHA256=' . base64_encode(hash_hmac('sha256', "Client-Id:{$cfg['client_id']}\nRequest-Id:$reqId\nRequest-Timestamp:$reqTs\nRequest-Target:$target\nDigest:$digest", $cfg['secret_key'], true));

$ch = curl_init('http://127.0.0.1:8000/payment/payment_webhook.php');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'Client-Id: ' . $cfg['client_id'], 'Request-Id: ' . $reqId, 'Request-Timestamp: ' . $reqTs, 'Signature: ' . $sig],
    CURLOPT_POSTFIELDS => $body,
]);
$resp = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Mengirim status '$status' untuk $orderNumber...\nHTTP $code\n$resp\n";
