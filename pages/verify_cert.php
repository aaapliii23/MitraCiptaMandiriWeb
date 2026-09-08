<?php
require_once '../config/database.php';

$certNumber = trim($_GET['cert'] ?? '');
$certData = null;
$user = null;
$class = null;
$instructor = null;
$materials = [];

if (!empty($certNumber)) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM certificates WHERE cert_number = ? LIMIT 1");
        $stmt->execute([$certNumber]);
        $certData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($certData) {
            $stmtUser = $pdo->prepare("SELECT id, name, email FROM users WHERE id = ? LIMIT 1");
            $stmtUser->execute([$certData['user_id']]);
            $user = $stmtUser->fetch(PDO::FETCH_ASSOC);

            $stmtClass = $pdo->prepare("SELECT * FROM classes WHERE id = ? LIMIT 1");
            $stmtClass->execute([$certData['class_id']]);
            $class = $stmtClass->fetch(PDO::FETCH_ASSOC);

            $stmtIns = $pdo->prepare("SELECT ins.* FROM orders o JOIN instructors ins ON o.instructor_id = ins.id WHERE o.user_id = ? AND o.class_id = ? LIMIT 1");
            $stmtIns->execute([$certData['user_id'], $certData['class_id']]);
            $instructor = $stmtIns->fetch(PDO::FETCH_ASSOC);

            $stmtMat = $pdo->prepare("SELECT * FROM materials WHERE class_id = ? ORDER BY sort_order ASC, id ASC");
            $stmtMat->execute([$certData['class_id']]);
            $materials = $stmtMat->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch (PDOException $e) {}
}

$hide_nav_items = true;
include '../includes/header.php';
?>

<section class="py-5" style="background: linear-gradient(180deg, #f8fafc 0%, #e2e8f0 100%); min-height: 90vh;">
    <div class="container py-3">
        <!-- Back Link -->
        <div class="mb-4">
            <a href="../index.php" class="btn btn-outline-secondary rounded-pill btn-sm px-3 fw-bold shadow-sm">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Beranda
            </a>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-7">
                <?php if ($certData && $user && $class): ?>
                    <!-- Valid Certificate Card -->
                    <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white mb-4 animate__animated animate__fadeIn">
                        <!-- Header Status -->
                        <div class="p-4 text-center text-white" style="background: linear-gradient(135deg, #064e3b, #059669);">
                            <div class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle mb-3 shadow" style="width: 76px; height: 76px;">
                                <i class="fas fa-check-circle text-success fs-1"></i>
                            </div>
                            <h3 class="fw-bold mb-1">Sertifikat Resmi &amp; Terverifikasi</h3>
                            <p class="mb-0 small opacity-90">Dokumen kompetensi ini sah dan tercatat dalam pangkalan data resmi LPK Mitra Cipta Mandiri.</p>
                        </div>

                        <div class="card-body p-4 p-md-5">
                            <!-- Certificate Metadata -->
                            <div class="text-center pb-4 border-bottom mb-4">
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle px-3 py-1.5 rounded-pill fw-bold mb-2" style="font-size: 0.85rem;">
                                    <i class="fas fa-shield-alt me-1"></i> VALIDASI RESMI SISTEM MCM
                                </span>
                                <div class="text-muted small">Nomor Registrasi Sertifikat:</div>
                                <div class="fs-4 fw-extrabold text-dark letter-spacing-1 font-monospace mt-1">
                                    <?php echo htmlspecialchars($certData['cert_number']); ?>
                                </div>
                            </div>

                            <!-- Graduate Details -->
                            <div class="row g-3 mb-4">
                                <div class="col-sm-6">
                                    <div class="p-3 bg-light rounded-3 border h-100">
                                        <div class="text-muted small mb-1"><i class="fas fa-user-graduate me-1 text-primary"></i> Nama Penerima</div>
                                        <div class="fw-bold text-dark fs-5"><?php echo htmlspecialchars($user['name']); ?></div>
                                        <div class="text-muted small"><?php echo htmlspecialchars($user['email']); ?></div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="p-3 bg-light rounded-3 border h-100">
                                        <div class="text-muted small mb-1"><i class="fas fa-award me-1 text-warning"></i> Status Kelulusan</div>
                                        <div class="fw-bold text-success fs-5">LULUS / KOMPETEN</div>
                                        <div class="text-muted small">Predikat Sangat Memuaskan</div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="p-3 bg-light rounded-3 border">
                                        <div class="text-muted small mb-1"><i class="fas fa-book-open me-1 text-info"></i> Program Kejuruan</div>
                                        <div class="fw-bold text-dark fs-5"><?php echo htmlspecialchars($class['name']); ?></div>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border mt-1">
                                            Bidang: <?php echo htmlspecialchars($class['category']); ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="p-3 bg-light rounded-3 border h-100">
                                        <div class="text-muted small mb-1"><i class="fas fa-calendar-alt me-1 text-danger"></i> Tanggal Penerbitan</div>
                                        <div class="fw-bold text-dark"><?php echo date('d F Y', strtotime($certData['issued_at'] ?? 'now')); ?></div>
                                        <div class="text-muted small">Bandung, Jawa Barat</div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="p-3 bg-light rounded-3 border h-100">
                                        <div class="text-muted small mb-1"><i class="fas fa-chalkboard-teacher me-1 text-primary"></i> Asesor / Instruktur</div>
                                        <div class="fw-bold text-dark"><?php echo htmlspecialchars($instructor['name'] ?? 'Tim Penguji & Asesor LPK MCM'); ?></div>
                                        <div class="text-muted small"><?php echo htmlspecialchars($instructor['specialization'] ?? 'Asesor Bersertifikasi'); ?></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Units Table -->
                            <div class="mb-4">
                                <h6 class="fw-bold text-dark mb-2"><i class="fas fa-list-check me-2 text-primary"></i>Unit Kompetensi yang Dikuasai:</h6>
                                <div class="table-responsive rounded-3 border">
                                    <table class="table table-sm table-striped align-middle mb-0" style="font-size: 0.85rem;">
                                        <thead class="table-dark">
                                            <tr>
                                                <th style="width: 40px;" class="text-center">No</th>
                                                <th>Judul Modul / Unit Kompetensi</th>
                                                <th style="width: 90px;" class="text-center">Hasil</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $unitItems = !empty($materials) ? $materials : [];
                                            if (empty($unitItems)) {
                                                $features = json_decode($class['features'] ?? '[]', true) ?: [];
                                                foreach ($features as $idx => $feat) {
                                                    $unitItems[] = ['title' => $feat];
                                                }
                                            }
                                            ?>
                                            <?php if (empty($unitItems)): ?>
                                                <tr><td colspan="3" class="text-center text-muted py-2">Unit kurikulum terintegrasi.</td></tr>
                                            <?php else: ?>
                                                <?php foreach ($unitItems as $idx => $m): ?>
                                                    <tr>
                                                        <td class="text-center fw-bold text-muted"><?php echo $idx + 1; ?></td>
                                                        <td class="fw-medium text-dark"><?php echo htmlspecialchars($m['title']); ?></td>
                                                        <td class="text-center text-success fw-bold"><i class="fas fa-check-circle me-1 small"></i>Kompeten</td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Footer Lembaga -->
                            <div class="p-3 rounded-3 text-center bg-white border d-flex align-items-center justify-content-center gap-3">
                                <img src="../assets/img/logo.png" alt="MCM Logo" style="height: 38px; width: auto;">
                                <div class="text-start">
                                    <div class="fw-bold text-dark" style="font-size: 0.85rem;">LPK MITRA CIPTA MANDIRI</div>
                                    <div class="text-muted small" style="font-size: 0.72rem;">Lembaga Pelatihan Kerja Vokasi &amp; Sertifikasi Resmi</div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Invalid / Not Found Card -->
                    <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white text-center p-5 animate__animated animate__shakeX">
                        <div class="d-inline-flex align-items-center justify-content-center bg-danger bg-opacity-10 text-danger rounded-circle mx-auto mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-times-circle fs-1"></i>
                        </div>
                        <h3 class="fw-bold text-dark mb-2">Sertifikat Tidak Ditemukan</h3>
                        <p class="text-muted mb-4" style="max-width: 480px; margin: 0 auto;">
                            Nomor sertifikat <strong><?php echo htmlspecialchars($certNumber ?: 'kosong'); ?></strong> tidak terdaftar dalam pangkalan data resmi LPK Mitra Cipta Mandiri. Pastikan kode QR atau tautan yang Anda pindai sudah benar.
                        </p>
                        <div>
                            <a href="../index.php" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                                Kembali ke Halaman Utama
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
