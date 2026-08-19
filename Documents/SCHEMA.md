# SCHEMA.md

Dokumentasi skema database **Mitra Cipta Mandiri (MCM)** — database `mcm_db`, MySQL via PDO.

Sumber kebenaran: `database/schema.sql`. Untuk upgrade DB lama yang belum punya tabel/kolom baru, lihat **Bab 18 (Upgrade)**.

## 1. Relasi Antar Tabel

```
classes ──┬──< orders          (orders.class_id → classes.id)
          ├──< testimonials    (testimonials.class_id → classes.id)
          ├──< enrollments     (enrollments.class_id → classes.id)
          ├──< materials       (materials.class_id → classes.id)
          ├──< certificates    (certificates.class_id → classes.id)
          └──< certificate_templates (certificate_templates.class_id → classes.id)

classes.category         →  class_categories.name  (relasi logis, tanpa FK)
classes.instructor_id    →  instructors.id          (relasi logis, opsional — instruktur terpilih saat checkout)

orders ──── 1:1 ────< enrollments   (enrollments.order_id → orders.id, dibuat otomatis saat payment_status = paid)

users ──┬──< orders          (orders.user_id → users.id, opsional bila checkout tanpa akun)
        ├──< enrollments     (enrollments.user_id → users.id)
        ├──< material_progress (material_progress.user_id → users.id)
        ├──< testimonials    (testimonials.user_id → users.id — testimoni kini di-submit dari halaman User LMS)
        └──< chat_messages   (chat_messages.user_id → users.id, opsional/nullable — chat bisa dari nomor tak terdaftar)

materials ──< material_progress   (material_progress.material_id → materials.id)

admins                   →  berdiri sendiri
instructors, certifications, gallery, finance_transactions →  berdiri sendiri
```

## 2. `admins`

Akun admin dashboard.

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | int(11) PK AI | |
| username | varchar(50) NOT NULL UNIQUE | |
| password | varchar(255) NOT NULL | Hash bcrypt |
| created_at | timestamp DEFAULT CURRENT_TIMESTAMP | |

Seed: `admin` / `admin` (hash bcrypt). DB produksi: `superadmin` / `AdminMCM2026`. **Tidak** ditautkan dari UI publik — akses hanya via URL langsung `admin_login.php`.

## 3. `classes`

Program/kursus pelatihan.

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | int(11) PK AI | |
| name | varchar(100) NOT NULL | Nama program |
| start_date | date NULL | Tanggal mulai |
| category | varchar(50) NOT NULL | Kategori (relasi logis ke `class_categories.name`) |
| description | text NOT NULL | Deskripsi |
| image | varchar(255) NOT NULL | Path gambar |
| features | text NOT NULL | Daftar fitur (JSON array string) |
| price | int(11) DEFAULT 500000 | Harga (Rupiah), sumber kebenaran perhitungan `orders.amount` |
| created_at | timestamp DEFAULT CURRENT_TIMESTAMP | |

## 4. `class_categories`

Kategori pelatihan.

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | int(11) PK AI | |
| name | varchar(100) NOT NULL | |
| slug | varchar(100) NOT NULL UNIQUE | Dibuat otomatis dari name |
| created_at | timestamp DEFAULT CURRENT_TIMESTAMP | |

Seed: Kecantikan, Kesehatan, Metodologi, Digital, Kuliner, Pariwisata.

## 5. `instructors` (DIREVISI — wajib kategori)

Instruktur (merangkap data Penguji; bagian **Penguji/Asesor dihapus**).

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | int(11) PK AI | |
| name | varchar(100) NOT NULL | |
| **category** | **varchar(100) NOT NULL** | **BARU — kategori Instruktur & Penguji, wajib diisi** (relasi logis ke `class_categories.name`) |
| specialization | varchar(150) NOT NULL | Spesialisasi/gelar |
| bio | text NULL | Latar belakang (untuk halaman Profil Penguji Ahli) |
| certifications | text NULL | Dipisah koma (mis. `Komunikasi,BNSP`) |
| image | varchar(255) NOT NULL | |
| created_at | timestamp DEFAULT CURRENT_TIMESTAMP | |

Seed: Bunga Lestari, Agus Wijaya, Dewi Anggraini (masing-masing dengan kategori).

## 6. `certifications`

Dokumen legalitas/sertifikasi lembaga.

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | int(11) PK AI | |
| title | varchar(150) NOT NULL | |
| description | text NULL | |
| image | varchar(255) NOT NULL | |
| created_at | timestamp DEFAULT CURRENT_TIMESTAMP | |

Seed: Surat Keterangan Kemenkumham, Sertifikat Akreditasi Lembaga, Piagam Penghargaan Pendidikan.

## 7. `users` (KINI AKTIF DIPAKAI)

Akun peserta/calon peserta — **wajib mencantumkan Nama Asli** (validasi super ketat saat register: minimal 2 kata, huruf alfabet/spasi/hubung saja, tanpa angka/simbol).

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | int(11) PK AI | |
| name | varchar(100) NOT NULL | Nama Asli (validasi ketat) |
| email | varchar(100) NOT NULL UNIQUE | |
| phone | varchar(20) NOT NULL | |
| password | varchar(255) NOT NULL | Hash bcrypt |
| created_at | timestamp DEFAULT CURRENT_TIMESTAMP | |

## 8. `orders` (DIREVISI — kolom pembayaran ditambahkan)

Pesanan/transaksi dari checkout kursus.

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | int(11) PK AI | |
| order_number | varchar(50) NOT NULL UNIQUE | `ORD-` + uniqid + time |
| user_id | int(11) NULL | FK → `users.id` (opsional; checkout dapat dilakukan tanpa akun terdaftar penuh, namun akses LMS tetap butuh akun) |
| customer_name | varchar(100) NOT NULL | |
| customer_phone | varchar(20) NOT NULL | |
| customer_email | varchar(100) NULL | |
| customer_address | text NULL | |
| customer_institution | varchar(100) NULL | |
| class_id | int(11) NOT NULL | FK → `classes.id` |
| instructor_id | int(11) NULL | FK → `instructors.id`, **Instruktur** yang dipilih user saat payment (opsional; pengganti `examiner_id`) |
| amount | int(11) NOT NULL | Dihitung ulang di server dari `classes.price`, jangan percaya input klien |
| status | ENUM('pending','confirmed','cancelled') DEFAULT 'pending' | Status administratif pesanan (dipakai admin, terpisah dari status pembayaran) |
| **payment_status** | **ENUM('unpaid','pending','paid','failed','expired') DEFAULT 'unpaid'** | **BARU** — status resmi dari Payment Gateway via webhook |
| **payment_method** | **varchar(50) NULL** | **BARU** — mis. `bank_transfer`, `qris`, `credit_card` (dari payload gateway) |
| **payment_gateway_ref** | **varchar(100) NULL** | **BARU** — ID/reference transaksi dari Payment Gateway |
| **paid_at** | **timestamp NULL** | **BARU** — waktu pembayaran dikonfirmasi lunas |
| created_at | timestamp DEFAULT CURRENT_TIMESTAMP | |

## 9. `gallery` (DIREVISI — foto kegiatan beranda)

Foto galeri kegiatan. Foto dengan flag "tampil di beranda" menjadi **slide foto** halaman depan.

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | int(11) PK AI | |
| category | varchar(50) NOT NULL | Mis. public_speaking, tata_rias |
| title | varchar(100) NOT NULL | |
| image | varchar(255) NOT NULL | Path atau URL |
| **show_on_home** | **tinyint(1) DEFAULT 0** | **BARU — 1 = Foto Kegiatan tampil sebagai slide di halaman depan** |
| created_at | timestamp DEFAULT CURRENT_TIMESTAMP | |

## 10. `testimonials` (DIREVISI — via LMS + data alumni)

Testimoni peserta (moderasi admin). **Input dipindah ke halaman User di LMS** (`lms/profile.php`), bukan form publik.

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | int(11) PK AI | |
| user_id | int(11) NULL | **BARU — FK → `users.id`**, pengirim (dari sesi LMS) |
| name | varchar(100) NOT NULL | |
| rating | tinyint(1) DEFAULT 5 | 1–5 |
| review | text NOT NULL | |
| image | varchar(255) NULL | Foto profil opsional |
| class_id | int(11) NULL | FK → `classes.id` |
| **graduation_year** | **varchar(10) NULL** | **BARU — data alumni (angkatan)** |
| **job** | **varchar(150) NULL** | **BARU — data alumni (pekerjaan saat ini)** |
| status | ENUM('pending','approved','rejected') DEFAULT 'pending' | Hanya `approved` tampil di publik |
| created_at | timestamp DEFAULT CURRENT_TIMESTAMP | |

**Klik kartu testimoni di beranda → modal data alumni** (angkatan, pekerjaan, program).

Seed: Siti Rahma (5, class 1), Budi Santoso (5, class 5), Dewi Lestari (4, class 7) — semuanya `approved`.

## 11. ~~`examiners`~~ (DIHAPUS)

Bagian **Penguji (Asesor) dihapus**. Data penguji kini dikelola sebagai **Instruktur** (tabel `instructors`, wajib `category`); halaman Profil Penguji Ahli (`examiners.php`) menampilkan data Instruktur. Tabel `examiners` tidak lagi dipakai; migrasi data dilakukan dengan menyalin baris yang relevan ke `instructors` lalu drop tabel bila sudah aman.

## 12. `enrollments` (BARU)

Kepesertaan aktif — dibuat otomatis saat `orders.payment_status` menjadi `paid`; menjadi dasar gating akses LMS.

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | int(11) PK AI | |
| user_id | int(11) NOT NULL | FK → `users.id` |
| class_id | int(11) NOT NULL | FK → `classes.id` |
| order_id | int(11) NOT NULL UNIQUE | FK → `orders.id` (1 order lunas = 1 enrollment) |
| enrolled_at | timestamp DEFAULT CURRENT_TIMESTAMP | Waktu enrollment dibuat (= waktu `paid_at` order) |

Index unik disarankan pada (`user_id`, `class_id`) untuk mencegah duplikasi enrollment bila terjadi retry webhook di luar penanganan idempoten aplikasi.

## 13. `materials` (BARU)

Materi/modul LMS per kelas, dikelola admin (`admin/pages/lms_materials.php`).

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | int(11) PK AI | |
| class_id | int(11) NOT NULL | FK → `classes.id` |
| title | varchar(150) NOT NULL | Judul materi |
| type | ENUM('video','pdf','text') NOT NULL | Tipe konten |
| content | text NOT NULL | URL embed (video), path file (`uploads/materials/xxxx.pdf`), atau isi teks terformat |
| sort_order | int(11) DEFAULT 0 | Urutan tampil dalam kelas |
| created_at | timestamp DEFAULT CURRENT_TIMESTAMP | |

## 14. `material_progress` (BARU)

Progres belajar per peserta per materi.

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | int(11) PK AI | |
| user_id | int(11) NOT NULL | FK → `users.id` |
| material_id | int(11) NOT NULL | FK → `materials.id` |
| completed | tinyint(1) DEFAULT 0 | 0 = belum, 1 = selesai |
| completed_at | timestamp NULL | Waktu ditandai selesai |

Index unik disarankan pada (`user_id`, `material_id`).

## 15. `chat_messages` (BARU, opsional/P3)

Log percakapan chatbot WhatsApp untuk audit & tinjauan admin.

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | int(11) PK AI | |
| user_id | int(11) NULL | FK → `users.id`, nullable (nomor pengirim belum tentu terdaftar sebagai user) |
| wa_number | varchar(20) NOT NULL | Nomor WhatsApp pengirim/tujuan |
| direction | ENUM('in','out') NOT NULL | `in` = pesan dari user, `out` = balasan bot/admin |
| message | text NOT NULL | Isi pesan |
| matched_intent | varchar(50) NULL | Intent yang cocok (mis. `jadwal`, `harga`, `pembayaran`), NULL bila tidak cocok/dieskalasi |
| created_at | timestamp DEFAULT CURRENT_TIMESTAMP | |

**Catatan keamanan:** tabel ini tidak boleh menyimpan token akses/API key. Hanya isi pesan & metadata percakapan.

## 16. Catatan Upload

Path gambar disimpan **relatif ke webroot**: `uploads/{classes,gallery,instructors,certs,testimonials}/uniqid.ext` (dir `examiners` **dihapus**). File materi LMS disimpan di `uploads/materials/uniqid.ext` (whitelist `pdf, mp4`; video besar sebaiknya embed URL, bukan upload). Render admin memakai `getImgSrc()` (prefix `../`). Seed awal sebagian memakai `assets/img/logo.png` sebagai fallback.

## 17. Kredensial Pihak Ketiga (bukan tabel — konfigurasi)

Kredensial Payment Gateway & WhatsApp Cloud API **tidak** disimpan di database, melainkan di `includes/config_secrets.php` (di luar VCS publik):
- `PAYMENT_SERVER_KEY`, `PAYMENT_CLIENT_KEY`, `PAYMENT_MODE`, `PAYMENT_WEBHOOK_SIGNATURE_KEY`
- `WA_PHONE_NUMBER_ID`, `WA_ACCESS_TOKEN`, `WA_VERIFY_TOKEN`

## 18. Upgrade DB Lama (Manual)

DB lama yang dibuat sebelum fitur tertentu perlu upgrade manual karena kode memakainya lebih dulu:

```sql
-- Kategori kelas & tanggal mulai
CREATE TABLE IF NOT EXISTS class_categories (...);  -- lihat schema.sql
ALTER TABLE classes ADD COLUMN start_date DATE;

-- Fitur testimoni & penguji (tabel ini di-create otomatis juga oleh endpoint admin,
-- tapi dianjurkan dibuat via schema.sql untuk konsistensi)
CREATE TABLE testimonials (...);  -- Bab 10
CREATE TABLE examiners (...);     -- Bab 11
ALTER TABLE classes ADD COLUMN examiner_id INT(11) NULL;

-- Pembayaran (BARU)
ALTER TABLE orders ADD COLUMN user_id INT(11) NULL;
ALTER TABLE orders ADD COLUMN examiner_id INT(11) NULL;
ALTER TABLE orders ADD COLUMN payment_status ENUM('unpaid','pending','paid','failed','expired') DEFAULT 'unpaid';
ALTER TABLE orders ADD COLUMN payment_method VARCHAR(50) NULL;
ALTER TABLE orders ADD COLUMN payment_gateway_ref VARCHAR(100) NULL;
ALTER TABLE orders ADD COLUMN paid_at TIMESTAMP NULL;

-- LMS (BARU)
CREATE TABLE IF NOT EXISTS enrollments (...);        -- Bab 12
CREATE TABLE IF NOT EXISTS materials (...);           -- Bab 13
CREATE TABLE IF NOT EXISTS material_progress (...);   -- Bab 14

-- Chatbot (BARU, opsional)
CREATE TABLE IF NOT EXISTS chat_messages (...);        -- Bab 15

-- Revisi berikutnya (BARU)
-- Instruktur wajib kategori
ALTER TABLE instructors ADD COLUMN category VARCHAR(100) NOT NULL DEFAULT '';
ALTER TABLE classes ADD COLUMN instructor_id INT(11) NULL;   -- instruktur terpilih saat checkout

-- Galeri: flag slide foto beranda
ALTER TABLE gallery ADD COLUMN show_on_home TINYINT(1) DEFAULT 0;

-- Testimoni: submit dari LMS + data alumni
ALTER TABLE testimonials ADD COLUMN user_id INT(11) NULL;
ALTER TABLE testimonials ADD COLUMN graduation_year VARCHAR(10) NULL;
ALTER TABLE testimonials ADD COLUMN job VARCHAR(150) NULL;

-- Order: penguji (examiner_id) diganti instruktur (instructor_id)
ALTER TABLE orders CHANGE COLUMN examiner_id instructor_id INT(11) NULL;

-- Quiz: tipe soal PG/Essay + kunci jawaban essay
ALTER TABLE quiz_questions ADD COLUMN question_type ENUM('mcq','essay') DEFAULT 'mcq';
ALTER TABLE quiz_questions ADD COLUMN essay_answer TEXT NULL;

-- Rekap keuangan & template sertifikat (Bab 19–20)
CREATE TABLE IF NOT EXISTS finance_transactions (...);      -- Bab 19
CREATE TABLE IF NOT EXISTS certificate_templates (...);     -- Bab 20
```

Endpoint `manage_*.php` melakukan `CREATE TABLE IF NOT EXISTS` untuk tabel masing-masing agar tetap berfungsi bila DB belum di-upgrade. Untuk `orders`, endpoint terkait (`create_transaction.php`, `payment_webhook.php`) sebaiknya memvalidasi keberadaan kolom pembayaran saat startup dan memberi pesan jelas bila DB belum di-upgrade, alih-alih gagal diam-diam.

## 19. `finance_transactions` (BARU)

Rekap **Uang Masuk & Uang Keluar** — termasuk catatan **sewa**. Dikelola admin (`admin/pages/finance.php`).

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | int(11) PK AI | |
| type | ENUM('in','out') NOT NULL | `in` = uang masuk (pemasukan), `out` = uang keluar (pengeluaran) |
| category | varchar(50) NOT NULL | Kategori, mis. `pemasukan_kursus`, `sewa`, `gaji`, `operasional`, `lainnya` |
| description | text NULL | Keterangan transaksi |
| amount | int(11) NOT NULL | Nominal (Rupiah, integer positif; divalidasi server) |
| transaction_date | date NOT NULL | Tanggal transaksi |
| created_at | timestamp DEFAULT CURRENT_TIMESTAMP | |

Laporan keuangan (termasuk cetak) dihitung dari tabel ini: total masuk, total keluar, saldo = masuk − keluar, dikelompokkan per kategori/periode.

## 20. `certificate_templates` (BARU)

**Template sertifikat berbeda-beda per kelas** — dipilih saat seting kelas; `lms/certificate.php` memakai template sesuai kelas saat menerbitkan sertifikat.

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | int(11) PK AI | |
| name | varchar(150) NOT NULL | Nama template |
| class_id | int(11) NULL | FK → `classes.id`; NULL = template umum/default |
| layout | varchar(50) DEFAULT 'default' | Layout/bingkai (mis. `default`, `elegant`, `modern`, `premium`) |
| bg_image | varchar(255) NULL | Gambar latar/bingkai sertifikat (opsional) |
| accent_color | varchar(20) NULL | Warna aksen template (hex) |
| is_default | tinyint(1) DEFAULT 0 | Template default bila kelas tidak menetapkan khusus |
| created_at | timestamp DEFAULT CURRENT_TIMESTAMP | |

Sertifikat terbit di tabel `certificates` (nomor unik `MCM-YYYY-NNNN`, UNIQUE `user_id`+`class_id`) hanya saat seluruh materi kelas selesai.

## 21. Soal Essay pada Test Materi (BARU)

Tabel `quiz_questions` diperluas agar test materi memuat **pilihan ganda + Essay**:

| Kolom | Tipe | Keterangan |
|---|---|---|
| question_type | ENUM('mcq','essay') DEFAULT 'mcq' | `mcq` = pilihan ganda (dinilai `correct_option`), `essay` = jawaban bebas |
| question | text NOT NULL | Pertanyaan |
| option_a..option_d | varchar(255) | Hanya dipakai untuk `mcq` (opsional/kosong untuk essay) |
| correct_option | ENUM('a','b','c','d') NULL | Hanya dipakai untuk `mcq` |
| essay_answer | text NULL | **BARU — kunci jawaban/referensi untuk soal essay** (dinilai sesuai ketentuan) |
| sort_order | int(11) DEFAULT 0 | |

`quiz_attempts` tetap menyimpan skor PG; bagian essay dinilai sesuai ketentuan (manual admin/kunci jawaban) dan digabungkan untuk kelulusan (≥70%).
