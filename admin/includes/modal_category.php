<!-- Category Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 1rem;">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" id="categoryModalTitle">Tambah Kategori Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="categoryForm" action="<?php echo $adminBase; ?>/actions/manage_categories.php" method="POST" onsubmit="event.preventDefault(); submitAjaxForm('categoryForm');">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(mcm_csrf_token()); ?>">
                <div class="modal-body p-4">
                    <input type="hidden" name="action" value="create" id="categoryAction">
                    <input type="hidden" name="id" id="categoryId">
                    <div class="mb-2">
                        <label class="form-label small fw-medium">Nama Kategori</label>
                        <input type="text" class="form-control" name="name" id="categoryName" placeholder="Contoh: Digital Marketing" required>
                    </div>
                </div>
                <div class="modal-footer border-top-0 px-4 pb-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold" id="categorySubmitBtn">Simpan Kategori</button>
                </div>
            </form>
        </div>
    </div>
</div>
