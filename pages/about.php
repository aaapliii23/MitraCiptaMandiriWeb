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
                <p class="lead text-secondary mb-4">LPK Mitra Cipta Mandiri adalah lembaga pelatihan di bawah Yayasan Mitra Cipta Mandiri yang berlokasi di <?php echo htmlspecialchars(mcm_setting('admin_address', 'Jl. Khp Hasan Mustopa No.57, Neglasari, Kec. Cibeunying Kaler, Kota Bandung, Jawa Barat 40124')); ?>. Lembaga ini telah memiliki izin dari Kemenkumham serta legalitas resmi sebagai LPK dari Disnaker Kota Bandung, Disnaker Provinsi Jawa Barat, dan Kementerian Ketenagakerjaan RI, serta telah terakreditasi pada tahun 2023.</p>
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
                    <img src="<?php echo htmlspecialchars(asset_src('assets/img/gallery/slide_landingpages/WhatsApp Image 2026-08-14 at 14.44.00.jpeg')); ?>" alt="MCM Office" class="img-fluid rounded-5 shadow-lg w-100" style="object-fit: cover; max-height: 450px;" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='../assets/img/hero-bg.jpg';">
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

        <div class="row g-4 position-relative mb-5 alur-row">
            <!-- Arrow connectors desktop (→) — between 1-2, 2-3, 3-4 -->
            <div class="alur-arrow d-none d-lg-flex" style="left: 25%;"><i class="fas fa-arrow-right"></i></div>
            <div class="alur-arrow d-none d-lg-flex" style="left: 50%;"><i class="fas fa-arrow-right"></i></div>
            <div class="alur-arrow d-none d-lg-flex" style="left: 75%;"><i class="fas fa-arrow-right"></i></div>
            
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm rounded-5 p-4 text-center h-100 bg-white position-relative" style="z-index: 1;">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle shadow-premium" style="width: 70px; height: 70px; background: linear-gradient(135deg, #0c4a6e, #0ea5e9); color: white;">
                        <i class="fas fa-search fs-4"></i>
                    </div>
                    <h6 class="fw-bold" style="color: #0c4a6e;">1. Konsultasi</h6>
                    <p class="small text-muted mb-0">Identifikasi minat dan pemilihan program pelatihan yang tepat.</p>
                </div>
            </div>
            <div class="col-12 d-lg-none d-flex justify-content-center my-1"><div class="alur-arrow-mobile"><i class="fas fa-arrow-down"></i></div></div>
            
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm rounded-5 p-4 text-center h-100 bg-white position-relative" style="z-index: 1;">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle shadow-premium" style="width: 70px; height: 70px; background: linear-gradient(135deg, #0c4a6e, #0ea5e9); color: white;">
                        <i class="fas fa-tools fs-4"></i>
                    </div>
                    <h6 class="fw-bold" style="color: #0c4a6e;">2. Praktik Intensif</h6>
                    <p class="small text-muted mb-0">Penguasaan keahlian melalui praktik langsung dengan alat standar industri.</p>
                </div>
            </div>
            <div class="col-12 d-lg-none d-flex justify-content-center my-1"><div class="alur-arrow-mobile"><i class="fas fa-arrow-down"></i></div></div>
            
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm rounded-5 p-4 text-center h-100 bg-white position-relative" style="z-index: 1;">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle shadow-premium" style="width: 70px; height: 70px; background: linear-gradient(135deg, #0c4a6e, #0ea5e9); color: white;">
                        <i class="fas fa-check-double fs-4"></i>
                    </div>
                    <h6 class="fw-bold" style="color: #0c4a6e;">3. Evaluasi Akhir</h6>
                    <p class="small text-muted mb-0">Uji kompetensi menyeluruh untuk memastikan standar penguasaan materi.</p>
                </div>
            </div>
            <div class="col-12 d-lg-none d-flex justify-content-center my-1"><div class="alur-arrow-mobile"><i class="fas fa-arrow-down"></i></div></div>
            
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
        <style>
            .alur-row { overflow: visible; }
            .alur-arrow {
                position: absolute;
                top: 52px;
                width: 36px; height: 36px;
                background: #fff;
                border: 1px solid #e2e8f0;
                border-radius: 50%;
                display: flex; align-items: center; justify-content: center;
                color: #0c4a6e;
                box-shadow: 0 4px 12px rgba(12,74,110,0.10);
                z-index: 2;
                transform: translateX(-50%);
            }
            .alur-arrow i { font-size: 0.85rem; color: #0ea5e9; }
            .alur-arrow-mobile {
                width: 32px; height: 32px;
                background: #fff;
                border: 1px solid #e2e8f0;
                border-radius: 50%;
                display: flex; align-items: center; justify-content: center;
                color: #0ea5e9;
                box-shadow: 0 2px 8px rgba(12,74,110,0.08);
            }
            .alur-arrow-mobile i { font-size: 0.8rem; }
        </style>

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

        <hr class="my-5 opacity-10">

        <!-- Dokumentasi Tempat Pelatihan MCM Section (Per Kategori) -->
        <?php
        $facilityCategories = [];
        try {
            $catStmt = $pdo->query("SELECT * FROM facility_categories ORDER BY name ASC");
            $dbCats = $catStmt ? $catStmt->fetchAll(PDO::FETCH_ASSOC) : [];
            foreach ($dbCats as $dc) {
                $facilityCategories[$dc['slug']] = [
                    'name' => $dc['name'],
                    'icon' => !empty($dc['icon']) ? $dc['icon'] : 'fa-building'
                ];
            }
        } catch (PDOException $e) {}

        if (empty($facilityCategories)) {
            $facilityCategories = [
                'alat_pemadam' => ['name' => 'Alat Pemadam', 'icon' => 'fa-fire-extinguisher'],
                'balkon'       => ['name' => 'Balkon',       'icon' => 'fa-door-open'],
                'kantor'       => ['name' => 'Kantor',       'icon' => 'fa-building'],
                'kelas'        => ['name' => 'Kelas',        'icon' => 'fa-chalkboard-teacher'],
                'lobby'        => ['name' => 'Lobby',        'icon' => 'fa-couch'],
                'mushola'      => ['name' => 'Mushola',      'icon' => 'fa-mosque'],
                'parkiran'     => ['name' => 'Parkiran',     'icon' => 'fa-parking'],
                'toilet'       => ['name' => 'Toilet',       'icon' => 'fa-restroom']
            ];
        }

        $facilityItems = [];
        try {
            $stmt = $pdo->query("SELECT * FROM facility_locations ORDER BY id DESC");
            $dbItems = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
            if (!empty($dbItems)) {
                foreach ($dbItems as $dbi) {
                    $cat = $dbi['category'] ?? 'kelas';
                    $facilityItems[] = [
                        'src'      => $dbi['image'],
                        'category' => $cat,
                        'cat_name' => $facilityCategories[$cat]['name'] ?? ucfirst($cat),
                        'title'    => $dbi['title'] ?: ($facilityCategories[$cat]['name'] ?? 'Fasilitas') . ' MCM',
                        'filename' => basename($dbi['image'])
                    ];
                }
            }
        } catch (PDOException $e) {}

        // Fallback to scanning filesystem if database is empty
        if (empty($facilityItems)) {
            $baseFasilitasDir = dirname(__DIR__) . '/assets/img/fasilitas';
            foreach ($facilityCategories as $slug => $meta) {
                $catDir = $baseFasilitasDir . '/' . $slug;
                if (is_dir($catDir)) {
                    $files = scandir($catDir);
                    foreach ($files as $f) {
                        if ($f !== '.' && $f !== '..' && preg_match('/\.(jpe?g|png|webp)$/i', $f)) {
                            $facilityItems[] = [
                                'src'      => 'assets/img/fasilitas/' . $slug . '/' . $f,
                                'category' => $slug,
                                'cat_name' => $meta['name'],
                                'title'    => 'Fasilitas ' . $meta['name'] . ' MCM',
                                'filename' => $f
                            ];
                        }
                    }
                }
            }
        }
        ?>
        <div class="text-center mb-5 mt-5">
            <div class="badge mb-2 p-2 px-3 rounded-pill d-inline-block fw-bold" style="background-color: rgba(14, 165, 233, 0.1); color: #0ea5e9;">
                <i class="fas fa-building me-1"></i> FASILITAS & SARANA TEMPAT PELATIHAN
            </div>
            <h2 class="fw-bold display-6" style="color: #0c4a6e;">Dokumentasi Tempat Pelatihan MCM</h2>
            <p class="text-muted" style="max-width: 700px; margin: 0 auto;">
                Jelajahi dokumentasi sarana, prasarana, ruang belajar, dan fasilitas pendukung standar profesional LPK Mitra Cipta Mandiri berdasarkan kategori.
            </p>
        </div>

        <!-- Filter Buttons Per Kategori -->
        <div class="d-flex flex-wrap justify-content-center gap-2 mb-5" id="facilityFilterContainer">
            <button class="btn btn-sm rounded-pill px-3 py-2 fw-bold facility-filter-btn active" data-filter="all" style="transition: all 0.3s ease;">
                <i class="fas fa-th-large me-1"></i> Semua Kategori
            </button>
            <?php foreach ($facilityCategories as $slug => $meta): ?>
                <button class="btn btn-sm rounded-pill px-3 py-2 fw-bold facility-filter-btn" data-filter="<?php echo $slug; ?>" style="transition: all 0.3s ease;">
                    <i class="fas <?php echo $meta['icon']; ?> me-1"></i> <?php echo htmlspecialchars($meta['name']); ?>
                </button>
            <?php endforeach; ?>
        </div>

        <!-- Facility Cards Grid -->
        <div class="row g-4 mb-5" id="facilityGrid">
            <?php if (!empty($facilityItems)): ?>
                <?php foreach ($facilityItems as $idx => $item): ?>
                    <div class="col-lg-4 col-md-6 facility-item" data-category="<?php echo htmlspecialchars($item['category']); ?>" data-src="<?php echo htmlspecialchars(asset_src($item['src'])); ?>" data-title="<?php echo htmlspecialchars($item['title']); ?>" data-cat="<?php echo htmlspecialchars($item['cat_name']); ?>">
                        <div class="card border-0 shadow-sm rounded-5 overflow-hidden h-100 bg-white facility-card" style="transition: all 0.3s ease; border: 1px solid rgba(12, 74, 110, 0.08) !important;">
                            <div class="position-relative overflow-hidden facility-thumb" style="height: 250px; cursor: pointer;">
                                <img src="<?php echo htmlspecialchars(asset_src($item['src'])); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" class="w-100 h-100 facility-img" style="object-fit: cover; transition: transform 0.5s ease; background:#e2e8f0;" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='../assets/img/hero-bg.jpg';">
                                <div class="facility-overlay position-absolute inset-0 d-flex align-items-center justify-content-center" style="background: rgba(12, 74, 110, 0.4); opacity: 0; transition: all 0.3s ease;">
                                    <span class="btn btn-light rounded-circle shadow-sm" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; color: #0c4a6e;">
                                        <i class="fas fa-expand-alt"></i>
                                    </span>
                                </div>
                                <div class="position-absolute top-0 start-0 m-3">
                                    <span class="badge px-3 py-2 rounded-pill shadow-sm" style="background: linear-gradient(135deg, #0c4a6e, #0ea5e9); font-size: 0.72rem;">
                                        <i class="fas <?php echo $facilityCategories[$item['category']]['icon'] ?? 'fa-building'; ?> me-1"></i>
                                        <?php echo htmlspecialchars($item['cat_name']); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <i class="fas fa-images fs-1 text-muted mb-3 d-block opacity-50"></i>
                    <p class="text-muted mb-1 fw-bold">Belum ada foto dalam kategori ini.</p>
                    <small class="text-muted">Foto dapat dimasukkan ke folder <code>assets/img/fasilitas/{kategori}/</code></small>
                </div>
            <?php endif; ?>
        </div>

        <div id="facilityEmptyNotice" class="text-center py-5 d-none">
            <i class="fas fa-folder-open fs-1 text-muted mb-3 d-block opacity-50"></i>
            <p class="text-muted mb-1 fw-bold">Belum ada dokumentasi untuk kategori ini.</p>
            <small class="text-muted">Silakan masukkan foto ke folder <code>assets/img/fasilitas/<span id="emptyCatName"></span>/</code></small>
        </div>

        <style>
            .facility-filter-btn {
                background: #f1f5f9;
                color: #475569;
                border: 1px solid #e2e8f0;
            }
            .facility-filter-btn:hover {
                background: #e2e8f0;
                color: #0c4a6e;
            }
            .facility-filter-btn.active {
                background: linear-gradient(135deg, #0c4a6e, #0ea5e9) !important;
                color: #ffffff !important;
                border-color: transparent !important;
                box-shadow: 0 4px 15px rgba(14, 165, 233, 0.3) !important;
            }
            .facility-card:hover {
                transform: translateY(-8px);
                box-shadow: 0 20px 40px rgba(14, 165, 233, 0.15) !important;
            }
            .facility-card:hover .facility-img {
                transform: scale(1.08);
            }
            .facility-card:hover .facility-overlay {
                opacity: 1 !important;
            }
            /* === Gallery Lightbox Premium — konsisten dengan #galeri === */
            .gallery-lightbox .modal-dialog {
                transform: scale(0.92);
                transition: transform 0.32s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.22s ease;
            }
            .gallery-lightbox.show .modal-dialog { transform: scale(1); }
            .gallery-lightbox .modal-content { background: transparent !important; }
            .modal-backdrop.show {
                background: rgba(6, 12, 24, 0.82) !important;
                backdrop-filter: blur(10px);
                -webkit-backdrop-filter: blur(10px);
                opacity: 1 !important;
            }
            .gallery-lb-wrapper { padding: 18px 14px 10px; }
            .gallery-lb-frame {
                border-radius: 1.25rem;
                background: #0f172a;
                box-shadow: 0 25px 60px rgba(0,0,0,0.45);
            }
            #facilityModalImg {
                border-radius: 1.25rem;
                transition: opacity 0.22s ease;
                background: #0f172a;
                display: block;
            }
            .gallery-lb-close {
                position: absolute;
                top: 0; right: 0;
                z-index: 30;
                width: 44px; height: 44px;
                border: none; border-radius: 50%;
                background: rgba(15, 23, 42, 0.58);
                backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);
                border: 1px solid rgba(255,255,255,0.22);
                color: #fff;
                display: flex; align-items: center; justify-content: center;
                font-size: 1.05rem;
                transition: all 0.22s ease;
                box-shadow: 0 8px 18px rgba(0,0,0,0.28);
            }
            .gallery-lb-close:hover {
                background: rgba(15, 23, 42, 0.88);
                transform: scale(1.08) rotate(90deg);
                border-color: rgba(255,255,255,0.36);
                color: #fff;
            }
            .gallery-lb-nav {
                position: absolute;
                top: 50%; transform: translateY(-50%);
                z-index: 22;
                width: 48px; height: 48px;
                border: none; border-radius: 50%;
                background: rgba(15, 23, 42, 0.52);
                backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);
                border: 1px solid rgba(255,255,255,0.18);
                color: #fff;
                display: flex; align-items: center; justify-content: center;
                font-size: 1.05rem;
                transition: all 0.22s ease;
                box-shadow: 0 8px 20px rgba(0,0,0,0.28);
            }
            .gallery-lb-nav:hover {
                background: linear-gradient(135deg, #0c4a6e, #0ea5e9);
                border-color: transparent;
                transform: translateY(-50%) scale(1.08);
            }
            .gallery-lb-prev { left: 8px; }
            .gallery-lb-next { right: 8px; }
            @media (min-width: 992px) {
                .gallery-lb-prev { left: -18px; }
                .gallery-lb-next { right: -18px; }
                .gallery-lb-wrapper { padding: 8px 32px 10px; }
            }
            .gallery-lb-caption {
                position: absolute; left: 0; right: 0; bottom: 0;
                padding: 54px 18px 16px;
                background: linear-gradient(to top, rgba(0,0,0,0.84) 0%, rgba(0,0,0,0.52) 46%, transparent 100%);
                display: flex; justify-content: space-between; align-items: flex-end; gap: 12px;
                pointer-events: none;
                border-bottom-left-radius: 1.25rem; border-bottom-right-radius: 1.25rem;
            }
            .gallery-lb-badge {
                display: inline-flex; align-items: center;
                background: rgba(14,165,233,0.92);
                color: #fff;
                font-size: 0.72rem; font-weight: 700;
                padding: 4px 10px; border-radius: 999px;
                margin-bottom: 6px;
                box-shadow: 0 4px 10px rgba(0,0,0,0.22);
            }
            .gallery-lb-title {
                color: #fff; font-weight: 600;
                font-size: clamp(0.92rem, 2vw, 1.08rem); line-height: 1.35;
                text-shadow: 0 2px 10px rgba(0,0,0,0.55);
                margin: 0;
            }
            .gallery-lb-counter {
                flex-shrink: 0;
                background: rgba(255,255,255,0.16);
                border: 1px solid rgba(255,255,255,0.22);
                backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px);
                color: #fff; font-weight: 700; font-size: 0.78rem;
                letter-spacing: 0.3px; padding: 7px 12px; border-radius: 999px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.2);
                white-space: nowrap;
            }
            .gallery-lb-thumbs {
                display: flex; gap: 8px;
                overflow-x: auto; overflow-y: hidden;
                padding: 12px 2px 6px;
                scrollbar-width: thin;
                scrollbar-color: rgba(255,255,255,0.28) transparent;
                scroll-behavior: smooth;
                justify-content: flex-start;
            }
            @media (min-width: 768px) { .gallery-lb-thumbs { justify-content: center; } }
            .gallery-lb-thumbs::-webkit-scrollbar { height: 6px; }
            .gallery-lb-thumbs::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.28); border-radius: 999px; }
            .gallery-lb-thumb {
                flex: 0 0 auto; width: 64px; height: 64px;
                border-radius: 0.72rem; overflow: hidden;
                border: 2px solid transparent; opacity: 0.62;
                cursor: pointer; transition: all 0.22s ease;
                background: #0f172a; padding: 0;
            }
            .gallery-lb-thumb img { width: 100%; height: 100%; object-fit: cover; display: block; }
            .gallery-lb-thumb:hover { opacity: 1; transform: translateY(-2px); border-color: rgba(255,255,255,0.34); }
            .gallery-lb-thumb.active {
                opacity: 1; border-color: #0ea5e9;
                box-shadow: 0 0 0 3px rgba(14,165,233,0.26), 0 8px 18px rgba(0,0,0,0.32);
                transform: translateY(-1px);
            }
            @media (max-width: 576px) {
                .gallery-lb-wrapper { padding: 46px 8px 8px; }
                .gallery-lb-close { top: 2px; right: 2px; width: 40px; height: 40px; font-size: 1rem; }
                .gallery-lb-nav { width: 42px; height: 42px; font-size: 0.96rem; }
                .gallery-lb-prev { left: 6px; } .gallery-lb-next { right: 6px; }
                .gallery-lb-caption { padding: 40px 14px 12px; }
                .gallery-lb-thumb { width: 54px; height: 54px; }
                #facilityModalImg { max-height: 62vh !important; }
            }
        </style>
    </div>
</section>

<!-- Modal Preview Foto Tempat Pelatihan — Premium (konsisten dengan galeri) -->
<div class="modal fade gallery-lightbox" id="facilityModal" tabindex="-1" aria-hidden="true" aria-labelledby="facilityModalTitle" data-bs-backdrop="true" data-bs-keyboard="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content bg-transparent border-0 shadow-none">
            <div class="gallery-lb-wrapper position-relative mx-auto" style="max-width: 960px; width: 100%;">
                <button type="button" class="gallery-lb-close" data-bs-dismiss="modal" aria-label="Tutup">
                    <i class="fas fa-times"></i>
                </button>
                <button type="button" class="gallery-lb-nav gallery-lb-prev" id="facilityPrev" aria-label="Foto sebelumnya">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button type="button" class="gallery-lb-nav gallery-lb-next" id="facilityNext" aria-label="Foto berikutnya">
                    <i class="fas fa-chevron-right"></i>
                </button>
                <div class="gallery-lb-frame rounded-4 overflow-hidden shadow-lg bg-dark position-relative">
                    <img src="" id="facilityModalImg" class="w-100 d-block" style="max-height: 76vh; object-fit: contain; background: #0f172a;" alt="Dokumentasi Tempat Pelatihan">
                    <div class="gallery-lb-caption">
                        <div>
                            <span id="facilityModalCategory" class="gallery-lb-badge"><i class="fas fa-tag me-1"></i> Kategori</span>
                            <h5 id="facilityModalTitle" class="gallery-lb-title mb-0"></h5>
                        </div>
                        <span id="facilityModalCounter" class="gallery-lb-counter">1 / 1</span>
                    </div>
                </div>
                <div class="gallery-lb-thumbs" id="facilityThumbs" aria-label="Thumbnail fasilitas"></div>
            </div>
        </div>
    </div>
</div>

<script>
// Facility Lightbox Premium — konsisten dengan galleryLightbox di #galeri
document.addEventListener('DOMContentLoaded', function() {
    const filterBtns = document.querySelectorAll('.facility-filter-btn');
    const items = document.querySelectorAll('.facility-item');
    const grid = document.getElementById('facilityGrid');
    const emptyNotice = document.getElementById('facilityEmptyNotice');
    const emptyCatName = document.getElementById('emptyCatName');

    const modalEl = document.getElementById('facilityModal');
    const modal = modalEl ? new bootstrap.Modal(modalEl) : null;
    const mImg = document.getElementById('facilityModalImg');
    const mTitle = document.getElementById('facilityModalTitle');
    const mCat = document.getElementById('facilityModalCategory');
    const mCounter = document.getElementById('facilityModalCounter');
    const mThumbs = document.getElementById('facilityThumbs');
    const mPrev = document.getElementById('facilityPrev');
    const mNext = document.getElementById('facilityNext');
    const mFrame = modalEl ? modalEl.querySelector('.gallery-lb-frame') : null;

    let facList = [];
    let lbList = [];
    let lbIndex = 0;
    let currentFilter = 'all';

    // Build master list from DOM data-attributes
    items.forEach(function(el) {
        facList.push({
            el: el,
            src: el.getAttribute('data-src') || (el.querySelector('img') ? el.querySelector('img').src : ''),
            title: el.getAttribute('data-title') || 'Dokumentasi MCM',
            cat: el.getAttribute('data-category') || '',
            catName: el.getAttribute('data-cat') || ''
        });
    });

    function rebuildLbList() {
        if (currentFilter === 'all') lbList = facList.slice();
        else lbList = facList.filter(function(o) { return o.cat === currentFilter; });
    }

    function buildThumbs() {
        if (!mThumbs) return;
        mThumbs.innerHTML = '';
        lbList.forEach(function(it, i) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'gallery-lb-thumb' + (i === lbIndex ? ' active' : '');
            btn.setAttribute('aria-label', 'Foto ' + (i+1) + ': ' + it.title);
            const safeTitle = it.title.replace(/"/g, '&quot;');
            btn.innerHTML = '<img src="' + it.src + '" alt="' + safeTitle + '" loading="lazy">';
            btn.addEventListener('click', function() { showAt(i); });
            mThumbs.appendChild(btn);
        });
        const ac = mThumbs.querySelector('.gallery-lb-thumb.active');
        if (ac) ac.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
    }

    function updateLb() {
        if (!lbList.length) return;
        const it = lbList[lbIndex];
        if (mImg) {
            mImg.style.opacity = '0.35';
            const ns = it.src;
            setTimeout(function() {
                mImg.src = ns;
                mImg.alt = it.title;
                if (mImg.complete) mImg.style.opacity = '1';
                else mImg.onload = function() { mImg.style.opacity = '1'; };
            }, 110);
        }
        if (mTitle) mTitle.textContent = it.title;
        if (mCat) mCat.innerHTML = '<i class="fas fa-tag me-1"></i> ' + it.catName;
        if (mCounter) mCounter.textContent = (lbIndex + 1) + ' / ' + lbList.length;
        const single = lbList.length <= 1;
        if (mPrev) mPrev.style.display = single ? 'none' : '';
        if (mNext) mNext.style.display = single ? 'none' : '';
        if (mThumbs) {
            mThumbs.querySelectorAll('.gallery-lb-thumb').forEach(function(el, i) { el.classList.toggle('active', i === lbIndex); });
            const cur = mThumbs.querySelector('.gallery-lb-thumb.active');
            if (cur) cur.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
        }
    }

    function showAt(i) {
        if (!lbList.length) return;
        lbIndex = (i + lbList.length) % lbList.length;
        updateLb();
    }

    function openAt(i) {
        lbIndex = i;
        buildThumbs();
        updateLb();
        if (modal) modal.show();
    }

    // Legacy shim — tetap dukung onclick lama jika ada
    window.openFacilityModal = function(imgSrc, title, category) {
        const idx = facList.findIndex(function(o) { return o.src === imgSrc; });
        if (idx >= 0) {
            // cari idx di lbList (filter-aware)
            const fIdx = lbList.findIndex(function(o) { return o.src === imgSrc; });
            openAt(fIdx >= 0 ? fIdx : 0);
        } else {
            if (mImg) mImg.src = imgSrc;
            if (mTitle) mTitle.textContent = title;
            if (mCat) mCat.innerHTML = '<i class="fas fa-tag me-1"></i> ' + category;
            if (modal) modal.show();
        }
    };

    function applyFilter(filter) {
        currentFilter = filter;
        let visibleCount = 0;
        items.forEach(function(item) {
            const cat = item.getAttribute('data-category');
            if (filter === 'all' || cat === filter) {
                item.style.display = '';
                item.style.animation = 'fadeIn 0.4s ease';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });
        if (visibleCount === 0) {
            if (emptyNotice) emptyNotice.classList.remove('d-none');
            if (emptyCatName) emptyCatName.textContent = filter;
        } else {
            if (emptyNotice) emptyNotice.classList.add('d-none');
        }
        rebuildLbList();
    }

    // Delegated click pada grid — konsisten dengan galeri utama
    if (grid) {
        grid.addEventListener('click', function(e) {
            const item = e.target.closest('.facility-item');
            if (!item) return;
            const idx = lbList.findIndex(function(o) { return o.el === item; });
            if (idx >= 0) openAt(idx);
        });
    }

    if (mPrev) mPrev.addEventListener('click', function(e) { e.stopPropagation(); showAt(lbIndex - 1); });
    if (mNext) mNext.addEventListener('click', function(e) { e.stopPropagation(); showAt(lbIndex + 1); });

    function handleKey(e) {
        if (!modalEl || !modalEl.classList.contains('show')) return;
        if (e.key === 'ArrowLeft') { e.preventDefault(); showAt(lbIndex - 1); }
        else if (e.key === 'ArrowRight') { e.preventDefault(); showAt(lbIndex + 1); }
    }
    if (modalEl) {
        modalEl.addEventListener('shown.bs.modal', function() { document.addEventListener('keydown', handleKey); });
        modalEl.addEventListener('hidden.bs.modal', function() { document.removeEventListener('keydown', handleKey); });
        if (mFrame) {
            let sx = 0;
            mFrame.addEventListener('touchstart', function(e) { sx = e.touches[0].clientX; }, { passive: true });
            mFrame.addEventListener('touchend', function(e) {
                const dx = e.changedTouches[0].clientX - sx;
                if (Math.abs(dx) > 48) {
                    if (dx < 0) showAt(lbIndex + 1); else showAt(lbIndex - 1);
                }
            }, { passive: true });
        }
    }

    filterBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            filterBtns.forEach(function(b) { b.classList.remove('active'); });
            this.classList.add('active');
            const f = this.getAttribute('data-filter');
            applyFilter(f);
        });
    });

    // Init
    rebuildLbList();
});
</script>

<?php include '../includes/footer.php'; ?>
