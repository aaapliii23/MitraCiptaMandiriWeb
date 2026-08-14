<?php
require_once 'includes/db_config.php';
$hide_nav_items = true;
include 'includes/header.php';
?>

<!-- Certification Page -->
<section class="section-padding bg-white animate__animated animate__fadeIn" style="padding-top: 50px;">
    <div class="container">
        <!-- Circular Back Button -->
        <div class="mb-4 text-start" style="margin-left: -5px;">
            <a href="index.php" class="btn rounded-circle d-inline-flex align-items-center justify-content-center shadow-premium btn-premium" style="width: 50px; height: 50px; background: linear-gradient(135deg, #0c4a6e, #0ea5e9); color: white; border: none; transition: all 0.3s ease;">
                <i class="fas fa-arrow-left fs-5"></i>
            </a>
        </div>

        <div class="text-start mb-5">
            <!-- Logo -->
            <div class="mb-4">
                <a class="d-flex align-items-center text-decoration-none" href="index.php">
                    <img src="assets/img/logo.png" alt="MCM Logo" style="height: 45px; width: auto;">
                    <div class="ms-2 ps-2 border-start border-2 border-dark d-flex flex-column justify-content-center" style="height: 35px;">
                        <span class="fw-bold text-dark" style="font-size: 0.8rem; letter-spacing: 1px; line-height: 1.1;">MITRA CIPTA</span>
                        <span class="fw-bold text-dark" style="font-size: 0.8rem; letter-spacing: 1px; line-height: 1.1;">MANDIRI</span>
                    </div>
                </a>
            </div>
            <div class="badge bg-primary bg-opacity-10 text-primary mb-3 p-2 px-3 rounded-pill fw-bold" style="background-color: rgba(12, 74, 110, 0.1) !important; color: #0c4a6e !important;">CERTIFICATIONS</div>
            <h1 class="display-5 fw-bold mb-3" style="color: #0c4a6e;">Sertifikasi <span style="color: #0ea5e9;">& Legalitas</span></h1>
            <p class="text-secondary fs-5" style="max-width: 700px;">Bukti nyata kualitas dan komitmen MCM dalam menyelenggarakan pelatihan vokasi standar nasional.</p>
        </div>

        <!-- Certification Content -->
        <div class="row g-4">
            <!-- Main MCM Certificate -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                    <div class="p-4 bg-light text-center border-bottom">
                        <img src="assets/img/logo.png" alt="MCM Logo" style="height: 120px; opacity: 0.2; position: absolute; top: 20px; right: 20px;">
                        <i class="fas fa-certificate fa-4x text-primary mb-3" style="color: #0ea5e9 !important;"></i>
                        <h4 class="fw-bold text-dark">Sertifikasi Internal MCM</h4>
                        <p class="text-muted small">Diberikan kepada lulusan yang telah menyelesaikan seluruh kurikulum dan uji kompetensi internal.</p>
                    </div>
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3"><i class="fas fa-check-circle text-success me-2"></i>Keunggulan Sertifikat:</h6>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2 d-flex align-items-start"><i class="fas fa-star text-warning me-2 mt-1 small"></i> <span>Diakui oleh seluruh mitra industri MCM.</span></li>
                            <li class="mb-2 d-flex align-items-start"><i class="fas fa-star text-warning me-2 mt-1 small"></i> <span>Memiliki nomor registrasi unik (QR Code Verified).</span></li>
                            <li class="mb-2 d-flex align-items-start"><i class="fas fa-star text-warning me-2 mt-1 small"></i> <span>Mencakup detail nilai dan kompetensi yang dikuasai.</span></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Industry Partners Certificate -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                    <div class="p-4 bg-light text-center border-bottom">
                        <i class="fas fa-award fa-4x text-warning mb-3"></i>
                        <h4 class="fw-bold text-dark">Sertifikasi BNSP / Industri</h4>
                        <p class="text-muted small">Kerjasama strategis dengan lembaga sertifikasi profesi nasional dan mitra perusahaan.</p>
                    </div>
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3"><i class="fas fa-check-circle text-success me-2"></i>Legalitas & Lisensi:</h6>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2 d-flex align-items-start"><i class="fas fa-shield-alt text-primary me-2 mt-1 small"></i> <span>Izin Operasional LPK (Lembaga Pelatihan Kerja).</span></li>
                            <li class="mb-2 d-flex align-items-start"><i class="fas fa-shield-alt text-primary me-2 mt-1 small"></i> <span>Terakreditasi oleh lembaga terkait.</span></li>
                            <li class="mb-2 d-flex align-items-start"><i class="fas fa-shield-alt text-primary me-2 mt-1 small"></i> <span>Instruktur bersertifikasi asesor kompetensi.</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Visual Placeholder for Actual Certificate Images -->
        <div class="mt-5 pt-4">
            <h5 class="fw-bold mb-4" style="color: #0c4a6e;">Pratinjau Sertifikat</h5>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="bg-light rounded-4 d-flex align-items-center justify-content-center p-5 border border-dashed" style="height: 250px;">
                        <div class="text-center opacity-50">
                            <i class="fas fa-file-contract fa-3x mb-2"></i>
                            <p class="small mb-0">Contoh Sertifikat A</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="bg-light rounded-4 d-flex align-items-center justify-content-center p-5 border border-dashed" style="height: 250px;">
                        <div class="text-center opacity-50">
                            <i class="fas fa-file-contract fa-3x mb-2"></i>
                            <p class="small mb-0">Contoh Sertifikat B</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="bg-light rounded-4 d-flex align-items-center justify-content-center p-5 border border-dashed" style="height: 250px;">
                        <div class="text-center opacity-50">
                            <i class="fas fa-file-contract fa-3x mb-2"></i>
                            <p class="small mb-0">Contoh Sertifikat C</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
