<?php
// admin/pages/chat_thread.php - Tampilan percakapan individual, status dibaca/dilihat, dan form reply
$isAnonThread = str_starts_with($threadNumber, 'web-');
$isUserThread = str_starts_with($threadNumber, 'user-');
$sName = !empty($threadStudent['name']) ? $threadStudent['name'] : ('Siswa #' . substr($threadNumber, 5));
$sPhone = !empty($threadStudent['phone']) ? $threadStudent['phone'] : '';
$sEmail = !empty($threadStudent['email']) ? $threadStudent['email'] : '';
?>
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-header bg-white border-0 py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <?php if ($isUserThread): ?>
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center flex-shrink-0" style="width: 42px; height: 42px; font-size: 1.15rem;">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="fw-bold text-dark fs-6"><?php echo htmlspecialchars($sName); ?></span>
                            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2 py-1" style="font-size:0.7rem;">Siswa LMS</span>
                        </div>
                        <div class="text-muted small" style="font-size:0.75rem;">
                            <?php if ($sPhone): ?>
                                <span class="me-2"><i class="fas fa-phone-alt me-1 text-secondary" style="font-size:0.65rem;"></i><?php echo htmlspecialchars($sPhone); ?></span>
                            <?php endif; ?>
                            <?php if ($sEmail): ?>
                                <span><i class="fas fa-envelope me-1 text-secondary" style="font-size:0.65rem;"></i><?php echo htmlspecialchars($sEmail); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php elseif ($isAnonThread): ?>
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-secondary bg-opacity-10 text-secondary d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px;">
                        <i class="fas fa-desktop"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark">Pengunjung Website</div>
                        <div class="text-muted small" style="font-size:0.72rem;"><?php echo htmlspecialchars($threadNumber); ?> <span class="badge bg-secondary bg-opacity-10 text-secondary ms-1 rounded-pill" style="font-size:0.65rem;">Anonim</span></div>
                    </div>
                </div>
            <?php else: ?>
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px;">
                        <i class="fab fa-whatsapp fs-5"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark"><?php echo htmlspecialchars($threadNumber); ?></div>
                        <div class="text-muted small" style="font-size:0.72rem;">WhatsApp</div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-light text-dark border rounded-pill px-3" id="adminChatMsgCount"><?php echo count($messages); ?> pesan</span>
            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1" style="font-size:0.68rem;" title="Pembaruan otomatis realtime aktif"><i class="fas fa-circle text-success me-1" style="font-size:0.45rem; vertical-align: middle;"></i>Live</span>
        </div>
    </div>
    <div id="adminChatBody" class="card-body p-4" style="max-height: 460px; overflow-y: auto; background: #f8fafc;">
        <?php if (empty($messages)): ?>
            <div class="text-center text-muted py-5 js-chat-empty">Belum ada pesan di percakapan ini.</div>
        <?php else: ?>
            <?php foreach ($messages as $msg): 
                $isIn = $msg['direction'] === 'in'; 
                $isAdmin = ($msg['sender_type'] ?? '') === 'admin'; 
                $isRead = !empty($msg['is_read']);
            ?>
                <div class="d-flex mb-3 <?php echo $isIn ? '' : 'justify-content-end'; ?>" data-id="<?php echo (int)$msg['id']; ?>">
                    <div class="rounded-3 px-3 py-2 shadow-sm small <?php echo $isIn ? 'bg-white border' : ($isAdmin ? 'bg-warning text-dark border border-warning' : 'bg-primary text-white'); ?>" style="max-width: 75%;">
                        <div><?php echo nl2br(htmlspecialchars($msg['message'])); ?><?php if($isAdmin) echo ' <span class="badge bg-dark ms-1" style="font-size:0.6rem;">Admin</span>'; ?></div>
                        <div class="small mt-1 d-flex align-items-center justify-content-between gap-3 <?php echo $isIn ? 'text-muted' : ($isAdmin ? 'text-dark opacity-75' : 'text-white-50'); ?>">
                            <span>
                                <?php echo date('d M Y H:i', strtotime($msg['created_at'])); ?>
                                <?php if (!empty($msg['matched_intent'])): ?>
                                    <span class="badge <?php echo $isIn ? 'bg-soft-primary text-primary' : ($isAdmin ? 'bg-dark text-white' : 'bg-white bg-opacity-25 text-white'); ?> ms-1"><?php echo htmlspecialchars($msg['matched_intent']); ?></span>
                                <?php endif; ?>
                            </span>
                            <span class="js-msg-status" data-status-id="<?php echo (int)$msg['id']; ?>">
                                <?php if (!$isIn): ?>
                                    <?php if ($isRead): ?>
                                        <span class="text-primary fw-semibold" title="Dilihat siswa pada: <?php echo !empty($msg['read_at']) ? date('d M Y H:i', strtotime($msg['read_at'])) : ''; ?>"><i class="fas fa-check-double text-primary"></i> Dilihat <?php echo !empty($msg['read_at']) ? date('H:i', strtotime($msg['read_at'])) : ''; ?></span>
                                    <?php else: ?>
                                        <span class="text-muted opacity-75" title="Terkirim"><i class="fas fa-check-double text-secondary opacity-75"></i> Terkirim</span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-muted" title="Dibaca admin: <?php echo !empty($msg['read_at']) ? date('d M Y H:i', strtotime($msg['read_at'])) : ''; ?>"><i class="fas fa-check-double text-primary"></i> Dibaca</span>
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <div class="card-footer bg-white border-0 p-4">
        <div class="small mb-2 <?php echo ($isAnonThread || $isUserThread) ? 'text-muted' : 'text-success'; ?>">
            <?php if ($isUserThread): ?>
                <i class="fas fa-graduation-cap me-1 text-primary"></i>
                <span class="text-primary fw-medium">Balasan ini langsung tampil di chat widget akun siswa <?php echo htmlspecialchars($sName); ?></span>
            <?php elseif ($isAnonThread): ?>
                <i class="fas fa-desktop me-1"></i>Balasan ini hanya muncul di widget chat website pengunjung
            <?php else: ?>
                <i class="fab fa-whatsapp me-1"></i>Balasan ini akan dikirim ke WhatsApp
            <?php endif; ?>
        </div>
        <form id="chatReplyForm" class="d-flex gap-2" action="<?php echo $adminBase; ?>/actions/manage_chat.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(mcm_csrf_token()); ?>">
            <input type="hidden" name="action" value="send_reply">
            <input type="hidden" name="wa_number" value="<?php echo htmlspecialchars($threadNumber); ?>">
            <input type="text" class="form-control rounded-pill" name="message" required placeholder="Tulis balasan...">
            <button type="submit" class="btn <?php echo $isUserThread ? 'btn-primary' : ($isAnonThread ? 'btn-secondary' : 'btn-success'); ?> rounded-pill fw-bold px-4">
                <i class="fas fa-paper-plane me-2"></i>Kirim
            </button>
        </form>
    </div>
</div>

<script>
(function(){
    var body = document.getElementById('adminChatBody');
    if (!body) return;
    body.scrollTop = body.scrollHeight;

    var threadNum = <?php echo json_encode($threadNumber); ?>;
    var adminBase = <?php echo json_encode($adminBase); ?>;
    var msgCountBadge = document.getElementById('adminChatMsgCount');
    var form = document.getElementById('chatReplyForm');

    var lastMsgId = 0;
    body.querySelectorAll('[data-id]').forEach(function(el){
        var mid = parseInt(el.getAttribute('data-id') || '0', 10);
        if (mid > lastMsgId) lastMsgId = mid;
    });

    function playChime() {
        try {
            var AudioCtx = window.AudioContext || window.webkitAudioContext;
            if (!AudioCtx) return;
            var ctx = new AudioCtx();
            var now = ctx.currentTime;
            var o1 = ctx.createOscillator(); var g1 = ctx.createGain();
            o1.frequency.setValueAtTime(587.33, now);
            g1.gain.setValueAtTime(0.12, now);
            g1.gain.exponentialRampToValueAtTime(0.001, now + 0.22);
            o1.connect(g1); g1.connect(ctx.destination);
            o1.start(now); o1.stop(now + 0.22);

            var o2 = ctx.createOscillator(); var g2 = ctx.createGain();
            o2.frequency.setValueAtTime(880, now + 0.12);
            g2.gain.setValueAtTime(0.15, now + 0.12);
            g2.gain.exponentialRampToValueAtTime(0.001, now + 0.4);
            o2.connect(g2); g2.connect(ctx.destination);
            o2.start(now + 0.12); o2.stop(now + 0.4);
        } catch(e) {}
    }

    var esc = function(s){ return String(s).replace(/[&<>"']/g, function(c){ return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]; }); };

    var isPolling = false;
    function pollMessages() {
        if (isPolling) return;
        isPolling = true;
        fetch(adminBase + '/actions/manage_chat.php?action=get_messages&wa_number=' + encodeURIComponent(threadNum) + '&after_id=' + lastMsgId + '&_t=' + Date.now())
        .then(function(r){ return r.json(); })
        .then(function(d){
            if (d.status === 'success') {
                // Update status dibaca/dilihat untuk pesan yang sudah ada
                if (Array.isArray(d.read_updates)) {
                    d.read_updates.forEach(function(ru){
                        var statusEl = body.querySelector('.js-msg-status[data-status-id="' + ru.id + '"]');
                        if (statusEl && ru.is_read) {
                            var tShort = ru.read_time_short ? (' Dilihat ' + ru.read_time_short) : ' Dilihat';
                            var tFull = ru.read_time_formatted ? ('Dilihat siswa pada: ' + ru.read_time_formatted) : 'Sudah dilihat siswa';
                            statusEl.innerHTML = '<span class="text-primary fw-semibold" title="' + esc(tFull) + '"><i class="fas fa-check-double text-primary"></i>' + esc(tShort) + '</span>';
                        }
                    });
                }

                // Append pesan baru
                if (Array.isArray(d.messages) && d.messages.length > 0) {
                    var empty = body.querySelector('.js-chat-empty');
                    if (empty) empty.remove();
                    var hasIncoming = false;

                    d.messages.forEach(function(m){
                        var mid = parseInt(m.id || 0, 10);
                        if (mid > lastMsgId) lastMsgId = mid;
                        if (body.querySelector('[data-id="' + mid + '"]')) return;

                        var isIn = m.direction === 'in';
                        var isAdmin = m.sender_type === 'admin';
                        if (isIn) hasIncoming = true;

                        var wrap = document.createElement('div');
                        wrap.className = 'd-flex mb-3 ' + (isIn ? '' : 'justify-content-end');
                        wrap.setAttribute('data-id', mid);

                        var bubbleClass = isIn ? 'bg-white border' : (isAdmin ? 'bg-warning text-dark border border-warning' : 'bg-primary text-white');
                        var adminBadge = isAdmin ? ' <span class="badge bg-dark ms-1" style="font-size:0.6rem;">Admin</span>' : '';
                        var intentBadge = m.matched_intent ? ' <span class="badge ' + (isIn ? 'bg-soft-primary text-primary' : (isAdmin ? 'bg-dark text-white' : 'bg-white bg-opacity-25 text-white')) + ' ms-1">' + esc(m.matched_intent) + '</span>' : '';
                        var metaClass = isIn ? 'text-muted' : (isAdmin ? 'text-dark opacity-75' : 'text-white-50');
                        var timeStr = esc(m.time_formatted || m.time_short || '');

                        var statusHtml = '';
                        if (!isIn) {
                            if (m.is_read) {
                                var rTime = m.read_time_short ? (' Dilihat ' + esc(m.read_time_short)) : ' Dilihat';
                                var rFull = m.read_time_formatted ? ('Dilihat siswa pada: ' + esc(m.read_time_formatted)) : 'Sudah dilihat siswa';
                                statusHtml = '<span class="text-primary fw-semibold" title="' + rFull + '"><i class="fas fa-check-double text-primary"></i>' + rTime + '</span>';
                            } else {
                                statusHtml = '<span class="text-muted opacity-75" title="Terkirim"><i class="fas fa-check-double text-secondary opacity-75"></i> Terkirim</span>';
                            }
                        } else {
                            statusHtml = '<span class="text-muted" title="Dibaca admin: ' + esc(m.read_time_formatted || '') + '"><i class="fas fa-check-double text-primary"></i> Dibaca</span>';
                        }

                        wrap.innerHTML = '<div class="rounded-3 px-3 py-2 shadow-sm small ' + bubbleClass + '" style="max-width:75%;">' +
                            '<div>' + esc(m.message).replace(/\n/g, '<br>') + adminBadge + '</div>' +
                            '<div class="small mt-1 d-flex align-items-center justify-content-between gap-3 ' + metaClass + '">' +
                                '<span>' + timeStr + intentBadge + '</span>' +
                                '<span class="js-msg-status" data-status-id="' + mid + '">' + statusHtml + '</span>' +
                            '</div>' +
                        '</div>';
                        body.appendChild(wrap);
                    });

                    body.scrollTop = body.scrollHeight;
                    var total = body.querySelectorAll('[data-id]').length;
                    if (msgCountBadge) msgCountBadge.textContent = total + ' pesan';
                    if (hasIncoming) playChime();
                }
            }
        })
        .catch(function(){})
        .finally(function(){ isPolling = false; });
    }

    // Polling setiap 2.5 detik untuk status dan pesan real-time
    var pollTimer = setInterval(pollMessages, 2500);
    window.addEventListener('beforeunload', function(){ clearInterval(pollTimer); });

    // Submit form balasan
    if (form && !form.dataset.bound) {
        form.dataset.bound = '1';
        form.addEventListener('submit', function(e){
            e.preventDefault();
            var btn = form.querySelector('button[type=submit]');
            var orig = btn ? btn.innerHTML : '';
            var input = form.querySelector('input[name=message]');
            var msg = (input ? input.value : '').trim();
            if (!msg) return;

            if (btn) { btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Mengirim...'; }
            var fd = new FormData(form);
            if (!fd.get('csrf_token') && window.MCM_CSRF_TOKEN) {
                fd.append('csrf_token', window.MCM_CSRF_TOKEN);
            }
            fetch(form.getAttribute('action'), { method: 'POST', body: fd })
            .then(function(r){ return r.json(); })
            .then(function(d){
                if (d.status === 'success') {
                    var empty = body.querySelector('.js-chat-empty');
                    if (empty) empty.remove();
                    var newMsgId = d.msg_id || (lastMsgId + 1);
                    if (newMsgId > lastMsgId) lastMsgId = newMsgId;

                    var wrap = document.createElement('div');
                    wrap.className = 'd-flex mb-3 justify-content-end';
                    wrap.setAttribute('data-id', newMsgId);
                    var now = new Date();
                    var time = now.getHours().toString().padStart(2,'0') + ':' + now.getMinutes().toString().padStart(2,'0');
                    wrap.innerHTML = '<div class="rounded-3 px-3 py-2 shadow-sm small bg-warning text-dark border border-warning" style="max-width:75%;">' +
                        '<div>' + esc(msg).replace(/\n/g, '<br>') + ' <span class="badge bg-dark ms-1" style="font-size:0.6rem;">Admin</span></div>' +
                        '<div class="small mt-1 d-flex align-items-center justify-content-between gap-3 text-dark opacity-75">' +
                            '<span>' + time + ' <span class="badge bg-dark text-white ms-1">admin</span></span>' +
                            '<span class="js-msg-status" data-status-id="' + newMsgId + '"><span class="text-muted opacity-75" title="Terkirim"><i class="fas fa-check-double text-secondary opacity-75"></i> Terkirim</span></span>' +
                        '</div>' +
                    '</div>';
                    body.appendChild(wrap);
                    body.scrollTop = body.scrollHeight;

                    var total = body.querySelectorAll('[data-id]').length;
                    if (msgCountBadge) msgCountBadge.textContent = total + ' pesan';
                    form.reset();
                } else {
                    if (window.Swal) Swal.fire('Gagal', d.message || 'Gagal mengirim', 'error');
                    else alert(d.message || 'Gagal');
                }
            })
            .catch(function(){ if(window.Swal) Swal.fire('Error','Terjadi kesalahan sistem','error'); })
            .finally(function(){ if(btn){ btn.disabled=false; btn.innerHTML=orig; }});
        });
    }
})();
</script>
