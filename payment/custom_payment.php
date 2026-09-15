<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/payment_gateway.php';
pg_ensure_payment_columns($pdo);
pg_ensure_payment_methods($pdo);
$pm = pg_payment_methods($pdo);

$orderNumber = trim($_GET['order'] ?? '');
if ($orderNumber === '') { http_response_code(404); include __DIR__ . '/../404.php'; exit; }

$stmt = $pdo->prepare("SELECT o.*, c.name AS class_name, c.category AS class_category, i.name AS instructor_name, i.specialization AS instructor_spec FROM orders o LEFT JOIN classes c ON o.class_id = c.id LEFT JOIN instructors i ON o.instructor_id = i.id WHERE o.order_number = ? LIMIT 1");
$stmt->execute([$orderNumber]);
$order = $stmt->fetch();
if (!$order) { http_response_code(404); include __DIR__ . '/../404.php'; exit; }

$className = $order['class_name'] ?? 'Program Pelatihan MCM';
$amount = (int)$order['amount'];
$customerName = $order['customer_name'];
$customerEmail = $order['customer_email'];
$instructorName = $order['instructor_name'] ?? null;
$instructorSpec = $order['instructor_spec'] ?? null;
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

if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
$csrf_token = $_SESSION['csrf_token'];
// Rekening tujuan bisa diubah admin di ?page=payment_methods (tabel bank_accounts)
pg_ensure_bank_accounts($pdo);
$bankAccounts = pg_bank_accounts($pdo, true);
if (empty($bankAccounts)) {
    // Fallback data lama (tabel settings) bila belum ada rekening aktif
    $bankAccounts = [[
        'id' => 0,
        'bank_name' => mcm_setting('manual_bank_name', 'BCA'),
        'account_number' => mcm_setting('manual_bank_account', '8210101010'),
        'account_holder' => mcm_setting('manual_bank_holder', 'Mitra Cipta Mandiri'),
    ]];
}
$isAwaiting = (($order['payment_method'] ?? '') === 'manual_transfer') && ($order['payment_status'] ?? '') !== 'paid' && ($order['status'] ?? '') === 'pending' && !empty($order['transfer_proof']);
// Status kartu metode dibaca dari DB (admin: Kelola Metode Pembayaran)
$pmCards = [
    ['method' => 'va',      'key' => 'virtual_account', 'icon' => 'fa-university',  'title' => 'Virtual Account',     'desc' => 'BCA, Mandiri, BRI...',             'col' => 'col-md-4'],
    ['method' => 'qris',    'key' => 'qris',            'icon' => 'fa-qrcode',       'title' => 'QRIS',                'desc' => 'Scan QR',                          'col' => 'col-md-4'],
    ['method' => 'ewallet', 'key' => 'e_wallet',        'icon' => 'fa-wallet',       'title' => 'E-Wallet',            'desc' => 'OVO/DANA/ShopeePay',               'col' => 'col-md-4'],
    ['method' => 'manual',  'key' => 'bank_transfer',   'icon' => 'fa-landmark',     'title' => 'Transfer Bank Manual','desc' => 'Upload bukti, verifikasi admin', 'col' => 'col-md-6'],
    ['method' => 'cc',      'key' => 'credit_card',     'icon' => 'fa-credit-card',  'title' => 'Kartu Kredit/Debit',  'desc' => 'Tetap via halaman DOKU resmi',    'col' => 'col-md-6'],
];
$pmDefault = 'manual';
if (empty($pm['bank_transfer']['is_active'])) {
    foreach ($pmCards as $c) { if (!empty($pm[$c['key']]['is_active'])) { $pmDefault = $c['method']; break; } }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Bayar Pesanan — MCM</title>
<?php $favicon_base = pg_base_url(); require __DIR__ . '/../includes/favicon.php'; ?>
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
.method-tab.disabled { opacity:.6; cursor:not-allowed; background:#f8fafc; }
.method-tab.disabled:hover { border-color:#e2e8f0; background:#f8fafc; }
.method-tab.disabled i { background:#e2e8f0; color:#94a3b8; }
.method-tab i { width:36px; height:36px; display:inline-flex; align-items:center; justify-content:center; border-radius:0.7rem; background:#f1f5f9; color:#0c4a6e; }
.method-tab.active i { background:linear-gradient(135deg,#0c4a6e,#0ea5e9); color:#fff; }
.va-box { background:#f8fafc; border:1px dashed #cbd5e1; border-radius:1rem; padding:16px; text-align:center; }
.va-number { font-size:1.6rem; font-weight:800; letter-spacing:1px; color:#0c4a6e; font-monospace; }
.countdown { font-variant-numeric: tabular-nums; font-weight:700; color:#dc2626; }
.qr-box { background:#fff; border:1px solid #e2e8f0; border-radius:1rem; padding:16px; display:inline-block; box-shadow:0 8px 24px rgba(15,23,42,.08); }
.instr-select { border-radius:999px; }
.method-panel { border-top:1px solid #eef2f7; padding-top:1.25rem; }
.va-number { overflow-wrap:anywhere; }
.manual-dd { position:relative; text-align:left; }
.manual-dd-btn { display:flex; align-items:center; gap:8px; width:100%; background:#fff; border:1px solid #dee2e6; border-radius:999px; padding:.375rem .75rem .375rem 1rem; font-size:1rem; line-height:1.5; cursor:pointer; }
.manual-dd-btn:hover { border-color:#0ea5e9; }
.manual-dd-btn:focus-visible { outline:none; border-color:#0ea5e9; box-shadow:0 0 0 .25rem rgba(14,165,233,.25); }
.manual-dd-val { flex:1; min-width:0; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.manual-dd-chev { color:#0c4a6e; font-size:.8rem; transition:transform .2s; }
.manual-dd.open .manual-dd-chev { transform:rotate(180deg); }
.manual-dd-list { position:absolute; top:calc(100% + 6px); left:0; right:0; background:#fff; border:1px solid #e2e8f0; border-radius:1rem; box-shadow:0 12px 32px rgba(15,23,42,.14); padding:6px; z-index:1050; max-height:240px; overflow:auto; }
.manual-dd-list.dd-fixed { position:fixed; }
.manual-dd-item { display:block; width:100%; text-align:left; background:transparent; border:0; border-radius:.65rem; padding:10px 12px; cursor:pointer; }
.manual-dd-item:hover { background:#f0f9ff; }
.manual-dd-item.active { background:#eff6ff; }
.manual-dd-item.active .manual-dd-bank { color:#0c4a6e; }
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
          <div class="d-flex justify-content-between py-2 border-bottom"><span class="text-muted small">Instruktur</span><span class="fw-bold small text-end" style="max-width:180px;"><?php echo $instructorName ? htmlspecialchars($instructorName) . '<br><span class="fw-normal text-muted" style="font-size:0.72rem;">'.htmlspecialchars($instructorSpec??'').'</span>' : '<span class="text-muted">Instruktur akan ditentukan oleh admin</span>'; ?></span></div>
          <div class="d-flex justify-content-between py-2"><span class="text-muted small">Mode</span><span class="badge bg-<?php echo strtolower($order['class_mode']??'offline')==='online'?'info':'success'; ?> bg-opacity-10 text-<?php echo strtolower($order['class_mode']??'offline')==='online'?'info':'success'; ?>"><?php echo htmlspecialchars(ucfirst($order['class_mode']??'offline')); ?></span></div>
          <div class="bg-light rounded-4 p-3 mt-3 text-center">
            <div class="small text-muted">Total Tagihan</div>
            <div class="h4 fw-bold mb-0" style="color:#0c4a6e;">Rp <?php echo number_format($amount,0,',','.'); ?></div>
            <div class="small text-muted">Sudah termasuk biaya admin</div>
          </div>
          <div id="statusBadge" class="mt-3 text-center">
            <?php if ($isAwaiting): ?>
            <span class="badge text-white" style="background:#7c3aed;"><i class="fas fa-user-check me-1"></i>Menunggu Konfirmasi Admin</span>
            <div class="small text-muted mt-1">Bukti transfer diterima, maks 1x24 jam</div>
            <?php else: ?>
            <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Menunggu Pembayaran</span>
            <div class="small text-muted mt-1">Status: <span id="paymentStatusText">unpaid</span></div>
            <?php endif; ?>
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
          <?php foreach ($pmCards as $c):
              $st = $pm[$c['key']] ?? ['is_active' => false, 'note' => ''];
              $pmOff = empty($st['is_active']);
              $pmBadge = $pmOff ? ($st['note'] !== '' ? $st['note'] : 'Nonaktif') : '';
          ?>
          <div class="<?php echo $c['col']; ?>"><div class="method-tab<?php echo $c['method'] === $pmDefault ? ' active' : ''; ?><?php echo $pmOff ? ' disabled' : ''; ?>" data-method="<?php echo $c['method']; ?>"<?php echo $pmOff ? ' aria-disabled="true"' : ''; ?>><div class="d-flex align-items-center gap-2"><i class="fas <?php echo $c['icon']; ?>"></i><div><div class="fw-bold small"><?php echo $c['title']; ?></div><div class="small text-muted" style="font-size:0.72rem;"><?php echo $c['desc']; ?></div><?php if ($pmBadge !== ''): ?><span class="badge bg-secondary fw-normal mt-1" style="font-size:0.65rem;"><?php echo htmlspecialchars($pmBadge); ?></span><?php endif; ?></div></div></div></div>
          <?php endforeach; ?>
        </div>

        <?php $vaBanks = ['bca' => 'BCA Virtual Account', 'mandiri' => 'Mandiri Virtual Account', 'bri' => 'BRI Virtual Account', 'bni' => 'BNI Virtual Account', 'danamon' => 'Danamon Virtual Account', 'permata' => 'Permata Virtual Account', 'cimb' => 'CIMB Virtual Account']; ?>
        <!-- VA Panel -->
        <div id="panel-va" class="method-panel<?php echo $pmDefault === 'va' ? '' : ' d-none'; ?>">
          <label class="small fw-bold mb-2" for="vaDropdownBtn">Pilih Bank VA</label>
          <div class="manual-dd mb-3" id="vaDropdown">
            <button type="button" class="manual-dd-btn" id="vaDropdownBtn" aria-haspopup="listbox" aria-expanded="false">
              <span class="manual-dd-val" id="vaDropdownLabel"><?php echo htmlspecialchars($vaBanks[$existingBank] ?? $vaBanks['danamon']); ?></span>
              <i class="fas fa-chevron-down manual-dd-chev"></i>
            </button>
            <div class="manual-dd-list d-none" id="vaDropdownList" role="listbox" aria-label="Pilih bank VA">
              <?php foreach ($vaBanks as $vk => $vl): ?>
              <button type="button" class="manual-dd-item<?php echo $vk === $existingBank ? ' active' : ''; ?>" role="option" data-key="<?php echo $vk; ?>" aria-selected="<?php echo $vk === $existingBank ? 'true' : 'false'; ?>">
                <div class="fw-bold small text-dark"><?php echo htmlspecialchars($vl); ?></div>
              </button>
              <?php endforeach; ?>
            </div>
          </div>
          <input type="hidden" id="vaBank" value="<?php echo htmlspecialchars($existingBank); ?>">
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
              <div class="small text-muted">Bank: <span id="vaBankLabel" class="fw-bold text-dark"><?php echo $isVAValid ? htmlspecialchars(strtoupper($existingBank)) : '-'; ?></span></div>
              <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill mt-2" id="btnCopyVA"><i class="fas fa-copy me-1"></i>Salin VA</button>
              <div class="mt-3 bg-white rounded-3 py-2 px-3 d-inline-block border">
                <span class="small text-muted">Nominal transfer: </span>
                <span class="fw-bold" style="color:#0c4a6e;">Rp <?php echo number_format($amount,0,',','.'); ?></span>
              </div>
              <div class="mt-2"><span class="badge bg-light text-dark border">Batas: <span id="vaExpiry" class="countdown"><?php echo $isVAValid ? htmlspecialchars($existingExpiry) : '-'; ?></span></span></div>
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
        <div id="panel-qris" class="method-panel text-center<?php echo $pmDefault === 'qris' ? '' : ' d-none'; ?>">
          <div class="small fw-bold mb-2 text-start">Kode QRIS</div>
          <button id="btnGenerateQRIS" class="btn btn-primary rounded-pill px-5"><i class="fas fa-qrcode me-2"></i>Tampilkan QRIS</button>
          <div id="qrisResult" class="mt-4 d-none">
            <div class="qr-box"><div id="qrisQR"></div></div>
            <div class="mt-3"><span class="badge bg-light text-dark border">Batas: <span id="qrisExpiry" class="countdown">-</span></span></div>
            <div class="small text-muted mt-2">Scan pakai OVO/DANA/ShopeePay/LinkAja/BCA mobile</div>
          </div>
        </div>

        <!-- E-Wallet Panel -->
        <div id="panel-ewallet" class="method-panel<?php echo $pmDefault === 'ewallet' ? '' : ' d-none'; ?>">
          <div class="small fw-bold mb-2">Pilih Provider</div>
          <div class="row g-2 mb-4">
            <div class="col-6 col-sm-3"><button class="btn btn-outline-primary rounded-pill w-100 ewallet-btn" data-ew="ovo">OVO</button></div>
            <div class="col-6 col-sm-3"><button class="btn btn-outline-primary rounded-pill w-100 ewallet-btn" data-ew="dana">DANA</button></div>
            <div class="col-6 col-sm-3"><button class="btn btn-outline-primary rounded-pill w-100 ewallet-btn" data-ew="shopeepay">ShopeePay</button></div>
            <div class="col-6 col-sm-3"><button class="btn btn-outline-primary rounded-pill w-100 ewallet-btn" data-ew="linkaja">LinkAja</button></div>
          </div>
          <div id="ewalletResult" class="d-none">
            <div class="d-flex flex-column flex-sm-row align-items-center justify-content-center gap-3">
              <div class="qr-box"><div id="ewalletQR"></div></div>
              <div class="text-center text-sm-start">
                <a id="ewalletLink" href="#" target="_blank" class="btn btn-success rounded-pill px-4"><i class="fas fa-external-link-alt me-2"></i>Buka di Aplikasi</a>
                <div class="mt-2 small text-muted">Atau scan kode QR</div>
                <div class="mt-2"><span class="badge bg-light text-dark border">Batas: <span id="ewalletExpiry" class="countdown">-</span></span></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Manual Panel -->
        <div id="panel-manual" class="method-panel<?php echo $pmDefault === 'manual' ? '' : ' d-none'; ?>">
          <div class="va-box">
            <div class="small text-muted">Transfer ke rekening berikut</div>
            <?php if (count($bankAccounts) > 1): ?>
            <label class="small fw-bold mt-2 mb-1" for="manualDropdownBtn">Pilih Bank Tujuan</label>
            <div class="manual-dd" id="manualDropdown">
              <button type="button" class="manual-dd-btn" id="manualDropdownBtn" aria-haspopup="listbox" aria-expanded="false">
                <span class="manual-dd-val" id="manualDropdownLabel"><?php echo htmlspecialchars($bankAccounts[0]['bank_name'] . ', ' . $bankAccounts[0]['account_number']); ?></span>
                <i class="fas fa-chevron-down manual-dd-chev"></i>
              </button>
              <div class="manual-dd-list d-none" id="manualDropdownList" role="listbox" aria-label="Pilih bank tujuan">
                <?php foreach (array_values($bankAccounts) as $i => $ba): ?>
                <button type="button" class="manual-dd-item<?php echo $i === 0 ? ' active' : ''; ?>" role="option" data-idx="<?php echo $i; ?>" aria-selected="<?php echo $i === 0 ? 'true' : 'false'; ?>">
                  <div class="fw-bold small text-dark manual-dd-bank"><?php echo htmlspecialchars($ba['bank_name']); ?></div>
                  <div class="small text-muted"><?php echo htmlspecialchars($ba['account_number'] . ', a.n. ' . $ba['account_holder']); ?></div>
                </button>
                <?php endforeach; ?>
              </div>
            </div>
            <?php endif; ?>
            <div class="fw-bold mt-2" style="color:#0c4a6e;"><i class="fas fa-landmark me-1"></i><span id="manualBankLabel"><?php echo htmlspecialchars($bankAccounts[0]['bank_name']); ?>, a.n. <?php echo htmlspecialchars($bankAccounts[0]['account_holder']); ?></span></div>
            <div class="va-number" id="manualAccount"><?php echo htmlspecialchars($bankAccounts[0]['account_number']); ?></div>
            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill mt-2" id="btnCopyAccount"><i class="fas fa-copy me-1"></i>Salin Nomor Rekening</button>
            <div class="mt-3 bg-white rounded-3 py-2 px-3 d-inline-block border">
              <span class="small text-muted">Nominal transfer: </span>
              <span class="fw-bold" style="color:#0c4a6e;">Rp <?php echo number_format($amount,0,',','.'); ?></span>
            </div>
          </div>
          <?php if ($isAwaiting): ?>
          <div class="alert rounded-4 py-2 small mt-3 mb-0 text-white" style="background:#7c3aed;"><i class="fas fa-user-check me-1"></i>Bukti transfer sudah kami terima dan menunggu konfirmasi admin (maks 1x24 jam). Upload ulang di bawah jika ingin mengganti bukti.</div>
          <?php endif; ?>
          <form id="proofForm" class="mt-3" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
            <input type="hidden" name="order" value="<?php echo htmlspecialchars($orderNumber); ?>">
            <label class="small fw-bold mb-2" for="proofFile">Upload Bukti Transfer <span class="fw-normal text-muted">(JPG/PNG/PDF, maks 2MB)</span></label>
            <input type="file" id="proofFile" name="bukti" class="form-control rounded-3" accept=".jpg,.jpeg,.png,.pdf" required>
            <div class="text-center mt-3">
              <img id="proofPreview" class="d-none rounded-3 border" style="max-width:100%; max-height:260px;" alt="Preview bukti transfer">
              <div id="proofPdfInfo" class="d-none small text-muted"><i class="fas fa-file-pdf me-1 text-danger"></i><span id="proofPdfName"></span></div>
            </div>
            <button type="submit" id="btnSendProof" class="btn btn-primary w-100 rounded-pill mt-3"><i class="fas fa-paper-plane me-2"></i>Kirim Bukti Pembayaran</button>
          </form>
        </div>

        <!-- CC Panel -->
        <div id="panel-cc" class="method-panel text-center<?php echo $pmDefault === 'cc' ? '' : ' d-none'; ?>">
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
    if(tab.classList.contains('disabled')) return;
    document.querySelectorAll('.method-tab').forEach(t=>t.classList.remove('active'));
    tab.classList.add('active');
    const m = tab.dataset.method;
    document.querySelectorAll('.method-panel').forEach(p=>p.classList.add('d-none'));
    document.getElementById('panel-'+m).classList.remove('d-none');
  });
});

// VA — dropdown bank custom (pakai komponen yang sama dengan Transfer Manual)
const vaWrap = document.getElementById('vaDropdown');
const vaBtn = document.getElementById('vaDropdownBtn');
const vaList = document.getElementById('vaDropdownList');
const vaHidden = document.getElementById('vaBank');
function vaSetOpen(open){
  if (!vaWrap) return;
  vaWrap.classList.toggle('open', open);
  vaList.classList.toggle('d-none', !open);
  vaList.classList.toggle('dd-fixed', open);
  if (!open) { vaList.style.top = ''; vaList.style.left = ''; vaList.style.width = ''; vaList.style.maxHeight = ''; }
  vaBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
  if (open) vaPlace();
}
// Lapisan fixed lepas dari overflow:hidden .payment-card; flip ke atas bila ruang bawah sempit
function vaPlace(){
  if (!vaWrap || vaList.classList.contains('d-none')) return;
  const r = vaBtn.getBoundingClientRect();
  const gap = 6, pad = 8;
  const maxH = Math.max(120, window.innerHeight - pad * 2);
  vaList.style.maxHeight = maxH + 'px';
  vaList.style.left = r.left + 'px';
  vaList.style.width = r.width + 'px';
  const need = Math.min(vaList.scrollHeight, maxH);
  let top = r.bottom + gap;
  if (top + need > window.innerHeight - pad) top = r.top - gap - need;
  vaList.style.top = Math.max(pad, top) + 'px';
}
function vaPick(key, label){
  vaHidden.value = key;
  document.getElementById('vaDropdownLabel').textContent = label;
  vaList.querySelectorAll('.manual-dd-item').forEach(function(el){
    const sel = el.getAttribute('data-key') === key;
    el.classList.toggle('active', sel);
    el.setAttribute('aria-selected', sel ? 'true' : 'false');
  });
}
vaBtn?.addEventListener('click', function(e){
  e.stopPropagation();
  vaSetOpen(vaList.classList.contains('d-none'));
});
vaList?.querySelectorAll('.manual-dd-item').forEach(function(el){
  el.addEventListener('click', function(){
    vaPick(el.getAttribute('data-key'), el.textContent.trim());
    vaSetOpen(false);
    vaBtn.focus();
  });
});
document.addEventListener('click', function(e){ if (vaWrap && !vaWrap.contains(e.target)) vaSetOpen(false); });
document.getElementById('btnCopyVA')?.addEventListener('click', ()=>{
  navigator.clipboard.writeText(document.getElementById('vaNumber').textContent.trim());
  Swal.fire('Disalin','Nomor VA disalin','success');
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

// Transfer Manual — dropdown rekening custom (multi-bank), salin rekening, preview, upload bukti
const BANK_ACCOUNTS = <?php echo json_encode(array_values($bankAccounts), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
const ddWrap = document.getElementById('manualDropdown');
const ddBtn = document.getElementById('manualDropdownBtn');
const ddList = document.getElementById('manualDropdownList');
function ddSetOpen(open){
  if (!ddWrap) return;
  ddWrap.classList.toggle('open', open);
  ddList.classList.toggle('d-none', !open);
  ddList.classList.toggle('dd-fixed', open);
  if (!open) { ddList.style.top = ''; ddList.style.left = ''; ddList.style.width = ''; ddList.style.maxHeight = ''; }
  ddBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
  if (open) ddPlace();
}
function ddPlace(){
  if (!ddWrap || ddList.classList.contains('d-none')) return;
  const r = ddBtn.getBoundingClientRect();
  const gap = 6, pad = 8;
  const maxH = Math.max(120, window.innerHeight - pad * 2);
  ddList.style.maxHeight = maxH + 'px';
  ddList.style.left = r.left + 'px';
  ddList.style.width = r.width + 'px';
  const need = Math.min(ddList.scrollHeight, maxH);
  let top = r.bottom + gap;
  if (top + need > window.innerHeight - pad) top = r.top - gap - need;
  ddList.style.top = Math.max(pad, top) + 'px';
}
function ddPick(i){
  const ba = BANK_ACCOUNTS[i] || BANK_ACCOUNTS[0];
  if (!ba) return;
  document.getElementById('manualDropdownLabel').textContent = ba.bank_name + ', ' + ba.account_number;
  document.getElementById('manualBankLabel').textContent = ba.bank_name + ', a.n. ' + ba.account_holder;
  document.getElementById('manualAccount').textContent = ba.account_number;
  ddList.querySelectorAll('.manual-dd-item').forEach(function(el){
    const sel = parseInt(el.getAttribute('data-idx'), 10) === i;
    el.classList.toggle('active', sel);
    el.setAttribute('aria-selected', sel ? 'true' : 'false');
  });
}
ddBtn?.addEventListener('click', function(e){
  e.stopPropagation();
  ddSetOpen(ddList.classList.contains('d-none'));
});
ddList?.querySelectorAll('.manual-dd-item').forEach(function(el){
  el.addEventListener('click', function(){
    ddPick(parseInt(el.getAttribute('data-idx'), 10));
    ddSetOpen(false);
    ddBtn.focus();
  });
});
document.addEventListener('click', function(e){ if (ddWrap && !ddWrap.contains(e.target)) ddSetOpen(false); });
document.addEventListener('keydown', function(e){ if (e.key === 'Escape') { ddSetOpen(false); vaSetOpen(false); } });
function ddReposition(){
  if (typeof ddList !== 'undefined' && ddList && !ddList.classList.contains('d-none')) ddPlace();
  if (typeof vaList !== 'undefined' && vaList && !vaList.classList.contains('d-none')) vaPlace();
}
window.addEventListener('scroll', ddReposition, {passive:true});
window.addEventListener('resize', ddReposition);
document.getElementById('btnCopyAccount')?.addEventListener('click', ()=>{
  navigator.clipboard.writeText(document.getElementById('manualAccount').textContent.trim());
  Swal.fire('Disalin','Nomor rekening disalin','success');
});
document.getElementById('proofFile')?.addEventListener('change', function(){
  const f = this.files[0];
  const img = document.getElementById('proofPreview'), pdf = document.getElementById('proofPdfInfo');
  img.classList.add('d-none'); pdf.classList.add('d-none');
  if(!f) return;
  const okExt = /\.(jpe?g|png|pdf)$/i.test(f.name);
  if(!okExt){ Swal.fire('Format salah','Gunakan JPG, PNG, atau PDF.','error'); this.value=''; return; }
  if(f.size > 2*1024*1024){ Swal.fire('Terlalu besar','Maksimal 2MB.','error'); this.value=''; return; }
  if(f.type === 'application/pdf'){ document.getElementById('proofPdfName').textContent = f.name; pdf.classList.remove('d-none'); }
  else { const rd = new FileReader(); rd.onload = e=>{ img.src = e.target.result; img.classList.remove('d-none'); }; rd.readAsDataURL(f); }
});
document.getElementById('proofForm')?.addEventListener('submit', async function(e){
  e.preventDefault();
  const fileInput = document.getElementById('proofFile');
  if(!fileInput.files.length){ Swal.fire('Belum ada file','Pilih file bukti transfer dulu.','warning'); return; }
  const btn = document.getElementById('btnSendProof');
  btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mengirim...';
  try{
    const r = await fetch(PAYMENT_BASE + '/upload_proof.php', {method:'POST', body:new FormData(this)});
    const j = await r.json();
    if(j.status !== 'success') throw new Error(j.message || 'Gagal upload');
    document.getElementById('statusBadge').innerHTML = '<span class="badge text-white" style="background:#7c3aed;"><i class="fas fa-user-check me-1"></i>Menunggu Konfirmasi Admin</span><div class="small text-muted mt-1">Bukti transfer diterima, maks 1x24 jam</div>';
    Swal.fire('Berhasil', j.message, 'success');
  }catch(err){ Swal.fire('Gagal', err.message, 'error'); }
  finally{ btn.disabled = false; btn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Kirim Bukti Pembayaran'; }
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
