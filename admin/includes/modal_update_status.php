<!-- Update Status Modal -->
<div class="modal fade" id="updateStatusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 1rem;">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold">Update Status Pesanan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted small mb-4">Ubah status pesanan untuk <strong id="modalParticipantName" class="text-dark"></strong>.</p>
                <form id="updateStatusForm" action="<?php echo $adminBase; ?>/actions/update_order.php" method="POST" class="ajax-form">
                    <input type="hidden" name="order_id" id="modalOrderId">
                    <div class="mb-4">
                        <select class="form-select form-control-lg" name="status" id="modalOrderStatus" required>
                            <option value="pending">Pending (Menunggu Pembayaran)</option>
                            <option value="confirmed">Lunas (Confirmed)</option>
                            <option value="cancelled">Dibatalkan</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2 rounded-pill fw-bold">Simpan Perubahan</button>
                </form>
            </div>
        </div>
    </div>
</div>
