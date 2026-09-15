<!-- Quiz Modal -->
<div class="modal fade" id="quizModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow" style="border-radius: 1rem;">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                <div>
                    <h5 class="modal-title fw-bold" id="quizModalTitle">Kelola Quiz</h5>
                    <small class="text-muted" id="quizModalSubtitle"></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="quizMaterialId">
                <div class="alert alert-info rounded-3 py-2 px-3 small mb-3">
                    <i class="fas fa-info-circle me-2"></i>Peserta wajib menjawab benar 100% (semua soal). Jawaban salah akan dibahas dan diminta perbaiki sampai benar semua.
                </div>

                <div class="d-flex justify-content-between align-items-center mb-2">
                    <label class="form-label small fw-bold mb-0">Daftar Soal</label>
                    <span class="badge bg-light text-dark border" id="quizCount">0 soal</span>
                </div>
                <div id="quizList" class="mb-3">
                    <div class="text-center text-muted py-4 small">Memuat soal...</div>
                </div>

                <hr>
                <label class="form-label small fw-bold" id="quizFormTitle">Tambah Soal</label>
                <form id="quizForm" class="mb-1" action="<?php echo $adminBase; ?>/actions/manage_quiz.php" method="POST" onsubmit="event.preventDefault(); submitQuizQuestionForm();">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(mcm_csrf_token()); ?>">
                    <input type="hidden" name="action" id="quizAction" value="create">
                    <input type="hidden" name="id" id="quizId">
                    <input type="hidden" name="material_id" id="quizMaterialInput">
                    <div class="mb-3">
                        <label class="form-label small">Tipe Soal</label>
                        <select class="form-select" name="question_type" id="quizType" onchange="quizTypeChanged(this.value)">
                            <option value="mcq">Pilihan Ganda (PG)</option>
                            <option value="essay">Essay</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small">Pertanyaan</label>
                        <textarea class="form-control" name="question" id="quizQuestion" rows="2" required placeholder="Tulis pertanyaan..."></textarea>
                    </div>
                    <div id="quizMcqFields">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small">Pilihan A</label>
                                <input type="text" class="form-control" name="option_a" id="quizOptionA" placeholder="Pilihan A">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small">Pilihan B</label>
                                <input type="text" class="form-control" name="option_b" id="quizOptionB" placeholder="Pilihan B">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small">Pilihan C</label>
                                <input type="text" class="form-control" name="option_c" id="quizOptionC" placeholder="Pilihan C">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small">Pilihan D</label>
                                <input type="text" class="form-control" name="option_d" id="quizOptionD" placeholder="Pilihan D">
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small">Kunci Jawaban</label>
                                <select class="form-select" name="correct_option" id="quizCorrect">
                                    <option value="a">A</option>
                                    <option value="b">B</option>
                                    <option value="c">C</option>
                                    <option value="d">D</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div id="quizEssayFields" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label small">Kunci Jawaban / Referensi</label>
                            <textarea class="form-control" name="essay_answer" id="quizEssayAnswer" rows="3" placeholder="Tulis poin-poin jawaban yang diharapkan sebagai acuan penilaian..."></textarea>
                            <small class="text-muted">Jawaban peserta dinilai otomatis berdasarkan kata kunci pada referensi ini.</small>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small">Pembahasan / Penjelasan <span class="text-muted fw-normal">(opsional, tampil saat jawaban salah)</span></label>
                        <textarea class="form-control" name="explanation" id="quizExplanation" rows="2" placeholder="Jelaskan mengapa jawaban benar demikian..."></textarea>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small">Urutan</label>
                            <input type="number" class="form-control" name="sort_order" id="quizSort" value="0" min="0">
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <button type="button" class="btn btn-light rounded-pill px-3" id="quizResetBtn" onclick="resetQuizQuestionForm()">Reset</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold" id="quizSubmitBtn">Simpan Soal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>