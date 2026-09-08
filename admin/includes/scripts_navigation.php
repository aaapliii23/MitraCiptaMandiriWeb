<script>
// INSTANT AJAX NAVIGATION (No Reload, No Flicker)
function initAjaxLinks() {
    document.querySelectorAll('a').forEach(link => {
        const href = link.getAttribute('href');
        if (href && href.startsWith('?page=') && !link.dataset.ajaxBound) {
            link.dataset.ajaxBound = "true";
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.getAttribute('href');
                loadContent(url, true);
            });
        }
    });
}

async function loadContent(url, pushState = true) {
    const progress = document.getElementById('ajaxProgress');
    try {
        if (progress) {
            progress.style.width = '0%';
            progress.style.display = 'block';
            setTimeout(() => progress.style.width = '30%', 10);
        }

        const pageName = new URLSearchParams(url).get('page');
        updateActiveStates(pageName);

        const response = await fetch(url);
        if (progress) progress.style.width = '70%';
        
        const html = await response.text();
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        
        const newMain = doc.querySelector('.main-content');
        if (newMain) {
            const mainContainer = document.querySelector('.main-content');
            mainContainer.innerHTML = newMain.innerHTML;
            if (pushState) history.pushState({page: pageName}, '', url);
            
            if (progress) {
                progress.style.width = '100%';
                setTimeout(() => progress.style.display = 'none', 300);
            }

            // Execute scripts in the new content (must be from the live DOM)
            mainContainer.querySelectorAll('script').forEach(oldScript => {
                const newScript = document.createElement('script');
                Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
                newScript.appendChild(document.createTextNode(oldScript.innerHTML));
                oldScript.parentNode.replaceChild(newScript, oldScript);
            });

            // Re-init AOS and Counters
            if (typeof AOS !== 'undefined') AOS.refreshHard();
            initCounters();
            initAjaxLinks();
            reinitBootstrapModals();
            if (typeof initBulkTables === 'function') initBulkTables();
            
            // Re-init Reports if on reports page
            if (pageName === 'reports' && typeof initReportsChart === 'function') {
                initReportsChart('weekly');
            }

            // (b) AJAX nav: init chart keuangan setelah konten finance ter-inject
            if (pageName === 'finance' && typeof initGrafikArusKas === 'function') {
                initGrafikArusKas();
            }

            // Clear notification badge when admin opens chat or orders
            if (typeof window.MCMNotifClearBadges === 'function') {
                window.MCMNotifClearBadges(pageName);
            }
            
            window.scrollTo(0, 0);
        }
    } catch (err) {
        if (progress) progress.style.display = 'none';
        window.location.href = url;
    }
}

function updateActiveStates(page) {
    document.querySelectorAll('.nav-link, .mcm-nav-item, .sidebar .nav-link').forEach(el => {
        const href = el.getAttribute('href');
        if (!href) return;
        let hrefPage = null;
        try {
            const url = new URL(href, window.location.origin);
            hrefPage = url.searchParams.get('page');
        } catch(e) {
            const m = href.match(/[?&]page=([^&]+)/);
            hrefPage = m ? m[1] : null;
        }
        if (hrefPage !== null) {
            if (hrefPage === page) {
                el.classList.add('active');
            } else {
                el.classList.remove('active');
            }
        }
    });
}

function initCounters() {
    document.querySelectorAll('.counter-value').forEach(counter => {
        const target = +counter.getAttribute('data-target');
        const duration = 1500;
        const increment = target / (duration / 16);
        
        let current = 0;
        const updateCount = () => {
            current += increment;
            if (current < target) {
                counter.innerText = Math.ceil(current);
                requestAnimationFrame(updateCount);
            } else {
                counter.innerText = target;
            }
        };
        updateCount();
    });
}

window.addEventListener('popstate', (e) => {
    if (e.state && e.state.page) {
        loadContent(`?page=${e.state.page}`, false);
    } else {
        window.location.reload();
    }
});

// Initial Load
document.addEventListener('DOMContentLoaded', () => {
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 800,
            once: true,
            offset: 50
        });
    }
    initCounters();
    initAjaxLinks();
    reinitBootstrapModals();
});
</script>
