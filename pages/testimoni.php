<?php
session_start();
require_once '../config/database.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

include '../includes/header.php';
?>

<section class="section-padding bg-white" style="padding-top: 120px;">
    <div class="container">
        <div class="card border-0 shadow-sm rounded-5 mx-auto p-5 text-center" style="max-width: 640px;">
            <div class="mx-auto mb-4 rounded-circle d-flex align-items-center justify-content-center" style="width: 90px; height: 90px; background-color: rgba(14, 165, 233, 0.12); color: #0ea5e9;">
                <i class="fas fa-pen fs-1"></i>
            </div>
            <h1 class="display-6 fw-bold mb-3" style="color: #0c4a6e;">Tulis <span style="color: #0ea5e9;">Testimoni</span></h1>
            <p class="text-secondary fs-5 mx-auto mb-4" style="max-width: 520px;">
                Untuk menulis testimoni, silakan masuk ke akun LMS Anda. Ulasan akan tampil di website setelah disetujui admin.
            </p>
            <div class="d-flex justify-content-center gap-2 flex-wrap">
                <?php if (!empty($_SESSION['user_id'])): ?>
                    <a href="../lms/profile.php" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm" style="background: linear-gradient(135deg, #0c4a6e, #0ea5e9); border: none;">
                        <i class="fas fa-pen me-2"></i>Tulis Testimoni di LMS
                    </a>
                <?php else: ?>
                    <a href="../auth/user_login.php" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm" style="background: linear-gradient(135deg, #0c4a6e, #0ea5e9); border: none;">
                        <i class="fas fa-sign-in-alt me-2"></i>Masuk Akun LMS
                    </a>
                    <a href="../index.php#testimoni" class="btn btn-light rounded-pill px-4 py-2 fw-bold shadow-sm border">Lihat Testimoni</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
