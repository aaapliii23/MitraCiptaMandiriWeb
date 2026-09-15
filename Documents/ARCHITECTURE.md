# ARCHITECTURE.md

Dokumen arsitektur aplikasi **Mitra Cipta Mandiri (MCM)** — website Lembaga Pelatihan & Kursus Profesional.

> **Revisi sebelumnya:** (1) penghapusan tombol Login Admin dari header/logo publik, (2) katalog Pelatihan/Kursus, (3) Testimoni + moderasi, (4) Chatbot terintegrasi WhatsApp API, (5) Payment/pembayaran kursus sungguhan, (6) LMS (materi/modul per kelas + gating quiz). Item (1)–(6) **SELESAI**.
>
> **Revisi ini menambahkan (BARU):** (7) Rekap Uang Masuk & Uang Keluar (termasuk sewa) + cetak laporan keuangan, (8) penghapusan bagian Penguji (Asesor) — data penguji menjadi Instruktur yang **wajib punya kategori**, (9) slide foto kegiatan di halaman depan dari galeri, (10) pemilihan Instruktur (opsional) saat payment, (11) input testimoni dipindah ke halaman User di LMS + kartu testimoni bisa diklik untuk lihat data alumni, (12) template sertifikat berbeda per kelas, (13) soal Essay di test materi, (14) register wajib Nama Asli (validasi ketat), (15) footer ikon TikTok + alamat klik ke Google Maps.

## 1. Ringkasan

Aplikasi dibangun **PHP 8 procedural** tanpa framework, menggunakan **MySQL via PDO**, tanpa Composer/build tool/testing framework. Frontend memakai **Bootstrap 5**, **Font Awesome**, **AOS**, dan **Swiper** dari CDN. File PHP flat di webroot tanpa routing; admin panel adalah satu halaman yang digerakkan parameter query `?page=`; LMS peserta adalah kelompok halaman terpisah (`lms/`) yang digerakkan sesi login peserta.

Server langsung menyajikan root repo (Apache/XAMPP docroot atau PHP built-in server).

## 2. Gambaran Arsitektur

```
                                  ┌──────────────────────────────┐
                                  │         Browser User         │
                                  └──────────────┬───────────────┘
                                                 │ HTTP
        ┌────────────────────────────────────────┴───────────────────────────────────────────┐
        │                               Webroot (docroot)                                    │
        │                                                                                     │
        │  PUBLIC (HTML + JS inline)                                                          │
        │   index.php, programs.php, class_detail.php, examiners.php,                         │
        │   testimoni.php, certification.php, about.php                                       │
        │   (Tombol "Login Admin" TIDAK ada di logo/navbar publik — hanya via /admin_login.php)│
        │   (Beranda: slide foto kegiatan dari galeri; footer: ikon TikTok + alamat → Gmaps)  │
        │                                                                                     │
        │  AUTH PESERTA (LMS)                                                                  │
        │   user_register.php (wajib Nama Asli), user_login.php, user_logout.php              │
        │                                                                                     │
        │  ACTION (redirect / POST / JSON)                                                     │
        │   create_transaction.php, payment_webhook.php                    │
        │                                                                                     │
        │  CHATBOT (WhatsApp Cloud API)                                                        │
        │   chatbot_webhook.php  ◄──────────────────────────► Meta WhatsApp Cloud API           │
        │                                                                                     │
        │  LMS PESERTA (guard $_SESSION['user_logged_in'] + enrollment lunas)                  │
        │   lms/dashboard.php, lms/course.php, lms/material.php, lms/quiz.php,                 │
        │   lms/certificate.php, lms/profile.php (submit testimoni)                            │
        │                                                                                     │
        │  ADMIN (single-page router ?page=)                                                   │
        │   admin_login.php → admin/dashboard.php ──► admin/pages/*.php                        │
        │   admin/actions/manage_*.php  (JSON endpoints, incl. keuangan)                       │
        └──────────────────────────────────┬─────────────────────────────────────────────────┘
                                           │ PDO (prepared statements)
                                ┌──────────▼──────────┐        ┌────────────────────────────┐
                                │      MySQL mcm_db    │        │  Payment Gateway (eksternal)│
                                └──────────────────────┘        │  mis. Midtrans / Xendit     │
                                           ▲                    └──────────────┬─────────────┘
                                           │ webhook callback                  │ redirect/snap
                                           └────────────────────────────────────┘
```

## 3. Komponen Utama

### 3.1 Shared / Reusable
- `includes/db_config.php` — koneksi PDO (`mcm_db`, user `root`). Satu-satunya titik konfigurasi DB.
- `includes/header.php` — navbar publik (link anchor ke section home) + inisialisasi `$_SESSION['csrf_token']`. **Tidak berisi tautan/tombol Login Admin** — login admin hanya diakses langsung via URL `admin_login.php` (tidak ditautkan dari UI publik).
- `includes/footer.php` — footer publik (termasuk ikon **TikTok** & alamat yang **diklik → Google Maps**) + script Bootstrap/AOS/Swiper + widget tombol chat WhatsApp mengambang.
- `includes/auth_user.php` — guard peserta LMS: cek `$_SESSION['user_logged_in']`, redirect ke `user_login.php` bila belum login.
- `includes/whatsapp_client.php` — helper kirim pesan keluar via WhatsApp Cloud API (dipakai chatbot & notifikasi status pembayaran/kursus).
- `includes/payment_gateway.php` — helper request ke Payment Gateway (buat transaksi, generate Snap/redirect URL) dan verifikasi signature webhook.

### 3.2 Public Home (index.php)
Wrapper tipis yang meng-include partial dari `partials/`:
- `section_beranda.php` — hero + Swiper dengan **slide foto kegiatan** (dari tabel `gallery`, foto ber-flag tampil di beranda).
- `section_tentang.php` — profil singkat.
- `section_galeri.php` — galeri kegiatan.
- `section_paket.php` — daftar kelas/program (dari tabel `classes`), tombol "Daftar & Bayar".
- `section_testimoni.php` — testimoni yang berstatus `approved` (tabel `testimonials`); **kartu dapat diklik → lihat data alumni**.
- `modal_checkout.php`, `modal_detail.php` — modal interaksi (checkout kini memicu alur pembayaran, bukan lagi redirect WhatsApp manual).
- `index_scripts.php` — JS interaksi halaman depan.

### 3.3 Halaman Publik Lain
- `programs.php`, `class_detail.php` — katalog kelas + detail + tombol beli/checkout (checkout menyertakan **pilihan Instruktur opsional**).
- `examiners.php` — **Profil Penguji Ahli**: menampilkan data **Penguji (Instruktur)** dari tabel `instructors` (dengan kategori), bukan lagi tabel `examiners`.
- `testimoni.php` — form publik submit testimoni **DIHAPUS**; input testimoni pindah ke halaman User di LMS (`lms/profile.php`). Halaman ini dapat menjadi landing informasi/redirect.
- `certification.php`, `about.php` — informasi legalitas & profil.

### 3.4 Payment / Pembayaran Kursus (DIREVISI)
- `create_transaction.php` — dipanggil dari modal checkout (POST, CSRF): validasi kelas & (opsional) **Instruktur terpilih**, insert `orders` (`payment_status = 'unpaid'`), panggil `includes/payment_gateway.php` untuk membuat transaksi di Payment Gateway, simpan `orders.payment_gateway_ref`, kembalikan URL/Snap token pembayaran ke browser.
- `payment_status.php` — halaman status transaksi untuk user (pending/paid/failed/expired), polling ringan atau tampil dari DB.
- `payment_webhook.php` — endpoint callback dari Payment Gateway (POST, **tanpa sesi**, verifikasi signature via `payment_gateway.php`), update `orders.payment_status` (`paid`/`failed`/`expired`) dan `orders.paid_at`. Jika `paid`, otomatis membuat baris `enrollments` (akses LMS terbuka) dan mengirim notifikasi WhatsApp via `whatsapp_client.php`.
- Alur lama (redirect WhatsApp manual ke `628978902864` + `generate_pdf.php`) **dipertahankan sebagai bukti/invoice**, kini dipicu setelah `payment_status = paid`, bukan langsung setelah checkout.

### 3.5 Chatbot Terintegrasi WhatsApp API (BARU)
- `chatbot_webhook.php` — satu endpoint untuk dua metode:
  - `GET` — verifikasi webhook Meta (`hub.verify_token` dicocokkan dengan token di `includes/whatsapp_client.php`/env).
  - `POST` — menerima pesan masuk dari WhatsApp Cloud API. Pesan dicocokkan ke intent sederhana berbasis kata kunci (kursus, jadwal, harga, pembayaran, modul) → balasan otomatis dari data `classes`/`orders`/FAQ statis; bila tidak cocok, pesan diteruskan/ditandai untuk dijawab manual oleh admin (nomor WhatsApp admin yang sama dengan yang dipakai checkout).
  - Semua percakapan dicatat ke tabel `chat_messages` agar admin dapat meninjau riwayat dari dashboard (`admin/pages/chatbot.php`, opsional/P2).
- Tombol chat mengambang di `includes/footer.php` mengarahkan ke `wa.me/<nomor>` dengan pesan prefilled sebagai jalur cepat tanpa menunggu bot.

### 3.6 LMS — Materi/Modul Kursus (DIREVISI)
- `user_register.php`, `user_login.php`, `user_logout.php` — auth peserta memakai tabel `users`; **registrasi wajib Nama Asli** (validasi super ketat: minimal 2 kata, huruf alfabet/spasi/hubung saja).
- `lms/dashboard.php` — daftar kelas yang **sudah dibeli & lunas** (join `enrollments`/`orders.payment_status = 'paid'`) milik peserta yang login.
- `lms/course.php?class_id=` — daftar modul/materi (tabel `materials`) untuk satu kelas, urut `sort_order`; guard: hanya bila ada `enrollments` aktif untuk kelas tsb.
- `lms/material.php?id=` — tampilan satu materi (video/PDF/teks); gating urut: materi dibuka setelah materi sebelumnya lulus test.
- `lms/quiz.php` — grading test per materi; soal **pilihan ganda + Essay** (soal PG dinilai `correct_option`, soal Essay dinilai manual/oleh admin atau kunci jawaban), kelulusan ≥70% menandai `material_progress.completed=1`.
- `lms/profile.php` — profil peserta: ubah data, ganti password, dan **submit testimoni** (input testimoni dipindah dari halaman depan).
- `lms/certificate.php` — sertifikat kelulusan dengan **template per kelas** (tabel `certificates` + `certificate_templates`), tersedia saat semua materi kelas selesai.
- `admin/pages/lms_materials.php` + `admin/actions/manage_materials.php` — CRUD materi per kelas (judul, tipe, konten/URL, urutan) oleh admin; `admin/actions/manage_quiz.php` — CRUD soal (termasuk Essay).

### 3.7 Admin Panel
- `admin/dashboard.php` — **router** `?page=`:
  - Guard `$_SESSION['admin_logged_in']`; redirect ke `admin_login.php` bila belum login.
  - Fetch data per halaman (classes, orders, testimonials, instructors, materials, finance, dsb).
  - Include `admin/includes/head.php`, `admin/pages/*.php`, `admin/includes/modals.php`, `admin/includes/scripts.php`.
- `admin/includes/head.php` — wrapper: `styles_head.php`, `styles_head_components.php`, `nav_bottom.php`, `sidebar.php`, `mobile_header.php`, `offcanvas.php`.
- `admin/includes/modals.php` — wrapper satu file per modal (`modal_*.php`).
- `admin/includes/scripts.php` — wrapper: `scripts_common.php`, `scripts_entities.php`, `styles_bottomnav.php`, `scripts_navigation.php`, `scripts_modals.php`.
- `admin/actions/manage_*.php` — **JSON endpoints** dipanggil `fetch()` dari dashboard: create/update/delete/approve/reject.
- `admin/pages/*.php` — konten per halaman (dashboard, orders, classes, gallery, instructors, certs, reports, finance, admins, categories, testimonials, lms_materials, settings).

### 3.8 Endpoint Admin (JSON)
| Endpoint | Entitas | Aksi |
|---|---|---|
| `manage_classes.php` | Kelas/Program | create/update/delete (termasuk template sertifikat) |
| `manage_gallery.php` | Galeri | create/update/delete (termasuk flag "tampil di beranda" untuk slide foto) |
| `manage_instructors.php` | Instruktur | create/update/delete — **wajib kategori** |
| `manage_certs.php` | Sertifikasi | create/update/delete |
| `manage_cert_templates.php` | Template Sertifikat | create/update/delete (template per kelas) |
| `manage_categories.php` | Kategori Kelas | create/update/delete |
| `manage_testimonials.php` | Testimoni | update/approve/reject/delete |
| ~~`manage_examiners.php`~~ | ~~Penguji~~ | **DIHAPUS** — digabung ke Instruktur |
| `manage_orders.php`, `update_order.php` | Pesanan | update status/detail |
| `manage_materials.php` | Materi LMS | create/update/delete/reorder |
| `manage_quiz.php` | Soal test | create/update/delete (PG + Essay) |
| `manage_finance.php` | Keuangan | create/update/delete — rekap uang masuk/keluar (termasuk sewa) |
| `manage_admins.php` | Admin | create/update/delete/change password |
| `reports_data.php` | Rekap | data laporan (rekap pembayaran + rekap keuangan) |
| `save_settings.php` | Pengaturan | **stub** (tidak ada tabel `settings`; selalu sukses) |

## 4. Alur Data Penting

### 4.1 Checkout & Pembayaran Kursus (DIREVISI)
1. `pages/class_detail.php` → pilih kelas → (opsional) **pilih Instruktur** → form checkout.
2. Form checkout → `create_transaction.php` (CSRF) → insert `orders` (`order_number = ORD-` + uniqid + time, `payment_status = 'unpaid'`, `instructor_id` bila dipilih) → `includes/payment_gateway.php` membuat transaksi di Payment Gateway → browser diarahkan ke halaman pembayaran gateway (Snap/redirect).
3. User membayar di Payment Gateway.
4. Payment Gateway memanggil `payment_webhook.php` (server-to-server) → verifikasi signature → update `orders.payment_status`.
5. Jika `paid`: insert `enrollments` (akses LMS terbuka), kirim notifikasi WhatsApp (`whatsapp_client.php`), tampilkan `payment_status.php` sukses + tautan ke `lms/dashboard.php`.
6. Jika `failed`/`expired`: `orders.payment_status` diperbarui, kelas **tidak** dapat diakses di LMS; user dapat mengulang checkout.

### 4.3 Testimoni (DIREVISI)
1. Peserta login LMS → `lms/profile.php` → form testimoni (CSRF, rating 1-5, ulasan, program, foto opsional) → insert status `pending`.
2. Admin: `?page=testimonials` → `approve`/`reject`/`update`/`delete` via `manage_testimonials.php`.
3. Publik: hanya status `approved` yang tampil di `section_testimoni.php`; klik kartu → data alumni (angkatan, pekerjaan, program).

### 4.4 Profil Penguji (Instruktur)
1. Admin `?page=instructors` → CRUD Instruktur (**wajib kategori**, spesialisasi, bio, sertifikasi) pada tabel `instructors`.
2. `examiners.php` (Profil Penguji Ahli) → menampilkan data Instruktur ber-kategori; tanpa tabel `examiners`.

### 4.5 Chatbot WhatsApp
1. User kirim pesan ke nomor WhatsApp bisnis MCM → Meta WhatsApp Cloud API meneruskan ke `chatbot_webhook.php` (POST).
2. Pesan dicocokkan ke intent (kursus/jadwal/harga/pembayaran/modul); jika cocok, balasan otomatis dikirim via `whatsapp_client.php`.
3. Jika tidak cocok, pesan dicatat sebagai butuh respons manual admin; admin membalas langsung dari WhatsApp Business.
4. Seluruh pesan (masuk/keluar) dicatat ke `chat_messages` untuk audit.

### 4.6 LMS — Akses Materi & Test
1. `enrollments` tercipta otomatis saat `orders.payment_status = 'paid'` (lihat 4.1).
2. `lms/dashboard.php` → daftar kelas ter-enroll → `lms/course.php` → daftar `materials` per kelas → `lms/material.php` menampilkan materi.
3. Gating urut: materi hanya terbuka bila materi urutan sebelumnya lulus test (`material_progress.completed=1`).
4. `lms/quiz.php` menilai test (soal PG + **Essay**) vs `correct_option`/kunci jawaban, simpan `quiz_attempts`, lalu upsert `material_progress`.
5. Tanpa enrollment aktif, akses ke `lms/course.php`/`lms/material.php` untuk `class_id` tsb ditolak (redirect ke `class_detail.php` dengan pesan "kelas belum dibeli/lunas").
6. Semua materi selesai → `lms/certificate.php` menerbitkan sertifikat dengan **template sesuai kelas** (tabel `certificates` + `certificate_templates`).

### 4.7 Upload Gambar
- Tujuan: `uploads/{classes,gallery,instructors,certs,testimonials}/` (dir `examiners` **dihapus**).
- Nama: `uniqid().ext`; whitelist `jpg/jpeg/png/webp`.
- Disimpan di DB sebagai path relatif webroot (mis. `uploads/classes/xxxx.jpg`).
- Render admin memakai helper `getImgSrc()` (prefix `../` bila perlu).
- Foto galeri ber-flag "tampil di beranda" menjadi slide halaman depan.
- Materi LMS bertipe file (PDF/video) memakai direktori terpisah `uploads/materials/` dengan whitelist tambahan (`pdf, mp4`) — lihat RULES.md §5.

### 4.8 Rekap Keuangan (BARU)
1. Admin `?page=finance` mencatat transaksi ke tabel `finance_transactions`: tipe (`in`/`out`), kategori (pemasukan, **sewa**, gaji, operasional, dll), nominal, deskripsi, tanggal.
2. `reports_data.php` menyajikan rekap per kategori/periode (total masuk, total keluar, saldo).
3. Cetak laporan keuangan → halaman cetak khusus dengan **tampilan baru** (print-to-PDF), konsisten dengan pola `generate_pdf.php`.

## 5. Keamanan
- **Auth admin**: `$_SESSION['admin_logged_in']`; setiap endpoint admin mengecek guard di bagian atas file. Tidak ada lagi entry point Login Admin yang ditautkan dari UI publik.
- **Auth peserta (LMS)**: `$_SESSION['user_logged_in']`; password di-hash bcrypt.
- **Password admin/peserta**: hash bcrypt, diverifikasi `password_verify()`.
- **CSRF**: form publik (`create_transaction.php`, `user_register.php`, `user_login.php`, `lms/profile.php` — termasuk submit testimoni) terhadap `$_SESSION['csrf_token']`. Endpoint admin **tidak** punya CSRF (konsisten pola lama).
- **Webhook eksternal** (`payment_webhook.php`, `chatbot_webhook.php`): **tidak** memakai sesi/CSRF (dipanggil server-to-server), **wajib** verifikasi signature/token resmi dari penyedia (Payment Gateway signature key; WhatsApp `hub.verify_token`) sebelum memproses payload.
- **Akses LMS**: setiap request materi memvalidasi kepemilikan `enrollments` milik `user_id` sesi aktif — bukan hanya status login.
- **SQL Injection**: semua query memakai PDO prepared statements.
- **Upload**: whitelist ekstensi; tidak ada eksekusi file dari direktori upload.
- **XSS**: output di-escape `htmlspecialchars()` pada data dinamis.
- **Secrets**: kunci Payment Gateway & token WhatsApp Cloud API disimpan di `includes/config_secrets.php` (di luar VCS publik, lihat RULES.md §8) — bukan hardcode di file endpoint.

## 6. Konvensi Struktur File (300 Baris)
Aturan: **file PHP yang melebihi 300 baris harus dipecah menjadi partials.**
- `index.php` → `partials/section_*.php`, `modal_*.php`, `index_scripts.php`.
- `admin_login.php` → `partials/admin_login_{head,body,scripts}.php`.
- `admin/dashboard.php` → wrapper (include), `admin/includes/*`, `admin/pages/*`.
- `admin/includes/scripts.php`, `modals.php`, `head.php` → wrapper yang meng-include partial masing-masing.
- `lms/dashboard.php`, `lms/course.php` → `partials/lms_*.php` bila melebihi 300 baris.
- `admin/pages/finance.php` → partial/modal tersendiri bila melebihi 300 baris.

## 7. Variabel Lingkungan / Konfigurasi
- DB: `includes/db_config.php` (`host localhost`, `dbname mcm_db`, `user root`, password diisi di file ini).
- Nomor WhatsApp admin (checkout lama & notifikasi): hardcoded `628978902864`.
- Admin login seed di schema: `admin` / `admin` (hash bcrypt). DB produksi: `superadmin` / `AdminMCM2026`.
- **Payment Gateway**: `includes/config_secrets.php` — `PAYMENT_SERVER_KEY`, `PAYMENT_CLIENT_KEY`, `PAYMENT_MODE` (`sandbox`/`production`), `PAYMENT_WEBHOOK_SIGNATURE_KEY`.
- **WhatsApp Cloud API**: `includes/config_secrets.php` — `WA_PHONE_NUMBER_ID`, `WA_ACCESS_TOKEN`, `WA_VERIFY_TOKEN` (dicocokkan saat `GET chatbot_webhook.php`).
