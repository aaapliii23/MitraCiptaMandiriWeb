<script>
// Mobile Sidebar Toggle
document.getElementById('openSidebar')?.addEventListener('click', () => {
    document.getElementById('sidebar').classList.add('show');
});
document.getElementById('closeSidebar')?.addEventListener('click', () => {
    document.getElementById('sidebar').classList.remove('show');
});

// Setup Modals Data
const updateStatusModal = document.getElementById('updateStatusModal');
if (updateStatusModal) {
    updateStatusModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        document.getElementById('modalOrderId').value = button.getAttribute('data-id');
        document.getElementById('modalParticipantName').textContent = button.getAttribute('data-name');
        document.getElementById('modalOrderStatus').value = button.getAttribute('data-status');
    });
}

const detailPesananModal = document.getElementById('detailPesananModal');
if (detailPesananModal) {
    detailPesananModal.addEventListener('show.bs.modal', function (event) {
        const btn = event.relatedTarget;
        if (!btn) return;
        
        const map = {
            'detailOrderNum': 'data-order',
            'detailName': 'data-name',
            'detailPhone': 'data-phone',
            'detailEmail': 'data-email',
            'detailInstansi': 'data-instansi',
            'detailAlamat': 'data-alamat',
            'detailKelas': 'data-kelas',
            'detailHarga': 'data-harga'
        };

        for (const [id, attr] of Object.entries(map)) {
            const el = document.getElementById(id);
            if (el) el.textContent = btn.getAttribute(attr) || '-';
        }
        // Mode khusus — badge Online/Offline
        const modeEl = document.getElementById('detailMode');
        if (modeEl) {
            const m = (btn.getAttribute('data-mode') || 'offline').toLowerCase();
            if (m === 'online') {
                modeEl.innerHTML = '<i class="fas fa-laptop me-1"></i> Mode Online';
                modeEl.className = 'badge bg-info bg-opacity-10 text-info';
            } else {
                modeEl.innerHTML = '<i class="fas fa-chalkboard-teacher me-1"></i> Mode Offline';
                modeEl.className = 'badge bg-success bg-opacity-10 text-success';
            }
        }
    });
}

function toggleWaLinkVisibility() {
    const ma = document.getElementById('classModeAvailable');
    const wrap = document.getElementById('waGroupLinkWrap');
    const input = document.getElementById('classWaLink');
    const mark = document.getElementById('waLinkRequiredMark');
    if (!ma || !wrap || !input) return;
    const mode = ma.value;
    const needsWa = (mode === 'offline' || mode === 'both');
    wrap.style.display = needsWa ? '' : 'none';
    if (needsWa) {
        input.required = true;
        if (mark) mark.style.display = '';
        input.placeholder = 'https://chat.whatsapp.com/... (wajib jika offline)';
    } else {
        input.required = false;
        if (mark) mark.style.display = 'none';
        input.placeholder = 'https://chat.whatsapp.com/...';
        input.value = '';
    }
}
function resetClassForm() {
    document.getElementById('classForm').reset();
    document.getElementById('classAction').value = 'create';
    document.getElementById('classId').value = '';
    document.getElementById('classModalTitle').textContent = 'Tambah Kelas';
    document.getElementById('classImage').required = true;
    const po = document.getElementById('classPriceOnline');
    const pf = document.getElementById('classPriceOffline');
    const pr = document.getElementById('classPrice');
    const ma = document.getElementById('classModeAvailable');
    const wa = document.getElementById('classWaLink');
    if (po) po.value = '';
    if (pf) pf.value = '';
    if (pr) pr.value = '';
    if (ma) ma.value = 'both';
    if (wa) wa.value = '';
    toggleWaLinkVisibility();
}

function editClass(data) {
    resetClassForm();
    document.getElementById('classAction').value = 'update';
    document.getElementById('classId').value = data.id;
    document.getElementById('className').value = data.name;
    document.getElementById('classStartDate').value = data.start_date || '';
    document.getElementById('classCategory').value = data.category;
    const p = parseInt(data.price) || 0;
    const poVal = data.price_online != null && parseInt(data.price_online) > 0 ? parseInt(data.price_online) : Math.round(p * 0.8);
    const pfVal = data.price_offline != null && parseInt(data.price_offline) > 0 ? parseInt(data.price_offline) : p;
    const elPo = document.getElementById('classPriceOnline');
    const elPf = document.getElementById('classPriceOffline');
    const elPr = document.getElementById('classPrice');
    const elMa = document.getElementById('classModeAvailable');
    const elWa = document.getElementById('classWaLink');
    if (elPo) elPo.value = poVal || '';
    if (elPf) elPf.value = pfVal || '';
    if (elPr) elPr.value = pfVal || p || '';
    if (elMa) elMa.value = data.mode_available || 'both';
    if (elWa) elWa.value = data.whatsapp_group_link || '';
    document.getElementById('classDescription').value = data.description;
    toggleWaLinkVisibility();
    
    // Parse features from JSON array
    try {
        let features = data.features;
        if (typeof features === 'string' && (features.startsWith('[') || features.startsWith('{'))) {
            features = JSON.parse(features);
        }
        document.getElementById('classFeatures').value = Array.isArray(features) ? features.join(', ') : features;
    } catch(e) {
        document.getElementById('classFeatures').value = data.features;
    }
    
    document.getElementById('classImage').required = false;
    document.getElementById('classModalTitle').textContent = 'Edit Paket Pelatihan';
    
    new bootstrap.Modal(document.getElementById('classModal')).show();
}
document.addEventListener('DOMContentLoaded', function(){
    const pf = document.getElementById('classPriceOffline');
    const po = document.getElementById('classPriceOnline');
    const pr = document.getElementById('classPrice');
    if (pf && pr) pf.addEventListener('input', function(){ pr.value = this.value; if (po && !po.value) po.value = Math.round(parseInt(this.value||0)*0.8); });
    const ma = document.getElementById('classModeAvailable');
    if (ma) ma.addEventListener('change', toggleWaLinkVisibility);
    toggleWaLinkVisibility();
});

function editInstructor(data) {
    document.getElementById('instructorForm').reset();
    document.getElementById('instructorAction').value = 'update';
    document.getElementById('instructorId').value = data.id;
    document.getElementById('instructorName').value = data.name;
    document.getElementById('instructorCategory').value = data.category;
    document.getElementById('instructorSpec').value = data.specialization;
    document.getElementById('instructorImage').required = false;
    document.getElementById('instructorModalTitle').textContent = 'Edit Data Instruktur';
    new bootstrap.Modal(document.getElementById('instructorModal')).show();
}

function editCert(data) {
    document.getElementById('certForm').reset();
    document.getElementById('certAction').value = 'update';
    document.getElementById('certId').value = data.id;
    document.getElementById('certTitle').value = data.title;
    document.getElementById('certDesc').value = data.description || '';
    document.getElementById('certImage').required = false;
    document.getElementById('certModalTitle').textContent = 'Edit Dokumen Legal';
    new bootstrap.Modal(document.getElementById('certModal')).show();
}

async function submitAjaxForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return;
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    const submitBtn = form.querySelector('button[type="submit"]') || document.querySelector(`button[onclick="submitAjaxForm('${formId}')"]`);
    const originalBtnText = submitBtn ? submitBtn.innerHTML : '';

    // Special bulk handling for Gallery multi-file creation
    if (formId === 'galleryForm') {
        const actionInput = document.getElementById('galleryAction');
        const fileInput = document.getElementById('galleryImageInput');
        
        if (actionInput && actionInput.value === 'create' && fileInput && fileInput.files && fileInput.files.length > 0) {
            const files = Array.from(fileInput.files);
            const totalFiles = files.length;
            const title = document.getElementById('galleryTitle').value;
            const category = document.getElementById('galleryCategory').value;
            const endpoint = form.getAttribute('action');

            if (submitBtn) {
                submitBtn.disabled = true;
            }

            let successCount = 0;
            let errorMessages = [];

            for (let i = 0; i < totalFiles; i++) {
                if (submitBtn) {
                    submitBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span>Mengupload ${i + 1} dari ${totalFiles}...`;
                }

                const singleFormData = new FormData();
                singleFormData.append('action', 'create');
                singleFormData.append('title', title);
                singleFormData.append('category', category);
                singleFormData.append('show_on_home', document.getElementById('galleryShowHome').checked ? '1' : '0');
                singleFormData.append('images[]', files[i]);

                try {
                    const res = await fetch(endpoint, { method: 'POST', body: singleFormData });
                    const text = await res.text();
                    let data;
                    try {
                        data = JSON.parse(text);
                    } catch(e) {
                        data = { status: 'error', message: 'Respon server tidak valid' };
                    }

                    if (data.status === 'success') {
                        successCount++;
                    } else {
                        errorMessages.push(`Foto #${i + 1} (${files[i].name}): ${data.message || 'Gagal'}`);
                    }
                } catch (err) {
                    errorMessages.push(`Foto #${i + 1} (${files[i].name}): Gagal koneksi`);
                }
            }

            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            }

            if (successCount > 0) {
                let successMsg = `${successCount} dari ${totalFiles} foto berhasil diupload!`;
                if (errorMessages.length > 0) {
                    successMsg += ' (Beberapa gagal: ' + errorMessages.join('; ') + ')';
                }
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: successMsg,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Upload',
                    text: errorMessages.join('\n') || 'Gagal mengupload foto.'
                });
            }
            return;
        }
    }

    // Default single request logic for all other forms
    const formData = new FormData(form);
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
    }

    fetch(form.getAttribute('action'), {
        method: 'POST',
        body: formData
    })
    .then(res => res.text())
    .then(text => {
        let data;
        try {
            data = JSON.parse(text);
        } catch(e) {
            throw new Error('Respons server tidak valid: ' + (text || '(kosong)').slice(0, 150));
        }
        if (data.status === 'success') {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message,
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: data.message
            });
        }
    })
    .catch(err => {
        console.error(err);
        Swal.fire({
            icon: 'error',
            title: 'Terjadi Kesalahan',
            text: err.message || 'Gagal menghubungi server.'
        });
    })
    .finally(() => {
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
        }
    });
}
</script>
