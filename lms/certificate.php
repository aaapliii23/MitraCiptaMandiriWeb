<?php
require_once '../includes/auth_user.php';
require_once '../config/database.php';

$userId = (int)$_SESSION['user_id'];
$classId = (int)($_GET['class_id'] ?? 0);
$orientation = in_array($_GET['orientation'] ?? '', ['portrait', 'landscape']) ? $_GET['orientation'] : 'landscape';

if (!$classId) {
    header('Location: dashboard.php');
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM enrollments WHERE user_id = ? AND class_id = ? LIMIT 1");
    $stmt->execute([$userId, $classId]);
    $enrollment = $stmt->fetch();

    if (!$enrollment) {
        http_response_code(403);
        $blocked = true;
        $certData = null;
    } else {
        $blocked = false;
        $stmt = $pdo->prepare("SELECT c.*, COUNT(m.id) AS total_materials FROM classes c LEFT JOIN materials m ON m.class_id = c.id WHERE c.id = ? GROUP BY c.id");
        $stmt->execute([$classId]);
        $class = $stmt->fetch();

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM material_progress mp JOIN materials m ON mp.material_id = m.id WHERE mp.user_id = ? AND m.class_id = ? AND mp.completed = 1");
        $stmt->execute([$userId, $classId]);
        $doneMaterials = (int)$stmt->fetchColumn();

        $totalMaterials = (int)($class['total_materials'] ?? 0);
        $eligible = $totalMaterials > 0 && $doneMaterials >= $totalMaterials;

        $certData = null;
        if ($eligible) {
            // Ensure verify_token column exists (auto-migrate if missing) + backfill before UNIQUE
            try {
                $pdo->exec("ALTER TABLE certificates ADD COLUMN verify_token VARCHAR(64) NOT NULL DEFAULT '' AFTER cert_number");
            } catch (PDOException $e) {}
            try {
                $pdo->exec("UPDATE certificates SET verify_token = LEFT(SHA2(CONCAT(UUID(), RAND(), id), 256), 40) WHERE verify_token = '' OR verify_token IS NULL");
            } catch (PDOException $e) {}
            try {
                $pdo->exec("ALTER TABLE certificates ADD UNIQUE KEY uq_cert_verify_token (verify_token)");
            } catch (PDOException $e) {}

            $stmt = $pdo->prepare("SELECT * FROM certificates WHERE user_id = ? AND class_id = ? LIMIT 1");
            $stmt->execute([$userId, $classId]);
            $certData = $stmt->fetch();

            if (!$certData) {
                $year = date('Y');
                for ($i = 0; $i < 10; $i++) {
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM certificates WHERE cert_number LIKE ?");
                    $stmt->execute(["MCM-$year-%"]);
                    $seq = (int)$stmt->fetchColumn() + 1;
                    $certNumber = sprintf("MCM-%s-%04d", $year, $seq);
                    $verifyToken = bin2hex(random_bytes(20));
                    try {
                        $stmt = $pdo->prepare("INSERT IGNORE INTO certificates (user_id, class_id, cert_number, verify_token) VALUES (?, ?, ?, ?)");
                        $stmt->execute([$userId, $classId, $certNumber, $verifyToken]);
                    } catch (PDOException $e) {
                        // Fallback if column not yet migrated (col missing)
                        $stmt = $pdo->prepare("INSERT IGNORE INTO certificates (user_id, class_id, cert_number) VALUES (?, ?, ?)");
                        $stmt->execute([$userId, $classId, $certNumber]);
                    }
                    if ($stmt->rowCount() > 0) {
                        $certData = ['cert_number' => $certNumber, 'verify_token' => $verifyToken, 'issued_at' => date('Y-m-d H:i:s')];
                        break;
                    }
                }
                if (!$certData) {
                    $stmt = $pdo->prepare("SELECT * FROM certificates WHERE user_id = ? AND class_id = ? LIMIT 1");
                    $stmt->execute([$userId, $classId]);
                    $certData = $stmt->fetch();
                }
            }
            // Fallback for legacy certificates without token
            if ($certData && empty($certData['verify_token'])) {
                $verifyToken = bin2hex(random_bytes(20));
                try {
                    $upd = $pdo->prepare("UPDATE certificates SET verify_token = ? WHERE user_id = ? AND class_id = ? AND (verify_token = '' OR verify_token IS NULL)");
                    $upd->execute([$verifyToken, $userId, $classId]);
                    $certData['verify_token'] = $verifyToken;
                } catch (PDOException $e) {}
            }
        }

        // Absolute verify URL base for QR (scheme://host + app prefix)
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
        $appBase = '';
        if ($scriptDir !== '/' && $scriptDir !== '.' && $scriptDir !== '') {
            $appBase = rtrim(str_replace('\\', '/', dirname($scriptDir)), '/');
            if ($appBase === '/' || $appBase === '.' || $appBase === '\\') $appBase = '';
        }
        $verifyBaseUrl = $scheme . '://' . $host . ($appBase ? $appBase : '');
        $verifyUrl = '';
        if (!empty($certData['verify_token'])) {
            $verifyUrl = $verifyBaseUrl . '/pages/verify_certificate.php?token=' . urlencode($certData['verify_token']);
        }

        // Fetch User
        $stmt = $pdo->prepare("SELECT name, email FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();

        // Fetch Materials (Units of Competency)
        $stmt = $pdo->prepare("SELECT * FROM materials WHERE class_id = ? ORDER BY sort_order ASC, id ASC");
        $stmt->execute([$classId]);
        $materials = $stmt->fetchAll();

        // Fetch Instructor info if available
        $instructor = null;
        $stmt = $pdo->prepare("SELECT ins.* FROM orders o JOIN instructors ins ON o.instructor_id = ins.id WHERE o.user_id = ? AND o.class_id = ? LIMIT 1");
        $stmt->execute([$userId, $classId]);
        $instructor = $stmt->fetch();

        // Fetch Template
        $template = ['layout' => 'default', 'accent_color' => '#0c4a6e', 'bg_image' => null];
        try {
            $stmt = $pdo->prepare("SELECT * FROM certificate_templates WHERE class_id = ? OR (class_id IS NULL AND is_default = 1) ORDER BY (class_id IS NOT NULL) DESC, is_default DESC LIMIT 1");
            $stmt->execute([$classId]);
            $tmpl = $stmt->fetch();
            if ($tmpl) {
                $template = ['layout' => $tmpl['layout'], 'accent_color' => $tmpl['accent_color'] ?: '#0c4a6e', 'bg_image' => $tmpl['bg_image']];
            }
        } catch (PDOException $e) {}
    }
} catch (PDOException $e) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sertifikat Kelulusan - <?php echo htmlspecialchars($user['name'] ?? 'MCM'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <?php include __DIR__ . '/partials/certificate_styles.php'; ?>
</head>
<body>

    <?php if ($blocked || !$certData): ?>
        <section class="d-flex align-items-center justify-content-center" style="min-height: 100vh;">
            <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-white" style="max-width: 500px;">
                <i class="fas fa-lock fs-1 text-danger opacity-50 mb-3"></i>
                <h4 class="fw-bold text-dark">Sertifikat Belum Tersedia</h4>
                <p class="text-muted small mb-4">Selesaikan seluruh materi dan evaluasi kelas terlebih dahulu untuk mengklaim sertifikat resmi Anda.</p>
                <div class="d-flex justify-content-center gap-2">
                    <a href="course.php?class_id=<?php echo (int)$classId; ?>" class="btn btn-primary rounded-pill fw-bold px-4">Kembali ke Kelas</a>
                    <a href="dashboard.php" class="btn btn-light border rounded-pill fw-bold px-4">Dashboard</a>
                </div>
            </div>
        </section>
    <?php else: ?>
        <!-- Top Toolbar Control (No Print) -->
        <div class="bg-white shadow-sm py-3 px-4 border-bottom no-print sticky-top">
            <div class="container d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div class="d-flex align-items-center gap-2">
                    <a href="course.php?class_id=<?php echo (int)$classId; ?>" class="btn btn-outline-secondary rounded-pill btn-sm px-3 fw-bold">
                        <i class="fas fa-arrow-left me-1"></i> Kembali ke Kelas
                    </a>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="small fw-bold text-muted me-1"><i class="fas fa-sliders-h me-1"></i> Orientasi Cetak:</span>
                    <div class="btn-group btn-group-sm rounded-pill p-1 bg-light border" role="group">
                        <button type="button" class="btn btn-orientation rounded-pill px-3 fw-bold <?php echo $orientation === 'landscape' ? 'btn-primary active' : 'btn-light'; ?>" data-mode="landscape" onclick="setOrientation('landscape')">
                            <i class="fas fa-image me-1"></i> Landscape (Default)
                        </button>
                        <button type="button" class="btn btn-orientation rounded-pill px-3 fw-bold <?php echo $orientation === 'portrait' ? 'btn-primary active' : 'btn-light'; ?>" data-mode="portrait" onclick="setOrientation('portrait')">
                            <i class="fas fa-portrait me-1"></i> Portrait (A4 Full)
                        </button>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-warning rounded-pill px-3 py-1 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#certTestimonialModal" style="font-size: 0.85rem;">
                        <i class="fas fa-star me-1"></i> Tulis Testimoni
                    </button>
                    <button onclick="window.print()" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" style="background: linear-gradient(135deg, #0c4a6e, #0ea5e9); border: none;">
                        <i class="fas fa-print me-2"></i> Cetak Bolak-Balik (2 Halaman)
                    </button>
                </div>
            </div>
        </div>

        <?php if (isset($_GET['reviewed'])): ?>
            <div class="container mt-3 no-print" style="max-width: 980px;">
                <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm mb-0" role="alert">
                    <i class="fas fa-check-circle me-2"></i><strong>Terima kasih!</strong> Ulasan testimoni kelulusan Anda berhasil dikirim dan akan tampil setelah disetujui admin.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        <?php endif; ?>

        <div class="cert-wrapper orientation-<?php echo $orientation; ?>" id="certWrapper">
            <div class="cert-container">
                <?php include __DIR__ . '/partials/certificate_front.php'; ?>
                <?php include __DIR__ . '/partials/certificate_back.php'; ?>
            </div>
        </div>

        <!-- Modal Tulis Testimoni Kelulusan -->
        <div class="modal fade no-print" id="certTestimonialModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow" style="border-radius: 1.25rem;">
                    <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                        <h5 class="modal-title fw-bold text-dark"><i class="fas fa-award text-warning me-2"></i>Testimoni Kelulusan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <form method="POST" action="profile.php" enctype="multipart/form-data">
                        <div class="modal-body p-4">
                            <input type="hidden" name="form_type" value="testimonial">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                            <input type="hidden" name="class_id" value="<?php echo (int)$classId; ?>">
                            
                            <div class="alert alert-info py-2 small rounded-3 mb-3">
                                <i class="fas fa-info-circle me-1"></i> Memberikan ulasan untuk program: <strong><?php echo htmlspecialchars($class['name']); ?></strong>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold">Rating Pelatihan</label>
                                <div class="d-flex gap-2">
                                    <?php for ($i = 5; $i >= 1; $i--): ?>
                                        <div class="text-center">
                                            <input type="radio" class="btn-check" name="rating" id="modalRating<?php echo $i; ?>" value="<?php echo $i; ?>" <?php echo $i === 5 ? 'checked' : ''; ?>>
                                            <label class="btn btn-outline-warning btn-sm rounded-pill px-3" for="modalRating<?php echo $i; ?>"><?php echo $i; ?> <i class="fas fa-star text-warning"></i></label>
                                        </div>
                                    <?php endfor; ?>
                                </div>
                            </div>

                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label class="form-label small fw-bold">Tahun Kelulusan</label>
                                    <input type="text" class="form-control form-control-sm" name="graduation_year" value="<?php echo date('Y'); ?>">
                                </div>
                                <div class="col-6">
                                    <label class="form-label small fw-bold">Pekerjaan / Profesi</label>
                                    <input type="text" class="form-control form-control-sm" name="job" placeholder="Contoh: Praktisi MUA">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold">Ulasan Anda *</label>
                                <textarea class="form-control" name="review" rows="3" required placeholder="Ceritakan pengalaman belajar Anda di MCM..."></textarea>
                            </div>

                            <div class="mb-2">
                                <label class="form-label small fw-bold">Foto Profil <span class="text-muted fw-normal">(opsional)</span></label>
                                <input type="file" class="form-control form-control-sm" name="photo" accept=".jpg,.jpeg,.png,.webp">
                            </div>
                        </div>
                        <div class="modal-footer border-top-0 px-4 pb-4">
                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold shadow-sm">Kirim Testimoni</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function setOrientation(mode) {
            const wrapper = document.getElementById('certWrapper');
            wrapper.className = 'cert-wrapper orientation-' + mode;
            document.getElementById('printPageSize').innerHTML = '@page { size: ' + mode + '; margin: 0; }';
            
            document.querySelectorAll('.btn-orientation').forEach(btn => {
                const isActive = btn.dataset.mode === mode;
                btn.classList.toggle('active', isActive);
                btn.classList.toggle('btn-primary', isActive);
                btn.classList.toggle('btn-light', !isActive);
            });
        }
    </script>
</body>
</html>