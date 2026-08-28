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
<link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/auth.css?v=<?php echo time(); ?>">

<section class="auth-wrapper">
    <div class="container auth-container">
        <div class="auth-split auth-anim">
            <div class="auth-branding">
                <div class="auth-branding-inner">
                    <div class="auth-brand-badge">
                        <img src="<?php echo $base_url; ?>assets/img/logo.png" alt="MCM"> Mitra Cipta Mandiri
                    </div>
                    <h2 class="auth-brand-title">Selamat datang kembali!</h2>
                    <p class="auth-brand-sub">Masuk untuk melanjutkan pembelajaran, mengakses materi pelatihan, dan mengunduh sertifikat resmi Anda.</p>
                    <ul class="auth-brand-features">
                        <li><i class="fas fa-book-open"></i><span>Akses materi & video pelatihan kapan saja dari LMS</span></li>
                        <li><i class="fas fa-award"></i><span>Sertifikat bernomor unik dengan verifikasi QR</span></li>
                        <li><i class="fas fa-headset"></i><span>Pendampingan instruktur & CS yang responsif</span></li>
                    </ul>
                    <div class="auth-brand-visual" aria-hidden="true">
                        <div class="auth-visual-card"><i class="fas fa-graduation-cap"></i></div>
                        <div class="auth-visual-card small"><i class="fas fa-certificate"></i></div>
                    </div>
                    <div class="auth-brand-foot"><i class="fas fa-shield-alt"></i> Platform resmi MCM — aman & terpercaya sejak 2021</div>
                </div>
                <div class="auth-branding-deco deco-1"></div>
                <div class="auth-branding-deco deco-2"></div>
            </div>

            <div class="auth-form-panel">
                <div class="auth-form-head">
                    <h3>Masuk LMS</h3>
                    <p>Akses kelas dan materi pelatihan Anda. Belum punya akun? Daftar sekarang gratis.</p>
                </div>

                <?php if (!empty($errorMessage)): ?>
                    <div class="auth-alert" role="alert"><i class="fas fa-exclamation-circle me-1"></i> <?php echo htmlspecialchars($errorMessage); ?></div>
                <?php endif; ?>

                <form method="POST" action="user_login.php" id="authLoginForm" novalidate>
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

                    <div class="auth-field">
                        <label class="auth-label" for="authEmail"><i class="fas fa-envelope"></i> Email</label>
                        <input type="email" class="form-control auth-input" id="authEmail" name="email" required autocomplete="email" placeholder="nama@email.com" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                        <div class="auth-feedback" id="emailFeedback"></div>
                    </div>

                    <div class="auth-field mb-lg">
                        <label class="auth-label" for="password"><i class="fas fa-lock"></i> Password</label>
                        <div class="auth-input-group">
                            <input type="password" class="form-control auth-input" name="password" id="password" required autocomplete="current-password" placeholder="Masukkan password Anda">
                            <button class="auth-eye-btn" type="button" id="togglePassword" tabindex="-1" aria-label="Lihat password"><i class="fas fa-eye"></i></button>
                        </div>
                        <div class="auth-feedback" id="passFeedback"></div>
                    </div>

                    <button type="submit" class="btn auth-btn" id="authSubmitBtn"><span class="btn-text">Masuk</span> <i class="fas fa-arrow-right"></i></button>

                    <div class="auth-divider"><span>atau</span></div>
                    <p class="auth-switch">Belum punya akun? <a href="user_register.php">Daftar sekarang</a></p>
                    <p class="auth-terms">Dengan masuk, Anda menyetujui <a href="#">Syarat Layanan</a> & <a href="#">Kebijakan Privasi</a> MCM.</p>
                </form>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
<<<<<<< HEAD
    var email = document.getElementById('authEmail');
    var pass = document.getElementById('password');
    var toggleBtn = document.getElementById('togglePassword');
    var emailFb = document.getElementById('emailFeedback');
    var passFb = document.getElementById('passFeedback');
    var form = document.getElementById('authLoginForm');
    var submitBtn = document.getElementById('authSubmitBtn');

    function setFeedback(el, input, msg, ok) {
        if (!el) return;
        if (!msg) { el.textContent=''; el.className='auth-feedback'; if(input) input.classList.remove('is-invalid','is-valid'); return; }
        el.textContent = msg;
        el.className = ok ? 'auth-feedback show ok' : 'auth-feedback show';
        if (input) {
            input.classList.toggle('is-invalid', !ok);
            input.classList.toggle('is-valid', !!ok);
        }
    }

    if (toggleBtn && pass) {
        toggleBtn.addEventListener('click', function() {
            var isPw = pass.getAttribute('type') === 'password';
            pass.setAttribute('type', isPw ? 'text' : 'password');
            var ic = this.querySelector('i');
            if (ic) { ic.classList.toggle('fa-eye'); ic.classList.toggle('fa-eye-slash'); }
            this.style.color = isPw ? '#0ea5e9' : '#94a3b8';
        });
        toggleBtn.addEventListener('mouseenter', function(){ this.style.color='#0ea5e9'; });
        toggleBtn.addEventListener('mouseleave', function(){
            var isText = pass.getAttribute('type')==='text';
            this.style.color = isText ? '#0ea5e9' : '#94a3b8';
        });
    }

    function validateEmail(showOk){
        if(!email) return true;
        var v = email.value.trim();
        if(!v){ setFeedback(emailFb,email,'',false); return false; }
        var re = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
        if(!re.test(v)){ setFeedback(emailFb,email,'Format email tidak valid.',false); return false; }
        if(showOk) setFeedback(emailFb,email,'Format email valid.',true);
        else setFeedback(emailFb,email,'',false);
        if(showOk && email.classList.contains('is-valid')) setTimeout(function(){ setFeedback(emailFb,email,'',false); }, 1800);
        return true;
    }
    function validatePass(){
        if(!pass) return true;
        var v = pass.value;
        if(!v){ setFeedback(passFb,pass,'Password wajib diisi.',false); return false; }
        if(v.length < 6){ setFeedback(passFb,pass,'Minimal 6 karakter.',false); return false; }
        setFeedback(passFb,pass,'',false);
        return true;
    }

    if(email){
        email.addEventListener('blur', function(){ validateEmail(false); });
        email.addEventListener('input', function(){
            if(emailFb.classList.contains('show') && !emailFb.classList.contains('ok')) validateEmail(false);
            if(email.classList.contains('is-valid')) email.classList.remove('is-valid');
        });
    }
    if(pass){
        pass.addEventListener('blur', validatePass);
        pass.addEventListener('input', function(){
            if(passFb.classList.contains('show')) validatePass();
            pass.classList.remove('is-invalid');
        });
        pass.addEventListener('focus', function(){ this.style.transition='all .2s ease'; });
    }

    if(form && submitBtn){
        form.addEventListener('submit', function(e){
            var okE = validateEmail(false);
            var okP = validatePass();
            if(!okE || !okP){ e.preventDefault(); var firstInvalid = form.querySelector('.is-invalid'); if(firstInvalid) firstInvalid.focus(); return; }
            var txt = submitBtn.querySelector('.btn-text');
            submitBtn.disabled = true;
            if(txt) txt.textContent = 'Memproses...';
            var ic = submitBtn.querySelector('i.fa-arrow-right');
            if(ic){ ic.className='btn-spinner'; }
        });
    }

    var firstEmpty = form ? form.querySelector('.auth-input') : null;
    if(firstEmpty && !firstEmpty.value) { /* keep focus natural */ }
=======
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
>>>>>>> FinalV1G
});
</script>

<?php include '../includes/footer.php'; ?>
