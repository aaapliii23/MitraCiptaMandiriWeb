# ARCHITECTURE.md

Dokumen arsitektur aplikasi **Mitra Cipta Mandiri (MCM)** — website Lembaga Pelatihan & Kursus Profesional.

> **Revisi ini menambahkan:** (1) penghapusan tombol Login Admin dari header/logo publik, (2) katalog Pelatihan/Kursus, (3) Testimoni + moderasi, (4) pemilihan Penguji + latar belakang, (5) Chatbot terintegrasi WhatsApp API, (6) Payment/pembayaran kursus sungguhan, (7) LMS (materi/modul per kelas). Item (1)–(2) **SELESAI** dan sudah tercermin di struktur di bawah. Item (3)–(4) sudah berjalan (moderasi testimoni & dropdown penguji). Item (5)–(7) adalah **penambahan baru** pada revisi ini.

## 1. Ringkasan

Aplikasi dibangun **PHP 8 procedural** tanpa framework, menggunakan **MySQL via PDO**, tanpa Composer/build tool/testing framework. Frontend memakai **Bootstrap 5**, **Font Awesome**, **AOS**, dan **Swiper** dari CDN. File PHP flat di webroot tanpa routing; admin panel adalah satu halaman yang digerakkan parameter query `?page=`; LMS peserta adalah kelompok halaman terpisah (`lms/`) yang digerakkan sesi login peserta.

Server langsung menyajikan root repo (Apache/XAMPP docroot atau PHP built-in server).

## 2. Gambaran Arsitektur

```
                                  ┌──────────────────────────────┐
                                  │         Browser User         │
                                  └──────────────┬───────────────┘
                                                 │ HTTP
        ┌─────────────────────────────────────────┴─────────────────────────────────────────┐
        │                               Webroot (docroot)                                    │
        │                                                                                     │
        │  PUBLIC (HTML + JS inline)                                                          │
        │   index.php, programs.php, class_detail.php, examiners.php,                         │
        │   testimoni.php, certification.php, about.php                                       │
        │   (Tombol "Login Admin" TIDAK ada di logo/navbar publik — hanya via /admin_login.php)│
        │                                                                                      │
        │  AUTH PESERTA (LMS)                                                                  │
        │   user_register.php, user_login.php, user_logout.php                                │
        │                                                                                      │
        │  ACTION (redirect / POST / JSON)                                                     │
        │   submit_booking.php, create_transaction.php, payment_webhook.php                    │
        │                                                                                      │
        │  CHATBOT (WhatsApp Cloud API)                                                        │
        │   chatbot_webhook.php  ◄──────────────────────────► Meta WhatsApp Cloud API           │
        │                                                                                      │
        │  LMS PESERTA (guard $_SESSION['user_logged_in'] + enrollment lunas)                  │
        │   lms/dashboard.php, lms/course.php, lms/material.php                                │
        │                                                                                      │
        │  ADMIN (single-page router ?page=)                                                   │
        │   admin_login.php → admin/dashboard.php ──► admin/pages/*.php                        │
        │   admin/actions/manage_*.php  (JSON endpoints)                                        │
        └──────────────────────────────────┬──────────────────────────────────────────────────┘
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
- `includes/footer.php` — footer publik + script Bootstrap/AOS/Swiper + widget tombol chat WhatsApp mengambang.
- `includes/auth_user.php` — guard peserta LMS: cek `$_SESSION['user_logged_in']`, redirect ke `user_login.php` bila belum login.
- `includes/whatsapp_client.php` — helper kirim pesan keluar via WhatsApp Cloud API (dipakai chatbot & notifikasi status pembayaran/kursus).
- `includes/payment_gateway.php` — helper request ke Payment Gateway (buat transaksi, generate Snap/redirect URL) dan verifikasi signature webhook.

### 3.2 Public Home (index.php)
Wrapper tipis yang meng-include partial dari `partials/`:
- `section_beranda.php` — hero + Swiper.
- `section_tentang.php` — profil singkat.
- `section_galeri.php` — galeri kegiatan.
- `section_paket.php` — daftar kelas/program (dari tabel `classes`), tombol "Daftar & Bayar".
- `section_testimoni.php` — testimoni yang berstatus `approved` (tabel `testimonials`).
- `modal_booking.php`, `modal_checkout.php`, `modal_detail.php` — modal interaksi (checkout kini memicu alur pembayaran, bukan lagi redirect WhatsApp manual).
- `index_scripts.php` — JS interaksi halaman depan.

### 3.3 Halaman Publik Lain
- `programs.php`, `class_detail.php` — katalog kelas + detail + tombol beli/checkout.
- `examiners.php` — pilih penguji (dropdown) → lihat latar belakang penguji (dinamis dari tabel `examiners`).
- `testimoni.php` — form publik submit testimoni (CSRF, upload foto opsional).
- `certification.php`, `about.php` — informasi legalitas & profil.
- `submit_booking.php` — simpan booking (konsultasi/tanya sebelum beli) + cek CSRF.

### 3.4 Payment / Pembayaran Kursus (BARU)
- `create_transaction.php` — dipanggil dari modal checkout (POST, CSRF): validasi kelas & (opsional) penguji terpilih, insert `orders` (`payment_status = 'unpaid'`), panggil `includes/payment_gateway.php` untuk membuat transaksi di Payment Gateway, simpan `orders.payment_gateway_ref`, kembalikan URL/Snap token pembayaran ke browser.
- `payment_status.php` — halaman status transaksi untuk user (pending/paid/failed/expired), polling ringan atau tampil dari DB.
- `payment_webhook.php` — endpoint callback dari Payment Gateway (POST, **tanpa sesi**, verifikasi signature via `payment_gateway.php`), update `orders.payment_status` (`paid`/`failed`/`expired`) dan `orders.paid_at`. Jika `paid`, otomatis membuat baris `enrollments` (akses LMS terbuka) dan mengirim notifikasi WhatsApp via `whatsapp_client.php`.
- Alur lama (redirect WhatsApp manual ke `6285793935707` + `generate_pdf.php`) **dipertahankan sebagai bukti/invoice**, kini dipicu setelah `payment_status = paid`, bukan langsung setelah checkout.

### 3.5 Chatbot Terintegrasi WhatsApp API (BARU)
- `chatbot_webhook.php` — satu endpoint untuk dua metode:
  - `GET` — verifikasi webhook Meta (`hub.verify_token` dicocokkan dengan token di `includes/whatsapp_client.php`/env).
  - `POST` — menerima pesan masuk dari WhatsApp Cloud API. Pesan dicocokkan ke intent sederhana berbasis kata kunci (kursus, jadwal, harga, pembayaran, modul) → balasan otomatis dari data `classes`/`orders`/FAQ statis; bila tidak cocok, pesan diteruskan/ditandai untuk dijawab manual oleh admin (nomor WhatsApp admin yang sama dengan yang dipakai checkout).
  - Semua percakapan dicatat ke tabel `chat_messages` agar admin dapat meninjau riwayat dari dashboard (`admin/pages/chatbot.php`, opsional/P2).
- Tombol chat mengambang di `includes/footer.php` mengarahkan ke `wa.me/<nomor>` dengan pesan prefilled sebagai jalur cepat tanpa menunggu bot.

### 3.6 LMS — Materi/Modul Kursus (BARU)
- `user_register.php`, `user_login.php`, `user_logout.php` — auth peserta memakai tabel `users` (sebelumnya tidak dipakai alur aktif, kini aktif).
- `lms/dashboard.php` — daftar kelas yang **sudah dibeli & lunas** (join `enrollments`/`orders.payment_status = 'paid'`) milik peserta yang login.
- `lms/course.php?class_id=` — daftar modul/materi (tabel `materials`) untuk satu kelas, urut `sort_order`; guard: hanya bila ada `enrollments` aktif untuk kelas tsb.
- `lms/material.php?id=` — tampilan satu materi (video/PDF/teks), menandai progres di `material_progress` saat dibuka/selesai.
- `admin/pages/lms_materials.php` + `admin/actions/manage_materials.php` — CRUD materi per kelas (judul, tipe, konten/URL, urutan) oleh admin.

### 3.7 Admin Panel
- `admin/dashboard.php` — **router** `?page=`:
  - Guard `$_SESSION['admin_logged_in']`; redirect ke `admin_login.php` bila belum login.
  - Fetch data per halaman (classes, orders, testimonials, examiners, materials, dsb).
  - Include `admin/includes/head.php`, `admin/pages/*.php`, `admin/includes/modals.php`, `admin/includes/scripts.php`.
- `admin/includes/head.php` — wrapper: `styles_head.php`, `styles_head_components.php`, `nav_bottom.php`, `sidebar.php`, `mobile_header.php`, `offcanvas.php`.
- `admin/includes/modals.php` — wrapper satu file per modal (`modal_*.php`).
- `admin/includes/scripts.php` — wrapper: `scripts_common.php`, `scripts_entities.php`, `styles_bottomnav.php`, `scripts_navigation.php`, `scripts_modals.php`.
- `admin/actions/manage_*.php` — **JSON endpoints** dipanggil `fetch()` dari dashboard: create/update/delete/approve/reject.
- `admin/pages/*.php` — konten per halaman (dashboard, orders, classes, gallery, instructors, certs, reports, admins, categories, testimonials, examiners, lms_materials, settings).

### 3.8 Endpoint Admin (JSON)
| Endpoint | Entitas | Aksi |
|---|---|---|
| `manage_classes.php` | Kelas/Program | create/update/delete |
| `manage_gallery.php` | Galeri | create/update/delete |
| `manage_instructors.php` | Instruktur | create/update/delete |
| `manage_certs.php` | Sertifikasi | create/update/delete |
| `manage_categories.php` | Kategori Kelas | create/update/delete |
| `manage_testimonials.php` | Testimoni | update/approve/reject/delete |
| `manage_examiners.php` | Penguji | create/update/delete |
| `manage_orders.php`, `update_order.php` | Pesanan | update status/detail |
| `update_booking.php` | Booking | update status |
| `manage_materials.php` | Materi LMS | create/update/delete/reorder |
| `manage_admins.php` | Admin | create/update/delete/change password |
| `reports_data.php` | Rekap | data laporan (termasuk rekap pembayaran) |
| `save_settings.php` | Pengaturan | **stub** (tidak ada tabel `settings`; selalu sukses) |

## 4. Alur Data Penting

### 4.1 Booking (konsultasi, non-pembayaran)
1. `index.php` → `submit_booking.php` (validasi CSRF) → tabel `bookings`.

### 4.2 Checkout & Pembayaran Kursus (DIREVISI)
1. `class_detail.php` → pilih kelas → (opsional) pilih penguji di `examiners.php` → modal checkout.
2. Modal checkout → `create_transaction.php` (CSRF) → insert `orders` (`order_number = ORD-` + uniqid + time, `payment_status = 'unpaid'`) → `includes/payment_gateway.php` membuat transaksi di Payment Gateway → browser diarahkan ke halaman pembayaran gateway (Snap/redirect).
3. User membayar di Payment Gateway.
4. Payment Gateway memanggil `payment_webhook.php` (server-to-server) → verifikasi signature → update `orders.payment_status`.
5. Jika `paid`: insert `enrollments` (akses LMS terbuka), kirim notifikasi WhatsApp (`whatsapp_client.php`), tampilkan `payment_status.php` sukses + tautan ke `lms/dashboard.php`.
6. Jika `failed`/`expired`: `orders.payment_status` diperbarui, kelas **tidak** dapat diakses di LMS; user dapat mengulang checkout.

### 4.3 Testimoni
1. Publik: `testimoni.php` (CSRF, rating 1-5, ulasan, program opsional, foto opsional) → status `pending`.
2. Admin: `?page=testimonials` → `approve`/`reject`/`update`/`delete` via `manage_testimonials.php`.
3. Publik: hanya status `approved` yang tampil di `section_testimoni.php`.

### 4.4 Profil Penguji
1. `examiners.php` → dropdown "Pilih Penguji" → detail latar belakang (foto, spesialisasi, bio, sertifikasi) dari tabel `examiners`.
2. Penguji terpilih (opsional) dapat dibawa ke checkout sebagai referensi asesor kelas.

### 4.5 Chatbot WhatsApp
1. User kirim pesan ke nomor WhatsApp bisnis MCM → Meta WhatsApp Cloud API meneruskan ke `chatbot_webhook.php` (POST).
2. Pesan dicocokkan ke intent (kursus/jadwal/harga/pembayaran/modul); jika cocok, balasan otomatis dikirim via `whatsapp_client.php`.
3. Jika tidak cocok, pesan dicatat sebagai butuh respons manual admin; admin membalas langsung dari WhatsApp Business.
4. Seluruh pesan (masuk/keluar) dicatat ke `chat_messages` untuk audit.

### 4.6 LMS — Akses Materi
1. `enrollments` tercipta otomatis saat `orders.payment_status = 'paid'` (lihat 4.2).
2. `lms/dashboard.php` → daftar kelas ter-enroll → `lms/course.php` → daftar `materials` per kelas → `lms/material.php` menandai `material_progress`.
3. Tanpa enrollment aktif, akses ke `lms/course.php`/`lms/material.php` untuk `class_id` tsb ditolak (redirect ke `class_detail.php` dengan pesan "kelas belum dibeli/lunas").

### 4.7 Upload Gambar
- Tujuan: `uploads/{classes,gallery,instructors,certs,testimonials,examiners}/`.
- Nama: `uniqid().ext`; whitelist `jpg/jpeg/png/webp`.
- Disimpan di DB sebagai path relatif webroot (mis. `uploads/classes/xxxx.jpg`).
- Render admin memakai helper `getImgSrc()` (prefix `../` bila perlu).
- Materi LMS bertipe file (PDF/video) memakai direktori terpisah `uploads/materials/` dengan whitelist tambahan (`pdf, mp4`) — lihat RULES.md §5.

## 5. Keamanan

- **Auth admin**: `$_SESSION['admin_logged_in']`; setiap endpoint admin mengecek guard di bagian atas file. Tidak ada lagi entry point Login Admin yang ditautkan dari UI publik.
- **Auth peserta (LMS)**: `$_SESSION['user_logged_in']`; password di-hash bcrypt.
- **Password admin/peserta**: hash bcrypt, diverifikasi `password_verify()`.
- **CSRF**: form publik (`submit_booking.php`, `testimoni.php`, `create_transaction.php`, `user_register.php`, `user_login.php`) terhadap `$_SESSION['csrf_token']`. Endpoint admin **tidak** punya CSRF (konsisten pola lama).
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

## 7. Variabel Lingkungan / Konfigurasi

- DB: `includes/db_config.php` (`host localhost`, `dbname mcm_db`, `user root`, password diisi di file ini).
- Nomor WhatsApp admin (checkout lama & notifikasi): hardcoded `6285793935707`.
- Admin login seed di schema: `admin` / `admin` (hash bcrypt). DB produksi: `superadmin` / `AdminMCM2026`.
- **Payment Gateway**: `includes/config_secrets.php` — `PAYMENT_SERVER_KEY`, `PAYMENT_CLIENT_KEY`, `PAYMENT_MODE` (`sandbox`/`production`), `PAYMENT_WEBHOOK_SIGNATURE_KEY`.
- **WhatsApp Cloud API**: `includes/config_secrets.php` — `WA_PHONE_NUMBER_ID`, `WA_ACCESS_TOKEN`, `WA_VERIFY_TOKEN` (dicocokkan saat `GET chatbot_webhook.php`).
