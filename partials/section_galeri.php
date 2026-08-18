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
                $c = $item['category'];
                if ($c !== '' && $c !== 'all') $galleryCats[$c] = true;
            }
            $galleryCats = array_keys($galleryCats);
            function galleryCatLabel($slug) {
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
                <div class="col-md-4 col-sm-6 gallery-item" data-category="<?php echo htmlspecialchars($item['category']); ?>">
                    <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                    <div class="overlay"><h5><?php echo htmlspecialchars($item['title']); ?></h5></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
