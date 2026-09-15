<!-- Finance Nota Modal -->
<div class="modal fade" id="financeNotaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 1rem;">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                <div>
                    <h6 class="fw-bold text-primary mb-0 small">MITRA CIPTA MANDIRI</h6>
                    <h5 class="modal-title fw-bold">Nota Transaksi</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 pt-2">
                <div class="border rounded-3 p-3 bg-light">
                    <div class="d-flex justify-content-between small mb-2">
                        <span class="text-muted">No. Transaksi</span>
                        <strong id="notaTrxId" class="text-dark">#-</strong>
                    </div>
                    <div class="d-flex justify-content-between small mb-2">
                        <span class="text-muted">Tanggal</span>
                        <strong id="notaTrxDate" class="text-dark">-</strong>
                    </div>
                    <div class="d-flex justify-content-between small mb-2">
                        <span class="text-muted">Keterangan</span>
                        <strong id="notaTrxItem" class="text-dark text-end ms-3">-</strong>
                    </div>
                    <div class="d-flex justify-content-between small mb-2">
                        <span class="text-muted">Kategori</span>
                        <strong id="notaTrxCategory" class="text-dark">-</strong>
                    </div>
                    <div class="d-flex justify-content-between small mb-2">
                        <span class="text-muted">Jenis</span>
                        <span id="notaTrxType">-</span>
                    </div>
                    <div class="d-flex justify-content-between small">
                        <span class="text-muted">Nominal</span>
                        <strong id="notaTrxAmount" class="fs-6">-</strong>
                    </div>
                    <div class="small mt-2 text-muted" id="notaTrxDescWrap" style="display:none;">
                        <hr class="my-2">
                        <span id="notaTrxDesc"></span>
                    </div>
                </div>
                <div class="mt-3" id="notaReceiptWrap">
                    <label class="form-label small fw-bold">Bukti / Lampiran</label>
                    <div id="notaReceiptBody"></div>
                </div>
            </div>
            <div class="modal-footer border-top-0 px-4 pb-4">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                <a id="notaPrintBtn" href="#" target="_blank" class="btn btn-primary rounded-pill px-4 fw-bold"><i class="fas fa-print me-1"></i>Cetak Nota</a>
            </div>
        </div>
    </div>
</div>
<script>
function notaReceiptUrl(path) {
    if (!path) return '';
    if (/^https?:\/\//i.test(path)) return path;
    return '../' + String(path).replace(/^\/+/, '');
}
function showFinanceNota(data) {
    data = data || {};
    document.getElementById('notaTrxId').textContent = '#' + (data.id || '-');
    let dateLabel = data.transaction_date || '-';
    try {
        if (data.transaction_date) {
            dateLabel = new Date(data.transaction_date + 'T00:00:00').toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
        }
    } catch (e) {}
    document.getElementById('notaTrxDate').textContent = dateLabel;
    document.getElementById('notaTrxItem').textContent = data.item_name || data.category || '-';
    document.getElementById('notaTrxCategory').textContent = (data.category || '-').replace(/_/g, ' ').replace(/\b\w/g, function(c){ return c.toUpperCase(); });
    const typeEl = document.getElementById('notaTrxType');
    if (data.type === 'in') {
        typeEl.innerHTML = '<span class="badge bg-success bg-opacity-10 text-success"><i class="fas fa-arrow-down me-1"></i>Masuk</span>';
    } else {
        typeEl.innerHTML = '<span class="badge bg-danger bg-opacity-10 text-danger"><i class="fas fa-arrow-up me-1"></i>Keluar</span>';
    }
    const amountEl = document.getElementById('notaTrxAmount');
    amountEl.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(parseInt(data.amount, 10) || 0);
    amountEl.className = 'fs-6 ' + (data.type === 'in' ? 'text-success' : 'text-danger');
    const descWrap = document.getElementById('notaTrxDescWrap');
    if (data.description) {
        document.getElementById('notaTrxDesc').textContent = data.description;
        descWrap.style.display = '';
    } else {
        descWrap.style.display = 'none';
    }
    const body = document.getElementById('notaReceiptBody');
    body.innerHTML = '';
    const url = notaReceiptUrl(data.receipt_image || '');
    if (!url) {
        body.innerHTML = '<span class="text-muted small">Tidak ada lampiran untuk transaksi ini.</span>';
    } else if (/\.pdf(\?.*)?$/i.test(url)) {
        const a = document.createElement('a');
        a.href = url; a.target = '_blank';
        a.className = 'btn btn-sm btn-outline-primary rounded-pill px-3';
        a.innerHTML = '<i class="fas fa-file-pdf me-1"></i>Buka File Nota (PDF)';
        body.appendChild(a);
    } else {
        const img = document.createElement('img');
        img.src = url; img.alt = 'Bukti transaksi';
        img.className = 'img-fluid rounded border';
        img.style.maxHeight = '220px';
        body.appendChild(img);
    }
    document.getElementById('notaPrintBtn').href = 'finance_nota_print.php?id=' + encodeURIComponent(data.id || '');
    new bootstrap.Modal(document.getElementById('financeNotaModal')).show();
}
</script>
