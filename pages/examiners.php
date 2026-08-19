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

        <!-- Instructor Grid -->
        <?php if (empty($instructors)): ?>
            <div class="alert alert-info rounded-4 text-center">Belum ada data penguji. Silakan cek kembali nanti.</div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($instructors as $ins): ?>
                    <?php $certs = array_filter(array_map('trim', explode(',', $ins['certifications'] ?? ''))); ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                            <div class="text-center p-4 bg-light">
                                <div class="mx-auto mb-3 bg-white rounded-circle p-2 shadow-sm" style="width: 140px; height: 140px; border: 3px solid #0ea5e9;">
                                    <img src="<?php echo htmlspecialchars(asset_src($ins['image'])); ?>" alt="<?php echo htmlspecialchars($ins['name']); ?>" class="w-100 h-100 rounded-circle" style="object-fit: cover;" onerror="this.onerror=null;this.src='../assets/img/logo.png';">
                                </div>
                                <h5 class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($ins['name']); ?></h5>
                                <p class="text-primary small fw-bold mb-2"><?php echo htmlspecialchars($ins['specialization']); ?></p>
                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3"><?php echo htmlspecialchars($ins['category'] ?: 'Instruktur'); ?></span>
                            </div>
                            <div class="p-4">
                                <p class="small text-muted mb-3" style="min-height: 3em;"><?php echo nl2br(htmlspecialchars($ins['bio'] ?: 'Praktisi berpengalaman di bidangnya, aktif di industri.')); ?></p>
                                <div class="d-flex justify-content-center gap-2 flex-wrap mb-4">
                                    <?php if ($certs): ?>
                                        <?php foreach ($certs as $c): ?>
                                            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3"><?php echo htmlspecialchars($c); ?></span>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <span class="small text-muted">Belum ada sertifikasi.</span>
                                    <?php endif; ?>
                                </div>
                                <a href="class_detail.php?instructor=<?php echo $ins['id']; ?>" class="btn btn-primary w-100 rounded-pill fw-bold py-2 shadow-sm" style="background: linear-gradient(135deg, #0c4a6e, #0ea5e9); border: none;">
                                    <i class="fas fa-user-check me-2"></i>Pilih Penguji Ini & Daftar
                                </a>
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