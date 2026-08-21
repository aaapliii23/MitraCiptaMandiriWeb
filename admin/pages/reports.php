<!-- REPORTS PAGE -->
<?php
$reportYear = $_GET['year'] ?? date('Y');
$filterClassId = (int)($_GET['class_id'] ?? 0);

// Fetch All Classes for Dropdown
$allClasses = [];
try {
    $allClasses = $pdo->query("SELECT id, name, category, price FROM classes ORDER BY name ASC")->fetchAll();
} catch (PDOException $e) {}

// Build Dynamic SQL Filter
$where = " WHERE 1=1";
$params = [];

if ($reportYear !== 'all' && !empty($reportYear)) {
    $where .= " AND YEAR(o.created_at) = :year";
    $params[':year'] = (int)$reportYear;
}
if ($filterClassId > 0) {
    $where .= " AND o.class_id = :cid";
    $params[':cid'] = $filterClassId;
}

// 1. Total Estimasi Omzet (Lunas)
$stmtRev = $pdo->prepare("SELECT COALESCE(SUM(o.amount), 0) FROM orders o JOIN classes c ON o.class_id = c.id $where AND o.payment_status = 'paid'");
$stmtRev->execute($params);
$revenue = (int)$stmtRev->fetchColumn();

// 2. Total Orders
$stmtTotal = $pdo->prepare("SELECT COUNT(o.id) FROM orders o JOIN classes c ON o.class_id = c.id $where");
$stmtTotal->execute($params);
$totalOrders = (int)$stmtTotal->fetchColumn();

// 3. Status Counts
$stmtPending = $pdo->prepare("SELECT COUNT(o.id) FROM orders o JOIN classes c ON o.class_id = c.id $where AND o.status = 'pending'");
$stmtPending->execute($params);
$pendingCount = (int)$stmtPending->fetchColumn();

$stmtConfirmed = $pdo->prepare("SELECT COUNT(o.id) FROM orders o JOIN classes c ON o.class_id = c.id $where AND o.status = 'confirmed'");
$stmtConfirmed->execute($params);
$confirmedCount = (int)$stmtConfirmed->fetchColumn();

$stmtCancelled = $pdo->prepare("SELECT COUNT(o.id) FROM orders o JOIN classes c ON o.class_id = c.id $where AND o.status = 'cancelled'");
$stmtCancelled->execute($params);
$cancelledCount = (int)$stmtCancelled->fetchColumn();

// 4. Payment Status Counts
$stmtPaid = $pdo->prepare("SELECT COUNT(o.id) FROM orders o JOIN classes c ON o.class_id = c.id $where AND o.payment_status = 'paid'");
$stmtPaid->execute($params);
$paidCount = (int)$stmtPaid->fetchColumn();

$stmtUnpaid = $pdo->prepare("SELECT COUNT(o.id) FROM orders o JOIN classes c ON o.class_id = c.id $where AND o.payment_status IN ('unpaid','pending')");
$stmtUnpaid->execute($params);
$unpaidCount = (int)$stmtUnpaid->fetchColumn();

$stmtFailed = $pdo->prepare("SELECT COUNT(o.id) FROM orders o JOIN classes c ON o.class_id = c.id $where AND o.payment_status = 'failed'");
$stmtFailed->execute($params);
$failedCount = (int)$stmtFailed->fetchColumn();

$stmtExpired = $pdo->prepare("SELECT COUNT(o.id) FROM orders o JOIN classes c ON o.class_id = c.id $where AND o.payment_status = 'expired'");
$stmtExpired->execute($params);
$expiredCount = (int)$stmtExpired->fetchColumn();

$conversionRate = $totalOrders > 0 ? round(($confirmedCount / $totalOrders) * 100) : 0;

// Scope Label
$scopeLabel = "Semua Pelatihan";
if ($filterClassId > 0) {
    foreach ($allClasses as $cl) {
        if ($cl['id'] == $filterClassId) {
            $scopeLabel = "Pelatihan: " . $cl['name'];
            break;
        }
    }
}
?>

<div class="row align-items-center mb-4 g-3 no-print" data-aos="fade-down">
    <div class="col-lg-5">
        <h2 class="fw-bold mb-1 text-dark">Laporan &amp; Analitik</h2>
        <p class="text-muted mb-0">Visualisasi data pendaftaran dan pertumbuhan MCM.</p>
    </div>
    <div class="col-lg-7 text-lg-end">
        <div class="d-flex flex-wrap justify-content-lg-end gap-2 align-items-center">
            <!-- Filter Pelatihan -->
            <select id="classFilter" class="form-select rounded-pill border-secondary border-opacity-25 fw-semibold" style="height: 42px; width: auto; min-width: 200px;" onchange="applyReportFilter()">
                <option value="0" <?php echo $filterClassId === 0 ? 'selected' : ''; ?>>Semua Pelatihan</option>
                <?php foreach ($allClasses as $cl): ?>
                    <option value="<?php echo $cl['id']; ?>" <?php echo $filterClassId == $cl['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cl['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <!-- Filter Tahun -->
            <select id="reportYear" class="form-select rounded-pill border-primary text-primary fw-bold" style="height: 42px; width: auto;" onchange="applyReportFilter()">
                <option value="all" <?php echo $reportYear === 'all' ? 'selected' : ''; ?>>Semua Tahun</option>
                <?php 
                $curY = date('Y');
                for($i = $curY; $i >= $curY - 5; $i--) {
                    $sel = ($reportYear == $i) ? 'selected' : '';
                    echo "<option value='$i' $sel>Tahun $i</option>";
                }
                ?>
            </select>

            <!-- Tombol Cetak -->
            <button class="btn btn-soft-primary px-4 rounded-pill fw-bold d-flex align-items-center gap-2" style="height: 42px;" onclick="printFilteredReport()">
                <i class="fas fa-print"></i> <span>Cetak</span>
            </button>
        </div>
    </div>
</div>

<!-- Key Stat Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
            <div class="d-flex align-items-center mb-3">
                <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-3"><i class="fas fa-wallet text-primary"></i></div>
                <h6 class="text-muted small text-uppercase fw-bold mb-0">Total Estimasi Omzet</h6>
            </div>
            <h3 class="fw-bold text-dark mb-1">Rp <?php echo number_format($revenue, 0, ',', '.'); ?></h3>
            <p class="small text-success mb-0"><i class="fas fa-arrow-up me-1"></i>Dari pembayaran lunas</p>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
            <div class="d-flex align-items-center mb-3">
                <div class="bg-warning bg-opacity-10 p-2 rounded-3 me-3"><i class="fas fa-clock text-warning"></i></div>
                <h6 class="text-muted small text-uppercase fw-bold mb-0">Pesanan Pending</h6>
            </div>
            <h3 class="fw-bold text-dark mb-1"><?php echo $pendingCount; ?></h3>
            <p class="small text-muted mb-0">Menunggu konfirmasi</p>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
            <div class="d-flex align-items-center mb-3">
                <div class="bg-success bg-opacity-10 p-2 rounded-3 me-3"><i class="fas fa-user-check text-success"></i></div>
                <h6 class="text-muted small text-uppercase fw-bold mb-0">Konversi Peserta</h6>
            </div>
            <h3 class="fw-bold text-dark mb-1"><?php echo $conversionRate; ?>%</h3>
            <div class="progress mt-2" style="height: 6px; border-radius: 10px;">
                <div class="progress-bar bg-success" style="width: <?php echo $conversionRate; ?>%"></div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
            <div class="d-flex align-items-center mb-3">
                <div class="bg-danger bg-opacity-10 p-2 rounded-3 me-3"><i class="fas fa-user-times text-danger"></i></div>
                <h6 class="text-muted small text-uppercase fw-bold mb-0">Pembatalan</h6>
            </div>
            <h3 class="fw-bold text-dark mb-1"><?php echo $cancelledCount; ?></h3>
            <p class="small text-danger mb-0">Pesanan dibatalkan</p>
        </div>
    </div>
</div>

<!-- Status Pembayaran Breakdown -->
<div class="row g-4 mb-4">
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white border-start border-success border-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted small text-uppercase fw-bold mb-1">Pembayaran Lunas</h6>
                    <h4 class="fw-bold text-success mb-0"><?php echo $paidCount; ?></h4>
                </div>
                <div class="bg-success bg-opacity-10 p-3 rounded-circle text-success"><i class="fas fa-check"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white border-start border-warning border-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted small text-uppercase fw-bold mb-1">Belum Dibayar</h6>
                    <h4 class="fw-bold text-warning mb-0"><?php echo $unpaidCount; ?></h4>
                </div>
                <div class="bg-warning bg-opacity-10 p-3 rounded-circle text-warning"><i class="fas fa-hourglass-half"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white border-start border-danger border-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted small text-uppercase fw-bold mb-1">Pembayaran Gagal</h6>
                    <h4 class="fw-bold text-danger mb-0"><?php echo $failedCount; ?></h4>
                </div>
                <div class="bg-danger bg-opacity-10 p-3 rounded-circle text-danger"><i class="fas fa-times"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white border-start border-secondary border-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted small text-uppercase fw-bold mb-1">Kedaluwarsa</h6>
                    <h4 class="fw-bold text-secondary mb-0"><?php echo $expiredCount; ?></h4>
                </div>
                <div class="bg-secondary bg-opacity-10 p-3 rounded-circle text-secondary"><i class="fas fa-clock"></i></div>
            </div>
        </div>
    </div>
</div>

<!-- Chart & Breakdown Section -->
<div class="row g-4 mb-4">
    <!-- Chart Column -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <div>
                    <h5 class="fw-bold mb-1 text-dark">Grafik Pendaftaran</h5>
                    <p class="small text-muted mb-0"><?php echo htmlspecialchars($scopeLabel); ?></p>
                </div>
                <div class="btn-group btn-group-sm rounded-pill overflow-hidden border">
                    <button class="btn btn-light px-3 active chart-toggle" onclick="updateChart('weekly', this)">Mingguan</button>
                    <button class="btn btn-light px-3 chart-toggle" onclick="updateChart('monthly', this)">Bulanan</button>
                    <button class="btn btn-light px-3 chart-toggle" onclick="updateChart('yearly', this)">Tahunan</button>
                </div>
            </div>
            <div style="height: 330px;">
                <canvas id="reportsChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Class Breakdown / Popular Column -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="text-muted small text-uppercase fw-bold mb-0">Program Terpopuler</h6>
                <small class="text-muted" style="font-size: 0.7rem;">Klik tombol peserta</small>
            </div>
            <div class="overflow-auto" style="max-height: 330px;">
                <?php 
                $sqlPopular = "SELECT c.id, c.name, c.category, c.price, COUNT(o.id) as total_order, 
                               SUM(CASE WHEN o.payment_status = 'paid' THEN 1 ELSE 0 END) as paid_order,
                               COALESCE(SUM(CASE WHEN o.payment_status = 'paid' THEN o.amount ELSE 0 END), 0) as omzet
                               FROM classes c 
                               LEFT JOIN orders o ON c.id = o.class_id";
                if ($reportYear !== 'all' && !empty($reportYear)) {
                    $sqlPopular .= " AND YEAR(o.created_at) = " . (int)$reportYear;
                }
                $sqlPopular .= " WHERE 1=1";
                if ($filterClassId > 0) {
                    $sqlPopular .= " AND c.id = " . (int)$filterClassId;
                }
                $sqlPopular .= " GROUP BY c.id ORDER BY total_order DESC, omzet DESC";
                
                $popularClasses = $pdo->query($sqlPopular)->fetchAll();
                ?>
                <?php if (empty($popularClasses)): ?>
                    <div class="text-center py-4 text-muted small">Tidak ada data pelatihan sesuai filter.</div>
                <?php else: ?>
                    <?php foreach ($popularClasses as $idx => $p): ?>
                        <div class="p-3 mb-2 rounded-3 border bg-light transition-all">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-bold text-dark small text-truncate me-2" style="max-width: 150px;" title="<?php echo htmlspecialchars($p['name']); ?>">
                                    <?php echo $idx + 1; ?>. <?php echo htmlspecialchars($p['name']); ?>
                                </span>
                                <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 py-1 fw-bold shadow-sm d-inline-flex align-items-center gap-1" style="font-size: 0.78rem; transition: transform 0.2s ease;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'" onclick="showClassParticipants(<?php echo $p['id']; ?>, '<?php echo htmlspecialchars(addslashes($p['name'])); ?>')" title="Klik untuk melihat daftar peserta terdaftar">
                                    <i class="fas fa-users" style="font-size: 0.7rem;"></i> <?php echo $p['total_order']; ?> Peserta
                                </button>
                            </div>
                            <div class="d-flex justify-content-between align-items-center text-muted small" style="font-size: 0.75rem;">
                                <span>Lunas: <strong class="text-success"><?php echo $p['paid_order']; ?></strong></span>
                                <span class="text-primary fw-bold">Rp <?php echo number_format($p['omzet'], 0, ',', '.'); ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Recent Orders Table under Filter -->
<div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold text-dark mb-0">Rincian Transaksi Sesuai Filter</h5>
        <span class="badge bg-light text-secondary rounded-pill"><?php echo $totalOrders; ?> Transaksi Ditemukan</span>
    </div>
    <div class="table-responsive rounded-4 border">
        <table class="table align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-3 py-2 small">No. Order</th>
                    <th class="py-2 small">Nama Peserta</th>
                    <th class="py-2 small">Program Pelatihan</th>
                    <th class="py-2 small">Nominal</th>
                    <th class="py-2 small text-center">Status Bayar</th>
                    <th class="py-2 small">Tanggal</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $sqlOrders = "SELECT o.*, c.name as class_name, c.category as class_category 
                              FROM orders o JOIN classes c ON o.class_id = c.id $where ORDER BY o.created_at DESC LIMIT 15";
                $stmtOrd = $pdo->prepare($sqlOrders);
                $stmtOrd->execute($params);
                $filteredOrders = $stmtOrd->fetchAll();
                ?>
                <?php if (empty($filteredOrders)): ?>
                    <tr><td colspan="6" class="text-center py-4 text-muted small">Tidak ada transaksi ditemukan pada filter ini.</td></tr>
                <?php else: ?>
                    <?php foreach ($filteredOrders as $ord): ?>
                        <tr>
                            <td class="ps-3 py-2"><code><?php echo htmlspecialchars($ord['order_number']); ?></code></td>
                            <td class="py-2 fw-semibold text-dark"><?php echo htmlspecialchars($ord['customer_name']); ?></td>
                            <td class="py-2">
                                <span class="badge bg-light text-dark border"><?php echo htmlspecialchars($ord['class_name']); ?></span>
                            </td>
                            <td class="py-2 fw-bold text-primary">Rp <?php echo number_format($ord['amount'], 0, ',', '.'); ?></td>
                            <td class="py-2 text-center">
                                <?php if ($ord['payment_status'] === 'paid'): ?>
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1">Lunas</span>
                                <?php elseif ($ord['payment_status'] === 'failed'): ?>
                                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-2 py-1">Gagal</span>
                                <?php else: ?>
                                    <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-2 py-1"><?php echo htmlspecialchars($ord['payment_status']); ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="py-2 text-muted small"><?php echo date('d M Y, H:i', strtotime($ord['created_at'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Daftar Peserta Pelatihan Tertentu -->
<div class="modal fade" id="classParticipantsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-5 overflow-hidden">
            <div class="modal-header border-0 bg-light px-4 py-3 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <div class="bg-primary text-white p-2 rounded-3">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="classParticipantsModalTitle">Daftar Peserta Pelatihan</h5>
                        <small class="text-muted" id="classParticipantsModalSubtitle">Memuat data peserta...</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Search Box inside Modal -->
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <div class="input-group" style="max-width: 320px;">
                        <span class="input-group-text bg-white border-end-0 rounded-start-pill"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" id="searchParticipantInput" class="form-control border-start-0 rounded-end-pill" placeholder="Cari nama, WA, atau no order..." onkeyup="filterParticipantsTable()">
                    </div>
                    <div id="participantCountBadge"></div>
                </div>

                <!-- Table inside Modal -->
                <div class="table-responsive rounded-4 border">
                    <table class="table align-middle table-hover mb-0" id="participantsTable">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-3 py-2 small" style="width: 40px;">No</th>
                                <th class="py-2 small">No. Order</th>
                                <th class="py-2 small">Nama Peserta</th>
                                <th class="py-2 small">WhatsApp / Kontak</th>
                                <th class="py-2 small text-center">Status Bayar</th>
                                <th class="py-2 small text-end">Nominal</th>
                                <th class="py-2 small pe-3">Tanggal Daftar</th>
                            </tr>
                        </thead>
                        <tbody id="participantsTableBody">
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                                    Memuat data peserta...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-0 bg-light px-4 py-3">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    let mainChart = null;
    let currentChartType = 'weekly';
    const currentYear = '<?php echo $reportYear; ?>';
    const currentClassId = '<?php echo $filterClassId; ?>';
    let loadedParticipants = [];

    function applyReportFilter() {
        const classId = document.getElementById('classFilter').value;
        const yearVal = document.getElementById('reportYear').value;
        window.location.href = `?page=reports&year=${encodeURIComponent(yearVal)}&class_id=${encodeURIComponent(classId)}`;
    }

    function printFilteredReport() {
        const classId = document.getElementById('classFilter').value;
        const yearVal = document.getElementById('reportYear').value;
        window.open(`reports_print.php?year=${encodeURIComponent(yearVal)}&class_id=${encodeURIComponent(classId)}`, '_blank');
    }

    function showClassParticipants(classId, className) {
        document.getElementById('classParticipantsModalTitle').textContent = `Daftar Peserta: ${className}`;
        document.getElementById('classParticipantsModalSubtitle').textContent = `Periode ${currentYear === 'all' ? 'Semua Tahun' : 'Tahun ' + currentYear}`;
        document.getElementById('searchParticipantInput').value = '';
        
        const tbody = document.getElementById('participantsTableBody');
        tbody.innerHTML = `<tr><td colspan="7" class="text-center py-5 text-muted"><div class="spinner-border spinner-border-sm text-primary me-2"></div>Memuat data peserta...</td></tr>`;

        const modal = new bootstrap.Modal(document.getElementById('classParticipantsModal'));
        modal.show();

        fetch(`actions/get_class_participants.php?class_id=${encodeURIComponent(classId)}&year=${encodeURIComponent(currentYear)}`)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    loadedParticipants = data.participants || [];
                    document.getElementById('participantCountBadge').innerHTML = `<span class="badge bg-primary rounded-pill px-3 py-2">Total ${loadedParticipants.length} Peserta Terdaftar</span>`;
                    renderParticipantsTable(loadedParticipants);
                } else {
                    tbody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-danger">${data.message || 'Gagal memuat peserta.'}</td></tr>`;
                }
            })
            .catch(() => {
                tbody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-danger">Terjadi kesalahan saat memuat data.</td></tr>`;
            });
    }

    function renderParticipantsTable(list) {
        const tbody = document.getElementById('participantsTableBody');
        if (!list || list.length === 0) {
            tbody.innerHTML = `<tr><td colspan="7" class="text-center py-5 text-muted"><i class="fas fa-user-slash fs-3 d-block mb-2 opacity-50"></i>Belum ada peserta terdaftar pada pelatihan ini.</td></tr>`;
            return;
        }

        let html = '';
        list.forEach((p, idx) => {
            let badgeClass = 'bg-warning text-warning';
            let statusText = p.payment_status || 'unpaid';
            if (p.payment_status === 'paid') {
                badgeClass = 'bg-success text-success';
                statusText = 'Lunas';
            } else if (p.payment_status === 'failed') {
                badgeClass = 'bg-danger text-danger';
                statusText = 'Gagal';
            } else if (p.payment_status === 'expired') {
                badgeClass = 'bg-secondary text-secondary';
                statusText = 'Kedaluwarsa';
            }

            const cleanPhone = (p.customer_phone || '').replace(/[^0-9]/g, '');
            const waLink = cleanPhone ? `https://wa.me/${cleanPhone.startsWith('0') ? '62' + cleanPhone.substring(1) : cleanPhone}` : '#';

            const formattedAmount = new Intl.NumberFormat('id-ID').format(p.amount || 0);
            const dateObj = new Date(p.created_at);
            const formattedDate = dateObj.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });

            html += `
                <tr>
                    <td class="ps-3 py-3 text-muted small">${idx + 1}</td>
                    <td class="py-3"><code>${p.order_number}</code></td>
                    <td class="py-3 fw-bold text-dark">${p.customer_name}</td>
                    <td class="py-3">
                        ${cleanPhone ? `
                            <a href="${waLink}" target="_blank" class="btn btn-sm btn-outline-success rounded-pill px-2 py-0 text-decoration-none" title="Kirim Pesan WhatsApp">
                                <i class="fab fa-whatsapp me-1"></i>${p.customer_phone}
                            </a>
                        ` : '<span class="text-muted">-</span>'}
                    </td>
                    <td class="py-3 text-center">
                        <span class="badge ${badgeClass} bg-opacity-10 rounded-pill px-3 py-1">${statusText}</span>
                    </td>
                    <td class="py-3 text-end fw-bold text-primary">Rp ${formattedAmount}</td>
                    <td class="py-3 pe-3 text-muted small">${formattedDate}</td>
                </tr>
            `;
        });
        tbody.innerHTML = html;
    }

    function filterParticipantsTable() {
        const query = (document.getElementById('searchParticipantInput').value || '').toLowerCase().trim();
        if (!query) {
            renderParticipantsTable(loadedParticipants);
            return;
        }

        const filtered = loadedParticipants.filter(p => {
            const name = (p.customer_name || '').toLowerCase();
            const phone = (p.customer_phone || '').toLowerCase();
            const order = (p.order_number || '').toLowerCase();
            return name.includes(query) || phone.includes(query) || order.includes(query);
        });

        renderParticipantsTable(filtered);
    }

    function initReportsChart(type = 'weekly') {
        currentChartType = type;
        const ctx = document.getElementById('reportsChart').getContext('2d');
        
        fetch(`actions/reports_data.php?type=${type}&year=${encodeURIComponent(currentYear)}&class_id=${encodeURIComponent(currentClassId)}`)
            .then(res => res.json())
            .then(data => {
                if (mainChart) mainChart.destroy();
                
                const gradient = ctx.createLinearGradient(0, 0, 0, 350);
                gradient.addColorStop(0, 'rgba(37, 99, 235, 0.25)');
                gradient.addColorStop(1, 'rgba(37, 99, 235, 0)');

                mainChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.labels || [],
                        datasets: [{
                            label: 'Jumlah Pendaftaran',
                            data: data.counts || [],
                            borderColor: '#2563eb',
                            borderWidth: 3,
                            backgroundColor: gradient,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#fff',
                            pointBorderColor: '#2563eb',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
                            x: { grid: { display: false } }
                        }
                    }
                });
            });
    }

    function updateChart(type, btn) {
        document.querySelectorAll('.chart-toggle').forEach(b => b.classList.remove('active', 'btn-primary'));
        document.querySelectorAll('.chart-toggle').forEach(b => b.classList.add('btn-light'));
        btn.classList.add('active', 'btn-primary');
        btn.classList.remove('btn-light');
        initReportsChart(type);
    }

    document.addEventListener('DOMContentLoaded', () => initReportsChart('weekly'));
</script>
