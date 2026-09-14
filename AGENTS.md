# AGENTS.md

Prosedural PHP 8 (lokal 8.4), MySQL PDO, tanpa framework/composer/build/test. Serve dari repo root (`php -S 127.0.0.1:8000 router.php`, Apache pakai `.htaccess`). Frontend Bootstrap 5 + Font Awesome/AOS/Swiper via CDN. Verifikasi hanya `php -l file.php` + cek browser. Plugin OpenCode: `.opencode/opencode.json` → `@dietrichgebert/ponytail`.

## Setup

- Instal baru: `mysql -u root mcm_db < database/schema.sql` (otoritatif). `config/database.php` default `mcm_db`/`root`/`password`, bisa override via `config/secrets.php` (`DB_*`) atau env.
- `config/secrets.php` + `config/mail_config.php` gitignored. Salin dari `config/secrets.example.php`. Tanpa secrets: payment error eksplisit (mock sudah dihapus), Cloudinary/Fonnte/WA return error + `error_log`, bukan mode diam-diam.
- DB lama: jalankan `database/migration_*.sql` yang relevan. Selain itu kode melakukan auto-migrate saat runtime (`CREATE TABLE IF NOT EXISTS` / `ALTER` di `config/database.php`, `admin/dashboard.php`, `lms/quiz.php`+`certificate.php`, `pg_ensure_payment_columns()`) — jangan duplikasi, cek dulu sebelum buat tabel.
- Seed admin ada di `schema.sql` (`admins`: admin, superadmin, dizasa — password bcrypt, lihat dump untuk kredensial dev aktual).
- Upload galeri butuh `php.ini`/`user.ini`/vhost (`PHP_INI_PERDIR`, `ini_set` tidak mempan): `upload_max_filesize=20M post_max_size=25M memory_limit=256M max_execution_time=120`.

## Commands

- Dev: `php -S 127.0.0.1:8000 router.php` (router + fallback 404 di `index.php` menangani docroot repo vs subfolder `/MitraCiptaMandiriWeb/`).
- Lint: `php -l <file.php>` — tidak ada lint/test/format lain.
- DB/tools: `php tools/sync_db.php`, `php tools/simulate_webhook.php`, `php tools/migrate_local_to_cloudinary.php` (uploads→Cloudinary), `php tools/remigrate_to_new_cloud.php` (antar cloud).

## Architecture

- No routing — URL = path file. Homepage `index.php` (tolak `?page=` dengan 404) + `pages/partials/section_*` + `modal_detail`/`modal_checkout` + `index_scripts.php`. `admin_login.php` root hanya redirect ke `auth/admin_login.php`.
- `admin/dashboard.php` router `?page=` (whitelist `dashboard,orders,classes,gallery,facilities,instructors,certs,reports,finance,admins,settings,categories,testimonials,materials,chat,chatbot,users,doku_channels`, selainnya 404). Guard `$_SESSION['admin_logged_in']` + `mcm_check_session_timeout(1800)`; user LMS via `includes/auth_user.php` (+ `enrollments` gate per kelas).
- Urutan include dashboard: `admin/includes/head.php` (`styles_head*`, `nav_bottom`, `sidebar`, `mobile_header`, `offcanvas`) → `admin/pages/<page>.php` → `modals.php` (`modal_*.php`) → `scripts.php` (`scripts_common.php`, `scripts_entities.php`, `styles_bottomnav.php`, `scripts_navigation.php`, `scripts_modals.php`, `scripts_notifications.php`). Jangan ubah urutan. `scripts_quiz.php` + `scripts_entities_crud.php` orphan (tidak di-include `scripts.php`, logika quiz live di `scripts_entities.php`) — jangan edit.
- Shared layout `includes/header.php`+`footer.php`. `$base_url` dihitung dari filesystem (`SCRIPT_FILENAME` vs `dirname(__DIR__)`); semua link/asset pakai `$base_url`, `require` selalu `__DIR__`-relatif. `$is_home = basename(SCRIPT_FILENAME)==='index.php'` → halaman non-home pakai `navbar-solid` + link `index.php#...`.
- Gambar: `asset_src()` (publik, di `config/database.php`) / `getImgSrc()` (admin, di `dashboard.php`) — kembalikan URL Cloudinary apa adanya, prefix relatif untuk lokal. Semua upload via `uploadImageToCloudinary($file, 'mcm/<fitur>')` (5MB, jpg/jpeg/png/webp/heic, signed `sha1(folder&timestamp+secret)`), simpan `secure_url` + `*_public_id`, hapus via `deleteImageFromCloudinary($publicId|url)`.
- CSRF dipakai di publik DAN admin. Publik: `$_SESSION['csrf_token']` dicek `hash_equals` di `payment/create_transaction.php`, `chat/chat_api.php`, `lms/progress.php`/`quiz.php`/`profile.php`, `auth/user_*`. Admin: `mcm_csrf_token()` di `head.php` (`meta` + `window.MCM_CSRF_TOKEN` + hidden input tiap `modal_*.php`), diverifikasi `mcm_csrf_verify()` di semua `admin/actions/manage_*.php` (+ rate-limit `mcm_rate_limit` di aksi sensitif).

## Flows & Gotchas

- Checkout: `modal_checkout.php` → `payment/create_transaction.php` (CSRF; WA dinormalisasi `0…`→`62…`, validasi `/^62[0-9]{9,13}$/`; email ada→link user, email baru→buat akun `validateFullName` min 2 kata + pwd≥6 + auto-login; `orders` `unpaid`; harga dihitung server-side dari `price_online/price_offline`) → redirect `custom_payment.php?order=` (VA via `pg_generate_va`, QRIS `pg_generate_qris`, e-wallet `pg_generate_ewallet`) → `payment_status.php`/`check_status.php` → `payment_webhook.php` (verifikasi `pg_verify_webhook`) → `enrollments`. `payment/payment_mock.php.bak` nonaktif — jangan acu; `pg_mode()` memetakan `mock`→`production`. `legacy/process_checkout.php` orphan, jangan jadi acuan.
- LMS: `lms/{dashboard,course,material,certificate,profile}.php`, progres berurutan (modul terkunci diblok). Tanpa kuis→toggle `lms/progress.php`; dengan kuis→`lms/quiz.php` harus **100% benar semua soal** (PG `correct_option` persis; essay ≥½ keyword ≥4 huruf dari `essay_answer`) baru `material_progress.completed=1`. Sertifikat 100% → `certificates` `MCM-YYYY-NNNN`, `verify_token` 40hex, verifikasi `pages/verify_certificate.php?token=`.
- Testimoni ditulis dari `lms/profile.php` (butuh lulus 100% + sertifikat) → status `pending`; home hanya tampil `approved`; admin `?page=testimonials`. `pages/testimoni.php` hanya redirect.
- `manage_gallery.php` harus selalu JSON murni (`ob_start`+`respondJson`, `parseIniSize` untuk `G/M/K`); jangan echo/blok HTML sebelum header.
- **Jangan baca `form.action`/`.id`/`.method` sebagai property JS di dashboard** — ada `<input name="action|id">` yang meng-override → 404 `[object HTMLInputElement]`. Pakai `form.getAttribute(...)` (sudah benar di `scripts_common/entities/quiz`).
- Chat: `chat_messages.sender_type ENUM(visitor,bot,admin)`; thread anon `web-<hex12>` (`localStorage.mcmChatVid` + cookie 90d, `user_id` selalu NULL), thread siswa `user-<id>`, thread WA nomor dinormalisasi. `web-` tidak dikirim ke Fonnte. Fonnte: header `Authorization: <token>` ke `api.fonnte.com/send` (`target` tanpa `+`).
- Teks UI/seed DB Bahasa Indonesia — pertahankan.

<!-- antislop:start -->
## antislop
For UI, copy, people, mobile layout, or code comments work, load the antislop skill for the task:
- Core filter, always on: `antislop`
Before starting, ask the user when antislop applies: during the work, or after it is done.
<!-- antislop:end -->
