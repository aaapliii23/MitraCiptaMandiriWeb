<!-- DASHBOARD HOME -->
        <div class="mb-4 d-flex justify-content-between align-items-end" data-aos="fade-down">
            <div>
                <div class="d-flex align-items-center mb-1">
                    <div class="p-2 rounded-3 me-3 bg-white shadow-sm border" style="color: <?php echo $greetColor; ?>;">
                        <i class="fas <?php echo $greetIcon; ?> fs-4"></i>
                    </div>
                    <h2 class="fw-bold mb-0 text-dark"><?php echo $greeting; ?>, <span class="gradient-text"><?php echo htmlspecialchars(explode(' ', $_SESSION['admin_username'])[0]); ?>!</span></h2>
                </div>
                <p class="text-muted mb-0 ps-5 ms-2">Platform MCM berjalan optimal hari ini. Berikut ringkasan performa terbaru.</p>
                <div class="greet-line ms-5 ps-2"></div>
            </div>
            <div class="d-none d-md-block">
                <span class="badge bg-white text-dark shadow-sm py-2 px-3 rounded-pill border">
                    <i class="far fa-calendar-alt me-2 text-primary"></i><?php echo date('d F Y'); ?>
                </span>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-6 col-xl-3" data-aos="fade-up" data-aos-delay="100">
                <a href="?page=orders" class="card stat-card border-0 shadow-sm h-100 text-decoration-none" style="background: linear-gradient(135deg, #1e40af, #3b82f6, #60a5fa); color: white;">
                    <div class="card-body p-4">
                        <span class="stat-icon mb-3"><i class="fas fa-shopping-bag"></i></span>
                        <h2 class="stat-number mb-1 counter-value" data-target="<?php echo $stats['orders']; ?>">0</h2>
                        <div class="stat-label">Total Pesanan</div>
                    </div>
                    <i class="fas fa-shopping-bag stat-float"></i>
                </a>
            </div>
            <div class="col-md-6 col-xl-3" data-aos="fade-up" data-aos-delay="200">
                <a href="?page=classes" class="card stat-card border-0 shadow-sm h-100 text-decoration-none" style="background: linear-gradient(135deg, #065f46, #10b981, #34d399); color: white;">
                    <div class="card-body p-4">
                        <span class="stat-icon mb-3"><i class="fas fa-book-open"></i></span>
                        <h2 class="stat-number mb-1 counter-value" data-target="<?php echo $stats['classes']; ?>">0</h2>
                        <div class="stat-label">Paket Aktif</div>
                    </div>
                    <i class="fas fa-book-open stat-float"></i>
                </a>
            </div>
            <div class="col-md-6 col-xl-3" data-aos="fade-up" data-aos-delay="300">
                <a href="?page=gallery" class="card stat-card border-0 shadow-sm h-100 text-decoration-none" style="background: linear-gradient(135deg, #5b21b6, #8b5cf6, #a78bfa); color: white;">
                    <div class="card-body p-4">
                        <span class="stat-icon mb-3"><i class="fas fa-images"></i></span>
                        <h2 class="stat-number mb-1 counter-value" data-target="<?php echo $stats['gallery']; ?>">0</h2>
                        <div class="stat-label">Foto Galeri</div>
                    </div>
                    <i class="fas fa-images stat-float"></i>
                </a>
            </div>
            <div class="col-md-6 col-xl-3" data-aos="fade-up" data-aos-delay="400">
                <a href="?page=admins" class="card stat-card border-0 shadow-sm h-100 text-decoration-none" style="background: linear-gradient(135deg, #334155, #64748b, #94a3b8); color: white;">
                    <div class="card-body p-4">
                        <span class="stat-icon mb-3"><i class="fas fa-user-shield"></i></span>
                        <h2 class="stat-number mb-1 counter-value" data-target="<?php echo $stats['admins']; ?>">0</h2>
                        <div class="stat-label">Tim Admin</div>
                    </div>
                    <i class="fas fa-user-shield stat-float"></i>
                </a>
            </div>
        </div>

        <div class="d-flex align-items-center justify-content-between mb-3">
            <h5 class="fw-bold text-dark mb-0"><i class="fas fa-wallet me-2 text-primary"></i>Rekap Pembayaran</h5>
            <a href="?page=orders" class="btn btn-sm btn-soft-primary px-3 rounded-pill fw-bold">Detail Pesanan</a>
        </div>
        <div class="row g-4 mb-4">
            <div class="col-md-6 col-xl-3" data-aos="fade-up" data-aos-delay="100">
                <a href="?page=finance" class="card stat-card border-0 shadow-sm h-100 text-decoration-none" style="background: linear-gradient(135deg, #064e3b, #059669); color: white;">
                    <div class="card-body p-4">
                        <span class="stat-icon mb-3"><i class="fas fa-coins"></i></span>
                        <h2 class="stat-number mb-1">Rp <?php echo number_format($stats['revenue'], 0, ',', '.'); ?></h2>
                        <div class="stat-label">Omzet Lunas</div>
                    </div>
                    <i class="fas fa-coins stat-float"></i>
                </a>
            </div>
            <div class="col-md-6 col-xl-3" data-aos="fade-up" data-aos-delay="200">
                <a href="?page=orders&status=paid" class="card stat-card border-0 shadow-sm h-100 text-decoration-none" style="background: linear-gradient(135deg, #0e7490, #06b6d4); color: white;">
                    <div class="card-body p-4">
                        <span class="stat-icon mb-3"><i class="fas fa-check-circle"></i></span>
                        <h2 class="stat-number mb-1 counter-value" data-target="<?php echo $stats['paid_orders']; ?>">0</h2>
                        <div class="stat-label">Pesanan Lunas</div>
                    </div>
                    <i class="fas fa-check-circle stat-float"></i>
                </a>
            </div>
            <div class="col-md-6 col-xl-3" data-aos="fade-up" data-aos-delay="300">
                <a href="?page=orders&status=waiting" class="card stat-card border-0 shadow-sm h-100 text-decoration-none" style="background: linear-gradient(135deg, #92400e, #f59e0b); color: white;">
                    <div class="card-body p-4">
                        <span class="stat-icon mb-3"><i class="fas fa-hourglass-half"></i></span>
                        <h2 class="stat-number mb-1 counter-value" data-target="<?php echo $stats['waiting_payment']; ?>">0</h2>
                        <div class="stat-label">Menunggu Bayar</div>
                    </div>
                    <i class="fas fa-hourglass-half stat-float"></i>
                </a>
            </div>
            <div class="col-md-6 col-xl-3" data-aos="fade-up" data-aos-delay="400">
                <a href="?page=orders&status=failed" class="card stat-card border-0 shadow-sm h-100 text-decoration-none" style="background: linear-gradient(135deg, #991b1b, #ef4444); color: white;">
                    <div class="card-body p-4">
                        <span class="stat-icon mb-3"><i class="fas fa-ban"></i></span>
                        <h2 class="stat-number mb-1 counter-value" data-target="<?php echo $stats['failed_orders']; ?>">0</h2>
                        <div class="stat-label">Gagal / Kadaluarsa</div>
                    </div>
                    <i class="fas fa-ban stat-float"></i>
                </a>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-4 px-4 border-bottom-0 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0 text-dark">Tren Pendaftaran</h5>
                        <div class="badge bg-light text-primary rounded-pill px-3">6 Bulan Terakhir</div>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <div style="height: 300px;">
                            <canvas id="registrationChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-4 px-4 border-bottom-0">
                        <h5 class="fw-bold mb-0 text-dark">Distribusi Paket</h5>
                    </div>
                    <div class="card-body px-4 pb-4 d-flex flex-column">
                        <div style="height: 200px;" class="mb-4">
                            <canvas id="categoryChart"></canvas>
                        </div>
                        <div class="mt-auto">
                            <h6 class="fw-bold text-dark mb-3 small text-uppercase">Program Terpopuler</h6>
                            <?php foreach (($popular_classes ?? []) as $pc): ?>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="small text-muted fw-medium text-truncate me-2"><?php echo htmlspecialchars($pc['name']); ?></span>
                                    <span class="badge bg-light text-primary rounded-pill"><?php echo $pc['total']; ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-4 px-4 border-bottom-0 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0 text-dark">Pesanan Terbaru</h5>
                        <a href="?page=orders" class="btn btn-sm btn-soft-primary px-3 rounded-pill fw-bold">Semua Pesanan</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead class="bg-light text-secondary small text-uppercase">
                                    <tr>
                                        <th class="ps-4">No. Order</th>
                                        <th>Nama Peserta</th>
                                        <th>Program</th>
                                        <th class="text-end pe-4">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (($recent_orders ?? []) as $ro): ?>
                                        <tr>
                                            <td class="ps-4 fw-bold">#<?php echo $ro['order_number']; ?></td>
                                            <td><?php echo htmlspecialchars($ro['customer_name']); ?></td>
                                            <td><span class="small text-muted"><?php echo htmlspecialchars($ro['class_name']); ?></span></td>
                                            <td class="text-end pe-4">
                                                <span class="badge badge-soft-<?php echo ($ro['status'] == 'confirmed' ? 'success' : ($ro['status'] == 'pending' ? 'warning' : 'danger')); ?>">
                                                    <?php echo ucfirst($ro['status']); ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-4 px-4 border-bottom-0">
                        <h5 class="fw-bold mb-0 text-dark">Aksi Cepat</h5>
                    </div>
                    <div class="card-body p-4 pt-0">
                        <div class="d-grid gap-2">
                            <a href="?page=classes" class="btn btn-light text-start p-3 border-0 rounded-4 transition-all hover-translate">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-3 me-3">
                                        <i class="fas fa-plus"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark small">Tambah Paket</div>
                                        <div class="text-muted" style="font-size: 0.7rem;">Buat program pelatihan baru</div>
                                    </div>
                                </div>
                            </a>
                            <a href="?page=gallery" class="btn btn-light text-start p-3 border-0 rounded-4 transition-all hover-translate">
                                <div class="d-flex align-items-center">
                                    <div class="bg-success bg-opacity-10 text-success p-2 rounded-3 me-3">
                                        <i class="fas fa-camera"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark small">Upload Foto</div>
                                        <div class="text-muted" style="font-size: 0.7rem;">Update galeri kegiatan</div>
                                    </div>
                                </div>
                            </a>
                            <a href="?page=settings" class="btn btn-light text-start p-3 border-0 rounded-4 transition-all hover-translate">
                                <div class="d-flex align-items-center">
                                    <div class="bg-warning bg-opacity-10 text-warning p-2 rounded-3 me-3">
                                        <i class="fas fa-cog"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark small">Ubah Kontak</div>
                                        <div class="text-muted" style="font-size: 0.7rem;">Update info WA/Email</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // Chart Initialization for Dashboard
            (function() {
                if (typeof Chart === 'undefined') return;

                const months = <?php echo json_encode(array_column($chart_data ?? [], 'month') ?: ['Jan', 'Feb', 'Mar']); ?>;
                const counts = <?php echo json_encode(array_column($chart_data ?? [], 'count') ?: [0, 0, 0]); ?>;
                const catLabels = <?php echo json_encode(array_column($category_data ?? [], 'category') ?: ['Belum Ada Data']); ?>;
                const catCounts = <?php echo json_encode(array_column($category_data ?? [], 'count') ?: [1]); ?>;

                const regCtx = document.getElementById('registrationChart')?.getContext('2d');
                if (regCtx) {
                    new Chart(regCtx, {
                        type: 'line',
                        data: {
                            labels: months,
                            datasets: [{
                                label: 'Pendaftaran',
                                data: counts,
                                borderColor: '#2563eb',
                                backgroundColor: 'rgba(37, 99, 235, 0.1)',
                                fill: true,
                                tension: 0.4,
                                pointRadius: 4,
                                pointBackgroundColor: '#fff',
                                pointBorderColor: '#2563eb',
                                pointBorderWidth: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                            scales: {
                                y: { beginAtZero: true, border: { display: false }, grid: { color: 'rgba(0,0,0,0.05)' } },
                                x: { border: { display: false }, grid: { display: false } }
                            }
                        }
                    });
                }

                const catCtx = document.getElementById('categoryChart')?.getContext('2d');
                if (catCtx) {
                    new Chart(catCtx, {
                        type: 'doughnut',
                        data: {
                            labels: catLabels,
                            datasets: [{
                                data: catCounts,
                                backgroundColor: ['#2563eb', '#10b981', '#f59e0b', '#7c3aed', '#ef4444', '#64748b'],
                                borderWidth: 0
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { position: 'bottom', labels: { boxWidth: 12, padding: 20, font: { family: 'Plus Jakarta Sans', weight: '600' } } }
                            },
                            cutout: '75%'
                        }
                    });
                }
            })();
        </script>
