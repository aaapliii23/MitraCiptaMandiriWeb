<?php
if (!isset($csrf_token)) {
    session_start();
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
    require_once 'includes/db_config.php';
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css"/>
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <?php if (!isset($hide_nav_items) || !$hide_nav_items): ?>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top" style="transition: all 0.4s ease;">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <img src="assets/img/logo.png" alt="MCM Logo" style="height: 40px; width: auto; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));">
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
                <ul class="navbar-nav mx-auto align-items-center gap-2">
                    <li class="nav-item"><a class="nav-link" href="#beranda">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="#tentang">Tentang</a></li>
                    <li class="nav-item"><a class="nav-link" href="#galeri">Galeri</a></li>
                    <li class="nav-item"><a class="nav-link" href="#paket">Paket</a></li>
                </ul>
                <div class="my-4 d-lg-none" style="border-top: 1px solid #000000 !important; opacity: 0.15;"></div>
                <div class="mt-3 mt-lg-0 text-center">
                    <a href="programs.php" class="btn rounded-pill fw-bold btn-premium" style="background: linear-gradient(135deg, #0c4a6e, #0ea5e9); color: white; border: none; padding: 10px 28px; box-shadow: 0 10px 20px -5px rgba(14, 165, 233, 0.4); display: inline-block;">Daftar Sekarang</a>
                </div>
            </div>
    </nav>
    <?php endif; ?>
    
    <!-- Admin Login Modal -->
