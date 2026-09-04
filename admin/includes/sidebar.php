<!-- Sidebar -->
<?php
$groupMap = ['dashboard' => 'utama', 'classes' => 'program', 'categories' => 'program', 'instructors' => 'program', 'materials' => 'program', 'users' => 'program', 'gallery' => 'program', 'facilities' => 'program', 'certs' => 'program', 'orders' => 'penjualan', 'finance' => 'penjualan', 'testimonials' => 'penjualan', 'chat' => 'penjualan', 'chatbot' => 'penjualan', 'reports' => 'laporan', 'admins' => 'sistem', 'settings' => 'sistem', 'doku_channels' => 'sistem'];
$activeGroup = $groupMap[$page] ?? 'utama';
function mcm_group($id, $label, $key, $activeGroup) {
    $open = $activeGroup === $key ? ' show' : '';
    $expanded = $activeGroup === $key ? 'true' : 'false';
    return '<button class="sidebar-group-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#grp-' . $id . '" aria-expanded="' . $expanded . '">'
        . '<span>' . $label . '</span><i class="fas fa-chevron-down"></i></button>'
        . '<div class="collapse' . $open . '" id="grp-' . $id . '">';
}
function mcm_item($page, $target, $icon, $label) {
    return '<li class="nav-item mb-1"><a href="?page=' . $target . '" class="nav-link ' . ($page === $target ? 'active' : '') . '">'
        . '<i class="' . $icon . '"></i> ' . $label . '</a></li>';
}
?>
<div class="sidebar d-flex flex-column shadow-lg" id="sidebar">
    <div class="p-4 d-flex align-items-center justify-content-between border-bottom border-secondary border-opacity-25">
        <a class="text-decoration-none d-flex align-items-center" href="#">
            <img src="../assets/img/logo.png" alt="MCM Logo" height="40" class="me-2">
            <div class="brand-text">
                <div class="fw-bold fs-6 text-white" style="letter-spacing: 1px; line-height: 1.1;">MITRA CIPTA</div>
                <div class="text-white-50" style="letter-spacing: 1.5px; font-size: 0.75rem;">MANDIRI</div>
            </div>
        </a>
        <button class="btn btn-link text-white-50 d-md-none p-0" id="closeSidebar"><i class="fas fa-times fs-5"></i></button>
    </div>
    
    <div class="px-4 py-4 mb-2 text-center">
        <div class="bg-gradient bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow" style="width: 70px; height: 70px; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));">
            <i class="fas fa-user-shield fs-2 text-white"></i>
        </div>
        <div class="text-white fw-bold fs-5"><?php echo htmlspecialchars($_SESSION['admin_username']); ?></div>
        <div class="badge bg-warning text-dark mt-1 px-3 rounded-pill">Superadmin</div>
    </div>
    
    <div class="sidebar-nav mb-auto">
        <?php echo mcm_group('utama', 'Utama', 'utama', $activeGroup); ?>
        <ul class="nav flex-column px-2">
            <?php echo mcm_item($page, 'dashboard', 'fas fa-th-large', 'Dashboard'); ?>
        </ul>
        </div>
        <?php echo mcm_group('program', 'Program &amp; Konten', 'program', $activeGroup); ?>
        <ul class="nav flex-column px-2">
            <?php echo mcm_item($page, 'classes', 'fas fa-book-open', 'Paket Pelatihan'); ?>
            <?php echo mcm_item($page, 'categories', 'fas fa-tags', 'Kategori Pelatihan'); ?>
            <?php echo mcm_item($page, 'instructors', 'fas fa-user-tie', 'Instruktur &amp; Asesor'); ?>
            <?php echo mcm_item($page, 'materials', 'fas fa-graduation-cap', 'Materi LMS'); ?>
            <?php echo mcm_item($page, 'users', 'fas fa-users', 'Peserta Terdaftar'); ?>
            <?php echo mcm_item($page, 'gallery', 'fas fa-camera-retro', 'Galeri Foto'); ?>
            <?php echo mcm_item($page, 'facilities', 'fas fa-building', 'Foto Lokasi &amp; Fasilitas'); ?>
            <?php echo mcm_item($page, 'certs', 'fas fa-certificate', 'Legalitas &amp; Sertifikasi'); ?>
        </ul>
        </div>
        <?php echo mcm_group('penjualan', 'Penjualan &amp; Pelanggan', 'penjualan', $activeGroup); ?>
        <ul class="nav flex-column px-2">
            <?php echo mcm_item($page, 'orders', 'fas fa-shopping-cart', 'Pesanan &amp; Transaksi'); ?>
            <?php echo mcm_item($page, 'finance', 'fas fa-money-bill-wave', 'Keuangan'); ?>
            <?php echo mcm_item($page, 'testimonials', 'fas fa-comment-dots', 'Testimoni'); ?>
            <?php
            $chatUnread = 0;
            try {
                if (isset($pdo)) {
                    $chatUnread = (int)$pdo->query("SELECT COUNT(*) FROM (SELECT MAX(id) AS mid FROM chat_messages GROUP BY wa_number) t JOIN chat_messages m ON m.id=t.mid WHERE m.direction='in' AND m.sender_type='visitor'")->fetchColumn();
                }
            } catch (Exception $e) {}
            ?>
            <li class="nav-item mb-1"><a href="?page=chat" class="nav-link <?php echo $page==='chat'?'active':''; ?>"><i class="fab fa-whatsapp"></i> Chat WhatsApp <?php if($chatUnread>0) echo '<span class="badge bg-danger rounded-pill ms-2">'.$chatUnread.'</span>'; ?></a></li>
            <?php echo mcm_item($page, 'chatbot', 'fas fa-robot', 'Chatbot &amp; Balasan'); ?>
        </ul>
        </div>
        <?php echo mcm_group('laporan', 'Laporan', 'laporan', $activeGroup); ?>
        <ul class="nav flex-column px-2">
            <?php echo mcm_item($page, 'reports', 'fas fa-chart-line', 'Laporan &amp; Rekap'); ?>
        </ul>
        </div>
        <?php echo mcm_group('sistem', 'Sistem', 'sistem', $activeGroup); ?>
        <ul class="nav flex-column px-2">
            <?php echo mcm_item($page, 'admins', 'fas fa-users-cog', 'Kelola Admin'); ?>
            <?php echo mcm_item($page, 'settings', 'fas fa-cog', 'Pengaturan Web'); ?>
            <?php echo mcm_item($page, 'doku_channels', 'fas fa-credit-card', 'Cek Channel DOKU'); ?>
        </ul>
        </div>
    </div>
    
    <div class="p-4 mt-auto border-top border-secondary border-opacity-25">
        <a href="logout.php" class="btn btn-danger w-100 fw-bold shadow-sm" style="background: rgba(220, 53, 69, 0.9);"><i class="fas fa-power-off me-2"></i>Keluar Sistem</a>
    </div>
</div>
