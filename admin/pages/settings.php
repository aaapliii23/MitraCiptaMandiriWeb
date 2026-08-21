<!-- SETTINGS PAGE -->
        <div class="mb-4">
            <h2 class="fw-bold mb-1 text-dark">Konfigurasi Platform</h2>
            <p class="text-muted">Kelola identitas, kontak, dan informasi publik MCM.</p>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm overflow-hidden">
                    <div class="card-header bg-white py-4 px-4 border-bottom-0">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2 me-3">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <h5 class="fw-bold mb-0 text-dark">Informasi Publik & Kontak</h5>
                        </div>
                    </div>
                    <div class="card-body p-4 pt-0">
                        <form id="settingsForm" action="<?php echo $adminBase; ?>/actions/save_settings.php" method="POST" onsubmit="event.preventDefault(); submitAjaxForm('settingsForm');">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted text-uppercase">WhatsApp Bisnis</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0"><i class="fab fa-whatsapp text-success"></i></span>
                                        <input type="text" class="form-control bg-light border-0 py-2" name="admin_whatsapp" value="6285793935707">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Email Official</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0"><i class="fas fa-envelope text-primary"></i></span>
                                        <input type="email" class="form-control bg-light border-0 py-2" name="admin_email" value="info@mcm.com">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Alamat Operasional</label>
                                    <textarea class="form-control bg-light border-0 py-2" name="admin_address" rows="3">Kota Sukabumi, Jawa Barat</textarea>
                                </div>
                                <div class="col-12">
                                    <h6 class="fw-bold text-dark mt-3 mb-3">Tautan Media Sosial</h6>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label small fw-bold text-muted">Instagram</label>
                                            <input type="text" class="form-control bg-light border-0" name="social_ig" value="#">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small fw-bold text-muted">Facebook</label>
                                            <input type="text" class="form-control bg-light border-0" name="social_fb" value="#">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small fw-bold text-muted">TikTok</label>
                                            <input type="text" class="form-control bg-light border-0" name="social_tt" value="#">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-5 pt-4 border-top text-end">
                                <button type="submit" class="btn btn-primary px-5 rounded-pill shadow-sm">Simpan Konfigurasi</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4 overflow-hidden">
                    <div class="card-header bg-white py-4 px-4 border-bottom-0">
                        <h6 class="fw-bold mb-0 text-dark">Identitas Visual</h6>
                    </div>
                    <div class="card-body p-4 pt-0 text-center">
                        <div class="mb-4 bg-light p-4 rounded-4 border border-dashed">
                            <img src="../assets/img/logo.png" alt="MCM Logo" class="mb-3" style="max-height: 80px;">
                            <p class="small text-muted mb-0">Logo saat ini (.png)</p>
                        </div>
                        <form id="logoForm" action="<?php echo $adminBase; ?>/actions/save_settings.php" method="POST" enctype="multipart/form-data" onsubmit="event.preventDefault(); submitAjaxForm('logoForm');">
                            <div class="mb-3">
                                <input type="file" class="form-control" name="logo" accept="image/png">
                            </div>
                            <button type="submit" class="btn btn-light w-100 rounded-pill fw-bold">Update Logo</button>
                        </form>
                    </div>
                </div>
                <div class="card border-0 shadow-sm bg-dark text-white">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-shield-alt text-warning me-2"></i>
                            <h6 class="fw-bold mb-0">Keamanan</h6>
                        </div>
                        <p class="small text-white-50 mb-4">Ganti kata sandi admin secara berkala untuk menjaga keamanan data.</p>
                        <button class="btn btn-outline-light w-100 rounded-pill" onclick="showModal('changePasswordModal')">Ganti Password</button>
                    </div>
                </div>
            </div>
        </div>
