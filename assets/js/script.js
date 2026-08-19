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
    // 3. Gallery Filtering
    const filterBtns = document.querySelectorAll('.filter-btn');
    const galleryItems = document.querySelectorAll('.gallery-item');

    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            // Remove active class from all
            filterBtns.forEach(b => b.classList.remove('active'));
            // Add active class to clicked
            btn.classList.add('active');

            const filterValue = btn.getAttribute('data-filter');

            galleryItems.forEach(item => {
                if (filterValue === 'all' || item.getAttribute('data-category') === filterValue) {
                    item.style.display = 'block';
                    // Trigger reflow for animation
                    setTimeout(() => {
                        item.style.opacity = '1';
                        item.style.transform = 'scale(1)';
                    }, 50);
                } else {
                    item.style.opacity = '0';
                    item.style.transform = 'scale(0.8)';
                    setTimeout(() => {
                        item.style.display = 'none';
                    }, 300); // match transition duration
                }
            });
        });
    });



        // Swiper Initialization
        const swiper = new Swiper('.paketSwiper', {
            slidesPerView: 1,
            spaceBetween: 30,
            autoHeight: true, 
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

    // Smooth scrolling
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const target = document.querySelector(targetId);
            if (target) {
                e.preventDefault();
                const navHeight = navbar ? navbar.offsetHeight : 90;
                const elementPosition = target.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - navHeight;

                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });

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

