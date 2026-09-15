<!-- CATEGORIES PAGE -->
        <div class="row align-items-center mb-4 g-3" data-aos="fade-down">
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

        <div data-bulk-table="categories">
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
                                <th>Nama Kategori</th>
                                <th>Slug</th>
                                <th>Tgl Dibuat</th>
                                <th class="text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($categories)): ?>
                                <tr><td colspan="5" class="text-center py-5 text-muted">Belum ada kategori.</td></tr>
                            <?php else: ?>
                                <?php foreach ($categories as $cat): ?>
                                    <tr>
                                        <td class="col-check"><input type="checkbox" class="bulk-row-check js-bulk-row" value="<?php echo (int)$cat['id']; ?>"></td>
                                        <td><span class="fw-bold text-dark cell-ellipsis" title="<?php echo htmlspecialchars($cat['name']); ?>" style="max-width:180px;"><?php echo htmlspecialchars($cat['name']); ?></span></td>
                                        <td><code class="small"><?php echo htmlspecialchars($cat['slug']); ?></code></td>
                                        <td class="small text-muted text-nowrap"><?php echo date('d M y', strtotime($cat['created_at'])); ?></td>
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
        </div>
