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

        <div class="row g-4">
            <?php if (empty($classes)): ?>
                <div class="col-12 text-center py-5">
                    <div class="text-muted">Belum ada paket pelatihan yang dibuat.</div>
                </div>
            <?php else: ?>
                <?php foreach ($classes as $c): ?>
                    <div class="col-md-6 col-xl-4">
                        <div class="card h-100 border-0 shadow-sm overflow-hidden">
                            <div class="position-relative">
                                <?php if (!empty($c['image'])): ?>
                                    <img src="<?php echo htmlspecialchars(getImgSrc($c['image'])); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($c['name']); ?>" style="height: 180px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="bg-light d-flex align-items-center justify-content-center" style="height: 180px;">
                                        <i class="fas fa-book-open fa-3x text-muted opacity-25"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="position-absolute top-0 end-0 p-3">
                                    <span class="badge bg-white text-primary shadow-sm rounded-pill"><?php echo htmlspecialchars($c['category']); ?></span>
                                </div>
                            </div>
                            <div class="card-body p-4">
                                <h5 class="fw-bold text-dark mb-2"><?php echo htmlspecialchars($c['name']); ?></h5>
                                <div class="text-primary fw-bold fs-5 mb-3">Rp <?php echo number_format($c['price'], 0, ',', '.'); ?></div>
                                <p class="text-muted small mb-4" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;"><?php echo htmlspecialchars($c['description'] ?? 'Tidak ada deskripsi.'); ?></p>
                                
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
