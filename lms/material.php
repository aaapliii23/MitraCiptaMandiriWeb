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
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                        <div class="card-body p-4">
                            <h5 class="fw-bold text-dark mb-1"><i class="fas fa-question-circle text-primary me-2"></i>Ujian Akhir Modul</h5>
                            <p class="text-muted small mb-3">Jawab semua soal di bawah ini. Nilai minimal untuk lulus adalah <b>70%</b>. Anda dapat mengulang ujian jika belum lulus.</p>

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
                                        <?php else: ?>
                                            <p class="text-success small mb-0"><i class="fas fa-check me-1"></i>Kunci jawaban: <?php echo strtoupper($q['correct_option']); ?>.</p>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <?php if ($quizLastAttempt): ?>
                                    <div class="alert alert-danger rounded-3 mb-3">
                                        <i class="fas fa-times-circle me-2"></i><b>Belum lulus.</b>
                                        Nilai terakhir Anda <?php echo (int)$quizLastAttempt['score']; ?>/<?php echo (int)$quizLastAttempt['total']; ?>. Silakan pelajari ulang modul lalu coba lagi.
                                    </div>
                                <?php endif; ?>
                                <form id="quizForm">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf); ?>">
                                    <input type="hidden" name="material_id" value="<?php echo (int)$materialId; ?>">
                                    <?php foreach ($quizQuestions as $q): ?>
                                        <div class="mb-4">
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
                                                        <label class="d-block rounded-3 border px-3 py-2 quiz-option">
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
                        <button class="btn btn-outline-secondary rounded-pill fw-bold px-4 disabled"><i class="fas fa-lock me-2"></i>Lulus ujian untuk lanjut</button>
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
</body>
</html>