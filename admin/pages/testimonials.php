<!-- TESTIMONIALS PAGE -->
        <div class="row align-items-center mb-4 g-3" data-aos="fade-down">
            <div class="col-md-6">
                <h2 class="fw-bold mb-1 text-dark">Testimoni</h2>
                <p class="text-muted mb-0">Kelola ulasan peserta. Setujui sebelum ditampilkan di website.</p>
            </div>
            <div class="col-md-6 text-md-end d-flex justify-content-md-end gap-2 align-items-center flex-wrap">
                <div class="input-group shadow-sm rounded-3 overflow-hidden" style="max-width: 280px;">
                    <span class="input-group-text bg-white border-end-0 text-muted ps-3"><i class="fas fa-search"></i></span>
                    <input type="text" id="testimonialSearch" class="form-control border-start-0 py-2" placeholder="Cari peserta / ulasan..." autocomplete="off">
                </div>
                <span class="badge bg-light text-dark border px-3 py-2 rounded-pill shadow-sm">
                    <i class="fas fa-star text-warning me-1"></i>Rata-rata: <?php echo number_format($avgRating, 1, ',', '.'); ?>
                </span>
            </div>
        </div>

        <div data-bulk-table="testimonials">
        <div class="admin-table-toolbar d-none" data-bulk-toolbar>
            <div class="small fw-bold text-primary"><i class="fas fa-check-square me-1"></i><span data-bulk-count>0 dipilih</span></div>
            <button type="button" class="btn btn-sm btn-danger rounded-pill px-3 fw-bold" data-bulk-delete><i class="fas fa-trash me-1"></i>Hapus Terpilih (<span data-bulk-count-num>0</span>)</button>
        </div>
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive table-responsive--no-scroll">
                    <table class="table align-middle admin-compact mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="col-check"><input type="checkbox" class="bulk-select-all js-bulk-select-all"></th>
                                <th>Peserta</th>
                                <th>Rating</th>
                                <th>Program</th>
                                <th>Ulasan</th>
                                <th>Status</th>
                                <th>Tgl</th>
                                <th class="text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="testimonialTableBody">
                            <?php if (empty($testimonials)): ?>
                                <tr><td colspan="8" class="text-center py-5 text-muted">Belum ada testimoni.</td></tr>
                            <?php else: ?>
                                <?php foreach ($testimonials as $t):
                                    $rating = (int)$t['rating'];
                                    $badges = [
                                        'pending' => ['badge-soft-warning', 'Menunggu'],
                                        'approved' => ['badge-soft-success', 'Disetujui'],
                                        'rejected' => ['badge-soft-danger', 'Ditolak'],
                                    ];
                                    $badge = $badges[$t['status']] ?? ['badge-soft-primary', $t['status']];
                                ?>
                                <tr>
                                    <td class="col-check"><input type="checkbox" class="bulk-row-check js-bulk-row" value="<?php echo (int)$t['id']; ?>"></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if (!empty($t['image'])): ?>
                                                <img src="<?php echo htmlspecialchars(getImgSrc($t['image'])); ?>" alt="<?php echo htmlspecialchars($t['name']); ?>" class="rounded-circle me-2 border border-2 border-white shadow-sm" style="width: 32px; height: 32px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="rounded-circle bg-light text-primary d-flex align-items-center justify-content-center fw-bold me-2" style="width: 32px; height: 32px; font-size:0.8rem;"><?php echo strtoupper(mb_substr(trim($t['name']), 0, 1)); ?></div>
                                            <?php endif; ?>
                                            <span class="fw-bold text-dark small" style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:120px;" title="<?php echo htmlspecialchars($t['name']); ?>"><?php echo htmlspecialchars($t['name']); ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star <?php echo $i <= $rating ? 'text-warning' : 'text-muted'; ?>" style="font-size: 0.65rem;"></i>
                                        <?php endfor; ?>
                                    </td>
                                    <td><span class="cell-ellipsis small text-muted" title="<?php echo htmlspecialchars($t['class_name'] ?? ''); ?>" style="max-width:120px;"><?php echo htmlspecialchars($t['class_name'] ?? '-'); ?></span></td>
                                    <td style="max-width: 240px;"><span class="cell-ellipsis small text-muted" title="<?php echo htmlspecialchars($t['review']); ?>" style="max-width:220px;"><?php echo htmlspecialchars($t['review']); ?></span></td>
                                    <td><span class="badge <?php echo $badge[0]; ?>" style="font-size:0.68rem;"><?php echo $badge[1]; ?></span></td>
                                    <td class="small text-muted text-nowrap"><?php echo date('d M y', strtotime($t['created_at'])); ?></td>
                                    <td class="text-end pe-4">
                                        <div class="d-inline-flex gap-2">
                                            <?php if ($t['status'] !== 'approved'): ?>
                                                <button class="btn btn-action btn-soft-success" title="Setujui" onclick="setTestimonialStatus(<?php echo $t['id']; ?>, 'approve')"><i class="fas fa-check"></i></button>
                                            <?php endif; ?>
                                            <?php if ($t['status'] !== 'rejected'): ?>
                                                <button class="btn btn-action btn-soft-warning" title="Tolak" onclick="setTestimonialStatus(<?php echo $t['id']; ?>, 'reject')"><i class="fas fa-times"></i></button>
                                            <?php endif; ?>
                                            <!-- <button class="btn btn-action btn-soft-primary" title="Edit" onclick="editTestimonial(<?php echo htmlspecialchars(json_encode($t)); ?>)"><i class="fas fa-edit"></i></button> -->
                                            <button class="btn btn-action btn-soft-danger" title="Hapus" onclick="deleteItem('testimonials', <?php echo $t['id']; ?>)"><i class="fas fa-trash"></i></button>
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
<script>attachTableSearch('testimonialSearch', 'testimonialTableBody', 8);</script>
