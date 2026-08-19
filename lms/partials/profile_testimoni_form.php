                    <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5">
                        <div class="text-center mb-4">
                            <div class="rounded-circle bg-warning bg-opacity-10 text-warning d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="fas fa-star fs-2"></i>
                            </div>
                            <h4 class="fw-bold text-dark mb-1">Tulis Testimoni</h4>
                            <p class="text-muted small mb-0">Bagikan pengalaman Anda. Ulasan tampil setelah disetujui admin.</p>
                        </div>

                        <?php if (!empty($testimonialMessage)): ?>
                            <div class="alert <?php echo strpos($testimonialMessage, 'berhasil') !== false ? 'alert-success' : 'alert-danger'; ?> py-2 small"><i class="fas fa-info-circle me-2"></i><?php echo htmlspecialchars($testimonialMessage); ?></div>
                        <?php endif; ?>

                        <form method="POST" action="profile.php" enctype="multipart/form-data">
                            <input type="hidden" name="form_type" value="testimonial">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Rating</label>
                                <div class="d-flex gap-2">
                                    <?php for ($i = 5; $i >= 1; $i--): ?>
                                        <div class="text-center">
                                            <input type="radio" class="btn-check" name="rating" id="trating<?php echo $i; ?>" value="<?php echo $i; ?>" <?php echo $i === 5 ? 'checked' : ''; ?>>
                                            <label class="btn btn-outline-warning btn-sm rounded-pill px-3" for="trating<?php echo $i; ?>"><?php echo $i; ?></label>
                                        </div>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Program yang Diikuti</label>
                                <select class="form-select" name="class_id">
                                    <option value="">-- Pilih Program --</option>
                                    <?php foreach ($enrolledClasses as $c): ?>
                                        <option value="<?php echo (int)$c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Angkatan (Tahun)</label>
                                    <input type="text" class="form-control" name="graduation_year" placeholder="2025">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Pekerjaan Saat Ini</label>
                                    <input type="text" class="form-control" name="job" placeholder="MUA Profesional">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Ulasan</label>
                                <textarea class="form-control" name="review" rows="4" required placeholder="Ceritakan pengalaman Anda mengikuti pelatihan di MCM..."></textarea>
                            </div>
                            <div class="mb-4">
                                <label class="form-label small fw-bold">Foto Profil <span class="text-muted fw-normal">(opsional)</span></label>
                                <input type="file" class="form-control" name="photo" accept=".jpg,.jpeg,.png,.webp">
                                <small class="text-muted">Format: JPG, PNG, WEBP.</small>
                            </div>
                            <button type="submit" class="btn btn-warning w-100 rounded-pill fw-bold py-2"><i class="fas fa-paper-plane me-2"></i>Kirim Testimoni</button>
                        </form>
                    </div>
