<?php
require_once '../includes/auth_user.php';
require_once '../config/database.php';

$userId = (int)$_SESSION['user_id'];

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

function normalizePhone($phone)
{
    $digits = preg_replace('/\D+/', '', $phone);
    if (strpos($digits, '0') === 0) {
        return '62' . substr($digits, 1);
    }
    if (strpos($digits, '8') === 0) {
        return '62' . $digits;
    }
    return $digits;
}

$successMessage = '';
$errorMessage = '';
$testimonialMessage = '';

$testimonialType = $_POST['form_type'] ?? '';
if ($testimonialType === 'testimonial') {
    $token = $_POST['csrf_token'] ?? '';
    $rating = (int)($_POST['rating'] ?? 5);
    $review = trim($_POST['review'] ?? '');
    $classId = !empty($_POST['class_id']) ? (int)$_POST['class_id'] : null;
    $graduationYear = trim($_POST['graduation_year'] ?? '');
    $job = trim($_POST['job'] ?? '');

    if ($rating < 1 || $rating > 5) $rating = 5;

    if (!hash_equals($csrf_token, $token)) {
        $testimonialMessage = 'Sesi tidak valid. Silakan muat ulang halaman.';
    } elseif (empty($review)) {
        $testimonialMessage = 'Ulasan wajib diisi.';
    } else {
        $image = null;
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, $allowed)) {
                $dir = dirname(__DIR__) . '/uploads/testimonials/';
                if (!is_dir($dir)) mkdir($dir, 0775, true);
                $dest = $dir . uniqid() . '.' . $ext;
                if (move_uploaded_file($_FILES['photo']['tmp_name'], $dest)) {
                    $image = 'uploads/testimonials/' . basename($dest);
                }
            }
        }

        try {
            $stmtName = $pdo->prepare("SELECT name FROM users WHERE id = ? LIMIT 1");
            $stmtName->execute([$userId]);
            $nameRow = $stmtName->fetch();
            $name = $nameRow['name'] ?? $_SESSION['user_name'] ?? '';
            $stmt = $pdo->prepare("INSERT INTO testimonials (user_id, name, rating, review, image, class_id, graduation_year, job, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
            if ($stmt->execute([$userId, $name, $rating, $review, $image, $classId, $graduationYear ?: null, $job ?: null])) {
                $testimonialMessage = 'Testimoni berhasil dikirim dan menunggu persetujuan admin.';
            } else {
                $testimonialMessage = 'Gagal menyimpan testimoni. Silakan coba lagi.';
            }
        } catch (PDOException $e) {
            $testimonialMessage = 'Terjadi kesalahan sistem.';
        }
    }
}

if ($testimonialType !== 'testimonial' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $newPassword2 = $_POST['new_password2'] ?? '';
    $token = $_POST['csrf_token'] ?? '';

    if (!hash_equals($csrf_token, $token)) {
        $errorMessage = 'Sesi tidak valid. Silakan muat ulang halaman.';
    } elseif (empty($name) || empty($email) || empty($phone)) {
        $errorMessage = 'Nama, email, dan nomor WhatsApp wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = 'Format email tidak valid.';
    } elseif (!empty($newPassword) || !empty($newPassword2)) {
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$userId]);
        $userRow = $stmt->fetch();
        if (!$userRow || !password_verify($currentPassword, $userRow['password'])) {
            $errorMessage = 'Password saat ini salah.';
        } elseif (strlen($newPassword) < 6) {
            $errorMessage = 'Password baru minimal 6 karakter.';
        } elseif ($newPassword !== $newPassword2) {
            $errorMessage = 'Konfirmasi password baru tidak cocok.';
        }
    }

    if (empty($errorMessage)) {
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ? LIMIT 1");
            $stmt->execute([$email, $userId]);
            if ($stmt->fetch()) {
                $errorMessage = 'Email sudah digunakan peserta lain.';
            } else {
                $normalizedPhone = normalizePhone($phone);
                if (!empty($newPassword)) {
                    $hash = password_hash($newPassword, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, phone = ?, password = ? WHERE id = ?");
                    $stmt->execute([$name, $email, $normalizedPhone, $hash, $userId]);
                } else {
                    $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, phone = ? WHERE id = ?");
                    $stmt->execute([$name, $email, $normalizedPhone, $userId]);
                }
                $_SESSION['user_name'] = $name;
                $_SESSION['user_email'] = $email;
                $successMessage = 'Profil berhasil diperbarui.';
            }
        } catch (PDOException $e) {
            $errorMessage = 'Terjadi kesalahan sistem.';
        }
    }
}

try {
    $stmt = $pdo->prepare("SELECT name, email, phone FROM users WHERE id = ? LIMIT 1");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    if (!$user) {
        header('Location: ../auth/user_logout.php');
        exit;
    }
} catch (PDOException $e) {
    header('Location: dashboard.php');
    exit;
}

$enrolledClasses = [];
try {
    $stmt = $pdo->prepare("SELECT c.id, c.name FROM enrollments e JOIN classes c ON e.class_id = c.id WHERE e.user_id = ? ORDER BY c.name ASC");
    $stmt->execute([$userId]);
    $enrolledClasses = $stmt->fetchAll();
} catch (PDOException $e) { $enrolledClasses = []; }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - LMS MCM</title>
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
                <a href="dashboard.php" class="btn btn-outline-primary btn-sm rounded-pill px-3"><i class="fas fa-tachometer-alt me-1"></i>Dashboard</a>
                <a href="../index.php" class="btn btn-outline-primary btn-sm rounded-pill px-3">Beranda</a>
                <a href="../auth/user_logout.php" class="btn btn-outline-danger btn-sm rounded-pill px-3"><i class="fas fa-sign-out-alt me-1"></i>Keluar</a>
            </div>
        </div>
    </nav>

    <section class="pt-5" style="margin-top: 56px; min-height: 80vh; background: #f8fafc;">
        <div class="container py-4">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5">
                        <div class="text-center mb-4">
                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="fas fa-user-cog fs-2"></i>
                            </div>
                            <h4 class="fw-bold text-dark mb-1">Profil Saya</h4>
                            <p class="text-muted small mb-0">Perbarui data diri dan kata sandi akun peserta Anda.</p>
                        </div>

                        <?php if (!empty($successMessage)): ?>
                            <div class="alert alert-success py-2 small"><i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($successMessage); ?></div>
                        <?php endif; ?>
                        <?php if (!empty($errorMessage)): ?>
                            <div class="alert alert-danger py-2 small"><?php echo htmlspecialchars($errorMessage); ?></div>
                        <?php endif; ?>

                        <form method="POST" action="profile.php">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Nama Lengkap</label>
                                <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Email</label>
                                <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">No. WhatsApp</label>
                                <input type="text" class="form-control" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required placeholder="Contoh: 081234567890">
                            </div>
                            <hr class="my-4">
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Password Saat Ini</label>
                                <input type="password" class="form-control" name="current_password" autocomplete="current-password" placeholder="Wajib jika mengganti password">
                            </div>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Password Baru</label>
                                    <input type="password" class="form-control" name="new_password" minlength="6" autocomplete="new-password" placeholder="Min. 6 karakter">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Konfirmasi Password Baru</label>
                                    <input type="password" class="form-control" name="new_password2" minlength="6" autocomplete="new-password">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold py-2"><i class="fas fa-save me-2"></i>Simpan Perubahan</button>
                        </form>
                    </div>
                </div>
                <div class="col-md-8 col-lg-6 mt-4">
                    <?php include __DIR__ . '/partials/profile_testimoni_form.php'; ?>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>