<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']);
    exit;
}

require_once '../../includes/db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $booking_id = filter_var($_POST['booking_id'] ?? '', FILTER_VALIDATE_INT);
    $booking_date = trim($_POST['booking_date'] ?? '');

    if (!$booking_id || empty($booking_date)) {
        echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("UPDATE bookings SET booking_date = :booking_date WHERE id = :id");
        $stmt->bindParam(':booking_date', $booking_date);
        $stmt->bindParam(':id', $booking_id);
        
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Tanggal berhasil diperbarui.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui data.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan sistem.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Metode request tidak diizinkan.']);
}
