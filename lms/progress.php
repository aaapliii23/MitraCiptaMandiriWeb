<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Silakan masuk terlebih dahulu.']);
    exit;
}

$payload = json_decode(file_get_contents('php://input'), true);
if (!is_array($payload)) {
    $payload = $_POST;
}

$materialId = (int)($payload['material_id'] ?? 0);
$completed = (int)($payload['completed'] ?? 0);
$token = (string)($payload['csrf_token'] ?? '');

if ($materialId <= 0 || !in_array($completed, [0, 1], true)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Data tidak valid.']);
    exit;
}

if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Sesi tidak valid. Silakan muat ulang halaman.']);
    exit;
}

$userId = (int)$_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("SELECT class_id FROM materials WHERE id = ? LIMIT 1");
    $stmt->execute([$materialId]);
    $classId = $stmt->fetchColumn();
    if (!$classId) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Materi tidak ditemukan.']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT id FROM enrollments WHERE user_id = ? AND class_id = ? LIMIT 1");
    $stmt->execute([$userId, $classId]);
    if (!$stmt->fetch()) {
        http_response_code(403);
        echo json_encode(['status' => 'error', 'message' => 'Anda belum terdaftar di kelas ini.']);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO material_progress (user_id, material_id, completed, completed_at) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE completed = VALUES(completed), completed_at = VALUES(completed_at)");
    $stmt->execute([$userId, $materialId, $completed, $completed ? date('Y-m-d H:i:s') : null]);

    echo json_encode(['status' => 'success', 'message' => $completed ? 'Materi ditandai selesai.' : 'Materi ditandai belum selesai.']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan sistem.']);
}