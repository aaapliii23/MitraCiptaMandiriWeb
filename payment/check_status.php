<?php
ini_set('display_errors','0');
ini_set('display_startup_errors','0');
error_reporting(E_ALL);
ob_start();
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/payment_gateway.php';

$orderNumber = trim($_GET['order'] ?? $_POST['order'] ?? '');
if ($orderNumber === '') {
    ob_clean(); echo json_encode(['status'=>'error','message'=>'Order tidak ditemukan']); exit;
}
$res = pg_check_status($pdo, $orderNumber);
if ($res['status'] !== 'success') {
    ob_clean(); echo json_encode($res); exit;
}
// Validasi signature tetap seperti webhook (jika ada header signature, tetap cek)
ob_clean(); echo json_encode(['status'=>'success','payment_status'=>$res['payment_status'],'order_status'=>$res['order_status'],'paid'=> $res['payment_status']==='paid']);
