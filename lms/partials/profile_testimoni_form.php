                    <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5">
                        <div class="text-center mb-4">
                            <div class="rounded-circle bg-warning bg-opacity-10 text-warning d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="fas fa-star fs-2"></i>
                            </div>
                            <h4 class="fw-bold text-dark mb-1">Tulis Testimoni Kelulusan</h4>
                            <p class="text-muted small mb-0">Ulasan khusus peserta yang telah menyelesaikan program dan memiliki sertifikat resmi.</p>
                        </div>

                        <?php if (!empty($testimonialMessage)): ?>
                            <div class="alert <?php echo strpos($testimonialMessage, 'berhasil') !== false ? 'alert-success' : 'alert-danger'; ?> py-2 small"><i class="fas fa-info-circle me-2"></i><?php echo htmlspecialchars($testimonialMessage); ?></div>
                        <?php endif; ?>

                        <?php if (empty($certifiedClasses)): ?>
                            <div class="text-center p-4 rounded-4 bg-light border">
                                <i class="fas fa-certificate text-muted fs-1 mb-3 opacity-50"></i>
                                <h6 class="fw-bold text-dark">Sertifikat Belum Diterbitkan</h6>
                                <p class="text-muted small mb-3">Fitur penulisan testimoni hanya terbuka bagi peserta yang telah lulus 100% dan memperoleh sertifikat kompetensi.</p>
                                <a href="dashboard.php" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold">Lanjutkan Belajar</a>
                            </div>
                        <?php else: ?>
                            <form method="POST" action="profile.php" enctype="multipart/form-data">
                                <input type="hidden" name="form_type" value="testimonial">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Rating Pelatihan</label>
                                    <div class="d-flex gap-2">
                                        <?php for ($i = 5; $i >= 1; $i--): ?>
                                            <div class="text-center">
                                                <input type="radio" class="btn-check" name="rating" id="trating<?php echo $i; ?>" value="<?php echo $i; ?>" <?php echo $i === 5 ? 'checked' : ''; ?>>
                                                <label class="btn btn-outline-warning btn-sm rounded-pill px-3" for="trating<?php echo $i; ?>"><?php echo $i; ?> <i class="fas fa-star text-warning"></i></label>
                                            </div>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Program Pelatihan Bersertifikat *</label>
                                    <select class="form-select" name="class_id" required>
                                        <option value="">-- Pilih Program yang Telah Lulus --</option>
                                        <?php foreach ($certifiedClasses as $c): ?>
                                            <option value="<?php echo (int)$c['id']; ?>">
                                                <?php echo htmlspecialchars($c['name']); ?> (No. Sertifikat: <?php echo htmlspecialchars($c['cert_number']); ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Tahun Kelulusan / Angkatan</label>
                                        <input type="text" class="form-control" name="graduation_year" placeholder="<?php echo date('Y'); ?>" value="<?php echo date('Y'); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Pekerjaan / Usaha Saat Ini</label>
                                        <input type="text" class="form-control" name="job" placeholder="Contoh: MUA Profesional / Wirausaha">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Ulasan & Pengalaman Belajar *</label>
                                    <textarea class="form-control" name="review" rows="4" required placeholder="Ceritakan bagaimana pelatihan di MCM membantu karir/keahlian Anda..."></textarea>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label small fw-bold">Foto Profil <span class="text-muted fw-normal">(opsional)</span></label>
                                    <input type="file" class="form-control" name="photo" accept=".jpg,.jpeg,.png,.webp">
                                    <small class="text-muted">Format: JPG, PNG, WEBP.</small>
                                </div>
                                <button type="submit" class="btn btn-warning w-100 rounded-pill fw-bold py-2"><i class="fas fa-paper-plane me-2"></i>Kirim Testimoni Alumni</button>
                            </form>
                        <?php endif; ?>
                    </div>
