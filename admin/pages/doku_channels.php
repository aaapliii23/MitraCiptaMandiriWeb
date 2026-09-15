<?php
require_once __DIR__ . '/../../includes/payment_gateway.php';
$cfg = pg_config();
$lastRef = '';
try { $lastRef = $pdo->query("SELECT payment_gateway_ref FROM orders WHERE payment_gateway_ref LIKE 'd80%' OR payment_gateway_ref LIKE '5d3%' OR payment_gateway_ref REGEXP '^[0-9a-f]{32}' ORDER BY id DESC LIMIT 1")->fetchColumn(); } catch(Exception $e){}
$lastCheckout = $lastRef ? 'https://checkout.doku.com/checkout-link-v2/'.htmlspecialchars($lastRef) : '';
?>
<!-- DOKU Channels Helper — live check per merchant -->
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h4 class="fw-bold mb-1"><i class="fas fa-credit-card me-2 text-primary"></i>Status Channel DOKU</h4>
        <p class="text-muted small mb-0">Client <code><?php echo htmlspecialchars($cfg['client_id']); ?></code> — <span class="badge bg-<?php echo $cfg['mode']==='production'?'success':'warning'; ?>"><?php echo htmlspecialchars($cfg['mode']); ?></span> — <?php echo htmlspecialchars($cfg['api_url'] ?: 'https://api.doku.com/checkout/v1/payment'); ?></p>
    </div>
    <button class="btn btn-primary rounded-pill px-4" id="btnLiveCheck"><i class="fas fa-satellite-dish me-2"></i>Cek Live per Channel</button>
</div>

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <div class="row g-3 small mb-3">
            <div class="col-md-4"><div class="text-muted small">Client ID</div><div class="fw-bold font-monospace small text-break"><?php echo htmlspecialchars($cfg['client_id']); ?></div></div>
            <div class="col-md-4"><div class="text-muted small">API URL</div><div class="fw-bold small text-break"><?php echo htmlspecialchars($cfg['api_url'] ?: 'https://api.doku.com/checkout/v1/payment'); ?></div></div>
            <div class="col-md-4"><div class="text-muted small">Mode</div><div class="fw-bold text-capitalize"><?php echo htmlspecialchars($cfg['mode']); ?></div></div>
        </div>
        <div id="liveResult" class="mt-3">
            <div class="text-center py-3 small text-muted">Klik <b>Cek Live per Channel</b> untuk probe langsung ke DOKU (tanpa buat order). Hasil per VA akan muncul di sini.</div>
        </div>
        <?php if($lastCheckout): ?>
        <div class="mt-3">
            <a href="<?php echo $lastCheckout; ?>" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill"><i class="fas fa-external-link-alt me-1"></i> Buka Checkout Terakhir</a>
            <span class="small text-muted ms-2">Link test Rp 10.000</span>
        </div>
        <?php endif; ?>
    </div>
</div>

<div id="liveTableWrap" class="card border-0 shadow-sm rounded-4 mb-4 d-none">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
                <thead class="table-light"><tr><th>Channel (VA)</th><th>HTTP</th><th>Status</th><th>Pesan</th></tr></thead>
                <tbody id="liveTbody"></tbody>
            </table>
        </div>
        <div class="p-3 small text-muted">Jika semua VA = <code>400 PAYMENT CHANNEL IS INACTIVE</code>, berarti DOKU menolak filter <code>payment_method_types</code> untuk merchant ini — biarkan tanpa filter (dashboard yang tentukan). Jika Danamon = <code>200</code> dan lainnya <code>400</code>, berarti hanya Danamon yang Active.</div>
    </div>
</div>

<div class="alert alert-info rounded-4 small">
    <i class="fas fa-info-circle me-2"></i><b>Cara aktifkan:</b> Dashboard DOKU → <code><?php echo htmlspecialchars($cfg['client_id']); ?></code> → <b>Settings → Payment Channels</b> → centang BCA/Mandiri/BRI/BNI/Permata/OVO/DANA/ShopeePay/QRIS → Save → tunggu approval RM DOKU. Setelah Active, checkout baru otomatis muncul tanpa ubah kode.
</div>

<script>
document.getElementById('btnLiveCheck')?.addEventListener('click', async function(){
    const btn=this, box=document.getElementById('liveResult'), wrap=document.getElementById('liveTableWrap'), tbody=document.getElementById('liveTbody');
    btn.disabled=true; btn.innerHTML='<span class="spinner-border spinner-border-sm me-2"></span>Mengecek...';
    box.innerHTML='<div class="text-center py-3"><span class="spinner-border text-primary"></span><div class="small text-muted mt-2">Probe 7 VA ke DOKU...</div></div>';
    wrap.classList.add('d-none'); tbody.innerHTML='';
    try{
        const r=await fetch('<?php echo $adminBase; ?>/actions/check_doku_channels.php');
        const j=await r.json();
        if(j.status!=='success'){ box.innerHTML='<div class="alert alert-danger">'+(j.message||'Gagal')+'</div>'; return; }
        const summary = j.summary || (j.summary_count+' dari '+(j.results?.length||7)+' channel VA aktif: '+(j.summary_list||'-'));
        box.innerHTML='<div class="alert '+(j.summary_count>1?'alert-success':'alert-warning')+' small mb-0"><b>'+summary+'</b><br>Mode: '+j.mode+' — Client: '+j.client_id+'<br>General checkout: '+(j.general||'-')+'</div>';
        tbody.innerHTML='';
        j.results.forEach(function(ch){
            const ok = ch.active;
            const badge = ok ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
            const tr = document.createElement('tr');
            tr.innerHTML = '<td class="font-monospace small">'+ch.channel+'<br><span class="text-muted" style="font-size:0.7rem;">'+(ch.label||'')+'</span></td><td class="small">'+ch.http+'</td><td>'+badge+'</td><td class="small text-muted" style="max-width:280px;word-break:break-word;">'+(ch.message||'')+'</td>';
            tbody.appendChild(tr);
        });
        wrap.classList.remove('d-none');
    }catch(e){ box.innerHTML='<div class="alert alert-danger">Gagal: '+e.message+'</div>'; }
    finally{ btn.disabled=false; btn.innerHTML='<i class="fas fa-satellite-dish me-2"></i>Cek Live per Channel'; }
});
</script>
