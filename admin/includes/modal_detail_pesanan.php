<!-- Detail Pesanan Modal -->
<div class="modal fade" id="detailPesananModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 1.5rem;">
            <div class="modal-header bg-light border-0 py-3 px-4">
                <h5 class="modal-title fw-bold text-dark"><i class="fas fa-info-circle me-2 text-info"></i>Detail Data Peserta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <img src="../assets/img/logo.png" alt="MCM Logo" style="height: 50px; width: auto;" class="mb-2">
                    <h6 class="fw-bold text-primary mb-0">Mitra Cipta Mandiri</h6>
                    <small class="text-muted" id="detailOrderNum"></small>
                </div>
                
                <div class="row g-3">
                    <div class="col-6">
                        <label class="small text-muted d-block">Nama Lengkap</label>
                        <span class="fw-bold text-dark" id="detailName"></span>
                    </div>
                    <div class="col-6">
                        <label class="small text-muted d-block">No. WhatsApp</label>
                        <span class="fw-bold text-dark" id="detailPhone"></span>
                    </div>
                    <div class="col-12">
                        <label class="small text-muted d-block">Alamat Email</label>
                        <span class="fw-bold text-dark" id="detailEmail"></span>
                    </div>
                    <div class="col-12">
                        <label class="small text-muted d-block">Asal Instansi/Sekolah</label>
                        <span class="fw-bold text-dark" id="detailInstansi"></span>
                    </div>
                    <div class="col-12">
                        <label class="small text-muted d-block">Alamat Lengkap</label>
                        <p class="fw-medium text-dark mb-0" id="detailAlamat"></p>
                    </div>
                    <div class="col-12 border-top pt-3">
                        <div class="bg-light p-3 rounded-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <label class="small text-muted d-block">Kelas Pilihan</label>
                                    <span class="fw-bold text-primary" id="detailKelas"></span>
                                </div>
                                <div class="text-end">
                                    <label class="small text-muted d-block">Total Bayar</label>
                                    <span class="fw-bold text-dark">Rp <span id="detailHarga"></span></span>
                                </div>
                            </div>
                            <div class="mt-2 text-center text-sm-start">
                                <span class="badge bg-light border" id="detailMode" style="font-size: 0.72rem;"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-secondary w-100 py-2 rounded-pill fw-bold" data-bs-dismiss="modal">Tutup Detail</button>
            </div>
        </div>
    </div>
</div>
