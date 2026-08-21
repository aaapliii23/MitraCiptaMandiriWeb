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
    // Auto sync paid orders
    $paidOrders = $pdo->query("SELECT o.id, o.order_number, o.customer_name, o.amount, o.created_at, c.name as class_name 
                               FROM orders o 
                               JOIN classes c ON o.class_id = c.id 
                               WHERE o.payment_status = 'paid'")->fetchAll(PDO::FETCH_ASSOC);

    $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM finance_transactions WHERE order_id = ?");
    $insStmt = $pdo->prepare("INSERT INTO finance_transactions (type, category, item_name, quantity, unit_price, amount, description, order_id, transaction_date) 
                              VALUES ('in', 'pemasukan_kursus', ?, 1, ?, ?, ?, ?, ?)");

    foreach ($paidOrders as $ord) {
        $checkStmt->execute([$ord['id']]);
        if ($checkStmt->fetchColumn() == 0) {
            $itemName = "Pendaftaran " . $ord['class_name'] . " (" . $ord['customer_name'] . ")";
            $desc = "Pemasukan pembayaran kursus no. order " . $ord['order_number'];
            $tDate = date('Y-m-d', strtotime($ord['created_at']));
            $insStmt->execute([$itemName, $ord['amount'], $ord['amount'], $desc, $ord['id'], $tDate]);
        }
    }

    if ($year === '' || $year === 'all') {
        $rows = $pdo->query("SELECT * FROM finance_transactions ORDER BY transaction_date ASC, id ASC")->fetchAll(PDO::FETCH_ASSOC);
        $periodLabel = 'Semua Periode';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM finance_transactions WHERE YEAR(transaction_date) = ? ORDER BY transaction_date ASC, id ASC");
        $stmt->execute([$year]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $periodLabel = 'Tahun ' . $year;
    }
    foreach ($rows as $tr) {
        if ($tr['type'] === 'in') $totalIn += (int)$tr['amount'];
        else $totalOut += (int)$tr['amount'];
    }
} catch (PDOException $e) {}

$netProfit = $totalIn - $totalOut;
$profitMargin = $totalIn > 0 ? round(($netProfit / $totalIn) * 100, 1) : 0;

$catRecap = [];
foreach ($rows as $tr) {
    $cat = $tr['category'] ?: 'lainnya';
    $key = $tr['type'] . '|' . $cat;
    if (!isset($catRecap[$key])) {
        $catRecap[$key] = ['type' => $tr['type'], 'category' => $cat, 'total' => 0];
    }
    $catRecap[$key]['total'] += (int)$tr['amount'];
}
usort($catRecap, function($a, $b) { return strcmp($a['type'], $b['type']) ?: $b['total'] <=> $a['total']; });
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan &amp; Laba Rugi - MCM</title>
    <link rel="icon" type="image/png" href="../assets/img/logo.png">
    <link rel="shortcut icon" type="image/png" href="../assets/img/logo.png">
    <link rel="apple-touch-icon" href="../assets/img/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f1f5f9; color: #1e293b; }
        .report-sheet { background: white; max-width: 860px; margin: 40px auto; box-shadow: 0 20px 50px rgba(0,0,0,0.08); border-radius: 1.25rem; overflow: hidden; }
        .report-head { background: linear-gradient(135deg, #0c4a6e, #0ea5e9); color: white; padding: 32px 40px; }
        .report-body { padding: 36px 40px; }
        .stat-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px 16px; }
        table thead th { background: #f8fafc; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; color: #64748b; }
        .sign-wrap { text-align: center; }
        @media print {
            body { background: white; }
            .report-sheet { margin: 0; box-shadow: none; border-radius: 0; max-width: 100% !important; }
            .no-print { display: none !important; }
            .report-head { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            thead { display: table-header-group; }
            .stat-box { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
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
                <img src="../assets/img/logo.png" alt="MCM" style="height: 50px; filter: drop-shadow(0 0 8px rgba(255,255,255,0.3));">
                <div class="ms-3">
                    <span class="d-block fw-bold fs-5" style="letter-spacing: 1px;">LPK MITRA CIPTA MANDIRI</span>
                    <span class="d-block small opacity-75">Lembaga Pelatihan &amp; Kursus Vokasi Bersertifikat</span>
                </div>
            </div>
            <div class="text-end small">
                <div class="fw-bold">LAPORAN ARUS KAS &amp; LABA BERSIH</div>
                <div class="opacity-90 fw-semibold"><?php echo htmlspecialchars($periodLabel); ?></div>
                <div class="opacity-75">Dicetak: <?php echo date('d F Y, H:i'); ?></div>
            </div>
        </div>

        <div class="report-body">
            <!-- 4 KPI Boxes -->
            <div class="row g-3 mb-4">
                <div class="col-3">
                    <div class="stat-box text-center">
                        <div class="text-muted small text-uppercase fw-bold mb-1" style="font-size: 0.68rem;">Pendapatan Kotor</div>
                        <h6 class="fw-bold text-success mb-0">Rp <?php echo number_format($totalIn, 0, ',', '.'); ?></h6>
                    </div>
                </div>
                <div class="col-3">
                    <div class="stat-box text-center">
                        <div class="text-muted small text-uppercase fw-bold mb-1" style="font-size: 0.68rem;">Total Uang Terpakai</div>
                        <h6 class="fw-bold text-danger mb-0">Rp <?php echo number_format($totalOut, 0, ',', '.'); ?></h6>
                    </div>
                </div>
                <div class="col-3">
                    <div class="stat-box text-center">
                        <div class="text-muted small text-uppercase fw-bold mb-1" style="font-size: 0.68rem;">Laba Bersih</div>
                        <h6 class="fw-bold <?php echo $netProfit >= 0 ? 'text-primary' : 'text-danger'; ?> mb-0">
                            Rp <?php echo number_format($netProfit, 0, ',', '.'); ?>
                        </h6>
                    </div>
                </div>
                <div class="col-3">
                    <div class="stat-box text-center">
                        <div class="text-muted small text-uppercase fw-bold mb-1" style="font-size: 0.68rem;">Margin Laba</div>
                        <h6 class="fw-bold text-dark mb-0"><?php echo $profitMargin; ?>%</h6>
                    </div>
                </div>
            </div>

            <!-- Rekapitulasi per Kategori -->
            <h6 class="fw-bold text-dark mb-2 pb-1 border-bottom"><i class="fas fa-chart-pie me-1 text-primary"></i> Rekapitulasi per Kategori</h6>
            <div class="row g-2 mb-4">
                <?php foreach ($catRecap as $cr): ?>
                    <div class="col-md-4 col-sm-6">
                        <div class="p-2 px-3 rounded-3 border bg-light d-flex justify-content-between align-items-center">
                            <span class="small <?php echo $cr['type'] === 'in' ? 'text-success' : 'text-danger'; ?> fw-bold">
                                <?php echo $cr['type'] === 'in' ? '[+] ' : '[-] '; ?><?php echo ucwords(str_replace('_', ' ', $cr['category'])); ?>
                            </span>
                            <span class="small fw-bold text-dark">Rp <?php echo number_format($cr['total'], 0, ',', '.'); ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Detailed Transactions Table -->
            <h6 class="fw-bold text-dark mb-2 pb-1 border-bottom"><i class="fas fa-list me-1 text-primary"></i> Rincian Pembelian Barang &amp; Transaksi Keuangan</h6>
            <div class="table-responsive mb-4">
                <table class="table table-bordered table-sm align-middle mb-0" style="font-size: 0.8rem;">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 35px;">No</th>
                            <th>Tanggal</th>
                            <th>Tipe</th>
                            <th>Kategori</th>
                            <th>Nama Barang / Uraian</th>
                            <th class="text-center">Kuantitas</th>
                            <th class="text-end">Nominal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($rows)): ?>
                            <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada catatan transaksi pada periode ini.</td></tr>
                        <?php else: ?>
                            <?php foreach ($rows as $i => $tr): ?>
                                <tr>
                                    <td class="text-center"><?php echo $i + 1; ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($tr['transaction_date'])); ?></td>
                                    <td>
                                        <span class="<?php echo $tr['type'] === 'in' ? 'text-success fw-bold' : 'text-danger fw-bold'; ?>">
                                            <?php echo $tr['type'] === 'in' ? 'Masuk' : 'Keluar'; ?>
                                        </span>
                                    </td>
                                    <td><?php echo ucwords(str_replace('_', ' ', $tr['category'])); ?></td>
                                    <td class="fw-semibold">
                                        <?php echo htmlspecialchars($tr['item_name'] ?: ucwords(str_replace('_', ' ', $tr['category']))); ?>
                                        <?php if (!empty($tr['description'])): ?>
                                            <span class="text-muted small d-block" style="font-size: 0.72rem;"><?php echo htmlspecialchars($tr['description']); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center text-muted">
                                        <?php echo $tr['quantity'] > 1 ? $tr['quantity'] . ' unit' : '1 unit'; ?>
                                    </td>
                                    <td class="text-end fw-bold <?php echo $tr['type'] === 'in' ? 'text-success' : 'text-danger'; ?>">
                                        <?php echo $tr['type'] === 'in' ? '+' : '-'; ?> Rp <?php echo number_format($tr['amount'], 0, ',', '.'); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Signature -->
            <div class="row mt-5 pt-3">
                <div class="col-7"></div>
                <div class="col-5 text-center">
                    <div class="small text-muted mb-1">Cirebon, <?php echo date('d F Y'); ?></div>
                    <div class="small fw-bold text-dark mb-5">Pimpinan &amp; Bagian Keuangan MCM</div>
                    <div class="fw-bold text-dark text-decoration-underline">( Hj. Ratu Fitria, S.Pd. )</div>
                    <div class="small text-muted">Direktur / Pimpinan Lembaga</div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
