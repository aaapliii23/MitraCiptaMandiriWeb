    <div class="d-lg-none sticky-top bg-white py-2 px-3 shadow-sm border-bottom mb-3 d-flex justify-content-between align-items-center" style="z-index: 1030;">
        <a href="?page=dashboard" class="d-flex align-items-center text-decoration-none">
            <img src="../assets/img/logo.png" alt="MCM Logo" height="32" class="me-2">
            <div class="brand-text">
                <div class="fw-bold text-dark" style="font-size: 0.85rem; line-height: 1.1;">MITRA CIPTA MANDIRI</div>
                <div class="text-primary fw-bold" style="font-size: 0.65rem; letter-spacing: 1px;">PANEL ADMIN</div>
            </div>
        </a>

        <!-- Avatar Button: hanya tampil di HP, isi dropdown = Sistem saja -->
        <div class="dropdown d-lg-none">
            <button class="btn p-0 border-0 bg-transparent dropdown-toggle-no-arrow" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="outline: none;">
                <div class="rounded-circle border border-2 border-primary p-1">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: linear-gradient(135deg, #2563eb, #3b82f6);">
                        <i class="fas fa-user-shield text-white" style="font-size: 0.85rem;"></i>
                    </div>
                </div>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-4 mt-2" style="min-width: 200px; z-index: 2000;">
                <li class="px-3 pt-2 pb-1">
                    <span class="text-muted small fw-bold text-uppercase" style="font-size: 0.65rem; letter-spacing: 1px;">Sistem</span>
                </li>
                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2 py-2 px-3 <?php echo (isset($_GET['page']) && $_GET['page']==='settings') ? 'active' : ''; ?>" href="?page=settings">
                        <span class="d-flex align-items-center justify-content-center rounded-2 bg-secondary bg-opacity-10" style="width:28px;height:28px;">
                            <i class="fas fa-cog text-secondary" style="font-size:0.75rem;"></i>
                        </span>
                        <span class="fw-semibold">Pengaturan Web</span>
                    </a>
                </li>
                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2 py-2 px-3 <?php echo (isset($_GET['page']) && $_GET['page']==='admins') ? 'active' : ''; ?>" href="?page=admins">
                        <span class="d-flex align-items-center justify-content-center rounded-2 bg-danger bg-opacity-10" style="width:28px;height:28px;">
                            <i class="fas fa-users-cog text-danger" style="font-size:0.75rem;"></i>
                        </span>
                        <span class="fw-semibold">Kelola Admin</span>
                    </a>
                </li>
                <li><hr class="dropdown-divider my-1"></li>
                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2 py-2 px-3 text-danger" href="../admin/logout.php">
                        <span class="d-flex align-items-center justify-content-center rounded-2 bg-danger bg-opacity-10" style="width:28px;height:28px;">
                            <i class="fas fa-sign-out-alt text-danger" style="font-size:0.75rem;"></i>
                        </span>
                        <span class="fw-semibold">Keluar</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <style>
        .dropdown-toggle-no-arrow::after { display: none !important; }
        .dropdown-menu .dropdown-item.active,
        .dropdown-menu .dropdown-item:active {
            background: rgba(37, 99, 235, 0.08);
            color: #2563eb;
            border-radius: 8px;
        }
        .dropdown-menu .dropdown-item:hover {
            background: rgba(37, 99, 235, 0.06);
            border-radius: 8px;
        }
        .dropdown-menu {
            padding: 8px;
        }
        .dropdown-menu li { list-style: none; }
    </style>
