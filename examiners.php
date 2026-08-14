<?php
require_once 'includes/db_config.php';
$hide_nav_items = true;
include 'includes/header.php';
?>

<!-- Examiners Profile Page -->
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
            <div class="badge bg-primary bg-opacity-10 text-primary mb-3 p-2 px-3 rounded-pill fw-bold" style="background-color: rgba(12, 74, 110, 0.1) !important; color: #0c4a6e !important;">OUR TEAM</div>
            <h1 class="display-5 fw-bold mb-3" style="color: #0c4a6e;">Profil <span style="color: #0ea5e9;">Penguji Ahli</span></h1>
            <p class="text-secondary fs-5" style="max-width: 700px;">Mengenal para profesional dan asesor kompetensi yang menjamin standar kelulusan terbaik di MCM.</p>
        </div>

        <!-- Examiners Grid -->
        <div class="row g-4 mt-2">
            <!-- Examiner 1 -->
            <div class="col-lg-4 col-md-6">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 text-center p-4">
                    <div class="mx-auto mb-4 bg-light rounded-circle p-2" style="width: 150px; height: 150px; border: 3px solid #0ea5e9;">
                        <img src="assets/img/logo.png" alt="Examiner" class="w-100 h-100 rounded-circle opacity-25" style="object-fit: cover;">
                    </div>
                    <h5 class="fw-bold text-dark mb-1">Drs. Ahmad Jaelani</h5>
                    <p class="text-primary small fw-bold mb-3">Asesor Public Speaking</p>
                    <div class="bg-light p-3 rounded-3 mb-3">
                        <p class="small text-muted mb-0">Berpengalaman lebih dari 15 tahun di industri komunikasi dan sertifikasi BNSP.</p>
                    </div>
                    <div class="d-flex justify-content-center gap-2">
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3">Komunikasi</span>
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3">BNSP</span>
                    </div>
                </div>
            </div>

            <!-- Examiner 2 -->
            <div class="col-lg-4 col-md-6">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 text-center p-4">
                    <div class="mx-auto mb-4 bg-light rounded-circle p-2" style="width: 150px; height: 150px; border: 3px solid #0ea5e9;">
                        <img src="assets/img/logo.png" alt="Examiner" class="w-100 h-100 rounded-circle opacity-25" style="object-fit: cover;">
                    </div>
                    <h5 class="fw-bold text-dark mb-1">Rina Wijaya, S.Pd</h5>
                    <p class="text-primary small fw-bold mb-3">Ahli Tata Rias & Estetika</p>
                    <div class="bg-light p-3 rounded-3 mb-3">
                        <p class="small text-muted mb-0">Praktisi MUA Profesional dengan spesialisasi tata rias pengantin dan seni estetika.</p>
                    </div>
                    <div class="d-flex justify-content-center gap-2">
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3">Beauty</span>
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3">Certified</span>
                    </div>
                </div>
            </div>

            <!-- Examiner 3 -->
            <div class="col-lg-4 col-md-6">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 text-center p-4">
                    <div class="mx-auto mb-4 bg-light rounded-circle p-2" style="width: 150px; height: 150px; border: 3px solid #0ea5e9;">
                        <img src="assets/img/logo.png" alt="Examiner" class="w-100 h-100 rounded-circle opacity-25" style="object-fit: cover;">
                    </div>
                    <h5 class="fw-bold text-dark mb-1">H. Supardi</h5>
                    <p class="text-primary small fw-bold mb-3">Pakar Pijat Kesehatan</p>
                    <div class="bg-light p-3 rounded-3 mb-3">
                        <p class="small text-muted mb-0">Ahli terapi pijat tradisional dan modern dengan lisensi kesehatan resmi.</p>
                    </div>
                    <div class="d-flex justify-content-center gap-2">
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3">Therapy</span>
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3">Kesehatan</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quality Commitment -->
        <div class="mt-5 p-5 rounded-5 bg-light text-center border">
            <h4 class="fw-bold text-dark mb-3">Komitmen Penguji Kami</h4>
            <p class="text-muted mx-auto" style="max-width: 800px;">Setiap penguji di MCM adalah praktisi yang tidak hanya menguasai teori, tetapi aktif di bidang industrinya masing-masing. Hal ini memastikan lulusan kami memiliki standar yang relevan dengan kebutuhan pasar kerja saat ini.</p>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
