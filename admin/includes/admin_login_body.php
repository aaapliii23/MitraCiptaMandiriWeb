<body>
    <div class="bg-shape-1"></div>
    <div class="bg-shape-2"></div>

    <div class="login-card">
        <div class="text-center mb-4">
            <div class="d-inline-flex align-items-center justify-content-center mb-3">
                <img src="../assets/img/logo.png" alt="MCM Logo" class="brand-logo">
                <div class="ms-2 ps-2 border-start border-2 brand-divider text-start">
                    <span class="fw-bold d-block text-dark brand-text-sm" style="font-size: 0.78rem; letter-spacing: 1px; line-height: 1.1;">MITRA CIPTA</span>
                    <span class="fw-bold d-block text-dark brand-text-sm" style="font-size: 0.78rem; letter-spacing: 1px; line-height: 1.1;">MANDIRI</span>
                </div>
            </div>
            <h4 class="fw-bold text-dark mb-1">Akses Administrator</h4>
            <p class="text-muted small mb-0">Silakan login untuk mengelola sistem MCM.</p>
        </div>

        <?php if (!empty($errorMessage)): ?>
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center py-2 px-3 mb-4 rounded-3" role="alert">
                <i class="fas fa-exclamation-circle me-2 fs-5 flex-shrink-0"></i>
                <div class="small fw-medium flex-grow-1"><?php echo htmlspecialchars($errorMessage); ?></div>
                <button type="button" class="btn-close ms-2 p-2" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form method="POST" action="admin_login.php" id="privateAdminLoginForm">
            <div class="mb-3">
                <label for="username" class="form-label small fw-bold text-secondary">Username</label>
                <div class="input-group">
                    <span class="input-group-text text-muted"><i class="fas fa-user"></i></span>
                    <input type="text" class="form-control" id="username" name="username" placeholder="Masukkan username" required autocomplete="username" autofocus>
                </div>
            </div>

            <div class="mb-4">
                <label for="password" class="form-label small fw-bold text-secondary">Password</label>
                <div class="input-group">
                    <span class="input-group-text text-muted"><i class="fas fa-lock"></i></span>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password" required autocomplete="current-password">
                    <span class="input-group-text toggle-password text-muted" id="togglePasswordBtn" title="Tampilkan/Sembunyikan Password">
                        <i class="fas fa-eye" id="togglePasswordIcon"></i>
                    </span>
                </div>
            </div>

            <button type="submit" class="btn btn-login w-100 mb-3">
                <i class="fas fa-sign-in-alt me-2"></i> Masuk ke Dashboard
            </button>
        </form>

        <div class="text-center mt-3 pt-3 border-top d-flex flex-column gap-1">
            <a href="../index.php" class="back-to-home"><i class="fas fa-arrow-left me-1"></i> Kembali ke Beranda</a>
            <small class="text-muted mt-1" style="font-size: 0.75rem;">&copy; <?php echo date('Y'); ?> MCM - Mitra Cipta Mandiri</small>
        </div>
    </div>
