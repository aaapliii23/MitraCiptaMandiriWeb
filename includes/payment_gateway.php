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
    if (!in_array('transfer_proof', $cols)) $adds[] = "ADD COLUMN transfer_proof varchar(255) DEFAULT NULL AFTER ewallet_type";
    if (!in_array('proof_uploaded_at', $cols)) $adds[] = "ADD COLUMN proof_uploaded_at datetime DEFAULT NULL AFTER transfer_proof";
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
    // ponytail: Jokul Direct hanya untuk bank ini (terbukti 200); BCA/Mandiri return invalid_client_id (butuh aktivasi/SNAP) → dummy
    $jokulBanks = ['danamon','cimb','bni','bri','permata'];
    $useRealDokuVA = ($isDoku && in_array($bank, $jokulBanks, true) && !empty($cfg['api_key']) && !empty($cfg['secret_key']));
    error_log("[VA] pg_generate_va order=$orderNumber bank=$bank isDoku=".($isDoku?'1':'0')." useReal=".($useRealDokuVA?'1':'0')." existing=".($order['va_number']??'-')." expiry=".($order['payment_expiry']??'-'));
    // Jika sudah ada VA untuk bank sama dan belum expired, pakai lagi — tapi JANGAN pakai dummy 88080-88086 jika real tersedia
    if (!empty($order['va_number']) && $order['va_bank'] === $bank && !empty($order['payment_expiry']) && strtotime($order['payment_expiry']) > time()) {
        $isDummy = (bool)preg_match('/^8808[0-6]/', (string)$order['va_number']);
        if ($isDummy && $useRealDokuVA) {
            error_log("[VA] existing dummy ".$order['va_number']." akan diganti real untuk $orderNumber");
        } else {
            error_log("[VA] reuse existing ".$order['va_number']);
            return ['status'=>'success','va_number'=>$order['va_number'],'va_bank'=>$bank,'expiry'=>$order['payment_expiry'],'amount'=>$order['amount']];
        }
    }
    if ($useRealDokuVA) {
        // Real DOKU VA (Jokul Direct) — payload beda per bank, terbukti via probe live 2026-09-14
        $mode = $cfg['mode'];
        $vaEndpoints = [
            'bri'=>'bri-virtual-account/v2/payment-code',
            'bni'=>'bni-virtual-account/v2/payment-code',
            'danamon'=>'danamon-virtual-account/v2/payment-code',
            'permata'=>'permata-virtual-account/v2/payment-code',
            'cimb'=>'cimb-virtual-account/v2/payment-code',
        ];
        $vaPath = $vaEndpoints[$bank] ?? 'danamon-virtual-account/v2/payment-code';
        $apiUrlVA = ($mode === 'production' ? 'https://api.doku.com/' : 'https://api-sandbox.doku.com/') . $vaPath;

        $custName = substr($order['customer_name'] ?? 'MCM Customer', 0, 20);
        $vaInfo = [];
        if ($bank === 'bni') {
            // BNI wajib merchant_unique_reference (alfanumerik, unik per request, maks 13)
            $mur = strtoupper(substr(md5($order['order_number'] . microtime(true)), 0, 12));
            $vaInfo = ['expired_time'=>1440,'billing_type'=>'FIXED','biling_type'=>'FIXED','info'=>substr($custName,0,32),'merchant_unique_reference'=>$mur];
        } elseif ($bank === 'permata') {
            // Permata wajib ref_info (bukan info1/2/3)
            $vaInfo = ['expired_time'=>1440,'reusable_status'=>false,'ref_info'=>[['ref_name'=>'customer','ref_value'=>substr($custName,0,30)]]];
        } elseif ($bank === 'bri') {
            // BRI butuh billing_type, kalau tidak: 400 Invalid JSON Format
            $vaInfo = ['billing_type'=>'FIX_BILL','expired_time'=>1440,'reusable_status'=>false,'info1'=>$custName,'info2'=>'MCM '.($order['class_id'] ?? ''),'info3'=>'VA '.ucfirst($bank)];
        } else {
            // Danamon & CIMB: info1/2/3
            $vaInfo = [
                'expired_time' => 1440, // 24 jam dalam menit
                'reusable_status' => false,
                'info1' => $custName,
                'info2' => 'MCM ' . ($order['class_id'] ?? ''),
                'info3' => 'VA ' . ucfirst($bank),
            ];
        }
        // ponytail: BRI & Permata strict — tolak order.currency & customer.phone (400 Invalid JSON Format)
        $strictBank = in_array($bank, ['bri','permata'], true);
        $payloadVA = [
            'order' => $strictBank ? [
                'amount' => (int)$order['amount'],
                'invoice_number' => $order['order_number'],
            ] : [
                'amount' => (int)$order['amount'],
                'invoice_number' => $order['order_number'],
                'currency' => 'IDR',
            ],
            'virtual_account_info' => $vaInfo,
            'customer' => $strictBank ? [
                'name' => $order['customer_name'] ?? 'Customer MCM',
                'email' => $order['customer_email'] ?? 'customer@mcm.id',
            ] : [
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
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE order_number = ? LIMIT 1");
    $stmt->execute([$orderNumber]);
    $order = $stmt->fetch();
    if (!$order) return ['status'=>'error','message'=>'Order tidak ditemukan'];
    if ($order['payment_status'] === 'paid') {
        return ['status'=>'success','payment_status'=>'paid','order_status'=>$order['status'],'order'=>$order];
    }
    // Transfer manual: JANGAN pernah auto-lunas — wajib konfirmasi admin
    $method = $order['payment_method'] ?? '';
    if ($method === 'manual_transfer' || !empty($order['transfer_proof'])) {
        return ['status'=>'success','payment_status'=>$order['payment_status'],'order_status'=>$order['status'],'order'=>$order];
    }
    // Fallback: tanya langsung ke DOKU jika ada jejak transaksi DOKU (VA real / hosted checkout)
    if (!empty($order['va_number']) || !empty($order['payment_gateway_ref'])) {
        $api = pg_doku_check_status($orderNumber);
        if ($api['ok']) {
            $txn = strtoupper((string)($api['data']['transaction']['status'] ?? ''));
            if ($txn === 'SUCCESS') {
                $channel = (string)($api['data']['channel']['id'] ?? '');
                $vaNum = $api['data']['virtual_account_info']['virtual_account_number'] ?? ($order['va_number'] ?? null);
                pg_apply_doku_paid($pdo, $order, pg_channel_to_method($channel, $method ?: null), $vaNum ?: null);
                $stmt->execute([$orderNumber]);
                $order = $stmt->fetch();
                return ['status'=>'success','payment_status'=>'paid','order_status'=>$order['status'],'order'=>$order,'source'=>'doku'];
            }
            if ($txn === 'EXPIRED' || $txn === 'FAILED') {
                $new = strtolower($txn);
                $pdo->prepare("UPDATE orders SET payment_status = ? WHERE id = ? AND payment_status != 'paid'")->execute([$new, $order['id']]);
                $stmt->execute([$orderNumber]);
                $order = $stmt->fetch();
                return ['status'=>'success','payment_status'=>$order['payment_status'],'order_status'=>$order['status'],'order'=>$order,'source'=>'doku'];
            }
        } else {
            error_log("[Payment] check-status DOKU gagal ($orderNumber): " . ($api['error'] ?? 'unknown'));
        }
    }
    return ['status'=>'success','payment_status'=>$order['payment_status'],'order_status'=>$order['status'],'order'=>$order];
}

// --- DOKU non-SNAP: verifikasi HTTP Notification, Check Status API, efek paid bersama ---

// Verifikasi signature notifikasi DOKU (Jokul Direct): HMAC-SHA256 atas
// Client-Id/Request-Id/Request-Timestamp/Request-Target/Digest dengan Secret Key.
// $headers: array header request (case-insensitive). $targetPath: path Notification URL
// terdaftar, mis. /payment/payment_webhook.php (diturunkan dari REQUEST_URI).
function pg_verify_doku_notify($rawBody, array $headers, $targetPath) {
    $cfg = pg_config();
    $secret = $cfg['secret_key'] ?? '';
    if ($secret === '' || !is_string($rawBody) || $rawBody === '') return false;
    $h = array_change_key_case($headers, CASE_LOWER);
    $clientId = trim((string)($h['client-id'] ?? ''));
    $reqId = trim((string)($h['request-id'] ?? ''));
    $ts = trim((string)($h['request-timestamp'] ?? ''));
    $sig = trim((string)($h['signature'] ?? ''));
    if ($clientId === '' || $reqId === '' || $ts === '' || $sig === '') return false;
    if (!empty($cfg['client_id']) && !hash_equals((string)$cfg['client_id'], $clientId)) return false;
    $sig = preg_replace('/^HMACSHA256=/i', '', $sig);
    $digest = base64_encode(hash('sha256', $rawBody, true));
    $raw = "Client-Id:$clientId\nRequest-Id:$reqId\nRequest-Timestamp:$ts\nRequest-Target:$targetPath\nDigest:$digest";
    $calc = base64_encode(hash_hmac('sha256', $raw, $secret, true));
    return hash_equals($calc, $sig);
}

// Check Status API non-SNAP: GET /orders/v1/status/{invoice} (tanpa Digest karena GET).
// Response envelope sama dengan notifikasi: order/transaction.status/service/acquirer/channel.
function pg_doku_check_status($invoiceNumber) {
    $cfg = pg_config();
    if (empty($cfg['client_id']) || empty($cfg['secret_key'])) return ['ok'=>false,'error'=>'gateway not configured'];
    $target = '/orders/v1/status/' . $invoiceNumber;
    $base = ($cfg['mode'] === 'production') ? 'https://api.doku.com' : 'https://api-sandbox.doku.com';
    $reqId = bin2hex(random_bytes(8));
    $ts = gmdate("Y-m-d\TH:i:s\Z");
    $raw = "Client-Id:{$cfg['client_id']}\nRequest-Id:$reqId\nRequest-Timestamp:$ts\nRequest-Target:$target";
    $sig = 'HMACSHA256=' . base64_encode(hash_hmac('sha256', $raw, $cfg['secret_key'], true));
    $ch = curl_init($base . $target);
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER=>true, CURLOPT_HTTPGET=>true, CURLOPT_TIMEOUT=>15,
        CURLOPT_HTTPHEADER=>['Content-Type: application/json','Client-Id: '.$cfg['client_id'],'Request-Id: '.$reqId,'Request-Timestamp: '.$ts,'Request-Target: '.$target,'Signature: '.$sig]]);
    $resp = curl_exec($ch);
    $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err = curl_error($ch);
    curl_close($ch);
    if ($resp === false) return ['ok'=>false,'error'=>'curl: '.$err];
    $data = json_decode($resp, true);
    if ($code >= 200 && $code < 300 && is_array($data)) return ['ok'=>true,'http'=>$code,'data'=>$data];
    $msg = is_array($data) ? (($data['error']['message'] ?? $data['message'] ?? null) ?: substr($resp, 0, 200)) : substr((string)$resp, 0, 200);
    return ['ok'=>false,'http'=>$code,'error'=>$msg];
}

// Petakan channel.id DOKU ke payment_method internal (konvensi: va_bni, qris, ewallet_dana, credit_card).
function pg_channel_to_method($channelId, $fallback = null) {
    $ch = strtoupper(trim((string)$channelId));
    if ($ch === '') return $fallback;
    if (str_starts_with($ch, 'VIRTUAL_ACCOUNT_')) {
        $bank = strtolower(preg_replace('/^VIRTUAL_ACCOUNT_(BANK_)?/', '', $ch));
        return 'va_' . $bank;
    }
    $map = ['QRIS'=>'qris','EMONEY_OVO_SNAP'=>'ewallet_ovo','EMONEY_DANA_SNAP'=>'ewallet_dana','EMONEY_SHOPEEPAY_SNAP'=>'ewallet_shopeepay','CREDIT_CARD'=>'credit_card'];
    if (isset($map[$ch])) return $map[$ch];
    return $fallback ?: strtolower($ch);
}

// Efek paid bersama (dipakai webhook DOKU + fallback check-status): samakan dengan approve admin —
// orders paid+confirmed, enrollment LMS, rekap finance, notifikasi WA (WA hanya jika whatsapp_client tersedia).
function pg_apply_doku_paid($pdo, $order, $method = null, $gatewayRef = null) {
    if (($order['payment_status'] ?? '') === 'paid') return true;
    $pdo->beginTransaction();
    try {
        $pdo->prepare("UPDATE orders SET payment_status = 'paid', payment_method = COALESCE(?, payment_method), payment_gateway_ref = COALESCE(payment_gateway_ref, ?), paid_at = COALESCE(paid_at, NOW()), status = 'confirmed' WHERE id = ?")
            ->execute([$method, $gatewayRef, $order['id']]);
        if (!empty($order['user_id'])) {
            $orderMode = strtolower($order['class_mode'] ?? 'offline');
            if (!in_array($orderMode, ['online','offline'], true)) $orderMode = 'offline';
            try {
                $pdo->prepare("INSERT INTO enrollments (user_id, class_id, class_mode, order_id) VALUES (?, ?, ?, ?)
                               ON DUPLICATE KEY UPDATE order_id = VALUES(order_id)")
                    ->execute([$order['user_id'], $order['class_id'], $orderMode, $order['id']]);
            } catch (PDOException $eOld) {
                if ($eOld->getCode() === '42S22') {
                    $pdo->prepare("INSERT IGNORE INTO enrollments (user_id, class_id, order_id) VALUES (?, ?, ?)")
                        ->execute([$order['user_id'], $order['class_id'], $order['id']]);
                } else { throw $eOld; }
            }
        }
        try {
            $className = (string)$pdo->query("SELECT name FROM classes WHERE id = " . (int)$order['class_id'])->fetchColumn();
            if ($className === '') $className = 'Kelas MCM';
            $checkFin = $pdo->prepare("SELECT COUNT(*) FROM finance_transactions WHERE order_id = ?");
            $checkFin->execute([$order['id']]);
            if ($checkFin->fetchColumn() == 0) {
                $pdo->prepare("INSERT INTO finance_transactions (type, category, item_name, quantity, unit_price, amount, description, order_id, transaction_date)
                               VALUES ('in', 'pemasukan_kursus', ?, 1, ?, ?, ?, ?, CURDATE())")
                    ->execute(["Pendaftaran " . $className . " (" . $order['customer_name'] . ")", $order['amount'], $order['amount'], "Pemasukan pembayaran kursus no. order " . $order['order_number'], $order['id']]);
            }
        } catch (Exception $fe) {}
        $pdo->commit();
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        error_log('[pg_apply_doku_paid] ' . $e->getMessage());
        return false;
    }
    // ponytail: WA di luar transaksi — gagal kirim tidak boleh rollback paid
    try {
        if (function_exists('wa_send_message')) {
            $classMode = strtolower($order['class_mode'] ?? 'offline');
            if (!in_array($classMode, ['online','offline'], true)) $classMode = 'offline';
            $className = (string)$pdo->query("SELECT name FROM classes WHERE id = " . (int)$order['class_id'])->fetchColumn();
            if ($className === '') $className = 'Kelas MCM';
            if ($classMode === 'online') {
                wa_send_message($pdo, $order['customer_phone'], "*PEMBAYARAN LUNAS - MCM*\n\nHalo " . $order['customer_name'] . ", pembayaran Anda untuk *" . $className . "* (Online) sudah kami terima. ✅\nNo. Order: " . $order['order_number'] . "\nSilakan login ke LMS untuk mulai belajar: " . pg_base_url() . "/lms/dashboard.php", 'pembayaran');
            } else {
                $waLink = null;
                try { $waLink = $pdo->query("SELECT whatsapp_group_link FROM classes WHERE id = " . (int)$order['class_id'])->fetchColumn(); } catch (Exception $e) {}
                if (!empty($waLink) && strpos($waLink, 'https://chat.whatsapp.com/') === 0) {
                    wa_send_message($pdo, $order['customer_phone'], "*PEMBAYARAN LUNAS - MCM*\n\nHalo " . $order['customer_name'] . ", pembayaran Anda untuk *" . $className . "* (Offline) sudah kami terima. ✅\nNo. Order: " . $order['order_number'] . "\n\nSilakan gabung ke grup WhatsApp kelas untuk info jadwal & lokasi pelatihan:\n" . $waLink, 'pembayaran');
                } else {
                    wa_send_message($pdo, $order['customer_phone'], "*PEMBAYARAN LUNAS - MCM*\n\nHalo " . $order['customer_name'] . ", pembayaran Anda untuk *" . $className . "* (Offline) sudah kami terima. ✅\nNo. Order: " . $order['order_number'] . "\n\nAdmin kami akan segera menghubungi Anda untuk info grup WhatsApp kelas & jadwal pelatihan.", 'pembayaran');
                }
            }
        }
    } catch (Exception $we) {}
    return true;
}
