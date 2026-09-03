document.addEventListener('DOMContentLoaded', function () {
    // 1. Initialize AOS (Animate On Scroll)
    AOS.init({
        duration: 800,
        easing: 'ease-in-out',
        once: true,
        offset: 100
    });

    // 2. Sticky Navbar & Mobile Toggle Fix
    const navbar = document.querySelector('.navbar');
    const navbarCollapse = document.getElementById('navbarNav');
    const navbarToggler = document.querySelector('.navbar-toggler');
    const navLinks = document.querySelectorAll('.nav-link');

    const handleScroll = () => {
        if (window.scrollY > 50 || (navbarCollapse && navbarCollapse.classList.contains('show'))) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    };

    window.addEventListener('scroll', handleScroll);

    // Ensure background stays solid when mobile menu is toggled
    if (navbarCollapse) {
        navbarCollapse.addEventListener('show.bs.collapse', () => navbar.classList.add('scrolled'));
    }

    // Handle closing when link is clicked
    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth < 992 && navbarCollapse.classList.contains('show')) {
                const bsCollapse = bootstrap.Collapse.getOrCreateInstance(navbarCollapse);
                bsCollapse.hide();
            }
        });
    });

    // Scroll to top when burger is closed manually
    if (navbarCollapse) {
        navbarCollapse.addEventListener('hide.bs.collapse', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    // Fix Modal Close Bug (Overflow & Backdrop)
    document.addEventListener('hidden.bs.modal', function () {
        if (!document.querySelector('.modal.show')) {
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) backdrop.remove();
        }
    });
    // 3. Gallery Filtering & Max 6 Limitation with "+X Foto Lainnya" + Premium Lightbox
    const filterBtns = document.querySelectorAll('.filter-btn');
    const galleryItems = document.querySelectorAll('.gallery-item');
    const galleryGrid = document.getElementById('galleryGrid');
    const galleryExpandContainer = document.getElementById('galleryExpandContainer');
    const lightboxModalEl = document.getElementById('galleryLightboxModal');
    const lightboxModal = lightboxModalEl ? new bootstrap.Modal(lightboxModalEl) : null;
    const lbImg = document.getElementById('galleryLightboxImg');
    const lbTitle = document.getElementById('galleryLightboxTitle');
    const lbCounter = document.getElementById('galleryLightboxCounter');
    const lbThumbs = document.getElementById('galleryLightboxThumbs');
    const lbPrev = document.getElementById('galleryLightboxPrev');
    const lbNext = document.getElementById('galleryLightboxNext');
    const lbFrame = document.querySelector('.gallery-lb-frame');

    let isGalleryExpanded = false;
    let currentGalleryFilter = 'all';
    let lbList = [];
    let lbIndex = 0;

    function buildLbThumbs() {
        if (!lbThumbs) return;
        lbThumbs.innerHTML = '';
        lbList.forEach((it, i) => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'gallery-lb-thumb' + (i === lbIndex ? ' active' : '');
            btn.setAttribute('aria-label', 'Foto ' + (i + 1) + ': ' + it.title);
            btn.innerHTML = '<img src="' + it.src + '" alt="' + it.title.replace(/"/g, '&quot;') + '" loading="lazy">';
            btn.addEventListener('click', function() { showLbAt(i); });
            lbThumbs.appendChild(btn);
        });
        const active = lbThumbs.querySelector('.gallery-lb-thumb.active');
        if (active) active.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
    }

    function updateLb() {
        if (!lbList.length) return;
        const it = lbList[lbIndex];
        if (lbImg) {
            lbImg.style.opacity = '0.35';
            const newSrc = it.src;
            const newAlt = it.title;
            setTimeout(function() {
                lbImg.src = newSrc;
                lbImg.alt = newAlt;
                if (lbImg.complete) lbImg.style.opacity = '1';
                else lbImg.onload = function() { lbImg.style.opacity = '1'; };
            }, 110);
        }
        if (lbTitle) lbTitle.textContent = it.title;
        if (lbCounter) lbCounter.textContent = (lbIndex + 1) + ' / ' + lbList.length;
        const single = lbList.length <= 1;
        if (lbPrev) lbPrev.style.display = single ? 'none' : '';
        if (lbNext) lbNext.style.display = single ? 'none' : '';
        if (lbThumbs) {
            lbThumbs.querySelectorAll('.gallery-lb-thumb').forEach(function(el, i) {
                el.classList.toggle('active', i === lbIndex);
            });
            const act = lbThumbs.querySelector('.gallery-lb-thumb.active');
            if (act) act.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
        }
    }

    function showLbAt(i) {
        if (!lbList.length) return;
        lbIndex = (i + lbList.length) % lbList.length;
        updateLb();
    }

    function openLbAt(i) {
        lbIndex = i;
        buildLbThumbs();
        updateLb();
        if (lightboxModal) lightboxModal.show();
    }

    function renderGallery(filter = 'all', expanded = false) {
        currentGalleryFilter = filter;
        isGalleryExpanded = expanded;

        const matchingItems = [];
        galleryItems.forEach(item => {
            const cat = item.getAttribute('data-category');
            const isMatch = filter === 'all' || cat === filter;
            if (isMatch) {
                matchingItems.push(item);
            } else {
                // fade-out before hide — premium transition
                item.classList.add('is-hidden');
                item.classList.remove('is-visible', 'gallery-more-trigger');
                const prevBadge = item.querySelector('.overlay-more-badge');
                if (prevBadge) prevBadge.remove();
                setTimeout(() => {
                    if (item.classList.contains('is-hidden')) item.style.display = 'none';
                }, 320);
            }
        });

        // Build lightbox list from filtered items (for navigation + counter)
        lbList = matchingItems.map(function(el) {
            const img = el.querySelector('img');
            const titleEl = el.querySelector('.overlay h5');
            return {
                src: img ? img.src : '',
                title: titleEl ? titleEl.textContent.trim() : 'Dokumentasi Pelatihan MCM',
                el: el
            };
        });

        const totalMatching = matchingItems.length;
        const maxVisible = 6;
        const showLimit = (expanded || totalMatching <= maxVisible) ? totalMatching : maxVisible;

        // R-27 empty state
        const emptyEl = document.getElementById('galleryEmptyState');
        if (emptyEl) {
            if (totalMatching === 0) {
                emptyEl.classList.remove('d-none');
                if (galleryGrid) galleryGrid.setAttribute('aria-hidden', 'true');
            } else {
                emptyEl.classList.add('d-none');
                if (galleryGrid) galleryGrid.removeAttribute('aria-hidden');
            }
        }

        matchingItems.forEach((item, index) => {
            item.classList.remove('gallery-more-trigger');
            const prevBadge = item.querySelector('.overlay-more-badge');
            if (prevBadge) prevBadge.remove();

            if (index < showLimit) {
                item.style.display = 'block';
                item.classList.remove('is-hidden');
                // trigger reflow then fade-in
                void item.offsetWidth;
                item.classList.add('is-visible');
                item.style.opacity = '';
                item.style.transform = '';

                // If not expanded and this is the 6th item (index == 5) and there are more items
                if (!expanded && index === maxVisible - 1 && totalMatching > maxVisible) {
                    const remainingCount = totalMatching - maxVisible;
                    item.classList.add('gallery-more-trigger');
                    
                    const moreOverlay = document.createElement('div');
                    moreOverlay.className = 'overlay-more-badge';
                    moreOverlay.innerHTML = `
                        <div class="more-content">
                            <div class="more-icon"><i class="fas fa-images"></i></div>
                            <div class="more-number">+${remainingCount} Foto</div>
                            <div class="more-text">Lihat Lebih Banyak <i class="fas fa-chevron-right ms-1"></i></div>
                        </div>
                    `;
                    item.appendChild(moreOverlay);
                }
            } else {
                item.classList.add('is-hidden');
                item.classList.remove('is-visible');
                item.style.display = 'none';
            }
        });

        if (galleryExpandContainer) {
            if (expanded && totalMatching > maxVisible) {
                galleryExpandContainer.innerHTML = `
                    <button class="btn btn-outline-primary rounded-pill px-4 py-2 fw-bold shadow-sm" id="btnCollapseGallery">
                        <i class="fas fa-chevron-up me-2"></i>Tampilkan Lebih Sedikit
                    </button>
                `;
                galleryExpandContainer.style.display = 'block';
                const btnCollapse = document.getElementById('btnCollapseGallery');
                if (btnCollapse) {
                    btnCollapse.addEventListener('click', () => {
                        renderGallery(currentGalleryFilter, false);
                        const sec = document.getElementById('galeri');
                        if (sec) sec.scrollIntoView({ behavior: 'smooth' });
                    });
                }
            } else {
                galleryExpandContainer.innerHTML = '';
                galleryExpandContainer.style.display = 'none';
            }
        }
    }

    // R-32 keyboard: Enter/Space on gallery-item
    if (galleryGrid) {
        galleryGrid.addEventListener('keydown', function(e) {
            const item = e.target.closest('.gallery-item');
            if (!item) return;
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                item.click();
            }
        });
        // empty state "Semua" button
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('[data-empty-filter="all"]');
            if (!btn) return;
            filterBtns.forEach(b => b.classList.remove('active'));
            const allBtn = document.querySelector('.filter-btn[data-filter="all"]');
            if (allBtn) allBtn.classList.add('active');
            renderGallery('all', false);
        });
        galleryGrid.addEventListener('click', function(e) {
            const moreTrigger = e.target.closest('.gallery-more-trigger');
            if (moreTrigger) {
                e.preventDefault();
                e.stopPropagation();
                renderGallery(currentGalleryFilter, true);
                return;
            }

            const item = e.target.closest('.gallery-item');
            if (item && lightboxModal) {
                const idx = lbList.findIndex(function(o) { return o.el === item; });
                if (idx >= 0) openLbAt(idx);
                else {
                    // fallback
                    const img = item.querySelector('img');
                    const titleEl = item.querySelector('.overlay h5');
                    const title = titleEl ? titleEl.textContent : 'Dokumentasi Pelatihan MCM';
                    if (img && lbImg && lbTitle) {
                        lbImg.src = img.src;
                        lbTitle.textContent = title;
                        lightboxModal.show();
                    }
                }
            }
        });
    }

    if (lbPrev) lbPrev.addEventListener('click', function(e) { e.stopPropagation(); showLbAt(lbIndex - 1); });
    if (lbNext) lbNext.addEventListener('click', function(e) { e.stopPropagation(); showLbAt(lbIndex + 1); });

    // Keyboard navigation when lightbox open
    function handleLbKey(e) {
        if (!lightboxModalEl || !lightboxModalEl.classList.contains('show')) return;
        if (e.key === 'ArrowLeft') { e.preventDefault(); showLbAt(lbIndex - 1); }
        else if (e.key === 'ArrowRight') { e.preventDefault(); showLbAt(lbIndex + 1); }
    }
    if (lightboxModalEl) {
        lightboxModalEl.addEventListener('shown.bs.modal', function() {
            document.addEventListener('keydown', handleLbKey);
        });
        lightboxModalEl.addEventListener('hidden.bs.modal', function() {
            document.removeEventListener('keydown', handleLbKey);
        });
        // Click on frame image also next (optional, but not conflicting with nav)
        if (lbFrame) {
            let touchStartX = 0;
            lbFrame.addEventListener('touchstart', function(e) { touchStartX = e.touches[0].clientX; }, { passive: true });
            lbFrame.addEventListener('touchend', function(e) {
                const dx = e.changedTouches[0].clientX - touchStartX;
                if (Math.abs(dx) > 48) {
                    if (dx < 0) showLbAt(lbIndex + 1);
                    else showLbAt(lbIndex - 1);
                }
            }, { passive: true });
        }
    }

    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            filterBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            const filterValue = btn.getAttribute('data-filter');
            renderGallery(filterValue, false);
        });
    });

    // Initial load
    if (galleryItems.length > 0) {
        renderGallery('all', false);
    }



        // Swiper Initialization
        const swiper = new Swiper('.paketSwiper', {
            slidesPerView: 1,
            spaceBetween: 30,
            autoHeight: false, 
            observer: true,
            observeParents: true,
            resizeObserver: true,
            loop: false,
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            breakpoints: {
                768: { slidesPerView: 2 },
                1024: { slidesPerView: 3 }
            }
        });

    // Setup modal objects
    const detailModalEl = document.getElementById('detailModal');
    const detailModal = detailModalEl ? new bootstrap.Modal(detailModalEl) : null;
    const checkoutModalEl = document.getElementById('checkoutModal');
    const checkoutModal = checkoutModalEl ? new bootstrap.Modal(checkoutModalEl) : null;

    let currentSelectedClass = null;

    // Buy Now Buttons -> Show Detail Modal First
    const buyNowBtns = document.querySelectorAll('.btn-buy-now');
    buyNowBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const classId = this.getAttribute('data-id');
            const data = window.mcmClassDetails ? window.mcmClassDetails[classId] : null;
            
            if (data) {
                currentSelectedClass = data;
                
                // Populate Detail Modal
                const titleEl = document.getElementById('detailModalTitle');
                if (titleEl) titleEl.textContent = data.name;
                
                const descEl = document.getElementById('detailModalDesc');
                if (descEl) descEl.textContent = data.description;
                
                const imgEl = document.getElementById('detailModalImage');
                if (imgEl) imgEl.src = data.image;
                
                const featuresContainer = document.getElementById('detailModalFeatures');
                featuresContainer.innerHTML = '';
                
                data.features.forEach(f => {
                    const featureHtml = `
                        <div class="col-md-6 mb-2">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <span class="text-secondary small fw-medium">${f}</span>
                            </div>
                        </div>
                    `;
                    featuresContainer.innerHTML += featureHtml;
                });

                if (detailModal) detailModal.show();
            }
        });
    });

    // Checkout Mode handling (Online/Offline)
    let checkoutSelectedMode = 'offline';
    let checkoutPriceOnline = 0;
    let checkoutPriceOffline = 0;
    let checkoutModeAvailable = 'both';
    function updateCheckoutPrice() {
        const isOnline = checkoutSelectedMode === 'online';
        const price = isOnline ? checkoutPriceOnline : checkoutPriceOffline;
        const priceEl = document.getElementById('checkoutClassPrice');
        const totalEl = document.getElementById('checkoutTotalPrice');
        const modeInfo = document.getElementById('checkoutModeInfo');
        if (priceEl) priceEl.textContent = 'Rp ' + price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        if (totalEl) totalEl.textContent = 'Rp ' + price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        document.querySelectorAll('input[name="class_mode"]').forEach(function(el){ el.value = checkoutSelectedMode; });
        document.querySelectorAll('#checkoutClassMode').forEach(function(el){ el.value = checkoutSelectedMode; });
        if (modeInfo) {
            if (checkoutModeAvailable === 'both') {
                modeInfo.classList.remove('d-none');
                modeInfo.innerHTML = isOnline ? '<i class="fas fa-laptop me-1 text-info"></i> Online: fleksibel, hemat 20%' : '<i class="fas fa-chalkboard-teacher me-1 text-success"></i> Offline: praktik langsung & advance';
            } else modeInfo.classList.add('d-none');
        }
        document.querySelectorAll('.mode-checkout-btn').forEach(function(btn){
            const m = btn.getAttribute('data-mode');
            if (m === checkoutSelectedMode) {
                btn.classList.add('active');
                btn.style.background = 'linear-gradient(135deg, var(--primary-color), var(--secondary-color))';
                btn.style.color = 'white';
                btn.classList.remove('text-muted');
            } else {
                btn.classList.remove('active');
                btn.style.background = 'transparent';
                btn.style.color = '';
                btn.classList.add('text-muted');
            }
        });
        document.querySelectorAll('#checkoutModeWrap').forEach(function(wrap){
            wrap.style.display = checkoutModeAvailable === 'both' ? '' : 'none';
        });
    }
    document.querySelectorAll('.mode-checkout-btn').forEach(function(btn){
        btn.addEventListener('click', function(){ checkoutSelectedMode = this.getAttribute('data-mode'); updateCheckoutPrice(); });
    });
    document.getElementById('btnLanjutCheckout')?.addEventListener('click', function(){
        if (!currentSelectedClass) return;
        const d = currentSelectedClass;
        checkoutPriceOnline = parseInt(d.price_online) || Math.round(parseInt(d.price||0)*0.8);
        checkoutPriceOffline = parseInt(d.price_offline) || parseInt(d.price||0);
        checkoutModeAvailable = d.mode_available || 'both';
        if (checkoutModeAvailable === 'online') checkoutSelectedMode = 'online';
        else if (checkoutModeAvailable === 'offline') checkoutSelectedMode = 'offline';
        else checkoutSelectedMode = 'offline';
        document.querySelectorAll('#checkoutClassId').forEach(function(el){ el.value = d.id; });
        const nameEl = document.getElementById('checkoutClassName');
        if (nameEl) nameEl.textContent = d.name;
        updateCheckoutPrice();
        if (detailModal) detailModal.hide();
        setTimeout(function(){ if (checkoutModal) checkoutModal.show(); }, 300);
    });
    if (checkoutModalEl) {
        checkoutModalEl.addEventListener('show.bs.modal', function(){
            if (currentSelectedClass) {
                checkoutPriceOnline = parseInt(currentSelectedClass.price_online) || Math.round(parseInt(currentSelectedClass.price||0)*0.8);
                checkoutPriceOffline = parseInt(currentSelectedClass.price_offline) || parseInt(currentSelectedClass.price||0);
                checkoutModeAvailable = currentSelectedClass.mode_available || 'both';
                updateCheckoutPrice();
            }
        });
    }

    // Robust Smooth Scrolling with accurate Navbar Offset
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const targetId = this.getAttribute('href');
            if (!targetId || targetId === '#') return;
            
            const target = document.querySelector(targetId);
            if (target) {
                e.preventDefault();
                const nav = document.querySelector('.navbar');
                const navHeight = nav ? (nav.offsetHeight || 80) : 80;
                
                // Traverse offsetParent chain to get true absolute document top
                let elementTop = 0;
                let el = target;
                while (el) {
                    elementTop += el.offsetTop || 0;
                    el = el.offsetParent;
                }
                
                const targetPosition = Math.max(0, elementTop - navHeight - 15);

                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });

                if (history.pushState) {
                    history.pushState(null, null, targetId);
                }

                // Close mobile menu if open
                const navCollapse = document.getElementById('navbarNav');
                if (navCollapse && navCollapse.classList.contains('show')) {
                    const bsCollapse = bootstrap.Collapse.getInstance(navCollapse);
                    if (bsCollapse) bsCollapse.hide();
                }
            }
        });
    });
});
