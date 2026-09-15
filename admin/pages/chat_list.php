<?php
// admin/pages/chat_list.php - Tampilan daftar percakapan dan filter
?>
<div class="d-flex gap-2 mb-3 flex-wrap" id="chatFilterTabs">
    <button type="button" class="btn btn-sm rounded-pill px-3 fw-bold btn-primary active" data-filter="all">Semua</button>
    <button type="button" class="btn btn-sm rounded-pill px-3 btn-outline-primary" data-filter="user"><i class="fas fa-graduation-cap me-1"></i>Siswa LMS</button>
    <button type="button" class="btn btn-sm rounded-pill px-3 btn-outline-primary" data-filter="web"><i class="fas fa-desktop me-1"></i>Widget (Anonim)</button>
    <button type="button" class="btn btn-sm rounded-pill px-3 btn-outline-primary" data-filter="wa"><i class="fab fa-whatsapp me-1"></i>WhatsApp</button>
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
                        <th>Pengirim / Kontak</th>
                        <th>Pesan Terakhir</th>
                        <th class="text-center">Masuk</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody id="conversationTableBody">
                    <?php if (empty($conversations)): ?>
                        <tr><td colspan="5" class="text-center py-5 text-muted">Belum ada percakapan.</td></tr>
                    <?php else: ?>
                        <?php foreach ($conversations as $cv): 
                            if (str_starts_with($cv['number'],'web-')) $kind = 'web';
                            elseif (str_starts_with($cv['number'],'user-')) $kind = 'user';
                            else $kind = 'wa';
                        ?>
                            <tr data-kind="<?php echo $kind; ?>" data-thread="<?php echo htmlspecialchars($cv['number']); ?>">
                                <td class="col-check"><input type="checkbox" class="bulk-row-check js-bulk-row" value="<?php echo htmlspecialchars($cv['number']); ?>"></td>
                                <td>
                                    <div class="fw-bold text-dark small" style="max-width:240px;" title="<?php echo htmlspecialchars($cv['number']); ?>">
                                        <?php if ($kind === 'user'): ?>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px; font-size: 0.85rem;">
                                                    <i class="fas fa-graduation-cap"></i>
                                                </div>
                                                <div>
                                                    <span class="text-dark fw-bold"><?php echo htmlspecialchars($cv['user_name'] ?: ('Siswa #' . substr($cv['number'], 5))); ?></span>
                                                    <div class="d-flex align-items-center gap-1" style="font-size:0.68rem;">
                                                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-1" style="font-size:0.62rem;">Siswa LMS</span>
                                                        <?php if (!empty($cv['user_phone'])): ?>
                                                            <span class="text-muted"><?php echo htmlspecialchars($cv['user_phone']); ?></span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php elseif ($kind === 'web'): ?>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="rounded-circle bg-secondary bg-opacity-10 text-secondary d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px; font-size: 0.85rem;">
                                                    <i class="fas fa-desktop"></i>
                                                </div>
                                                <div>
                                                    <span class="text-dark fw-bold">Pengunjung Web</span>
                                                    <small class="text-muted d-block" style="font-size:0.68rem;"><?php echo htmlspecialchars($cv['number']); ?></small>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px; font-size: 0.85rem;">
                                                    <i class="fab fa-whatsapp"></i>
                                                </div>
                                                <div>
                                                    <span class="text-dark fw-bold"><?php echo htmlspecialchars($cv['number']); ?></span>
                                                    <small class="text-muted d-block" style="font-size:0.68rem;">WhatsApp</small>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        <?php if ($cv['in_count'] > 0): ?>
                                            <span class="badge bg-danger rounded-pill ms-1" style="font-size:0.6rem;"><?php echo $cv['in_count']; ?></span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="text-muted small" style="max-width: 380px;">
                                    <div class="text-truncate js-last-msg"><?php echo htmlspecialchars(mb_strimwidth($cv['last']['message'], 0, 90, '...')); ?></div>
                                    <div class="small text-muted">
                                        <span class="js-last-time"><?php echo date('d M Y H:i', strtotime($cv['last']['created_at'])); ?></span>
                                        <?php if ($cv['last']['matched_intent']): ?>
                                            <span class="badge bg-soft-primary text-primary ms-1"><?php echo htmlspecialchars($cv['last']['matched_intent']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="text-center small">
                                    <?php if ($cv['in_count'] > 0): ?>
                                        <span class="badge bg-danger rounded-pill px-2 py-1 js-in-count" style="font-size:0.68rem;"><?php echo (int)$cv['in_count']; ?> baru</span>
                                    <?php else: ?>
                                        <span class="text-muted opacity-75 js-in-count" style="font-size:0.75rem;" title="Semua pesan telah dibaca"><i class="fas fa-check-double text-info me-1"></i>Dibaca</span>
                                    <?php endif; ?>
                                </td>
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
        if((q&&vis===0)||(active!=='all'&&vis===0)){
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
