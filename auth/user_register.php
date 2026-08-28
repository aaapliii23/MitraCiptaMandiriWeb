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
                $cv = preg_replace('/[^a-f0-9]/', '', strtolower($_POST['chat_visitor_id'] ?? ''));
                if ($cv !== '' && strlen($cv) === 12) {
                    try { $pdo->prepare("UPDATE chat_messages SET user_id=? WHERE wa_number=? AND (user_id IS NULL OR user_id=0)")->execute([$userId, 'web-'.$cv]); } catch (Exception $e) {}
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
                    <h2 class="auth-brand-title">Tingkatkan kompetensi Anda bersama Mitra Cipta Mandiri</h2>
                    <p class="auth-brand-sub">Bergabunglah dengan ribuan peserta yang telah meraih sertifikat resmi & skill praktis siap kerja.</p>
                    <ul class="auth-brand-features">
                        <li><i class="fas fa-check"></i><span>Pelatihan vokasi premium — materi praktis & instruktur berpengalaman</span></li>
                        <li><i class="fas fa-certificate"></i><span>Sertifikat resmi bernomor unik + verifikasi QR online</span></li>
                        <li><i class="fas fa-users"></i><span>Komunitas alumni & pendampingan karir berkelanjutan</span></li>
                    </ul>
                    <div class="auth-brand-visual" aria-hidden="true">
                        <div class="auth-visual-card"><i class="fas fa-chalkboard-teacher"></i></div>
                        <div class="auth-visual-card small"><i class="fas fa-award"></i></div>
                    </div>
                    <div class="auth-brand-foot"><i class="fas fa-star" style="color:#fbbf24"></i> Dipercaya 5000+ alumni di seluruh Indonesia</div>
                </div>
                <div class="auth-branding-deco deco-1"></div>
                <div class="auth-branding-deco deco-2"></div>
            </div>

            <div class="auth-form-panel">
                <div class="auth-form-head">
                    <h3>Buat Akun Peserta</h3>
                    <p>Daftar untuk mengakses LMS setelah pembayaran lunas. Akun dibuat otomatis saat checkout juga.</p>
                </div>

                <?php if (!empty($errorMessage)): ?>
                    <div class="auth-alert" role="alert"><i class="fas fa-exclamation-circle me-1"></i> <?php echo htmlspecialchars($errorMessage); ?></div>
                <?php endif; ?>

                <form method="POST" action="user_register.php" id="authRegisterForm" novalidate>
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                    <input type="hidden" name="chat_visitor_id" id="chatVisitorIdReg" value="">

                    <div class="auth-field">
                        <label class="auth-label" for="authName"><i class="fas fa-user"></i> Nama Asli Lengkap</label>
                        <input type="text" class="form-control auth-input" id="authName" name="name" required autocomplete="name" minlength="3" maxlength="100" pattern="[A-Za-z\u00C0-\u017F\s-]+" placeholder="Contoh: Siti Aminah" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                        <div class="auth-feedback" id="nameFeedback"></div>
                        <div class="auth-hint">Hanya huruf, spasi & tanda hubung — sesuai ijazah untuk cetak sertifikat.</div>
                    </div>

                    <div class="auth-field">
                        <label class="auth-label" for="authEmail"><i class="fas fa-envelope"></i> Email</label>
                        <input type="email" class="form-control auth-input" id="authEmail" name="email" required autocomplete="email" placeholder="nama@email.com" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                        <div class="auth-feedback" id="emailFeedback"></div>
                    </div>

                    <div class="auth-field">
                        <label class="auth-label" for="authPhone"><i class="fab fa-whatsapp"></i> No. WhatsApp</label>
                        <input type="tel" class="form-control auth-input" id="authPhone" name="phone" required autocomplete="tel" placeholder="Contoh: 081234567890" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                        <div class="auth-feedback" id="phoneFeedback"></div>
                    </div>

                    <div class="auth-field">
                        <label class="auth-label" for="password"><i class="fas fa-lock"></i> Password</label>
                        <div class="auth-input-group">
                            <input type="password" class="form-control auth-input" name="password" id="password" required minlength="6" autocomplete="new-password" placeholder="Minimal 6 karakter">
                            <button class="auth-eye-btn" type="button" id="togglePassword" tabindex="-1" aria-label="Lihat password"><i class="fas fa-eye"></i></button>
                        </div>
                        <div class="pw-strength" id="pwStrength"><span></span><span></span><span></span><span></span></div>
                        <div class="pw-strength-label" id="pwStrengthLabel"></div>
                        <div class="auth-feedback" id="passFeedback"></div>
                    </div>

                    <div class="auth-field mb-lg">
                        <label class="auth-label" for="password2"><i class="fas fa-lock"></i> Konfirmasi Password</label>
                        <div class="auth-input-group">
                            <input type="password" class="form-control auth-input" name="password2" id="password2" required minlength="6" autocomplete="new-password" placeholder="Ulangi password">
                            <button class="auth-eye-btn" type="button" id="togglePassword2" tabindex="-1" aria-label="Lihat konfirmasi password"><i class="fas fa-eye"></i></button>
                        </div>
                        <div class="auth-feedback" id="pass2Feedback"></div>
                    </div>

                    <button type="submit" class="btn auth-btn" id="authSubmitBtn"><span class="btn-text">Daftar</span> <i class="fas fa-arrow-right"></i></button>

                    <p class="auth-switch">Sudah punya akun? <a href="user_login.php">Masuk di sini</a></p>
                    <p class="auth-terms">Dengan mendaftar, Anda menyetujui <a href="#">Syarat Layanan</a> & <a href="#">Kebijakan Privasi</a> MCM.</p>
                </form>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var nameI = document.getElementById('authName');
    var emailI = document.getElementById('authEmail');
    var phoneI = document.getElementById('authPhone');
    var pw = document.getElementById('password');
    var pw2 = document.getElementById('password2');
    var nameFb = document.getElementById('nameFeedback');
    var emailFb = document.getElementById('emailFeedback');
    var phoneFb = document.getElementById('phoneFeedback');
    var passFb = document.getElementById('passFeedback');
    var pass2Fb = document.getElementById('pass2Feedback');
    var strengthBar = document.getElementById('pwStrength');
    var strengthLabel = document.getElementById('pwStrengthLabel');
    var form = document.getElementById('authRegisterForm');
    var submitBtn = document.getElementById('authSubmitBtn');

    function setFb(el, input, msg, ok){
        if(!el) return;
        if(!msg){ el.textContent=''; el.className='auth-feedback'; if(input) input.classList.remove('is-invalid','is-valid'); return; }
        el.textContent = msg;
        el.className = ok ? 'auth-feedback show ok' : 'auth-feedback show';
        if(input){ input.classList.toggle('is-invalid', !ok); input.classList.toggle('is-valid', !!ok); }
    }

    function setupToggle(btnId, inputId){
        var btn=document.getElementById(btnId), inp=document.getElementById(inputId);
        if(!btn||!inp) return;
        btn.addEventListener('click', function(){
            var isPw = inp.getAttribute('type')==='password';
            inp.setAttribute('type', isPw ? 'text' : 'password');
            var ic=this.querySelector('i');
            if(ic){ ic.classList.toggle('fa-eye'); ic.classList.toggle('fa-eye-slash'); }
            this.style.color = isPw ? '#0ea5e9' : '#94a3b8';
        });
        btn.addEventListener('mouseenter', function(){ this.style.color='#0ea5e9'; });
        btn.addEventListener('mouseleave', function(){
            var isText = inp.getAttribute('type')==='text';
            this.style.color = isText ? '#0ea5e9' : '#94a3b8';
        });
    }
    setupToggle('togglePassword','password');
    setupToggle('togglePassword2','password2');

    function validateName(showOk){
        if(!nameI) return true;
        var v=nameI.value.trim();
        if(!v){ setFb(nameFb,nameI,'Nama wajib diisi.',false); return false; }
        if(v.length<3){ setFb(nameFb,nameI,'Minimal 3 karakter.',false); return false; }
        if(v.length>100){ setFb(nameFb,nameI,'Maksimal 100 karakter.',false); return false; }
        var re=/^[\p{L}]+(?:[ \-][\p{L}]+)*$/u;
        try{ if(!re.test(v)){ setFb(nameFb,nameI,'Hanya huruf, spasi & tanda hubung.',false); return false; } }catch(e){
            if(!/^[A-Za-z\u00C0-\u024F\s-]+$/.test(v)){ setFb(nameFb,nameI,'Hanya huruf, spasi & tanda hubung.',false); return false; }
        }
        if(showOk) setFb(nameFb,nameI,'Nama valid.',true); else setFb(nameFb,nameI,'',false);
        return true;
    }
    function validateEmail(){
        if(!emailI) return true;
        var v=emailI.value.trim();
        if(!v){ setFb(emailFb,emailI,'Email wajib diisi.',false); return false; }
        var re=/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
        if(!re.test(v)){ setFb(emailFb,emailI,'Format email tidak valid.',false); return false; }
        setFb(emailFb,emailI,'',false); emailI.classList.remove('is-invalid'); emailI.classList.add('is-valid');
        setTimeout(function(){ if(emailI.value.trim()===v) { emailI.classList.remove('is-valid'); } }, 1500);
        return true;
    }
    function validatePhone(){
        if(!phoneI) return true;
        var v=phoneI.value.trim();
        if(!v){ setFb(phoneFb,phoneI,'No. WhatsApp wajib diisi.',false); return false; }
        var digits=v.replace(/\D/g,'');
        if(digits.length<10){ setFb(phoneFb,phoneI,'Nomor terlalu pendek.',false); return false; }
        if(digits.length>15){ setFb(phoneFb,phoneI,'Nomor terlalu panjang.',false); return false; }
        setFb(phoneFb,phoneI,'',false); phoneI.classList.remove('is-invalid');
        return true;
    }
    function scorePassword(p){
        var s=0;
        if(p.length>=6) s++;
        if(p.length>=8) s++;
        if(/[a-z]/.test(p) && /[A-Z]/.test(p)) s++;
        if(/\d/.test(p)) s++;
        if(/[^A-Za-z0-9]/.test(p)) s++;
        return Math.min(s,4);
    }
    function updateStrength(){
        if(!pw || !strengthBar || !strengthLabel) return 0;
        var v=pw.value;
        if(!v){
            strengthBar.querySelectorAll('span').forEach(function(sp){ sp.style.background='#e2e8f0'; });
            strengthLabel.textContent=''; strengthLabel.className='pw-strength-label';
            return 0;
        }
        var sc=scorePassword(v);
        var colors=['#ef4444','#f59e0b','#0ea5e9','#16a34a'];
        var labels=['Lemah','Cukup','Baik','Kuat'];
        var cls=['weak','fair','good','strong'];
        var spans=strengthBar.querySelectorAll('span');
        spans.forEach(function(sp,i){
            sp.style.background = i < sc ? colors[sc-1] : '#e2e8f0';
            sp.style.opacity = i < sc ? '1' : '.9';
        });
        if(sc===0){
            // length <6 but has something -> weak
            spans[0].style.background='#ef4444';
            strengthLabel.textContent='Terlalu pendek (min 6)';
            strengthLabel.className='pw-strength-label weak';
            return 0;
        }
        strengthLabel.textContent = labels[sc-1];
        strengthLabel.className = 'pw-strength-label ' + cls[sc-1];
        return sc;
    }
    function validatePass(){
        if(!pw) return true;
        var v=pw.value;
        if(!v){ setFb(passFb,pw,'Password wajib diisi.',false); return false; }
        if(v.length<6){ setFb(passFb,pw,'Minimal 6 karakter.',false); return false; }
        setFb(passFb,pw,'',false);
        return true;
    }
    function validatePass2(){
        if(!pw2) return true;
        var v=pw2.value, p=pw ? pw.value : '';
        if(!v){ setFb(pass2Fb,pw2,'Konfirmasi wajib diisi.',false); return false; }
        if(v!==p){ setFb(pass2Fb,pw2,'Tidak cocok dengan password.',false); return false; }
        setFb(pass2Fb,pw2,'Password cocok.',true);
        return true;
    }

    if(nameI){
        nameI.addEventListener('blur', function(){ validateName(false); });
        nameI.addEventListener('input', function(){
            if(nameFb.classList.contains('show') && !nameFb.classList.contains('ok')) validateName(false);
            updateStrength();
        });
    }
    if(emailI){
        emailI.addEventListener('blur', validateEmail);
        emailI.addEventListener('input', function(){
            if(emailFb.classList.contains('show') && !emailFb.classList.contains('ok')) validateEmail();
            else emailI.classList.remove('is-invalid','is-valid');
        });
    }
    if(phoneI){
        phoneI.addEventListener('blur', validatePhone);
        phoneI.addEventListener('input', function(){ phoneI.classList.remove('is-invalid'); if(phoneFb.classList.contains('show')) validatePhone(); });
    }
    if(pw){
        pw.addEventListener('input', function(){
            updateStrength();
            if(passFb.classList.contains('show')) validatePass();
            if(pw2 && pw2.value) validatePass2();
        });
        pw.addEventListener('blur', function(){ validatePass(); updateStrength(); });
    }
    if(pw2){
        pw2.addEventListener('input', validatePass2);
        pw2.addEventListener('blur', validatePass2);
    }

    if(form && submitBtn){
        form.addEventListener('submit', function(e){
            var ok = true;
            if(!validateName(false)) ok=false;
            if(!validateEmail()) ok=false;
            if(!validatePhone()) ok=false;
            if(!validatePass()) ok=false;
            if(!validatePass2()) ok=false;
            if(!ok){ e.preventDefault(); var first=form.querySelector('.is-invalid'); if(first) first.focus(); return; }
            var cv = localStorage.getItem('mcmChatVid') || (document.cookie.match(/(?:^|; )mcmChatVid=([a-f0-9]{12})/) || [])[1] || '';
            var cvEl = document.getElementById('chatVisitorIdReg');
            if (cvEl) cvEl.value = cv;
            var txt=submitBtn.querySelector('.btn-text');
            submitBtn.disabled=true;
            if(txt) txt.textContent='Memproses...';
            var ic=submitBtn.querySelector('i.fa-arrow-right');
            if(ic) ic.className='btn-spinner';
        });
    }
    // init hidden chat id on load
    var cvInit = localStorage.getItem('mcmChatVid') || (document.cookie.match(/(?:^|; )mcmChatVid=([a-f0-9]{12})/) || [])[1] || '';
    var elInit = document.getElementById('chatVisitorIdReg');
    if (elInit) elInit.value = cvInit;
});
</script>

<?php include '../includes/footer.php'; ?>
