<!-- ORDERS PAGE -->
        <div class="row align-items-center mb-4 g-3">
            <div class="col-md-6">
                <h2 class="fw-bold mb-1 text-dark">Data Transaksi</h2>
                <p class="text-muted mb-0">Kelola pesanan dan status pembayaran pelanggan.</p>
            </div>
            <div class="col-md-6">
                <form action="" method="GET" class="d-flex gap-2 align-items-center">
                    <input type="hidden" name="page" value="orders">
                    <select name="mode" class="form-select shadow-sm rounded-3" style="max-width: 150px;" onchange="this.form.submit()">
                        <option value="all" <?php echo ($orderModeFilter ?? 'all') === 'all' ? 'selected' : ''; ?>>Semua Mode</option>
                        <option value="online" <?php echo ($orderModeFilter ?? '') === 'online' ? 'selected' : ''; ?>>Online</option>
                        <option value="offline" <?php echo ($orderModeFilter ?? '') === 'offline' ? 'selected' : ''; ?>>Offline</option>
                    </select>
                    <div class="input-group shadow-sm rounded-3 overflow-hidden flex-grow-1">
                        <span class="input-group-text bg-white border-end-0 text-muted ps-3"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 py-2" placeholder="Cari Pelanggan / No. Order..." value="<?php echo htmlspecialchars($search ?? ''); ?>">
                    </div>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm">Cari</button>
                </form>
            </div>
        </div>

        <?php if (!empty($orderStatus)): ?>
        <div class="mb-3">
            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3 py-2">
                <i class="fas fa-filter me-1"></i>Status: <b class="text-capitalize"><?php echo htmlspecialchars($orderStatus === 'waiting' ? 'menunggu bayar' : ($orderStatus === 'failed' ? 'gagal/kadaluarsa' : $orderStatus)); ?></b>
                <a href="?page=orders" class="text-danger ms-2 text-decoration-none" title="Hapus filter"><i class="fas fa-times-circle"></i></a>
            </span>
        </div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Pelanggan</th>
                                <th>Kategori & Kelas</th>
                                <th class="text-center">Mode</th>
                                <th>Instruktur</th>
                                <th>Total</th>
                                <th class="text-center">Pembayaran</th>
                                <th class="text-center">Status</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($orders)): ?>
                                <tr><td colspan="9" class="text-center py-5 text-muted">Data tidak ditemukan.</td></tr>
                            <?php else: ?>
                                <?php foreach ($orders as $o): ?>
                                    <tr>
                                        <td class="text-secondary fw-bold small"><?php echo htmlspecialchars($o['order_number']); ?></td>
                                        <td>
                                            <div class="fw-bold text-dark"><?php echo htmlspecialchars($o['customer_name']); ?></div>
                                            <div class="small text-muted d-flex align-items-center mt-1">
                                                <i class="fab fa-whatsapp me-1 text-success"></i><?php echo htmlspecialchars($o['customer_phone']); ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="small text-muted mb-1 text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;"><?php echo htmlspecialchars($o['class_category']); ?></div>
                                            <div class="fw-bold text-primary"><?php echo htmlspecialchars($o['class_name']); ?></div>
                                        </td>
                                        <td class="text-center"><?php $cm = strtolower($o['class_mode'] ?? 'offline'); if ($cm === 'online') echo '<span class="badge bg-info bg-opacity-10 text-info"><i class="fas fa-laptop me-1"></i>Online</span>'; else echo '<span class="badge bg-success bg-opacity-10 text-success"><i class="fas fa-chalkboard-teacher me-1"></i>Offline</span>'; ?></td>
                                        <td class="small text-muted"><?php echo $o['instructor_name'] ? htmlspecialchars($o['instructor_name']) : '<span class="text-muted">-</span>'; ?></td>
                                        <td class="fw-bold text-dark">Rp <?php echo number_format($o['amount'], 0, ',', '.'); ?></td>
                                        <td class="text-center">
                                            <?php
                                            $pay = $o['payment_status'] ?? 'unpaid';
                                            $payBadge = [
                                                'paid' => ['badge-soft-success', 'fas fa-check-circle', 'Lunas'],
                                                'unpaid' => ['badge-soft-secondary', 'fas fa-hourglass-half', 'Belum Bayar'],
                                                'pending' => ['badge-soft-warning', 'fas fa-clock', 'Proses'],
                                                'failed' => ['badge-soft-danger', 'fas fa-times-circle', 'Gagal'],
                                                'expired' => ['badge-soft-danger', 'fas fa-clock', 'Kedaluwarsa'],
                                            ][$pay] ?? ['badge-soft-secondary', 'fas fa-circle', $pay];
                                            ?>
                                            <span class="badge <?php echo $payBadge[0]; ?>"><i class="<?php echo $payBadge[1]; ?> me-1"></i><?php echo $payBadge[2]; ?></span>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($o['status'] === 'pending'): ?>
                                                <span class="badge badge-soft-warning"><i class="fas fa-clock me-1"></i>Pending</span>
                                            <?php elseif ($o['status'] === 'confirmed'): ?>
                                                <span class="badge badge-soft-success"><i class="fas fa-check-circle me-1"></i>Lunas</span>
                                            <?php else: ?>
                                                <span class="badge badge-soft-danger"><i class="fas fa-times-circle me-1"></i>Batal</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end gap-2">
                                                <button class="btn btn-action btn-soft-primary" data-bs-toggle="modal" data-bs-target="#detailPesananModal" 
                                                    data-order="<?php echo htmlspecialchars($o['order_number']); ?>"
                                                    data-name="<?php echo htmlspecialchars($o['customer_name']); ?>"
                                                    data-phone="<?php echo htmlspecialchars($o['customer_phone']); ?>"
                                                    data-email="<?php echo htmlspecialchars(!empty($o['customer_email']) ? $o['customer_email'] : '-'); ?>"
                                                    data-instansi="<?php echo htmlspecialchars(!empty($o['customer_institution']) ? $o['customer_institution'] : '-'); ?>"
                                                    data-alamat="<?php echo htmlspecialchars(!empty($o['customer_address']) ? $o['customer_address'] : '-'); ?>"
                                                    data-kelas="<?php echo htmlspecialchars($o['class_name']); ?>"
                                                    data-mode="<?php echo htmlspecialchars(strtolower($o['class_mode'] ?? 'offline')); ?>"
                                                    data-harga="<?php echo number_format($o['amount'], 0, ',', '.'); ?>">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-action btn-soft-primary" data-bs-toggle="modal" data-bs-target="#updateStatusModal" data-id="<?php echo $o['id']; ?>" data-name="<?php echo htmlspecialchars($o['customer_name']); ?>" data-status="<?php echo $o['status']; ?>">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-action btn-soft-danger" onclick="deleteItem('orders', <?php echo $o['id']; ?>)">
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
