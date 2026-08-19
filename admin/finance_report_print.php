<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../auth/admin_login.php');
    exit;
}

require_once '../config/database.php';

$year = isset($_GET['year']) ? preg_replace('/[^0-9]|^0+/', '', $_GET['year']) : date('Y');

$rows = [];
$totalIn = $totalOut = 0;
try {
    if ($year === '' || $year === 'all') {
        $rows = $pdo->query("SELECT * FROM finance_transactions ORDER BY transaction_date ASC, id ASC")->fetchAll();
        $periodLabel = 'Semua Periode';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM finance_transactions WHERE YEAR(transaction_date) = ? ORDER BY transaction_date ASC, id ASC");
        $stmt->execute([$year]);
        $rows = $stmt->fetchAll();
        $periodLabel = 'Tahun ' . $year;
    }
    foreach ($rows as $tr) {
        if ($tr['type'] === 'in') $totalIn += (int)$tr['amount'];
        else $totalOut += (int)$tr['amount'];
    }
} catch (PDOException $e) {}
$balance = $totalIn - $totalOut;

$catRecap = [];
foreach ($rows as $tr) {
    $key = $tr['type'] . '|' . $tr['category'];
    if (!isset($catRecap[$key])) $catRecap[$key] = ['type' => $tr['type'], 'category' => $tr['category'], 'total' => 0];
    $catRecap[$key]['total'] += (int)$tr['amount'];
}
usort($catRecap, function($a, $b) { return strcmp($a['type'], $b['type']) ?: $b['total'] <=> $a['total']; });

$categoryLabels = [
    'pemasukan_kursus' => 'Pemasukan Kursus',
    'sewa' => 'Sewa',
    'gaji' => 'Gaji',
    'operasional' => 'Operasional',
    'lainnya' => 'Lainnya',
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan - Mitra Cipta Mandiri</title>
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
            <a href="dashboard.php?page=finance" class="btn btn-light rounded-pill px-4 py-2 fw-bold border">Kembali</a>
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
                <div class="fw-bold">LAPORAN KEUANGAN</div>
                <div class="opacity-75"><?php echo htmlspecialchars($periodLabel); ?></div>
                <div class="opacity-75">Dicetak: <?php echo date('d F Y'); ?></div>
            </div>
        </div>

        <div class="report-body">
            <div class="kop text-center mb-4">
                <h4 class="fw-bold mb-1">LAPORAN REKAP KEUANGAN</h4>
                <span class="text-muted small">Periode: <?php echo htmlspecialchars($periodLabel); ?></span>
            </div>

            <table class="table align-middle mb-4">
                <thead>
                    <tr>
                        <th>Kategori</th>
                        <th class="text-end">Uang Masuk</th>
                        <th class="text-end">Uang Keluar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($catRecap as $cr): ?>
                        <tr>
                            <td><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $cr['category']))); ?></td>
                            <td class="text-end"><?php echo $cr['type'] === 'in' ? 'Rp ' . number_format($cr['total'], 0, ',', '.') : '-'; ?></td>
                            <td class="text-end"><?php echo $cr['type'] === 'out' ? 'Rp ' . number_format($cr['total'], 0, ',', '.') : '-'; ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($catRecap)): ?>
                        <tr><td colspan="3" class="text-center text-muted py-4">Belum ada transaksi pada periode ini.</td></tr>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr class="table-light fw-bold">
                        <td>TOTAL</td>
                        <td class="text-end">Rp <?php echo number_format($totalIn, 0, ',', '.'); ?></td>
                        <td class="text-end">Rp <?php echo number_format($totalOut, 0, ',', '.'); ?></td>
                    </tr>
                    <tr class="fw-bold" style="border-top: 2px solid #0c4a6e;">
                        <td colspan="2">SALDO AKHIR</td>
                        <td class="text-end <?php echo $balance < 0 ? 'text-danger' : 'text-success'; ?>">Rp <?php echo number_format($balance, 0, ',', '.'); ?></td>
                    </tr>
                </tfoot>
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
                    <div class="fw-bold mb-1">Bendahara</div>
                    <div class="mb-5"></div>
                    <div class="border-top border-2 border-dark mx-auto" style="width: 200px;"></div>
                    <div class="small text-muted mt-1">Administrasi MCM</div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
