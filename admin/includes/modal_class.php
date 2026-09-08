<!-- Class Modal -->
<div class="modal fade" id="classModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow" style="border-radius: 1rem;">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" id="classModalTitle">Tambah Kelas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
<form id="classForm" action="<?php echo $adminBase; ?>/actions/manage_classes.php" method="POST" enctype="multipart/form-data" onsubmit="event.preventDefault(); submitAjaxForm('classForm');">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(mcm_csrf_token()); ?>">
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
                            <label class="form-label small fw-medium">Harga Offline (Rp)</label>
                            <input type="number" class="form-control" name="price_offline" id="classPriceOffline" placeholder="Misal: 1200000" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium">Harga Online (Rp)</label>
                            <input type="number" class="form-control" name="price_online" id="classPriceOnline" placeholder="Misal: 960000" required>
                            <small class="text-muted" style="font-size: 0.7rem;">Online biasanya 20% lebih hemat (0,8× offline)</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium">Mode Tersedia</label>
                            <select class="form-select" name="mode_available" id="classModeAvailable" required>
                                <option value="both">Keduanya (Online & Offline)</option>
                                <option value="offline">Hanya Offline</option>
                                <option value="online">Hanya Online</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium">Harga Lama (legacy, otomatis)</label>
                            <input type="number" class="form-control bg-light" name="price" id="classPrice" placeholder="Otomatis = Offline" readonly>
                            <small class="text-muted" style="font-size: 0.7rem;">Diisi otomatis dari Harga Offline</small>
                        </div>
                        <div class="col-12" id="waGroupLinkWrap" style="display:none;">
                            <label class="form-label small fw-medium">Link Grup WhatsApp (Offline) <span class="text-danger" id="waLinkRequiredMark" style="display:none;">*</span> <span class="text-muted fw-normal">(wajib jika mode mencakup Offline)</span></label>
                            <input type="url" class="form-control" name="whatsapp_group_link" id="classWaLink" placeholder="https://chat.whatsapp.com/..." pattern="https://chat\.whatsapp\.com/.*">
                            <small class="text-muted d-block mt-1">Harus diawali <code>https://chat.whatsapp.com/</code> — kosongkan jika belum ada, nanti bisa diisi admin.</small>
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
                            <label class="form-label small fw-medium">Gambar Cover Pelatihan</label>
                            <input type="file" class="form-control" name="image" id="classImage" accept="image/*">
                            <small class="text-muted d-block mt-1">Format: JPG, PNG, WEBP. Biarkan kosong jika tidak ingin mengubah gambar (saat edit).</small>
                            
                            <!-- Pratinjau Foto Utuh -->
                            <div id="classImagePreviewContainer" class="mt-3 p-3 rounded-4 border bg-light text-center" style="display: none;">
                                <div class="d-flex justify-content-between align-items-center mb-2 px-1">
                                    <span class="small fw-bold text-dark" id="classImagePreviewLabel"><i class="fas fa-image me-1 text-primary"></i>Pratinjau Foto</span>
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-0" id="classImageClearBtn" style="display: none; font-size: 0.75rem;" onclick="clearClassImagePreview()"><i class="fas fa-times me-1"></i>Batal Ganti</button>
                                </div>
                                <div id="classImagePreviewBox" class="position-relative d-flex align-items-center justify-content-center p-2" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 0.75rem; overflow: hidden; min-height: 160px; max-height: 280px;">
                                    <img id="classImagePreview" src="" alt="Pratinjau Foto" class="img-fluid" style="max-height: 260px; max-width: 100%; width: auto; height: auto; object-fit: contain; border-radius: 0.5rem;">
                                </div>
                                <div class="small text-muted mt-2" style="font-size: 0.75rem;">
                                    <i class="fas fa-check-circle text-success me-1"></i>Foto ditampilkan 100% utuh proporsional tanpa terpotong
                                </div>
                            </div>
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
