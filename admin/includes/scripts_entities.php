<script>
function updateFacilityFilePreview(input) {
    const previewContainer = document.getElementById('facilityFilePreview');
    if (!previewContainer) return;
    if (!input.files || input.files.length === 0) {
        previewContainer.innerHTML = '';
        return;
    }
    const count = input.files.length;
    let html = `<div class="alert alert-info py-2 px-3 mb-0 rounded-3 small">`;
    html += `<div class="fw-bold mb-1"><i class="fas fa-images me-2"></i>Terpilih ${count} foto:</div>`;
    html += `<ul class="mb-0 ps-3" style="max-height: 120px; overflow-y: auto;">`;
    for (let i = 0; i < Math.min(count, 10); i++) {
        html += `<li>${input.files[i].name} (${(input.files[i].size / 1024).toFixed(1)} KB)</li>`;
    }
    if (count > 10) {
        html += `<li><em>...dan ${count - 10} foto lainnya</em></li>`;
    }
    html += `</ul></div>`;
    previewContainer.innerHTML = html;
}

function resetFacilityForm() {
    const form = document.getElementById('facilityForm');
    if (form) form.reset();
    document.getElementById('facilityAction').value = 'create';
    document.getElementById('facilityId').value = '';
    document.getElementById('facilityAdminModalTitle').innerHTML = '<i class="fas fa-building me-2 text-primary"></i>Tambah Foto Lokasi &amp; Fasilitas';
    document.getElementById('facilityImageInput').required = true;
    document.getElementById('facilityImageInput').setAttribute('multiple', 'multiple');
    document.getElementById('facilitySubmitBtn').innerHTML = '<i class="fas fa-cloud-upload-alt me-2"></i>Upload Foto';
    document.getElementById('facilityImageLabel').innerHTML = 'Pilih File Foto <span class="text-danger">*</span>';
    const previewContainer = document.getElementById('facilityFilePreview');
    if (previewContainer) previewContainer.innerHTML = '';
}

function editFacility(data) {
    resetFacilityForm();
    document.getElementById('facilityAction').value = 'update';
    document.getElementById('facilityId').value = data.id;
    const normCat = (data.category||'').toLowerCase().replace(/[^a-z0-9]+/g,'_').replace(/^_+|_+$/g,'');
    document.getElementById('facilityCategory').value = normCat || data.category;
    document.getElementById('facilityTitle').value = data.title;
    document.getElementById('facilityDescription').value = data.description || '';
    document.getElementById('facilityImageInput').required = false;
    document.getElementById('facilityImageInput').removeAttribute('multiple');
    document.getElementById('facilityImageLabel').innerHTML = 'Ganti File Foto (Opsional)';
    document.getElementById('facilityAdminModalTitle').innerHTML = '<i class="fas fa-edit me-2 text-primary"></i>Edit Foto Lokasi';
    document.getElementById('facilitySubmitBtn').innerHTML = '<i class="fas fa-save me-2"></i>Simpan Perubahan';
    new bootstrap.Modal(document.getElementById('facilityAdminModal')).show();
}

function deleteFacilityCategory(id, name) {
    Swal.fire({
        title: 'Hapus Kategori?',
        text: `Apakah Anda yakin ingin menghapus kategori "${name}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append('action', 'delete_category');
            formData.append('id', id);
            const csrfFacCat = document.querySelector('input[name="csrf_token"]')?.value || window.MCM_CSRF_TOKEN || '';
            if (csrfFacCat) formData.append('csrf_token', csrfFacCat);

            fetch('<?php echo $adminBase; ?>/actions/manage_facilities.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.text()).then(t=>{ let d; try{ d=JSON.parse(t);}catch(e){ throw new Error('Respons tidak valid: '+t.slice(0,120)); } return d; })
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({ icon:'success', title:'Terhapus!', text:data.message, timer:1500, showConfirmButton:false }).then(() => {
                        if (typeof window.mcmCloseModalsAndRefresh==='function') window.mcmCloseModalsAndRefresh();
                        else if (typeof loadContent==='function'){ const p=new URLSearchParams(window.location.search).get('page')||'dashboard'; loadContent('?page='+p,false); }
                    });
                } else {
                    Swal.fire('Gagal!', data.message || 'Gagal', 'error');
                }
            })
            .catch(err => Swal.fire('Gagal!', err.message || 'Terjadi kesalahan sistem.', 'error'));
        }
    });
}

function updateGalleryFilePreview(input) {
    const previewContainer = document.getElementById('galleryFilePreview');
    if (!previewContainer) return;
    if (!input.files || input.files.length === 0) {
        previewContainer.innerHTML = '';
        return;
    }
    const count = input.files.length;
    let html = `<div class="alert alert-info py-2 px-3 mb-0 rounded-3 small">`;
    html += `<div class="fw-bold mb-1"><i class="fas fa-images me-2"></i>Terpilih ${count} foto:</div>`;
    html += `<ul class="mb-0 ps-3" style="max-height: 120px; overflow-y: auto;">`;
    for (let i = 0; i < Math.min(count, 10); i++) {
        html += `<li>${input.files[i].name} (${(input.files[i].size / 1024).toFixed(1)} KB)</li>`;
    }
    if (count > 10) {
        html += `<li><em>...dan ${count - 10} foto lainnya</em></li>`;
    }
    html += `</ul></div>`;
    previewContainer.innerHTML = html;
}

function resetGalleryForm() {
    const form = document.getElementById('galleryForm');
    if (form) form.reset();
    document.getElementById('galleryAction').value = 'create';
    document.getElementById('galleryId').value = '';
    document.getElementById('galleryModalTitle').textContent = 'Tambah Foto Galeri';
    document.getElementById('galleryImageInput').required = true;
    document.getElementById('galleryImageInput').setAttribute('multiple', 'multiple');
    document.getElementById('gallerySubmitBtn').textContent = 'Upload Foto';
    document.getElementById('galleryShowHome').checked = false;
    const previewContainer = document.getElementById('galleryFilePreview');
    if (previewContainer) previewContainer.innerHTML = '';
}

function editGallery(data) {
    resetGalleryForm();
    document.getElementById('galleryAction').value = 'update';
    document.getElementById('galleryId').value = data.id;
    document.getElementById('galleryTitle').value = data.title;
    document.getElementById('galleryCategory').value = data.category;
    document.getElementById('galleryImageInput').required = false;
    document.getElementById('galleryImageInput').removeAttribute('multiple');
    document.getElementById('galleryModalTitle').textContent = 'Edit Foto';
    document.getElementById('gallerySubmitBtn').textContent = 'Simpan Perubahan';
    document.getElementById('galleryShowHome').checked = parseInt(data.show_on_home || 0) === 1;
    new bootstrap.Modal(document.getElementById('galleryModal')).show();
}

function resetCategoryForm() {
    const form = document.getElementById('categoryForm');
    if (form) form.reset();
    document.getElementById('categoryAction').value = 'create';
    document.getElementById('categoryId').value = '';
    document.getElementById('categoryModalTitle').textContent = 'Tambah Kategori Baru';
    document.getElementById('categorySubmitBtn').textContent = 'Simpan Kategori';
}

function editCategory(data) {
    resetCategoryForm();
    document.getElementById('categoryAction').value = 'update';
    document.getElementById('categoryId').value = data.id;
    document.getElementById('categoryName').value = data.name;
    document.getElementById('categoryModalTitle').textContent = 'Edit Kategori';
    document.getElementById('categorySubmitBtn').textContent = 'Simpan Perubahan';
    new bootstrap.Modal(document.getElementById('categoryModal')).show();
}

function resetCertTemplateForm() {
    const form = document.getElementById('certTemplateForm');
    if (form) form.reset();
    document.getElementById('certTemplateAction').value = 'create';
    document.getElementById('certTemplateId').value = '';
    document.getElementById('certTemplateColor').value = '#1e40af';
    document.getElementById('certTemplateModalTitle').textContent = 'Tambah Template Sertifikat';
}

function editCertTemplate(data) {
    resetCertTemplateForm();
    document.getElementById('certTemplateAction').value = 'update';
    document.getElementById('certTemplateId').value = data.id;
    document.getElementById('certTemplateName').value = data.name;
    document.getElementById('certTemplateClass').value = data.class_id || '';
    document.getElementById('certTemplateLayout').value = data.layout || 'default';
    if (data.accent_color) document.getElementById('certTemplateColor').value = data.accent_color;
    document.getElementById('certTemplateDefault').checked = parseInt(data.is_default) === 1;
    document.getElementById('certTemplateModalTitle').textContent = 'Edit Template Sertifikat';
    new bootstrap.Modal(document.getElementById('certTemplateModal')).show();
}

function resetAdminForm() {
    const form = document.getElementById('adminForm');
    if (form) form.reset();
    document.getElementById('adminAction').value = 'create';
    document.getElementById('adminId').value = '';
    document.getElementById('adminModalTitle').textContent = 'Tambah Admin Baru';
    document.getElementById('adminPasswordLabel').textContent = 'Password Baru';
    document.getElementById('adminPassword').required = true;
    document.getElementById('adminPasswordHint').style.display = 'none';
    document.getElementById('adminSubmitBtn').textContent = 'Buat Akun';
}

function editAdmin(data) {
    resetAdminForm();
    document.getElementById('adminAction').value = 'update';
    document.getElementById('adminId').value = data.id;
    document.getElementById('adminUsername').value = data.username;
    document.getElementById('adminPassword').required = false;
    document.getElementById('adminPasswordLabel').textContent = 'Password Baru (opsional)';
    document.getElementById('adminPasswordHint').style.display = '';
    document.getElementById('adminModalTitle').textContent = 'Edit Data Admin';
    document.getElementById('adminSubmitBtn').textContent = 'Simpan Perubahan';
    new bootstrap.Modal(document.getElementById('adminModal')).show();
}

function resetTestimonialForm() {
    const form = document.getElementById('testimonialForm');
    if (form) form.reset();
    document.getElementById('testimonialAction').value = 'update';
    document.getElementById('testimonialId').value = '';
    document.getElementById('testimonialModalTitle').textContent = 'Edit Testimoni';
    document.getElementById('testimonialSubmitBtn').textContent = 'Simpan Perubahan';
}

function editTestimonial(data) {
    resetTestimonialForm();
    document.getElementById('testimonialId').value = data.id;
    document.getElementById('testimonialName').value = data.name;
    document.getElementById('testimonialRating').value = data.rating;
    document.getElementById('testimonialReview').value = data.review;
    document.getElementById('testimonialGraduationYear').value = data.graduation_year || '';
    document.getElementById('testimonialJob').value = data.job || '';
    document.getElementById('testimonialStatus').value = data.status;
    document.getElementById('testimonialModalTitle').textContent = 'Edit Testimoni';
    new bootstrap.Modal(document.getElementById('testimonialModal')).show();
}

function setTestimonialStatus(id, action) {
    const adminBase = '<?php echo $adminBase; ?>';
    const formData = new FormData();
    formData.append('action', action);
    formData.append('id', id);
    const csrfTesti = document.querySelector('input[name="csrf_token"]')?.value || window.MCM_CSRF_TOKEN || '';
    if (csrfTesti) formData.append('csrf_token', csrfTesti);
    fetch(adminBase + '/actions/manage_testimonials.php', { method: 'POST', body: formData })
    .then(res => res.text()).then(t=>{ let d; try{ d=JSON.parse(t);}catch(e){ throw new Error('Respons tidak valid: '+t.slice(0,120)); } return d; })
    .then(data => {
        const ok = data.status === 'success';
        Swal.fire(ok ? 'Berhasil!' : 'Gagal', data.message, data.status).then(() => {
            if (ok) {
                if (typeof window.mcmCloseModalsAndRefresh==='function') window.mcmCloseModalsAndRefresh();
                else if (typeof loadContent==='function'){ const p=new URLSearchParams(window.location.search).get('page')||'dashboard'; loadContent('?page='+p,false); }
            }
        });
    }).catch(err=> Swal.fire('Gagal', err.message || 'Terjadi kesalahan', 'error'));
}

function materialTypeChanged() {
    const type = document.getElementById('materialType').value;
    document.getElementById('materialTextWrap').classList.toggle('d-none', type !== 'text');
    document.getElementById('materialVideoWrap').classList.toggle('d-none', type !== 'video');
    document.getElementById('materialPdfWrap').classList.toggle('d-none', type !== 'pdf');
    const text = document.getElementById('materialContent');
    const video = document.getElementById('materialVideo');
    const pdf = document.getElementById('materialPdfUrl');
    text.removeAttribute('name');
    video.removeAttribute('name');
    pdf.removeAttribute('name');
    text.required = false;
    video.required = false;
    if (type === 'text') { text.setAttribute('name', 'content'); text.required = true; }
    else if (type === 'video') video.setAttribute('name', 'content');
    else pdf.setAttribute('name', 'content');
    materialPdfSourceChanged();
}

function materialPdfSourceChanged() {
    const isUpload = document.getElementById('pdfSourceUpload').checked;
    document.getElementById('materialFileWrap').classList.toggle('d-none', !isUpload);
    document.getElementById('materialUrlWrap').classList.toggle('d-none', isUpload);
    document.getElementById('materialFile').disabled = !isUpload || document.getElementById('materialType').value !== 'pdf';
    const pdf = document.getElementById('materialPdfUrl');
    if (document.getElementById('materialType').value === 'pdf') {
        if (isUpload) pdf.removeAttribute('name'); else pdf.setAttribute('name', 'content');
    }
}

function validateMaterialForm() {
    if (document.getElementById('materialType').value !== 'pdf') return true;
    if (document.getElementById('pdfSourceUpload').checked) {
        const file = document.getElementById('materialFile');
        if (document.getElementById('materialAction').value === 'create' && (!file.files || file.files.length === 0)) {
            Swal.fire('Belum ada file', 'Pilih file PDF untuk diunggah, atau ganti Sumber PDF ke URL Eksternal.', 'warning');
            return false;
        }
        document.getElementById('materialPdfUrl').removeAttribute('name');
    } else {
        const url = document.getElementById('materialPdfUrl').value.trim();
        if (!/^https?:\/\/.+\..+/.test(url)) {
            Swal.fire('URL tidak valid', 'Isi URL PDF eksternal (http/https), atau ganti Sumber PDF ke Upload File.', 'warning');
            return false;
        }
        document.getElementById('materialFile').value = '';
    }
    return true;
}

function resetMaterialForm() {
    const form = document.getElementById('materialForm');
    if (form) form.reset();
    document.getElementById('materialAction').value = 'create';
    document.getElementById('materialId').value = '';
    document.getElementById('materialModalTitle').textContent = 'Tambah Materi';
    document.getElementById('materialFileHint').textContent = 'Unggah file PDF (jenis .pdf).';
    materialTypeChanged();
}

function editMaterial(data) {
    resetMaterialForm();
    document.getElementById('materialAction').value = 'update';
    document.getElementById('materialId').value = data.id;
    document.getElementById('materialClass').value = data.class_id;
    document.getElementById('materialTitle').value = data.title;
    document.getElementById('materialSort').value = data.sort_order;
    document.getElementById('materialType').value = data.type;
    materialTypeChanged();
    if (data.type === 'text') {
        document.getElementById('materialContent').value = data.content;
    } else if (data.type === 'video') {
        document.getElementById('materialVideo').value = data.content;
    } else {
        const isFile = data.content && data.content.indexOf('uploads/') === 0;
        document.getElementById('pdfSourceUpload').checked = !!isFile;
        document.getElementById('pdfSourceUrl').checked = !isFile;
        if (isFile) {
            document.getElementById('materialFileHint').textContent = 'File saat ini: ' + data.content.split('/').pop() + ' — biarkan kosong jika tidak ingin mengganti.';
        } else {
            document.getElementById('materialPdfUrl').value = data.content;
        }
        materialPdfSourceChanged();
    }
    document.getElementById('materialModalTitle').textContent = 'Edit Materi';
    new bootstrap.Modal(document.getElementById('materialModal')).show();
}

function resetChatbotIntentForm() {
    const form = document.getElementById('chatbotIntentForm');
    if (form) form.reset();
    document.getElementById('chatbotIntentAction').value = 'create';
    document.getElementById('chatbotIntentId').value = '';
    document.getElementById('chatbotIntentModalTitle').textContent = 'Tambah Intent';
    document.getElementById('chatbotIntentSubmitBtn').textContent = 'Simpan';
    document.getElementById('chatbotIntentEnabled').checked = true;
    new bootstrap.Modal(document.getElementById('chatbotIntentModal')).show();
}

function editChatbotIntent(data) {
    document.getElementById('chatbotIntentAction').value = 'update';
    document.getElementById('chatbotIntentId').value = data.id;
    document.getElementById('chatbotIntentName').value = data.intent;
    document.getElementById('chatbotIntentKeywords').value = data.keywords;
    document.getElementById('chatbotIntentReply').value = data.reply;
    document.getElementById('chatbotIntentEnabled').checked = parseInt(data.enabled) === 1;
    document.getElementById('chatbotIntentModalTitle').textContent = 'Edit Intent';
    document.getElementById('chatbotIntentSubmitBtn').textContent = 'Simpan Perubahan';
    new bootstrap.Modal(document.getElementById('chatbotIntentModal')).show();
}

function resetFinanceForm() {
    const form = document.getElementById('financeForm');
    if (form) form.reset();
    document.getElementById('financeAction').value = 'create';
    document.getElementById('financeId').value = '';
    document.getElementById('financeModalTitle').textContent = 'Tambah Transaksi';
    document.getElementById('financeSubmitBtn').textContent = 'Simpan Transaksi';
    financeTypeChanged('in');
    document.getElementById('financeDate').value = new Date().toISOString().slice(0, 10);
}

function financeTypeChanged(type) {
    const cat = document.getElementById('financeCategory');
    const keep = cat.value;
    const options = type === 'in'
        ? { 'pemasukan_kursus': 'Pemasukan Kursus', 'sewa': 'Sewa / Rental', 'lainnya': 'Lainnya' }
        : { 'sewa': 'Sewa / Rental', 'gaji': 'Gaji', 'operasional': 'Operasional', 'lainnya': 'Lainnya' };
    cat.innerHTML = Object.entries(options).map(([v, l]) => `<option value="${v}">${l}</option>`).join('');
    if (options[keep]) cat.value = keep;
}

function editFinance(data) {
    resetFinanceForm();
    document.getElementById('financeAction').value = 'update';
    document.getElementById('financeId').value = data.id;
    document.getElementById('financeTypeIn').checked = data.type === 'in';
    document.getElementById('financeTypeOut').checked = data.type === 'out';
    financeTypeChanged(data.type);
    document.getElementById('financeCategory').value = data.category;
    document.getElementById('financeAmount').value = data.amount;
    document.getElementById('financeDate').value = data.transaction_date;
    document.getElementById('financeDescription').value = data.description || '';
    document.getElementById('financeModalTitle').textContent = 'Edit Transaksi';
    document.getElementById('financeSubmitBtn').textContent = 'Simpan Perubahan';
    new bootstrap.Modal(document.getElementById('financeModal')).show();
}

function quizTypeChanged(type) {
    const mcq = document.getElementById('quizMcqFields');
    const essay = document.getElementById('quizEssayFields');
    if (mcq) mcq.style.display = type === 'essay' ? 'none' : '';
    if (essay) essay.style.display = type === 'essay' ? '' : 'none';
}

function resetQuizQuestionForm() {
    const form = document.getElementById('quizForm');
    if (form) form.reset();
    const matId = document.getElementById('quizMaterialId') ? document.getElementById('quizMaterialId').value : '';
    const matInput = document.getElementById('quizMaterialInput');
    if (matInput) matInput.value = matId;
    const actionEl = document.getElementById('quizAction');
    if (actionEl) actionEl.value = 'create';
    const idEl = document.getElementById('quizId');
    if (idEl) idEl.value = '';
    const qEl = document.getElementById('quizQuestion');
    if (qEl) qEl.value = '';
    const typeEl = document.getElementById('quizType');
    if (typeEl) typeEl.value = 'mcq';
    quizTypeChanged('mcq');
    const expEl = document.getElementById('quizExplanation');
    if (expEl) expEl.value = '';
    const sortEl = document.getElementById('quizSort');
    if (sortEl) sortEl.value = '0';
    const formTitle = document.getElementById('quizFormTitle');
    if (formTitle) formTitle.textContent = 'Tambah Soal';
    const submitBtn = document.getElementById('quizSubmitBtn');
    if (submitBtn) submitBtn.innerHTML = 'Simpan Soal';
    const resetBtn = document.getElementById('quizResetBtn');
    if (resetBtn) resetBtn.textContent = 'Reset';
    document.querySelectorAll('#quizList .quiz-card-item').forEach(card => {
        card.classList.remove('border-primary', 'bg-primary', 'bg-opacity-10');
    });
}

function editQuizQuestion(dataOrId) {
    let data = dataOrId;
    if (typeof dataOrId === 'number' || typeof dataOrId === 'string') {
        data = (window._quizCache || []).find(x => String(x.id) === String(dataOrId));
    }
    if (!data) return;

    resetQuizQuestionForm();

    const actionEl = document.getElementById('quizAction');
    if (actionEl) actionEl.value = 'update';

    const idEl = document.getElementById('quizId');
    if (idEl) idEl.value = data.id;

    const matId = document.getElementById('quizMaterialId') ? document.getElementById('quizMaterialId').value : (data.material_id || '');
    const matInput = document.getElementById('quizMaterialInput');
    if (matInput) matInput.value = matId;

    const type = data.question_type === 'essay' ? 'essay' : 'mcq';
    const typeEl = document.getElementById('quizType');
    if (typeEl) typeEl.value = type;
    quizTypeChanged(type);

    const qInput = document.getElementById('quizQuestion');
    if (qInput) qInput.value = data.question || '';

    const optA = document.getElementById('quizOptionA');
    if (optA) optA.value = data.option_a || '';
    const optB = document.getElementById('quizOptionB');
    if (optB) optB.value = data.option_b || '';
    const optC = document.getElementById('quizOptionC');
    if (optC) optC.value = data.option_c || '';
    const optD = document.getElementById('quizOptionD');
    if (optD) optD.value = data.option_d || '';

    const cor = document.getElementById('quizCorrect');
    if (cor) cor.value = data.correct_option || 'a';

    const essayAns = document.getElementById('quizEssayAnswer');
    if (essayAns) essayAns.value = data.essay_answer || '';

    const expEl = document.getElementById('quizExplanation');
    if (expEl) expEl.value = data.explanation || '';

    const sortEl = document.getElementById('quizSort');
    if (sortEl) sortEl.value = data.sort_order ?? 0;

    const formTitle = document.getElementById('quizFormTitle');
    if (formTitle) formTitle.innerHTML = '<i class="fas fa-edit me-1 text-primary"></i> Edit Soal (ID: ' + data.id + ')';

    const submitBtn = document.getElementById('quizSubmitBtn');
    if (submitBtn) submitBtn.innerHTML = '<i class="fas fa-save me-1"></i> Simpan Perubahan';

    const resetBtn = document.getElementById('quizResetBtn');
    if (resetBtn) resetBtn.textContent = 'Batal Edit';

    document.querySelectorAll('#quizList .quiz-card-item').forEach(card => {
        card.classList.remove('border-primary', 'bg-primary', 'bg-opacity-10');
    });
    const activeCard = document.getElementById('quizCard_' + data.id);
    if (activeCard) {
        activeCard.classList.add('border-primary', 'bg-primary', 'bg-opacity-10');
    }

    const modalEl = document.getElementById('quizModal');
    const formEl = document.getElementById('quizForm');
    if (modalEl && formEl) {
        modalEl.scrollTo({ top: formEl.offsetTop - 30, behavior: 'smooth' });
    }
    if (formEl) {
        formEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
    setTimeout(() => {
        if (qInput) {
            qInput.focus();
        }
    }, 200);
}

window.editQuizQuestionById = function(id) {
    editQuizQuestion(id);
};

function renderQuizList(questions) {
    window._quizCache = questions || [];
    const container = document.getElementById('quizList');
    document.getElementById('quizCount').textContent = questions.length + ' soal';
    if (!questions.length) {
        container.innerHTML = '<div class="text-center text-muted py-4 small border rounded-3">Belum ada soal. Tambahkan soal pertama di form bawah.</div>';
        return;
    }
    let html = '';
    questions.forEach(q => {
        const isEssay = q.question_type === 'essay';
        html += `<div class="d-flex justify-content-between align-items-start border rounded-3 p-3 mb-2 quiz-card-item" id="quizCard_${q.id}">
            <div>
                <div class="fw-semibold text-dark mb-1">${q.question}</div>
                <small class="text-muted">
                    ${isEssay ? '<i class="fas fa-align-left me-1"></i>Soal Essay' : 'A. ' + (q.option_a || '-') + ' &nbsp; B. ' + (q.option_b || '-') + ' &nbsp; C. ' + (q.option_c || '-') + ' &nbsp; D. ' + (q.option_d || '-')}
                </small>
                <div class="mt-1"><span class="badge ${isEssay ? 'bg-info bg-opacity-10 text-info' : 'bg-success bg-opacity-10 text-success'} small">${isEssay ? 'Essay' : 'Kunci: ' + (q.correct_option || '').toUpperCase()}</span></div>
            </div>
            <div class="d-inline-flex gap-2 flex-shrink-0">
                <button type="button" class="btn btn-action btn-soft-primary btn-edit-soal" data-soal-id="${q.id}" onclick="editQuizQuestion(${q.id})" title="Edit"><i class="fas fa-edit"></i></button>
                <button type="button" class="btn btn-action btn-soft-danger btn-delete-soal" data-soal-id="${q.id}" onclick="deleteQuizQuestion(${q.id})" title="Hapus"><i class="fas fa-trash"></i></button>
            </div>
        </div>`;
    });
    container.innerHTML = html;
}

if (!window._quizEventDelegated) {
    window._quizEventDelegated = true;
    document.addEventListener('click', function(e) {
        const editBtn = e.target.closest('.btn-edit-soal');
        const delBtn = e.target.closest('.btn-delete-soal');
        if (editBtn) {
            e.preventDefault();
            const id = editBtn.dataset.soalId;
            if (id) editQuizQuestion(id);
        } else if (delBtn) {
            e.preventDefault();
            const id = delBtn.dataset.soalId;
            if (id) deleteQuizQuestion(id);
        }
    });
}

function fetchQuizList() {
    const adminBase = '<?php echo $adminBase; ?>';
    const materialId = document.getElementById('quizMaterialId').value;
    fetch(adminBase + '/actions/manage_quiz.php?action=list&material_id=' + materialId)
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            renderQuizList(data.questions);
        } else {
            document.getElementById('quizList').innerHTML = '<div class="text-center text-danger py-4 small">Gagal memuat soal.</div>';
        }
    })
    .catch(() => {
        document.getElementById('quizList').innerHTML = '<div class="text-center text-danger py-4 small">Terjadi kesalahan sistem.</div>';
    });
}

function openQuizManager(materialId, materialTitle) {
    document.getElementById('quizMaterialId').value = materialId;
    document.getElementById('quizMaterialInput').value = materialId;
    document.getElementById('quizModalTitle').textContent = 'Kelola Quiz';
    document.getElementById('quizModalSubtitle').textContent = materialTitle;
    document.getElementById('quizList').innerHTML = '<div class="text-center text-muted py-4 small">Memuat soal...</div>';
    resetQuizQuestionForm();
    new bootstrap.Modal(document.getElementById('quizModal')).show();
    fetchQuizList();
}

function deleteQuizQuestion(id) {
    Swal.fire({
        title: 'Hapus soal ini?',
        text: "Soal yang dihapus tidak dapat dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (!result.isConfirmed) return;
        const adminBase = '<?php echo $adminBase; ?>';
        const fd = new FormData();
        fd.append('action', 'delete');
        fd.append('id', id);
        const csrfDQ = document.querySelector('input[name="csrf_token"]')?.value || window.MCM_CSRF_TOKEN || '';
        if (csrfDQ) fd.append('csrf_token', csrfDQ);
        fetch(adminBase + '/actions/manage_quiz.php', { method: 'POST', body: fd })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                fetchQuizList();
            } else {
                Swal.fire('Gagal!', data.message || 'Gagal menghapus soal.', 'error');
            }
        })
        .catch(() => Swal.fire('Gagal!', 'Terjadi kesalahan sistem.', 'error'));
    });
}

function submitQuizQuestionForm() {
    const form = document.getElementById('quizForm');
    if (!form) return;
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    const adminBase = '<?php echo $adminBase; ?>';
    const submitBtn = document.getElementById('quizSubmitBtn');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';

    fetch(form.getAttribute('action'), {
        method: 'POST',
        body: new FormData(form)
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            resetQuizQuestionForm();
            fetchQuizList();
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message,
                timer: 1500,
                showConfirmButton: false
            });
        } else {
            Swal.fire({ icon: 'error', title: 'Gagal', text: data.message });
        }
    })
    .catch(() => Swal.fire({ icon: 'error', title: 'Terjadi Kesalahan', text: 'Gagal menghubungi server.' }))
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
}

function deleteItem(type, id) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Data yang dihapus tidak dapat dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            let endpoint = '';
            const adminBase = '<?php echo $adminBase; ?>';
            switch(type) {
                case 'classes': endpoint = adminBase + '/actions/manage_classes.php'; break;
                case 'gallery': endpoint = adminBase + '/actions/manage_gallery.php'; break;
                case 'instructors': endpoint = adminBase + '/actions/manage_instructors.php'; break;
                case 'certs': endpoint = adminBase + '/actions/manage_certs.php'; break;
                case 'admins': endpoint = adminBase + '/actions/manage_admins.php'; break;
                case 'orders': endpoint = adminBase + '/actions/manage_orders.php'; break;
                case 'categories': endpoint = adminBase + '/actions/manage_categories.php'; break;
                case 'testimonials': endpoint = adminBase + '/actions/manage_testimonials.php'; break;
                case 'materials': endpoint = adminBase + '/actions/manage_materials.php'; break;
                case 'chatbot': endpoint = adminBase + '/actions/manage_chatbot.php'; break;
                case 'quiz': endpoint = adminBase + '/actions/manage_quiz.php'; break;
                case 'finance': endpoint = adminBase + '/actions/manage_finance.php'; break;
                case 'cert_templates': endpoint = adminBase + '/actions/manage_cert_templates.php'; break;
                case 'facilities': endpoint = adminBase + '/actions/manage_facilities.php'; break;
            }
            
            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('id', id);
            // CSRF untuk delete
            const csrfToken = document.querySelector('input[name="csrf_token"]')?.value || window.MCM_CSRF_TOKEN || '';
            if (csrfToken) formData.append('csrf_token', csrfToken);
            
            fetch(endpoint, { method: 'POST', body: formData })
            .then(res => res.text()).then(t => { let d; try{ d=JSON.parse(t);}catch(e){ throw new Error('Respons tidak valid: '+t.slice(0,120)); } return d; })
            .then(data => {
                if(data.status === 'success') {
                    Swal.fire({ icon:'success', title:'Terhapus!', text:data.message, timer:1500, showConfirmButton:false }).then(() => {
                        if (typeof window.mcmCloseModalsAndRefresh === 'function') window.mcmCloseModalsAndRefresh();
                        else if (typeof loadContent === 'function') { const p=new URLSearchParams(window.location.search).get('page')||'dashboard'; loadContent('?page='+p,false); }
                        else {
                            const btn = document.querySelector('button[onclick*="deleteItem"][onclick*="'+id+'"]');
                            if (btn) { const row = btn.closest('tr') || btn.closest('[data-bulk-card]') || btn.closest('.col-md-4') || btn.closest('.card'); if (row) row.remove(); }
                        }
                    });
                } else {
                    Swal.fire('Gagal!', data.message || 'Gagal menghapus', 'error');
                }
            }).catch(err => Swal.fire('Error', err.message || 'Terjadi kesalahan sistem.', 'error'));
        }
    });
}

// ===== Bulk select & bulk delete (shared) =====
function getBulkEndpoint(type){
    const adminBase = '<?php echo $adminBase; ?>';
    const map = {
        classes: adminBase + '/actions/manage_classes.php',
        gallery: adminBase + '/actions/manage_gallery.php',
        instructors: adminBase + '/actions/manage_instructors.php',
        certs: adminBase + '/actions/manage_certs.php',
        cert_templates: adminBase + '/actions/manage_cert_templates.php',
        admins: adminBase + '/actions/manage_admins.php',
        orders: adminBase + '/actions/manage_orders.php',
        categories: adminBase + '/actions/manage_categories.php',
        testimonials: adminBase + '/actions/manage_testimonials.php',
        materials: adminBase + '/actions/manage_materials.php',
        chatbot: adminBase + '/actions/manage_chatbot.php',
        finance: adminBase + '/actions/manage_finance.php',
        facilities: adminBase + '/actions/manage_facilities.php',
        users: adminBase + '/actions/manage_users.php',
        chat: adminBase + '/actions/manage_chat.php'
    };
    return map[type] || '';
}
    // Optimized bulk — single delegation, no MutationObserver loop
    function initBulkTables(){
        document.querySelectorAll('[data-bulk-table]').forEach(function(wrap){
            if (wrap.dataset.bulkInit) return;
            wrap.dataset.bulkInit = '1';
            const type = wrap.getAttribute('data-bulk-table');
            const endpoint = getBulkEndpoint(type);
            const toolbar = wrap.querySelector('[data-bulk-toolbar]');
            const countEl = wrap.querySelector('[data-bulk-count]');
            const delBtn = wrap.querySelector('[data-bulk-delete]');
            const selectAll = wrap.querySelector('.js-bulk-select-all');
            // cache rows on demand, use offsetParent check (no getComputedStyle reflow)
            const getVisibleRows = () => {
                const all = wrap.querySelectorAll('.js-bulk-row');
                const vis = [];
                for (let i=0;i<all.length;i++){
                    const cb = all[i];
                    const tr = cb.closest('tr');
                    const card = cb.closest('[data-bulk-card]');
                    const el = tr || card || cb;
                    // offsetParent null => hidden (display:none), fast path
                    if (el.style.display === 'none') continue;
                    if (el.offsetParent === null && el.tagName !== 'BODY') {
                        // for <tr> offsetParent is null when hidden, also check card hidden via class
                        if (tr || card) continue;
                    }
                    // also respect explicit hidden via .d-none on card wrapper
                    if (card && card.style.display === 'none') continue;
                    vis.push(cb);
                }
                return vis;
            };
            let rafPending = false;
            function updateToolbar(){
                if (rafPending) return;
                rafPending = true;
                requestAnimationFrame(function(){
                    rafPending = false;
                    const vis = getVisibleRows();
                    let checked = 0;
                    for (let i=0;i<vis.length;i++) if (vis[i].checked) checked++;
                    const n = checked;
                    if (toolbar){
                        const shouldHide = n===0;
                        if (toolbar.classList.contains('d-none') !== shouldHide) toolbar.classList.toggle('d-none', shouldHide);
                        const c2 = toolbar.querySelector('[data-bulk-count-num]');
                        if (c2) c2.textContent = n;
                        if (countEl) countEl.textContent = n + ' dipilih';
                    }
                    if (selectAll){
                        const totalVis = vis.length;
                        if (n===0){ if(selectAll.checked||selectAll.indeterminate){ selectAll.checked=false; selectAll.indeterminate=false; } }
                        else if (n===totalVis){ if(!selectAll.checked||selectAll.indeterminate){ selectAll.checked=true; selectAll.indeterminate=false; } }
                        else { if(selectAll.checked||!selectAll.indeterminate){ selectAll.checked=false; selectAll.indeterminate=true; } }
                    }
                });
            }
            if (selectAll){
                selectAll.addEventListener('change', function(){
                    const vis = getVisibleRows();
                    for (let i=0;i<vis.length;i++) vis[i].checked = selectAll.checked;
                    updateToolbar();
                });
            }
            // delegation: one listener per wrap
            wrap.addEventListener('change', function(e){
                if (e.target.classList.contains('js-bulk-row')) updateToolbar();
            });
            // also update on filter/search inputs inside wrap
            wrap.addEventListener('input', function(e){
                if (e.target.matches('input[type="text"], input[type="search"]')) {
                    // debounce 150ms
                    clearTimeout(wrap._bulkInputTimer);
                    wrap._bulkInputTimer = setTimeout(updateToolbar, 150);
                }
            });
            // filter pills (facilities, chat) trigger display:none via click
            wrap.addEventListener('click', function(e){
                if (e.target.closest('[data-filter], .admin-fac-filter')) {
                    setTimeout(updateToolbar, 50);
                }
            });
            if (delBtn){
                delBtn.addEventListener('click', function(){
                    const vis = getVisibleRows();
                    const checked = [];
                    for (let i=0;i<vis.length;i++) if (vis[i].checked) checked.push(vis[i]);
                    if (!checked.length) return;
                    let sendIds;
                    if (type==='chat') sendIds = checked.map(cb=> cb.value).filter(v=> String(v).trim()!=='');
                    else { sendIds=[]; for(let i=0;i<checked.length;i++){ const v=parseInt(checked[i].value,10); if(v>0) sendIds.push(v); } }
                    if (!sendIds.length) return;
                    Swal.fire({
                        title: 'Hapus '+sendIds.length+' data terpilih?',
                        text: 'Data yang dihapus tidak dapat dikembalikan. Jumlah: '+sendIds.length,
                        icon: 'warning',
                        showCancelButton:true,
                        confirmButtonColor:'#dc2626',
                        cancelButtonColor:'#6b7280',
                        confirmButtonText:'Ya, Hapus '+sendIds.length,
                        cancelButtonText:'Batal'
                    }).then(function(res){
                        if (!res.isConfirmed) return;
                        const fd = new FormData();
                        fd.append('action', type==='chat' ? 'bulk_delete_thread' : 'bulk_delete');
                        if (type==='chat') fd.append('wa_numbers', JSON.stringify(sendIds));
                        else fd.append('ids', JSON.stringify(sendIds));
                        const csrfBulk = document.querySelector('input[name="csrf_token"]')?.value || window.MCM_CSRF_TOKEN || '';
                        if (csrfBulk) fd.append('csrf_token', csrfBulk);
                        const ep = endpoint || getBulkEndpoint(type);
                        if (!ep){ Swal.fire('Gagal','Endpoint tidak ditemukan','error'); return; }
                        delBtn.disabled = true;
                        const orig = delBtn.innerHTML;
                        delBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Menghapus...';
                        fetch(ep, { method:'POST', body: fd })
                        .then(r=>r.text()).then(t=>{ let d; try{ d=JSON.parse(t);}catch(e){ throw new Error('Respons tidak valid: '+t.slice(0,120)); } return d; })
                        .then(d=>{
                            if (d.status==='success'){
                                Swal.fire({ icon:'success', title:'Terhapus!', text:d.message || (sendIds.length+' data berhasil dihapus.'), timer:1500, showConfirmButton:false }).then(()=> {
                                    if (typeof window.mcmCloseModalsAndRefresh==='function') window.mcmCloseModalsAndRefresh();
                                    else if (typeof loadContent==='function'){ const p=new URLSearchParams(window.location.search).get('page')||'dashboard'; loadContent('?page='+p,false); }
                                    else {
                                        // fallback partial: hapus baris terpilih dari DOM
                                        checked.forEach(function(cb){ const row=cb.closest('tr')||cb.closest('[data-bulk-card]'); if(row) row.remove(); });
                                        if (typeof updateToolbar==='function') updateToolbar();
                                    }
                                });
                            } else {
                                Swal.fire('Gagal', d.message || 'Gagal menghapus', 'error');
                            }
                        })
                        .catch(()=> Swal.fire('Error','Terjadi kesalahan sistem.','error'))
                        .finally(()=>{ delBtn.disabled=false; delBtn.innerHTML=orig; });
                    });
                });
            }
            updateToolbar();
            // lightweight observer for table body / card grid childList only (search replaces tbody)
            const bodyEl = wrap.querySelector('tbody, .row.g-4, #adminFacilityGrid, .admin-fac-card');
            const obsTarget = wrap.querySelector('tbody') || wrap.querySelector('.row') || wrap;
            try {
                const obs = new MutationObserver(function(){ clearTimeout(wrap._bulkObsTimer); wrap._bulkObsTimer = setTimeout(updateToolbar, 80); });
                obs.observe(obsTarget, { childList:true, subtree:false });
                wrap._bulkObs = obs;
            } catch(e){}
        });
    }
    // expose debounced global trigger for order search & other dynamic filters
    window.refreshBulkToolbar = function(){
        document.querySelectorAll('[data-bulk-table]').forEach(function(wrap){
            const ev = new Event('change', {bubbles:true});
            wrap.dispatchEvent(ev);
        });
    };
    document.addEventListener('DOMContentLoaded', initBulkTables);
    document.addEventListener('ajaxReload', initBulkTables);
    if (document.readyState !== 'loading') initBulkTables();
    window.initBulkTables = initBulkTables;
    window.getBulkEndpoint = getBulkEndpoint;

// AJAX Form Submission — delegated (fix redirect JSON + partial-render for updateStatus)
document.addEventListener('submit', function(e) {
    const form = e.target;
    if (!(form instanceof HTMLFormElement)) return;
    const isAjaxForm = form.classList.contains('ajax-form');
    const action = form.getAttribute('action') || '';
    const isAdminAction = action.includes('admin/actions/');
    if (!isAjaxForm && !isAdminAction) return;
    if (form.id === 'chatReplyForm' || form.id === 'quizForm') return;
    const onSubmitAttr = form.getAttribute('onsubmit') || '';
    if (onSubmitAttr.includes('submitAjaxForm') || onSubmitAttr.includes('submitQuizQuestionForm')) return;

    // khusus updateStatusForm -> partial-render tanpa reload
    if (form.id === 'updateStatusForm') {
        e.preventDefault();
        const submitBtn = form.querySelector('button[type="submit"]');
        if (!submitBtn) return;
        const orig = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
        const fd = new FormData(form);
        const orderId = fd.get('order_id');
        const newStatus = fd.get('status');
        fetch(form.getAttribute('action'), { method: form.getAttribute('method') || 'POST', body: fd })
        .then(r => r.text()).then(t => { let d; try{ d=JSON.parse(t);}catch(e){ throw new Error('Respons tidak valid: '+t.slice(0,120)); } return d; })
        .then(d => {
            if (d.status === 'success') {
                // tutup modal
                const modalEl = document.getElementById('updateStatusModal');
                if (modalEl) { const m = bootstrap.Modal.getInstance(modalEl); if (m) m.hide(); }
                // update badge di baris yang sesuai (tanpa reload)
                const btn = document.querySelector('button[data-bs-target="#updateStatusModal"][data-id="'+orderId+'"]');
                const row = btn ? btn.closest('tr') : null;
                if (row) {
                    const badgeCell = row.querySelector('td:nth-child(7)');
                    if (badgeCell) {
                        let html = '';
                        if (newStatus === 'pending') html = '<span class="badge badge-soft-warning"><i class="fas fa-clock me-1"></i>Pending</span>';
                        else if (newStatus === 'confirmed') html = '<span class="badge badge-soft-success"><i class="fas fa-check-circle me-1"></i>Lunas</span>';
                        else html = '<span class="badge badge-soft-danger"><i class="fas fa-times-circle me-1"></i>Batal</span>';
                        badgeCell.innerHTML = html;
                        // sync data-status pada tombol edit agar modal berikutnya benar
                        btn.setAttribute('data-status', newStatus);
                    }
                }
                Swal.fire({ icon:'success', title:'Berhasil!', text:d.message, timer:1500, showConfirmButton:false });
            } else {
                Swal.fire('Gagal', d.message || 'Gagal menyimpan', 'error');
            }
        })
        .catch(err => Swal.fire('Error', err.message || 'Terjadi kesalahan sistem.', 'error'))
        .finally(() => { submitBtn.disabled = false; submitBtn.innerHTML = orig; });
        return;
    }

    // generic ajax-form (delegated) — untuk finance, facility, settings, dll.
    e.preventDefault();
    const submitBtn = form.querySelector('button[type="submit"]');
    if (!submitBtn) return;
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';
    fetch(form.getAttribute('action'), { method: form.getAttribute('method') || 'POST', body: new FormData(form) })
    .then(res => res.text()).then(t => { let d; try{ d=JSON.parse(t);}catch(e){ throw new Error('Respons tidak valid: '+t.slice(0,120)); } return d; })
    .then(data => {
        if (data.status === 'success') {
            Swal.fire({ icon:'success', title:'Berhasil!', text:data.message, timer:1500, showConfirmButton:false }).then(() => {
                if (typeof window.mcmCloseModalsAndRefresh==='function') window.mcmCloseModalsAndRefresh();
                else if (typeof loadContent==='function'){ const p=new URLSearchParams(window.location.search).get('page')||'dashboard'; loadContent('?page='+p,false); }
            });
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        } else {
            Swal.fire('Gagal', data.message || 'Gagal', 'error');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    })
    .catch(err => {
        Swal.fire('Error', err.message || 'Terjadi kesalahan sistem.', 'error');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
});
</script>
