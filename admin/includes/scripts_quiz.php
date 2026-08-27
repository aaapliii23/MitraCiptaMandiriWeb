<script>
function resetQuizQuestionForm() {
    const form = document.getElementById('quizForm');
    if (form) form.reset();
    document.getElementById('quizAction').value = 'create';
    document.getElementById('quizId').value = '';
    document.getElementById('quizQuestion').value = '';
    document.getElementById('quizFormTitle').textContent = 'Tambah Soal';
    document.getElementById('quizSubmitBtn').textContent = 'Simpan Soal';
    const expEl = document.getElementById('quizExplanation');
    if (expEl) expEl.value = '';
}

function editQuizQuestion(data) {
    resetQuizQuestionForm();
    document.getElementById('quizAction').value = 'update';
    document.getElementById('quizId').value = data.id;
    document.getElementById('quizQuestion').value = data.question;
    document.getElementById('quizOptionA').value = data.option_a;
    document.getElementById('quizOptionB').value = data.option_b;
    document.getElementById('quizOptionC').value = data.option_c;
    document.getElementById('quizOptionD').value = data.option_d;
    document.getElementById('quizCorrect').value = data.correct_option;
    document.getElementById('quizSort').value = data.sort_order;
    const expEl = document.getElementById('quizExplanation');
    if (expEl) expEl.value = data.explanation || '';
    document.getElementById('quizFormTitle').textContent = 'Edit Soal';
    document.getElementById('quizSubmitBtn').textContent = 'Simpan Perubahan';
    document.getElementById('quizForm').scrollIntoView({ behavior: 'smooth', block: 'center' });
}

function renderQuizList(questions) {
    const container = document.getElementById('quizList');
    document.getElementById('quizCount').textContent = questions.length + ' soal';
    if (!questions.length) {
        container.innerHTML = '<div class="text-center text-muted py-4 small border rounded-3">Belum ada soal. Tambahkan soal pertama di form bawah.</div>';
        return;
    }
    let html = '';
    questions.forEach(q => {
        html += `<div class="d-flex justify-content-between align-items-start border rounded-3 p-3 mb-2">
            <div>
                <div class="fw-semibold text-dark mb-1">${q.question}</div>
                <small class="text-muted">
                    A. ${q.option_a} &nbsp; B. ${q.option_b} &nbsp; C. ${q.option_c} &nbsp; D. ${q.option_d}
                </small>
                <div class="mt-1"><span class="badge bg-success bg-opacity-10 text-success small">Kunci: ${q.correct_option.toUpperCase()}</span></div>
            </div>
            <div class="d-inline-flex gap-2 flex-shrink-0">
                <button class="btn btn-action btn-soft-primary" title="Edit" onclick="editQuizQuestion(${JSON.stringify(q).replace(/</g, '\\u003c')})"><i class="fas fa-edit"></i></button>
                <button class="btn btn-action btn-soft-danger" title="Hapus" onclick="deleteItem('quiz', ${q.id})"><i class="fas fa-trash"></i></button>
            </div>
        </div>`;
    });
    container.innerHTML = html;
}

function openQuizManager(materialId, materialTitle) {
    const adminBase = '<?php echo $adminBase; ?>';
    document.getElementById('quizMaterialId').value = materialId;
    document.getElementById('quizMaterialInput').value = materialId;
    document.getElementById('quizModalTitle').textContent = 'Kelola Quiz';
    document.getElementById('quizModalSubtitle').textContent = materialTitle;
    document.getElementById('quizList').innerHTML = '<div class="text-center text-muted py-4 small">Memuat soal...</div>';
    resetQuizQuestionForm();
    new bootstrap.Modal(document.getElementById('quizModal')).show();
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
</script>
