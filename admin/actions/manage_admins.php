<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['admin_logged_in'])) exit;

require_once '../../includes/db_config.php';

$action = $_POST['action'] ?? '';

if ($action === 'create') {
    $user = trim($_POST['username'] ?? '');
    $pass = password_hash($_POST['password'] ?? '', PASSWORD_DEFAULT);

    if (empty($user)) {
        echo json_encode(['status' => 'error', 'message' => 'Username wajib diisi.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
        $stmt->execute([$user, $pass]);
        echo json_encode(['status' => 'success', 'message' => 'Admin baru berhasil ditambahkan.']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Username sudah terdaftar atau terjadi kesalahan.']);
    }
} elseif ($action === 'change_password') {
    $newPass = trim($_POST['new_password'] ?? '');
    if (strlen($newPass) < 6) {
        echo json_encode(['status' => 'error', 'message' => 'Password minimal 6 karakter.']);
        exit;
    }
    $hashed = password_hash($newPass, PASSWORD_DEFAULT);
    $adminUser = $_SESSION['admin_username'] ?? '';
    try {
        $stmt = $pdo->prepare("UPDATE admins SET password = ? WHERE username = ?");
        $stmt->execute([$hashed, $adminUser]);
        echo json_encode(['status' => 'success', 'message' => 'Password berhasil diperbarui.']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal mengubah password.']);
    }
} elseif ($action === 'delete') {
    $id = $_POST['id'] ?? null;
    if ($id) {
        $stmt = $pdo->prepare("DELETE FROM admins WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['status' => 'success', 'message' => 'Akun admin berhasil dihapus.']);
    }
}


