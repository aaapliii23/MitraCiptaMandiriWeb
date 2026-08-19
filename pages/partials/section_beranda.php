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
