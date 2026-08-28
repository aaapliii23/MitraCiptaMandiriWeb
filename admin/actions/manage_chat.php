<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['admin_logged_in'])) exit;

require_once '../../config/database.php';
require_once '../../includes/whatsapp_client.php';

$action = $_POST['action'] ?? '';

if ($action === 'send_reply') {
    $waNumber = trim($_POST['wa_number'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (strpos($waNumber, 'web-') !== 0) {
        $digits = preg_replace('/\D+/', '', $waNumber);
        if (strpos($digits, '0') === 0) {
            $digits = '62' . substr($digits, 1);
        } elseif (strpos($digits, '8') === 0) {
            $digits = '62' . $digits;
        }
        $waNumber = $digits;
    }

    if (empty($waNumber) || empty($message)) {
        echo json_encode(['status' => 'error', 'message' => 'Nomor dan pesan wajib diisi.']);
        exit;
    }

    $isAnon = str_starts_with($waNumber, 'web-');
    try {
        $ok = wa_send_message($pdo, $waNumber, $message, 'admin', 'admin');
        if ($isAnon) {
            echo json_encode(['status' => 'success', 'message' => 'Balasan tersimpan — hanya di widget', 'wa_sent' => false]);
        } else {
            if ($ok) {
                echo json_encode(['status' => 'success', 'message' => 'Balasan terkirim ke WhatsApp', 'wa_sent' => true]);
            } else {
                echo json_encode(['status' => 'success', 'message' => 'Balasan tersimpan, tapi gagal terkirim ke WhatsApp. Cek koneksi Fonnte.', 'wa_sent' => false, 'wa_error' => 'Fonnte gagal']);
            }
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal mengirim balasan.']);
    }
} elseif ($action === 'delete_thread') {
    $waNumber = trim($_POST['wa_number'] ?? '');
    if ($waNumber) {
        $stmt = $pdo->prepare("DELETE FROM chat_messages WHERE wa_number = ?");
        $stmt->execute([$waNumber]);
        echo json_encode(['status' => 'success', 'message' => 'Percakapan dihapus.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Nomor tidak valid.']);
    }
}
if ($action === 'bulk_delete_thread') {
    $raw = $_POST['wa_numbers'] ?? $_POST['ids'] ?? '';
    $nums = [];
    if (is_array($raw)) $nums = $raw;
    elseif (is_string($raw) && $raw !== '') { $d=json_decode($raw,true); $nums=is_array($d)?$d:array_filter(array_map('trim',explode(',',$raw))); }
    $nums = array_values(array_unique(array_filter($nums, fn($v)=> trim($v)!=='')));
    if (empty($nums)) { echo json_encode(['status'=>'error','message'=>'Tidak ada percakapan terpilih']); exit; }
    if (count($nums)>100) { echo json_encode(['status'=>'error','message'=>'Maksimal 100']); exit; }
    // sanitasi: hanya izinkan web-... atau angka
    $clean=[]; foreach($nums as $n){ $n=trim($n); if(str_starts_with($n,'web-')){ $c=preg_replace('/[^a-z0-9\-]/','',strtolower($n)); if($c) $clean[]=$c; } else { $d=preg_replace('/\D+/','',$n); if($d) $clean[]=$d; } }
    if(empty($clean)){ echo json_encode(['status'=>'error','message'=>'Tidak ada nomor valid']); exit; }
    try{
        $deleted=0;
        $stmt=$pdo->prepare("DELETE FROM chat_messages WHERE wa_number = ?");
        foreach($clean as $num){ $stmt->execute([$num]); $deleted+=$stmt->rowCount(); }
        echo json_encode(['status'=>'success','message'=> $deleted.' pesan dari '.count($clean).' percakapan dihapus']);
    }catch(PDOException $e){ echo json_encode(['status'=>'error','message'=>'Gagal hapus massal']); }
    exit;
}
