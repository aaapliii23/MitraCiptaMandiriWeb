<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['admin_logged_in'])) exit;

require_once '../../config/database.php';
require_once '../../includes/security.php';
mcm_cors_headers();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!mcm_csrf_verify($_POST['csrf_token'] ?? $_POST['_token'] ?? '')) {
        echo json_encode(['status'=>'error','message'=>'CSRF token tidak valid. Muat ulang halaman.']); exit;
    }
    $rateKey = 'admin_' . basename(__FILE__, '.php') . '_' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
    $rl = mcm_rate_limit($rateKey, 30, 60);
    if (!$rl['allowed']) { echo json_encode(['status'=>'error','message'=>$rl['message']]); exit; }
}
require_once '../../includes/payment_gateway.php';
pg_ensure_payment_methods($pdo);
pg_ensure_bank_accounts($pdo);

$active = (isset($_POST['active']) && is_array($_POST['active'])) ? $_POST['active'] : [];
$notes = (isset($_POST['note']) && is_array($_POST['note'])) ? $_POST['note'] : [];

try {
    $keys = $pdo->query("SELECT method_key FROM payment_methods")->fetchAll(PDO::FETCH_COLUMN, 0);
    $upd = $pdo->prepare("UPDATE payment_methods SET is_active = ?, note = ? WHERE method_key = ?");
    foreach ($keys as $k) {
        $isActive = isset($active[$k]) ? 1 : 0;
        // bank_transfer tidak punya field note (card rekening) — paksa NULL
        $note = ($k === 'bank_transfer') ? null : (is_string($notes[$k] ?? '') ? trim($notes[$k]) : '');
        $upd->execute([$isActive, ($note === '' || $note === null) ? null : mb_substr($note, 0, 100), $k]);
    }
    // --- Rekening Transfer Bank Manual (tambah/edit/hapus sekaligus) ---
    $delIds = array_values(array_unique(array_filter(array_map('intval', (array)($_POST['delete_account_ids'] ?? [])), function($v){ return $v > 0; })));
    if ($delIds) {
        $pdo->query("DELETE FROM bank_accounts WHERE id IN (" . implode(',', $delIds) . ")");
    }
    $accActive = (isset($_POST['accounts_active']) && is_array($_POST['accounts_active'])) ? $_POST['accounts_active'] : [];
    $updAcc = $pdo->prepare("UPDATE bank_accounts SET bank_name = ?, account_number = ?, account_holder = ?, is_active = ? WHERE id = ?");
    $delAcc = $pdo->prepare("DELETE FROM bank_accounts WHERE id = ?");
    foreach ((array)($_POST['accounts'] ?? []) as $id => $a) {
        $id = (int)$id;
        if ($id <= 0 || in_array($id, $delIds, true) || !is_array($a)) continue;
        $bn = trim((string)($a['bank_name'] ?? ''));
        $an = trim((string)($a['account_number'] ?? ''));
        $ah = trim((string)($a['account_holder'] ?? ''));
        if ($bn === '' && $an === '' && $ah === '') { $delAcc->execute([$id]); continue; } // baris dikosongkan = hapus
        if ($bn === '' || $an === '') continue; // baris tak lengkap = biarkan apa adanya
        $updAcc->execute([mb_substr($bn, 0, 50), mb_substr($an, 0, 50), mb_substr($ah, 0, 100), isset($accActive[$id]) ? 1 : 0, $id]);
    }
    $insAcc = $pdo->prepare("INSERT INTO bank_accounts (bank_name, account_number, account_holder, is_active) VALUES (?,?,?,?)");
    foreach ((array)($_POST['new_accounts'] ?? []) as $n) {
        if (!is_array($n)) continue;
        $bn = trim((string)($n['bank_name'] ?? ''));
        $an = trim((string)($n['account_number'] ?? ''));
        $ah = trim((string)($n['account_holder'] ?? ''));
        if ($bn === '' || $an === '') continue; // baris baru kosong = abaikan
        $insAcc->execute([mb_substr($bn, 0, 50), mb_substr($an, 0, 50), mb_substr($ah, 0, 100), isset($n['is_active']) ? 1 : 0]);
    }
    // Validasi: metode manual aktif wajib punya min. 1 rekening aktif
    $methodOn = isset($active['bank_transfer']);
    $nActive = (int)$pdo->query("SELECT COUNT(*) FROM bank_accounts WHERE is_active = 1")->fetchColumn();
    if ($methodOn && $nActive === 0) {
        echo json_encode(['status'=>'success','message'=>'Metode pembayaran diperbarui. Peringatan: Transfer Bank Manual aktif tetapi tidak ada rekening aktif — user tidak bisa memilih rekening saat membayar.']);
    } else {
        echo json_encode(['status'=>'success','message'=>'Metode pembayaran berhasil diperbarui.']);
    }
} catch (PDOException $e) {
    echo json_encode(['status'=>'error','message'=>'Gagal menyimpan ke database.']);
}
