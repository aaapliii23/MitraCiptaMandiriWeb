<!-- GALLERY PAGE -->
        <div class="row align-items-center mb-5 g-3">
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

        <div class="row g-4">
            <?php foreach ($gallery as $g): ?>
                <div class="col-md-4 col-sm-6">
                    <div class="card h-100 border-0 shadow-sm overflow-hidden">
                        <div class="position-relative" style="height: 220px;">
                            <img src="<?php echo htmlspecialchars(getImgSrc($g['image'])); ?>" class="w-100 h-100" style="object-fit: cover;" alt="<?php echo htmlspecialchars($g['title']); ?>">
                            <div class="position-absolute top-0 end-0 p-3">
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
