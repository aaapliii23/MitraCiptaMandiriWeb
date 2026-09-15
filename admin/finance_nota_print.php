<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../auth/admin_login.php');
    exit;
}

require_once '../config/database.php';

$id = (int)($_GET['id'] ?? 0);
$tr = null;
if ($id > 0) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM finance_transactions WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $tr = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    } catch (PDOException $e) { $tr = null; }
}
if (!$tr) { http_response_code(404); echo 'Transaksi tidak ditemukan.'; exit; }

function nota_url($path) {
    if (!$path) return '';
    if (preg_match('#^https?://#i', $path)) return $path;
    return '../' . ltrim($path, '/');
}
$catLabel = ucwords(str_replace('_', ' ', $tr['category'] ?? '-'));
$isIn = ($tr['type'] ?? '') === 'in';
$receiptUrl = nota_url($tr['receipt_image'] ?? '');
$receiptIsPdf = $receiptUrl !== '' && preg_match('/\.pdf(\?.*)?$/i', $receiptUrl);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Nota Transaksi #<?php echo (int)$tr['id']; ?> - Mitra Cipta Mandiri</title>
<style>
    * { box-sizing: border-box; font-family: Arial, Helvetica, sans-serif; }
    body { background: #f1f5f9; margin: 0; padding: 24px; color: #1e293b; }
    .nota { max-width: 560px; margin: 0 auto; background: #fff; border: 2px solid #0c4a6e; border-radius: 12px; overflow: hidden; }
    .nota-head { background: #0c4a6e; color: #fff; text-align: center; padding: 18px 16px; }
    .nota-head h2 { margin: 0; font-size: 20px; letter-spacing: 1px; }
    .nota-head p { margin: 4px 0 0; font-size: 13px; opacity: .9; }
    .nota-body { padding: 20px 24px; }
    .row { display: flex; justify-content: space-between; gap: 16px; padding: 8px 0; border-bottom: 1px dashed #cbd5e1; font-size: 14px; }
    .row:last-child { border-bottom: none; }
    .label { color: #64748b; }
    .value { font-weight: bold; text-align: right; }
    .amount { font-size: 22px; color: <?php echo $isIn ? '#16a34a' : '#dc2626'; ?>; }
    .badge { display: inline-block; padding: 2px 12px; border-radius: 999px; font-size: 12px; font-weight: bold; }
    .badge.in { background: #dcfce7; color: #16a34a; }
    .badge.out { background: #fee2e2; color: #dc2626; }
    .receipt { margin-top: 16px; text-align: center; }
    .receipt img { max-width: 100%; max-height: 420px; border: 1px solid #cbd5e1; border-radius: 8px; }
    .nota-foot { padding: 16px 24px 22px; text-align: center; font-size: 12px; color: #64748b; }
    .toolbar { max-width: 560px; margin: 0 auto 16px; text-align: right; }
    .toolbar button { background: #0c4a6e; color: #fff; border: none; border-radius: 999px; padding: 10px 24px; font-weight: bold; cursor: pointer; }
    @media print {
        body { background: #fff; padding: 0; }
        .toolbar { display: none; }
        .nota { border-radius: 0; max-width: 100%; }
    }
</style>
</head>
<body>
<div class="toolbar no-print"><button onclick="window.print()"><i class="fas fa-print"></i> Cetak / Simpan PDF</button></div>
<div class="nota">
    <div class="nota-head">
        <h2>MITRA CIPTA MANDIRI</h2>
        <p>Nota Transaksi Keuangan</p>
    </div>
    <div class="nota-body">
        <div class="row"><span class="label">No. Transaksi</span><span class="value">#<?php echo (int)$tr['id']; ?></span></div>
        <div class="row"><span class="label">Tanggal</span><span class="value"><?php echo htmlspecialchars(date('d F Y', strtotime($tr['transaction_date']))); ?></span></div>
        <div class="row"><span class="label">Keterangan</span><span class="value"><?php echo htmlspecialchars($tr['item_name'] ?: $catLabel); ?></span></div>
        <div class="row"><span class="label">Kategori</span><span class="value"><?php echo htmlspecialchars($catLabel); ?></span></div>
        <div class="row"><span class="label">Jenis</span><span class="value"><span class="badge <?php echo $isIn ? 'in' : 'out'; ?>"><?php echo $isIn ? 'Masuk' : 'Keluar'; ?></span></span></div>
        <?php if (!empty($tr['description'])): ?>
        <div class="row"><span class="label">Catatan</span><span class="value"><?php echo htmlspecialchars($tr['description']); ?></span></div>
        <?php endif; ?>
        <div class="row"><span class="label">Nominal</span><span class="value amount">Rp <?php echo number_format((int)$tr['amount'], 0, ',', '.'); ?></span></div>
        <?php if ($receiptUrl && !$receiptIsPdf): ?>
        <div class="receipt"><img src="<?php echo htmlspecialchars($receiptUrl); ?>" alt="Bukti transaksi"></div>
        <?php elseif ($receiptIsPdf): ?>
        <div class="receipt"><a href="<?php echo htmlspecialchars($receiptUrl); ?>" target="_blank">Buka file bukti (PDF)</a></div>
        <?php endif; ?>
    </div>
    <div class="nota-foot">Dicetak pada <?php echo date('d/m/Y H:i'); ?> &bull; Dokumen internal Mitra Cipta Mandiri</div>
</div>
</body>
</html>
