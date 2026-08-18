# RULES.md

Aturan pengembangan **Mitra Cipta Mandiri (MCM)** — wajib dipatuhi saat menulis/mengubah kode.

## 1. Stack & Tooling

- PHP 8 procedural, tanpa framework.
- MySQL via PDO (prepared statements) — koneksi di `includes/db_config.php` (`mcm_db`, user `root`; ubah di file itu bila DB beda).
- Tanpa Composer/build/testing framework. Verifikasi: `php -l file.php` + uji di browser.
- Frontend: Bootstrap 5 + Font Awesome + AOS + Swiper dari CDN (jangan install ulang).
- Integrasi pihak ketiga (Payment Gateway, WhatsApp Cloud API) dipanggil dengan `curl`/`file_get_contents` bawaan PHP — **jangan** menambahkan Composer/SDK vendor tanpa persetujuan eksplisit, konsisten dengan prinsip "tanpa Composer".

## 2. Struktur File & Aturan 300 Baris

- File PHP flat di webroot, tanpa routing. Grup LMS memakai subfolder `lms/` (masih flat di dalamnya, tanpa routing).
- **File PHP yang melebihi 300 baris WAJIB dipecah menjadi partials:**
  - `index.php` → `partials/section_*.php`, `partials/modal_*.php`, `partials/index_scripts.php`.
  - `admin_login.php` → `partials/admin_login_{head,body,scripts}.php`.
  - `admin/dashboard.php` (router) → `admin/includes/*` (wrapper) + `admin/pages/*.php` (konten per `?page=`).
  - `admin/includes/head.php`, `modals.php`, `scripts.php` → wrapper yang meng-include partial dalam **urutan yang sama** seperti semula (CRUD dulu, lalu navigasi AJAX).
  - `lms/dashboard.php`, `lms/course.php` → `partials/lms_*.php` bila melebihi 300 baris.

## 3. Pola Endpoint Admin

Semua `admin/actions/manage_*.php`:
1. `session_start(); header('Content-Type: application/json');`
2. Guard `if (!isset($_SESSION['admin_logged_in']))` → JSON `Unauthorized`, exit.
3. `require_once '../../includes/db_config.php';`
4. Baca `$_POST['action']` = `create`/`update`/`delete` (+ `approve`/`reject` untuk testimoni, `reorder` untuk materi LMS).
5. Balas `json_encode(['status' => 'success'|'error', 'message' => '...'])`.

## 4. JS Admin — Jangan Baca `form.action`/`form.id`/`form.method`

Form admin punya `<input type="hidden" name="action">` dan `name="id"`. Named property itu **menimpa properti DOM**, sehingga `form.action` menjadi objek `HTMLInputElement` dan `fetch(form.action)` menghasilkan URL `[object HTMLInputElement]` (404).
- **Selalu pakai** `form.getAttribute('action')` / `form.getAttribute('method')`.
- `submitAjaxForm()` dan handler `.ajax-form` sudah mematuhi ini. Aturan ini juga berlaku untuk form baru di `admin/pages/lms_materials.php`.

## 5. Upload Gambar & File Materi

- Tujuan gambar: `uploads/{classes,gallery,instructors,certs,testimonials,examiners}/` — pastikan direktori ada & writable.
- Tujuan materi LMS: `uploads/materials/` — pastikan direktori ada & writable.
- Nama file: `uniqid().ext`.
- Whitelist ekstensi gambar: `jpg, jpeg, png, webp` (periksa `pathinfo(..., PATHINFO_EXTENSION)` lowercase).
- Whitelist ekstensi materi: `pdf, mp4` (video besar sebaiknya berupa URL eksternal/embed, bukan upload langsung, untuk menjaga ukuran repo/hosting).
- Simpan di DB sebagai **path relatif webroot** (mis. `uploads/classes/xxxx.jpg`, `uploads/materials/xxxx.pdf`).
- Di admin, render lewat helper `getImgSrc()` (didefinisikan di `admin/dashboard.php`); untuk materi non-gambar gunakan helper sejenis `getMaterialSrc()` bila dibutuhkan.

## 6. Auth & Sesi

- **Admin**: login `admin_login.php` (dengan partial). Logout: `admin_logout.php`. Guard: `$_SESSION['admin_logged_in']`. Admin dashboard redirect ke `admin_login.php` bila belum login.
- **Login admin TIDAK ditautkan dari UI publik** — tidak ada tombol/link di logo, navbar, atau footer publik yang mengarah ke `admin_login.php`. Akses hanya via URL langsung.
- **Peserta (LMS)**: login `user_login.php`, register `user_register.php`, logout `user_logout.php`. Guard: `$_SESSION['user_logged_in']` (dicek via `includes/auth_user.php`).
- Password admin & peserta di-hash bcrypt; verifikasi `password_verify()`.
- Admin default di seed schema: `admin` / `admin`. DB produksi: `superadmin` / `AdminMCM2026`.

## 7. CSRF

- **Form publik** wajib CSRF: `submit_booking.php`, `testimoni.php`, `create_transaction.php`, `user_register.php`, `user_login.php`.
  - Buat token: `$_SESSION['csrf_token'] = bin2hex(random_bytes(32));` (di `index.php`/`includes/header.php`).
  - Validasi: `hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])`.
- **Endpoint admin** tidak punya CSRF (konsisten dengan aplikasi).
- **Webhook eksternal** (`payment_webhook.php`, `chatbot_webhook.php`) **tidak** memakai CSRF/sesi sama sekali (dipanggil server-to-server oleh pihak ketiga) — sebagai gantinya **wajib** verifikasi signature/token resmi (lihat §8 & §9).

## 8. Payment Gateway (BARU)

- Kredensial (`PAYMENT_SERVER_KEY`, `PAYMENT_CLIENT_KEY`, `PAYMENT_MODE`, `PAYMENT_WEBHOOK_SIGNATURE_KEY`) **wajib** disimpan di `includes/config_secrets.php`, file ini **tidak boleh** di-commit ke VCS publik (tambahkan ke `.gitignore`).
- `create_transaction.php` hanya boleh membuat transaksi setelah validasi CSRF & data kelas valid; jangan pernah mempercayai `amount` dari input klien — hitung ulang dari `classes.price` di server.
- `payment_webhook.php` **wajib**:
  1. Memverifikasi signature/notifikasi sesuai dokumentasi Payment Gateway sebelum memproses payload apa pun.
  2. Bersifat **idempoten** — cek dulu status transaksi saat ini di DB sebelum update; jika sudah `paid`, jangan proses ulang (hindari double insert `enrollments`/double notifikasi).
  3. Menulis `payment_status`, `payment_gateway_ref`, dan `paid_at` dalam satu transaksi DB (`BEGIN`/`COMMIT`) bersamaan dengan insert `enrollments` bila `paid`.
- Jangan pernah menandai kursus dapat diakses (`enrollments`) hanya berdasarkan redirect sukses di browser — status resmi **hanya** berasal dari webhook server-to-server.
- Gunakan `PAYMENT_MODE=sandbox` di lingkungan pengembangan; jangan hardcode kunci produksi di kode.

## 9. Chatbot WhatsApp API (BARU)

- Kredensial (`WA_PHONE_NUMBER_ID`, `WA_ACCESS_TOKEN`, `WA_VERIFY_TOKEN`) disimpan di `includes/config_secrets.php`, sama seperti Payment Gateway.
- `chatbot_webhook.php` method `GET` **wajib** memvalidasi `hub.verify_token` sama persis dengan `WA_VERIFY_TOKEN` sebelum mengembalikan `hub.challenge`.
- `chatbot_webhook.php` method `POST` **wajib** memvalidasi bahwa payload berasal dari Meta (App Secret/signature header bila tersedia) sebelum diproses.
- Pencocokan intent dilakukan dengan pencocokan kata kunci sederhana (bukan regex kompleks/AI eksternal) agar mudah dirawat tanpa Composer/SDK tambahan; daftar intent & balasan disimpan sebagai array PHP di `includes/whatsapp_client.php` atau tabel sederhana, bukan hardcode berulang di banyak file.
- Semua pesan masuk/keluar dicatat ke `chat_messages` untuk audit — jangan menyimpan token akses di tabel ini.
- Nomor WhatsApp bisnis untuk chatbot **sama** dengan nomor admin (`6285793935707`) yang sudah dipakai di alur checkout/notifikasi, agar konsisten satu kanal komunikasi resmi.

## 10. LMS — Materi/Modul (BARU)

- Akses materi (`lms/course.php`, `lms/material.php`) **wajib** memvalidasi kepemilikan `enrollments` milik `user_id` sesi aktif untuk `class_id` yang diminta — validasi di server, bukan hanya menyembunyikan tombol di UI.
- `manage_materials.php` mengikuti pola §3 (guard admin, JSON response) dan menambah aksi `reorder` untuk mengubah `sort_order` materi dalam satu kelas.
- Materi bertipe video sebaiknya berupa URL embed eksternal (mis. YouTube unlisted) kecuali file kecil yang diunggah sesuai whitelist §5.
- Progres (`material_progress`) ditulis dari sisi user saat membuka/menandai materi selesai; jangan biarkan endpoint ini diakses tanpa guard `user_logged_in` + validasi enrollment.

## 11. Keamanan

- Semua query pakai **PDO prepared statements** — jangan interpolasi langsung.
- Escape output dinamis dengan `htmlspecialchars()`.
- Upload: whitelist ekstensi; jangan pernah mengeksekusi file upload.
- Jangan menulis secret/key ke kode atau repository — semua kredensial pihak ketiga (Payment Gateway, WhatsApp) di `includes/config_secrets.php` yang di-`.gitignore`.
- Endpoint webhook (`payment_webhook.php`, `chatbot_webhook.php`) tidak boleh mengekspos data internal (mis. daftar semua order) di response-nya — cukup balas sesuai kontrak API pihak ketiga (`200 OK` / `hub.challenge`).

## 12. Data & Kolom

- CRUD kelas (`?page=classes`) **wajib** memakai kolom `classes.start_date` dan tabel `class_categories`.
- Tabel `testimonials` memakai `status` ENUM `pending/approved/rejected`; **hanya `approved` yang tampil di publik**.
- Tabel `examiners`: isi `bio` dan `certifications` (dipisah koma) agar halaman profil penguji informatif.
- Tabel `orders` memakai kolom tambahan `payment_status` ENUM `unpaid/pending/paid/failed/expired`, `payment_method`, `payment_gateway_ref`, `paid_at` — lihat SCHEMA.md §9.
- Tabel baru `enrollments`, `materials`, `material_progress`, `chat_messages` — lihat SCHEMA.md §15–18.
- Upgrade DB lama manual (mis. `ALTER TABLE orders ADD COLUMN payment_status ...`). `schema.sql` adalah sumber kebenaran.

## 13. Gaya Penulisan

- **Tanpa komentar** di kode kecuali diminta.
- **Teks UI & konten seed berbahasa Indonesia** untuk semua yang terlihat pengguna.
- Konsisten dengan pola file tetangga (modal → `modal_*.php`, page → `admin/pages/*.php`, endpoint → `manage_*.php`, halaman LMS → `lms/*.php`).
- Tidak ada alat lint/format; pastikan `php -l` bersih dan halaman berfungsi di browser.

## 14. Verifikasi

1. `php -l` untuk setiap file yang diubah.
2. Uji alur di browser (publik, LMS, & admin), termasuk upload dan JSON endpoint.
3. Uji alur pembayaran dengan kredensial **sandbox** Payment Gateway (jangan pernah uji dengan kunci produksi) — pastikan `payment_webhook.php` idempoten dengan mengirim payload sukses dua kali dan memastikan `enrollments` tidak dobel.
4. Uji `chatbot_webhook.php` dengan payload contoh dari dokumentasi WhatsApp Cloud API (mode sandbox/test number bila tersedia).
5. Bila memungkinkan gunakan headless test (puppeteer-core + Chrome lokal) untuk alur form/modal/checkout/LMS.
