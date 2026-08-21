    <!-- Mobile Navigation Drawer (Offcanvas) -->
    <div class="offcanvas offcanvas-start border-0 shadow-lg" tabindex="-1" id="adminOffcanvas" style="width: 320px; background-color: #0f172a; color: #fff;">
        <div class="offcanvas-header py-3 px-4 border-bottom border-secondary border-opacity-25 d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <img src="../assets/img/logo.png" alt="MCM Logo" height="32" class="me-2">
                <div>
                    <div class="fw-bold text-white fs-6" style="letter-spacing: 0.5px; line-height: 1.1;">MITRA CIPTA</div>
                    <div class="text-white-50 small" style="letter-spacing: 1px; font-size: 0.7rem;">MANDIRI</div>
                </div>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>

        <div class="offcanvas-body p-3" style="overflow-y: auto; -webkit-overflow-scrolling: touch;">
            <!-- Profile Info Card -->
            <div class="p-3 mb-3 rounded-4 text-center" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.08);">
                <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-2 shadow" style="width: 48px; height: 48px; background: linear-gradient(135deg, #2563eb, #3b82f6);">
                    <i class="fas fa-user-shield text-white fs-5"></i>
                </div>
                <div class="fw-bold text-white"><?php echo htmlspecialchars($_SESSION['admin_username']); ?></div>
                <span class="badge bg-warning text-dark rounded-pill px-2 py-1 small" style="font-size: 0.7rem;">Superadmin MCM</span>
            </div>

            <!-- Group 1: UTAMA -->
            <div class="mb-3">
                <div class="text-uppercase text-muted fw-bold px-2 mb-2" style="font-size: 0.68rem; letter-spacing: 1.2px; color: #64748b !important;">
                    <i class="fas fa-home me-1"></i> Utama
                </div>
                <div class="list-group list-group-flush gap-1">
                    <a href="?page=dashboard" class="mobile-nav-link <?php echo $page === 'dashboard' ? 'active' : ''; ?>">
                        <i class="fas fa-th-large me-2"></i> Dashboard
                    </a>
                </div>
            </div>

            <!-- Group 2: PROGRAM & KONTEN -->
            <div class="mb-3">
                <div class="text-uppercase text-muted fw-bold px-2 mb-2" style="font-size: 0.68rem; letter-spacing: 1.2px; color: #64748b !important;">
                    <i class="fas fa-layer-group me-1"></i> Program &amp; Konten
                </div>
                <div class="list-group list-group-flush gap-1">
                    <a href="?page=classes" class="mobile-nav-link <?php echo $page === 'classes' ? 'active' : ''; ?>">
                        <i class="fas fa-book-open me-2"></i> Paket Pelatihan
                    </a>
                    <a href="?page=categories" class="mobile-nav-link <?php echo $page === 'categories' ? 'active' : ''; ?>">
                        <i class="fas fa-tags me-2"></i> Kategori Pelatihan
                    </a>
                    <a href="?page=instructors" class="mobile-nav-link <?php echo $page === 'instructors' ? 'active' : ''; ?>">
                        <i class="fas fa-user-tie me-2"></i> Instruktur &amp; Asesor
                    </a>
                    <a href="?page=materials" class="mobile-nav-link <?php echo $page === 'materials' ? 'active' : ''; ?>">
                        <i class="fas fa-graduation-cap me-2"></i> Materi LMS
                    </a>
                    <a href="?page=users" class="mobile-nav-link <?php echo $page === 'users' ? 'active' : ''; ?>">
                        <i class="fas fa-users me-2"></i> Peserta Terdaftar
                    </a>
                    <a href="?page=gallery" class="mobile-nav-link <?php echo $page === 'gallery' ? 'active' : ''; ?>">
                        <i class="fas fa-camera-retro me-2"></i> Galeri Foto
                    </a>
                    <a href="?page=facilities" class="mobile-nav-link <?php echo $page === 'facilities' ? 'active' : ''; ?>">
                        <i class="fas fa-building me-2"></i> Foto Lokasi &amp; Fasilitas
                    </a>
                    <a href="?page=certs" class="mobile-nav-link <?php echo $page === 'certs' ? 'active' : ''; ?>">
                        <i class="fas fa-certificate me-2"></i> Legalitas &amp; Sertifikasi
                    </a>
                </div>
            </div>

            <!-- Group 3: PENJUALAN & PELANGGAN -->
            <div class="mb-3">
                <div class="text-uppercase text-muted fw-bold px-2 mb-2" style="font-size: 0.68rem; letter-spacing: 1.2px; color: #64748b !important;">
                    <i class="fas fa-cash-register me-1"></i> Penjualan &amp; Pelanggan
                </div>
                <div class="list-group list-group-flush gap-1">
                    <a href="?page=orders" class="mobile-nav-link <?php echo $page === 'orders' ? 'active' : ''; ?>">
                        <i class="fas fa-shopping-cart me-2"></i> Pesanan &amp; Transaksi
                    </a>
                    <a href="?page=finance" class="mobile-nav-link <?php echo $page === 'finance' ? 'active' : ''; ?>">
                        <i class="fas fa-money-bill-wave me-2"></i> Keuangan
                    </a>
                    <a href="?page=testimonials" class="mobile-nav-link <?php echo $page === 'testimonials' ? 'active' : ''; ?>">
                        <i class="fas fa-comment-dots me-2"></i> Testimoni
                    </a>
                    <a href="?page=chat" class="mobile-nav-link <?php echo $page === 'chat' ? 'active' : ''; ?>">
                        <i class="fab fa-whatsapp me-2"></i> Chat WhatsApp
                    </a>
                    <a href="?page=chatbot" class="mobile-nav-link <?php echo $page === 'chatbot' ? 'active' : ''; ?>">
                        <i class="fas fa-robot me-2"></i> Chatbot &amp; Balasan
                    </a>
                </div>
            </div>

            <!-- Group 4: LAPORAN -->
            <div class="mb-3 pb-5">
                <div class="text-uppercase text-muted fw-bold px-2 mb-2" style="font-size: 0.68rem; letter-spacing: 1.2px; color: #64748b !important;">
                    <i class="fas fa-chart-line me-1"></i> Laporan
                </div>
                <div class="list-group list-group-flush gap-1">
                    <a href="?page=reports" class="mobile-nav-link <?php echo $page === 'reports' ? 'active' : ''; ?>">
                        <i class="fas fa-chart-bar me-2"></i> Laporan &amp; Rekap
                    </a>
                </div>
            </div>

        </div><!-- end offcanvas-body -->
    </div><!-- end offcanvas -->

    <style>
        .mobile-nav-link {
            display: flex;
            align-items: center;
            padding: 10px 14px;
            color: #94a3b8;
            text-decoration: none;
            border-radius: 12px;
            font-size: 0.88rem;
            font-weight: 600;
            transition: all 0.2s ease;
            background: transparent;
        }
        .mobile-nav-link i {
            width: 22px;
            font-size: 1rem;
        }
        .mobile-nav-link:hover {
            color: #ffffff;
            background: rgba(255,255,255,0.08);
        }
        .mobile-nav-link.active {
            color: #ffffff !important;
            background: linear-gradient(135deg, #2563eb, #3b82f6) !important;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.35);
        }
    </style>
