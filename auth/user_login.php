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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $postedToken = $_POST['csrf_token'] ?? '';

    if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $postedToken)) {
        $errorMessage = 'Sesi tidak valid. Silakan muat ulang halaman.';
    } elseif (empty($email) || empty($password)) {
        $errorMessage = 'Email dan password wajib diisi.';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id, name, email, password FROM users WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            if ($user && password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['user_logged_in'] = true;
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $cv = preg_replace('/[^a-f0-9]/', '', strtolower($_POST['chat_visitor_id'] ?? ''));
                if ($cv !== '' && strlen($cv) === 12) {
                    try { $pdo->prepare("UPDATE chat_messages SET user_id=? WHERE wa_number=? AND (user_id IS NULL OR user_id=0)")->execute([$user['id'], 'web-'.$cv]); } catch (Exception $e) {}
                    $_SESSION['chat_visitor_id'] = $cv;
                    setcookie('mcmChatVid', $cv, time()+90*24*60*60, '/');
                }
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['status' => 'success']);
                    exit;
                }
                header('Location: ../lms/dashboard.php');
                exit;
            } else {
                $errorMessage = 'Email atau password salah.';
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
            <div class="col-md-6 col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5">
                    <div class="text-center mb-4">
                        <h3 class="fw-bold text-dark">Masuk LMS</h3>
                        <p class="text-muted mb-0">Akses kelas dan materi pelatihan Anda.</p>
                    </div>
                    <?php if (!empty($errorMessage)): ?>
                        <div class="alert alert-danger py-2 small"><?php echo htmlspecialchars($errorMessage); ?></div>
                    <?php endif; ?>
                    <form method="POST" action="user_login.php" id="loginForm">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                        <input type="hidden" name="chat_visitor_id" id="chatVisitorId" value="">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Email</label>
                            <input type="email" class="form-control" name="email" required autocomplete="email">
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-bold">Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" name="password" id="password" required autocomplete="current-password">
                                <button class="btn bg-white toggle-pass-btn" type="button" id="togglePassword" tabindex="-1" aria-label="Lihat password" style="border:1px solid #E2E8F0; border-left:0; border-top-right-radius: var(--radius-md); border-bottom-right-radius: var(--radius-md); color:#94a3b8;"><i class="fas fa-eye"></i></button>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold py-2">Masuk</button>
                    </form>
                    <p class="text-center text-muted small mt-4 mb-0">Belum punya akun?
                        <a href="user_register.php" class="fw-bold text-primary text-decoration-none">Daftar sekarang</a>
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
    var btn = document.getElementById('togglePassword');
    var input = document.getElementById('password');
    if (btn && input) {
        btn.addEventListener('click', function() {
            var isPassword = input.getAttribute('type') === 'password';
            input.setAttribute('type', isPassword ? 'text' : 'password');
            var icon = this.querySelector('i');
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });
    }
    var cv = localStorage.getItem('mcmChatVid') || (document.cookie.match(/(?:^|; )mcmChatVid=([a-f0-9]{12})/) || [])[1] || '';
    var el = document.getElementById('chatVisitorId');
    if (el) el.value = cv;
});
</script>

<?php include '../includes/footer.php'; ?>
