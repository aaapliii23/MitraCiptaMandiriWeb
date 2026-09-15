<!-- Admin Modal -->
<div class="modal fade" id="adminModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 1rem;">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" id="adminModalTitle">Tambah Admin Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="adminForm" action="<?php echo $adminBase; ?>/actions/manage_admins.php" method="POST" onsubmit="event.preventDefault(); submitAjaxForm('adminForm');">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(mcm_csrf_token()); ?>">
                <div class="modal-body p-4">
                    <input type="hidden" name="action" value="create" id="adminAction">
                    <input type="hidden" name="id" id="adminId">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Username Admin</label>
                        <input type="text" class="form-control" name="username" id="adminUsername" required placeholder="Contoh: admin_mcm">
                    </div>
                    <div class="mb-1">
                        <label class="form-label small fw-bold" id="adminPasswordLabel">Password Baru</label>
                        <input type="password" class="form-control" name="password" id="adminPassword" required placeholder="Minimal 6 karakter">
                        <small class="text-muted d-none" id="adminPasswordHint">Biarkan kosong jika tidak ingin mengubah password.</small>
                    </div>
                </div>
                <div class="modal-footer border-top-0 px-4 pb-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold" id="adminSubmitBtn">Buat Akun</button>
                </div>
            </form>
        </div>
    </div>
</div>
