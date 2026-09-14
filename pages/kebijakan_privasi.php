<?php
require_once '../config/database.php';
$waNumber = mcm_setting('contact_whatsapp', '628123456789');
$waClean = preg_replace('/[^0-9]/', '', $waNumber);
$email = mcm_setting('contact_email', 'info@mitraciptamandiri.com');
$address = mcm_setting('admin_address', 'Jl. Khp Hasan Mustopa No.57, Neglasari, Kec. Cibeunying Kaler, Kota Bandung, Jawa Barat 40124');
include '../includes/header.php';

$clauses = [
    [
        'id' => 'data-kumpul',
        'icon' => 'fas fa-database',
        'title' => '1. Data yang Kami Kumpulkan',
        'body' => '<p class="text-muted mb-2" style="line-height: 1.8; font-size: 0.95rem;">Kami hanya mengumpulkan informasi yang relevan dan dibutuhkan untuk menunjang aktivitas pelatihan Anda, meliputi:</p><ul class="text-muted ps-3 mb-0" style="line-height: 1.8; font-size: 0.95rem;"><li><strong>Informasi Identitas:</strong> Nama lengkap (sesuai KTP untuk sertifikat), alamat email aktif, nomor WhatsApp.</li><li><strong>Informasi Pembelajaran:</strong> Riwayat modul yang telah diakses, rekaman penyelesaian kuis, nilai evaluasi, dan status sertifikat.</li><li><strong>Informasi Transaksi:</strong> Nomor pesanan, waktu transaksi, dan status pembayaran. Seluruh informasi sensitif kartu bank diproses langsung oleh payment gateway resmi berstandar PCI-DSS tanpa disimpan di server MCM.</li></ul>'
    ],
    [
        'id' => 'penggunaan-data',
        'icon' => 'fas fa-tasks',
        'title' => '2. Tujuan Penggunaan Informasi',
        'body' => '<p class="text-muted mb-2" style="line-height: 1.8; font-size: 0.95rem;">Data Anda dipergunakan untuk keperluan:</p><ul class="text-muted ps-3 mb-0" style="line-height: 1.8; font-size: 0.95rem;"><li>Penyediaan akses ke materi online dan pengoperasian dashboard LMS peserta.</li><li>Penerbitan dan verifikasi keaslian sertifikat kompetensi pelatihan resmi.</li><li>Pengiriman notifikasi status pembayaran dan reminder belajar via WhatsApp resmi MCM.</li><li>Peningkatan kualitas materi serta layanan pelanggan.</li></ul>'
    ],
    [
        'id' => 'pihak-ketiga',
        'icon' => 'fas fa-handshake',
        'title' => '3. Berbagi Data dengan Pihak Ketiga',
        'body' => '<p class="text-muted mb-0" style="line-height: 1.8; font-size: 0.95rem;">MCM memiliki komitmen ketat: <strong>tidak pernah menjual, menyewakan, atau memperdagangkan data pribadi Anda kepada pihak mana pun</strong>. Pertukaran data hanya dilakukan secara terbatas dengan mitra teknis terpercaya (seperti gateway pembayaran berizin Bank Indonesia dan penyedia infrastruktur cloud berstandar keamanan tinggi) semata-mata untuk memproses transaksi dan menjaga ketersediaan layanan.</p>'
    ],
    [
        'id' => 'keamanan-data',
        'icon' => 'fas fa-lock',
        'title' => '4. Protokol Keamanan Sistem',
        'body' => '<p class="text-muted mb-0" style="line-height: 1.8; font-size: 0.95rem;">Kami menerapkan standar keamanan berlapis untuk melindungi data Anda dari akses tanpa izin. Ini mencakup enkripsi kata sandi menggunakan algoritma <code>bcrypt</code>, pengamanan koneksi internet via protokol SSL/TLS (HTTPS), perlindungan token anti-CSRF pada setiap formulir, serta pembatasan akses data internal hanya kepada staf administrasi yang berwenang.</p>'
    ],
    [
        'id' => 'cookie-sesi',
        'icon' => 'fas fa-cookie-bite',
        'title' => '5. Penggunaan Cookie & Sesi',
        'body' => '<p class="text-muted mb-0" style="line-height: 1.8; font-size: 0.95rem;">Website MCM menggunakan cookie sesi yang aman dan bersifat sementara. Cookie ini bertujuan untuk menjaga status login Anda saat berpindah halaman dan mengingat sesi obrolan konsultasi. Kami tidak menggunakan cookie pelacak lintas situs untuk keperluan periklanan pihak ketiga.</p>'
    ],
    [
        'id' => 'hak-pengguna',
        'icon' => 'fas fa-user-cog',
        'title' => '6. Hak Pengguna atas Data Pribadi',
        'body' => '<p class="text-muted mb-2" style="line-height: 1.8; font-size: 0.95rem;">Sebagai pemilik data pribadi, Anda memiliki hak penuh untuk:</p><ul class="text-muted ps-3 mb-0" style="line-height: 1.8; font-size: 0.95rem;"><li>Memeriksa atau meminta salinan data akun Anda yang tersimpan di sistem kami.</li><li>Mengajukan perbaikan terhadap nama, nomor telepon, atau data profil yang keliru.</li><li>Mengajukan permohonan penonaktifan atau penghapusan akun jika Anda sudah tidak lagi menggunakan layanan MCM.</li></ul>'
    ],
    [
        'id' => 'retensi-data',
        'icon' => 'fas fa-history',
        'title' => '7. Periode Penyimpanan (Retensi Data)',
        'body' => '<p class="text-muted mb-0" style="line-height: 1.8; font-size: 0.95rem;">Data akun akan tetap disimpan selama akun Anda aktif. Catatan sertifikat kelulusan dan nomor registrasi akan disimpan secara berkelanjutan agar peserta dapat melakukan verifikasi keaslian sertifikat kapan pun di masa mendatang sesuai standar audit lembaga vokasi.</p>'
    ],
    [
        'id' => 'kontak',
        'icon' => 'fas fa-envelope-open-text',
        'title' => '8. Kontak Petugas Privasi Data',
        'body' => '<p class="text-muted mb-3" style="line-height: 1.8; font-size: 0.95rem;">Apabila Anda ingin menggunakan hak atas data pribadi atau memiliki pertanyaan seputar kebijakan privasi ini, silakan menghubungi kami melalui:</p><div class="row g-3"><div class="col-sm-6"><div class="p-3 rounded-3 bg-light border"><small class="text-muted d-block mb-1"><i class="fas fa-envelope me-1 text-primary"></i> Email Resmi:</small><a href="mailto:'.htmlspecialchars($email).'" class="fw-bold text-dark text-decoration-none small">'.htmlspecialchars($email).'</a></div></div><div class="col-sm-6"><div class="p-3 rounded-3 bg-light border"><small class="text-muted d-block mb-1"><i class="fab fa-whatsapp me-1 text-success"></i> WhatsApp Pengaduan:</small><span class="fw-bold text-dark small">'.htmlspecialchars($waNumber).'</span></div></div><div class="col-12"><div class="p-3 rounded-3 bg-light border"><small class="text-muted d-block mb-1"><i class="fas fa-map-marker-alt me-1 text-danger"></i> Alamat Kantor Lembaga:</small><span class="text-dark small">'.htmlspecialchars($address).'</span></div></div></div>'
    ]
];
?>

<section class="legal-section section-padding" style="padding-top: 100px; padding-bottom: 60px; min-height: 85vh; background: #f8fafc;">
    <div class="container py-2 py-md-4">
        <!-- Top Navigation Bar & Action Row -->
        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <a href="javascript:void(0)" onclick="if(window.history.length>1&&document.referrer){history.back();}else{window.location.href='../index.php';}" class="btn-back-circle shadow-premium btn-premium" title="Kembali">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 small">
                            <li class="breadcrumb-item"><a href="../index.php" class="text-decoration-none text-muted"><i class="fas fa-home me-1"></i>Beranda</a></li>
                            <li class="breadcrumb-item text-muted">Legalitas</li>
                            <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Kebijakan Privasi</li>
                        </ol>
                    </nav>
                    <span class="text-muted small d-none d-sm-inline">Kembali ke halaman sebelumnya</span>
                </div>
            </div>

            <!-- Tab Switcher between Terms & Privacy -->
            <div class="legal-tab-switcher p-1 rounded-pill bg-white border shadow-sm d-flex align-items-center gap-1">
                <a href="syarat_layanan.php" class="legal-tab-btn">
                    <i class="fas fa-file-contract me-1"></i> Syarat &amp; Ketentuan
                </a>
                <a href="kebijakan_privasi.php" class="legal-tab-btn active">
                    <i class="fas fa-user-shield me-1"></i> Kebijakan Privasi
                </a>
            </div>
        </div>

        <!-- Modern Hero Banner -->
        <div class="rounded-4 p-4 p-md-5 mb-4 mb-md-5 text-white position-relative overflow-hidden shadow-sm" style="background: linear-gradient(135deg, #0c4a6e 0%, #0369a1 60%, #0ea5e9 100%);">
            <div class="position-relative z-1" style="max-width: 820px;">
                <span class="badge px-3 py-2 rounded-pill mb-3 fw-bold text-uppercase" style="background: rgba(255,255,255,0.2); backdrop-filter: blur(8px); letter-spacing: 1px; font-size: 0.75rem;">
                    <i class="fas fa-user-lock me-1"></i> PERLINDUNGAN DATA PRIBADI
                </span>
                <h1 class="fw-bold mb-3 text-white" style="font-size: clamp(1.5rem, 4vw, 2.25rem);">Kebijakan Privasi</h1>
                <p class="lead mb-4 text-white text-opacity-90" style="font-size: 1rem; line-height: 1.7;">
                    Komitmen penuh LPK Mitra Cipta Mandiri dalam menjaga kerahasiaan, mengelola, serta melindungi data dan hak privasi Anda saat berinteraksi dengan layanan kami.
                </p>
                <div class="d-flex flex-wrap gap-2 gap-sm-3 align-items-center pt-2 text-white text-opacity-90 small">
                    <!-- <span class="d-inline-flex align-items-center"><i class="far fa-calendar-check me-2 opacity-75"></i> Berlaku: <strong>1 Jan 2025</strong></span>
                    <span class="opacity-50 d-none d-sm-inline">•</span>
                    <span class="d-inline-flex align-items-center"><i class="fas fa-history me-2 opacity-75"></i> Diperbarui: <strong>1 Sep 2025</strong></span>
                    <span class="opacity-50 d-none d-sm-inline">•</span> -->
                    <!-- <span class="d-inline-flex align-items-center"><i class="far fa-clock me-2 opacity-75"></i> Waktu Baca: <strong>~3 Menit</strong></span> -->
                </div>
            </div>
            <div class="position-absolute end-0 top-0 translate-middle-y me-n4 mt-n4 d-none d-lg-block pointer-events-none" style="opacity: 0.08;">
                <i class="fas fa-shield-alt text-white" style="font-size: 18rem;"></i>
            </div>
        </div>

        <!-- Main Layout: 2 Columns -->
        <div class="row g-4 g-xl-5">
            <!-- Left Column: Sticky Table of Contents & Quick Help -->
            <div class="col-lg-4 col-xl-3">
                <div class="sticky-top" style="top: 100px; z-index: 10;">
                    <!-- Table of Contents Card (Collapsible on mobile) -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden bg-white">
                        <div class="card-header bg-white border-bottom py-3 px-3 px-md-4 d-flex align-items-center justify-content-between cursor-pointer" data-bs-toggle="collapse" data-bs-target="#legalTocCollapse" role="button" aria-expanded="false" aria-controls="legalTocCollapse">
                            <h6 class="fw-bold mb-0 text-dark d-flex align-items-center">
                                <i class="fas fa-list-ul text-primary me-2"></i> Daftar Isi
                            </h6>
                            <span class="badge bg-primary bg-opacity-10 text-primary border rounded-pill d-lg-none small">
                                Menu <i class="fas fa-chevron-down ms-1"></i>
                            </span>
                        </div>
                        <div class="collapse d-lg-block" id="legalTocCollapse">
                            <div class="p-2" id="legalTocList">
                                <?php foreach ($clauses as $idx => $c): ?>
                                <a href="#<?php echo htmlspecialchars($c['id']); ?>" class="legal-toc-link <?php echo $idx === 0 ? 'active-toc' : ''; ?>">
                                    <span><?php echo htmlspecialchars($c['title']); ?></span>
                                    <i class="fas fa-chevron-right small opacity-50"></i>
                                </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Support Card -->
                    <div class="card border-0 shadow-sm rounded-4 p-4 text-center bg-white d-none d-lg-block">
                        <div class="mx-auto d-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 50px; height: 50px; background: rgba(14, 165, 233, 0.1); color: #0284c7;">
                            <i class="fas fa-user-lock fs-4"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Privasi Terjamin</h6>
                        <p class="text-muted small mb-3">MCM tidak pernah memperjualbelikan data Anda kepada pihak ketiga mana pun.</p>
                        <?php if (!empty($waClean)): ?>
                        <a href="https://wa.me/<?php echo htmlspecialchars($waClean); ?>?text=Halo%20Admin%20MCM,%20saya%20ingin%20bertanya%20terkait%20kebijakan%20privasi%20data" target="_blank" class="btn btn-sm btn-primary rounded-pill w-100 py-2 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2 mb-2">
                            <i class="fab fa-whatsapp fs-6"></i> Tanya Admin Privasi
                        </a>
                        <?php endif; ?>
                        <button type="button" onclick="window.print()" class="btn btn-sm btn-outline-secondary rounded-pill w-100 py-2">
                            <i class="fas fa-print me-1"></i> Cetak Dokumen
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right Column: Detailed Clauses -->
            <div class="col-lg-8 col-xl-9">
                <!-- Summary Notice Box -->
                <div class="card border-0 shadow-sm rounded-4 p-3 p-md-4 mb-4 bg-white border-start border-4 border-info">
                    <div class="d-flex align-items-start gap-3">
                        <div class="flex-shrink-0 text-info pt-1">
                            <i class="fas fa-shield-alt fs-4"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-1">Prinsip Perlindungan Data Pribadi MCM</h6>
                            <p class="text-muted mb-0" style="line-height: 1.7; font-size: 0.95rem;">
                                Mitra Cipta Mandiri (<strong>MCM</strong>) menjamin perlindungan privasi seluruh peserta sesuai dengan regulasi perlindungan data yang berlaku di Indonesia. Data Anda hanya dipergunakan semata-mata untuk kelancaran proses pembelajaran dan penerbitan sertifikat resmi.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Clauses Loop -->
                <?php foreach ($clauses as $c): ?>
                <div class="legal-clause-card" id="<?php echo htmlspecialchars($c['id']); ?>">
                    <div class="d-flex align-items-start gap-3">
                        <div class="legal-clause-icon">
                            <i class="<?php echo htmlspecialchars($c['icon']); ?>"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h2 class="fw-bold mb-2 text-dark" style="font-size: 1.15rem;"><?php echo htmlspecialchars($c['title']); ?></h2>
                            <?php echo $c['body']; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>

                <!-- Official Endorsement Card -->
                <div class="card border-0 rounded-4 p-4 text-center bg-white shadow-sm mt-4">
                    <p class="text-muted small mb-2">Dokumen Kebijakan Privasi ini diterbitkan dan diawasi oleh</p>
                    <h6 class="fw-bold text-dark mb-1">LPK MITRA CIPTA MANDIRI (MCM)</h6>
                    <small class="text-muted">Lembaga Pelatihan Vokasi Terakreditasi • Bandung, Jawa Barat</small>
                    <div class="mt-3">
                        <a href="syarat_layanan.php" class="btn btn-sm btn-light rounded-pill px-4 border text-primary fw-semibold">
                            Buka Syarat &amp; Ketentuan Layanan <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tocLinks = document.querySelectorAll('#legalTocList a');
    const sections = Array.from(tocLinks).map(link => {
        const targetId = link.getAttribute('href').slice(1);
        return document.getElementById(targetId);
    }).filter(el => el !== null);

    function onScroll() {
        const scrollPos = window.scrollY + 140;
        let activeIdx = 0;
        sections.forEach((sec, idx) => {
            if (sec.offsetTop <= scrollPos) {
                activeIdx = idx;
            }
        });
        tocLinks.forEach((link, idx) => {
            if (idx === activeIdx) {
                link.classList.add('active-toc');
            } else {
                link.classList.remove('active-toc');
            }
        });
    }
    window.addEventListener('scroll', onScroll, { passive: true });

    tocLinks.forEach(link => {
        link.addEventListener('click', function() {
            const collapseEl = document.getElementById('legalTocCollapse');
            if (window.innerWidth < 992 && collapseEl && typeof bootstrap !== 'undefined') {
                const bsCollapse = bootstrap.Collapse.getInstance(collapseEl);
                if (bsCollapse) bsCollapse.hide();
            }
        });
    });
});
</script>

<?php include '../includes/footer.php'; ?>
