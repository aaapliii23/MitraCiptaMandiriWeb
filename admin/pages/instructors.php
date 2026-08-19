<!-- INSTRUCTORS PAGE -->
        <div class="row align-items-center mb-4 g-3">
            <div class="col-md-6">
                <h2 class="fw-bold mb-1 text-dark">Tim Pengajar</h2>
                <p class="text-muted mb-0">Data instruktur dan penguji profesional MCM.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <button class="btn btn-primary px-4 shadow-sm rounded-pill" onclick="showModal('instructorModal')">
                    <i class="fas fa-plus me-2"></i>Tambah Instruktur
                </button>
            </div>
        </div>

        <div class="row g-4">
            <?php foreach ($instructors as $ins): ?>
                <div class="col-md-4 col-xl-3">
                    <div class="card border-0 shadow-sm text-center p-4">
                        <div class="position-relative mb-3 d-inline-block mx-auto">
                            <img src="<?php echo htmlspecialchars(getImgSrc($ins['image'])); ?>" class="rounded-circle shadow-sm" style="width: 100px; height: 100px; object-fit: cover; border: 4px solid #fff;">
                            <div class="position-absolute bottom-0 end-0">
                                <span class="badge badge-soft-success rounded-circle p-2 border border-2 border-white"><i class="fas fa-check"></i></span>
                            </div>
                        </div>
                        <h6 class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($ins['name']); ?></h6>
                        <div class="text-primary small fw-bold mb-1"><?php echo htmlspecialchars($ins['specialization']); ?></div>
                        <div class="mb-4"><span class="badge bg-primary bg-opacity-10 text-primary rounded-pill"><?php echo htmlspecialchars($ins['category'] ?: 'Umum'); ?></span></div>
                        <div class="d-flex justify-content-center gap-2">
                            <button class="btn btn-action btn-soft-primary" onclick="editInstructor(<?php echo htmlspecialchars(json_encode($ins)); ?>)">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-action btn-soft-danger" onclick="deleteItem('instructors', <?php echo $ins['id']; ?>)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
