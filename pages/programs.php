<?php
require_once '../config/database.php';
$hide_nav_items = true;
include '../includes/header.php';

// Fetch all classes from DB
try {
    $stmt = $pdo->query("SELECT * FROM classes ORDER BY id ASC");
    $classes = $stmt->fetchAll();
    
    // Extract unique categories
    $categories = array_unique(array_column($classes, 'category'));
} catch(PDOException $e) { $classes = []; $categories = []; }
?>

<!-- All Programs Full Page -->
<section class="section-padding bg-white animate__animated animate__fadeIn" style="padding-top: 50px;">
    <div class="container">
        <!-- Circular Back Button above Logo -->
        <div class="mb-4 text-start" style="margin-left: -5px;">
            <a href="../index.php" class="btn rounded-circle d-inline-flex align-items-center justify-content-center shadow-premium btn-premium" style="width: 50px; height: 50px; background: linear-gradient(135deg, #0c4a6e, #0ea5e9); color: white; border: none; transition: all 0.3s ease;">
                <i class="fas fa-arrow-left fs-5"></i>
            </a>
        </div>

        <div class="text-start mb-5">
            <!-- Logo directly above badge -->
            <div class="mb-4">
                <a class="d-flex align-items-center text-decoration-none" href="../index.php">
                    <img src="../assets/img/logo.png" alt="MCM Logo" style="height: 45px; width: auto;">
                    <div class="ms-2 ps-2 border-start border-2 border-dark d-flex flex-column justify-content-center" style="height: 35px;">
                        <span class="fw-bold text-dark" style="font-size: 0.8rem; letter-spacing: 1px; line-height: 1.1;">MITRA CIPTA</span>
                        <span class="fw-bold text-dark" style="font-size: 0.8rem; letter-spacing: 1px; line-height: 1.1;">MANDIRI</span>
                    </div>
                </a>
            </div>
            <div class="badge bg-primary bg-opacity-10 text-primary mb-3 p-2 px-3 rounded-pill fw-bold" style="background-color: rgba(12, 74, 110, 0.1) !important; color: #0c4a6e !important;">OUR PROGRAMS</div>
            <h1 class="display-5 fw-bold mb-3" style="color: #0c4a6e;">Jelajahi <span style="color: #0ea5e9;">Pelatihan Kami</span></h1>
            <p class="text-secondary fs-5" style="max-width: 700px;">Temukan berbagai pilihan program pelatihan vokasi unggulan yang dirancang khusus untuk membangun kemandirian finansial Anda.</p>
        </div>

        <!-- Search & Filter Section -->
        <div class="row g-3 mb-5 align-items-center">
            <div class="col-lg-5">
                <div class="position-relative">
                    <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                    <input type="text" id="classSearch" class="form-control ps-5 py-3 rounded-pill border-0 shadow-sm" placeholder="Cari program pelatihan..." style="background-color: #f8fafc;">
                </div>
            </div>
            <div class="col-lg-7">
                <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
                    <button class="btn btn-filter active rounded-pill px-4 py-2 fw-bold" data-filter="all">Semua</button>
                    <?php foreach ($categories as $cat): ?>
                        <button class="btn btn-filter rounded-pill px-4 py-2 fw-bold" data-filter="<?php echo htmlspecialchars($cat); ?>">
                            <?php echo htmlspecialchars($cat); ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <style>
            .btn-filter {
                background: white;
                color: #64748b;
                border: 1px solid #e2e8f0;
                transition: all 0.3s ease;
            }
            .btn-filter:hover {
                border-color: #0ea5e9;
                color: #0ea5e9;
            }
            .btn-filter.active {
                background: linear-gradient(135deg, #0c4a6e, #0ea5e9);
                color: white;
                border: none;
                box-shadow: 0 4px 12px rgba(14, 165, 233, 0.2);
            }
            .class-item {
                transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.4s ease;
            }
        </style>

        <!-- Class Grid -->
        <div class="row g-4">
            <?php foreach ($classes as $c): 
                $featuresArr = json_decode($c['features'], true) ?: [];
            ?>
            <div class="col-lg-4 col-md-6 class-item" data-category="<?php echo htmlspecialchars($c['category']); ?>" data-name="<?php echo strtolower(htmlspecialchars($c['name'])); ?>">
                <div class="paket-card h-100 shadow-sm border rounded-4 overflow-hidden bg-white">
                    <div class="position-relative" style="height: 200px;">
                        <img src="<?php echo htmlspecialchars(asset_src($c['image'])); ?>" alt="<?php echo htmlspecialchars($c['name']); ?>" class="w-100 h-100" style="object-fit: cover;" onerror="this.onerror=null;this.src='../assets/img/logo.png';">
                        <div class="position-absolute top-0 end-0 m-3">
                            <span class="badge bg-primary px-3 py-2 rounded-pill shadow-sm">MCM Official</span>
                        </div>
                    </div>
                    <div class="paket-card-content p-4">
                        <h4 class="fw-bold mb-2"><?php echo htmlspecialchars($c['name']); ?></h4>
                        <p class="text-muted small mb-4" style="height: 4.5em; overflow: hidden;"><?php echo substr(strip_tags($c['description']), 0, 120); ?>...</p>
                        <h5 class="text-primary fw-bold mb-4">Rp <?php echo number_format($c['price'], 0, ',', '.'); ?></h5>
                        
                        <div class="mb-4">
                            <?php foreach (array_slice($featuresArr, 0, 3) as $f): ?>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-check-circle text-success me-2 small"></i>
                                    <span class="small text-secondary"><?php echo htmlspecialchars($f); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <a href="class_detail.php?id=<?php echo $c['id']; ?>&from=programs" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-sm" 
                                style="background: linear-gradient(135deg, #0c4a6e, #0ea5e9); border: none;">
                            Daftar Sekarang
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('classSearch');
        const filterBtns = document.querySelectorAll('.btn-filter');
        const classItems = document.querySelectorAll('.class-item');

        function filterClasses() {
            const searchTerm = searchInput.value.toLowerCase().trim();
            const activeFilter = document.querySelector('.btn-filter.active').dataset.filter;

            classItems.forEach(item => {
                const name = item.dataset.name;
                const category = item.dataset.category;
                
                const matchesSearch = searchTerm === "" || name.includes(searchTerm);
                const matchesFilter = (activeFilter === 'all' || category === activeFilter);

                if (matchesSearch && matchesFilter) {
                    item.style.display = 'block';
                    // Small timeout to ensure transition triggers
                    requestAnimationFrame(() => {
                        item.style.opacity = '1';
                        item.style.transform = 'scale(1)';
                    });
                } else {
                    item.style.opacity = '0';
                    item.style.transform = 'scale(0.8)';
                    setTimeout(() => {
                        if (item.style.opacity === '0') {
                            item.style.display = 'none';
                        }
                    }, 300);
                }
            });
        }

        searchInput.addEventListener('input', filterClasses);

        filterBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                filterBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                filterClasses();
            });
        });
    });
</script>

<?php include '../includes/footer.php'; ?>
