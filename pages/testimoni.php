<?php
session_start();
require_once '../includes/db_config.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

$submitted = false;
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $errorMessage = 'Sesi tidak valid. Silakan muat ulang halaman.';
    } else {
        $name = trim($_POST['name'] ?? '');
        $rating = (int)($_POST['rating'] ?? 5);
        $review = trim($_POST['review'] ?? '');
        $classId = !empty($_POST['class_id']) ? (int)$_POST['class_id'] : null;

        if ($rating < 1 || $rating > 5) $rating = 5;
        if (empty($name) || empty($review)) {
            $errorMessage = 'Nama dan ulasan wajib diisi.';
        } else {
            $image = null;
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $allowed = ['jpg', 'jpeg', 'png', 'webp'];
                $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
                if (in_array($ext, $allowed)) {
                    $dir = '../uploads/testimonials/';
                    if (!is_dir($dir)) mkdir($dir, 0775, true);
                    $dest = $dir . uniqid() . '.' . $ext;
                    if (move_uploaded_file($_FILES['photo']['tmp_name'], $dest)) {
                        $image = $dest;
                    }
                }
            }

            $stmt = $pdo->prepare("INSERT INTO testimonials (name, rating, review, image, class_id, status) VALUES (?, ?, ?, ?, ?, 'pending')");
            if ($stmt->execute([$name, $rating, $review, $image, $classId])) {
                $submitted = true;
            } else {
                $errorMessage = 'Gagal menyimpan testimoni. Silakan coba lagi.';
            }
        }
    }
}

// Load classes for optional program selection
$classes = [];
try {
    $classes = $pdo->query("SELECT id, name FROM classes ORDER BY name ASC")->fetchAll();
} catch (PDOException $e) { $classes = []; }

include '../includes/header.php';
?>

<style>
    .star-rating { display: flex; flex-direction: row-reverse; justify-content: flex-end; gap: 4px; }
    .star-rating input { display: none; }
    .star-rating label { font-size: 1.8rem; color: #d1d5db; cursor: pointer; transition: transform 0.2s ease; }
    .star-rating label:hover, .star-rating label:hover ~ label { color: #f59e0b; transform: scale(1.15); }
    .star-rating input:checked ~ label { color: #f59e0b; }
</style>

<section class="section-padding bg-white" style="padding-top: 120px;">
    <div class="container">
        <div class="text-center mb-5">
            <div class="badge bg-primary bg-opacity-10 text-primary mb-3 p-2 px-3 rounded-pill fw-bold" style="background-color: rgba(12, 74, 110, 0.1) !important; color: #0c4a6e !important;">TESTIMONI</div>
            <h1 class="display-5 fw-bold mb-3" style="color: #0c4a6e;">Tulis <span style="color: #0ea5e9;">Testimoni Anda</span></h1>
            <p class="text-secondary fs-5 mx-auto" style="max-width: 650px;">Bagikan pengalaman Anda mengikuti pelatihan di MCM. Ulasan Anda akan tampil di website setelah disetujui admin.</p>
        </div>

        <?php if ($submitted): ?>
            <div class="card border-0 shadow-sm rounded-5 p-5 text-center mx-auto" style="max-width: 560px;">
                <div class="mx-auto mb-4 rounded-circle d-flex align-items-center justify-content-center" style="width: 90px; height: 90px; background-color: rgba(22, 163, 74, 0.12); color: #16a34a;">
                    <i class="fas fa-check-circle fs-1"></i>
                </div>
                <h3 class="fw-bold mb-2" style="color: #0c4a6e;">Terima Kasih!</h3>
                <p class="text-secondary mb-4">Testimoni Anda berhasil dikirim dan sedang <strong>menunggu persetujuan admin</strong> sebelum ditampilkan di website.</p>
                <div class="d-flex justify-content-center gap-2">
                    <a href="../index.php#testimoni" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" style="background: linear-gradient(135deg, #0c4a6e, #0ea5e9); border: none;">Lihat Testimoni</a>
                    <a href="testimoni.php" class="btn btn-light rounded-pill px-4 fw-bold shadow-sm border">Tulis Lagi</a>
                </div>
            </div>
        <?php else: ?>
            <div class="card border-0 shadow-sm rounded-5 mx-auto" style="max-width: 680px;">
                <div class="card-body p-4 p-md-5">
                    <?php if (!empty($errorMessage)): ?>
                        <div class="alert alert-danger d-flex align-items-center rounded-4 py-2 px-3 mb-4" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <span><?php echo htmlspecialchars($errorMessage); ?></span>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="testimoni.php" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-secondary mb-2">Rating Anda *</label>
                            <div class="star-rating">
                                <input type="radio" id="star5" name="rating" value="5" checked><label for="star5"><i class="fas fa-star"></i></label>
                                <input type="radio" id="star4" name="rating" value="4"><label for="star4"><i class="fas fa-star"></i></label>
                                <input type="radio" id="star3" name="rating" value="3"><label for="star3"><i class="fas fa-star"></i></label>
                                <input type="radio" id="star2" name="rating" value="2"><label for="star2"><i class="fas fa-star"></i></label>
                                <input type="radio" id="star1" name="rating" value="1"><label for="star1"><i class="fas fa-star"></i></label>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-secondary mb-2">Nama Lengkap *</label>
                            <input type="text" class="form-control form-control-lg bg-light border-0 rounded-3" name="name" required placeholder="Budi Santoso">
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-secondary mb-2">Program yang Diikuti <span class="text-muted fw-normal">(opsional)</span></label>
                            <select class="form-select form-select-lg bg-light border-0 rounded-3" name="class_id">
                                <option value="">-- Pilih Program --</option>
                                <?php foreach ($classes as $c): ?>
                                    <option value="<?php echo (int)$c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-secondary mb-2">Ulasan Anda *</label>
                            <textarea class="form-control bg-light border-0 rounded-3" name="review" rows="5" required placeholder="Ceritakan pengalaman Anda mengikuti pelatihan di MCM..."></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-secondary mb-2">Foto Profil <span class="text-muted fw-normal">(opsional)</span></label>
                            <input type="file" class="form-control bg-light border-0 rounded-3" name="photo" accept=".jpg,.jpeg,.png,.webp">
                            <small class="text-muted">Format: JPG, PNG, WEBP. Maksimal 2MB.</small>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-sm mt-2" style="background: linear-gradient(135deg, #0c4a6e, #0ea5e9); border: none; font-size: 1rem;">
                            <i class="fas fa-paper-plane me-2"></i>Kirim Testimoni
                        </button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include '../includes/footer.php'; ?>