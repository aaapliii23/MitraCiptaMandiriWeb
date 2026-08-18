<!-- Testimonial Modal -->
<div class="modal fade" id="testimonialModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 1rem;">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" id="testimonialModalTitle">Edit Testimoni</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="testimonialForm" action="<?php echo $adminBase; ?>/actions/manage_testimonials.php" method="POST" onsubmit="event.preventDefault(); submitAjaxForm('testimonialForm');">
                <div class="modal-body p-4">
                    <input type="hidden" name="action" value="update" id="testimonialAction">
                    <input type="hidden" name="id" id="testimonialId">
                    <div class="mb-2">
                        <label class="form-label small fw-medium">Nama</label>
                        <input type="text" class="form-control" name="name" id="testimonialName" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-medium">Rating</label>
                        <select class="form-select" name="rating" id="testimonialRating">
                            <option value="5">5 - Sangat Baik</option>
                            <option value="4">4 - Baik</option>
                            <option value="3">3 - Cukup</option>
                            <option value="2">2 - Kurang</option>
                            <option value="1">1 - Buruk</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-medium">Ulasan</label>
                        <textarea class="form-control" name="review" id="testimonialReview" rows="4" required></textarea>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-medium">Status</label>
                        <select class="form-select" name="status" id="testimonialStatus">
                            <option value="pending">Menunggu</option>
                            <option value="approved">Disetujui</option>
                            <option value="rejected">Ditolak</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top-0 px-4 pb-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold" id="testimonialSubmitBtn">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>