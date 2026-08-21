<?php
require_once '../includes/auth_user.php';
require_once '../config/database.php';

$userId = (int)$_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("
        SELECT e.id AS enrollment_id, e.enrolled_at, c.*,
               (SELECT COUNT(*) FROM materials m WHERE m.class_id = c.id) AS total_materials,
               (SELECT COUNT(*) FROM material_progress mp JOIN materials m ON mp.material_id = m.id WHERE mp.user_id = ? AND m.class_id = c.id AND mp.completed = 1) AS done_materials
        FROM enrollments e
        JOIN classes c ON e.class_id = c.id
        WHERE e.user_id = ?
        ORDER BY e.enrolled_at DESC
    ");
    $stmt->execute([$userId, $userId]);
    $enrolled = $stmt->fetchAll();
} catch (PDOException $e) {
    $enrolled = [];
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
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4">
                <div>
                    <h3 class="fw-bold text-dark mb-1">Halo, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h3>
                    <p class="text-muted mb-0">Ini kelas pelatihan yang telah Anda bayar dan bisa diakses.</p>
                </div>
                <span class="badge bg-primary rounded-pill px-3 py-2 mt-3 mt-md-0"><?php echo count($enrolled); ?> kelas aktif</span>
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
                <div class="row g-4">
                    <?php foreach ($enrolled as $c):
                        $total = (int)$c['total_materials'];
                        $done = (int)$c['done_materials'];
                        $pct = $total > 0 ? round(($done / $total) * 100) : 0;
                    ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                                <img src="../<?php echo htmlspecialchars($c['image']); ?>" alt="<?php echo htmlspecialchars($c['name']); ?>" class="card-img-top" style="height: 160px; object-fit: cover;" onerror="this.src='../assets/img/logo.png';">
                                <div class="card-body p-4 d-flex flex-column">
                                    <span class="badge bg-soft-primary text-primary align-self-start rounded-pill px-3 mb-2"><?php echo htmlspecialchars($c['category']); ?></span>
                                    <h5 class="fw-bold text-dark mb-2"><?php echo htmlspecialchars($c['name']); ?></h5>
                                    <p class="text-muted small mb-3 text-truncate"><?php echo htmlspecialchars($c['description']); ?></p>
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between small mb-1">
                                            <span class="text-muted">Progres Belajar</span>
                                            <span class="fw-bold text-primary"><?php echo $pct; ?>% (<?php echo $done; ?>/<?php echo $total; ?> materi)</span>
                                        </div>
                                        <div class="progress" style="height: 8px; border-radius: 10px;">
                                            <div class="progress-bar bg-primary rounded-pill" style="width: <?php echo $pct; ?>%"></div>
                                        </div>
                                    </div>
                                    <div class="mt-auto">
                                        <a href="course.php?class_id=<?php echo (int)$c['id']; ?>" class="btn btn-primary w-100 rounded-pill fw-bold"><?php echo $total > 0 ? 'Mulai Belajar' : 'Lihat Kelas'; ?></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>