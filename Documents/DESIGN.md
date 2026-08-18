# DESIGN.md

Panduan desain dan identitas visual website **Mitra Cipta Mandiri (MCM)**.

> Revisi ini menghapus tombol Login Admin dari header publik, dan menambahkan pola UI untuk Payment, LMS, dan Chatbot WhatsApp.

## 1. Identitas Visual

| Elemen | Nilai |
|---|---|
| Brand | MITRA CIPTA MANDIRI |
| Logo | `assets/img/logo.png` (muncul di header, halaman profil penguji, halaman detail, LMS) — **logo tidak lagi menjadi tautan/tombol ke Login Admin**; logo hanya menaut ke `index.php` (beranda) |
| Nada | Profesional, edukatif, tepercaya, modern |

### Warna Utama
| Warna | Hex | Penggunaan |
|---|---|---|
| Biru gelap (primary) | `#0c4a6e` | Judul, tombol, teks aksen, sidebar admin, header LMS |
| Biru langit (accent) | `#0ea5e9` | Highlight, border lingkaran foto, link, ikon |
| Gradien tombol | `linear-gradient(135deg, #0c4a6e, #0ea5e9)` | CTA utama, tombol kembali, header hero, tombol "Bayar Sekarang" |
| Hijau WhatsApp | `#25D366` | Tombol chat mengambang, badge "Terhubung WhatsApp" |
| Latar terang | `bg-light` (#f8f9fa) | Section testimoni, kartu, placeholder |
| Teks | `text-dark`, `text-secondary`/`text-muted` | Hierarki tipografi |
| Status | `badge-soft-success` / `-warning` / `-danger` / `-info` | Status pesanan, testimoni, pembayaran, progres LMS |

### Tipografi
- Basis: Bootstrap default (system stack) di halaman publik & LMS.
- Heading memakai `fw-bold`; spasi huruf `letter-spacing: 1px` untuk teks logo.
- Ukuran: `display-5` untuk judul hero, `fs-5` untuk subjudul, `small` untuk label/keterangan.

## 2. Komponen UI (Publik)

| Komponen | Pola |
|---|---|
| Kartu | `card border-0 shadow-sm rounded-4 overflow-hidden` |
| Tombol | `btn rounded-pill px-4 shadow-sm`, primary berbasis gradien biru |
| Badge | `badge bg-primary bg-opacity-10 text-primary rounded-pill px-3` |
| Navbar | Bootstrap navbar, item anchor `#section`, link dinamis sesuai `$hide_nav_items`. **Tidak ada item/tombol "Login Admin"**; navbar publik hanya berisi navigasi konten + tombol "Masuk"/"Daftar" untuk peserta LMS (opsional, kanan navbar) |
| Hero | Section `section-padding` + Swiper slider |
| Modal | Bootstrap modal `modal-dialog-centered`, header tanpa border-bawah |
| Avatar lingkaran | `rounded-circle`, ukuran konsisten (40–150 px), `object-fit: cover` |
| Rating | 5 ikon `fa-star` (`text-warning` aktif / `text-muted` kosong) |
| Dropdown pilih penguji | `form-select form-select-lg shadow-sm` + area detail dinamis |
| Tombol chat mengambang | `position-fixed bottom-0 end-0`, lingkaran hijau `#25D366`, ikon `fa-whatsapp`, muncul di semua halaman publik & LMS |

## 3. Halaman Publik

| Halaman | Deskripsi Desain |
|---|---|
| `index.php` | Hero (slider) → Tentang → Galeri → Paket Pelatihan (kartu kelas) → Testimoni (`section-padding bg-light`) → Footer + tombol chat mengambang. Anchor nav: Beranda, Tentang, Galeri, Paket, Testimoni |
| `programs.php` | Grid kartu program pelatihan |
| `class_detail.php` | Detail kelas, fitur, harga, tombol "Pilih Penguji" (opsional) + tombol "Daftar & Bayar"; tombol kembali melingkar |
| `examiners.php` | Header brand + badge "OUR TEAM"; dropdown "Pilih Penguji"; kartu detail (foto lingkaran, nama, spesialisasi, bio, badge sertifikasi); state kosong berisi hint; section "Komitmen Penguji Kami" |
| `testimoni.php` | Header "Tulis Testimoni Anda"; form rating bintang (`star-rating` radio), nama, program (select), ulasan, foto profil opsional; validasi CSRF; pesan sukses "menunggu persetujuan admin" |
| `certification.php` / `about.php` | Info legalitas & visi-misi dengan tombol kembali melingkar |
| `user_login.php` / `user_register.php` | Kartu terpusat sederhana (`card shadow-sm rounded-4`, max-width 420px), form email/password, link silang login↔daftar |

## 4. Desain Payment / Pembayaran (BARU)

| Elemen | Pola |
|---|---|
| Modal checkout | Diperluas: ringkasan kelas + penguji terpilih (bila ada) → data pemesan → tombol gradien "Bayar Sekarang" (bukan lagi langsung redirect WhatsApp) |
| Redirect pembayaran | Snap/redirect page bawaan Payment Gateway (di luar kendali styling MCM); setelah selesai, kembali ke `payment_status.php` |
| `payment_status.php` | Kartu status terpusat: ikon besar sesuai status (✓ hijau = paid, jam kuning = pending, ✕ merah = failed/expired), nomor order, ringkasan kelas, tombol "Buka LMS" (bila paid) atau "Coba Bayar Lagi" (bila failed/expired) |
| Badge status pembayaran | `soft-warning` unpaid/pending, `soft-success` paid, `soft-danger` failed/expired |
| Invoice/bukti | `generate_pdf.php` tetap dipertahankan sebagai halaman cetak, kini hanya dapat diakses setelah `payment_status = paid` |

## 5. Desain LMS (BARU)

| Elemen | Pola |
|---|---|
| Layout | Sidebar kiri daftar kelas ter-enroll (mirip pola sidebar admin, warna primary) + konten kanan daftar modul/materi |
| `lms/dashboard.php` | Grid/list kartu kelas yang sudah lunas, badge progres (`x/y materi selesai`), tombol "Lanjutkan Belajar" |
| `lms/course.php` | List modul bernomor urut, ikon tipe materi (`fa-file-video`, `fa-file-pdf`, `fa-file-lines`), checkmark hijau untuk materi selesai, ikon gembok untuk materi yang belum lunas/di luar enrollment |
| `lms/material.php` | Panel konten (video embed / PDF viewer / teks terformat) + tombol "Tandai Selesai"; progress bar di header (`progress` Bootstrap, warna accent `#0ea5e9`) |
| Empty state | Bila belum ada kelas ter-enroll: ilustrasi ringan + tombol "Lihat Program Pelatihan" ke `programs.php` |

## 6. Desain Chatbot WhatsApp (BARU)

| Elemen | Pola |
|---|---|
| Tombol mengambang | Lingkaran hijau `#25D366`, ikon `fa-whatsapp`, posisi `fixed bottom-24 right-24`, tersedia di semua halaman publik & LMS; klik membuka `wa.me/<nomor>` dengan pesan prefilled sesuai konteks halaman (mis. "Halo, saya ingin tanya kelas {nama_kelas}") |
| Balasan bot | Terjadi di aplikasi WhatsApp user (bukan UI web MCM) — tidak memerlukan komponen visual tambahan di website, cukup badge kecil "Chat cepat via WhatsApp" di dekat tombol |
| Panel riwayat chat (admin, opsional/P2) | `admin/pages/chatbot.php` — daftar percakapan mirip inbox, bubble chat kiri/kanan, badge "Dijawab Bot" / "Perlu Respons Admin" |

## 7. Desain Admin

### Layout
- Sidebar kiri (desktop) + bottom navigation (mobile) + offcanvas panel admin.
- Header wrapper `admin/includes/head.php`: `styles_head.php`, `styles_head_components.php`, `nav_bottom.php`, `sidebar.php`, `mobile_header.php`, `offcanvas.php`.
- Menu sidebar baru: **Materi LMS** (ikon `fa-graduation-cap`), **Pembayaran** (ikon `fa-credit-card`, sub dari menu Pesanan), **Chatbot** (ikon `fa-comment-dots`, opsional/P2).

### Pola Halaman Admin
```
Header halaman:
  Judul fw-bold + deskripsi (kolom kiri)
  Tombol aksi utama "Tambah ..." (kolom kanan, text-md-end)

Kartu tabel:
  card border-0 shadow-sm rounded-4
  thead bg-light, th ps-4, td text-end pe-4
  Empty state: colspan, text-center py-5 text-muted
```

### Tombol Aksi Tabel
- `btn btn-action btn-soft-primary` — edit (ikon `fa-edit`)
- `btn btn-action btn-soft-danger` — hapus (ikon `fa-trash`)
- `btn btn-action btn-soft-success` — setujui (ikon `fa-check`)
- `btn btn-action btn-soft-warning` — tolak (ikon `fa-times`)
- `btn btn-action btn-soft-info` — lihat detail transaksi/materi (ikon `fa-eye`)

### Modal
- Satu file per modal di `admin/includes/modal_*.php`, di-include wrapper `modals.php`.
- Form memakai `submitAjaxForm()` (fetch JSON), `enctype="multipart/form-data"` bila ada upload.
- Modal materi LMS (`modal_material.php`): field judul, tipe (video/PDF/teks), upload/URL, urutan.
- Konfirmasi hapus memakai SweetAlert2 (`deleteItem()`).

### Feedback Pengguna
- SweetAlert2 untuk sukses/gagal CRUD.
- Spinner di tombol submit saat proses.
- Badge status berwarna (soft) untuk pending/confirmed/cancelled, pending/approved/rejected, dan unpaid/paid/failed/expired.

## 8. Status & Badge

| Domain | Status | Badge |
|---|---|---|
| Pesanan (`orders`) | pending / confirmed / cancelled | soft-warning / soft-success / soft-danger |
| Pembayaran (`orders.payment_status`) | unpaid / pending / paid / failed / expired | soft-secondary / soft-warning / soft-success / soft-danger / soft-danger |
| Testimoni (`testimonials`) | pending / approved / rejected | soft-warning / soft-success / soft-danger |
| Booking (`bookings`) | (kolom status di `update_booking.php`) | — |
| Progres materi (`material_progress`) | belum / selesai | soft-secondary / soft-success |

## 9. Responsivitas

- Breakpoint Bootstrap: grid `col-lg-4 col-md-6` untuk kartu grid (galeri, kelas, penguji).
- Sidebar admin & sidebar LMS disembunyikan di mobile → digantikan bottom nav/offcanvas.
- Modal `modal-dialog-centered` agar nyaman di layar kecil, termasuk modal checkout pembayaran.
- Hero swiper menyesuaikan tinggi otomatis.
- Tombol chat mengambang disesuaikan agar tidak menutupi bottom nav mobile (`bottom` diperbesar di breakpoint `sm`).

## 10. Interaksi & Animasi

- **AOS** (`data-aos`) untuk animasi scroll pada section publik (mis. `fade-down`, `fade-up`).
- **Swiper** untuk slider hero dan galeri.
- **animate__animated animate__fadeIn** untuk fade-in halaman (mis. `examiners.php`, `lms/dashboard.php`).
- Transisi ringan pada hover tombol/badge (transform scale, shadow).
- Progress bar LMS mengisi dengan transisi halus (`transition: width .3s ease`).
