<?php
// includes/chat_widget.php
// Widget Chat Interaktif untuk Pengunjung Umum (Anonim) dan Siswa Terdaftar (LMS)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$mcmChatCsrf = json_encode($_SESSION['csrf_token'] ?? '');
$mcmChatUserId = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true ? (int)($_SESSION['user_id'] ?? 0) : 0;
$mcmChatUserName = isset($_SESSION['user_name']) ? (string)$_SESSION['user_name'] : '';
$chatWidgetBase = isset($base_url) ? $base_url : (str_contains($_SERVER['SCRIPT_NAME'] ?? '', '/lms/') ? '../' : '');
?>
<div id="mcmChatWidget">
    <div class="mcm-chat-panel" id="mcmChatPanel">
        <div class="mcm-chat-head">
            <div class="d-flex align-items-center">
                <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 38px; height: 38px;">
                    <i class="fas <?php echo $mcmChatUserId > 0 ? 'fa-graduation-cap' : 'fa-comment-dots'; ?> text-white"></i>
                </div>
                <div>
                    <div class="fw-bold text-white" style="font-size: 0.95rem;">
                        <?php echo $mcmChatUserId > 0 ? 'Bantuan Siswa MCM' : 'Chat MCM'; ?>
                    </div>
                    <div class="small text-white-50" style="font-size: 0.72rem;">
                        <?php echo $mcmChatUserId > 0 ? 'Siswa: ' . htmlspecialchars($mcmChatUserName) : 'Balasan instan oleh bot & admin'; ?>
                    </div>
                </div>
            </div>
            <button type="button" class="btn btn-link text-white-50 p-0" id="mcmChatClose"><i class="fas fa-times"></i></button>
        </div>
        <div class="mcm-chat-body" id="mcmChatBody">
            <div class="text-center text-muted small py-4">
                <span class="spinner-border spinner-border-sm me-1"></span>Memuat percakapan...
            </div>
        </div>
        <div class="mcm-chat-chips" id="mcmChatChips">
            <?php if ($mcmChatUserId > 0): ?>
                <button type="button" data-q="kapan jadwal pelatihan mulai?">Jadwal Kelas</button>
                <button type="button" data-q="bagaimana cara dapat sertifikat?">Sertifikat</button>
                <button type="button" data-q="apa saja modul materi?">Materi & Quiz</button>
                <button type="button" data-q="halo min mau tanya">Tanya Admin</button>
            <?php else: ?>
                <?php
                $chipIntents = [];
                if (isset($pdo)) {
                    try { $chipIntents = $pdo->query("SELECT intent, keywords FROM chatbot_intents WHERE enabled=1 ORDER BY id ASC")->fetchAll(); } catch (Exception $e) {}
                }
                if (!empty($chipIntents)) {
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
            <?php endif; ?>
        </div>
        <div class="mcm-chat-input">
            <input type="text" id="mcmChatText" placeholder="Ketik pesan..." maxlength="500">
            <button type="button" id="mcmChatSend" aria-label="Kirim pesan"><i class="fas fa-paper-plane"></i></button>
        </div>
    </div>

    <!-- Floating Toast Preview jika ada balasan admin saat panel tertutup -->
    <div class="mcm-chat-toast" id="mcmChatToast" style="display:none;" title="Klik untuk membuka chat">
        <div class="mcm-chat-toast-avatar"><i class="fas fa-headset"></i></div>
        <div class="mcm-chat-toast-body">
            <div class="mcm-chat-toast-sender">Admin MCM</div>
            <div class="mcm-chat-toast-text" id="mcmChatToastText">...</div>
        </div>
        <button type="button" class="mcm-chat-toast-close" id="mcmChatToastClose" aria-label="Tutup notifikasi">&times;</button>
    </div>

    <!-- Floating Action Button (FAB) -->
    <button type="button" class="mcm-chat-fab" id="mcmChatFab" aria-label="Chat">
        <i class="fas fa-comment-dots"></i>
        <span class="mcm-chat-badge" id="mcmChatBadge" aria-label="Pesan baru dari admin" role="status"></span>
    </button>
</div>

<style>
    .mcm-chat-fab { position: fixed; right: 24px; bottom: 24px; z-index: 1040; width: 56px; height: 56px; border: none; border-radius: 50%; background: linear-gradient(135deg, #0c4a6e, #0ea5e9); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 24px; box-shadow: 0 8px 20px rgba(14, 165, 233, 0.45); transition: transform 0.2s; cursor: pointer; }
    .mcm-chat-fab:hover { transform: scale(1.08); }
    .mcm-chat-badge { position: absolute; top: -3px; right: -3px; min-width: 22px; height: 22px; padding: 0 5px; background: #ef4444; color: #fff; font-size: 11px; font-weight: 700; border: 2.5px solid #fff; border-radius: 999px; display: none; align-items: center; justify-content: center; z-index: 10; pointer-events: none; box-shadow: 0 2px 8px rgba(239, 68, 68, 0.6); animation: mcmBadgePulse 1.8s infinite; }
    .mcm-chat-badge.show { display: flex !important; }
    @keyframes mcmBadgePulse { 0%{box-shadow: 0 0 0 0 rgba(239,68,68,0.7); transform: scale(1);} 50%{box-shadow: 0 0 0 8px rgba(239,68,68,0); transform: scale(1.08);} 100%{box-shadow: 0 0 0 0 rgba(239,68,68,0); transform: scale(1);} }

    .mcm-chat-toast { position: fixed; right: 24px; bottom: 90px; z-index: 1039; max-width: 310px; background: #ffffff; border-radius: 16px; padding: 10px 14px; display: flex; align-items: flex-start; gap: 10px; box-shadow: 0 12px 30px rgba(15, 23, 42, 0.2), 0 0 0 1px rgba(14, 165, 233, 0.25); cursor: pointer; animation: mcmToastSlide 0.35s cubic-bezier(.16,1,.3,1) both; }
    .mcm-chat-toast:hover { transform: translateY(-2px); box-shadow: 0 16px 36px rgba(15, 23, 42, 0.25), 0 0 0 1px rgba(14, 165, 233, 0.4); }
    .mcm-chat-toast-avatar { width: 34px; height: 34px; border-radius: 50%; background: linear-gradient(135deg, #0c4a6e, #0ea5e9); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0; }
    .mcm-chat-toast-body { flex: 1; min-width: 0; }
    .mcm-chat-toast-sender { font-size: 0.76rem; font-weight: 700; color: #0369a1; margin-bottom: 2px; }
    .mcm-chat-toast-text { font-size: 0.82rem; color: #334155; line-height: 1.35; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .mcm-chat-toast-close { background: none; border: none; font-size: 18px; line-height: 1; color: #94a3b8; padding: 0 2px; cursor: pointer; }
    .mcm-chat-toast-close:hover { color: #334155; }
    @keyframes mcmToastSlide { 0%{opacity:0; transform: translateY(12px) scale(0.95);} 100%{opacity:1; transform: translateY(0) scale(1);} }

    .mcm-chat-panel { position: fixed; right: 24px; bottom: 92px; z-index: 1040; width: min(360px, calc(100vw - 32px)); height: 480px; max-height: min(480px, calc(100vh - 140px)); background: #fff; border-radius: 1rem; overflow: hidden; box-shadow: 0 20px 50px rgba(2, 6, 23, 0.25); display: none; flex-direction: column; }
    .mcm-chat-panel.open { display: flex; }
    .mcm-chat-head { display: flex; align-items: center; justify-content: space-between; padding: 14px 16px; background: linear-gradient(135deg, #0c4a6e, #0ea5e9); }
    .mcm-chat-body { flex: 1; overflow-y: auto; padding: 14px; background: #f8fafc; }
    .mcm-chat-body .mcm-msg { max-width: 82%; padding: 8px 12px; border-radius: 12px; margin-bottom: 8px; font-size: 0.85rem; line-height: 1.45; white-space: pre-line; word-break: break-word; }
    .mcm-msg.mcm-in { background: linear-gradient(135deg, #0c4a6e, #0ea5e9); color: #fff; border-top-right-radius: 4px; margin-left: auto; box-shadow: 0 2px 6px rgba(14, 165, 233, 0.25); }
    .mcm-msg.mcm-in .mcm-time { color: rgba(255, 255, 255, 0.75); text-align: right; }
    .mcm-msg.mcm-out { background: #fff; color: #1e293b; border: 1px solid #e2e8f0; border-top-left-radius: 4px; margin-right: auto; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); }
    .mcm-msg.mcm-out .mcm-time { color: #94a3b8; text-align: left; }
    .mcm-msg.mcm-admin { background: #fefce8; border: 1px solid #fde047; color: #854d0e; border-top-left-radius: 4px; margin-right: auto; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); }
    .mcm-msg.mcm-admin .mcm-time { color: #a16207; text-align: left; }
    .mcm-msg .mcm-time { display: block; font-size: 0.65rem; opacity: 0.75; margin-top: 3px; }
    .mcm-chat-chips { display: flex; flex-wrap: wrap; gap: 6px; padding: 8px 14px; border-top: 1px solid #e2e8f0; }
    .mcm-chat-chips button { border: 1px solid #cbd5e1; background: #fff; color: #0c4a6e; border-radius: 999px; padding: 4px 12px; font-size: 0.75rem; font-weight: 600; }
    .mcm-chat-chips button:hover { background: #f1f5f9; }
    .mcm-chat-input { display: flex; gap: 8px; padding: 10px 12px; border-top: 1px solid #e2e8f0; }
    .mcm-chat-input input { flex: 1; border: 1px solid #e2e8f0; border-radius: 999px; padding: 8px 14px; font-size: 0.85rem; outline: none; }
    .mcm-chat-input button { border: none; border-radius: 50%; width: 38px; height: 38px; background: linear-gradient(135deg, #0c4a6e, #0ea5e9); color: #fff; }
    @media (max-width: 991.98px) {
        .mcm-chat-fab { right: 16px; bottom: 16px; width: 52px; height: 52px; font-size: 22px; }
        .mcm-chat-toast { right: 16px; bottom: 76px; max-width: calc(100vw - 32px); }
        .mcm-chat-panel { right: 12px; bottom: 76px; width: min(360px, calc(100vw - 24px)); max-height: calc(100vh - 100px); }
    }
    @media (max-width: 575.98px) {
        .mcm-chat-panel { right: 8px; left: 8px; width: auto; bottom: 72px; }
    }
</style>

<script>
(function() {
    const csrf = <?php echo $mcmChatCsrf; ?>;
    const chatUserId = <?php echo (int)$mcmChatUserId; ?>;
    const isUser = chatUserId > 0;
    const apiBase = <?php echo json_encode($chatWidgetBase); ?>;

    const fab = document.getElementById('mcmChatFab');
    const panel = document.getElementById('mcmChatPanel');
    const body = document.getElementById('mcmChatBody');
    const text = document.getElementById('mcmChatText');
    const badge = document.getElementById('mcmChatBadge');
    const toast = document.getElementById('mcmChatToast');
    const toastText = document.getElementById('mcmChatToastText');
    const toastClose = document.getElementById('mcmChatToastClose');

    let opened = false;
    let pollTimer = null;
    let lastMessages = [];
    let originalDocTitle = document.title;
    let titleInterval = null;
    let lastChimedId = 0;

    // Untuk pengunjung anonim: generate & simpan vid di localStorage/cookie
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
    let vid = isUser ? '' : getVid();

    function lsKey() {
        return isUser ? ('mcmChatLastSeen_u' + chatUserId) : ('mcmChatLastSeen_' + vid);
    }

    function getLastSeenId() {
        var raw = localStorage.getItem(lsKey());
        if (!raw) return 0;
        var val = parseInt(raw, 10);
        if (isNaN(val) || val > 10000000) {
            localStorage.removeItem(lsKey());
            return 0;
        }
        return val;
    }

    function setLastSeenId(id) {
        if (id > 0) {
            localStorage.setItem(lsKey(), String(id));
        }
    }

    function esc(s) {
        return String(s).replace(/[&<>"']/g, function(c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }

    function render(messages) {
        if (!messages || messages.length === 0) {
            if (isUser) {
                body.innerHTML = '<div class="text-center text-muted small py-4"><i class="fas fa-graduation-cap text-primary fs-3 mb-2 d-block"></i>Halo <strong>' + esc(<?php echo json_encode($mcmChatUserName); ?>) + '</strong>!<br>Ada yang bisa kami bantu seputar kelas atau materi pelatihan Anda?</div>';
            } else {
                body.innerHTML = '<div class="text-center text-muted small py-4"><i class="fas fa-comments text-primary fs-3 mb-2 d-block"></i>Belum ada percakapan.<br>Tulis pesan atau pilih tombol pertanyaan di bawah.</div>';
            }
            return;
        }
        body.innerHTML = messages.map(function(m) {
            var isIn = m.direction === 'in';
            var isAdmin = m.sender_type === 'admin';
            var dir = isIn ? 'mcm-in' : (isAdmin ? 'mcm-admin' : 'mcm-out');
            var label = isAdmin ? '<span class="badge bg-warning text-dark ms-1" style="font-size:0.6rem;">Admin</span>' : '';
            var statusHtml = '';
            if (isIn) {
                var isRead = m.is_read == 1 || m.is_read === true;
                if (isRead) {
                    var readTime = m.read_time ? (' Dibaca ' + esc(m.read_time)) : ' Dibaca';
                    var fullTooltip = m.read_full_time ? ('Dibaca oleh admin: ' + esc(m.read_full_time)) : 'Sudah dibaca admin';
                    statusHtml = ' <span class="mcm-status-read" title="' + fullTooltip + '" style="color:#7dd3fc; margin-left:5px; font-size:0.68rem; font-weight:500;"><i class="fas fa-check-double"></i>' + readTime + '</span>';
                } else {
                    statusHtml = ' <span class="mcm-status-sent" title="Terkirim" style="opacity:0.6; margin-left:5px; font-size:0.68rem;"><i class="fas fa-check-double"></i> Terkirim</span>';
                }
            }
            return '<div class="mcm-msg ' + dir + '" data-id="' + esc(m.id || '') + '">' + esc(m.message) + label + '<span class="mcm-time">' + esc(m.time || '') + statusHtml + '</span></div>';
        }).join('');
        body.scrollTop = body.scrollHeight;
    }

    function renderError(msg) {
        body.innerHTML = '<div class="text-center py-4"><div class="text-danger small mb-2"><i class="fas fa-exclamation-triangle me-1"></i>' + esc(msg) + '</div><button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" id="mcmChatRetry">Coba lagi</button></div>';
        var btn = document.getElementById('mcmChatRetry');
        if (btn) btn.addEventListener('click', loadHistory);
    }

    function playChime() {
        try {
            var AudioCtx = window.AudioContext || window.webkitAudioContext;
            if (!AudioCtx) return;
            var ctx = new AudioCtx();
            var now = ctx.currentTime;
            var o1 = ctx.createOscillator();
            var g1 = ctx.createGain();
            o1.frequency.setValueAtTime(587.33, now);
            g1.gain.setValueAtTime(0.12, now);
            g1.gain.exponentialRampToValueAtTime(0.001, now + 0.22);
            o1.connect(g1);
            g1.connect(ctx.destination);
            o1.start(now);
            o1.stop(now + 0.22);

            var o2 = ctx.createOscillator();
            var g2 = ctx.createGain();
            o2.frequency.setValueAtTime(880, now + 0.12);
            g2.gain.setValueAtTime(0.15, now + 0.12);
            g2.gain.exponentialRampToValueAtTime(0.001, now + 0.4);
            o2.connect(g2);
            g2.connect(ctx.destination);
            o2.start(now + 0.12);
            o2.stop(now + 0.4);
        } catch (e) {}
    }

    function getUnreadAdminMessages(msgs) {
        var lastSeen = getLastSeenId();
        return (msgs || []).filter(function(m) {
            if (m.sender_type === 'admin' && m.direction === 'out') {
                var id = parseInt(m.id || 0, 10);
                return id > lastSeen;
            }
            return false;
        });
    }

    function showNotification(unread) {
        if (opened || !unread || unread.length === 0) return;
        var latestMsg = unread[unread.length - 1];
        var latestId = parseInt(latestMsg.id || 0, 10);

        if (badge) {
            badge.textContent = unread.length > 9 ? '9+' : (unread.length > 1 ? unread.length : '');
            badge.classList.add('show');
        }
        if (toast && toastText && toast.style.display === 'none') {
            toastText.textContent = latestMsg.message || 'Pesan baru dari admin';
            toast.style.display = 'flex';
        }
        if (!titleInterval) {
            var toggle = false;
            titleInterval = setInterval(function() {
                document.title = (toggle ? '💬 (1) Balasan Admin! - ' : '') + originalDocTitle;
                toggle = !toggle;
            }, 1000);
        }
        if (latestId > lastChimedId) {
            lastChimedId = latestId;
            playChime();
        }
    }

    function hideNotification() {
        if (badge) badge.classList.remove('show');
        if (toast) toast.style.display = 'none';
        if (titleInterval) {
            clearInterval(titleInterval);
            titleInterval = null;
            document.title = originalDocTitle;
        }
    }

    function markAllRead() {
        hideNotification();
        var maxId = 0;
        (lastMessages || []).forEach(function(m) {
            if (m.sender_type === 'admin' && m.direction === 'out') {
                var id = parseInt(m.id || 0, 10);
                if (id > maxId) maxId = id;
            }
        });
        if (maxId > 0) setLastSeenId(maxId);
    }

    function getHistoryUrl() {
        var url = apiBase + 'chat/chat_api.php?action=history&_t=' + Date.now();
        if (isUser) {
            url += '&user_id=' + encodeURIComponent(chatUserId);
        } else {
            url += '&visitor_id=' + encodeURIComponent(vid);
        }
        return url;
    }

    function loadHistory() {
        if (!body.dataset.hasContent) {
            body.innerHTML = '<div class="text-center text-muted small py-4"><span class="spinner-border spinner-border-sm me-1"></span>Memuat percakapan...</div>';
        }
        fetch(getHistoryUrl(), { cache: 'no-store' })
            .then(function(r) { return r.json(); })
            .then(function(d) {
                if (d.status === 'success') {
                    body.dataset.hasContent = '1';
                    lastMessages = d.messages || [];
                    render(lastMessages);
                    if (opened) {
                        markAllRead();
                    } else {
                        var unread = getUnreadAdminMessages(lastMessages);
                        if (unread.length > 0) showNotification(unread);
                    }
                } else {
                    renderError(d.message || 'Gagal memuat percakapan.');
                }
            })
            .catch(function(err) {
                renderError('Gagal memuat percakapan, coba lagi');
            });
    }

    function send(message) {
        const msg = (message || text.value).trim();
        if (!msg) return;
        text.value = '';

        var bodyData = 'action=send&csrf_token=' + encodeURIComponent(csrf) + '&message=' + encodeURIComponent(msg);
        if (!isUser) {
            bodyData += '&visitor_id=' + encodeURIComponent(vid);
        }

        fetch(apiBase + 'chat/chat_api.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: bodyData
        })
        .then(function(r) { return r.json(); })
        .then(function(d) {
            if (d.status === 'error') {
                console.error('[MCM Chat] send error:', d);
            }
            loadHistory();
        })
        .catch(function(err) {
            console.error('[MCM Chat] send fail:', err);
            loadHistory();
        });
    }

    function bgPoll() {
        if (opened) return;
        fetch(getHistoryUrl(), { cache: 'no-store' })
            .then(function(r) { return r.ok ? r.json() : null; })
            .then(function(d) {
                if (d && d.status === 'success') {
                    lastMessages = d.messages || [];
                    var unread = getUnreadAdminMessages(lastMessages);
                    if (unread.length > 0) {
                        showNotification(unread);
                    } else {
                        hideNotification();
                    }
                }
            })
            .catch(function() {});
    }

    function openChat() {
        opened = true;
        panel.classList.add('open');
        markAllRead();
        loadHistory();
        if (pollTimer) clearInterval(pollTimer);
        pollTimer = setInterval(loadHistory, 3000);
        setTimeout(function() { text.focus(); }, 150);
    }

    function closeChat() {
        opened = false;
        panel.classList.remove('open');
        if (pollTimer) clearInterval(pollTimer);
        pollTimer = setInterval(bgPoll, 6000);
    }

    fab.addEventListener('click', function() {
        if (opened) closeChat(); else openChat();
    });

    document.getElementById('mcmChatClose').addEventListener('click', closeChat);

    if (toast) {
        toast.addEventListener('click', function(e) {
            if (e.target === toastClose || (toastClose && toastClose.contains(e.target))) {
                e.stopPropagation();
                toast.style.display = 'none';
                return;
            }
            fab.click();
        });
    }

    document.getElementById('mcmChatSend').addEventListener('click', function() { send(); });
    text.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            send();
        }
    });

    document.querySelectorAll('#mcmChatChips button').forEach(function(b) {
        b.addEventListener('click', function() {
            send(this.getAttribute('data-q'));
        });
    });

    // Start background poll & initial load check
    bgPoll();
    pollTimer = setInterval(bgPoll, 6000);
})();
</script>
