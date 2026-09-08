<?php
$threadNumber = isset($threadNumber) ? $threadNumber : '';

$conversations = [];
$messages = [];
if (empty($threadNumber)) {
    $tmp = [];
    foreach ($chats as $ch) {
        $num = $ch['wa_number'];
        if (!isset($tmp[$num])) {
            $tmp[$num] = ['number' => $num, 'last' => $ch, 'in_count' => 0];
        }
        if ($ch['direction'] === 'in') $tmp[$num]['in_count']++;
    }
    $conversations = array_values($tmp);
} else {
    // query thread anonim (web-) maupun WA
    try {
        $cleanDigits = preg_replace('/\D+/', '', $threadNumber);
        $stmt = $pdo->prepare("SELECT * FROM chat_messages WHERE wa_number = ? OR wa_number = ? OR wa_number = ? ORDER BY created_at ASC, id ASC");
        $stmt->execute([$threadNumber, '+' . $cleanDigits, $cleanDigits]);
        $messages = $stmt->fetchAll();
    } catch (PDOException $e) {
        foreach ($chats as $ch) {
            if ($ch['wa_number'] === $threadNumber) $messages[] = $ch;
        }
        $messages = array_reverse($messages);
    }
}
?>
<!-- CHAT PAGE -->
        <div class="row align-items-center mb-4 g-3" data-aos="fade-down">
            <div class="col-md-6">
                <h2 class="fw-bold mb-1 text-dark">Chat WhatsApp</h2>
                <p class="text-muted mb-0">Pantau percakapan chatbot dan balas pertanyaan peserta.</p>
            </div>
            <div class="col-md-6 text-md-end d-flex justify-content-md-end gap-2 align-items-center flex-wrap">
                <?php if (empty($threadNumber)): ?>
                <div class="input-group shadow-sm rounded-3 overflow-hidden" style="max-width: 280px;">
                    <span class="input-group-text bg-white border-end-0 text-muted ps-3"><i class="fas fa-search"></i></span>
                    <input type="text" id="chatThreadSearch" class="form-control border-start-0 py-2" placeholder="Cari nomor / pesan..." autocomplete="off">
                </div>
                <?php endif; ?>
                <?php if (!empty($threadNumber)): ?>
                    <a href="?page=chat" class="btn btn-light border rounded-pill px-4 fw-bold"><i class="fas fa-arrow-left me-2"></i>Semua Percakapan</a>
                <?php endif; ?>
            </div>
        </div>

        <?php if (empty($threadNumber)): ?>
            <div class="d-flex gap-2 mb-3" id="chatFilterTabs">
                <button type="button" class="btn btn-sm rounded-pill px-3 fw-bold btn-primary active" data-filter="all">Semua</button>
                <button type="button" class="btn btn-sm rounded-pill px-3 btn-outline-primary" data-filter="wa"><i class="fab fa-whatsapp me-1"></i>WhatsApp</button>
                <button type="button" class="btn btn-sm rounded-pill px-3 btn-outline-primary" data-filter="web">Widget (Anonim)</button>
            </div>
            <div data-bulk-table="chat">
            <div class="admin-table-toolbar d-none" data-bulk-toolbar>
                <div class="small fw-bold text-primary"><i class="fas fa-check-square me-1"></i><span data-bulk-count>0 dipilih</span></div>
                <button type="button" class="btn btn-sm btn-danger rounded-pill px-3 fw-bold" data-bulk-delete><i class="fas fa-trash me-1"></i>Hapus Terpilih (<span data-bulk-count-num>0</span>)</button>
            </div>
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-0">
                    <div class="table-responsive table-responsive--no-scroll">
                        <table class="table align-middle admin-compact mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="col-check"><input type="checkbox" class="bulk-select-all js-bulk-select-all"></th>
                                    <th>Nomor WhatsApp</th>
                                    <th>Pesan Terakhir</th>
                                    <th class="text-center">Masuk</th>
                                    <th class="text-end pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="conversationTableBody">
                                <?php if (empty($conversations)): ?>
                                    <tr><td colspan="5" class="text-center py-5 text-muted">Belum ada percakapan.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($conversations as $cv): $kind = str_starts_with($cv['number'],'web-') ? 'web' : 'wa'; ?>
                                        <tr data-kind="<?php echo $kind; ?>">
                                            <td class="col-check"><input type="checkbox" class="bulk-row-check js-bulk-row" value="<?php echo htmlspecialchars($cv['number']); ?>"></td>
                                            <td>
                                                <div class="fw-bold text-dark small" style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:170px;" title="<?php echo htmlspecialchars($cv['number']); ?>">
                                                    <i class="fab fa-whatsapp text-success me-1"></i><?php echo htmlspecialchars($cv['number']); ?>
                                                    <?php if ($cv['in_count'] > 0): ?>
                                                        <span class="badge bg-success rounded-pill ms-1" style="font-size:0.6rem;"><?php echo $cv['in_count']; ?></span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td class="text-muted small" style="max-width: 380px;">
                                                <div class="text-truncate"><?php echo htmlspecialchars(mb_strimwidth($cv['last']['message'], 0, 90, '...')); ?></div>
                                                <div class="small text-muted">
                                                    <?php echo date('d M Y H:i', strtotime($cv['last']['created_at'])); ?>
                                                    <?php if ($cv['last']['matched_intent']): ?>
                                                        <span class="badge bg-soft-primary text-primary ms-1"><?php echo htmlspecialchars($cv['last']['matched_intent']); ?></span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td class="text-center text-muted small"><?php echo (int)$cv['in_count']; ?></td>
                                            <td class="text-end pe-4">
                                                <div class="d-inline-flex gap-2">
                                                    <a href="?page=chat&thread=<?php echo urlencode($cv['number']); ?>" class="btn btn-action btn-soft-primary" title="Buka Percakapan"><i class="fas fa-comment-dots"></i></a>
                                                    <button class="btn btn-action btn-soft-danger" title="Hapus Percakapan" onclick="deleteConversation('<?php echo urlencode($cv['number']); ?>')"><i class="fas fa-trash"></i></button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            </div>
            <script>
            (function(){
                if (typeof window.debounce !== 'function') {
                    window.debounce = function(fn, ms){ let t=null; return function(){ const a=arguments,s=this; clearTimeout(t); t=setTimeout(function(){ fn.apply(s,a); }, ms||300); }; };
                }
                var input=document.getElementById('chatThreadSearch');
                var tbody=document.getElementById('conversationTableBody');
                if(!input||!tbody) return;
                if(input.dataset.filterBound) return;
                input.dataset.filterBound='1';
                var active='all';
                function apply(){
                    var q=(input.value||'').toLowerCase().trim();
                    var vis=0;
                    tbody.querySelectorAll('tr[data-kind]').forEach(function(tr){
                        var kind=tr.getAttribute('data-kind');
                        var okKind=(active==='all'||kind===active);
                        var okSearch=!q||tr.textContent.toLowerCase().indexOf(q)!==-1;
                        var show=okKind&&okSearch;
                        tr.style.display=show?'':'none';
                        if(show) vis++;
                    });
                    var empty=tbody.querySelector('tr.table-search-empty');
                    if(q&&vis===0||active!=='all'&&vis===0){
                        if(!empty){empty=document.createElement('tr');empty.className='table-search-empty';tbody.appendChild(empty);}
                        empty.innerHTML='<td colspan="5" class="text-center py-5 text-muted">Tidak ada hasil untuk filter ini.</td>';
                        empty.style.display='';
                    }else if(empty){empty.style.display='none';}
                }
                input.addEventListener('input', window.debounce(apply,300));
                document.querySelectorAll('#chatFilterTabs [data-filter]').forEach(function(b){
                    if(b.dataset.tabBound) return;
                    b.dataset.tabBound='1';
                    b.addEventListener('click',function(){
                        active=this.getAttribute('data-filter');
                        document.querySelectorAll('#chatFilterTabs [data-filter]').forEach(function(x){
                            x.className=x.getAttribute('data-filter')===active?'btn btn-sm rounded-pill px-3 fw-bold btn-primary active':'btn btn-sm rounded-pill px-3 btn-outline-primary';
                        });
                        apply();
                    });
                });
            })();
            </script>
        <?php else: ?>
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white border-0 py-3 px-4 d-flex align-items-center justify-content-between">
                    <div class="fw-bold text-dark"><i class="fab fa-whatsapp text-success me-2"></i><?php echo htmlspecialchars($threadNumber); ?></div>
                    <span class="badge bg-light text-dark border rounded-pill px-3"><?php echo count($messages); ?> pesan</span>
                </div>
                <div id="adminChatBody" class="card-body p-4" style="max-height: 460px; overflow-y: auto; background: #f8fafc;">
                    <?php if (empty($messages)): ?>
                        <div class="text-center text-muted py-5">Belum ada pesan di percakapan ini.</div>
                    <?php else: ?>
                        <?php foreach ($messages as $msg): $isIn = $msg['direction'] === 'in'; $isAdmin = ($msg['sender_type'] ?? '') === 'admin'; ?>
                            <div class="d-flex mb-3 <?php echo $isIn ? '' : 'justify-content-end'; ?>">
                                <div class="rounded-3 px-3 py-2 shadow-sm small <?php echo $isIn ? 'bg-white border' : ($isAdmin ? 'bg-warning text-dark border border-warning' : 'bg-primary text-white'); ?>" style="max-width: 75%;">
                                    <div><?php echo nl2br(htmlspecialchars($msg['message'])); ?><?php if($isAdmin) echo ' <span class="badge bg-dark ms-1" style="font-size:0.6rem;">Admin</span>'; ?></div>
                                    <div class="small mt-1 <?php echo $isIn ? 'text-muted' : ($isAdmin ? 'text-dark opacity-75' : 'text-white-50'); ?>">
                                        <?php echo date('d M Y H:i', strtotime($msg['created_at'])); ?>
                                        <?php if ($msg['matched_intent']): ?>
                                            <span class="badge <?php echo $isIn ? 'bg-soft-primary text-primary' : ($isAdmin ? 'bg-dark text-white' : 'bg-white bg-opacity-25 text-white'); ?> ms-1"><?php echo htmlspecialchars($msg['matched_intent']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <script>
                (function(){
                    var b=document.getElementById('adminChatBody');
                    if(b) b.scrollTop=b.scrollHeight;
                })();
                </script>
                <div class="card-footer bg-white border-0 p-4">
                    <?php $isAnonThread = str_starts_with($threadNumber, 'web-'); ?>
                    <div class="small mb-2 <?php echo $isAnonThread ? 'text-muted' : 'text-success'; ?>">
                        <i class="fas <?php echo $isAnonThread ? 'fa-desktop' : 'fa-whatsapp'; ?> me-1"></i>
                        <?php echo $isAnonThread ? 'Balasan ini hanya muncul di widget chat website' : 'Balasan ini akan dikirim ke WhatsApp'; ?>
                    </div>
                    <form id="chatReplyForm" class="d-flex gap-2" action="<?php echo $adminBase; ?>/actions/manage_chat.php" method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(mcm_csrf_token()); ?>">
                        <input type="hidden" name="action" value="send_reply">
                        <input type="hidden" name="wa_number" value="<?php echo htmlspecialchars($threadNumber); ?>">
                        <input type="text" class="form-control rounded-pill" name="message" required placeholder="Tulis balasan...">
                        <button type="submit" class="btn btn-success rounded-pill fw-bold px-4"><i class="fab fa-whatsapp me-2"></i>Kirim</button>
                    </form>
                </div>
            </div>

            <script>
            (function(){
                var form = document.getElementById('chatReplyForm');
                if (form && !form.dataset.bound) {
                    form.dataset.bound = '1';
                    form.addEventListener('submit', function(e){
                        e.preventDefault();
                        var btn = form.querySelector('button[type=submit]');
                        var orig = btn ? btn.innerHTML : '';
                        if (btn) { btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Mengirim...'; }
                        var fd = new FormData(form);
                        if (!fd.get('csrf_token') && window.MCM_CSRF_TOKEN) {
                            fd.append('csrf_token', window.MCM_CSRF_TOKEN);
                        }
                        fetch(form.getAttribute('action'), { method: 'POST', body: fd })
                        .then(function(r){ return r.json(); })
                        .then(function(d){
                            if (d.status === 'success') {
                                var body = document.getElementById('adminChatBody');
                                var empty = body ? body.querySelector('.text-center.text-muted.py-5') : null;
                                if (empty) empty.remove();
                                var wrap = document.createElement('div');
                                wrap.className = 'd-flex mb-3 justify-content-end';
                                var now = new Date();
                                var time = now.getHours().toString().padStart(2,'0') + ':' + now.getMinutes().toString().padStart(2,'0');
                                var msg = fd.get('message') || '';
                                var esc = function(s){ return String(s).replace(/[&<>"']/g, function(c){ return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]; }); };
                                wrap.innerHTML = '<div class="rounded-3 px-3 py-2 shadow-sm small bg-warning text-dark border border-warning" style="max-width:75%;"><div>' + esc(msg) + ' <span class="badge bg-dark ms-1" style="font-size:0.6rem;">Admin</span></div><div class="small mt-1 text-dark opacity-75">' + time + ' <span class="badge bg-dark text-white ms-1">admin</span></div></div>';
                                if (body) { body.appendChild(wrap); body.scrollTop = body.scrollHeight; }
                                form.reset();
                                if (d.wa_error) {
                                    if (window.Swal) Swal.fire('Tersimpan', d.message, 'info');
                                    else alert(d.message);
                                }
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
            <script>
            function deleteConversation(number) {
                Swal.fire({
                    title: 'Hapus percakapan ini?',
                    text: 'Semua pesan dari nomor ini akan dihapus.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const adminBase = '<?php echo $adminBase; ?>';
                        const cleanNumber = decodeURIComponent(number);
                        const formData = new FormData();
                        formData.append('action', 'delete_thread');
                        formData.append('wa_number', cleanNumber);
                        const csrf = document.querySelector('input[name="csrf_token"]')?.value || window.MCM_CSRF_TOKEN || '';
                        if (csrf) formData.append('csrf_token', csrf);
                        fetch(adminBase + '/actions/manage_chat.php', { method: 'POST', body: formData })
                        .then(res => res.text()).then(t=>{ let d; try{ d=JSON.parse(t);}catch(e){ throw new Error('Respons tidak valid: '+t.slice(0,120)); } return d; })
                        .then(data => {
                            const ok = data.status === 'success';
                            Swal.fire({ icon: ok?'success':'error', title: ok?'Terhapus!':'Gagal', text:data.message, timer: ok?1500:undefined, showConfirmButton: !ok }).then(() => {
                                if (ok) {
                                    if (typeof window.mcmCloseModalsAndRefresh==='function') window.mcmCloseModalsAndRefresh();
                                    else if (typeof loadContent==='function') loadContent('?page=chat', false);
                                    else {
                                        const r=document.querySelector('tr[data-kind]');
                                        const tr=document.querySelector('a[href*="thread='+encodeURIComponent(number)+'"]')?.closest('tr');
                                        if (tr) tr.remove();
                                    }
                                }
                            });
                        }).catch(err=> Swal.fire('Gagal', err.message || 'Gagal', 'error'));
                    }
                });
            }
            </script>
        <?php endif; ?>