<!-- Testimoni Section -->
    <section id="testimoni" class="section-padding bg-light">
        <div class="container">
            <div class="section-title mb-5" data-aos="fade-up">
                <h2 class="fw-bold display-6 mb-3 text-center">Apa Kata <span class="text-secondary">Mereka?</span></h2>
                <p class="text-secondary text-center">Testimoni peserta yang telah mengikuti pelatihan di MCM</p>
            </div>

            <?php
            $testimonials = [];
            try {
                $stmt = $pdo->query("SELECT t.*, c.name as class_name FROM testimonials t LEFT JOIN classes c ON t.class_id = c.id WHERE t.status = 'approved' ORDER BY t.created_at DESC");
                $testimonials = $stmt->fetchAll();
            } catch (PDOException $e) { $testimonials = []; }
            ?>

            <?php if (!empty($testimonials)): ?>
            <div class="row g-4">
                <?php foreach ($testimonials as $t):
                    $initials = strtoupper(mb_substr(trim($t['name']), 0, 1));
                    $rating = (int)$t['rating'];
                ?>
                <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="<?php echo $loopIndex = ($loopIndex ?? 0) + 1; ?>0">
                    <div class="card border-0 shadow-sm rounded-4 h-100 p-4">
                        <div class="d-flex align-items-center mb-3">
                            <?php if (!empty($t['image'])): ?>
                                <img src="<?php echo htmlspecialchars($t['image']); ?>" alt="<?php echo htmlspecialchars($t['name']); ?>" class="rounded-circle me-3 border border-2 border-white shadow-sm" style="width: 52px; height: 52px; object-fit: cover;">
                            <?php else: ?>
                                <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold me-3" style="width: 52px; height: 52px; font-size: 1.3rem;"><?php echo $initials; ?></div>
                            <?php endif; ?>
                            <div>
                                <h6 class="fw-bold mb-0 text-dark"><?php echo htmlspecialchars($t['name']); ?></h6>
                                <?php if (!empty($t['class_name'])): ?>
                                    <small class="text-muted"><i class="fas fa-graduation-cap me-1" style="font-size: 0.7rem;"></i><?php echo htmlspecialchars($t['class_name']); ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="mb-3">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fas fa-star <?php echo $i <= $rating ? 'text-warning' : 'text-muted'; ?>" style="font-size: 0.85rem;"></i>
                            <?php endfor; ?>
                        </div>
                        <p class="text-secondary mb-0" style="line-height: 1.7;">&ldquo;<?php echo htmlspecialchars($t['review']); ?>&rdquo;</p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="text-center py-5" data-aos="fade-up">
                <i class="fas fa-comment-dots fs-1 text-muted opacity-50 mb-3"></i>
                <p class="text-muted fs-5 mb-0">Belum ada testimoni. Jadilah yang pertama memberikan ulasan!</p>
            </div>
            <?php endif; ?>

            <div class="text-center mt-5" data-aos="fade-up">
                <a href="pages/testimoni.php" class="btn btn-primary btn-premium px-5 py-3 rounded-pill fw-bold shadow-sm" style="background: linear-gradient(135deg, #0c4a6e, #0ea5e9); border: none;">
                    <i class="fas fa-pen me-2"></i>Tulis Testimoni
                </a>
            </div>
        </div>
    </section>