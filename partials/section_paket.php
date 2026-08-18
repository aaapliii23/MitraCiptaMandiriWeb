    <!-- Paket Pelatihan (Swiper Slider) -->
    <section id="paket" class="section-padding bg-light">
        <div class="container">
            <?php $examinerParam = (int)($_GET['examiner'] ?? 0) > 0 ? '&examiner=' . (int)$_GET['examiner'] : ''; ?>
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
                                        <a href="pages/class_detail.php?id=<?php echo $c['id']; ?>&from=landing<?php echo $examinerParam; ?>" class="btn btn-primary btn-premium w-100 py-3 rounded-pill fw-bold shadow-sm" 
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
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            </div>
        </div>
    </section>
