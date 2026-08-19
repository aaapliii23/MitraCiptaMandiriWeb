<?php
require_once '../config/database.php';
$hide_nav_items = true; // Hide navbar links, keep only logo
include '../includes/header.php';
?>

<!-- About MCM Full Page -->
<section class="section-padding bg-white animate__animated animate__fadeIn" style="padding-top: 50px;">
    <div class="container">
        <!-- Circular Back Button above Logo -->
        <div class="mb-4 text-start" style="margin-left: -5px;">
            <a href="../index.php" class="btn rounded-circle d-inline-flex align-items-center justify-content-center shadow-premium btn-premium" style="width: 50px; height: 50px; background: linear-gradient(135deg, #0c4a6e, #0ea5e9); color: white; border: none; transition: all 0.3s ease;">
                <i class="fas fa-arrow-left fs-5"></i>
            </a>
        </div>

        <!-- Hero Section Inside Page -->
        <div class="row align-items-center mb-5">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <!-- Logo directly above badge -->
                <div class="mb-4">
                    <a class="d-flex align-items-center text-decoration-none" href="../index.php">
                        <img src="../assets/img/logo.png" alt="MCM Logo" style="height: 45px; width: auto;">
                        <div class="ms-2 ps-2 border-start border-2 border-dark d-flex flex-column justify-content-center" style="height: 35px;">
                            <span class="fw-bold text-dark" style="font-size: 0.8rem; letter-spacing: 1px; line-height: 1.1;">MITRA CIPTA</span>
                            <span class="fw-bold text-dark" style="font-size: 0.8rem; letter-spacing: 1px; line-height: 1.1;">MANDIRI</span>
                        </div>
                    </a>
                </div>
                <div class="badge bg-primary bg-opacity-10 text-primary mb-3 p-2 px-3 rounded-pill fw-bold" style="background-color: rgba(12, 74, 110, 0.1) !important; color: #0c4a6e !important;">PROFIL LEMBAGA</div>
                <h1 class="display-4 fw-bold mb-4" style="color: #0c4a6e;">Tentang <br><span style="color: #0ea5e9;">Mitra Cipta Mandiri</span></h1>
                <p class="lead text-secondary mb-4">LPK Mitra Cipta Mandiri adalah lembaga pelatihan di bawahYayasanMITRA CIPTA MANDIRI, berlokasi di Jl. Terusan CiliwungNo. 30KotaBandung, Jawa Barat ,dengan ijin Kemenkumhamdansudahmemiliki legalitas sebagai LPK dari Disnaker Kota Bandung, DisnakerPropinsi Jawa Barat dan Kementrian KetenagakerjaanRI dansudahterakreditasi tahun 2023</p>
                <div class="row g-4 mb-4">
                    <div class="col-6">
                        <div class="d-flex align-items-center">
                            <div class="p-3 rounded-4 shadow-sm me-3" style="background-color: rgba(14, 165, 233, 0.1); color: #0ea5e9;"><i class="fas fa-certificate fs-4"></i></div>
                            <div>
                                <h6 class="fw-bold mb-0" style="color: #0c4a6e;">Legalitas</h6>
                                <p class="small text-muted mb-0">Terakreditasi</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex align-items-center">
                            <div class="p-3 rounded-4 shadow-sm me-3" style="background-color: rgba(14, 165, 233, 0.1); color: #0ea5e9;"><i class="fas fa-users fs-4"></i></div>
                            <div>
                                <h6 class="fw-bold mb-0" style="color: #0c4a6e;">Instruktur</h6>
                                <p class="small text-muted mb-0">Praktisi Ahli</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="position-relative">
                    <img src="../assets/img/hero-bg.jpg" alt="MCM Office" class="img-fluid rounded-5 shadow-lg" onerror="this.src='https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=800&q=80'">
                    <div class="position-absolute bottom-0 end-0 bg-white p-4 m-4 rounded-4 shadow-lg d-none d-md-block">
                        <h4 class="fw-bold mb-1" style="color: #0c4a6e;">10+ Tahun</h4>
                        <p class="small text-muted mb-0">Mencetak Alumni Sukses</p>
                    </div>
                </div>
            </div>
        </div>

        <hr class="my-5 opacity-10">

        <!-- Visi & Misi -->
        <div class="row g-5">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-5 p-4 p-lg-5 h-100 bg-light" style="border: 1px solid rgba(12, 74, 110, 0.05) !important;">
                    <div class="p-3 rounded-circle d-inline-block mb-4" style="width: 70px; height: 70px; line-height: 40px; text-align: center; background-color: rgba(12, 74, 110, 0.1); color: #0c4a6e;">
                        <i class="fas fa-eye fs-3"></i>
                    </div>
                    <h3 class="fw-bold mb-4" style="color: #0c4a6e;">Visi Kami</h3>
                    <p class="text-secondary fs-5 lh-lg">Menjadi lembaga pelatihan kerja terdepan yang menciptakan individu kompeten, inovatif, dan berdaya saing tinggi dengan semangat kebahagiaan dan kreativitas dalam setiap proses pembelajaran.</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-5 p-4 p-lg-5 h-100 bg-light" style="border: 1px solid rgba(14, 165, 233, 0.05) !important;">
                    <div class="p-3 rounded-circle d-inline-block mb-4" style="width: 70px; height: 70px; line-height: 40px; text-align: center; background-color: rgba(14, 165, 233, 0.1); color: #0ea5e9;">
                        <i class="fas fa-bullseye fs-3"></i>
                    </div>
                    <h3 class="fw-bold mb-4" style="color: #0c4a6e;">Misi Kami</h3>
                    <ul class="list-unstyled">
                        <li class="d-flex align-items-start mb-3">
                            <i class="fas fa-check-circle text-success mt-1 me-3"></i>
                            <span class="text-secondary">Menyelenggarakan pelatihan kerja berkualitas yang berorientasi pada kebutuhan pasar kerja dan perkembangan teknologi terkini.</span>
                        </li>
                        <li class="d-flex align-items-start mb-3">
                            <i class="fas fa-check-circle text-success mt-1 me-3"></i>
                            <span class="text-secondary">Mengembangkan kurikulum dan metode pembelajaran yang interaktif, praktis, dan menyenangkan untuk menumbuhkan minat serta potensi peserta.</span>
                        </li>
                        <li class="d-flex align-items-start mb-3">
                            <i class="fas fa-check-circle text-success mt-1 me-3"></i>
                            <span class="text-secondary">Membekali peserta dengan keterampilan teknis dan non-teknis (soft skills) yang relevan, seperti kemampuan berpikir kritis, kolaborasi, dan adaptasi.</span>
                        </li>
                        <li class="d-flex align-items-start mb-3">
                            <i class="fas fa-check-circle text-success mt-1 me-3"></i>
                            <span class="text-secondary">Menciptakan lingkungan belajar yang mendukung, inklusif, dan inspiratif agar setiap peserta dapat berkreasi dan berkembang dengan optimal.</span>
                        </li>
                        <li class="d-flex align-items-start mb-3">
                            <i class="fas fa-check-circle text-success mt-1 me-3"></i>
                            <span class="text-secondary">Membangun kemitraan strategis dengan industri dan pemangku kepentingan untuk memperluas peluang kerja dan pengembangan karir bagi lulusan.</span>
                        </li>
                        <li class="d-flex align-items-start">
                            <i class="fas fa-check-circle text-success mt-1 me-3"></i>
                            <span class="text-secondary">Mendorong setiap individu untuk menemukan passion dan kebahagiaan dalam proses belajar dan berkarya, sehingga dapat memberikan kontribusi positif bagi diri sendiri, masyarakat, dan bangsa.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <hr class="my-5 opacity-10">

        <!-- Training Flow (Visual Grafik/Flow) Section -->
        <div class="text-center mb-5">
            <h2 class="fw-bold" style="color: #0c4a6e;">Alur Pengembangan Keahlian</h2>
            <p class="text-muted">Metodologi kami dalam mentransformasi potensi menjadi kompetensi nyata</p>
        </div>

        <div class="row g-4 position-relative mb-5">
            <!-- Connection Line (Desktop) -->
            <div class="position-absolute top-50 start-0 end-0 d-none d-lg-block" style="height: 2px; background: linear-gradient(90deg, #0c4a6e 0%, #0ea5e9 100%); transform: translateY(-50%); z-index: 0; opacity: 0.2;"></div>
            
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm rounded-5 p-4 text-center h-100 bg-white position-relative" style="z-index: 1;">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle shadow-premium" style="width: 70px; height: 70px; background: linear-gradient(135deg, #0c4a6e, #0ea5e9); color: white;">
                        <i class="fas fa-search fs-4"></i>
                    </div>
                    <h6 class="fw-bold" style="color: #0c4a6e;">1. Konsultasi</h6>
                    <p class="small text-muted mb-0">Identifikasi minat dan pemilihan program pelatihan yang tepat.</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm rounded-5 p-4 text-center h-100 bg-white position-relative" style="z-index: 1;">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle shadow-premium" style="width: 70px; height: 70px; background: linear-gradient(135deg, #0c4a6e, #0ea5e9); color: white;">
                        <i class="fas fa-tools fs-4"></i>
                    </div>
                    <h6 class="fw-bold" style="color: #0c4a6e;">2. Praktik Intensif</h6>
                    <p class="small text-muted mb-0">Penguasaan keahlian melalui praktik langsung dengan alat standar industri.</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm rounded-5 p-4 text-center h-100 bg-white position-relative" style="z-index: 1;">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle shadow-premium" style="width: 70px; height: 70px; background: linear-gradient(135deg, #0c4a6e, #0ea5e9); color: white;">
                        <i class="fas fa-check-double fs-4"></i>
                    </div>
                    <h6 class="fw-bold" style="color: #0c4a6e;">3. Evaluasi Akhir</h6>
                    <p class="small text-muted mb-0">Uji kompetensi menyeluruh untuk memastikan standar penguasaan materi.</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm rounded-5 p-4 text-center h-100 bg-white position-relative" style="z-index: 1;">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle shadow-premium" style="width: 70px; height: 70px; background: linear-gradient(135deg, #0c4a6e, #0ea5e9); color: white;">
                        <i class="fas fa-user-shield fs-4"></i>
                    </div>
                    <h6 class="fw-bold" style="color: #0c4a6e;">4. Siap Mandiri</h6>
                    <p class="small text-muted mb-0">Lulusan memiliki portofolio dan sertifikat untuk memulai karir atau usaha sendiri.</p>
                </div>
            </div>
        </div>

        <hr class="my-5 opacity-10">

        <!-- FAQ Section (To fill content) -->
        <div class="row g-5">
            <div class="col-lg-4">
                <h2 class="fw-bold mb-4" style="color: #0c4a6e;">Pertanyaan Umum</h2>
                <p class="text-muted">Hal-hal yang sering ditanyakan mengenai program pelatihan kami.</p>
                <div class="bg-light p-4 rounded-5 border-start border-4" style="border-color: #0ea5e9 !important;">
                    <p class="mb-0 small italic text-secondary">"Investasi terbaik adalah investasi pada diri sendiri melalui keahlian nyata."</p>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="accordion accordion-flush" id="faqAccordion">
                    <div class="accordion-item bg-transparent border-bottom py-2">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed bg-transparent fw-bold text-dark mcm-faq-btn" type="button">
                                Apakah pelatihan ini bersertifikat?
                            </button>
                        </h2>
                        <div class="accordion-collapse collapse mcm-faq-content">
                            <div class="accordion-body text-secondary">
                                Ya, setiap peserta yang lulus evaluasi akan menerima sertifikat resmi dari LPK Mitra Cipta Mandiri sebagai bukti penguasaan kompetensi di bidang yang dipilih.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item bg-transparent border-bottom py-2">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed bg-transparent fw-bold text-dark mcm-faq-btn" type="button">
                                Siapa saja yang bisa mendaftar?
                            </button>
                        </h2>
                        <div class="accordion-collapse collapse mcm-faq-content">
                            <div class="accordion-body text-secondary">
                                Kami membuka pintu bagi siapa saja (umum, lulusan sekolah, atau pekerja) yang memiliki semangat untuk belajar keahlian baru secara praktis dan mandiri.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item bg-transparent border-bottom py-2">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed bg-transparent fw-bold text-dark mcm-faq-btn" type="button">
                                Bagaimana metode pelatihannya?
                            </button>
                        </h2>
                        <div class="accordion-collapse collapse mcm-faq-content">
                            <div class="accordion-body text-secondary">
                                Metode kami menitikberatkan pada 80% praktik dan 20% teori, menggunakan fasilitas workshop yang lengkap untuk memastikan peserta benar-benar mahir menggunakan peralatan standar industri.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                document.querySelectorAll('.mcm-faq-btn').forEach((btn, index) => {
                    btn.addEventListener('click', function() {
                        const content = document.querySelectorAll('.mcm-faq-content')[index];
                        const isExpanded = content.classList.contains('show');
                        
                        // Close all others
                        document.querySelectorAll('.mcm-faq-content').forEach(c => c.classList.remove('show'));
                        document.querySelectorAll('.mcm-faq-btn').forEach(b => b.classList.add('collapsed'));
                        
                        if (!isExpanded) {
                            content.classList.add('show');
                            this.classList.remove('collapsed');
                        }
                    });
                });
            </script>
        </div>

        <hr class="my-5 opacity-10">

        <div class="text-center mb-5 mt-5 pt-5">
            <h2 class="fw-bold" style="color: #0c4a6e;">Nilai-Nilai Utama Kami</h2>
            <p class="text-muted">Prinsip yang mendasari setiap langkah pelayanan kami</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-5 p-4 text-center h-100 bg-white" style="border: 1px solid rgba(12, 74, 110, 0.05) !important;">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle" style="width: 60px; height: 60px; background-color: rgba(12, 74, 110, 0.1); color: #0c4a6e;">
                        <i class="fas fa-award fs-4"></i>
                    </div>
                    <h5 class="fw-bold" style="color: #0c4a6e;">Profesionalisme</h5>
                    <p class="small text-muted mb-0">Memberikan standar pelatihan tertinggi dengan instruktur yang berpengalaman di bidangnya.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-5 p-4 text-center h-100 bg-white" style="border: 1px solid rgba(14, 165, 233, 0.05) !important;">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle" style="width: 60px; height: 60px; background-color: rgba(14, 165, 233, 0.1); color: #0ea5e9;">
                        <i class="fas fa-lightbulb fs-4"></i>
                    </div>
                    <h5 class="fw-bold" style="color: #0c4a6e;">Inovasi</h5>
                    <p class="small text-muted mb-0">Mengadaptasi kurikulum dan metode belajar dengan perkembangan teknologi industri terbaru.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-5 p-4 text-center h-100 bg-white" style="border: 1px solid rgba(12, 74, 110, 0.05) !important;">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle" style="width: 60px; height: 60px; background-color: rgba(12, 74, 110, 0.1); color: #0c4a6e;">
                        <i class="fas fa-handshake fs-4"></i>
                    </div>
                    <h5 class="fw-bold" style="color: #0c4a6e;">Integritas</h5>
                    <p class="small text-muted mb-0">Membangun kepercayaan melalui kejujuran, transparansi, dan tanggung jawab sosial.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
