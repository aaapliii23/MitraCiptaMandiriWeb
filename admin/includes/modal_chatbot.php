<!-- Chatbot Intent Modal -->
<div class="modal fade" id="chatbotIntentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 1rem;">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" id="chatbotIntentModalTitle">Tambah Intent</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="chatbotIntentForm" action="<?php echo $adminBase; ?>/actions/manage_chatbot.php" method="POST" onsubmit="event.preventDefault(); submitAjaxForm('chatbotIntentForm');">
                <div class="modal-body p-4">
                    <input type="hidden" name="action" value="create" id="chatbotIntentAction">
                    <input type="hidden" name="id" id="chatbotIntentId">
                    <div class="mb-2">
                        <label class="form-label small fw-medium">Nama Intent</label>
                        <input type="text" class="form-control" name="intent" id="chatbotIntentName" required placeholder="contoh: pendaftaran" pattern="[a-z0-9_]{1,50}">
                        <div class="form-text">Hanya huruf kecil, angka, dan underscore.</div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-medium">Kata Kunci</label>
                        <input type="text" class="form-control" name="keywords" id="chatbotIntentKeywords" required placeholder="daftar, daftarkan, register, ikut">
                        <div class="form-text">Pisahkan dengan koma. Pesan yang mengandung kata kunci akan cocok dengan intent ini.</div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-medium">Balasan Otomatis</label>
                        <textarea class="form-control" name="reply" id="chatbotIntentReply" rows="5" required></textarea>
                        <div class="form-text">Gunakan <code>{classes}</code> untuk daftar program dan <code>{prices}</code> untuk daftar harga (diisi otomatis dari data kelas).</div>
                    </div>
                    <div class="form-check form-switch mt-3">
                        <input class="form-check-input" type="checkbox" role="switch" name="enabled" id="chatbotIntentEnabled" checked>
                        <label class="form-check-label small fw-medium" for="chatbotIntentEnabled">Aktif</label>
                    </div>
                </div>
                <div class="modal-footer border-top-0 px-4 pb-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold" id="chatbotIntentSubmitBtn">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>