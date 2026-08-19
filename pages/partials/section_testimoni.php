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
                    <div class="card border-0 shadow-sm rounded-4 h-100 p-4 testimonial-card" role="button" data-bs-toggle="modal" data-bs-target="#alumniModal"
                         data-name="<?php echo htmlspecialchars($t['name']); ?>"
                         data-image="<?php echo htmlspecialchars($t['image'] ?? ''); ?>"
                         data-rating="<?php echo $rating; ?>"
                         data-review="<?php echo htmlspecialchars($t['review']); ?>"
                         data-program="<?php echo htmlspecialchars($t['class_name'] ?? ''); ?>"
                         data-year="<?php echo htmlspecialchars($t['graduation_year'] ?? ''); ?>"
                         data-job="<?php echo htmlspecialchars($t['job'] ?? ''); ?>">
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
                <p class="text-muted fs-5 mb-0">Belum ada testimoni alumni yang ditampilkan.</p>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Alumni Detail Modal -->
    <div class="modal fade" id="alumniModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 1.25rem;">
                <div class="modal-body p-4 text-center">
                    <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    <div id="alumniAvatar" class="mx-auto mb-3 rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold" style="width: 72px; height: 72px; font-size: 1.6rem;">A</div>
                    <h5 class="fw-bold text-dark mb-1" id="alumniName"></h5>
                    <div class="mb-2" id="alumniStars"></div>
                    <p class="text-secondary mb-3" id="alumniReview" style="line-height: 1.7;"></p>
                    <div class="row g-2">
                        <div class="col-4">
                            <div class="bg-light rounded-3 py-2 px-1">
                                <small class="text-muted d-block">Program</small>
                                <span class="fw-bold small text-dark" id="alumniProgram">-</span>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="bg-light rounded-3 py-2 px-1">
                                <small class="text-muted d-block">Angkatan</small>
                                <span class="fw-bold small text-dark" id="alumniYear">-</span>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="bg-light rounded-3 py-2 px-1">
                                <small class="text-muted d-block">Pekerjaan</small>
                                <span class="fw-bold small text-dark" id="alumniJob">-</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.testimonial-card').forEach(function (card) {
            card.addEventListener('click', function () {
                document.getElementById('alumniName').textContent = card.dataset.name;
                document.getElementById('alumniReview').textContent = '\u201C' + card.dataset.review + '\u201D';
                document.getElementById('alumniProgram').textContent = card.dataset.program || '-';
                document.getElementById('alumniYear').textContent = card.dataset.year || '-';
                document.getElementById('alumniJob').textContent = card.dataset.job || '-';
                var stars = '';
                for (var i = 1; i <= 5; i++) {
                    stars += '<i class="fas fa-star ' + (i <= parseInt(card.dataset.rating) ? 'text-warning' : 'text-muted') + '" style="font-size: 0.9rem;"></i>';
                }
                document.getElementById('alumniStars').innerHTML = stars;
                var avatar = document.getElementById('alumniAvatar');
                if (card.dataset.image) {
                    avatar.innerHTML = '<img src="' + card.dataset.image + '" alt="' + card.dataset.name + '" class="rounded-circle border border-2 border-white shadow-sm" style="width: 72px; height: 72px; object-fit: cover;">';
                } else {
                    avatar.textContent = card.dataset.name.charAt(0).toUpperCase();
                }
            });
        });
    </script>
