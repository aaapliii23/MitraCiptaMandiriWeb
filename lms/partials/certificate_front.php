<!-- ==================== HALAMAN 1: DEPAN (Great Learning style — MCM navy/gold) ==================== -->
<?php
$issuedRaw = $certData['issued_at'] ?? date('Y-m-d H:i:s');
$issuedFmt = date('d F Y', strtotime($issuedRaw));
$regNumber = $certData['cert_number'] ?? '-';
$verifyUrlAbs = $verifyUrl ?? '';
if (empty($verifyUrlAbs) && !empty($certData['verify_token'])) {
    $schemeFb = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $hostFb = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $scriptDirFb = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    $appBaseFb = '';
    if ($scriptDirFb !== '/' && $scriptDirFb !== '.' && $scriptDirFb !== '') {
        $appBaseFb = rtrim(str_replace('\\', '/', dirname($scriptDirFb)), '/');
        if ($appBaseFb === '/' || $appBaseFb === '.' || $appBaseFb === '\\') $appBaseFb = '';
    }
    $verifyBaseFb = $schemeFb . '://' . $hostFb . ($appBaseFb ? $appBaseFb : '');
    $verifyUrlAbs = $verifyBaseFb . '/pages/verify_certificate.php?token=' . urlencode($certData['verify_token']);
}
$instructorName = $instructor['name'] ?? 'Tim Asesor LSP MCM';
$instructorRole = $instructor['specialization'] ?? 'Asesor Kompetensi';
?>
<div class="cert-page page-front">
    <!-- Guilloche watermark -->
    <div class="cert-guilloche" aria-hidden="true">
        <svg viewBox="0 0 1000 700" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
            <defs>
                <pattern id="guillochePat" width="220" height="110" patternUnits="userSpaceOnUse" patternTransform="rotate(-8)">
                    <g fill="none" stroke="#0c4a6e" stroke-width="0.7" opacity="0.9">
                        <path d="M -10 20 Q 55 0 110 20 T 230 20" />
                        <path d="M -10 42 Q 55 22 110 42 T 230 42" opacity="0.7"/>
                        <path d="M -10 64 Q 55 44 110 64 T 230 64" opacity="0.55"/>
                        <path d="M -10 86 Q 55 66 110 86 T 230 86" opacity="0.4"/>
                    </g>
                    <g fill="none" stroke="#d4af37" stroke-width="0.45" opacity="0.9">
                        <path d="M -10 31 Q 55 12 110 31 T 230 31" />
                        <path d="M -10 75 Q 55 56 110 75 T 230 75" opacity="0.6"/>
                    </g>
                </pattern>
            </defs>
            <rect width="100%" height="52%" fill="url(#guillochePat)" />
            <rect y="52%" width="100%" height="48%" fill="url(#guillochePat)" opacity="0.35" />
        </svg>
    </div>

    <!-- Right vertical ribbon navy + gold notch -->
    <div class="cert-ribbon-v" aria-hidden="true"></div>

    <!-- Circular verified seal overlapping ribbon -->
    <div class="cert-seal-badge" aria-hidden="true">
        <svg class="seal-svg" viewBox="0 0 150 150" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Terverifikasi Sertifikat Resmi">
            <!-- outer double circle -->
            <circle cx="75" cy="75" r="71" fill="#ffffff" stroke="#0c4a6e" stroke-width="2.8"/>
            <circle cx="75" cy="75" r="65" fill="none" stroke="#d4af37" stroke-width="1.4" />
            <circle cx="75" cy="75" r="62" fill="none" stroke="#0c4a6e" stroke-width="0.7" opacity="0.85"/>
            <!-- curved text paths -->
            <defs>
                <path id="sealTopArc" d="M 22 75 A 53 53 0 0 1 128 75" />
                <path id="sealBotArc" d="M 22 75 A 53 53 0 0 0 128 75" />
            </defs>
            <text fill="#0c4a6e" font-size="9.2" font-weight="800" letter-spacing="1.6" font-family="'Plus Jakarta Sans', sans-serif">
                <textPath href="#sealTopArc" startOffset="50%" text-anchor="middle" dominant-baseline="middle">TERVERIFIKASI</textPath>
            </text>
            <text fill="#0c4a6e" font-size="7.6" font-weight="800" letter-spacing="1.2" font-family="'Plus Jakarta Sans', sans-serif">
                <textPath href="#sealBotArc" startOffset="50%" text-anchor="middle">SERTIFIKAT RESMI</textPath>
            </text>
            <!-- small stars left/right -->
            <text x="18" y="76" text-anchor="middle" font-size="7" fill="#d4af37">★</text>
            <text x="132" y="76" text-anchor="middle" font-size="7" fill="#d4af37">★</text>
        </svg>
        <div class="seal-center-logo">
            <img src="../assets/img/logo.png" alt="MCM" loading="lazy">
        </div>
    </div>

    <div class="cert-border cert-front-border">
        <!-- Top brand -->
        <div class="cert-front-header">
            <div class="cert-brand">
                <img src="../assets/img/logo.png" alt="MCM Logo">
                <div class="cert-brand-text">
                    <span class="brand-title">MITRA CIPTA MANDIRI</span>
                    <span class="brand-sub">Lembaga Pelatihan Kerja Vokasi</span>
                </div>
            </div>
        </div>

        <!-- Title -->
        <div>
            <h1 class="cert-front-title cert-title-cinzel">SERTIFIKAT KOMPETENSI</h1>
            <div class="cert-front-reg">Nomor Registrasi: <strong><?php echo htmlspecialchars($regNumber); ?></strong></div>
        </div>

        <!-- Body: presented to -->
        <div class="cert-front-body">
            <div class="cert-presented-label">Diberikan kepada</div>
            <div class="cert-recipient-name"><?php echo htmlspecialchars($user['name']); ?></div>

            <div class="cert-desc">Telah berhasil menyelesaikan program pelatihan dan dinyatakan <strong>LULUS / KOMPETEN</strong> pada program:</div>
            <div class="cert-class-name"><?php echo htmlspecialchars($class['name']); ?></div>
            <span class="cert-category-badge"><i class="fas fa-award" style="color:var(--cert-gold);"></i> Bidang Kejuruan: <?php echo htmlspecialchars($class['category']); ?></span>
        </div>

        <!-- Bottom: signatures + meta + QR -->
        <div class="cert-front-footer">
            <div class="cert-sign cert-sign-left" style="text-align:center; display:flex; flex-direction:column; align-items:center; justify-content:flex-end;">
                <div class="cert-issued-label" style="margin-bottom:4px;"><i class="fas fa-map-marker-alt me-1"></i>Alamat Lembaga</div>
                <div class="small fw-bold text-dark" style="line-height:1.5; font-size:0.68rem; max-width:210px;"><?php echo nl2br(htmlspecialchars(mcm_setting('admin_address', 'Jl. Khp Hasan Mustopa No.57, Neglasari, Kec. Cibeunying Kaler, Kota Bandung, Jawa Barat 40124'))); ?></div>
            </div>

            <div class="cert-center-meta">
                <div class="cert-issued-label">Diterbitkan oleh</div>
                <div class="cert-issued-org">Mitra Cipta Mandiri</div>
                <div class="cert-issued-date"><?php echo htmlspecialchars($issuedFmt); ?></div>
            </div>

            <div class="cert-qr-wrap">
                <?php if (!empty($verifyUrlAbs)): ?>
                <a href="<?php echo htmlspecialchars($verifyUrlAbs); ?>" target="_blank" rel="noopener" class="cert-qr-link" title="Klik atau scan untuk verifikasi sertifikat">
                    <div id="certQr" data-verify-url="<?php echo htmlspecialchars($verifyUrlAbs); ?>"></div>
                </a>
                <div class="cert-qr-caption">Scan untuk verifikasi</div>
                <?php else: ?>
                <div class="cert-qr-link" style="opacity:0.55;pointer-events:none;">
                    <div id="certQr" style="width:88px;height:88px;display:flex;align-items:center;justify-content:center;color:#94a3b8;font-size:0.62rem;font-weight:700;text-align:center;">QR<br>verifikasi<br>tidak tersedia</div>
                </div>
                <div class="cert-qr-caption" style="opacity:0.6;">Verifikasi QR</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($verifyUrlAbs)): ?>
<script src="../assets/js/qrcode.min.js"></script>
<script>
// ponytail: client-side QR via qrcode.js (single file, no composer). Server-side endroid/qr-code bisa dipakai nanti untuk resolusi cetak lebih tinggi.
(function(){
    var el = document.getElementById('certQr');
    if (!el) return;
    var url = el.getAttribute('data-verify-url') || <?php echo json_encode($verifyUrlAbs); ?>;
    if (!url) return;
    function gen(){
        if (typeof QRCode === 'undefined') return;
        el.innerHTML = '';
        new QRCode(el, {
            text: url,
            width: 88,
            height: 88,
            colorDark: '#0c4a6e',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.M
        });
        // enforce size for print
        var c = el.querySelector('canvas');
        var img = el.querySelector('img');
        if (c) { c.style.width='88px'; c.style.height='88px'; }
        if (img) { img.style.width='88px'; img.style.height='88px'; }
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', gen);
    else gen();
})();
</script>
<?php endif; ?>
