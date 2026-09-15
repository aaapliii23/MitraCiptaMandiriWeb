<?php
// Navbar bersama LMS. Desktop/tablet (>=768px): tombol horizontal di kanan.
// Mobile (<768px): hamburger membuka menu vertikal full-width (tidak ada tombol terpotong).
// Variabel opsional sebelum include:
//   $lms_nav_active = 'dashboard'|'profile'|'cart' (penanda tombol aktif)
//   $lms_back_url + $lms_back_label (tombol kembali kontekstual, mis. di material.php)
$lms_nav_active = $lms_nav_active ?? '';
$lms_back_url = $lms_back_url ?? '';
$lms_back_label = $lms_back_label ?? '';
$lms_user = htmlspecialchars($_SESSION['user_name'] ?? 'Peserta', ENT_QUOTES, 'UTF-8');
?>
<style>
.lms-nav .navbar-brand { min-width: 0; }
.lms-nav .navbar-toggler { padding: .55rem .75rem; }
@media (max-width: 767.98px) {
    .lms-nav .lms-nav-menu .btn { display: block; width: 100%; padding: .65rem 1rem; font-size: .95rem; }
}
</style>
<nav class="navbar navbar-expand-md fixed-top shadow-sm bg-white lms-nav" style="transition: all 0.4s ease;">
    <div class="container px-3 px-md-4">
        <a class="navbar-brand d-flex align-items-center" href="../index.php" style="min-width: 0;">
            <img src="../assets/img/logo.png" alt="Logo MCM" class="flex-shrink-0" style="height: 40px; width: auto;">
            <span class="fw-bold ms-2 text-truncate" style="font-size: 0.82rem; letter-spacing: 1px;">LMS MITRA CIPTA MANDIRI</span>
        </a>
        <button class="navbar-toggler flex-shrink-0" type="button" data-bs-toggle="collapse" data-bs-target="#lmsNavbar" aria-controls="lmsNavbar" aria-expanded="false" aria-label="Buka menu navigasi" title="Menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="lmsNavbar">
            <div class="lms-nav-menu d-flex flex-column flex-md-row align-items-stretch align-items-md-center gap-2 ms-md-auto py-3 py-md-0">
                <span class="text-muted small text-truncate px-1 d-md-none d-lg-inline"><i class="fas fa-user me-1"></i><?php echo $lms_user; ?></span>
                <?php if ($lms_back_url !== ''): ?>
                    <a href="<?php echo htmlspecialchars($lms_back_url, ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3 text-truncate" aria-label="Kembali"><i class="fas fa-arrow-left me-1"></i><span class="d-none d-lg-inline"><?php echo htmlspecialchars($lms_back_label, ENT_QUOTES, 'UTF-8'); ?></span><span class="d-lg-none">Kembali</span></a>
                <?php endif; ?>
                <a href="dashboard.php" class="btn btn-outline-primary btn-sm rounded-pill px-3<?php echo $lms_nav_active === 'dashboard' ? ' active' : ''; ?>"<?php echo $lms_nav_active === 'dashboard' ? ' aria-current="page"' : ''; ?> aria-label="Dashboard"><i class="fas fa-tachometer-alt me-1"></i><span class="d-none d-lg-inline">Dashboard</span></a>
                <a href="profile.php" class="btn btn-outline-primary btn-sm rounded-pill px-3<?php echo $lms_nav_active === 'profile' ? ' active' : ''; ?>"<?php echo $lms_nav_active === 'profile' ? ' aria-current="page"' : ''; ?> aria-label="Profil"><i class="fas fa-user-cog me-1"></i><span class="d-none d-lg-inline">Profil</span></a>
                <a href="cart.php" class="btn btn-outline-warning btn-sm rounded-pill px-3<?php echo $lms_nav_active === 'cart' ? ' active' : ''; ?>"<?php echo $lms_nav_active === 'cart' ? ' aria-current="page"' : ''; ?> aria-label="Keranjang"><i class="fas fa-shopping-cart me-1"></i><span class="d-none d-lg-inline">Keranjang</span></a>
                <a href="../index.php" class="btn btn-outline-primary btn-sm rounded-pill px-3" aria-label="Beranda"><i class="fas fa-home me-1"></i><span class="d-none d-lg-inline">Beranda</span></a>
                <a href="../auth/user_logout.php" class="btn btn-outline-danger btn-sm rounded-pill px-3" aria-label="Keluar"><i class="fas fa-sign-out-alt me-1"></i><span class="d-none d-lg-inline">Keluar</span></a>
            </div>
        </div>
    </div>
</nav>
