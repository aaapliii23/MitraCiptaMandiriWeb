<?php
require_once '../includes/auth_user.php';
require_once '../includes/db_config.php';

header('Content-Type: application/json');

$userId = (int)$_SESSION['user_id'];
$input = json_decode(file_get_contents('php://input'), true);
$materialId = (int)($input['material_id'] ?? 0);
$csrf = $input['csrf_token'] ?? '';
$answers = is_array($input['answers'] ?? null) ? $input['answers'] : [];

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

    $correct = 0;
    $total = count($questions);
    $submitted = [];
    foreach ($answers as $qid => $opt) {
        $opt = strtolower(trim((string)$opt));
        if (in_array($opt, ['a', 'b', 'c', 'd'])) {
            $submitted[(int)$qid] = $opt;
        }
    }

    foreach ($questions as $q) {
        $qid = (int)$q['id'];
        if (isset($submitted[$qid]) && $submitted[$qid] === $q['correct_option']) {
            $correct++;
        }
    }

    $pct = $total > 0 ? (int)round(($correct / $total) * 100) : 0;
    $passed = $pct >= 70 ? 1 : 0;

    $stmt = $pdo->prepare("INSERT INTO quiz_attempts (user_id, material_id, score, total, passed) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$userId, $materialId, $correct, $total, $passed]);

    if ($passed) {
        $stmt = $pdo->prepare("INSERT INTO material_progress (user_id, material_id, completed, completed_at) VALUES (?, ?, 1, NOW()) ON DUPLICATE KEY UPDATE completed = 1, completed_at = NOW()");
        $stmt->execute([$userId, $materialId]);
    }

    $nextMaterialId = null;
    if ($idx !== false && $idx < count($ids) - 1) {
        $nextMaterialId = (int)$ids[$idx + 1];
    }

    echo json_encode([
        'status' => 'success',
        'correct' => $correct,
        'total' => $total,
        'pct' => $pct,
        'passed' => (bool)$passed,
        'next_material_id' => $nextMaterialId
    ]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan sistem.']);
}