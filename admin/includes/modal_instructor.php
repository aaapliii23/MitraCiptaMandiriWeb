<!-- Instructor Modal -->
<div class="modal fade" id="instructorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 1rem;">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" id="instructorModalTitle">Tambah Instruktur / Penguji</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="instructorForm" action="<?php echo $adminBase; ?>/actions/manage_instructors.php" method="POST" enctype="multipart/form-data" onsubmit="event.preventDefault(); submitAjaxForm('instructorForm');">
                <div class="modal-body p-4">
                    <input type="hidden" name="action" id="instructorAction" value="create">
                    <input type="hidden" name="id" id="instructorId">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Lengkap</label>
                        <input type="text" class="form-control" name="name" id="instructorName" required placeholder="Gunakan gelar jika ada">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Kategori <span class="text-danger">*</span></label>
                        <select class="form-select" name="category" id="instructorCategory" required>
                            <option value="">-- Pilih Kategori --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo htmlspecialchars($cat['name']); ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted">Kategori Instruktur & Penguji (wajib), mengikuti kategori pelatihan.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Spesialisasi / Gelar</label>
                        <input type="text" class="form-control" name="specialization" id="instructorSpec" required placeholder="Contoh: Ahli Tata Rias">
                    </div>
                    <div class="mb-1">
                        <label class="form-label small fw-bold">Foto Profil</label>
                        <input type="file" class="form-control" name="image" id="instructorImage" accept="image/*">
                        <small class="text-muted">Biarkan kosong jika tidak ingin mengubah foto.</small>
                    </div>
                </div>
                <div class="modal-footer border-top-0 px-4 pb-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>
