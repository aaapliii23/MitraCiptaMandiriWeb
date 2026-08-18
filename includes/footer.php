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
    ?>
    <!-- Footer -->
    <footer class="pt-5 pb-4" style="background-color: #0f172a !important; color: white;">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="d-flex align-items-center mb-4">
                        <img src="<?php echo $base_url; ?>assets/img/logo.png" alt="MCM Logo" style="height: 45px; filter: brightness(0) invert(1);">
                        <div class="ms-2 ps-2 border-start border-2 border-light d-flex flex-column justify-content-center" style="height: 35px;">
                            <span class="fw-bold text-white" style="font-size: 0.75rem; letter-spacing: 1px; line-height: 1.1;">MITRA CIPTA</span>
                            <span class="fw-bold text-white" style="font-size: 0.75rem; letter-spacing: 1px; line-height: 1.1;">MANDIRI</span>
                        </div>
                    </div>
                    <p class="text-white-50">MCM - Mitra Cipta Mandiri adalah lembaga pelatihan vokasi premium yang berfokus pada pengembangan skill praktis untuk kemandirian ekonomi.</p>
                </div>
                <div class="col-md-4">
                    <h5 class="fw-bold mb-4">Tautan Cepat</h5>
                    <ul class="list-unstyled text-white-50">
                        <li><a href="<?php echo $base_url; ?>index.php" class="text-white-50 text-decoration-none mb-2 d-block">Beranda</a></li>
                        <li><a href="<?php echo $base_url; ?>pages/about.php" class="text-white-50 text-decoration-none mb-2 d-block">Tentang Kami</a></li>
                        <li><a href="<?php echo $base_url; ?>pages/programs.php" class="text-white-50 text-decoration-none mb-2 d-block">Program Pelatihan</a></li>
                    </ul>
                </div>
                <div class="col-md-4 text-start text-md-end">
                    <h5 class="fw-bold mb-4">Hubungi Kami</h5>
                    <p class="text-white-50 mb-1"><i class="fas fa-map-marker-alt me-2"></i>Jl. Khp Hasan Mustopa No.57, Neglasari, Kec. Cibeunying Kaler, Kota Bandung, Jawa Barat 40124</p>
                    <p class="text-white-50 mb-3"><i class="fas fa-phone-alt me-2"></i> +62 857-9393-5707</p>
                    <div class="d-flex justify-content-md-end gap-3">
                        <a href="https://www.instagram.com/lpkmitraciptamandiri?utm_source=qr&igsh=MWpjaTY3dHh0NnY3Yg==" class="text-white fs-4"><i class="fab fa-instagram"></i></a>
                        <a href="https://www.facebook.com/p/LPK-Mitra-Cipta-Mandiri-100083627607564/" class="text-white fs-4"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="text-white fs-4"><i class="fab fa-whatsapp"></i></a>
                    </div>
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
                <button type="button" data-q="berapa harga kelas?">Harga</button>
                <button type="button" data-q="kapan jadwal pelatihan mulai?">Jadwal</button>
                <button type="button" data-q="apa saja program pelatihan?">Program</button>
                <button type="button" data-q="dimana alamat MCM?">Lokasi</button>
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
        .mcm-chat-fab { position: fixed; right: 20px; bottom: 20px; z-index: 1055; width: 56px; height: 56px; border: none; border-radius: 50%; background: linear-gradient(135deg, #0c4a6e, #0ea5e9); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 24px; box-shadow: 0 8px 20px rgba(14, 165, 233, 0.45); transition: transform 0.2s; }
        .mcm-chat-fab:hover { transform: scale(1.08); }
        .mcm-chat-panel { position: fixed; right: 20px; bottom: 88px; z-index: 1055; width: min(360px, calc(100vw - 40px)); height: 480px; max-height: calc(100vh - 120px); background: #fff; border-radius: 1rem; overflow: hidden; box-shadow: 0 20px 50px rgba(2, 6, 23, 0.25); display: none; flex-direction: column; }
        .mcm-chat-panel.open { display: flex; }
        .mcm-chat-head { display: flex; align-items: center; justify-content: space-between; padding: 14px 16px; background: linear-gradient(135deg, #0c4a6e, #0ea5e9); }
        .mcm-chat-body { flex: 1; overflow-y: auto; padding: 14px; background: #f8fafc; }
        .mcm-chat-body .mcm-msg { max-width: 82%; padding: 8px 12px; border-radius: 12px; margin-bottom: 8px; font-size: 0.85rem; line-height: 1.45; white-space: pre-line; }
        .mcm-msg.mcm-in { background: #fff; border: 1px solid #e2e8f0; border-top-left-radius: 4px; }
        .mcm-msg.mcm-out { background: linear-gradient(135deg, #0c4a6e, #0ea5e9); color: #fff; border-top-right-radius: 4px; margin-left: auto; }
        .mcm-msg .mcm-time { display: block; font-size: 0.65rem; opacity: 0.65; margin-top: 3px; }
        .mcm-chat-chips { display: flex; flex-wrap: wrap; gap: 6px; padding: 8px 14px; border-top: 1px solid #e2e8f0; }
        .mcm-chat-chips button { border: 1px solid #cbd5e1; background: #fff; color: #0c4a6e; border-radius: 999px; padding: 4px 12px; font-size: 0.75rem; font-weight: 600; }
        .mcm-chat-chips button:hover { background: #f1f5f9; }
        .mcm-chat-input { display: flex; gap: 8px; padding: 10px 12px; border-top: 1px solid #e2e8f0; }
        .mcm-chat-input input { flex: 1; border: 1px solid #e2e8f0; border-radius: 999px; padding: 8px 14px; font-size: 0.85rem; outline: none; }
        .mcm-chat-input button { border: none; border-radius: 50%; width: 38px; height: 38px; background: linear-gradient(135deg, #0c4a6e, #0ea5e9); color: #fff; }
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

        function esc(s) {
            return String(s).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
        }

        function render(messages) {
            body.innerHTML = messages.map(m => {
                const dir = m.direction === 'in' ? 'mcm-in' : 'mcm-out';
                return '<div class="mcm-msg ' + dir + '">' + esc(m.message) + '<span class="mcm-time">' + esc(m.time || '') + '</span></div>';
            }).join('') || '<div class="text-center text-muted small py-3">Belum ada percakapan. Tulis pesan untuk bertanya.</div>';
            body.scrollTop = body.scrollHeight;
        }

        function loadHistory() {
            fetch('<?php echo $base_url; ?>chat/chat_api.php?action=history')
                .then(r => r.json())
                .then(d => { if (d.status === 'success') render(d.messages); });
        }

        function send(message) {
            const msg = (message || text.value).trim();
            if (!msg) return;
            text.value = '';
            fetch('<?php echo $base_url; ?>chat/chat_api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=send&csrf_token=' + encodeURIComponent(csrf) + '&message=' + encodeURIComponent(msg)
            })
            .then(r => r.json())
            .then(d => { loadHistory(); })
            .catch(() => {});
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
