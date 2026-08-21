<!-- Gallery Modal -->
<div class="modal fade" id="galleryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 1rem;">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" id="galleryModalTitle">Tambah Foto Galeri</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="galleryForm" action="<?php echo $adminBase; ?>/actions/manage_gallery.php" method="POST" enctype="multipart/form-data" onsubmit="event.preventDefault(); submitAjaxForm('galleryForm');">
                <div class="modal-body p-4">
                    <input type="hidden" name="action" value="create" id="galleryAction">
                    <input type="hidden" name="id" id="galleryId">
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Judul Foto (Akan digunakan untuk semua foto)</label>
                        <input type="text" class="form-control" name="title" id="galleryTitle" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Filter / Kategori</label>
                         <select class="form-select" name="category" required id="galleryCategory">
                            <option value="umum">Umum</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo htmlspecialchars($cat['slug']); ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-medium">File Gambar (Bisa pilih banyak sekaligus)</label>
                        <input type="file" class="form-control" name="images[]" id="galleryImageInput" accept="image/*" required multiple onchange="updateGalleryFilePreview(this)">
                        <div id="galleryFilePreview" class="mt-2"></div>
                        <small class="text-muted d-block mt-1">Gunakan tombol Ctrl / Shift (Windows) atau Cmd (Mac) saat memilih file untuk memilih lebih dari 1 foto sekaligus.</small>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" name="show_on_home" id="galleryShowHome" value="1">
                        <label class="form-check-label small fw-medium" for="galleryShowHome">
                            <i class="fas fa-home me-1 text-primary"></i>Tampil di Slide Foto Beranda
                        </label>
                        <small class="text-muted d-block ms-0 mt-1">Centang agar foto kegiatan ini muncul pada slide foto halaman depan.</small>
                    </div>
                </div>
                <div class="modal-footer border-top-0 px-4 pb-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold" id="gallerySubmitBtn">Upload Foto</button>
                </div>
            </form>
        </div>
    </div>
</div>
