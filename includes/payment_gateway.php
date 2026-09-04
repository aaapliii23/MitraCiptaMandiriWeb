<?php
if (file_exists(__DIR__ . '/../config/secrets.php')) {
    require_once __DIR__ . '/../config/secrets.php';
}

function pg_mode() {
    $m = defined('PAYMENT_MODE') ? PAYMENT_MODE : 'production';
    // mock sudah dihapus — fallback ke production jika masih tertulis mock
    if ($m === 'mock') $m = 'production';
    return $m;
}

// Ambil kredensial dengan fallback 3 nama baru + 3 nama lama (kompatibel)
function pg_config() {
    $clientId = '';
    if (defined('PAYMENT_GATEWAY_CLIENT_ID') && PAYMENT_GATEWAY_CLIENT_ID !== '') $clientId = PAYMENT_GATEWAY_CLIENT_ID;
    elseif (defined('PAYMENT_CLIENT_ID') && PAYMENT_CLIENT_ID !== '') $clientId = PAYMENT_CLIENT_ID;
    elseif (defined('PAYMENT_CLIENT_KEY') && PAYMENT_CLIENT_KEY !== '') $clientId = PAYMENT_CLIENT_KEY;

    $apiKey = '';
    if (defined('PAYMENT_GATEWAY_API_KEY') && PAYMENT_GATEWAY_API_KEY !== '') $apiKey = PAYMENT_GATEWAY_API_KEY;
    elseif (defined('PAYMENT_API_KEY') && PAYMENT_API_KEY !== '') $apiKey = PAYMENT_API_KEY;
    elseif (defined('PAYMENT_SERVER_KEY') && PAYMENT_SERVER_KEY !== '') $apiKey = PAYMENT_SERVER_KEY;

    $secretKey = '';
    if (defined('PAYMENT_GATEWAY_SECRET_KEY') && PAYMENT_GATEWAY_SECRET_KEY !== '') $secretKey = PAYMENT_GATEWAY_SECRET_KEY;
    elseif (defined('PAYMENT_SECRET_KEY') && PAYMENT_SECRET_KEY !== '') $secretKey = PAYMENT_SECRET_KEY;
    elseif (defined('PAYMENT_WEBHOOK_SIGNATURE_KEY') && PAYMENT_WEBHOOK_SIGNATURE_KEY !== '') $secretKey = PAYMENT_WEBHOOK_SIGNATURE_KEY;

    $apiUrl = defined('PAYMENT_GATEWAY_API_URL') ? PAYMENT_GATEWAY_API_URL : '';

    return [
        'mode' => pg_mode(),
        'client_id' => $clientId,
        'api_key' => $apiKey,
        'secret_key' => $secretKey,
        'api_url' => $apiUrl,
    ];
}

function pg_client_id() {
    return pg_config()['client_id'];
}

function pg_base_url() {
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
    if (basename($scriptDir) === 'payment') {
        $baseFolder = dirname($scriptDir);
    } else {
        $baseFolder = $scriptDir;
    }
    $baseFolder = rtrim($baseFolder, '/');
    if ($baseFolder === '/' || $baseFolder === '\\' || $baseFolder === '.') {
        $baseFolder = '';
    }

    return ($https ? 'https' : 'http') . '://' . $host . $baseFolder;
}

function pg_create_transaction($pdo, $order, $class) {
    $cfg = pg_config();
    $mode = $cfg['mode'];

    // Butuh API Key + Secret untuk real gateway
    if (empty($cfg['api_key']) || empty($cfg['secret_key'])) {
        error_log("[Payment] PAYMENT_GATEWAY_API_KEY / SECRET_KEY kosong (mode=$mode). Isi di config/secrets.php");
        return ['status' => 'error', 'message' => 'Payment Gateway belum dikonfigurasi. Isi Client ID, API Key, Secret Key di config/secrets.php'];
    }

    // Tentukan endpoint: custom URL > Midtrans fallback
    $apiUrl = trim($cfg['api_url']);
    $isMidtrans = false;
    if ($apiUrl === '') {
        // fallback Midtrans Snap (paling umum di Indonesia)
        $isMidtrans = true;
        $apiUrl = ($mode === 'production')
            ? 'https://app.midtrans.com/snap/v1/transactions'
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';
    }

    $isDoku = str_contains($apiUrl, 'doku.com');
    // Payload — sesuaikan gateway
    if ($isDoku) {
        $payload = [
            'order' => [
                'amount' => (int)$order['amount'],
                'invoice_number' => $order['order_number'],
                'currency' => 'IDR',
                'callback_url' => pg_base_url() . '/payment/payment_status.php?order=' . urlencode($order['order_number']),
                'auto_redirect' => true,
            ],
            'payment' => ['payment_due_date' => 60],
            'customer' => [
                'name' => $class['name'] ?? 'Customer',
                'email' => 'customer@mcm.id',
                'phone' => '628000000000',
            ],
        ];
    } else {
        $payload = [
            'transaction_details' => [
                'order_id' => $order['order_number'],
                'gross_amount' => (int)$order['amount'],
            ],
            'customer_details' => [
                'first_name' => $class['name'] ?? 'Customer',
                'email' => 'customer@example.com',
            ],
            'item_details' => [[
                'id' => $class['id'] ?? $order['id'],
                'price' => (int)$order['amount'],
                'quantity' => 1,
                'name' => substr($class['name'] ?? 'Pembayaran MCM', 0, 50),
            ]],
            'client_id' => $cfg['client_id'],
        ];
    }

    $ch = curl_init($apiUrl);
    $headers = ['Content-Type: application/json', 'Accept: application/json'];
    if ($isDoku) {
        $requestId = bin2hex(random_bytes(8));
        $requestTimestamp = gmdate("Y-m-d\TH:i:s\Z");
        $requestTarget = parse_url($apiUrl, PHP_URL_PATH) ?? '/checkout/v1/payment';
        $bodyJson = json_encode($payload);
        $digest = base64_encode(hash('sha256', $bodyJson, true));
        $sigComponents = "Client-Id:" . $cfg['client_id'] . "\n"
                       . "Request-Id:" . $requestId . "\n"
                       . "Request-Timestamp:" . $requestTimestamp . "\n"
                       . "Request-Target:" . $requestTarget . "\n"
                       . "Digest:" . $digest;
        $signature = base64_encode(hash_hmac('sha256', $sigComponents, $cfg['secret_key'], true));
        $headers[] = 'Client-Id: ' . $cfg['client_id'];
        $headers[] = 'Request-Id: ' . $requestId;
        $headers[] = 'Request-Timestamp: ' . $requestTimestamp;
        $headers[] = 'Signature: HMACSHA256=' . $signature;
        $headers[] = 'Digest: SHA-256=' . $digest;
        // DOKU juga butuh API Key via header X-Api-Key kadang
        $headers[] = 'X-Api-Key: ' . $cfg['api_key'];
    } elseif ($isMidtrans) {
        $headers[] = 'Authorization: Basic ' . base64_encode($cfg['api_key'] . ':');
    } else {
        if ($cfg['client_id'] !== '') $headers[] = 'X-Client-ID: ' . $cfg['client_id'];
        $headers[] = 'X-API-Key: ' . $cfg['api_key'];
        $headers[] = 'X-Secret-Key: ' . $cfg['secret_key'];
    }

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_TIMEOUT => 20,
    ]);
    $resp = curl_exec($ch);
    $err = curl_error($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($resp === false) {
        error_log("[Payment] curl gagal ke $apiUrl: $err");
        return ['status' => 'error', 'message' => 'Gagal terhubung ke Payment Gateway: ' . $err];
    }

    $data = json_decode($resp, true);
    // Midtrans sukses: { token, redirect_url }
    if ($isMidtrans && $code >= 200 && $code < 300 && isset($data['redirect_url'])) {
        $ref = $data['token'] ?? $order['order_number'];
        $pdo->prepare("UPDATE orders SET payment_gateway_ref = ? WHERE id = ?")->execute([$ref, $order['id']]);
        return ['status' => 'success', 'payment_url' => $data['redirect_url'], 'payment_gateway_ref' => $ref];
    }
    // DOKU sukses: { response: { payment: { url } } } atau { url }
    if ($isDoku && $code >= 200 && $code < 300) {
        $payUrl = $data['response']['payment']['url'] ?? $data['response']['url'] ?? $data['payment']['url'] ?? $data['url'] ?? $data['data']['response']['payment']['url'] ?? '';
        if ($payUrl !== '') {
            $ref = $data['response']['payment']['token_id'] ?? $data['token'] ?? $data['id'] ?? $order['order_number'];
            $pdo->prepare("UPDATE orders SET payment_gateway_ref = ? WHERE id = ?")->execute([$ref, $order['id']]);
            return ['status' => 'success', 'payment_url' => $payUrl, 'payment_gateway_ref' => $ref];
        }
    }
    // Gateway custom: harapkan { payment_url / redirect_url / checkout_url }
    if ($code >= 200 && $code < 300) {
        $payUrl = $data['payment_url'] ?? $data['redirect_url'] ?? $data['checkout_url'] ?? $data['url'] ?? $data['response']['payment']['url'] ?? '';
        if ($payUrl !== '') {
            $ref = $data['token'] ?? $data['id'] ?? $order['order_number'];
            $pdo->prepare("UPDATE orders SET payment_gateway_ref = ? WHERE id = ?")->execute([$ref, $order['id']]);
            return ['status' => 'success', 'payment_url' => $payUrl, 'payment_gateway_ref' => $ref];
        }
    }

    $errMsg = '';
    if (isset($data['error'])) $errMsg = is_array($data['error']) ? ($data['error']['message'] ?? json_encode($data['error'])) : $data['error'];
    elseif (isset($data['message'])) $errMsg = is_array($data['message']) ? json_encode($data['message']) : $data['message'];
    else $errMsg = $data['status_message'] ?? substr($resp, 0, 300);
    error_log("[Payment] gateway error HTTP $code: $resp");
    return ['status' => 'error', 'message' => 'Gateway menolak transaksi: ' . $errMsg];
}

function pg_verify_webhook($payload) {
    $cfg = pg_config();
    // Verifikasi pakai Secret Key: cocokkan signature header
    $secret = $cfg['secret_key'];
    if ($secret === '') return false;
    $provided = $payload['signature'] ?? $payload['x_signature'] ?? $_SERVER['HTTP_X_SIGNATURE'] ?? $_SERVER['HTTP_X_CALLBACK_SIGNATURE'] ?? '';
    if ($provided === '') return false;
    // dukung HMAC SHA256 / SHA512
    foreach (['sha256', 'sha512'] as $algo) {
        $calc = hash_hmac($algo, (string)($payload['order_number'] ?? json_encode($payload)), $secret);
        if (hash_equals($calc, (string)$provided)) return true;
    }
    // fallback: cocokkan langsung
    return hash_equals($secret, (string)$provided);
}

function pg_mock_signature($orderNumber) {
    // deprecated: simulasi dihapus, tetap untuk kompatibilitas
    return hash_hmac('sha256', (string)$orderNumber, pg_config()['secret_key'] ?? 'mcm-removed');
}
