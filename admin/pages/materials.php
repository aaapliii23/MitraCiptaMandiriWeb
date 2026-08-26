<!-- MATERIALS PAGE -->
        <?php
        // Kelompokkan materi per kelas (query sudah ORDER BY class_id)
        $groupedMaterials = [];
        foreach ($materials as $m) {
            $groupedMaterials[$m['class_name']][] = $m;
        }
        ?>
        <div class="row align-items-center mb-4 g-3" data-aos="fade-down">
            <div class="col-md-6">
                <h2 class="fw-bold mb-1 text-dark">Materi LMS</h2>
                <p class="text-muted mb-0">Kelola materi belajar per paket pelatihan untuk peserta LMS.</p>
            </div>
            <div class="col-md-6 text-md-end d-flex gap-2 justify-content-md-end flex-wrap">
                <select id="materialClassFilter" class="form-select rounded-pill px-3" style="max-width: 260px;">
                    <option value="all">Semua Kelas</option>
                    <?php foreach (array_keys($groupedMaterials) as $gName): ?>
                        <option value="<?php echo htmlspecialchars($gName); ?>"><?php echo htmlspecialchars($gName); ?></option>
                    <?php endforeach; ?>
                </select>
                <button class="btn btn-primary px-4 shadow-sm rounded-pill" onclick="resetMaterialForm(); showModal('materialModal');">
                    <i class="fas fa-plus me-2"></i>Tambah Materi
                </button>
            </div>
        </div>

        <?php if (empty($materials)): ?>
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body text-center py-5 text-muted">Belum ada materi.</div>
            </div>
        <?php else: ?>
        <div class="accordion" id="materialsAccordion">
            <?php $first = true; foreach ($groupedMaterials as $className => $items): ?>
            <?php $accId = 'matAcc' . md5($className); ?>
            <div class="accordion-item border-0 shadow-sm rounded-4 overflow-hidden mb-3 material-group" data-class="<?php echo htmlspecialchars($className); ?>">
                <h2 class="accordion-header">
                    <button class="accordion-button fw-bold text-dark<?php echo $first ? '' : ' collapsed'; ?>" type="button"
                            data-bs-toggle="collapse" data-bs-target="#<?php echo $accId; ?>">
                        <i class="fas fa-graduation-cap me-2 text-primary"></i><?php echo htmlspecialchars($className); ?>
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill ms-2"><?php echo count($items); ?> materi</span>
                    </button>
                </h2>
                <div id="<?php echo $accId; ?>" class="accordion-collapse collapse<?php echo $first ? ' show' : ''; ?>"
                     data-bs-parent="#materialsAccordion">
                    <div class="accordion-body p-0">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4">Judul Materi</th>
                                        <th>Tipe</th>
                                        <th class="text-center">Urutan</th>
                                        <th class="text-end pe-4">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($items as $m): ?>
                                    <tr>
                                        <td class="ps-4 fw-bold text-dark"><?php echo htmlspecialchars($m['title']); ?></td>
                                        <td>
                                            <span class="badge rounded-pill px-3 text-uppercase
                                                <?php echo $m['type'] === 'video' ? 'bg-danger bg-opacity-10 text-danger' : ($m['type'] === 'pdf' ? 'bg-warning bg-opacity-10 text-warning' : 'bg-info bg-opacity-10 text-info'); ?>">
                                                <i class="fas <?php echo $m['type'] === 'video' ? 'fa-video' : ($m['type'] === 'pdf' ? 'fa-file-pdf' : 'fa-align-left'); ?> me-1"></i><?php echo $m['type']; ?>
                                            </span>
                                        </td>
                                        <td class="text-center text-muted small"><?php echo (int)$m['sort_order']; ?></td>
                                        <td class="text-end pe-4">
                                            <div class="d-inline-flex gap-2">
                                                <button class="btn btn-action btn-soft-primary" title="Kelola Quiz" onclick="openQuizManager(<?php echo (int)$m['id']; ?>, '<?php echo htmlspecialchars(addslashes($m['title'])); ?>')"><i class="fas fa-question-circle"></i></button>
                                                <button class="btn btn-action btn-soft-primary" title="Edit" onclick="editMaterial(<?php echo htmlspecialchars(json_encode($m)); ?>)"><i class="fas fa-edit"></i></button>
                                                <button class="btn btn-action btn-soft-danger" title="Hapus" onclick="deleteItem('materials', <?php echo (int)$m['id']; ?>)"><i class="fas fa-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <?php $first = false; endforeach; ?>
        </div>

        <script>
        (function() {
            function initMaterialFilter() {
                var filter = document.getElementById('materialClassFilter');
                if (!filter || filter.dataset.matBound) return;
                filter.dataset.matBound = '1';
                filter.addEventListener('change', function() {
                    var selected = this.value;
                    var groups = document.querySelectorAll('.material-group');
                    groups.forEach(function(g) {
                        var match = (selected === 'all' || g.getAttribute('data-class') === selected);
                        g.style.display = match ? '' : 'none';
                    });
                    if (selected !== 'all') {
                        var visible = document.querySelector('.material-group[data-class="' + CSS.escape(selected) + '"] .accordion-collapse');
                        if (visible && !visible.classList.contains('show') && typeof bootstrap !== 'undefined') {
                            bootstrap.Collapse.getOrCreateInstance(visible).show();
                        }
                    }
                });
            }
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initMaterialFilter);
            } else {
                initMaterialFilter();
            }
        })();
        </script>
        <?php endif; ?>
