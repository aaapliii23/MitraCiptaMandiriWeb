# SCHEMA.md

Dokumentasi skema database **Mitra Cipta Mandiri (MCM)** — database `mcm_db`, MySQL via PDO.

Sumber kebenaran: `database/schema.sql`. Untuk upgrade DB lama yang belum punya tabel/kolom baru, lihat **Bab 19 (Upgrade)**.

## 1. Relasi Antar Tabel

```
classes ──┬──< orders          (orders.class_id → classes.id)
          ├──< testimonials    (testimonials.class_id → classes.id)
          ├──< enrollments     (enrollments.class_id → classes.id)
          └──< materials       (materials.class_id → classes.id)

classes.category         →  class_categories.name  (relasi logis, tanpa FK)
classes.examiner_id      →  examiners.id            (relasi logis, opsional — penguji terpilih saat checkout)

orders ──── 1:1 ────< enrollments   (enrollments.order_id → orders.id, dibuat otomatis saat payment_status = paid)

users ──┬──< orders          (orders.user_id → users.id, opsional bila checkout tanpa akun)
        ├──< enrollments     (enrollments.user_id → users.id)
        ├──< material_progress (material_progress.user_id → users.id)
        └──< chat_messages   (chat_messages.user_id → users.id, opsional/nullable — chat bisa dari nomor tak terdaftar)

materials ──< material_progress   (material_progress.material_id → materials.id)

bookings                 →  berdiri sendiri
admins                   →  berdiri sendiri
instructors, certifications, gallery →  berdiri sendiri
```

## 2. `bookings`

Data booking (konsultasi) dari form publik.

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | int(11) PK AI | |
| name | varchar(255) NOT NULL | Nama pemesan |
| whatsapp | varchar(20) NOT NULL | Nomor WhatsApp |
| email | varchar(255) NOT NULL | Email |
| service | varchar(100) NOT NULL | Layanan/program |
| booking_date | date NULL | Tanggal booking |
| created_at | timestamp DEFAULT CURRENT_TIMESTAMP | |

## 3. `admins`

Akun admin dashboard.

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | int(11) PK AI | |
| username | varchar(50) NOT NULL UNIQUE | |
| password | varchar(255) NOT NULL | Hash bcrypt |
| created_at | timestamp DEFAULT CURRENT_TIMESTAMP | |

Seed: `admin` / `admin` (hash bcrypt). DB produksi: `superadmin` / `AdminMCM2026`. **Tidak** ditautkan dari UI publik — akses hanya via URL langsung `admin_login.php`.

## 4. `classes`

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

## 5. `class_categories`

Kategori pelatihan.

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | int(11) PK AI | |
| name | varchar(100) NOT NULL | |
| slug | varchar(100) NOT NULL UNIQUE | Dibuat otomatis dari name |
| created_at | timestamp DEFAULT CURRENT_TIMESTAMP | |

Seed: Kecantikan, Kesehatan, Metodologi, Digital, Kuliner, Pariwisata.

## 6. `instructors`

Instruktur.

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | int(11) PK AI | |
| name | varchar(100) NOT NULL | |
| specialization | varchar(150) NOT NULL | Spesialisasi/gelar |
| image | varchar(255) NOT NULL | |
| created_at | timestamp DEFAULT CURRENT_TIMESTAMP | |

Seed: Bunga Lestari, Agus Wijaya, Dewi Anggraini.

## 7. `certifications`

Dokumen legalitas/sertifikasi lembaga.

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | int(11) PK AI | |
| title | varchar(150) NOT NULL | |
| description | text NULL | |
| image | varchar(255) NOT NULL | |
| created_at | timestamp DEFAULT CURRENT_TIMESTAMP | |

Seed: Surat Keterangan Kemenkumham, Sertifikat Akreditasi Lembaga, Piagam Penghargaan Pendidikan.

## 8. `users` (KINI AKTIF DIPAKAI)

Akun peserta/calon peserta — sebelumnya tersedia di schema namun belum dipakai; **kini aktif** untuk login peserta & akses LMS.

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | int(11) PK AI | |
| name | varchar(100) NOT NULL | |
| email | varchar(100) NOT NULL UNIQUE | |
| phone | varchar(20) NOT NULL | |
| password | varchar(255) NOT NULL | Hash bcrypt |
| created_at | timestamp DEFAULT CURRENT_TIMESTAMP | |

## 9. `orders` (DIREVISI — kolom pembayaran ditambahkan)

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
| examiner_id | int(11) NULL | FK → `examiners.id`, penguji yang dipilih user (opsional) |
| amount | int(11) NOT NULL | Dihitung ulang di server dari `classes.price`, jangan percaya input klien |
| status | ENUM('pending','confirmed','cancelled') DEFAULT 'pending' | Status administratif pesanan (dipakai admin, terpisah dari status pembayaran) |
| **payment_status** | **ENUM('unpaid','pending','paid','failed','expired') DEFAULT 'unpaid'** | **BARU** — status resmi dari Payment Gateway via webhook |
| **payment_method** | **varchar(50) NULL** | **BARU** — mis. `bank_transfer`, `qris`, `credit_card` (dari payload gateway) |
| **payment_gateway_ref** | **varchar(100) NULL** | **BARU** — ID/reference transaksi dari Payment Gateway |
| **paid_at** | **timestamp NULL** | **BARU** — waktu pembayaran dikonfirmasi lunas |
| created_at | timestamp DEFAULT CURRENT_TIMESTAMP | |

## 10. `gallery`

Foto galeri kegiatan.

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | int(11) PK AI | |
| category | varchar(50) NOT NULL | Mis. public_speaking, tata_rias |
| title | varchar(100) NOT NULL | |
| image | varchar(255) NOT NULL | Path atau URL |
| created_at | timestamp DEFAULT CURRENT_TIMESTAMP | |

## 11. `testimonials`

Testimoni peserta (moderasi admin).

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | int(11) PK AI | |
| name | varchar(100) NOT NULL | |
| rating | tinyint(1) DEFAULT 5 | 1–5 |
| review | text NOT NULL | |
| image | varchar(255) NULL | Foto profil opsional |
| class_id | int(11) NULL | FK → `classes.id` |
| status | ENUM('pending','approved','rejected') DEFAULT 'pending' | Hanya `approved` tampil di publik |
| created_at | timestamp DEFAULT CURRENT_TIMESTAMP | |

Seed: Siti Rahma (5, class 1), Budi Santoso (5, class 5), Dewi Lestari (4, class 7) — semuanya `approved`.

## 12. `examiners`

Penguji/asesor — ditampilkan di halaman profil penguji (`examiners.php`), dapat dipilih saat checkout.

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | int(11) PK AI | |
| name | varchar(100) NOT NULL | |
| specialization | varchar(150) NOT NULL | |
| bio | text NULL | Latar belakang |
| certifications | text NULL | Dipisah koma (mis. `Komunikasi,BNSP`) |
| image | varchar(255) NOT NULL | |
| created_at | timestamp DEFAULT CURRENT_TIMESTAMP | |

Seed: Drs. Ahmad Jaelani (Asesor Public Speaking), Rina Wijaya S.Pd (Ahli Tata Rias & Estetika), H. Supardi (Pakar Pijat Kesehatan).

## 13. `enrollments` (BARU)

Kepesertaan aktif — dibuat otomatis saat `orders.payment_status` menjadi `paid`; menjadi dasar gating akses LMS.

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | int(11) PK AI | |
| user_id | int(11) NOT NULL | FK → `users.id` |
| class_id | int(11) NOT NULL | FK → `classes.id` |
| order_id | int(11) NOT NULL UNIQUE | FK → `orders.id` (1 order lunas = 1 enrollment) |
| enrolled_at | timestamp DEFAULT CURRENT_TIMESTAMP | Waktu enrollment dibuat (= waktu `paid_at` order) |

Index unik disarankan pada (`user_id`, `class_id`) untuk mencegah duplikasi enrollment bila terjadi retry webhook di luar penanganan idempoten aplikasi.

## 14. `materials` (BARU)

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

## 15. `material_progress` (BARU)

Progres belajar per peserta per materi.

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | int(11) PK AI | |
| user_id | int(11) NOT NULL | FK → `users.id` |
| material_id | int(11) NOT NULL | FK → `materials.id` |
| completed | tinyint(1) DEFAULT 0 | 0 = belum, 1 = selesai |
| completed_at | timestamp NULL | Waktu ditandai selesai |

Index unik disarankan pada (`user_id`, `material_id`).

## 16. `chat_messages` (BARU, opsional/P3)

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

## 17. Catatan Upload

Path gambar disimpan **relatif ke webroot**: `uploads/{classes,gallery,instructors,certs,testimonials,examiners}/uniqid.ext`. File materi LMS disimpan di `uploads/materials/uniqid.ext` (whitelist `pdf, mp4`; video besar sebaiknya embed URL, bukan upload). Render admin memakai `getImgSrc()` (prefix `../`). Seed awal sebagian memakai `assets/img/logo.png` sebagai fallback.

## 18. Kredensial Pihak Ketiga (bukan tabel — konfigurasi)

Kredensial Payment Gateway & WhatsApp Cloud API **tidak** disimpan di database, melainkan di `includes/config_secrets.php` (di luar VCS publik):
- `PAYMENT_SERVER_KEY`, `PAYMENT_CLIENT_KEY`, `PAYMENT_MODE`, `PAYMENT_WEBHOOK_SIGNATURE_KEY`
- `WA_PHONE_NUMBER_ID`, `WA_ACCESS_TOKEN`, `WA_VERIFY_TOKEN`

## 19. Upgrade DB Lama (Manual)

DB lama yang dibuat sebelum fitur tertentu perlu upgrade manual karena kode memakainya lebih dulu:

```sql
-- Kategori kelas & tanggal mulai
CREATE TABLE IF NOT EXISTS class_categories (...);  -- lihat schema.sql
ALTER TABLE classes ADD COLUMN start_date DATE;

-- Fitur testimoni & penguji (tabel ini di-create otomatis juga oleh endpoint admin,
-- tapi dianjurkan dibuat via schema.sql untuk konsistensi)
CREATE TABLE testimonials (...);  -- Bab 11
CREATE TABLE examiners (...);     -- Bab 12
ALTER TABLE classes ADD COLUMN examiner_id INT(11) NULL;

-- Pembayaran (BARU)
ALTER TABLE orders ADD COLUMN user_id INT(11) NULL;
ALTER TABLE orders ADD COLUMN examiner_id INT(11) NULL;
ALTER TABLE orders ADD COLUMN payment_status ENUM('unpaid','pending','paid','failed','expired') DEFAULT 'unpaid';
ALTER TABLE orders ADD COLUMN payment_method VARCHAR(50) NULL;
ALTER TABLE orders ADD COLUMN payment_gateway_ref VARCHAR(100) NULL;
ALTER TABLE orders ADD COLUMN paid_at TIMESTAMP NULL;

-- LMS (BARU)
CREATE TABLE IF NOT EXISTS enrollments (...);        -- Bab 13
CREATE TABLE IF NOT EXISTS materials (...);           -- Bab 14
CREATE TABLE IF NOT EXISTS material_progress (...);   -- Bab 15

-- Chatbot (BARU, opsional)
CREATE TABLE IF NOT EXISTS chat_messages (...);        -- Bab 16
```

Endpoint `manage_*.php` melakukan `CREATE TABLE IF NOT EXISTS` untuk tabel masing-masing agar tetap berfungsi bila DB belum di-upgrade. Untuk `orders`, endpoint terkait (`create_transaction.php`, `payment_webhook.php`) sebaiknya memvalidasi keberadaan kolom pembayaran saat startup dan memberi pesan jelas bila DB belum di-upgrade, alih-alih gagal diam-diam.
