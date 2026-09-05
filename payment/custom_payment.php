<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/payment_gateway.php';
pg_ensure_payment_columns($pdo);

$orderNumber = trim($_GET['order'] ?? '');
if ($orderNumber === '') { http_response_code(404); include __DIR__ . '/../404.php'; exit; }

$stmt = $pdo->prepare("SELECT o.*, c.name AS class_name, c.category AS class_category FROM orders o LEFT JOIN classes c ON o.class_id = c.id WHERE o.order_number = ? LIMIT 1");
$stmt->execute([$orderNumber]);
$order = $stmt->fetch();
if (!$order) { http_response_code(404); include __DIR__ . '/../404.php'; exit; }

$className = $order['class_name'] ?? 'Program Pelatihan MCM';
$amount = (int)$order['amount'];
$customerName = $order['customer_name'];
$customerEmail = $order['customer_email'];
$isPaid = ($order['payment_status'] === 'paid');

// Jika sudah paid, langsung ke status
if ($isPaid) {
    header("Location: payment_status.php?order=" . urlencode($orderNumber));
    exit;
}

// Cek VA yang sudah ada & belum expired (persisten)
$existingVA = $order['va_number'] ?? null;
$existingBank = $order['va_bank'] ?? 'danamon';
$existingExpiry = $order['payment_expiry'] ?? null;
$isVAValid = !empty($existingVA) && !empty($existingExpiry) && strtotime($existingExpiry) > time();
$existingQRIS = $order['qris_string'] ?? null;
$isQRISValid = !empty($existingQRIS) && !empty($existingExpiry) && strtotime($existingExpiry) > time() && ($order['payment_method'] ?? '') === 'qris';
$existingEwalletUrl = $order['ewallet_url'] ?? null;
$existingEwalletType = $order['ewallet_type'] ?? null;
$isEwalletValid = !empty($existingEwalletUrl) && !empty($existingExpiry) && strtotime($existingExpiry) > time() && str_starts_with($order['payment_method'] ?? '', 'ewallet_');
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Bayar Pesanan — MCM</title>
<link rel="icon" type="image/png" href="<?php echo pg_base_url(); ?>/assets/img/logo.png">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="<?php echo pg_base_url(); ?>/assets/css/style.css">
<style>
.payment-wrap { background:#f8fafc; min-height:100vh; padding:32px 0 48px; }
.payment-card { background:#fff; border-radius:1.25rem; border:1px solid #e2e8f0; box-shadow:0 10px 30px rgba(15,23,42,0.06); overflow:hidden; }
.payment-head { background: linear-gradient(135deg,#0c4a6e,#0ea5e9); color:#fff; padding:20px 24px; }
.payment-head h5 { color:#fff; margin:0; }
.method-tab { border:1px solid #e2e8f0; border-radius:0.9rem; padding:12px 14px; cursor:pointer; transition:all .2s; background:#fff; }
.method-tab:hover { border-color:#0ea5e9; background:#f0f9ff; }
.method-tab.active { border-color:#0c4a6e; background:#eff6ff; box-shadow:0 6px 16px rgba(14,165,233,0.15); }
.method-tab i { width:36px; height:36px; display:inline-flex; align-items:center; justify-content:center; border-radius:0.7rem; background:#f1f5f9; color:#0c4a6e; }
.method-tab.active i { background:linear-gradient(135deg,#0c4a6e,#0ea5e9); color:#fff; }
.va-box { background:#f8fafc; border:1px dashed #cbd5e1; border-radius:1rem; padding:16px; text-align:center; }
.va-number { font-size:1.6rem; font-weight:800; letter-spacing:1px; color:#0c4a6e; font-monospace; }
.countdown { font-variant-numeric: tabular-nums; font-weight:700; color:#dc2626; }
.qr-box { background:#fff; border:1px solid #e2e8f0; border-radius:1rem; padding:16px; display:inline-block; }
.instr-select { border-radius:999px; }
</style>
</head>
<body>
<div class="payment-wrap">
<div class="container" style="max-width:1080px;">
  <div class="d-flex align-items-center gap-3 mb-4">
    <a href="<?php echo pg_base_url(); ?>/index.php" class="btn btn-light rounded-pill px-4"><i class="fas fa-arrow-left me-2"></i>Beranda</a>
    <div class="ms-auto small text-muted">Order <span class="fw-bold text-dark"><?php echo htmlspecialchars($orderNumber); ?></span></div>
  </div>

  <div class="row g-4">
    <!-- Order Summary -->
    <div class="col-lg-4">
      <div class="payment-card">
        <div class="payment-head">
          <h5><i class="fas fa-receipt me-2"></i>Order Summary</h5>
          <div class="small opacity-75">Invoice: <?php echo htmlspecialchars($orderNumber); ?></div>
        </div>
        <div class="p-4">
          <div class="d-flex justify-content-between py-2 border-bottom"><span class="text-muted small">Program</span><span class="fw-bold small text-end" style="max-width:180px;"><?php echo htmlspecialchars($className); ?></span></div>
          <div class="d-flex justify-content-between py-2 border-bottom"><span class="text-muted small">Peserta</span><span class="fw-bold small"><?php echo htmlspecialchars($customerName); ?></span></div>
          <div class="d-flex justify-content-between py-2 border-bottom"><span class="text-muted small">Email</span><span class="fw-bold small text-break"><?php echo htmlspecialchars($customerEmail); ?></span></div>
          <div class="d-flex justify-content-between py-2"><span class="text-muted small">Mode</span><span class="badge bg-<?php echo strtolower($order['class_mode']??'offline')==='online'?'info':'success'; ?> bg-opacity-10 text-<?php echo strtolower($order['class_mode']??'offline')==='online'?'info':'success'; ?>"><?php echo htmlspecialchars(ucfirst($order['class_mode']??'offline')); ?></span></div>
          <div class="bg-light rounded-4 p-3 mt-3 text-center">
            <div class="small text-muted">Total Tagihan</div>
            <div class="h4 fw-bold mb-0" style="color:#0c4a6e;">Rp <?php echo number_format($amount,0,',','.'); ?></div>
            <div class="small text-muted">Sudah termasuk biaya admin</div>
          </div>
          <div id="statusBadge" class="mt-3 text-center">
            <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Menunggu Pembayaran</span>
            <div class="small text-muted mt-1">Status: <span id="paymentStatusText">unpaid</span></div>
          </div>
          <button id="btnCheckStatus" class="btn btn-outline-primary w-100 rounded-pill mt-3"><i class="fas fa-sync me-2"></i>Cek Status Pembayaran</button>
          <div class="small text-muted text-center mt-2">Auto cek tiap 10 detik</div>
        </div>
      </div>
    </div>

    <!-- Method Selector + Detail -->
    <div class="col-lg-8">
      <div class="payment-card p-4">
        <h6 class="fw-bold mb-3">Pilih Metode Pembayaran</h6>
        <div class="row g-3 mb-4" id="methodGrid">
          <div class="col-md-4"><div class="method-tab active" data-method="va"><div class="d-flex align-items-center gap-2"><i class="fas fa-university"></i><div><div class="fw-bold small">Virtual Account</div><div class="small text-muted" style="font-size:0.72rem;">BCA, Mandiri, BRI...</div></div></div></div></div>
          <div class="col-md-4"><div class="method-tab" data-method="qris"><div class="d-flex align-items-center gap-2"><i class="fas fa-qrcode"></i><div><div class="fw-bold small">QRIS</div><div class="small text-muted" style="font-size:0.72rem;">Scan QR</div></div></div></div></div>
          <div class="col-md-4"><div class="method-tab" data-method="ewallet"><div class="d-flex align-items-center gap-2"><i class="fas fa-wallet"></i><div><div class="fw-bold small">E-Wallet</div><div class="small text-muted" style="font-size:0.72rem;">OVO/DANA/ShopeePay</div></div></div></div></div>
          <div class="col-12"><div class="method-tab" data-method="cc"><div class="d-flex align-items-center gap-2"><i class="fas fa-credit-card"></i><div><div class="fw-bold small">Kartu Kredit/Debit</div><div class="small text-muted" style="font-size:0.72rem;">Tetap via halaman DOKU resmi</div></div></div></div></div>
        </div>

        <!-- VA Panel -->
        <div id="panel-va" class="method-panel">
          <label class="small fw-bold mb-2">Pilih Bank VA</label>
          <select id="vaBank" class="form-select rounded-pill mb-3">
            <option value="bca" <?php echo $existingBank==='bca'?'selected':''; ?>>BCA Virtual Account</option>
            <option value="mandiri" <?php echo $existingBank==='mandiri'?'selected':''; ?>>Mandiri Virtual Account</option>
            <option value="bri" <?php echo $existingBank==='bri'?'selected':''; ?>>BRI Virtual Account</option>
            <option value="bni" <?php echo $existingBank==='bni'?'selected':''; ?>>BNI Virtual Account</option>
            <option value="danamon" <?php echo $existingBank==='danamon'?'selected':''; ?>>Danamon Virtual Account</option>
            <option value="permata" <?php echo $existingBank==='permata'?'selected':''; ?>>Permata Virtual Account</option>
            <option value="cimb" <?php echo $existingBank==='cimb'?'selected':''; ?>>CIMB Virtual Account</option>
          </select>
          <?php if ($isVAValid): ?>
            <div class="alert alert-success rounded-4 py-2 small mb-3"><i class="fas fa-check-circle me-1"></i>VA aktif untuk bank ini — nomor di bawah tetap sama walau refresh.</div>
            <button id="btnGenerateVA" class="btn btn-outline-primary w-100 rounded-pill"><i class="fas fa-sync me-2"></i>Generate Ulang VA</button>
          <?php else: ?>
            <?php if (!empty($existingVA)): ?><div class="alert alert-warning rounded-4 py-2 small mb-3"><i class="fas fa-clock me-1"></i>VA sebelumnya kadaluarsa — silakan buat baru.</div><?php endif; ?>
            <button id="btnGenerateVA" class="btn btn-primary w-100 rounded-pill"><i class="fas fa-bolt me-2"></i>Buat Nomor VA</button>
          <?php endif; ?>
          <div id="vaResult" class="mt-4 <?php echo $isVAValid?'':'d-none'; ?>">
            <div class="va-box">
              <div class="small text-muted">Nomor Virtual Account</div>
              <div class="va-number" id="vaNumber"><?php echo $isVAValid ? htmlspecialchars($existingVA) : '-'; ?></div>
              <div class="small text-muted">Bank: <span id="vaBankLabel" class="fw-bold text-dark"><?php echo $isVAValid ? htmlspecialchars(strtoupper($existingBank)) : '-'; ?></span> • Batas: <span id="vaExpiry" class="countdown"><?php echo $isVAValid ? htmlspecialchars($existingExpiry) : '-'; ?></span></div>
              <button class="btn btn-outline-secondary btn-sm rounded-pill mt-2" onclick="navigator.clipboard.writeText(document.getElementById('vaNumber').textContent); Swal.fire('Disalin','Nomor VA disalin','success')"><i class="fas fa-copy me-1"></i>Salin VA</button>
            </div>
            <div class="mt-3">
              <label class="small fw-bold">Cara Bayar</label>
              <select id="vaInstr" class="form-select instr-select form-select-sm">
                <option value="atm">ATM</option>
                <option value="mbanking">M-Banking</option>
                <option value="ibanking">Internet Banking</option>
              </select>
              <ol id="vaSteps" class="small text-muted mt-2 ps-3"></ol>
            </div>
          </div>
        </div>

        <!-- QRIS Panel -->
        <div id="panel-qris" class="method-panel d-none text-center">
          <button id="btnGenerateQRIS" class="btn btn-primary rounded-pill px-5"><i class="fas fa-qrcode me-2"></i>Tampilkan QRIS</button>
          <div id="qrisResult" class="mt-4 d-none">
            <div class="qr-box"><div id="qrisQR"></div></div>
            <div class="mt-3"><span class="badge bg-light text-dark border">Batas: <span id="qrisExpiry" class="countdown">-</span></span></div>
            <div class="small text-muted mt-2">Scan pakai OVO/DANA/ShopeePay/LinkAja/BCA mobile</div>
          </div>
        </div>

        <!-- E-Wallet Panel -->
        <div id="panel-ewallet" class="method-panel d-none">
          <div class="d-flex gap-2 mb-3">
            <button class="btn btn-outline-primary rounded-pill flex-fill ewallet-btn" data-ew="ovo">OVO</button>
            <button class="btn btn-outline-primary rounded-pill flex-fill ewallet-btn" data-ew="dana">DANA</button>
            <button class="btn btn-outline-primary rounded-pill flex-fill ewallet-btn" data-ew="shopeepay">ShopeePay</button>
            <button class="btn btn-outline-primary rounded-pill flex-fill ewallet-btn" data-ew="linkaja">LinkAja</button>
          </div>
          <div id="ewalletResult" class="d-none text-center">
            <div class="qr-box"><div id="ewalletQR"></div></div>
            <a id="ewalletLink" href="#" target="_blank" class="btn btn-success rounded-pill mt-3"><i class="fas fa-external-link-alt me-2"></i>Buka di Aplikasi</a>
            <div class="mt-2 small text-muted">Atau scan QR di atas</div>
            <div class="small">Batas: <span id="ewalletExpiry" class="countdown">-</span></div>
          </div>
        </div>

        <!-- CC Panel -->
        <div id="panel-cc" class="method-panel d-none text-center">
          <p class="small text-muted">Kartu kredit/debit tetap via halaman DOKU resmi untuk keamanan (form kartu DOKU).</p>
          <button id="btnCC" class="btn btn-primary rounded-pill px-5"><i class="fas fa-credit-card me-2"></i>Bayar dengan Kartu</button>
        </div>
      </div>
    </div>
  </div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
const ORDER_NUMBER = <?php echo json_encode($orderNumber); ?>;
let countdownTimer = null;
function startCountdown(expiryStr, elId){
  clearInterval(countdownTimer);
  const el = document.getElementById(elId);
  if(!el) return;
  function tick(){
    const diff = new Date(expiryStr) - new Date();
    if(diff <= 0){ el.textContent = 'Kadaluarsa'; clearInterval(countdownTimer); return; }
    const h = Math.floor(diff/3600000), m = Math.floor((diff%3600000)/60000), s = Math.floor((diff%60000)/1000);
    el.textContent = (h>0? h+'j ':'') + String(m).padStart(2,'0')+':'+String(s).padStart(2,'0');
  }
  tick(); countdownTimer = setInterval(tick, 1000);
}

// Method tab switching
document.querySelectorAll('.method-tab').forEach(tab=>{
  tab.addEventListener('click', ()=>{
    document.querySelectorAll('.method-tab').forEach(t=>t.classList.remove('active'));
    tab.classList.add('active');
    const m = tab.dataset.method;
    document.querySelectorAll('.method-panel').forEach(p=>p.classList.add('d-none'));
    document.getElementById('panel-'+m).classList.remove('d-none');
  });
});

// VA — pakai URL absolut agar benar dari /payment/ maupun /pages/payment/ (wrapper)
const PAYMENT_BASE = <?php echo json_encode(pg_base_url() . '/payment'); ?>;
document.getElementById('btnGenerateVA')?.addEventListener('click', async function(){
  const btn=this, bank=document.getElementById('vaBank').value;
  btn.disabled=true; btn.innerHTML='<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
  try{
    const fd=new FormData(); fd.append('order', ORDER_NUMBER); fd.append('method','va'); fd.append('bank', bank);
    const r=await fetch(PAYMENT_BASE + '/generate_payment.php',{method:'POST',body:fd});
    const j=await r.json();
    if(j.status!=='success') throw new Error(j.message||'Gagal');
    document.getElementById('vaNumber').textContent=j.va_number;
    document.getElementById('vaBankLabel').textContent=j.va_bank.toUpperCase();
    startCountdown(j.expiry,'vaExpiry');
    document.getElementById('vaResult').classList.remove('d-none');
    updateInstr(bank);
  }catch(e){ Swal.fire('Gagal', e.message,'error'); }
  finally{ btn.disabled=false; btn.innerHTML='<i class="fas fa-bolt me-2"></i>Buat Nomor VA'; }
});
function updateInstr(bank){
  const sel=document.getElementById('vaInstr').value;
  const steps={
    atm: ['Masukkan kartu ATM','Pilih Transfer','Masukkan nomor VA','Masukkan nominal Rp '+ (<?php echo $amount; ?>).toLocaleString('id-ID'),'Konfirmasi'],
    mbanking: ['Buka M-Banking','Pilih Transfer Virtual Account','Masukkan nomor VA','Konfirmasi nominal','Masukkan PIN'],
    ibanking: ['Login Internet Banking','Pilih Pembayaran VA','Masukkan nomor VA','Konfirmasi']
  };
  const ol=document.getElementById('vaSteps');
  ol.innerHTML=(steps[sel]||steps['atm']).map(s=>'<li>'+s+'</li>').join('');
}
document.getElementById('vaInstr')?.addEventListener('change', ()=> updateInstr(document.getElementById('vaBank').value));

// QRIS
document.getElementById('btnGenerateQRIS')?.addEventListener('click', async function(){
  const btn=this; btn.disabled=true; btn.innerHTML='<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
  try{
    const fd=new FormData(); fd.append('order', ORDER_NUMBER); fd.append('method','qris');
    const r=await fetch(PAYMENT_BASE + '/generate_payment.php',{method:'POST',body:fd});
    const j=await r.json();
    if(j.status!=='success') throw new Error(j.message);
    const box=document.getElementById('qrisQR'); box.innerHTML='';
    new QRCode(box, {text:j.qris_string, width:180, height:180});
    startCountdown(j.expiry,'qrisExpiry');
    document.getElementById('qrisResult').classList.remove('d-none');
  }catch(e){ Swal.fire('Gagal',e.message,'error'); }
  finally{ btn.disabled=false; btn.innerHTML='<i class="fas fa-qrcode me-2"></i>Tampilkan QRIS'; }
});

// E-Wallet
document.querySelectorAll('.ewallet-btn').forEach(btn=>{
  btn.addEventListener('click', async function(){
    const type=this.dataset.ew;
    document.querySelectorAll('.ewallet-btn').forEach(b=>b.classList.remove('active','btn-primary'));
    this.classList.add('active','btn-primary');
    try{
      const fd=new FormData(); fd.append('order', ORDER_NUMBER); fd.append('method','ewallet'); fd.append('ewallet', type);
      const r=await fetch(PAYMENT_BASE + '/generate_payment.php',{method:'POST',body:fd});
      const j=await r.json();
      if(j.status!=='success') throw new Error(j.message);
      const box=document.getElementById('ewalletQR'); box.innerHTML='';
      new QRCode(box, {text:j.qris_string || j.ewallet_url, width:180, height:180});
      document.getElementById('ewalletLink').href=j.ewallet_url;
      startCountdown(j.expiry,'ewalletExpiry');
      document.getElementById('ewalletResult').classList.remove('d-none');
    }catch(e){ Swal.fire('Gagal',e.message,'error'); }
  });
});

// CC
document.getElementById('btnCC')?.addEventListener('click', async function(){
  const btn=this; btn.disabled=true;
  try{
    const fd=new FormData(); fd.append('order', ORDER_NUMBER); fd.append('method','cc');
    const r=await fetch(PAYMENT_BASE + '/generate_payment.php',{method:'POST',body:fd});
    const j=await r.json();
    if(j.status==='success' && j.payment_url) window.location.href=j.payment_url;
    else throw new Error(j.message||'Gagal');
  }catch(e){ Swal.fire('Gagal',e.message,'error'); btn.disabled=false; }
});

// Polling status — pakai URL absolut
let pollTimer=setInterval(async ()=>{
  try{
    const r=await fetch(PAYMENT_BASE + '/check_status.php?order='+encodeURIComponent(ORDER_NUMBER));
    const j=await r.json();
    document.getElementById('paymentStatusText').textContent=j.payment_status||'-';
    if(j.payment_status==='paid'){
      clearInterval(pollTimer);
      document.getElementById('statusBadge').innerHTML='<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Lunas</span>';
      Swal.fire({icon:'success',title:'Pembayaran Berhasil!',text:'Terima kasih, akses LMS sudah terbuka.', timer:2000, showConfirmButton:false}).then(()=> window.location.href='payment_status.php?order='+encodeURIComponent(ORDER_NUMBER));
    }
  }catch(e){}
}, 8000);

document.getElementById('btnCheckStatus')?.addEventListener('click', async ()=>{
  const r=await fetch(PAYMENT_BASE + '/check_status.php?order='+encodeURIComponent(ORDER_NUMBER));
  const j=await r.json();
  Swal.fire(j.payment_status==='paid' ? 'Lunas' : 'Status', 'Status: '+(j.payment_status||'-'), j.payment_status==='paid'?'success':'info');
  if(j.payment_status==='paid') window.location.href='payment_status.php?order='+encodeURIComponent(ORDER_NUMBER);
});

// Auto-tampilkan VA yang sudah ada & belum expired (persisten)
<?php if ($isVAValid): ?>
document.addEventListener('DOMContentLoaded', ()=>{
  startCountdown(<?php echo json_encode($existingExpiry); ?>,'vaExpiry');
  updateInstr(document.getElementById('vaBank').value);
  // pastikan tab VA aktif
  document.querySelectorAll('.method-tab').forEach(t=>t.classList.remove('active'));
  document.querySelector('.method-tab[data-method="va"]')?.classList.add('active');
  document.querySelectorAll('.method-panel').forEach(p=>p.classList.add('d-none'));
  document.getElementById('panel-va').classList.remove('d-none');
});
<?php endif; ?>
<?php if ($isQRISValid): ?>
document.addEventListener('DOMContentLoaded', ()=>{
  const box=document.getElementById('qrisQR'); if(box && !box.innerHTML.trim()){
    new QRCode(box, {text:<?php echo json_encode($existingQRIS); ?>, width:180, height:180});
    startCountdown(<?php echo json_encode($existingExpiry); ?>,'qrisExpiry');
    document.getElementById('qrisResult').classList.remove('d-none');
    document.querySelectorAll('.method-tab').forEach(t=>t.classList.remove('active'));
    document.querySelector('.method-tab[data-method="qris"]')?.classList.add('active');
    document.querySelectorAll('.method-panel').forEach(p=>p.classList.add('d-none'));
    document.getElementById('panel-qris').classList.remove('d-none');
  }
});
<?php endif; ?>
<?php if ($isEwalletValid): ?>
document.addEventListener('DOMContentLoaded', ()=>{
  const box=document.getElementById('ewalletQR'); if(box && !box.innerHTML.trim()){
    new QRCode(box, {text:<?php echo json_encode($existingQRIS ?? $existingEwalletUrl); ?>, width:180, height:180});
    document.getElementById('ewalletLink').href=<?php echo json_encode($existingEwalletUrl); ?>;
    startCountdown(<?php echo json_encode($existingExpiry); ?>,'ewalletExpiry');
    document.getElementById('ewalletResult').classList.remove('d-none');
  }
});
<?php endif; ?>
</script>
</body>
</html>
