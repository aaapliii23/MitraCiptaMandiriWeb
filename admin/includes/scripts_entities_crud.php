<script>
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

function resetExaminerForm() {
    const form = document.getElementById('examinerForm');
    if (form) form.reset();
    document.getElementById('examinerAction').value = 'create';
    document.getElementById('examinerId').value = '';
    document.getElementById('examinerModalTitle').textContent = 'Tambah Penguji';
}

function editExaminer(data) {
    resetExaminerForm();
    document.getElementById('examinerAction').value = 'update';
    document.getElementById('examinerId').value = data.id;
    document.getElementById('examinerName').value = data.name;
    document.getElementById('examinerSpec').value = data.specialization;
    document.getElementById('examinerBio').value = data.bio || '';
    document.getElementById('examinerCerts').value = data.certifications || '';
    document.getElementById('examinerModalTitle').textContent = 'Edit Penguji';
    new bootstrap.Modal(document.getElementById('examinerModal')).show();
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
</script>
