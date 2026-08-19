<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['admin_logged_in'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}
require_once '../../config/database.php';

try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS `finance_transactions` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `type` ENUM('in','out') NOT NULL,
      `category` varchar(50) NOT NULL,
      `description` text DEFAULT NULL,
      `amount` int(11) NOT NULL,
      `transaction_date` date NOT NULL,
      `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
} catch (PDOException $e) {}

$action = $_POST['action'] ?? '';
$allowedCategories = ['pemasukan_kursus', 'sewa', 'gaji', 'operasional', 'lainnya'];

if ($action === 'create' || $action === 'update') {
    $id = $_POST['id'] ?? '';
    $type = $_POST['type'] ?? '';
    $category = trim($_POST['category'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $amount = (int)($_POST['amount'] ?? 0);
    $transaction_date = $_POST['transaction_date'] ?? '';

    if (!in_array($type, ['in', 'out'])) {
        echo json_encode(['status' => 'error', 'message' => 'Tipe transaksi tidak valid']);
        exit;
    }
    if (!in_array($category, $allowedCategories)) {
        echo json_encode(['status' => 'error', 'message' => 'Kategori tidak valid']);
        exit;
    }
    if ($amount <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Nominal harus lebih dari 0']);
        exit;
    }
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $transaction_date)) {
        echo json_encode(['status' => 'error', 'message' => 'Tanggal transaksi tidak valid']);
        exit;
    }

    try {
        if ($action === 'create') {
            $stmt = $pdo->prepare("INSERT INTO finance_transactions (type, category, description, amount, transaction_date) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$type, $category, $description ?: null, $amount, $transaction_date]);
            echo json_encode(['status' => 'success', 'message' => 'Transaksi berhasil disimpan']);
        } else {
            if (empty($id)) {
                echo json_encode(['status' => 'error', 'message' => 'ID tidak valid']);
                exit;
            }
            $stmt = $pdo->prepare("UPDATE finance_transactions SET type = ?, category = ?, description = ?, amount = ?, transaction_date = ? WHERE id = ?");
            $stmt->execute([$type, $category, $description ?: null, $amount, $transaction_date, $id]);
            echo json_encode(['status' => 'success', 'message' => 'Transaksi berhasil diperbarui']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan transaksi']);
    }
    exit;
}

if ($action === 'delete') {
    $id = $_POST['id'] ?? '';
    try {
        $stmt = $pdo->prepare("DELETE FROM finance_transactions WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['status' => 'success', 'message' => 'Transaksi berhasil dihapus']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus transaksi']);
    }
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Aksi tidak dikenali']);
?>
