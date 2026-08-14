# AGENTS.md

Aplikasi PHP 8 (prosedural, tanpa framework), MySQL via PDO, tanpa composer/build/alat testing. Serve langsung dari root repo (mis. docroot Apache/XAMPP atau PHP built-in server). Frontend memakai Bootstrap 5 + Font Awesome + AOS + Swiper dari CDN.

## Setup

- Import `schema.sql` ke MySQL; `includes/db_config.php` terhubung ke db `mcm_db` (user `root`, password kosong) — ubah di file itu jika DB Anda berbeda.
- **`schema.sql` sudah dilengkapi** (tabel `instructors` dan `certifications` beserta seed-nya kini ada di schema dan DB; sebelum di-update, kode memakainya tapi tabel belum ada sehingga upload di `?page=instructors`/`?page=certs` gagal dengan body kosong). Tabel `class_categories` dan kolom `classes.start_date` sudah ada; DB lama harus di-upgrade manual (`ALTER TABLE classes ADD COLUMN start_date DATE`). CRUD kelas (admin `?page=classes`) **wajib** punya `start_date`.
- Admin default di-seed di schema: `admin` / `admin` (hash bcrypt, diverifikasi via `password_verify`).

## Auth & admin

- Auth admin memakai `$_SESSION['admin_logged_in']`; setiap skrip `admin_*` menjaga endpointnya di bagian atas. Login/logout ditangani `admin_login.php`/`admin_logout.php`.
- Semua endpoint `admin_manage_*.php` adalah handler JSON (`header('Content-Type: application/json')`, `$_POST['action']` = create/update/delete) yang dipanggil dari dashboard.
- `admin_dashboard.php` adalah UI admin satu halaman yang digerakkan `?page=`, berisi modal dan JS inline yang memanggil endpoint `admin_manage_*`. Jika belum login ia redirect ke `index.php`.
- `admin_save_settings.php` hanyalah stub (tidak ada tabel `settings`) — selalu mengembalikan sukses.

## Alur & jebakan penting

- Satu-satunya cek CSRF di aplikasi ini adalah `submit_booking.php` terhadap `$_SESSION['csrf_token']` (dibuat di `index.php`/`includes/header.php`); endpoint admin tidak punya CSRF.
- Checkout: `process_checkout.php` insert ke `orders` (`order_number` = `ORD-` + uniqid + time), membuat link WhatsApp ke nomor admin **hardcoded** `6285793935707`, menyimpan `$_SESSION['last_wa_link']`, lalu redirect ke `generate_pdf.php?order=...&print=true`.
- `generate_pdf.php` bukan PDF sungguhan — ia merender bukti HTML "print to PDF" dan otomatis membuka dialog print (`?print=true`).
- Upload gambar masuk ke `uploads/{classes,gallery,instructors,certs}/` sebagai `uniqid().ext` (whitelist `jpg/jpeg/png/webp`), disimpan di DB sebagai path relatif. Kode upload admin mengasumsikan direktori itu ada dan writable.
- Nomor WhatsApp booking harus cocok dengan `/^\+62[0-9]{8,13}$/`.
- **Jangan baca `form.action`/`form.id`/`form.method` sebagai properti JS di dashboard admin.** Form punya `<input type="hidden" name="action">` (dan `name="id"`), sehingga properti `form.action` di-override oleh named property → menjadi objek `HTMLInputElement` dan `fetch(form.action)` menghasilkan URL `[object HTMLInputElement]` (404). Selalu pakai `form.getAttribute('action')` / `form.getAttribute('method')`. Ini sudah diperbaiki di `submitAjaxForm()` dan handler `.ajax-form`.

## Gaya penulisan

- File flat di webroot (tanpa routing); yang dipakai bersama: `includes/db_config.php`, `includes/header.php`, `includes/footer.php`.
- Teks UI dan konten seed DB berbahasa Indonesia; pertahankan string berbahasa Indonesia untuk hal yang terlihat pengguna.
- Tidak ada comment, tidak ada alat lint/test/format — verifikasi dengan menjalankan PHP (`php -l file.php`) dan menguji halaman di browser.
