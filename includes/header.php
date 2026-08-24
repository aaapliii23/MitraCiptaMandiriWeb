<?php
$script_file = str_replace('\\', '/', $_SERVER['SCRIPT_FILENAME'] ?? '');
$app_root = str_replace('\\', '/', dirname(__DIR__));
if ($script_file !== '' && strpos($script_file, $app_root) === 0) {
    $rel = trim(substr(str_replace('\\', '/', dirname($script_file)), strlen($app_root)), '/');
    $base_url = ($rel === '') ? '' : str_repeat('../', substr_count($rel, '/') + 1);
} else {
    $script_dir = dirname($_SERVER['SCRIPT_NAME'] ?? '/');
    $base_url = ($script_dir === '/' || $script_dir === '' || $script_dir === '\\') ? '' : str_repeat('../', substr_count(rtrim($script_dir, '/'), '/'));
}
$is_home = basename($_SERVER['SCRIPT_FILENAME'] ?? '') === 'index.php';
if (!isset($csrf_token)) {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    $csrf_token = $_SESSION['csrf_token'];
}

// Fetch Classes if not already available
?>
<style>
    .navbar-toggler {
        cursor: pointer !important;
        -webkit-tap-highlight-color: transparent !important;
    }
</style>
<?php
if (!isset($classItems)) {
    require_once __DIR__ . '/../config/database.php';
    try {
        $stmt = $pdo->query("SELECT * FROM classes ORDER BY id ASC");
        $classItems = $stmt->fetchAll();
    } catch(PDOException $e) { $classItems = []; }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MCM - Mitra Cipta Mandiri</title>
    <link rel="icon" type="image/png" href="<?php echo $base_url; ?>assets/img/logo.png">
    <link rel="shortcut icon" type="image/png" href="<?php echo $base_url; ?>assets/img/logo.png">
    <link rel="apple-touch-icon" href="<?php echo $base_url; ?>assets/img/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css"/>
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/style.css?v=<?php echo time(); ?>">
</head>
<body>
    <?php if (!isset($hide_nav_items) || !$hide_nav_items): ?>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top<?php echo $is_home ? '' : ' navbar-solid'; ?>" style="transition: all 0.4s ease;">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="<?php echo $base_url; ?>index.php">
                <img src="<?php echo $base_url; ?>assets/img/logo.png" alt="MCM Logo" style="height: 40px; width: auto; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));">
                <div class="ms-2 ps-2 border-start border-2 brand-divider d-flex flex-column justify-content-center" style="height: 35px;">
                    <span class="fw-bold brand-text" style="font-size: 0.75rem; letter-spacing: 1px; line-height: 1.1;">MITRA CIPTA</span>
                    <span class="fw-bold brand-text" style="font-size: 0.75rem; letter-spacing: 1px; line-height: 1.1;">MANDIRI</span>
                </div>
            </a>
            <button class="navbar-toggler" type="button" id="mcmMainToggler">
                <span class="navbar-toggler-icon"></span>
            </button>
            <script>
                document.getElementById('mcmMainToggler')?.addEventListener('click', function() {
                    const target = document.getElementById('navbarNav');
                    if (target.classList.contains('show')) {
                        target.classList.remove('show');
                    } else {
                        target.classList.add('show');
                    }
                });
            </script>
            <div class="collapse navbar-collapse" id="navbarNav">
                <?php $nav_sections = [['beranda', 'Beranda'], ['tentang', 'Tentang'], ['galeri', 'Galeri'], ['paket', 'Paket'], ['testimoni', 'Testimoni']]; ?>
                <ul class="navbar-nav mx-auto align-items-center gap-2">
                    <?php foreach ($nav_sections as $ns): ?>
                        <?php $href = ($ns[0] === 'tentang') ? $base_url . 'pages/about.php' : ($is_home ? '#' . $ns[0] : $base_url . 'index.php#' . $ns[0]); ?>
                        <li class="nav-item"><a class="nav-link" href="<?php echo $href; ?>"><?php echo $ns[1]; ?></a></li>
                    <?php endforeach; ?>
                </ul>
                <div class="my-4 d-lg-none" style="border-top: 1px solid #000000 !important; opacity: 0.15;"></div>
                <div class="mt-3 mt-lg-0 text-center d-flex flex-column flex-lg-row gap-2 align-items-center">
                    <?php if (!empty($_SESSION['user_logged_in'])): ?>
                        <div class="d-flex align-items-center gap-2">
                            <a href="<?php echo $base_url; ?>lms/dashboard.php" class="btn rounded-pill fw-bold btn-premium" style="background: linear-gradient(135deg, #0c4a6e, #0ea5e9); color: white; border: none; padding: 10px 24px; box-shadow: 0 10px 20px -5px rgba(14, 165, 233, 0.4); display: inline-flex; align-items: center; justify-content: center;"><i class="fas fa-graduation-cap me-2"></i>LMS Saya</a>
                            <span class="small text-muted d-none d-lg-inline-flex align-items-center"><?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?></span>
                        </div>
                        <a href="<?php echo $base_url; ?>auth/user_logout.php" class="btn btn-outline-secondary rounded-pill fw-bold" style="padding: 8px 20px;">Keluar</a>
                    <?php else: ?>
                        <a href="<?php echo $base_url; ?>auth/user_login.php" class="btn btn-outline-primary rounded-pill fw-bold" style="padding: 8px 22px;">Masuk</a>
                        <a href="<?php echo $base_url; ?>auth/user_register.php" class="btn rounded-pill fw-bold btn-premium" style="background: linear-gradient(135deg, #0c4a6e, #0ea5e9); color: white; border: none; padding: 10px 28px; box-shadow: 0 10px 20px -5px rgba(14, 165, 233, 0.4); display: inline-block;">Daftar Sekarang</a>
                    <?php endif; ?>
                </div>
            </div>
    </nav>
    <?php endif; ?>
    
    <!-- Admin Login Modal -->
