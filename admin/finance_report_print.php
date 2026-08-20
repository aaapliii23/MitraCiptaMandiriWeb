<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../auth/admin_login.php');
    exit;
}

require_once '../config/database.php';

$year = isset($_GET['year']) ? preg_replace('/[^0-9]|^0+/', '', $_GET['year']) : date('Y');
$mode = (isset($_GET['mode']) && $_GET['mode'] === 'kategori') ? 'kategori' : 'menyeluruh';
$rpcat = trim($_GET['cat'] ?? '');
$periodLabel = ($year === '' || $year === 'all') ? 'Semua Periode' : 'Tahun ' . $year;

$rows = [];
$totalIn = $totalOut = 0;
try {
    if ($year === '' || $year === 'all') {
        $rows = $pdo->query("SELECT * FROM finance_transactions ORDER BY transaction_date ASC, id ASC")->fetchAll();
    } else {
        $stmt = $pdo->prepare("SELECT * FROM finance_transactions WHERE YEAR(transaction_date) = ? ORDER BY transaction_date ASC, id ASC");
        $stmt->execute([$year]);
        $rows = $stmt->fetchAll();
    }
    foreach ($rows as $tr) {
        if ($tr['type'] === 'in') $totalIn += (int)$tr['amount'];
        else $totalOut += (int)$tr['amount'];
    }
} catch (PDOException $e) {}
$balance = $totalIn - $totalOut;

$periodRecap = [];
$months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
foreach ($rows as $tr) {
    $ts = strtotime($tr['transaction_date']);
    if ($year === '' || $year === 'all') {
        $key = date('Y', $ts);
        $label = 'Tahun ' . $key;
    } else {
        $key = date('Y-m', $ts);
        $label = $months[(int)date('n', $ts) - 1] . ' ' . date('Y', $ts);
    }
    if (!isset($periodRecap[$key])) $periodRecap[$key] = ['label' => $label, 'in' => 0, 'out' => 0];
    if ($tr['type'] === 'in') $periodRecap[$key]['in'] += (int)$tr['amount'];
    else $periodRecap[$key]['out'] += (int)$tr['amount'];
}
ksort($periodRecap);

$catRows = [];
$catTotOrders = $catTotOmzet = 0;
$catLabel = '';
try {
    $sql = "SELECT c.category, COUNT(o.id) AS total_orders, SUM(o.amount) AS omzet
            FROM orders o JOIN classes c ON o.class_id = c.id
            WHERE o.payment_status = 'paid'";
    $args = [];
    if ($year !== '' && $year !== 'all') { $sql .= " AND YEAR(o.created_at) = ?"; $args[] = $year; }
    if ($rpcat !== '') { $sql .= " AND c.category = ?"; $args[] = $rpcat; $catLabel = $rpcat; }
    $sql .= " GROUP BY c.category ORDER BY omzet DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($args);
    $catRows = $stmt->fetchAll();
    foreach ($catRows as $cr) {
        $catTotOrders += (int)$cr['total_orders'];
        $catTotOmzet += (int)$cr['omzet'];
    }
} catch (PDOException $e) {}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $mode === 'kategori' ? 'Laporan Rekap Omzet per Kategori' : 'Laporan Keuangan'; ?> - Mitra Cipta Mandiri</title>
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
                <div class="fw-bold"><?php echo $mode === 'kategori' ? 'REKAP OMZET PER KATEGORI' : 'LAPORAN KEUANGAN'; ?></div>
                <div class="opacity-75"><?php echo htmlspecialchars($periodLabel); ?></div>
                <div class="opacity-75">Dicetak: <?php echo date('d F Y'); ?></div>
            </div>
        </div>

        <div class="report-body">
            <div class="kop text-center mb-4">
                <h4 class="fw-bold mb-1"><?php echo $mode === 'kategori' ? 'REKAP OMZET PER KATEGORI PELATIHAN' : 'LAPORAN REKAP KEUANGAN MENYELURUH'; ?></h4>
                <span class="text-muted small">Periode: <?php echo htmlspecialchars($periodLabel); ?><?php echo $catLabel !== '' ? ' | Kategori: ' . htmlspecialchars($catLabel) : ''; ?></span>
            </div>

            <?php if ($mode === 'kategori'): ?>
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Kategori Pelatihan</th>
                        <th class="text-end">Jumlah Pesanan</th>
                        <th class="text-end">Omzet</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($catRows)): ?>
                        <tr><td colspan="3" class="text-center text-muted py-4">Belum ada omzet pada periode ini.</td></tr>
                    <?php else: ?>
                        <?php foreach ($catRows as $cr): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($cr['category']); ?></td>
                                <td class="text-end"><?php echo number_format($cr['total_orders'], 0, ',', '.'); ?> pesanan</td>
                                <td class="text-end fw-bold">Rp <?php echo number_format($cr['omzet'], 0, ',', '.'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <?php if (!empty($catRows)): ?>
                <tfoot>
                    <tr class="table-light fw-bold">
                        <td>TOTAL</td>
                        <td class="text-end"><?php echo number_format($catTotOrders, 0, ',', '.'); ?> pesanan</td>
                        <td class="text-end">Rp <?php echo number_format($catTotOmzet, 0, ',', '.'); ?></td>
                    </tr>
                </tfoot>
                <?php endif; ?>
            </table>
            <?php else: ?>
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Periode</th>
                        <th class="text-end">Uang Masuk</th>
                        <th class="text-end">Uang Keluar</th>
                        <th class="text-end">Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($periodRecap)): ?>
                        <tr><td colspan="4" class="text-center text-muted py-4">Belum ada transaksi pada periode ini.</td></tr>
                    <?php else: ?>
                        <?php $running = 0; foreach ($periodRecap as $pr): $running += $pr['in'] - $pr['out']; ?>
                            <tr>
                                <td><?php echo htmlspecialchars($pr['label']); ?></td>
                                <td class="text-end">Rp <?php echo number_format($pr['in'], 0, ',', '.'); ?></td>
                                <td class="text-end">Rp <?php echo number_format($pr['out'], 0, ',', '.'); ?></td>
                                <td class="text-end">Rp <?php echo number_format($running, 0, ',', '.'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr class="table-light fw-bold">
                        <td>TOTAL</td>
                        <td class="text-end">Rp <?php echo number_format($totalIn, 0, ',', '.'); ?></td>
                        <td class="text-end">Rp <?php echo number_format($totalOut, 0, ',', '.'); ?></td>
                        <td class="text-end <?php echo $balance < 0 ? 'text-danger' : 'text-success'; ?>">Rp <?php echo number_format($balance, 0, ',', '.'); ?></td>
                    </tr>
                </tfoot>
            </table>
            <?php endif; ?>

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