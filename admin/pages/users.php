<!-- USERS PAGE -->
        <div class="row align-items-center mb-4 g-3">
            <div class="col-md-6">
                <h2 class="fw-bold mb-1 text-dark">Peserta Terdaftar</h2>
                <p class="text-muted mb-0">Data user/peserta yang sudah mendaftar akun di MCM.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-4 py-2 fw-bold" style="font-size: 0.85rem;">
                    <i class="fas fa-users me-2"></i>Total: <?php echo count($users); ?> Peserta
                </span>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive table-responsive--no-scroll">
                <table class="table align-middle admin-compact mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Peserta</th>
                            <th>Email</th>
                            <th>No. WhatsApp</th>
                            <th>Kelas Terdaftar</th>
                            <th>Tgl Registrasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                            <tr><td colspan="6" class="text-center text-muted py-5">Belum ada peserta yang terdaftar.</td></tr>
                        <?php else: ?>
                            <?php foreach ($users as $idx => $u): ?>
                                <tr>
                                    <td class="fw-bold text-muted"><?php echo $idx + 1; ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; min-width: 40px; font-weight: 700;">
                                                <?php echo strtoupper(substr($u['name'], 0, 1)); ?>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark"><?php echo htmlspecialchars($u['name']); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="small text-muted"><?php echo htmlspecialchars($u['email']); ?></span>
                                    </td>
                                    <td>
                                        <?php
                                        $phone = $u['phone'] ?? '';
                                        $displayPhone = $phone;
                                        if (substr($phone, 0, 2) === '62') $displayPhone = '0' . substr($phone, 2);
                                        ?>
                                        <span class="small"><?php echo htmlspecialchars($displayPhone); ?></span>
                                    </td>
                                    <td>
                                        <?php if ((int)$u['total_kelas'] > 0): ?>
                                            <span class="badge badge-soft-success rounded-pill px-3"><?php echo $u['total_kelas']; ?> Kelas</span>
                                            <div class="small text-muted mt-1" style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?php echo htmlspecialchars($u['enrolled_classes'] ?? ''); ?>">
                                                <?php echo htmlspecialchars($u['enrolled_classes'] ?? ''); ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="badge badge-soft-warning rounded-pill px-3">Belum Enroll</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="small text-muted"><?php echo date('d M Y', strtotime($u['created_at'])); ?></span>
                                        <div class="small text-muted opacity-50"><?php echo date('H:i', strtotime($u['created_at'])); ?> WIB</div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
