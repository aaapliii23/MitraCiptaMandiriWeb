<!-- MATERIALS PAGE -->
        <div class="row align-items-center mb-5 g-3" data-aos="fade-down">
            <div class="col-md-6">
                <h2 class="fw-bold mb-1 text-dark">Materi LMS</h2>
                <p class="text-muted mb-0">Kelola materi belajar per paket pelatihan untuk peserta LMS.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <button class="btn btn-primary px-4 shadow-sm rounded-pill" onclick="resetMaterialForm(); showModal('materialModal');">
                    <i class="fas fa-plus me-2"></i>Tambah Materi
                </button>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Judul Materi</th>
                                <th>Kelas</th>
                                <th>Tipe</th>
                                <th class="text-center">Urutan</th>
                                <th class="text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($materials)): ?>
                                <tr><td colspan="5" class="text-center py-5 text-muted">Belum ada materi.</td></tr>
                            <?php else: ?>
                                <?php foreach ($materials as $m): ?>
                                    <tr>
                                        <td class="ps-4 fw-bold text-dark"><?php echo htmlspecialchars($m['title']); ?></td>
                                        <td class="text-muted small"><?php echo htmlspecialchars($m['class_name']); ?></td>
                                        <td>
                                            <span class="badge rounded-pill px-3 text-uppercase
                                                <?php echo $m['type'] === 'video' ? 'bg-danger bg-opacity-10 text-danger' : ($m['type'] === 'pdf' ? 'bg-warning bg-opacity-10 text-warning' : 'bg-info bg-opacity-10 text-info'); ?>">
                                                <i class="fas <?php echo $m['type'] === 'video' ? 'fa-video' : ($m['type'] === 'pdf' ? 'fa-file-pdf' : 'fa-align-left'); ?> me-1"></i><?php echo $m['type']; ?>
                                            </span>
                                        </td>
                                        <td class="text-center text-muted small"><?php echo (int)$m['sort_order']; ?></td>
                                        <td class="text-end pe-4">
                                            <div class="d-inline-flex gap-2">
                                                <button class="btn btn-action btn-soft-primary" title="Kelola Quiz" onclick="openQuizManager(<?php echo (int)$m['id']; ?>, '<?php echo htmlspecialchars(addslashes($m['title'])); ?>')"><i class="fas fa-question-circle"></i></button>
                                                <button class="btn btn-action btn-soft-primary" title="Edit" onclick="editMaterial(<?php echo htmlspecialchars(json_encode($m)); ?>)"><i class="fas fa-edit"></i></button>
                                                <button class="btn btn-action btn-soft-danger" title="Hapus" onclick="deleteItem('materials', <?php echo (int)$m['id']; ?>)"><i class="fas fa-trash"></i></button>
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