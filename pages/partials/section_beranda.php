    <!-- Hero Section -->
    <?php
    $heroSlides = array_values(array_filter($galleryItems, function($g) { return (int)($g['show_on_home'] ?? 0) === 1; }));
    ?>
    <section id="beranda" class="hero-section<?php echo !empty($heroSlides) ? ' has-slider' : ''; ?>">
        <?php if (!empty($heroSlides)): ?>
        <!-- Background Photo Slider -->
        <div class="hero-swiper swiper" id="heroSwiper">
            <div class="swiper-wrapper">
                <?php foreach ($heroSlides as $gs): ?>
                <div class="swiper-slide">
                    <img src="<?php echo htmlspecialchars(asset_src($gs['image'])); ?>" alt="<?php echo htmlspecialchars($gs['title']); ?>">
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="hero-overlay"></div>
        <script>
            new Swiper('#heroSwiper', {
                effect: 'fade',
                fadeEffect: { crossFade: true },
                loop: true,
                autoplay: { delay: 4500, disableOnInteraction: false },
                speed: 1200
            });
        </script>
        <?php endif; ?>
        <div class="container hero-container">
            <div class="row align-items-center">
                <div class="col-lg-7 hero-content" data-aos="fade-right">
                    <h1 class="display-3 fw-bold text-white mb-4">Raih Kemandirian<br>Bersama MCM</h1>
                    <p class="lead mb-4 text-white-50">Lembaga pelatihan vokasi premium yang membekali Anda dengan keahlian praktis dan siap kerja. Bangun karir atau bisnis Anda hari ini.</p>
                    <div class="btn-group-custom mt-5 d-flex flex-column flex-md-row gap-3">
                        <a href="pages/programs.php" class="btn rounded-pill fw-bold btn-hero-primary" style="background: linear-gradient(135deg, #0c4a6e, #0ea5e9); color: white; border: none; padding: 16px 42px; box-shadow: 0 10px 25px -5px rgba(14, 165, 233, 0.4); display: flex; align-items: center; justify-content: center; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); letter-spacing: 0.5px;">
                            <i class="fas fa-layer-group me-2"></i> Jelajahi Pelatihan
                        </a>
                        <a href="pages/about.php" class="btn btn-outline-light px-5 py-3 rounded-pill fw-bold btn-hero-secondary" style="border: 2px solid rgba(255,255,255,0.7); transition: all 0.4s ease; display: flex; align-items: center; justify-content: center; padding: 16px 42px; letter-spacing: 0.5px;">
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

                <!-- Right Side Clean Photo Slider -->
                <div class="col-lg-5 d-none d-lg-block" data-aos="fade-left" data-aos-delay="200">
                    <div class="position-relative ms-lg-4">
                        <?php
                        $slideFolder = dirname(__DIR__, 2) . '/assets/img/gallery/slide_landingpages';
                        $slideImages = [];
                        if (is_dir($slideFolder)) {
                            $scanned = scandir($slideFolder);
                            foreach ($scanned as $sf) {
                                if ($sf !== '.' && $sf !== '..' && preg_match('/\.(jpe?g|png|webp)$/i', $sf)) {
                                    $slideImages[] = 'assets/img/gallery/slide_landingpages/' . $sf;
                                }
                            }
                        }
                        if (empty($slideImages)) {
                            foreach ($galleryItems as $gi) {
                                $slideImages[] = $gi['image'];
                            }
                        }
                        ?>
                        <!-- Clean Photo Frame -->
                        <div class="swiper activitySwiper rounded-5 overflow-hidden position-relative" style="height: 440px; border: 3px solid rgba(255, 255, 255, 0.25); box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.6);">
                            <div class="swiper-wrapper">
                                <?php foreach ($slideImages as $img): ?>
                                <div class="swiper-slide">
                                    <img src="<?php echo htmlspecialchars(asset_src($img)); ?>" alt="Kegiatan MCM" class="w-100 h-100" style="object-fit: cover;">
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <!-- Minimalist Pagination Dots -->
                            <div class="swiper-pagination activity-pagination pb-2"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                new Swiper('.activitySwiper', {
                    effect: 'fade',
                    fadeEffect: { crossFade: true },
                    loop: true,
                    autoplay: {
                        delay: 2000,
                        disableOnInteraction: false,
                    },
                    speed: 1000,
                    pagination: {
                        el: '.activity-pagination',
                        clickable: true,
                        dynamicBullets: true,
                    }
                });
            });
        </script>
    </section>
