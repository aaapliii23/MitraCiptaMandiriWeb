    <!-- Gallery Section -->
    <section id="galeri" class="section-padding bg-white">
        <div class="container">
            <div class="section-title text-center mb-5" data-aos="fade-up">
                <h2 class="fw-bold display-5 mb-3">Galeri <span class="text-secondary">Kegiatan</span></h2>
                <p class="text-secondary fs-5">Momen-momen inspiratif selama pelatihan di MCM</p>
            </div>

            <!-- Filters -->
            <?php
            $galleryCats = [];
            foreach ($galleryItems as $item) {
                $c = strtolower(trim($item['category'] ?? ''));
                if ($c === 'all' || $c === '') $c = 'umum';
                $galleryCats[$c] = true;
            }
            $galleryCats = array_keys($galleryCats);

            function galleryCatLabel($slug) {
                if ($slug === 'umum') return 'Umum';
                return ucwords(str_replace(['_', '-'], ' ', $slug));
            }
            ?>
            <div class="gallery-filters" data-aos="fade-up" data-aos-delay="100">
                <button class="filter-btn active" data-filter="all">Semua</button>
                <?php foreach ($galleryCats as $gc): ?>
                <button class="filter-btn" data-filter="<?php echo htmlspecialchars($gc); ?>"><?php echo htmlspecialchars(galleryCatLabel($gc)); ?></button>
                <?php endforeach; ?>
            </div>

            <!-- Gallery Grid -->
            <div class="row g-4" id="galleryGrid" data-aos="fade-up" data-aos-delay="200">
                <?php foreach ($galleryItems as $item): ?>
                <?php 
                    $itemCat = strtolower(trim($item['category'] ?? ''));
                    if ($itemCat === 'all' || $itemCat === '') $itemCat = 'umum';
                ?>
                <div class="col-md-4 col-sm-6 gallery-item" data-category="<?php echo htmlspecialchars($itemCat); ?>">
                    <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                    <div class="overlay"><h5><?php echo htmlspecialchars($item['title']); ?></h5></div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Expand / Collapse Button -->
            <div class="text-center mt-5" id="galleryExpandContainer" style="display: none;"></div>
        </div>
    </section>

    <!-- Gallery Lightbox Modal -->
    <div class="modal fade" id="galleryLightboxModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 rounded-4 overflow-hidden shadow-lg bg-dark">
                <div class="modal-header border-0 pb-0 px-4 pt-3 position-absolute top-0 end-0 z-index-1">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0 text-center position-relative">
                    <img src="" id="galleryLightboxImg" class="img-fluid w-100" style="max-height: 80vh; object-fit: contain;" alt="Preview Foto">
                    <div class="p-3 text-white text-start" style="background: rgba(15, 23, 42, 0.9);">
                        <h5 class="fw-bold mb-0" id="galleryLightboxTitle"></h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .gallery-item {
            position: relative;
            cursor: pointer;
            overflow: hidden;
            border-radius: 1.25rem;
        }
        .gallery-more-trigger {
            cursor: pointer;
        }
        .gallery-more-trigger .overlay {
            display: none !important;
        }
        .overlay-more-badge {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(12, 74, 110, 0.88), rgba(14, 165, 233, 0.82));
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: #ffffff;
            z-index: 10;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            padding: 20px;
        }
        .gallery-more-trigger:hover .overlay-more-badge {
            background: linear-gradient(135deg, rgba(12, 74, 110, 0.95), rgba(14, 165, 233, 0.92));
            transform: scale(1.02);
        }
        .overlay-more-badge .more-icon {
            font-size: 2rem;
            margin-bottom: 6px;
            opacity: 0.95;
            animation: float-icon 2s ease-in-out infinite;
        }
        .overlay-more-badge .more-number {
            font-size: 1.5rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            margin-bottom: 4px;
        }
        .overlay-more-badge .more-text {
            font-size: 0.85rem;
            font-weight: 600;
            opacity: 0.95;
            background: rgba(255, 255, 255, 0.25);
            padding: 6px 16px;
            border-radius: 50px;
            display: inline-block;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        @keyframes float-icon {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-4px); }
        }
    </style>
