<?php
require_once '../includes/auth_user.php';
require_once '../config/database.php';

$userId = (int)$_SESSION['user_id'];
$materialId = (int)($_GET['material_id'] ?? 0);

if (!$materialId) {
    header('Location: dashboard.php');
    exit;
}

function youtubeId($url)
{
    if (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/)([A-Za-z0-9_-]{11})/', $url, $m)) {
        return $m[1];
    }
    return null;
}

try {
    $stmt = $pdo->prepare("SELECT m.*, c.id AS class_id, c.name AS class_name FROM materials m JOIN classes c ON m.class_id = c.id WHERE m.id = ? LIMIT 1");
    $stmt->execute([$materialId]);
    $material = $stmt->fetch();
    if (!$material) {
        header('Location: dashboard.php');
        exit;
    }

    $stmt = $pdo->prepare("SELECT * FROM enrollments WHERE user_id = ? AND class_id = ? LIMIT 1");
    $stmt->execute([$userId, $material['class_id']]);
    if (!$stmt->fetch()) {
        http_response_code(403);
        $notEnrolled = true;
    } else {
        $notEnrolled = false;
        $stmt = $pdo->prepare("SELECT mp.completed FROM material_progress mp WHERE mp.material_id = ? AND mp.user_id = ? LIMIT 1");
        $stmt->execute([$materialId, $userId]);
        $isDone = (bool)(int)$stmt->fetchColumn();

        $stmt = $pdo->prepare("SELECT m.id, (SELECT mp.completed FROM material_progress mp WHERE mp.material_id = m.id AND mp.user_id = ?) AS is_done FROM materials m WHERE m.class_id = ? ORDER BY m.sort_order ASC, m.id ASC");
        $stmt->execute([$userId, $material['class_id']]);
        $siblings = $stmt->fetchAll();
        $ids = array_column($siblings, 'id');
        $idx = array_search($materialId, $ids);
        $prevId = $idx > 0 ? $ids[$idx - 1] : null;
        $nextId = $idx !== false && $idx < count($ids) - 1 ? $ids[$idx + 1] : null;

        $locked = false;
        if ($idx !== false) {
            for ($i = 0; $i < $idx; $i++) {
                if (!(int)$siblings[$i]['is_done']) { $locked = true; break; }
            }
        }

        $quizQuestions = [];
        $quizPassed = false;
        $quizLastAttempt = null;
        try {
            $stmt = $pdo->prepare("SELECT * FROM quiz_questions WHERE material_id = ? ORDER BY sort_order ASC, id ASC");
            $stmt->execute([$materialId]);
            $quizQuestions = $stmt->fetchAll();

            if ($quizQuestions) {
                $stmt = $pdo->prepare("SELECT score, total, passed, DATE_FORMAT(created_at, '%d %M %Y %H:%i') AS created_at FROM quiz_attempts WHERE user_id = ? AND material_id = ? ORDER BY id DESC LIMIT 1");
                $stmt->execute([$userId, $materialId]);
                $quizLastAttempt = $stmt->fetch();
                $quizPassed = $quizLastAttempt && (int)$quizLastAttempt['passed'] === 1;
            }
        } catch (PDOException $e) {
            $quizQuestions = [];
            $quizPassed = false;
            $quizLastAttempt = null;
        }
    }
} catch (PDOException $e) {
    header('Location: dashboard.php');
    exit;
}

$csrf = $_SESSION['csrf_token'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $notEnrolled ? 'Akses Ditolak' : htmlspecialchars($material['title']) . ' - LMS MCM'; ?></title>
    <link rel="icon" type="image/png" href="../assets/img/logo.png">
    <link rel="shortcut icon" type="image/png" href="../assets/img/logo.png">
    <link rel="apple-touch-icon" href="../assets/img/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg fixed-top shadow-sm bg-white" style="transition: all 0.4s ease;">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="../index.php">
                <img src="../assets/img/logo.png" alt="MCM Logo" style="height: 40px;">
                <span class="fw-bold ms-2" style="font-size: 0.9rem; letter-spacing: 1px;">LMS MITRA CIPTA MANDIRI</span>
            </a>
            <div class="d-flex align-items-center gap-2">
                <?php if (!$notEnrolled): ?>
                    <a href="course.php?class_id=<?php echo (int)$material['class_id']; ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3"><i class="fas fa-arrow-left me-1"></i><?php echo htmlspecialchars($material['class_name']); ?></a>
                <?php endif; ?>
                <a href="dashboard.php" class="btn btn-outline-primary btn-sm rounded-pill px-3"><i class="fas fa-tachometer-alt me-1"></i>Dashboard</a>
                <a href="profile.php" class="btn btn-outline-primary btn-sm rounded-pill px-3"><i class="fas fa-user-cog me-1"></i>Profil</a>
                <a href="../auth/user_logout.php" class="btn btn-outline-danger btn-sm rounded-pill px-3"><i class="fas fa-sign-out-alt me-1"></i>Keluar</a>
            </div>
        </div>
    </nav>

    <section class="pt-5" style="margin-top: 56px; min-height: 80vh; background: #f8fafc;">
        <div class="container py-4">
            <?php if ($notEnrolled): ?>
                <div class="card border-0 shadow-sm rounded-4 p-5 text-center">
                    <i class="fas fa-lock fs-1 text-danger opacity-25 mb-3"></i>
                    <h5 class="fw-bold text-dark">Akses Ditolak</h5>
                    <p class="text-muted">Anda belum terdaftar di kelas ini.</p>
                    <div class="mt-2">
                        <a href="../pages/programs.php" class="btn btn-primary rounded-pill fw-bold px-4">Lihat Program</a>
                    </div>
                </div>
            <?php elseif ($locked): ?>
                <div class="card border-0 shadow-sm rounded-4 p-5 text-center">
                    <i class="fas fa-lock fs-1 text-danger opacity-25 mb-3"></i>
                    <h5 class="fw-bold text-dark">Modul Terkunci</h5>
                    <p class="text-muted">Selesaikan modul sebelumnya beserta ujiannya terlebih dahulu untuk membuka modul ini.</p>
                    <div class="mt-2">
                        <a href="course.php?class_id=<?php echo (int)$material['class_id']; ?>" class="btn btn-primary rounded-pill fw-bold px-4"><i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar Modul</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
                    <div>
                        <span class="badge rounded-pill px-3 mb-2
                            <?php echo $material['type'] === 'video' ? 'bg-danger bg-opacity-10 text-danger' : ($material['type'] === 'pdf' ? 'bg-warning bg-opacity-10 text-warning' : 'bg-info bg-opacity-10 text-info'); ?>">
                            <i class="fas <?php echo $material['type'] === 'video' ? 'fa-video' : ($material['type'] === 'pdf' ? 'fa-file-pdf' : 'fa-align-left'); ?> me-1"></i><?php echo strtoupper($material['type']); ?>
                        </span>
                        <h4 class="fw-bold text-dark mb-0"><?php echo htmlspecialchars($material['title']); ?></h4>
                    </div>
                    <?php if ($isDone): ?>
                        <span class="btn btn-outline-success rounded-pill fw-bold px-4 disabled"><i class="fas fa-check-circle me-2"></i>Selesai</span>
                    <?php elseif (empty($quizQuestions)): ?>
                        <button id="markDone" class="btn btn-success rounded-pill fw-bold px-4"><i class="fas fa-check-circle me-2"></i>Tandai Selesai</button>
                    <?php endif; ?>
                </div>

                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                    <div class="card-body p-4">
                        <?php if ($material['type'] === 'video'): ?>
                            <?php $vid = youtubeId($material['content']); ?>
                            <?php if ($vid): ?>
                                <div class="ratio ratio-16x9 rounded-3 overflow-hidden">
                                    <iframe src="https://www.youtube.com/embed/<?php echo htmlspecialchars($vid); ?>" title="<?php echo htmlspecialchars($material['title']); ?>" allowfullscreen allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" frameborder="0"></iframe>
                                </div>
                            <?php else: ?>
                                <a href="<?php echo htmlspecialchars($material['content']); ?>" target="_blank" rel="noopener" class="btn btn-outline-danger rounded-pill fw-bold"><i class="fas fa-external-link-alt me-2"></i>Buka Video</a>
                            <?php endif; ?>
                        <?php elseif ($material['type'] === 'pdf'): ?>
                            <?php
                            $src = $material['content'];
                            if (strpos($src, 'uploads/') === 0) {
                                $src = '../' . $src;
                            }
                            ?>
                            <iframe src="<?php echo htmlspecialchars($src); ?>" style="width: 100%; height: 70vh;" class="rounded-3 border"></iframe>
                        <?php else: ?>
                            <div class="text-dark lh-lg" style="white-space: pre-line;"><?php echo nl2br(htmlspecialchars($material['content'])); ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($quizQuestions): ?>
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4" id="quizCard">
                        <div class="card-body p-4">
                            <h5 class="fw-bold text-dark mb-1"><i class="fas fa-question-circle text-primary me-2"></i>Ujian Akhir Modul</h5>
                            <p class="text-muted small mb-3" id="quizDesc">Jawab semua soal di bawah ini. Anda harus menjawab <b>100% benar</b> untuk lulus. </p>

                            <?php if ($quizPassed): ?>
                                <div class="alert alert-success rounded-3 mb-3">
                                    <i class="fas fa-check-circle me-2"></i><b>Selamat, Anda lulus!</b>
                                    Nilai <?php echo (int)$quizLastAttempt['score']; ?>/<?php echo (int)$quizLastAttempt['total']; ?> pada <?php echo htmlspecialchars($quizLastAttempt['created_at']); ?>.
                                </div>
                                <?php foreach ($quizQuestions as $q): ?>
                                    <div class="mb-3">
                                        <p class="fw-semibold text-dark mb-1"><?php echo htmlspecialchars($q['question']); ?></p>
                                        <?php if ($q['question_type'] === 'essay'): ?>
                                            <p class="text-info small mb-0"><i class="fas fa-align-left me-1"></i>Soal Essay — Referensi jawaban: <?php echo htmlspecialchars($q['essay_answer'] ?? '-'); ?></p>
                                            <?php if (!empty($q['explanation'])): ?><p class="text-muted small mb-0"><i class="fas fa-comment-dots me-1"></i>Pembahasan: <?php echo htmlspecialchars($q['explanation']); ?></p><?php endif; ?>
                                        <?php else: ?>
                                            <p class="text-success small mb-0"><i class="fas fa-check me-1"></i>Kunci jawaban: <?php echo strtoupper($q['correct_option']); ?>. <?php echo htmlspecialchars($q['option_' . $q['correct_option']] ?? ''); ?></p>
                                            <?php if (!empty($q['explanation'])): ?><p class="text-muted small mb-0"><i class="fas fa-comment-dots me-1"></i>Pembahasan: <?php echo htmlspecialchars($q['explanation']); ?></p><?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <?php if ($quizLastAttempt): ?>
                                    <div class="alert alert-warning rounded-3 mb-3">
                                        <i class="fas fa-exclamation-triangle me-2"></i><b>Progres tersimpan.</b> Nilai terakhir <?php echo (int)$quizLastAttempt['score']; ?>/<?php echo (int)$quizLastAttempt['total']; ?>. Lanjutkan kuis sampai 100% benar untuk membuka modul berikutnya.
                                    </div>
                                <?php endif; ?>
                                <div id="quizContainer">
                                    <form id="quizForm">
                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf); ?>">
                                        <input type="hidden" name="material_id" value="<?php echo (int)$materialId; ?>">
                                        <?php foreach ($quizQuestions as $q): ?>
                                            <div class="mb-4 quiz-q" data-qid="<?php echo (int)$q['id']; ?>">
                                                <p class="fw-semibold text-dark mb-2">
                                                    <?php echo htmlspecialchars($q['question']); ?>
                                                    <?php if ($q['question_type'] === 'essay'): ?>
                                                        <span class="badge bg-info bg-opacity-10 text-info ms-1">Essay</span>
                                                    <?php endif; ?>
                                                </p>
                                                <?php if ($q['question_type'] === 'essay'): ?>
                                                    <textarea class="form-control" name="answer[<?php echo (int)$q['id']; ?>]" rows="4" required placeholder="Tulis jawaban Anda..."></textarea>
                                                <?php else: ?>
                                                    <?php foreach (['a' => $q['option_a'], 'b' => $q['option_b'], 'c' => $q['option_c'], 'd' => $q['option_d']] as $key => $opt): ?>
                                                        <div class="form-check ps-0 mb-1">
                                                            <label class="d-block rounded-3 border px-3 py-2 quiz-option" style="cursor:pointer;">
                                                                <input class="form-check-input me-2" type="radio" name="answer[<?php echo (int)$q['id']; ?>]" value="<?php echo $key; ?>" required>
                                                                <span class="fw-semibold"><?php echo strtoupper($key); ?>.</span> <?php echo htmlspecialchars($opt); ?>
                                                            </label>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                        <button type="submit" class="btn btn-primary rounded-pill fw-bold px-4"><i class="fas fa-paper-plane me-2"></i>Kumpulkan Jawaban</button>
                                    </form>
                                    <div id="quizReview" style="display:none;"></div>
                                    <div id="quizRetryBox" class="mt-3" style="display:none;"></div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="d-flex justify-content-between align-items-center">
                    <?php if ($prevId): ?>
                        <a href="material.php?material_id=<?php echo (int)$prevId; ?>" class="btn btn-outline-primary rounded-pill fw-bold px-4"><i class="fas fa-chevron-left me-2"></i>Sebelumnya</a>
                    <?php else: ?>
                        <span></span>
                    <?php endif; ?>
                    <?php if (!$isDone && $quizQuestions): ?>
                        <button class="btn btn-outline-secondary rounded-pill fw-bold px-4 disabled"><i class="fas fa-lock me-2"></i>Lulus ujian 100% untuk lanjut</button>
                    <?php elseif (!$isDone): ?>
                        <button class="btn btn-outline-secondary rounded-pill fw-bold px-4 disabled"><i class="fas fa-lock me-2"></i>Selesaikan modul ini dulu</button>
                    <?php elseif ($nextId): ?>
                        <a href="material.php?material_id=<?php echo (int)$nextId; ?>" class="btn btn-primary rounded-pill fw-bold px-4">Berikutnya<i class="fas fa-chevron-right ms-2"></i></a>
                    <?php else: ?>
                        <a href="course.php?class_id=<?php echo (int)$material['class_id']; ?>" class="btn btn-success rounded-pill fw-bold px-4"><i class="fas fa-check-circle me-2"></i>Kembali ke Daftar Modul</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php if (!$notEnrolled && !$locked): ?>
    <script>
        const CSRF_TOKEN = <?php echo json_encode($csrf); ?>;
        const MATERIAL_ID = <?php echo (int)$materialId; ?>;
        const QUIZ_QUESTIONS = <?php echo json_encode($quizQuestions, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT); ?>;

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
        const quizReview = document.getElementById('quizReview');
        const quizRetryBox = document.getElementById('quizRetryBox');
        const quizContainer = document.getElementById('quizContainer');
        let retryData = null;

        function esc(s){ return String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c])); }

        function shuffleArray(a){
            const arr = a.slice();
            for(let i=arr.length-1;i>0;i--){
                const j=Math.floor(Math.random()*(i+1));
                const tmp=arr[i]; arr[i]=arr[j]; arr[j]=tmp;
            }
            return arr;
        }

        function renderReview(details){
            if(!details || !details.length){ quizReview.innerHTML=''; return; }
            let html = '<div class="mt-2"><h6 class="fw-bold mb-3"><i class="fas fa-comments me-2 text-primary"></i>Pembahasan</h6>';
            details.forEach(d=>{
                const ok = d.is_correct;
                const border = ok ? 'border-success' : 'border-danger';
                const badge = ok ? '<span class="badge bg-success bg-opacity-10 text-success"><i class="fas fa-check me-1"></i>Benar</span>' : (d.question_type==='essay' ? '<span class="badge bg-warning bg-opacity-10 text-warning"><i class="fas fa-exclamation-triangle me-1"></i>Perlu Diperbaiki</span>' : '<span class="badge bg-danger bg-opacity-10 text-danger"><i class="fas fa-times me-1"></i>Salah</span>');
                html += `<div class="card border ${border} rounded-3 mb-3"><div class="card-body p-3">`;
                html += `<div class="d-flex justify-content-between align-items-start gap-2 mb-2"><div class="fw-semibold text-dark" style="flex:1;">${esc(d.question)}</div>${badge}</div>`;
                if(d.question_type === 'essay'){
                    html += `<div class="small mb-1"><span class="text-muted">Jawaban Anda:</span> <span class="text-dark">${esc(d.your_answer || '- belum dijawab -')}</span></div>`;
                    html += `<div class="small mb-1"><span class="text-muted">Jawaban Referensi:</span> <span class="text-dark">${esc(d.correct_answer || '-')}</span></div>`;
                } else {
                    const yourText = d.your_answer_text ? `${d.your_answer.toUpperCase()}. ${esc(d.your_answer_text)}` : (d.your_answer ? d.your_answer.toUpperCase() : '-');
                    const correctText = d.correct_option_text ? `${d.correct_answer.toUpperCase()}. ${esc(d.correct_option_text)}` : d.correct_answer.toUpperCase();
                    html += `<div class="small mb-1"><span class="text-muted">Jawaban Anda:</span> <span class="${ok?'text-success fw-semibold':'text-danger fw-semibold'}">${esc(yourText)}</span></div>`;
                    if(!ok){
                        html += `<div class="small mb-1"><span class="text-muted">Jawaban Benar:</span> <span class="text-success fw-semibold">${esc(correctText)}</span></div>`;
                    }
                }
                if(!ok && d.explanation){
                    html += `<div class="alert alert-light border small mb-0 mt-2 py-2"><i class="fas fa-comment-dots text-primary me-1"></i><b>Pembahasan:</b> ${esc(d.explanation)}</div>`;
                } else if(!ok && !d.explanation && d.question_type==='mcq'){
                    html += ``;
                }
                html += `</div></div>`;
            });
            html += `</div>`;
            quizReview.innerHTML = html;
        }

        function renderRetryForm(wrongDetails){
            const wrongIds = wrongDetails.map(d=>d.question_id);
            let qs = QUIZ_QUESTIONS.filter(q => wrongIds.includes(parseInt(q.id)));
            qs = shuffleArray(qs);
            let html = `<form id="retryForm"><input type="hidden" name="csrf_token" value="${esc(CSRF_TOKEN)}"><input type="hidden" name="material_id" value="${MATERIAL_ID}">`;
            html += `<div class="alert alert-warning rounded-3 py-2 small"><i class="fas fa-redo me-2"></i>Perbaikan Soal — tersisa <b>${qs.length}</b> soal yang perlu diperbaiki. Urutan soal & pilihan diacak.</div>`;
            qs.forEach(q=>{
                const qid = parseInt(q.id);
                html += `<div class="mb-4"><p class="fw-semibold text-dark mb-2">${esc(q.question)}${q.question_type==='essay'? ' <span class="badge bg-info bg-opacity-10 text-info ms-1">Essay</span>':''}</p>`;
                if(q.question_type==='essay'){
                    html += `<textarea class="form-control" name="answer[${qid}]" rows="4" required placeholder="Tulis jawaban Anda..."></textarea>`;
                } else {
                    let opts = [{k:'a', t:q.option_a},{k:'b', t:q.option_b},{k:'c', t:q.option_c},{k:'d', t:q.option_d}];
                    opts = shuffleArray(opts);
                    const labels = ['A','B','C','D'];
                    opts.forEach((o, idx)=>{
                        html += `<div class="form-check ps-0 mb-1"><label class="d-block rounded-3 border px-3 py-2" style="cursor:pointer;"><input class="form-check-input me-2" type="radio" name="answer[${qid}]" value="${o.k}" required> <span class="fw-semibold">${labels[idx]}.</span> ${esc(o.t)}</label></div>`;
                    });
                }
                html += `</div>`;
            });
            html += `<button type="submit" class="btn btn-primary rounded-pill fw-bold px-4"><i class="fas fa-paper-plane me-2"></i>Kumpulkan Perbaikan</button></form>`;
            if(quizForm) quizForm.style.display='none';
            quizReview.style.display='block';
            quizRetryBox.innerHTML = '';
            const retryContainer = document.createElement('div');
            retryContainer.id = 'retryFormContainer';
            retryContainer.innerHTML = html;
            quizContainer.appendChild(retryContainer);
            retryContainer.querySelector('#retryForm').addEventListener('submit', handleRetrySubmit);
        }

        function handleRetrySubmit(e){
            e.preventDefault();
            const form = e.target;
            const btn = form.querySelector('button[type=submit]');
            btn.disabled = true;
            const answers = {};
            new FormData(form).forEach((v,k)=>{
                const m = k.match(/^answer\[(\d+)\]$/);
                if(m) answers[m[1]] = v;
            });
            fetch('quiz.php',{
                method:'POST',
                headers:{'Content-Type':'application/json'},
                body: JSON.stringify({material_id:MATERIAL_ID, answers:answers, csrf_token:CSRF_TOKEN, is_retry:true})
            })
            .then(res=>res.json())
            .then(data=>{
                btn.disabled=false;
                if(data.status==='success'){
                    const existingRetry = document.getElementById('retryFormContainer');
                    if(existingRetry) existingRetry.remove();
                    quizForm.style.display='none';
                    renderReview(data.details);
                    quizReview.style.display='block';
                    if(data.all_completed){
                        quizRetryBox.style.display='none';
                        Swal.fire({icon:'success', title:'Lulus 100%!', html:'Semua soal sudah benar. Modul selesai.', confirmButtonText:'Lanjut'}).then(()=>window.location.reload());
                    } else {
                        const wrong = (data.details||[]).filter(d=>!d.is_correct);
                        quizRetryBox.style.display='block';
                        quizRetryBox.innerHTML = `<button class="btn btn-warning rounded-pill fw-bold px-4" id="retryBtn"><i class="fas fa-redo me-2"></i>Perbaiki Soal yang Salah (${wrong.length} soal)</button>`;
                        document.getElementById('retryBtn').onclick = ()=>{ quizReview.style.display='none'; quizRetryBox.style.display='none'; renderRetryForm(wrong); };
                    }
                } else {
                    Swal.fire('Gagal', data.message, 'error');
                }
            })
            .catch(()=>{ btn.disabled=false; Swal.fire('Error','Terjadi kesalahan sistem.','error'); });
        }

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
                        quizForm.style.display='none';
                        renderReview(data.details);
                        quizReview.style.display='block';
                        if(data.all_completed){
                            quizRetryBox.style.display='none';
                            Swal.fire({icon:'success', title:'Lulus 100%!', html:'Semua soal sudah benar. Modul selesai.', confirmButtonText:'Lanjut'}).then(()=>window.location.reload());
                        } else {
                            const wrong = (data.details||[]).filter(d=>!d.is_correct);
                            if(wrong.length){
                                quizRetryBox.style.display='block';
                                quizRetryBox.innerHTML = `<button class="btn btn-warning rounded-pill fw-bold px-4" id="retryBtn"><i class="fas fa-redo me-2"></i>Perbaiki Soal yang Salah (${wrong.length} soal)</button>`;
                                document.getElementById('retryBtn').onclick = ()=>{ quizReview.style.display='none'; quizRetryBox.style.display='none'; renderRetryForm(wrong); };
                            }
                        }
                    } else {
                        Swal.fire('Gagal', data.message, 'error');
                    }
                })
                .catch(() => { btn.disabled = false; Swal.fire('Error', 'Terjadi kesalahan sistem.', 'error'); });
            });
        }
    </script>
    <?php endif; ?>
</body>
</html>
