<!-- CATEGORIES PAGE -->
        <div class="row align-items-center mb-5 g-3" data-aos="fade-down">
            <div class="col-md-6">
                <h2 class="fw-bold mb-1 text-dark">Kategori Pelatihan</h2>
                <p class="text-muted mb-0">Kelola jenis-jenis pelatihan yang tersedia di MCM.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <button class="btn btn-primary px-4 shadow-sm rounded-pill" onclick="resetCategoryForm(); showModal('categoryModal');">
                    <i class="fas fa-plus me-2"></i>Tambah Kategori
                </button>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Nama Kategori</th>
                                <th>Slug</th>
                                <th>Tgl Dibuat</th>
                                <th class="text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($categories)): ?>
                                <tr><td colspan="4" class="text-center py-5 text-muted">Belum ada kategori.</td></tr>
                            <?php else: ?>
                                <?php foreach ($categories as $cat): ?>
                                    <tr>
                                        <td class="ps-4"><span class="fw-bold text-dark"><?php echo htmlspecialchars($cat['name']); ?></span></td>
                                        <td><code><?php echo htmlspecialchars($cat['slug']); ?></code></td>
                                        <td><?php echo date('d M Y', strtotime($cat['created_at'])); ?></td>
                                        <td class="text-end pe-4">
                                            <div class="d-inline-flex gap-2">
                                                <button class="btn btn-action btn-soft-primary" onclick="editCategory(<?php echo htmlspecialchars(json_encode($cat)); ?>)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-action btn-soft-danger" onclick="deleteItem('categories', <?php echo $cat['id']; ?>)">
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
