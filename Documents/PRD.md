# PRD.md

Product Requirements Document — Website **Mitra Cipta Mandiri (MCM)**, Lembaga Pelatihan & Kursus Profesional.

## 0. Status Revisi
| # | Item | Status |
|---|---|---|
| 1 | Hapus tombol Login Admin dari logo/header publik + rapikan struktur kode | **SELESAI** |
| 2 | Fitur Pelatihan/Kursus (katalog, detail) | **SELESAI** |
| 3 | Fitur Testimoni (rating, ulasan, foto, moderasi admin) | **SELESAI** |
| 4 | Chatbot terintegrasi WhatsApp API | **SELESAI** |
| 5 | Payment / pembayaran kursus sungguhan | **SELESAI** |
| 6 | LMS — materi/modul per kelas yang dibeli + gating quiz | **SELESAI** |
| 7 | Rekap Uang Masuk & Uang Keluar (termasuk catatan sewa) | **BARU — revisi ini** |
| 8 | Hapus bagian Penguji (Asesor); Instruktur & Penguji wajib punya kategori | **BARU — revisi ini** |
| 9 | Slide foto di halaman depan + input Foto Kegiatan (masuk ke galeri) | **BARU — revisi ini** |
| 10 | Pemilihan Instruktur (opsional) saat payment | **BARU — revisi ini** |
| 11 | Input testimoni dipindah ke halaman User (LMS); testimoni bisa diklik untuk lihat data alumni | **BARU — revisi ini** |
| 12 | Profil Penguji Ahli hanya menampilkan data Penguji (Instruktur) | **BARU — revisi ini** |
| 13 | Template sertifikat berbeda-beda per kelas (setelah kelas selesai) | **BARU — revisi ini** |
| 14 | Tampilan cetak laporan keuangan diubah | **BARU — revisi ini** |
| 15 | Test materi memuat soal Essay | **BARU — revisi ini** |
| 16 | Register user wajib Nama Asli (validasi super ketat) | **BARU — revisi ini** |
| 17 | Footer + ikon TikTok; alamat kantor bisa diklik ke Google Maps | **BARU — revisi ini** |

## 1. Ringkasan Produk
Website untuk Lembaga Pelatihan & Kursus MCM yang menampilkan program pelatihan, profil pengajar/penguji (instruktur dengan kategori), legalitas, testimoni peserta (dapat diklik untuk melihat data alumni), memfasilitasi **pembayaran kursus sungguhan** melalui Payment Gateway dengan opsi **memilih Instruktur**, memberi akses ke **LMS (materi/modul + test yang memuat soal Essay + sertifikat bertemplate per kelas)** setelah pembayaran berhasil, serta menyediakan **chatbot WhatsApp** untuk pertanyaan seputar kursus, jadwal, dan pembayaran. Halaman depan menampilkan **slide foto kegiatan** (dari galeri). Admin mengelola seluruh konten, transaksi, **rekap uang masuk/keluar (termasuk sewa)**, dan materi dari satu dashboard.

## 2. Tujuan & Sasaran
- Memperkenalkan program pelatihan MCM dan meningkatkan kepercayaan calon peserta (testimoni berdata alumni, instruktur kredibel dengan kategori, legalitas).
- Mempermudah calon peserta membeli & membayar kursus secara online, dengan opsi memilih instruktur pengampu kelas.
- Memberi akses belajar mandiri (LMS) kepada peserta yang sudah membayar, termasuk test dengan soal Essay dan sertifikat bertemplate per kelas setelah kelas selesai.
- Menyediakan kanal tanya-jawab otomatis (chatbot WhatsApp) untuk mengurangi beban respons manual admin.
- Membantu admin memantau kesehatan keuangan melalui rekap uang masuk/keluar (termasuk sewa) dan cetak laporan keuangan dengan tampilan yang jelas.
- Mempermudah admin mengelola konten, transaksi, dan materi tanpa keterampilan teknis.

## 3. Target Pengguna (Persona)
### Calon Peserta / Publik
- Ingin mencari kursus (make-up, pariwisata, digital, kuliner, terapi, metodologi).
- Melihat slide foto kegiatan di halaman depan dan galeri untuk menilai suasana kelas.
- Ingin mengecek latar belakang penguji (instruktur) beserta kategorinya sebelum memutuskan.
- Membaca testimoni peserta lain (bisa diklik untuk melihat data alumni) dan ingin menulis ulasan.
- Ingin bertanya cepat via WhatsApp (chatbot) tentang jadwal/harga/pembayaran sebelum membeli.

### Peserta Terdaftar (BARU)
- Sudah membuat akun (`users`, wajib Nama Asli) dan membeli minimal satu kursus.
- Login ke LMS untuk mengakses materi/modul kelas yang sudah lunas, mengerjakan test (termasuk soal Essay).
- Menulis testimoni dari halaman User di LMS (bukan lagi form publik di halaman depan).
- Melihat sertifikat kelulusan dengan template yang sesuai kelasnya setelah kelas selesai.

### Admin (Superadmin)
- Mengelola program, galeri (foto kegiatan untuk slide beranda), instruktur (wajib kategori), sertifikasi, kategori, testimoni, pesanan, admin, laporan.
- Memoderasi testimoni (setujui/tolak) sebelum tampil publik.
- Memantau status pembayaran transaksi, mengelola materi LMS, mengelola rekap uang masuk/keluar (termasuk sewa), dan mencetak laporan keuangan.
- Mengatur template sertifikat per kelas dan soal test (termasuk Essay) per materi.

## 4. Fitur & Kebutuhan Fungsional
### 4.1 Publik — Landing & Konten
| ID | Fitur | Deskripsi | Prioritas |
|---|---|---|---|
| F01 | Slide foto halaman depan | Hero berisi slide foto kegiatan (dari galeri, Swiper); foto yang ditandai "tampil di beranda" muncul sebagai slide | P1 |
| F02 | Tentang | Ringkasan profil lembaga | P1 |
| F03 | Galeri kegiatan | Grid foto kegiatan (dinamis); input **Foto Kegiatan** untuk halaman depan disimpan di sini | P2 |
| F04 | Paket pelatihan | Daftar kelas: nama, kategori, harga, tanggal mulai, fitur, gambar | P1 |
| F05 | Detail kelas | Halaman detail + tombol beli/checkout | P1 |
| F06 | Legalitas/sertifikasi | Halaman dokumen legalitas & sertifikasi | P2 |
| F07 | Visi & Misi | Halaman about | P2 |
| F58 | Footer sosial & alamat | Footer memuat ikon **TikTok**; alamat kantor (Jl. K.H.P Hasan Mustopa No.57, Neglasari, Cibeunying Kaler, Bandung) **bisa diklik langsung ke Google Maps** | P1 |
| F31 | Tanpa tombol Login Admin di UI publik | Logo & navbar publik tidak menampilkan/menautkan Login Admin | P1 — **selesai** |

### 4.2 Publik — Checkout & Pembayaran (DIREVISI)
| ID | Fitur | Deskripsi | Prioritas |
|---|---|---|---|
| F09 | Checkout | Isi data pemesan → pilih kursus → (opsional) **pilih Instruktur** → buat transaksi | P1 |
| F52 | Pilih Instruktur (opsional) | Saat payment user bisa memilih instruktur pengampu kelas (opsional); data tersimpan di pesanan | P1 |
| F32 | Pembayaran online | Integrasi Payment Gateway (mis. Midtrans/Xendit): user diarahkan ke halaman pembayaran resmi, sistem menerima status via webhook | P1 — **selesai** |
| F33 | Gating akses kursus | Kursus/LMS hanya dapat diakses bila `payment_status = paid`; bila `failed`/belum bayar, LMS terkunci | P1 — **selesai** |
| F34 | Halaman status pembayaran | User dapat melihat status transaksi (pending/paid/failed/expired) dan mengulang pembayaran bila gagal | P1 — **selesai** |
| F10 | Nomor WhatsApp | Validasi `/^\+62[0-9]{8,13}$/` pada form yang membutuhkan nomor WA | P1 |
| F11 | Bukti pemesanan | Halaman `generate_pdf.php` siap cetak, terbuka setelah pembayaran `paid` | P2 |

### 4.3 Publik — Profil Penguji (Instruktur) (DIREVISI)
> Bagian **Penguji (Asesor) dihapus**; data penguji kini adalah **Instruktur** dari tabel `instructors` yang **wajib memiliki kategori**.
| ID | Fitur | Deskripsi | Prioritas |
|---|---|---|---|
| F12 | Profil Penguji Ahli | Halaman hanya menampilkan data **Penguji (Instruktur)**: foto, nama, **kategori**, spesialisasi, bio, sertifikasi | P1 |
| F13 | Data dinamis | Data dari DB `instructors` (dengan kategori), dikelola admin | P1 |
| F14 | Empty state | Hint bila belum ada data | P2 |

### 4.4 Testimoni (DIREVISI)
| ID | Fitur | Deskripsi | Prioritas |
|---|---|---|---|
| F15 | Submit testimoni | **Input dipindah ke halaman User di LMS** (rating bintang 1-5, nama, ulasan, program, foto, CSRF) — bukan lagi form publik di halaman depan | P1 |
| F16 | Moderasi | Testimoni tersimpan `pending`; tampil publik hanya bila `approved` | P1 |
| F17 | Section testimoni | Tampil di beranda "Apa Kata Mereka?"; **kartu bisa diklik untuk melihat data alumni** (angkatan, pekerjaan, program) | P1 |
| F54 | Detail data alumni | Klik kartu testimoni → modal/panel berisi data alumni (nama, program, angkatan, pekerjaan) | P2 |

### 4.5 Chatbot Terintegrasi WhatsApp API (BARU)
| ID | Fitur | Deskripsi | Prioritas |
|---|---|---|---|
| F35 | Tombol chat mengambang | Tersedia di semua halaman publik & LMS, membuka WhatsApp dengan pesan prefilled | P1 |
| F36 | Auto-reply berbasis intent | Bot menjawab pertanyaan umum: daftar kursus, jadwal, harga, status pembayaran, cara akses modul | P1 |
| F37 | Eskalasi ke admin | Pertanyaan di luar intent yang dikenali diteruskan/ditandai untuk dijawab manual oleh admin di WhatsApp Business | P2 |
| F38 | Log percakapan (admin) | Riwayat chat tersimpan dan dapat ditinjau admin (`chat_messages`) | P3 |

### 4.6 LMS — Materi/Modul Kursus (DIREVISI)
| ID | Fitur | Deskripsi | Prioritas |
|---|---|---|---|
| F39 | Akun peserta | Registrasi & login peserta (tabel `users`); **wajib mencantumkan Nama Asli** | P1 |
| F57 | Validasi Nama Asli (super ketat) | Saat register nama wajib diisi, minimal 2 kata, huruf alfabet/spasi/hubung saja (tanpa angka & simbol), dan panjang wajar | P1 |
| F40 | Dashboard LMS | Daftar kelas yang sudah dibeli & lunas milik peserta yang login | P1 |
| F41 | Modul per kelas | Daftar materi (video/PDF/teks) sesuai kelas yang dibeli, urut sesuai kurikulum | P1 |
| F42 | Progres belajar | Penanda materi selesai per peserta, progress bar per kelas | P2 |
| F43 | Gating enrollment | Materi hanya dapat diakses bila peserta memiliki `enrollments` aktif untuk kelas tsb | P1 |
| F56 | Test materi + Essay | Test per materi berisi soal pilihan ganda **dan soal Essay**; kelulusan menetapkan gating modul berikutnya | P1 |
| F55 | Sertifikat bertemplate | Setiap kelas selesai → sertifikat dengan **template berbeda-beda sesuai ketentuan kelas** (AKHIR) | P2 |

### 4.7 Admin — Manajemen Konten (DIREVISI)
| ID | Fitur | Deskripsi | Prioritas |
|---|---|---|---|
| F18 | Kelas/Program | CRUD kelas (nama, kategori, tanggal mulai, deskripsi, fitur, harga, gambar, template sertifikat) | P1 |
| F19 | Kategori | CRUD kategori pelatihan (name + slug) | P2 |
| F20 | Galeri | CRUD + upload banyak gambar; **flag "Foto Kegiatan" untuk tampil sebagai slide di beranda** | P2 |
| F47 | Slide foto beranda | Admin memilih foto galeri yang tampil sebagai slide halaman depan | P1 |
| F21 | Instruktur | CRUD + foto; **input wajib kategori** (kategori Instruktur & Penguji) | P2 |
| F22 | Sertifikasi | CRUD + gambar | P2 |
| F23 | ~~Penguji~~ | **DIHAPUS** — bagian Penguji (Asesor) dihapus; data digabung ke Instruktur yang punya kategori | — |
| F44 | Materi LMS | CRUD materi per kelas (judul, tipe, konten/file/URL, urutan) | P1 — **selesai** |
| F51 | Kategori Instruktur & Penguji | Wajib memilih kategori saat menginput Instruktur (data penguji = instruktur ber-kategori) | P1 |

### 4.8 Admin — Transaksi & Moderasi
| ID | Fitur | Deskripsi | Prioritas |
|---|---|---|---|
| F24 | Pesanan | List pesanan, ubah status (pending/confirmed/cancelled), lihat detail | P1 |
| F26 | Testimoni | Setujui/tolak/edit/hapus + rata-rata rating | P1 |
| F27 | Laporan & rekap | Statistik/datanya via `reports_data.php`, termasuk rekap pembayaran | P2 |
| F45 | Monitoring pembayaran | Lihat status pembayaran tiap transaksi (unpaid/pending/paid/failed/expired), sinkron dari webhook | P1 — **selesai** |
| F46 | Kelola chatbot (opsional) | Lihat riwayat chat, ubah balasan otomatis intent | P3 |

### 4.9 Admin — Sistem
| ID | Fitur | Deskripsi | Prioritas |
|---|---|---|---|
| F28 | Login admin | username + password (bcrypt), akses hanya via URL langsung `admin_login.php` (tidak ditautkan dari UI publik) | P1 |
| F29 | Kelola admin | CRUD admin + ganti password | P2 |
| F30 | Pengaturan web | **Stub** (selalu sukses; tidak ada tabel `settings`) | P3 |

### 4.10 Admin — Rekap Keuangan (BARU)
| ID | Fitur | Deskripsi | Prioritas |
|---|---|---|---|
| F49 | Rekap Uang Masuk & Uang Keluar | Catat transaksi keuangan: pemasukan (mis. pembayaran kursus) & pengeluaran (mis. **sewa**, gaji, operasional); rekap per kategori/tanggal | P1 |
| F50 | Cetak laporan keuangan | Halaman cetak laporan keuangan dengan **tampilan baru** (khusus cetak, print-to-PDF) | P1 |

## 5. Kebutuhan Non-Fungsional
- **Kinerja**: halaman ringan (HTML + CSS/JS CDN, tanpa build); siap disajikan langsung dari webroot.
- **Kompatibilitas**: PHP 8, MySQL 5.7+/8, Bootstrap 5; responsive mobile-first.
- **Keamanan**: PDO prepared statements; escape output; upload whitelist; auth sesi admin & peserta; CSRF pada form publik; verifikasi signature pada webhook pembayaran & chatbot.
- **Keandalan pembayaran**: webhook idempoten (transaksi yang sama tidak diproses dua kali); status transaksi selalu bersumber dari webhook gateway, bukan asumsi klien.
- **Usability**: UI Bahasa Indonesia; admin satu dashboard dengan modal; konfirmasi destruktif via SweetAlert2.
- **Maintainability**: file flat dengan aturan pecah partial di atas 300 baris; endpoint JSON terpisah; kredensial pihak ketiga terpisah dari kode (bukan hardcode).

## 6. User Stories (Ringkas)
- Sebagai calon peserta, saya dapat melihat slide foto kegiatan di halaman depan, melihat daftar program dan detailnya, lalu membeli dan **membayar online** kursus tersebut dengan opsi **memilih Instruktur**.
- Sebagai peserta yang sudah membayar, saya dapat login ke LMS dan mengakses materi/modul kelas saya, mengerjakan test (pilihan ganda + **Essay**), serta melihat progres belajar saya.
- Sebagai peserta yang selesai kelas, saya dapat melihat sertifikat dengan **template khusus kelas saya**.
- Sebagai peserta, saya dapat menulis testimoni **dari halaman User di LMS**; testimoni tampil setelah disetujui admin, dan pengunjung bisa mengklik kartunya untuk melihat **data alumni**.
- Sebagai calon peserta, saya dapat bertanya melalui WhatsApp dan mendapat balasan otomatis untuk pertanyaan umum, atau dijawab admin bila pertanyaan lebih spesifik.
- Sebagai admin, saya dapat mengelola seluruh konten, memoderasi testimoni, memantau status pembayaran, mengelola materi LMS (termasuk soal Essay), dan **mencatat rekap uang masuk/keluar (termasuk sewa)** serta mencetak laporan keuangan.
- Sebagai admin, saya dapat menambah/mengubah/menghapus data Instruktur (wajib kategori) yang tampil di halaman profil penguji.
- Sebagai pengunjung, saya **tidak lagi melihat** tombol Login Admin di logo/navbar publik; saya bisa klik ikon **TikTok** di footer dan klik **alamat kantor** untuk langsung membuka Google Maps.

## 7. Alur Kunci

### Alur Checkout & Pembayaran (DIREVISI)
```
class_detail.php → (opsional) pilih Instruktur → modal checkout →
create_transaction.php (insert `orders`, payment_status=unpaid) →
redirect ke Payment Gateway → user bayar →
payment_webhook.php (verifikasi signature, update payment_status) →
jika paid: insert `enrollments` + notifikasi WhatsApp → payment_status.php sukses → akses lms/dashboard.php
jika failed/expired: payment_status.php gagal → user dapat mengulang
```

### Alur Testimoni (DIREVISI)
```
Peserta login LMS → halaman User (lms/profile.php) → submit testimoni (CSRF) → insert status `pending` →
admin `?page=testimonials` approve/reject →
hanya `approved` tampil di home → kartu bisa diklik → lihat data alumni
```

### Alur Profil Penguji (Instruktur)
```
Admin `?page=instructors` CRUD (wajib kategori) → DB `instructors` →
halaman Profil Penguji Ahli menampilkan data Instruktur (kategori, spesialisasi, bio, sertifikasi)
```

### Alur Rekap Keuangan (BARU)
```
Admin `?page=finance` catat transaksi (tipe uang masuk/keluar, kategori — termasuk sewa, nominal, tanggal) →
rekap per kategori/periode →
cetak laporan keuangan (tampilan khusus cetak)
```

### Alur Sertifikat (BARU)
```
Admin `?page=classes`/`?page=certs` set template sertifikat per kelas →
kelas selesai (semua materi + test lulus) →
lms/certificate.php generate sertifikat dengan template kelas tsb →
user bisa unduh/cetak
```

### Alur Chatbot WhatsApp
```
User kirim pesan WA → Meta WhatsApp Cloud API →
chatbot_webhook.php (POST) → cocokkan intent →
balas otomatis via whatsapp_client.php ATAU tandai untuk admin →
catat ke `chat_messages`
```

### Alur LMS
```
Pembayaran paid → enrollments aktif →
lms/dashboard.php (daftar kelas ter-enroll) →
lms/course.php (daftar materi) → lms/material.php (buka materi, kerjakan test PG + Essay) →
lulus test → materi berikutnya terbuka → progress bar terupdate
```

## 8. Metrik Keberhasilan
- Jumlah transaksi tersimpan, serta **rasio transaksi `paid` vs `unpaid`/`failed`**.
- Jumlah testimoni masuk dan rasio persetujuan; jumlah klik "lihat data alumni" pada kartu testimoni.
- Kelengkapan data instruktur (semua instruktur punya **kategori**, bio & sertifikasi).
- **Rasio pertanyaan chatbot yang terjawab otomatis** vs yang harus dieskalasi ke admin.
- **Rata-rata progres penyelesaian materi LMS** per kelas, termasuk kelulusan test (PG + Essay).
- Kelengkapan rekap keuangan: saldo pemasukan vs pengeluaran (termasuk sewa) tercatat & laporan keuangan dapat dicetak.
- Kepuasan admin: semua CRUD (termasuk materi LMS, rekap keuangan, & monitoring pembayaran) selesai tanpa buka DB manual.

## 9. Ruang Lingkup (Out of Scope)
- Multi-bahasa.
- Fitur sosial (komentar/like/forum diskusi) di LMS — cukup materi + progres sederhana.
- Refund/pembatalan pembayaran otomatis — ditangani manual oleh admin melalui dashboard Payment Gateway.
- Chatbot berbasis AI generatif bebas topik — chatbot terbatas pada intent yang telah didefinisikan (FAQ + data kursus).
- Modul akuntansi lengkap (neraca, arus kas standar) — rekap uang masuk/keluar cukup berbasis pencatatan sederhana + sewa.
