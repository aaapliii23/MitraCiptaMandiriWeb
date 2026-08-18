<?php
require_once '../includes/db_config.php';

$orderNumber = $_GET['order'] ?? '';
$stmt = $pdo->prepare("SELECT o.*, c.name AS class_name FROM orders o LEFT JOIN classes c ON o.class_id = c.id WHERE o.order_number = ?");
$stmt->execute([$orderNumber]);
$order = $stmt->fetch();

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
                            <a href="../lms/dashboard.php" class="btn rounded-pill py-2 fw-bold text-white" style="background: linear-gradient(135deg, #0c4a6e, #0ea5e9);">
                                <i class="fas fa-book-open me-2"></i>Buka LMS
                            </a>
                            <a href="generate_pdf.php?order=<?php echo urlencode($order['order_number']); ?>" class="btn btn-outline-primary rounded-pill py-2 fw-bold">
                                <i class="fas fa-file-invoice me-2"></i>Lihat Bukti Pendaftaran
                            </a>
                        </div>
                    <?php elseif (in_array($order['payment_status'], ['failed', 'expired', 'unpaid'])): ?>
                        <div class="d-grid gap-2">
                            <a href="payment_mock.php?order=<?php echo urlencode($order['order_number']); ?>&token=retry" class="btn rounded-pill py-2 fw-bold text-white" style="background: linear-gradient(135deg, #0c4a6e, #0ea5e9);">
                                <i class="fas fa-redo me-2"></i>Coba Bayar Lagi
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="d-grid gap-2">
                            <a href="../index.php" class="btn btn-outline-primary rounded-pill py-2 fw-bold">Kembali ke Beranda</a>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="mx-auto mb-4 d-flex align-items-center justify-content-center rounded-circle bg-light" style="width: 96px; height: 96px;">
                        <i class="fas fa-search" style="font-size: 2.5rem; color: #6b7280;"></i>
                    </div>
                    <h4 class="fw-bold mb-2 text-dark">Order Tidak Ditemukan</h4>
                    <p class="text-muted small mb-4">Tidak ada transaksi dengan nomor tersebut.</p>
                    <a href="../index.php" class="btn btn-primary rounded-pill px-4 fw-bold">Kembali ke Beranda</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>