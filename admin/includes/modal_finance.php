<!-- Finance Transaction Modal -->
<div class="modal fade" id="financeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-5 overflow-hidden">
            <div class="modal-header border-0 bg-light px-4 py-3">
                <h5 class="modal-title fw-bold text-dark" id="financeModalTitle">
                    <i class="fas fa-money-bill-wave text-primary me-2"></i>Catat Transaksi Keuangan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="financeForm" class="ajax-form" action="<?php echo $adminBase; ?>/actions/manage_finance.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(mcm_csrf_token()); ?>">
                <input type="hidden" name="action" value="create" id="financeAction">
                <input type="hidden" name="id" id="financeId">

                <div class="modal-body p-4">
                    <!-- Tipe Transaksi Switch -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark mb-2 d-block">Tipe Transaksi <span class="text-danger">*</span></label>
                        <div class="d-flex p-1 bg-light rounded-pill" id="financeTypeToggle">
                            <input type="radio" class="btn-check" name="type" id="financeTypeOut" value="out" checked onchange="toggleFinanceType('out')" autocomplete="off">
                            <label class="btn btn-sm flex-fill rounded-pill fw-bold finance-type-btn active" data-type="out" for="financeTypeOut" style="background: linear-gradient(135deg, #dc2626, #ef4444); color: white; border: none; transition: all 0.25s ease;">
                                <i class="fas fa-arrow-up me-1"></i> Uang Keluar
                            </label>

                            <input type="radio" class="btn-check" name="type" id="financeTypeIn" value="in" onchange="toggleFinanceType('in')" autocomplete="off">
                            <label class="btn btn-sm flex-fill rounded-pill fw-bold finance-type-btn text-muted" data-type="in" for="financeTypeIn" style="background: transparent; border: none; transition: all 0.25s ease;">
                                <i class="fas fa-arrow-down me-1"></i> Uang Masuk
                            </label>
                        </div>
                        <div id="financeTypeInfo" class="small mt-2 px-1" style="color: #64748b;">Pengeluaran untuk pembelian barang, bahan praktek, operasional, atau gaji.</div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Kategori <span class="text-danger">*</span></label>
                            <select class="form-select rounded-4 py-2" name="category" id="financeCategory" required>
                                <!-- Will be dynamically populated by JS based on type -->
                                <option value="pembelian_barang">Pembelian Barang / Alat Praktek</option>
                                <option value="bahan_praktek">Bahan Baku / Habis Pakai</option>
                                <option value="operasional">Operasional (Listrik/Air/Internet/ATK)</option>
                                <option value="gaji">Gaji Instruktur &amp; Staf</option>
                                <option value="sewa_lokasi">Sewa Lokasi / Gedung</option>
                                <option value="pengeluaran_lain">Pengeluaran Lainnya</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Tanggal Transaksi <span class="text-danger">*</span></label>
                            <input type="date" class="form-control rounded-4 py-2" name="transaction_date" id="financeDate" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                    </div>

                    <!-- Nama Barang / Uraian -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark" id="financeItemLabel">Nama Barang / Transaksi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control rounded-4 py-2" name="item_name" id="financeItemName" placeholder="Contoh: Beli 5 Set Kuas Makeup & Eyeshadow" required>
                    </div>

                    <!-- Calculator Box: Jumlah x Harga Satuan = Total -->
                    <div class="card border-0 bg-light rounded-4 p-3 mb-3" id="calcBox">
                        <div class="row g-2 align-items-center">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-dark mb-1">Jumlah / Kuantitas</label>
                                <div class="input-group">
                                    <input type="number" class="form-control rounded-pill" name="quantity" id="financeQuantity" value="1" min="1" oninput="calcFinanceTotal()">
                                    <span class="input-group-text bg-white rounded-pill ms-1 small">Unit</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-dark mb-1">Harga Satuan (Rp)</label>
                                <input type="number" class="form-control rounded-pill" name="unit_price" id="financeUnitPrice" placeholder="0" min="0" oninput="calcFinanceTotal()">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-dark mb-1">Total Nominal (Rp) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control rounded-pill fw-bold text-primary" name="amount" id="financeAmount" placeholder="0" min="1" required>
                            </div>
                        </div>
                        <div class="form-text small mt-2 text-muted">
                            <i class="fas fa-info-circle me-1"></i>Total nominal terisi otomatis saat Anda mengisi kuantitas &amp; harga satuan (atau bisa langsung diketik manual).
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Keterangan / Catatan Tambahan</label>
                            <textarea class="form-control rounded-4 py-2" name="description" id="financeDescription" rows="2" placeholder="Catatan opsional (misal: Toko ABC, No Resi, dll)"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Upload Nota / Bukti Struk (Opsional)</label>
                            <input type="file" class="form-control rounded-4 py-2" name="receipt_image" id="financeReceiptInput" accept="image/*,.pdf">
                            <div class="form-text small">Mendukung format JPG, PNG, WEBP, PDF.</div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 bg-light px-4 py-3">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold" id="financeSubmitBtn">
                        <i class="fas fa-save me-2"></i>Simpan Transaksi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleFinanceType(type) {
    const outLabel = document.querySelector('label[for="financeTypeOut"]');
    const inLabel = document.querySelector('label[for="financeTypeIn"]');
    const info = document.getElementById('financeTypeInfo');
    if (outLabel && inLabel) {
        if (type === 'out') {
            outLabel.style.background = 'linear-gradient(135deg, #dc2626, #ef4444)';
            outLabel.style.color = 'white';
            outLabel.classList.add('active');
            inLabel.style.background = 'transparent';
            inLabel.style.color = '#64748b';
            inLabel.classList.remove('active');
            if (info) info.textContent = 'Pengeluaran untuk pembelian barang, bahan praktek, operasional, atau gaji.';
        } else {
            inLabel.style.background = 'linear-gradient(135deg, #16a34a, #22c55e)';
            inLabel.style.color = 'white';
            inLabel.classList.add('active');
            outLabel.style.background = 'transparent';
            outLabel.style.color = '#64748b';
            outLabel.classList.remove('active');
            if (info) info.textContent = 'Pemasukan dari pembayaran peserta, kerjasama, atau sumber lain.';
        }
    }
    const catSelect = document.getElementById('financeCategory');
    const itemLabel = document.getElementById('financeItemLabel');
    const itemName = document.getElementById('financeItemName');

    if (type === 'in') {
        catSelect.innerHTML = `
            <option value="pemasukan_kursus">Pemasukan Pendaftaran Kursus</option>
            <option value="sewa">Pemasukan Sewa / Rental Studio &amp; Alat</option>
            <option value="pendapatan_jasa">Pendapatan Jasa &amp; Kerjasama</option>
            <option value="pemasukan_lain">Pemasukan Lainnya</option>
        `;
        itemLabel.innerHTML = 'Sumber Pemasukan <span class="text-danger">*</span>';
        itemName.placeholder = 'Contoh: Pembayaran Sewa Studio Rias';
    } else {
        catSelect.innerHTML = `
            <option value="pembelian_barang">Pembelian Barang / Alat Praktek</option>
            <option value="bahan_praktek">Bahan Baku / Habis Pakai</option>
            <option value="operasional">Operasional (Listrik/Air/Internet/ATK)</option>
            <option value="gaji">Gaji Instruktur &amp; Staf</option>
            <option value="sewa_lokasi">Sewa Lokasi / Gedung</option>
            <option value="pengeluaran_lain">Pengeluaran Lainnya</option>
        `;
        itemLabel.innerHTML = 'Nama Barang / Keperluan <span class="text-danger">*</span>';
        itemName.placeholder = 'Contoh: Beli 5 Set Kuas Makeup & Eyeshadow';
    }
}

function calcFinanceTotal() {
    const qty = parseFloat(document.getElementById('financeQuantity').value) || 1;
    const unitPrice = parseFloat(document.getElementById('financeUnitPrice').value) || 0;
    if (unitPrice > 0) {
        document.getElementById('financeAmount').value = Math.round(qty * unitPrice);
    }
}

function resetFinanceForm() {
    const form = document.getElementById('financeForm');
    if (form) form.reset();
    document.getElementById('financeAction').value = 'create';
    document.getElementById('financeId').value = '';
    document.getElementById('financeTypeOut').checked = true;
    toggleFinanceType('out');
    document.getElementById('financeDate').value = new Date().toISOString().split('T')[0];
    document.getElementById('financeQuantity').value = 1;
    document.getElementById('financeUnitPrice').value = '';
    document.getElementById('financeAmount').value = '';
    document.getElementById('financeModalTitle').innerHTML = '<i class="fas fa-money-bill-wave text-primary me-2"></i>Catat Transaksi Keuangan';
    document.getElementById('financeSubmitBtn').innerHTML = '<i class="fas fa-save me-2"></i>Simpan Transaksi';
}

function editFinance(data) {
    resetFinanceForm();
    document.getElementById('financeAction').value = 'update';
    document.getElementById('financeId').value = data.id;
    
    if (data.type === 'in') {
        document.getElementById('financeTypeIn').checked = true;
        toggleFinanceType('in');
    } else {
        document.getElementById('financeTypeOut').checked = true;
        toggleFinanceType('out');
    }
    
    document.getElementById('financeCategory').value = data.category;
    document.getElementById('financeItemName').value = data.item_name || '';
    document.getElementById('financeQuantity').value = data.quantity || 1;
    document.getElementById('financeUnitPrice').value = data.unit_price || '';
    document.getElementById('financeAmount').value = data.amount;
    document.getElementById('financeDate').value = data.transaction_date;
    document.getElementById('financeDescription').value = data.description || '';
    
    document.getElementById('financeModalTitle').innerHTML = '<i class="fas fa-edit text-primary me-2"></i>Edit Transaksi Keuangan';
    document.getElementById('financeSubmitBtn').innerHTML = '<i class="fas fa-save me-2"></i>Simpan Perubahan';
    
    new bootstrap.Modal(document.getElementById('financeModal')).show();
}
</script>
