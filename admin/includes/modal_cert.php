<!-- Cert Modal -->
<div class="modal fade" id="certModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 1rem;">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" id="certModalTitle">Tambah Dokumen Sertifikasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="certForm" action="<?php echo $adminBase; ?>/actions/manage_certs.php" method="POST" enctype="multipart/form-data" onsubmit="event.preventDefault(); submitAjaxForm('certForm');">
                <div class="modal-body p-4">
                    <input type="hidden" name="action" id="certAction" value="create">
                    <input type="hidden" name="id" id="certId">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Dokumen</label>
                        <input type="text" class="form-control" name="title" id="certTitle" required placeholder="Contoh: Izin LPK MCM">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Keterangan Singkat</label>
                        <textarea class="form-control" name="description" id="certDesc" rows="2" placeholder="Detail singkat mengenai dokumen ini"></textarea>
                    </div>
                    <div class="mb-1">
                        <label class="form-label small fw-bold">Scan Dokumen (Gambar)</label>
                        <input type="file" class="form-control" name="image" id="certImage" accept="image/*">
                        <small class="text-muted">Biarkan kosong jika tidak ingin mengubah dokumen.</small>
                    </div>
                </div>
                <div class="modal-footer border-top-0 px-4 pb-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Simpan Dokumen</button>
                </div>
            </form>
        </div>
    </div>
</div>
