    <?php
    $instructorsByCat = [];
    try {
        $insStmt = $pdo->query("SELECT id, name, category, specialization FROM instructors ORDER BY name ASC");
        $allIns = $insStmt->fetchAll();
        foreach ($allIns as $insItem) {
            $catKey = strtolower(trim($insItem['category']));
            if (!isset($instructorsByCat[$catKey])) $instructorsByCat[$catKey] = [];
            $instructorsByCat[$catKey][] = $insItem;
        }
    } catch (PDOException $e) {}
    ?>
    <script>
        window.mcmInstructorsByCat = <?php echo json_encode($instructorsByCat); ?>;
    </script>

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
                            <h5 class="fw-bold mb-3">Ringkasan Pendaftaran</h5>
                            
                            <div class="d-flex align-items-center mb-3 bg-white bg-opacity-10 p-2 rounded-3">
                                <div class="bg-white bg-opacity-25 p-2 rounded-circle me-3">
                                    <i class="fas fa-book-open text-white fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0 text-white checkoutClassName" id="checkoutClassName" style="font-size: 0.9rem;">Nama Kelas</h6>
                                    <div class="text-white-50 small" id="checkoutLearningTypeLabel" style="font-size: 0.75rem;">Kelas Offline (Tatap Muka)</div>
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

                            <?php if (!empty($_SESSION['user_logged_in']) && !empty($_SESSION['user_id'])): ?>
                                <?php
                                $checkoutUser = [];
                                try {
                                    $stmt = $pdo->prepare("SELECT name, email, phone FROM users WHERE id = ? LIMIT 1");
                                    $stmt->execute([(int)$_SESSION['user_id']]);
                                    $checkoutUser = $stmt->fetch() ?: [];
                                } catch (PDOException $e) {}
                                ?>
                                <!-- ===== LOGGED IN: Simplified ===== -->
                                <div class="mb-3 text-center text-md-start">
                                    <h5 class="fw-bold" style="color: #0F172A; margin-bottom: 2px;">Konfirmasi Pendaftaran</h5>
                                    <p class="text-muted small mb-0" style="font-size: 0.75rem;">Data diri terisi otomatis dari akun Anda.</p>
                                </div>

                                <!-- User Info Card -->
                                <div class="bg-primary bg-opacity-5 border border-primary border-opacity-10 rounded-4 p-3 mb-3">
                                    <div class="d-flex align-items-center mb-1">
                                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 34px; height: 34px; min-width: 34px;">
                                            <i class="fas fa-user-check" style="font-size: 0.85rem;"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark small"><?php echo htmlspecialchars($checkoutUser['name'] ?? $_SESSION['user_name'] ?? ''); ?></div>
                                            <div class="text-muted" style="font-size: 0.72rem;"><?php echo htmlspecialchars($checkoutUser['email'] ?? $_SESSION['user_email'] ?? ''); ?></div>
                                        </div>
                                    </div>
                                    <div class="text-muted small d-flex align-items-center">
                                        <i class="fas fa-shield-alt text-success me-1" style="font-size: 0.7rem;"></i>
                                        <span style="font-size: 0.7rem;">Data diri sudah terverifikasi dari akun Anda.</span>
                                    </div>
                                </div>

                                <form id="checkoutForm" action="payment/create_transaction.php" method="POST" onsubmit="return submitPayment(this);">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                                    <input type="hidden" name="customer_name"    value="<?php echo htmlspecialchars($checkoutUser['name']  ?? ''); ?>">
                                    <input type="hidden" name="customer_email"   value="<?php echo htmlspecialchars($checkoutUser['email'] ?? ''); ?>">
                                    <input type="hidden" name="customer_phone"   value="<?php echo htmlspecialchars($checkoutUser['phone'] ?? ''); ?>">
                                    <input type="hidden" name="class_id" class="checkoutClassId" id="checkoutClassId">

                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-secondary mb-1" style="font-size: 0.7rem;">Alamat Lengkap *</label>
                                        <textarea class="form-control form-control-sm bg-light" name="customer_address" rows="2" required placeholder="Jl. Sudirman No. 123..."></textarea>
                                    </div>

                                    <!-- Instructor dropdown populated by JS -->
                                    <div class="mb-3" id="checkoutInstructorWrap" style="display:none;">
                                        <label class="form-label small fw-bold text-secondary mb-1" style="font-size: 0.7rem;">Pilih Asesor / Instruktur <span class="text-muted fw-normal">(opsional)</span></label>
                                        <select name="instructor_id" id="checkoutInstructor" class="form-select form-select-sm bg-light border-0 rounded-3">
                                            <option value="0">-- Tidak dipilih --</option>
                                        </select>
                                    </div>

                                    <!-- Learning Type Selector -->
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-secondary mb-1" style="font-size: 0.72rem;">Pilih Metode Pembelajaran *</label>
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <label class="learning-type-option d-block p-2 rounded-3 border text-start position-relative h-100" for="learnOfflineLoggedIn" id="cardLearnOffline" style="cursor: pointer; transition: all 0.2s ease;">
                                                    <div class="form-check m-0 p-0 d-flex align-items-center">
                                                        <input class="form-check-input me-2 mt-0" type="radio" name="learning_type" id="learnOfflineLoggedIn" value="offline" checked onchange="updateCheckoutLearningType('offline')">
                                                        <div>
                                                            <div class="fw-bold text-dark small" style="font-size: 0.78rem;"><i class="fas fa-chalkboard-teacher text-primary me-1"></i>Offline</div>
                                                            <div class="text-muted" style="font-size: 0.65rem; line-height: 1.1;">Tatap Muka & Praktek</div>
                                                            <div class="fw-bold text-primary small mt-1 checkoutPriceOfflineBadge">Rp 0</div>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                            <div class="col-6">
                                                <label class="learning-type-option d-block p-2 rounded-3 border text-start position-relative h-100" for="learnOnlineLoggedIn" id="cardLearnOnline" style="cursor: pointer; transition: all 0.2s ease;">
                                                    <div class="form-check m-0 p-0 d-flex align-items-center">
                                                        <input class="form-check-input me-2 mt-0" type="radio" name="learning_type" id="learnOnlineLoggedIn" value="online" onchange="updateCheckoutLearningType('online')">
                                                        <div>
                                                            <div class="fw-bold text-dark small" style="font-size: 0.78rem;"><i class="fas fa-laptop text-info me-1"></i>Online</div>
                                                            <div class="text-muted" style="font-size: 0.65rem; line-height: 1.1;">LMS & Video Modul</div>
                                                            <div class="fw-bold text-info small mt-1 checkoutPriceOnlineBadge">Rp 0</div>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary w-100 py-2 rounded-pill fw-bold shadow-sm" style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); border: none; font-size: 0.9rem;">
                                        <i class="fas fa-credit-card me-2"></i>Daftar &amp; Bayar
                                    </button>
                                </form>

                            <?php else: ?>
                                <!-- ===== GUEST: Full Form ===== -->
                                <div class="mb-3 text-center text-md-start">
                                    <h5 class="fw-bold" style="color: #0F172A; margin-bottom: 2px;">Data Diri Peserta</h5>
                                    <p class="text-muted small mb-0" style="font-size: 0.75rem;">Lengkapi data Anda untuk pendaftaran.</p>
                                </div>

                                <form id="checkoutForm" action="payment/create_transaction.php" method="POST" onsubmit="return submitPayment(this);">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                                    <input type="hidden" name="class_id" class="checkoutClassId" id="checkoutClassId">
                                    
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

                                        <!-- Instructor dropdown populated by JS -->
                                        <div class="col-md-12" id="checkoutInstructorWrap" style="display:none;">
                                            <label class="form-label small fw-bold text-secondary mb-1" style="font-size: 0.7rem;">Pilih Asesor / Instruktur <span class="text-muted fw-normal">(opsional)</span></label>
                                            <select name="instructor_id" id="checkoutInstructor" class="form-select form-select-sm bg-light border-0 rounded-3">
                                                <option value="0">-- Tidak dipilih --</option>
                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold text-secondary mb-1" style="font-size: 0.7rem;">Password Akun LMS *</label>
                                            <input type="password" class="form-control form-control-sm bg-light" name="customer_password" placeholder="Minimal 6 karakter" minlength="6" autocomplete="new-password">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold text-secondary mb-1" style="font-size: 0.7rem;">Konfirmasi Password *</label>
                                            <input type="password" class="form-control form-control-sm bg-light" name="customer_password2" placeholder="Ulangi password" minlength="6" autocomplete="new-password">
                                        </div>
                                    </div>
                                    
                                    <!-- Learning Type Selector -->
                                    <div class="mt-3 mb-2">
                                        <label class="form-label small fw-bold text-secondary mb-1" style="font-size: 0.72rem;">Pilih Metode Pembelajaran *</label>
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <label class="learning-type-option d-block p-2 rounded-3 border text-start position-relative h-100" for="learnOfflineGuest" id="cardLearnOfflineGuest" style="cursor: pointer; transition: all 0.2s ease;">
                                                    <div class="form-check m-0 p-0 d-flex align-items-center">
                                                        <input class="form-check-input me-2 mt-0" type="radio" name="learning_type" id="learnOfflineGuest" value="offline" checked onchange="updateCheckoutLearningType('offline')">
                                                        <div>
                                                            <div class="fw-bold text-dark small" style="font-size: 0.78rem;"><i class="fas fa-chalkboard-teacher text-primary me-1"></i>Offline</div>
                                                            <div class="text-muted" style="font-size: 0.65rem; line-height: 1.1;">Tatap Muka & Praktek</div>
                                                            <div class="fw-bold text-primary small mt-1 checkoutPriceOfflineBadge">Rp 0</div>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                            <div class="col-6">
                                                <label class="learning-type-option d-block p-2 rounded-3 border text-start position-relative h-100" for="learnOnlineGuest" id="cardLearnOnlineGuest" style="cursor: pointer; transition: all 0.2s ease;">
                                                    <div class="form-check m-0 p-0 d-flex align-items-center">
                                                        <input class="form-check-input me-2 mt-0" type="radio" name="learning_type" id="learnOnlineGuest" value="online" onchange="updateCheckoutLearningType('online')">
                                                        <div>
                                                            <div class="fw-bold text-dark small" style="font-size: 0.78rem;"><i class="fas fa-laptop text-info me-1"></i>Online</div>
                                                            <div class="text-muted" style="font-size: 0.65rem; line-height: 1.1;">LMS & Video Modul</div>
                                                            <div class="fw-bold text-info small mt-1 checkoutPriceOnlineBadge">Rp 0</div>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="alert alert-info border-0 d-flex align-items-center my-2 p-2" style="border-radius: 0.75rem;">
                                        <i class="fas fa-info-circle fs-5 me-2"></i>
                                        <small style="font-size: 0.68rem; line-height: 1.2;">Akun LMS dibuat otomatis dari data ini. Kosongkan password bila email Anda sudah terdaftar.</small>
                                    </div>

                                    <button type="submit" class="btn btn-primary w-100 py-2 rounded-pill fw-bold shadow-sm mt-2" style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); border: none; font-size: 0.9rem;">
                                        <i class="fas fa-credit-card me-2"></i>Daftar &amp; Bayar
                                    </button>
                                </form>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    let currentSelectedCheckoutClass = null;

    function populateCheckoutModal(classData) {
        currentSelectedCheckoutClass = classData;
        const offPrice = classData.price_offline && parseInt(classData.price_offline) > 0 ? parseInt(classData.price_offline) : parseInt(classData.price || 0);
        const onPrice = classData.price_online && parseInt(classData.price_online) > 0 ? parseInt(classData.price_online) : Math.round(offPrice * 0.75);
        
        document.querySelectorAll('.checkoutClassId').forEach(el => el.value = classData.id);
        document.querySelectorAll('.checkoutClassName').forEach(el => el.textContent = classData.name);
        
        document.querySelectorAll('.checkoutPriceOfflineBadge').forEach(el => {
            el.textContent = 'Rp ' + offPrice.toLocaleString('id-ID');
        });
        document.querySelectorAll('.checkoutPriceOnlineBadge').forEach(el => {
            el.textContent = 'Rp ' + onPrice.toLocaleString('id-ID');
        });

        // Populate Instructor Options if category matches
        const catKey = (classData.category || '').toLowerCase().trim();
        const insList = window.mcmInstructorsByCat ? (window.mcmInstructorsByCat[catKey] || []) : [];
        const insWraps = document.querySelectorAll('#checkoutInstructorWrap');
        const insSelects = document.querySelectorAll('#checkoutInstructor');

        insSelects.forEach(selectEl => {
            selectEl.innerHTML = '<option value="0">-- Tidak dipilih --</option>';
            if (insList.length > 0) {
                insList.forEach(ins => {
                    const opt = document.createElement('option');
                    opt.value = ins.id;
                    opt.textContent = ins.name + ' — ' + ins.specialization;
                    selectEl.appendChild(opt);
                });
            }
        });

        insWraps.forEach(wrap => {
            wrap.style.display = (insList.length > 0) ? 'block' : 'none';
        });

        updateCheckoutLearningType('offline');
    }

    function updateCheckoutLearningType(type) {
        if (!currentSelectedCheckoutClass) return;
        const offPrice = currentSelectedCheckoutClass.price_offline && parseInt(currentSelectedCheckoutClass.price_offline) > 0 ? parseInt(currentSelectedCheckoutClass.price_offline) : parseInt(currentSelectedCheckoutClass.price || 0);
        const onPrice = currentSelectedCheckoutClass.price_online && parseInt(currentSelectedCheckoutClass.price_online) > 0 ? parseInt(currentSelectedCheckoutClass.price_online) : Math.round(offPrice * 0.75);
        const selectedPrice = (type === 'online') ? onPrice : offPrice;
        const formatted = 'Rp ' + selectedPrice.toLocaleString('id-ID');

        document.querySelectorAll('input[name="learning_type"]').forEach(r => {
            r.checked = (r.value === type);
        });

        const cards = [
            { off: document.getElementById('cardLearnOffline'), on: document.getElementById('cardLearnOnline') },
            { off: document.getElementById('cardLearnOfflineGuest'), on: document.getElementById('cardLearnOnlineGuest') }
        ];

        cards.forEach(c => {
            if (c.off && c.on) {
                if (type === 'offline') {
                    c.off.classList.add('border-primary', 'bg-primary', 'bg-opacity-10');
                    c.off.classList.remove('border-light');
                    c.on.classList.remove('border-info', 'bg-info', 'bg-opacity-10');
                    c.on.classList.add('border-light');
                } else {
                    c.on.classList.add('border-info', 'bg-info', 'bg-opacity-10');
                    c.on.classList.remove('border-light');
                    c.off.classList.remove('border-primary', 'bg-primary', 'bg-opacity-10');
                    c.off.classList.add('border-light');
                }
            }
        });

        const priceEl = document.getElementById('checkoutClassPrice');
        const totalEl = document.getElementById('checkoutTotalPrice');
        const typeLabelEl = document.getElementById('checkoutLearningTypeLabel');
        if (priceEl) priceEl.textContent = formatted;
        if (totalEl) totalEl.textContent = formatted;
        if (typeLabelEl) typeLabelEl.textContent = (type === 'online') ? 'Kelas Online (LMS & Video)' : 'Kelas Offline (Tatap Muka)';
    }
    </script>

