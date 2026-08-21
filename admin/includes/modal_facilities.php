<!-- FACILITY LOCATION MODAL (ADD / EDIT PHOTO) -->
<div class="modal fade" id="facilityAdminModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-5 overflow-hidden">
            <div class="modal-header border-0 bg-light px-4 py-3">
                <h5 class="modal-title fw-bold text-dark" id="facilityAdminModalTitle">
                    <i class="fas fa-building me-2 text-primary"></i>Tambah Foto Lokasi &amp; Fasilitas
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="facilityForm" class="ajax-form" action="<?php echo $adminBase; ?>/actions/manage_facilities.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" id="facilityAction" value="create">
                <input type="hidden" name="id" id="facilityId" value="">

                <div class="modal-body p-4">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label fw-bold text-dark small mb-0">Kategori Lokasi <span class="text-danger">*</span></label>
                            <a href="javascript:void(0)" class="small text-primary text-decoration-none fw-bold" onclick="bootstrap.Modal.getInstance(document.getElementById('facilityAdminModal')).hide(); new bootstrap.Modal(document.getElementById('facilityCategoryModal')).show();">
                                <i class="fas fa-plus-circle me-1"></i>+ Kategori Baru
                            </a>
                        </div>
                        <select name="category" id="facilityCategory" class="form-select rounded-4 py-2" required>
                            <option value="">-- Pilih Kategori Lokasi --</option>
                            <?php if (!empty($facilityCategories)): ?>
                                <?php foreach ($facilityCategories as $fc): ?>
                                    <option value="<?php echo htmlspecialchars($fc['slug']); ?>">
                                        <?php echo htmlspecialchars($fc['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="alat_pemadam">Alat Pemadam</option>
                                <option value="balkon">Balkon</option>
                                <option value="kantor">Kantor</option>
                                <option value="kelas">Kelas</option>
                                <option value="lobby">Lobby</option>
                                <option value="mushola">Mushola</option>
                                <option value="parkiran">Parkiran</option>
                                <option value="toilet">Toilet</option>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">Judul Foto</label>
                        <input type="text" name="title" id="facilityTitle" class="form-control rounded-4 py-2" placeholder="Contoh: Ruang Kelas Teori A (Opsional)">
                        <div class="form-text small">Jika dikosongkan, judul akan otomatis diisi sesuai kategori.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">Keterangan / Deskripsi</label>
                        <textarea name="description" id="facilityDescription" class="form-control rounded-4 py-2" rows="2" placeholder="Keterangan fasilitas atau sarana pendukung (opsional)..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small" id="facilityImageLabel">Pilih File Foto <span class="text-danger">*</span></label>
                        <input type="file" name="images[]" id="facilityImageInput" class="form-control rounded-4 py-2" accept=".jpg,.jpeg,.png,.webp,.heic" multiple required onchange="updateFacilityFilePreview(this)">
                        <div class="form-text small">Mendukung format JPG, PNG, WEBP, HEIC. Bisa pilih beberapa foto sekaligus.</div>
                    </div>

                    <div id="facilityFilePreview" class="mt-2"></div>
                </div>

                <div class="modal-footer border-0 bg-light px-4 py-3">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold" id="facilitySubmitBtn">
                        <i class="fas fa-cloud-upload-alt me-2"></i>Upload Foto
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- FACILITY CATEGORY MANAGEMENT MODAL -->
<div class="modal fade" id="facilityCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-5 overflow-hidden">
            <div class="modal-header border-0 bg-light px-4 py-3">
                <h5 class="modal-title fw-bold text-dark">
                    <i class="fas fa-tags me-2 text-primary"></i>Kelola Kategori Lokasi &amp; Fasilitas
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Form Tambah Kategori Baru -->
                <div class="card border-0 bg-light rounded-4 p-3 mb-4">
                    <h6 class="fw-bold text-dark mb-3"><i class="fas fa-plus-circle me-2 text-primary"></i>Tambah Kategori Baru</h6>
                    <form id="facilityCatForm" class="ajax-form" action="<?php echo $adminBase; ?>/actions/manage_facilities.php" method="POST">
                        <input type="hidden" name="action" value="create_category">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-5">
                                <label class="form-label small fw-bold text-dark mb-1">Nama Kategori <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="facCatName" class="form-control rounded-pill" placeholder="Contoh: Dapur Pastry" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-dark mb-1">Pilihan Ikon</label>
                                <select name="icon" id="facCatIcon" class="form-select rounded-pill">
                                    <option value="fa-building">🏢 Gedung / Ruangan</option>
                                    <option value="fa-chalkboard-teacher">🧑‍🏫 Kelas / Teori</option>
                                    <option value="fa-door-open">🚪 Balkon / Pintu</option>
                                    <option value="fa-couch">🛋️ Lobby / Lounge</option>
                                    <option value="fa-mosque">🕌 Mushola / Ibadah</option>
                                    <option value="fa-parking">🅿️ Parkiran</option>
                                    <option value="fa-restroom">🚻 Toilet / Sanitasi</option>
                                    <option value="fa-fire-extinguisher">🧯 Alat Pemadam</option>
                                    <option value="fa-utensils">🍴 Dapur / Resto</option>
                                    <option value="fa-cut">✂️ Salon / Tata Rias</option>
                                    <option value="fa-laptop">💻 Lab Komputer</option>
                                    <option value="fa-spa">💆 Spa / Pijat</option>
                                    <option value="fa-camera">📷 Studio Foto</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary rounded-pill w-100 fw-bold">
                                    <i class="fas fa-save me-1"></i> Simpan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Daftar Kategori yang Ada -->
                <h6 class="fw-bold text-dark mb-3"><i class="fas fa-list me-2 text-primary"></i>Daftar Kategori Saat Ini</h6>
                <div class="table-responsive rounded-4 border">
                    <table class="table align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-3 py-2 small">Ikon &amp; Nama Kategori</th>
                                <th class="py-2 small">Slug / Kode</th>
                                <th class="py-2 small text-center">Jumlah Foto</th>
                                <th class="py-2 small text-end pe-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($facilityCategories)): ?>
                                <?php foreach ($facilityCategories as $fc): ?>
                                    <?php $cnt = $catCounts[$fc['slug']] ?? 0; ?>
                                    <tr>
                                        <td class="ps-3 py-2">
                                            <i class="fas <?php echo htmlspecialchars($fc['icon']); ?> text-primary me-2"></i>
                                            <span class="fw-bold text-dark"><?php echo htmlspecialchars($fc['name']); ?></span>
                                        </td>
                                        <td class="py-2"><code><?php echo htmlspecialchars($fc['slug']); ?></code></td>
                                        <td class="py-2 text-center">
                                            <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-2 py-1"><?php echo $cnt; ?> foto</span>
                                        </td>
                                        <td class="py-2 text-end pe-3">
                                            <button type="button" class="btn btn-action btn-soft-danger btn-sm" title="Hapus Kategori" onclick="deleteFacilityCategory(<?php echo $fc['id']; ?>, '<?php echo htmlspecialchars($fc['name']); ?>')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted small">Belum ada data kategori.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-0 bg-light px-4 py-3">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
