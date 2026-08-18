<!-- ADMINS PAGE -->
        <div class="row align-items-center mb-5 g-3">
            <div class="col-md-6">
                <h2 class="fw-bold mb-1 text-dark">Manajemen Tim</h2>
                <p class="text-muted mb-0">Kelola akses dan otoritas admin platform MCM.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <button class="btn btn-primary px-4 shadow-sm rounded-pill" onclick="resetAdminForm(); showModal('adminModal');">
                    <i class="fas fa-user-plus me-2"></i>Tambah Admin
                </button>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th class="ps-4">Admin ID</th>
                                <th>Username</th>
                                <th>Terdaftar Pada</th>
                                <th class="text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($admins as $ad): ?>
                                <tr>
                                    <td class="ps-4 text-secondary fw-bold">#<?php echo $ad['id']; ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 38px; height: 38px;">
                                                <i class="fas fa-user-shield"></i>
                                            </div>
                                            <div class="fw-bold text-dark"><?php echo htmlspecialchars($ad['username']); ?></div>
                                        </div>
                                    </td>
                                    <td class="text-muted small"><?php echo date('d M Y, H:i', strtotime($ad['created_at'])); ?></td>
                                    <td class="text-end pe-4">
                                        <?php if ($ad['username'] !== $_SESSION['admin_username']): ?>
                                            <div class="d-inline-flex gap-2">
                                                <button class="btn btn-action btn-soft-primary" onclick="editAdmin(<?php echo htmlspecialchars(json_encode($ad)); ?>)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-action btn-soft-danger" onclick="deleteItem('admins', <?php echo $ad['id']; ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        <?php else: ?>
                                            <span class="badge badge-soft-primary">Anda</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
