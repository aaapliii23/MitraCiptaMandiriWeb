<?php
session_start();
header('Content-Type: application/json');
require_once '../config/database.php';
require_once '../includes/payment_gateway.php';
require_once '../includes/whatsapp_client.php';

function validateFullName($name)
{
    if (strlen($name) < 3 || strlen($name) > 100) return false;
    $words = preg_split('/\s+/', trim($name));
    if (count($words) < 2) return false;
    return preg_match('/^[\p{L}]+(?:[ -][\p{L}]+)*$/u', $name) === 1;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}

if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
    echo json_encode(['status' => 'error', 'message' => 'Sesi tidak valid. Silakan muat ulang halaman.']);
    exit;
}

$classId = (int)($_POST['class_id'] ?? 0);
$classMode = strtolower(trim($_POST['class_mode'] ?? 'offline'));
if (!in_array($classMode, ['online','offline'], true)) $classMode = 'offline';
$instructorId = (int)($_POST['instructor_id'] ?? 0);
$userId = isset($_SESSION['user_logged_in']) ? (int)($_SESSION['user_id'] ?? 0) : null;
if ($userId < 1) $userId = null;

if ($userId) {
    $stmt = $pdo->prepare("SELECT name, email, phone, password FROM users WHERE id = ? LIMIT 1");
    $stmt->execute([$userId]);
    $dbUser = $stmt->fetch();
    $customerName    = $dbUser['name']    ?? trim($_POST['customer_name']    ?? '');
    $customerEmail   = $dbUser['email']   ?? trim($_POST['customer_email']   ?? '');
    $customerPhone   = $dbUser['phone']   ?? trim($_POST['customer_phone']   ?? '');
    $customerAddress = trim($_POST['customer_address'] ?? '-');
    if ($customerAddress === '') $customerAddress = '-';
    $customerInstitution = trim($_POST['customer_institution'] ?? '');
} else {
    $customerName        = trim($_POST['customer_name']        ?? '');
    $customerPhone       = trim($_POST['customer_phone']       ?? '');
    $customerEmail       = trim($_POST['customer_email']       ?? '');
    $customerInstitution = trim($_POST['customer_institution'] ?? '');
    $customerAddress     = trim($_POST['customer_address']     ?? '');
}

if (!$classId || $customerName === '' || $customerEmail === '') {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap.']);
    exit;
}

if (!$userId && ($customerPhone === '' || $customerAddress === '')) {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap.']);
    exit;
}

if ($instructorId > 0) {
    $stmt = $pdo->prepare("SELECT id FROM instructors WHERE id = ?");
    $stmt->execute([$instructorId]);
    if (!$stmt->fetch()) {
        $instructorId = 0;
    }
}

if (!filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Format email tidak valid.']);
    exit;
}

$digits = preg_replace('/\D+/', '', $customerPhone);
if (strpos($digits, '0') === 0) $digits = '62' . substr($digits, 1);
if (!empty($customerPhone) && !preg_match('/^62[0-9]{9,13}$/', $digits)) {
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

// Validasi mode_available dan hitung amount server-side (jangan percaya client)
$modeAvailable = $class['mode_available'] ?? 'both';
if (!in_array($modeAvailable, ['online','offline','both'], true)) $modeAvailable = 'both';
if ($modeAvailable === 'online' && $classMode !== 'online') $classMode = 'online';
if ($modeAvailable === 'offline' && $classMode !== 'offline') $classMode = 'offline';
$priceLegacy = (int)($class['price'] ?? 0);
$priceOnline = isset($class['price_online']) && (int)$class['price_online'] > 0 ? (int)$class['price_online'] : (int)round($priceLegacy * 0.8);
$priceOffline = isset($class['price_offline']) && (int)$class['price_offline'] > 0 ? (int)$class['price_offline'] : $priceLegacy;
$amount = $classMode === 'online' ? $priceOnline : $priceOffline;
if ($amount <= 0) $amount = $priceLegacy;
$orderNumber = 'ORD-' . strtoupper(uniqid()) . '-' . time();

if (!$userId) {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$customerEmail]);
    $existingUserId = (int)$stmt->fetchColumn();
    if ($existingUserId) {
        $userId = $existingUserId;
    } else {
        $password = $_POST['customer_password'] ?? '';
        $password2 = $_POST['customer_password2'] ?? '';
        if (!validateFullName($customerName)) {
            echo json_encode(['status' => 'error', 'message' => 'Nama harus Nama Asli: minimal 2 kata, hanya huruf, spasi, dan tanda hubung.']);
            exit;
        }
        if (strlen($password) < 6) {
            echo json_encode(['status' => 'error', 'message' => 'Password akun LMS minimal 6 karakter.']);
            exit;
        }
        if ($password !== $password2) {
            echo json_encode(['status' => 'error', 'message' => 'Konfirmasi password tidak cocok.']);
            exit;
        }
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)");
        $stmt->execute([$customerName, $customerEmail, $digits, $hash]);
        $userId = (int)$pdo->lastInsertId();
        session_regenerate_id(true);
        $_SESSION['user_logged_in'] = true;
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_name'] = $customerName;
        $_SESSION['user_email'] = $customerEmail;
    }
}

// Cek apakah kolom class_mode ada (untuk DB yang belum migrasi, fallback)
$hasClassMode = true;
try {
    $pdo->query("SELECT class_mode FROM orders LIMIT 1");
} catch (PDOException $e) { $hasClassMode = false; }
if ($hasClassMode) {
    $stmt = $pdo->prepare("INSERT INTO orders (order_number, user_id, customer_name, customer_phone, customer_email, customer_address, customer_institution, class_id, class_mode, instructor_id, amount, status, payment_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', 'unpaid')");
    $stmt->execute([$orderNumber, $userId, $customerName, $digits, $customerEmail, $customerAddress, $customerInstitution ?: '-', $classId, $classMode, $instructorId ?: null, $amount]);
} else {
    $stmt = $pdo->prepare("INSERT INTO orders (order_number, user_id, customer_name, customer_phone, customer_email, customer_address, customer_institution, class_id, instructor_id, amount, status, payment_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', 'unpaid')");
    $stmt->execute([$orderNumber, $userId, $customerName, $digits, $customerEmail, $customerAddress, $customerInstitution ?: '-', $classId, $instructorId ?: null, $amount]);
}
$orderId = (int)$pdo->lastInsertId();

// Notifikasi admin pesanan baru (Fonnte) — gagal kirim tidak boleh gagalkan transaksi
try {
    $adminWa = defined('MCM_WA_ADMIN') ? MCM_WA_ADMIN : (defined('FONNTE_TOKEN') ? '' : '');
    if ($adminWa !== '') {
        $adminMsg = "*PESANAN BARU - MCM*\n\n";
        $adminMsg .= "Order: $orderNumber\n";
        $adminMsg .= "Peserta: $customerName ($digits)\n";
        $adminMsg .= "Kelas: {$class['name']} (" . ($classMode === 'online' ? 'Online' : 'Offline') . ")\n";
        $adminMsg .= "Nominal: Rp " . number_format($amount, 0, ',', '.') . "\n";
        $adminMsg .= "Status: Menunggu pembayaran";
        wa_send_message($pdo, $adminWa, $adminMsg, 'admin_notif');
    }
} catch (Throwable $e) { error_log("[Fonnte admin notif] " . $e->getMessage()); }

$res = pg_create_transaction($pdo, ['id' => $orderId, 'order_number' => $orderNumber, 'amount' => $amount], $class);
if ($res['status'] !== 'success') {
    echo json_encode(['status' => 'error', 'message' => 'Gagal membuat transaksi pembayaran.']);
    exit;
}

echo json_encode(['status' => 'success', 'payment_url' => $res['payment_url'], 'order_number' => $orderNumber]);