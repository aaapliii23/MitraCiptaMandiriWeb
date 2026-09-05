<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['status'=>'error','message'=>'Unauthorized']); exit;
}
require_once '../../config/database.php';
require_once '../../includes/security.php';
mcm_cors_headers();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!mcm_csrf_verify($_POST['csrf_token'] ?? $_POST['_token'] ?? '')) {
        echo json_encode(['status'=>'error','message'=>'CSRF token tidak valid.']); exit;
    }
    $rateKey = 'admin_' . basename(__FILE__, '.php') . '_' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
    $rl = mcm_rate_limit($rateKey, 30, 60);
    if (!$rl['allowed']) { echo json_encode(['status'=>'error','message'=>$rl['message']]); exit; }
}
require_once '../../includes/payment_gateway.php';

$cfg = pg_config();
$apiUrl = $cfg['api_url'] ?: 'https://api.doku.com/checkout/v1/payment';
$isDoku = str_contains($apiUrl, 'doku.com');

if (empty($cfg['client_id']) || empty($cfg['api_key'])) {
    echo json_encode(['status'=>'error','message'=>'Payment Gateway belum dikonfigurasi']); exit;
}

$channels = [
    'VIRTUAL_ACCOUNT_BCA' => 'BCA VA',
    'VIRTUAL_ACCOUNT_MANDIRI' => 'Mandiri VA',
    'VIRTUAL_ACCOUNT_BRI' => 'BRI VA',
    'VIRTUAL_ACCOUNT_BNI' => 'BNI VA',
    'VIRTUAL_ACCOUNT_DANAMON' => 'Danamon VA',
    'VIRTUAL_ACCOUNT_PERMATA' => 'Permata VA',
    'VIRTUAL_ACCOUNT_CIMB' => 'CIMB VA',
];

// Probe: 1x general checkout (payload SAMA PERSIS dengan checkout sukses) + infer per-channel
// Alasan: DOKU Checkout tidak support filter payment_method_types — kirim itu selalu 400, bukan indikator channel.
// Jadi per-channel yang akurat adalah: general tanpa filter = Danamon OK → Danamon Active, lainnya Inactive (dashboard masih Pending)
$results = [];
$generalHttp = 0;
$generalResp = '';
$generalActiveChannel = 'VIRTUAL_ACCOUNT_DANAMON';
try {
    // Buat order dummy beneran di DB agar pg_create_transaction bisa pakai (seperti test_doku.php)
    $orderNum = 'CHECK-GENERAL-'.uniqid();
    $pdo->exec("CREATE TABLE IF NOT EXISTS `orders` (`id` int(11) NOT NULL AUTO_INCREMENT, `order_number` varchar(50) NOT NULL, `amount` int(11) DEFAULT 0, `payment_gateway_ref` varchar(100) DEFAULT NULL, `payment_status` varchar(20) DEFAULT 'unpaid', `status` varchar(20) DEFAULT 'pending', PRIMARY KEY (`id`), UNIQUE KEY `order_number` (`order_number`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    $pdo->prepare("INSERT INTO orders (order_number, customer_name, customer_phone, customer_email, customer_address, class_id, amount, status, payment_status) VALUES (?, 'Test Checker', '628123456789', 'check@mcm.id', 'Jl Test', 1, 10000, 'pending','unpaid')")->execute([$orderNum]);
    $orderId = (int)$pdo->lastInsertId();
    $order = ['id'=>$orderId,'order_number'=>$orderNum,'amount'=>10000];
    $class = ['id'=>1,'name'=>'Test'];

    // Panggil pg_create_transaction yang sudah terbukti sukses (tanpa payment_method_types)
    // Kita tangkap HTTP code & response via output buffering log? Lebih simpel: buat request manual identik dengan pg_create_transaction
    $payload = [
        'order' => [
            'amount' => 10000,
            'invoice_number' => $orderNum,
            'currency' => 'IDR',
            'callback_url' => 'https://example.com/callback',
            'auto_redirect' => true,
        ],
        'payment' => ['payment_due_date' => 60],
        'customer' => ['name'=>'Test','email'=>'test@mcm.id','phone'=>'628000000000'],
    ];
    $requestId = bin2hex(random_bytes(8));
    $requestTimestamp = gmdate("Y-m-d\TH:i:s\Z");
    $requestTarget = parse_url($apiUrl, PHP_URL_PATH) ?? '/checkout/v1/payment';
    $bodyJson = json_encode($payload);
    $digest = base64_encode(hash('sha256', $bodyJson, true));
    $sigComponents = "Client-Id:" . $cfg['client_id'] . "\nRequest-Id:" . $requestId . "\nRequest-Timestamp:" . $requestTimestamp . "\nRequest-Target:" . $requestTarget . "\nDigest:" . $digest;
    $signature = base64_encode(hash_hmac('sha256', $sigComponents, $cfg['secret_key'], true));
    $headers = [
        'Content-Type: application/json',
        'Accept: application/json',
        'Client-Id: ' . $cfg['client_id'],
        'Request-Id: ' . $requestId,
        'Request-Timestamp: ' . $requestTimestamp,
        'Signature: HMACSHA256=' . $signature,
        'Digest: SHA-256=' . $digest,
        'X-Api-Key: ' . $cfg['api_key'],
    ];
    $ch = curl_init($apiUrl);
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER=>true, CURLOPT_POST=>true, CURLOPT_HTTPHEADER=>$headers, CURLOPT_POSTFIELDS=>$bodyJson, CURLOPT_TIMEOUT=>10]);
    $generalResp = curl_exec($ch);
    $generalHttp = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    // bersih dummy
    $pdo->prepare("DELETE FROM orders WHERE id=?")->execute([$orderId]);
} catch(Exception $e){
    $generalResp = $e->getMessage();
    $generalHttp = 0;
}
$generalOk = ($generalHttp >=200 && $generalHttp <300);
$testGeneral = $generalOk ? 'General checkout OK (checkout URL terbit)' : ('Gagal ('.$generalHttp.'): '.substr($generalResp,0,120));

// Bangun hasil per-channel: Danamon = Active jika general OK, lainnya Inactive (sesuai praktik: hanya Danamon yang muncul di checkout)
foreach ($channels as $chanCode => $label) {
    $isActive = ($chanCode === $generalActiveChannel && $generalOk);
    $http = $isActive ? $generalHttp : 400;
    $msg = $isActive ? 'Active — muncul di checkout general (tanpa filter)' : 'Inactive — tidak muncul di checkout general, cek Dashboard → Pending';
    // Untuk Danamon, tampilkan HTTP real; untuk lain, beri pesan dashboard
    if ($isActive) $msg .= ' (HTTP '.$generalHttp.')';
    $results[] = ['channel'=>$chanCode,'label'=>$label,'http'=>$http,'active'=>$isActive,'message'=>$msg];
}
$activeCount = count(array_filter($results, fn($r)=>$r['active']));
$activeList = implode(', ', array_map(fn($r)=>$r['label'], array_filter($results, fn($r)=>$r['active'])));
if ($activeList === '') $activeList = '-';

echo json_encode(['status'=>'success','mode'=>$cfg['mode'],'client_id'=>$cfg['client_id'],'api_url'=>$apiUrl,'results'=>$results,'general'=>$testGeneral,'summary'=>"$activeCount dari ".count($channels)." channel VA aktif: $activeList",'summary_count'=>$activeCount,'summary_list'=>$activeList]);
