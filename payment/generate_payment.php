<?php
// Pastikan output HANYA JSON murni — matikan display_errors agar warning tidak jadi HTML
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
error_reporting(E_ALL);
ob_start();
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/payment_gateway.php';
require_once __DIR__ . '/../includes/security.php';
mcm_cors_headers();
$rateKey = 'gen_va_' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
$rl = mcm_rate_limit($rateKey, 20, 60);
if (!$rl['allowed']) { ob_clean(); echo json_encode(['status'=>'error','message'=>$rl['message']]); exit; }
mcm_rate_limit_hit($rateKey, 60);

$orderNumber = trim($_POST['order'] ?? $_GET['order'] ?? '');
$method = strtolower(trim($_POST['method'] ?? $_GET['method'] ?? 'va'));
$bank = strtolower(trim($_POST['bank'] ?? $_GET['bank'] ?? 'danamon'));
$ewallet = strtolower(trim($_POST['ewallet'] ?? $_GET['ewallet'] ?? 'dana'));

if ($orderNumber === '') {
    ob_clean(); echo json_encode(['status'=>'error','message'=>'Order tidak ditemukan']); exit;
}

pg_ensure_payment_columns($pdo);

// Validasi order milik user (IDOR protection)
$stmt = $pdo->prepare("SELECT * FROM orders WHERE order_number = ? LIMIT 1");
$stmt->execute([$orderNumber]);
$order = $stmt->fetch();
if (!$order) {
    ob_clean(); echo json_encode(['status'=>'error','message'=>'Order tidak ditemukan']); exit;
}
// IDOR: jika sudah login, pastikan order miliknya (kecuali admin)
if (!empty($_SESSION['user_logged_in']) && !empty($_SESSION['user_id'])) {
    if (!empty($order['user_id']) && (int)$order['user_id'] !== (int)$_SESSION['user_id']) {
        ob_clean(); echo json_encode(['status'=>'error','message'=>'Akses ditolak: bukan pesanan Anda']); exit;
    }
}

if ($order['payment_status'] === 'paid') {
    ob_clean(); echo json_encode(['status'=>'success','message'=>'Sudah lunas','payment_status'=>'paid']); exit;
}

if ($method === 'va') {
    $res = pg_generate_va($pdo, $orderNumber, $bank);
} elseif ($method === 'qris') {
    $res = pg_generate_qris($pdo, $orderNumber);
} elseif ($method === 'ewallet') {
    $res = pg_generate_ewallet($pdo, $orderNumber, $ewallet);
} elseif ($method === 'cc') {
    // Kartu kredit tetap pakai hosted DOKU
    $cfg = pg_config();
    // fallback ke pg_create_transaction hosted
    $res = pg_create_transaction($pdo, $order, ['id'=>$order['class_id'],'name'=>'Pembayaran MCM']);
    if ($res['status']==='success') {
        ob_clean(); echo json_encode(['status'=>'success','payment_url'=>$res['payment_url'],'method'=>'cc']);
        exit;
    }
    ob_clean(); echo json_encode($res); exit;
} else {
    ob_clean(); echo json_encode(['status'=>'error','message'=>'Metode tidak dikenal']); exit;
}

ob_clean(); echo json_encode($res);
