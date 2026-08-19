    <!-- Checkout Modal -->
    <div class="modal fade" id="checkoutModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); border-radius: 1.25rem; overflow: hidden;">
                <div class="row g-0">
                    <!-- Left side: Order Summary & Logo -->
                    <div class="col-md-5 d-flex flex-column p-4 text-white position-relative" style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); overflow: hidden;">
                        
                        <!-- Background Pattern -->
                        <div style="position: absolute; top: -50px; right: -50px; width: 150px; height: 150px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>

                        <!-- Brand Logo -->
                        <div class="d-flex align-items-center mb-4 position-relative z-index-1">
                            <img src="assets/img/logo.png" alt="MCM Logo" style="height: 40px; width: auto; filter: drop-shadow(0 0 10px rgba(255,255,255,0.3));">
                            <div class="ms-2 ps-2 border-start border-2 border-white border-opacity-50 text-start">
                                <span class="text-white fw-bold d-block" style="letter-spacing: 1px; font-size: 0.8rem; line-height: 1.1;">MITRA CIPTA</span>
                                <span class="text-white fw-bold d-block" style="letter-spacing: 1px; font-size: 0.8rem; line-height: 1.1;">MANDIRI</span>
                            </div>
                        </div>

                        <div class="mt-auto position-relative z-index-1">
                            <h5 class="fw-bold mb-3">Ringkasan</h5>
                            
                            <div class="d-flex align-items-center mb-3 bg-white bg-opacity-10 p-2 rounded-3">
                                <div class="bg-white bg-opacity-25 p-2 rounded-circle me-3">
                                    <i class="fas fa-book-open text-white fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0 text-white" id="checkoutClassName" style="font-size: 0.9rem;">Nama Kelas</h6>
                                    <div class="text-white-50 small" style="font-size: 0.75rem;">Pelatihan Vokasi</div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between mb-1 small">
                                <span class="text-white-50">Harga Kelas</span>
                                <span class="fw-medium text-white" id="checkoutClassPrice">Rp 0</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3 small">
                                <span class="text-white-50">Biaya Admin</span>
                                <span class="fw-bold text-warning">Gratis</span>
                            </div>
                            
                            <hr class="border-white border-opacity-25 mb-3">
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-white">TOTAL</span>
                                <span class="fw-bold text-white fs-5" id="checkoutTotalPrice">Rp 0</span>
                            </div>
                        </div>
                    </div>

                    <!-- Right side: Form -->
                    <div class="col-md-7">
                        <div class="modal-header border-0 pb-0 pe-4 pt-3">
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4 pt-2">
                            
                            <div class="mb-3 text-center text-md-start">
                                <h5 class="fw-bold" style="color: #0F172A; margin-bottom: 2px;">Data Diri Peserta</h5>
                                <p class="text-muted small mb-0" style="font-size: 0.75rem;">Lengkapi data Anda untuk pendaftaran.</p>
                            </div>

                            <form id="checkoutForm" action="payment/create_transaction.php" method="POST" onsubmit="return submitPayment(this);">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-secondary mb-1" style="font-size: 0.7rem;">Nama Lengkap *</label>
                                        <input type="text" class="form-control form-control-sm bg-light" name="customer_name" required placeholder="Budi Santoso">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-secondary mb-1" style="font-size: 0.7rem;">No. WhatsApp *</label>
                                        <input type="text" class="form-control form-control-sm bg-light" name="customer_phone" required placeholder="0812...">
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label small fw-bold text-secondary mb-1" style="font-size: 0.7rem;">Alamat Email *</label>
                                        <input type="email" class="form-control form-control-sm bg-light" name="customer_email" required placeholder="email@contoh.com">
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label small fw-bold text-secondary mb-1" style="font-size: 0.7rem;">Asal Instansi/Sekolah</label>
                                        <input type="text" class="form-control form-control-sm bg-light" name="customer_institution" placeholder="SMA 1 Jakarta / PT Maju Jaya">
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label small fw-bold text-secondary mb-1" style="font-size: 0.7rem;">Alamat Lengkap *</label>
                                        <textarea class="form-control form-control-sm bg-light" name="customer_address" rows="2" required placeholder="Jl. Sudirman No. 123..."></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-secondary mb-1" style="font-size: 0.7rem;">Password Akun LMS *</label>
                                        <input type="password" class="form-control form-control-sm bg-light" name="customer_password" placeholder="Minimal 6 karakter" minlength="6" autocomplete="new-password">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-secondary mb-1" style="font-size: 0.7rem;">Konfirmasi Password *</label>
                                        <input type="password" class="form-control form-control-sm bg-light" name="customer_password2" placeholder="Ulangi password" minlength="6" autocomplete="new-password">
                                    </div>
                                </div>
                                
                                <div class="alert alert-info border-0 d-flex align-items-center mt-3 mb-3 p-2" style="border-radius: 0.75rem;">
                                    <i class="fas fa-info-circle fs-5 me-2"></i>
                                    <small style="font-size: 0.7rem; line-height: 1.2;">Akun LMS dibuat otomatis dari data ini (password di atas). Kosongkan password bila email Anda sudah terdaftar.</small>
                                </div>
                            
                                <input type="hidden" name="class_id" id="checkoutClassId">
                                <button type="submit" class="btn btn-primary w-100 py-2 rounded-pill fw-bold shadow-sm" style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); border: none; font-size: 0.9rem;">
                                    <i class="fas fa-credit-card me-2"></i>Bayar Sekarang
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
