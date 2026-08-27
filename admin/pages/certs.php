<!-- CERTIFICATIONS PAGE -->
        <div class="row align-items-center mb-4 g-3">
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
                                <a href="<?php echo htmlspecialchars(getImgSrc($ct['image'])); ?>" target="_blank" class="btn btn-light rounded-pill fw-bold small">Lihat Dokumen Lengkap</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4 mt-5">
            <div>
                <h4 class="fw-bold text-dark mb-1">Template Sertifikat</h4>
                <p class="text-muted small mb-0">Kelola layout sertifikat kelulusan per program.</p>
            </div>
            <button class="btn btn-primary px-4 shadow-sm rounded-pill" onclick="showModal('certTemplateModal')">
                <i class="fas fa-plus me-2"></i>Tambah Template
            </button>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Template</th>
                                <th>Program</th>
                                <th>Layout</th>
                                <th>Aksen</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($certTemplates)): ?>
                                <tr><td colspan="6" class="text-center py-5 text-muted">Belum ada template sertifikat.</td></tr>
                            <?php else: ?>
                                <?php foreach ($certTemplates as $tm):
                                    $layoutLabels = ['default' => 'Standar', 'elegant' => 'Elegant', 'modern' => 'Modern', 'premium' => 'Premium'];
                                ?>
                                <tr>
                                    <td class="ps-4">
                                        <span class="fw-bold text-dark"><?php echo htmlspecialchars($tm['name']); ?></span>
                                        <?php if (!empty($tm['bg_image'])): ?>
                                            <span class="text-muted small d-block"><i class="fas fa-image me-1" style="font-size: 0.7rem;"></i>Dengan gambar latar</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-muted small"><?php echo htmlspecialchars($tm['class_name'] ?? 'Semua Program'); ?></td>
                                    <td><span class="badge badge-soft-primary"><?php echo $layoutLabels[$tm['layout']] ?? $tm['layout']; ?></span></td>
                                    <td>
                                        <?php if (!empty($tm['accent_color'])): ?>
                                            <span class="d-inline-block rounded-circle border" style="width: 18px; height: 18px; background: <?php echo htmlspecialchars($tm['accent_color']); ?>;"></span>
                                            <span class="small text-muted ms-1"><?php echo htmlspecialchars($tm['accent_color']); ?></span>
                                        <?php else: ?>-<?php endif; ?>
                                    </td>
                                    <td><?php echo $tm['is_default'] ? '<span class="badge badge-soft-success">Default</span>' : '<span class="badge badge-soft-light text-muted">Khusus</span>'; ?></td>
                                    <td class="text-end pe-4">
                                        <div class="d-inline-flex gap-2">
                                            <button class="btn btn-action btn-soft-primary" title="Edit" onclick="editCertTemplate(<?php echo htmlspecialchars(json_encode($tm)); ?>)"><i class="fas fa-edit"></i></button>
                                            <button class="btn btn-action btn-soft-danger" title="Hapus" onclick="deleteItem('cert_templates', <?php echo $tm['id']; ?>)"><i class="fas fa-trash"></i></button>
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
