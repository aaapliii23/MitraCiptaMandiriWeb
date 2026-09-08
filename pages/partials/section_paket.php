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
                            <div class="paket-card w-100 border-0 rounded-4 bg-white d-flex flex-column">
                                <a href="pages/class_detail.php?id=<?php echo $c['id']; ?>&from=landing" class="text-decoration-none d-block">
                                    <div class="position-relative w-100 overflow-hidden" style="height: 220px; flex-shrink: 0; border-top-left-radius: 1.25rem; border-top-right-radius: 1.25rem; background: #e2e8f0;">
                                        <img src="<?php echo htmlspecialchars(asset_src($c['image'])); ?>" alt="<?php echo htmlspecialchars($c['name']); ?>" class="w-100 h-100" style="object-fit: cover; object-position: top center; display: block;" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='assets/img/logo.png';">
                                        <div class="position-absolute top-0 end-0 m-3" style="z-index: 2;">
                                            <span class="badge bg-primary px-3 py-2 rounded-pill shadow-sm" style="font-size: 0.7rem; font-weight: 600;">MCM Official</span>
                                        </div>
                                    </div>
                                </a>
                                <div class="paket-card-content p-4 d-flex flex-column flex-grow-1 bg-white" style="border-bottom-left-radius: 1.25rem; border-bottom-right-radius: 1.25rem;">
                                    <a href="pages/class_detail.php?id=<?php echo $c['id']; ?>&from=landing" class="text-decoration-none text-dark">
                                        <h4 class="fw-bold mb-2 text-dark hover-primary" style="font-size: 1.2rem;"><?php echo htmlspecialchars($c['name']); ?></h4>
                                    </a>
                                    <div class="mb-3">
                                        <span class="badge bg-info bg-opacity-10 text-info px-3 py-2 rounded-pill small fw-bold" style="background-color: rgba(14, 165, 233, 0.1) !important;">
                                            <i class="fas fa-calendar-alt me-2"></i>Mulai: <?php echo date('d M Y', strtotime($c['start_date'])); ?>
                                        </span>
                                    </div>
                                    <p class="text-muted small mb-3" style="min-height: 2.6em; line-height: 1.4;"><?php echo substr(strip_tags($c['description']), 0, 90); ?>...</p>
                                    <?php
                                    $pLegacy2 = (int)($c['price'] ?? 0);
                                    $pOn2 = isset($c['price_online']) && (int)$c['price_online'] > 0 ? (int)$c['price_online'] : (int)round($pLegacy2 * 0.8);
                                    $pOff2 = isset($c['price_offline']) && (int)$c['price_offline'] > 0 ? (int)$c['price_offline'] : $pLegacy2;
                                    $ma2 = $c['mode_available'] ?? 'both';
                                    if ($ma2 === 'online') {
                                        echo '<h5 class="text-primary fw-bold mb-1" style="font-size:1.1rem;">Rp '.number_format($pOn2,0,',','.').'</h5><small class="text-muted d-block mb-3"><span class="badge bg-info bg-opacity-10 text-info" style="font-size:0.7rem;"><i class="fas fa-laptop me-1"></i>Online</span></small>';
                                    } elseif ($ma2 === 'offline') {
                                        echo '<h5 class="text-primary fw-bold mb-1" style="font-size:1.1rem;">Rp '.number_format($pOff2,0,',','.').'</h5><small class="text-muted d-block mb-3"><span class="badge bg-success bg-opacity-10 text-success" style="font-size:0.7rem;"><i class="fas fa-chalkboard-teacher me-1"></i>Offline</span></small>';
                                    } else {
                                        if ($pOn2 !== $pOff2) {
                                            echo '<div class="mb-3"><small class="text-muted d-block" style="font-size:0.72rem;">Mulai dari</small><h5 class="text-primary fw-bold mb-0" style="font-size:1.1rem;">Rp '.number_format(min($pOn2,$pOff2),0,',','.').'</h5><small class="text-muted" style="font-size:0.7rem;">Offline Rp '.number_format($pOff2,0,',','.').' &bull; Online Rp '.number_format($pOn2,0,',','.').'</small></div>';
                                        } else {
                                            echo '<h5 class="text-primary fw-bold mb-3" style="font-size:1.1rem;">Rp '.number_format($pLegacy2,0,',','.').'</h5>';
                                        }
                                    }
                                    ?>
                                    
                                    <div class="mb-3">
                                        <?php foreach (array_slice($featuresArr, 0, 3) as $f): ?>
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="fas fa-check-circle text-success me-2" style="font-size: 0.85rem;"></i>
                                                <span class="small text-secondary fw-medium"><?php echo htmlspecialchars($f); ?></span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>

                                    <div class="mt-auto pt-3 pb-2">
                                        <a href="pages/class_detail.php?id=<?php echo $c['id']; ?>&from=landing" class="btn btn-primary btn-premium w-100 py-3 rounded-pill fw-bold shadow-sm d-block text-center" 
                                                style="background: linear-gradient(135deg, #0c4a6e, #0ea5e9); border: none; font-size: 0.95rem;">
                                            Daftar Sekarang
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

            </div>
        </div>
    </section>
