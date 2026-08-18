<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['admin_logged_in'])) exit;

require_once '../../includes/db_config.php';

try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS `quiz_questions` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `material_id` int(11) NOT NULL,
      `question` text NOT NULL,
      `option_a` varchar(255) NOT NULL,
      `option_b` varchar(255) NOT NULL,
      `option_c` varchar(255) NOT NULL,
      `option_d` varchar(255) NOT NULL,
      `correct_option` ENUM('a','b','c','d') NOT NULL,
      `sort_order` int(11) NOT NULL DEFAULT 0,
      PRIMARY KEY (`id`),
      KEY `material_id` (`material_id`)
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
    $question = trim($_POST['question'] ?? '');
    $optionA = trim($_POST['option_a'] ?? '');
    $optionB = trim($_POST['option_b'] ?? '');
    $optionC = trim($_POST['option_c'] ?? '');
    $optionD = trim($_POST['option_d'] ?? '');
    $correct = $_POST['correct_option'] ?? '';
    $sortOrder = (int)($_POST['sort_order'] ?? 0);

    if (!$materialId || empty($question) || empty($optionA) || empty($optionB) || empty($optionC) || empty($optionD) || !in_array($correct, ['a', 'b', 'c', 'd'])) {
        echo json_encode(['status' => 'error', 'message' => 'Data soal tidak lengkap.']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT id FROM materials WHERE id = ?");
    $stmt->execute([$materialId]);
    if (!$stmt->fetch()) {
        echo json_encode(['status' => 'error', 'message' => 'Materi tidak ditemukan.']);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO quiz_questions (material_id, question, option_a, option_b, option_c, option_d, correct_option, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$materialId, $question, $optionA, $optionB, $optionC, $optionD, $correct, $sortOrder]);
    echo json_encode(['status' => 'success', 'message' => 'Soal quiz berhasil ditambahkan.']);
} elseif ($action === 'update') {
    $id = (int)($_POST['id'] ?? 0);
    $question = trim($_POST['question'] ?? '');
    $optionA = trim($_POST['option_a'] ?? '');
    $optionB = trim($_POST['option_b'] ?? '');
    $optionC = trim($_POST['option_c'] ?? '');
    $optionD = trim($_POST['option_d'] ?? '');
    $correct = $_POST['correct_option'] ?? '';
    $sortOrder = (int)($_POST['sort_order'] ?? 0);

    if (!$id || empty($question) || empty($optionA) || empty($optionB) || empty($optionC) || empty($optionD) || !in_array($correct, ['a', 'b', 'c', 'd'])) {
        echo json_encode(['status' => 'error', 'message' => 'Data soal tidak lengkap.']);
        exit;
    }

    $stmt = $pdo->prepare("UPDATE quiz_questions SET question = ?, option_a = ?, option_b = ?, option_c = ?, option_d = ?, correct_option = ?, sort_order = ? WHERE id = ?");
    $stmt->execute([$question, $optionA, $optionB, $optionC, $optionD, $correct, $sortOrder, $id]);
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
}