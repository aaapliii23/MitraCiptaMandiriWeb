<!-- Finance Transaction Modal -->
<div class="modal fade" id="financeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 1rem;">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" id="financeModalTitle">Tambah Transaksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="financeForm" action="<?php echo $adminBase; ?>/actions/manage_finance.php" method="POST" onsubmit="event.preventDefault(); submitAjaxForm('financeForm');">
                <div class="modal-body p-4">
                    <input type="hidden" name="action" value="create" id="financeAction">
                    <input type="hidden" name="id" id="financeId">
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Tipe Transaksi</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="type" id="financeTypeIn" value="in" checked>
                            <label class="btn btn-outline-success rounded-pill me-2 fw-bold" for="financeTypeIn" onclick="financeTypeChanged('in')">
                                <i class="fas fa-arrow-down me-1"></i>Uang Masuk
                            </label>
                            <input type="radio" class="btn-check" name="type" id="financeTypeOut" value="out">
                            <label class="btn btn-outline-danger rounded-pill fw-bold" for="financeTypeOut" onclick="financeTypeChanged('out')">
                                <i class="fas fa-arrow-up me-1"></i>Uang Keluar
                            </label>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-medium">Kategori</label>
                        <select class="form-select" name="category" id="financeCategory" required>
                            <option value="pemasukan_kursus">Pemasukan Kursus</option>
                            <option value="sewa">Sewa / Rental</option>
                            <option value="gaji">Gaji</option>
                            <option value="operasional">Operasional</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label small fw-medium">Nominal (Rp)</label>
                            <input type="number" class="form-control" name="amount" id="financeAmount" min="1" placeholder="0" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-medium">Tanggal</label>
                            <input type="date" class="form-control" name="transaction_date" id="financeDate" required>
                        </div>
                    </div>
                    <div>
                        <label class="form-label small fw-medium">Keterangan</label>
                        <textarea class="form-control" name="description" id="financeDescription" rows="2" placeholder="Opsional"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top-0 px-4 pb-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold" id="financeSubmitBtn">Simpan Transaksi</button>
                </div>
            </form>
        </div>
    </div>
</div>
