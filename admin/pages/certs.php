<!-- CERTIFICATIONS PAGE -->
        <div class="row align-items-center mb-5 g-3">
            <div class="col-md-6">
                <h2 class="fw-bold mb-1 text-dark">Legalitas & Sertifikasi</h2>
                <p class="text-muted mb-0">Arsip dokumen resmi dan sertifikat lembaga.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <button class="btn btn-primary px-4 shadow-sm rounded-pill" onclick="showModal('certModal')">
                    <i class="fas fa-plus me-2"></i>Tambah Dokumen
                </button>
            </div>
        </div>

        <div class="row g-4">
            <?php foreach ($certs as $ct): ?>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm group">
                        <div class="p-4 bg-light text-center" style="border-radius: 1.25rem 1.25rem 0 0;">
                            <img src="<?php echo htmlspecialchars(getImgSrc($ct['image'])); ?>" class="shadow-lg rounded-3" style="height: 160px; width: auto; max-width: 100%; object-fit: contain;">
                        </div>
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h6 class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($ct['title']); ?></h6>
                                    <span class="small text-muted"><i class="far fa-calendar-alt me-1"></i>Diupload <?php echo date('d M Y', strtotime($ct['created_at'])); ?></span>
                                </div>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-action btn-soft-primary" onclick="editCert(<?php echo htmlspecialchars(json_encode($ct)); ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-action btn-soft-danger" onclick="deleteItem('certs', <?php echo $ct['id']; ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="d-grid">
                                <a href="<?php echo htmlspecialchars($ct['image']); ?>" target="_blank" class="btn btn-light rounded-pill fw-bold small">Lihat Dokumen Lengkap</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
