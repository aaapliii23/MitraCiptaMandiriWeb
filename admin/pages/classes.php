<!-- CLASSES PAGE -->
        <div class="row align-items-center mb-4 g-3">
            <div class="col-md-6">
                <h2 class="fw-bold mb-1 text-dark">Paket Pelatihan</h2>
                <p class="text-muted mb-0">Kelola kurikulum dan paket kursus profesional MCM.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <div class="d-flex justify-content-md-end gap-2">
                    <button class="btn btn-outline-primary px-4 shadow-sm rounded-pill" onclick="showModal('categoryModal')">
                        <i class="fas fa-tags me-2"></i>Kategori
                    </button>
                    <button class="btn btn-primary px-4 shadow-sm rounded-pill" onclick="resetClassForm(); showModal('classModal');">
                        <i class="fas fa-plus me-2"></i>Tambah Paket
                    </button>
                </div>
            </div>
        </div>

        <div data-bulk-table="classes">
        <div class="admin-table-toolbar d-none" data-bulk-toolbar>
            <div class="small fw-bold text-primary"><i class="fas fa-check-square me-1"></i><span data-bulk-count>0 dipilih</span></div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" onclick="document.querySelectorAll('[data-bulk-table=classes] .js-bulk-row').forEach(cb=>cb.checked=false); document.querySelector('[data-bulk-table=classes] .js-bulk-select-all').checked=false; document.querySelector('[data-bulk-table=classes] .js-bulk-select-all').indeterminate=false; document.querySelector('[data-bulk-table=classes] [data-bulk-toolbar]').classList.add('d-none');">Batal</button>
                <button type="button" class="btn btn-sm btn-danger rounded-pill px-3 fw-bold" data-bulk-delete><i class="fas fa-trash me-1"></i>Hapus Terpilih (<span data-bulk-count-num>0</span>)</button>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2 mb-3">
            <label class="small fw-bold text-muted mb-0" style="cursor:pointer;"><input type="checkbox" class="bulk-select-all js-bulk-select-all me-1"> Pilih semua</label>
            <span class="small text-muted">(centang kartu untuk aksi massal)</span>
        </div>
        <div class="row g-4">
            <?php if (empty($classes)): ?>
                <div class="col-12 text-center py-5">
                    <div class="text-muted">Belum ada paket pelatihan yang dibuat.</div>
                </div>
            <?php else: ?>
                <?php foreach ($classes as $c): ?>
                    <div class="col-md-6 col-xl-4" data-bulk-card>
                        <div class="card h-100 border-0 shadow-sm overflow-hidden position-relative">
                            <div class="position-absolute top-0 start-0 m-2" style="z-index:2;">
                                <input type="checkbox" class="bulk-row-check js-bulk-row" value="<?php echo (int)$c['id']; ?>" style="width:18px;height:18px;accent-color:#2563eb; cursor:pointer; box-shadow:0 2px 6px rgba(0,0,0,0.15);">
                            </div>
                            <div class="position-relative w-100 overflow-hidden" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                                <?php if (!empty($c['image'])): ?>
                                    <img src="<?php echo htmlspecialchars(getImgSrc($c['image'])); ?>" class="w-100 d-block" alt="<?php echo htmlspecialchars($c['name']); ?>" style="height: auto; max-height: 380px; object-fit: contain;">
                                <?php else: ?>
                                    <div class="bg-light d-flex align-items-center justify-content-center w-100" style="height: 180px;">
                                        <i class="fas fa-book-open fa-3x text-muted opacity-25"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="position-absolute top-0 end-0 p-3" style="z-index: 2;">
                                    <span class="badge bg-white text-primary shadow-sm rounded-pill border"><?php echo htmlspecialchars($c['category']); ?></span>
                                </div>
                            </div>
                            <div class="card-body p-4">
                                <h5 class="fw-bold text-dark mb-2"><?php echo htmlspecialchars($c['name']); ?></h5>
                                <?php
                                $p = (int)($c['price'] ?? 0);
                                $po = isset($c['price_online']) && (int)$c['price_online'] > 0 ? (int)$c['price_online'] : (int)round($p * 0.8);
                                $pf = isset($c['price_offline']) && (int)$c['price_offline'] > 0 ? (int)$c['price_offline'] : $p;
                                $ma = $c['mode_available'] ?? 'both';
                                ?>
                                <?php if ($ma === 'online'): ?>
                                    <div class="mb-2">
                                        <span class="badge bg-info bg-opacity-10 text-info small me-1"><i class="fas fa-laptop me-1"></i>Online</span>
                                        <span class="text-primary fw-bold">Rp <?php echo number_format($po,0,',','.'); ?></span>
                                    </div>
                                <?php elseif ($ma === 'offline'): ?>
                                    <div class="mb-2">
                                        <span class="badge bg-success bg-opacity-10 text-success small me-1"><i class="fas fa-chalkboard-teacher me-1"></i>Offline</span>
                                        <span class="text-primary fw-bold">Rp <?php echo number_format($pf,0,',','.'); ?></span>
                                    </div>
                                <?php else: ?>
                                    <?php if ($po !== $pf): ?>
                                        <div class="small text-muted">Mulai dari</div>
                                        <div class="text-primary fw-bold fs-5 mb-1">Rp <?php echo number_format(min($po,$pf),0,',','.'); ?></div>
                                        <div class="d-flex flex-wrap gap-1 align-items-center mb-2">
                                            <span class="badge bg-light border border-secondary text-dark small fw-semibold px-2 py-1">
                                                <i class="fas fa-chalkboard-teacher me-1 text-success"></i>Offline Rp <?php echo number_format($pf,0,',','.'); ?>
                                            </span>
                                            <span class="badge bg-light border border-secondary text-dark small fw-semibold px-2 py-1">
                                                <i class="fas fa-laptop me-1 text-info"></i>Online Rp <?php echo number_format($po,0,',','.'); ?>
                                            </span>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-primary fw-bold fs-5 mb-3">Rp <?php echo number_format($p,0,',','.'); ?> <small class="text-muted" style="font-size:0.7rem;">(Online &amp; Offline)</small></div>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <p class="text-muted small mb-2" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;"><?php echo htmlspecialchars($c['description'] ?? 'Tidak ada deskripsi.'); ?></p>
                                <?php if (!empty($c['whatsapp_group_link'])): ?>
                                <div class="small mb-3"><a href="<?php echo htmlspecialchars($c['whatsapp_group_link']); ?>" target="_blank" class="badge bg-success bg-opacity-10 text-success border text-decoration-none"><i class="fab fa-whatsapp me-1"></i>Grup WA Offline</a> <small class="text-muted d-block mt-1 text-truncate" style="max-width: 220px; font-size:0.68rem;"><?php echo htmlspecialchars($c['whatsapp_group_link']); ?></small></div>
                                <?php elseif (in_array($ma ?? 'both', ['offline','both'], true)): ?>
                                <div class="small text-warning mb-3" style="font-size:0.72rem;"><i class="fas fa-exclamation-triangle me-1"></i>Link WA Offline belum diisi</div>
                                <?php endif; ?>
                                
                                <div class="d-flex gap-2">
                                    <button class="btn btn-light flex-grow-1 fw-bold rounded-pill" onclick='editClass(<?php echo json_encode($c); ?>)'>
                                        <i class="fas fa-edit me-2"></i>Edit
                                    </button>
                                    <button class="btn btn-soft-danger btn-action rounded-circle" onclick="deleteItem('classes', <?php echo $c['id']; ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        </div>
