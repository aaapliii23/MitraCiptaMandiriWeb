<?php
session_start();
if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    header('Location: ../lms/dashboard.php');
    exit;
}
require_once '../config/database.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

$errorMessage = '';
$isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');

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

function validateFullName($name)
{
    if (strlen($name) < 3 || strlen($name) > 100) return false;
    return preg_match('/^[\p{L}]+(?:[ -][\p{L}]+)*$/u', $name) === 1;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';
    $postedToken = $_POST['csrf_token'] ?? '';

    if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $postedToken)) {
        $errorMessage = 'Sesi tidak valid. Silakan muat ulang halaman.';
    } elseif (empty($name) || empty($email) || empty($phone) || empty($password)) {
        $errorMessage = 'Semua kolom wajib diisi.';
    } elseif (!validateFullName($name)) {
        $errorMessage = 'Nama hanya boleh berisi huruf, spasi, dan tanda hubung (tanpa angka atau simbol).';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = 'Format email tidak valid.';
    } elseif (strlen($password) < 6) {
        $errorMessage = 'Password minimal 6 karakter.';
    } elseif ($password !== $password2) {
        $errorMessage = 'Konfirmasi password tidak cocok.';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $errorMessage = 'Email sudah terdaftar. Silakan masuk.';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $normalizedPhone = normalizePhone($phone);
                $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $email, $normalizedPhone, $hash]);
                $userId = $pdo->lastInsertId();

                session_regenerate_id(true);
                $_SESSION['user_logged_in'] = true;
                $_SESSION['user_id'] = $userId;
                $_SESSION['user_name'] = $name;
                $_SESSION['user_email'] = $email;

                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['status' => 'success']);
                    exit;
                }
                header('Location: ../lms/dashboard.php');
                exit;
            }
        } catch (PDOException $e) {
            $errorMessage = 'Terjadi kesalahan sistem.';
        }
    }

    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => $errorMessage]);
        exit;
    }
}

?>
<?php include '../includes/header.php'; ?>

<section class="pt-5 pb-5" style="margin-top: 80px; min-height: 70vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5">
                    <div class="text-center mb-4">
                        <h3 class="fw-bold text-dark">Buat Akun Peserta</h3>
                        <p class="text-muted mb-0">Daftar untuk mengakses LMS setelah pembayaran lunas.</p>
                    </div>
                    <?php if (!empty($errorMessage)): ?>
                        <div class="alert alert-danger py-2 small"><?php echo htmlspecialchars($errorMessage); ?></div>
                    <?php endif; ?>
                    <form method="POST" action="user_register.php">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Nama Asli Lengkap</label>
                            <input type="text" class="form-control" name="name" required autocomplete="name" minlength="3" maxlength="100" pattern="[A-Za-z\u00C0-\u017F\s-]+">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Email</label>
                            <input type="email" class="form-control" name="email" required autocomplete="email">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">No. WhatsApp</label>
                            <input type="text" class="form-control" name="phone" required placeholder="Contoh: 081234567890" autocomplete="tel">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" name="password" id="password" required minlength="6" autocomplete="new-password">
                                <button class="btn bg-white toggle-pass-btn" type="button" id="togglePassword" tabindex="-1" aria-label="Lihat password" style="border:1px solid #E2E8F0; border-left:0; border-top-right-radius: var(--radius-md); border-bottom-right-radius: var(--radius-md); color:#94a3b8;"><i class="fas fa-eye"></i></button>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-bold">Konfirmasi Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" name="password2" id="password2" required minlength="6" autocomplete="new-password">
                                <button class="btn bg-white toggle-pass-btn" type="button" id="togglePassword2" tabindex="-1" aria-label="Lihat konfirmasi password" style="border:1px solid #E2E8F0; border-left:0; border-top-right-radius: var(--radius-md); border-bottom-right-radius: var(--radius-md); color:#94a3b8;"><i class="fas fa-eye"></i></button>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold py-2">Daftar</button>
                    </form>
                    <p class="text-center text-muted small mt-4 mb-0">Sudah punya akun?
                        <a href="user_login.php" class="fw-bold text-primary text-decoration-none">Masuk di sini</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.toggle-pass-btn:hover { color: #0ea5e9 !important; }
.toggle-pass-btn:focus { box-shadow: none; }
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    function setupToggle(btnId, inputId) {
        var btn = document.getElementById(btnId);
        var input = document.getElementById(inputId);
        if (!btn || !input) return;
        btn.addEventListener('click', function() {
            var isPassword = input.getAttribute('type') === 'password';
            input.setAttribute('type', isPassword ? 'text' : 'password');
            var icon = this.querySelector('i');
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });
    }
    setupToggle('togglePassword', 'password');
    setupToggle('togglePassword2', 'password2');
});
</script>

<?php include '../includes/footer.php'; ?>
