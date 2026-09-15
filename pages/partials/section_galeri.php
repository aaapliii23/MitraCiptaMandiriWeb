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

            <!-- Gallery Grid — featured first item, rest uniform (DESIGN.md: ENERGY 2 / RHYTHM 2 / MOTION 1, hierarchy: flagship) -->
            <div class="row g-3 g-md-4" id="galleryGrid" data-aos="fade-up" data-aos-delay="200">
                <?php foreach ($galleryItems as $idx => $item): ?>
                <?php 
                    $itemCat = strtolower(trim($item['category'] ?? ''));
                    if ($itemCat === 'all' || $itemCat === '') $itemCat = 'umum';
                    $isFeatured = $idx === 0;
                ?>
                <div class="<?php echo $isFeatured ? 'col-md-8 col-sm-12' : 'col-md-4 col-sm-6'; ?> gallery-item <?php echo $isFeatured ? 'gallery-item--featured' : ''; ?>" data-category="<?php echo htmlspecialchars($itemCat); ?>" tabindex="0" role="button" aria-label="Lihat foto <?php echo htmlspecialchars($item['title']); ?>">
                    <img src="<?php echo htmlspecialchars(asset_src($item['image'])); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" loading="lazy" onerror="this.onerror=null;this.src='<?php echo htmlspecialchars(asset_src('assets/img/hero-bg.jpg')); ?>';">
                    <div class="overlay">
                        <span class="overlay-cat"><?php echo htmlspecialchars(galleryCatLabel($itemCat)); ?></span>
                        <h5><?php echo htmlspecialchars($item['title']); ?></h5>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div id="galleryEmptyState" class="text-center py-5 d-none" role="status" aria-live="polite">
                <div class="d-inline-flex flex-column align-items-center gap-2">
                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width:56px;height:56px;"><i class="fas fa-images text-muted"></i></div>
                    <div class="fw-semibold text-dark">Tidak ada foto untuk kategori ini</div>
                    <div class="small text-muted">Coba pilih <button type="button" class="btn btn-link btn-sm p-0 fw-bold" data-empty-filter="all">Semua</button> atau kategori lain.</div>
                </div>
            </div>
            <!-- Expand / Collapse Button -->
            <div class="text-center mt-5" id="galleryExpandContainer" style="display: none;"></div>
        </div>
    </section>

    <!-- Gallery Lightbox Modal — Premium -->
    <div class="modal fade gallery-lightbox" id="galleryLightboxModal" tabindex="-1" aria-hidden="true" aria-labelledby="galleryLightboxTitle" data-bs-backdrop="true" data-bs-keyboard="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content bg-transparent border-0 shadow-none">
                <div class="gallery-lb-wrapper position-relative mx-auto" style="max-width: 960px; width: 100%;">
                    <!-- Close -->
                    <button type="button" class="gallery-lb-close" data-bs-dismiss="modal" aria-label="Tutup">
                        <i class="fas fa-times"></i>
                    </button>
                    <!-- Prev / Next -->
                    <button type="button" class="gallery-lb-nav gallery-lb-prev" id="galleryLightboxPrev" aria-label="Foto sebelumnya">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button type="button" class="gallery-lb-nav gallery-lb-next" id="galleryLightboxNext" aria-label="Foto berikutnya">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                    <!-- Frame -->
                    <div class="gallery-lb-frame rounded-4 overflow-hidden shadow-lg bg-dark position-relative">
                        <img src="" id="galleryLightboxImg" class="w-100 d-block" style="max-height: 76vh; object-fit: contain; background: #0f172a;" alt="Preview Galeri">
                        <div class="gallery-lb-caption">
                            <h5 id="galleryLightboxTitle" class="gallery-lb-title mb-0"></h5>
                            <span id="galleryLightboxCounter" class="gallery-lb-counter">1 / 1</span>
                        </div>
                    </div>
                    <!-- Thumbnails -->
                    <div class="gallery-lb-thumbs" id="galleryLightboxThumbs" aria-label="Thumbnail galeri"></div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Galeri — DESIGN.md: ENERGY 2 / RHYTHM 2 / MOTION 1 — purpose per R-31 */
        .gallery-item {
            position: relative;
            cursor: pointer;
            overflow: hidden;
            border-radius: 1rem;
            box-shadow: 0 4px 14px rgba(15,23,42,0.08);
            transition: transform 0.35s ease, box-shadow 0.35s ease, opacity 0.35s ease;
            height: 280px;
            background: #e2e8f0;
        }
        .gallery-item--featured { height: 380px; }
        @media (max-width: 767px) {
            .gallery-item, .gallery-item--featured { height: 240px; }
        }
        .gallery-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 28px rgba(15,23,42,0.14);
        }
        .gallery-item img {
            width: 100%; height: 100%; object-fit: cover;
            transition: transform 0.5s ease;
        }
        .gallery-item:hover img { transform: scale(1.03); }
        .gallery-item .overlay {
            position: absolute; left: 0; right: 0; bottom: 0;
            padding: 44px 16px 14px;
            background: linear-gradient(to top, rgba(15,23,42,0.78) 0%, rgba(15,23,42,0.32) 55%, transparent 100%);
            display: flex; flex-direction: column; justify-content: flex-end;
            opacity: 0; transform: translateY(8px);
            transition: opacity 0.3s ease, transform 0.3s ease;
        }
        .gallery-item:hover .overlay,
        .gallery-item:focus-within .overlay { opacity: 1; transform: translateY(0); }
        .gallery-item .overlay-cat {
            display: inline-block; align-self: flex-start;
            font-size: 0.68rem; font-weight: 700; letter-spacing: 0.04em; text-transform: uppercase;
            color: #0c4a6e; background: rgba(255,255,255,0.92);
            padding: 3px 8px; border-radius: 999px; margin-bottom: 6px;
        }
        .gallery-item .overlay h5 {
            color: #fff; font-size: 0.92rem; font-weight: 700; line-height: 1.3; margin: 0;
            text-shadow: 0 1px 8px rgba(0,0,0,0.35);
            transform: translateY(0);
        }
        /* Filter transition — JS toggles .is-hidden */
        .gallery-item.is-hidden { opacity: 0; transform: scale(0.96); pointer-events: none; }
        .gallery-item.is-visible { opacity: 1; transform: scale(1); }
        /* +N card — foto asli jadi background, overlay gelap tipis + blur */
        .gallery-more-trigger { cursor: pointer; }
        .gallery-more-trigger .overlay { display: none !important; }
        .overlay-more-badge {
            position: absolute; inset: 0;
            background: rgba(15,23,42,0.52);
            backdrop-filter: blur(3px) saturate(1.05);
            -webkit-backdrop-filter: blur(3px) saturate(1.05);
            display: flex; align-items: center; justify-content: center;
            text-align: center; color: #fff; z-index: 2;
            transition: background 0.3s ease;
            padding: 20px;
        }
        .gallery-more-trigger:hover .overlay-more-badge { background: rgba(15,23,42,0.62); }
        .gallery-more-trigger img { filter: blur(1.5px) brightness(0.92); }
        .gallery-more-trigger:hover img { filter: blur(0px) brightness(1); }
        .overlay-more-badge .more-icon { font-size: 1.6rem; margin-bottom: 8px; opacity: 0.9; }
        .overlay-more-badge .more-number { font-size: 1.35rem; font-weight: 800; letter-spacing: -0.02em; margin-bottom: 6px; }
        .overlay-more-badge .more-text {
            font-size: 0.8rem; font-weight: 600; opacity: 0.95;
            background: rgba(255,255,255,0.18); border: 1px solid rgba(255,255,255,0.22);
            padding: 6px 14px; border-radius: 999px; display: inline-block;
        }

        /* === Gallery Lightbox Premium === */
        .gallery-lightbox .modal-dialog {
            transform: scale(0.92);
            transition: transform 0.32s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.22s ease;
        }
        .gallery-lightbox.show .modal-dialog {
            transform: scale(1);
        }
        .gallery-lightbox .modal-content {
            background: transparent !important;
        }
        .modal-backdrop.show {
            background: rgba(6, 12, 24, 0.82) !important;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            opacity: 1 !important;
        }
        .gallery-lb-wrapper {
            padding: 18px 14px 10px;
        }
        .gallery-lb-frame {
            border-radius: 1.25rem;
            background: #0f172a;
            box-shadow: 0 25px 60px rgba(0,0,0,0.45);
        }
        #galleryLightboxImg {
            border-radius: 1.25rem;
            transition: opacity 0.22s ease, transform 0.22s ease;
            background: #0f172a;
            display: block;
        }
        /* Close — semi-transparan gelap + X putih */
        .gallery-lb-close {
            position: absolute;
            top: 0;
            right: 0;
            z-index: 30;
            width: 44px;
            height: 44px;
            border: none;
            border-radius: 50%;
            background: rgba(15, 23, 42, 0.78);
            border: 1px solid rgba(255,255,255,0.22);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.05rem;
            transition: all 0.22s ease;
            box-shadow: 0 8px 18px rgba(0,0,0,0.28);
        }
        .gallery-lb-close:hover {
            background: rgba(15, 23, 42, 0.88);
            transform: scale(1.08) rotate(90deg);
            border-color: rgba(255,255,255,0.36);
            color: #fff;
        }
        .gallery-lb-close:active { transform: scale(0.96) rotate(90deg); }
        /* Nav arrows */
        .gallery-lb-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            z-index: 22;
            width: 48px;
            height: 48px;
            border: none;
            border-radius: 50%;
            background: rgba(15, 23, 42, 0.72);
            border: 1px solid rgba(255,255,255,0.18);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.05rem;
            transition: all 0.22s ease;
            box-shadow: 0 8px 20px rgba(0,0,0,0.28);
        }
        .gallery-lb-nav:hover {
            background: linear-gradient(135deg, #0c4a6e, #0ea5e9);
            border-color: transparent;
            transform: translateY(-50%) scale(1.08);
        }
        .gallery-lb-nav:active { transform: translateY(-50%) scale(0.96); }
        .gallery-lb-prev { left: 8px; }
        .gallery-lb-next { right: 8px; }
        @media (min-width: 992px) {
            .gallery-lb-prev { left: -18px; }
            .gallery-lb-next { right: -18px; }
            .gallery-lb-wrapper { padding: 8px 32px 10px; }
        }
        /* Caption gradient overlay */
        .gallery-lb-caption {
            position: absolute;
            left: 0; right: 0; bottom: 0;
            padding: 54px 18px 16px;
            background: linear-gradient(to top, rgba(0,0,0,0.84) 0%, rgba(0,0,0,0.52) 46%, transparent 100%);
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 12px;
            pointer-events: none;
            border-bottom-left-radius: 1.25rem;
            border-bottom-right-radius: 1.25rem;
        }
        .gallery-lb-title {
            color: #fff;
            font-weight: 600;
            font-size: clamp(0.92rem, 2vw, 1.08rem);
            line-height: 1.35;
            text-shadow: 0 2px 10px rgba(0,0,0,0.55);
            margin: 0;
        }
        .gallery-lb-counter {
            flex-shrink: 0;
            background: rgba(255,255,255,0.16);
            border: 1px solid rgba(255,255,255,0.22);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            color: #fff;
            font-weight: 700;
            font-size: 0.78rem;
            letter-spacing: 0.3px;
            padding: 7px 12px;
            border-radius: 999px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            white-space: nowrap;
        }
        /* Thumbnails strip */
        .gallery-lb-thumbs {
            display: flex;
            gap: 8px;
            overflow-x: auto;
            overflow-y: hidden;
            padding: 12px 2px 6px;
            scrollbar-width: thin;
            scrollbar-color: rgba(255,255,255,0.28) transparent;
            scroll-behavior: smooth;
            justify-content: flex-start;
        }
        @media (min-width: 768px) { .gallery-lb-thumbs { justify-content: center; } }
        .gallery-lb-thumbs::-webkit-scrollbar { height: 6px; }
        .gallery-lb-thumbs::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.28); border-radius: 999px; }
        .gallery-lb-thumbs::-webkit-scrollbar-track { background: transparent; }
        .gallery-lb-thumb {
            flex: 0 0 auto;
            width: 64px;
            height: 64px;
            border-radius: 0.72rem;
            overflow: hidden;
            border: 2px solid transparent;
            opacity: 0.62;
            cursor: pointer;
            transition: all 0.22s ease;
            background: #0f172a;
            padding: 0;
        }
        .gallery-lb-thumb img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .gallery-lb-thumb:hover { opacity: 1; transform: translateY(-2px); border-color: rgba(255,255,255,0.34); }
        .gallery-lb-thumb.active {
            opacity: 1;
            border-color: #0ea5e9;
            box-shadow: 0 0 0 3px rgba(14,165,233,0.26), 0 8px 18px rgba(0,0,0,0.32);
            transform: translateY(-1px);
        }
        @media (max-width: 576px) {
            .gallery-lb-wrapper { padding: 46px 8px 8px; }
            .gallery-lb-close { top: 2px; right: 2px; width: 40px; height: 40px; font-size: 1rem; }
            .gallery-lb-nav { width: 42px; height: 42px; font-size: 0.96rem; }
            .gallery-lb-prev { left: 6px; }
            .gallery-lb-next { right: 6px; }
            .gallery-lb-caption { padding: 40px 14px 12px; }
            .gallery-lb-thumb { width: 54px; height: 54px; }
            #galleryLightboxImg { max-height: 62vh !important; }
        }
    </style>
