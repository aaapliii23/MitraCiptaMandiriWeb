<!-- EXAMINERS PAGE -->
        <div class="row align-items-center mb-5 g-3" data-aos="fade-down">
            <div class="col-md-6">
                <h2 class="fw-bold mb-1 text-dark">Penguji (Asesor)</h2>
                <p class="text-muted mb-0">Kelola data penguji/asesor yang akan dipilih calon peserta saat profil.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <button class="btn btn-primary px-4 shadow-sm rounded-pill" onclick="resetExaminerForm(); showModal('examinerModal');">
                    <i class="fas fa-plus me-2"></i>Tambah Penguji
                </button>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Penguji</th>
                                <th>Spesialisasi</th>
                                <th>Sertifikasi</th>
                                <th>Tgl Ditambahkan</th>
                                <th class="text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($examiners)): ?>
                                <tr><td colspan="5" class="text-center py-5 text-muted">Belum ada penguji.</td></tr>
                            <?php else: ?>
                                <?php foreach ($examiners as $ex): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                <img src="<?php echo htmlspecialchars(getImgSrc($ex['image'])); ?>" alt="<?php echo htmlspecialchars($ex['name']); ?>" class="rounded-circle me-3 border border-2 border-white shadow-sm" style="width: 40px; height: 40px; object-fit: cover;">
                                                <span class="fw-bold text-dark"><?php echo htmlspecialchars($ex['name']); ?></span>
                                            </div>
                                        </td>
                                        <td class="text-muted small"><?php echo htmlspecialchars($ex['specialization']); ?></td>
                                        <td class="text-muted small"><?php echo htmlspecialchars($ex['certifications'] ?: '-'); ?></td>
                                        <td class="small text-muted"><?php echo date('d M Y', strtotime($ex['created_at'])); ?></td>
                                        <td class="text-end pe-4">
                                            <div class="d-inline-flex gap-2">
                                                <button class="btn btn-action btn-soft-primary" title="Edit" onclick="editExaminer(<?php echo htmlspecialchars(json_encode($ex)); ?>)"><i class="fas fa-edit"></i></button>
                                                <button class="btn btn-action btn-soft-danger" title="Hapus" onclick="deleteItem('examiners', <?php echo $ex['id']; ?>)"><i class="fas fa-trash"></i></button>
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