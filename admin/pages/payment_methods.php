<?php
require_once __DIR__ . '/../../includes/payment_gateway.php';
pg_ensure_payment_methods($pdo);
$pmRows = $pdo->query("SELECT * FROM payment_methods ORDER BY id ASC")->fetchAll();
// Transfer Bank Manual dipisah dari loop umum: punya list rekening, tanpa field note
$pmManual = null;
$pmActive = [];
$pmInactive = [];
foreach ($pmRows as $m) {
    if (($m['method_key'] ?? '') === 'bank_transfer') { $pmManual = $m; continue; }
    if (!empty($m['is_active'])) $pmActive[] = $m; else $pmInactive[] = $m;
}
$bankRows = pg_bank_accounts($pdo, false);
$manualOn = $pmManual && !empty($pmManual['is_active']);
// Urutan preview mengikuti halaman pembayaran user
$prevNames = [];
foreach ($pmRows as $m) $prevNames[$m['method_key']] = $m['method_name'];
$prevList = [];
foreach (['virtual_account', 'qris', 'e_wallet', 'bank_transfer', 'credit_card'] as $pk) {
    if (isset($prevNames[$pk])) $prevList[] = ['key' => $pk, 'name' => $prevNames[$pk]];
}
$nSumActive = count($pmActive) + ($manualOn ? 1 : 0);
$nSumInactive = count($pmInactive) + (!$manualOn && $pmManual ? 1 : 0);

if (!function_exists('pm_method_card')) {
    function pm_method_card($m) {
        $k = $m['method_key'];
        $on = !empty($m['is_active']);
        ?>
        <div class="border rounded-4 p-3 mb-3 pm-card<?php echo $on ? ' pm-on' : ''; ?>" data-pm-card>
            <div class="d-flex align-items-center justify-content-between gap-3">
                <div>
                    <div class="fw-bold text-dark"><?php echo htmlspecialchars($m['method_name']); ?></div>
                    <div class="small <?php echo $on ? 'text-primary fw-bold' : 'text-muted'; ?>" data-pm-status><?php echo $on ? 'Aktif' : 'Nonaktif'; ?></div>
                </div>
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" role="switch" data-pm-toggle id="pm_<?php echo htmlspecialchars($k); ?>" name="active[<?php echo htmlspecialchars($k); ?>]" value="1"<?php echo $on ? ' checked' : ''; ?>>
                    <label class="visually-hidden" for="pm_<?php echo htmlspecialchars($k); ?>">Status <?php echo htmlspecialchars($m['method_name']); ?></label>
                </div>
            </div>
            <div class="mt-2<?php echo $on ? ' d-none' : ''; ?>" data-pm-note>
                <input type="text" class="form-control bg-light border-0" id="note_<?php echo htmlspecialchars($k); ?>" name="note[<?php echo htmlspecialchars($k); ?>]" value="<?php echo htmlspecialchars($m['note'] ?? ''); ?>" maxlength="100" placeholder="Contoh: Masih dalam pengembangan" aria-label="Teks badge <?php echo htmlspecialchars($m['method_name']); ?>">
            </div>
        </div>
        <?php
    }
}

if (!function_exists('pm_manual_card')) {
    function pm_manual_card($pmManual, $bankRows) {
        $on = !empty($pmManual['is_active']);
        ?>
        <div class="border rounded-4 p-3 mb-3 pm-card<?php echo $on ? ' pm-on' : ''; ?>" data-pm-card>
            <div class="d-flex align-items-center justify-content-between gap-3">
                <div>
                    <div class="fw-bold text-dark"><?php echo htmlspecialchars($pmManual['method_name']); ?></div>
                    <div class="small <?php echo $on ? 'text-primary fw-bold' : 'text-muted'; ?>" data-pm-status><?php echo $on ? 'Aktif' : 'Nonaktif'; ?></div>
                </div>
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" role="switch" data-pm-toggle id="pm_bank_transfer" name="active[bank_transfer]" value="1"<?php echo $on ? ' checked' : ''; ?>>
                    <label class="visually-hidden" for="pm_bank_transfer">Status Transfer Bank Manual</label>
                </div>
            </div>
            <div id="bankAccWarn" class="alert alert-warning rounded-3 py-2 small mt-2 mb-0 d-none"><i class="fas fa-exclamation-triangle me-1"></i>Metode ini aktif tetapi tidak ada rekening aktif. User tidak bisa memilih rekening saat membayar.</div>
            <div class="border-top mt-3 pt-3">
                <div class="small fw-bold text-muted mb-2">Rekening Bank (<?php echo count($bankRows); ?>)</div>
                <div id="bankAccountList">
                    <?php foreach ($bankRows as $b): $bid = (int)$b['id']; $bOn = !empty($b['is_active']); ?>
                    <div class="border rounded-3 p-3 mb-2 bank-acc-row" data-acc-id="<?php echo $bid; ?>">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-muted mb-1" for="acc_bank_<?php echo $bid; ?>">Bank</label>
                                <input type="text" class="form-control bg-light border-0" id="acc_bank_<?php echo $bid; ?>" name="accounts[<?php echo $bid; ?>][bank_name]" value="<?php echo htmlspecialchars($b['bank_name']); ?>" maxlength="50">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-muted mb-1" for="acc_num_<?php echo $bid; ?>">No. Rekening</label>
                                <input type="text" class="form-control bg-light border-0" id="acc_num_<?php echo $bid; ?>" name="accounts[<?php echo $bid; ?>][account_number]" value="<?php echo htmlspecialchars($b['account_number']); ?>" maxlength="50">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-muted mb-1" for="acc_holder_<?php echo $bid; ?>">Atas Nama</label>
                                <input type="text" class="form-control bg-light border-0" id="acc_holder_<?php echo $bid; ?>" name="accounts[<?php echo $bid; ?>][account_holder]" value="<?php echo htmlspecialchars($b['account_holder']); ?>" maxlength="100">
                            </div>
                            <div class="col-md-2">
                                <div class="form-check form-switch">
                                    <input class="form-check-input acc-active" type="checkbox" role="switch" data-acc-toggle id="acc_active_<?php echo $bid; ?>" name="accounts_active[<?php echo $bid; ?>]" value="1"<?php echo $bOn ? ' checked' : ''; ?>>
                                    <label class="form-check-label small <?php echo $bOn ? 'text-primary fw-bold' : 'text-muted'; ?>" data-acc-status for="acc_active_<?php echo $bid; ?>"><?php echo $bOn ? 'Aktif' : 'Nonaktif'; ?></label>
                                </div>
                            </div>
                            <div class="col-md-1 text-end">
                                <button type="button" class="btn btn-sm btn-outline-danger rounded-circle acc-del" title="Hapus rekening" aria-label="Hapus rekening <?php echo htmlspecialchars($b['bank_name']); ?>"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="text-muted small mb-2<?php echo count($bankRows) ? ' d-none' : ''; ?>" data-acc-empty>Belum ada rekening. Klik Tambah Rekening untuk menambah yang pertama.</div>
                <div id="bankAccDeleted"></div>
                <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 mt-2" id="btnAddAccount"><i class="fas fa-plus me-1"></i>Tambah Rekening</button>
            </div>
        </div>
        <?php
    }
}
?>
<style>
.pm-card{border:1px solid #e2e8f0;border-left:3px solid #cbd5e1;}
.pm-card.pm-on{border-color:#e2e8f0;border-left:3px solid #0ea5e9;}
.pm-card .form-check-input{width:2.75em;height:1.4em;cursor:pointer;}
.pm-card .form-check-input:checked{background-color:#0ea5e9;border-color:#0ea5e9;}
.pm-card .form-check-input:focus{border-color:#0ea5e9;box-shadow:0 0 0 .25rem rgba(14,165,233,.25);}
.pm-side{top:1rem;}
.pm-prev-body{min-width:0;}
</style>
<!-- PAYMENT METHODS PAGE -->
<div class="mb-4">
    <h2 class="fw-bold mb-1 text-dark">Kelola Metode Pembayaran</h2>
    <p class="text-muted">Aktif/nonaktif metode di halaman pembayaran. Perubahan langsung berlaku tanpa deploy ulang.</p>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="card-header bg-white py-4 px-4 border-bottom-0">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2 me-3">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <h5 class="fw-bold mb-0 text-dark">Status Metode Pembayaran</h5>
                </div>
            </div>
            <div class="card-body p-4 pt-0">
                <form action="<?php echo $adminBase; ?>/actions/manage_payment_methods.php" method="POST" class="ajax-form">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(mcm_csrf_token()); ?>">
                    <h6 class="fw-bold text-muted small text-uppercase mb-2">Aktif Digunakan (<?php echo count($pmActive) + ($manualOn ? 1 : 0); ?>)</h6>
                    <?php if ($manualOn) pm_manual_card($pmManual, $bankRows); ?>
                    <?php foreach ($pmActive as $m) pm_method_card($m); ?>
                    <?php if (!$manualOn && !count($pmActive)): ?>
                    <div class="text-muted small border rounded-4 p-3 mb-3">Tidak ada metode yang aktif.</div>
                    <?php endif; ?>

                    <h6 class="fw-bold text-muted small text-uppercase mb-2 mt-4">Belum Tersedia (<?php echo count($pmInactive) + (!$manualOn && $pmManual ? 1 : 0); ?>)</h6>
                    <?php if (!$manualOn && $pmManual) pm_manual_card($pmManual, $bankRows); ?>
                    <?php foreach ($pmInactive as $m) pm_method_card($m); ?>
                    <?php if (($manualOn || !$pmManual) && !count($pmInactive)): ?>
                    <div class="text-muted small border rounded-4 p-3 mb-3">Semua metode sudah aktif.</div>
                    <?php endif; ?>
                    <div class="mt-4 pt-3 border-top text-end">
                        <button type="submit" class="btn btn-primary px-5 rounded-pill shadow-sm">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="sticky-lg-top pm-side">
            <div class="card border-0 shadow-sm mb-4 overflow-hidden">
                <div class="card-header bg-white py-3 px-4 border-bottom-0">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-chart-pie text-primary me-2"></i>Ringkasan</h6>
                </div>
                <div class="card-body p-4 pt-0">
                    <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                        <span class="small text-muted">Metode aktif</span>
                        <span class="h4 fw-bold mb-0 text-primary" data-sum-active><?php echo $nSumActive; ?></span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                        <span class="small text-muted">Belum tersedia</span>
                        <span class="h4 fw-bold mb-0 text-muted" data-sum-inactive><?php echo $nSumInactive; ?></span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between py-2">
                        <span class="small text-muted">Rekening terdaftar</span>
                        <span class="h4 fw-bold mb-0 text-dark" data-sum-acc><?php echo count($bankRows); ?></span>
                    </div>
                </div>
            </div>
            <div class="card border-0 shadow-sm mb-4 overflow-hidden">
                <div class="card-header bg-white py-3 px-4 border-bottom-0">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-eye text-primary me-2"></i>Preview Tampilan User</h6>
                </div>
                <div class="card-body p-4 pt-0">
                    <p class="small text-muted mt-0 mb-3">Simulasi metode di halaman pembayaran. Ikut berubah saat toggle digeser.</p>
                    <div id="pmPreview">
                        <?php foreach ($prevList as $p): $pk = $p['key']; $isMan = ($pk === 'bank_transfer');
                            $pOn = $isMan ? $manualOn : in_array($pk, array_column($pmActive, 'method_key'));
                            $pNote = '';
                            foreach ($pmRows as $r) if (($r['method_key'] ?? '') === $pk) $pNote = (string)($r['note'] ?? '');
                            $nAccOn = 0; foreach ($bankRows as $b) if (!empty($b['is_active'])) $nAccOn++;
                            $pSub = $isMan ? ($pOn ? $nAccOn . ' rekening aktif' : '') : ($pOn ? '' : $pNote);
                        ?>
                        <div class="d-flex align-items-center gap-2 border rounded-3 px-2 py-2 mb-2 <?php echo $pOn ? 'bg-white' : 'bg-light'; ?>" data-prev="<?php echo htmlspecialchars($pk); ?>">
                            <i class="fas <?php echo $pOn ? 'fa-check-circle text-primary' : 'fa-lock text-muted'; ?>" data-prev-icon></i>
                            <div class="flex-grow-1 pm-prev-body">
                                <div class="small fw-bold text-dark text-truncate"><?php echo htmlspecialchars($p['name']); ?></div>
                                <div class="small text-muted text-truncate<?php echo $pSub === '' ? ' d-none' : ''; ?>" data-prev-sub><?php echo htmlspecialchars($pSub); ?></div>
                            </div>
                            <span class="badge bg-secondary fw-normal<?php echo $pOn ? ' d-none' : ''; ?>" data-prev-badge><?php echo htmlspecialchars($pNote !== '' ? $pNote : 'Nonaktif'); ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="card border-0 shadow-sm bg-dark text-white">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-info-circle text-warning me-2"></i>
                        <h6 class="fw-bold mb-0">Tips</h6>
                    </div>
                    <ul class="small text-white-50 mb-0 ps-3">
                        <li class="mb-2">Metode nonaktif otomatis terkunci di halaman pembayaran user beserta badge catatannya.</li>
                        <li class="mb-2">Minimal 1 metode aktif agar user bisa checkout.</li>
                        <li>Transfer Bank Manual butuh min. 1 rekening aktif.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
(function(){
    // Toggle utama: aksen card, label status, dan collapse field catatan
    document.querySelectorAll('[data-pm-toggle]').forEach(function(tgl){
        tgl.addEventListener('change', function(){
            var card = tgl.closest('[data-pm-card]');
            var on = tgl.checked;
            if (card) card.classList.toggle('pm-on', on);
            var st = card ? card.querySelector('[data-pm-status]') : null;
            if (st) {
                st.textContent = on ? 'Aktif' : 'Nonaktif';
                st.classList.toggle('text-primary', on);
                st.classList.toggle('fw-bold', on);
                st.classList.toggle('text-muted', !on);
            }
            var note = card ? card.querySelector('[data-pm-note]') : null;
            if (note) note.classList.toggle('d-none', on);
            refreshSide();
        });
    });
    document.querySelectorAll('[data-pm-note] input').forEach(function(inp){
        inp.addEventListener('input', refreshSide);
    });

    // Sidebar kanan: ringkasan angka + preview tampilan user, live ikut toggle
    function refreshSummary(){
        var tgls = document.querySelectorAll('[data-pm-toggle]');
        var nOn = 0;
        tgls.forEach(function(t){ if (t.checked) nOn++; });
        var elOn = document.querySelector('[data-sum-active]');
        if (elOn) elOn.textContent = nOn;
        var elOff = document.querySelector('[data-sum-inactive]');
        if (elOff) elOff.textContent = tgls.length - nOn;
        var elAcc = document.querySelector('[data-sum-acc]');
        var accList = document.getElementById('bankAccountList');
        if (elAcc) elAcc.textContent = accList ? accList.querySelectorAll('.bank-acc-row').length : 0;
    }
    function refreshPreview(){
        var accList = document.getElementById('bankAccountList');
        document.querySelectorAll('[data-prev]').forEach(function(row){
            var key = row.getAttribute('data-prev');
            var tgl = document.getElementById(key === 'bank_transfer' ? 'pm_bank_transfer' : 'pm_' + key);
            var on = tgl ? tgl.checked : false;
            row.classList.toggle('bg-white', on);
            row.classList.toggle('bg-light', !on);
            var icon = row.querySelector('[data-prev-icon]');
            if (icon) icon.className = 'fas ' + (on ? 'fa-check-circle text-primary' : 'fa-lock text-muted');
            var noteInput = document.getElementById('note_' + key);
            var note = noteInput ? noteInput.value.trim() : '';
            var badge = row.querySelector('[data-prev-badge]');
            if (badge) {
                badge.classList.toggle('d-none', on);
                if (!on) badge.textContent = note !== '' ? note : 'Nonaktif';
            }
            var sub = row.querySelector('[data-prev-sub]');
            if (sub) {
                var txt = '';
                if (key === 'bank_transfer' && on) {
                    txt = (accList ? accList.querySelectorAll('.acc-active:checked').length : 0) + ' rekening aktif';
                } else if (key !== 'bank_transfer' && !on) {
                    txt = note;
                }
                sub.textContent = txt;
                sub.classList.toggle('d-none', txt === '');
            }
        });
    }
    function refreshSide(){ refreshSummary(); refreshPreview(); }

    // List rekening Transfer Bank Manual
    var list = document.getElementById('bankAccountList');
    if (!list) { refreshSide(); return; }
    var deletedBox = document.getElementById('bankAccDeleted');
    var warnBox = document.getElementById('bankAccWarn');
    var emptyHint = document.querySelector('[data-acc-empty]');
    var methodToggle = document.getElementById('pm_bank_transfer');
    var addBtn = document.getElementById('btnAddAccount');
    var newIdx = 0;
    function newRowHtml(i){
        return '<div class="border rounded-3 p-3 mb-2 bank-acc-row">'
            + '<div class="row g-2 align-items-end">'
            + '<div class="col-md-3"><label class="form-label small fw-bold text-muted mb-1" for="new_bank_' + i + '">Bank</label>'
            + '<input type="text" class="form-control bg-light border-0" id="new_bank_' + i + '" name="new_accounts[' + i + '][bank_name]" maxlength="50" placeholder="Contoh: Mandiri"></div>'
            + '<div class="col-md-3"><label class="form-label small fw-bold text-muted mb-1" for="new_num_' + i + '">No. Rekening</label>'
            + '<input type="text" class="form-control bg-light border-0" id="new_num_' + i + '" name="new_accounts[' + i + '][account_number]" maxlength="50" placeholder="Contoh: 8900101010"></div>'
            + '<div class="col-md-3"><label class="form-label small fw-bold text-muted mb-1" for="new_holder_' + i + '">Atas Nama</label>'
            + '<input type="text" class="form-control bg-light border-0" id="new_holder_' + i + '" name="new_accounts[' + i + '][account_holder]" maxlength="100" placeholder="Nama pemilik rekening"></div>'
            + '<div class="col-md-2"><div class="form-check form-switch">'
            + '<input class="form-check-input acc-active" type="checkbox" role="switch" data-acc-toggle id="new_active_' + i + '" name="new_accounts[' + i + '][is_active]" value="1" checked>'
            + '<label class="form-check-label small text-primary fw-bold" data-acc-status for="new_active_' + i + '">Aktif</label></div></div>'
            + '<div class="col-md-1 text-end"><button type="button" class="btn btn-sm btn-outline-danger rounded-circle acc-del" title="Hapus rekening" aria-label="Hapus rekening baru"><i class="fas fa-trash"></i></button></div>'
            + '</div></div>';
    }
    function refreshAccState(){
        var rows = list.querySelectorAll('.bank-acc-row').length;
        if (emptyHint) emptyHint.classList.toggle('d-none', rows > 0);
        var nActive = list.querySelectorAll('.acc-active:checked').length;
        var methodOn = methodToggle ? methodToggle.checked : false;
        warnBox.classList.toggle('d-none', !(methodOn && nActive === 0));
        refreshSide();
    }
    addBtn.addEventListener('click', function(){
        list.insertAdjacentHTML('beforeend', newRowHtml(newIdx++));
        refreshAccState();
    });
    list.addEventListener('click', function(e){
        var del = e.target.closest('.acc-del');
        if (!del) return;
        var row = del.closest('.bank-acc-row');
        var id = row.getAttribute('data-acc-id');
        if (id) {
            var hid = document.createElement('input');
            hid.type = 'hidden';
            hid.name = 'delete_account_ids[]';
            hid.value = id;
            deletedBox.appendChild(hid);
        }
        row.remove();
        refreshAccState();
    });
    list.addEventListener('change', function(e){
        var tgl = e.target.closest('[data-acc-toggle]');
        if (tgl) {
            var lbl = tgl.closest('.form-check').querySelector('[data-acc-status]');
            if (lbl) {
                lbl.textContent = tgl.checked ? 'Aktif' : 'Nonaktif';
                lbl.classList.toggle('text-primary', tgl.checked);
                lbl.classList.toggle('fw-bold', tgl.checked);
                lbl.classList.toggle('text-muted', !tgl.checked);
            }
        }
        refreshAccState();
    });
    if (methodToggle) methodToggle.addEventListener('change', refreshAccState);
    refreshAccState();
})();
</script>
