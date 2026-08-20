<!-- FINANCE PAGE -->
<?php
$finYear = isset($_GET['fin_year']) ? preg_replace('/[^0-9]/', '', $_GET['fin_year']) : date('Y');
$rp = (($_GET['rp'] ?? 'keseluruhan') === 'kategori') ? 'kategori' : 'keseluruhan';
$rpcat = trim($_GET['rpcat'] ?? '');
if (!function_exists('rp_link')) {
    function rp_link($rpVal, $rpcatVal = null) {
        $q = $_GET;
        $q['rp'] = $rpVal;
        unset($q['rpcat']);
        if ($rpcatVal !== null && $rpcatVal !== '') $q['rpcat'] = $rpcatVal;
        return '?' . http_build_query($q);
    }
}
$finRows = [];
$totalIn = $totalOut = 0;
try {
    if ($finYear === '' || $finYear === 'all') {
        $finRows = $pdo->query("SELECT * FROM finance_transactions ORDER BY transaction_date DESC, id DESC")->fetchAll();
    } else {
        $stmt = $pdo->prepare("SELECT * FROM finance_transactions WHERE YEAR(transaction_date) = ? ORDER BY transaction_date DESC, id DESC");
        $stmt->execute([$finYear]);
        $finRows = $stmt->fetchAll();
    }
    foreach ($finRows as $tr) {
        if ($tr['type'] === 'in') $totalIn += (int)$tr['amount'];
        else $totalOut += (int)$tr['amount'];
    }
} catch (PDOException $e) {}
$balance = $totalIn - $totalOut;
?>
        <div class="row align-items-center mb-4 g-3 no-print" data-aos="fade-down">
            <div class="col-md-6">
                <h2 class="fw-bold mb-1 text-dark">Keuangan</h2>
                <p class="text-muted mb-0">Rekap uang masuk & uang keluar, termasuk pemasukan sewa/rental.</p>
            </div>
            <div class="col-md-6 text-md-end d-flex justify-content-md-end gap-2 align-items-center">
                <select id="financeYear" class="form-select rounded-pill border-primary text-primary fw-bold" style="width: auto; height: 42px;" onchange="location.href='?page=finance&fin_year=' + this.value + '&rp=<?php echo $rp; ?>&rpcat=<?php echo urlencode($rpcat); ?>'">
                    <option value="all">Semua Tahun</option>
                    <?php
                    $finYears = $pdo->query("SELECT DISTINCT YEAR(transaction_date) AS y FROM finance_transactions ORDER BY y DESC")->fetchAll();
                    $yRange = range(date('Y'), date('Y') - 5);
                    $finYears = array_unique(array_merge(array_column($finYears, 'y'), $yRange));
                    foreach ($finYears as $y) {
                        $sel = ($y == $finYear) ? 'selected' : '';
                        echo "<option value='$y' $sel>Tahun $y</option>";
                    }
                    ?>
                </select>
                <div class="dropdown">
                    <button class="btn btn-soft-primary px-3 rounded-pill dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" style="height: 42px;">
                        <i class="fas fa-<?php echo $rp === 'kategori' ? 'layer-group' : 'chart-line'; ?> me-2"></i>
                        <?php echo $rp === 'kategori' ? 'Rekap Per Kategori' : 'Rekap Keseluruhan'; ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm rounded-3">
                        <li><a class="dropdown-item <?php echo $rp === 'keseluruhan' ? 'active' : ''; ?>" href="<?php echo rp_link('keseluruhan'); ?>"><i class="fas fa-chart-line me-2 text-primary"></i>Rekap Keseluruhan</a></li>
                        <li><a class="dropdown-item <?php echo $rp === 'kategori' ? 'active' : ''; ?>" href="<?php echo rp_link('kategori'); ?>"><i class="fas fa-layer-group me-2 text-primary"></i>Rekap Per Kategori</a></li>
                    </ul>
                </div>
                <button class="btn btn-primary px-4 shadow-sm rounded-pill" onclick="resetFinanceForm(); showModal('financeModal');">
                    <i class="fas fa-plus me-2"></i>Tambah Transaksi
                </button>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-success bg-opacity-10 p-2 rounded-3 me-3"><i class="fas fa-arrow-down text-success"></i></div>
                        <h6 class="text-muted small text-uppercase fw-bold mb-0">Total Uang Masuk</h6>
                    </div>
                    <h3 class="fw-bold text-success mb-0">Rp <?php echo number_format($totalIn, 0, ',', '.'); ?></h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-danger bg-opacity-10 p-2 rounded-3 me-3"><i class="fas fa-arrow-up text-danger"></i></div>
                        <h6 class="text-muted small text-uppercase fw-bold mb-0">Total Uang Keluar</h6>
                    </div>
                    <h3 class="fw-bold text-danger mb-0">Rp <?php echo number_format($totalOut, 0, ',', '.'); ?></h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-3"><i class="fas fa-wallet text-primary"></i></div>
                        <h6 class="text-muted small text-uppercase fw-bold mb-0">Saldo</h6>
                    </div>
                    <h3 class="fw-bold text-primary mb-0">Rp <?php echo number_format($balance, 0, ',', '.'); ?></h3>
                </div>
            </div>
        </div>

        <?php $rpYear = $finYear; include __DIR__ . '/../includes/partials/rekap_panel.php'; ?>

        <div class="row g-4 mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4">Tanggal</th>
                                        <th>Tipe</th>
                                        <th>Kategori</th>
                                        <th>Keterangan</th>
                                        <th class="text-end">Nominal</th>
                                        <th class="text-end pe-4">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($finRows)): ?>
                                        <tr><td colspan="6" class="text-center py-5 text-muted">Belum ada transaksi keuangan.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($finRows as $tr): ?>
                                            <tr>
                                                <td class="ps-4"><?php echo date('d M Y', strtotime($tr['transaction_date'])); ?></td>
                                                <td>
                                                    <?php if ($tr['type'] === 'in'): ?>
                                                        <span class="badge bg-success bg-opacity-10 text-success"><i class="fas fa-arrow-down me-1"></i>Masuk</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger bg-opacity-10 text-danger"><i class="fas fa-arrow-up me-1"></i>Keluar</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><span class="fw-bold text-dark small"><?php echo ucwords(str_replace('_', ' ', $tr['category'])); ?></span></td>
                                                <td class="small text-muted"><?php echo htmlspecialchars($tr['description'] ?: '-'); ?></td>
                                                <td class="text-end fw-bold <?php echo $tr['type'] === 'in' ? 'text-success' : 'text-danger'; ?>">
                                                    <?php echo ($tr['type'] === 'out' ? '-' : '+') . ' Rp ' . number_format($tr['amount'], 0, ',', '.'); ?>
                                                </td>
                                                <td class="text-end pe-4">
                                                    <div class="d-inline-flex gap-2">
                                                        <button class="btn btn-action btn-soft-primary" title="Edit" onclick="editFinance(<?php echo htmlspecialchars(json_encode($tr)); ?>)">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button class="btn btn-action btn-soft-danger" title="Hapus" onclick="deleteItem('finance', <?php echo $tr['id']; ?>)">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <style>
            @media print {
                .no-print, .sidebar, .mcm-bottom-nav, .d-md-none, .btn-action { display: none !important; }
                .main-content { margin: 0 !important; padding: 0 !important; width: 100% !important; }
                .card { border: 1px solid #eee !important; box-shadow: none !important; }
                body { background: white !important; }
            }
            .main-content { overflow-y: auto !important; height: 100vh; }
        </style>
