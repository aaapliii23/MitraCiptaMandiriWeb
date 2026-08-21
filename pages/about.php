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
                    <img src="<?php echo htmlspecialchars(asset_src('assets/img/gallery/slide_landingpages/WhatsApp Image 2026-08-14 at 14.44.00.jpeg')); ?>" alt="MCM Office" class="img-fluid rounded-5 shadow-lg w-100" style="object-fit: cover; max-height: 450px;" onerror="this.onerror=null;this.src='../assets/img/hero-bg.jpg';">
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
                    <div class="col-lg-4 col-md-6 facility-item" data-category="<?php echo htmlspecialchars($item['category']); ?>">
                        <div class="card border-0 shadow-sm rounded-5 overflow-hidden h-100 bg-white facility-card" style="transition: all 0.3s ease; border: 1px solid rgba(12, 74, 110, 0.08) !important;">
                            <div class="position-relative overflow-hidden" style="height: 250px; cursor: pointer;" onclick="openFacilityModal('<?php echo htmlspecialchars(asset_src($item['src'])); ?>', '<?php echo htmlspecialchars($item['title']); ?>', '<?php echo htmlspecialchars($item['cat_name']); ?>')">
                                <img src="<?php echo htmlspecialchars(asset_src($item['src'])); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" class="w-100 h-100 facility-img" style="object-fit: cover; transition: transform 0.5s ease;" onerror="this.onerror=null;this.src='../assets/img/hero-bg.jpg';">
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
        </style>
    </div>
</section>

<!-- Modal Preview Foto Tempat Pelatihan -->
<div class="modal fade" id="facilityModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-5 overflow-hidden" style="background: #ffffff;">
            <div class="modal-header border-0 pb-0 pe-4 pt-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1 rounded-pill small mb-1" id="facilityModalCategory">Kategori</span>
                    <h6 class="modal-title fw-bold text-dark mb-0" id="facilityModalTitle">Dokumentasi Tempat Pelatihan MCM</h6>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <img id="facilityModalImg" src="" alt="Dokumentasi Tempat Pelatihan" class="img-fluid rounded-4 shadow-sm w-100" style="max-height: 550px; object-fit: contain;">
            </div>
        </div>
    </div>
</div>

<script>
    // Modal Opener
    function openFacilityModal(imgSrc, title, category) {
        document.getElementById('facilityModalImg').src = imgSrc;
        document.getElementById('facilityModalTitle').textContent = title;
        document.getElementById('facilityModalCategory').textContent = category;
        const modal = new bootstrap.Modal(document.getElementById('facilityModal'));
        modal.show();
    }

    // Category Filtering
    document.addEventListener('DOMContentLoaded', function() {
        const filterBtns = document.querySelectorAll('.facility-filter-btn');
        const items = document.querySelectorAll('.facility-item');
        const emptyNotice = document.getElementById('facilityEmptyNotice');
        const emptyCatName = document.getElementById('emptyCatName');

        filterBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                filterBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                const filter = this.getAttribute('data-filter');
                let visibleCount = 0;

                items.forEach(item => {
                    const itemCat = item.getAttribute('data-category');
                    if (filter === 'all' || itemCat === filter) {
                        item.style.display = '';
                        item.style.animation = 'fadeIn 0.4s ease';
                        visibleCount++;
                    } else {
                        item.style.display = 'none';
                    }
                });

                if (visibleCount === 0) {
                    emptyNotice.classList.remove('d-none');
                    if (emptyCatName) emptyCatName.textContent = filter;
                } else {
                    emptyNotice.classList.add('d-none');
                }
            });
        });
    });
</script>

<?php include '../includes/footer.php'; ?>
