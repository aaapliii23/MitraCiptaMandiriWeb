<?php if (!$notEnrolled && !$locked): ?>
<script>
    const CSRF_TOKEN = <?php echo json_encode($csrf); ?>;
    const MATERIAL_ID = <?php echo (int)$materialId; ?>;

    const markDone = document.getElementById('markDone');
    if (markDone) {
        markDone.addEventListener('click', function() {
            fetch('progress.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ material_id: MATERIAL_ID, completed: 1, csrf_token: CSRF_TOKEN })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    window.location.reload();
                } else {
                    Swal.fire('Gagal', data.message, 'error');
                }
            })
            .catch(() => Swal.fire('Error', 'Terjadi kesalahan sistem.', 'error'));
        });
    }

    const quizForm = document.getElementById('quizForm');
    if (quizForm) {
        quizForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = quizForm.querySelector('button[type=submit]');
            btn.disabled = true;
            const answers = {};
            new FormData(quizForm).forEach((v, k) => {
                const m = k.match(/^answer\[(\d+)\]$/);
                if (m) answers[m[1]] = v;
            });
            fetch('quiz.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    material_id: MATERIAL_ID,
                    answers: answers,
                    csrf_token: CSRF_TOKEN
                })
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                if (data.status === 'success') {
                    Swal.fire({
                        icon: data.passed ? 'success' : 'warning',
                        title: data.passed ? 'Lulus!' : 'Belum Lulus',
                        html: 'Nilai Anda <b>' + data.correct + '/' + data.total + '</b> (' + data.pct + '%).' + (data.passed ? '' : '<br>Silakan pelajari ulang modul lalu coba lagi.'),
                        confirmButtonText: 'OK'
                    }).then(() => { if (data.passed) window.location.reload(); });
                } else {
                    Swal.fire('Gagal', data.message, 'error');
                }
            })
            .catch(() => { btn.disabled = false; Swal.fire('Error', 'Terjadi kesalahan sistem.', 'error'); });
        });
    }
</script>
<?php endif; ?>
