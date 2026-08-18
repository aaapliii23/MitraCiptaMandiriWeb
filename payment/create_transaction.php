<?php
session_start();
header('Content-Type: application/json');
require_once '../includes/db_config.php';
require_once '../includes/payment_gateway.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}

if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
    echo json_encode(['status' => 'error', 'message' => 'Sesi tidak valid. Silakan muat ulang halaman.']);
    exit;
}

$classId = (int)($_POST['class_id'] ?? 0);
$examinerId = (int)($_POST['examiner_id'] ?? 0);
$customerName = trim($_POST['customer_name'] ?? '');
$customerPhone = trim($_POST['customer_phone'] ?? '');
$customerEmail = trim($_POST['customer_email'] ?? '');
$customerInstitution = trim($_POST['customer_institution'] ?? '');
$customerAddress = trim($_POST['customer_address'] ?? '');

if (!$classId || $customerName === '' || $customerPhone === '' || $customerEmail === '' || $customerAddress === '') {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap.']);
    exit;
}

if ($examinerId > 0) {
    $stmt = $pdo->prepare("SELECT id FROM examiners WHERE id = ?");
    $stmt->execute([$examinerId]);
    if (!$stmt->fetch()) {
        $examinerId = 0;
    }
}

if (!filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Format email tidak valid.']);
    exit;
}

$digits = preg_replace('/\D+/', '', $customerPhone);
if (strpos($digits, '0') === 0) $digits = '62' . substr($digits, 1);
if (!preg_match('/^62[0-9]{9,13}$/', $digits)) {
    echo json_encode(['status' => 'error', 'message' => 'Nomor WhatsApp tidak valid (gunakan format +62...).']);
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM classes WHERE id = ?");
$stmt->execute([$classId]);
$class = $stmt->fetch();
if (!$class) {
    echo json_encode(['status' => 'error', 'message' => 'Program tidak ditemukan.']);
    exit;
}

$amount = (int)$class['price'];
$orderNumber = 'ORD-' . strtoupper(uniqid()) . '-' . time();
$userId = isset($_SESSION['user_logged_in']) ? (int)($_SESSION['user_id'] ?? 0) : null;
if ($userId < 1) $userId = null;

$stmt = $pdo->prepare("INSERT INTO orders (order_number, user_id, customer_name, customer_phone, customer_email, customer_address, customer_institution, class_id, examiner_id, amount, status, payment_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', 'unpaid')");
$stmt->execute([$orderNumber, $userId, $customerName, $digits, $customerEmail, $customerAddress, $customerInstitution ?: '-', $classId, $examinerId ?: null, $amount]);
$orderId = (int)$pdo->lastInsertId();

$res = pg_create_transaction($pdo, ['id' => $orderId, 'order_number' => $orderNumber, 'amount' => $amount], $class);
if ($res['status'] !== 'success') {
    echo json_encode(['status' => 'error', 'message' => 'Gagal membuat transaksi pembayaran.']);
    exit;
}

echo json_encode(['status' => 'success', 'payment_url' => $res['payment_url'], 'order_number' => $orderNumber]);