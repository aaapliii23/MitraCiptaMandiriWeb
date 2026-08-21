<?php
require_once '../config/database.php';

// Get class ID from URL
$class_id = $_GET['id'] ?? null;
if (!$class_id) {
    header("Location: programs.php");
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM classes WHERE id = ?");
    $stmt->execute([$class_id]);
    $class = $stmt->fetch();
    
    if (!$class) {
        header("Location: programs.php");
        exit;
    }
    
    $featuresArr = json_decode($class['features'], true) ?: [];
} catch(PDOException $e) {
    header("Location: programs.php");
    exit;
}

$hide_nav_items = true;
include '../includes/header.php';

$instructorId = (int)($_GET['instructor'] ?? 0);
$selectedInstructor = null;
$instructorOptions = [];
try {
    $cat = trim($class['category'] ?? '');
    if (!empty($cat)) {
        $stmt = $pdo->prepare("SELECT * FROM instructors WHERE LOWER(TRIM(category)) = LOWER(TRIM(?)) ORDER BY name ASC");
        $stmt->execute([$cat]);
        $instructorOptions = $stmt->fetchAll();
    }
    foreach ($instructorOptions as $ins) {
        if ((int)$ins['id'] === $instructorId) $selectedInstructor = $ins;
    }
} catch (PDOException $e) {}
?>

<?php
// Determine back URL based on origin
$from = $_GET['from'] ?? 'landing';
$back_url = ($from === 'programs') ? 'programs.php' : '../index.php#paket';
?>

<!-- Class Detail Page -->
<section class="section-padding bg-white animate__animated animate__fadeIn" style="padding-top: 50px;">
    <div class="container">
        <!-- Circular Back Button -->
        <div class="mb-4 text-start" style="margin-left: -5px;">
            <a href="<?php echo $back_url; ?>" class="btn rounded-circle d-inline-flex align-items-center justify-content-center shadow-premium btn-premium" style="width: 50px; height: 50px; background: linear-gradient(135deg, #0c4a6e, #0ea5e9); color: white; border: none; transition: all 0.3s ease;">
                <i class="fas fa-arrow-left fs-5"></i>
            </a>
        </div>

        <div class="text-start mb-5">
            <!-- Logo -->
            <div class="mb-4">
                <a class="d-flex align-items-center text-decoration-none" href="../index.php">
                    <img src="../assets/img/logo.png" alt="MCM Logo" style="height: 45px; width: auto;">
                    <div class="ms-2 ps-2 border-start border-2 border-dark d-flex flex-column justify-content-center" style="height: 35px;">
                        <span class="fw-bold text-dark" style="font-size: 0.8rem; letter-spacing: 1px; line-height: 1.1;">MITRA CIPTA</span>
                        <span class="fw-bold text-dark" style="font-size: 0.8rem; letter-spacing: 1px; line-height: 1.1;">MANDIRI</span>
                    </div>
                </a>
            </div>
            <div class="badge mb-3 p-2 px-3 rounded-pill d-inline-block fw-bold" style="background-color: rgba(14, 165, 233, 0.1); color: #0ea5e9;">PROFIL PROGRAM</div>
            <h1 class="display-5 fw-bold mb-3" style="color: #0c4a6e;"><?php echo htmlspecialchars($class['name']); ?></h1>
            <p class="text-secondary fs-5" style="max-width: 800px;">Pelatihan vokasi terintegrasi dengan standar industri nasional untuk membangun kemandirian ekonomi.</p>
        </div>

        <div class="row g-5">
            <!-- Left Side: Information -->
            <div class="col-lg-8">
                <!-- Image Header -->
                <div class="rounded-5 overflow-hidden mb-5 shadow-sm" style="height: 400px;">
                    <img src="<?php echo htmlspecialchars(asset_src($class['image'])); ?>" alt="<?php echo htmlspecialchars($class['name']); ?>" class="w-100 h-100" style="object-fit: cover;" onerror="this.onerror=null;this.src='../assets/img/logo.png';">
                </div>

                <!-- Content Sections -->
                <div class="mb-5">
                    <h3 class="fw-bold mb-4" style="color: #0c4a6e;">Deskripsi Pelatihan</h3>
                    <p class="text-secondary" style="line-height: 1.8;">
                        <?php echo nl2br(htmlspecialchars($class['description'])); ?>
                    </p>
                </div>

                <div class="mb-5">
                    <h3 class="fw-bold mb-4" style="color: #0c4a6e;">Apa yang Akan Anda Pelajari?</h3>
                    <div class="row g-3">
                        <?php foreach ($featuresArr as $f): ?>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center p-3 rounded-4 bg-light">
                                    <i class="fas fa-check-circle text-success me-3 fs-5"></i>
                                    <span class="fw-bold text-dark" style="font-size: 0.95rem;"><?php echo htmlspecialchars($f); ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="mb-5">
                    <h3 class="fw-bold mb-4" style="color: #0c4a6e;">Syarat & Ketentuan</h3>
                    <div class="bg-warning bg-opacity-10 p-4 rounded-5 border border-warning border-opacity-20 mb-4">
                        <p class="mb-0 small text-dark"><i class="fas fa-exclamation-triangle me-2 text-warning"></i> Mohon baca seluruh ketentuan sebelum melakukan pendaftaran.</p>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item border-0 px-0 py-3 d-flex align-items-start bg-transparent">
                            <div class="bg-primary bg-opacity-10 p-2 rounded-circle me-3 mt-1" style="color: #0c4a6e;"><i class="fas fa-user-check small"></i></div>
                            <div><h6 class="fw-bold mb-1 text-dark">Persyaratan Dasar</h6><p class="small text-muted mb-0">Peserta minimal berusia 17 tahun atau sudah memiliki KTP/Identitas.</p></div>
                        </li>
                        <li class="list-group-item border-0 px-0 py-3 d-flex align-items-start bg-transparent">
                            <div class="bg-primary bg-opacity-10 p-2 rounded-circle me-3 mt-1" style="color: #0c4a6e;"><i class="fas fa-calendar-alt small"></i></div>
                            <div><h6 class="fw-bold mb-1 text-dark">Kehadiran</h6><p class="small text-muted mb-0">Peserta wajib mengikuti minimal 80% dari total pertemuan untuk kelulusan.</p></div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Right Side: Sidebar Registration -->
            <div class="col-lg-4 mt-5 mt-lg-0">
                <div class="sticky-top" style="top: 100px; z-index: 10;">
                    <div class="card border-0 shadow-premium rounded-5 p-4 p-md-5 text-center bg-white border border-info border-opacity-10">
                        <div class="text-center mb-4">
                            <div class="bg-info bg-opacity-10 text-info p-4 rounded-circle d-inline-block" style="width: 80px; height: 80px;">
                                <i class="fas fa-user-edit fs-2"></i>
                            </div>
                        </div>
                        <h3 class="fw-bold mb-2 text-dark text-center">Daftar Sekarang</h3>
                        <p class="text-muted small mb-4 text-center">Amankan kursi Anda sekarang dan mulai perjalanan karir profesional bersama MCM.</p>
                        
                        <div class="text-center mb-4 bg-light p-3 rounded-4 border border-light">
                            <label class="small text-muted d-block mb-1">Investasi Pelatihan</label>
                            <h4 class="fw-bold mb-0" style="color: #0c4a6e;">Rp <?php echo number_format($class['price'], 0, ',', '.'); ?></h4>
                        </div>

                        <?php if ($selectedInstructor): ?>
                            <div class="text-center mb-4 bg-success bg-opacity-10 p-3 rounded-4 border border-success border-opacity-25">
                                <label class="small text-muted d-block mb-1">Instruktur Terpilih</label>
                                <div class="fw-bold text-dark"><?php echo htmlspecialchars($selectedInstructor['name']); ?></div>
                                <small class="text-muted"><?php echo htmlspecialchars($selectedInstructor['specialization']); ?></small>
                            </div>
                        <?php endif; ?>

                        <!-- Integrated Registration Form -->
                        <div id="registrationForm" class="text-start">

                            <?php if (!empty($_SESSION['user_logged_in']) && !empty($_SESSION['user_id'])): ?>
                                <!-- ===== LOGGED IN: Simplified Form ===== -->
                                <?php
                                $loggedUser = [];
                                try {
                                    $stmt = $pdo->prepare("SELECT name, email, phone FROM users WHERE id = ? LIMIT 1");
                                    $stmt->execute([(int)$_SESSION['user_id']]);
                                    $loggedUser = $stmt->fetch() ?: [];
                                } catch (PDOException $e) {}
                                ?>
                                <!-- User Info Card -->
                                <div class="bg-primary bg-opacity-5 border border-primary border-opacity-10 rounded-4 p-3 mb-4">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 38px; height: 38px; min-width: 38px;">
                                            <i class="fas fa-user-check"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark small"><?php echo htmlspecialchars($loggedUser['name'] ?? $_SESSION['user_name'] ?? ''); ?></div>
                                            <div class="text-muted" style="font-size: 0.72rem;"><?php echo htmlspecialchars($loggedUser['email'] ?? $_SESSION['user_email'] ?? ''); ?></div>
                                        </div>
                                    </div>
                                    <div class="text-muted small d-flex align-items-center">
                                        <i class="fas fa-shield-alt text-success me-1"></i>
                                        <span style="font-size: 0.72rem;">Data diri terisi otomatis dari akun Anda.</span>
                                    </div>
                                </div>

                                <form action="../payment/create_transaction.php" method="POST" onsubmit="return submitPayment(this);">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                                    <input type="hidden" name="class_id" value="<?php echo $class['id']; ?>">
                                    <input type="hidden" name="customer_name" value="<?php echo htmlspecialchars($loggedUser['name'] ?? ''); ?>">
                                    <input type="hidden" name="customer_email" value="<?php echo htmlspecialchars($loggedUser['email'] ?? ''); ?>">
                                    <input type="hidden" name="customer_phone" value="<?php echo htmlspecialchars($loggedUser['phone'] ?? ''); ?>">
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-secondary mb-1 d-block">Alamat Lengkap *</label>
                                        <textarea class="form-control bg-light border-0 py-2 rounded-3" name="customer_address" rows="2" required placeholder="Jl. Sudirman No. 123..."></textarea>
                                    </div>

                                    <?php if (!empty($instructorOptions)): ?>
                                    <div class="mb-4">
                                        <label class="form-label small fw-bold text-secondary mb-1 d-block">Pilih Asesor / Instruktur <span class="text-muted fw-normal">(opsional)</span></label>
                                        <select name="instructor_id" id="instructorSelect" class="form-select bg-light border-0 py-2 rounded-3">
                                            <option value="0">-- Tidak dipilih --</option>
                                            <?php foreach ($instructorOptions as $ins): ?>
                                                <option value="<?php echo $ins['id']; ?>" <?php echo $selectedInstructor && (int)$selectedInstructor['id'] === (int)$ins['id'] ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($ins['name'] . ' — ' . $ins['specialization']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <?php endif; ?>

                                    <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-sm"
                                            style="background: linear-gradient(135deg, #0c4a6e, #0ea5e9); border: none; font-size: 1rem;">
                                        <i class="fas fa-credit-card me-2"></i>Bayar Sekarang
                                    </button>
                                </form>

                            <?php else: ?>
                                <!-- ===== GUEST: Full Form ===== -->
                                <h5 class="fw-bold mb-3 text-dark">Data Diri Peserta</h5>
                                <form action="../payment/create_transaction.php" method="POST" onsubmit="return submitPayment(this);">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-secondary mb-1 d-block">Nama Lengkap *</label>
                                        <input type="text" class="form-control bg-light border-0 py-2 rounded-3" name="customer_name" required placeholder="Budi Santoso">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-secondary mb-1 d-block">No. WhatsApp *</label>
                                        <input type="text" class="form-control bg-light border-0 py-2 rounded-3" name="customer_phone" required placeholder="0812...">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-secondary mb-1 d-block">Alamat Email *</label>
                                        <input type="email" class="form-control bg-light border-0 py-2 rounded-3" name="customer_email" required placeholder="email@contoh.com">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-secondary mb-1 d-block">Alamat Lengkap *</label>
                                        <textarea class="form-control bg-light border-0 py-2 rounded-3" name="customer_address" rows="2" required placeholder="Jl. Sudirman No. 123..."></textarea>
                                    </div>

                                    <input type="hidden" name="class_id" value="<?php echo $class['id']; ?>">
                                    <?php if (!empty($instructorOptions)): ?>
                                    <div class="mb-4">
                                        <label class="form-label small fw-bold text-secondary mb-1 d-block">Pilih Asesor / Instruktur <span class="text-muted fw-normal">(opsional)</span></label>
                                        <select name="instructor_id" id="instructorSelect" class="form-select bg-light border-0 py-2 rounded-3">
                                            <option value="0">-- Tidak dipilih --</option>
                                            <?php foreach ($instructorOptions as $ins): ?>
                                                <option value="<?php echo $ins['id']; ?>" <?php echo $selectedInstructor && (int)$selectedInstructor['id'] === (int)$ins['id'] ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($ins['name'] . ' — ' . $ins['specialization']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <?php endif; ?>

                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-secondary mb-1 d-block">Password Akun LMS</label>
                                        <input type="password" class="form-control bg-light border-0 py-2 rounded-3" name="customer_password" placeholder="Min. 6 karakter — kosongkan jika email sudah terdaftar" minlength="6" autocomplete="new-password">
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label small fw-bold text-secondary mb-1 d-block">Konfirmasi Password</label>
                                        <input type="password" class="form-control bg-light border-0 py-2 rounded-3" name="customer_password2" placeholder="Ulangi password" minlength="6" autocomplete="new-password">
                                    </div>

                                    <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-sm"
                                            style="background: linear-gradient(135deg, #0c4a6e, #0ea5e9); border: none; font-size: 1rem;">
                                        <i class="fas fa-credit-card me-2"></i>Bayar Sekarang
                                    </button>
                                </form>
                            <?php endif; ?>

                        </div>
                        
                        <hr class="my-4 opacity-10">
                        
                        <div class="text-center">
                            <h6 class="fw-bold mb-3 small text-uppercase" style="letter-spacing: 1px; color: #0c4a6e;">Konsultasi Gratis</h6>
                            <div class="d-flex align-items-center justify-content-center p-3 rounded-4 bg-light border border-light transition-all hover-lift">
                                <i class="fab fa-whatsapp fs-3 text-success me-3"></i>
                                <div class="text-start">
                                    <p class="small fw-bold mb-0 text-dark">Hubungi Admin</p>
                                    <a href="https://wa.me/6285793935707" target="_blank" class="small text-decoration-none text-primary">Tanya lewat WA</a>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 p-3 rounded-4 border border-dashed text-center">
                            <p class="small text-muted mb-0" style="font-size: 0.75rem;"><i class="fas fa-lock me-2"></i>Data pendaftaran Anda aman & terenkripsi.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
