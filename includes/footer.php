    <?php
    $script_file = str_replace('\\', '/', $_SERVER['SCRIPT_FILENAME'] ?? '');
    $app_root = str_replace('\\', '/', dirname(__DIR__));
    if ($script_file !== '' && strpos($script_file, $app_root) === 0) {
        $rel = trim(substr(str_replace('\\', '/', dirname($script_file)), strlen($app_root)), '/');
        $base_url = ($rel === '') ? '' : str_repeat('../', substr_count($rel, '/') + 1);
    } else {
        $script_dir = dirname($_SERVER['SCRIPT_NAME'] ?? '/');
        $base_url = ($script_dir === '/' || $script_dir === '' || $script_dir === '\\') ? '' : str_repeat('../', substr_count(rtrim($script_dir, '/'), '/'));
    }
    $footerLogo = function_exists('mcm_setting') ? mcm_setting('logo_url', $base_url.'assets/img/logo.png') : $base_url.'assets/img/logo.png';
    ?>
    <!-- Footer -->
    <footer class="pt-5 pb-4" style="background-color: #0f172a !important; color: white;">
        <div class="container">
            <?php
            // Data dinamis footer — dipakai di kolom Office & Ikuti Kami
            $fAddress = mcm_setting('admin_address', 'Jl. Khp Hasan Mustopa No.57, Neglasari, Kec. Cibeunying Kaler, Kota Bandung, Jawa Barat 40124');
            $fWa      = preg_replace('/\D/', '', mcm_setting('admin_whatsapp', '628978902864'));
            if (strpos($fWa, '0') === 0) $fWa = '62' . substr($fWa, 1);
            if (empty($fWa)) $fWa = '628978902864';
            $fEmail   = mcm_setting('admin_email', '');
            $fIg      = trim(mcm_setting('social_ig', ''));
            $fFb      = trim(mcm_setting('social_fb', ''));
            $fTt      = trim(mcm_setting('social_tt', ''));
            $fMaps    = trim(mcm_setting('maps_url', ''));
            $is_home  = basename($_SERVER['SCRIPT_FILENAME'] ?? '') === 'index.php';
            $__fHome  = $is_home ? '#beranda' : ($base_url . 'index.php');
            ?>
            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="d-flex align-items-center mb-4">
                        <img src="<?php echo htmlspecialchars($footerLogo); ?>" alt="MCM Logo" style="height: 45px; filter: brightness(0) invert(1);">
                        <div class="ms-2 ps-2 border-start border-2 border-light d-flex flex-column justify-content-center" style="height: 35px;">
                            <span class="fw-bold text-white" style="font-size: 0.75rem; letter-spacing: 1px; line-height: 1.1;">MITRA CIPTA</span>
                            <span class="fw-bold text-white" style="font-size: 0.75rem; letter-spacing: 1px; line-height: 1.1;">MANDIRI</span>
                        </div>
                    </div>
                    <p class="text-white-50">MCM - Mitra Cipta Mandiri adalah lembaga pelatihan vokasi premium yang berfokus pada pengembangan skill praktis untuk kemandirian ekonomi.</p>
                </div>
                <div class="col-lg-2 col-md-6">
                    <h5 class="fw-bold mb-4">Menu</h5>
                    <ul class="list-unstyled text-white-50">
                        <li><a href="<?php echo htmlspecialchars($__fHome); ?>" class="text-white-50 text-decoration-none mb-2 d-block">Beranda</a></li>
                        <li><a href="<?php echo $base_url; ?>pages/about.php" class="text-white-50 text-decoration-none mb-2 d-block">Tentang Kami</a></li>
                        <li><a href="<?php echo $base_url; ?>pages/programs.php" class="text-white-50 text-decoration-none mb-2 d-block">Program Pelatihan</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h5 class="fw-bold mb-4">Ikuti Kami</h5>
                    <div class="d-flex gap-3">
                        <?php if ($fIg !== '' && $fIg !== '#'): ?><a href="<?php echo htmlspecialchars($fIg); ?>" target="_blank" rel="noopener" class="text-white fs-4" title="Instagram"><i class="fab fa-instagram"></i></a><?php endif; ?>
                        <?php if ($fFb !== '' && $fFb !== '#'): ?><a href="<?php echo htmlspecialchars($fFb); ?>" target="_blank" rel="noopener" class="text-white fs-4" title="Facebook"><i class="fab fa-facebook"></i></a><?php endif; ?>
                        <?php if ($fTt !== '' && $fTt !== '#'): ?><a href="<?php echo htmlspecialchars($fTt); ?>" target="_blank" rel="noopener" class="text-white fs-4" title="TikTok"><i class="fab fa-tiktok"></i></a><?php endif; ?>
                        <a href="https://wa.me/<?php echo htmlspecialchars($fWa); ?>" target="_blank" rel="noopener" class="text-white fs-4" title="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                    </div>
                    <p class="small text-white-50 mt-3 mb-0" style="line-height: 1.6;">Dapatkan update terbaru seputar program & kegiatan MCM.</p>
                </div>
                <div class="col-lg-4 col-md-6">
                    <h5 class="fw-bold mb-4">Office</h5>
                    <?php if ($fAddress !== ''): ?>
                    <p class="text-white-50 mb-2">
                        <a href="<?php echo $fMaps !== '' ? htmlspecialchars($fMaps) : 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($fAddress); ?>" target="_blank" rel="noopener" class="text-white-50 text-decoration-none" style="line-height: 1.6;">
                            <i class="fas fa-map-marker-alt me-2"></i><?php echo htmlspecialchars($fAddress); ?>
                        </a>
                    </p>
                    <?php endif; ?>
                    <p class="text-white-50 mb-2"><i class="fas fa-phone-alt me-2"></i> +<?php echo htmlspecialchars($fWa); ?></p>
                    <?php if ($fEmail !== ''): ?>
                    <p class="text-white-50 mb-0"><i class="fas fa-envelope me-2"></i> <?php echo htmlspecialchars($fEmail); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <hr class="mt-5 border-white-50">
            <div class="text-center text-white-50">
                <small>&copy; 2026 Mitra Cipta Mandiri. Hak Cipta Dilindungi.</small>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?php echo $base_url; ?>assets/js/script.js?v=<?php echo time(); ?>"></script>
    <script>
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true
        });
    </script>
    <script>
        function submitPayment(form) {
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn ? submitBtn.innerHTML : '';
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
            }
            fetch(form.getAttribute('action'), {
                method: form.getAttribute('method') || 'POST',
                body: new FormData(form)
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    window.location.href = data.payment_url;
                } else {
                    if (submitBtn) { submitBtn.disabled = false; submitBtn.innerHTML = originalText; }
                    if (window.Swal) Swal.fire('Gagal', data.message, 'error');
                    else alert(data.message);
                }
            })
            .catch(() => {
                if (submitBtn) { submitBtn.disabled = false; submitBtn.innerHTML = originalText; }
                if (window.Swal) Swal.fire('Error', 'Terjadi kesalahan sistem.', 'error');
                else alert('Terjadi kesalahan sistem.');
            });
            return false;
        }
    </script>

    <!-- Scroll to Top — progress ring biru MCM -->
    <button type="button" id="mcmScrollTop" class="mcm-scrolltop" aria-label="Kembali ke atas">
        <svg class="mcm-scrolltop-svg" width="48" height="48" viewBox="0 0 48 48" aria-hidden="true">
            <circle class="mcm-scrolltop-track" cx="24" cy="24" r="20" fill="none" stroke="#e2e8f0" stroke-width="3"/>
            <circle class="mcm-scrolltop-progress" cx="24" cy="24" r="20" fill="none" stroke="#0ea5e9" stroke-width="3" stroke-linecap="round" transform="rotate(-90 24 24)" stroke-dasharray="125.66" stroke-dashoffset="125.66"/>
        </svg>
        <i class="fas fa-arrow-up"></i>
    </button>
    <style>
        .mcm-scrolltop{position:fixed;right:24px;bottom:96px;z-index:1035;width:48px;height:48px;border:none;border-radius:50%;background:#fff;color:#0c4a6e;display:flex;align-items:center;justify-content:center;box-shadow:0 8px 20px rgba(15,23,42,0.12),0 2px 8px rgba(15,23,42,0.08);opacity:0;visibility:hidden;transform:translateY(8px) scale(0.96);transition:opacity 0.3s ease,visibility 0.3s ease,transform 0.3s ease,box-shadow 0.2s ease;cursor:pointer}
        .mcm-scrolltop.visible{opacity:1;visibility:visible;transform:translateY(0) scale(1)}
        .mcm-scrolltop.chat-open{opacity:0 !important;visibility:hidden !important;transform:translateY(8px) scale(0.96) !important;pointer-events:none}
        .mcm-scrolltop:hover{box-shadow:0 12px 28px rgba(14,165,233,0.22),0 4px 12px rgba(15,23,42,0.10);transform:translateY(-1px) scale(1.02)}
        .mcm-scrolltop:active{transform:scale(0.97)}
        .mcm-scrolltop-svg{position:absolute;inset:0;width:48px;height:48px;pointer-events:none}
        .mcm-scrolltop-progress{transition:stroke-dashoffset 0.15s linear}
        .mcm-scrolltop i{position:relative;z-index:1;font-size:14px}
        @media(max-width:991.98px){.mcm-scrolltop{right:16px;bottom:88px;width:44px;height:44px}.mcm-scrolltop-svg{width:44px;height:44px}}
        @media(max-width:575.98px){.mcm-scrolltop{bottom:84px}}
        /* chat input send button selalu di atas */
        #mcmChatWidget .mcm-chat-input button{position:relative;z-index:2}
        #mcmChatWidget{z-index:1040}
        #mcmChatWidget .mcm-chat-panel{z-index:1041}
        #mcmChatWidget .mcm-chat-fab{z-index:1040}
    </style>
    <script>
    (function(){
        const btn=document.getElementById('mcmScrollTop');
        if(!btn) return;
        const progress=btn.querySelector('.mcm-scrolltop-progress');
        const panel=document.getElementById('mcmChatPanel');
        const circumference=2*Math.PI*20; // 125.66
        let ticking=false;
        function isChatOpen(){ return panel && panel.classList.contains('open'); }
        function update(){
            const scrollTop=window.scrollY||document.documentElement.scrollTop;
            const docHeight=document.documentElement.scrollHeight - window.innerHeight;
            const pct=docHeight>0?Math.min(scrollTop/docHeight,1):0;
            if(progress) progress.style.strokeDashoffset=(circumference - pct*circumference).toFixed(2);
            if(scrollTop>300 && !isChatOpen()) btn.classList.add('visible');
            else btn.classList.remove('visible');
            if(isChatOpen()) btn.classList.add('chat-open');
            else btn.classList.remove('chat-open');
            ticking=false;
        }
        function onScroll(){ if(!ticking){ ticking=true; requestAnimationFrame(update); } }
        window.addEventListener('scroll', onScroll, {passive:true});
        window.addEventListener('resize', onScroll);
        btn.addEventListener('click', function(){ window.scrollTo({top:0, behavior:'smooth'}); });
        // pantau buka/tutup chat
        if(panel){
            const obs=new MutationObserver(update);
            obs.observe(panel, {attributes:true, attributeFilter:['class']});
        }
        // init
        update();
    })();
    </script>

    <?php require_once __DIR__ . '/chat_widget.php'; ?>

</body>
</html>
