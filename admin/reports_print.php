<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../auth/admin_login.php');
    exit;
}

require_once '../config/database.php';

$year = $_GET['year'] ?? date('Y');
$classId = (int)($_GET['class_id'] ?? 0);

// Fetch All Classes
$allClasses = [];
try {
    $allClasses = $pdo->query("SELECT id, name, category FROM classes ORDER BY name ASC")->fetchAll();
} catch (PDOException $e) {}

// Scope Label
$scopeTitle = "Semua Pelatihan (Keseluruhan)";
if ($classId > 0) {
    foreach ($allClasses as $cl) {
        if ($cl['id'] == $classId) {
            $scopeTitle = "Pelatihan: " . $cl['name'];
            break;
        }
    }
}

$periodLabel = ($year === 'all' || empty($year)) ? 'Semua Periode' : 'Tahun ' . $year;

// Build Filter SQL
$where = " WHERE 1=1";
$params = [];

if ($year !== 'all' && !empty($year)) {
    $where .= " AND YEAR(o.created_at) = :year";
    $params[':year'] = (int)$year;
}
if ($classId > 0) {
    $where .= " AND o.class_id = :cid";
    $params[':cid'] = $classId;
}

try {
    // 1. Total Revenue
    $stmtRev = $pdo->prepare("SELECT COALESCE(SUM(o.amount), 0) FROM orders o JOIN classes c ON o.class_id = c.id $where AND o.payment_status = 'paid'");
    $stmtRev->execute($params);
    $revenue = (int)$stmtRev->fetchColumn();

    // 2. Total Orders
    $stmtTotal = $pdo->prepare("SELECT COUNT(o.id) FROM orders o JOIN classes c ON o.class_id = c.id $where");
    $stmtTotal->execute($params);
    $totalOrders = (int)$stmtTotal->fetchColumn();

    // 3. Status
    $stmtPending = $pdo->prepare("SELECT COUNT(o.id) FROM orders o JOIN classes c ON o.class_id = c.id $where AND o.status = 'pending'");
    $stmtPending->execute($params);
    $pendingCount = (int)$stmtPending->fetchColumn();

    $stmtConfirmed = $pdo->prepare("SELECT COUNT(o.id) FROM orders o JOIN classes c ON o.class_id = c.id $where AND o.status = 'confirmed'");
    $stmtConfirmed->execute($params);
    $confirmedCount = (int)$stmtConfirmed->fetchColumn();

    $stmtCancelled = $pdo->prepare("SELECT COUNT(o.id) FROM orders o JOIN classes c ON o.class_id = c.id $where AND o.status = 'cancelled'");
    $stmtCancelled->execute($params);
    $cancelledCount = (int)$stmtCancelled->fetchColumn();

    // 4. Payment status
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

    // Program breakdown
    $sqlPopular = "SELECT c.id, c.name, c.category, COUNT(o.id) as total_order, 
                   SUM(CASE WHEN o.payment_status = 'paid' THEN 1 ELSE 0 END) as paid_order,
                   COALESCE(SUM(CASE WHEN o.payment_status = 'paid' THEN o.amount ELSE 0 END), 0) as omzet
                   FROM classes c 
                   LEFT JOIN orders o ON c.id = o.class_id";
    if ($year !== 'all' && !empty($year)) {
        $sqlPopular .= " AND YEAR(o.created_at) = " . (int)$year;
    }
    $sqlPopular .= " WHERE 1=1";
    if ($classId > 0) {
        $sqlPopular .= " AND c.id = " . (int)$classId;
    }
    $sqlPopular .= " GROUP BY c.id ORDER BY total_order DESC, omzet DESC";
    $classBreakdowns = $pdo->query($sqlPopular)->fetchAll();

    // Order items
    $sqlOrders = "SELECT o.*, c.name as class_name, c.category as class_category 
                  FROM orders o JOIN classes c ON o.class_id = c.id $where ORDER BY o.created_at DESC";
    $stmtOrd = $pdo->prepare($sqlOrders);
    $stmtOrd->execute($params);
    $ordersList = $stmtOrd->fetchAll();

} catch (PDOException $e) {
    $revenue = 0; $totalOrders = 0; $pendingCount = 0; $confirmedCount = 0; $cancelledCount = 0;
    $paidCount = 0; $unpaidCount = 0; $failedCount = 0; $expiredCount = 0;
    $classBreakdowns = []; $ordersList = [];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan - <?php echo htmlspecialchars($scopeTitle); ?> - MCM</title>
    <link rel="icon" type="image/png" href="../assets/img/logo.png">
    <link rel="shortcut icon" type="image/png" href="../assets/img/logo.png">
    <link rel="apple-touch-icon" href="../assets/img/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f1f5f9; color: #1e293b; }
        .report-sheet { background: white; max-width: 900px; margin: 30px auto 60px; box-shadow: 0 20px 50px rgba(0,0,0,0.08); border-radius: 1.25rem; overflow: hidden; }
        .report-head { background: linear-gradient(135deg, #0c4a6e, #0ea5e9); color: white; padding: 30px 40px; }
        .report-body { padding: 35px 40px; }
        .stat-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px 18px; }
        table thead th { background: #f8fafc; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.8px; color: #475569; }
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

    <!-- Controls Bar -->
    <div class="container no-print mt-4 text-center">
        <div class="card border-0 shadow-sm rounded-4 p-3 d-inline-flex flex-row flex-wrap align-items-center justify-content-center gap-2 bg-white">
            <select id="printClass" class="form-select form-select-sm rounded-pill fw-semibold" style="width: auto; min-width: 200px;" onchange="applyPrintFilter()">
                <option value="0" <?php echo $classId === 0 ? 'selected' : ''; ?>>Semua Pelatihan</option>
                <?php foreach ($allClasses as $cl): ?>
                    <option value="<?php echo $cl['id']; ?>" <?php echo $classId == $cl['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cl['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select id="printYear" class="form-select form-select-sm rounded-pill" style="width: auto;" onchange="applyPrintFilter()">
                <option value="all" <?php echo $year === 'all' ? 'selected' : ''; ?>>Semua Periode</option>
                <?php 
                $curY = date('Y');
                for($i = $curY; $i >= $curY - 5; $i--) {
                    $sel = ($year == $i) ? 'selected' : '';
                    echo "<option value='$i' $sel>Tahun $i</option>";
                }
                ?>
            </select>

            <button onclick="window.print()" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold shadow-sm" style="background: #0c4a6e; border: none;">
                <i class="fas fa-print me-1"></i> Cetak / Simpan PDF
            </button>
            <a href="dashboard.php?page=reports&year=<?php echo urlencode($year); ?>&class_id=<?php echo urlencode($classId); ?>" class="btn btn-light btn-sm rounded-pill px-3 border">Kembali</a>
        </div>
    </div>

    <!-- Printable Report Sheet -->
    <div class="report-sheet">
        <div class="report-head d-flex justify-content-between align-items-start">
            <div class="d-flex align-items-center">
                <img src="../assets/img/logo.png" alt="MCM" style="height: 48px; filter: drop-shadow(0 0 6px rgba(255,255,255,0.4));">
                <div class="ms-3">
                    <span class="d-block fw-bold fs-5" style="letter-spacing: 1px;">LPK MITRA CIPTA MANDIRI</span>
                    <span class="d-block small opacity-75">Lembaga Pelatihan &amp; Kursus Vokasi Bersertifikat</span>
                </div>
            </div>
            <div class="text-end small">
                <div class="fw-bold">LAPORAN REKAPITULASI PENDAFTARAN</div>
                <div class="opacity-90 fw-semibold"><?php echo htmlspecialchars($scopeTitle); ?></div>
                <div class="opacity-75"><?php echo htmlspecialchars($periodLabel); ?> &bull; <?php echo date('d/m/Y H:i'); ?></div>
            </div>
        </div>

        <div class="report-body">
            <!-- Filter Metadata Banner -->
            <div class="p-3 mb-4 rounded-3 border bg-light d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <span class="text-muted small text-uppercase fw-bold">Cakupan Laporan:</span>
                    <strong class="text-dark d-block"><?php echo htmlspecialchars($scopeTitle); ?></strong>
                </div>
                <div>
                    <span class="text-muted small text-uppercase fw-bold">Periode:</span>
                    <strong class="text-primary d-block"><?php echo htmlspecialchars($periodLabel); ?></strong>
                </div>
                <div>
                    <span class="text-muted small text-uppercase fw-bold">Dicetak Oleh:</span>
                    <strong class="text-dark d-block"><?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Administrator'); ?></strong>
                </div>
            </div>

            <!-- Summary Cards Row -->
            <div class="row g-3 mb-4">
                <div class="col-3">
                    <div class="stat-box text-center">
                        <div class="text-muted small text-uppercase fw-bold mb-1" style="font-size: 0.7rem;">Total Omzet</div>
                        <h6 class="fw-bold text-primary mb-0">Rp <?php echo number_format($revenue, 0, ',', '.'); ?></h6>
                    </div>
                </div>
                <div class="col-3">
                    <div class="stat-box text-center">
                        <div class="text-muted small text-uppercase fw-bold mb-1" style="font-size: 0.7rem;">Total Pendaftar</div>
                        <h6 class="fw-bold text-dark mb-0"><?php echo $totalOrders; ?> Peserta</h6>
                    </div>
                </div>
                <div class="col-3">
                    <div class="stat-box text-center">
                        <div class="text-muted small text-uppercase fw-bold mb-1" style="font-size: 0.7rem;">Pembayaran Lunas</div>
                        <h6 class="fw-bold text-success mb-0"><?php echo $paidCount; ?> Lunas</h6>
                    </div>
                </div>
                <div class="col-3">
                    <div class="stat-box text-center">
                        <div class="text-muted small text-uppercase fw-bold mb-1" style="font-size: 0.7rem;">Pending / Belum Bayar</div>
                        <h6 class="fw-bold text-warning mb-0"><?php echo $unpaidCount; ?> Order</h6>
                    </div>
                </div>
            </div>

            <!-- Breakdown Per Program Pelatihan -->
            <h6 class="fw-bold text-dark mb-2 pb-1 border-bottom"><i class="fas fa-list-alt me-1 text-primary"></i> Rincian Program Pelatihan</h6>
            <div class="table-responsive mb-4">
                <table class="table table-bordered table-sm align-middle mb-0" style="font-size: 0.85rem;">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 40px;">No</th>
                            <th>Nama Program Pelatihan</th>
                            <th>Kategori</th>
                            <th class="text-center">Total Order</th>
                            <th class="text-center">Lunas</th>
                            <th class="text-end">Total Omzet</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($classBreakdowns)): ?>
                            <tr><td colspan="6" class="text-center py-3 text-muted">Tidak ada data program.</td></tr>
                        <?php else: ?>
                            <?php foreach ($classBreakdowns as $i => $cb): ?>
                                <tr>
                                    <td class="text-center"><?php echo $i + 1; ?></td>
                                    <td class="fw-semibold text-dark"><?php echo htmlspecialchars($cb['name']); ?></td>
                                    <td><?php echo htmlspecialchars($cb['category']); ?></td>
                                    <td class="text-center fw-bold"><?php echo $cb['total_order']; ?></td>
                                    <td class="text-center text-success fw-bold"><?php echo $cb['paid_order']; ?></td>
                                    <td class="text-end fw-bold text-primary">Rp <?php echo number_format($cb['omzet'], 0, ',', '.'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Daftar Transaksi Lengkap -->
            <h6 class="fw-bold text-dark mb-2 pb-1 border-bottom"><i class="fas fa-receipt me-1 text-primary"></i> Daftar Transaksi &amp; Pendaftaran</h6>
            <div class="table-responsive mb-4">
                <table class="table table-bordered table-sm align-middle mb-0" style="font-size: 0.8rem;">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 35px;">No</th>
                            <th>No. Order</th>
                            <th>Nama Peserta</th>
                            <th>WhatsApp</th>
                            <th>Pelatihan</th>
                            <th class="text-end">Nominal</th>
                            <th class="text-center">Status</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($ordersList)): ?>
                            <tr><td colspan="8" class="text-center py-3 text-muted">Tidak ada data pendaftaran ditemukan.</td></tr>
                        <?php else: ?>
                            <?php foreach ($ordersList as $idx => $ord): ?>
                                <tr>
                                    <td class="text-center"><?php echo $idx + 1; ?></td>
                                    <td><code><?php echo htmlspecialchars($ord['order_number']); ?></code></td>
                                    <td class="fw-semibold"><?php echo htmlspecialchars($ord['customer_name']); ?></td>
                                    <td><?php echo htmlspecialchars($ord['customer_phone'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($ord['class_name']); ?></td>
                                    <td class="text-end fw-bold">Rp <?php echo number_format($ord['amount'], 0, ',', '.'); ?></td>
                                    <td class="text-center">
                                        <?php if ($ord['payment_status'] === 'paid'): ?>
                                            <span class="text-success fw-bold">Lunas</span>
                                        <?php elseif ($ord['payment_status'] === 'failed'): ?>
                                            <span class="text-danger">Gagal</span>
                                        <?php else: ?>
                                            <span class="text-warning"><?php echo htmlspecialchars($ord['payment_status']); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-muted" style="font-size: 0.75rem;"><?php echo date('d/m/Y H:i', strtotime($ord['created_at'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Signature Section -->
            <div class="row mt-5 pt-3">
                <div class="col-7"></div>
                <div class="col-5 text-center">
                    <div class="small text-muted mb-1">Cirebon, <?php echo date('d F Y'); ?></div>
                    <div class="small fw-bold text-dark mb-5">Pimpinan LPK Mitra Cipta Mandiri</div>
                    <div class="fw-bold text-dark text-decoration-underline">( Hj. Ratu Fitria, S.Pd. )</div>
                    <div class="small text-muted">Direktur / Pimpinan Lembaga</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function applyPrintFilter() {
            const classId = document.getElementById('printClass').value;
            const year = document.getElementById('printYear').value;
            window.location.href = `reports_print.php?year=${encodeURIComponent(year)}&class_id=${encodeURIComponent(classId)}`;
        }
    </script>
</body>
</html>
