<?php
session_start();
require_once '../config/database.php';
require_once '../includes/security.php';
mcm_cors_headers();
// Rate limit reset: 5x per 15 menit per IP
$rateKey = 'reset_' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
$rl = mcm_rate_limit($rateKey, 5, 900);
if (!$rl['allowed']) { http_response_code(429); die($rl['message']); }

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS `password_resets` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `email` varchar(255) NOT NULL,
        `token_hash` varchar(255) NOT NULL,
        `expires_at` datetime NOT NULL,
        `used_at` datetime DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `email` (`email`),
        KEY `token_hash` (`token_hash`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
} catch (PDOException $e) {}

$rawToken = trim($_GET['token'] ?? $_POST['token'] ?? '');
$tokenHash = $rawToken !== '' ? hash('sha256', $rawToken) : '';

$valid = false;
$email = '';
$errorMessage = '';
$successMessage = '';

if ($rawToken === '' || $tokenHash === '') {
    $errorMessage = 'Token tidak valid.';
} else {
    try {
        $stmt = $pdo->prepare("SELECT * FROM password_resets WHERE token_hash = ? LIMIT 1");
        $stmt->execute([$tokenHash]);
        $row = $stmt->fetch();
        if (!$row) {
            $errorMessage = 'Link reset tidak valid atau sudah kadaluarsa.';
        } elseif (!empty($row['used_at'])) {
            $errorMessage = 'Link ini sudah pernah digunakan. Silakan minta link baru.';
        } elseif (strtotime($row['expires_at']) < time()) {
            $errorMessage = 'Link sudah kadaluarsa (lebih dari 1 jam). Silakan minta link baru.';
        } else {
            $valid = true;
            $email = $row['email'];
        }
    } catch (PDOException $e) {
        $errorMessage = 'Terjadi kesalahan sistem.';
    }
}

// Handle submit new password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $valid) {
    $postedToken = $_POST['csrf_token'] ?? '';
    $newPass = $_POST['password'] ?? '';
    $newPass2 = $_POST['password2'] ?? '';
    $postedRawToken = trim($_POST['token'] ?? '');

    if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $postedToken)) {
        $errorMessage = 'Sesi tidak valid. Muat ulang halaman.';
        $valid = false;
    } elseif (strlen($newPass) < 6) {
        $errorMessage = 'Password minimal 6 karakter.';
    } elseif ($newPass !== $newPass2) {
        $errorMessage = 'Konfirmasi password tidak cocok.';
    } else {
        // Re-validate token (防止 race)
        $ph = hash('sha256', $postedRawToken);
        $stmt = $pdo->prepare("SELECT * FROM password_resets WHERE token_hash = ? LIMIT 1");
        $stmt->execute([$ph]);
        $row = $stmt->fetch();
        if (!$row || !empty($row['used_at']) || strtotime($row['expires_at']) < time()) {
            $errorMessage = 'Token tidak valid atau sudah kadaluarsa.';
            $valid = false;
        } else {
            try {
                $hash = password_hash($newPass, PASSWORD_DEFAULT);
                $pdo->prepare("UPDATE users SET password = ? WHERE email = ?")->execute([$hash, $row['email']]);
                $pdo->prepare("UPDATE password_resets SET used_at = NOW() WHERE token_hash = ?")->execute([$ph]);
                // Hapus token lain yang masih aktif untuk email ini (sekali pakai)
                // $pdo->prepare("DELETE FROM password_resets WHERE email = ? AND token_hash != ?")->execute([$row['email'], $ph]);
                $successMessage = 'Password berhasil diubah. Silakan masuk dengan password baru.';
                $valid = false; // jangan tampilkan form lagi
            } catch (PDOException $e) {
                $errorMessage = 'Gagal update password.';
            }
        }
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
                    <h2 class="auth-brand-title">Buat password baru</h2>
                    <p class="auth-brand-sub">Atur ulang password LMS Anda dengan aman. Link hanya berlaku 1 jam & sekali pakai.</p>
                    <ul class="auth-brand-features">
                        <li><i class="fas fa-check"></i><span>Minimal 6 karakter, konfirmasi harus cocok</span></li>
                        <li><i class="fas fa-shield-alt"></i><span>Token kadaluarsa otomatis setelah 1 jam</span></li>
                        <li><i class="fas fa-key"></i><span>Password langsung terenkripsi</span></li>
                    </ul>
                    <div class="auth-brand-visual" aria-hidden="true">
                        <div class="auth-visual-card"><i class="fas fa-lock"></i></div>
                        <div class="auth-visual-card small"><i class="fas fa-sync-alt"></i></div>
                    </div>
                    <div class="auth-brand-foot"><i class="fas fa-shield-alt"></i> Aman & terenkripsi</div>
                </div>
                <div class="auth-branding-deco deco-1"></div>
                <div class="auth-branding-deco deco-2"></div>
            </div>

            <div class="auth-form-panel">
                <div class="auth-form-head">
                    <h3>Atur Password Baru</h3>
                    <?php if ($email): ?><p>Untuk akun <b><?php echo htmlspecialchars($email); ?></b></p><?php else: ?><p>Masukkan password baru Anda.</p><?php endif; ?>
                </div>

                <?php if (!empty($successMessage)): ?>
                    <div class="auth-alert" role="alert" style="border-color:#bbf7d0;background:#f0fdf4;color:#166534;"><i class="fas fa-check-circle me-1"></i> <?php echo htmlspecialchars($successMessage); ?></div>
                    <p class="auth-switch"><a href="user_login.php">Masuk sekarang</a></p>
                <?php elseif (!$valid): ?>
                    <div class="auth-alert" role="alert"><i class="fas fa-exclamation-circle me-1"></i> <?php echo htmlspecialchars($errorMessage ?: 'Link tidak valid.'); ?></div>
                    <p class="auth-switch"><a href="forgot_password.php">Minta link baru</a></p>
                    <p class="auth-switch"><a href="user_login.php"><i class="fas fa-arrow-left me-1"></i> Kembali ke Masuk</a></p>
                <?php else: ?>
                    <?php if (!empty($errorMessage)): ?>
                        <div class="auth-alert" role="alert"><i class="fas fa-exclamation-circle me-1"></i> <?php echo htmlspecialchars($errorMessage); ?></div>
                    <?php endif; ?>
                    <form method="POST" action="reset_password.php" id="resetForm" novalidate>
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                        <input type="hidden" name="token" value="<?php echo htmlspecialchars($rawToken); ?>">
                        <div class="auth-field">
                            <label class="auth-label" for="newPass"><i class="fas fa-lock"></i> Password Baru</label>
                            <div class="auth-input-group">
                                <input type="password" class="form-control auth-input" name="password" id="newPass" required minlength="6" autocomplete="new-password" placeholder="Minimal 6 karakter">
                                <button class="auth-eye-btn" type="button" id="toggleNewPass" tabindex="-1" aria-label="Lihat password"><i class="fas fa-eye"></i></button>
                            </div>
                            <div class="pw-strength" id="pwStrength"><span></span><span></span><span></span><span></span></div>
                            <div class="pw-strength-label" id="pwStrengthLabel"></div>
                        </div>
                        <div class="auth-field mb-lg">
                            <label class="auth-label" for="newPass2"><i class="fas fa-lock"></i> Konfirmasi Password</label>
                            <div class="auth-input-group">
                                <input type="password" class="form-control auth-input" name="password2" id="newPass2" required minlength="6" autocomplete="new-password" placeholder="Ulangi password">
                                <button class="auth-eye-btn" type="button" id="toggleNewPass2" tabindex="-1"><i class="fas fa-eye"></i></button>
                            </div>
                            <div class="auth-feedback" id="pass2Fb"></div>
                        </div>
                        <button type="submit" class="btn auth-btn"><span class="btn-text">Simpan Password Baru</span> <i class="fas fa-check"></i></button>
                        <p class="auth-switch" style="margin-top:16px;"><a href="user_login.php">Batal, kembali ke Masuk</a></p>
                    </form>
                <?php endif; ?>
                <p class="auth-terms">Butuh bantuan? Hubungi <a href="mailto:Isi Email MCM">support@mitraciptamandiri.com</a></p>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function(){
    function setupToggle(btnId, inpId){
        var b=document.getElementById(btnId), inp=document.getElementById(inpId);
        if(!b||!inp) return;
        b.addEventListener('click', function(){
            var isPw=inp.type==='password';
            inp.type=isPw?'text':'password';
            var ic=this.querySelector('i');
            if(ic){ ic.classList.toggle('fa-eye'); ic.classList.toggle('fa-eye-slash'); }
            this.style.color=isPw?'#0ea5e9':'#94a3b8';
        });
    }
    setupToggle('toggleNewPass','newPass');
    setupToggle('toggleNewPass2','newPass2');
    // strength meter
    var pw=document.getElementById('newPass'), bar=document.getElementById('pwStrength'), lab=document.getElementById('pwStrengthLabel');
    function score(p){ var s=0; if(p.length>=6)s++; if(p.length>=8)s++; if(/[a-z]/.test(p)&&/[A-Z]/.test(p))s++; if(/\d/.test(p))s++; if(/[^A-Za-z0-9]/.test(p))s++; return Math.min(s,4); }
    if(pw&&bar&&lab){
        pw.addEventListener('input', function(){
            var v=this.value; if(!v){ bar.querySelectorAll('span').forEach(function(sp){sp.style.background='#e2e8f0';}); lab.textContent=''; return; }
            var sc=score(v); var colors=['#ef4444','#f59e0b','#0ea5e9','#16a34a']; var labels=['Lemah','Cukup','Baik','Kuat']; var cls=['weak','fair','good','strong'];
            bar.querySelectorAll('span').forEach(function(sp,i){ sp.style.background=i<sc?colors[sc-1]:'#e2e8f0'; });
            if(sc===0){ lab.textContent='Terlalu pendek'; lab.className='pw-strength-label weak'; } else { lab.textContent=labels[sc-1]; lab.className='pw-strength-label '+cls[sc-1]; }
        });
    }
    var p2=document.getElementById('newPass2'), fb=document.getElementById('pass2Fb');
    if(p2&&fb){
        p2.addEventListener('input', function(){
            var a=document.getElementById('newPass').value, b=this.value;
            if(!b){ fb.textContent=''; fb.className='auth-feedback'; return; }
            if(a!==b){ fb.textContent='Tidak cocok.'; fb.className='auth-feedback show'; } else { fb.textContent='Cocok.'; fb.className='auth-feedback show ok'; }
        });
    }
});
</script>

<?php include '../includes/footer.php'; ?>
