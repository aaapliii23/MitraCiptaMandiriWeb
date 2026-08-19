<!-- ==================== HALAMAN 1: DEPAN ==================== -->
<div class="cert-page page-front">
    <div class="cert-border text-center">
        <div>
            <div class="d-flex justify-content-center align-items-center gap-2 mb-1">
                <img src="../assets/img/logo.png" alt="MCM Logo" style="height: 58px;">
                <div class="text-start ps-2 border-start border-2 border-dark" style="height: 48px; display: flex; flex-direction: column; justify-content: center;">
                    <span class="fw-bold text-dark cert-title-cinzel" style="font-size: 1.05rem; line-height: 1.1;">MITRA CIPTA MANDIRI</span>
                    <span class="text-muted" style="font-size: 0.65rem; letter-spacing: 1px;">LEMBAGA PELATIHAN KERJA VOKASI</span>
                </div>
            </div>
            
            <div class="cert-ribbon mt-3 mb-1">SERTIFIKAT KOMPETENSI</div>
            <div class="text-muted small fw-semibold">Nomor Registrasi: <?php echo htmlspecialchars($certData['cert_number']); ?></div>
        </div>

        <div class="my-auto portrait-spacer">
            <p class="text-muted small mb-1 text-uppercase" style="letter-spacing: 1.5px;">Diberikan Kepada:</p>
            <div class="cert-recipient-name"><?php echo htmlspecialchars($user['name']); ?></div>
            <p class="text-muted small mt-1 mb-3"><?php echo htmlspecialchars($user['email']); ?></p>

            <p class="small text-secondary mb-1">Telah mengikuti dan dinyatakan <strong class="text-dark">LULUS / KOMPETEN</strong> pada program pelatihan:</p>
            <h3 class="fw-bold cert-class-name"><?php echo htmlspecialchars($class['name']); ?></h3>
            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 fw-bold" style="font-size: 0.75rem;">
                Bidang Kejuruan: <?php echo htmlspecialchars($class['category']); ?>
            </span>
        </div>

        <div>
            <div class="row align-items-end justify-content-between pt-3">
                <div class="col-4 text-center">
                    <div class="text-muted small mb-1">Diterbitkan pada</div>
                    <div class="fw-bold small"><?php echo date('d F Y', strtotime($certData['issued_at'] ?? 'now')); ?></div>
                    <div class="mt-4 border-top border-2 border-dark mx-auto" style="width: 140px;"></div>
                    <div class="small fw-bold text-dark mt-1">Pimpinan LPK Mitra Cipta Mandiri</div>
                </div>

                <div class="col-4 d-flex justify-content-center">
                    <div class="cert-seal">
                        <i class="fas fa-award fs-5 mb-1 text-warning"></i>
                        <span>KOMPETEN<br>RESMI</span>
                    </div>
                </div>

                <div class="col-4 text-center">
                    <div class="text-muted small mb-1">Instruktur / Asesor</div>
                    <div class="fw-bold small"><?php echo htmlspecialchars($instructor['name'] ?? 'Tim Asesor LSP MCM'); ?></div>
                    <div class="mt-4 border-top border-2 border-dark mx-auto" style="width: 140px;"></div>
                    <div class="small fw-bold text-dark mt-1"><?php echo htmlspecialchars($instructor['specialization'] ?? 'Asesor Kompetensi'); ?></div>
                </div>
            </div>
        </div>
    </div>
</div>
