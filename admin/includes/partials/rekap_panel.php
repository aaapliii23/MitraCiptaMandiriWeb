<?php
// ============================================================
// Rekap panel: "Rekap Keseluruhan" (bulanan) vs "Rekap Per Kategori" (omzet per kategori pelatihan)
// Variabel input: $rpYear ('' | 'all' | 'YYYY'). Baca $rp & $rpcat dari $_GET.
// ============================================================
if (!isset($rpYear)) $rpYear = date('Y');
$rp = (($_GET['rp'] ?? 'keseluruhan') === 'kategori') ? 'kategori' : 'keseluruhan';
$rpcat = trim($_GET['rpcat'] ?? '');
$rpAll = ($rpYear === '' || $rpYear === 'all');

if (!function_exists('rp_link')) {
    function rp_link($rpVal, $rpcatVal = null) {
        $q = $_GET;
        $q['rp'] = $rpVal;
        unset($q['rpcat']);
        if ($rpcatVal !== null && $rpcatVal !== '') $q['rpcat'] = $rpcatVal;
        return '?' . http_build_query($q);
    }
}

// --- rekap bulanan (keseluruhan) ---
$rpMonths = [];
$rpTotIn = $rpTotOut = 0;
$rpMNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
try {
    if ($rpAll) {
        $rpRows = $pdo->query("SELECT * FROM finance_transactions ORDER BY transaction_date ASC, id ASC")->fetchAll();
    } else {
        $rpStmt = $pdo->prepare("SELECT * FROM finance_transactions WHERE YEAR(transaction_date) = ? ORDER BY transaction_date ASC, id ASC");
        $rpStmt->execute([$rpYear]);
        $rpRows = $rpStmt->fetchAll();
    }
} catch (PDOException $e) { $rpRows = []; }
foreach ($rpRows as $tr) {
    $ts = strtotime($tr['transaction_date']);
    if ($rpAll) { $key = date('Y', $ts); $label = 'Tahun ' . $key; }
    else { $key = date('Y-m', $ts); $label = $rpMNames[(int)date('n', $ts) - 1] . ' ' . date('Y', $ts); }
    if (!isset($rpMonths[$key])) $rpMonths[$key] = ['label' => $label, 'in' => 0, 'out' => 0];
    if ($tr['type'] === 'in') { $rpMonths[$key]['in'] += (int)$tr['amount']; $rpTotIn += (int)$tr['amount']; }
    else { $rpMonths[$key]['out'] += (int)$tr['amount']; $rpTotOut += (int)$tr['amount']; }
}
ksort($rpMonths);
$rpBalance = $rpTotIn - $rpTotOut;

// --- omzet per kategori pelatihan ---
$rpCatList = [];
try { $rpCatList = $pdo->query("SELECT DISTINCT category FROM classes WHERE category <> '' ORDER BY category")->fetchAll(PDO::FETCH_COLUMN); } catch (PDOException $e) {}
if ($rpcat !== '' && !in_array($rpcat, $rpCatList, true)) $rpcat = '';
$rpCatRows = [];
$rpTotOrders = $rpTotOmzet = 0;
try {
    $rpSql = "SELECT c.category, COUNT(o.id) AS total_orders, SUM(o.amount) AS omzet
              FROM orders o JOIN classes c ON o.class_id = c.id
              WHERE o.payment_status = 'paid'";
    $rpArgs = [];
    if (!$rpAll) { $rpSql .= " AND YEAR(o.created_at) = ?"; $rpArgs[] = $rpYear; }
    if ($rpcat !== '') { $rpSql .= " AND c.category = ?"; $rpArgs[] = $rpcat; }
    $rpSql .= " GROUP BY c.category ORDER BY omzet DESC";
    $rpStmt = $pdo->prepare($rpSql);
    $rpStmt->execute($rpArgs);
    $rpCatRows = $rpStmt->fetchAll();
    foreach ($rpCatRows as $cr) {
        $rpTotOrders += (int)$cr['total_orders'];
        $rpTotOmzet += (int)$cr['omzet'];
    }
} catch (PDOException $e) {}

$rpPrintUrl = 'finance_report_print.php?year=' . urlencode($rpYear) . '&mode=' . $rp;
if ($rp === 'kategori' && $rpcat !== '') $rpPrintUrl .= '&cat=' . urlencode($rpcat);
?>
<div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <h5 class="fw-bold mb-0 text-dark">
            <i class="fas fa-<?php echo $rp === 'kategori' ? 'layer-group' : 'chart-line'; ?> me-2 text-primary"></i>
            <?php echo $rp === 'kategori' ? 'Rekap Omzet per Kategori Pelatihan' : 'Rekap Keuangan Keseluruhan'; ?>
        </h5>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <?php if ($rp === 'kategori'): ?>
            <select class="form-select rounded-pill border-primary text-primary fw-bold" style="width: auto; height: 42px;" onchange="location.href=this.value">
                <option value="<?php echo rp_link('kategori', ''); ?>">Semua Kategori</option>
                <?php foreach ($rpCatList as $rpCatName): ?>
                    <option value="<?php echo rp_link('kategori', $rpCatName); ?>" <?php echo $rpcat === $rpCatName ? 'selected' : ''; ?>><?php echo htmlspecialchars($rpCatName); ?></option>
                <?php endforeach; ?>
            </select>
            <?php endif; ?>
            <a class="btn btn-soft-primary px-4 rounded-pill" href="<?php echo $rpPrintUrl; ?>" target="_blank" style="height: 42px; text-decoration: none;">
                <i class="fas fa-print me-2"></i>Cetak
            </a>
        </div>
    </div>

    <?php if ($rp === 'kategori'): ?>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Kategori Pelatihan</th>
                        <th class="text-end">Jumlah Pesanan</th>
                        <th class="text-end pe-4">Omzet</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rpCatRows)): ?>
                        <tr><td colspan="3" class="text-center text-muted py-4">Belum ada omzet<?php echo !$rpAll ? ' tahun ' . $rpYear : ''; ?>.</td></tr>
                    <?php else: ?>
                        <?php foreach ($rpCatRows as $cr): ?>
                            <tr>
                                <td class="ps-4 fw-bold text-dark small"><?php echo htmlspecialchars($cr['category']); ?></td>
                                <td class="text-end small"><?php echo number_format($cr['total_orders'], 0, ',', '.'); ?> pesanan</td>
                                <td class="text-end pe-4 fw-bold small text-success">Rp <?php echo number_format($cr['omzet'], 0, ',', '.'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <?php if (!empty($rpCatRows)): ?>
                <tfoot>
                    <tr class="table-light fw-bold">
                        <td class="ps-4">TOTAL</td>
                        <td class="text-end"><?php echo number_format($rpTotOrders, 0, ',', '.'); ?> pesanan</td>
                        <td class="text-end pe-4">Rp <?php echo number_format($rpTotOmzet, 0, ',', '.'); ?></td>
                    </tr>
                </tfoot>
                <?php endif; ?>
            </table>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Periode</th>
                        <th class="text-end">Uang Masuk</th>
                        <th class="text-end">Uang Keluar</th>
                        <th class="text-end pe-4">Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rpMonths)): ?>
                        <tr><td colspan="4" class="text-center text-muted py-4">Belum ada transaksi keuangan.</td></tr>
                    <?php else: ?>
                        <?php $rpRunning = 0; foreach ($rpMonths as $pm): $rpRunning += $pm['in'] - $pm['out']; ?>
                            <tr>
                                <td class="ps-4 fw-bold text-dark small"><?php echo htmlspecialchars($pm['label']); ?></td>
                                <td class="text-end small text-success">Rp <?php echo number_format($pm['in'], 0, ',', '.'); ?></td>
                                <td class="text-end small text-danger">Rp <?php echo number_format($pm['out'], 0, ',', '.'); ?></td>
                                <td class="text-end pe-4 small fw-bold">Rp <?php echo number_format($rpRunning, 0, ',', '.'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr class="table-light fw-bold">
                        <td class="ps-4">TOTAL</td>
                        <td class="text-end">Rp <?php echo number_format($rpTotIn, 0, ',', '.'); ?></td>
                        <td class="text-end">Rp <?php echo number_format($rpTotOut, 0, ',', '.'); ?></td>
                        <td class="text-end pe-4">Rp <?php echo number_format($rpBalance, 0, ',', '.'); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    <?php endif; ?>
</div>