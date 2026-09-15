<?php
require_once '../config/database.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$token = trim($_GET['token'] ?? '');
$cert = null;
$class = null;
$materials = [];
$instructor = null;
$error = null;

function tglIndo($dateStr) {
    $months = [1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    $ts = strtotime($dateStr);
    if (!$ts) return htmlspecialchars($dateStr);
    $d = date('j', $ts);
    $m = (int)date('n', $ts);
    $y = date('Y', $ts);
    return $d . ' ' . ($months[$m] ?? date('F', $ts)) . ' ' . $y;
}

if ($token === '') {
    $error = 'Token verifikasi tidak ditemukan. Pastikan Anda membuka tautan dari QR sertifikat yang sah.';
} else {
    try {
        // auto-migrate column if missing for fresh installs / old DB (backfill dulu baru UNIQUE)
        try {
            $pdo->exec("ALTER TABLE certificates ADD COLUMN verify_token VARCHAR(64) NOT NULL DEFAULT '' AFTER cert_number");
        } catch (PDOException $e) {}
        try {
            $pdo->exec("UPDATE certificates SET verify_token = LEFT(SHA2(CONCAT(UUID(), RAND(), id), 256), 40) WHERE verify_token = '' OR verify_token IS NULL");
        } catch (PDOException $e) {}
        try {
            $pdo->exec("ALTER TABLE certificates ADD UNIQUE KEY uq_cert_verify_token (verify_token)");
        } catch (PDOException $e) {}

        $stmt = $pdo->prepare("SELECT cert.*, u.name AS user_name, u.email AS user_email, cl.name AS class_name, cl.category AS class_category, cl.features AS class_features, cl.id AS class_id_real
            FROM certificates cert
            JOIN users u ON cert.user_id = u.id
            JOIN classes cl ON cert.class_id = cl.id
            WHERE cert.verify_token = ? LIMIT 1");
        $stmt->execute([$token]);
        $cert = $stmt->fetch();

        if (!$cert) {
            $error = 'Sertifikat tidak ditemukan atau token tidak valid. Periksa kembali QR pada sertifikat resmi Anda.';
        } else {
            $class = ['name' => $cert['class_name'], 'category' => $cert['class_category'], 'features' => $cert['class_features'], 'id' => $cert['class_id_real']];
            // materials
            try {
                $stmt = $pdo->prepare("SELECT * FROM materials WHERE class_id = ? ORDER BY sort_order ASC, id ASC");
                $stmt->execute([$cert['class_id']]);
                $materials = $stmt->fetchAll();
            } catch (PDOException $e) { $materials = []; }

            // instructor
            try {
                $stmt = $pdo->prepare("SELECT ins.* FROM orders o JOIN instructors ins ON o.instructor_id = ins.id WHERE o.user_id = ? AND o.class_id = ? ORDER BY o.id DESC LIMIT 1");
                $stmt->execute([$cert['user_id'], $cert['class_id']]);
                $instructor = $stmt->fetch();
            } catch (PDOException $e) { $instructor = null; }
        }
    } catch (PDOException $e) {
        $error = 'Terjadi kesalahan sistem saat memverifikasi sertifikat.';
    }
}

$hide_nav_items = true;
include '../includes/header.php';
?>
<link rel="stylesheet" href="../assets/css/verify_certificate.css?v=<?php echo time(); ?>">

<section class="verify-wrapper">
    <div class="verify-card">
        <?php if ($error): ?>
            <div class="verify-header">
                <div class="verify-brand">
                    <img src="../assets/img/logo.png" alt="MCM">
                    <div class="verify-brand-text">
                        <strong>MITRA CIPTA MANDIRI</strong>
                        <span>Lembaga Pelatihan Kerja Vokasi</span>
                    </div>
                </div>
                <span class="verify-badge invalid"><i class="fas fa-times-circle"></i> VERIFIKASI GAGAL</span>
                <h1 class="verify-title" style="font-size:1.15rem;">SERTIFIKAT TIDAK DITEMUKAN</h1>
            </div>
            <div class="verify-invalid-card">
                <i class="fas fa-shield-alt main-icon"></i>
                <h2>Sertifikat tidak valid</h2>
                <p><?php echo htmlspecialchars($error); ?></p>
                <p class="small text-muted mt-3" style="font-size:0.78rem;">Jika Anda merasa ini kesalahan, hubungi admin MCM dengan menyertakan nomor sertifikat dan foto sertifikat fisik Anda.</p>
                <div class="mt-4 d-flex justify-content-center gap-2 flex-wrap">
                    <a href="<?php echo htmlspecialchars($__homeAbs ?? '/index.php'); ?>" class="btn btn-primary rounded-pill px-4 fw-bold"><i class="fas fa-home me-1"></i> Kembali ke Beranda</a>
                    <a href="about.php" class="btn btn-outline-secondary rounded-pill px-4 fw-bold">Hubungi Kami</a>
                </div>
            </div>
        <?php else: ?>
            <div class="verify-header">
                <div class="verify-brand">
                    <img src="../assets/img/logo.png" alt="MCM">
                    <div class="verify-brand-text">
                        <strong>MITRA CIPTA MANDIRI</strong>
                        <span>Lembaga Pelatihan Kerja Vokasi</span>
                    </div>
                </div>
                <span class="verify-badge"><i class="fas fa-check-circle"></i> ✓ SERTIFIKAT TERVERIFIKASI</span>
                <h1 class="verify-title">VERIFIKASI SERTIFIKAT RESMI</h1>
                <p class="verify-subtitle">Sertifikat ini diterbitkan secara sah oleh sistem Mitra Cipta Mandiri dan tercatat dalam basis data resmi lembaga.</p>
            </div>

            <div class="verify-body-inner">
                <div class="verify-grid">
                    <div class="verify-field">
                        <label>Nama Lengkap</label>
                        <div class="val navy"><?php echo htmlspecialchars($cert['user_name']); ?></div>
                    </div>
                    <div class="verify-field">
                        <label>Email</label>
                        <div class="val muted"><?php echo htmlspecialchars($cert['user_email']); ?></div>
                    </div>
                    <div class="verify-field">
                        <label>Nomor Sertifikat</label>
                        <div class="val mono"><?php echo htmlspecialchars($cert['cert_number']); ?></div>
                    </div>
                    <div class="verify-field">
                        <label>Status Kelulusan</label>
                        <div><span class="verify-status"><i class="fas fa-award"></i> KOMPETEN / LULUS</span></div>
                    </div>
                    <div class="verify-field">
                        <label>Program / Kelas</label>
                        <div class="val"><?php echo htmlspecialchars($cert['class_name']); ?></div>
                        <div class="small text-muted mt-1" style="font-size:0.75rem;"><i class="fas fa-tag me-1" style="color:#d4af37;"></i> Bidang Kejuruan: <strong><?php echo htmlspecialchars($cert['class_category']); ?></strong></div>
                    </div>
                    <div class="verify-field">
                        <label>Tanggal Penerbitan</label>
                        <div class="val"><?php echo tglIndo($cert['issued_at']); ?></div>
                        <div class="small text-muted" style="font-size:0.72rem;"><?php echo htmlspecialchars(date('H:i', strtotime($cert['issued_at']))); ?> WIB</div>
                    </div>
                    <div class="verify-field" style="grid-column: 1 / -1;">
                        <label>Asesor / Instruktur Penilai</label>
                        <div class="val"><?php echo htmlspecialchars($instructor['name'] ?? 'Tim Asesor LSP MCM'); ?></div>
                        <div class="small text-muted" style="font-size:0.75rem;"><?php echo htmlspecialchars($instructor['specialization'] ?? 'Asesor Kompetensi Bersertifikasi'); ?></div>
                    </div>
                </div>

                <div class="verify-units">
                    <h3><i class="fas fa-list-check"></i> Daftar Unit Kompetensi yang Dikuasai</h3>
                    <?php
                    $unitItems = !empty($materials) ? $materials : [];
                    if (empty($unitItems) && !empty($class['features'])) {
                        $features = json_decode($class['features'], true) ?: [];
                        foreach ($features as $idx => $feat) {
                            $unitItems[] = ['title' => $feat, 'sort_order' => $idx + 1];
                        }
                    }
                    ?>
                    <?php if (empty($unitItems)): ?>
                        <div class="small text-muted py-3 px-3 border rounded-3 bg-light">Unit kompetensi terintegrasi pada kurikulum inti program.</div>
                    <?php else: ?>
                    <div style="overflow-x:auto;">
                    <table class="verify-table">
                        <thead>
                            <tr>
                                <th class="center" style="width:48px;">No</th>
                                <th style="width:130px;">Kode Unit</th>
                                <th>Judul Unit Kompetensi</th>
                                <th class="center" style="width:92px;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($unitItems as $idx => $m):
                                $prefix = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $class['name']), 0, 3) ?: 'MCM');
                                $code = sprintf("MCM-%s-%03d", $prefix, $idx + 1);
                            ?>
                            <tr>
                                <td class="center fw-bold"><?php echo $idx + 1; ?></td>
                                <td class="fw-semibold" style="font-size:0.75rem;color:#0c4a6e;"><?php echo $code; ?></td>
                                <td><?php echo htmlspecialchars($m['title']); ?></td>
                                <td class="center ok"><i class="fas fa-check-circle me-1"></i>Kompeten</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="verify-note">
                    <i class="fas fa-shield-alt"></i>
                    <span>Verifikasi ini diterbitkan otomatis oleh sistem <strong>Mitra Cipta Mandiri</strong>, dapat dipindai kapan saja melalui QR pada sertifikat resmi. Jika data tidak sesuai dengan sertifikat fisik, hubungi admin untuk konfirmasi lebih lanjut.</span>
                </div>

                <div class="verify-actions">
                    <a href="<?php echo htmlspecialchars($__homeAbs ?? '/index.php'); ?>" class="btn btn-outline-secondary rounded-pill"><i class="fas fa-home me-1"></i> Beranda</a>
                    <a href="../lms/certificate.php?class_id=<?php echo (int)$cert['class_id']; ?>" class="btn btn-primary rounded-pill" style="background: linear-gradient(135deg,#0c4a6e,#0ea5e9); border:none;"><i class="fas fa-certificate me-1"></i> Lihat Sertifikat</a>
                    <button type="button" onclick="window.print()" class="btn btn-light border rounded-pill"><i class="fas fa-print me-1"></i> Cetak Halaman</button>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
