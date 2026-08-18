    <div class="offcanvas offcanvas-end border-0 shadow" tabindex="-1" id="adminOffcanvas" style="border-radius: 2rem 0 0 2rem;">
        <div class="offcanvas-header bg-light py-4 px-4">
            <h5 class="offcanvas-title fw-bold text-dark"><i class="fas fa-user-shield me-2 text-primary"></i>Panel Admin</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-4">
            <div class="text-center mb-5 p-4 rounded-4 bg-light shadow-sm">
                <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow" style="width: 80px; height: 80px; background: linear-gradient(135deg, #2563eb, #3b82f6);">
                    <i class="fas fa-user-shield fs-1 text-white"></i>
                </div>
                <h5 class="fw-bold mb-0 text-dark"><?php echo htmlspecialchars($_SESSION['admin_username']); ?></h5>
                <span class="badge bg-warning text-dark rounded-pill px-3 mt-2">Superadmin MCM</span>
            </div>

            <div class="list-group list-group-flush gap-2">
                <a href="?page=testimonials" class="list-group-item list-group-item-action border-0 rounded-3 py-3 px-4 d-flex align-items-center bg-light mb-2">
                    <i class="fas fa-comment-dots me-3 text-primary fs-5"></i>
                    <div class="fw-bold text-dark">Testimoni</div>
                </a>
                <a href="?page=examiners" class="list-group-item list-group-item-action border-0 rounded-3 py-3 px-4 d-flex align-items-center bg-light mb-2">
                    <i class="fas fa-user-check me-3 text-primary fs-5"></i>
                    <div class="fw-bold text-dark">Penguji</div>
                </a>
                <a href="?page=materials" class="list-group-item list-group-item-action border-0 rounded-3 py-3 px-4 d-flex align-items-center bg-light mb-2">
                    <i class="fas fa-graduation-cap me-3 text-primary fs-5"></i>
                    <div class="fw-bold text-dark">Materi LMS</div>
                </a>
                <a href="?page=chat" class="list-group-item list-group-item-action border-0 rounded-3 py-3 px-4 d-flex align-items-center bg-light mb-2">
                    <i class="fab fa-whatsapp me-3 text-primary fs-5"></i>
                    <div class="fw-bold text-dark">Chat WhatsApp</div>
                </a>
                <a href="?page=admins" class="list-group-item list-group-item-action border-0 rounded-3 py-3 px-4 d-flex align-items-center bg-light mb-2">
                    <i class="fas fa-users-cog me-3 text-primary fs-5"></i>
                    <div class="fw-bold text-dark">Kelola Admin</div>
                </a>
                <a href="?page=settings" class="list-group-item list-group-item-action border-0 rounded-3 py-3 px-4 d-flex align-items-center bg-light mb-2">
                    <i class="fas fa-cog me-3 text-primary fs-5"></i>
                    <div class="fw-bold text-dark">Pengaturan Web</div>
                </a>
                <div class="mt-4 pt-4 border-top">
                    <a href="logout.php" class="btn btn-danger w-100 py-3 rounded-pill fw-bold shadow">
                        <i class="fas fa-power-off me-2"></i> Keluar Sistem
                    </a>
                </div>
            </div>
        </div>
    </div>
