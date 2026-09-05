<!-- Material Modal -->
<div class="modal fade" id="materialModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow" style="border-radius: 1rem;">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" id="materialModalTitle">Tambah Materi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="materialForm" action="<?php echo $adminBase; ?>/actions/manage_materials.php" method="POST" enctype="multipart/form-data" onsubmit="event.preventDefault(); submitAjaxForm('materialForm');">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(mcm_csrf_token()); ?>">
                <div class="modal-body p-4">
                    <input type="hidden" name="action" id="materialAction" value="create">
                    <input type="hidden" name="id" id="materialId">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Kelas</label>
                            <select class="form-select" name="class_id" id="materialClass" required>
                                <option value="">Pilih kelas...</option>
                                <?php foreach ($classes as $c): ?>
                                    <option value="<?php echo (int)$c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Tipe Materi</label>
                            <select class="form-select" name="type" id="materialType" onchange="materialTypeChanged()">
                                <option value="text">Teks</option>
                                <option value="video">Video (YouTube)</option>
                                <option value="pdf">PDF / Dokumen</option>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label small fw-bold">Judul Materi</label>
                            <input type="text" class="form-control" name="title" id="materialTitle" required placeholder="Contoh: Modul 1 - Pengenalan Dasar">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Urutan</label>
                            <input type="number" class="form-control" name="sort_order" id="materialSort" value="0" min="0" placeholder="0">
                        </div>
                    </div>

                    <div class="mt-3" id="materialTextWrap">
                        <label class="form-label small fw-bold">Isi Materi (Teks)</label>
                        <textarea class="form-control" name="content" id="materialContent" rows="8" placeholder="Tulis isi materi di sini..."></textarea>
                    </div>

                    <div class="mt-3 d-none" id="materialVideoWrap">
                        <label class="form-label small fw-bold">URL Video YouTube</label>
                        <input type="text" class="form-control" name="content" id="materialVideo" placeholder="https://www.youtube.com/watch?v=xxxxxx">
                        <small class="text-muted">Mendukung link youtube.com, youtu.be, atau short.</small>
                    </div>

                    <div class="mt-3 d-none" id="materialPdfWrap">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Upload File PDF</label>
                            <input type="file" class="form-control" name="content_file" id="materialFile" accept="application/pdf">
                            <small class="text-muted" id="materialFileHint">Unggah file PDF (maksimal berapa pun; jenis .pdf).</small>
                        </div>
                        <div class="mb-1">
                            <label class="form-label small fw-bold">atau URL PDF Eksternal</label>
                            <input type="text" class="form-control" name="content" id="materialPdfUrl" placeholder="https://contoh.com/file.pdf">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 px-4 pb-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Simpan Materi</button>
                </div>
            </form>
        </div>
    </div>
</div>