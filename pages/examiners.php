<?php
require_once '../config/database.php';
$hide_nav_items = true;
include '../includes/header.php';

$instructors = [];
try {
    $instructors = $pdo->query("SELECT * FROM instructors ORDER BY name ASC")->fetchAll();
} catch (PDOException $e) {}
?>

<!-- Examiners Profile Page (data Instruktur) -->
<section class="section-padding bg-white animate__animated animate__fadeIn" style="padding-top: 50px;">
    <div class="container">
        <!-- Circular Back Button -->
        <div class="mb-4 text-start" style="margin-left: -5px;">
            <a href="../index.php" class="btn rounded-circle d-inline-flex align-items-center justify-content-center shadow-premium btn-premium" style="width: 50px; height: 50px; background: linear-gradient(135deg, #0c4a6e, #0ea5e9); color: white; border: none; transition: all 0.3s ease;">
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
            <div class="badge bg-primary bg-opacity-10 text-primary mb-3 p-2 px-3 rounded-pill fw-bold" style="background-color: rgba(12, 74, 110, 0.1) !important; color: #0c4a6e !important;">OUR TEAM</div>
            <h1 class="display-5 fw-bold mb-3" style="color: #0c4a6e;">Profil <span style="color: #0ea5e9;">Penguji Ahli</span></h1>
            <p class="text-secondary fs-5" style="max-width: 700px;">Kenali latar belakang para instruktur dan asesor kompetensi profesional di MCM sebelum Anda memilih program.</p>
        </div>

        <!-- Instructor Grid — modern premium -->
        <style>
            .mcm-exam-grid{display:grid;grid-template-columns:repeat(3, 1fr);gap:24px;align-items:stretch}
            @media(max-width: 991px){.mcm-exam-grid{grid-template-columns:repeat(2, 1fr);gap:20px}}
            @media(max-width: 767px){.mcm-exam-grid{grid-template-columns:1fr;gap:20px}}
            .mcm-exam-card{border-radius:18px;background:#fff;border:1px solid rgba(226,232,240,0.9);box-shadow:0 8px 24px rgba(15,23,42,0.06);transition:transform .28s ease, box-shadow .28s ease;overflow:hidden;display:flex;flex-direction:column;height:100%}
            .mcm-exam-card:hover{transform:translateY(-4px);box-shadow:0 16px 32px rgba(15,23,42,0.10)}
            .mcm-exam-photo{width:112px;height:112px;border-radius:50%;margin:0 auto 14px;overflow:hidden;background:#f1f5f9;position:relative;box-shadow:0 8px 20px rgba(15,23,42,0.08);border:1px solid #fff;outline:1px solid rgba(226,232,240,0.9);outline-offset:2px;flex-shrink:0}
            .mcm-exam-photo img{width:100%;height:100%;object-fit:cover;display:block}
            .mcm-exam-photo.is-placeholder{display:flex;align-items:center;justify-content:center;color:#0c4a6e;font-weight:800;font-size:1.6rem;letter-spacing:-0.02em;background:linear-gradient(135deg,#f1f5f9,#e2e8f0);border:none;outline:none;box-shadow:inset 0 1px 0 rgba(255,255,255,0.9), 0 8px 20px rgba(15,23,42,0.06)}
            .mcm-cat-badge{font-size:0.68rem;font-weight:700;letter-spacing:0.03em;border-radius:999px;padding:6px 12px;border:1px solid transparent}
            .mcm-cert-list{display:flex;flex-direction:column;gap:8px;align-items:stretch}
            .mcm-cert-item{display:flex;align-items:center;gap:10px;font-size:0.82rem;color:#334155;background:#f8fafc;border:1px solid #eef2f7;border-radius:12px;padding:8px 12px;line-height:1.4}
            .mcm-cert-item i{flex:0 0 22px;width:22px;height:22px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:0.7rem;color:#fff;background:linear-gradient(135deg,#0c4a6e,#0ea5e9)}
        </style>
        <?php if (empty($instructors)): ?>
            <div class="alert alert-info rounded-4 text-center">Belum ada data penguji. Silakan cek kembali nanti.</div>
        <?php else: ?>
            <?php
            // warna kategori — purpose: diferensiasi sekilas
            function mcmCatStyle($cat){
                $c = strtolower(trim($cat ?? ''));
                if (str_contains($c, 'digital')) return 'background:rgba(99,102,241,0.10);color:#4f46e5;border-color:rgba(99,102,241,0.18);';
                if (str_contains($c, 'kesehatan')) return 'background:rgba(20,184,166,0.10);color:#0f766e;border-color:rgba(20,184,166,0.18);';
                if (str_contains($c, 'pariwisata')) return 'background:rgba(249,115,22,0.10);color:#c2410c;border-color:rgba(249,115,22,0.18);';
                if (str_contains($c, 'kecantikan')) return 'background:rgba(236,72,153,0.10);color:#be185d;border-color:rgba(236,72,153,0.18);';
                if (str_contains($c, 'kuliner')) return 'background:rgba(245,158,11,0.12);color:#92400e;border-color:rgba(245,158,11,0.20);';
                return 'background:rgba(14,165,233,0.08);color:#0c4a6e;border-color:rgba(14,165,233,0.14);';
            }
            function mcmInitials($name){
                $parts = preg_split('/\s+/', trim($name));
                $a = strtoupper(mb_substr($parts[0] ?? '',0,1));
                $b = strtoupper(mb_substr($parts[1] ?? '',0,1));
                return $a . $b;
            }
            ?>
            <div class="mcm-exam-grid">
                <?php foreach ($instructors as $ins): ?>
                    <?php $certs = array_filter(array_map('trim', explode(',', $ins['certifications'] ?? ''))); ?>
                    <?php $isPlaceholder = empty($ins['image']) || str_contains(strtolower($ins['image']), 'logo.png') || str_contains(strtolower($ins['image']), 'placeholder'); ?>
                    <div class="mcm-exam-item d-flex">
                        <div class="mcm-exam-card w-100 d-flex flex-column">
                            <div class="text-center p-4 p-lg-5 pb-3">
                                <?php if ($isPlaceholder): ?>
                                    <div class="mcm-exam-photo is-placeholder" aria-hidden="true"><?php echo htmlspecialchars(mcmInitials($ins['name'])); ?></div>
                                <?php else: ?>
                                    <div class="mcm-exam-photo">
                                        <img src="<?php echo htmlspecialchars(asset_src($ins['image'])); ?>" alt="<?php echo htmlspecialchars($ins['name']); ?>" loading="lazy" decoding="async" onerror="this.closest('.mcm-exam-photo').classList.add('is-placeholder'); this.closest('.mcm-exam-photo').innerHTML='<?php echo htmlspecialchars(mcmInitials($ins['name'])); ?>'; this.remove();">
                                    </div>
                                <?php endif; ?>
                                <span class="mcm-cat-badge" style="<?php echo mcmCatStyle($ins['category']); ?>"><?php echo htmlspecialchars($ins['category'] ?: 'Instruktur'); ?></span>
                                <h5 class="fw-bold mt-3 mb-1" style="color:#0f172a;font-size:1.15rem;letter-spacing:-0.01em;line-height:1.3;"><?php echo htmlspecialchars($ins['name']); ?></h5>
                                <div class="small fw-semibold" style="color:#0ea5e9;letter-spacing:0.01em;"><?php echo htmlspecialchars($ins['specialization']); ?></div>
                            </div>
                            <div class="px-4 px-lg-5 pb-4 flex-grow-1 d-flex flex-column">
                                <p class="small mb-3 flex-grow-1" style="color:#475569;line-height:1.7;min-height:3.2em;"><?php echo nl2br(htmlspecialchars($ins['bio'] ?: 'Praktisi berpengalaman di bidangnya, aktif di industri dan pendampingan peserta.')); ?></p>
                                <?php if ($certs): ?>
                                    <div class="mcm-cert-list mt-2">
                                        <?php foreach ($certs as $c): ?>
                                            <div class="mcm-cert-item"><i class="fas fa-check"></i><span><?php echo htmlspecialchars($c); ?></span></div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="small text-center py-2" style="color:#94a3b8;">Sertifikasi menyusul</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Quality Commitment -->
        <div class="mt-5 p-5 rounded-5 bg-light text-center border">
            <h4 class="fw-bold text-dark mb-3">Komitmen Penguji Kami</h4>
            <p class="text-muted mx-auto" style="max-width: 800px;">Setiap penguji di MCM adalah praktisi yang tidak hanya menguasai teori, tetapi aktif di bidang industrinya masing-masing. Hal ini memastikan lulusan kami memiliki standar yang relevan dengan kebutuhan pasar kerja saat ini.</p>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>