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
            $fWa      = preg_replace('/\D/', '', mcm_setting('admin_whatsapp', '6285793935707'));
            $fEmail   = mcm_setting('admin_email', '');
            $fIg      = trim(mcm_setting('social_ig', ''));
            $fFb      = trim(mcm_setting('social_fb', ''));
            $fTt      = trim(mcm_setting('social_tt', ''));
            $fMaps    = trim(mcm_setting('maps_url', ''));
            $__fHome = '/index.php'; if(!empty($_SERVER['SCRIPT_NAME'])&&str_contains($_SERVER['SCRIPT_NAME'],'/MitraCiptaMandiriWeb/')) $__fHome='/MitraCiptaMandiriWeb/index.php';
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

    <?php $mcmChatCsrf = json_encode($_SESSION['csrf_token'] ?? ''); ?>
    <div id="mcmChatWidget">
        <div class="mcm-chat-panel" id="mcmChatPanel">
            <div class="mcm-chat-head">
                <div class="d-flex align-items-center">
                    <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 38px; height: 38px;">
                        <i class="fas fa-comment-dots text-white"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-white" style="font-size: 0.95rem;">Chat MCM</div>
                        <div class="small text-white-50" style="font-size: 0.72rem;">Balasan instan oleh bot</div>
                    </div>
                </div>
                <button type="button" class="btn btn-link text-white-50 p-0" id="mcmChatClose"><i class="fas fa-times"></i></button>
            </div>
            <div class="mcm-chat-body" id="mcmChatBody">
                <div class="text-center text-muted small py-4">Memuat percakapan...</div>
            </div>
            <div class="mcm-chat-chips" id="mcmChatChips">
                <?php
                $chipIntents = [];
                try { $chipIntents = $pdo->query("SELECT intent, keywords FROM chatbot_intents WHERE enabled=1 ORDER BY id ASC")->fetchAll(); } catch (Exception $e) {}
                if ($chipIntents) {
                    foreach ($chipIntents as $ci) {
                        $label = ucfirst(htmlspecialchars($ci['intent']));
                        $kws = array_values(array_filter(array_map('trim', explode(',', $ci['keywords']))));
                        $q = $kws[0] ?? $ci['intent'];
                        echo '<button type="button" data-q="'.htmlspecialchars($q).'">'.$label.'</button>';
                    }
                } else {
                    echo '<button type="button" data-q="berapa harga kelas?">Harga</button>';
                    echo '<button type="button" data-q="kapan jadwal pelatihan mulai?">Jadwal</button>';
                    echo '<button type="button" data-q="apa saja program pelatihan?">Program</button>';
                    echo '<button type="button" data-q="dimana alamat MCM?">Lokasi</button>';
                }
                ?>
            </div>
            <div class="mcm-chat-input">
                <input type="text" id="mcmChatText" placeholder="Ketik pesan..." maxlength="500">
                <button type="button" id="mcmChatSend"><i class="fas fa-paper-plane"></i></button>
            </div>
        </div>
        <button type="button" class="mcm-chat-fab" id="mcmChatFab" aria-label="Chat">
            <i class="fas fa-comment-dots"></i>
        </button>
    </div>
    <style>
        .mcm-chat-fab { position: fixed; right: 24px; bottom: 24px; z-index: 1040; width: 56px; height: 56px; border: none; border-radius: 50%; background: linear-gradient(135deg, #0c4a6e, #0ea5e9); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 24px; box-shadow: 0 8px 20px rgba(14, 165, 233, 0.45); transition: transform 0.2s; }
        .mcm-chat-fab:hover { transform: scale(1.08); }
        .mcm-chat-panel { position: fixed; right: 24px; bottom: 92px; z-index: 1040; width: min(360px, calc(100vw - 32px)); height: 480px; max-height: min(480px, calc(100vh - 140px)); background: #fff; border-radius: 1rem; overflow: hidden; box-shadow: 0 20px 50px rgba(2, 6, 23, 0.25); display: none; flex-direction: column; }
        .mcm-chat-panel.open { display: flex; }
        .mcm-chat-head { display: flex; align-items: center; justify-content: space-between; padding: 14px 16px; background: linear-gradient(135deg, #0c4a6e, #0ea5e9); }
        .mcm-chat-body { flex: 1; overflow-y: auto; padding: 14px; background: #f8fafc; }
        .mcm-chat-body .mcm-msg { max-width: 82%; padding: 8px 12px; border-radius: 12px; margin-bottom: 8px; font-size: 0.85rem; line-height: 1.45; white-space: pre-line; }
        .mcm-msg.mcm-in { background: #fff; border: 1px solid #e2e8f0; border-top-left-radius: 4px; }
        .mcm-msg.mcm-out { background: linear-gradient(135deg, #0c4a6e, #0ea5e9); color: #fff; border-top-right-radius: 4px; margin-left: auto; }
        .mcm-msg.mcm-admin { background: #fef3c7; border: 1px solid #fcd34d; color: #92400e; border-top-right-radius: 4px; margin-left: auto; }
        .mcm-msg .mcm-time { display: block; font-size: 0.65rem; opacity: 0.65; margin-top: 3px; }
        .mcm-chat-chips { display: flex; flex-wrap: wrap; gap: 6px; padding: 8px 14px; border-top: 1px solid #e2e8f0; }
        .mcm-chat-chips button { border: 1px solid #cbd5e1; background: #fff; color: #0c4a6e; border-radius: 999px; padding: 4px 12px; font-size: 0.75rem; font-weight: 600; }
        .mcm-chat-chips button:hover { background: #f1f5f9; }
        .mcm-chat-input { display: flex; gap: 8px; padding: 10px 12px; border-top: 1px solid #e2e8f0; }
        .mcm-chat-input input { flex: 1; border: 1px solid #e2e8f0; border-radius: 999px; padding: 8px 14px; font-size: 0.85rem; outline: none; }
        .mcm-chat-input button { border: none; border-radius: 50%; width: 38px; height: 38px; background: linear-gradient(135deg, #0c4a6e, #0ea5e9); color: #fff; }
        @media (max-width: 991.98px) {
            .mcm-chat-fab { right: 16px; bottom: 16px; width: 52px; height: 52px; font-size: 22px; }
            .mcm-chat-panel { right: 12px; bottom: 76px; width: min(360px, calc(100vw - 24px)); max-height: calc(100vh - 100px); }
        }
        @media (max-width: 575.98px) {
            .mcm-chat-panel { right: 8px; left: 8px; width: auto; bottom: 72px; }
        }
    </style>
    <script>
    (function() {
        const csrf = <?php echo $mcmChatCsrf; ?>;
        const fab = document.getElementById('mcmChatFab');
        const panel = document.getElementById('mcmChatPanel');
        const body = document.getElementById('mcmChatBody');
        const text = document.getElementById('mcmChatText');
        let opened = false;
        let pollTimer = null;
        function getVid() {
            let v = localStorage.getItem('mcmChatVid');
            if (!v || !/^[a-f0-9]{12}$/.test(v)) {
                const m = document.cookie.match(/(?:^|; )mcmChatVid=([a-f0-9]{12})/);
                if (m) v = m[1];
            }
            if (!v || !/^[a-f0-9]{12}$/.test(v)) {
                v = Math.random().toString(16).slice(2,14).padEnd(12,'0').slice(0,12);
            }
            localStorage.setItem('mcmChatVid', v);
            document.cookie = 'mcmChatVid=' + v + '; expires=' + new Date(Date.now()+90*24*60*60*1000).toUTCString() + '; path=/';
            return v;
        }
        let vid = getVid();

        function esc(s) {
            return String(s).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
        }

        function render(messages) {
            body.innerHTML = messages.map(m => {
                const isIn = m.direction === 'in';
                const isAdmin = m.sender_type === 'admin';
                const dir = isIn ? 'mcm-in' : (isAdmin ? 'mcm-admin' : 'mcm-out');
                const label = isAdmin ? '<span class="badge bg-warning text-dark ms-1" style="font-size:0.6rem;">Admin</span>' : '';
                return '<div class="mcm-msg ' + dir + '">' + esc(m.message) + label + '<span class="mcm-time">' + esc(m.time || '') + '</span></div>';
            }).join('') || '<div class="text-center text-muted small py-3">Belum ada percakapan. Tulis pesan untuk bertanya.</div>';
            body.scrollTop = body.scrollHeight;
        }

        function renderError(msg) {
            body.innerHTML = '<div class="text-center py-4"><div class="text-danger small mb-2"><i class="fas fa-exclamation-triangle me-1"></i>' + esc(msg) + '</div><button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" id="mcmChatRetry">Coba lagi</button></div>';
            var btn = document.getElementById('mcmChatRetry');
            if (btn) btn.addEventListener('click', loadHistory);
        }

        function loadHistory() {
            // tampilkan loading hanya jika masih kosong
            if (!body.dataset.hasContent) body.innerHTML = '<div class="text-center text-muted small py-4"><span class="spinner-border spinner-border-sm me-1"></span>Memuat percakapan...</div>';
            fetch('<?php echo $base_url; ?>chat/chat_api.php?action=history&visitor_id=' + encodeURIComponent(vid), { headers: { 'Accept': 'application/json' } })
                .then(function(r) {
                    if (!r.ok) throw new Error('HTTP ' + r.status);
                    return r.text().then(function(t) {
                        try { return JSON.parse(t); }
                        catch(e) { throw new Error('Response bukan JSON: ' + t.slice(0, 150)); }
                    });
                })
                .then(function(d) {
                    if (d.status === 'success') {
                        if (d.visitor_id) { vid = d.visitor_id; localStorage.setItem('mcmChatVid', vid); }
                        body.dataset.hasContent = '1';
                        render(d.messages);
                    } else {
                        renderError(d.message || 'Gagal memuat percakapan.');
                        console.error('[MCM Chat] history error:', d);
                    }
                })
                .catch(function(err) {
                    renderError('Gagal memuat percakapan, coba lagi');
                    console.error('[MCM Chat] fetch history failed:', err);
                });
        }

        function send(message) {
            const msg = (message || text.value).trim();
            if (!msg) return;
            text.value = '';
            fetch('<?php echo $base_url; ?>chat/chat_api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=send&csrf_token=' + encodeURIComponent(csrf) + '&message=' + encodeURIComponent(msg) + '&visitor_id=' + encodeURIComponent(vid)
            })
            .then(function(r) {
                if (!r.ok) throw new Error('HTTP '+r.status);
                return r.text().then(function(t){ try{ return JSON.parse(t);} catch(e){ throw new Error('Response bukan JSON: '+t.slice(0,150)); }});
            })
            .then(function(d) {
                if (d.status === 'error') {
                    // tampilkan error tapi tetap render history agar tidak stuck
                    console.error('[MCM Chat] send error:', d);
                    // optional: tampilkan toast
                    if (d.message && window.Swal) Swal.fire('Gagal', d.message, 'error');
                }
                if (d.visitor_id) { vid = d.visitor_id; localStorage.setItem('mcmChatVid', vid); }
                loadHistory();
            })
            .catch(function(err){
                console.error('[MCM Chat] send fail:', err);
                if (window.Swal) Swal.fire('Error', 'Gagal mengirim pesan, coba lagi', 'error');
                loadHistory();
            });
        }

        fab.addEventListener('click', () => {
            opened = !opened;
            panel.classList.toggle('open', opened);
            if (opened) {
                loadHistory();
                pollTimer = setInterval(loadHistory, 5000);
            } else if (pollTimer) {
                clearInterval(pollTimer);
            }
        });
        document.getElementById('mcmChatClose').addEventListener('click', () => {
            opened = false;
            panel.classList.remove('open');
            if (pollTimer) clearInterval(pollTimer);
        });
        document.getElementById('mcmChatSend').addEventListener('click', () => send());
        text.addEventListener('keydown', e => { if (e.key === 'Enter') send(); });
        document.querySelectorAll('#mcmChatChips button').forEach(b => b.addEventListener('click', () => send(b.dataset.q)));
    })();
    </script>
</body>
</html>
