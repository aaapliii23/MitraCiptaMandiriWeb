<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']);
    exit;
}

require_once '../../config/database.php';
require_once '../../includes/cloudinary.php';

// Auto-create & upgrade table
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS `finance_transactions` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `type` ENUM('in','out') NOT NULL,
      `category` varchar(50) NOT NULL,
      `item_name` varchar(255) DEFAULT NULL,
      `quantity` int(11) NOT NULL DEFAULT 1,
      `unit_price` int(11) NOT NULL DEFAULT 0,
      `amount` int(11) NOT NULL,
      `description` text DEFAULT NULL,
      `receipt_image` varchar(255) DEFAULT NULL,
      `order_id` int(11) DEFAULT NULL,
      `transaction_date` date NOT NULL,
      `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // Check if columns exist, if not add them
    $cols = $pdo->query("SHOW COLUMNS FROM finance_transactions")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('item_name', $cols)) $pdo->exec("ALTER TABLE finance_transactions ADD COLUMN item_name VARCHAR(255) DEFAULT NULL");
    if (!in_array('quantity', $cols)) $pdo->exec("ALTER TABLE finance_transactions ADD COLUMN quantity INT(11) NOT NULL DEFAULT 1");
    if (!in_array('unit_price', $cols)) $pdo->exec("ALTER TABLE finance_transactions ADD COLUMN unit_price INT(11) NOT NULL DEFAULT 0");
    if (!in_array('receipt_image', $cols)) $pdo->exec("ALTER TABLE finance_transactions ADD COLUMN receipt_image VARCHAR(255) DEFAULT NULL");
    if (!in_array('order_id', $cols)) $pdo->exec("ALTER TABLE finance_transactions ADD COLUMN order_id INT(11) DEFAULT NULL");
    if (!in_array('receipt_public_id', $cols)) $pdo->exec("ALTER TABLE finance_transactions ADD COLUMN receipt_public_id VARCHAR(255) DEFAULT NULL AFTER receipt_image");
} catch (PDOException $e) {}

$action = $_POST['action'] ?? '';

// ACTION: SINKRONISASI PEMASUKAN DARI PESANAN LUNAS
if ($action === 'sync_orders') {
    try {
        $stmt = $pdo->query("SELECT o.id, o.order_number, o.customer_name, o.amount, o.created_at, c.name as class_name 
                             FROM orders o 
                             JOIN classes c ON o.class_id = c.id 
                             WHERE o.payment_status = 'paid'");
        $paidOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $insertedCount = 0;
        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM finance_transactions WHERE order_id = ?");
        $insStmt = $pdo->prepare("INSERT INTO finance_transactions (type, category, item_name, quantity, unit_price, amount, description, order_id, transaction_date) 
                                  VALUES ('in', 'pemasukan_kursus', ?, 1, ?, ?, ?, ?, ?)");

        foreach ($paidOrders as $ord) {
            $checkStmt->execute([$ord['id']]);
            if ($checkStmt->fetchColumn() == 0) {
                $itemName = "Pendaftaran " . $ord['class_name'] . " (" . $ord['customer_name'] . ")";
                $desc = "Pemasukan otomatis dari pesanan " . $ord['order_number'];
                $tDate = date('Y-m-d', strtotime($ord['created_at']));
                $insStmt->execute([$itemName, $ord['amount'], $ord['amount'], $desc, $ord['id'], $tDate]);
                $insertedCount++;
            }
        }

        echo json_encode([
            'status' => 'success',
            'message' => $insertedCount > 0 ? "Berhasil menyinkronkan $insertedCount transaksi pesanan lunas ke catatan keuangan." : "Semua pesanan lunas sudah tersinkronisasi ke keuangan."
        ]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyinkronkan pesanan: ' . $e->getMessage()]);
    }
    exit;
}

// ACTION: CREATE / UPDATE TRANSAKSI
if ($action === 'create' || $action === 'update') {
    $id = (int)($_POST['id'] ?? 0);
    $type = $_POST['type'] ?? 'out';
    $category = trim($_POST['category'] ?? 'operasional');
    $item_name = trim($_POST['item_name'] ?? '');
    $quantity = max(1, (int)($_POST['quantity'] ?? 1));
    $unit_price = (int)($_POST['unit_price'] ?? 0);
    $amount = (int)($_POST['amount'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $transaction_date = trim($_POST['transaction_date'] ?? date('Y-m-d'));

    if (!in_array($type, ['in', 'out'])) {
        echo json_encode(['status' => 'error', 'message' => 'Tipe transaksi tidak valid.']);
        exit;
    }

    if (empty($item_name)) {
        $item_name = ($type === 'in' ? 'Pemasukan ' : 'Pengeluaran ') . ucwords(str_replace('_', ' ', $category));
    }

    // Auto-calculate amount if unit_price is provided and amount is not set
    if ($amount <= 0 && $unit_price > 0) {
        $amount = $quantity * $unit_price;
    }

    if ($amount <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Nominal total harus lebih dari 0.']);
        exit;
    }

    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $transaction_date)) {
        $transaction_date = date('Y-m-d');
    }

    // Handle receipt image upload -> Cloudinary for images, local for pdf
    $receipt_image = null; $receipt_public_id = null;
    if (isset($_FILES['receipt_image']) && $_FILES['receipt_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['receipt_image']['error'] !== UPLOAD_ERR_OK) { echo json_encode(['status'=>'error','message'=>'Upload nota gagal.']); exit; }
        $ext = strtolower(pathinfo($_FILES['receipt_image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg','jpeg','png','webp'], true)) {
            $res = uploadImageToCloudinary($_FILES['receipt_image'], 'mcm/finance');
            if (!$res['ok']) { echo json_encode(['status'=>'error','message'=>$res['error']]); exit; }
            $receipt_image = $res['url']; $receipt_public_id = $res['public_id'] ?? cloudinaryPublicIdFromUrl($res['url']);
        } elseif ($ext === 'pdf') {
            $uploadDir = '../../uploads/finance';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            $dest = 'uploads/finance/' . uniqid('rec_') . '.pdf';
            if (move_uploaded_file($_FILES['receipt_image']['tmp_name'], '../../' . $dest)) $receipt_image = $dest;
            else { echo json_encode(['status'=>'error','message'=>'Gagal menyimpan PDF.']); exit; }
        } else { echo json_encode(['status'=>'error','message'=>'Format nota harus JPG/PNG/WEBP/PDF.']); exit; }
    }

    try {
        if ($action === 'create') {
            $stmt = $pdo->prepare("INSERT INTO finance_transactions (type, category, item_name, quantity, unit_price, amount, description, receipt_image, receipt_public_id, transaction_date) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$type, $category, $item_name, $quantity, $unit_price, $amount, $description ?: null, $receipt_image, $receipt_public_id, $transaction_date]);
            echo json_encode(['status' => 'success', 'message' => 'Transaksi keuangan berhasil dicatat.']);
        } else {
            if ($id <= 0) {
                echo json_encode(['status' => 'error', 'message' => 'ID transaksi tidak valid.']);
                exit;
            }
            if ($receipt_image) {
                $stmt = $pdo->prepare("UPDATE finance_transactions SET type = ?, category = ?, item_name = ?, quantity = ?, unit_price = ?, amount = ?, description = ?, receipt_image = ?, receipt_public_id = ?, transaction_date = ? WHERE id = ?");
                $stmt->execute([$type, $category, $item_name, $quantity, $unit_price, $amount, $description ?: null, $receipt_image, $receipt_public_id, $transaction_date, $id]);
            } else {
                $stmt = $pdo->prepare("UPDATE finance_transactions SET type = ?, category = ?, item_name = ?, quantity = ?, unit_price = ?, amount = ?, description = ?, transaction_date = ? WHERE id = ?");
                $stmt->execute([$type, $category, $item_name, $quantity, $unit_price, $amount, $description ?: null, $transaction_date, $id]);
            }
            echo json_encode(['status' => 'success', 'message' => 'Data transaksi berhasil diperbarui.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan transaksi: ' . $e->getMessage()]);
    }
    exit;
}

// ACTION: DELETE
if ($action === 'delete') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'ID tidak valid.']);
        exit;
    }

    try {
        try { $stmt = $pdo->prepare("SELECT receipt_image, receipt_public_id FROM finance_transactions WHERE id = ?"); $stmt->execute([$id]); $rec = $stmt->fetch(); } catch (PDOException $e) { $stmt = $pdo->prepare("SELECT receipt_image FROM finance_transactions WHERE id = ?"); $stmt->execute([$id]); $rec = $stmt->fetch(); if ($rec) $rec['receipt_public_id'] = ''; }
        $del = $pdo->prepare("DELETE FROM finance_transactions WHERE id = ?");
        $del->execute([$id]);
        if ($rec && (!empty($rec['receipt_public_id']) || str_contains($rec['receipt_image'] ?? '', 'res.cloudinary.com'))) { $pid = $rec['receipt_public_id'] ?: $rec['receipt_image']; $delRes = deleteImageFromCloudinary($pid); if (!$delRes['ok']) error_log("[Cloudinary delete finance $id] ".$delRes['error']); }
        if ($rec && !empty($rec['receipt_image']) && strpos($rec['receipt_image'], 'http') !== 0) {
            @unlink('../../' . $rec['receipt_image']);
        }
        echo json_encode(['status' => 'success', 'message' => 'Transaksi berhasil dihapus.']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus transaksi.']);
    }
    exit;
}
if ($action === 'bulk_delete') {
    $raw = $_POST['ids'] ?? '';
    $ids = [];
    if (is_array($raw)) $ids = $raw;
    elseif (is_string($raw) && $raw !== '') { $d=json_decode($raw,true); $ids=is_array($d)?$d:array_filter(array_map('trim',explode(',',$raw))); }
    $ids = array_values(array_unique(array_filter(array_map('intval',$ids))));
    if (empty($ids)) { echo json_encode(['status'=>'error','message'=>'Tidak ada data terpilih']); exit; }
    if (count($ids)>100) { echo json_encode(['status'=>'error','message'=>'Maksimal 100 data']); exit; }
    try {
        // hapus file terkait dulu
        $ph = implode(',', array_fill(0,count($ids),'?'));
        $stmt = $pdo->prepare("SELECT receipt_image, receipt_public_id FROM finance_transactions WHERE id IN ($ph)");
        $stmt->execute($ids);
        $recs = $stmt->fetchAll();
        $del = $pdo->prepare("DELETE FROM finance_transactions WHERE id IN ($ph)");
        $del->execute($ids);
        foreach ($recs as $rec){
            if (!empty($rec['receipt_public_id']) || str_contains($rec['receipt_image'] ?? '', 'res.cloudinary.com')){ $pid=$rec['receipt_public_id']?:$rec['receipt_image']; $r=deleteImageFromCloudinary($pid); if(!$r['ok']) error_log("[Cloudinary bulk finance] ".$r['error']); }
            if (!empty($rec['receipt_image']) && strpos($rec['receipt_image'],'http')!==0) @unlink('../../'.$rec['receipt_image']);
        }
        echo json_encode(['status'=>'success','message'=> $del->rowCount().' transaksi berhasil dihapus.']);
    } catch(PDOException $e){ echo json_encode(['status'=>'error','message'=>'Gagal hapus massal']); }
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Aksi tidak dikenal.']);
