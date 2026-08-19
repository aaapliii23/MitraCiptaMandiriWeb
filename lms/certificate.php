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
    $pdo->exec("CREATE TABLE IF NOT EXISTS `certificates` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `user_id` int(11) NOT NULL,
      `class_id` int(11) NOT NULL,
      `cert_number` varchar(50) NOT NULL UNIQUE,
      `issued_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      UNIQUE KEY `uq_cert_user_class` (`user_id`, `class_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    $pdo->exec("CREATE TABLE IF NOT EXISTS `certificate_templates` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `name` varchar(150) NOT NULL,
      `class_id` int(11) DEFAULT NULL,
      `layout` varchar(50) NOT NULL DEFAULT 'default',
      `bg_image` varchar(255) DEFAULT NULL,
      `accent_color` varchar(20) DEFAULT NULL,
      `is_default` tinyint(1) NOT NULL DEFAULT 0,
      `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

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
                    $stmt = $pdo->prepare("INSERT IGNORE INTO certificates (user_id, class_id, cert_number) VALUES (?, ?, ?)");
                    $stmt->execute([$userId, $classId, $certNumber]);
                    if ($stmt->rowCount() > 0) {
                        $certData = ['cert_number' => $certNumber];
                        break;
                    }
                }
            }
        }

$stmt = $pdo->prepare("SELECT name, email FROM users WHERE id = ? LIMIT 1");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();

    $template = ['layout' => 'default', 'accent_color' => '#1e40af', 'bg_image' => null];
    try {
        $stmt = $pdo->prepare("SELECT * FROM certificate_templates WHERE class_id = ? OR (class_id IS NULL AND is_default = 1) ORDER BY (class_id IS NOT NULL) DESC, is_default DESC LIMIT 1");
        $stmt->execute([$classId]);
        $tmpl = $stmt->fetch();
        if ($tmpl) {
            $template = ['layout' => $tmpl['layout'], 'accent_color' => $tmpl['accent_color'] ?: '#1e40af', 'bg_image' => $tmpl['bg_image']];
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
    <title>Sertifikat - MCM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        :root {
            --cert-accent: <?php echo htmlspecialchars($template['accent_color']); ?>;
        }
        body {
            background-color: <?php echo !empty($template['bg_image']) ? 'transparent' : ''; ?>;
            background-image: <?php echo !empty($template['bg_image']) ? "url('" . htmlspecialchars(asset_src($template['bg_image'])) . "')" : 'none'; ?>;
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        .certificate {
            background: #ffffff;
            border: 12px double var(--cert-accent);
            border-radius: 12px;
            padding: 48px 56px;
            color: #0f172a;
        }
        .cert-ribbon {
            background: linear-gradient(135deg, var(--cert-accent), <?php echo htmlspecialchars($template['accent_color']); ?>cc);
            color: #fff;
            display: inline-block;
            padding: 10px 34px;
            border-radius: 40px;
            letter-spacing: 4px;
            font-size: 0.85rem;
            font-weight: 700;
        }
        .cert-name {
            font-size: 2.4rem;
            font-weight: 800;
            color: var(--cert-accent);
        }
        .cert-seal {
            color: #f59e0b;
        }
        .certificate.layout-elegant {
            border: 2px solid var(--cert-accent);
            border-radius: 0;
            padding: 44px 52px;
            position: relative;
        }
        .certificate.layout-elegant::before {
            content: '';
            position: absolute;
            inset: 10px;
            border: 1px solid var(--cert-accent);
            border-radius: 4px;
            pointer-events: none;
        }
        .certificate.layout-modern {
            border: none;
            border-top: 6px solid var(--cert-accent);
            border-bottom: 6px solid var(--cert-accent);
            border-radius: 4px;
            padding: 48px 56px;
            box-shadow: 0 0 0 2px var(--cert-accent), 0 10px 40px rgba(0,0,0,0.12);
        }
        .certificate.layout-modern .cert-ribbon {
            border-radius: 0;
            padding: 12px 44px;
        }
        .certificate.layout-premium {
            border: 8px solid;
            border-image: linear-gradient(135deg, var(--cert-accent), #d4af37) 1;
            border-radius: 2px;
            padding: 48px 56px;
            background: #fffdf5;
        }
        .certificate.layout-premium .cert-ribbon {
            background: linear-gradient(135deg, #d4af37, #b45309);
            border-radius: 0;
        }
        .certificate.layout-premium .cert-seal {
            color: #d4af37;
        }
        @media print {
            body { background: #fff !important; }
            .no-print { display: none !important; }
            .certificate { border: 12px double var(--cert-accent); box-shadow: none !important; }
            .certificate.layout-elegant { border: 2px solid var(--cert-accent); }
            .certificate.layout-modern { border: none; border-top: 6px solid var(--cert-accent); border-bottom: 6px solid var(--cert-accent); }
            .certificate.layout-premium { border: 8px solid var(--cert-accent); }
        }
    </style>
</head>
<body class="bg-light">
    <?php if ($blocked || !$certData): ?>
        <section class="d-flex align-items-center justify-content-center" style="min-height: 100vh;">
            <div class="card border-0 shadow-sm rounded-4 p-5 text-center">
                <i class="fas fa-lock fs-1 text-danger opacity-25 mb-3"></i>
                <h5 class="fw-bold text-dark">Sertifikat Belum Tersedia</h5>
                <p class="text-muted mb-3">Selesaikan seluruh materi kelas terlebih dahulu untuk mendapatkan sertifikat.</p>
                <div>
                    <a href="course.php?class_id=<?php echo (int)$classId; ?>" class="btn btn-primary rounded-pill fw-bold px-4">Kembali ke Kelas</a>
                    <a href="dashboard.php" class="btn btn-outline-primary rounded-pill fw-bold px-4 ms-2">Dashboard</a>
                </div>
            </div>
        </section>
    <?php else: ?>
        <div class="py-4">
            <div class="text-center mb-4 no-print">
                <a href="course.php?class_id=<?php echo (int)$classId; ?>" class="btn btn-outline-primary rounded-pill fw-bold px-4"><i class="fas fa-arrow-left me-2"></i>Kembali ke Kelas</a>
                <button onclick="window.print()" class="btn btn-primary rounded-pill fw-bold px-4"><i class="fas fa-print me-2"></i>Cetak / Simpan PDF</button>
            </div>
            <div class="container" style="max-width: 980px;">
                <div class="certificate shadow-lg layout-<?php echo htmlspecialchars($template['layout']); ?>">
                    <div class="text-center">
                        <img src="../assets/img/logo.png" alt="MCM Logo" style="height: 90px;">
                        <div class="fw-bold mt-2 mb-1" style="letter-spacing: 3px; font-size: 1.15rem;">MITRA CIPTA MANDIRI</div>
                        <div class="text-muted small">Lembaga Pelatihan Vokasi Bersertifikat</div>
                        <div class="cert-ribbon mt-4 mb-2">SERTIFIKAT KELULUSAN</div>
                        <div class="text-muted small">Nomor: <?php echo htmlspecialchars($certData['cert_number']); ?></div>
                        <p class="mt-4 mb-1 text-muted small">Diberikan kepada</p>
                        <div class="cert-name"><?php echo htmlspecialchars($user['name']); ?></div>
                        <p class="text-muted small mt-1 mb-0"><?php echo htmlspecialchars($user['email']); ?></p>
                        <hr style="width: 55%; margin: 28px auto; border: 1px solid #e2e8f0;">
                        <p class="lead fw-medium px-3">Atas kelulusannya pada program pelatihan</p>
                        <h4 class="fw-bold mb-0" style="color: var(--cert-accent);"><?php echo htmlspecialchars($class['name']); ?></h4>
                        <p class="text-muted small mt-2 mb-0"><?php echo htmlspecialchars($class['category']); ?></p>
                        <p class="mt-4 px-md-5 text-muted small">Peserta telah menyelesaikan seluruh materi pelatihan dan dinyatakan LULUS sesuai standar kompetensi yang ditetapkan.</p>
                        <div class="row mt-5 align-items-end">
                            <div class="col-6 text-center">
                                <div class="text-muted small mb-1">Diterbitkan</div>
                                <div class="fw-bold"><?php echo date('d F Y', strtotime($certData['issued_at'] ?? 'now')); ?></div>
                            </div>
                            <div class="col-6 text-center">
                                <div class="mb-5"></div>
                                <div class="fw-bold mb-1">Mitra Cipta Mandiri</div>
                                <div class="border-top border-2 border-dark mx-auto" style="width: 180px;"></div>
                            </div>
                        </div>
                        <div class="text-center mt-4 cert-seal">
                            <i class="fas fa-award fs-2"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>