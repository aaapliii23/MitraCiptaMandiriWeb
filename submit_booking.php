<?php
session_start();
header('Content-Type: application/json');

require_once 'includes/db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Verify CSRF Token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo json_encode(['status' => 'error', 'message' => 'Token keamanan tidak valid. Silakan muat ulang halaman.']);
        exit;
    }

    // 2. Retrieve and Sanitize Inputs
    $name = htmlspecialchars(trim($_POST['name'] ?? ''));
    $whatsapp = htmlspecialchars(trim($_POST['whatsapp'] ?? ''));
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $kelas = htmlspecialchars(trim($_POST['kelas'] ?? ''));

    // Map kelas to service for database
    $service = $kelas;

    // 3. Validation
    if (empty($name) || empty($whatsapp) || empty($email) || empty($kelas)) {
        echo json_encode(['status' => 'error', 'message' => 'Semua kolom wajib diisi.']);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'Format email tidak valid.']);
        exit;
    }

    // WhatsApp format validation (must start with +62)
    if (!preg_match('/^\+62[0-9]{8,13}$/', $whatsapp)) {
        echo json_encode(['status' => 'error', 'message' => 'Nomor WhatsApp harus diawali dengan +62 dan berisi angka yang valid.']);
        exit;
    }

    // 4. Insert into Database
    try {
        $stmt = $pdo->prepare("INSERT INTO bookings (name, whatsapp, email, service) VALUES (:name, :whatsapp, :email, :service)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':whatsapp', $whatsapp);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':service', $service);
        
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Booking berhasil! Tim kami akan segera menghubungi Anda melalui WhatsApp.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan data booking.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan sistem. Silakan coba lagi nanti.']);
        // error_log($e->getMessage()); // In production
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Metode request tidak diizinkan.']);
}
?>
