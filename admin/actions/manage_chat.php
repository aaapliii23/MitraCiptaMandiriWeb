<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']);
    exit;
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/security.php';
mcm_cors_headers();
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    if (!mcm_csrf_verify($_POST['csrf_token'] ?? $_POST['_token'] ?? '')) {
        echo json_encode(['status'=>'error','message'=>'CSRF token tidak valid. Muat ulang halaman.']); exit;
    }
    $rateKey = 'admin_' . basename(__FILE__, '.php') . '_' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
    $rl = mcm_rate_limit($rateKey, 30, 60);
    if (!$rl['allowed']) { echo json_encode(['status'=>'error','message'=>$rl['message']]); exit; }
}
require_once __DIR__ . '/../../includes/whatsapp_client.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';

if ($action === 'send_reply') {
    $waNumber = trim($_POST['wa_number'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (!str_starts_with($waNumber, 'web-') && !str_starts_with($waNumber, 'user-')) {
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
    $isUser = str_starts_with($waNumber, 'user-');
    try {
        $ok = wa_send_message($pdo, $waNumber, $message, null, 'admin');
        $newId = (int)$pdo->lastInsertId();
        if ($isAnon) {
            echo json_encode(['status' => 'success', 'message' => 'Balasan tersimpan (widget web anonim)', 'wa_sent' => false, 'msg_id' => $newId]);
        } elseif ($isUser) {
            echo json_encode(['status' => 'success', 'message' => 'Balasan tersimpan (langsung tampil di akun Siswa LMS)', 'wa_sent' => false, 'msg_id' => $newId]);
        } else {
            if ($ok) {
                echo json_encode(['status' => 'success', 'message' => 'Balasan terkirim ke WhatsApp', 'wa_sent' => true, 'msg_id' => $newId]);
            } else {
                echo json_encode(['status' => 'success', 'message' => 'Balasan tersimpan di sistem.', 'wa_sent' => false, 'wa_error' => 'Gateway offline/mock', 'msg_id' => $newId]);
            }
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal mengirim balasan: ' . $e->getMessage()]);
    }
} elseif ($action === 'get_messages') {
    $waNumber = trim($_GET['wa_number'] ?? $_POST['wa_number'] ?? '');
    $afterId = (int)($_GET['after_id'] ?? $_POST['after_id'] ?? 0);
    if (empty($waNumber)) {
        echo json_encode(['status' => 'error', 'message' => 'Nomor percakapan diperlukan.']);
        exit;
    }
    try {
        // Admin membuka/melihat percakapan -> tandai semua pesan masuk sebagai sudah dibaca
        $pdo->prepare("UPDATE chat_messages SET is_read = 1, read_at = NOW() WHERE wa_number = ? AND direction = 'in' AND is_read = 0")->execute([$waNumber]);

        $fieldSelect = "id, wa_number, user_id, direction, sender_type, message, matched_intent, is_read, read_at, created_at, 
                        DATE_FORMAT(created_at, '%d %b %Y %H:%i') AS time_formatted, 
                        DATE_FORMAT(created_at, '%H:%i') AS time_short,
                        DATE_FORMAT(read_at, '%H:%i') AS read_time_short,
                        DATE_FORMAT(read_at, '%d %b %Y %H:%i') AS read_time_formatted";

        if (str_starts_with($waNumber, 'user-') || str_starts_with($waNumber, 'web-')) {
            $stmt = $pdo->prepare("SELECT $fieldSelect FROM chat_messages WHERE wa_number = ? AND id > ? ORDER BY id ASC LIMIT 50");
            $stmt->execute([$waNumber, $afterId]);
        } else {
            $cleanDigits = preg_replace('/\D+/', '', $waNumber);
            $stmt = $pdo->prepare("SELECT $fieldSelect FROM chat_messages WHERE (wa_number = ? OR wa_number = ? OR wa_number = ?) AND id > ? ORDER BY id ASC LIMIT 50");
            $stmt->execute([$waNumber, '+' . $cleanDigits, $cleanDigits, $afterId]);
        }
        $msgs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Ambil update status pesan keluar yang sudah dilihat siswa
        $stmtRead = $pdo->prepare("SELECT id, is_read, DATE_FORMAT(read_at, '%H:%i') AS read_time_short, DATE_FORMAT(read_at, '%d %b %Y %H:%i') AS read_time_formatted FROM chat_messages WHERE wa_number = ? AND direction = 'out' AND is_read = 1 ORDER BY id DESC LIMIT 30");
        $stmtRead->execute([$waNumber]);
        $readUpdates = $stmtRead->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'status' => 'success', 
            'messages' => $msgs,
            'read_updates' => $readUpdates
        ]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal memuat pesan: ' . $e->getMessage()]);
    }
    exit;
} elseif ($action === 'get_conversations') {
    try {
        $allChats = $pdo->query("SELECT * FROM chat_messages ORDER BY created_at DESC, id DESC LIMIT 500")->fetchAll(PDO::FETCH_ASSOC);
        $usersMap = [];
        try {
            $uRows = $pdo->query("SELECT id, name, phone, email FROM users")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($uRows as $ur) $usersMap[(int)$ur['id']] = $ur;
        } catch (Exception $e) {}

        $tmp = [];
        foreach ($allChats as $ch) {
            $num = $ch['wa_number'];
            if (!isset($tmp[$num])) {
                $kind = 'wa';
                $name = $num;
                $phone = '';
                if (str_starts_with($num, 'web-')) {
                    $kind = 'web';
                    $name = $num;
                } elseif (str_starts_with($num, 'user-')) {
                    $kind = 'user';
                    $uid = (int)substr($num, 5);
                    $name = $usersMap[$uid]['name'] ?? ('Siswa #' . $uid);
                    $phone = $usersMap[$uid]['phone'] ?? '';
                }
                $tmp[$num] = [
                    'number' => $num,
                    'kind' => $kind,
                    'name' => $name,
                    'phone' => $phone,
                    'last_message' => mb_strimwidth($ch['message'], 0, 90, '...'),
                    'last_time' => date('d M Y H:i', strtotime($ch['created_at'])),
                    'matched_intent' => $ch['matched_intent'],
                    'in_count' => 0,
                    'last_id' => (int)$ch['id']
                ];
            }
            if ($ch['direction'] === 'in' && empty($ch['is_read'])) $tmp[$num]['in_count']++;
        }
        echo json_encode(['status' => 'success', 'conversations' => array_values($tmp)]);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
} elseif ($action === 'delete_thread') {
    $waNumber = trim($_POST['wa_number'] ?? '');
    if ($waNumber) {
        if (str_starts_with($waNumber, 'user-') || str_starts_with($waNumber, 'web-')) {
            $stmt = $pdo->prepare("DELETE FROM chat_messages WHERE wa_number = ?");
            $stmt->execute([$waNumber]);
        } else {
            $cleanDigits = preg_replace('/\D+/', '', $waNumber);
            $stmt = $pdo->prepare("DELETE FROM chat_messages WHERE wa_number = ? OR wa_number = ? OR wa_number = ?");
            $stmt->execute([$waNumber, '+' . $cleanDigits, $cleanDigits]);
        }
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
    // sanitasi: izinkan web-..., user-..., atau angka
    $clean=[]; foreach($nums as $n){ $n=trim($n); if(str_starts_with($n,'web-') || str_starts_with($n,'user-')){ $c=preg_replace('/[^a-z0-9\-]/','',strtolower($n)); if($c) $clean[]=$c; } else { $d=preg_replace('/\D+/','',$n); if($d) $clean[]=$d; } }
    if(empty($clean)){ echo json_encode(['status'=>'error','message'=>'Tidak ada nomor valid']); exit; }
    try{
        $deleted=0;
        $stmt=$pdo->prepare("DELETE FROM chat_messages WHERE wa_number = ?");
        foreach($clean as $num){ $stmt->execute([$num]); $deleted+=$stmt->rowCount(); }
        echo json_encode(['status'=>'success','message'=> $deleted.' pesan dari '.count($clean).' percakapan dihapus']);
    }catch(PDOException $e){ echo json_encode(['status'=>'error','message'=>'Gagal hapus massal']); }
    exit;
}
