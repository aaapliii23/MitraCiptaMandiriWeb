<!-- BOOKINGS PAGE -->
        <div class="row align-items-center mb-5 g-3">
            <div class="col-md-6">
                <h2 class="fw-bold mb-1 text-dark">Form Booking / Kontak</h2>
                <p class="text-muted mb-0">Kelola calon peserta yang mengisi form pendaftaran di website.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2"><i class="fas fa-calendar-check me-2"></i><?php echo count($bookings); ?> booking masuk</span>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="bg-light text-secondary small text-uppercase">
                            <tr>
                                <th class="ps-4">Nama</th>
                                <th>WhatsApp</th>
                                <th>Email</th>
                                <th>Program</th>
                                <th>Jadwal</th>
                                <th class="text-center">Terdaftar</th>
                                <th class="text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($bookings)): ?>
                                <tr><td colspan="7" class="text-center py-5 text-muted">Belum ada data booking.</td></tr>
                            <?php else: ?>
                                <?php foreach ($bookings as $b): ?>
                                    <tr>
                                        <td class="ps-4 fw-bold text-dark"><?php echo htmlspecialchars($b['name']); ?></td>
                                        <td>
                                            <span class="small text-muted d-flex align-items-center">
                                                <i class="fab fa-whatsapp me-1 text-success"></i><?php echo htmlspecialchars($b['whatsapp']); ?>
                                            </span>
                                        </td>
                                        <td class="small text-muted"><?php echo htmlspecialchars($b['email']); ?></td>
                                        <td class="fw-bold text-primary small"><?php echo htmlspecialchars($b['service']); ?></td>
                                        <td>
                                            <?php if (!empty($b['booking_date'])): ?>
                                                <span class="badge badge-soft-success"><i class="fas fa-calendar-check me-1"></i><?php echo date('d M Y', strtotime($b['booking_date'])); ?></span>
                                            <?php else: ?>
                                                <span class="badge badge-soft-warning">Belum dijadwalkan</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center small text-muted"><?php echo date('d M Y H:i', strtotime($b['created_at'])); ?></td>
                                        <td class="text-end pe-4">
                                            <div class="d-flex justify-content-end gap-2">
                                                <button class="btn btn-action btn-soft-primary" data-bs-toggle="modal" data-bs-target="#bookingDateModal"
                                                    data-id="<?php echo $b['id']; ?>"
                                                    data-name="<?php echo htmlspecialchars($b['name']); ?>"
                                                    data-date="<?php echo htmlspecialchars($b['booking_date'] ?? ''); ?>">
                                                    <i class="fas fa-calendar-alt"></i>
                                                </button>
                                                <button class="btn btn-action btn-soft-danger" onclick="deleteItem('bookings', <?php echo $b['id']; ?>)">
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