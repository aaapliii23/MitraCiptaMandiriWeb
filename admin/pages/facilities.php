<!-- FACILITIES & LOCATION PHOTOS PAGE -->
<?php
// Build lookup map from DB categories
$facCatsMap = [];
foreach ($facilityCategories as $fc) {
    $facCatsMap[$fc['slug']] = [
        'id'   => $fc['id'],
        'name' => $fc['name'],
        'icon' => !empty($fc['icon']) ? $fc['icon'] : 'fa-building'
    ];
}

// Fallback defaults if map empty
if (empty($facCatsMap)) {
    $facCatsMap = [
        'alat_pemadam' => ['id' => 0, 'name' => 'Alat Pemadam', 'icon' => 'fa-fire-extinguisher'],
        'balkon'       => ['id' => 0, 'name' => 'Balkon',       'icon' => 'fa-door-open'],
        'kantor'       => ['id' => 0, 'name' => 'Kantor',       'icon' => 'fa-building'],
        'kelas'        => ['id' => 0, 'name' => 'Kelas',        'icon' => 'fa-chalkboard-teacher'],
        'lobby'        => ['id' => 0, 'name' => 'Lobby',        'icon' => 'fa-couch'],
        'mushola'      => ['id' => 0, 'name' => 'Mushola',      'icon' => 'fa-mosque'],
        'parkiran'     => ['id' => 0, 'name' => 'Parkiran',     'icon' => 'fa-parking'],
        'toilet'       => ['id' => 0, 'name' => 'Toilet',       'icon' => 'fa-restroom']
    ];
}

// Count per category
$catCounts = [];
foreach ($facilities as $fac) {
    $c = $fac['category'] ?? 'kelas';
    $catCounts[$c] = ($catCounts[$c] ?? 0) + 1;
}
?>

<div class="row align-items-center mb-4 g-3">
    <div class="col-md-6">
        <h2 class="fw-bold mb-1 text-dark">Foto Lokasi &amp; Fasilitas</h2>
        <p class="text-muted mb-0">Kelola foto dokumentasi dan kategori sarana tempat pelatihan MCM.</p>
    </div>
    <div class="col-md-6 text-md-end d-flex gap-2 justify-content-md-end flex-wrap">
        <button class="btn btn-outline-primary px-3 shadow-sm rounded-pill" onclick="showModal('facilityCategoryModal');">
            <i class="fas fa-tags me-2"></i>Kelola Kategori
        </button>
        <button class="btn btn-primary px-4 shadow-sm rounded-pill" onclick="resetFacilityForm(); showModal('facilityAdminModal');">
            <i class="fas fa-plus-circle me-2"></i>Tambah Foto Lokasi
        </button>
    </div>
</div>

<!-- Category Filter Pills -->
<div class="d-flex flex-wrap gap-2 mb-4">
    <button class="btn btn-sm rounded-pill px-3 py-2 fw-bold admin-fac-filter active" data-filter="all" style="transition: all 0.3s ease;">
        Semua <span class="badge bg-white text-dark ms-1 rounded-pill"><?php echo count($facilities); ?></span>
    </button>
    <?php foreach ($facCatsMap as $slug => $meta): ?>
        <button class="btn btn-sm rounded-pill px-3 py-2 fw-bold admin-fac-filter" data-filter="<?php echo $slug; ?>" style="transition: all 0.3s ease;">
            <i class="fas <?php echo $meta['icon']; ?> me-1"></i> <?php echo htmlspecialchars($meta['name']); ?>
            <span class="badge bg-light text-secondary ms-1 rounded-pill"><?php echo $catCounts[$slug] ?? 0; ?></span>
        </button>
    <?php endforeach; ?>
</div>

<div class="row g-4" id="adminFacilityGrid">
    <?php if (!empty($facilities)): ?>
        <?php foreach ($facilities as $fac): ?>
            <div class="col-lg-3 col-md-4 col-sm-6 admin-fac-card" data-category="<?php echo htmlspecialchars($fac['category']); ?>">
                <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden bg-white" style="border: 1px solid rgba(0,0,0,0.05) !important;">
                    <div class="position-relative" style="height: 190px;">
                        <img src="<?php echo htmlspecialchars(getImgSrc($fac['image'])); ?>" class="w-100 h-100" style="object-fit: cover;" alt="<?php echo htmlspecialchars($fac['title']); ?>" onerror="this.onerror=null;this.src='../assets/img/logo.png';">
                        <div class="position-absolute top-0 start-0 m-2">
                            <span class="badge px-2 py-1 rounded-pill shadow-sm" style="background: linear-gradient(135deg, #0c4a6e, #0ea5e9); font-size: 0.7rem;">
                                <i class="fas <?php echo $facCatsMap[$fac['category']]['icon'] ?? 'fa-building'; ?> me-1"></i>
                                <?php echo htmlspecialchars($facCatsMap[$fac['category']]['name'] ?? ucfirst($fac['category'])); ?>
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <h6 class="fw-bold text-dark mb-1 text-truncate" title="<?php echo htmlspecialchars($fac['title']); ?>">
                                <?php echo htmlspecialchars($fac['title']); ?>
                            </h6>
                            <?php if (!empty($fac['description'])): ?>
                                <p class="small text-muted mb-2 text-truncate" style="font-size: 0.78rem;"><?php echo htmlspecialchars($fac['description']); ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="d-flex justify-content-end gap-2 mt-2 pt-2 border-top">
                            <button class="btn btn-action btn-soft-primary" title="Edit Foto" onclick="editFacility(<?php echo htmlspecialchars(json_encode($fac)); ?>)">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-action btn-soft-danger" title="Hapus Foto" onclick="deleteItem('facilities', <?php echo $fac['id']; ?>)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12 text-center py-5">
            <div class="p-5 bg-white rounded-5 shadow-sm border border-light">
                <i class="fas fa-building fs-1 text-muted mb-3 d-block opacity-50"></i>
                <h5 class="fw-bold text-dark">Belum Ada Foto Lokasi</h5>
                <p class="text-muted mb-3">Mulai tambahkan foto dokumentasi lokasi dan fasilitas MCM.</p>
                <button class="btn btn-primary px-4 rounded-pill" onclick="resetFacilityForm(); showModal('facilityAdminModal');">
                    <i class="fas fa-plus-circle me-2"></i>Tambah Foto Pertama
                </button>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
    .admin-fac-filter {
        background: #f8fafc;
        color: #475569;
        border: 1px solid #e2e8f0;
    }
    .admin-fac-filter:hover {
        background: #e2e8f0;
        color: #0c4a6e;
    }
    .admin-fac-filter.active {
        background: linear-gradient(135deg, #0c4a6e, #0ea5e9) !important;
        color: #ffffff !important;
        border-color: transparent !important;
        box-shadow: 0 4px 12px rgba(14, 165, 233, 0.25) !important;
    }
    .admin-fac-filter.active .badge {
        background: rgba(255,255,255,0.2) !important;
        color: #ffffff !important;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterBtns = document.querySelectorAll('.admin-fac-filter');
        const cards = document.querySelectorAll('.admin-fac-card');

        filterBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                filterBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                const filter = this.getAttribute('data-filter');
                cards.forEach(card => {
                    const cardCat = card.getAttribute('data-category');
                    if (filter === 'all' || cardCat === filter) {
                        card.style.display = '';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });
    });
</script>
