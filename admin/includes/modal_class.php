<!-- Class Modal -->
<div class="modal fade" id="classModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow" style="border-radius: 1rem;">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" id="classModalTitle">Tambah Kelas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
<form id="classForm" action="<?php echo $adminBase; ?>/actions/manage_classes.php" method="POST" enctype="multipart/form-data" onsubmit="event.preventDefault(); submitAjaxForm('classForm');">
                <div class="modal-body p-4">
                    <input type="hidden" name="action" id="classAction" value="create">
                    <input type="hidden" name="id" id="classId">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-medium">Nama Kelas</label>
                            <input type="text" class="form-control" name="name" id="className" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium">Tanggal Mulai Pelatihan</label>
                            <input type="date" class="form-control" name="start_date" id="classStartDate" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium">Kategori</label>
                            <select class="form-select" name="category" id="classCategory" required>
                                <option value="">Pilih Kategori...</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo htmlspecialchars($cat['name']); ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium text-dark"><i class="fas fa-chalkboard-teacher text-primary me-1"></i>Harga Kelas Offline / Tatap Muka (Rp) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="price_offline" id="classPriceOffline" placeholder="Misal: 1200000" min="0" required>
                            <input type="hidden" name="price" id="classPrice">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium text-dark"><i class="fas fa-laptop-code text-info me-1"></i>Harga Kelas Online / LMS (Rp) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="price_online" id="classPriceOnline" placeholder="Misal: 850000" min="0" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-medium text-dark"><i class="fab fa-whatsapp text-success me-1"></i>Link Grup WhatsApp <span class="text-muted fw-normal">(khusus peserta offline)</span></label>
                            <input type="url" class="form-control" name="wa_group_link" id="classWaGroupLink" placeholder="https://chat.whatsapp.com/..." id="classWaGroupLink">
                            <small class="text-muted d-block mt-1"><i class="fas fa-info-circle me-1"></i>Link ini akan ditampilkan kepada peserta yang mendaftar dengan metode <strong>Offline</strong>. Kosongkan jika belum ada.</small>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-medium">Deskripsi</label>
                            <textarea class="form-control" name="description" id="classDescription" rows="3" required></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-medium">Fitur / Materi (Pisahkan dengan koma)</label>
                            <input type="text" class="form-control" name="features" id="classFeatures" placeholder="Materi 1, Materi 2, Sertifikat..." required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-medium">Gambar Cover</label>
                            <input type="file" class="form-control" name="image" id="classImage" accept="image/*">
                            <small class="text-muted d-block mt-1">Biarkan kosong jika tidak ingin mengubah gambar (saat edit).</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 px-4 pb-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Simpan Kelas</button>
                </div>
            </form>
        </div>
    </div>
</div>
