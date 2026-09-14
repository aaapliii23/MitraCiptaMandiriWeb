<?php
// admin/pages/chat.php - Panel Percakapan Chatbot, WhatsApp, dan Siswa LMS
$threadNumber = isset($threadNumber) ? $threadNumber : '';

$conversations = [];
$messages = [];
$usersMap = [];

// Pre-load data pengguna untuk memetakan nama siswa
try {
    $uRows = $pdo->query("SELECT id, name, phone, email FROM users")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($uRows as $ur) {
        $usersMap[(int)$ur['id']] = $ur;
    }
} catch (Exception $e) {}

if (empty($threadNumber)) {
    $tmp = [];
    try {
        $allChats = $pdo->query("SELECT * FROM chat_messages ORDER BY created_at DESC, id DESC LIMIT 1000")->fetchAll();
    } catch (Exception $e) {
        $allChats = $chats ?? [];
    }
    foreach ($allChats as $ch) {
        $num = $ch['wa_number'];
        if (!isset($tmp[$num])) {
            $uName = '';
            $uPhone = '';
            if (str_starts_with($num, 'user-')) {
                $uid = (int)substr($num, 5);
                $uName = $usersMap[$uid]['name'] ?? ('Siswa #' . $uid);
                $uPhone = $usersMap[$uid]['phone'] ?? '';
            }
            $tmp[$num] = [
                'number' => $num,
                'last' => $ch,
                'in_count' => 0,
                'user_id' => $ch['user_id'],
                'user_name' => $uName,
                'user_phone' => $uPhone
            ];
        }
        if ($ch['direction'] === 'in' && empty($ch['is_read'])) $tmp[$num]['in_count']++;
    }
    $conversations = array_values($tmp);
} else {
    try {
        // Tandai pesan masuk thread ini sebagai sudah dibaca
        $pdo->prepare("UPDATE chat_messages SET is_read = 1, read_at = NOW() WHERE wa_number = ? AND direction = 'in' AND is_read = 0")->execute([$threadNumber]);

        if (str_starts_with($threadNumber, 'user-') || str_starts_with($threadNumber, 'web-')) {
            $stmt = $pdo->prepare("SELECT * FROM chat_messages WHERE wa_number = ? ORDER BY id ASC");
            $stmt->execute([$threadNumber]);
        } else {
            $cleanDigits = preg_replace('/\D+/', '', $threadNumber);
            $stmt = $pdo->prepare("SELECT * FROM chat_messages WHERE wa_number = ? OR wa_number = ? OR wa_number = ? ORDER BY id ASC");
            $stmt->execute([$threadNumber, '+' . $cleanDigits, $cleanDigits]);
        }
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        foreach ($chats as $ch) {
            if ($ch['wa_number'] === $threadNumber) $messages[] = $ch;
        }
    }

    $threadStudent = null;
    if (str_starts_with($threadNumber, 'user-')) {
        $tUid = (int)substr($threadNumber, 5);
        $threadStudent = $usersMap[$tUid] ?? null;
        if (!$threadStudent) {
            try {
                $uStmt = $pdo->prepare("SELECT id, name, phone, email FROM users WHERE id = ?");
                $uStmt->execute([$tUid]);
                $threadStudent = $uStmt->fetch(PDO::FETCH_ASSOC);
            } catch (Exception $e) {}
        }
    }
}
?>
<!-- CHAT PAGE -->
<div class="row align-items-center mb-4 g-3" data-aos="fade-down">
    <div class="col-md-6">
        <h2 class="fw-bold mb-1 text-dark">Chat WhatsApp & Bantuan</h2>
        <p class="text-muted mb-0">Pantau percakapan chatbot dan balas pertanyaan siswa maupun pengunjung.</p>
    </div>
    <div class="col-md-6 text-md-end d-flex justify-content-md-end gap-2 align-items-center flex-wrap">
        <?php if (empty($threadNumber)): ?>
            <div class="input-group shadow-sm rounded-3 overflow-hidden" style="max-width: 280px;">
                <span class="input-group-text bg-white border-end-0 text-muted ps-3"><i class="fas fa-search"></i></span>
                <input type="text" id="chatThreadSearch" class="form-control border-start-0 py-2" placeholder="Cari nama / nomor / pesan..." autocomplete="off">
            </div>
        <?php else: ?>
            <a href="?page=chat" class="btn btn-light border rounded-pill px-4 fw-bold"><i class="fas fa-arrow-left me-2"></i>Semua Percakapan</a>
        <?php endif; ?>
    </div>
</div>

<?php if (empty($threadNumber)): ?>
    <?php require __DIR__ . '/chat_list.php'; ?>
<?php else: ?>
    <?php require __DIR__ . '/chat_thread.php'; ?>
<?php endif; ?>

<script>
function deleteConversation(number) {
    if (!window.Swal) {
        if (!confirm('Hapus percakapan ini?')) return;
    }
    const doDelete = function() {
        const adminBase = '<?php echo $adminBase; ?>';
        const cleanNumber = decodeURIComponent(number);
        const formData = new FormData();
        formData.append('action', 'delete_thread');
        formData.append('wa_number', cleanNumber);
        const csrf = document.querySelector('input[name="csrf_token"]')?.value || window.MCM_CSRF_TOKEN || '';
        if (csrf) formData.append('csrf_token', csrf);
        fetch(adminBase + '/actions/manage_chat.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            const ok = data.status === 'success';
            if (window.Swal) {
                Swal.fire({ icon: ok?'success':'error', title: ok?'Terhapus!':'Gagal', text:data.message, timer: ok?1500:undefined, showConfirmButton: !ok }).then(() => {
                    if (ok) {
                        const tr = document.querySelector('tr[data-thread="'+cleanNumber+'"]') || document.querySelector('a[href*="thread='+encodeURIComponent(number)+'"]')?.closest('tr');
                        if (tr) tr.remove();
                    }
                });
            } else {
                alert(data.message);
                if (ok) location.reload();
            }
        }).catch(err => { if(window.Swal) Swal.fire('Gagal', err.message || 'Gagal', 'error'); else alert('Gagal'); });
    };

    if (window.Swal) {
        Swal.fire({
            title: 'Hapus percakapan ini?',
            text: 'Semua pesan dari nomor/siswa ini akan dihapus.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => { if (result.isConfirmed) doDelete(); });
    } else {
        doDelete();
    }
}
</script>