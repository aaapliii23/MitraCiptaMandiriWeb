<!-- Examiner Modal -->
<div class="modal fade" id="examinerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 1rem;">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" id="examinerModalTitle">Tambah Penguji</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="examinerForm" action="<?php echo $adminBase; ?>/actions/manage_examiners.php" method="POST" enctype="multipart/form-data" onsubmit="event.preventDefault(); submitAjaxForm('examinerForm');">
                <div class="modal-body p-4">
                    <input type="hidden" name="action" id="examinerAction" value="create">
                    <input type="hidden" name="id" id="examinerId">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Lengkap</label>
                        <input type="text" class="form-control" name="name" id="examinerName" required placeholder="Gunakan gelar jika ada">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Spesialisasi</label>
                        <input type="text" class="form-control" name="specialization" id="examinerSpec" required placeholder="Contoh: Asesor Public Speaking & Komunikasi">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Latar Belakang / Bio</label>
                        <textarea class="form-control" name="bio" id="examinerBio" rows="3" placeholder="Cerita singkat tentang pengalaman dan kualifikasi penguji"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Sertifikasi</label>
                        <textarea class="form-control" name="certifications" id="examinerCerts" rows="2" placeholder="Pisahkan dengan koma, contoh: BNSP, Kemenaker"></textarea>
                    </div>
                    <div class="mb-1">
                        <label class="form-label small fw-bold">Foto Profil</label>
                        <input type="file" class="form-control" name="image" id="examinerImage" accept="image/*">
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