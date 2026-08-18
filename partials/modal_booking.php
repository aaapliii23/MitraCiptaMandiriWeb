    <!-- Booking Modal -->
    <div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); border-radius: 1.5rem; overflow: hidden;">
                <div class="row g-0">
                    <!-- Left side: Image/Branding (Hidden on mobile) -->
                    <div class="col-md-5 d-none d-md-flex flex-column justify-content-center align-items-center text-center p-4 p-lg-5" style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));">
                        
                        <!-- Logo | Text Layout -->
                        <div class="d-flex align-items-center justify-content-center mb-4 mt-3">
                            <img src="assets/img/logo.png" alt="MCM Logo" style="height: 90px; width: auto; filter: drop-shadow(0 0 10px rgba(255,255,255,0.3));">
                            <div class="ms-3 ps-3 border-start border-2 border-white border-opacity-50 text-start">
                                <span class="text-white fw-bold d-block" style="letter-spacing: 2px; font-size: 1.2rem; line-height: 1.2;">MITRA CIPTA</span>
                                <span class="text-white fw-bold d-block" style="letter-spacing: 2px; font-size: 1.2rem; line-height: 1.2;">MANDIRI</span>
                            </div>
                        </div>

                        <h3 class="text-white fw-bold mb-3 fs-4 mt-3">Mulai Karir Anda<br>Bersama Kami</h3>
                        <p class="text-white-50 px-3 small">Langkah pertama menuju kemandirian finansial dan profesionalisme tingkat tinggi.</p>
                    </div>
                    <!-- Right side: Form -->
                    <div class="col-md-7">
                        <div class="modal-header border-0 pb-0 pe-4 pt-4">
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4 p-md-5 pt-2">
                            <!-- Mobile Logo (Visible only on small screens) -->
                            <div class="d-flex d-md-none align-items-center justify-content-center mb-4 pb-3 border-bottom">
                                <img src="assets/img/logo.png" alt="MCM Logo" style="height: 65px; width: auto;">
                                <div class="ms-3 ps-3 border-start border-2 text-start" style="border-color: var(--primary-color) !important;">
                                    <span class="fw-bold d-block" style="color: var(--primary-color); letter-spacing: 1px; font-size: 1rem; line-height: 1.2;">MITRA CIPTA</span>
                                    <span class="fw-bold d-block" style="color: var(--primary-color); letter-spacing: 1px; font-size: 1rem; line-height: 1.2;">MANDIRI</span>
                                </div>
                            </div>
                            
                            <div class="mb-4 text-center text-md-start">
                                <h3 class="fw-bold" style="color: #0F172A;" id="bookingModalLabel">Form Pendaftaran</h3>
                                <p class="text-muted small">Lengkapi data diri Anda di bawah ini.</p>
                            </div>

                            <form id="bookingForm">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                                <input type="hidden" name="class_id" id="bookingClassId">
                                
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="name" class="form-label small fw-medium">Nama Lengkap</label>
                                        <input type="text" class="form-control" id="name" name="name" required placeholder="John Doe">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="whatsapp" class="form-label small fw-medium">No. WhatsApp</label>
                                        <input type="text" class="form-control" id="whatsapp" name="whatsapp" required placeholder="+6281234567890" value="+62">
                                    </div>
                                    <div class="col-12">
                                        <label for="email" class="form-label small fw-medium">Alamat Email</label>
                                        <input type="email" class="form-control" id="email" name="email" required placeholder="email@contoh.com">
                                    </div>
                                    <div class="col-12">
                                        <label for="kelas" class="form-label small fw-medium">Kelas Pelatihan</label>
                                        <select class="form-select" id="kelas" name="kelas" required>
                                            <option value="" disabled selected>Pilih kelas pelatihan...</option>
                                            <?php foreach ($classItems as $c): ?>
                                                <option value="<?php echo htmlspecialchars($c['name']); ?>"><?php echo htmlspecialchars($c['name']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label for="booking_date" class="form-label small fw-medium">Tanggal Kedatangan <span class="text-muted">(opsional)</span></label>
                                        <input type="date" class="form-control" id="booking_date" name="booking_date" min="<?php echo date('Y-m-d'); ?>">
                                    </div>
                                    <div class="col-12 text-center mt-4">
                                        <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 fw-bold w-100" id="btnCheckoutLanjut">Lanjutkan ke Pembayaran</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
