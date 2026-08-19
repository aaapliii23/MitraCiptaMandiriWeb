<!-- REPORTS PAGE -->
<?php $reportYear = (int)($_GET['year'] ?? date('Y')); ?>
        <div class="row align-items-center mb-4 g-3 no-print" data-aos="fade-down">
            <div class="col-md-6">
                <h2 class="fw-bold mb-1 text-dark">Laporan & Analitik</h2>
                <p class="text-muted mb-0">Visualisasi data pendaftaran dan pertumbuhan MCM.</p>
            </div>
            <div class="col-md-6 text-md-end d-flex justify-content-md-end gap-2 align-items-center">
                <select id="reportYear" class="form-select rounded-pill border-primary text-primary fw-bold" style="width: auto; height: 42px;" onchange="updateReportYear(this.value)">
                    <?php 
                    $currentYear = date('Y');
                    for($i = $currentYear; $i >= $currentYear - 5; $i--) {
                        $sel = ($i == $reportYear) ? 'selected' : '';
                        echo "<option value='$i' $sel>Tahun $i</option>";
                    }
                    ?>
                </select>
                <button class="btn btn-soft-primary px-4 rounded-pill" onclick="window.print()" style="height: 42px;">
                    <i class="fas fa-print me-2"></i>Cetak
                </button>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-3">
                            <i class="fas fa-wallet text-primary"></i>
                        </div>
                        <h6 class="text-muted small text-uppercase fw-bold mb-0">Total Estimasi Omzet</h6>
                    </div>
                    <?php 
                    $revenue = $pdo->query("SELECT SUM(amount) FROM orders WHERE payment_status = 'paid'")->fetchColumn();
                    ?>
                    <h3 class="fw-bold text-dark mb-1">Rp <?php echo number_format($revenue ?: 0, 0, ',', '.'); ?></h3>
                    <p class="small text-success mb-0"><i class="fas fa-arrow-up me-1"></i>Dari pembayaran lunas</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-warning bg-opacity-10 p-2 rounded-3 me-3">
                            <i class="fas fa-clock text-warning"></i>
                        </div>
                        <h6 class="text-muted small text-uppercase fw-bold mb-0">Pesanan Pending</h6>
                    </div>
                    <?php 
                    $pendingCount = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn();
                    ?>
                    <h3 class="fw-bold text-dark mb-1"><?php echo $pendingCount; ?></h3>
                    <p class="small text-muted mb-0">Menunggu konfirmasi</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-success bg-opacity-10 p-2 rounded-3 me-3">
                            <i class="fas fa-user-check text-success"></i>
                        </div>
                        <h6 class="text-muted small text-uppercase fw-bold mb-0">Konversi Peserta</h6>
                    </div>
                    <?php 
                    $totalOrd = ($stats['orders'] ?? 0) ?: 1;
                    $confirmedCount = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'confirmed'")->fetchColumn() ?: 0;
                    $rate = round(($confirmedCount / $totalOrd) * 100);
                    ?>
                    <h3 class="fw-bold text-dark mb-1"><?php echo $rate; ?>%</h3>
                    <div class="progress" style="height: 6px; border-radius: 10px;">
                        <div class="progress-bar bg-success" style="width: <?php echo $rate; ?>%"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-danger bg-opacity-10 p-2 rounded-3 me-3">
                            <i class="fas fa-user-times text-danger"></i>
                        </div>
                        <h6 class="text-muted small text-uppercase fw-bold mb-0">Pembatalan</h6>
                    </div>
                    <?php 
                    $cancelledCount = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'cancelled'")->fetchColumn();
                    ?>
                    <h3 class="fw-bold text-dark mb-1"><?php echo $cancelledCount; ?></h3>
                    <p class="small text-danger mb-0">Pesanan dibatalkan</p>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-success bg-opacity-10 p-2 rounded-3 me-3">
                            <i class="fas fa-check-circle text-success"></i>
                        </div>
                        <h6 class="text-muted small text-uppercase fw-bold mb-0">Pembayaran Lunas</h6>
                    </div>
                    <?php 
                    $paidCount = $pdo->query("SELECT COUNT(*) FROM orders WHERE payment_status = 'paid'")->fetchColumn();
                    ?>
                    <h3 class="fw-bold text-dark mb-1"><?php echo $paidCount; ?></h3>
                    <p class="small text-success mb-0"><i class="fas fa-check me-1"></i>payment_status = paid</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-secondary bg-opacity-10 p-2 rounded-3 me-3">
                            <i class="fas fa-hourglass-half text-secondary"></i>
                        </div>
                        <h6 class="text-muted small text-uppercase fw-bold mb-0">Belum Dibayar</h6>
                    </div>
                    <?php 
                    $unpaidCount = $pdo->query("SELECT COUNT(*) FROM orders WHERE payment_status IN ('unpaid','pending')")->fetchColumn();
                    ?>
                    <h3 class="fw-bold text-dark mb-1"><?php echo $unpaidCount; ?></h3>
                    <p class="small text-muted mb-0">unpaid + pending</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-danger bg-opacity-10 p-2 rounded-3 me-3">
                            <i class="fas fa-times-circle text-danger"></i>
                        </div>
                        <h6 class="text-muted small text-uppercase fw-bold mb-0">Pembayaran Gagal</h6>
                    </div>
                    <?php 
                    $failedCount = $pdo->query("SELECT COUNT(*) FROM orders WHERE payment_status = 'failed'")->fetchColumn();
                    ?>
                    <h3 class="fw-bold text-dark mb-1"><?php echo $failedCount; ?></h3>
                    <p class="small text-danger mb-0">payment_status = failed</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-warning bg-opacity-10 p-2 rounded-3 me-3">
                            <i class="fas fa-clock text-warning"></i>
                        </div>
                        <h6 class="text-muted small text-uppercase fw-bold mb-0">Kedaluwarsa</h6>
                    </div>
                    <?php 
                    $expiredCount = $pdo->query("SELECT COUNT(*) FROM orders WHERE payment_status = 'expired'")->fetchColumn();
                    ?>
                    <h3 class="fw-bold text-dark mb-1"><?php echo $expiredCount; ?></h3>
                    <p class="small text-warning mb-0">payment_status = expired</p>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <?php
            $finIn = 0; $finOut = 0;
            try {
                $stmt = $pdo->prepare("SELECT type, SUM(amount) AS total FROM finance_transactions WHERE YEAR(transaction_date) = ? GROUP BY type");
                $stmt->execute([$reportYear]);
                foreach ($stmt->fetchAll() as $fr) {
                    if ($fr['type'] === 'in') $finIn = (int)$fr['total'];
                    else $finOut = (int)$fr['total'];
                }
            } catch (PDOException $e) {}
            ?>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-success bg-opacity-10 p-2 rounded-3 me-3"><i class="fas fa-arrow-down text-success"></i></div>
                        <h6 class="text-muted small text-uppercase fw-bold mb-0">Uang Masuk</h6>
                    </div>
                    <h3 class="fw-bold text-success mb-1">Rp <?php echo number_format($finIn, 0, ',', '.'); ?></h3>
                    <p class="small text-muted mb-0">Rekap keuangan tahun <?php echo $reportYear; ?></p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-danger bg-opacity-10 p-2 rounded-3 me-3"><i class="fas fa-arrow-up text-danger"></i></div>
                        <h6 class="text-muted small text-uppercase fw-bold mb-0">Uang Keluar</h6>
                    </div>
                    <h3 class="fw-bold text-danger mb-1">Rp <?php echo number_format($finOut, 0, ',', '.'); ?></h3>
                    <p class="small text-muted mb-0">Termasuk sewa & operasional</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-3"><i class="fas fa-wallet text-primary"></i></div>
                        <h6 class="text-muted small text-uppercase fw-bold mb-0">Saldo Keuangan</h6>
                    </div>
                    <h3 class="fw-bold text-primary mb-1">Rp <?php echo number_format($finIn - $finOut, 0, ',', '.'); ?></h3>
                    <p class="small text-muted mb-0">Uang masuk &minus; uang keluar</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-warning bg-opacity-10 p-2 rounded-3 me-3"><i class="fas fa-chart-pie text-warning"></i></div>
                        <h6 class="text-muted small text-uppercase fw-bold mb-0">Rasio Keluar / Masuk</h6>
                    </div>
                    <h3 class="fw-bold text-warning mb-1"><?php echo $finIn > 0 ? round(($finOut / $finIn) * 100) . '%' : '0%'; ?></h3>
                    <p class="small text-muted mb-0">Proporsi pengeluaran</p>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="fw-bold mb-1 text-dark">Grafik Pendaftaran</h5>
                            <div class="btn-group btn-group-sm rounded-pill overflow-hidden border">
                                <button class="btn btn-light px-3 active chart-toggle" onclick="updateChart('weekly', this)">Mingguan</button>
                                <button class="btn btn-light px-3 chart-toggle" onclick="updateChart('monthly', this)">Bulanan</button>
                                <button class="btn btn-light px-3 chart-toggle" onclick="updateChart('yearly', this)">Tahunan</button>
                            </div>
                        </div>
                        <i class="fas fa-chart-line text-primary opacity-25 fs-4"></i>
                    </div>
                    <div style="height: 350px;">
                        <canvas id="reportsChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-primary text-white overflow-hidden position-relative" style="background: linear-gradient(135deg, #2563eb, #3b82f6) !important;">
                    <div class="position-relative z-index-1">
                        <h6 class="text-white-50 small text-uppercase fw-bold mb-3">Total Peserta</h6>
                        <h2 class="fw-bold mb-2"><?php echo number_format($stats['orders'] ?? 0); ?></h2>
                        <p class="small mb-0 opacity-75">Peserta terdaftar di seluruh program.</p>
                    </div>
                    <i class="fas fa-users position-absolute bottom-0 end-0 fs-1 opacity-10 m-3"></i>
                </div>
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                    <h6 class="text-muted small text-uppercase fw-bold mb-4">Program Terpopuler</h6>
                    <div class="overflow-auto" style="max-height: 250px;">
                        <?php 
                        $popular = $pdo->query("SELECT c.name, COUNT(o.id) as total FROM classes c LEFT JOIN orders o ON c.id = o.class_id GROUP BY c.id ORDER BY total DESC LIMIT 5")->fetchAll();
                        if (empty($popular)): ?>
                            <div class="text-center py-4 text-muted small">Belum ada data pendaftaran.</div>
                        <?php else: ?>
                            <?php foreach ($popular as $idx => $p): ?>
                                <div class="d-flex align-items-center mb-4">
                                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-3 fw-bold text-primary" style="min-width: 35px; height: 35px; font-size: 0.8rem;"><?php echo $idx + 1; ?></div>
                                    <div class="flex-grow-1">
                                        <div class="fw-bold text-dark small text-truncate" style="max-width: 150px;"><?php echo htmlspecialchars($p['name']); ?></div>
                                        <div class="progress mt-2" style="height: 6px; border-radius: 10px;">
                                            <?php $orderTotal = $stats['orders'] ?? 0; ?>
                                            <div class="progress-bar bg-primary rounded-pill" style="width: <?php echo ($orderTotal > 0 ? ($p['total'] / $orderTotal) * 100 : 0); ?>%"></div>
                                        </div>
                                    </div>
                                    <div class="ms-3 fw-bold text-dark small"><?php echo $p['total']; ?></div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <style>
            @media print {
                .no-print, .sidebar, .mcm-bottom-nav, .d-md-none, .btn-group, #reportYear, .btn-action { display: none !important; }
                .main-content { margin: 0 !important; padding: 0 !important; width: 100% !important; }
                .card { border: 1px solid #eee !important; box-shadow: none !important; }
                body { background: white !important; }
            }
            .main-content { overflow-y: auto !important; height: 100vh; }
        </style>

        <script>
            let mainChart = null;
            let currentChartType = 'weekly';
            let currentReportYear = new Date().getFullYear();

            function initReportsChart(type = 'weekly', year = null) {
                currentChartType = type;
                if (year) currentReportYear = year;
                
                const ctx = document.getElementById('reportsChart').getContext('2d');
                fetch(`actions/reports_data.php?type=${type}&year=${currentReportYear}`)
                    .then(res => res.json())
                    .then(data => {
                        if (mainChart) mainChart.destroy();
                        
                        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
                        gradient.addColorStop(0, 'rgba(37, 99, 235, 0.2)');
                        gradient.addColorStop(1, 'rgba(37, 99, 235, 0)');

                        mainChart = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: data.labels,
                                datasets: [{
                                    label: 'Jumlah Pendaftaran',
                                    data: data.counts,
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

            function updateReportYear(year) {
                location.href = '?page=reports&year=' + year;
            }

            document.addEventListener('DOMContentLoaded', () => initReportsChart('weekly', <?php echo $reportYear; ?>));
        </script>
