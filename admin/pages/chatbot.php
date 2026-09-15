<!-- CHATBOT PAGE -->
        <div class="row align-items-center mb-4 g-3">
            <div class="col-md-6">
                <h2 class="fw-bold mb-1 text-dark">Chatbot & Balasan Otomatis</h2>
                <p class="text-muted mb-0">Kelola intent dan balasan otomatis chatbot WhatsApp & widget chat website.</p>
            </div>
            <div class="col-md-6 text-md-end d-flex justify-content-md-end gap-2 align-items-center flex-wrap">
                <div class="input-group shadow-sm rounded-3 overflow-hidden" style="max-width: 280px;">
                    <span class="input-group-text bg-white border-end-0 text-muted ps-3"><i class="fas fa-search"></i></span>
                    <input type="text" id="intentSearch" class="form-control border-start-0 py-2" placeholder="Cari intent / kata kunci / balasan..." autocomplete="off">
                </div>
                <button class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" onclick="resetChatbotIntentForm()">
                    <i class="fas fa-plus me-2"></i>Tambah Intent
                </button>
            </div>
        </div>

        <div class="alert alert-info border-0 small d-flex align-items-center">
            <i class="fas fa-info-circle me-2"></i>
            Balasan dengan token <code>{classes}</code> atau <code>{prices}</code> otomatis diisi daftar program/harga terbaru dari database.
        </div>

        <div data-bulk-table="chatbot">
        <div class="admin-table-toolbar d-none" data-bulk-toolbar>
            <div class="small fw-bold text-primary"><i class="fas fa-check-square me-1"></i><span data-bulk-count>0 dipilih</span></div>
            <button type="button" class="btn btn-sm btn-danger rounded-pill px-3 fw-bold" data-bulk-delete><i class="fas fa-trash me-1"></i>Hapus Terpilih (<span data-bulk-count-num>0</span>)</button>
        </div>
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive table-responsive--no-scroll">
                    <table class="table align-middle admin-compact mb-0">
                        <thead class="bg-light text-secondary small text-uppercase">
                            <tr>
                                <th class="col-check"><input type="checkbox" class="bulk-select-all js-bulk-select-all"></th>
                                <th>Intent</th>
                                <th>Kata Kunci</th>
                                <th>Balasan</th>
                                <th class="text-center">Status</th>
                                <th class="text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="intentTableBody">
                            <?php if (empty($chatbotIntents)): ?>
                                <tr><td colspan="6" class="text-center py-5 text-muted">Belum ada data intent.</td></tr>
                            <?php else: ?>
                                <?php foreach ($chatbotIntents as $ci): ?>
                                    <tr>
                                        <td class="col-check"><input type="checkbox" class="bulk-row-check js-bulk-row" value="<?php echo (int)$ci['id']; ?>"></td>
                                        <td><span class="badge bg-primary bg-opacity-10 text-primary small"><?php echo htmlspecialchars($ci['intent']); ?></span></td>
                                        <td><span class="cell-ellipsis small text-muted" title="<?php echo htmlspecialchars($ci['keywords']); ?>" style="max-width:160px;"><?php echo htmlspecialchars($ci['keywords']); ?></span></td>
                                        <td style="max-width: 300px;">
                                            <span class="cell-ellipsis small text-muted" title="<?php echo htmlspecialchars($ci['reply']); ?>" style="max-width:260px; white-space:pre-line;"><?php echo htmlspecialchars(mb_strimwidth(str_replace(["\r","\n"], ' ', $ci['reply']),0,100,'...')); ?></span>
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
        </div>
<script>attachTableSearch('intentSearch', 'intentTableBody', 6);</script>
