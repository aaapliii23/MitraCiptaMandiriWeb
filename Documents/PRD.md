# PRD.md

Product Requirements Document — Website **Mitra Cipta Mandiri (MCM)**, Lembaga Pelatihan & Kursus Profesional.

## 0. Status Revisi

| # | Item | Status |
|---|---|---|
| 1 | Hapus tombol Login Admin dari logo/header publik + rapikan struktur kode | **SELESAI** |
| 2 | Fitur Pelatihan/Kursus (katalog, detail) | **SELESAI** |
| 3 | Fitur Testimoni (rating, ulasan, foto, moderasi admin) | **SELESAI** |
| 4 | Pemilihan Penguji + cek latar belakang | **SELESAI** |
| 5 | Chatbot terintegrasi WhatsApp API | **BARU — revisi ini** |
| 6 | Payment / pembayaran kursus sungguhan | **BARU — revisi ini** |
| 7 | LMS — materi/modul per kelas yang dibeli | **BARU — revisi ini** |

## 1. Ringkasan Produk

Website untuk Lembaga Pelatihan & Kursus MCM yang menampilkan program pelatihan, profil pengajar/penguji, legalitas, testimoni peserta, memfasilitasi **pembayaran kursus sungguhan** melalui Payment Gateway, memberi akses ke **LMS (materi/modul)** setelah pembayaran berhasil, serta menyediakan **chatbot WhatsApp** untuk pertanyaan seputar kursus, jadwal, dan pembayaran. Admin mengelola seluruh konten, transaksi, dan materi dari satu dashboard.

## 2. Tujuan & Sasaran

- Memperkenalkan program pelatihan MCM dan meningkatkan kepercayaan calon peserta (testimoni, penguji kredibel, legalitas).
- Mempermudah calon peserta membeli & membayar kursus secara online, tanpa bergantung sepenuhnya pada konfirmasi manual WhatsApp.
- Memberi akses belajar mandiri (LMS) kepada peserta yang sudah membayar.
- Menyediakan kanal tanya-jawab otomatis (chatbot WhatsApp) untuk mengurangi beban respons manual admin.
- Mempermudah admin mengelola konten, transaksi, dan materi tanpa keterampilan teknis.

## 3. Target Pengguna (Persona)

### Calon Peserta / Publik
- Ingin mencari kursus (make-up, pariwisata, digital, kuliner, terapi, metodologi).
- Ingin mengecek latar belakang penguji sebelum memutuskan.
- Membaca testimoni peserta lain dan ingin menulis ulasan.
- Ingin bertanya cepat via WhatsApp (chatbot) tentang jadwal/harga/pembayaran sebelum membeli.

### Peserta Terdaftar (BARU)
- Sudah membuat akun (`users`) dan membeli minimal satu kursus.
- Login ke LMS untuk mengakses materi/modul kelas yang sudah lunas.
- Melihat progres belajarnya sendiri.

### Admin (Superadmin)
- Mengelola program, galeri, instruktur, sertifikasi, kategori, testimoni, penguji, pesanan, admin, laporan.
- Memoderasi testimoni (setujui/tolak) sebelum tampil publik.
- Memantau status pembayaran transaksi dan mengelola materi LMS per kelas.

## 4. Fitur & Kebutuhan Fungsional

### 4.1 Publik — Landing & Konten
| ID | Fitur | Deskripsi | Prioritas |
|---|---|---|---|
| F01 | Hero slider | Slide banner di beranda | P1 |
| F02 | Tentang | Ringkasan profil lembaga | P1 |
| F03 | Galeri kegiatan | Grid foto kegiatan (dinamis) | P2 |
| F04 | Paket pelatihan | Daftar kelas: nama, kategori, harga, tanggal mulai, fitur, gambar | P1 |
| F05 | Detail kelas | Halaman detail + tombol beli/checkout | P1 |
| F06 | Legalitas/sertifikasi | Halaman dokumen legalitas & sertifikasi | P2 |
| F07 | Visi & Misi | Halaman about | P2 |
| F31 | Tanpa tombol Login Admin di UI publik | Logo & navbar publik tidak menampilkan/menautkan Login Admin | P1 — **selesai** |

### 4.2 Publik — Booking, Checkout & Pembayaran (DIREVISI)
| ID | Fitur | Deskripsi | Prioritas |
|---|---|---|---|
| F08 | Booking | Form booking konsultasi (nama, WhatsApp, email, layanan, tanggal) dengan validasi CSRF | P1 |
| F09 | Checkout | Isi data pemesan → pilih kursus → (opsional) pilih penguji → buat transaksi | P1 |
| F32 | Pembayaran online | Integrasi Payment Gateway (mis. Midtrans/Xendit): user diarahkan ke halaman pembayaran resmi, sistem menerima status via webhook | P1 — **baru** |
| F33 | Gating akses kursus | Kursus/LMS hanya dapat diakses bila `payment_status = paid`; bila `failed`/belum bayar, LMS terkunci | P1 — **baru** |
| F34 | Halaman status pembayaran | User dapat melihat status transaksi (pending/paid/failed/expired) dan mengulang pembayaran bila gagal | P1 — **baru** |
| F10 | Nomor WhatsApp | Validasi `/^\+62[0-9]{8,13}$/` pada form yang membutuhkan nomor WA | P1 |
| F11 | Bukti pemesanan | Halaman `generate_pdf.php` siap cetak, terbuka setelah pembayaran `paid` | P2 |

### 4.3 Publik — Profil Penguji
| ID | Fitur | Deskripsi | Prioritas |
|---|---|---|---|
| F12 | Pilih penguji | Dropdown "Pilih Penguji" → detail latar belakang (foto, spesialisasi, bio, sertifikasi) | P1 |
| F13 | Data dinamis | Data penguji dari DB `examiners`, dikelola admin | P1 |
| F14 | Empty state | Hint bila belum memilih / belum ada data | P2 |

### 4.4 Publik — Testimoni
| ID | Fitur | Deskripsi | Prioritas |
|---|---|---|---|
| F15 | Submit testimoni | Rating bintang 1-5, nama, ulasan, program (opsional), foto (opsional), CSRF | P1 |
| F16 | Moderasi | Testimoni tersimpan `pending`; tampil publik hanya bila `approved` | P1 |
| F17 | Section testimoni | Tampil di beranda "Apa Kata Mereka?" + tombol "Tulis Testimoni" | P1 |

### 4.5 Chatbot Terintegrasi WhatsApp API (BARU)
| ID | Fitur | Deskripsi | Prioritas |
|---|---|---|---|
| F35 | Tombol chat mengambang | Tersedia di semua halaman publik & LMS, membuka WhatsApp dengan pesan prefilled | P1 |
| F36 | Auto-reply berbasis intent | Bot menjawab pertanyaan umum: daftar kursus, jadwal, harga, status pembayaran, cara akses modul | P1 |
| F37 | Eskalasi ke admin | Pertanyaan di luar intent yang dikenali diteruskan/ditandai untuk dijawab manual oleh admin di WhatsApp Business | P2 |
| F38 | Log percakapan (admin) | Riwayat chat tersimpan dan dapat ditinjau admin (`chat_messages`) | P3 |

### 4.6 LMS — Materi/Modul Kursus (BARU)
| ID | Fitur | Deskripsi | Prioritas |
|---|---|---|---|
| F39 | Akun peserta | Registrasi & login peserta (tabel `users`, kini aktif dipakai) | P1 |
| F40 | Dashboard LMS | Daftar kelas yang sudah dibeli & lunas milik peserta yang login | P1 |
| F41 | Modul per kelas | Daftar materi (video/PDF/teks) sesuai kelas yang dibeli, urut sesuai kurikulum | P1 |
| F42 | Progres belajar | Penanda materi selesai per peserta, progress bar per kelas | P2 |
| F43 | Gating enrollment | Materi hanya dapat diakses bila peserta memiliki `enrollments` aktif untuk kelas tsb | P1 |

### 4.7 Admin — Manajemen Konten
| ID | Fitur | Deskripsi | Prioritas |
|---|---|---|---|
| F18 | Kelas/Program | CRUD kelas (nama, kategori, tanggal mulai, deskripsi, fitur, harga, gambar) | P1 |
| F19 | Kategori | CRUD kategori pelatihan (name + slug) | P2 |
| F20 | Galeri | CRUD + upload banyak gambar | P2 |
| F21 | Instruktur | CRUD + foto | P2 |
| F22 | Sertifikasi | CRUD + gambar | P2 |
| F23 | Penguji | CRUD (nama, spesialisasi, bio, sertifikasi, foto) | P1 |
| F44 | Materi LMS | CRUD materi per kelas (judul, tipe, konten/file/URL, urutan) | P1 — **baru** |

### 4.8 Admin — Transaksi & Moderasi
| ID | Fitur | Deskripsi | Prioritas |
|---|---|---|---|
| F24 | Pesanan | List pesanan, ubah status (pending/confirmed/cancelled), lihat detail | P1 |
| F25 | Booking | Kelola status booking | P2 |
| F26 | Testimoni | Setujui/tolak/edit/hapus + rata-rata rating | P1 |
| F27 | Laporan & rekap | Statistik/datanya via `reports_data.php`, termasuk rekap pembayaran | P2 |
| F45 | Monitoring pembayaran | Lihat status pembayaran tiap transaksi (unpaid/pending/paid/failed/expired), sinkron dari webhook | P1 — **baru** |
| F46 | Kelola chatbot (opsional) | Lihat riwayat chat, ubah balasan otomatis intent | P3 — **baru** |

### 4.9 Admin — Sistem
| ID | Fitur | Deskripsi | Prioritas |
|---|---|---|---|
| F28 | Login admin | username + password (bcrypt), akses hanya via URL langsung `admin_login.php` (tidak ditautkan dari UI publik) | P1 |
| F29 | Kelola admin | CRUD admin + ganti password | P2 |
| F30 | Pengaturan web | **Stub** (selalu sukses; tidak ada tabel `settings`) | P3 |

## 5. Kebutuhan Non-Fungsional

- **Kinerja**: halaman ringan (HTML + CSS/JS CDN, tanpa build); siap disajikan langsung dari webroot.
- **Kompatibilitas**: PHP 8, MySQL 5.7+/8, Bootstrap 5; responsive mobile-first.
- **Keamanan**: PDO prepared statements; escape output; upload whitelist; auth sesi admin & peserta; CSRF pada form publik; verifikasi signature pada webhook pembayaran & chatbot.
- **Keandalan pembayaran**: webhook idempoten (transaksi yang sama tidak diproses dua kali); status transaksi selalu bersumber dari webhook gateway, bukan asumsi klien.
- **Usability**: UI Bahasa Indonesia; admin satu dashboard dengan modal; konfirmasi destruktif via SweetAlert2.
- **Maintainability**: file flat dengan aturan pecah partial di atas 300 baris; endpoint JSON terpisah; kredensial pihak ketiga terpisah dari kode (bukan hardcode).

## 6. User Stories (Ringkas)

- Sebagai calon peserta, saya dapat melihat daftar program dan detailnya, memilih penguji, lalu membeli dan **membayar online** kursus tersebut.
- Sebagai peserta yang sudah membayar, saya dapat login ke LMS dan mengakses materi/modul kelas saya, serta melihat progres belajar saya.
- Sebagai calon peserta, saya dapat bertanya melalui WhatsApp dan mendapat balasan otomatis untuk pertanyaan umum, atau dijawab admin bila pertanyaan lebih spesifik.
- Sebagai peserta, saya dapat menulis testimoni; testimoni tampil setelah disetujui admin.
- Sebagai admin, saya dapat mengelola seluruh konten, memoderasi testimoni, memantau status pembayaran, dan mengelola materi LMS.
- Sebagai admin, saya dapat menambah/mengubah/menghapus data penguji yang tampil di halaman profil penguji.
- Sebagai pengunjung, saya **tidak lagi melihat** tombol Login Admin di logo/navbar publik.

## 7. Alur Kunci

### Alur Booking (konsultasi)
```
Beranda → Pilih program → submit_booking.php (simpan `bookings`) → sukses
```

### Alur Checkout & Pembayaran (DIREVISI)
```
class_detail.php → (opsional) examiners.php pilih penguji → modal checkout →
create_transaction.php (insert `orders`, payment_status=unpaid) →
redirect ke Payment Gateway → user bayar →
payment_webhook.php (verifikasi signature, update payment_status) →
jika paid: insert `enrollments` + notifikasi WhatsApp → payment_status.php sukses → akses lms/dashboard.php
jika failed/expired: payment_status.php gagal → user dapat mengulang
```

### Alur Testimoni
```
testimoni.php (submit, CSRF) → insert status `pending` →
admin `?page=testimonials` approve/reject →
hanya `approved` tampil di home
```

### Alur Profil Penguji
```
Admin `?page=examiners` CRUD → DB `examiners` →
examiners.php (dropdown pilih → detail latar belakang)
```

### Alur Chatbot WhatsApp (BARU)
```
User kirim pesan WA → Meta WhatsApp Cloud API →
chatbot_webhook.php (POST) → cocokkan intent →
balas otomatis via whatsapp_client.php ATAU tandai untuk admin →
catat ke `chat_messages`
```

### Alur LMS (BARU)
```
Pembayaran paid → enrollments aktif →
lms/dashboard.php (daftar kelas ter-enroll) →
lms/course.php (daftar materi) → lms/material.php (buka materi, tandai selesai) →
progress bar terupdate
```

## 8. Metrik Keberhasilan

- Jumlah booking & transaksi tersimpan, serta **rasio transaksi `paid` vs `unpaid`/`failed`**.
- Jumlah testimoni masuk dan rasio persetujuan.
- Kelengkapan data penguji (semua penguji punya bio & sertifikasi).
- **Rasio pertanyaan chatbot yang terjawab otomatis** vs yang harus dieskalasi ke admin.
- **Rata-rata progres penyelesaian materi LMS** per kelas.
- Kepuasan admin: semua CRUD (termasuk materi LMS & monitoring pembayaran) selesai tanpa buka DB manual.

## 9. Ruang Lingkup (Out of Scope)

- Multi-bahasa.
- Fitur sosial (komentar/like/forum diskusi) di LMS — cukup materi + progres sederhana.
- Sertifikat kelulusan otomatis (PDF sertifikat) — dapat menjadi revisi lanjutan.
- Refund/pembatalan pembayaran otomatis — ditangani manual oleh admin melalui dashboard Payment Gateway.
- Chatbot berbasis AI generatif bebas topik — chatbot revisi ini terbatas pada intent yang telah didefinisikan (FAQ + data kursus).
