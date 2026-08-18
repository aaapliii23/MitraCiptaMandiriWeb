<!-- CHATBOT PAGE -->
        <div class="row align-items-center mb-5 g-3">
            <div class="col-md-6">
                <h2 class="fw-bold mb-1 text-dark">Chatbot & Balasan Otomatis</h2>
                <p class="text-muted mb-0">Kelola intent dan balasan otomatis chatbot WhatsApp & widget chat website.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <button class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" onclick="resetChatbotIntentForm()">
                    <i class="fas fa-plus me-2"></i>Tambah Intent
                </button>
            </div>
        </div>

        <div class="alert alert-info border-0 small d-flex align-items-center">
            <i class="fas fa-info-circle me-2"></i>
            Balasan dengan token <code>{classes}</code> atau <code>{prices}</code> otomatis diisi daftar program/harga terbaru dari database.
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="bg-light text-secondary small text-uppercase">
                            <tr>
                                <th class="ps-4">Intent</th>
                                <th>Kata Kunci</th>
                                <th>Balasan</th>
                                <th class="text-center">Status</th>
                                <th class="text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($chatbotIntents)): ?>
                                <tr><td colspan="5" class="text-center py-5 text-muted">Belum ada data intent.</td></tr>
                            <?php else: ?>
                                <?php foreach ($chatbotIntents as $ci): ?>
                                    <tr>
                                        <td class="ps-4"><span class="badge bg-primary bg-opacity-10 text-primary"><?php echo htmlspecialchars($ci['intent']); ?></span></td>
                                        <td class="small text-muted"><?php echo htmlspecialchars($ci['keywords']); ?></td>
                                        <td class="small text-muted" style="max-width: 300px;">
                                            <div class="text-truncate" style="white-space: pre-line;" title="<?php echo htmlspecialchars($ci['reply']); ?>"><?php echo nl2br(htmlspecialchars(mb_strimwidth($ci['reply'], 0, 120, '...'))); ?></div>
                                        </td>
                                        <td class="text-center">
                                            <?php if ((int)$ci['enabled'] === 1): ?>
                                                <span class="badge badge-soft-success"><i class="fas fa-check-circle me-1"></i>Aktif</span>
                                            <?php else: ?>
                                                <span class="badge badge-soft-secondary">Nonaktif</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="d-flex justify-content-end gap-2">
                                                <button class="btn btn-action btn-soft-primary" onclick="editChatbotIntent(<?php echo htmlspecialchars(json_encode($ci)); ?>)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-action btn-soft-danger" onclick="deleteItem('chatbot', <?php echo $ci['id']; ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
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