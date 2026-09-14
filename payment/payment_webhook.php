<?php
// Webhook notifikasi DOKU (Jokul Direct / Checkout) — server-to-server, TANPA sesi/CSRF.
// Daftarkan URL file ini sebagai Notification URL di Dashboard DOKU, mis:
//   https://domain-anda/payment/payment_webhook.php
// DOKU mengirim header Client-Id/Request-Id/Request-Timestamp/Signature (HMAC-SHA256)
// dan body {service, acquirer, channel:{id}, transaction:{status,...}, order:{invoice_number,...}}.
// transaction.status: SUCCESS → paid (+enrollment LMS, finance, WA); FAILED/EXPIRED →
// update payment_status saja; PENDING → ack 200 tanpa perubahan. Transfer manual tidak
// pernah disentuh endpoint ini (tetap konfirmasi admin).
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/payment_gateway.php';
require_once __DIR__ . '/../includes/whatsapp_client.php';

header('Content-Type: application/json');

$rawBody = file_get_contents('php://input');
if (!is_string($rawBody)) $rawBody = '';
if (function_exists('getallheaders')) {
    $headers = getallheaders() ?: [];
} else {
    $headers = [];
    foreach ($_SERVER as $k => $v) {
        if (str_starts_with($k, 'HTTP_')) {
            $name = str_replace('_', '-', strtolower(substr($k, 5)));
            $headers[$name] = $v;
        }
    }
}
// Request-Target = path Notification URL terdaftar (dukung instalasi subfolder)
$targetPath = parse_url($_SERVER['REQUEST_URI'] ?? '/payment/payment_webhook.php', PHP_URL_PATH);
if (!is_string($targetPath) || $targetPath === '') $targetPath = '/payment/payment_webhook.php';

if (!pg_verify_doku_notify($rawBody, $headers, $targetPath)) {
    error_log('[doku_webhook] invalid signature from ' . ($_SERVER['REMOTE_ADDR'] ?? '?'));
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Invalid signature']);
    exit;
}

$payload = json_decode($rawBody, true);
if (!is_array($payload)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Payload tidak valid']);
    exit;
}

$orderNumber = (string)($payload['order']['invoice_number'] ?? '');
$txnStatus = strtoupper((string)($payload['transaction']['status'] ?? ''));
$channelId = (string)($payload['channel']['id'] ?? '');
if ($orderNumber === '' || $txnStatus === '') {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Payload tidak valid']);
    exit;
}
error_log("[doku_webhook] $orderNumber channel=$channelId status=$txnStatus");

try {
    pg_ensure_payment_columns($pdo);
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE order_number = ? LIMIT 1");
    $stmt->execute([$orderNumber]);
    $order = $stmt->fetch();
    if (!$order) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Order tidak ditemukan']);
        exit;
    }
    // Transfer manual: abaikan notifikasi DOKU (tetap konfirmasi admin), tapi ack 200 agar DOKU tidak retry
    if (($order['payment_method'] ?? '') === 'manual_transfer' || !empty($order['transfer_proof'])) {
        echo json_encode(['status' => 'success', 'message' => 'Ignored: manual transfer', 'order_number' => $orderNumber]);
        exit;
    }
    if ($order['payment_status'] === 'paid') {
        echo json_encode(['status' => 'success', 'message' => 'Already processed', 'order_number' => $orderNumber, 'payment_status' => 'paid']);
        exit;
    }
    if ($txnStatus === 'SUCCESS') {
        $vaNum = $payload['virtual_account_info']['virtual_account_number'] ?? null;
        $gatewayRef = $vaNum ?: (string)($payload['transaction']['original_request_id'] ?? '');
        $ok = pg_apply_doku_paid($pdo, $order, pg_channel_to_method($channelId, $order['payment_method'] ?? null), $gatewayRef ?: null);
        if (!$ok) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Gagal memproses pembayaran']);
            exit;
        }
        echo json_encode(['status' => 'success', 'order_number' => $orderNumber, 'payment_status' => 'paid']);
        exit;
    }
    if ($txnStatus === 'PENDING' || $txnStatus === 'REDIRECT') {
        echo json_encode(['status' => 'success', 'message' => 'Pending, menunggu pembayaran', 'order_number' => $orderNumber]);
        exit;
    }
    if (in_array($txnStatus, ['FAILED', 'EXPIRED', 'REFUNDED', 'TIMEOUT'], true)) {
        $new = strtolower($txnStatus === 'TIMEOUT' ? 'expired' : ($txnStatus === 'REFUNDED' ? 'failed' : $txnStatus));
        $pdo->prepare("UPDATE orders SET payment_status = ? WHERE id = ? AND payment_status != 'paid'")->execute([$new, $order['id']]);
        echo json_encode(['status' => 'success', 'order_number' => $orderNumber, 'payment_status' => $new]);
        exit;
    }
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Status tidak dikenal: ' . $txnStatus]);
} catch (Exception $e) {
    error_log('[doku_webhook] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Gagal memproses webhook']);
}
