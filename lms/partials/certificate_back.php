<!-- ==================== HALAMAN 2: BELAKANG (UNIT KOMPETENSI) ==================== -->
<div class="cert-page page-back">
    <div class="cert-border">
        <div>
            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                <div>
                    <h5 class="fw-bold mb-0 text-dark" style="font-size: 1.1rem; color: var(--cert-navy) !important;">DAFTAR UNIT KOMPETENSI / MODUL</h5>
                    <small class="text-muted">Lampiran Transkrip Sertifikat No: <strong><?php echo htmlspecialchars($certData['cert_number']); ?></strong></small>
                </div>
                <img src="../assets/img/logo.png" alt="MCM Logo" style="height: 38px;">
            </div>

            <div class="row g-2 mb-3 small bg-light p-2 rounded-3 border">
                <div class="col-6"><strong>Nama Peserta:</strong> <?php echo htmlspecialchars($user['name']); ?></div>
                <div class="col-6"><strong>Program:</strong> <?php echo htmlspecialchars($class['name']); ?></div>
                <div class="col-6"><strong>Kejuruan:</strong> <?php echo htmlspecialchars($class['category']); ?></div>
                <div class="col-6"><strong>Status Akhir:</strong> <span class="text-success fw-bold">KOMPETEN (K)</span></div>
            </div>

            <!-- Table of Competency Units -->
            <table class="units-table">
                <thead>
                    <tr>
                        <th style="width: 45px;" class="text-center">No</th>
                        <th style="width: 130px;">Kode Unit</th>
                        <th>Judul Unit Kompetensi / Materi Pelatihan</th>
                        <th style="width: 75px;" class="text-center">Durasi</th>
                        <th style="width: 90px;" class="text-center">Hasil</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $unitItems = !empty($materials) ? $materials : [];
                    if (empty($unitItems)) {
                        $features = json_decode($class['features'] ?? '[]', true) ?: [];
                        foreach ($features as $idx => $feat) {
                            $unitItems[] = ['title' => $feat, 'sort_order' => $idx + 1];
                        }
                    }
                    $totalHours = count($unitItems) * 4;
                    ?>
                    <?php if (empty($unitItems)): ?>
                        <tr><td colspan="5" class="text-center text-muted py-3">Unit kompetensi terintegrasi pada kurikulum inti.</td></tr>
                    <?php else: ?>
                        <?php foreach ($unitItems as $idx => $m): 
                            $prefix = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $class['name']), 0, 3) ?: 'MCM');
                            $code = sprintf("MCM-%s-%03d", $prefix, $idx + 1);
                        ?>
                            <tr>
                                <td class="text-center fw-bold"><?php echo $idx + 1; ?></td>
                                <td class="fw-semibold text-primary" style="font-size: 0.75rem;"><?php echo $code; ?></td>
                                <td class="fw-medium"><?php echo htmlspecialchars($m['title']); ?></td>
                                <td class="text-center">4 JP</td>
                                <td class="text-center text-success fw-bold">Kompeten</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr class="fw-bold bg-light" style="border-top: 2px solid #0c4a6e;">
                        <td colspan="3" class="text-end">TOTAL DURASI PELATIHAN:</td>
                        <td class="text-center"><?php echo $totalHours; ?> JP</td>
                        <td class="text-center text-success">KOMPETEN</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="pt-3 border-top mt-auto">
            <div class="row align-items-end justify-content-between">
                <div class="col-5 text-center">
                    <div class="text-muted small mb-1">Ditetapkan di Bandung, <?php echo date('d F Y', strtotime($certData['issued_at'] ?? 'now')); ?></div>
                    <div class="small fw-bold text-dark">Pimpinan LPK Mitra Cipta Mandiri</div>
                    <div class="mt-4 border-top border-2 border-dark mx-auto" style="width: 150px;"></div>
                    <div class="small text-muted mt-1">Mitra Cipta Mandiri</div>
                </div>
                <div class="col-5 text-center">
                    <div class="text-muted small mb-1">Asesor / Instruktur Penilai</div>
                    <div class="small fw-bold text-dark"><?php echo htmlspecialchars($instructor['name'] ?? 'Tim Penguji & Asesor'); ?></div>
                    <div class="mt-4 border-top border-2 border-dark mx-auto" style="width: 150px;"></div>
                    <div class="small text-muted mt-1">NIP/Reg: <?php echo htmlspecialchars($instructor['specialization'] ?? 'Asesor Bersertifikasi'); ?></div>
                </div>
            </div>
        </div>
    </div>
</div>
