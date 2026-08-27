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

            fetch('<?php echo $adminBase; ?>/actions/manage_facilities.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire('Terhapus!', data.message, 'success').then(() => window.location.reload());
                } else {
                    Swal.fire('Gagal!', data.message, 'error');
                }
            })
            .catch(() => {
                Swal.fire('Gagal!', 'Terjadi kesalahan sistem.', 'error');
            });
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
    fetch(adminBase + '/actions/manage_testimonials.php', { method: 'POST', body: formData })
    .then(res => res.json())
    .then(data => {
        Swal.fire(data.status === 'success' ? 'Berhasil!' : 'Gagal', data.message, data.status).then(() => {
            if (data.status === 'success') window.location.reload();
        });
    });
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
        if (data.content && data.content.indexOf('uploads/') === 0) {
            document.getElementById('materialFileHint').textContent = 'File saat ini: ' + data.content.split('/').pop() + '. Unggah file baru untuk mengganti.';
        } else {
            document.getElementById('materialPdfUrl').value = data.content;
        }
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
    document.getElementById('quizAction').value = 'create';
    document.getElementById('quizId').value = '';
    document.getElementById('quizQuestion').value = '';
    document.getElementById('quizType').value = 'mcq';
    quizTypeChanged('mcq');
    const expEl = document.getElementById('quizExplanation');
    if (expEl) expEl.value = '';
    document.getElementById('quizFormTitle').textContent = 'Tambah Soal';
    document.getElementById('quizSubmitBtn').textContent = 'Simpan Soal';
}

function editQuizQuestion(data) {
    resetQuizQuestionForm();
    document.getElementById('quizAction').value = 'update';
    document.getElementById('quizId').value = data.id;
    const type = data.question_type === 'essay' ? 'essay' : 'mcq';
    document.getElementById('quizType').value = type;
    quizTypeChanged(type);
    document.getElementById('quizQuestion').value = data.question;
    document.getElementById('quizOptionA').value = data.option_a || '';
    document.getElementById('quizOptionB').value = data.option_b || '';
    document.getElementById('quizOptionC').value = data.option_c || '';
    document.getElementById('quizOptionD').value = data.option_d || '';
    document.getElementById('quizCorrect').value = data.correct_option || 'a';
    document.getElementById('quizEssayAnswer').value = data.essay_answer || '';
    const expEl = document.getElementById('quizExplanation');
    if (expEl) expEl.value = data.explanation || '';
    document.getElementById('quizSort').value = data.sort_order;
    document.getElementById('quizFormTitle').textContent = 'Edit Soal';
    document.getElementById('quizSubmitBtn').textContent = 'Simpan Perubahan';
    document.getElementById('quizForm').scrollIntoView({ behavior: 'smooth', block: 'center' });
}

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
        html += `<div class="d-flex justify-content-between align-items-start border rounded-3 p-3 mb-2">
            <div>
                <div class="fw-semibold text-dark mb-1">${q.question}</div>
                <small class="text-muted">
                    ${isEssay ? '<i class="fas fa-align-left me-1"></i>Soal Essay' : 'A. ' + q.option_a + ' &nbsp; B. ' + q.option_b + ' &nbsp; C. ' + q.option_c + ' &nbsp; D. ' + q.option_d}
                </small>
                <div class="mt-1"><span class="badge ${isEssay ? 'bg-info bg-opacity-10 text-info' : 'bg-success bg-opacity-10 text-success'} small">${isEssay ? 'Essay' : 'Kunci: ' + (q.correct_option || '').toUpperCase()}</span></div>
            </div>
            <div class="d-inline-flex gap-2 flex-shrink-0">
                <button type="button" class="btn btn-action btn-soft-primary btn-edit-soal" data-soal-id="${q.id}" title="Edit"><i class="fas fa-edit"></i></button>
                <button type="button" class="btn btn-action btn-soft-danger btn-delete-soal" data-soal-id="${q.id}" title="Hapus"><i class="fas fa-trash"></i></button>
            </div>
        </div>`;
    });
    container.innerHTML = html;
}

// Event delegation: tombol edit/hapus dirender dinamis via innerHTML,
// listener dipasang SEKALI di modal statis (#quizModal) agar tetap berlaku untuk konten baru.
(function(){
    const modal = document.getElementById('quizModal');
    if (!modal || modal.dataset.soalDelegated) return;
    modal.dataset.soalDelegated = 'true';
    modal.addEventListener('click', function(e) {
        const editBtn = e.target.closest('.btn-edit-soal');
        const delBtn = e.target.closest('.btn-delete-soal');
        if (editBtn) {
            const q = (window._quizCache || []).find(x => String(x.id) === editBtn.dataset.soalId);
            if (q) editQuizQuestion(q);
        } else if (delBtn) {
            deleteQuizQuestion(delBtn.dataset.soalId);
        }
    });
})();

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
            openQuizManager(document.getElementById('quizMaterialId').value, document.getElementById('quizModalSubtitle').textContent);
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
            
            fetch(endpoint, { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success') {
                    Swal.fire('Terhapus!', data.message, 'success').then(() => window.location.reload());
                } else {
                    Swal.fire('Gagal!', data.message, 'error');
                }
            });
        }
    });
}

// AJAX Form Submission
document.querySelectorAll('.ajax-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';

        fetch(this.getAttribute('action'), {
            method: this.getAttribute('method') || 'POST',
            body: new FormData(this)
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                Swal.fire('Berhasil!', data.message, 'success').then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire('Gagal', data.message, 'error');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        })
        .catch(err => {
            Swal.fire('Error', 'Terjadi kesalahan sistem.', 'error');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    });
});
</script>
