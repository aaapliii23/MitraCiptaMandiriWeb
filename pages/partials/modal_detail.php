    <!-- Class Detail: Full Professional Dashboard Portal -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content border-0" style="background: #f8fafc;">
                <!-- Header Branding (Consistent with Navbar) -->
                <nav class="navbar navbar-expand bg-white border-bottom py-3 px-4 shadow-sm" style="position: sticky; top: 0; z-index: 100;">
                    <div class="container-fluid">
                        <a class="navbar-brand d-flex align-items-center" href="#">
                            <img src="assets/img/logo.png" alt="MCM Logo" style="height: 35px; width: auto;">
                            <div class="ms-2 ps-2 border-start border-2 border-dark d-flex flex-column justify-content-center" style="height: 30px;">
                                <span class="fw-bold text-dark" style="font-size: 0.7rem; letter-spacing: 1px; line-height: 1.1;">MITRA CIPTA</span>
                                <span class="fw-bold text-dark" style="font-size: 0.7rem; letter-spacing: 1px; line-height: 1.1;">MANDIRI</span>
                            </div>
                        </a>
                        <div class="ms-auto d-flex align-items-center">
                            <span class="text-muted small d-none d-md-block me-3"><i class="fas fa-info-circle me-1"></i> Informasi Lengkap Program Pelatihan</span>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                    </div>
                </nav>

                <div class="container py-4 px-3 px-md-5">
                    <div class="row g-4">
                        <!-- Main Content Area -->
                        <div class="col-lg-8">
                            <!-- Hero Brief -->
                            <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                                <div class="row g-0">
                                    <div class="col-md-5">
                                        <img src="" id="detailModalImage" class="w-100 h-100" style="object-fit: cover; min-height: 250px;" alt="Banner">
                                    </div>
                                    <div class="col-md-7 p-4 p-md-5 d-flex flex-column justify-content-center">
                                        <div class="badge mb-3 p-2 px-3 rounded-pill d-inline-block" style="width: fit-content; background-color: rgba(14, 165, 233, 0.1); color: #0ea5e9;">PROFIL PROGRAM</div>
                                        <h1 class="fw-bold mb-3" id="detailModalTitle" style="color: #0c4a6e;">Nama Kelas</h1>
                                        <p class="text-secondary fs-5" id="detailModalDescShort">Pelatihan vokasi terintegrasi dengan standar industri nasional.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Detailed Tabs Section -->
                            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                                <div class="card-header bg-white p-0">
                                    <ul class="nav nav-pills p-3 gap-2" id="pills-tab" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active fw-bold rounded-pill" id="pills-desc-tab" data-bs-toggle="pill" data-bs-target="#pills-desc" type="button" role="tab" style="--bs-nav-pills-link-active-bg: #0c4a6e;"><i class="fas fa-align-left me-2"></i>Penjelasan</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link fw-bold rounded-pill" id="pills-materi-tab" data-bs-toggle="pill" data-bs-target="#pills-materi" type="button" role="tab" style="color: #0c4a6e;"><i class="fas fa-book me-2"></i>Kurikulum</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link fw-bold rounded-pill" id="pills-terms-tab" data-bs-toggle="pill" data-bs-target="#pills-terms" type="button" role="tab" style="color: #0c4a6e;"><i class="fas fa-shield-alt me-2"></i>Ketentuan</button>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-body p-4 p-md-5">
                                    <div class="tab-content" id="pills-tabContent">
                                        <!-- Penjelasan Tab -->
                                        <div class="tab-pane fade show active" id="pills-desc" role="tabpanel">
                                            <div class="animate-content">
                                                <h4 class="fw-bold mb-4">Visi & Tujuan Program</h4>
                                                <p class="text-secondary lh-lg fs-5 mb-5" id="detailModalDesc">
                                                    Deskripsi lengkap mengenai program pelatihan akan dimuat di sini secara rinci dan mendalam.
                                                </p>
                                                
                                                <div class="row g-4 mb-5">
                                                    <div class="col-md-6">
                                                        <div class="p-4 rounded-4 bg-white border shadow-sm h-100">
                                                            <div class="bg-primary bg-opacity-10 p-2 rounded-3 d-inline-block mb-3"><i class="fas fa-bullseye text-primary"></i></div>
                                                            <h6 class="fw-bold">Target Peserta</h6>
                                                            <p class="small text-muted mb-0">Program ini dirancang bagi pemula, praktisi, maupun profesional yang ingin memperdalam keahlian teknis secara sistematis.</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="p-4 rounded-4 bg-white border shadow-sm h-100">
                                                            <div class="bg-primary bg-opacity-10 p-2 rounded-3 d-inline-block mb-3"><i class="fas fa-rocket text-primary"></i></div>
                                                            <h6 class="fw-bold">Output Kompetensi</h6>
                                                            <p class="small text-muted mb-0">Lulusan akan memiliki standar keahlian industri dan sertifikasi resmi yang diakui untuk menunjang karir atau bisnis mandiri.</p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <h5 class="fw-bold mb-4">Kenapa Harus Bergabung?</h5>
                                                <div class="row g-3">
                                                    <div class="col-sm-6">
                                                        <div class="d-flex p-3 rounded-4 bg-light align-items-center hover-lift transition-all">
                                                            <div class="bg-success bg-opacity-20 p-2 rounded-circle me-3"><i class="fas fa-check text-success small"></i></div>
                                                            <span class="fw-bold small">Kurikulum Berbasis Industri</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="d-flex p-3 rounded-4 bg-light align-items-center hover-lift transition-all">
                                                            <div class="bg-success bg-opacity-20 p-2 rounded-circle me-3"><i class="fas fa-check text-success small"></i></div>
                                                            <span class="fw-bold small">Peralatan Standar Profesional</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="d-flex p-3 rounded-4 bg-light align-items-center hover-lift transition-all">
                                                            <div class="bg-success bg-opacity-20 p-2 rounded-circle me-3"><i class="fas fa-check text-success small"></i></div>
                                                            <span class="fw-bold small">Pendampingan Mentor Ahli</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="d-flex p-3 rounded-4 bg-light align-items-center hover-lift transition-all">
                                                            <div class="bg-success bg-opacity-20 p-2 rounded-circle me-3"><i class="fas fa-check text-success small"></i></div>
                                                            <span class="fw-bold small">Akses Jaringan Alumni MCM</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Materi Tab -->
                                        <div class="tab-pane fade" id="pills-materi" role="tabpanel">
                                            <h4 class="fw-bold mb-4">Apa yang Akan Anda Pelajari?</h4>
                                            <div class="row g-3" id="detailModalFeatures">
                                                <!-- Features injected by JS -->
                                            </div>
                                        </div>
                                        <!-- Ketentuan Tab -->
                                        <div class="tab-pane fade" id="pills-terms" role="tabpanel">
                                            <h4 class="fw-bold mb-4">Syarat & Ketentuan Peserta</h4>
                                            <div class="bg-warning bg-opacity-10 p-4 rounded-4 border border-warning border-opacity-20 mb-4">
                                                <p class="mb-0 small"><i class="fas fa-exclamation-triangle me-2 text-warning"></i> Mohon baca seluruh ketentuan sebelum melakukan pendaftaran.</p>
                                            </div>
                                            <ul class="list-group list-group-flush">
                                                <li class="list-group-item border-0 px-0 py-3 d-flex align-items-start">
                                                    <div class="bg-primary bg-opacity-10 p-1 rounded-circle me-3 mt-1"><i class="fas fa-user-check text-primary small"></i></div>
                                                    <div><h6 class="fw-bold mb-1">Persyaratan Dasar</h6><p class="small text-muted mb-0">Peserta minimal berusia 17 tahun atau sudah memiliki KTP/Identitas.</p></div>
                                                </li>
                                                <li class="list-group-item border-0 px-0 py-3 d-flex align-items-start">
                                                    <div class="bg-primary bg-opacity-10 p-1 rounded-circle me-3 mt-1"><i class="fas fa-calendar-alt text-primary small"></i></div>
                                                    <div><h6 class="fw-bold mb-1">Kehadiran</h6><p class="small text-muted mb-0">Peserta wajib mengikuti minimal 80% dari total pertemuan untuk kelulusan.</p></div>
                                                </li>
                                                <li class="list-group-item border-0 px-0 py-3 d-flex align-items-start">
                                                    <div class="bg-primary bg-opacity-10 p-1 rounded-circle me-3 mt-1"><i class="fas fa-file-invoice-dollar text-primary small"></i></div>
                                                    <div><h6 class="fw-bold mb-1">Administrasi</h6><p class="small text-muted mb-0">Biaya pendaftaran tidak dapat dikembalikan jika peserta mengundurkan diri setelah kelas dimulai.</p></div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sidebar Actions Area -->
                        <div class="col-lg-4">
                            <!-- Registration Card -->
                            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 text-center sticky-top" style="top: 100px;">
                                <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle d-inline-block mb-4" style="width: 70px; height: 70px; line-height: 40px;">
                                    <i class="fas fa-user-edit fs-3"></i>
                                </div>
                                <h3 class="fw-bold mb-2">Daftar Sekarang</h3>
                                <p class="text-muted mb-4">Amankan kursi Anda sekarang dan mulai perjalanan karir profesional bersama MCM.</p>
                                
                                <button type="button" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-lg mb-3" id="btnLanjutCheckout" style="background: linear-gradient(135deg, #0c4a6e, #0ea5e9); border: none; font-size: 1.1rem;">
                                    Daftar & Bayar
                                </button>
                                
                                <hr class="my-4">
                                
                                <div class="text-start">
                                    <h6 class="fw-bold mb-3 small text-uppercase" style="letter-spacing: 1px;">Konsultasi Gratis</h6>
                                    <div class="d-flex align-items-center p-3 rounded-4 bg-light border border-light">
                                        <i class="fab fa-whatsapp fs-3 text-success me-3"></i>
                                        <div>
                                            <p class="small fw-bold mb-0">Hubungi Admin</p>
                                            <a href="https://wa.me/6285793935707" target="_blank" class="small text-decoration-none text-primary">Tanya lewat WA</a>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 p-3 rounded-4 border border-dashed text-center">
                                    <p class="small text-muted mb-0"><i class="fas fa-lock me-2"></i>Data pendaftaran Anda aman dan hanya digunakan untuk keperluan pelatihan.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
