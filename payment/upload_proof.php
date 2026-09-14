<?php
// Upload bukti Transfer Bank Manual — output HANYA JSON murni
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
error_reporting(E_ALL);
ob_start();
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/payment_gateway.php';
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../includes/cloudinary.php';
mcm_cors_headers();

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    ob_clean(); echo json_encode(['status' => 'error', 'message' => 'Invalid request']); exit;
}
if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    ob_clean(); echo json_encode(['status' => 'error', 'message' => 'Sesi tidak valid. Silakan muat ulang halaman.']); exit;
}

$orderNumber = trim($_POST['order'] ?? '');
if ($orderNumber === '') {
    ob_clean(); echo json_encode(['status' => 'error', 'message' => 'Order tidak ditemukan']); exit;
}

pg_ensure_payment_columns($pdo);
$stmt = $pdo->prepare("SELECT * FROM orders WHERE order_number = ? LIMIT 1");
$stmt->execute([$orderNumber]);
$order = $stmt->fetch();
if (!$order) {
    ob_clean(); echo json_encode(['status' => 'error', 'message' => 'Order tidak ditemukan']); exit;
}
// IDOR: jika sudah login, pastikan order miliknya
if (!empty($_SESSION['user_logged_in']) && !empty($_SESSION['user_id'])) {
    if (!empty($order['user_id']) && (int)$order['user_id'] !== (int)$_SESSION['user_id']) {
        ob_clean(); echo json_encode(['status' => 'error', 'message' => 'Akses ditolak: bukan pesanan Anda']); exit;
    }
}
if ($order['payment_status'] === 'paid') {
    ob_clean(); echo json_encode(['status' => 'error', 'message' => 'Pesanan ini sudah lunas']); exit;
}

$file = $_FILES['bukti'] ?? null;
if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
    $msg = ($file['error'] ?? 0) === UPLOAD_ERR_INI_SIZE || ($file['error'] ?? 0) === UPLOAD_ERR_FORM_SIZE
        ? 'File terlalu besar (maksimal 2MB).' : 'Pilih file bukti transfer dulu (JPG, PNG, atau PDF).';
    ob_clean(); echo json_encode(['status' => 'error', 'message' => $msg]); exit;
}
if ((int)$file['size'] > 2 * 1024 * 1024) {
    ob_clean(); echo json_encode(['status' => 'error', 'message' => 'File terlalu besar, maksimal 2MB.']); exit;
}
$ext = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));
if (!in_array($ext, ['jpg', 'jpeg', 'png', 'pdf'], true)) {
    ob_clean(); echo json_encode(['status' => 'error', 'message' => 'Format tidak didukung. Gunakan JPG, PNG, atau PDF.']); exit;
}
$mime = @mime_content_type($file['tmp_name']);
$mimeOk = in_array($mime, ['image/jpeg', 'image/png', 'application/pdf', 'image/jpg'], true)
    || ($ext === 'pdf' && $mime === 'application/octet-stream');
if (!$mimeOk) {
    ob_clean(); echo json_encode(['status' => 'error', 'message' => 'File tidak valid (harus gambar JPG/PNG atau PDF).']); exit;
}

// Upload ke Cloudinary seperti fitur gambar lain (mcm/bukti_transfer)
$res = uploadImageToCloudinary($file, 'mcm/bukti_transfer');
if (!$res['ok']) {
    ob_clean(); echo json_encode(['status' => 'error', 'message' => $res['error']]); exit;
}
$relPath = $res['url'];
// Hapus bukti lama jika upload ulang (dukung URL Cloudinary + file lokal lama)
if (!empty($order['transfer_proof'])) {
    $old = $order['transfer_proof'];
    if (str_contains($old, 'res.cloudinary.com')) {
        $delRes = deleteImageFromCloudinary($old);
        if (!$delRes['ok']) error_log('[upload_proof delete] ' . $delRes['error']);
    } else {
        $oldPath = __DIR__ . '/../' . ltrim($old, '/');
        if (is_file($oldPath)) @unlink($oldPath);
    }
}
try {
    $pdo->prepare("UPDATE orders SET transfer_proof = ?, proof_uploaded_at = NOW(), payment_method = 'manual_transfer', payment_status = 'pending' WHERE id = ?")
        ->execute([$relPath, $order['id']]);
} catch (PDOException $e) {
    $delRes = deleteImageFromCloudinary($relPath);
    if (!$delRes['ok']) error_log('[upload_proof rollback] ' . $delRes['error']);
    error_log('[upload_proof] db: ' . $e->getMessage());
    ob_clean(); echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan data. Coba lagi.']); exit;
}

ob_clean();
echo json_encode(['status' => 'success', 'message' => 'Bukti pembayaran berhasil dikirim, mohon tunggu konfirmasi dari admin (maks 1x24 jam).', 'proof' => $relPath]);
