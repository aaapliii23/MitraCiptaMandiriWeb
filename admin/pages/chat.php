<?php
$threadNumber = isset($threadNumber) ? $threadNumber : '';

$conversations = [];
$messages = [];
if (empty($threadNumber)) {
    $tmp = [];
    foreach ($chats as $ch) {
        $num = $ch['wa_number'];
        if (!isset($tmp[$num])) {
            $tmp[$num] = ['number' => $num, 'last' => $ch, 'in_count' => 0];
        }
        if ($ch['direction'] === 'in') $tmp[$num]['in_count']++;
    }
    $conversations = array_values($tmp);
} else {
    foreach ($chats as $ch) {
        if ($ch['wa_number'] === $threadNumber) $messages[] = $ch;
    }
    $messages = array_reverse($messages);
}
?>
<!-- CHAT PAGE -->
        <div class="row align-items-center mb-5 g-3" data-aos="fade-down">
            <div class="col-md-6">
                <h2 class="fw-bold mb-1 text-dark">Chat WhatsApp</h2>
                <p class="text-muted mb-0">Pantau percakapan chatbot dan balas pertanyaan peserta.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <?php if (!empty($threadNumber)): ?>
                    <a href="?page=chat" class="btn btn-light border rounded-pill px-4 fw-bold"><i class="fas fa-arrow-left me-2"></i>Semua Percakapan</a>
                <?php endif; ?>
            </div>
        </div>

        <?php if (empty($threadNumber)): ?>
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Nomor WhatsApp</th>
                                    <th>Pesan Terakhir</th>
                                    <th class="text-center">Intensitas Masuk</th>
                                    <th class="text-end pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($conversations)): ?>
                                    <tr><td colspan="4" class="text-center py-5 text-muted">Belum ada percakapan.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($conversations as $cv): ?>
                                        <tr>
                                            <td class="ps-4">
                                                <div class="fw-bold text-dark">
                                                    <i class="fab fa-whatsapp text-success me-2"></i><?php echo htmlspecialchars($cv['number']); ?>
                                                    <?php if ($cv['in_count'] > 0): ?>
                                                        <span class="badge bg-success rounded-pill ms-1"><?php echo $cv['in_count']; ?></span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td class="text-muted small" style="max-width: 380px;">
                                                <div class="text-truncate"><?php echo htmlspecialchars(mb_strimwidth($cv['last']['message'], 0, 90, '...')); ?></div>
                                                <div class="small text-muted">
                                                    <?php echo date('d M Y H:i', strtotime($cv['last']['created_at'])); ?>
                                                    <?php if ($cv['last']['matched_intent']): ?>
                                                        <span class="badge bg-soft-primary text-primary ms-1"><?php echo htmlspecialchars($cv['last']['matched_intent']); ?></span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td class="text-center text-muted small"><?php echo (int)$cv['in_count']; ?></td>
                                            <td class="text-end pe-4">
                                                <div class="d-inline-flex gap-2">
                                                    <a href="?page=chat&thread=<?php echo urlencode($cv['number']); ?>" class="btn btn-action btn-soft-primary" title="Buka Percakapan"><i class="fas fa-comment-dots"></i></a>
                                                    <button class="btn btn-action btn-soft-danger" title="Hapus Percakapan" onclick="deleteConversation('<?php echo urlencode($cv['number']); ?>')"><i class="fas fa-trash"></i></button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white border-0 py-3 px-4 d-flex align-items-center justify-content-between">
                    <div class="fw-bold text-dark"><i class="fab fa-whatsapp text-success me-2"></i><?php echo htmlspecialchars($threadNumber); ?></div>
                    <span class="badge bg-light text-dark border rounded-pill px-3"><?php echo count($messages); ?> pesan</span>
                </div>
                <div class="card-body p-4" style="max-height: 460px; overflow-y: auto; background: #f8fafc;">
                    <?php if (empty($messages)): ?>
                        <div class="text-center text-muted py-5">Belum ada pesan di percakapan ini.</div>
                    <?php else: ?>
                        <?php foreach ($messages as $msg): ?>
                            <div class="d-flex mb-3 <?php echo $msg['direction'] === 'in' ? '' : 'justify-content-end'; ?>">
                                <div class="rounded-3 px-3 py-2 shadow-sm small <?php echo $msg['direction'] === 'in' ? 'bg-white border' : 'bg-primary text-white'; ?>" style="max-width: 75%;">
                                    <div><?php echo nl2br(htmlspecialchars($msg['message'])); ?></div>
                                    <div class="small mt-1 <?php echo $msg['direction'] === 'in' ? 'text-muted' : 'text-white-50'; ?>">
                                        <?php echo date('d M Y H:i', strtotime($msg['created_at'])); ?>
                                        <?php if ($msg['matched_intent']): ?>
                                            <span class="badge <?php echo $msg['direction'] === 'in' ? 'bg-soft-primary text-primary' : 'bg-white bg-opacity-25 text-white'; ?> ms-1"><?php echo htmlspecialchars($msg['matched_intent']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <div class="card-footer bg-white border-0 p-4">
                    <form id="chatReplyForm" class="ajax-form d-flex gap-2" action="<?php echo $adminBase; ?>/actions/manage_chat.php" method="POST">
                        <input type="hidden" name="action" value="send_reply">
                        <input type="hidden" name="wa_number" value="<?php echo htmlspecialchars($threadNumber); ?>">
                        <input type="text" class="form-control rounded-pill" name="message" required placeholder="Tulis balasan...">
                        <button type="submit" class="btn btn-success rounded-pill fw-bold px-4"><i class="fab fa-whatsapp me-2"></i>Kirim</button>
                    </form>
                </div>
            </div>

            <script>
            function deleteConversation(number) {
                Swal.fire({
                    title: 'Hapus percakapan ini?',
                    text: 'Semua pesan dari nomor ini akan dihapus.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const adminBase = '<?php echo $adminBase; ?>';
                        const formData = new FormData();
                        formData.append('action', 'delete_thread');
                        formData.append('wa_number', number);
                        fetch(adminBase + '/actions/manage_chat.php', { method: 'POST', body: formData })
                        .then(res => res.json())
                        .then(data => {
                            Swal.fire(data.status === 'success' ? 'Terhapus!' : 'Gagal', data.message, data.status).then(() => {
                                if (data.status === 'success') window.location.href = '?page=chat';
                            });
                        });
                    }
                });
            }
            </script>
        <?php endif; ?>