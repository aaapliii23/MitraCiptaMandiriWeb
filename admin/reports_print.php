<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../auth/admin_login.php');
    exit;
}

require_once '../config/database.php';

$year = isset($_GET['year']) ? preg_replace('/[^0-9]|^0+/', '', $_GET['year']) : date('Y');
$periodLabel = ($year === '' || $year === 'all') ? 'Semua Periode' : 'Tahun ' . $year;

// Retrieve statistics
try {
    if ($year === '' || $year === 'all') {
        $revenue = $pdo->query("SELECT SUM(amount) FROM orders WHERE payment_status = 'paid'")->fetchColumn();
        $pendingCount = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn();
        $totalOrd = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn() ?: 1;
        $confirmedCount = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'confirmed'")->fetchColumn() ?: 0;
        $cancelledCount = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'cancelled'")->fetchColumn();
        $paidCount = $pdo->query("SELECT COUNT(*) FROM orders WHERE payment_status = 'paid'")->fetchColumn();
        $unpaidCount = $pdo->query("SELECT COUNT(*) FROM orders WHERE payment_status IN ('unpaid','pending')")->fetchColumn();
        $failedCount = $pdo->query("SELECT COUNT(*) FROM orders WHERE payment_status = 'failed'")->fetchColumn();
        $expiredCount = $pdo->query("SELECT COUNT(*) FROM orders WHERE payment_status = 'expired'")->fetchColumn();
        
        $popular = $pdo->query("SELECT c.name, COUNT(o.id) as total FROM classes c LEFT JOIN orders o ON c.id = o.class_id GROUP BY c.id ORDER BY total DESC LIMIT 5")->fetchAll();
    } else {
        $revenue = $pdo->query("SELECT SUM(amount) FROM orders WHERE payment_status = 'paid' AND YEAR(created_at) = $year")->fetchColumn();
        $pendingCount = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending' AND YEAR(created_at) = $year")->fetchColumn();
        $totalOrd = $pdo->query("SELECT COUNT(*) FROM orders WHERE YEAR(created_at) = $year")->fetchColumn() ?: 1;
        $confirmedCount = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'confirmed' AND YEAR(created_at) = $year")->fetchColumn() ?: 0;
        $cancelledCount = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'cancelled' AND YEAR(created_at) = $year")->fetchColumn();
        $paidCount = $pdo->query("SELECT COUNT(*) FROM orders WHERE payment_status = 'paid' AND YEAR(created_at) = $year")->fetchColumn();
        $unpaidCount = $pdo->query("SELECT COUNT(*) FROM orders WHERE payment_status IN ('unpaid','pending') AND YEAR(created_at) = $year")->fetchColumn();
        $failedCount = $pdo->query("SELECT COUNT(*) FROM orders WHERE payment_status = 'failed' AND YEAR(created_at) = $year")->fetchColumn();
        $expiredCount = $pdo->query("SELECT COUNT(*) FROM orders WHERE payment_status = 'expired' AND YEAR(created_at) = $year")->fetchColumn();
        
        $popular = $pdo->query("SELECT c.name, COUNT(o.id) as total FROM classes c LEFT JOIN orders o ON c.id = o.class_id AND YEAR(o.created_at) = $year GROUP BY c.id ORDER BY total DESC LIMIT 5")->fetchAll();
    }
    $rate = round(($confirmedCount / $totalOrd) * 100);
} catch (PDOException $e) {
    $revenue = 0; $pendingCount = 0; $totalOrd = 0; $confirmedCount = 0; $cancelledCount = 0;
    $paidCount = 0; $unpaidCount = 0; $failedCount = 0; $expiredCount = 0; $rate = 0;
    $popular = [];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pendaftaran & Statistik - Mitra Cipta Mandiri</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f1f5f9; color: #1e293b; }
        .report-sheet { background: white; max-width: 820px; margin: 40px auto; box-shadow: 0 20px 50px rgba(0,0,0,0.08); border-radius: 1.25rem; overflow: hidden; }
        .report-head { background: linear-gradient(135deg, #0c4a6e, #0ea5e9); color: white; padding: 34px 44px; }
        .report-body { padding: 36px 44px; }
        .kop { border-bottom: 2px solid #0c4a6e; padding-bottom: 16px; }
        .title-line { letter-spacing: 2px; font-weight: 800; }
        table thead th { background: #f8fafc; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; color: #64748b; }
        .sign-wrap { text-align: center; }
        @media print {
            body { background: white; }
            .report-sheet { margin: 0; box-shadow: none; border-radius: 0; max-width: 100% !important; }
            .no-print { display: none !important; }
            .report-head { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            thead { display: table-header-group; }
        }
    </style>
</head>
<body>

    <div class="container no-print mt-4 text-center">
        <div class="d-flex flex-wrap justify-content-center gap-2">
            <button onclick="window.print()" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm" style="background: #0c4a6e; border: none;">
                <i class="fas fa-print me-2"></i> Cetak PDF / Simpan
            </button>
            <a href="dashboard.php?page=reports" class="btn btn-light rounded-pill px-4 py-2 fw-bold border">Kembali</a>
        </div>
    </div>

    <div class="report-sheet">
        <div class="report-head d-flex justify-content-between align-items-start">
            <div class="d-flex align-items-center">
                <img src="../assets/img/logo.png" alt="MCM" style="height: 52px; filter: drop-shadow(0 0 8px rgba(255,255,255,0.3));">
                <div class="ms-3">
                    <span class="d-block title-line fw-bold fs-5">MITRA CIPTA MANDIRI</span>
                    <span class="d-block small opacity-75">Lembaga Pelatihan Vokasi Bersertifikat</span>
                </div>
            </div>
            <div class="text-end small">
                <div class="fw-bold">LAPORAN PENDAFTARAN & STATISTIK</div>
                <div class="opacity-75"><?php echo htmlspecialchars($periodLabel); ?></div>
                <div class="opacity-75">Dicetak: <?php echo date('d F Y'); ?></div>
            </div>
        </div>

        <div class="report-body">
            <div class="kop text-center mb-4">
                <h4 class="fw-bold mb-1">LAPORAN REKAP PENDAFTARAN & STATISTIK</h4>
                <span class="text-muted small">Periode: <?php echo htmlspecialchars($periodLabel); ?></span>
            </div>

            <h5 class="fw-bold mb-3 mt-4 text-dark"><i class="fas fa-chart-pie me-2 text-primary"></i>Statistik & Kinerja Penjualan</h5>
            <table class="table align-middle mb-4">
                <thead>
                    <tr>
                        <th>Parameter Laporan</th>
                        <th class="text-end">Nilai / Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Total Estimasi Omzet (Pembayaran Lunas)</td>
                        <td class="text-end fw-bold text-success">Rp <?php echo number_format($revenue ?: 0, 0, ',', '.'); ?></td>
                    </tr>
                    <tr>
                        <td>Total Pendaftaran / Peserta Masuk</td>
                        <td class="text-end"><?php echo number_format($totalOrd); ?> Peserta</td>
                    </tr>
                    <tr>
                        <td>Rasio Konversi Kelulusan/Penerimaan</td>
                        <td class="text-end"><?php echo $rate; ?>%</td>
                    </tr>
                    <tr>
                        <td>Pesanan Pending (Menunggu Konfirmasi)</td>
                        <td class="text-end"><?php echo $pendingCount; ?> Pesanan</td>
                    </tr>
                    <tr>
                        <td>Pembayaran Lunas (Paid)</td>
                        <td class="text-end text-success"><?php echo $paidCount; ?></td>
                    </tr>
                    <tr>
                        <td>Pembayaran Belum Dilunasi (Unpaid/Pending)</td>
                        <td class="text-end text-muted"><?php echo $unpaidCount; ?></td>
                    </tr>
                    <tr>
                        <td>Pembayaran Gagal (Failed)</td>
                        <td class="text-end text-danger"><?php echo $failedCount; ?></td>
                    </tr>
                    <tr>
                        <td>Pembayaran Kadaluwarsa (Expired)</td>
                        <td class="text-end text-warning"><?php echo $expiredCount; ?></td>
                    </tr>
                    <tr>
                        <td>Total Pembatalan Pesanan</td>
                        <td class="text-end text-danger"><?php echo $cancelledCount; ?></td>
                    </tr>
                </tbody>
            </table>

            <h5 class="fw-bold mb-3 mt-4 text-dark"><i class="fas fa-trophy me-2 text-warning"></i>Program Terpopuler & Peminatan</h5>
            <table class="table align-middle mb-4">
                <thead>
                    <tr>
                        <th style="width: 80px;">Peringkat</th>
                        <th>Nama Paket Pelatihan</th>
                        <th class="text-end">Jumlah Peminat</th>
                        <th class="text-end">Kontribusi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($popular)): ?>
                        <tr><td colspan="4" class="text-center text-muted py-4">Belum ada data pendaftaran program pada periode ini.</td></tr>
                    <?php else: ?>
                        <?php foreach ($popular as $idx => $p): ?>
                            <tr>
                                <td class="fw-bold text-primary">#<?php echo $idx + 1; ?></td>
                                <td class="fw-bold"><?php echo htmlspecialchars($p['name']); ?></td>
                                <td class="text-end"><?php echo $p['total']; ?> Pendaftar</td>
                                <td class="text-end"><?php echo $totalOrd > 0 ? round(($p['total'] / $totalOrd) * 100) : 0; ?>%</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <div class="row mt-5">
                <div class="col-6">
                    <div class="text-muted small mb-1">Mengetahui,</div>
                    <div class="fw-bold mb-1">Kepala Lembaga</div>
                    <div class="mb-5"></div>
                    <div class="border-top border-2 border-dark mx-auto" style="width: 200px;"></div>
                    <div class="small text-muted mt-1">Mitra Cipta Mandiri</div>
                </div>
                <div class="col-6 sign-wrap">
                    <div class="text-muted small mb-1">Dibuat oleh,</div>
                    <div class="fw-bold mb-1">Administrasi LPK</div>
                    <div class="mb-5"></div>
                    <div class="border-top border-2 border-dark mx-auto" style="width: 200px;"></div>
                    <div class="small text-muted mt-1">Administrasi MCM</div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            if(window.location.search.indexOf('print=true') !== -1 || true) {
                setTimeout(() => window.print(), 500);
            }
        });
    </script>
</body>
</html>
