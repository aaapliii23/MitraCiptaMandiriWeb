# AGENTS.md

Aplikasi PHP 8 (prosedural, tanpa framework), MySQL via PDO, tanpa composer/build/alat testing. Serve langsung dari root repo (mis. docroot Apache/XAMPP atau PHP built-in server). Frontend memakai Bootstrap 5 + Font Awesome + AOS + Swiper dari CDN.

## Setup

- Import `schema.sql` ke MySQL; `includes/db_config.php` terhubung ke db `mcm_db` (user `root`, password `password`) — ubah di file itu jika DB Anda berbeda.
- **`schema.sql` sudah dilengkapi** (tabel `instructors`, `certifications`, `chatbot_intents`, `certificates`, dan seed-nya kini ada di schema dan DB; sebelum di-update, kode memakainya tapi tabel belum ada sehingga fitur terkait gagal). Tabel `class_categories` dan kolom `classes.start_date` sudah ada; DB lama harus di-upgrade manual (`ALTER TABLE classes ADD COLUMN start_date DATE`). CRUD kelas (admin `?page=classes`) **wajib** punya `start_date`.
- Tabel baru `certificates` dan `chatbot_intents` di-auto-create `CREATE TABLE IF NOT EXISTS` dari kode (masing-masing di `lms/certificate.php` dan `admin/actions/manage_chatbot.php`/`admin/dashboard.php`); schema.sql tetap sumber otoritatif untuk instalasi baru.
- Admin default di-seed di schema: `admin` / `admin` (hash bcrypt, diverifikasi via `password_verify`).

## Auth & admin

- Auth admin memakai `$_SESSION['admin_logged_in']`; setiap skrip `admin_*` menjaga endpointnya di bagian atas. Login/logout ditangani `admin_login.php`/`admin_logout.php`.
- Semua endpoint `admin_manage_*.php` adalah handler JSON (`header('Content-Type: application/json')`, `$_POST['action']` = create/update/delete) yang dipanggil dari dashboard.
- `admin_dashboard.php` adalah UI admin satu halaman yang digerakkan `?page=`, berisi modal dan JS inline yang memanggil endpoint `admin_manage_*`. Jika belum login ia redirect ke `index.php`.
- `admin_save_settings.php` hanyalah stub (tidak ada tabel `settings`) — selalu mengembalikan sukses.
- Halaman admin: `?page=dashboard` (termasuk **rekap pembayaran**: omzet lunas, lunas, menunggu bayar, gagal/kadaluarsa dari `orders`), `orders`, `bookings` (form booking publik + atur `booking_date` via `update_booking.php`), `chatbot` (kelola intent/balasan otomatis), `chat`, `classes`, `gallery`, `instructors`, `certs`, `reports`, `admins`, `settings`, `categories`, `testimonials`, `examiners`, `materials`.

## Alur & jebakan penting

- **CSRF publik**: `$_SESSION['csrf_token']` dibuat di `index.php`/`includes/header.php`; dicek (`hash_equals`) di `booking/submit_booking.php`, `payment/create_transaction.php`, `chat/chat_api.php`, `lms/progress.php`, `lms/profile.php`, `user/user_register.php`, `user/user_login.php`, `pages/testimoni.php`. Endpoint admin tidak punya CSRF.
- **Checkout (aktif)**: modal checkout (`partials/modal_checkout.php`) POST ke `payment/create_transaction.php` → insert `orders` (`payment_status=unpaid`) → `pg_create_transaction()` (`includes/payment_gateway.php`, mode `PAYMENT_MODE='mock'` → `payment/payment_mock.php`) → `payment/payment_status.php`; jika `paid`, insert `enrollments` → LMS terbuka. `payment/generate_pdf.php` (bukti print-to-PDF) **hanya** bisa diakses bila `payment_status='paid'` (selain itu redirect `payment/payment_status.php`).
- `process_checkout.php` adalah **legacy/orphan** (tidak ada form yang memanggilnya) — jangan dijadikan acuan alur checkout.
- **Booking publik**: detail kelas → modal booking (`partials/modal_booking.php`) → `booking/submit_booking.php` (simpan `bookings`, validasi `+62`, field `booking_date` opsional) → lanjut ke modal checkout dengan data terisi. Entry lain: `pages/class_detail.php` → checkout langsung.
- **LMS**: `lms/{dashboard,course,material,certificate,profile}.php`; semua gated `includes/auth_user.php` + cek `enrollments`. Sertifikat (`lms/certificate.php`) tersedia saat semua materi kelas selesai (100%), nomor unik `MCM-YYYY-NNNN` di tabel `certificates` (UNIQUE `user_id`+`class_id`). Profil peserta bisa ubah data + ganti password (verifikasi password lama).
- **Gating LMS berurutan + quiz**: materi dibuka berurutan (`course.php` hitung `locked` = ada materi urutan sebelumnya belum selesai). Materi tanpa soal quiz → toggle "Tandai Selesai" manual (`lms/progress.php`, POST JSON + CSRF). Materi dengan soal quiz → wajib lulus quiz (nilai ≥70%) agar `material_progress.completed=1` dan modul berikutnya terbuka. Endpoint grading `lms/quiz.php` (POST JSON + CSRF) menilai vs `correct_option`, simpan ke `quiz_attempts`, lalu upsert `material_progress`. Akses langsung ke modul terkunci diblokir `lms/material.php`. Admin kelola soal via tombol ikon quiz di `?page=materials` → `admin/actions/manage_quiz.php` (action list/create/update/delete) + `admin/includes/modal_quiz.php`. Tabel `quiz_questions` & `quiz_attempts` di-auto-create dari kode dan ada di `database/schema.sql`.
- **Chatbot**: intent & balasan dikelola admin di `?page=chatbot` (tabel `chatbot_intents`), dibaca oleh `wa_match_intent($msg, $pdo)`/`wa_intent_reply($pdo,$intent)` di `includes/whatsapp_client.php`; token `{classes}`/`{prices}` diganti dinamis dari tabel `classes`. Bila tabel kosong, fallback ke array hardcoded. Nomor `web-` (widget chat website) tidak dikirim ke WA eksternal.
- `payment/generate_pdf.php` bukan PDF sungguhan — ia merender bukti HTML "print to PDF" dan otomatis membuka dialog print (`?print=true`).
- Upload gambar masuk ke `uploads/{classes,gallery,instructors,certs}/` sebagai `uniqid().ext` (whitelist `jpg/jpeg/png/webp`), disimpan di DB sebagai path relatif. Kode upload admin mengasumsikan direktori itu ada dan writable.
- Nomor WhatsApp booking harus cocok dengan `/^\+62[0-9]{8,13}$/` (create_transaction menormalisasi `0...`→`62...`).
- **Jangan baca `form.action`/`form.id`/`form.method` sebagai properti JS di dashboard admin.** Form punya `<input type="hidden" name="action">` (dan `name="id"`), sehingga properti `form.action` di-override oleh named property → menjadi objek `HTMLInputElement` dan `fetch(form.action)` menghasilkan URL `[object HTMLInputElement]` (404). Selalu pakai `form.getAttribute('action')` / `form.getAttribute('method')`. Ini sudah diperbaiki di `submitAjaxForm()` dan handler `.ajax-form`.

## Gaya penulisan

- **Struktur folder per modul/fitur**: `user/` (login, register, logout), `payment/` (create_transaction, payment_mock, payment_status, payment_webhook, generate_pdf), `chat/` (chat_api, chatbot_webhook), `booking/` (submit_booking), `pages/` (about, programs, class_detail, examiners, certification, testimoni). `index.php`, `admin_login.php`, `process_checkout.php` (legacy) tetap di webroot. Tidak ada routing — URL file = path folder-nya.
- **Header/footer shared (`includes/header.php`, `includes/footer.php`)** di-include dari root (`index.php`) maupun subfolder (`pages/*`, `user/*`). Keduanya menghitung `$base_url` dari `dirname($_SERVER['SCRIPT_NAME'])` (`''` di root, `'../'` di subfolder) dan semua link/asset di-prefix `$base_url`; `require` db_config memakai `__DIR__`.
- File flat di webroot (tanpa routing); yang dipakai bersama: `includes/db_config.php`, `includes/header.php`, `includes/footer.php`.
- **Aturan 300 baris: file PHP dipecah jadi partials jika melebihi 300 baris.** Public home `index.php` dan `admin_login.php` adalah wrapper tipis yang include partial dari `partials/` (`section_*.php`, `modal_*.php`, `index_scripts.php`, `admin_login_*.php`).
- **Admin dashboard (`admin/dashboard.php`)** adalah router `?page=` yang include:
  - `admin/includes/head.php` (wrapper) → `styles_head.php`, `styles_head_components.php`, `nav_bottom.php`, `sidebar.php`, `mobile_header.php`, `offcanvas.php`.
  - `admin/pages/*.php` (konten per halaman).
  - `admin/includes/modals.php` (wrapper) → `modal_*.php` (satu file per modal).
  - `admin/includes/scripts.php` (wrapper) → `scripts_common.php`, `scripts_entities.php`, `styles_bottomnav.php`, `scripts_navigation.php`, `scripts_modals.php`. Semua partial ini tetap harus di-include dalam urutan yang sama seperti awalnya (CRUD dulu, lalu navigasi AJAX).
- Teks UI dan konten seed DB berbahasa Indonesia; pertahankan string berbahasa Indonesia untuk hal yang terlihat pengguna.
- Tidak ada comment, tidak ada alat lint/test/format — verifikasi dengan menjalankan PHP (`php -l file.php`) dan menguji halaman di browser.
