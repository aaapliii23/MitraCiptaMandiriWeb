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
    // 3. Gallery Filtering & Max 6 Limitation with "+X Foto Lainnya"
    const filterBtns = document.querySelectorAll('.filter-btn');
    const galleryItems = document.querySelectorAll('.gallery-item');
    const galleryGrid = document.getElementById('galleryGrid');
    const galleryExpandContainer = document.getElementById('galleryExpandContainer');
    const lightboxModalEl = document.getElementById('galleryLightboxModal');
    const lightboxModal = lightboxModalEl ? new bootstrap.Modal(lightboxModalEl) : null;

    let isGalleryExpanded = false;
    let currentGalleryFilter = 'all';

    function renderGallery(filter = 'all', expanded = false) {
        currentGalleryFilter = filter;
        isGalleryExpanded = expanded;

        const matchingItems = [];
        galleryItems.forEach(item => {
            const cat = item.getAttribute('data-category');
            if (filter === 'all' || cat === filter) {
                matchingItems.push(item);
            } else {
                item.style.display = 'none';
                item.classList.remove('gallery-more-trigger');
                const prevBadge = item.querySelector('.overlay-more-badge');
                if (prevBadge) prevBadge.remove();
            }
        });

        const totalMatching = matchingItems.length;
        const maxVisible = 6;
        const showLimit = (expanded || totalMatching <= maxVisible) ? totalMatching : maxVisible;

        matchingItems.forEach((item, index) => {
            item.classList.remove('gallery-more-trigger');
            const prevBadge = item.querySelector('.overlay-more-badge');
            if (prevBadge) prevBadge.remove();

            if (index < showLimit) {
                item.style.display = 'block';
                setTimeout(() => {
                    item.style.opacity = '1';
                    item.style.transform = 'scale(1)';
                }, 30);

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
                item.style.display = 'none';
                item.style.opacity = '0';
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

    if (galleryGrid) {
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
                const img = item.querySelector('img');
                const titleEl = item.querySelector('.overlay h5');
                const title = titleEl ? titleEl.textContent : 'Dokumentasi Pelatihan MCM';
                if (img) {
                    document.getElementById('galleryLightboxImg').src = img.src;
                    document.getElementById('galleryLightboxTitle').textContent = title;
                    lightboxModal.show();
                }
            }
        });
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

    const btnLanjutCheckout = document.getElementById('btnLanjutCheckout');
    if (btnLanjutCheckout) {
        btnLanjutCheckout.addEventListener('click', function() {
            if (currentSelectedClass) {
                if (detailModal) detailModal.hide();
                if (typeof populateCheckoutModal === 'function') {
                    populateCheckoutModal(currentSelectedClass);
                }
                if (checkoutModal) checkoutModal.show();
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

