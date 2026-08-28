<!-- GALLERY PAGE -->
        <div class="row align-items-center mb-4 g-3">
            <div class="col-md-6">
                <h2 class="fw-bold mb-1 text-dark">Galeri Foto</h2>
                <p class="text-muted mb-0">Dokumentasi visual kegiatan dan fasilitas.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <button class="btn btn-primary px-4 shadow-sm rounded-pill" onclick="resetGalleryForm(); showModal('galleryModal');">
                    <i class="fas fa-upload me-2"></i>Tambah Foto
                </button>
            </div>
        </div>

        <div data-bulk-table="gallery">
        <div class="admin-table-toolbar d-none" data-bulk-toolbar>
            <div class="small fw-bold text-primary"><i class="fas fa-check-square me-1"></i><span data-bulk-count>0 dipilih</span></div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" onclick="document.querySelectorAll('[data-bulk-table=gallery] .js-bulk-row').forEach(cb=>cb.checked=false); document.querySelector('[data-bulk-table=gallery] .js-bulk-select-all').checked=false; document.querySelector('[data-bulk-table=gallery] .js-bulk-select-all').indeterminate=false; document.querySelector('[data-bulk-table=gallery] [data-bulk-toolbar]').classList.add('d-none');">Batal</button>
                <button type="button" class="btn btn-sm btn-danger rounded-pill px-3 fw-bold" data-bulk-delete><i class="fas fa-trash me-1"></i>Hapus Terpilih (<span data-bulk-count-num>0</span>)</button>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2 mb-3">
            <label class="small fw-bold text-muted mb-0" style="cursor:pointer;"><input type="checkbox" class="bulk-select-all js-bulk-select-all me-1"> Pilih semua</label>
        </div>
        <div class="row g-4">
            <?php foreach ($gallery as $g): ?>
                <div class="col-md-4 col-sm-6" data-bulk-card>
                    <div class="card h-100 border-0 shadow-sm overflow-hidden">
                        <input type="checkbox" class="bulk-row-check js-bulk-row position-absolute" value="<?php echo (int)$g['id']; ?>" style="width:18px;height:18px;accent-color:#2563eb;z-index:2; top:10px; left:10px; cursor:pointer; box-shadow:0 2px 6px rgba(0,0,0,0.2);">
                        <div class="position-relative" style="height: 220px;">
                            <img src="<?php echo htmlspecialchars(getImgSrc($g['image'])); ?>" class="w-100 h-100" style="object-fit: cover;" alt="<?php echo htmlspecialchars($g['title']); ?>">
                            <div class="position-absolute top-0 end-0 p-3 d-flex gap-2">
                                <?php if ((int)($g['show_on_home'] ?? 0) === 1): ?>
                                    <span class="badge bg-primary text-white rounded-pill"><i class="fas fa-home me-1"></i>Beranda</span>
                                <?php endif; ?>
                                <span class="badge bg-dark bg-opacity-50 text-white rounded-pill backdrop-blur"><?php echo htmlspecialchars($g['category']); ?></span>
                            </div>
                        </div>
                        <div class="card-body p-3 d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold text-dark mb-0"><?php echo htmlspecialchars($g['title']); ?></h6>
                            <div class="d-flex gap-2">
                                <button class="btn btn-action btn-soft-primary" onclick="editGallery(<?php echo htmlspecialchars(json_encode($g)); ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-action btn-soft-danger" onclick="deleteItem('gallery', <?php echo $g['id']; ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        </div>