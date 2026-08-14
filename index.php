<?php
session_start();
// Generate CSRF Token for the form
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

require_once 'includes/db_config.php';

// Fetch Gallery
try {
    $stmt = $pdo->query("SELECT * FROM gallery ORDER BY id ASC");
    $galleryItems = $stmt->fetchAll();
} catch(PDOException $e) { $galleryItems = []; }

// Fetch Classes
try {
    $stmt = $pdo->query("SELECT * FROM classes ORDER BY id ASC");
    $classItems = $stmt->fetchAll();
} catch(PDOException $e) { $classItems = []; }

// Prepare Class Details for JS
$jsClassData = [];
foreach($classItems as $c) {
    $jsClassData[$c['id']] = [
        'id' => $c['id'],
        'name' => $c['name'],
        'description' => $c['description'],
        'price' => $c['price'],
        'image' => $c['image'],
        'features' => json_decode($c['features'], true) ?: []
    ];
}
?>
<script>window.mcmClassDetails = <?php echo json_encode($jsClassData); ?>;</script>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MCM - Mitra Cipta Mandiri | Solusi Kemandirian Ekonomi</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- AOS Animation CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css"/>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
</head>
<body>

    <?php include 'includes/header.php'; ?>

    <!-- Hero Section -->
    <section id="beranda" class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7 hero-content" data-aos="fade-right">
                    <h1 class="display-3 fw-bold text-white mb-4">Raih Kemandirian<br>Bersama MCM</h1>
                    <p class="lead mb-4 text-white-50">Lembaga pelatihan vokasi premium yang membekali Anda dengan keahlian praktis dan siap kerja. Bangun karir atau bisnis Anda hari ini.</p>
                    <div class="btn-group-custom mt-5 d-flex flex-column flex-md-row gap-3">
                        <a href="programs.php" class="btn rounded-pill fw-bold btn-hero-primary" style="background: linear-gradient(135deg, #0c4a6e, #0ea5e9); color: white; border: none; padding: 16px 42px; box-shadow: 0 10px 25px -5px rgba(14, 165, 233, 0.4); display: flex; align-items: center; justify-content: center; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); letter-spacing: 0.5px;">
                            <i class="fas fa-layer-group me-2"></i> Jelajahi Pelatihan
                        </a>
                        <a href="about.php" class="btn btn-outline-light px-5 py-3 rounded-pill fw-bold btn-hero-secondary" style="border: 2px solid rgba(255,255,255,0.7); transition: all 0.4s ease; display: flex; align-items: center; justify-content: center; padding: 16px 42px; letter-spacing: 0.5px;">
                            <i class="fas fa-university me-2"></i> Tentang MCM
                        </a>
                    </div>
                    
                    <!-- Stats in Hero -->
                    <div class="row text-white border-top border-light pt-4 mt-5" style="opacity: 0.9;">
                        <div class="col-4">
                            <h3 class="fw-bold mb-0">5+</h3>
                            <small>Program Pilihan</small>
                        </div>
                        <div class="col-4">
                            <h3 class="fw-bold mb-0">100+</h3>
                            <small>Alumni Sukses</small>
                        </div>
                        <div class="col-4">
                            <h3 class="fw-bold mb-0"><i class="fas fa-certificate"></i></h3>
                            <small>Tersertifikasi</small>
                        </div>
                    </div>
                </div>

                <!-- Right Side Feature Card -->
                <div class="col-lg-5 d-none d-lg-block" data-aos="fade-left" data-aos-delay="200">
                    <div class="p-5 rounded-4 ms-lg-4" style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.15); backdrop-filter: blur(20px); box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);">
                        <div class="d-flex align-items-center mb-5">
                            <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 56px; height: 56px; background: #f59e0b; border: 3px solid rgba(255,255,255,0.3); color: white; box-shadow: 0 10px 25px rgba(245, 158, 11, 0.4);">
                                <i class="fas fa-award fs-4"></i>
                            </div>
                            <h4 class="text-white mb-0 fw-bold" style="letter-spacing: 0.5px;">Keunggulan MCM</h4>
                        </div>
                        
                        <ul class="list-unstyled text-white mb-0">
                            <li class="mb-4 d-flex align-items-start">
                                <div class="bg-white bg-opacity-10 p-2 rounded-3 me-3" style="border: 1px solid rgba(245, 158, 11, 0.4); width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-user-tie" style="color: #f59e0b; font-size: 1.2rem;"></i>
                                </div>
                                <div>
                                    <strong class="d-block mb-1 text-white" style="font-size: 1.1rem;">Instruktur Praktisi</strong>
                                    <small class="text-white-50" style="font-size: 0.85rem;">Belajar langsung dari tenaga ahli profesional industri.</small>
                                </div>
                            </li>
                            <li class="mb-4 d-flex align-items-start">
                                <div class="bg-white bg-opacity-10 p-2 rounded-3 me-3" style="border: 1px solid rgba(245, 158, 11, 0.4); width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-microchip" style="color: #f59e0b; font-size: 1.2rem;"></i>
                                </div>
                                <div>
                                    <strong class="d-block mb-1 text-white" style="font-size: 1.1rem;">Fasilitas Modern</strong>
                                    <small class="text-white-50" style="font-size: 0.85rem;">Peralatan standar industri terbaru dan memadai.</small>
                                </div>
                            </li>
                            <li class="d-flex align-items-start">
                                <div class="bg-white bg-opacity-10 p-2 rounded-3 me-3" style="border: 1px solid rgba(245, 158, 11, 0.4); width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-chart-line" style="color: #f59e0b; font-size: 1.2rem;"></i>
                                </div>
                                <div>
                                    <strong class="d-block mb-1 text-white" style="font-size: 1.1rem;">Jalur Karir Strategis</strong>
                                    <small class="text-white-50" style="font-size: 0.85rem;">Pendampingan penempatan kerja dan rintisan usaha.</small>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="tentang" class="section-padding bg-white">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-5 mb-lg-0" data-aos="fade-right">
                    <!-- Placeholder for About Image, using a colored div or unspash placeholder -->
                    <div class="position-relative">
                        <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Tentang MCM" class="img-fluid rounded-4 shadow-lg">
                        <div class="position-absolute bottom-0 end-0 bg-white p-4 rounded-4 shadow-lg" style="transform: translate(20px, 20px);">
                            <h3 class="mb-0 fw-bold" style="color: var(--secondary-color) !important;">10+</h3>
                            <p class="text-muted mb-0">Tahun Pengalaman</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 ps-lg-5" data-aos="fade-left">
                    <div class="section-title text-start mb-4">
                        <h2>Siapa Kami?</h2>
                    </div>
                    <p class="lead text-muted">Mitra Cipta Mandiri (MCM) adalah solusi terpercaya untuk pengembangan kompetensi dan kemandirian ekonomi.</p>
                    <p>Kami hadir untuk memberikan pelatihan yang aplikatif dan berbasis industri. Dengan instruktur profesional, fasilitas memadai, dan metode pembelajaran interaktif, kami memastikan setiap peserta siap menghadapi dunia kerja atau memulai wirausaha mandiri.</p>
                    
                    <div class="row mt-4">
                        <div class="col-sm-6 mb-3">
                            <a href="certification.php" class="text-decoration-none d-flex align-items-center p-3 rounded-4 transition-all bg-white shadow-sm border border-info border-opacity-10 hover-button-premium">
                                <div class="bg-info bg-opacity-10 p-3 rounded-circle me-3 text-info">
                                    <i class="fas fa-check-circle fa-lg"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0 fw-bold" style="color: #0c4a6e;">Tersertifikasi</h6>
                                    <small class="text-muted" style="font-size: 0.7rem;">Lihat Legalitas <i class="fas fa-chevron-right ms-1"></i></small>
                                </div>
                            </a>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <a href="examiners.php" class="text-decoration-none d-flex align-items-center p-3 rounded-4 transition-all bg-white shadow-sm border border-info border-opacity-10 hover-button-premium">
                                <div class="bg-info bg-opacity-10 p-3 rounded-circle me-3 text-info">
                                    <i class="fas fa-user-shield fa-lg"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0 fw-bold" style="color: #0c4a6e;">Profil Penguji</h6>
                                    <small class="text-muted" style="font-size: 0.7rem;">Lihat Ahli Kami <i class="fas fa-chevron-right ms-1"></i></small>
                                </div>
                            </a>
                        </div>
                    </div>

                    <style>
                        .hover-button-premium {
                            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                        }
                        .hover-button-premium:hover {
                            transform: translateY(-5px);
                            box-shadow: 0 10px 25px rgba(14, 165, 233, 0.15) !important;
                            border-color: #0ea5e9 !important;
                            background: linear-gradient(to right, #ffffff, #f0f9ff) !important;
                        }
                        .hover-button-premium:hover i.fa-chevron-right {
                            transform: translateX(3px);
                            transition: transform 0.3s ease;
                        }
                    </style>
                </div>
            </div>
        </div>
    </section>


    <!-- Gallery Section -->
    <section id="galeri" class="section-padding bg-white">
        <div class="container">
            <div class="section-title text-center mb-5" data-aos="fade-up">
                <h2 class="fw-bold display-5 mb-3">Galeri <span class="text-secondary">Kegiatan</span></h2>
                <p class="text-secondary fs-5">Momen-momen inspiratif selama pelatihan di MCM</p>
            </div>

            <!-- Filters -->
            <div class="gallery-filters" data-aos="fade-up" data-aos-delay="100">
                <button class="filter-btn active" data-filter="all">Semua</button>
                <button class="filter-btn" data-filter="public_speaking">Public Speaking</button>
                <button class="filter-btn" data-filter="tata_rias">Tata Rias</button>
                <button class="filter-btn" data-filter="pijat">Pijat</button>
                <button class="filter-btn" data-filter="barber">Barber</button>
                <button class="filter-btn" data-filter="catering">Catering</button>
            </div>

            <!-- Gallery Grid -->
            <div class="row g-4" id="galleryGrid" data-aos="fade-up" data-aos-delay="200">
                <?php foreach ($galleryItems as $item): ?>
                <div class="col-md-4 col-sm-6 gallery-item" data-category="<?php echo htmlspecialchars($item['category']); ?>">
                    <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                    <div class="overlay"><h5><?php echo htmlspecialchars($item['title']); ?></h5></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Paket Pelatihan (Swiper Slider) -->
    <section id="paket" class="section-padding bg-light">
        <div class="container">
            <div class="section-title mb-5" data-aos="fade-up">
                <h2 class="fw-bold display-6 mb-3 text-center">Pilihan <span class="text-secondary">Paket Pelatihan</span></h2>
                <p class="text-secondary text-center">Pilih paket yang paling sesuai dengan kebutuhan Anda</p>
            </div>
            
            <div class="paket-slider-container" data-aos="fade-up" data-aos-delay="100">
                <div class="swiper paketSwiper">
                    <div class="swiper-wrapper">
                        <?php foreach ($classItems as $c): 
                            $featuresArr = json_decode($c['features'], true) ?: [];
                        ?>
                        <div class="swiper-slide d-flex align-items-stretch">
                            <div class="paket-card w-100 shadow-sm border-0 rounded-4 bg-white d-flex flex-column">
                                <div class="position-relative" style="height: 190px; flex-shrink: 0; border-top-left-radius: 1rem; border-top-right-radius: 1rem; overflow: hidden;">
                                    <img src="<?php echo htmlspecialchars($c['image']); ?>" alt="<?php echo htmlspecialchars($c['name']); ?>" class="w-100 h-100" style="object-fit: cover;">
                                    <div class="position-absolute top-0 end-0 m-3">
                                        <span class="badge bg-primary px-3 py-2 rounded-pill shadow-sm" style="font-size: 0.7rem; font-weight: 600;">MCM Official</span>
                                    </div>
                                </div>
                                <div class="paket-card-content p-4 pb-4 d-flex flex-column flex-grow-1">
                                    <h4 class="fw-bold mb-2 text-dark" style="font-size: 1.2rem;"><?php echo htmlspecialchars($c['name']); ?></h4>
                                    <div class="mb-3">
                                        <span class="badge bg-info bg-opacity-10 text-info px-3 py-2 rounded-pill small fw-bold" style="background-color: rgba(14, 165, 233, 0.1) !important;">
                                            <i class="fas fa-calendar-alt me-2"></i>Mulai: <?php echo date('d M Y', strtotime($c['start_date'])); ?>
                                        </span>
                                    </div>
                                    <p class="text-muted small mb-3" style="min-height: 2.6em; line-height: 1.4;"><?php echo substr(strip_tags($c['description']), 0, 90); ?>...</p>
                                    <h5 class="text-primary fw-bold mb-3" style="font-size: 1.1rem;">Rp <?php echo number_format($c['price'], 0, ',', '.'); ?></h5>
                                    
                                    <div class="mb-3 flex-grow-1">
                                        <?php foreach (array_slice($featuresArr, 0, 3) as $f): ?>
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="fas fa-check-circle text-success me-2" style="font-size: 0.85rem;"></i>
                                                <span class="small text-secondary fw-medium"><?php echo htmlspecialchars($f); ?></span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>

                                    <div class="pt-2 mt-auto">
                                        <a href="class_detail.php?id=<?php echo $c['id']; ?>&from=landing" class="btn btn-primary btn-premium w-100 py-3 rounded-pill fw-bold shadow-sm" 
                                                style="background: linear-gradient(135deg, #0c4a6e, #0ea5e9); border: none; font-size: 0.9rem;">
                                            Daftar Sekarang
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <!-- Swiper Navigation -->
                <div class="swiper-button-next" style="color: var(--primary-color); transform: scale(0.7); background: white; width: 50px; height: 50px; border-radius: 50%; shadow: 0 4px 10px rgba(0,0,0,0.1);"></div>
                <div class="swiper-button-prev" style="color: var(--primary-color); transform: scale(0.7); background: white; width: 50px; height: 50px; border-radius: 50%; shadow: 0 4px 10px rgba(0,0,0,0.1);"></div>
            </div>
        </div>
    </section>

    <!-- Class Detail: Full Professional Dashboard Portal -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content border-0" style="background: #f8fafc;">
                <!-- Header Branding (Consistent with Navbar) -->
                <nav class="navbar navbar-expand bg-white border-bottom py-3 px-4 shadow-sm" style="position: sticky; top: 0; z-index: 100;">
                    <div class="container-fluid">
                        <a class="navbar-brand d-flex align-items-center" href="#">
                            <img src="assets/img/logo.png" alt="MCM Logo" style="height: 35px; width: auto;">
                            <div class="ms-2 ps-2 border-start border-2 border-dark d-flex flex-column justify-content-center" style="height: 30px;">
                                <span class="fw-bold text-dark" style="font-size: 0.7rem; letter-spacing: 1px; line-height: 1.1;">MITRA CIPTA</span>
                                <span class="fw-bold text-dark" style="font-size: 0.7rem; letter-spacing: 1px; line-height: 1.1;">MANDIRI</span>
                            </div>
                        </a>
                        <div class="ms-auto d-flex align-items-center">
                            <span class="text-muted small d-none d-md-block me-3"><i class="fas fa-info-circle me-1"></i> Informasi Lengkap Program Pelatihan</span>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                    </div>
                </nav>

                <div class="container py-4 px-3 px-md-5">
                    <div class="row g-4">
                        <!-- Main Content Area -->
                        <div class="col-lg-8">
                            <!-- Hero Brief -->
                            <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                                <div class="row g-0">
                                    <div class="col-md-5">
                                        <img src="" id="detailModalImage" class="w-100 h-100" style="object-fit: cover; min-height: 250px;" alt="Banner">
                                    </div>
                                    <div class="col-md-7 p-4 p-md-5 d-flex flex-column justify-content-center">
                                        <div class="badge mb-3 p-2 px-3 rounded-pill d-inline-block" style="width: fit-content; background-color: rgba(14, 165, 233, 0.1); color: #0ea5e9;">PROFIL PROGRAM</div>
                                        <h1 class="fw-bold mb-3" id="detailModalTitle" style="color: #0c4a6e;">Nama Kelas</h1>
                                        <p class="text-secondary fs-5" id="detailModalDescShort">Pelatihan vokasi terintegrasi dengan standar industri nasional.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Detailed Tabs Section -->
                            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                                <div class="card-header bg-white p-0">
                                    <ul class="nav nav-pills p-3 gap-2" id="pills-tab" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active fw-bold rounded-pill" id="pills-desc-tab" data-bs-toggle="pill" data-bs-target="#pills-desc" type="button" role="tab" style="--bs-nav-pills-link-active-bg: #0c4a6e;"><i class="fas fa-align-left me-2"></i>Penjelasan</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link fw-bold rounded-pill" id="pills-materi-tab" data-bs-toggle="pill" data-bs-target="#pills-materi" type="button" role="tab" style="color: #0c4a6e;"><i class="fas fa-book me-2"></i>Kurikulum</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link fw-bold rounded-pill" id="pills-terms-tab" data-bs-toggle="pill" data-bs-target="#pills-terms" type="button" role="tab" style="color: #0c4a6e;"><i class="fas fa-shield-alt me-2"></i>Ketentuan</button>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-body p-4 p-md-5">
                                    <div class="tab-content" id="pills-tabContent">
                                        <!-- Penjelasan Tab -->
                                        <div class="tab-pane fade show active" id="pills-desc" role="tabpanel">
                                            <div class="animate-content">
                                                <h4 class="fw-bold mb-4">Visi & Tujuan Program</h4>
                                                <p class="text-secondary lh-lg fs-5 mb-5" id="detailModalDesc">
                                                    Deskripsi lengkap mengenai program pelatihan akan dimuat di sini secara rinci dan mendalam.
                                                </p>
                                                
                                                <div class="row g-4 mb-5">
                                                    <div class="col-md-6">
                                                        <div class="p-4 rounded-4 bg-white border shadow-sm h-100">
                                                            <div class="bg-primary bg-opacity-10 p-2 rounded-3 d-inline-block mb-3"><i class="fas fa-bullseye text-primary"></i></div>
                                                            <h6 class="fw-bold">Target Peserta</h6>
                                                            <p class="small text-muted mb-0">Program ini dirancang bagi pemula, praktisi, maupun profesional yang ingin memperdalam keahlian teknis secara sistematis.</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="p-4 rounded-4 bg-white border shadow-sm h-100">
                                                            <div class="bg-primary bg-opacity-10 p-2 rounded-3 d-inline-block mb-3"><i class="fas fa-rocket text-primary"></i></div>
                                                            <h6 class="fw-bold">Output Kompetensi</h6>
                                                            <p class="small text-muted mb-0">Lulusan akan memiliki standar keahlian industri dan sertifikasi resmi yang diakui untuk menunjang karir atau bisnis mandiri.</p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <h5 class="fw-bold mb-4">Kenapa Harus Bergabung?</h5>
                                                <div class="row g-3">
                                                    <div class="col-sm-6">
                                                        <div class="d-flex p-3 rounded-4 bg-light align-items-center hover-lift transition-all">
                                                            <div class="bg-success bg-opacity-20 p-2 rounded-circle me-3"><i class="fas fa-check text-success small"></i></div>
                                                            <span class="fw-bold small">Kurikulum Berbasis Industri</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="d-flex p-3 rounded-4 bg-light align-items-center hover-lift transition-all">
                                                            <div class="bg-success bg-opacity-20 p-2 rounded-circle me-3"><i class="fas fa-check text-success small"></i></div>
                                                            <span class="fw-bold small">Peralatan Standar Profesional</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="d-flex p-3 rounded-4 bg-light align-items-center hover-lift transition-all">
                                                            <div class="bg-success bg-opacity-20 p-2 rounded-circle me-3"><i class="fas fa-check text-success small"></i></div>
                                                            <span class="fw-bold small">Pendampingan Mentor Ahli</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="d-flex p-3 rounded-4 bg-light align-items-center hover-lift transition-all">
                                                            <div class="bg-success bg-opacity-20 p-2 rounded-circle me-3"><i class="fas fa-check text-success small"></i></div>
                                                            <span class="fw-bold small">Akses Jaringan Alumni MCM</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Materi Tab -->
                                        <div class="tab-pane fade" id="pills-materi" role="tabpanel">
                                            <h4 class="fw-bold mb-4">Apa yang Akan Anda Pelajari?</h4>
                                            <div class="row g-3" id="detailModalFeatures">
                                                <!-- Features injected by JS -->
                                            </div>
                                        </div>
                                        <!-- Ketentuan Tab -->
                                        <div class="tab-pane fade" id="pills-terms" role="tabpanel">
                                            <h4 class="fw-bold mb-4">Syarat & Ketentuan Peserta</h4>
                                            <div class="bg-warning bg-opacity-10 p-4 rounded-4 border border-warning border-opacity-20 mb-4">
                                                <p class="mb-0 small"><i class="fas fa-exclamation-triangle me-2 text-warning"></i> Mohon baca seluruh ketentuan sebelum melakukan pendaftaran.</p>
                                            </div>
                                            <ul class="list-group list-group-flush">
                                                <li class="list-group-item border-0 px-0 py-3 d-flex align-items-start">
                                                    <div class="bg-primary bg-opacity-10 p-1 rounded-circle me-3 mt-1"><i class="fas fa-user-check text-primary small"></i></div>
                                                    <div><h6 class="fw-bold mb-1">Persyaratan Dasar</h6><p class="small text-muted mb-0">Peserta minimal berusia 17 tahun atau sudah memiliki KTP/Identitas.</p></div>
                                                </li>
                                                <li class="list-group-item border-0 px-0 py-3 d-flex align-items-start">
                                                    <div class="bg-primary bg-opacity-10 p-1 rounded-circle me-3 mt-1"><i class="fas fa-calendar-alt text-primary small"></i></div>
                                                    <div><h6 class="fw-bold mb-1">Kehadiran</h6><p class="small text-muted mb-0">Peserta wajib mengikuti minimal 80% dari total pertemuan untuk kelulusan.</p></div>
                                                </li>
                                                <li class="list-group-item border-0 px-0 py-3 d-flex align-items-start">
                                                    <div class="bg-primary bg-opacity-10 p-1 rounded-circle me-3 mt-1"><i class="fas fa-file-invoice-dollar text-primary small"></i></div>
                                                    <div><h6 class="fw-bold mb-1">Administrasi</h6><p class="small text-muted mb-0">Biaya pendaftaran tidak dapat dikembalikan jika peserta mengundurkan diri setelah kelas dimulai.</p></div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sidebar Actions Area -->
                        <div class="col-lg-4">
                            <!-- Registration Card -->
                            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 text-center sticky-top" style="top: 100px;">
                                <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle d-inline-block mb-4" style="width: 70px; height: 70px; line-height: 40px;">
                                    <i class="fas fa-user-edit fs-3"></i>
                                </div>
                                <h3 class="fw-bold mb-2">Daftar Sekarang</h3>
                                <p class="text-muted mb-4">Amankan kursi Anda sekarang dan mulai perjalanan karir profesional bersama MCM.</p>
                                
                                <button type="button" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-lg mb-3" id="btnLanjutBooking" style="background: linear-gradient(135deg, #0c4a6e, #0ea5e9); border: none; font-size: 1.1rem;">
                                    Mulai Isi Pendaftaran
                                </button>
                                
                                <hr class="my-4">
                                
                                <div class="text-start">
                                    <h6 class="fw-bold mb-3 small text-uppercase" style="letter-spacing: 1px;">Konsultasi Gratis</h6>
                                    <div class="d-flex align-items-center p-3 rounded-4 bg-light border border-light">
                                        <i class="fab fa-whatsapp fs-3 text-success me-3"></i>
                                        <div>
                                            <p class="small fw-bold mb-0">Hubungi Admin</p>
                                            <a href="https://wa.me/6285793935707" target="_blank" class="small text-decoration-none text-primary">Tanya lewat WA</a>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 p-3 rounded-4 border border-dashed text-center">
                                    <p class="small text-muted mb-0"><i class="fas fa-lock me-2"></i>Data pendaftaran Anda aman dan hanya digunakan untuk keperluan pelatihan.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                                    <div class="col-12 text-center mt-4">
                                        <button type="button" class="btn btn-primary btn-lg rounded-pill px-5 fw-bold w-100" id="btnCheckoutLanjut">Lanjutkan ke Pembayaran</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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

                            <form id="checkoutForm" action="process_checkout.php" method="POST">
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
                                </div>
                                
                                <div class="alert alert-info border-0 d-flex align-items-center mt-3 mb-3 p-2" style="border-radius: 0.75rem;">
                                    <i class="fas fa-info-circle fs-5 me-2"></i>
                                    <small style="font-size: 0.7rem; line-height: 1.2;">Setelah klik Place Order, pesanan akan tersimpan dan diarahkan ke WhatsApp Admin.</small>
                                </div>
                            
                                <input type="hidden" name="class_id" id="checkoutClassId">
                                <button type="submit" class="btn btn-primary w-100 py-2 rounded-pill fw-bold shadow-sm" style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); border: none; font-size: 0.9rem;">
                                    <i class="fas fa-check-circle me-2"></i>Selesaikan Pendaftaran & Hubungi WA
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <!-- Scripts -->
    <script>
        // Inject PHP data to JS
        const serverClassData = <?php 
            $jsClasses = [];
            foreach($classItems as $c) {
                $jsClasses[$c['name']] = [
                    'title' => $c['name'],
                    'desc' => $c['description'],
                    'image' => $c['image'],
                    'features' => json_decode($c['features'], true) ?: []
                ];
            }
            echo json_encode($jsClasses);
        ?>;
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="assets/js/script.js?v=<?php echo time(); ?>"></script>
</body>
</html>
