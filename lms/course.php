<?php
require_once '../includes/auth_user.php';
require_once '../config/database.php';

$userId = (int)$_SESSION['user_id'];
$classId = (int)($_GET['class_id'] ?? 0);

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
        $notEnrolled = true;
    } else {
        $notEnrolled = false;
        $stmt = $pdo->prepare("SELECT c.*, COUNT(m.id) AS total_materials FROM classes c LEFT JOIN materials m ON m.class_id = c.id WHERE c.id = ? GROUP BY c.id");
        $stmt->execute([$classId]);
        $class = $stmt->fetch();

        $stmt = $pdo->prepare("SELECT m.*, (SELECT mp.completed FROM material_progress mp WHERE mp.material_id = m.id AND mp.user_id = ?) AS is_done FROM materials m WHERE m.class_id = ? ORDER BY m.sort_order ASC, m.id ASC");
        $stmt->execute([$userId, $classId]);
        $materials = $stmt->fetchAll();

        $prevAllDone = true;
        foreach ($materials as $k => $m) {
            $materials[$k]['locked'] = !$prevAllDone;
            if (!$m['is_done']) $prevAllDone = false;
        }
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
    <title><?php echo $notEnrolled ? 'Akses Ditolak' : htmlspecialchars($class['name']) . ' - LMS MCM'; ?></title>
    <link rel="icon" type="image/png" href="../assets/img/logo.png">
    <link rel="shortcut icon" type="image/png" href="../assets/img/logo.png">
    <link rel="apple-touch-icon" href="../assets/img/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11">
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
                <a href="dashboard.php" class="btn btn-outline-primary btn-sm rounded-pill px-3"><i class="fas fa-tachometer-alt me-1"></i>Dashboard</a>
                <a href="profile.php" class="btn btn-outline-primary btn-sm rounded-pill px-3"><i class="fas fa-user-cog me-1"></i>Profil</a>
                <a href="../auth/user_logout.php" class="btn btn-outline-danger btn-sm rounded-pill px-3"><i class="fas fa-sign-out-alt me-1"></i>Keluar</a>
            </div>
        </div>
    </nav>

    <section class="pt-5" style="margin-top: 56px; min-height: 80vh; background: #f8fafc;">
        <div class="container py-4">
            <?php if ($notEnrolled): ?>
                <div class="card border-0 shadow-sm rounded-4 p-5 text-center">
                    <i class="fas fa-lock fs-1 text-danger opacity-25 mb-3"></i>
                    <h5 class="fw-bold text-dark">Akses Ditolak</h5>
                    <p class="text-muted">Anda belum terdaftar di kelas ini. Lakukan pembayaran untuk mulai belajar.</p>
                    <div class="mt-2">
                        <a href="../pages/programs.php" class="btn btn-primary rounded-pill fw-bold px-4">Lihat Program</a>
                    </div>
                </div>
            <?php else:
                $doneCount = 0;
                foreach ($materials as $m) if ($m['is_done']) $doneCount++;
                $pct = count($materials) > 0 ? round(($doneCount / count($materials)) * 100) : 0;
            ?>
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                    <div class="row g-0">
                        <div class="col-md-4">
                            <img src="../<?php echo htmlspecialchars($class['image']); ?>" alt="<?php echo htmlspecialchars($class['name']); ?>" style="width: 100%; height: 100%; min-height: 180px; object-fit: cover;" onerror="this.src='../assets/img/logo.png';">
                        </div>
                        <div class="col-md-8">
                            <div class="p-4">
                                <span class="badge bg-soft-primary text-primary rounded-pill px-3 mb-2"><?php echo htmlspecialchars($class['category']); ?></span>
                                <h3 class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($class['name']); ?></h3>
                                <p class="text-muted mb-3"><?php echo htmlspecialchars($class['description']); ?></p>
                                <div class="d-flex justify-content-between small mb-1">
                                    <span class="text-muted">Progres Belajar</span>
                                    <span class="fw-bold text-primary"><?php echo $pct; ?>% (<?php echo $doneCount; ?>/<?php echo count($materials); ?> materi)</span>
                                </div>
                                <div class="progress mb-3" style="height: 8px; border-radius: 10px;">
                                    <div class="progress-bar bg-primary rounded-pill" style="width: <?php echo $pct; ?>%"></div>
                                </div>
                                <?php if ($pct >= 100 && count($materials) > 0): ?>
                                    <a href="certificate.php?class_id=<?php echo (int)$classId; ?>" class="btn btn-success rounded-pill fw-bold px-4"><i class="fas fa-award me-2"></i>Lihat Sertifikat</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-dark mb-0">Daftar Materi</h5>
                    <span class="badge bg-light text-dark rounded-pill px-3 py-2 border"><?php echo count($materials); ?> materi</span>
                </div>

                <?php if (empty($materials)): ?>
                    <div class="card border-0 shadow-sm rounded-4 p-5 text-center">
                        <i class="fas fa-box-open fs-1 text-primary opacity-25 mb-3"></i>
                        <h5 class="fw-bold text-dark">Belum ada materi</h5>
                        <p class="text-muted mb-0">Materi kelas sedang disiapkan oleh admin.</p>
                    </div>
                <?php else: ?>
                    <div class="list-group rounded-4 overflow-hidden shadow-sm border-0">
                        <?php foreach ($materials as $i => $m): ?>
                            <?php if ($m['locked']): ?>
                                <div class="list-group-item d-flex align-items-center gap-3 p-3 bg-light" style="border-left: 4px solid #cbd5e1;">
                                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-secondary bg-opacity-10 text-secondary flex-shrink-0" style="width: 34px; height: 34px;">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <div class="flex-grow-1">
                                        <div class="fw-bold text-muted"><?php echo htmlspecialchars($m['title']); ?></div>
                                        <small class="text-muted"><i class="fas fa-lock me-1"></i>Terkunci - selesaikan modul sebelumnya</small>
                                    </div>
                                    <span class="badge rounded-pill px-3 text-uppercase small
                                        <?php echo $m['type'] === 'video' ? 'bg-danger bg-opacity-10 text-danger' : ($m['type'] === 'pdf' ? 'bg-warning bg-opacity-10 text-warning' : 'bg-info bg-opacity-10 text-info'); ?>">
                                        <i class="fas <?php echo $m['type'] === 'video' ? 'fa-video' : ($m['type'] === 'pdf' ? 'fa-file-pdf' : 'fa-align-left'); ?> me-1"></i><?php echo $m['type']; ?>
                                    </span>
                                </div>
                            <?php else: ?>
                                <a href="material.php?material_id=<?php echo (int)$m['id']; ?>" class="list-group-item list-group-item-action d-flex align-items-center gap-3 p-3 text-decoration-none" style="border-left: 4px solid <?php echo $m['is_done'] ? '#16a34a' : '#e2e8f0'; ?>;">
                                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle flex-shrink-0 <?php echo $m['is_done'] ? 'btn-success text-white' : 'btn-light border'; ?>" style="width: 34px; height: 34px;">
                                        <i class="fas <?php echo $m['is_done'] ? 'fa-check' : 'fa-play'; ?>"></i>
                                    </span>
                                    <div class="flex-grow-1">
                                        <div class="fw-bold <?php echo $m['is_done'] ? 'text-success' : 'text-dark'; ?>"><?php echo htmlspecialchars($m['title']); ?></div>
                                        <small class="text-muted">Materi <?php echo $i + 1; ?></small>
                                    </div>
                                    <?php if ($m['is_done']): ?>
                                        <span class="badge badge-soft-success"><i class="fas fa-check-circle me-1"></i>Selesai</span>
                                    <?php else: ?>
                                        <span class="badge bg-primary bg-opacity-10 text-primary"><i class="fas fa-play me-1"></i>Mulai</span>
                                    <?php endif; ?>
                                </a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>