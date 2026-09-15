<!-- Cert Template Modal -->
<div class="modal fade" id="certTemplateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 1rem;">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" id="certTemplateModalTitle">Tambah Template Sertifikat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="certTemplateForm" action="<?php echo $adminBase; ?>/actions/manage_cert_templates.php" method="POST" enctype="multipart/form-data" onsubmit="event.preventDefault(); submitAjaxForm('certTemplateForm');">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(mcm_csrf_token()); ?>">
                <div class="modal-body p-4">
                    <input type="hidden" name="action" id="certTemplateAction" value="create">
                    <input type="hidden" name="id" id="certTemplateId">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Template</label>
                        <input type="text" class="form-control" name="name" id="certTemplateName" required placeholder="Contoh: Sertifikat Kecantikan">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Program (Kelas)</label>
                        <select class="form-select" name="class_id" id="certTemplateClass">
                            <option value="">Semua Program (Default)</option>
                            <?php foreach ($classes as $cl): ?>
                                <option value="<?php echo (int)$cl['id']; ?>"><?php echo htmlspecialchars($cl['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Layout</label>
                        <select class="form-select" name="layout" id="certTemplateLayout">
                            <option value="default">Standar</option>
                            <option value="elegant">Elegant</option>
                            <option value="modern">Modern</option>
                            <option value="premium">Premium</option>
                        </select>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold">Warna Aksen</label>
                            <input type="color" class="form-control form-control-color w-100" name="accent_color" id="certTemplateColor" value="#1e40af">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">Gambar Latar</label>
                            <input type="file" class="form-control" name="bg_image" id="certTemplateBg" accept=".jpg,.jpeg,.png,.webp">
                            <small class="text-muted">Opsional.</small>
                        </div>
                    </div>
                    <div class="form-check form-switch mb-1">
                        <input class="form-check-input" type="checkbox" name="is_default" id="certTemplateDefault" value="1">
                        <label class="form-check-label small" for="certTemplateDefault">Jadikan Template Default</label>
                    </div>
                </div>
                <div class="modal-footer border-top-0 px-4 pb-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Simpan Template</button>
                </div>
            </form>
        </div>
    </div>
</div>