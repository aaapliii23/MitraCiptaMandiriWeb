<?php
require_once '../includes/auth_user.php';
require_once '../config/database.php';

$userId = (int)$_SESSION['user_id'];

try {
    // Identitas card = (class_id, class_mode) dari baris enrollment itu sendiri,
    // sehingga kelas sama yang dibeli di 2 mode tampil sebagai 2 card terpisah.
    $stmt = $pdo->prepare("
        SELECT e.id AS enrollment_id, e.enrolled_at, e.order_id, c.*,
               COALESCE(e.class_mode, o.class_mode, 'offline') AS class_mode,
               c.whatsapp_group_link,
               c.description_offline,
               (SELECT COUNT(*) FROM materials m WHERE m.class_id = c.id) AS total_materials,
               (SELECT COUNT(*) FROM material_progress mp JOIN materials m ON mp.material_id = m.id WHERE mp.user_id = ? AND m.class_id = c.id AND mp.completed = 1) AS done_materials
        FROM enrollments e
        JOIN classes c ON e.class_id = c.id
        LEFT JOIN orders o ON e.order_id = o.id
        WHERE e.user_id = ?
        ORDER BY e.enrolled_at DESC
    ");
    $stmt->execute([$userId, $userId]);
    $enrolled = $stmt->fetchAll();
} catch (PDOException $e) {
    // Fallback DB lama: kolom e.class_mode belum ada (migrasi belum dijalankan)
    try {
        $stmt = $pdo->prepare("
            SELECT e.id AS enrollment_id, e.enrolled_at, e.order_id, c.*,
                   COALESCE(o.class_mode, 'offline') AS class_mode,
                   c.whatsapp_group_link,
                   c.description_offline,
                   (SELECT COUNT(*) FROM materials m WHERE m.class_id = c.id) AS total_materials,
                   (SELECT COUNT(*) FROM material_progress mp JOIN materials m ON mp.material_id = m.id WHERE mp.user_id = ? AND m.class_id = c.id AND mp.completed = 1) AS done_materials
            FROM enrollments e
            JOIN classes c ON e.class_id = c.id
            LEFT JOIN orders o ON e.order_id = o.id
            WHERE e.user_id = ?
            ORDER BY e.enrolled_at DESC
        ");
        $stmt->execute([$userId, $userId]);
        $enrolled = $stmt->fetchAll();
    } catch (PDOException $e2) {
        $enrolled = [];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LMS Saya - MCM</title>
    <link rel="icon" type="image/png" href="../assets/img/logo.png">
    <link rel="shortcut icon" type="image/png" href="../assets/img/logo.png">
    <link rel="apple-touch-icon" href="../assets/img/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg fixed-top shadow-sm bg-white" style="transition: all 0.4s ease;">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="../index.php">
                <img src="../assets/img/logo.png" alt="MCM Logo" style="height: 40px;">
                <span class="fw-bold ms-2" style="font-size: 0.9rem; letter-spacing: 1px;">LMS MITRA CIPTA MANDIRI</span>
            </a>
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted small d-none d-md-inline"><i class="fas fa-user me-1"></i><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                <a href="profile.php" class="btn btn-outline-primary btn-sm rounded-pill px-3"><i class="fas fa-user-cog me-1"></i>Profil</a>
                <a href="../index.php" class="btn btn-outline-primary btn-sm rounded-pill px-3">Beranda</a>
                <a href="../auth/user_logout.php" class="btn btn-outline-danger btn-sm rounded-pill px-3"><i class="fas fa-sign-out-alt me-1"></i>Keluar</a>
            </div>
        </div>
    </nav>

    <section class="pt-5" style="margin-top: 56px; min-height: 80vh; background: #f8fafc;">
        <div class="container py-4">
            <?php if (!empty($_SESSION['flash_success'])): ?>
                <div class="alert alert-success alert-dismissible fade show py-2 small" role="alert"><i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
            <?php endif; ?>
            <?php if (!empty($_SESSION['flash_error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show py-2 small" role="alert"><?php echo htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
            <?php endif; ?>
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
                <div>
                    <h3 class="fw-bold text-dark mb-1">Halo, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h3>
                    <p class="text-muted mb-0">Ini kelas pelatihan yang telah Anda bayar dan bisa diakses.</p>
                </div>
                <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center gap-2 mt-2 mt-md-0 flex-shrink-0">
                    <span class="badge bg-primary rounded-pill px-3 py-2 text-center"><?php echo count($enrolled); ?> kelas aktif</span>
                    <a href="../pages/programs.php" class="btn btn-primary rounded-pill fw-bold px-4 py-2 shadow-sm text-nowrap" style="background: linear-gradient(135deg, #0c4a6e, #0ea5e9); border: none;"><i class="fas fa-layer-group me-2"></i>Lihat Semua Paket Kelas</a>
                </div>
            </div>

            <?php if (empty($enrolled)): ?>
                <div class="card border-0 shadow-sm rounded-4 p-5 text-center">
                    <i class="fas fa-book-open fs-1 text-primary opacity-25 mb-3"></i>
                    <h5 class="fw-bold text-dark">Belum ada kelas aktif</h5>
                    <p class="text-muted">Anda akan otomatis terdaftar di kelas ini setelah pembayaran lunas.</p>
                    <div class="mt-2">
                        <a href="../pages/programs.php" class="btn btn-primary rounded-pill fw-bold px-4">Lihat Program Pelatihan</a>
                    </div>
                </div>
            <?php else: ?>
                <?php
                $online = [];
                $offline = [];
                foreach ($enrolled as $row) {
                    $mode = strtolower(trim($row['class_mode'] ?? 'offline'));
                    if ($mode === 'online') $online[] = $row; else $offline[] = $row;
                }
                ?>
                <?php if (!empty($online)): ?>
                <div class="d-flex align-items-center gap-2 mb-3">
                    <h5 class="fw-bold text-dark mb-0"><i class="fas fa-laptop me-2 text-info"></i>Kelas Online</h5>
                    <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-3 py-1" style="font-size:0.75rem;"><?php echo count($online); ?> kelas</span>
                </div>
                <div class="row g-4 mb-5">
                    <?php foreach ($online as $c):
                        $total = (int)$c['total_materials'];
                        $done = (int)$c['done_materials'];
                        $pct = $total > 0 ? round(($done / $total) * 100) : 0;
                    ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                                <img src="<?php echo htmlspecialchars(asset_src($c['image'])); ?>" alt="<?php echo htmlspecialchars($c['name']); ?>" class="card-img-top" style="height: 160px; object-fit: cover; background:#e2e8f0; aspect-ratio: 16/9;" width="400" height="160" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='../assets/img/logo.png';">
                                <div class="card-body p-4 d-flex flex-column">
                                    <div class="d-flex flex-wrap gap-2 mb-2">
                                        <span class="badge bg-soft-primary text-primary rounded-pill px-3"><?php echo htmlspecialchars($c['category']); ?></span>
                                        <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-2" style="font-size:0.7rem;"><i class="fas fa-laptop me-1"></i>Online</span>
                                    </div>
                                    <h5 class="fw-bold text-dark mb-2"><?php echo htmlspecialchars($c['name']); ?></h5>
                                    <p class="text-muted small mb-3 text-truncate"><?php echo htmlspecialchars($c['description']); ?></p>
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between small mb-1">
                                            <span class="text-muted">Progres Belajar</span>
                                            <span class="fw-bold <?php echo $pct >= 100 ? 'text-success' : 'text-primary'; ?>"><?php echo $pct; ?>% (<?php echo $done; ?>/<?php echo $total; ?> materi)</span>
                                        </div>
                                        <div class="progress" style="height: 8px; border-radius: 10px;">
                                            <div class="progress-bar <?php echo $pct >= 100 ? 'bg-success' : 'bg-primary'; ?> rounded-pill" style="width: <?php echo $pct; ?>%"></div>
                                        </div>
                                    </div>
                                    <div class="mt-auto">
                                        <?php
                                        $btnLabel = 'Mulai Belajar';
                                        $btnClass = 'btn-primary';
                                        if ($total === 0) {
                                            $btnLabel = 'Lihat Kelas';
                                        } elseif ($pct >= 100) {
                                            $btnLabel = 'Kelas Selesai';
                                            $btnClass = 'btn-success';
                                        }
                                        ?>
                                        <a href="course.php?class_id=<?php echo (int)$c['id']; ?>" class="btn <?php echo $btnClass; ?> w-100 rounded-pill fw-bold">
                                            <?php if ($pct >= 100 && $total > 0): ?><i class="fas fa-check-circle me-1"></i><?php endif; ?>
                                            <?php echo $btnLabel; ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                <?php if (!empty($offline)): ?>
                <div class="d-flex align-items-center gap-2 mb-3">
                    <h5 class="fw-bold text-dark mb-0"><i class="fas fa-chalkboard-teacher me-2 text-success"></i>Kelas Offline</h5>
                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1" style="font-size:0.75rem;"><?php echo count($offline); ?> kelas</span>
                </div>
                <div class="row g-4">
                    <?php foreach ($offline as $c): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                                <img src="<?php echo htmlspecialchars(asset_src($c['image'])); ?>" alt="<?php echo htmlspecialchars($c['name']); ?>" class="card-img-top" style="height: 160px; object-fit: cover; background:#e2e8f0; aspect-ratio: 16/9;" width="400" height="160" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='../assets/img/logo.png';">
                                <div class="card-body p-4 d-flex flex-column">
                                    <div class="d-flex flex-wrap gap-2 mb-2">
                                        <span class="badge bg-soft-primary text-primary rounded-pill px-3"><?php echo htmlspecialchars($c['category']); ?></span>
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2" style="font-size:0.7rem;"><i class="fas fa-chalkboard-teacher me-1"></i>Offline</span>
                                    </div>
                                    <h5 class="fw-bold text-dark mb-2"><?php echo htmlspecialchars($c['name']); ?></h5>
                                    <p class="text-muted small mb-3 text-truncate"><?php echo htmlspecialchars($c['description']); ?></p>
                                    <div class="mb-3">
                                        <div class="d-flex align-items-center gap-2 small text-muted">
                                            <i class="fas fa-map-marker-alt text-success"></i> Kelas tatap muka — jadwal akan diinfo via WhatsApp
                                        </div>
                                    </div>
                                    <div class="mt-auto">
                                        <button type="button" class="btn btn-primary w-100 rounded-pill fw-bold" data-bs-toggle="modal" data-bs-target="#offlineModal<?php echo (int)$c['id']; ?>">Lihat Info Kelas</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                <?php if (!empty($offline)): ?>
                    <?php foreach ($offline as $c): ?>
                    <div class="modal fade" id="offlineModal<?php echo (int)$c['id']; ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 rounded-4 shadow-lg">
                                <div class="modal-header border-0 pb-0">
                                    <h5 class="modal-title fw-bold text-dark"><?php echo htmlspecialchars($c['name']); ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 mb-2"><i class="fas fa-chalkboard-teacher me-1"></i>Offline</span>
                                        <p class="text-muted small mb-0" style="white-space: pre-line;"><?php echo nl2br(htmlspecialchars(!empty($c['description_offline']) ? $c['description_offline'] : $c['description'])); ?></p>
                                    </div>
                                    <?php $wa = trim($c['whatsapp_group_link'] ?? ''); if ($wa !== '' && strpos($wa, 'https://chat.whatsapp.com/') === 0): ?>
                                    <a href="<?php echo htmlspecialchars($wa); ?>" target="_blank" rel="noopener" class="btn rounded-pill py-2 fw-bold text-white w-100 shadow-sm" style="background: linear-gradient(135deg, #25d366, #128c7e);">
                                        <i class="fab fa-whatsapp me-2"></i>Gabung Grup WhatsApp Kelas
                                    </a>
                                    <?php else: ?>
                                    <div class="alert alert-warning border-0 rounded-3 small mb-0" style="background: #fef3c7; color: #92400e;">
                                        <i class="fas fa-info-circle me-2"></i>Admin akan segera menghubungi Anda via WhatsApp untuk info grup kelas & jadwal pelatihan.
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <div class="modal-footer border-0 pt-0">
                                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>