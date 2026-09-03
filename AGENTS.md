# AGENTS.md

Prosedural PHP 8, MySQL PDO, tanpa framework/composer/build/test. Serve dari repo root (`php -S 127.0.0.1:8000` atau Apache docroot). Frontend Bootstrap 5 + Font Awesome/AOS/Swiper via CDN. Verifikasi hanya `php -l file.php` + cek browser. Plugin OpenCode aktif: `.opencode/opencode.json` → `@dietrichgebert/ponytail`.

## Setup

- `mysql -u root mcm_db < database/schema.sql` — `config/database.php` konek ke `mcm_db`/`root`/`password`; ubah di file itu bila beda.
- `config/secrets.php` gitignored, opsional. Jika tidak ada, `includes/payment_gateway.php` + `includes/whatsapp_client.php` + `includes/cloudinary.php` + `tools/send_chatbot.php` jalan mode mock. Salin dari `config/secrets.example.php` untuk kredensial nyata (Midtrans `PAYMENT_*`, WA `WA_*`, Fonnte `FONNTE_TOKEN`, Cloudinary `CLOUDINARY_*`).
- DB lama butuh migrasi: `database/migration_*.sql` (cert verification `verify_token`, `class_categories`/`classes.start_date`, `sender_type`/`enrollment_mode`/online-offline/whatsapp_group/quiz_explanation). Tabel `certificates`, `certificate_templates`, `finance_transactions`, `chatbot_intents`, `chat_messages.sender_type` juga di-auto `CREATE TABLE IF NOT EXISTS` dari kode — `schema.sql` tetap otoritatif untuk instalasi baru.
- Admin seed: `admin`/`admin` (bcrypt). Dev DB tambahan: `superadmin`/`AdminMCM2026` (manual, tidak di schema).
- `php.ini` upload galeri: `upload_max_filesize=20M` `post_max_size=25M` `memory_limit=256M` `max_execution_time=120` (`PHP_INI_PERDIR` — harus `php.ini`/`.user.ini`/vhost, `ini_set` tidak mempan).

## Commands

- Dev root: `php -S 127.0.0.1:8000` ; subfolder bug (`$base_url`/`asset_src`): `php -S 127.0.0.1:8001 -t <parent>` lalu buka `/MitraCiptaMandiriWeb/...`
- Lint: `php -l <file.php>` — tidak ada lint/test/format lain.
- DB tools: `php tools/sync_db.php`, `php tools/migrate_local_to_cloudinary.php` (uploads→Cloudinary), `php tools/remigrate_to_new_cloud.php` (antar cloud).

## Architecture

- No routing — URL = path file. Webroot hanya `index.php` + `admin_login.php`; folder: `config/`, `auth/`, `payment/`, `chat/`, `pages/`+`pages/partials/` (`section_*`,`modal_*`,`index_scripts.php`), `lms/`+`lms/partials/`, `admin/` (`dashboard.php` router `?page=` + `pages/` + `actions/` + `includes/`), `legacy/process_checkout.php` orphan jangan jadi acuan, `tools/`.
- Shared layout: `includes/header.php`+`includes/footer.php` di-include dari root maupun subfolder. `$base_url` dihitung filesystem (`SCRIPT_FILENAME` relatif ke `dirname(__DIR__)`), semua link/asset pakai `$base_url`; `require` DB selalu `__DIR__`. `$is_home = basename(SCRIPT_FILENAME)==='index.php'` → halaman non-home butuh `navbar-solid` + link `index.php#...`.
- `admin/dashboard.php` router include urutan: `admin/includes/head.php` → `styles_head*`+`sidebar`+`mobile_header`+`offcanvas`; `admin/pages/*.php`; `admin/includes/modals.php` (→ `modal_*.php`); `admin/includes/scripts.php` (→ `scripts_common.php`/`scripts_entities*.php`/`scripts_navigation.php`/`scripts_modals.php` + `styles_bottomnav.php`). Jangan ubah urutan.
- Aturan 300 baris: file >300 baris wajib pecah ke partials (`index.php` → `pages/partials/`, `auth/admin_login.php` → `admin/includes/admin_login_*`). Jaga wrapper tipis.

## Auth, CSRF, Images

- Admin: `$_SESSION['admin_logged_in']` guard di tiap `admin_*`; login `auth/admin_login.php`. Semua `admin/actions/manage_*.php` handler JSON `$_POST['action']=create/update/delete` dari dashboard. User LMS: `includes/auth_user.php` + `enrollments` gate.
- CSRF publik (`$_SESSION['csrf_token']` dari `index.php`/`includes/header.php`) dicek `hash_equals` di `payment/create_transaction.php`, `chat/chat_api.php`, `lms/progress.php`/`lms/profile.php`, `auth/user_*`. Admin endpoint tidak pakai CSRF.
- Gambar: semua upload lewat `includes/cloudinary.php` → `uploadImageToCloudinary($file, 'mcm/<feature>')` (validasi 5MB jpg/png/webp/heic, signed `sha1(folder&timestamp+secret)`), simpan `secure_url` + `public_id` (`*_public_id`, `settings.logo_public_id`), hapus via `deleteImageFromCloudinary($publicId|url)`. Folder: `mcm/{gallery,classes,instructors,certs,cert_templates,facilities,finance,testimonials,logo}`. Fallback `asset_src($path)` (publik) / `getImgSrc($path)` (admin) — handle `https://res.cloudinary.com/...` absolut vs `uploads/...` relatif; homepage root render mentah. Direktori Cloudinary baru `mcm/*` terbentuk setelah upload pertama ke subfolder itu.
- WhatsApp: `includes/whatsapp_client.php` (`wa_match_intent`/`wa_intent_reply` baca `chatbot_intents`, token `{classes}`/`{prices}` dinamis; fallback hardcoded). Fonnte `FONNTE_TOKEN` → `Authorization: <token>` ke `api.fonnte.com/send` (`target` tanpa `+`, `message`); `web-` visitor tidak ke Fonnte. `chat_messages.sender_type ENUM(visitor,bot,admin)`; widget anon `web-<hex12>` di `localStorage.mcmChatVid` + cookie 90d.

## Flows & Gotchas

- Checkout aktif: `pages/partials/modal_checkout.php` → `payment/create_transaction.php` — normalisasi WA `0...`→`62...` validasi `/^\+62[0-9]{8,13}$/`, email ada→link user, email baru→buat akun (`validateFullName`, pwd≥6) + auto-login + `orders` `unpaid` → `pg_create_transaction()` (`PAYMENT_MODE=mock`→`payment/payment_mock.php`) → `payment_status.php` → `payment_webhook.php` → `enrollments` (`order.user_id`). `pg_base_url()` bangun `host/payment/payment_mock.php` tanpa subfolder prefix — salah bila app bukan docroot. `generate_pdf.php` hanya `payment_status=paid`, lainnya redirect.
- LMS: `lms/{dashboard,course,material,certificate,profile}.php` (progress berurutan, materi tanpa quiz→toggle `lms/progress.php`, dengan quiz→lulus ≥70% di `lms/quiz.php` (PG `correct_option`, Essay ≥½ keyword `essay_answer`) baru `material_progress.completed=1`; akses modul terkunci diblok `lms/material.php`; kelola soal admin `?page=materials`→`manage_quiz.php`). Sertifikat `lms/certificate.php` 100% materi → `certificates` `MCM-YYYY-NNNN` UNIQUE `user_id+class_id`, template per kelas `certificate_templates` (`default/elegant/modern/premium`, `accent_color`, `bg_image`), QR client-side `assets/js/qrcode.min.js` → `pages/verify_certificate.php?token=...` (`verify_token` 40hex `random_bytes(20)`, UNIQUE `uq_cert_verify_token`).
- Testimoni via `lms/profile.php` → `testimonials` `pending` (`user_id`,`graduation_year`,`job`); `pages/testimoni.php` hanya redirect; home `section_testimoni.php` tampil `approved` klik→modal alumni; admin `?page=testimonials`.
- Galeri `manage_gallery.php` selalu JSON murni (`ob_start`+`respondJson`, `parseIniSize` untuk `G/M/K`); `scripts_common.php` `console.error` bila parse gagal.
- **Jangan baca `form.action`/`form.id`/`form.method` sebagai property JS** di dashboard — ada `<input name="action|id">` yang override → `[object HTMLInputElement]` 404. Pakai `form.getAttribute('action')` (sudah fix di `submitAjaxForm`/`.ajax-form`).
- Teks UI/seed DB Bahasa Indonesia — pertahankan.

<!-- antislop:start -->
## antislop
For UI, copy, people, mobile layout, or code comments work, load the antislop skill for the task:
- Core filter, always on: `antislop`
Before starting, ask the user when antislop applies: during the work, or after it is done.
<!-- antislop:end -->
