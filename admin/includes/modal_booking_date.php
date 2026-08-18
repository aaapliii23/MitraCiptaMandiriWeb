<!-- Booking Date Modal -->
<div class="modal fade" id="bookingDateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 1rem;">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold">Atur Jadwal Booking</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted small mb-4">Tentukan jadwal kedatangan untuk <strong id="modalBookingName" class="text-dark"></strong>.</p>
                <form id="bookingDateForm" action="<?php echo $adminBase; ?>/actions/update_booking.php" method="POST" class="ajax-form">
                    <input type="hidden" name="booking_id" id="modalBookingId">
                    <div class="mb-4">
                        <label class="form-label small fw-bold">Tanggal Jadwal</label>
                        <input type="date" class="form-control form-control-lg" name="booking_date" id="modalBookingDate" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2 rounded-pill fw-bold">Simpan Jadwal</button>
                </form>
            </div>
        </div>
    </div>
</div>