<!-- Sidebar -->
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
    
    <ul class="nav flex-column mb-auto px-2">
        <li class="nav-item mb-1">
            <a href="?page=dashboard" class="nav-link <?php echo $page == 'dashboard' ? 'active' : ''; ?>">
                <i class="fas fa-th-large"></i> Dashboard
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="?page=orders" class="nav-link <?php echo $page == 'orders' ? 'active' : ''; ?>">
                <i class="fas fa-shopping-cart"></i> Pesanan & Transaksi
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="?page=bookings" class="nav-link <?php echo $page == 'bookings' ? 'active' : ''; ?>">
                <i class="fas fa-calendar-check"></i> Form Booking / Kontak
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="?page=classes" class="nav-link <?php echo $page == 'classes' ? 'active' : ''; ?>">
                <i class="fas fa-book-open"></i> Paket Pelatihan
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="?page=gallery" class="nav-link <?php echo $page == 'gallery' ? 'active' : ''; ?>">
                <i class="fas fa-camera-retro"></i> Galeri Foto
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="?page=instructors" class="nav-link <?php echo $page == 'instructors' ? 'active' : ''; ?>">
                <i class="fas fa-user-tie"></i> Instruktur & Penguji
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="?page=certs" class="nav-link <?php echo $page == 'certs' ? 'active' : ''; ?>">
                <i class="fas fa-certificate"></i> Legalitas & Sertifikasi
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="?page=reports" class="nav-link <?php echo $page == 'reports' ? 'active' : ''; ?>">
                <i class="fas fa-chart-line"></i> Laporan & Rekap
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="?page=categories" class="nav-link <?php echo $page == 'categories' ? 'active' : ''; ?>">
                <i class="fas fa-tags"></i> Kategori Pelatihan
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="?page=testimonials" class="nav-link <?php echo $page == 'testimonials' ? 'active' : ''; ?>">
                <i class="fas fa-comment-dots"></i> Testimoni
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="?page=examiners" class="nav-link <?php echo $page == 'examiners' ? 'active' : ''; ?>">
                <i class="fas fa-user-check"></i> Penguji
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="?page=materials" class="nav-link <?php echo $page == 'materials' ? 'active' : ''; ?>">
                <i class="fas fa-graduation-cap"></i> Materi LMS
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="?page=chat" class="nav-link <?php echo $page == 'chat' ? 'active' : ''; ?>">
                <i class="fab fa-whatsapp"></i> Chat WhatsApp
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="?page=chatbot" class="nav-link <?php echo $page == 'chatbot' ? 'active' : ''; ?>">
                <i class="fas fa-robot"></i> Chatbot & Balasan
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="?page=admins" class="nav-link <?php echo $page == 'admins' ? 'active' : ''; ?>">
                <i class="fas fa-users-cog"></i> Kelola Admin
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="?page=settings" class="nav-link <?php echo $page == 'settings' ? 'active' : ''; ?>">
                <i class="fas fa-cog"></i> Pengaturan Web
            </a>
        </li>
    </ul>
    
    <div class="p-4 mt-auto border-top border-secondary border-opacity-25">
        <a href="logout.php" class="btn btn-danger w-100 fw-bold shadow-sm" style="background: rgba(220, 53, 69, 0.9);"><i class="fas fa-power-off me-2"></i>Keluar Sistem</a>
    </div>
</div>
