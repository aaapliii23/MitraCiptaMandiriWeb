<?php
session_start();
require_once '../config/database.php';
require_once '../includes/security.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// Buat tabel password_resets jika belum ada
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

$message = '';
$messageType = ''; // success / error
$isSuccess = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rateKey = 'forgot_' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown') . '_' . strtolower(trim($_POST['email'] ?? ''));
    $rl = mcm_rate_limit($rateKey, 3, 900);
    if (!$rl['allowed']) {
        $message = $rl['message'];
        $messageType = 'error';
    } else {
    $email = trim($_POST['email'] ?? '');
    $postedToken = $_POST['csrf_token'] ?? '';

    if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $postedToken)) {
        $message = 'Sesi tidak valid. Silakan muat ulang halaman.';
        $messageType = 'error';
    } elseif (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Masukkan alamat email yang valid.';
        $messageType = 'error';
    } else {
        // Selalu tampilkan pesan sukses yang sama (anti-enumeration)
        $successMsg = 'Jika email tersebut terdaftar, link reset password telah dikirim. Silakan cek inbox (dan folder Spam). Link berlaku 1 jam.';

        try {
            $stmt = $pdo->prepare("SELECT id, name, email FROM users WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user) {
                // Generate token 64 hex (32 bytes)
                $rawToken = bin2hex(random_bytes(32));
                $tokenHash = hash('sha256', $rawToken);
                $expiresAt = date('Y-m-d H:i:s', time() + 3600); // 1 jam

                // Hapus token lama yang belum dipakai untuk email ini (opsional, biar 1 aktif)
                $pdo->prepare("DELETE FROM password_resets WHERE email = ? AND used_at IS NULL")->execute([$email]);
                $pdo->prepare("INSERT INTO password_resets (email, token_hash, expires_at) VALUES (?, ?, ?)")->execute([$email, $tokenHash, $expiresAt]);

                // Buat link reset — pakai base URL dinamis
                $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';
                $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
                $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/auth/forgot_password.php'));
                // $scriptDir = /MitraCiptaMandiriWeb/auth atau /auth
                $basePath = rtrim(dirname($scriptDir), '/');
                if ($basePath === '/' || $basePath === '.') $basePath = '';
                // Fallback simple: ambil dari HTTP_HOST + /auth/reset_password.php
                $resetLink = ($https ? 'https' : 'http') . '://' . $host . $basePath . '/auth/reset_password.php?token=' . urlencode($rawToken);
                // Jika basePath kosong dan host sudah include subfolder, alternatif: gunakan REQUEST_URI base
                if (strpos($resetLink, '/auth/reset_password.php') === false) {
                    $resetLink = ($https ? 'https' : 'http') . '://' . $host . '/auth/reset_password.php?token=' . urlencode($rawToken);
                    // Coba deteksi subfolder dari SCRIPT_NAME
                    if (preg_match('#^(/.+?)/auth/forgot_password\.php#', $_SERVER['SCRIPT_NAME'] ?? '', $m)) {
                        $resetLink = ($https ? 'https' : 'http') . '://' . $host . $m[1] . '/auth/reset_password.php?token=' . urlencode($rawToken);
                    }
                }

                // Kirim email via mailer
                require_once __DIR__ . '/../includes/mailer.php';
                $subject = 'Reset Password — Mitra Cipta Mandiri';
                $htmlBody = '
                <div style="font-family:Arial,sans-serif;max-width:600px;margin:0 auto;padding:24px;border:1px solid #e2e8f0;border-radius:16px;">
                    <div style="text-align:center;margin-bottom:20px;">
                        <img src="https://via.placeholder.com/120x40?text=MCM" alt="MCM" style="height:40px;">
                        <h2 style="color:#0c4a6e;margin:12px 0 4px;">Reset Password</h2>
                        <p style="color:#64748b;font-size:14px;margin:0;">Mitra Cipta Mandiri — LMS</p>
                    </div>
                    <p>Halo <b>' . htmlspecialchars($user['name']) . '</b>,</p>
                    <p>Kami menerima permintaan reset password untuk akun <b>' . htmlspecialchars($email) . '</b>.</p>
                    <p>Klik tombol di bawah untuk reset password (berlaku <b>1 jam</b>):</p>
                    <p style="text-align:center;margin:24px 0;">
                        <a href="' . htmlspecialchars($resetLink) . '" style="display:inline-block;background:linear-gradient(135deg,#0c4a6e,#0ea5e9);color:#fff;padding:12px 28px;border-radius:999px;text-decoration:none;font-weight:700;">Reset Password</a>
                    </p>
                    <p style="font-size:13px;color:#64748b;">Atau copy link ini ke browser:<br><a href="' . htmlspecialchars($resetLink) . '" style="color:#0ea5e9;word-break:break-all;">' . htmlspecialchars($resetLink) . '</a></p>
                    <p style="font-size:13px;color:#94a3b8;">Link akan kadaluarsa pada ' . htmlspecialchars($expiresAt) . ' WIB. Jika kamu tidak minta reset, abaikan email ini.</p>
                    <hr style="border:none;border-top:1px solid #e2e8f0;margin:20px 0;">
                    <p style="font-size:12px;color:#94a3b8;text-align:center;">Mitra Cipta Mandiri — Platform resmi MCM</p>
                </div>';
                $textBody = "Halo {$user['name']},\n\nKlik link untuk reset password (1 jam): $resetLink\n\nJika tidak minta, abaikan.";

                $res = mcm_send_email($email, $subject, $htmlBody, $textBody);
                if (!$res['success']) {
                    error_log("[FORGOT] Gagal kirim ke $email: " . $res['message']);
                    // Tetap anggap sukses untuk user (jangan bocorkan), tapi log untuk admin
                }
            }
        } catch (Exception $e) {
            error_log("[FORGOT] Error: " . $e->getMessage());
        }
        }

        mcm_rate_limit_hit($rateKey);
        // Selalu sukses di mata user
        $message = $successMsg;
        $messageType = 'success';
        $isSuccess = true;
    } // end rate limit else
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
                    <h2 class="auth-brand-title">Lupa password?</h2>
                    <p class="auth-brand-sub">Tenang, kami bantu pulihkan akses LMS Anda. Link reset akan dikirim ke email terdaftar dan berlaku 1 jam.</p>
                    <ul class="auth-brand-features">
                        <li><i class="fas fa-envelope"></i><span>Link reset dikirim ke email terdaftar</span></li>
                        <li><i class="fas fa-clock"></i><span>Berlaku 1 jam & sekali pakai</span></li>
                        <li><i class="fas fa-shield-alt"></i><span>Tidak bocorkan apakah email terdaftar</span></li>
                    </ul>
                    <div class="auth-brand-visual" aria-hidden="true">
                        <div class="auth-visual-card"><i class="fas fa-key"></i></div>
                        <div class="auth-visual-card small"><i class="fas fa-unlock-alt"></i></div>
                    </div>
                    <div class="auth-brand-foot"><i class="fas fa-shield-alt"></i> Aman & terenkripsi</div>
                </div>
                <div class="auth-branding-deco deco-1"></div>
                <div class="auth-branding-deco deco-2"></div>
            </div>

            <div class="auth-form-panel">
                <div class="auth-form-head">
                    <h3>Reset Password</h3>
                    <p>Masukkan email akun LMS Anda. Kami kirimkan link reset.</p>
                </div>

                <?php if (!empty($message)): ?>
                    <div class="<?php echo $messageType === 'success' ? 'auth-alert' : 'auth-alert'; ?>" role="alert" style="<?php echo $messageType === 'success' ? 'border-color:#bbf7d0;background:#f0fdf4;color:#166534;' : ''; ?>">
                        <i class="fas <?php echo $messageType === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?> me-1"></i> <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <?php if (!$isSuccess): ?>
                <form method="POST" action="forgot_password.php" id="forgotForm" novalidate>
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                    <div class="auth-field">
                        <label class="auth-label" for="forgotEmail"><i class="fas fa-envelope"></i> Email Terdaftar</label>
                        <input type="email" class="form-control auth-input" id="forgotEmail" name="email" required autocomplete="email" placeholder="nama@email.com" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                        <div class="auth-hint">Kami kirim link ke email ini jika terdaftar.</div>
                    </div>
                    <button type="submit" class="btn auth-btn"><span class="btn-text">Kirim Link Reset</span> <i class="fas fa-paper-plane"></i></button>
                    <p class="auth-switch" style="margin-top:18px;"><a href="user_login.php"><i class="fas fa-arrow-left me-1"></i> Kembali ke Masuk</a></p>
                </form>
                <?php else: ?>
                    <p class="auth-switch"><a href="user_login.php"><i class="fas fa-arrow-left me-1"></i> Kembali ke Masuk</a></p>
                <?php endif; ?>
                <p class="auth-terms">Butuh bantuan? Hubungi <a href="mailto:isiEmailMCM">support@mitraciptamandiri.com</a></p>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
