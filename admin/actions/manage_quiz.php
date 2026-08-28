<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['admin_logged_in'])) exit;

require_once '../../config/database.php';

try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS `quiz_questions` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `material_id` int(11) NOT NULL,
      `question_type` ENUM('mcq','essay') NOT NULL DEFAULT 'mcq',
      `question` text NOT NULL,
      `option_a` varchar(255) NOT NULL,
      `option_b` varchar(255) NOT NULL,
      `option_c` varchar(255) NOT NULL,
      `option_d` varchar(255) NOT NULL,
      `correct_option` ENUM('a','b','c','d') DEFAULT NULL,
      `essay_answer` text DEFAULT NULL,
      `explanation` text DEFAULT NULL,
      `sort_order` int(11) NOT NULL DEFAULT 0,
      PRIMARY KEY (`id`),
      KEY `material_id` (`material_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    try {
        $pdo->query("SELECT question_type FROM quiz_questions LIMIT 1");
    } catch (PDOException $e) {
        $pdo->exec("ALTER TABLE quiz_questions ADD COLUMN question_type ENUM('mcq','essay') NOT NULL DEFAULT 'mcq' AFTER material_id");
        $pdo->exec("ALTER TABLE quiz_questions MODIFY correct_option ENUM('a','b','c','d') DEFAULT NULL");
        $pdo->exec("ALTER TABLE quiz_questions ADD COLUMN essay_answer text DEFAULT NULL AFTER correct_option");
    }
    try { $pdo->query("SELECT explanation FROM quiz_questions LIMIT 1"); } catch (PDOException $e) { try { $pdo->exec("ALTER TABLE quiz_questions ADD COLUMN explanation TEXT NULL AFTER essay_answer"); } catch (PDOException $ee) {} }
    $pdo->exec("CREATE TABLE IF NOT EXISTS `quiz_progress` (
      `id` INT NOT NULL AUTO_INCREMENT,
      `user_id` INT NOT NULL,
      `question_id` INT NOT NULL,
      `is_correct` TINYINT(1) NOT NULL DEFAULT 0,
      `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      UNIQUE KEY `uq_user_question` (`user_id`,`question_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    $pdo->exec("CREATE TABLE IF NOT EXISTS `quiz_attempts` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `user_id` int(11) NOT NULL,
      `material_id` int(11) NOT NULL,
      `score` int(11) NOT NULL,
      `total` int(11) NOT NULL,
      `passed` tinyint(1) NOT NULL DEFAULT 0,
      `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      KEY `user_material` (`user_id`, `material_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
} catch (PDOException $e) {}

$action = $_POST['action'] ?? ($_GET['action'] ?? '');

if ($action === 'list') {
    $materialId = (int)($_GET['material_id'] ?? 0);
    if (!$materialId) {
        echo json_encode(['status' => 'error', 'message' => 'Materi tidak valid.']);
        exit;
    }
    $stmt = $pdo->prepare("SELECT q.*, m.title AS material_title FROM quiz_questions q JOIN materials m ON m.id = q.material_id WHERE q.material_id = ? ORDER BY q.sort_order ASC, q.id ASC");
    $stmt->execute([$materialId]);
    echo json_encode(['status' => 'success', 'questions' => $stmt->fetchAll()]);
} elseif ($action === 'create') {
    $materialId = (int)($_POST['material_id'] ?? 0);
    $questionType = ($_POST['question_type'] ?? 'mcq') === 'essay' ? 'essay' : 'mcq';
    $question = trim($_POST['question'] ?? '');
    $optionA = trim($_POST['option_a'] ?? '');
    $optionB = trim($_POST['option_b'] ?? '');
    $optionC = trim($_POST['option_c'] ?? '');
    $optionD = trim($_POST['option_d'] ?? '');
    $correct = $_POST['correct_option'] ?? '';
    $essayAnswer = trim($_POST['essay_answer'] ?? '');
    $explanation = trim($_POST['explanation'] ?? '');
    $sortOrder = (int)($_POST['sort_order'] ?? 0);

    if (!$materialId || empty($question)) {
        echo json_encode(['status' => 'error', 'message' => 'Data soal tidak lengkap.']);
        exit;
    }
    if ($questionType === 'mcq' && (empty($optionA) || empty($optionB) || empty($optionC) || empty($optionD) || !in_array($correct, ['a', 'b', 'c', 'd']))) {
        echo json_encode(['status' => 'error', 'message' => 'Data soal tidak lengkap.']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT id FROM materials WHERE id = ?");
    $stmt->execute([$materialId]);
    if (!$stmt->fetch()) {
        echo json_encode(['status' => 'error', 'message' => 'Materi tidak ditemukan.']);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO quiz_questions (material_id, question_type, question, option_a, option_b, option_c, option_d, correct_option, essay_answer, explanation, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$materialId, $questionType, $question, $optionA, $optionB, $optionC, $optionD, $questionType === 'mcq' ? $correct : null, $questionType === 'essay' ? $essayAnswer : null, $explanation !== '' ? $explanation : null, $sortOrder]);
    echo json_encode(['status' => 'success', 'message' => 'Soal quiz berhasil ditambahkan.']);
} elseif ($action === 'update') {
    $id = (int)($_POST['id'] ?? 0);
    $questionType = ($_POST['question_type'] ?? 'mcq') === 'essay' ? 'essay' : 'mcq';
    $question = trim($_POST['question'] ?? '');
    $optionA = trim($_POST['option_a'] ?? '');
    $optionB = trim($_POST['option_b'] ?? '');
    $optionC = trim($_POST['option_c'] ?? '');
    $optionD = trim($_POST['option_d'] ?? '');
    $correct = $_POST['correct_option'] ?? '';
    $essayAnswer = trim($_POST['essay_answer'] ?? '');
    $explanation = trim($_POST['explanation'] ?? '');
    $sortOrder = (int)($_POST['sort_order'] ?? 0);

    if (!$id || empty($question)) {
        echo json_encode(['status' => 'error', 'message' => 'Data soal tidak lengkap.']);
        exit;
    }
    if ($questionType === 'mcq' && (empty($optionA) || empty($optionB) || empty($optionC) || empty($optionD) || !in_array($correct, ['a', 'b', 'c', 'd']))) {
        echo json_encode(['status' => 'error', 'message' => 'Data soal tidak lengkap.']);
        exit;
    }

    $stmt = $pdo->prepare("UPDATE quiz_questions SET question_type = ?, question = ?, option_a = ?, option_b = ?, option_c = ?, option_d = ?, correct_option = ?, essay_answer = ?, explanation = ?, sort_order = ? WHERE id = ?");
    $stmt->execute([$questionType, $question, $optionA, $optionB, $optionC, $optionD, $questionType === 'mcq' ? $correct : null, $questionType === 'essay' ? $essayAnswer : null, $explanation !== '' ? $explanation : null, $sortOrder, $id]);
    echo json_encode(['status' => 'success', 'message' => 'Soal quiz diperbarui.']);
} elseif ($action === 'delete') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id) {
        $stmt = $pdo->prepare("SELECT material_id FROM quiz_questions WHERE id = ?");
        $stmt->execute([$id]);
        $materialId = $stmt->fetchColumn();
        $pdo->prepare("DELETE FROM quiz_questions WHERE id = ?")->execute([$id]);
        if ($materialId) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM quiz_questions WHERE material_id = ?");
            $stmt->execute([$materialId]);
            if ((int)$stmt->fetchColumn() === 0) {
                $pdo->prepare("DELETE FROM quiz_attempts WHERE material_id = ?")->execute([$materialId]);
            }
        }
        echo json_encode(['status' => 'success', 'message' => 'Soal quiz dihapus.']);
    }
} elseif ($action === 'bulk_delete') {
    $raw = $_POST['ids'] ?? '';
    $ids = [];
    if (is_array($raw)) $ids = $raw;
    elseif (is_string($raw) && $raw !== '') { $d=json_decode($raw,true); $ids=is_array($d)?$d:array_filter(array_map('trim',explode(',',$raw))); }
    $ids = array_values(array_unique(array_filter(array_map('intval',$ids))));
    if (empty($ids)) { echo json_encode(['status'=>'error','message'=>'Tidak ada data terpilih']); exit; }
    if (count($ids)>100) { echo json_encode(['status'=>'error','message'=>'Maksimal 100']); exit; }
    try {
        $ph = implode(',', array_fill(0, count($ids), '?'));
        $stmt=$pdo->prepare("SELECT DISTINCT material_id FROM quiz_questions WHERE id IN ($ph)"); $stmt->execute($ids); $mids=$stmt->fetchAll(PDO::FETCH_COLUMN);
        $del=$pdo->prepare("DELETE FROM quiz_questions WHERE id IN ($ph)"); $del->execute($ids);
        foreach($mids as $mid){ $stmt=$pdo->prepare("SELECT COUNT(*) FROM quiz_questions WHERE material_id=?"); $stmt->execute([$mid]); if((int)$stmt->fetchColumn()===0) $pdo->prepare("DELETE FROM quiz_attempts WHERE material_id=?")->execute([$mid]); }
        echo json_encode(['status'=>'success','message'=> $del->rowCount().' soal dihapus']);
    } catch (PDOException $e) { echo json_encode(['status'=>'error','message'=>'Gagal hapus massal']); }
}