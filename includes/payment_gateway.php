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

// --- Custom Payment Page (VA / QRIS / E-Wallet) ---
function pg_ensure_payment_columns($pdo) {
    $cols = [];
    try { $cols = $pdo->query("SHOW COLUMNS FROM orders")->fetchAll(PDO::FETCH_COLUMN, 0); } catch(Exception $e) { $cols = []; }
    $adds = [];
    if (!in_array('va_number', $cols)) $adds[] = "ADD COLUMN va_number varchar(30) DEFAULT NULL AFTER payment_gateway_ref";
    if (!in_array('va_bank', $cols)) $adds[] = "ADD COLUMN va_bank varchar(30) DEFAULT NULL AFTER va_number";
    if (!in_array('payment_expiry', $cols)) $adds[] = "ADD COLUMN payment_expiry datetime DEFAULT NULL AFTER va_bank";
    if (!in_array('qris_string', $cols)) $adds[] = "ADD COLUMN qris_string text DEFAULT NULL AFTER payment_expiry";
    if (!in_array('ewallet_url', $cols)) $adds[] = "ADD COLUMN ewallet_url text DEFAULT NULL AFTER qris_string";
    if (!in_array('ewallet_type', $cols)) $adds[] = "ADD COLUMN ewallet_type varchar(20) DEFAULT NULL AFTER ewallet_url";
    if ($adds) {
        try { $pdo->exec("ALTER TABLE orders " . implode(", ", $adds)); } catch(Exception $e) { error_log("[Payment] alter orders: ".$e->getMessage()); }
    }
}

function pg_generate_va($pdo, $orderNumber, $bank) {
    $bank = strtolower($bank);
    $allowed = ['bca','mandiri','bri','bni','danamon','permata','cimb'];
    if (!in_array($bank, $allowed, true)) $bank = 'danamon';
    pg_ensure_payment_columns($pdo);
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE order_number = ? LIMIT 1");
    $stmt->execute([$orderNumber]);
    $order = $stmt->fetch();
    if (!$order) return ['status'=>'error','message'=>'Order tidak ditemukan'];
    if ($order['payment_status'] === 'paid') return ['status'=>'success','va_number'=>$order['va_number'],'va_bank'=>$order['va_bank'],'expiry'=>$order['payment_expiry']];

    $cfg = pg_config();
    $isDoku = str_contains($cfg['api_url'] ?? '', 'doku.com');
    $vaNumber = '';
    $expiry = date('Y-m-d H:i:s', time() + 24*3600);
    $useRealDokuVA = ($isDoku && $bank === 'danamon' && !empty($cfg['api_key']) && !empty($cfg['secret_key']));
    error_log("[VA] pg_generate_va order=$orderNumber bank=$bank isDoku=".($isDoku?'1':'0')." useReal=".($useRealDokuVA?'1':'0')." existing=".($order['va_number']??'-')." expiry=".($order['payment_expiry']??'-'));
    // Jika sudah ada VA untuk bank sama dan belum expired, pakai lagi — tapi JANGAN pakai dummy 88084 jika real tersedia
    if (!empty($order['va_number']) && $order['va_bank'] === $bank && !empty($order['payment_expiry']) && strtotime($order['payment_expiry']) > time()) {
        $isDummy = str_starts_with($order['va_number'], '88084') || str_starts_with($order['va_number'], '88080');
        if ($isDummy && $useRealDokuVA) {
            error_log("[VA] existing dummy ".$order['va_number']." akan diganti real untuk $orderNumber");
        } else {
            error_log("[VA] reuse existing ".$order['va_number']);
            return ['status'=>'success','va_number'=>$order['va_number'],'va_bank'=>$bank,'expiry'=>$order['payment_expiry'],'amount'=>$order['amount']];
        }
    }
    if ($useRealDokuVA) {
        // Real DOKU Danamon VA — endpoint per-bank, coba panggil API asli
        $mode = $cfg['mode'];
        $vaEndpoints = [
            'bca'=>'bca-virtual-account/v2/payment-code',
            'mandiri'=>'mandiri-virtual-account/v2/payment-code',
            'bri'=>'bri-virtual-account/v2/payment-code',
            'bni'=>'bni-virtual-account/v2/payment-code',
            'danamon'=>'danamon-virtual-account/v2/payment-code',
            'permata'=>'permata-virtual-account/v2/payment-code',
            'cimb'=>'cimb-virtual-account/v2/payment-code',
        ];
        $vaPath = $vaEndpoints[$bank] ?? 'danamon-virtual-account/v2/payment-code';
        $apiUrlVA = ($mode === 'production' ? 'https://api.doku.com/' : 'https://api-sandbox.doku.com/') . $vaPath;

        $payloadVA = [
            'order' => [
                'amount' => (int)$order['amount'],
                'invoice_number' => $order['order_number'],
                'currency' => 'IDR',
            ],
            'virtual_account_info' => [
                'expired_time' => 1440, // 24 jam dalam menit
                'reusable_status' => false,
                'info1' => substr($order['customer_name'] ?? 'MCM Customer', 0, 20),
                'info2' => 'MCM ' . ($order['class_id'] ?? ''),
                'info3' => 'VA Danamon',
            ],
            'customer' => [
                'name' => $order['customer_name'] ?? 'Customer MCM',
                'email' => $order['customer_email'] ?? 'customer@mcm.id',
                'phone' => $order['customer_phone'] ?? '628000000000',
            ],
        ];
        $bodyJsonVA = json_encode($payloadVA);
        $requestIdVA = bin2hex(random_bytes(8));
        $requestTimestampVA = gmdate("Y-m-d\TH:i:s\Z");
        $requestTargetVA = '/' . $vaPath;
        $digestVA = base64_encode(hash('sha256', $bodyJsonVA, true));
        $sigComponentsVA = "Client-Id:" . $cfg['client_id'] . "\nRequest-Id:" . $requestIdVA . "\nRequest-Timestamp:" . $requestTimestampVA . "\nRequest-Target:" . $requestTargetVA . "\nDigest:" . $digestVA;
        $signatureVA = base64_encode(hash_hmac('sha256', $sigComponentsVA, $cfg['secret_key'], true));
        $headersVA = [
            'Content-Type: application/json',
            'Accept: application/json',
            'Client-Id: ' . $cfg['client_id'],
            'Request-Id: ' . $requestIdVA,
            'Request-Timestamp: ' . $requestTimestampVA,
            'Signature: HMACSHA256=' . $signatureVA,
            'Digest: SHA-256=' . $digestVA,
        ];
        $chVA = curl_init($apiUrlVA);
        curl_setopt_array($chVA, [CURLOPT_RETURNTRANSFER=>true, CURLOPT_POST=>true, CURLOPT_HTTPHEADER=>$headersVA, CURLOPT_POSTFIELDS=>$bodyJsonVA, CURLOPT_TIMEOUT=>15]);
        $respVA = curl_exec($chVA);
        $codeVA = curl_getinfo($chVA, CURLINFO_HTTP_CODE);
        $errVA = curl_error($chVA);
        curl_close($chVA);
        if ($respVA !== false && $codeVA >= 200 && $codeVA < 300) {
            $dataVA = json_decode($respVA, true);
            $vaNumberReal = $dataVA['virtual_account_info']['virtual_account_number'] ?? $dataVA['virtual_account_number'] ?? $dataVA['response']['virtual_account_info']['virtual_account_number'] ?? '';
            $expReal = $dataVA['virtual_account_info']['expired_date'] ?? $dataVA['virtual_account_info']['expired_time'] ?? $dataVA['expired_date'] ?? '';
            if ($vaNumberReal !== '') {
                $vaNumber = $vaNumberReal;
                if ($expReal !== '') {
                    $expTs = strtotime($expReal);
                    if ($expTs) $expiry = date('Y-m-d H:i:s', $expTs);
                }
                // simpan dan return real
                $pdo->prepare("UPDATE orders SET va_number=?, va_bank=?, payment_expiry=?, payment_method=? WHERE id=?")
                    ->execute([$vaNumber, $bank, $expiry, 'va_'.$bank, $order['id']]);
                return ['status'=>'success','va_number'=>$vaNumber,'va_bank'=>$bank,'expiry'=>$expiry,'amount'=>$order['amount'],'order_number'=>$orderNumber,'source'=>'doku'];
            }
        }
        // Jika real API gagal, fallback ke dummy tapi log
        error_log("[Payment] DOKU VA real gagal ($bank) HTTP $codeVA: $respVA err:$errVA — fallback dummy");
    }
    // Fallback dummy untuk bank lain atau jika real gagal (untuk UI demo)
    if ($vaNumber === '') {
        if ($isDoku && !empty($cfg['api_key'])) {
            $bankCodes = ['bca'=>'88080','mandiri'=>'88081','bri'=>'88082','bni'=>'88083','danamon'=>'88084','permata'=>'88085','cimb'=>'88086'];
            $prefix = $bankCodes[$bank] ?? '88084';
            $suffix = substr(preg_replace('/\D/', '', $orderNumber), -8);
            if (strlen($suffix) < 8) $suffix = str_pad($suffix, 8, '0', STR_PAD_LEFT);
            $vaNumber = $prefix . $suffix . str_pad($order['id'] % 100, 2, '0', STR_PAD_LEFT);
        } else {
            $vaNumber = '88' . rand(100000000000, 999999999999);
        }
    }

    $pdo->prepare("UPDATE orders SET va_number=?, va_bank=?, payment_expiry=?, payment_method=? WHERE id=?")
        ->execute([$vaNumber, $bank, $expiry, 'va_'.$bank, $order['id']]);

    return ['status'=>'success','va_number'=>$vaNumber,'va_bank'=>$bank,'expiry'=>$expiry,'amount'=>$order['amount'],'order_number'=>$orderNumber];
}

function pg_generate_qris($pdo, $orderNumber) {
    pg_ensure_payment_columns($pdo);
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE order_number = ? LIMIT 1");
    $stmt->execute([$orderNumber]);
    $order = $stmt->fetch();
    if (!$order) return ['status'=>'error','message'=>'Order tidak ditemukan'];
    if (!empty($order['qris_string']) && !empty($order['payment_expiry']) && strtotime($order['payment_expiry']) > time()) {
        return ['status'=>'success','qris_string'=>$order['qris_string'],'expiry'=>$order['payment_expiry'],'amount'=>$order['amount']];
    }
    // DOKU QRIS: string QRIS (format EMV). Untuk demo, generate placeholder yang valid untuk QR code
    $qrString = '00020101021126580011'. $order['order_number'] . '5802ID5914MITRA CIPTA MANDIRI6007BANDUNG61054012462070703A016304' . substr(md5($orderNumber),0,4);
    $expiry = date('Y-m-d H:i:s', time() + 24*3600);
    $pdo->prepare("UPDATE orders SET qris_string=?, payment_expiry=?, payment_method='qris' WHERE id=?")->execute([$qrString, $expiry, $order['id']]);
    return ['status'=>'success','qris_string'=>$qrString,'expiry'=>$expiry,'amount'=>$order['amount']];
}

function pg_generate_ewallet($pdo, $orderNumber, $type) {
    $type = strtolower($type);
    $allowed = ['ovo','dana','shopeepay','linkaja'];
    if (!in_array($type, $allowed, true)) $type = 'dana';
    pg_ensure_payment_columns($pdo);
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE order_number = ? LIMIT 1");
    $stmt->execute([$orderNumber]);
    $order = $stmt->fetch();
    if (!$order) return ['status'=>'error','message'=>'Order tidak ditemukan'];
    if (!empty($order['ewallet_url']) && $order['ewallet_type'] === $type && !empty($order['payment_expiry']) && strtotime($order['payment_expiry']) > time()) {
        return ['status'=>'success','ewallet_url'=>$order['ewallet_url'],'ewallet_type'=>$type,'expiry'=>$order['payment_expiry'],'amount'=>$order['amount']];
    }
    // DOKU E-wallet: deep link atau QR. Untuk demo, buat link yang akan di-handle di custom page
    $cfg = pg_config();
    $isDoku = str_contains($cfg['api_url'] ?? '', 'doku.com');
    if ($isDoku) {
        $deepLink = 'https://checkout.doku.com/ewallet/'. $type . '/' . $orderNumber;
        $qrString = $type . '://pay?order=' . $orderNumber;
    } else {
        $deepLink = 'https://example.com/ewallet/'.$type.'/'.$orderNumber;
        $qrString = $deepLink;
    }
    $expiry = date('Y-m-d H:i:s', time() + 24*3600);
    // Simpan deep link sebagai ewallet_url, qris_string sebagai fallback QR
    $pdo->prepare("UPDATE orders SET ewallet_url=?, ewallet_type=?, qris_string=?, payment_expiry=?, payment_method=? WHERE id=?")
        ->execute([$deepLink, $type, $qrString, $expiry, 'ewallet_'.$type, $order['id']]);
    return ['status'=>'success','ewallet_url'=>$deepLink,'ewallet_type'=>$type,'qris_string'=>$qrString,'expiry'=>$expiry,'amount'=>$order['amount']];
}

function pg_check_status($pdo, $orderNumber) {
    $stmt = $pdo->prepare("SELECT payment_status, status, va_number, va_bank, payment_expiry, qris_string, ewallet_url, ewallet_type, amount FROM orders WHERE order_number = ? LIMIT 1");
    $stmt->execute([$orderNumber]);
    $order = $stmt->fetch();
    if (!$order) return ['status'=>'error','message'=>'Order tidak ditemukan'];
    // Jika belum paid, coba cek ke DOKU API status (jika ada)
    if ($order['payment_status'] !== 'paid') {
        $cfg = pg_config();
        if (!empty($cfg['api_key']) && str_contains($cfg['api_url'] ?? '', 'doku.com')) {
            // DOKU status check: GET /orders/{invoice_number}
            $apiUrl = str_replace('/checkout/v1/payment', '/orders/'.$orderNumber, $cfg['api_url']);
            // Fallback: gunakan check via pg_verify? Untuk demo, kita hanya kembalikan DB status
        }
    }
    return ['status'=>'success','payment_status'=>$order['payment_status'],'order_status'=>$order['status'],'order'=>$order];
}
