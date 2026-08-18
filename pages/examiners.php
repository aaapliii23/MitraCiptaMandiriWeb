<?php
require_once '../includes/db_config.php';
$hide_nav_items = true;
include '../includes/header.php';

$examiners = [];
try {
    $examiners = $pdo->query("SELECT * FROM examiners ORDER BY name ASC")->fetchAll();
} catch (PDOException $e) {}
$examiners_json = array_map(function($e) { $e['image'] = asset_src($e['image'] ?? ''); return $e; }, $examiners);
?>

<!-- Examiners Profile Page -->
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
            <p class="text-secondary fs-5" style="max-width: 700px;">Pilih penguji terlebih dahulu untuk mengenal latar belakang para profesional dan asesor kompetensi di MCM.</p>
        </div>

        <!-- Examiner Selector -->
        <div class="row justify-content-center mb-4">
            <div class="col-lg-6 col-md-8">
                <label class="form-label fw-bold text-dark" for="examinerSelect">
                    <i class="fas fa-user-check me-2" style="color: #0ea5e9;"></i>Pilih Penguji
                </label>
                <?php if (empty($examiners)): ?>
                    <div class="alert alert-info rounded-4 mb-0">Belum ada data penguji. Silakan cek kembali nanti.</div>
                <?php else: ?>
                    <select id="examinerSelect" class="form-select form-select-lg shadow-sm" onchange="showExaminer(this.value)">
                        <option value="">-- Pilih Penguji --</option>
                        <?php foreach ($examiners as $ex): ?>
                            <option value="<?php echo $ex['id']; ?>"><?php echo htmlspecialchars($ex['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php endif; ?>
            </div>
        </div>

        <!-- Examiner Detail -->
        <div class="row justify-content-center">
            <div class="col-lg-7 col-md-9">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden text-center p-4">
                    <div id="examinerEmpty" class="py-5">
                        <i class="fas fa-user-tie fa-3x text-muted mb-3"></i>
                        <p class="text-muted mb-0">Silakan pilih penguji di atas untuk melihat latar belakangnya.</p>
                    </div>
                    <div id="examinerData" style="display: none;">
                        <div class="mx-auto mb-4 bg-light rounded-circle p-2" style="width: 150px; height: 150px; border: 3px solid #0ea5e9;">
                            <img id="exDPhoto" src="" alt="Foto Penguji" class="w-100 h-100 rounded-circle" style="object-fit: cover;">
                        </div>
                        <h5 class="fw-bold text-dark mb-1" id="exDName"></h5>
                        <p class="text-primary small fw-bold mb-3" id="exDSpec"></p>
                        <div class="bg-light p-3 rounded-3 mb-3">
                            <p class="small text-muted mb-0" id="exDBio"></p>
                        </div>
                        <div class="d-flex justify-content-center gap-2 flex-wrap" id="exDCerts"></div>
                        <a id="exDPick" href="programs.php" class="btn btn-primary rounded-pill fw-bold px-4 py-2 mt-4 shadow-sm" style="background: linear-gradient(135deg, #0c4a6e, #0ea5e9); border: none;">
                            <i class="fas fa-user-check me-2"></i>Pilih Penguji Ini & Daftar
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quality Commitment -->
        <div class="mt-5 p-5 rounded-5 bg-light text-center border">
            <h4 class="fw-bold text-dark mb-3">Komitmen Penguji Kami</h4>
            <p class="text-muted mx-auto" style="max-width: 800px;">Setiap penguji di MCM adalah praktisi yang tidak hanya menguasai teori, tetapi aktif di bidang industrinya masing-masing. Hal ini memastikan lulusan kami memiliki standar yang relevan dengan kebutuhan pasar kerja saat ini.</p>
        </div>
    </div>
</section>

<script>
const examiners = <?php echo json_encode($examiners_json, JSON_HEX_TAG | JSON_HEX_QUOT | JSON_HEX_APOS | JSON_UNESCAPED_SLASHES); ?>;

function showExaminer(id) {
    const ex = examiners.find(e => String(e.id) === String(id));
    const emptyBox = document.getElementById('examinerEmpty');
    const dataBox = document.getElementById('examinerData');
    if (!ex) {
        dataBox.style.display = 'none';
        emptyBox.style.display = '';
        return;
    }
    emptyBox.style.display = 'none';
    dataBox.style.display = '';
    document.getElementById('exDPhoto').src = ex.image || '../assets/img/logo.png';
    document.getElementById('exDName').textContent = ex.name;
    document.getElementById('exDSpec').textContent = ex.specialization;
    document.getElementById('exDBio').textContent = ex.bio || 'Tidak ada deskripsi latar belakang.';
    document.getElementById('exDPick').href = 'programs.php?examiner=' + ex.id;
    const certs = (ex.certifications || '').split(',').map(s => s.trim()).filter(Boolean);
    document.getElementById('exDCerts').innerHTML = certs.length
        ? certs.map(c => '<span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3">' + c.replace(/[<>&"']/g, '') + '</span>').join('')
        : '<span class="small text-muted">Belum ada sertifikasi.</span>';
}
</script>

<?php include '../includes/footer.php'; ?>