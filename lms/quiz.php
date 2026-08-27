<?php
require_once '../includes/auth_user.php';
require_once '../config/database.php';

header('Content-Type: application/json');

$userId = (int)$_SESSION['user_id'];
$input = json_decode(file_get_contents('php://input'), true);
$materialId = (int)($input['material_id'] ?? 0);
$csrf = $input['csrf_token'] ?? '';
$answers = is_array($input['answers'] ?? null) ? $input['answers'] : [];
$isRetry = !empty($input['is_retry']);

if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrf)) {
    echo json_encode(['status' => 'error', 'message' => 'Sesi tidak valid. Muat ulang halaman.']);
    exit;
}

if (!$materialId) {
    echo json_encode(['status' => 'error', 'message' => 'Materi tidak valid.']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT m.*, c.id AS class_id FROM materials m JOIN classes c ON m.class_id = c.id WHERE m.id = ? LIMIT 1");
    $stmt->execute([$materialId]);
    $material = $stmt->fetch();
    if (!$material) {
        echo json_encode(['status' => 'error', 'message' => 'Materi tidak ditemukan.']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT * FROM enrollments WHERE user_id = ? AND class_id = ? LIMIT 1");
    $stmt->execute([$userId, $material['class_id']]);
    if (!$stmt->fetch()) {
        echo json_encode(['status' => 'error', 'message' => 'Anda belum terdaftar di kelas ini.']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT m.id, (SELECT mp.completed FROM material_progress mp WHERE mp.material_id = m.id AND mp.user_id = ?) AS is_done FROM materials m WHERE m.class_id = ? ORDER BY m.sort_order ASC, m.id ASC");
    $stmt->execute([$userId, $material['class_id']]);
    $siblings = $stmt->fetchAll();
    $ids = array_column($siblings, 'id');
    $idx = array_search($materialId, $ids);
    if ($idx !== false) {
        for ($i = 0; $i < $idx; $i++) {
            if (!(int)$siblings[$i]['is_done']) {
                echo json_encode(['status' => 'error', 'message' => 'Modul masih terkunci.']);
                exit;
            }
        }
    }

    try { $pdo->exec("ALTER TABLE quiz_questions ADD COLUMN explanation TEXT NULL AFTER essay_answer"); } catch (PDOException $e) {}
    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS `quiz_progress` (
          `id` INT NOT NULL AUTO_INCREMENT,
          `user_id` INT NOT NULL,
          `question_id` INT NOT NULL,
          `is_correct` TINYINT(1) NOT NULL DEFAULT 0,
          `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          UNIQUE KEY `uq_user_question` (`user_id`,`question_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    } catch (PDOException $e) {}
    try { $pdo->exec("CREATE TABLE IF NOT EXISTS `quiz_attempts` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `user_id` int(11) NOT NULL,
      `material_id` int(11) NOT NULL,
      `score` int(11) NOT NULL,
      `total` int(11) NOT NULL,
      `passed` tinyint(1) NOT NULL DEFAULT 0,
      `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      KEY `user_material` (`user_id`, `material_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"); } catch (PDOException $e) {}

    $stmt = $pdo->prepare("SELECT * FROM quiz_questions WHERE material_id = ? ORDER BY sort_order ASC, id ASC");
    $stmt->execute([$materialId]);
    $questions = $stmt->fetchAll();

    if (!$questions) {
        echo json_encode(['status' => 'error', 'message' => 'Modul ini tidak memiliki ujian.']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT passed FROM quiz_attempts WHERE user_id = ? AND material_id = ? ORDER BY id DESC LIMIT 1");
    $stmt->execute([$userId, $materialId]);
    if ((int)$stmt->fetchColumn() === 1) {
        echo json_encode(['status' => 'error', 'message' => 'Anda sudah lulus ujian modul ini.']);
        exit;
    }

    $totalAll = count($questions);
    $qMap = [];
    foreach ($questions as $qq) $qMap[(int)$qq['id']] = $qq;

    $submittedMcq = [];
    $submittedEssay = [];
    foreach ($answers as $qid => $val) {
        $qid = (int)$qid;
        if (!$qid || !isset($qMap[$qid])) continue;
        $q = $qMap[$qid];
        if ($q['question_type'] === 'essay') {
            $submittedEssay[$qid] = trim((string)$val);
        } else {
            $opt = strtolower(trim((string)$val));
            if (in_array($opt, ['a','b','c','d'])) $submittedMcq[$qid] = $opt;
            else $submittedMcq[$qid] = $opt;
        }
    }

    $details = [];
    $upsert = $pdo->prepare("INSERT INTO quiz_progress (user_id, question_id, is_correct) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE is_correct = VALUES(is_correct), updated_at = NOW()");

    foreach ($qMap as $qid => $q) {
        if (!isset($submittedMcq[$qid]) && !array_key_exists($qid, $submittedEssay)) continue;
        $isCorrect = 0;
        $yourAnswerRaw = '';
        $correctAnswer = '';
        $correctText = '';
        $explanation = $q['explanation'] ?? null;

        if ($q['question_type'] === 'essay') {
            $yourAnswerRaw = $submittedEssay[$qid] ?? '';
            $referenceRaw = trim($q['essay_answer'] ?? '');
            $correctAnswer = $referenceRaw;
            $correctText = $referenceRaw;
            $ansLower = mb_strtolower($yourAnswerRaw);
            $refLower = mb_strtolower($referenceRaw);
            if ($ansLower === '' ) {
                $isCorrect = 0;
            } elseif ($refLower === '') {
                $isCorrect = 1;
            } else {
                $keywords = preg_split('/[\s,.;:!?()\-]+/', $refLower);
                $keywords = array_filter($keywords, function($k){ return mb_strlen($k) >= 4; });
                if (empty($keywords)) $isCorrect = 1;
                else {
                    $matched = 0;
                    foreach ($keywords as $k) if (strpos($ansLower, $k) !== false) $matched++;
                    if ($matched >= ceil(count($keywords)/2)) $isCorrect = 1;
                }
            }
        } else {
            $yourAnswerRaw = $submittedMcq[$qid] ?? '';
            $correctAnswer = $q['correct_option'] ?? '';
            $optKey = $correctAnswer;
            if ($optKey && isset($q['option_'.$optKey])) $correctText = $q['option_'.$optKey];
            else $correctText = '';
            if (isset($submittedMcq[$qid]) && $submittedMcq[$qid] === $correctAnswer) $isCorrect = 1;
        }

        $upsert->execute([$userId, $qid, $isCorrect]);

        $details[] = [
            'question_id' => $qid,
            'question' => $q['question'],
            'question_type' => $q['question_type'],
            'is_correct' => (bool)$isCorrect,
            'your_answer' => $yourAnswerRaw,
            'correct_answer' => $correctAnswer,
            'correct_option_text' => $correctText,
            'explanation' => $explanation,
            'your_answer_text' => $q['question_type'] === 'mcq' && $yourAnswerRaw && isset($q['option_'.$yourAnswerRaw]) ? $q['option_'.$yourAnswerRaw] : $yourAnswerRaw,
        ];
    }

    if (empty($details)) {
        echo json_encode(['status' => 'error', 'message' => 'Tidak ada jawaban yang dikirim.']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM quiz_questions WHERE material_id = ? AND id NOT IN (SELECT question_id FROM quiz_progress WHERE user_id = ? AND is_correct = 1)");
    $stmt->execute([$materialId, $userId]);
    $remaining = (int)$stmt->fetchColumn();
    $allCompleted = $remaining === 0;

    $sessionCorrect = 0;
    foreach ($details as $d) if ($d['is_correct']) $sessionCorrect++;

    $passed = $allCompleted ? 1 : 0;
    $nextMaterialId = null;
    if ($idx !== false && $idx < count($ids) - 1) $nextMaterialId = (int)$ids[$idx+1];

    if ($allCompleted) {
        $stmt = $pdo->prepare("INSERT INTO quiz_attempts (user_id, material_id, score, total, passed) VALUES (?, ?, ?, ?, 1)");
        $stmt->execute([$userId, $materialId, $totalAll, $totalAll]);
        $stmt = $pdo->prepare("INSERT INTO material_progress (user_id, material_id, completed, completed_at) VALUES (?, ?, 1, NOW()) ON DUPLICATE KEY UPDATE completed = 1, completed_at = NOW()");
        $stmt->execute([$userId, $materialId]);
    }

    echo json_encode([
        'status' => 'success',
        'details' => $details,
        'all_completed' => $allCompleted,
        'passed' => (bool)$allCompleted,
        'correct' => $sessionCorrect,
        'total' => count($details),
        'total_all' => $totalAll,
        'remaining' => $remaining,
        'pct' => count($details) ? (int)round(($sessionCorrect/count($details))*100) : 0,
        'next_material_id' => $allCompleted ? $nextMaterialId : null
    ]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan sistem.']);
}
