<!-- FINANCE PAGE (MANAJEMEN KEUANGAN & LABA BERSIH) -->
<?php
$finYear = isset($_GET['fin_year']) ? preg_replace('/[^0-9]/', '', $_GET['fin_year']) : date('Y');

// Auto-create & upgrade table
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS `finance_transactions` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `type` ENUM('in','out') NOT NULL,
      `category` varchar(50) NOT NULL,
      `item_name` varchar(255) DEFAULT NULL,
      `quantity` int(11) NOT NULL DEFAULT 1,
      `unit_price` int(11) NOT NULL DEFAULT 0,
      `amount` int(11) NOT NULL,
      `description` text DEFAULT NULL,
      `receipt_image` varchar(255) DEFAULT NULL,
      `order_id` int(11) DEFAULT NULL,
      `transaction_date` date NOT NULL,
      `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    $cols = $pdo->query("SHOW COLUMNS FROM finance_transactions")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('item_name', $cols)) $pdo->exec("ALTER TABLE finance_transactions ADD COLUMN item_name VARCHAR(255) DEFAULT NULL");
    if (!in_array('quantity', $cols)) $pdo->exec("ALTER TABLE finance_transactions ADD COLUMN quantity INT(11) NOT NULL DEFAULT 1");
    if (!in_array('unit_price', $cols)) $pdo->exec("ALTER TABLE finance_transactions ADD COLUMN unit_price INT(11) NOT NULL DEFAULT 0");
    if (!in_array('receipt_image', $cols)) $pdo->exec("ALTER TABLE finance_transactions ADD COLUMN receipt_image VARCHAR(255) DEFAULT NULL");
    if (!in_array('order_id', $cols)) $pdo->exec("ALTER TABLE finance_transactions ADD COLUMN order_id INT(11) DEFAULT NULL");

    // Automatic Realtime Sync: Sync all paid orders from orders table into finance_transactions
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
} catch (PDOException $e) {}

$finRows = [];
$totalIn = 0;
$totalOut = 0;

try {
    if ($finYear === '' || $finYear === 'all') {
        $stmt = $pdo->query("SELECT * FROM finance_transactions ORDER BY transaction_date DESC, id DESC");
    } else {
        $stmt = $pdo->prepare("SELECT * FROM finance_transactions WHERE YEAR(transaction_date) = ? ORDER BY transaction_date DESC, id DESC");
        $stmt->execute([$finYear]);
    }
    $finRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($finRows as $tr) {
        if ($tr['type'] === 'in') {
            $totalIn += (int)$tr['amount'];
        } else {
            $totalOut += (int)$tr['amount'];
        }
    }
} catch (PDOException $e) {}

// Laba Bersih & Margin
$netProfit = $totalIn - $totalOut;
$profitMargin = $totalIn > 0 ? round(($netProfit / $totalIn) * 100, 1) : 0;

// Rekap Pengeluaran & Pemasukan per Kategori
$catRecap = [];
foreach ($finRows as $tr) {
    $cat = $tr['category'] ?: 'lainnya';
    $key = $tr['type'] . '|' . $cat;
    if (!isset($catRecap[$key])) {
        $catRecap[$key] = [
            'type' => $tr['type'],
            'category' => $cat,
            'total' => 0,
            'count' => 0
        ];
    }
    $catRecap[$key]['total'] += (int)$tr['amount'];
    $catRecap[$key]['count']++;
}
usort($catRecap, function($a, $b) { return strcmp($a['type'], $b['type']) ?: $b['total'] <=> $a['total']; });

// Monthly breakdown for visual chart
$monthlyData = [];
for ($m = 1; $m <= 12; $m++) {
    $monthlyData[$m] = ['in' => 0, 'out' => 0];
}
foreach ($finRows as $tr) {
    $m = (int)date('n', strtotime($tr['transaction_date']));
    if (isset($monthlyData[$m])) {
        if ($tr['type'] === 'in') $monthlyData[$m]['in'] += (int)$tr['amount'];
        else $monthlyData[$m]['out'] += (int)$tr['amount'];
    }
}
$monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
$chartIn = [];
$chartOut = [];
for ($m = 1; $m <= 12; $m++) {
    $chartIn[] = $monthlyData[$m]['in'];
    $chartOut[] = $monthlyData[$m]['out'];
}
?>

<div class="row align-items-center mb-4 g-3 no-print" data-aos="fade-down">
    <div class="col-lg-5">
        <h2 class="fw-bold mb-1 text-dark">Keuangan &amp; Laba Bersih</h2>
        <p class="text-muted mb-0">Kelola arus kas, pembelian barang, pendapatan kotor &amp; laba bersih.</p>
    </div>
    <div class="col-lg-7 text-lg-end">
        <div class="d-flex flex-wrap justify-content-lg-end gap-2 align-items-center">
            <!-- Filter Tahun -->
            <select id="financeYear" class="form-select rounded-pill border-primary text-primary fw-bold" style="width: auto; height: 42px;" onchange="location.href='?page=finance&fin_year=' + this.value">
                <option value="all" <?php echo $finYear === 'all' ? 'selected' : ''; ?>>Semua Tahun</option>
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

            <!-- Tombol Sinkronisasi Pesanan Lunas -->
            <button class="btn btn-outline-success rounded-pill px-3 fw-bold shadow-sm d-flex align-items-center gap-2" style="height: 42px;" onclick="syncPaidOrders()" title="Tarik data transaksi pesanan lunas ke catatan pemasukan otomatis">
                <i class="fas fa-sync-alt"></i> <span>Sinkronkan Pesanan Lunas</span>
            </button>

            <!-- Tombol Tambah Transaksi -->
            <button class="btn btn-primary px-3 rounded-pill fw-bold shadow-sm d-flex align-items-center gap-2" style="height: 42px;" onclick="resetFinanceForm(); showModal('financeModal');">
                <i class="fas fa-plus-circle"></i> <span>Tambah Transaksi</span>
            </button>

            <!-- Tombol Cetak -->
            <a class="btn btn-soft-primary px-3 rounded-pill fw-bold d-flex align-items-center gap-2" href="finance_report_print.php?year=<?php echo htmlspecialchars($finYear); ?>" target="_blank" style="height: 42px; text-decoration: none;">
                <i class="fas fa-print"></i> <span>Cetak</span>
            </a>
        </div>
    </div>
</div>

<!-- 4 Key Financial KPI Cards -->
<div class="row g-4 mb-4">
    <!-- Pendapatan Kotor -->
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white border-top border-success border-4">
            <div class="d-flex align-items-center mb-3">
                <div class="bg-success bg-opacity-10 p-3 rounded-circle me-3 text-success">
                    <i class="fas fa-arrow-down fs-5"></i>
                </div>
                <div>
                    <h6 class="text-muted small text-uppercase fw-bold mb-0">Pendapatan Kotor</h6>
                    <small class="text-success fw-semibold">Total Uang Masuk</small>
                </div>
            </div>
            <h3 class="fw-bold text-success mb-1">Rp <?php echo number_format($totalIn, 0, ',', '.'); ?></h3>
            <p class="small text-muted mb-0">Kursus, sewa &amp; pemasukan lain</p>
        </div>
    </div>

    <!-- Total Uang Terpakai / Pengeluaran -->
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white border-top border-danger border-4">
            <div class="d-flex align-items-center mb-3">
                <div class="bg-danger bg-opacity-10 p-3 rounded-circle me-3 text-danger">
                    <i class="fas fa-shopping-cart fs-5"></i>
                </div>
                <div>
                    <h6 class="text-muted small text-uppercase fw-bold mb-0">Uang Terpakai</h6>
                    <small class="text-danger fw-semibold">Total Pengeluaran</small>
                </div>
            </div>
            <h3 class="fw-bold text-danger mb-1">Rp <?php echo number_format($totalOut, 0, ',', '.'); ?></h3>
            <p class="small text-muted mb-0">Beli barang, bahan, operasional</p>
        </div>
    </div>

    <!-- Laba Bersih (Net Profit) -->
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white border-top <?php echo $netProfit >= 0 ? 'border-primary' : 'border-warning'; ?> border-4">
            <div class="d-flex align-items-center mb-3">
                <div class="<?php echo $netProfit >= 0 ? 'bg-primary text-primary' : 'bg-warning text-warning'; ?> bg-opacity-10 p-3 rounded-circle me-3">
                    <i class="fas fa-wallet fs-5"></i>
                </div>
                <div>
                    <h6 class="text-muted small text-uppercase fw-bold mb-0">Laba Bersih</h6>
                    <small class="<?php echo $netProfit >= 0 ? 'text-primary' : 'text-warning'; ?> fw-semibold">Saldo Akhir / Profit</small>
                </div>
            </div>
            <h3 class="fw-bold <?php echo $netProfit >= 0 ? 'text-primary' : 'text-danger'; ?> mb-1">
                Rp <?php echo number_format($netProfit, 0, ',', '.'); ?>
            </h3>
            <p class="small mb-0 <?php echo $netProfit >= 0 ? 'text-success' : 'text-danger'; ?>">
                <i class="fas <?php echo $netProfit >= 0 ? 'fa-check-circle' : 'fa-exclamation-triangle'; ?> me-1"></i>
                <?php echo $netProfit >= 0 ? 'Surplus / Menguntungkan' : 'Defisit Operasional'; ?>
            </p>
        </div>
    </div>

    <!-- Margin Keuntungan -->
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white border-top border-info border-4">
            <div class="d-flex align-items-center mb-3">
                <div class="bg-info bg-opacity-10 p-3 rounded-circle me-3 text-info">
                    <i class="fas fa-percentage fs-5"></i>
                </div>
                <div>
                    <h6 class="text-muted small text-uppercase fw-bold mb-0">Margin Laba</h6>
                    <small class="text-info fw-semibold">Rasio Bersih / Kotor</small>
                </div>
            </div>
            <h3 class="fw-bold text-dark mb-1"><?php echo $profitMargin; ?>%</h3>
            <div class="progress mt-2" style="height: 6px; border-radius: 10px;">
                <div class="progress-bar bg-info" style="width: <?php echo max(0, min(100, $profitMargin)); ?>%"></div>
            </div>
        </div>
    </div>
</div>

<!-- Charts & Category Recap Section -->
<div class="row g-4 mb-4">
    <!-- Visual Financial Chart -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <div>
                    <h5 class="fw-bold mb-1 text-dark">Grafik Arus Kas Bulanan</h5>
                    <p class="small text-muted mb-0">Perbandingan uang masuk vs uang terpakai <?php echo $finYear === 'all' ? 'sepanjang waktu' : 'tahun ' . $finYear; ?></p>
                </div>
                <div class="d-flex gap-2">
                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2"><i class="fas fa-circle me-1 small"></i> Pemasukan</span>
                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-2"><i class="fas fa-circle me-1 small"></i> Pengeluaran</span>
                </div>
            </div>
            <div style="height: 300px;">
                <canvas id="financeMonthlyChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Breakdown per Kategori -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
            <h6 class="text-muted small text-uppercase fw-bold mb-3">Rekapitulasi per Kategori</h6>
            <div class="overflow-auto" style="max-height: 300px;">
                <?php if (empty($catRecap)): ?>
                    <div class="text-center py-5 text-muted small">
                        <i class="fas fa-receipt fs-3 d-block mb-2 opacity-50"></i>Belum ada transaksi pada periode ini.
                    </div>
                <?php else: ?>
                    <?php foreach ($catRecap as $cr): ?>
                        <div class="p-3 mb-2 rounded-3 border bg-light">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div>
                                    <span class="badge <?php echo $cr['type'] === 'in' ? 'bg-success' : 'bg-danger'; ?> rounded-pill px-2 py-1 small me-1" style="font-size: 0.65rem;">
                                        <?php echo $cr['type'] === 'in' ? 'Masuk' : 'Keluar'; ?>
                                    </span>
                                    <span class="small fw-bold text-dark"><?php echo ucwords(str_replace('_', ' ', $cr['category'])); ?></span>
                                </div>
                                <span class="badge bg-white text-dark border rounded-pill"><?php echo $cr['count']; ?>x</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <small class="text-muted">Total Nominal:</small>
                                <span class="small fw-bold <?php echo $cr['type'] === 'in' ? 'text-success' : 'text-danger'; ?>">
                                    Rp <?php echo number_format($cr['total'], 0, ',', '.'); ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Transactions Table -->
<div data-bulk-table="finance">
<div class="admin-table-toolbar d-none" data-bulk-toolbar>
    <div class="small fw-bold text-primary"><i class="fas fa-check-square me-1"></i><span data-bulk-count>0 dipilih</span></div>
    <button type="button" class="btn btn-sm btn-danger rounded-pill px-3 fw-bold" data-bulk-delete><i class="fas fa-trash me-1"></i>Hapus Terpilih (<span data-bulk-count-num>0</span>)</button>
</div>
<div class="card border-0 shadow-sm rounded-4 p-0 bg-white mb-4 overflow-hidden">
    <div class="d-flex justify-content-between align-items-center p-3 flex-wrap gap-2 border-bottom bg-light">
        <div>
            <h5 class="fw-bold text-dark mb-0" style="font-size:0.95rem;">Daftar Transaksi &amp; Pengeluaran</h5>
            <p class="small text-muted mb-0" style="font-size:0.75rem;">Rincian pembelian &amp; pemasukan — kolom digabung agar tidak scroll.</p>
        </div>
        <span class="badge bg-light text-secondary rounded-pill px-3 py-2 border small"><?php echo count($finRows); ?> Transaksi</span>
    </div>

    <div class="table-responsive table-responsive--no-scroll">
        <table class="table align-middle admin-compact table-hover mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="col-check"><input type="checkbox" class="bulk-select-all js-bulk-select-all"></th>
                    <th class="text-center" style="width:36px;">No</th>
                    <th>Tanggal</th>
                    <th>Keterangan</th>
                    <th class="text-center">Qty</th>
                    <th class="text-end">Nominal</th>
                    <th class="text-center">Nota</th>
                    <th class="text-end pe-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($finRows)): ?>
                    <tr><td colspan="8" class="text-center py-5 text-muted small">Belum ada data transaksi keuangan tercatat.</td></tr>
                <?php else: ?>
                    <?php foreach ($finRows as $idx => $tr): ?>
                        <tr>
                            <td class="col-check"><input type="checkbox" class="bulk-row-check js-bulk-row" value="<?php echo (int)$tr['id']; ?>"></td>
                            <td class="text-center text-muted small"><?php echo $idx + 1; ?></td>
                            <td class="text-nowrap small">
                                <div class="fw-semibold text-dark" style="font-size:0.8rem;"><?php echo date('d M y', strtotime($tr['transaction_date'])); ?></div>
                                <?php if ($tr['type'] === 'in'): ?>
                                    <span class="badge bg-success bg-opacity-10 text-success" style="font-size:0.62rem;"><i class="fas fa-arrow-down me-1"></i>Masuk</span>
                                <?php else: ?>
                                    <span class="badge bg-danger bg-opacity-10 text-danger" style="font-size:0.62rem;"><i class="fas fa-arrow-up me-1"></i>Keluar</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="cell-stack" style="max-width:280px;">
                                    <span class="line-main" title="<?php echo htmlspecialchars($tr['item_name'] ?: $tr['category']); ?>"><?php echo htmlspecialchars($tr['item_name'] ?: ucwords(str_replace('_',' ',$tr['category']))); ?></span>
                                    <span class="d-flex align-items-center gap-1"><span class="badge bg-light text-dark border" style="font-size:0.62rem;"><?php echo ucwords(str_replace('_',' ',$tr['category'])); ?></span></span>
                                    <?php if (!empty($tr['description'])): ?>
                                        <span class="line-sub" title="<?php echo htmlspecialchars($tr['description']); ?>"><?php echo htmlspecialchars(mb_strimwidth($tr['description'],0,80,'...')); ?></span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="text-center">
                                <?php if ($tr['quantity'] > 1 || $tr['unit_price'] > 0): ?>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary" style="font-size:0.68rem;"><?php echo $tr['quantity']; ?>×<?php echo $tr['unit_price']>0 ? ' Rp'.number_format($tr['unit_price'],0,',','.') : ''; ?></span>
                                <?php else: ?>
                                    <span class="text-muted small">1</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end fw-bold text-nowrap small <?php echo $tr['type'] === 'in' ? 'text-success' : 'text-danger'; ?>">
                                <?php echo $tr['type']==='in'?'+':'-'; ?>Rp <?php echo number_format($tr['amount'],0,',','.'); ?>
                            </td>
                            <td class="text-center">
                                <?php if (!empty($tr['receipt_image'])): ?>
                                    <a href="../<?php echo htmlspecialchars($tr['receipt_image']); ?>" target="_blank" class="btn btn-sm btn-outline-info rounded-pill px-2 py-1" style="font-size:0.7rem;" title="Lihat Nota"><i class="fas fa-file-invoice me-1"></i>Nota</a>
                                <?php else: ?>
                                    <span class="text-muted small">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end pe-3">
                                <div class="d-inline-flex gap-1">
                                    <button class="btn btn-action btn-soft-primary btn-sm" onclick="editFinance(<?php echo htmlspecialchars(json_encode($tr)); ?>)" title="Edit"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-action btn-soft-danger btn-sm" onclick="deleteItem('finance', <?php echo $tr['id']; ?>)" title="Hapus"><i class="fas fa-trash"></i></button>
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

<script>
function syncPaidOrders() {
    Swal.fire({
        title: 'Sinkronkan Pesanan Lunas?',
        text: 'Sistem akan otomatis mendata seluruh pesanan lunas ke catatan pemasukan keuangan.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Sinkronkan!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Sedang memproses...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            const formData = new FormData();
            formData.append('action', 'sync_orders');

            fetch('actions/manage_finance.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.text()).then(t=>{ let d; try{ d=JSON.parse(t);}catch(e){ throw new Error('Respons tidak valid: '+t.slice(0,120)); } return d; })
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({ icon:'success', title:'Berhasil!', text:data.message, timer:1500, showConfirmButton:false }).then(() => {
                        if (typeof window.mcmCloseModalsAndRefresh==='function') window.mcmCloseModalsAndRefresh();
                        else if (typeof loadContent==='function'){ loadContent('?page=finance', false); }
                    });
                } else {
                    Swal.fire('Gagal!', data.message || 'Gagal', 'error');
                }
            })
            .catch(err => Swal.fire('Gagal!', err.message || 'Terjadi kesalahan saat memproses data.', 'error'));
        }
    });
}

// Dipanggil ulang kapan saja: full page load maupun setelah AJAX nav meng-inject konten.
// Data chart di-inject PHP di atas (bukan fetch) -> tidak ada race condition data.
function initGrafikArusKas() {
    const canvasEl = document.getElementById('financeMonthlyChart');
    if (!canvasEl || typeof Chart === 'undefined') return;
    // Hancurkan instance lama pada canvas yang sama agar tidak "Canvas is already in use"
    if (window._cashflowChart) {
        window._cashflowChart.destroy();
        window._cashflowChart = null;
    }
    const monthLabels = <?php echo json_encode($monthLabels); ?>;
    const chartIn = <?php echo json_encode($chartIn); ?>;
    const chartOut = <?php echo json_encode($chartOut); ?>;

    window._cashflowChart = new Chart(canvasEl.getContext('2d'), {
        type: 'bar',
        data: {
            labels: monthLabels,
            datasets: [
                {
                    label: 'Pemasukan (Uang Masuk)',
                    data: chartIn,
                    backgroundColor: 'rgba(22, 163, 74, 0.85)',
                    borderRadius: 6,
                },
                {
                    label: 'Pengeluaran (Uang Terpakai)',
                    data: chartOut,
                    backgroundColor: 'rgba(220, 38, 38, 0.85)',
                    borderRadius: 6,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + (value >= 1000000 ? (value / 1000000) + ' Jt' : (value / 1000) + ' Rb');
                        }
                    },
                    grid: { color: 'rgba(0,0,0,0.05)' }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
}

// (a) Full page load / refresh: script berada SETELAH <canvas> di dokumen,
//     jadi elemen sudah ada — panggil langsung tanpa menunggu DOMContentLoaded.
initGrafikArusKas();
</script>
