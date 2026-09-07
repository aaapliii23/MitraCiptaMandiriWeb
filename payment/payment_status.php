<?php
session_start();
require_once '../config/database.php';

$orderNumber = $_GET['order'] ?? '';
$hasMode = true;
try { $pdo->query("SELECT class_mode FROM orders LIMIT 1"); } catch (Exception $e) { $hasMode = false; }
$hasWaCol = true;
try { $pdo->query("SELECT whatsapp_group_link FROM classes LIMIT 1"); } catch (Exception $e) { $hasWaCol = false; }
if ($hasMode && $hasWaCol) {
    $stmt = $pdo->prepare("SELECT o.*, c.name AS class_name, c.whatsapp_group_link, i.name AS instructor_name, i.specialization AS instructor_spec FROM orders o LEFT JOIN classes c ON o.class_id = c.id LEFT JOIN instructors i ON o.instructor_id = i.id WHERE o.order_number = ?");
} elseif ($hasMode) {
    $stmt = $pdo->prepare("SELECT o.*, c.name AS class_name, i.name AS instructor_name, i.specialization AS instructor_spec FROM orders o LEFT JOIN classes c ON o.class_id = c.id LEFT JOIN instructors i ON o.instructor_id = i.id WHERE o.order_number = ?");
} else {
    $stmt = $pdo->prepare("SELECT o.*, c.name AS class_name, i.name AS instructor_name, i.specialization AS instructor_spec FROM orders o LEFT JOIN classes c ON o.class_id = c.id LEFT JOIN instructors i ON o.instructor_id = i.id WHERE o.order_number = ?");
}
$stmt->execute([$orderNumber]);
$order = $stmt->fetch();
// Ambil link WA jika ada
$waLink = null;
if ($hasWaCol && $order) {
    // $order sudah join, tapi jika query tanpa wa_link, fetch lagi
    if (!isset($order['whatsapp_group_link'])) {
        try {
            $st2 = $pdo->prepare("SELECT whatsapp_group_link FROM classes WHERE id = ?");
            $st2->execute([$order['class_id']]);
            $waLink = $st2->fetchColumn();
        } catch (Exception $e) { $waLink = null; }
    } else {
        $waLink = $order['whatsapp_group_link'] ?? null;
    }
    // Validasi format
    if ($waLink && strpos($waLink, 'https://chat.whatsapp.com/') !== 0) $waLink = null;
    // Keamanan: hanya tampilkan jika paid dan (order milik session atau order_number dianggap token)
    // Jika ada user_id, cek kecocokan
    if ($waLink && $order['payment_status'] === 'paid') {
        $isOwner = true;
        if (!empty($order['user_id']) && isset($_SESSION['user_id']) && (int)$order['user_id'] !== (int)$_SESSION['user_id']) {
            $isOwner = false;
        }
        // Untuk guest, order_number sudah random, dianggap cukup; tetap tampilkan jika paid
        if (!$isOwner) $waLink = null;
    } else if ($order && $order['payment_status'] !== 'paid') {
        $waLink = null;
    }
}
$classMode = $hasMode && $order ? strtolower($order['class_mode'] ?? 'offline') : 'offline';
if (!in_array($classMode, ['online','offline'], true)) $classMode = 'offline';

$statusMeta = [
    'paid'    => ['icon' => 'fa-check-circle', 'color' => '#16a34a', 'title' => 'Pembayaran Berhasil', 'desc' => 'Pembayaran Anda telah kami terima. Akses LMS sudah terbuka.'],
    'pending' => ['icon' => 'fa-clock', 'color' => '#d97706', 'title' => 'Menunggu Pembayaran', 'desc' => 'Pembayaran sedang diproses. Silakan tunggu konfirmasi.'],
    'unpaid'  => ['icon' => 'fa-hourglass-half', 'color' => '#d97706', 'title' => 'Belum Dibayar', 'desc' => 'Pesanan tersimpan, namun pembayaran belum selesai.'],
    'failed'  => ['icon' => 'fa-times-circle', 'color' => '#dc2626', 'title' => 'Pembayaran Gagal', 'desc' => 'Pembayaran tidak berhasil. Silakan coba lagi.'],
    'expired' => ['icon' => 'fa-clock', 'color' => '#dc2626', 'title' => 'Pembayaran Kedaluwarsa', 'desc' => 'Waktu pembayaran habis. Silakan lakukan pembayaran ulang.'],
];
$meta = $statusMeta[$order['payment_status'] ?? 'unpaid'] ?? $statusMeta['unpaid'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Pembayaran - MCM</title>
    <link rel="icon" type="image/png" href="../assets/img/logo.png">
    <link rel="shortcut icon" type="image/png" href="../assets/img/logo.png">
    <link rel="apple-touch-icon" href="../assets/img/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light" style="min-height: 100vh;">
    <div class="container py-5" style="max-width: 560px;">
        <div class="card border-0 shadow rounded-4 overflow-hidden">
            <div class="text-center p-5">
                <?php if ($order): ?>
                    <div class="mx-auto mb-4 d-flex align-items-center justify-content-center rounded-circle bg-light" style="width: 96px; height: 96px;">
                        <i class="fas <?php echo $meta['icon']; ?>" style="font-size: 3rem; color: <?php echo $meta['color']; ?>;"></i>
                    </div>
                    <h4 class="fw-bold mb-1" style="color: <?php echo $meta['color']; ?>;"><?php echo $meta['title']; ?></h4>
                    <p class="text-muted small mb-4" style="max-width: 380px; margin-inline: auto;"><?php echo $meta['desc']; ?></p>

                    <div class="bg-light rounded-4 p-4 text-start mb-4">
                        <div class="d-flex justify-content-between py-1">
                            <span class="text-muted small">No. Order</span>
                            <span class="fw-bold small"><?php echo htmlspecialchars($order['order_number']); ?></span>
                        </div>
                        <div class="d-flex justify-content-between py-1">
                            <span class="text-muted small">Program</span>
                            <span class="fw-bold small"><?php echo htmlspecialchars($order['class_name'] ?? '-'); ?></span>
                        </div>
                        <div class="d-flex justify-content-between py-1">
                            <span class="text-muted small">Instruktur</span>
                            <span class="fw-bold small text-end" style="max-width:160px;"><?php echo !empty($order['instructor_name']) ? htmlspecialchars($order['instructor_name']) . '<br><span class="fw-normal text-muted" style="font-size:0.7rem;">'.htmlspecialchars($order['instructor_spec']??'').'</span>' : '<span class="text-muted">Instruktur akan ditentukan oleh admin</span>'; ?></span>
                        </div>
                        <div class="d-flex justify-content-between py-1">
                            <span class="text-muted small">Mode</span>
                            <span class="fw-bold small"><?php $cm = strtolower($order['class_mode'] ?? 'offline'); echo $cm==='online' ? '<span class="badge bg-info bg-opacity-10 text-info" style="font-size:0.7rem;"><i class="fas fa-laptop me-1"></i>Online</span>' : '<span class="badge bg-success bg-opacity-10 text-success" style="font-size:0.7rem;"><i class="fas fa-chalkboard-teacher me-1"></i>Offline</span>'; ?></span>
                        </div>
                        <div class="d-flex justify-content-between py-1">
                            <span class="text-muted small">Metode</span>
                            <span class="fw-bold small"><?php echo htmlspecialchars($order['payment_method'] ?? '-'); ?></span>
                        </div>
                        <div class="d-flex justify-content-between py-1">
                            <span class="text-muted small">Total</span>
                            <span class="fw-bold small">Rp <?php echo number_format($order['amount'], 0, ',', '.'); ?></span>
                        </div>
                        <?php if ($order['paid_at']): ?>
                            <div class="d-flex justify-content-between py-1">
                                <span class="text-muted small">Dibayar</span>
                                <span class="fw-bold small"><?php echo date('d M Y H:i', strtotime($order['paid_at'])); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if ($order['payment_status'] === 'paid'): ?>
                        <div class="d-grid gap-2">
                            <?php if ($classMode === 'offline' && !empty($waLink)): ?>
                            <a href="<?php echo htmlspecialchars($waLink); ?>" target="_blank" rel="noopener" class="btn rounded-pill py-3 fw-bold text-white shadow-sm" style="background: linear-gradient(135deg, #25d366, #128c7e);">
                                <i class="fab fa-whatsapp me-2"></i>Gabung Grup WhatsApp Kelas
                            </a>
                            <a href="generate_pdf.php?order=<?php echo urlencode($order['order_number']); ?>" class="btn btn-outline-primary rounded-pill py-2 fw-bold">
                                <i class="fas fa-file-invoice me-2"></i>Lihat Bukti Pendaftaran
                            </a>
                            <a href="../lms/dashboard.php" class="btn btn-light rounded-pill py-2 fw-bold border">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                            <?php elseif ($classMode === 'offline'): ?>
                            <div class="alert alert-warning border-0 rounded-3 small text-start mb-2" style="background: #fef3c7; color: #92400e;">
                                <i class="fas fa-info-circle me-2"></i>Admin akan segera menghubungi Anda via WhatsApp untuk info grup kelas & jadwal pelatihan.
                            </div>
                            <a href="generate_pdf.php?order=<?php echo urlencode($order['order_number']); ?>" class="btn btn-outline-primary rounded-pill py-2 fw-bold">
                                <i class="fas fa-file-invoice me-2"></i>Lihat Bukti Pendaftaran
                            </a>
                            <a href="../lms/dashboard.php" class="btn btn-light rounded-pill py-2 fw-bold border">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                            <?php else: ?>
                            <a href="../lms/dashboard.php" class="btn rounded-pill py-2 fw-bold text-white" style="background: linear-gradient(135deg, #0c4a6e, #0ea5e9);">
                                <i class="fas fa-book-open me-2"></i>Buka LMS
                            </a>
                            <a href="generate_pdf.php?order=<?php echo urlencode($order['order_number']); ?>" class="btn btn-outline-primary rounded-pill py-2 fw-bold">
                                <i class="fas fa-file-invoice me-2"></i>Lihat Bukti Pendaftaran
                            </a>
                            <?php endif; ?>
                        </div>
                    <?php elseif (in_array($order['payment_status'], ['failed', 'expired', 'unpaid'])): ?>
                        <div class="d-grid gap-2">
                            <p class="small text-muted">Pembayaran belum selesai. Silakan hubungi admin atau buat pesanan baru.</p>
                            <a href="../index.php#paket" class="btn rounded-pill py-2 fw-bold text-white" style="background: linear-gradient(135deg, #0c4a6e, #0ea5e9);">
                                <i class="fas fa-redo me-2"></i>Pesan Ulang
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="d-grid gap-2">
                            <a href="<?php echo (str_contains($_SERVER['SCRIPT_NAME']??'','/MitraCiptaMandiriWeb/') ? '/MitraCiptaMandiriWeb/index.php' : '/index.php'); ?>" class="btn btn-outline-primary rounded-pill py-2 fw-bold">Kembali ke Beranda</a>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="mx-auto mb-4 d-flex align-items-center justify-content-center rounded-circle bg-light" style="width: 96px; height: 96px;">
                        <i class="fas fa-search" style="font-size: 2.5rem; color: #6b7280;"></i>
                    </div>
                    <h4 class="fw-bold mb-2 text-dark">Order Tidak Ditemukan</h4>
                    <p class="text-muted small mb-4">Tidak ada transaksi dengan nomor tersebut.</p>
                    <a href="<?php echo (str_contains($_SERVER['SCRIPT_NAME']??'','/MitraCiptaMandiriWeb/') ? '/MitraCiptaMandiriWeb/index.php' : '/index.php'); ?>" class="btn btn-primary rounded-pill px-4 fw-bold">Kembali ke Beranda</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>