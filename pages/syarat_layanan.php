<?php
require_once '../config/database.php';
$waNumber = mcm_setting('contact_whatsapp', '628123456789');
$waClean = preg_replace('/[^0-9]/', '', $waNumber);
$email = mcm_setting('contact_email', 'info@mitraciptamandiri.com');
$address = mcm_setting('admin_address', 'Jl. Khp Hasan Mustopa No.57, Neglasari, Kec. Cibeunying Kaler, Kota Bandung, Jawa Barat 40124');
include '../includes/header.php';

$clauses = [
    [
        'id' => 'tentang-mcm',
        'icon' => 'fas fa-university',
        'title' => '1. Tentang Mitra Cipta Mandiri (MCM)',
        'body' => '<p class="text-muted mb-0" style="line-height: 1.8; font-size: 0.95rem;">Lembaga Pelatihan Kerja (LPK) Mitra Cipta Mandiri adalah lembaga pendidikan dan pelatihan vokasi resmi di bawah naungan Yayasan Mitra Cipta Mandiri. MCM berizin resmi dari Kemenkumham, Dinas Tenaga Kerja Kota Bandung, Disnaker Provinsi Jawa Barat, serta Kementerian Ketenagakerjaan RI dan telah terakreditasi sejak tahun 2023. Layanan mencakup pendaftaran pelatihan vokasi, modul online Learning Management System (LMS), ujian kompetensi, penerbitan sertifikat resmi, dan konsultasi karir.</p>'
    ],
    [
        'id' => 'akun-pengguna',
        'icon' => 'fas fa-user-shield',
        'title' => '2. Akun Pengguna & Keamanan Data',
        'body' => '<p class="text-muted mb-2" style="line-height: 1.8; font-size: 0.95rem;">Setiap peserta diwajibkan mendaftarkan akun dengan data identitas yang valid dan sesuai dengan kartu identitas resmi (KTP/SIM/Paspor) guna keperluan penerbitan sertifikat.</p><ul class="text-muted ps-3 mb-0" style="line-height: 1.8; font-size: 0.95rem;"><li>Satu alamat email hanya berlaku untuk satu akun peserta.</li><li>Peserta bertanggung jawab penuh menjaga kerahasiaan kata sandi dan seluruh aktivitas pada akun tersebut.</li><li>Jika terjadi indikasi penyalahgunaan atau akses tanpa izin, peserta wajib segera memberitahukan tim pengelola MCM.</li></ul>'
    ],
    [
        'id' => 'pembayaran',
        'icon' => 'fas fa-credit-card',
        'title' => '3. Pembayaran & Pendaftaran Kursus',
        'body' => '<p class="text-muted mb-0" style="line-height: 1.8; font-size: 0.95rem;">Akses materi belajar LMS akan diberikan otomatis dan instan begitu pembayaran dikonfirmasi lunas oleh sistem. Pembayaran sah hanya dilakukan melalui payment gateway resmi yang disediakan di website MCM (QRIS, Virtual Account, Transfer Bank resmi, dan kanal terafiliasi). MCM tidak bertanggung jawab atas segala transaksi di luar kanal pembayaran resmi tersebut.</p>'
    ],
    [
        'id' => 'refund',
        'icon' => 'fas fa-undo-alt',
        'title' => '4. Kebijakan Pengembalian Dana (Refund)',
        'body' => '<p class="text-muted mb-3" style="line-height: 1.8; font-size: 0.95rem;">Kami berkomitmen memberikan kepastian dan perlindungan bagi peserta melalui kebijakan refund yang transparan:</p><div class="p-3 rounded-3 bg-light border mb-0 small text-muted" style="line-height: 1.7;"><div class="d-flex align-items-center gap-2 mb-1 fw-bold text-dark"><i class="fas fa-shield-check text-success"></i> Ketentuan Garansi Refund:</div>Permohonan refund dapat diajukan dalam waktu maksimal <strong>3 (tiga) hari kerja</strong> sejak pembayaran terkonfirmasi, dengan syarat mutlak peserta <strong>belum membuka atau mengakses materi pelatihan sama sekali</strong>. Apabila peserta telah mengakses materi pertama atau periode 3 hari kerja telah lewat, maka pembayaran bersifat final dan tidak dapat ditarik kembali. Proses pengembalian dana yang disetujui memakan waktu 7–14 hari kerja.</div>'
    ],
    [
        'id' => 'sertifikat',
        'icon' => 'fas fa-award',
        'title' => '5. Penerbitan Sertifikat Kelulusan',
        'body' => '<p class="text-muted mb-2" style="line-height: 1.8; font-size: 0.95rem;">Sertifikat kompetensi resmi diterbitkan secara otomatis dan dapat diunduh langsung melalui dashboard LMS peserta dengan ketentuan:</p><ul class="text-muted ps-3 mb-0" style="line-height: 1.8; font-size: 0.95rem;"><li>Peserta telah menyelesaikan seluruh materi belajar (100% progress).</li><li>Peserta telah lulus seluruh evaluasi kuis dengan skor minimal 70% atau menjawab seluruh soal dengan benar sesuai standar pelatihan.</li><li>Setiap sertifikat memiliki <strong>Nomor Registrasi Unik</strong> dan <strong>QR Code Terverifikasi</strong> yang dapat dicek keasliannya secara publik di portal verifikasi MCM.</li><li>MCM berhak mencabut sertifikat jika di kemudian hari ditemukan adanya pemalsuan data identitas atau kecurangan.</li></ul>'
    ],
    [
        'id' => 'larangan',
        'icon' => 'fas fa-ban',
        'title' => '6. Larangan & Integritas Akademik',
        'body' => '<p class="text-muted mb-2" style="line-height: 1.8; font-size: 0.95rem;">Dalam memanfaatkan layanan dan sistem LMS MCM, pengguna dilarang keras untuk:</p><ul class="text-muted ps-3 mb-0" style="line-height: 1.8; font-size: 0.95rem;"><li>Merekam, mengunduh secara ilegal, membagikan, atau memperjualbelikan materi video, modul, dan soal ujian ke pihak luar tanpa izin tertulis MCM.</li><li>Meminjamkan, menyewakan, atau membagikan kredensial akun kepada orang lain.</li><li>Melakukan upaya peretasan, scraping otomatis, atau manipulasi data nilai kuis pada sistem LMS.</li><li>Pelanggaran terhadap poin ini dapat berakibat pada pemblokiran akun permanen dan tuntutan hukum hak kekayaan intelektual.</li></ul>'
    ],
    [
        'id' => 'perubahan',
        'icon' => 'fas fa-sync-alt',
        'title' => '7. Pembaruan Kurikulum & Ketentuan',
        'body' => '<p class="text-muted mb-0" style="line-height: 1.8; font-size: 0.95rem;">Guna menjaga relevansi materi pelatihan dengan perkembangan industri, MCM berhak memperbarui kurikulum, materi ajar, maupun syarat dan ketentuan ini dari waktu ke waktu. Pembaruan substansial akan diinformasikan melalui pengumuman di portal LMS atau surat elektronik. Melanjutkan penggunaan layanan setelah perubahan berarti Anda menerima syarat yang diperbarui tersebut.</p>'
    ],
    [
        'id' => 'hukum',
        'icon' => 'fas fa-gavel',
        'title' => '8. Hukum yang Berlaku & Domisili',
        'body' => '<p class="text-muted mb-0" style="line-height: 1.8; font-size: 0.95rem;">Syarat dan ketentuan ini diatur dan ditafsirkan secara penuh berdasarkan hukum Negara Kesatuan Republik Indonesia. Setiap perselisihan yang timbul akan diupayakan untuk diselesaikan terlebih dahulu melalui musyawarah mufakat. Apabila tidak tercapai mufakat, maka para pihak sepakat untuk memilih domisili hukum di Pengadilan Negeri Kota Bandung, Jawa Barat.</p>'
    ],
    [
        'id' => 'kontak',
        'icon' => 'fas fa-envelope-open-text',
        'title' => '9. Saluran Resmi & Informasi Kontak',
        'body' => '<p class="text-muted mb-3" style="line-height: 1.8; font-size: 0.95rem;">Jika Anda memiliki pertanyaan, permohonan klarifikasi, atau keluhan seputar syarat dan ketentuan ini, silakan menghubungi kami melalui:</p><div class="row g-3"><div class="col-sm-6"><div class="p-3 rounded-3 bg-light border"><small class="text-muted d-block mb-1"><i class="fas fa-envelope me-1 text-primary"></i> Email Resmi:</small><a href="mailto:'.htmlspecialchars($email).'" class="fw-bold text-dark text-decoration-none small">'.htmlspecialchars($email).'</a></div></div><div class="col-sm-6"><div class="p-3 rounded-3 bg-light border"><small class="text-muted d-block mb-1"><i class="fab fa-whatsapp me-1 text-success"></i> WhatsApp Konsultasi:</small><span class="fw-bold text-dark small">'.htmlspecialchars($waNumber).'</span></div></div><div class="col-12"><div class="p-3 rounded-3 bg-light border"><small class="text-muted d-block mb-1"><i class="fas fa-map-marker-alt me-1 text-danger"></i> Kantor Lembaga:</small><span class="text-dark small">'.htmlspecialchars($address).'</span></div></div></div>'
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
                            <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Syarat Layanan</li>
                        </ol>
                    </nav>
                    <span class="text-muted small d-none d-sm-inline">Kembali ke halaman sebelumnya</span>
                </div>
            </div>

            <!-- Tab Switcher between Terms & Privacy -->
            <div class="legal-tab-switcher p-1 rounded-pill bg-white border shadow-sm d-flex align-items-center gap-1">
                <a href="syarat_layanan.php" class="legal-tab-btn active">
                    <i class="fas fa-file-contract me-1"></i> Syarat &amp; Ketentuan
                </a>
                <a href="kebijakan_privasi.php" class="legal-tab-btn">
                    <i class="fas fa-user-shield me-1"></i> Kebijakan Privasi
                </a>
            </div>
        </div>

        <!-- Modern Hero Banner -->
        <div class="rounded-4 p-4 p-md-5 mb-4 mb-md-5 text-white position-relative overflow-hidden shadow-sm" style="background: linear-gradient(135deg, #0c4a6e 0%, #0369a1 60%, #0ea5e9 100%);">
            <div class="position-relative z-1" style="max-width: 820px;">
                <span class="badge px-3 py-2 rounded-pill mb-3 fw-bold text-uppercase" style="background: rgba(255,255,255,0.2); backdrop-filter: blur(8px); letter-spacing: 1px; font-size: 0.75rem;">
                    <i class="fas fa-balance-scale me-1"></i> DOKUMEN REGULASI RESMI
                </span>
                <h1 class="fw-bold mb-3 text-white" style="font-size: clamp(1.5rem, 4vw, 2.25rem);">Syarat &amp; Ketentuan Layanan</h1>
                <p class="lead mb-4 text-white text-opacity-90" style="font-size: 1rem; line-height: 1.7;">
                    Panduan resmi mengenai pendaftaran pelatihan vokasi, penggunaan sistem pembelajaran online (LMS), sertifikasi kompetensi, serta hak dan kewajiban peserta di LPK Mitra Cipta Mandiri.
                </p>
                <div class="d-flex flex-wrap gap-2 gap-sm-3 align-items-center pt-2 text-white text-opacity-90 small">
                    <span class="d-inline-flex align-items-center"><i class="far fa-calendar-check me-2 opacity-75"></i> Berlaku: <strong>1 Jan 2025</strong></span>
                    <span class="opacity-50 d-none d-sm-inline">•</span>
                    <span class="d-inline-flex align-items-center"><i class="fas fa-history me-2 opacity-75"></i> Diperbarui: <strong>1 Sep 2025</strong></span>
                    <span class="opacity-50 d-none d-sm-inline">•</span>
                    <span class="d-inline-flex align-items-center"><i class="far fa-clock me-2 opacity-75"></i> Waktu Baca: <strong>~4 Menit</strong></span>
                </div>
            </div>
            <div class="position-absolute end-0 top-0 translate-middle-y me-n4 mt-n4 d-none d-lg-block pointer-events-none" style="opacity: 0.08;">
                <i class="fas fa-file-contract text-white" style="font-size: 18rem;"></i>
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
                            <i class="fas fa-headset fs-4"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Ada Pertanyaan?</h6>
                        <p class="text-muted small mb-3">Tim administrasi &amp; konselor MCM siap membantu menjawab pertanyaan Anda.</p>
                        <?php if (!empty($waClean)): ?>
                        <a href="https://wa.me/<?php echo htmlspecialchars($waClean); ?>?text=Halo%20Admin%20MCM,%20saya%20ingin%20bertanya%20terkait%20syarat%20dan%20ketentuan%20layanan" target="_blank" class="btn btn-sm btn-primary rounded-pill w-100 py-2 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2 mb-2">
                            <i class="fab fa-whatsapp fs-6"></i> Hubungi WhatsApp
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
                <div class="card border-0 shadow-sm rounded-4 p-3 p-md-4 mb-4 bg-white border-start border-4 border-primary">
                    <div class="d-flex align-items-start gap-3">
                        <div class="flex-shrink-0 text-primary pt-1">
                            <i class="fas fa-info-circle fs-4"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-1">Pernyataan Persetujuan Pengguna</h6>
                            <p class="text-muted mb-0" style="line-height: 1.7; font-size: 0.95rem;">
                                Dengan mendaftar, mengakses materi, atau melakukan transaksi di platform <strong>Mitra Cipta Mandiri (MCM)</strong>, Anda menyatakan secara sadar telah membaca, memahami, dan menyetujui seluruh syarat &amp; ketentuan di bawah ini tanpa paksaan dari pihak mana pun.
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
                    <p class="text-muted small mb-2">Dokumen ini diterbitkan secara sah oleh Manajemen Lembaga Pelatihan Kerja</p>
                    <h6 class="fw-bold text-dark mb-1">LPK MITRA CIPTA MANDIRI (MCM)</h6>
                    <small class="text-muted">Terakreditasi Lembaga Pelatihan Kerja • Terdaftar Resmi di Disnaker &amp; Kemnaker RI</small>
                    <div class="mt-3">
                        <a href="kebijakan_privasi.php" class="btn btn-sm btn-light rounded-pill px-4 border text-primary fw-semibold">
                            Buka Kebijakan Privasi MCM <i class="fas fa-arrow-right ms-1"></i>
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
